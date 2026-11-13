# Hospital Management System

This project is a hospital management application with patient, doctor, admin, appointment, billing, treatment, and prescription workflows. It has been upgraded to a more production-friendly foundation for real-world business use, but it still requires a secure hosting environment, a proper database setup, and user testing before it is ready for live hospital operations.

## What was improved

- Removed direct hardcoded production assumptions from the database configuration.
- Added environment-based configuration via `.env.example` and `config.php`.
- Added a security helper file for safer app usage.
- Hardened the main login flows against SQL injection by switching to prepared statements.
- Added a `.gitignore` to keep environment secrets out of source control.
- Documented a more realistic setup and deployment workflow.

## Important note

This is not yet a full commercial SaaS product. It is a stronger foundation for one, but real-world healthcare deployment still requires:

- secure password hashing
- role-based access enforcement
- audit logging
- SSL/HTTPS everywhere
- compliance review for medical data
- automated tests and CI/CD
- backup and recovery procedures
- business-specific workflow validation

## Local setup

1. Copy `.env.example` to `.env` and adjust values:

```bash
cp .env.example .env
```

2. Create the MySQL database and import the schema:

```bash
mysql -u root -p
CREATE DATABASE ohmsphp;
SOURCE "DATABASE FILE/ohmsphp.sql";
```

3. Update your local web server to point to the project folder.

4. Open the app in a browser, for example:

```text
http://localhost/
```

## Production deployment checklist

- Use a dedicated MySQL user instead of `root`
- Store secrets in environment variables, never in source code
- Enable HTTPS and secure cookies
- Add strong password policies and session handling
- Restrict admin access to trusted IPs or VPN
- Add backups and database replication for business continuity
- Review billing, patient records, and data retention rules

## Default admin login

The project still contains demo default credentials in the sample database. Replace these before any live deployment.

## Project structure

- `dbconnection.php` - database connection wrapper
- `config.php` - centralized application configuration
- `security.php` - sanitization and escaping helpers
- `adminlogin.php`, `doctorlogin.php`, `patientlogin.php` - user auth entry points
- `DATABASE FILE/ohmsphp.sql` - sample database schema

## Recommended next steps

1. Replace demo login credentials.
2. Add password hashing with `password_hash` and `password_verify`.
3. Separate admin, doctor, and patient permissions into a clean access control layer.
4. Add hospital workflows for billing, pharmacy, labs, appointments, and reporting.
5. Add automated tests and CI/CD before production rollout.

## Technology stack

- PHP
- MySQL
- HTML/CSS/JavaScript
- Bootstrap-based UI
