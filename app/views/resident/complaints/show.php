<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php
$current_status = $complaint['status'];
$complaint_timeline = complaint_timeline_steps($current_status);
?>

<section class="workflow-page">
    <div class="workflow-header">
        <div>
            <p class="workflow-kicker">Complaint Details</p>
            <h1 class="workflow-title"><?= e($complaint['reference_no']); ?></h1>
            <p class="workflow-subtitle"><?= e($complaint['subject']); ?></p>
        </div>
        <a class="btn-secondary" href="<?= site_url('resident/complaints'); ?>">Back to Complaints</a>
    </div>

    <div class="workflow-detail-grid lg:grid-cols-[0.9fr_1.1fr]">
        <div class="space-y-6">
            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Current Status</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="status-pill <?= complaint_status_badge_class($current_status); ?>">
                        <?= e(complaint_status_display_label($current_status)); ?>
                    </span>
                    <span class="status-pill <?= complaint_priority_badge_class($complaint['priority']); ?>">
                        <?= e(complaint_priority_label($complaint['priority'])); ?> Priority
                    </span>
                </div>

                <ol class="mt-5 space-y-3 text-sm">
                    <?php foreach ($complaint_timeline as $step): ?>
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
                <h2 class="text-lg font-semibold text-slate-950">Staff Updates</h2>
                <div class="mt-4 space-y-5 text-sm">
                    <div>
                        <h3 class="font-medium text-slate-800">Staff Notes</h3>
                        <?php if (!empty($complaint['staff_notes'])): ?>
                            <p class="mt-2 whitespace-pre-line leading-6 text-slate-700"><?= e($complaint['staff_notes']); ?></p>
                        <?php else: ?>
                            <p class="mt-2 text-slate-600">No staff notes yet.</p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h3 class="font-medium text-slate-800">Resolution Notes</h3>
                        <?php if (!empty($complaint['resolution_notes'])): ?>
                            <p class="mt-2 whitespace-pre-line leading-6 text-slate-700"><?= e($complaint['resolution_notes']); ?></p>
                        <?php else: ?>
                            <p class="mt-2 text-slate-600">No resolution notes yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>

        <div class="space-y-6">
            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Complaint Information</h2>
                <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="font-medium text-slate-800">Category</dt>
                        <dd class="mt-1 text-slate-600"><?= e(complaint_category_label($complaint['category'])); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Submitted</dt>
                        <dd class="mt-1 text-slate-600"><?= e(date('M d, Y h:i A', strtotime($complaint['created_at']))); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Incident Date</dt>
                        <dd class="mt-1 text-slate-600"><?= !empty($complaint['incident_date']) ? e(date('M d, Y', strtotime($complaint['incident_date']))) : 'Not provided'; ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Assigned Staff</dt>
                        <dd class="mt-1 text-slate-600"><?= e($complaint['assigned_to_name'] ?: 'Not assigned yet'); ?></dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="font-medium text-slate-800">Location</dt>
                        <dd class="mt-1 text-slate-600"><?= e($complaint['location']); ?></dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="font-medium text-slate-800">Respondent</dt>
                        <dd class="mt-1 text-slate-600"><?= e($complaint['respondent_name'] ?: 'Not provided'); ?></dd>
                    </div>
                </dl>

                <div class="mt-5">
                    <h3 class="text-sm font-medium text-slate-800">Description</h3>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700"><?= e($complaint['description']); ?></p>
                </div>
            </section>

            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Evidence Attachments</h2>

                <?php if (empty($attachments)): ?>
                    <p class="mt-3 text-sm text-slate-600">No evidence files were uploaded for this complaint.</p>
                <?php else: ?>
                    <ul class="workflow-file-list mt-4 space-y-3">
                        <?php foreach ($attachments as $attachment): ?>
                            <?php $attachment_exists = safe_storage_path($attachment['file_path'] ?? '', 'runtime/uploads/complaints') !== null; ?>
                            <li class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="font-medium text-slate-950"><?= e($attachment['original_name']); ?></p>
                                    <p class="mt-1 text-sm text-slate-600">
                                        <?= e(format_file_size($attachment['file_size'])); ?> -
                                        <?= e($attachment['file_type']); ?>
                                    </p>
                                </div>
                                <?php if ($attachment_exists): ?>
                                    <a class="btn-secondary" target="_blank" href="<?= site_url('resident/complaints/attachment/' . $attachment['id']); ?>">
                                        Open
                                    </a>
                                <?php else: ?>
                                    <span class="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-950">
                                        File missing
                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </div>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
