<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php
$statuses = [
    'submitted',
    'under_review',
    'needs_info',
    'approved',
    'ready_for_pickup',
    'released',
];

$current_status = $request['status'];
$payment_status = !empty($payment['payment_status']) ? $payment['payment_status'] : 'pending_payment';
$final_document_exists = !empty($final_document['file_path'])
    && safe_storage_path($final_document['file_path'], 'runtime/uploads/final_documents') !== null;
?>

<section class="workflow-page">
    <div class="workflow-header">
        <div>
            <p class="workflow-kicker">Request Details</p>
            <h1 class="workflow-title"><?= e($request['reference_no']); ?></h1>
            <p class="workflow-subtitle"><?= e($request['service_name']); ?></p>
        </div>
        <a class="btn-secondary" href="<?= site_url('resident/requests'); ?>">Back to Requests</a>
    </div>

    <div class="workflow-detail-grid lg:grid-cols-[0.9fr_1.1fr]">
        <div class="space-y-6">
            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Current Status</h2>
                <div class="mt-3">
                    <span class="status-pill <?= status_badge_class($current_status); ?>">
                        <?= e(status_label($current_status)); ?>
                    </span>
                </div>

                <ol class="mt-5 space-y-3 text-sm">
                    <?php foreach ($statuses as $status): ?>
                        <li class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-md <?= ($status === $current_status) ? 'bg-teal-700' : 'bg-slate-300'; ?>"></span>
                            <span class="<?= ($status === $current_status) ? 'font-semibold text-slate-950' : 'text-slate-600'; ?>">
                                <?= e(status_label($status)); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                    <?php if ($current_status === 'rejected'): ?>
                        <li class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-md bg-rose-700"></span>
                            <span class="font-semibold text-rose-900">Rejected</span>
                        </li>
                    <?php endif; ?>
                </ol>
            </section>

            <?php if ((int) $request['requires_payment'] === 1): ?>
                <section id="payment" class="workflow-card">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">Payment Proof</h2>
                            <p class="mt-1 text-sm text-slate-600">Upload proof only after following the payment instructions provided by the barangay office.</p>
                        </div>
                        <span class="status-pill <?= payment_status_badge_class($payment_status); ?>">
                            <?= e(payment_status_label($payment_status)); ?>
                        </span>
                    </div>

                    <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="font-medium text-slate-800">Required Amount</dt>
                            <dd class="mt-1 text-slate-600"><?= e(format_money($request['fee'])); ?></dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-800">Method</dt>
                            <dd class="mt-1 text-slate-600"><?= e(payment_method_label($payment['payment_method'] ?? null)); ?></dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-800">Reference Number</dt>
                            <dd class="mt-1 text-slate-600"><?= e($payment['reference_number'] ?? 'Not submitted yet'); ?></dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-800">Submitted</dt>
                            <dd class="mt-1 text-slate-600">
                                <?= !empty($payment['submitted_at']) ? e(date('M d, Y h:i A', strtotime($payment['submitted_at']))) : 'Not submitted yet'; ?>
                            </dd>
                        </div>
                    </dl>

                    <?php if (!empty($payment['remarks'])): ?>
                        <div class="mt-4 rounded-md border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900">
                            <p class="font-medium">Payment remarks</p>
                            <p class="mt-1 whitespace-pre-line"><?= e($payment['remarks']); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($payment_status === 'payment_verified'): ?>
                        <p class="mt-4 rounded-md border border-teal-200 bg-teal-50 p-4 text-sm text-teal-950">
                            Your payment proof has been verified by barangay staff.
                        </p>
                    <?php else: ?>
                        <form class="mt-5 space-y-4" method="POST" enctype="multipart/form-data" action="<?= site_url('resident/requests/payment/store/' . $request['id']); ?>">
                            <?php csrf_field(); ?>

                            <div>
                                <label class="form-label" for="payment_method">Payment Method</label>
                                <select class="form-input" id="payment_method" name="payment_method" required>
                                    <option value="">Choose method</option>
                                    <?php foreach ($payment_methods as $method_value => $method_label): ?>
                                        <option value="<?= e($method_value); ?>" <?= (($payment['payment_method'] ?? '') === $method_value) ? 'selected' : ''; ?>>
                                            <?= e($method_label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="form-label" for="reference_number">Reference Number</label>
                                <input class="form-input" id="reference_number" type="text" name="reference_number" value="<?= e($payment['reference_number'] ?? ''); ?>" maxlength="120" required>
                            </div>

                            <div>
                                <label class="form-label" for="payment_proof">Proof of Payment</label>
                                <input class="form-input" id="payment_proof" type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" required>
                                <p class="mt-2 text-xs text-slate-600">
                                    Allowed types: JPG, PNG, PDF. Maximum size: <?= e($max_payment_upload_mb); ?>MB.
                                    Uploading again replaces your previous proof.
                                </p>
                            </div>

                            <button class="btn-primary w-full" type="submit">
                                <?= in_array($payment_status, ['payment_submitted', 'payment_rejected'], true) ? 'Resubmit Payment Proof' : 'Submit Payment Proof'; ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Final Document</h2>
                <?php if (!empty($final_document) && !empty($can_download_final_document) && $final_document_exists): ?>
                    <div class="mt-4 rounded-md border border-teal-200 bg-teal-50 p-4 text-sm text-teal-950">
                        <p class="font-medium">Ready for download</p>
                        <p class="mt-1">
                            <?= e($final_document['original_name']); ?> -
                            <?= e(format_file_size($final_document['file_size'])); ?>
                        </p>
                        <a class="mt-3 inline-flex w-full justify-center rounded-md bg-teal-700 px-3 py-2 text-sm font-semibold text-white hover:bg-teal-800 sm:w-auto" href="<?= site_url('resident/requests/download-final-document/' . $request['id']); ?>">
                            Download Final Document
                        </a>
                    </div>
                <?php elseif (!empty($final_document) && !$final_document_exists): ?>
                    <div class="mt-4 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950">
                        <p class="font-medium">Final document is being checked</p>
                        <p class="mt-1">
                            A document record exists, but the file is not available right now. Please contact barangay staff.
                        </p>
                    </div>
                <?php elseif (!empty($final_document)): ?>
                    <div class="mt-4 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950">
                        <p class="font-medium">Final document uploaded</p>
                        <p class="mt-1">
                            Download becomes available when the request is approved, ready for pickup, or released.
                        </p>
                    </div>
                <?php else: ?>
                    <p class="mt-3 text-sm text-slate-600">
                        No final document is available yet. This will appear after staff approval and release.
                    </p>
                <?php endif; ?>
            </section>
        </div>

        <div class="space-y-6">
            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Request Information</h2>
                <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="font-medium text-slate-800">Service</dt>
                        <dd class="mt-1 text-slate-600"><?= e($request['service_name']); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Submitted</dt>
                        <dd class="mt-1 text-slate-600"><?= e(date('M d, Y h:i A', strtotime($request['created_at']))); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Fee</dt>
                        <dd class="mt-1 text-slate-600"><?= e(format_money($request['fee'])); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Payment</dt>
                        <dd class="mt-1 text-slate-600">
                            <?= ((int) $request['requires_payment'] === 1) ? e(payment_status_label($payment_status)) : 'Not required'; ?>
                        </dd>
                    </div>
                </dl>

                <div class="mt-5">
                    <h3 class="text-sm font-medium text-slate-800">Purpose</h3>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700"><?= e($request['purpose']); ?></p>
                </div>

                <?php if (!empty($request['remarks'])): ?>
                    <div class="mt-5">
                        <h3 class="text-sm font-medium text-slate-800">Resident Remarks</h3>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700"><?= e($request['remarks']); ?></p>
                    </div>
                <?php endif; ?>

                <div class="mt-5">
                    <h3 class="text-sm font-medium text-slate-800">Staff Notes</h3>
                    <?php if (!empty($request['staff_notes'])): ?>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700"><?= e($request['staff_notes']); ?></p>
                    <?php else: ?>
                        <p class="mt-2 text-sm text-slate-600">No staff notes yet.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Submitted Attachments</h2>

                <?php if (empty($attachments)): ?>
                    <p class="mt-3 text-sm text-slate-600">No attachments were recorded for this request.</p>
                <?php else: ?>
                    <ul class="workflow-file-list mt-4 space-y-3">
                        <?php foreach ($attachments as $attachment): ?>
                            <li>
                                <p class="font-medium text-slate-950"><?= e($attachment['original_name']); ?></p>
                                <p class="mt-1 text-sm text-slate-600">
                                    <?= e(format_file_size($attachment['file_size'])); ?> -
                                    <?= e($attachment['file_type']); ?>
                                </p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="mt-4 text-xs text-slate-600">Files are stored securely for barangay staff review.</p>
                <?php endif; ?>
            </section>
        </div>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
