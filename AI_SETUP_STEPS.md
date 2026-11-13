# Set up the assistant safely (step by step)

## What this feature is

The current assistant is a **deterministic, rules-based information and navigation tool**. It is not a generative AI model, a medical device, or a clinical decision-support system. It must not be used to diagnose, assess severity, recommend medicines/doses, interpret tests, or make care decisions. A clinician remains responsible for clinical care.

## Step 1 — Use a safe environment

- Work in a local or staging copy, never directly against a live patient database.
- Back up the database and source before deploying changes.
- Do not use real patient information in development or testing.
- Review applicable health-data, privacy, and security obligations with qualified professionals before any real-world deployment.

## Step 2 — Install the existing application

- Use a supported PHP runtime and MySQL/MariaDB version compatible with the legacy application.
- Configure database credentials through the project's environment configuration, using a dedicated database account (not `root`).
- Import the main database schema first: `DATABASE FILE/ohmsphp.sql`.
- Import the assistant tables second: `DATABASE FILE/ai_features_schema.sql`.
- Verify both imports completed successfully in a disposable local database.

## Step 3 — Test the safe assistant behavior

Sign in with a test patient account and verify:

1. Empty text is rejected and text longer than 4,000 characters is rejected.
2. A normal test description receives only the non-diagnostic clinician-contact notice.
3. Emergency warning phrases show emergency-service guidance; do not treat this keyword check as reliable medical triage.
4. No medication or dosage recommendations are shown or saved.
5. The form rejects a missing/invalid CSRF token.
6. Chat history displays user-submitted text safely escaped.

Do not test using actual medical details. The current schema stores submitted text and chat messages in the database; treat those records as sensitive and restrict access accordingly.

## Step 4 — Deploy only after operational review

- Require HTTPS, secure session-cookie settings, least-privilege database access, access logging, encrypted backups, retention/deletion rules, and a tested incident response process.
- Review authorization for every page and ensure one patient cannot access another patient's records.
- Conduct security testing and clinical/legal review. These steps are not implemented or certified by this assistant setup.
- Do not expose the reminder cron endpoint to the public internet; use a restricted command-line scheduler and monitor job results.

## Step 5 — If a real AI provider is considered later

There is no external AI provider configured by default. Before integrating one, select an approved provider and have security, privacy, clinical, and legal owners review it. Keep credentials in a secret manager, send no identifiable patient data unless explicitly approved and legally covered, document retention and data-processing terms, validate outputs, provide human escalation, and keep the system from prescribing or diagnosing. Start with synthetic data in a sandbox and require clinical validation before any workflow can affect care.

## Current limitations

- Keyword checks are incomplete and can miss emergencies or flag ordinary text; they are not triage.
- General reminders are not personalized care plans.
- Health-risk prediction is disabled because the legacy data does not support a validated clinical model.
- The assistant UI and data-storage schema are a safety-oriented demo foundation, not evidence of production readiness or regulatory compliance.
