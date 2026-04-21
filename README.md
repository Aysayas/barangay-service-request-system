# eBarangayHub

Centralized Barangay Services, Reports, and Community Access

eBarangayHub is a LavaLust-based multi-role barangay web application built for resident services, staff processing, admin management, complaints, community information, reporting, CSV exports, charts, Gmail SMTP notifications, and a scoped AI-assisted help experience with safe fallbacks.

## Release Status

- Final integrated build after V5
- Main branch-ready project structure
- Intended for local demo, defense, submission, and guided handoff

## Local Use Focus

This final packaging pass is aimed at:

- local XAMPP use
- same-Wi-Fi desktop and phone testing
- classroom demo and defense flow
- student-friendly handoff to another developer or evaluator

This phase does not prepare the project for Netlify, Vercel, or shared-hosting rewrites.

## System Overview

The application centralizes common barangay workflows into one server-rendered PHP platform:

- Residents can register, log in, request services, upload supporting files, submit payment proof for paid services, file complaints, and download final documents when released.
- Staff can review request queues, inspect uploads, verify payments, update statuses, add notes, and process complaints.
- Admins can manage services, users, announcements, community content, complaints, audit logs, reports, exports, and charts.
- The assistant can answer eBarangayHub-specific questions through a rule-based fallback and optional server-side AI integration.

## Core Modules

### Resident

- Resident registration and login
- Resident dashboard
- Service browsing
- Request creation with file uploads
- Request history and detail pages
- Payment proof submission for paid services
- Final document visibility and controlled download
- Complaint submission, evidence upload, and tracking

### Staff

- Staff dashboard
- Request queue, search, and filters
- Request detail review
- Status updates and notes
- Payment proof review
- Final document upload and replacement
- Complaint queue and complaint review workflow

### Admin

- Admin dashboard
- Service management
- User management
- Announcement management
- Community management
- Complaint oversight
- Audit log review
- Report summaries, filters, CSV exports, and charts

### Public / Shared

- Homepage
- Public community pages
- Assistant page
- Shared layouts, flash messages, and role-aware navigation

## Tech Stack

- PHP framework: LavaLust
- PHP dependencies: PHPMailer via Composer
- Database: MySQL
- Local stack: XAMPP + phpMyAdmin
- Styling: Tailwind CSS
- Charts: local Chart.js asset
- Rendering style: server-rendered PHP views
- AI integration style: optional server-side AI calls with safe local fallback

## Project Structure Overview

High-level structure:

```text
app/
  config/          Core config, env loading, database config, middleware map
  controllers/     Resident, staff, admin, assistant, and public controllers
  helpers/         Shared helper functions and display utilities
  libraries/       Assistant, AI, notifications, and other reusable services
  middlewares/     Auth, guest, and role-based route guards
  models/          Database access and reporting models
  views/           Public, resident, staff, admin, auth, and error views

database/
  full_database_rebuild.sql
  phase_01_auth.sql
  phase_02_resident_requests.sql
  phase_03_staff_processing.sql
  phase_04_admin_management.sql
  phase_05_final_documents.sql
  phase_06_simulated_payments.sql
  phase_07_complaints.sql
  phase_08_community_section.sql

public/
  assets/css/output.css   Compiled Tailwind CSS
  assets/js/chart.umd.js  Local Chart.js asset
  router.php              Local PHP server router

resources/
  css/input.css           Tailwind source styles

runtime/
  logs/                   Notification and AI logs
  session/                Session security/runtime files
  uploads/                Resident requests, payment proofs, complaints,
                          final documents, and community uploads
```

## Requirements

Minimum local requirements:

- Windows with XAMPP installed
- PHP compatible with the current LavaLust project setup
- Apache and MySQL running from XAMPP
- phpMyAdmin available
- Composer installed
- Node.js and npm installed

Recommended project location:

```text
C:\xampp\htdocs\barangay_system_webapp\LavaLust
```

## Quick Local Start

If you only need the fastest reliable local setup path, use this order:

1. Copy or clone the project into XAMPP `htdocs`
2. Start Apache and MySQL in XAMPP
3. Run `composer install`
4. Run `npm install`
5. Import `database/full_database_rebuild.sql`
6. Copy `.env.example` to `.env`
7. Update `app/config/database.php` if your MySQL credentials differ from the default local setup
8. Fill Gmail SMTP and AI values only if you want those optional integrations
9. Run `npm run build`
10. Run `npm.cmd run serve`
11. Open `http://localhost:3000/`

## Local Installation

### 1. Place the project inside XAMPP htdocs

Example:

```text
C:\xampp\htdocs\barangay_system_webapp\LavaLust
```

### 2. Start XAMPP services

Start these in XAMPP Control Panel:

- Apache
- MySQL

### 3. Install PHP dependencies

From the project root:

```bash
composer install
```

If Composer is not installed globally on Windows:

```bash
php composer.phar install
```

### 4. Install frontend dependencies

```bash
npm install
```

### 5. Build Tailwind CSS

```bash
npm run build
```

### 6. Start the local application server

```bash
npm run serve
```

If Windows PowerShell blocks `npm`, use:

```bash
npm.cmd run serve
```

The built-in server uses:

```text
http://localhost:3000/
```

## Local Same-Wi-Fi Phone Testing

eBarangayHub is mobile-friendly as a responsive web app. It is not a native mobile app or PWA.

Desktop testing:

- Open `http://localhost:3000/` in your PC browser

Mobile testing on the same Wi-Fi:

1. Connect your phone and PC to the same local network
2. In PowerShell, get your PC IP address:

```bash
ipconfig
```

3. Look for the active IPv4 address, for example:

```text
192.168.1.5
```

4. If you want phone access, run the PHP server so it listens on the network:

```bash
php -S 0.0.0.0:3000 -t public public/router.php
```

5. On the phone browser, open:

```text
http://YOUR_PC_IP:3000
```

Example:

```text
http://192.168.1.5:3000
```

Note:

- `npm run serve` uses `localhost:3000`, which is ideal for desktop testing
- `php -S 0.0.0.0:3000 -t public public/router.php` is more useful when testing from a phone on the same network
- If the phone still cannot connect, check Windows Firewall and confirm the active network is marked as a private network

## Database Setup

### Preferred setup: full rebuild

Use the complete rebuild file for a fresh local environment:

```text
database/full_database_rebuild.sql
```

Default database name in the current config:

```text
barangay_service_request_system
```

The database connection is currently configured in:

```text
app/config/database.php
```

This project does not currently read MySQL credentials from `.env`. If you use a different MySQL username, password, host, port, or database name, update `app/config/database.php`.

### phpMyAdmin import steps

1. Open `http://localhost/phpmyadmin`
2. Create a database named `barangay_service_request_system`
3. Open that database
4. Go to the **Import** tab
5. Choose:

```text
database/full_database_rebuild.sql
```

6. Run the import and wait for success

### Optional phase-by-phase SQL order

Only use this if you specifically need a phased setup:

```text
database/phase_01_auth.sql
database/phase_02_resident_requests.sql
database/phase_03_staff_processing.sql
database/phase_04_admin_management.sql
database/phase_05_final_documents.sql
database/phase_06_simulated_payments.sql
database/phase_07_complaints.sql
database/phase_08_community_section.sql
```

Later phases after these were mostly code, design, AI, reporting, export, and responsiveness work, so they do not add separate SQL files here.

## Environment Setup

Copy the template file:

```text
.env.example -> .env
```

The app loads `.env` through `app/config/config.php`.

### Important note

- `.env` is gitignored
- Keep real credentials out of GitHub
- If `.env` is missing, the app still runs
- Gmail SMTP and AI both degrade safely when left disabled or incomplete

### Required vs optional local configuration

Required for the app to run locally:

- `app/config/database.php`
- `database/full_database_rebuild.sql`
- `composer install`
- `npm install`
- `npm run build`

Optional for extended demo behavior:

- `.env` Gmail SMTP values for live email notifications
- `.env` AI values for AI-assisted assistant replies and report summaries

## Gmail SMTP Setup

The app uses Gmail SMTP through PHPMailer.

Minimum mail fields in `.env`:

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

Notes:

- Use a Google App Password, not your normal Gmail password
- The app continues working if these values are blank
- When mail is not configured, notifications are skipped instead of breaking the main workflow

Notification log path:

```text
runtime/logs/notifications.log
```

## AI Setup

AI is optional. The assistant and report summaries still work with fallback behavior when AI is disabled or incomplete.

Recommended default local setup:

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

Behavior:

- If `AI_ENABLED=false`, the assistant stays in fallback mode
- If AI config is incomplete, the app falls back automatically
- AI failures do not break requests, complaints, reports, or the assistant page
- If AI is disabled for a final defense, the system still demonstrates assistant fallback mode and fallback report summaries safely

AI log path:

```text
runtime/logs/ai.log
```

## Demo / Test Accounts

After importing `database/full_database_rebuild.sql`:

- Admin: `admin@barangay.local` / `password123`
- Staff: `staff@barangay.local` / `password123`
- Resident: create one from the register page

These are intended for local demo and defense use only.

## Runtime and Upload Notes

Runtime content is not fully tracked in Git and must be writable on the target machine.

Important runtime areas:

```text
runtime/logs/
runtime/session/
runtime/uploads/resident_requests/
runtime/uploads/payment_proofs/
runtime/uploads/complaints/
runtime/uploads/final_documents/
runtime/uploads/community/
```

Notes:

- Existing uploads only move with the project if you copy the `runtime/uploads/` content
- A fresh deployment can start with empty upload folders
- The web server must be able to write to these runtime directories

## Useful Commands

Install dependencies:

```bash
composer install
npm install
```

Build CSS:

```bash
npm run build
```

Run local server:

```bash
npm run serve
```

Run local server for same-Wi-Fi phone testing:

```bash
php -S 0.0.0.0:3000 -t public public/router.php
```

Tailwind dev watch mode:

```bash
npm run dev
```

## Local Transfer / Release Readiness Notes

If you need to move the project to another local machine for demo, defense, or handoff, prepare:

- PHP and Apache or another compatible web server
- MySQL database
- Composer dependencies installed
- Node dependencies installed at least once for CSS build
- A valid `public/assets/css/output.css`
- A configured `app/config/database.php`
- A copied `.env` file if Gmail SMTP or AI will be used
- Writable `runtime/` directories
- Uploaded files copied over if preserving old records

Before using it on another local machine, review:

- `app/config/config.php` environment and cookie settings
- `app/config/database.php` database credentials
- `app/config/api.php` JWT and token secrets if the API helper will ever be enabled
- web server document root so it points to the `public/` folder

Local caution notes:

- Review local-only secrets and keys in config files before handing the project to another person
- Use stronger credentials than the seeded demo accounts
- Confirm file write permissions for `runtime/`
- eBarangayHub is a mobile-friendly responsive web app, not a native mobile app or PWA

## Troubleshooting

### XAMPP / MySQL

- Apache or MySQL not starting: restart XAMPP as administrator and check port conflicts
- Database connection failing: verify `app/config/database.php`
- Missing tables or columns: re-import `database/full_database_rebuild.sql`

### Composer / PHP

- PHPMailer missing: run `composer install`
- Duplicate `mysqli` or `openssl` warnings during local CLI checks: review local PHP/XAMPP extension loading; these warnings do not automatically mean the app code is broken

### Tailwind / Frontend

- Styling missing: run `npm install` and `npm run build`
- Local CSS not updating while working: run `npm run dev`
- Charts not showing: confirm `public/assets/js/chart.umd.js` exists

### Email

- No email received: check `runtime/logs/notifications.log`
- SMTP config incomplete: fill `MAIL_USERNAME`, `MAIL_PASSWORD`, and `MAIL_FROM_EMAIL`
- Gmail still not sending: verify 2-Step Verification and use a Google App Password

### AI

- Assistant stays in fallback mode: expected when AI is disabled or incomplete
- AI summaries not appearing: check `runtime/logs/ai.log` and review `.env`
- If AI is left disabled, the app still works through local fallbacks

### Uploads / Runtime

- File access problems: make sure `runtime/uploads/` subfolders exist and are writable
- Missing old uploaded files after moving machines: copy the `runtime/uploads/` content from the old project

### Desktop / Mobile Local Testing

- Phone cannot open the app while PC can: use `php -S 0.0.0.0:3000 -t public public/router.php` instead of `npm run serve`
- Phone still cannot connect: confirm both devices are on the same Wi-Fi and use the correct IPv4 address from `ipconfig`
- PowerShell blocks `npm`: use `npm.cmd run serve`
- App opens on desktop but not on mobile: check Windows Firewall rules for PHP if network access is blocked
- Connection still blocked: confirm the PC is not using a guest or isolated Wi-Fi mode that prevents device-to-device traffic

## Suggested Final Demo / Defense Walkthrough

Use this order for a clean live demo:

1. Register a resident account or log in as an existing resident
2. Open the resident dashboard
3. Create a service request with an attachment
4. Submit payment proof for a paid service if needed
5. Log in as staff
6. Review the request queue, inspect attachments, and verify or reject payment
7. Approve the request and upload the final document
8. Return to the resident account and download the final document
9. Submit a complaint with evidence
10. Log in as staff or admin and process the complaint
11. Open the public community section
12. Open admin reports, charts, and CSV exports
13. Show the assistant answering in fallback mode or AI-assisted mode
14. Show admin management pages for services, users, announcements, community, and audit logs
15. Optional: open the same app on a phone over the same Wi-Fi to show responsive mobile behavior

## What Is Complete

Final integrated scope includes:

- multi-role auth and dashboards
- resident request workflow
- payment proof workflow
- final document workflow
- complaints module
- community module
- admin management tools
- audit logging
- reports, CSV exports, and charts
- Gmail SMTP notifications
- AI assistant foundation and AI-assisted summaries with fallbacks
- full responsive and mobile hardening
- responsive mobile-friendly web experience for phone-sized browsers

## Known Limitations

Intentional current limitations:

- payment flow is simulated, not connected to a live gateway
- email sending depends on valid Gmail SMTP credentials
- AI is optional and still scoped tightly to the application domain
- dense admin/report tables still use horizontal scrolling on small screens where that is safer than hiding data
- this is a responsive web application, not a native mobile app or PWA

## Handoff Summary

For another developer or panel member, the fastest reliable setup path is:

1. clone or copy the project into XAMPP `htdocs`
2. run `composer install`
3. run `npm install`
4. import `database/full_database_rebuild.sql`
5. copy `.env.example` to `.env`
6. fill Gmail SMTP and AI values only if needed
7. run `npm run build`
8. run `npm run serve`
9. open `http://localhost:3000/`

## Final Local Packaging Checklist

Before final submission or classroom defense, confirm:

- `database/full_database_rebuild.sql` imports cleanly
- `composer install` completes without missing dependencies
- `npm install` and `npm run build` complete successfully
- `public/assets/css/output.css` exists
- `runtime/logs/` and `runtime/uploads/` folders are writable
- `.env` is present if Gmail SMTP or AI will be used
- `app/config/database.php` matches the local MySQL setup
- desktop testing works at `http://localhost:3000/`
- optional same-Wi-Fi phone testing works at `http://YOUR_PC_IP:3000/`
