<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>
<?php
$next_action_counts = $next_action_counts ?? [];
$active_request_count = (int) ($next_action_counts['active_request_count'] ?? 0);
$pending_payment_proof_count = (int) ($next_action_counts['pending_payment_proof_count'] ?? 0);
$ready_document_count = (int) ($next_action_counts['ready_document_count'] ?? 0);
$active_complaint_count = (int) ($complaint_counts['active_count'] ?? 0);
?>

<section class="dashboard-page">
    <div class="dashboard-hero dashboard-hero-resident">
        <div class="section-heading-row">
            <div>
                <p class="dashboard-eyebrow">Resident Workspace</p>
                <h1 class="dashboard-title">Welcome, <?= e($user['name'] ?? 'Resident'); ?></h1>
                <p class="dashboard-subtitle">
                    Start a request, complete payment proof when needed, download released documents, and keep barangay concerns moving from one workspace.
                </p>
            </div>
            <a class="btn-primary" href="<?= site_url('resident/services'); ?>">Submit New Request</a>
        </div>

        <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="mini-stat">
                <p class="mini-stat-label">Active Requests</p>
                <p class="mini-stat-value"><?= e($active_request_count); ?></p>
            </div>
            <div class="mini-stat">
                <p class="mini-stat-label">Payment Proof Needed</p>
                <p class="mini-stat-value text-amber-700"><?= e($pending_payment_proof_count); ?></p>
            </div>
            <div class="mini-stat">
                <p class="mini-stat-label">Ready Documents</p>
                <p class="mini-stat-value text-teal-700"><?= e($ready_document_count); ?></p>
            </div>
            <div class="mini-stat">
                <p class="mini-stat-label">Active Complaints</p>
                <p class="mini-stat-value text-amber-700"><?= e($active_complaint_count); ?></p>
            </div>
        </div>
    </div>

    <div class="dashboard-action-grid">
        <a class="action-tile" href="<?= site_url('resident/services'); ?>">
            <span class="action-tile-label">Submit New Request</span>
            <span class="action-tile-text block">Choose a barangay service and submit the required details.</span>
        </a>
        <a class="action-tile" href="<?= site_url('resident/requests'); ?>">
            <span class="action-tile-label">View My Requests</span>
            <span class="action-tile-text block">Check status, staff notes, payment proof, and document availability.</span>
        </a>
        <a class="action-tile" href="<?= site_url('resident/requests'); ?>">
            <span class="action-tile-label">Upload Payment Proof</span>
            <span class="action-tile-text block">Open requests that need payment proof or a corrected submission.</span>
        </a>
        <a class="action-tile" href="<?= site_url('resident/requests'); ?>">
            <span class="action-tile-label">Download Final Document</span>
            <span class="action-tile-text block">Find approved or released requests with available final documents.</span>
        </a>
        <a class="action-tile" href="<?= site_url('resident/complaints/create'); ?>">
            <span class="action-tile-label">Submit Complaint</span>
            <span class="action-tile-text block">Send a concern for staff review with optional evidence.</span>
        </a>
        <a class="action-tile" href="<?= site_url('assistant'); ?>">
            <span class="action-tile-label">Ask Assistant</span>
            <span class="action-tile-text block">Get guided help for requests, payments, complaints, and documents.</span>
        </a>
    </div>

    <div>
        <div class="section-heading-row">
            <div>
                <p class="page-kicker">Request Snapshot</p>
                <h2 class="mt-2 text-xl font-bold text-slate-950">Your service request progress</h2>
            </div>
            <a class="btn-secondary" href="<?= site_url('resident/requests'); ?>">View My Requests</a>
        </div>
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
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
            <p class="metric-label">Approved/Released</p>
            <p class="metric-value text-teal-700"><?= e($counts['completed_count'] ?? 0); ?></p>
        </div>
    </div>

    <div>
        <div class="section-heading-row">
            <div>
                <p class="page-kicker">Complaint Snapshot</p>
                <h2 class="mt-2 text-xl font-bold text-slate-950">Your complaint tracking summary</h2>
            </div>
            <a class="btn-secondary" href="<?= site_url('resident/complaints'); ?>">View My Complaints</a>
        </div>
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="metric-card-featured">
            <p class="metric-label">Total Complaints</p>
            <p class="metric-value"><?= e($complaint_counts['total_complaints'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Complaint Submitted</p>
            <p class="metric-value text-amber-700"><?= e($complaint_counts['submitted_count'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Active Complaints</p>
            <p class="metric-value text-amber-700"><?= e($complaint_counts['active_count'] ?? 0); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Resolved/Closed</p>
            <p class="metric-value text-teal-700"><?= e($complaint_counts['resolved_count'] ?? 0); ?></p>
        </div>
    </div>

    <div class="dashboard-work-grid">
        <section class="section-panel">
            <div class="section-heading-row">
                <div>
                    <p class="page-kicker">Announcements</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-950">Latest barangay notices</h2>
                </div>
                <a class="inline-action-link" href="<?= site_url('community'); ?>">Open community</a>
            </div>

            <?php if (empty($latest_announcements)): ?>
                <div class="empty-state mt-4">
                    No published announcements yet.
                </div>
            <?php else: ?>
                <div class="mt-4 space-y-4">
                    <?php foreach ($latest_announcements as $announcement): ?>
                        <article class="compact-note">
                            <h3 class="font-semibold text-slate-950"><?= e($announcement['title']); ?></h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600"><?= e($announcement['body']); ?></p>
                            <?php if (!empty($announcement['published_at'])): ?>
                                <p class="mt-3 text-xs font-medium text-slate-500"><?= e(date('M d, Y', strtotime($announcement['published_at']))); ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="section-panel">
            <div class="section-heading-row">
                <div>
                    <p class="page-kicker">Community Updates</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-950">Helpful public information</h2>
                </div>
                <a class="inline-action-link" href="<?= site_url('community'); ?>">View all</a>
            </div>

            <?php if (empty($latest_community_posts)): ?>
                <div class="empty-state mt-4">
                    No community updates are published yet.
                </div>
            <?php else: ?>
                <div class="mt-4 space-y-4">
                    <?php foreach ($latest_community_posts as $post): ?>
                        <article class="compact-note">
                            <span class="status-pill <?= community_category_badge_class($post['category']); ?>">
                                <?= e(community_category_label($post['category'])); ?>
                            </span>
                            <h3 class="mt-3 font-semibold text-slate-950"><?= e($post['title']); ?></h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600"><?= e(community_post_summary($post)); ?></p>
                            <a class="mt-3 inline-action-link" href="<?= site_url('community/' . $post['slug']); ?>">Open</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="dashboard-work-grid">
        <section class="section-panel">
            <div class="section-heading-row">
                <div>
                    <p class="page-kicker">Recent Requests</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-950">Latest service activity</h2>
                </div>
                <a class="inline-action-link" href="<?= site_url('resident/requests'); ?>">View all</a>
            </div>

            <?php if (empty($recent_requests)): ?>
                <div class="empty-state mt-4">
                    No requests yet. Browse services to submit your first request.
                </div>
            <?php else: ?>
                <div class="data-table-wrap mt-4">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
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
                                    <td><?= e($request['service_name']); ?></td>
                                    <td>
                                        <span class="status-pill <?= status_badge_class($request['status']); ?>">
                                            <?= e(status_label($request['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?= e(date('M d, Y', strtotime($request['created_at']))); ?></td>
                                    <td>
                                        <a class="inline-action-link" href="<?= site_url('resident/requests/' . $request['id']); ?>">View</a>
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
                    <h2 class="mt-2 text-lg font-semibold text-slate-950">Latest concern updates</h2>
                </div>
                <a class="inline-action-link" href="<?= site_url('resident/complaints'); ?>">View all</a>
            </div>

            <?php if (empty($recent_complaints)): ?>
                <div class="empty-state mt-4">
                    No complaints yet. Submit a complaint if you need barangay staff to review a concern.
                </div>
            <?php else: ?>
                <div class="data-table-wrap mt-4">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_complaints as $complaint): ?>
                                <tr>
                                    <td class="font-medium text-slate-950"><?= e($complaint['reference_no']); ?></td>
                                    <td><?= e($complaint['subject']); ?></td>
                                    <td>
                                        <span class="status-pill <?= complaint_status_badge_class($complaint['status']); ?>">
                                            <?= e(complaint_status_label($complaint['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a class="inline-action-link" href="<?= site_url('resident/complaints/' . $complaint['id']); ?>">View</a>
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
