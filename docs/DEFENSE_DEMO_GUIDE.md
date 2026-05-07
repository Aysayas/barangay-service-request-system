# eBarangayHub Defense Demo Guide

This guide gives a smooth presentation order for a classroom defense or guided handoff.

## Demo Accounts

After importing `database/full_database_rebuild.sql`, the README lists:

- Admin: `admin@barangay.local` / `password123`
- Staff: `staff@barangay.local` / `password123`
- Resident: create a resident account from the register page

Use local-only credentials for defense. Replace these credentials before any real use.

## Recommended Demo Flow

### 1. Public Landing And Purpose

Open `http://localhost:3000/`.

Use clean URLs throughout the walkthrough. For example, open `/admin/reports`, not `/index.php/admin/reports`, when the app is running through `public/router.php`.

Presenter notes:

- Introduce eBarangayHub as a centralized barangay service platform.
- Point out the target users: residents, barangay staff, and administrators.
- Mention that the app is server-rendered PHP using LavaLust, MySQL, Tailwind, and local assets.

### 2. Resident Flow

1. Register a new resident or log in as an existing resident.
2. Open the resident dashboard.
3. Show next-action cards and active workflow counts.
4. Browse available services.
5. Create a new service request.
6. Upload any required attachment.
7. Open the request detail page.
8. Show status timeline, payment proof panel, and final document area.
9. Download the resident request PDF.
10. Submit a complaint with a clear description and optional evidence.
11. Open complaint detail and show complaint timeline.
12. Download the resident complaint PDF.

Presenter notes:

- The resident experience focuses on request tracking, complaint tracking, payment proof submission, and final document access.
- The system keeps residents inside their own records.

### 3. Staff Flow

1. Log out and log in as staff.
2. Open the staff dashboard.
3. Show priority work: new requests, payment proofs awaiting review, and complaints awaiting action.
4. Open the staff request queue.
5. Open the resident request created earlier.
6. Inspect attachments.
7. Review payment proof if available.
8. Verify or reject payment proof with remarks.
9. Update request status through the workflow.
10. Upload or replace a final document when allowed.
11. Download the staff request case PDF.
12. Open the complaint queue and process a complaint.

Presenter notes:

- Staff pages focus on operational processing.
- Status transitions are guided so paid requests cannot move forward before payment proof is verified.
- Payment is handled as a proof upload and staff review workflow, not as a live online payment gateway.

### 4. Admin Flow

1. Log out and log in as admin.
2. Open the admin dashboard.
3. Show operational summary cards.
4. Show management modules:
   - users
   - services
   - announcements
   - community posts
   - audit logs
5. Open the admin reports dashboard.
6. Show charts.
7. Export summary CSV.
8. Download the report summary PDF.
9. Download the visual analytics PDF.
10. Open request, payment, complaint, and community report pages.
11. Apply filters.
12. Download filtered CSV and filtered PDF reports.
13. Open an admin request detail page and download the admin request case PDF.

Presenter notes:

- Admin tools focus on oversight, reporting, records, and management.
- CSV exports support spreadsheet analysis.
- PDFs support printable summaries and case records.

### 5. Assistant And Fallbacks

1. Open the assistant page.
2. Ask a basic app-related question.
3. Explain that the assistant uses safe fallback responses if AI is disabled or incomplete.
4. Open reports and show AI-assisted or fallback report summaries.

Presenter notes:

- AI is optional and does not block core workflows.
- If AI credentials are absent, the app remains usable through local fallback behavior.

### 6. Mobile Check

Optional but useful:

1. Run:

```powershell
php -S 0.0.0.0:3000 -t public public/router.php
```

2. Find the PC IPv4 address with:

```powershell
ipconfig
```

3. Open `http://YOUR_PC_IP:3000` on a phone connected to the same Wi-Fi.
4. Show homepage, login, dashboard, and a workflow detail page.

Phone URLs should also stay clean, such as `http://YOUR_PC_IP:3000/admin/reports`.

## Presenter Walkthrough Script

Use this as a simple speaking guide.

### Problem

Barangay services often require residents to visit offices repeatedly, follow up manually, and track documents through informal communication. Staff and administrators also need organized queues, records, reports, and proof of actions.

### Objective

eBarangayHub centralizes resident service requests, complaints, community updates, reports, and administrative records into one role-based web platform.

### Target Users

The system serves three major roles:

- Residents submit and track requests and complaints.
- Staff process requests, review payment proof, upload final documents, and handle complaints.
- Administrators manage users, services, community content, reports, exports, PDFs, and audit logs.

### Main Workflow

A resident submits a service request. Staff review the request, check attachments, verify payment proof if required, update the request status, and upload the final document. The resident can then track the status and download the final document when it is released.

### Technologies

The system uses LavaLust PHP, MySQL, XAMPP, Tailwind CSS, Composer packages, PHPMailer for Gmail SMTP notifications, Dompdf for PDFs, local Chart.js assets, and optional AI support with fallback safeguards.

### Security And Access Control

The app uses authentication, role-based middleware, and ownership checks. Residents can only view their own records. Staff and admin features are protected by role-specific routes.

### Reports And PDFs

Admins can view reports, charts, CSV exports, filtered report PDFs, case PDFs, and a visual analytics PDF. PDFs are generated server-side through Dompdf.

### AI Feature

The assistant and report summaries can use AI when configured. If AI is disabled or unavailable, the system falls back safely and continues working.

### Limitations

The app is currently prepared for local XAMPP use. Payment is handled as proof upload and staff review, not as a live payment gateway. AI and Gmail SMTP depend on proper credentials. The app is responsive, but it is not a native mobile app or PWA.

### Future Improvements

Future work can include live payment gateway integration, production deployment hardening, richer analytics, native mobile or PWA support, and expanded notification channels.
