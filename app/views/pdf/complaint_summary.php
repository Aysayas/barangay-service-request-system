<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Complaint Summary <?= e($complaint['reference_no']); ?></title>
    <style>
        body { color: #263238; font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; line-height: 1.5; margin: 0; }
        .page { padding: 28px; }
        .header { border-bottom: 3px solid #0097A7; margin-bottom: 22px; padding-bottom: 16px; }
        .brand { display: table; width: 100%; }
        .brand-logo { display: table-cell; vertical-align: middle; width: 220px; }
        .brand-logo img { max-height: 54px; max-width: 210px; }
        .brand-text { display: table-cell; text-align: right; vertical-align: middle; }
        .brand-title { color: #0D47A1; font-size: 18px; font-weight: bold; margin: 0; }
        .brand-subtitle { color: #455A64; font-size: 11px; margin: 4px 0 0; }
        h1 { color: #0D47A1; font-size: 22px; margin: 18px 0 4px; }
        h2 { border-bottom: 1px solid #D9E2EC; color: #0D47A1; font-size: 14px; margin: 22px 0 10px; padding-bottom: 5px; }
        .muted { color: #607D8B; }
        .reference { color: #263238; font-size: 13px; font-weight: bold; margin: 0; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #D9E2EC; padding: 8px 9px; text-align: left; vertical-align: top; }
        th { background: #EEF2F7; color: #263238; font-weight: bold; width: 34%; }
        .badge { background: #EEF2F7; border: 1px solid #CBD5E1; border-radius: 4px; display: inline-block; font-size: 11px; font-weight: bold; padding: 3px 7px; }
        .section { page-break-inside: avoid; }
        .text-block { border: 1px solid #D9E2EC; padding: 10px; white-space: pre-line; }
        .footer { border-top: 1px solid #D9E2EC; color: #607D8B; font-size: 10px; margin-top: 26px; padding-top: 10px; }
        ul { margin: 0; padding-left: 18px; }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="brand">
                <div class="brand-logo">
                    <?php if (!empty($brand_logo_data_uri)): ?>
                        <img src="<?= e($brand_logo_data_uri); ?>" alt="eBarangayHub">
                    <?php else: ?>
                        <p class="brand-title">eBarangayHub</p>
                    <?php endif; ?>
                </div>
                <div class="brand-text">
                    <p class="brand-title">Complaint Summary</p>
                    <p class="brand-subtitle">Centralized Barangay Services, Reports, and Community Access</p>
                </div>
            </div>
            <h1>Complaint Record</h1>
            <p class="reference"><?= e($complaint['reference_no']); ?></p>
            <p class="muted">Generated on <?= e(date('M d, Y h:i A', strtotime($generated_at))); ?></p>
        </div>

        <div class="section">
            <h2>Complaint Information</h2>
            <table>
                <tr>
                    <th>Subject</th>
                    <td><?= e($complaint['subject']); ?></td>
                </tr>
                <tr>
                    <th>Category</th>
                    <td><?= e(complaint_category_label($complaint['category'])); ?></td>
                </tr>
                <tr>
                    <th>Priority</th>
                    <td><span class="badge"><?= e(complaint_priority_label($complaint['priority'])); ?></span></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><span class="badge"><?= e(complaint_status_display_label($complaint['status'])); ?></span></td>
                </tr>
                <tr>
                    <th>Submitted Date</th>
                    <td><?= e(date('M d, Y h:i A', strtotime($complaint['created_at']))); ?></td>
                </tr>
                <tr>
                    <th>Incident Date</th>
                    <td><?= !empty($complaint['incident_date']) ? e(date('M d, Y', strtotime($complaint['incident_date']))) : 'Not provided'; ?></td>
                </tr>
                <tr>
                    <th>Location</th>
                    <td><?= e($complaint['location']); ?></td>
                </tr>
                <tr>
                    <th>Assigned Staff</th>
                    <td><?= e($complaint['assigned_to_name'] ?: 'Not assigned yet'); ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2>Resident Information</h2>
            <table>
                <tr>
                    <th>Name</th>
                    <td><?= e($resident['name'] ?? $complaint['complainant_name'] ?? 'Resident'); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= e($resident['email'] ?? $complaint['complainant_email'] ?? 'Not provided'); ?></td>
                </tr>
                <tr>
                    <th>Contact</th>
                    <td><?= e($complaint['complainant_contact'] ?: 'Not provided'); ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2>Description</h2>
            <div class="text-block"><?= e($complaint['description']); ?></div>
        </div>

        <?php if (!empty($complaint['staff_notes'])): ?>
            <div class="section">
                <h2>Staff Notes</h2>
                <div class="text-block"><?= e($complaint['staff_notes']); ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($complaint['resolution_notes'])): ?>
            <div class="section">
                <h2>Resolution Notes</h2>
                <div class="text-block"><?= e($complaint['resolution_notes']); ?></div>
            </div>
        <?php endif; ?>

        <div class="section">
            <h2>Evidence Attachments</h2>
            <?php if (empty($attachments)): ?>
                <p class="muted">No evidence files were uploaded for this complaint.</p>
            <?php else: ?>
                <p class="muted"><?= e(count($attachments)); ?> evidence file(s) recorded.</p>
                <ul>
                    <?php foreach ($attachments as $attachment): ?>
                        <li><?= e($attachment['original_name']); ?> (<?= e(format_file_size($attachment['file_size'])); ?>)</li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="footer">
            This PDF is a generated summary from eBarangayHub. Use the online record for the latest complaint status and staff updates.
        </div>
    </div>
</body>
</html>
