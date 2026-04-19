# eBarangayHub

Centralized Barangay Services, Reports, and Community Access

eBarangayHub is a LavaLust-based barangay web application for handling resident services, complaints, community information, reports, simulated payments, and final document release.

## Stack

- PHP framework: LavaLust
- Local server: XAMPP
- Database: MySQL via XAMPP/phpMyAdmin
- Frontend styling: Tailwind CSS
- Rendering style: server-rendered PHP views

## Core Modules

- Resident registration, login, logout, and role-based dashboards
- Resident service request submission with protected attachments
- Staff request queue, status updates, notes, and payment review
- Admin service, user, announcement, community, audit log, and report management
- Protected final document upload and controlled resident download
- Simulated payment workflow for paid services
- Complaint submission, tracking, evidence review, and staff processing
- Public community section for updates, events, advisories, programs, and resources
- Admin reports with filtered tables, CSV exports, and chart dashboard
- Virtual help assistant with a rule-based fallback and optional server-side AI foundation
- Optional Gmail SMTP email notifications for important workflow events

## Local Setup

1. Place the project in your XAMPP `htdocs` folder.
2. Start Apache and MySQL from XAMPP.
3. Install PHP mail dependencies:

```bash
composer install
```

If Composer is not installed globally on Windows, install Composer first or use a local `composer.phar`, then run:

```bash
php composer.phar install
```

4. Install frontend dependencies:

```bash
npm install
```

5. Build Tailwind output:

```bash
npm run build
```

6. Start the local server:

```bash
npm run serve
```

7. Open:

```text
http://localhost:3000/
```

## Database Setup

For a fresh local rebuild, import this file in phpMyAdmin:

```text
database/full_database_rebuild.sql
```

For phase-by-phase setup, import the files in numeric order:

```text
phase_01_auth.sql
phase_02_resident_requests.sql
phase_03_staff_processing.sql
phase_04_admin_management.sql
phase_05_final_documents.sql
phase_06_simulated_payments.sql
phase_07_complaints.sql
phase_08_community_section.sql
```

Some later phases may be code-only and may not need SQL.

## Demo Accounts

After importing the database seed data:

- Admin: `admin@barangay.local` / `password123`
- Staff: `staff@barangay.local` / `password123`
- Resident: create a new resident account from the Register page

## Email Notifications

Email notifications use Gmail SMTP through PHPMailer. Copy `.env.example` to `.env`, then set your Gmail account and Google App Password:

```text
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
MAIL_USERNAME=your_gmail_address@gmail.com
MAIL_PASSWORD=your_google_app_password_without_spaces
MAIL_FROM_EMAIL=your_gmail_address@gmail.com
MAIL_FROM_NAME=eBarangayHub Notifications
```

Use a Google App Password, not your normal Gmail password. Remove spaces from the App Password before placing it in `.env`.

Email activity is logged here:

```text
runtime/logs/notifications.log
```

## AI Assistant Foundation

The assistant works without a real AI key because the rule-based fallback remains active. To prepare real AI testing, add these optional values to `.env`:

```text
AI_ENABLED=false
AI_PROVIDER=openai
AI_API_KEY=
AI_MODEL=
AI_TIMEOUT=30
AI_MAX_TOKENS=500
AI_TEMPERATURE=0.2
AI_ASSISTANT_ENABLED=true
AI_REPORT_SUMMARIES_ENABLED=true
AI_LOG_VERBOSE=false
AI_MAX_ASSISTANT_CHARS=1200
AI_MAX_REPORT_SUMMARY_CHARS=700
```

Keep `AI_ENABLED=false` for normal local demos. When AI is enabled and configured, the server calls the configured provider from PHP only and uses AI as the primary answer path. If AI is disabled, missing, unsupported, times out, out of scope, or fails output checks, the assistant automatically falls back to the existing rule-based answers.

`AI_ASSISTANT_ENABLED=true` controls whether the assistant may use the AI provider. `AI_REPORT_SUMMARIES_ENABLED=true` controls admin report summaries. Report summaries use compact metrics that PHP already computed, not raw tables or uploaded files. If AI is disabled or fails, the report pages still show deterministic local fallback summaries.

`AI_LOG_VERBOSE=false` keeps AI logs quieter during normal local demos. The max character settings keep assistant replies and report summaries concise before they reach the UI.

AI activity is logged here:

```text
runtime/logs/ai.log
```

V3 currently uses AI for assistant answers and admin report summaries. It does not add chat history, uploaded-file retrieval, embeddings, autonomous actions, or AI-generated analysis over private files.

## Maintenance Notes

- `.env` is intentionally gitignored. Keep Gmail credentials out of GitHub.
- Keep AI API keys in `.env` only. Never place them in views, JavaScript, or committed files.
- `vendor/` is intentionally gitignored. Run `composer install` after cloning.
- `node_modules/` is intentionally gitignored. Run `npm install` after cloning.
- `public/assets/js/chart.umd.js` is a local Chart.js asset for offline demos.
- `public/assets/css/output.css` is generated from `resources/css/input.css`.

## Quick Troubleshooting

- Blank page or route issue: restart the local server with `npm run serve`.
- Missing database table: import `database/full_database_rebuild.sql` into phpMyAdmin.
- Tailwind styles missing: run `npm run build`.
- Email not received: check `runtime/logs/notifications.log`.
- Email log says SMTP config is incomplete: fill `MAIL_USERNAME`, `MAIL_PASSWORD`, and `MAIL_FROM_EMAIL` in `.env`.
- Email log says SMTP send failed: confirm Gmail 2-Step Verification is enabled and the App Password is correct.
- Assistant stays in fallback mode: this is expected when `AI_ENABLED=false` or AI config is incomplete.
- AI not responding: check `runtime/logs/ai.log`, then confirm `AI_ENABLED`, `AI_PROVIDER`, `AI_API_KEY`, and `AI_MODEL`.
- Charts not showing: confirm `public/assets/js/chart.umd.js` exists.
