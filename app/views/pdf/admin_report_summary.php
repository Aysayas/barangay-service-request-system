<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Report Summary</title>
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
        table { border-collapse: collapse; margin-bottom: 12px; width: 100%; }
        th, td { border: 1px solid #D9E2EC; padding: 8px 9px; text-align: left; vertical-align: top; }
        th { background: #EEF2F7; color: #263238; font-weight: bold; width: 50%; }
        .summary-box { background: #F8FAFC; border: 1px solid #D9E2EC; padding: 11px; }
        .section { page-break-inside: avoid; }
        .footer { border-top: 1px solid #D9E2EC; color: #607D8B; font-size: 10px; margin-top: 26px; padding-top: 10px; }
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
                    <p class="brand-title">Admin Report Summary</p>
                    <p class="brand-subtitle">Centralized Barangay Services, Reports, and Community Access</p>
                </div>
            </div>
            <h1>Operational Report Summary</h1>
            <p class="muted">Generated on <?= e(date('M d, Y h:i A', strtotime($generated_at))); ?></p>
        </div>

        <?php if (!empty($report_summary['text'])): ?>
            <div class="section">
                <h2><?= e($report_summary['source_label'] ?? 'Report Summary'); ?></h2>
                <div class="summary-box"><?= e($report_summary['text']); ?></div>
            </div>
        <?php endif; ?>

        <div class="section">
            <h2>Request Summary</h2>
            <table>
                <tr>
                    <th>Total Requests</th>
                    <td><?= e($summary['total_requests'] ?? 0); ?></td>
                </tr>
                <tr>
                    <th>Pending Requests</th>
                    <td><?= e($summary['pending_requests'] ?? 0); ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2>Payment Proof Summary</h2>
            <table>
                <tr>
                    <th>Total Payment Records</th>
                    <td><?= e($summary['total_payments'] ?? 0); ?></td>
                </tr>
                <tr>
                    <th>Verified Payment Proofs</th>
                    <td><?= e($summary['verified_payments'] ?? 0); ?></td>
                </tr>
                <tr>
                    <th>Verified Amount</th>
                    <td><?= e(format_money($summary['verified_amount'] ?? 0)); ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2>Complaint Summary</h2>
            <table>
                <tr>
                    <th>Total Complaints</th>
                    <td><?= e($summary['total_complaints'] ?? 0); ?></td>
                </tr>
                <tr>
                    <th>Open Complaints</th>
                    <td><?= e($summary['open_complaints'] ?? 0); ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2>Community Summary</h2>
            <table>
                <tr>
                    <th>Total Community Posts</th>
                    <td><?= e($summary['total_posts'] ?? 0); ?></td>
                </tr>
                <tr>
                    <th>Published Posts</th>
                    <td><?= e($summary['published_posts'] ?? 0); ?></td>
                </tr>
                <tr>
                    <th>Upcoming Events</th>
                    <td><?= e($summary['upcoming_events'] ?? 0); ?></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            This PDF summarizes the current reports dashboard. Charts and filtered tables remain available in the admin reports area.
        </div>
    </div>
</body>
</html>
