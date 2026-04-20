<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php $base_url = site_url('staff/requests'); ?>

<section class="workflow-page">
    <div class="workflow-header">
        <div>
            <p class="workflow-kicker">Staff Queue</p>
            <h1 class="workflow-title">Resident Requests</h1>
            <p class="workflow-subtitle">
                Filter and search submitted requests from residents.
            </p>
        </div>
        <a class="btn-secondary" href="<?= site_url('staff/dashboard'); ?>">Back to Dashboard</a>
    </div>

    <div class="filter-card mt-6">
        <form class="grid gap-4 md:grid-cols-[0.7fr_1fr_auto]" method="GET" action="<?= $base_url; ?>">
            <div>
                <label class="form-label" for="status">Status</label>
                <select class="form-input" id="status" name="status">
                    <option value="all" <?= ($current_status === 'all') ? 'selected' : ''; ?>>All</option>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= e($status); ?>" <?= ($current_status === $status) ? 'selected' : ''; ?>>
                            <?= e(status_label($status)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" for="search">Search</label>
                <input class="form-input" id="search" type="text" name="search" value="<?= e($search); ?>" placeholder="Reference, resident, or service">
            </div>
            <div class="flex items-end gap-3">
                <button class="btn-primary" type="submit">Apply</button>
                <a class="btn-secondary" href="<?= $base_url; ?>">Reset</a>
            </div>
        </form>

        <div class="mt-5 flex flex-wrap gap-2 text-sm">
            <a class="rounded-md px-3 py-2 <?= ($current_status === 'all') ? 'bg-teal-700 text-white' : 'bg-slate-100 text-slate-800 hover:text-teal-700'; ?>" href="<?= $base_url; ?>">
                All
            </a>
            <?php foreach ($statuses as $status): ?>
                <a class="rounded-md px-3 py-2 <?= ($current_status === $status) ? 'bg-teal-700 text-white' : 'bg-slate-100 text-slate-800 hover:text-teal-700'; ?>" href="<?= $base_url . '?status=' . rawurlencode($status); ?>">
                    <?= e(status_label($status)); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($requests)): ?>
        <div class="empty-state-strong mt-8">
            No requests matched your filters.
        </div>
    <?php else: ?>
        <div class="mt-8 workflow-table-wrap">
            <table class="workflow-table">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Reference No.</th>
                        <th class="px-4 py-3 font-medium">Resident</th>
                        <th class="px-4 py-3 font-medium">Service</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Payment</th>
                        <th class="px-4 py-3 font-medium">Created</th>
                        <th class="px-4 py-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-950"><?= e($request['reference_no']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e($request['resident_name']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e($request['service_name']); ?></td>
                            <td class="px-4 py-3">
                                <span class="rounded-md px-2 py-1 text-xs font-medium <?= status_badge_class($request['status']); ?>">
                                    <?= e(status_label($request['status'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <?php if ((int) $request['requires_payment'] === 1): ?>
                                    <span class="rounded-md px-2 py-1 text-xs font-medium <?= payment_status_badge_class($request['payment_status']); ?>">
                                        <?= e(payment_status_label($request['payment_status'])); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-xs text-slate-600">Not required</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?= e(date('M d, Y h:i A', strtotime($request['created_at']))); ?></td>
                            <td class="px-4 py-3">
                                <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('staff/requests/' . $request['id']); ?>">
                                    Review
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
