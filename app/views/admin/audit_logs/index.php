<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php $base_url = site_url('admin/audit-logs'); ?>

<section>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Admin</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950">Audit Logs</h1>
            <p class="mt-3 max-w-2xl text-zinc-700">Review important staff and admin actions.</p>
        </div>
        <a class="btn-secondary" href="<?= site_url('admin/dashboard'); ?>">Back to Dashboard</a>
    </div>

    <div class="mt-6 rounded-md border border-zinc-200 bg-white p-5">
        <form class="grid gap-4 md:grid-cols-[1fr_1fr_auto]" method="GET" action="<?= $base_url; ?>">
            <div>
                <label class="form-label" for="search">Search</label>
                <input class="form-input" id="search" type="text" name="search" value="<?= e($search); ?>" placeholder="User, action, target, or description">
            </div>
            <div>
                <label class="form-label" for="action">Action</label>
                <input class="form-input" id="action" type="text" name="action" value="<?= e($action); ?>" placeholder="created_service">
            </div>
            <div class="flex items-end gap-3">
                <button class="btn-primary" type="submit">Apply</button>
                <a class="btn-secondary" href="<?= $base_url; ?>">Reset</a>
            </div>
        </form>
    </div>

    <?php if (empty($logs)): ?>
        <div class="mt-8 rounded-md border border-zinc-200 bg-white p-5 text-sm text-zinc-600">
            No audit logs matched your filters.
        </div>
    <?php else: ?>
        <div class="mt-8 overflow-x-auto rounded-md border border-zinc-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-100 text-zinc-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">User</th>
                        <th class="px-4 py-3 font-medium">Action</th>
                        <th class="px-4 py-3 font-medium">Target</th>
                        <th class="px-4 py-3 font-medium">Description</th>
                        <th class="px-4 py-3 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="px-4 py-3 text-zinc-700"><?= e($log['user_name'] ?: 'System'); ?></td>
                            <td class="px-4 py-3">
                                <span class="rounded-md bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-800">
                                    <?= e(audit_action_label($log['action'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-700"><?= e($log['target_type'] . ' #' . $log['target_id']); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e($log['description']); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e(date('M d, Y h:i A', strtotime($log['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
