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
- Simulated virtual help assistant for common system questions
- Optional Gmail SMTP email notifications for important workflow events

## Local Setup

1. Place the project in your XAMPP `htdocs` folder.
2. Start Apache and MySQL from XAMPP.
3. Import the SQL files from `database/` in phase order, or use `database/full_database_rebuild.sql` for a clean rebuild.
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

Use a Google App Password, not your normal Gmail password. If PHPMailer is not installed yet, run:

```bash
composer install
```

If Composer is not installed globally on Windows, install Composer first or use a local `composer.phar`, then run:

```bash
php composer.phar install
```
