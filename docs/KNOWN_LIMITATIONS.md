# eBarangayHub Known Limitations

These limitations are intentional and should be explained honestly during defense or handoff.

## Local Deployment Scope

The project is currently prepared for local XAMPP use, classroom defense, and guided handoff. It is not yet hardened as a production shared-hosting or cloud deployment package.

## Payment Workflow

The payment workflow is based on resident payment proof upload and staff review. It is not connected to a live payment gateway.

## AI Dependency

AI-assisted replies and report summaries depend on valid provider configuration. If AI is disabled or incomplete, the app uses fallback responses and summaries.

## Gmail SMTP Dependency

Email notifications depend on valid Gmail SMTP credentials and a Google App Password. If mail settings are blank or incorrect, the app skips email sending safely and continues the main workflow.

## PDF Charts

Report PDFs use tables and static HTML bars. They do not render Chart.js/canvas charts inside PDFs. The interactive charts remain available on the web reports dashboard.

## Mobile Behavior

The app is responsive and mobile-friendly, but it is not a native mobile app or PWA. Dense admin/report tables may still use horizontal scrolling on small screens to preserve full data visibility.

## Runtime Uploads

Uploaded files live under `runtime/uploads/`. Moving the project to another machine requires copying runtime upload files if old uploaded records should remain downloadable.

## Configuration Split

Some local settings still live outside `.env`, especially database credentials in `app/config/database.php`. This should be reviewed before handoff or deployment.

## Seeded Local Accounts

Seeded local accounts are for defense and local evaluation only. Replace credentials before any real use.

