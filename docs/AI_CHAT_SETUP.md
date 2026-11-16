# Careflow AI chat deployment

The GitHub Pages showcase is static, so it cannot run the private AI API key or server code. This repository includes a Vercel serverless endpoint in `api/chat.js`; the public showcase calls that endpoint.

## Deploy the AI endpoint

1. Sign in to Vercel and choose **Add New → Project**.
2. Import `utasar/hospital-management` from GitHub.
3. Use the repository root as the project root. The included `vercel.json` publishes the static showcase from `docs/` while keeping `api/chat.js` as a serverless function.
4. In the Vercel project, open **Settings → Environment Variables** and add:
   - Name: `OPENAI_API_KEY`
   - Value: your OpenAI API key (keep it secret; never put it in the website source)
   - Environments: Production (and Preview if you want preview deployments)
5. Optionally add `OPENAI_MODEL` with a model available to your API project. The default is `gpt-6-luna`.
6. Deploy. The chat endpoint will be `https://YOUR-VERCEL-PROJECT.vercel.app/api/chat`.

## Connect the GitHub Pages page

1. Open `docs/index.html` and find `CAREflow_CHAT_API`.
2. Replace the example URL with the exact endpoint URL Vercel gives your project, ending in `/api/chat`.
3. Commit that one-line change to the publishing branch. GitHub Pages will redeploy the updated showcase.

The endpoint currently allows the GitHub Pages origin `https://utasar.github.io` and the default Vercel project hostname `https://careflow-ai-api.vercel.app`. If Vercel gives you a different hostname, add that exact hostname to `allowedOrigins` in `api/chat.js` before deploying (or ask for help updating it).

## Important limits and safety

- The chat is a general health-information education demo. It can explain common symptoms, low-risk self-care, and OTC medicine label concepts, but it must not diagnose, prescribe, select an individual medicine, or calculate a personal dose.
- Emergency symptoms must be directed to 911 in the US (or the local emergency number elsewhere).
- Chat messages are sent to the OpenAI API to generate responses; `store: false` is set on the Responses API request. Do not enter names, contact details, patient details, medical records, passwords, or payment information. Review OpenAI API data controls and terms before public launch.
- The API uses OpenAI web search for current health information. The assistant is instructed to prefer authoritative public-health sources such as MedlinePlus/NIH, FDA, CDC, NHS, and major academic medical centers.
- The API key is read only by the server from Vercel environment variables.
- The included in-memory request limit is a lightweight safeguard, not a production-grade abuse-control system; Vercel serverless instances may not share counters. Set provider spending limits and usage alerts, and add a persistent rate limiter before broad public promotion.
- The public GitHub Pages showcase and the legacy PHP/MySQL hospital application remain separate. Do not expose the legacy application or use it with real patient data.
