# eBarangayHub Routes Reference

Use this as a quick defense and handoff guide for the main web routes. The local development server should be started with:

```powershell
php -S 0.0.0.0:3000 -t public public/router.php
```

When using this command, open clean URLs such as `/admin/reports`. Do not add `/index.php` to the URL.

## Public Routes

| Path | Purpose | Access | Controller |
| --- | --- | --- | --- |
| `/` | Homepage | Public | `Welcome::index` |
| `/community` | Public community feed | Public | `Community::index` |
| `/community/{slug}` | Public community detail | Public | `Community::show` |
| `/assistant` | Assistant page | Public page | `Assistant::index` |
| `/assistant/ask` | Assistant response endpoint | Public page action | `Assistant::ask` |

## Authentication Routes

| Path | Purpose | Access | Controller |
| --- | --- | --- | --- |
| `/login` | Login form and submission | Guest only | `Auth::loginForm`, `Auth::login` |
| `/register` | Resident registration form and submission | Guest only | `Auth::registerForm`, `Auth::register` |
| `/logout` | End signed-in session | Authenticated | `Auth::logout` |
| `/dashboard` | Role-aware dashboard redirect | Authenticated | `Dashboard::index` |

## Resident Routes

| Path | Purpose | Access | Controller |
| --- | --- | --- | --- |
| `/resident/dashboard` | Resident dashboard | Resident | `Dashboard::resident` |
| `/resident/services` | Available services | Resident | `ResidentRequests::services` |
| `/resident/requests` | Resident request history | Resident | `ResidentRequests::index` |
| `/resident/requests/create` | Create request | Resident | `ResidentRequests::create` |
| `/resident/requests/{id}` | Request details | Resident owner | `ResidentRequests::show` |
| `/resident/requests/{id}/pdf` | Request PDF summary | Resident owner | `ResidentRequests::pdf` |
| `/resident/requests/payment/{id}` | Payment proof form | Resident owner | `Payments::residentForm` |
| `/resident/requests/download-final-document/{id}` | Final document download | Resident owner | `FinalDocuments::residentDownload` |
| `/resident/complaints` | Complaint history | Resident | `ResidentComplaints::index` |
| `/resident/complaints/create` | Create complaint | Resident | `ResidentComplaints::create` |
| `/resident/complaints/{id}` | Complaint details | Resident owner | `ResidentComplaints::show` |
| `/resident/complaints/{id}/pdf` | Complaint PDF summary | Resident owner | `ResidentComplaints::pdf` |

## Staff Routes

| Path | Purpose | Access | Controller |
| --- | --- | --- | --- |
| `/staff/dashboard` | Staff dashboard | Staff | `Dashboard::staff` |
| `/staff/requests` | Request queue | Staff | `StaffRequests::index` |
| `/staff/requests/{id}` | Request case details | Staff | `StaffRequests::show` |
| `/staff/requests/{id}/pdf` | Request case PDF | Staff | `StaffRequests::pdf` |
| `/staff/requests/payment-proof/{payment_id}` | Payment proof file | Staff | `Payments::staffProof` |
| `/staff/requests/final-document/{id}` | Final document file | Staff | `FinalDocuments::staffDownload` |
| `/staff/complaints` | Complaint queue | Staff | `StaffComplaints::index` |
| `/staff/complaints/{id}` | Complaint details | Staff | `StaffComplaints::show` |

## Admin Routes

| Path | Purpose | Access | Controller |
| --- | --- | --- | --- |
| `/admin/dashboard` | Admin dashboard | Admin | `Dashboard::admin` |
| `/admin/requests` | Request oversight | Admin | `AdminRequests::index` |
| `/admin/requests/{id}` | Request case details | Admin | `AdminRequests::show` |
| `/admin/requests/{id}/pdf` | Request case PDF | Admin | `AdminRequests::pdf` |
| `/admin/complaints` | Complaint oversight | Admin | `AdminComplaints::index` |
| `/admin/complaints/{id}` | Complaint details | Admin | `AdminComplaints::show` |
| `/admin/users` | User management | Admin | `AdminUsers::index` |
| `/admin/services` | Service management | Admin | `AdminServices::index` |
| `/admin/announcements` | Announcement management | Admin | `AdminAnnouncements::index` |
| `/admin/community` | Community content management | Admin | `AdminCommunity::index` |
| `/admin/audit-logs` | Operational activity log | Admin | `AdminAuditLogs::index` |

## Reports, CSV Exports, And PDF Downloads

| Path | Purpose | Access | Controller |
| --- | --- | --- | --- |
| `/admin/reports` | Reports dashboard | Admin | `Reports::index` |
| `/admin/reports/pdf` | Summary PDF | Admin | `Reports::pdf` |
| `/admin/reports/visual-pdf` | Visual analytics PDF | Admin | `Reports::visualPdf` |
| `/admin/reports/export` | Summary CSV export | Admin | `Reports::exportSummary` |
| `/admin/reports/requests` | Request report | Admin | `Reports::requests` |
| `/admin/reports/requests/pdf` | Filtered request report PDF | Admin | `Reports::requestPdf` |
| `/admin/reports/requests/export` | Filtered request CSV export | Admin | `Reports::exportRequests` |
| `/admin/reports/payments` | Payment proof report | Admin | `Reports::payments` |
| `/admin/reports/payments/pdf` | Filtered payment report PDF | Admin | `Reports::paymentPdf` |
| `/admin/reports/payments/export` | Filtered payment CSV export | Admin | `Reports::exportPayments` |
| `/admin/reports/complaints` | Complaint report | Admin | `Reports::complaints` |
| `/admin/reports/complaints/pdf` | Filtered complaint report PDF | Admin | `Reports::complaintPdf` |
| `/admin/reports/complaints/export` | Filtered complaint CSV export | Admin | `Reports::exportComplaints` |
| `/admin/reports/community` | Community report | Admin | `Reports::community` |
| `/admin/reports/community/pdf` | Filtered community report PDF | Admin | `Reports::communityPdf` |
| `/admin/reports/community/export` | Filtered community CSV export | Admin | `Reports::exportCommunity` |

## Clean URL Notes

- Correct local format: `http://localhost:3000/admin/reports`
- Correct phone format: `http://YOUR_PC_IP:3000/admin/reports`
- Compatibility URLs like `/index.php/admin/reports` are normalized by `public/router.php` during local PHP-server use.
- Keep specific PDF/export routes above dynamic detail routes so files are not swallowed by `{id}` routes.
