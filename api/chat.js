// Vercel serverless endpoint for the public Careflow demo chatbot.
// Configure OPENAI_API_KEY in Vercel Project Settings > Environment Variables.
const allowedOrigins = new Set([
  "https://utasar.github.io",
  "https://careflow-ai-api.vercel.app",
  "http://localhost:3000",
  "http://127.0.0.1:3000"
]);
const recentRequests = new Map();
const WINDOW_MS = 10 * 60 * 1000;
const MAX_REQUESTS = 12;

function send(res, status, payload) {
  res.status(status).json(payload);
}

module.exports = async function handler(req, res) {
  const origin = req.headers.origin;
  if (origin && allowedOrigins.has(origin)) {
    res.setHeader("Access-Control-Allow-Origin", origin);
    res.setHeader("Vary", "Origin");
  }
  res.setHeader("Cache-Control", "no-store");
  res.setHeader("X-Content-Type-Options", "nosniff");

  if (req.method === "OPTIONS") {
    res.setHeader("Access-Control-Allow-Methods", "POST, OPTIONS");
    res.setHeader("Access-Control-Allow-Headers", "Content-Type");
    return res.status(204).end();
  }
  if (req.method !== "POST") return send(res, 405, { error: "Method not allowed." });
  if (!origin || !allowedOrigins.has(origin)) return send(res, 403, { error: "This website is not allowed to use the chat service." });
  if (!process.env.OPENAI_API_KEY) return send(res, 503, { error: "The AI service is not configured yet. Please try again later." });

  const ip = String(req.headers["x-forwarded-for"] || "unknown").split(",")[0].trim();
  const now = Date.now();
  const bucket = recentRequests.get(ip) || { count: 0, resetAt: now + WINDOW_MS };
  if (now > bucket.resetAt) { bucket.count = 0; bucket.resetAt = now + WINDOW_MS; }
  bucket.count += 1;
  recentRequests.set(ip, bucket);
  if (bucket.count > MAX_REQUESTS) return send(res, 429, { error: "Too many messages. Please wait a few minutes and try again." });
  if (recentRequests.size > 2000) {
    for (const [key, value] of recentRequests) if (now > value.resetAt) recentRequests.delete(key);
  }

  const body = req.body && typeof req.body === "object" ? req.body : {};
  const messages = Array.isArray(body.messages) ? body.messages : [];
  if (!messages.length || messages.length > 10) return send(res, 400, { error: "Send between 1 and 10 recent messages." });
  const safeMessages = [];
  for (const item of messages) {
    if (!item || !["user", "assistant"].includes(item.role) || typeof item.content !== "string") {
      return send(res, 400, { error: "Invalid chat message." });
    }
    const content = item.content.trim();
    if (!content || content.length > 1200) return send(res, 400, { error: "Each message must be 1–1,200 characters." });
    safeMessages.push({ role: item.role, content });
  }
  if (safeMessages[safeMessages.length - 1].role !== "user") return send(res, 400, { error: "The latest message must be from the visitor." });

  try {
    const upstream = await fetch("https://api.openai.com/v1/responses", {
      method: "POST",
      headers: {
        "Authorization": "Bearer " + process.env.OPENAI_API_KEY,
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        model: process.env.OPENAI_MODEL || "gpt-6-luna",
        tools: [{ type: "web_search" }],
        instructions: `You are Careflow's general health-information assistant for a public educational demo. You are not a doctor, pharmacist, nurse, or emergency service. You may explain common symptoms in general terms, basic self-care, and general over-the-counter (OTC) medicine education. Never diagnose a visitor, claim certainty about the cause of a symptom, prescribe or select a medicine for an individual, calculate or recommend a personal dose, or tell someone to start, stop, or change a medicine.

For symptom questions:
- Give a short list of common possible explanations only when appropriate, clearly stating that many causes are possible and you cannot determine the cause from chat.
- Offer low-risk general self-care information where appropriate, without implying it is a substitute for medical care.
- Ask at most one or two relevant follow-up questions only if they materially help explain general information. Do not request names, exact birth dates, addresses, phone numbers, medical-record numbers, photos, or other identifying information.
- Clearly describe urgent warning signs. For potentially life-threatening symptoms (for example chest pain with breathing difficulty, severe trouble breathing, stroke signs, fainting/unresponsiveness, severe allergic reaction, or a sudden extremely severe headache), tell the visitor to call 911 in the US or their local emergency number now. Do not delay emergency care to answer more questions.

For medicine questions:
- Provide educational information about what an OTC medicine class or ingredient is commonly used for, common label warnings, and the importance of following the exact product label.
- Do not give a personal dose, dosing schedule, or individualized medicine choice. Do not assume a product's strength or formulation.
- Explain that age limits and warnings vary by product and country. For children, pregnancy or breastfeeding, older adults, chronic conditions, allergies, or people taking other medicines, advise checking with a pharmacist or clinician before using a medicine.
- Warn about duplicate active ingredients across combination products and possible interactions when relevant, without making a personalized safety determination.
- Do not recommend prescription-only medicines, controlled drugs, antibiotics, or supplements as treatment.

Use web search for current or specific health and medicine facts. Prefer authoritative sources such as MedlinePlus/NIH, FDA, CDC, NHS, and major academic medical centers. Give the source organization and a direct link when useful; do not invent citations. If reliable information is unavailable, say so. Do not rely on forums, ads, or product marketing.

If the visitor describes an emergency, prioritize emergency direction before any other content. If asked for a diagnosis, say you cannot diagnose and offer general information plus appropriate care-seeking guidance. If asked for a medicine dose, explain that you cannot determine an individual dose and direct them to the exact package label or a pharmacist/clinician. Keep replies calm, plain-language, concise, and nonjudgmental.

Do not claim Careflow is a medical service, that a clinician reviews chats, or that the site stores or protects health records. Remind visitors not to enter identifying or highly sensitive information. This is educational information only, not diagnosis or treatment.`,
        input: safeMessages.map(m => ({ role: m.role, content: m.content })),
        max_output_tokens: 350,
        store: false
      })
    });
    const result = await upstream.json();
    if (!upstream.ok) {
      console.error("OpenAI API error:", upstream.status, result && result.error ? result.error.type : "unknown");
      return send(res, 502, { error: "The AI service could not respond right now. Please try again shortly." });
    }
    const reply = (result.output || [])
      .flatMap(item => item.content || [])
      .filter(part => part.type === "output_text" && typeof part.text === "string")
      .map(part => part.text)
      .join("\n")
      .trim();
    if (!reply) return send(res, 502, { error: "The AI service returned an empty response. Please try again." });
    return send(res, 200, { reply });
  } catch (error) {
    console.error("Chat endpoint error:", error && error.message ? error.message : "unknown");
    return send(res, 500, { error: "A connection error occurred. Please try again later." });
  }
};
