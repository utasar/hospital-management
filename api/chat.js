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
        model: process.env.OPENAI_MODEL || "gpt-5.5",
        instructions: "You are Careflow's friendly website demo assistant. Help visitors understand this portfolio project, its sample features, and general non-sensitive software concepts. You are not a healthcare professional. Never diagnose, triage symptoms, recommend medicines, dosages, or treatment, and never request personal, patient, financial, or account information. If asked for medical advice or emergency help, say you cannot provide it and advise contacting a licensed healthcare professional or local emergency services for an emergency. Do not claim the demo books appointments, stores data, or connects to a real hospital. Keep answers concise, clear, and safe.",
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
