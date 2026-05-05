<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php
$base_url = site_url('admin/users');
$has_filters = ($current_role !== 'all') || ($search !== '');
?>

<section class="management-page">
    <div class="management-header">
        <div>
            <p class="page-kicker">Admin</p>
            <h1 class="management-title">User Accounts</h1>
            <p class="management-subtitle">Review resident registrations and manage authorized staff/admin access.</p>
        </div>
        <a class="btn-primary" href="<?= site_url('admin/users/create'); ?>">Create Staff/Admin</a>
    </div>

    <div class="filter-card">
        <form class="grid gap-4 md:grid-cols-[0.6fr_1fr_auto]" method="GET" action="<?= $base_url; ?>">
            <div class="md:col-span-3">
                <p class="compact-note">Use role and search filters to locate resident, staff, and administrator accounts without changing account records.</p>
            </div>
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
            <div class="management-filter-actions">
                <button class="btn-primary" type="submit">Apply</button>
                <a class="btn-secondary" href="<?= $base_url; ?>">Reset</a>
            </div>
        </form>
    </div>

    <?php if (empty($users)): ?>
        <div class="empty-state-strong mt-8">
            <?php if ($has_filters): ?>
                <h2 class="text-lg font-semibold text-slate-950">No users match the current filters.</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Try another role, search by a different name or email, or reset the filters.
                </p>
                <a class="btn-secondary mt-5" href="<?= $base_url; ?>">Reset Filters</a>
            <?php else: ?>
                <h2 class="text-lg font-semibold text-slate-950">No user records are available yet.</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Resident registrations and staff/admin accounts will appear here once they are created.
                </p>
                <a class="btn-primary mt-5" href="<?= site_url('admin/users/create'); ?>">Create Staff/Admin</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="management-table-wrap">
            <table class="management-table management-table-wide">
                <thead>
                    <tr>
                        <th class="px-4 py-3 font-medium">Name</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium">Role</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Created</th>
                        <th class="px-4 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $row): ?>
                        <?php
                            $role_badge = ($row['role'] === 'admin') ? 'badge-info' : (($row['role'] === 'staff') ? 'badge-warning' : 'badge-neutral');
                            $status_badge = ($row['status'] === 'active') ? 'badge-success' : 'badge-neutral';
                        ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-950"><?= e($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e($row['email']); ?></td>
                            <td class="px-4 py-3">
                                <span class="status-pill <?= $role_badge; ?>"><?= e(ucfirst($row['role'])); ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="status-pill <?= $status_badge; ?>">
                                    <?= e(ucfirst($row['status'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?= !empty($row['created_at']) ? e(date('M d, Y', strtotime($row['created_at']))) : 'Not recorded'; ?></td>
                            <td class="px-4 py-3">
                                <div class="management-row-actions">
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
