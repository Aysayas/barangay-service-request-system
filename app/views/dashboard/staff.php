<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <p class="page-kicker">Staff</p>
    <h1 class="page-title">Welcome, <?= e($user['name'] ?? 'Staff'); ?></h1>
    <p class="page-subtitle">
        Review resident requests, check submitted attachments, and update request statuses.
    </p>

    <div class="mt-6 flex flex-wrap gap-3">
        <a class="btn-primary" href="<?= site_url('staff/requests'); ?>">Open Request Queue</a>
        <a class="btn-secondary" href="<?= site_url('staff/complaints'); ?>">Open Complaint Queue</a>
        <a class="btn-secondary" href="<?= site_url('staff/requests') . '?status=submitted'; ?>">Submitted</a>
        <a class="btn-secondary" href="<?= site_url('staff/requests') . '?status=under_review'; ?>">Under Review</a>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="metric-card">
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

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="metric-card">
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

    <div class="mt-8">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-zinc-950">Recent Incoming Requests</h2>
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
                            <th class="px-4 py-3 font-medium">Reference</th>
                            <th class="px-4 py-3 font-medium">Resident</th>
                            <th class="px-4 py-3 font-medium">Service</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Date</th>
                            <th class="px-4 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        <?php foreach ($recent_requests as $request): ?>
                            <tr>
                                <td class="px-4 py-3 font-medium text-zinc-950"><?= e($request['reference_no']); ?></td>
                                <td class="px-4 py-3 text-zinc-700"><?= e($request['resident_name']); ?></td>
                                <td class="px-4 py-3 text-zinc-700"><?= e($request['service_name']); ?></td>
                                <td class="px-4 py-3">
                                    <span class="status-pill <?= status_badge_class($request['status']); ?>">
                                        <?= e(status_label($request['status'])); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-zinc-700"><?= e(date('M d, Y', strtotime($request['created_at']))); ?></td>
                                <td class="px-4 py-3">
                                    <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('staff/requests/' . $request['id']); ?>">Review</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-8">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-zinc-950">Recent Complaints</h2>
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
                                    <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('staff/complaints/' . $complaint['id']); ?>">Review</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
