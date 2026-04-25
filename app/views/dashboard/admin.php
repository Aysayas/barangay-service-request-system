<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>
<?php
$content_counts = $content_counts ?? [];
$published_announcements = (int) ($content_counts['published_announcements'] ?? 0);
$published_community_posts = (int) ($content_counts['published_community_posts'] ?? 0);
$published_content_total = (int) ($content_counts['published_content_total'] ?? ($published_announcements + $published_community_posts));
?>

<section class="dashboard-page">
    <div class="dashboard-hero dashboard-hero-admin">
        <div class="section-heading-row">
            <div>
                <p class="dashboard-eyebrow text-teal-700">Admin Management</p>
                <h1 class="dashboard-title">Welcome, <?= e($user['name'] ?? 'Admin'); ?></h1>
                <p class="dashboard-subtitle">
                    Monitor system activity, open workload, published information, audit history, and reporting from one administrative workspace.
                </p>
            </div>
            <a class="btn-primary" href="<?= site_url('admin/reports'); ?>">Open Reports</a>
        </div>

        <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
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
            <div class="mini-stat">
                <p class="mini-stat-label">Published Content</p>
                <p class="mini-stat-value text-teal-700"><?= e($published_content_total); ?></p>
            </div>
        </div>
    </div>

    <section class="section-panel">
        <div class="section-heading-row">
            <div>
                <p class="page-kicker">Operational Summary</p>
                <h2 class="mt-2 text-xl font-bold text-slate-950">System areas to monitor</h2>
            </div>
        </div>
        <div class="dashboard-action-grid mt-4">
            <a class="action-tile" href="<?= site_url('admin/users') . '?role=resident'; ?>">
                <span class="action-tile-label">Residents</span>
                <span class="metric-value block text-teal-700"><?= e($user_counts['total_residents'] ?? 0); ?></span>
                <span class="action-tile-text block">Review registered resident accounts and profile details.</span>
            </a>
            <a class="action-tile" href="<?= site_url('admin/users') . '?role=staff'; ?>">
                <span class="action-tile-label">Staff</span>
                <span class="metric-value block text-amber-700"><?= e($user_counts['total_staff'] ?? 0); ?></span>
                <span class="action-tile-text block">Manage staff access for request and complaint handling.</span>
            </a>
            <a class="action-tile" href="<?= site_url('admin/requests'); ?>">
                <span class="action-tile-label">Requests</span>
                <span class="metric-value block"><?= e($request_counts['total_requests'] ?? 0); ?></span>
                <span class="action-tile-text block">Monitor all resident service request records.</span>
            </a>
            <a class="action-tile" href="<?= site_url('admin/complaints'); ?>">
                <span class="action-tile-label">Complaints</span>
                <span class="metric-value block text-amber-700"><?= e($complaint_counts['total_complaints'] ?? 0); ?></span>
                <span class="action-tile-text block">Oversee concern handling and resolution activity.</span>
            </a>
            <a class="action-tile" href="<?= site_url('admin/community'); ?>">
                <span class="action-tile-label">Published Content</span>
                <span class="metric-value block text-teal-700"><?= e($published_content_total); ?></span>
                <span class="action-tile-text block">Keep announcements and community posts current.</span>
            </a>
            <a class="action-tile" href="<?= site_url('admin/reports'); ?>">
                <span class="action-tile-label">Reports</span>
                <span class="metric-value block text-teal-700">Open</span>
                <span class="action-tile-text block">Review summaries, charts, CSV exports, and AI-assisted insights.</span>
            </a>
        </div>
    </section>

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
        <div class="metric-card">
            <p class="metric-label">Announcements</p>
            <p class="metric-value text-teal-700"><?= e($published_announcements); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Community Posts</p>
            <p class="metric-value text-teal-700"><?= e($published_community_posts); ?></p>
        </div>
    </div>

    <div class="dashboard-work-grid">
        <section class="section-panel">
            <div class="section-heading-row">
                <div>
                    <p class="page-kicker">Recent Users</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-950">Latest account activity</h2>
                </div>
                <a class="inline-action-link" href="<?= site_url('admin/users'); ?>">View all</a>
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
                                <?php
                                    $role_badge = ($row['role'] === 'admin') ? 'badge-info' : (($row['role'] === 'staff') ? 'badge-warning' : 'badge-neutral');
                                    $status_badge = ($row['status'] === 'active') ? 'badge-success' : 'badge-neutral';
                                ?>
                                <tr>
                                    <td><?= e($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td>
                                        <span class="status-pill <?= $role_badge; ?>"><?= e(ucfirst($row['role'])); ?></span>
                                    </td>
                                    <td>
                                        <span class="status-pill <?= $status_badge; ?>"><?= e(ucfirst($row['status'])); ?></span>
                                    </td>
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
                <a class="inline-action-link" href="<?= site_url('admin/requests'); ?>">View all</a>
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
                                        <a class="inline-action-link" href="<?= site_url('admin/requests/' . $request['id']); ?>">
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
            <a class="inline-action-link" href="<?= site_url('admin/complaints'); ?>">View all</a>
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
                                    <a class="inline-action-link" href="<?= site_url('admin/complaints/' . $complaint['id']); ?>">
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

    <section class="section-panel">
        <div class="section-heading-row">
            <div>
                <p class="page-kicker">Audit Activity</p>
                <h2 class="mt-2 text-lg font-semibold text-slate-950">Recent system actions</h2>
            </div>
            <a class="inline-action-link" href="<?= site_url('admin/audit-logs'); ?>">View audit logs</a>
        </div>

        <?php if (empty($recent_audit_logs)): ?>
            <div class="empty-state mt-4">
                No audit activity has been recorded yet.
            </div>
        <?php else: ?>
            <ul class="mt-4 divide-y divide-slate-200 text-sm">
                <?php foreach ($recent_audit_logs as $log): ?>
                    <li class="py-3">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <p class="font-medium text-slate-950"><?= e($log['description']); ?></p>
                                <p class="mt-1 text-slate-600">
                                    <?= e($log['user_name'] ?: 'System'); ?> -
                                    <?= e(date('M d, Y h:i A', strtotime($log['created_at']))); ?>
                                </p>
                            </div>
                            <span class="status-pill badge-neutral"><?= e(audit_action_label($log['action'])); ?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
