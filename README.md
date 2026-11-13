# Hospital Management System — Demo Project

A legacy PHP/MySQL hospital-management **demo and learning project**. It contains example patient, doctor, appointment, billing, and prescription workflows. It is not a production-ready medical records system and must not be used to store real patient data or make clinical decisions.

## Run locally for a project demo

Use a disposable local machine or isolated staging environment. Do not use a public server or real patient information for this setup.

1. Install PHP with the `mysqli` extension and MySQL/MariaDB. The original codebase is legacy; check for PHP warnings/errors and test all flows on your actual runtime before presenting it.
2. Create a fresh database and a dedicated local user. Do not use the MySQL `root` account for the app:

   ```sql
   CREATE DATABASE ohmsphp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'hms_demo'@'localhost' IDENTIFIED BY 'replace-with-a-long-local-password';
   GRANT ALL PRIVILEGES ON ohmsphp.* TO 'hms_demo'@'localhost';
   FLUSH PRIVILEGES;
   ```

3. Create your private environment file and edit the database values. `.env` is ignored by Git and should never be committed:

   ```bash
   cp .env.example .env
   ```

4. Import the sample schema into the **fresh demo database only**:

   ```bash
   mysql -u hms_demo -p ohmsphp < "DATABASE FILE/ohmsphp.sql"
   mysql -u hms_demo -p ohmsphp < "DATABASE FILE/ai_features_schema.sql"
   ```

5. Run the preflight check and start PHP's local development server:

   ```bash
   php tools/preflight.php
   php -S 127.0.0.1:8000
   ```

6. Open `http://127.0.0.1:8000`. Test the admin, doctor, patient, appointment, and AI information pages with fictional demo data before presenting.

If database import or startup fails, check your PHP/MySQL versions and logs. The preflight check verifies basic prerequisites; it does not prove every feature works.

## Before publishing the demo

- **Do not publish a live instance with the sample database.** The SQL dump contains demo identities, contact details, and plaintext default passwords. Treat all included records as public, non-private sample data. Replace/remove them before hosting and never reuse the listed credentials.
- The database schema includes fields for card numbers and CVVs. Do not enter real payment card data; never store CVVs. Use a payment provider's hosted checkout if you add payments.
- Review all existing pages before exposing them publicly. This old application has legacy SQL, authorization, upload, and session patterns outside the limited changes in this branch.
- The login updates are not a complete authentication redesign; existing database passwords are stored in plaintext. Do not use real accounts or passwords.
- Use HTTPS, restricted database permissions, backups, logging, and a security review for any internet-hosted demo. A public code repository is not a safe place for secrets or patient data.
- The AI pages are a rules-based demo, not medical AI: no diagnosis, medication, or treatment decisions. Keyword emergency warnings are incomplete and are not triage.

## Publishing the code

This branch is a preparation for review, not a release certification. Review changes, run the preflight check, test the site locally, replace sample content and credentials, confirm you have rights to publish the code/assets, and then merge/publish the branch. Keep `.env` private.

## Current limitations

This repository is an old, multi-page PHP application. This setup improves local configuration and documents safe demo usage, but does not comprehensively modernize every page, guarantee compatibility with current PHP, provide complete automated tests, or establish healthcare compliance. Do not use it as a real hospital service.
