<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Visual Analytics Report</title>
    <style>
        body { color: #263238; font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; line-height: 1.45; margin: 0; }
        .page { padding: 24px; }
        .header { border-bottom: 3px solid #0097A7; margin-bottom: 18px; padding-bottom: 14px; }
        .brand { display: table; width: 100%; }
        .brand-logo { display: table-cell; vertical-align: middle; width: 220px; }
        .brand-logo img { max-height: 50px; max-width: 210px; }
        .brand-text { display: table-cell; text-align: right; vertical-align: middle; }
        .brand-title { color: #0D47A1; font-size: 17px; font-weight: bold; margin: 0; }
        .brand-subtitle { color: #455A64; font-size: 10px; margin: 4px 0 0; }
        h1 { color: #0D47A1; font-size: 21px; margin: 16px 0 4px; }
        h2 { border-bottom: 1px solid #D9E2EC; color: #0D47A1; font-size: 13px; margin: 18px 0 9px; padding-bottom: 5px; }
        h3 { color: #263238; font-size: 12px; margin: 0 0 3px; }
        .muted { color: #607D8B; }
        .summary-grid { margin-bottom: 12px; width: 100%; }
        .summary-grid td { border: 1px solid #D9E2EC; padding: 9px; width: 25%; }
        .summary-label { color: #607D8B; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .summary-value { color: #0D47A1; display: block; font-size: 18px; font-weight: bold; margin-top: 2px; }
        .section { page-break-inside: avoid; }
        .distribution { border: 1px solid #D9E2EC; margin-bottom: 14px; padding: 10px; page-break-inside: avoid; }
        .distribution-desc { color: #607D8B; margin: 0 0 9px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #D9E2EC; padding: 6px 7px; text-align: left; vertical-align: middle; }
        th { background: #EEF2F7; color: #263238; font-weight: bold; }
        .label-col { width: 32%; }
        .count-col { text-align: right; width: 12%; }
        .percent-col { text-align: right; width: 12%; }
        .bar-cell { width: 44%; }
        .bar-track { background: #EEF2F7; border: 1px solid #D9E2EC; height: 12px; width: 100%; }
        .bar-fill { background: #0097A7; height: 12px; }
        .empty { background: #F8FAFC; border: 1px solid #D9E2EC; color: #607D8B; padding: 9px; }
        .footer { border-top: 1px solid #D9E2EC; color: #607D8B; font-size: 9px; margin-top: 22px; padding-top: 9px; }
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
                    <p class="brand-title">Visual Analytics Report</p>
                    <p class="brand-subtitle">Centralized Barangay Services, Reports, and Community Access</p>
                </div>
            </div>
            <h1>Admin Visual Analytics Report</h1>
            <p class="muted">Generated on <?= e(date('M d, Y h:i A', strtotime($generated_at))); ?></p>
        </div>

        <div class="section">
            <h2>Overall Summary</h2>
            <table class="summary-grid">
                <tr>
                    <td>
                        <span class="summary-label">Total Requests</span>
                        <span class="summary-value"><?= e($summary['total_requests'] ?? 0); ?></span>
                    </td>
                    <td>
                        <span class="summary-label">Payment Proof Records</span>
                        <span class="summary-value"><?= e($summary['total_payments'] ?? 0); ?></span>
                    </td>
                    <td>
                        <span class="summary-label">Total Complaints</span>
                        <span class="summary-value"><?= e($summary['total_complaints'] ?? 0); ?></span>
                    </td>
                    <td>
                        <span class="summary-label">Community Posts</span>
                        <span class="summary-value"><?= e($summary['total_posts'] ?? 0); ?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="summary-label">Pending Requests</span>
                        <span class="summary-value"><?= e($summary['pending_requests'] ?? 0); ?></span>
                    </td>
                    <td>
                        <span class="summary-label">Verified Proofs</span>
                        <span class="summary-value"><?= e($summary['verified_payments'] ?? 0); ?></span>
                    </td>
                    <td>
                        <span class="summary-label">Open Complaints</span>
                        <span class="summary-value"><?= e($summary['open_complaints'] ?? 0); ?></span>
                    </td>
                    <td>
                        <span class="summary-label">Published Posts</span>
                        <span class="summary-value"><?= e($summary['published_posts'] ?? 0); ?></span>
                    </td>
                </tr>
            </table>
        </div>

        <?php foreach ($distributions as $distribution): ?>
            <div class="distribution">
                <h3><?= e($distribution['title']); ?></h3>
                <p class="distribution-desc"><?= e($distribution['description']); ?></p>

                <?php if (empty($distribution['rows']['total'])): ?>
                    <div class="empty">No data is available for this distribution yet.</div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th class="label-col">Label</th>
                                <th class="count-col">Count</th>
                                <th class="percent-col">Percent</th>
                                <th class="bar-cell">Visual Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($distribution['rows']['rows'] as $row): ?>
                                <tr>
                                    <td class="label-col"><?= e($row['label']); ?></td>
                                    <td class="count-col"><?= e($row['count']); ?></td>
                                    <td class="percent-col"><?= e(number_format((float) $row['percent'], 1)); ?>%</td>
                                    <td class="bar-cell">
                                        <div class="bar-track">
                                            <div class="bar-fill" style="width: <?= e(number_format((float) $row['bar_width'], 1)); ?>%;"></div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="footer">
            This PDF uses report chart data rendered as static HTML bars for reliable document output. Interactive charts remain available in the admin reports dashboard.
        </div>
    </div>
</body>
</html>
