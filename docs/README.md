# Publish the portfolio showcase

The `docs/` folder contains a static, browser-only project showcase. It does not connect to the PHP/MySQL application, accept health details, or store submissions. It is the only part of this repository that is appropriate for a simple public portfolio preview without first deploying the legacy backend.

## GitHub Pages

1. Merge the reviewed branch into the repository's publishing branch when ready.
2. In GitHub, open **Settings → Pages**.
3. Choose **Deploy from a branch**, select the publishing branch, and choose `/docs` as the folder. Save and wait for GitHub Pages to publish.
4. Open the Pages URL shown in Settings to confirm the showcase loads over HTTPS.
5. Do not enable a PHP host or connect this showcase to the legacy database as part of this static-site setup.

GitHub Pages serves static files; it will not execute PHP or MySQL. The PHP app is not safe to expose publicly as-is. It still has legacy authentication, access-control, SQL, and data-handling problems, and its SQL dump includes demo identities and plaintext passwords. Do not use real patient information or publish a live instance until the application is comprehensively rebuilt/reviewed and the sample data is removed.

## Optional custom domain

Domain availability and price vary by registrar. Check your preferred registrar for `dr.care` or alternatives such as `utsavacharya.care`, `utsavacharya.dev`, or `careflowbyutsav.com`; none are confirmed available. Avoid a `Dr`-branded domain if it could imply you are a licensed clinician.

After purchasing a domain you are entitled to use, add it in **Settings → Pages → Custom domain** and follow GitHub's current DNS instructions for that exact domain. Keep HTTPS enabled, and do not add a `CNAME` file until you have selected and configured a domain you own.

## Feedback

Ask people to review the design and software workflow. Do not request real symptoms, medical histories, credentials, or payment information in public feedback. For general software feedback, use GitHub Discussions or issues and warn contributors not to include private information.
