// Netlify Server: AI chat endpoint for the Careflow showcase page.
// Uses Netlify AI Gateway (credentials are injected automatically).
// Conversations are not stored: the browser sends recent turns with each request.
import Anthropic from '@anthropic-ai/sdk'

const anthropic = new Anthropic()

const MODEL = 'claude-haiku-5-5'
const MAX_TURNS = 12
const MAX_CHARS = 1500

const SYSTEM_PROMPT = `You are "Dr. Cares", the friendly assistant on the Careflow showcase site — a fictional hospital-management software portfolio project by Utsav Acharya.

You can help with:
- Explaining what the project does: appointment booking and approval, patient/doctor/admin roles, departments, prescriptions, treatments, billing, rooms, and medicine records (built in PHP/MySQL).
- How a hospital-management system like this typically works, and general software/design questions about it.
- General, widely known health-education information (e.g. what a general check-up involves, healthy habits), clearly framed as general information.

Strict rules:
- You are NOT a doctor. Never diagnose, triage, interpret test results, or recommend specific medications or doses. For personal health concerns, encourage contacting a licensed healthcare professional.
- If a message suggests an emergency (chest pain, trouble breathing, severe bleeding, stroke signs, suicidal thoughts, overdose, etc.), tell the user to contact local emergency services immediately, and keep the reply short.
- This site cannot book real appointments or access any patient records. Say so if asked.
- Ask users not to share personal, medical, password, or payment information.
- Keep answers concise (usually under 150 words), warm, and plain-language. Use short lists when helpful.`

const json = (body, status = 200) =>
  new Response(JSON.stringify(body), {
    status,
    headers: { 'Content-Type': 'application/json' },
  })

function sanitize(messages) {
  if (!Array.isArray(messages)) return null
  const cleaned = messages
    .filter((m) => m && (m.role === 'user' || m.role === 'assistant') && typeof m.content === 'string')
    .map((m) => ({ role: m.role, content: m.content.trim().slice(0, MAX_CHARS) }))
    .filter((m) => m.content.length > 0)
    .slice(-MAX_TURNS)
  // Conversation must start with a user turn and end with one.
  while (cleaned.length && cleaned[0].role !== 'user') cleaned.shift()
  if (!cleaned.length || cleaned[cleaned.length - 1].role !== 'user') return null
  return cleaned
}

export default async function handler(request) {
  const url = new URL(request.url)

  if (url.pathname !== '/api/chat') return json({ error: 'Not found' }, 404)
  if (request.method !== 'POST') return json({ error: 'Method not allowed' }, 405)

  let body
  try {
    body = await request.json()
  } catch {
    return json({ error: 'Invalid JSON body' }, 400)
  }

  const messages = sanitize(body?.messages)
  if (!messages) return json({ error: 'Send a non-empty message.' }, 400)

  let stream
  try {
    // Awaiting here surfaces auth/rate-limit errors before any bytes are sent.
    stream = await anthropic.messages.create({
      model: MODEL,
      max_tokens: 600,
      system: SYSTEM_PROMPT,
      messages,
      stream: true,
    })
  } catch (err) {
    console.error('Chat error:', err?.status, err?.message)
    const status = err?.status === 429 ? 429 : 502
    const error =
      status === 429
        ? 'The assistant is busy right now. Please wait a moment and try again.'
        : 'The assistant is unavailable right now. Please try again shortly.'
    return json({ error }, status)
  }

  const encoder = new TextEncoder()
  return new Response(
    new ReadableStream({
      async start(controller) {
        try {
          for await (const event of stream) {
            if (event.type === 'content_block_delta' && event.delta.type === 'text_delta') {
              controller.enqueue(encoder.encode(event.delta.text))
            }
          }
        } catch (err) {
          console.error('Chat stream error:', err?.message)
          controller.enqueue(encoder.encode('\n\n[Sorry, the reply was interrupted. Please try again.]'))
        } finally {
          controller.close()
        }
      },
      cancel() {
        stream.controller.abort()
      },
    }),
    { headers: { 'Content-Type': 'text/plain; charset=utf-8', 'Cache-Control': 'no-store' } },
  )
}

export const config = {
  path: '/api/*',
}
