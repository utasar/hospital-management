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
5. Optionally add `OPENAI_MODEL` with a model available to your API project. The default is `gpt-5.5`.
6. Deploy. The chat endpoint will be `https://YOUR-VERCEL-PROJECT.vercel.app/api/chat`.

## Connect the GitHub Pages page

1. Open `docs/index.html` and find `CAREflow_CHAT_API`.
2. Replace the example URL with the exact endpoint URL Vercel gives your project, ending in `/api/chat`.
3. Commit that one-line change to the publishing branch. GitHub Pages will redeploy the updated showcase.

The endpoint currently allows the GitHub Pages origin `https://utasar.github.io` and the default Vercel project hostname `https://careflow-ai-api.vercel.app`. If Vercel gives you a different hostname, add that exact hostname to `allowedOrigins` in `api/chat.js` before deploying (or ask for help updating it).

## Important limits and safety

- The chat is a portfolio demo for questions about the Careflow website and general software topics, not a medical assistant.
- Do not enter patient details, symptoms, health records, passwords, or payment information.
- The API key is read only by the server from Vercel environment variables.
- The included in-memory request limit is a lightweight safeguard, not a production-grade abuse-control system; Vercel serverless instances may not share counters. Set provider spending limits and usage alerts, and add a persistent rate limiter before broad public promotion.
- The public GitHub Pages showcase and the legacy PHP/MySQL hospital application remain separate. Do not expose the legacy application or use it with real patient data.
