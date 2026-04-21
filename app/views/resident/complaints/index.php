<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="workflow-page">
    <div class="workflow-header">
        <div>
            <p class="workflow-kicker">Complaint History</p>
            <h1 class="workflow-title">My Complaints</h1>
            <p class="workflow-subtitle">
                Submit barangay concerns separately from normal service requests and track staff review.
            </p>
        </div>
        <a class="btn-primary" href="<?= site_url('resident/complaints/create'); ?>">Submit Complaint</a>
    </div>

    <?php if (empty($complaints)): ?>
        <div class="empty-state-strong mt-8">
            <p class="text-sm text-slate-600">You have not submitted any complaints yet.</p>
            <a class="mt-4 inline-block font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('resident/complaints/create'); ?>">
                Submit your first complaint
            </a>
        </div>
    <?php else: ?>
        <div class="workflow-table-wrap">
            <table class="workflow-table">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Reference No.</th>
                        <th class="px-4 py-3 font-medium">Subject</th>
                        <th class="px-4 py-3 font-medium">Category</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Priority</th>
                        <th class="px-4 py-3 font-medium">Created</th>
                        <th class="px-4 py-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($complaints as $complaint): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-950"><?= e($complaint['reference_no']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e($complaint['subject']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e(complaint_category_label($complaint['category'])); ?></td>
                            <td class="px-4 py-3">
                                <span class="rounded-md px-2 py-1 text-xs font-medium <?= complaint_status_badge_class($complaint['status']); ?>">
                                    <?= e(complaint_status_label($complaint['status'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-md px-2 py-1 text-xs font-medium <?= complaint_priority_badge_class($complaint['priority']); ?>">
                                    <?= e(complaint_priority_label($complaint['priority'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?= e(date('M d, Y h:i A', strtotime($complaint['created_at']))); ?></td>
                            <td class="px-4 py-3">
                                <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('resident/complaints/' . $complaint['id']); ?>">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
