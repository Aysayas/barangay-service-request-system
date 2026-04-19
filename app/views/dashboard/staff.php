<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <div class="dashboard-hero dashboard-hero-staff">
        <div class="section-heading-row">
            <div>
                <p class="dashboard-eyebrow text-amber-700">Staff Operations</p>
                <h1 class="dashboard-title">Welcome, <?= e($user['name'] ?? 'Staff'); ?></h1>
                <p class="dashboard-subtitle">
                    Review incoming service requests, verify payment proofs, process complaints, and keep resident workflows moving.
                </p>
            </div>
            <a class="btn-primary" href="<?= site_url('staff/requests'); ?>">Open Request Queue</a>
        </div>

        <div class="mini-stat-grid mt-6">
            <div class="mini-stat">
                <p class="mini-stat-label">Submitted Requests</p>
                <p class="mini-stat-value text-amber-700"><?= e($counts['submitted_count'] ?? 0); ?></p>
            </div>
            <div class="mini-stat">
                <p class="mini-stat-label">Needs Info</p>
                <p class="mini-stat-value text-rose-700"><?= e($counts['needs_info_count'] ?? 0); ?></p>
            </div>
            <div class="mini-stat">
                <p class="mini-stat-label">Investigating Complaints</p>
                <p class="mini-stat-value text-amber-700"><?= e($complaint_counts['investigating_count'] ?? 0); ?></p>
            </div>
        </div>
    </div>

    <div class="dashboard-action-grid mt-6">
        <a class="action-tile" href="<?= site_url('staff/requests'); ?>">
            <span class="action-tile-label">Request Queue</span>
            <span class="action-tile-text block">Review requirements, payment state, notes, and final document readiness.</span>
        </a>
        <a class="action-tile" href="<?= site_url('staff/complaints'); ?>">
            <span class="action-tile-label">Complaint Queue</span>
            <span class="action-tile-text block">Review resident concerns, evidence, staff notes, and resolution status.</span>
        </a>
        <a class="action-tile" href="<?= site_url('staff/requests') . '?status=submitted'; ?>">
            <span class="action-tile-label">New Requests</span>
            <span class="action-tile-text block">Jump directly to submitted items waiting for first review.</span>
        </a>
        <a class="action-tile" href="<?= site_url('staff/requests') . '?status=under_review'; ?>">
            <span class="action-tile-label">Under Review</span>
            <span class="action-tile-text block">Continue processing requests already being checked.</span>
        </a>
    </div>

    <div class="mt-8">
        <div class="section-heading-row">
            <div>
                <p class="page-kicker">Request Operations</p>
                <h2 class="mt-2 text-xl font-bold text-slate-950">Service request workload</h2>
            </div>
            <a class="btn-secondary" href="<?= site_url('staff/requests'); ?>">View Queue</a>
        </div>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="metric-card-featured">
            <p class="metric-label">Total Requests</p>
            <p class="metric-value"><?= e($counts['total_requests'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Submitted</p>
            <p class="metric-value text-amber-700"><?= e($counts['submitted_count'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Under Review</p>
            <p class="metric-value text-amber-700"><?= e($counts['under_review_count'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Needs Info</p>
            <p class="metric-value text-rose-700"><?= e($counts['needs_info_count'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Approved</p>
            <p class="metric-value text-teal-700"><?= e($counts['approved_count'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Rejected</p>
            <p class="metric-value text-rose-700"><?= e($counts['rejected_count'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Ready for Pickup</p>
            <p class="metric-value text-teal-700"><?= e($counts['ready_for_pickup_count'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Released</p>
            <p class="metric-value text-teal-700"><?= e($counts['released_count'] ?? 0); ?></p>
        </div>
    </div>

    <div class="mt-8">
        <div class="section-heading-row">
            <div>
                <p class="page-kicker">Complaint Operations</p>
                <h2 class="mt-2 text-xl font-bold text-slate-950">Resident concern workload</h2>
            </div>
            <a class="btn-secondary" href="<?= site_url('staff/complaints'); ?>">View Queue</a>
        </div>
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="metric-card-featured">
            <p class="metric-label">Total Complaints</p>
            <p class="metric-value"><?= e($complaint_counts['total_complaints'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Submitted Complaints</p>
            <p class="metric-value text-amber-700"><?= e($complaint_counts['submitted_count'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Investigating</p>
            <p class="metric-value text-amber-700"><?= e($complaint_counts['investigating_count'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Resolved/Closed</p>
            <p class="metric-value text-teal-700"><?= e(($complaint_counts['resolved_count'] ?? 0) + ($complaint_counts['closed_count'] ?? 0)); ?></p>
        </div>
    </div>

    <div class="dashboard-work-grid mt-8">
        <section class="section-panel">
            <div class="section-heading-row">
                <div>
                    <p class="page-kicker">Incoming Requests</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-950">Recently submitted service work</h2>
                </div>
                <a class="text-sm font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('staff/requests'); ?>">View queue</a>
            </div>

            <?php if (empty($recent_requests)): ?>
                <div class="empty-state mt-4">
                    No resident requests yet.
                </div>
            <?php else: ?>
                <div class="data-table-wrap mt-4">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Resident</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_requests as $request): ?>
                                <tr>
                                    <td class="font-medium text-slate-950"><?= e($request['reference_no']); ?></td>
                                    <td><?= e($request['resident_name']); ?></td>
                                    <td><?= e($request['service_name']); ?></td>
                                    <td>
                                        <span class="status-pill <?= status_badge_class($request['status']); ?>">
                                            <?= e(status_label($request['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?= e(date('M d, Y', strtotime($request['created_at']))); ?></td>
                                    <td>
                                        <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('staff/requests/' . $request['id']); ?>">Review</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <section class="section-panel">
            <div class="section-heading-row">
                <div>
                    <p class="page-kicker">Recent Complaints</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-950">Resident concerns needing review</h2>
                </div>
                <a class="text-sm font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('staff/complaints'); ?>">View queue</a>
            </div>

            <?php if (empty($recent_complaints)): ?>
                <div class="empty-state mt-4">
                    No resident complaints yet.
                </div>
            <?php else: ?>
                <div class="data-table-wrap mt-4">
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
                                        <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('staff/complaints/' . $complaint['id']); ?>">Review</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
