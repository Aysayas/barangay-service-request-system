<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php $base_url = site_url('admin/users'); ?>

<section class="management-page">
    <div class="management-header">
        <div>
            <p class="page-kicker">Admin</p>
            <h1 class="management-title">Users</h1>
            <p class="management-subtitle">View residents and manage staff/admin accounts.</p>
        </div>
        <a class="btn-primary" href="<?= site_url('admin/users/create'); ?>">Create Staff/Admin</a>
    </div>

    <div class="filter-card">
        <form class="grid gap-4 md:grid-cols-[0.6fr_1fr_auto]" method="GET" action="<?= $base_url; ?>">
            <div>
                <label class="form-label" for="role">Role</label>
                <select class="form-input" id="role" name="role">
                    <option value="all" <?= ($current_role === 'all') ? 'selected' : ''; ?>>All</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= e($role); ?>" <?= ($current_role === $role) ? 'selected' : ''; ?>><?= e(ucfirst($role)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" for="search">Search</label>
                <input class="form-input" id="search" type="text" name="search" value="<?= e($search); ?>" placeholder="Name or email">
            </div>
            <div class="flex items-end gap-3">
                <button class="btn-primary" type="submit">Apply</button>
                <a class="btn-secondary" href="<?= $base_url; ?>">Reset</a>
            </div>
        </form>
    </div>

    <?php if (empty($users)): ?>
        <div class="empty-state-strong mt-8">
            No users matched your filters.
        </div>
    <?php else: ?>
        <div class="management-table-wrap">
            <table class="management-table">
                <thead>
                    <tr>
                        <th class="px-4 py-3 font-medium">Name</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium">Role</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $row): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-950"><?= e($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e($row['email']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e(ucfirst($row['role'])); ?></td>
                            <td class="px-4 py-3">
                                <span class="rounded-md px-2 py-1 text-xs font-medium <?= ($row['status'] === 'active') ? 'bg-teal-50 text-teal-900' : 'bg-slate-100 text-slate-800'; ?>">
                                    <?= e(ucfirst($row['status'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a class="btn-secondary" href="<?= site_url('admin/users/edit/' . $row['id']); ?>">Edit</a>
                                    <form method="POST" action="<?= site_url('admin/users/toggle/' . $row['id']); ?>">
                                        <?php csrf_field(); ?>
                                        <button class="btn-secondary" type="submit" onclick="return confirm('Update this user account status?');"><?= ($row['status'] === 'active') ? 'Deactivate' : 'Activate'; ?></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
