<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php $base_url = site_url('admin/complaints'); ?>

<section class="workflow-page">
    <div class="workflow-header">
        <div>
            <p class="workflow-kicker">Admin Oversight</p>
            <h1 class="workflow-title">Complaints</h1>
            <p class="workflow-subtitle">
                Monitor resident complaints, staff handling, evidence, and resolution status.
            </p>
        </div>
        <a class="btn-secondary" href="<?= site_url('admin/dashboard'); ?>">Back to Dashboard</a>
    </div>

    <div class="filter-card">
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
            <div class="workflow-filter-actions">
                <button class="btn-primary" type="submit">Apply</button>
                <a class="btn-secondary" href="<?= $base_url; ?>">Reset</a>
            </div>
        </form>
    </div>

    <?php if (empty($complaints)): ?>
        <div class="empty-state-strong mt-8">
            No complaints matched your filters.
        </div>
    <?php else: ?>
        <div class="workflow-table-wrap">
            <table class="workflow-table workflow-table-wide">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Reference No.</th>
                        <th class="px-4 py-3 font-medium">Complainant</th>
                        <th class="px-4 py-3 font-medium">Subject</th>
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
                            <td class="px-4 py-3">
                                <span class="status-pill <?= complaint_status_badge_class($complaint['status']); ?>">
                                    <?= e(complaint_status_label($complaint['status'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="status-pill <?= complaint_priority_badge_class($complaint['priority']); ?>">
                                    <?= e(complaint_priority_label($complaint['priority'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?= e($complaint['assigned_to_name'] ?: 'Unassigned'); ?></td>
                            <td class="px-4 py-3">
                                <a class="inline-action-link" href="<?= site_url('admin/complaints/' . $complaint['id']); ?>">
                                    Open
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
