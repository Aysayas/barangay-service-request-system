<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php
$payment_status = !empty($payment['payment_status']) ? $payment['payment_status'] : 'pending_payment';
$final_document_status = 'Not Available Yet';

if (!empty($final_document) && !empty($final_document_exists) && !empty($can_download_final_document)) {
    $final_document_status = 'Ready for Download';
} elseif (!empty($final_document) && !empty($final_document_exists)) {
    $final_document_status = 'Uploaded, Waiting for Release';
} elseif (!empty($final_document)) {
    $final_document_status = 'Uploaded, File Unavailable';
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Request Summary <?= e($request['reference_no']); ?></title>
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
                    <p class="brand-title">Request Summary</p>
                    <p class="brand-subtitle">Centralized Barangay Services, Reports, and Community Access</p>
                </div>
            </div>
            <h1>Service Request Record</h1>
            <p class="reference"><?= e($request['reference_no']); ?></p>
            <p class="muted">Generated on <?= e(date('M d, Y h:i A', strtotime($generated_at))); ?></p>
        </div>

        <div class="section">
            <h2>Request Information</h2>
            <table>
                <tr>
                    <th>Service</th>
                    <td><?= e($request['service_name']); ?></td>
                </tr>
                <tr>
                    <th>Submitted Date</th>
                    <td><?= e(date('M d, Y h:i A', strtotime($request['created_at']))); ?></td>
                </tr>
                <tr>
                    <th>Current Status</th>
                    <td><span class="badge"><?= e(request_status_display_label($request['status'])); ?></span></td>
                </tr>
                <tr>
                    <th>Service Fee</th>
                    <td><?= e(format_money($request['fee'])); ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2>Resident Information</h2>
            <table>
                <tr>
                    <th>Name</th>
                    <td><?= e($resident['name'] ?? 'Resident'); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= e($resident['email'] ?? 'Not provided'); ?></td>
                </tr>
            </table>
        </div>

        <?php if ((int) $request['requires_payment'] === 1): ?>
            <div class="section">
                <h2>Payment Proof</h2>
                <table>
                    <tr>
                        <th>Status</th>
                        <td><span class="badge"><?= e(payment_status_display_label($payment_status)); ?></span></td>
                    </tr>
                    <tr>
                        <th>Method</th>
                        <td><?= e(payment_method_label($payment['payment_method'] ?? null)); ?></td>
                    </tr>
                    <tr>
                        <th>Reference Number</th>
                        <td><?= e($payment['reference_number'] ?? 'Not submitted yet'); ?></td>
                    </tr>
                    <tr>
                        <th>Submitted Date</th>
                        <td><?= !empty($payment['submitted_at']) ? e(date('M d, Y h:i A', strtotime($payment['submitted_at']))) : 'Not submitted yet'; ?></td>
                    </tr>
                </table>
            </div>
        <?php endif; ?>

        <div class="section">
            <h2>Final Document</h2>
            <table>
                <tr>
                    <th>Status</th>
                    <td><?= e($final_document_status); ?></td>
                </tr>
                <tr>
                    <th>File Name</th>
                    <td><?= !empty($final_document['original_name']) ? e($final_document['original_name']) : 'Not available yet'; ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2>Purpose</h2>
            <div class="text-block"><?= e($request['purpose']); ?></div>
        </div>

        <?php if (!empty($request['remarks'])): ?>
            <div class="section">
                <h2>Resident Remarks</h2>
                <div class="text-block"><?= e($request['remarks']); ?></div>
            </div>
        <?php endif; ?>

        <div class="section">
            <h2>Submitted Attachments</h2>
            <?php if (empty($attachments)): ?>
                <p class="muted">No attachments were recorded for this request.</p>
            <?php else: ?>
                <p class="muted"><?= e(count($attachments)); ?> attachment(s) recorded.</p>
                <ul>
                    <?php foreach ($attachments as $attachment): ?>
                        <li><?= e($attachment['original_name']); ?> (<?= e(format_file_size($attachment['file_size'])); ?>)</li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="footer">
            This PDF is a generated summary from eBarangayHub. Use the online record for the latest status and downloadable final document.
        </div>
    </div>
</body>
</html>
