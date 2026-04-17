<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Admin Reports</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950">Payment Reports</h1>
            <p class="mt-3 max-w-2xl text-zinc-700">Track simulated payment records, verification status, and payment amounts.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="btn-primary" href="<?= e($export_url); ?>">Export CSV</a>
            <a class="btn-secondary" href="<?= site_url('admin/reports'); ?>">Back to Reports</a>
        </div>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Payment Records</p><p class="mt-2 text-2xl font-bold text-zinc-950"><?= e($summary['total_payments']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Pending Payment</p><p class="mt-2 text-2xl font-bold text-amber-700"><?= e($summary['pending_payment_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Payment Submitted</p><p class="mt-2 text-2xl font-bold text-zinc-950"><?= e($summary['payment_submitted_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Payment Verified</p><p class="mt-2 text-2xl font-bold text-teal-700"><?= e($summary['payment_verified_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Payment Rejected</p><p class="mt-2 text-2xl font-bold text-rose-700"><?= e($summary['payment_rejected_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Expected Amount</p><p class="mt-2 text-2xl font-bold text-zinc-950"><?= e(format_money($summary['expected_amount'])); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Verified Amount</p><p class="mt-2 text-2xl font-bold text-teal-700"><?= e(format_money($summary['verified_amount'])); ?></p></div>
    </div>

    <form class="mt-8 grid gap-4 rounded-md border border-zinc-200 bg-white p-5 md:grid-cols-5" method="GET" action="<?= site_url('admin/reports/payments'); ?>">
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
            <label class="form-label" for="payment_status">Payment Status</label>
            <select class="form-input" id="payment_status" name="payment_status">
                <option value="">All</option>
                <?php foreach ($payment_statuses as $payment_status): ?>
                    <option value="<?= e($payment_status); ?>" <?= ($filters['payment_status'] === $payment_status) ? 'selected' : ''; ?>><?= e(payment_status_label($payment_status)); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-end gap-3">
            <button class="btn-primary" type="submit">Apply</button>
            <a class="btn-secondary" href="<?= site_url('admin/reports/payments'); ?>">Reset</a>
        </div>
    </form>

    <?php if (empty($rows)): ?>
        <div class="mt-8 rounded-md border border-zinc-200 bg-white p-5 text-sm text-zinc-600">No payments matched the selected filters.</div>
    <?php else: ?>
        <div class="mt-8 overflow-x-auto rounded-md border border-zinc-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-100 text-zinc-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Request</th>
                        <th class="px-4 py-3 font-medium">Resident</th>
                        <th class="px-4 py-3 font-medium">Service</th>
                        <th class="px-4 py-3 font-medium">Amount</th>
                        <th class="px-4 py-3 font-medium">Method</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Submitted</th>
                        <th class="px-4 py-3 font-medium">Verified</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-zinc-950"><?= e($row['reference_no']); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e($row['resident_name']); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e($row['service_name']); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e(format_money($row['amount'])); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e(payment_method_label($row['payment_method'])); ?></td>
                            <td class="px-4 py-3"><span class="rounded-md px-2 py-1 text-xs font-medium <?= payment_status_badge_class($row['payment_status']); ?>"><?= e(payment_status_label($row['payment_status'])); ?></span></td>
                            <td class="px-4 py-3 text-zinc-700"><?= !empty($row['submitted_at']) ? e(date('M d, Y', strtotime($row['submitted_at']))) : 'Not submitted'; ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= !empty($row['verified_at']) ? e(date('M d, Y', strtotime($row['verified_at']))) : 'Not verified'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
