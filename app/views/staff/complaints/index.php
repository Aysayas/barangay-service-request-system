<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php $base_url = site_url('staff/complaints'); ?>

<section class="workflow-page">
    <div class="workflow-header">
        <div>
            <p class="workflow-kicker">Staff Queue</p>
            <h1 class="workflow-title">Resident Complaints</h1>
            <p class="workflow-subtitle">
                Review complaint details, evidence files, priority, assignments, and resolution notes.
            </p>
        </div>
        <a class="btn-secondary" href="<?= site_url('staff/dashboard'); ?>">Back to Dashboard</a>
    </div>

    <div class="filter-card mt-6">
        <form class="grid gap-4 md:grid-cols-[0.7fr_1fr_auto]" method="GET" action="<?= $base_url; ?>">
            <div>
                <label class="form-label" for="status">Status</label>
                <select class="form-input" id="status" name="status">
                    <option value="all" <?= ($current_status === 'all') ? 'selected' : ''; ?>>All</option>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= e($status); ?>" <?= ($current_status === $status) ? 'selected' : ''; ?>>
                            <?= e(complaint_status_label($status)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" for="search">Search</label>
                <input class="form-input" id="search" type="text" name="search" value="<?= e($search); ?>" placeholder="Reference, subject, complainant, or category">
            </div>
            <div class="flex items-end gap-3">
                <button class="btn-primary" type="submit">Apply</button>
                <a class="btn-secondary" href="<?= $base_url; ?>">Reset</a>
            </div>
        </form>

        <div class="mt-5 flex flex-wrap gap-2 text-sm">
            <a class="rounded-md px-3 py-2 <?= ($current_status === 'all') ? 'bg-teal-700 text-white' : 'bg-slate-100 text-slate-800 hover:text-teal-700'; ?>" href="<?= $base_url; ?>">
                All
            </a>
            <?php foreach ($statuses as $status): ?>
                <a class="rounded-md px-3 py-2 <?= ($current_status === $status) ? 'bg-teal-700 text-white' : 'bg-slate-100 text-slate-800 hover:text-teal-700'; ?>" href="<?= $base_url . '?status=' . rawurlencode($status); ?>">
                    <?= e(complaint_status_label($status)); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($complaints)): ?>
        <div class="empty-state-strong mt-8">
            No complaints matched your filters.
        </div>
    <?php else: ?>
        <div class="mt-8 workflow-table-wrap">
            <table class="workflow-table">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Reference No.</th>
                        <th class="px-4 py-3 font-medium">Complainant</th>
                        <th class="px-4 py-3 font-medium">Subject</th>
                        <th class="px-4 py-3 font-medium">Category</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Priority</th>
                        <th class="px-4 py-3 font-medium">Assigned</th>
                        <th class="px-4 py-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($complaints as $complaint): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-950"><?= e($complaint['reference_no']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e($complaint['complainant_name']); ?></td>
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
                            <td class="px-4 py-3 text-slate-700"><?= e($complaint['assigned_to_name'] ?: 'Unassigned'); ?></td>
                            <td class="px-4 py-3">
                                <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('staff/complaints/' . $complaint['id']); ?>">
                                    Review
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
