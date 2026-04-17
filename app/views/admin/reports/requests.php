<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Admin Reports</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950">Request Reports</h1>
            <p class="mt-3 max-w-2xl text-zinc-700">Filter service requests and review request workflow totals.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="btn-primary" href="<?= e($export_url); ?>">Export CSV</a>
            <a class="btn-secondary" href="<?= site_url('admin/reports'); ?>">Back to Reports</a>
        </div>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Total</p><p class="mt-2 text-2xl font-bold text-zinc-950"><?= e($summary['total_requests']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Submitted</p><p class="mt-2 text-2xl font-bold text-amber-700"><?= e($summary['submitted_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Under Review</p><p class="mt-2 text-2xl font-bold text-amber-700"><?= e($summary['under_review_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Needs Info</p><p class="mt-2 text-2xl font-bold text-rose-700"><?= e($summary['needs_info_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Approved</p><p class="mt-2 text-2xl font-bold text-teal-700"><?= e($summary['approved_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Rejected</p><p class="mt-2 text-2xl font-bold text-rose-700"><?= e($summary['rejected_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Ready for Pickup</p><p class="mt-2 text-2xl font-bold text-amber-700"><?= e($summary['ready_for_pickup_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Released</p><p class="mt-2 text-2xl font-bold text-teal-700"><?= e($summary['released_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">This Month</p><p class="mt-2 text-2xl font-bold text-zinc-950"><?= e($summary['requests_this_month']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Most Requested</p><p class="mt-2 text-base font-bold text-zinc-950"><?= e($summary['most_requested_service']); ?></p><p class="mt-1 text-xs text-zinc-600"><?= e($summary['most_requested_service_total']); ?> request(s)</p></div>
    </div>

    <form class="mt-8 grid gap-4 rounded-md border border-zinc-200 bg-white p-5 md:grid-cols-5" method="GET" action="<?= site_url('admin/reports/requests'); ?>">
        <div>
            <label class="form-label" for="from_date">From</label>
            <input class="form-input" id="from_date" type="date" name="from_date" value="<?= e($filters['from_date']); ?>">
        </div>
        <div>
            <label class="form-label" for="to_date">To</label>
            <input class="form-input" id="to_date" type="date" name="to_date" value="<?= e($filters['to_date']); ?>">
        </div>
        <div>
            <label class="form-label" for="service_id">Service</label>
            <select class="form-input" id="service_id" name="service_id">
                <option value="">All</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= e($service['id']); ?>" <?= ((int) $filters['service_id'] === (int) $service['id']) ? 'selected' : ''; ?>><?= e($service['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label" for="status">Status</label>
            <select class="form-input" id="status" name="status">
                <option value="">All</option>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status); ?>" <?= ($filters['status'] === $status) ? 'selected' : ''; ?>><?= e(status_label($status)); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-end gap-3">
            <button class="btn-primary" type="submit">Apply</button>
            <a class="btn-secondary" href="<?= site_url('admin/reports/requests'); ?>">Reset</a>
        </div>
    </form>

    <?php if (empty($rows)): ?>
        <div class="mt-8 rounded-md border border-zinc-200 bg-white p-5 text-sm text-zinc-600">No requests matched the selected filters.</div>
    <?php else: ?>
        <div class="mt-8 overflow-x-auto rounded-md border border-zinc-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-100 text-zinc-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Reference</th>
                        <th class="px-4 py-3 font-medium">Resident</th>
                        <th class="px-4 py-3 font-medium">Service</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Payment</th>
                        <th class="px-4 py-3 font-medium">Final Doc</th>
                        <th class="px-4 py-3 font-medium">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-zinc-950"><?= e($row['reference_no']); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e($row['resident_name']); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e($row['service_name']); ?></td>
                            <td class="px-4 py-3"><span class="rounded-md px-2 py-1 text-xs font-medium <?= status_badge_class($row['status']); ?>"><?= e(status_label($row['status'])); ?></span></td>
                            <td class="px-4 py-3">
                                <?php if ((int) $row['requires_payment'] === 1): ?>
                                    <span class="rounded-md px-2 py-1 text-xs font-medium <?= payment_status_badge_class($row['payment_status']); ?>"><?= e(payment_status_label($row['payment_status'])); ?></span>
                                <?php else: ?>
                                    <span class="text-xs text-zinc-600">Not required</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-zinc-700"><?= ((int) $row['has_final_document'] === 1) ? 'Available' : 'None'; ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e(date('M d, Y', strtotime($row['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
