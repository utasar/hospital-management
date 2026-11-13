# AI Features — Safety and Setup

The AI-related pages in this repository are an early demo foundation. The current assistant is deterministic and rules-based; it is **not** connected to a generative AI provider, and it is not clinically validated.

## Current behavior

- **Health information intake:** stores a patient's submitted description and returns general guidance to contact a licensed professional. It does not make diagnoses, provide confidence scores, or recommend medication/dosage.
- **Emergency phrase warning:** a small keyword list can display emergency-service guidance. This is not triage and can miss urgent situations; users must not rely on it to determine severity.
- **Chatbot:** offers limited navigation and general safety responses. It cannot provide medical advice.
- **Preventive care:** generic educational reminders only, not a personalized care plan.
- **Health-risk predictions:** disabled until a validated and clinically governed approach exists.
- **Appointment reminders:** separate scheduling utility; configure and secure it independently.

## Setup

Follow [AI_SETUP_STEPS.md](AI_SETUP_STEPS.md). Import the primary database schema and then `DATABASE FILE/ai_features_schema.sql` in a disposable local/staging environment. Never test with real patient data.

## Safety requirements

- Do not use these features to diagnose, prescribe, interpret results, or make treatment decisions.
- Seek qualified clinical, security, privacy, and legal review before using health data or deploying in a healthcare setting.
- The database schema persists symptom and chat text; treat it as sensitive data and define strict access, retention, deletion, and backup controls.
- No external AI vendor is configured. Any later integration needs a provider/privacy review, secret management, data minimization, human oversight, and clinical validation.

This repository is not certified HIPAA/GDPR compliant and this documentation is not legal or medical advice.
