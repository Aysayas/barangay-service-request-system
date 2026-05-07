<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>
<?php
$payment_status = !empty($payment['payment_status']) ? $payment['payment_status'] : 'pending_payment';
$can_upload_final_document = final_document_upload_allowed($request, $payment);
$final_document_block_reason = final_document_block_reason($request, $payment);
$final_document_exists = !empty($final_document['file_path'])
    && safe_storage_path($final_document['file_path'], 'runtime/uploads/final_documents') !== null;
$request_timeline = request_timeline_steps(
    $request['status'],
    ((int) $request['requires_payment'] === 1) ? $payment_status : null,
    $final_document_exists
);
?>

<section class="workflow-page">
    <div class="workflow-header">
        <div>
            <p class="workflow-kicker">Admin Review</p>
            <h1 class="workflow-title"><?= e($request['reference_no']); ?></h1>
            <p class="workflow-subtitle"><?= e($request['resident_name']); ?> - <?= e($request['service_name']); ?></p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="btn-primary" href="<?= site_url('admin/requests/' . $request['id'] . '/pdf'); ?>">Download PDF</a>
            <a class="btn-secondary" href="<?= site_url('admin/requests'); ?>">Back to Requests</a>
        </div>
    </div>

    <div class="workflow-detail-grid lg:grid-cols-[1fr_0.9fr]">
        <div class="space-y-6">
            <section class="workflow-card">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">Request Information</h2>
                        <p class="mt-1 text-sm text-slate-600">Submitted <?= e(date('M d, Y h:i A', strtotime($request['created_at']))); ?></p>
                    </div>
                    <span class="status-pill <?= status_badge_class($request['status']); ?>">
                        <?= e(request_status_display_label($request['status'])); ?>
                    </span>
                </div>

                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="font-medium text-slate-800">Resident</dt>
                        <dd class="mt-1 text-slate-600"><?= e($request['resident_name']); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Email</dt>
                        <dd class="mt-1 text-slate-600"><?= e($request['resident_email']); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Service</dt>
                        <dd class="mt-1 text-slate-600"><?= e($request['service_name']); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Fee</dt>
                        <dd class="mt-1 text-slate-600"><?= e(format_money($request['fee'])); ?></dd>
                    </div>
                </dl>

                <div class="mt-5">
                    <h3 class="text-sm font-medium text-slate-800">Purpose</h3>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700"><?= e($request['purpose']); ?></p>
                </div>

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
                <h2 class="text-lg font-semibold text-slate-950">Request Timeline</h2>
                <p class="mt-1 text-sm text-slate-600">Read-only overview of request progress, payment proof review, and final document release.</p>
                <ol class="mt-4 space-y-3 text-sm">
                    <?php foreach ($request_timeline as $step): ?>
                        <li class="rounded-md border p-4 <?= e($step['card_class']); ?>">
                            <div class="flex items-start gap-3">
                                <span class="mt-1 h-3 w-3 shrink-0 rounded-md <?= e($step['dot_class']); ?>"></span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-semibold <?= e($step['label_class']); ?>"><?= e($step['label']); ?></span>
                                        <span class="status-pill <?= e($step['pill_class']); ?>"><?= e($step['state_label']); ?></span>
                                    </div>
                                    <p class="mt-1 leading-6 <?= e($step['description_class']); ?>"><?= e($step['description']); ?></p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </section>

            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Submitted Attachments</h2>

                <?php if (empty($attachments)): ?>
                    <p class="mt-3 text-sm text-slate-600">No attachments were submitted.</p>
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
                <?php endif; ?>
            </section>
        </div>

        <div class="space-y-6">
            <section class="workflow-card">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">Payment Proof Review</h2>
                        <p class="mt-1 text-sm text-slate-600">Admin visibility only. Staff handles verification.</p>
                    </div>
                    <?php if ((int) $request['requires_payment'] === 1): ?>
                        <span class="status-pill <?= payment_status_badge_class($payment_status); ?>">
                            <?= e(payment_status_display_label($payment_status)); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ((int) $request['requires_payment'] !== 1): ?>
                    <p class="mt-4 text-sm text-slate-600">Payment Not Required for this service.</p>
                <?php else: ?>
                    <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="font-medium text-slate-800">Amount</dt>
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
                            <dt class="font-medium text-slate-800">Reviewed By</dt>
                            <dd class="mt-1 text-slate-600"><?= e($payment['verified_by_name'] ?? 'Not reviewed yet'); ?></dd>
                        </div>
                    </dl>

                    <?php
                        $payment_proof_exists = !empty($payment['proof_file_path'])
                            && safe_storage_path($payment['proof_file_path'], 'runtime/uploads/payment_proofs') !== null;
                    ?>
                    <?php if (!empty($payment['proof_file_path']) && $payment_proof_exists): ?>
                        <a class="mt-4 inline-flex w-full justify-center rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800 hover:border-teal-600 hover:text-teal-700 sm:w-auto" target="_blank" href="<?= site_url('admin/requests/payment-proof/' . $payment['id']); ?>">
                            Open Payment Proof
                        </a>
                    <?php elseif (!empty($payment['proof_file_path'])): ?>
                        <p class="mt-4 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950">
                            Payment proof record exists, but the file is missing.
                        </p>
                    <?php else: ?>
                        <p class="mt-4 text-sm text-slate-600">No payment proof has been submitted yet.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </section>

            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Final Document</h2>

                <?php if (!empty($final_document)): ?>
                    <div class="mt-4 rounded-md border border-teal-200 bg-teal-50 p-4 text-sm text-teal-950">
                        <p class="font-medium"><?= e($final_document['original_name']); ?></p>
                        <p class="mt-1">
                            <?= e(format_file_size($final_document['file_size'])); ?> -
                            uploaded <?= e(date('M d, Y h:i A', strtotime($final_document['uploaded_at']))); ?>
                            by <?= e($final_document['uploaded_by_name']); ?>
                        </p>
                        <?php if ($final_document_exists): ?>
                            <a class="mt-3 inline-flex w-full justify-center rounded-md bg-teal-700 px-3 py-2 text-sm font-semibold text-white hover:bg-teal-800 sm:w-auto" href="<?= site_url('admin/requests/final-document/' . $request['id']); ?>">
                                Download Final Document
                            </a>
                        <?php else: ?>
                            <p class="mt-3 rounded-md border border-amber-200 bg-amber-50 p-3 text-amber-950">
                                Final document record exists, but the file is missing.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="mt-3 text-sm text-slate-600">No final document has been uploaded yet.</p>
                <?php endif; ?>

                <?php if ($can_upload_final_document): ?>
                    <form class="mt-5 space-y-4" method="POST" enctype="multipart/form-data" action="<?= site_url('admin/requests/upload-final-document/' . $request['id']); ?>">
                        <?php csrf_field(); ?>

                        <div>
                            <label class="form-label" for="final_document">Upload Final Document</label>
                            <input class="form-input" id="final_document" type="file" name="final_document" accept=".pdf,.doc,.docx" required>
                            <p class="mt-2 text-xs text-slate-600">
                                Allowed types: PDF, DOC, DOCX. Maximum size: <?= e($max_upload_mb); ?>MB.
                                Upload the approved final document only. Uploading a new file replaces the current final document.
                            </p>
                        </div>

                        <button class="btn-primary w-full" type="submit">
                            <?= !empty($final_document) ? 'Replace Final Document' : 'Upload Final Document'; ?>
                        </button>
                    </form>
                <?php else: ?>
                    <p class="mt-4 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950">
                        <?= e($final_document_block_reason); ?>
                    </p>
                <?php endif; ?>
            </section>

            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Activity Log</h2>
                <?php if (empty($audit_logs)): ?>
                    <p class="mt-3 text-sm text-slate-600">No activity recorded yet.</p>
                <?php else: ?>
                    <ul class="mt-4 divide-y divide-slate-200 text-sm">
                        <?php foreach ($audit_logs as $log): ?>
                            <li class="py-3">
                                <p class="font-medium text-slate-950"><?= e($log['description']); ?></p>
                                <p class="mt-1 text-slate-600">
                                    <?= e($log['user_name'] ?: 'System'); ?> -
                                    <?= e(date('M d, Y h:i A', strtotime($log['created_at']))); ?>
                                </p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </div>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
