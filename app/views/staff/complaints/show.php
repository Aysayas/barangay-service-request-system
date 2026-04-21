<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php $status_flow = ['submitted', 'under_review', 'needs_info', 'investigating', 'resolved', 'closed']; ?>

<section class="workflow-page">
    <div class="workflow-header">
        <div>
            <p class="workflow-kicker">Staff Complaint Review</p>
            <h1 class="workflow-title"><?= e($complaint['reference_no']); ?></h1>
            <p class="workflow-subtitle"><?= e($complaint['complainant_name']); ?> - <?= e($complaint['subject']); ?></p>
        </div>
        <a class="btn-secondary" href="<?= site_url('staff/complaints'); ?>">Back to Complaints</a>
    </div>

    <div class="workflow-detail-grid lg:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-6">
            <section class="workflow-card">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">Complaint Details</h2>
                        <p class="mt-1 text-sm text-slate-600">Submitted <?= e(date('M d, Y h:i A', strtotime($complaint['created_at']))); ?></p>
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
                        <dt class="font-medium text-slate-800">Complainant</dt>
                        <dd class="mt-1 text-slate-600"><?= e($complaint['complainant_name']); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Email</dt>
                        <dd class="mt-1 text-slate-600"><?= e($complaint['complainant_email']); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Contact Number</dt>
                        <dd class="mt-1 text-slate-600"><?= e($complaint['complainant_contact'] ?: 'Not provided'); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Category</dt>
                        <dd class="mt-1 text-slate-600"><?= e(complaint_category_label($complaint['category'])); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Incident Date</dt>
                        <dd class="mt-1 text-slate-600"><?= !empty($complaint['incident_date']) ? e(date('M d, Y', strtotime($complaint['incident_date']))) : 'Not provided'; ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Assigned To</dt>
                        <dd class="mt-1 text-slate-600"><?= e($complaint['assigned_to_name'] ?: 'Unassigned'); ?></dd>
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
                    <p class="mt-3 text-sm text-slate-600">No evidence files were uploaded.</p>
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
                                    <a class="btn-secondary" target="_blank" href="<?= site_url('staff/complaints/attachment/' . $attachment['id']); ?>">
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

            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Activity Log</h2>
                <?php if (empty($audit_logs)): ?>
                    <p class="mt-3 text-sm text-slate-600">No complaint activity recorded yet.</p>
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

        <div class="space-y-6">
            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Update Complaint</h2>
                <form class="mt-5 space-y-5" method="POST" action="<?= site_url('staff/complaints/update/' . $complaint['id']); ?>">
                    <?php csrf_field(); ?>

                    <div>
                        <label class="form-label" for="status">Status</label>
                        <select class="form-input" id="status" name="status" required>
                            <?php foreach ($statuses as $status): ?>
                                <?php
                                    $disabled = $status !== $complaint['status'] && !complaint_status_transition_allowed($complaint['status'], $status);
                                ?>
                                <option value="<?= e($status); ?>" <?= ($complaint['status'] === $status) ? 'selected' : ''; ?> <?= $disabled ? 'disabled' : ''; ?>>
                                    <?= e(complaint_status_label($status)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-2 text-xs text-slate-600">
                            Complaint statuses must move through the handling workflow. Resolution notes are required before resolving, closing, or dismissing a complaint.
                        </p>
                    </div>

                    <div>
                        <label class="form-label" for="priority">Priority</label>
                        <select class="form-input" id="priority" name="priority" required>
                            <?php foreach ($priorities as $priority): ?>
                                <option value="<?= e($priority); ?>" <?= ($complaint['priority'] === $priority) ? 'selected' : ''; ?>>
                                    <?= e(complaint_priority_label($priority)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="form-label" for="assigned_to">Assigned Staff</label>
                        <select class="form-input" id="assigned_to" name="assigned_to">
                            <option value="0">Unassigned</option>
                            <?php foreach ($staff_users as $staff_user): ?>
                                <option value="<?= e($staff_user['id']); ?>" <?= ((int) $complaint['assigned_to'] === (int) $staff_user['id']) ? 'selected' : ''; ?>>
                                    <?= e($staff_user['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="form-label" for="staff_notes">Staff Notes</label>
                        <textarea class="form-input min-h-32" id="staff_notes" name="staff_notes"><?= e($complaint['staff_notes'] ?? ''); ?></textarea>
                        <p class="mt-2 text-xs text-slate-600">Residents can see these notes on their complaint details page.</p>
                    </div>

                    <div>
                        <label class="form-label" for="resolution_notes">Resolution Notes</label>
                        <textarea class="form-input min-h-32" id="resolution_notes" name="resolution_notes"><?= e($complaint['resolution_notes'] ?? ''); ?></textarea>
                        <p class="mt-2 text-xs text-slate-600">Required when marking a complaint resolved, closed, or dismissed.</p>
                    </div>

                    <button class="btn-primary w-full" type="submit">Save Complaint Review</button>
                </form>
            </section>

            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Status Direction</h2>
                <ol class="mt-4 space-y-3 text-sm">
                    <?php foreach ($status_flow as $status): ?>
                        <li class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-md <?= ($status === $complaint['status']) ? 'bg-teal-700' : 'bg-slate-300'; ?>"></span>
                            <span class="<?= ($status === $complaint['status']) ? 'font-semibold text-slate-950' : 'text-slate-600'; ?>">
                                <?= e(complaint_status_label($status)); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                    <?php if ($complaint['status'] === 'dismissed'): ?>
                        <li class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-md bg-rose-700"></span>
                            <span class="font-semibold text-rose-900">Dismissed</span>
                        </li>
                    <?php endif; ?>
                </ol>
            </section>

            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Resident Account</h2>
                <dl class="mt-4 space-y-4 text-sm">
                    <div>
                        <dt class="font-medium text-slate-800">Resident Name</dt>
                        <dd class="mt-1 text-slate-600"><?= e($complaint['resident_name']); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Account Email</dt>
                        <dd class="mt-1 text-slate-600"><?= e($complaint['resident_email']); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-800">Address</dt>
                        <dd class="mt-1 text-slate-600"><?= e($complaint['resident_address'] ?: 'Not provided'); ?></dd>
                    </div>
                </dl>
            </section>
        </div>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
