<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php $base_url = site_url('admin/audit-logs'); ?>

<section class="management-page">
    <div class="management-header">
        <div>
            <p class="page-kicker">Admin</p>
            <h1 class="management-title">Audit Logs</h1>
            <p class="management-subtitle">Review important staff and admin actions.</p>
        </div>
        <a class="btn-secondary" href="<?= site_url('admin/dashboard'); ?>">Back to Dashboard</a>
    </div>

    <div class="filter-card">
        <form class="grid gap-4 md:grid-cols-[1fr_1fr_auto]" method="GET" action="<?= $base_url; ?>">
            <div>
                <label class="form-label" for="search">Search</label>
                <input class="form-input" id="search" type="text" name="search" value="<?= e($search); ?>" placeholder="User, action, target, or description">
            </div>
            <div>
                <label class="form-label" for="action">Action</label>
                <input class="form-input" id="action" type="text" name="action" value="<?= e($action); ?>" placeholder="created_service">
            </div>
            <div class="management-filter-actions">
                <button class="btn-primary" type="submit">Apply</button>
                <a class="btn-secondary" href="<?= $base_url; ?>">Reset</a>
            </div>
        </form>
    </div>

    <?php if (empty($logs)): ?>
        <div class="empty-state-strong mt-8">
            No audit logs matched your filters.
        </div>
    <?php else: ?>
        <div class="management-table-wrap">
            <table class="management-table">
                <thead>
                    <tr>
                        <th class="px-4 py-3 font-medium">User</th>
                        <th class="px-4 py-3 font-medium">Action</th>
                        <th class="px-4 py-3 font-medium">Target</th>
                        <th class="px-4 py-3 font-medium">Description</th>
                        <th class="px-4 py-3 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="px-4 py-3 text-slate-700"><?= e($log['user_name'] ?: 'System'); ?></td>
                            <td class="px-4 py-3">
                                <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-800">
                                    <?= e(audit_action_label($log['action'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?= e($log['target_type'] . ' #' . $log['target_id']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e($log['description']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e(date('M d, Y h:i A', strtotime($log['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
