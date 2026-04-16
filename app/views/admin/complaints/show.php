<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Admin Complaint Review</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950"><?= e($complaint['reference_no']); ?></h1>
            <p class="mt-3 text-zinc-700"><?= e($complaint['complainant_name']); ?> - <?= e($complaint['subject']); ?></p>
        </div>
        <a class="btn-secondary" href="<?= site_url('admin/complaints'); ?>">Back to Complaints</a>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_0.9fr]">
        <div class="space-y-6">
            <section class="rounded-md border border-zinc-200 bg-white p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-zinc-950">Complaint Information</h2>
                        <p class="mt-1 text-sm text-zinc-600">Submitted <?= e(date('M d, Y h:i A', strtotime($complaint['created_at']))); ?></p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-md px-2 py-1 text-sm font-medium <?= complaint_status_badge_class($complaint['status']); ?>">
                            <?= e(complaint_status_label($complaint['status'])); ?>
                        </span>
                        <span class="rounded-md px-2 py-1 text-sm font-medium <?= complaint_priority_badge_class($complaint['priority']); ?>">
                            <?= e(complaint_priority_label($complaint['priority'])); ?>
                        </span>
                    </div>
                </div>

                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="font-medium text-zinc-800">Complainant</dt>
                        <dd class="mt-1 text-zinc-600"><?= e($complaint['complainant_name']); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-zinc-800">Email</dt>
                        <dd class="mt-1 text-zinc-600"><?= e($complaint['complainant_email']); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-zinc-800">Contact Number</dt>
                        <dd class="mt-1 text-zinc-600"><?= e($complaint['complainant_contact'] ?: 'Not provided'); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-zinc-800">Category</dt>
                        <dd class="mt-1 text-zinc-600"><?= e(complaint_category_label($complaint['category'])); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-zinc-800">Incident Date</dt>
                        <dd class="mt-1 text-zinc-600"><?= !empty($complaint['incident_date']) ? e(date('M d, Y', strtotime($complaint['incident_date']))) : 'Not provided'; ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-zinc-800">Assigned To</dt>
                        <dd class="mt-1 text-zinc-600"><?= e($complaint['assigned_to_name'] ?: 'Unassigned'); ?></dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="font-medium text-zinc-800">Location</dt>
                        <dd class="mt-1 text-zinc-600"><?= e($complaint['location']); ?></dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="font-medium text-zinc-800">Respondent</dt>
                        <dd class="mt-1 text-zinc-600"><?= e($complaint['respondent_name'] ?: 'Not provided'); ?></dd>
                    </div>
                </dl>

                <div class="mt-5">
                    <h3 class="text-sm font-medium text-zinc-800">Description</h3>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-zinc-700"><?= e($complaint['description']); ?></p>
                </div>
            </section>

            <section class="rounded-md border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold text-zinc-950">Evidence Attachments</h2>

                <?php if (empty($attachments)): ?>
                    <p class="mt-3 text-sm text-zinc-600">No evidence files were uploaded.</p>
                <?php else: ?>
                    <ul class="mt-4 divide-y divide-zinc-200">
                        <?php foreach ($attachments as $attachment): ?>
                            <li class="flex flex-wrap items-center justify-between gap-3 py-3">
                                <div>
                                    <p class="font-medium text-zinc-950"><?= e($attachment['original_name']); ?></p>
                                    <p class="mt-1 text-sm text-zinc-600">
                                        <?= e(format_file_size($attachment['file_size'])); ?> -
                                        <?= e($attachment['file_type']); ?>
                                    </p>
                                </div>
                                <a class="btn-secondary" target="_blank" href="<?= site_url('admin/complaints/attachment/' . $attachment['id']); ?>">
                                    Open
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </div>

        <div class="space-y-6">
            <section class="rounded-md border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold text-zinc-950">Processing Summary</h2>
                <dl class="mt-4 space-y-4 text-sm">
                    <div>
                        <dt class="font-medium text-zinc-800">Status</dt>
                        <dd class="mt-1 text-zinc-600"><?= e(complaint_status_label($complaint['status'])); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-zinc-800">Priority</dt>
                        <dd class="mt-1 text-zinc-600"><?= e(complaint_priority_label($complaint['priority'])); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-zinc-800">Updated</dt>
                        <dd class="mt-1 text-zinc-600"><?= e(date('M d, Y h:i A', strtotime($complaint['updated_at']))); ?></dd>
                    </div>
                </dl>

                <div class="mt-5">
                    <h3 class="text-sm font-medium text-zinc-800">Staff Notes</h3>
                    <?php if (!empty($complaint['staff_notes'])): ?>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-zinc-700"><?= e($complaint['staff_notes']); ?></p>
                    <?php else: ?>
                        <p class="mt-2 text-sm text-zinc-600">No staff notes yet.</p>
                    <?php endif; ?>
                </div>

                <div class="mt-5">
                    <h3 class="text-sm font-medium text-zinc-800">Resolution Notes</h3>
                    <?php if (!empty($complaint['resolution_notes'])): ?>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-zinc-700"><?= e($complaint['resolution_notes']); ?></p>
                    <?php else: ?>
                        <p class="mt-2 text-sm text-zinc-600">No resolution notes yet.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="rounded-md border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold text-zinc-950">Activity Log</h2>
                <?php if (empty($audit_logs)): ?>
                    <p class="mt-3 text-sm text-zinc-600">No complaint activity recorded yet.</p>
                <?php else: ?>
                    <ul class="mt-4 divide-y divide-zinc-200 text-sm">
                        <?php foreach ($audit_logs as $log): ?>
                            <li class="py-3">
                                <p class="font-medium text-zinc-950"><?= e($log['description']); ?></p>
                                <p class="mt-1 text-zinc-600">
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
