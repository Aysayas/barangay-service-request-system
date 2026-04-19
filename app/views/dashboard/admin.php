<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <p class="page-kicker">Admin</p>
    <h1 class="page-title">Welcome, <?= e($user['name'] ?? 'Admin'); ?></h1>
    <p class="page-subtitle">
        Manage services, users, announcements, and review system activity.
    </p>

    <div class="mt-6 flex flex-wrap gap-3">
        <a class="btn-primary" href="<?= site_url('admin/services'); ?>">Services</a>
        <a class="btn-secondary" href="<?= site_url('admin/users'); ?>">Users</a>
        <a class="btn-secondary" href="<?= site_url('admin/announcements'); ?>">Announcements</a>
        <a class="btn-secondary" href="<?= site_url('admin/community'); ?>">Community</a>
        <a class="btn-secondary" href="<?= site_url('admin/requests'); ?>">Requests</a>
        <a class="btn-secondary" href="<?= site_url('admin/complaints'); ?>">Complaints</a>
        <a class="btn-secondary" href="<?= site_url('admin/reports'); ?>">Reports</a>
        <a class="btn-secondary" href="<?= site_url('admin/audit-logs'); ?>">Audit Logs</a>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="metric-card">
            <p class="metric-label">Total Users</p>
            <p class="metric-value"><?= e($user_counts['total_users'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Residents</p>
            <p class="metric-value text-teal-700"><?= e($user_counts['total_residents'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Staff</p>
            <p class="metric-value text-amber-700"><?= e($user_counts['total_staff'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Admins</p>
            <p class="metric-value"><?= e($user_counts['total_admins'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Services</p>
            <p class="metric-value"><?= e($service_counts['total_services'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Active Services</p>
            <p class="metric-value text-teal-700"><?= e($service_counts['active_services'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Requests</p>
            <p class="metric-value"><?= e($request_counts['total_requests'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Pending Requests</p>
            <p class="metric-value text-amber-700"><?= e($request_counts['pending_requests'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Complaints</p>
            <p class="metric-value"><?= e($complaint_counts['total_complaints'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Open Complaints</p>
            <p class="metric-value text-amber-700"><?= e($complaint_counts['open_complaints'] ?? 0); ?></p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <section>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-lg font-semibold text-zinc-950">Recent Users</h2>
                <a class="text-sm font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('admin/users'); ?>">View all</a>
            </div>
            <div class="data-table-wrap mt-4">
                <?php if (empty($recent_users)): ?>
                    <p class="p-5 text-sm text-slate-600">No users have been added yet.</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 font-medium">Name</th>
                                <th class="px-4 py-3 font-medium">Role</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            <?php foreach ($recent_users as $row): ?>
                                <tr>
                                    <td class="px-4 py-3 text-zinc-700"><?= e($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td class="px-4 py-3 text-zinc-700"><?= e(ucfirst($row['role'])); ?></td>
                                    <td class="px-4 py-3 text-zinc-700"><?= e(ucfirst($row['status'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>

        <section>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-lg font-semibold text-zinc-950">Recent Requests</h2>
                <a class="text-sm font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('admin/requests'); ?>">View all</a>
            </div>
            <div class="data-table-wrap mt-4">
                <?php if (empty($recent_requests)): ?>
                    <p class="p-5 text-sm text-slate-600">No service requests have been submitted yet.</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 font-medium">Reference</th>
                                <th class="px-4 py-3 font-medium">Service</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            <?php foreach ($recent_requests as $request): ?>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-zinc-950"><?= e($request['reference_no']); ?></td>
                                    <td class="px-4 py-3 text-zinc-700"><?= e($request['service_name']); ?></td>
                                    <td class="px-4 py-3">
                                        <span class="status-pill <?= status_badge_class($request['status']); ?>">
                                            <?= e(status_label($request['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('admin/requests/' . $request['id']); ?>">
                                            Open
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="mt-8">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-zinc-950">Recent Complaints</h2>
            <a class="text-sm font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('admin/complaints'); ?>">View all</a>
        </div>
        <div class="data-table-wrap mt-4">
            <?php if (empty($recent_complaints)): ?>
                <p class="p-5 text-sm text-slate-600">No resident complaints have been submitted yet.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 font-medium">Reference</th>
                            <th class="px-4 py-3 font-medium">Complainant</th>
                            <th class="px-4 py-3 font-medium">Subject</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        <?php foreach ($recent_complaints as $complaint): ?>
                            <tr>
                                <td class="px-4 py-3 font-medium text-zinc-950"><?= e($complaint['reference_no']); ?></td>
                                <td class="px-4 py-3 text-zinc-700"><?= e($complaint['resident_name']); ?></td>
                                <td class="px-4 py-3 text-zinc-700"><?= e($complaint['subject']); ?></td>
                                <td class="px-4 py-3">
                                    <span class="status-pill <?= complaint_status_badge_class($complaint['status']); ?>">
                                        <?= e(complaint_status_label($complaint['status'])); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('admin/complaints/' . $complaint['id']); ?>">
                                        Open
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
