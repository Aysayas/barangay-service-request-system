# eBarangayHub Final QA Checklist

Use this checklist before the final defense, handoff, or local submission.

## Environment Readiness

- [ ] XAMPP Apache is running.
- [ ] XAMPP MySQL is running.
- [ ] `database/full_database_rebuild.sql` imports cleanly in phpMyAdmin.
- [ ] `composer install` has been run.
- [ ] `npm install` has been run.
- [ ] `npm run build` or `npm.cmd run build` completes.
- [ ] `.env` exists if Gmail SMTP or AI will be demonstrated.
- [ ] `runtime/logs/` is writable.
- [ ] `runtime/uploads/` subfolders are writable.
- [ ] Local app opens at `http://localhost:3000/`.
- [ ] Clean URLs work without `/index.php`, such as `http://localhost:3000/admin/reports`.
- [ ] Compatibility URLs like `/index.php/admin/reports` redirect to `/admin/reports` when using `public/router.php`.

## Authentication And Access

- [ ] Resident registration works.
- [ ] Login works for resident, staff, and admin accounts.
- [ ] Logout works.
- [ ] Guest users cannot open protected resident pages.
- [ ] Guest users cannot open protected staff pages.
- [ ] Guest users cannot open protected admin pages.
- [ ] Resident users cannot open staff/admin URLs.
- [ ] Staff users cannot open admin-only report and management URLs.

## Resident Workflow

- [ ] Resident dashboard loads and shows next actions.
- [ ] Resident can browse services.
- [ ] Resident can create a service request.
- [ ] Required fields and validation messages are clear.
- [ ] Attachments upload when allowed.
- [ ] Request history shows the new request.
- [ ] Request detail shows status timeline.
- [ ] Payment proof form opens for paid services.
- [ ] Payment proof upload works with a valid file.
- [ ] Payment proof status updates after staff review.
- [ ] Rejected payment proof shows staff remarks clearly to the resident.
- [ ] Verified payment proof allows the paid request to continue toward final document release.
- [ ] Final document download appears only when allowed.
- [ ] Resident request PDF downloads.
- [ ] Resident can submit a complaint.
- [ ] Complaint evidence upload works when provided.
- [ ] Complaint detail shows complaint status timeline.
- [ ] Resident complaint PDF downloads.

## Staff Workflow

- [ ] Staff dashboard loads and shows priority work.
- [ ] Staff request queue search and filters work.
- [ ] Staff can open request details.
- [ ] Staff can inspect request attachments.
- [ ] Staff can review payment proof.
- [ ] Staff can reject payment proof with remarks.
- [ ] Staff can verify payment proof.
- [ ] Staff cannot approve paid requests before payment proof is verified.
- [ ] Staff payment proof decisions use clear labels: Pending Review, Verified, and Rejected.
- [ ] Staff can update request status through valid transitions.
- [ ] Staff can upload or replace final documents when allowed.
- [ ] Staff request case PDF downloads.
- [ ] Staff complaint queue search and filters work.
- [ ] Staff can open complaint details.
- [ ] Staff can update complaint status, priority, assignment, notes, and resolution fields where allowed.

## Admin Workflow

- [ ] Admin dashboard loads and shows operational summary.
- [ ] Admin users list/search/filter works.
- [ ] Admin can create/edit/toggle users.
- [ ] Admin services list works.
- [ ] Admin can create/edit/toggle services.
- [ ] Admin announcements list works.
- [ ] Admin can create/edit/toggle announcements.
- [ ] Admin community list works.
- [ ] Admin can create/edit/toggle/feature community posts.
- [ ] Admin request oversight pages load.
- [ ] Admin request case PDF downloads.
- [ ] Admin complaint oversight pages load.
- [ ] Admin audit logs load and filters work.

## Reports, Exports, And PDFs

- [ ] Admin reports dashboard loads.
- [ ] Web charts render from local `public/assets/js/chart.umd.js`.
- [ ] Report summary PDF downloads.
- [ ] Visual analytics PDF downloads.
- [ ] Request report page filters work.
- [ ] Request CSV export downloads.
- [ ] Request filtered PDF downloads.
- [ ] Payment report page filters work.
- [ ] Payment CSV export downloads.
- [ ] Payment filtered PDF downloads.
- [ ] Complaint report page filters work.
- [ ] Complaint CSV export downloads.
- [ ] Complaint filtered PDF downloads.
- [ ] Community report page filters work.
- [ ] Community CSV export downloads.
- [ ] Community filtered PDF downloads.
- [ ] CSV files open in Excel or a spreadsheet app with readable UTF-8 text.
- [ ] PDF failures, if any, are logged in `runtime/logs/pdf.log`.

## AI And Notifications

- [ ] Assistant page opens.
- [ ] Assistant returns fallback answers when AI is disabled.
- [ ] Assistant returns AI-assisted answers when AI is enabled and configured.
- [ ] Admin report summaries show AI-assisted or fallback summary state.
- [ ] Gmail SMTP sends notifications when configured.
- [ ] Workflows continue when Gmail SMTP is disabled or incomplete.
- [ ] Notification issues are logged in `runtime/logs/notifications.log`.
- [ ] AI issues are logged in `runtime/logs/ai.log`.

## Mobile And Browser Checks

- [ ] Homepage is usable at 360px, 390px, and 430px widths.
- [ ] Login/register forms are usable on phone widths.
- [ ] Resident dashboard and workflow pages are readable on phone widths.
- [ ] Staff queues and detail pages are readable on phone widths.
- [ ] Admin reports and management tables scroll horizontally where needed.
- [ ] Mobile navigation opens and closes cleanly.
- [ ] Buttons remain easy to tap on phone widths.
- [ ] Same-Wi-Fi phone testing works with `php -S 0.0.0.0:3000 -t public public/router.php`.
- [ ] Phone URLs use clean paths such as `http://YOUR_PC_IP:3000/admin/reports`, not `/index.php/admin/reports`.
