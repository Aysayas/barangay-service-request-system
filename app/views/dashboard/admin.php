<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="dashboard-page">
    <div class="dashboard-hero dashboard-hero-admin">
        <div class="section-heading-row">
            <div>
                <p class="dashboard-eyebrow text-cyan-700">Admin Management</p>
                <h1 class="dashboard-title">Welcome, <?= e($user['name'] ?? 'Admin'); ?></h1>
                <p class="dashboard-subtitle">
                    Oversee users, services, requests, complaints, community content, reports, exports, charts, and audit activity from one command center.
                </p>
            </div>
            <a class="btn-primary" href="<?= site_url('admin/reports'); ?>">Open Reports</a>
        </div>

        <div class="mini-stat-grid mt-6">
            <div class="mini-stat">
                <p class="mini-stat-label">Active Services</p>
                <p class="mini-stat-value text-teal-700"><?= e($service_counts['active_services'] ?? 0); ?></p>
            </div>
            <div class="mini-stat">
                <p class="mini-stat-label">Pending Requests</p>
                <p class="mini-stat-value text-amber-700"><?= e($request_counts['pending_requests'] ?? 0); ?></p>
            </div>
            <div class="mini-stat">
                <p class="mini-stat-label">Open Complaints</p>
                <p class="mini-stat-value text-amber-700"><?= e($complaint_counts['open_complaints'] ?? 0); ?></p>
            </div>
        </div>
    </div>

    <div>
        <div class="section-heading-row">
            <div>
                <p class="page-kicker">Management Shortcuts</p>
                <h2 class="mt-2 text-xl font-bold text-slate-950">Core admin areas</h2>
            </div>
        </div>
        <div class="dashboard-action-grid mt-4">
            <a class="action-tile" href="<?= site_url('admin/services'); ?>">
                <span class="action-tile-label">Services</span>
                <span class="action-tile-text block">Manage active service listings, fees, and requirements.</span>
            </a>
            <a class="action-tile" href="<?= site_url('admin/users'); ?>">
                <span class="action-tile-label">Users</span>
                <span class="action-tile-text block">Review resident, staff, and admin accounts.</span>
            </a>
            <a class="action-tile" href="<?= site_url('admin/community'); ?>">
                <span class="action-tile-label">Community</span>
                <span class="action-tile-text block">Publish events, advisories, programs, and resources.</span>
            </a>
            <a class="action-tile" href="<?= site_url('admin/reports'); ?>">
                <span class="action-tile-label">Reports</span>
                <span class="action-tile-text block">Open summaries, charts, CSV exports, and AI-assisted insights.</span>
            </a>
            <a class="action-tile" href="<?= site_url('admin/requests'); ?>">
                <span class="action-tile-label">Requests</span>
                <span class="action-tile-text block">Review all resident service request records.</span>
            </a>
            <a class="action-tile" href="<?= site_url('admin/complaints'); ?>">
                <span class="action-tile-label">Complaints</span>
                <span class="action-tile-text block">Oversee complaint status, notes, and evidence review.</span>
            </a>
            <a class="action-tile" href="<?= site_url('admin/announcements'); ?>">
                <span class="action-tile-label">Announcements</span>
                <span class="action-tile-text block">Manage homepage notices and published updates.</span>
            </a>
            <a class="action-tile" href="<?= site_url('admin/audit-logs'); ?>">
                <span class="action-tile-label">Audit Logs</span>
                <span class="action-tile-text block">Trace staff and admin actions across the system.</span>
            </a>
        </div>
    </div>

    <div>
        <div class="section-heading-row">
            <div>
                <p class="page-kicker">Executive Snapshot</p>
                <h2 class="mt-2 text-xl font-bold text-slate-950">System totals and workload signals</h2>
            </div>
        </div>
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="metric-card-featured">
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

    <div class="dashboard-work-grid">
        <section class="section-panel">
            <div class="section-heading-row">
                <div>
                    <p class="page-kicker">Recent Users</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-950">Latest account activity</h2>
                </div>
                <a class="text-sm font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('admin/users'); ?>">View all</a>
            </div>
            <div class="data-table-wrap mt-4">
                <?php if (empty($recent_users)): ?>
                    <div class="empty-state m-4">No users have been added yet.</div>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_users as $row): ?>
                                <tr>
                                    <td><?= e($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td><?= e(ucfirst($row['role'])); ?></td>
                                    <td><?= e(ucfirst($row['status'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>

        <section class="section-panel">
            <div class="section-heading-row">
                <div>
                    <p class="page-kicker">Recent Requests</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-950">Latest submitted services</h2>
                </div>
                <a class="text-sm font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('admin/requests'); ?>">View all</a>
            </div>
            <div class="data-table-wrap mt-4">
                <?php if (empty($recent_requests)): ?>
                    <div class="empty-state m-4">No service requests have been submitted yet.</div>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_requests as $request): ?>
                                <tr>
                                    <td class="font-medium text-slate-950"><?= e($request['reference_no']); ?></td>
                                    <td><?= e($request['service_name']); ?></td>
                                    <td>
                                        <span class="status-pill <?= status_badge_class($request['status']); ?>">
                                            <?= e(status_label($request['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
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

    <section class="section-panel">
        <div class="section-heading-row">
            <div>
                <p class="page-kicker">Recent Complaints</p>
                <h2 class="mt-2 text-lg font-semibold text-slate-950">Latest resident concern records</h2>
            </div>
            <a class="text-sm font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('admin/complaints'); ?>">View all</a>
        </div>
        <div class="data-table-wrap mt-4">
            <?php if (empty($recent_complaints)): ?>
                <div class="empty-state m-4">No resident complaints have been submitted yet.</div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Complainant</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_complaints as $complaint): ?>
                            <tr>
                                <td class="font-medium text-slate-950"><?= e($complaint['reference_no']); ?></td>
                                <td><?= e($complaint['resident_name']); ?></td>
                                <td><?= e($complaint['subject']); ?></td>
                                <td>
                                    <span class="status-pill <?= complaint_status_badge_class($complaint['status']); ?>">
                                        <?= e(complaint_status_label($complaint['status'])); ?>
                                    </span>
                                </td>
                                <td>
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
    </section>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
