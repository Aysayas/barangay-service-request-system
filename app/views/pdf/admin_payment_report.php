<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Report</title>
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
        .muted { color: #607D8B; }
        table { border-collapse: collapse; margin-bottom: 10px; width: 100%; }
        th, td { border: 1px solid #D9E2EC; padding: 6px 7px; text-align: left; vertical-align: top; }
        th { background: #EEF2F7; color: #263238; font-weight: bold; }
        .metric th { width: 35%; }
        .rows { font-size: 9px; }
        .badge { background: #EEF2F7; border: 1px solid #CBD5E1; border-radius: 4px; display: inline-block; font-size: 9px; font-weight: bold; padding: 2px 5px; }
        .section { page-break-inside: avoid; }
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
                    <p class="brand-title">Payment Report</p>
                    <p class="brand-subtitle">Centralized Barangay Services, Reports, and Community Access</p>
                </div>
            </div>
            <h1>Filtered Payment Proof Report</h1>
            <p class="muted">Generated on <?= e(date('M d, Y h:i A', strtotime($generated_at))); ?></p>
        </div>

        <div class="section">
            <h2>Filter Context</h2>
            <table class="metric">
                <?php foreach ($filter_context as $filter): ?>
                    <tr>
                        <th><?= e($filter['label']); ?></th>
                        <td><?= e($filter['value']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="section">
            <h2>Summary Metrics</h2>
            <table class="metric">
                <tr><th>Payment Records</th><td><?= e($summary['total_payments']); ?></td></tr>
                <tr><th>Awaiting Payment Proof</th><td><?= e($summary['pending_payment_count']); ?></td></tr>
                <tr><th>Pending Review</th><td><?= e($summary['payment_submitted_count']); ?></td></tr>
                <tr><th>Verified</th><td><?= e($summary['payment_verified_count']); ?></td></tr>
                <tr><th>Rejected</th><td><?= e($summary['payment_rejected_count']); ?></td></tr>
                <tr><th>Expected Amount</th><td><?= e(format_money($summary['expected_amount'])); ?></td></tr>
                <tr><th>Verified Amount</th><td><?= e(format_money($summary['verified_amount'])); ?></td></tr>
            </table>
        </div>

        <div class="section">
            <h2>Payment Proof Rows</h2>
            <?php if (empty($rows)): ?>
                <p class="muted">No payment proof records match the selected report filters.</p>
            <?php else: ?>
                <table class="rows">
                    <thead>
                        <tr>
                            <th>Request</th>
                            <th>Resident</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Reviewed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?= e($row['reference_no']); ?></td>
                                <td><?= e($row['resident_name']); ?></td>
                                <td><?= e($row['service_name']); ?></td>
                                <td><?= e(format_money($row['amount'])); ?></td>
                                <td><?= e(payment_method_label($row['payment_method'])); ?></td>
                                <td><span class="badge"><?= e(payment_status_display_label($row['payment_status'])); ?></span></td>
                                <td><?= !empty($row['submitted_at']) ? e(date('M d, Y', strtotime($row['submitted_at']))) : 'Not submitted'; ?></td>
                                <td><?= !empty($row['verified_at']) ? e(date('M d, Y', strtotime($row['verified_at']))) : 'Not reviewed'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="footer">
            PDF rows follow the web report table limit of up to <?= e($row_limit); ?> recent filtered records. Use CSV export for full spreadsheet-style analysis.
        </div>
    </div>
</body>
</html>
