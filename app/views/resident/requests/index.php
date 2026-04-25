<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="workflow-page">
    <div class="workflow-header">
        <div>
            <p class="workflow-kicker">Request History</p>
            <h1 class="workflow-title">My Requests</h1>
            <p class="workflow-subtitle">
                Track the status of every service request you submitted.
            </p>
        </div>
        <a class="btn-primary" href="<?= site_url('resident/services'); ?>">New Request</a>
    </div>

    <?php if (empty($requests)): ?>
        <div class="empty-state-strong mt-8">
            <p class="text-sm text-slate-600">You have not submitted any requests yet.</p>
            <a class="mt-4 inline-action-link" href="<?= site_url('resident/services'); ?>">
                Browse available services
            </a>
        </div>
    <?php else: ?>
        <div class="workflow-table-wrap">
            <table class="workflow-table workflow-table-wide">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Reference No.</th>
                        <th class="px-4 py-3 font-medium">Service</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Created</th>
                        <th class="px-4 py-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-950"><?= e($request['reference_no']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e($request['service_name']); ?></td>
                            <td class="px-4 py-3">
                                <span class="status-pill <?= status_badge_class($request['status']); ?>">
                                    <?= e(status_label($request['status'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?= e(date('M d, Y h:i A', strtotime($request['created_at']))); ?></td>
                            <td class="px-4 py-3">
                                <a class="inline-action-link" href="<?= site_url('resident/requests/' . $request['id']); ?>">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
