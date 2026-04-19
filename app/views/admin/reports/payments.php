<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="page-kicker">Admin Reports</p>
            <h1 class="page-title">Payment Reports</h1>
            <p class="page-subtitle">Track simulated payment records, verification status, and payment amounts.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="btn-primary" href="<?= e($export_url); ?>">Export CSV</a>
            <a class="btn-secondary" href="<?= site_url('admin/reports'); ?>">Back to Reports</a>
        </div>
    </div>

    <?php if (!empty($report_summary['text'])): ?>
        <section class="surface-card mt-8">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="page-kicker">Report Summary</p>
                    <h2 class="mt-1 text-lg font-semibold text-slate-950"><?= e($report_summary['source_label'] ?? 'Summary'); ?></h2>
                </div>
                <?php if (($report_summary['source'] ?? '') === 'fallback'): ?>
                    <span class="status-pill border-slate-200 bg-slate-100 text-slate-700">Fallback</span>
                <?php else: ?>
                    <span class="status-pill border-teal-200 bg-teal-50 text-teal-800">AI-Assisted</span>
                <?php endif; ?>
            </div>
            <p class="mt-3 text-sm leading-6 text-slate-600"><?= e($report_summary['text']); ?></p>
            <?php if (($report_summary['source'] ?? '') === 'fallback' && !empty($report_summary['fallback_reason']) && !in_array($report_summary['fallback_reason'], ['disabled', 'incomplete_config'], true)): ?>
                <p class="mt-3 text-xs text-zinc-500">AI summary was unavailable, so eBarangayHub used the local fallback summary.</p>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Payment Records</p><p class="mt-2 text-2xl font-bold text-zinc-950"><?= e($summary['total_payments']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Pending Payment</p><p class="mt-2 text-2xl font-bold text-amber-700"><?= e($summary['pending_payment_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Payment Submitted</p><p class="mt-2 text-2xl font-bold text-zinc-950"><?= e($summary['payment_submitted_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Payment Verified</p><p class="mt-2 text-2xl font-bold text-teal-700"><?= e($summary['payment_verified_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Payment Rejected</p><p class="mt-2 text-2xl font-bold text-rose-700"><?= e($summary['payment_rejected_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Expected Amount</p><p class="mt-2 text-2xl font-bold text-zinc-950"><?= e(format_money($summary['expected_amount'])); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Verified Amount</p><p class="mt-2 text-2xl font-bold text-teal-700"><?= e(format_money($summary['verified_amount'])); ?></p></div>
    </div>

    <form class="filter-card mt-8 grid gap-4 md:grid-cols-5" method="GET" action="<?= site_url('admin/reports/payments'); ?>">
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
        <div class="empty-state mt-8">No payments matched the selected filters.</div>
    <?php else: ?>
        <div class="data-table-wrap mt-8">
            <table class="data-table">
                <thead>
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
                            <td class="px-4 py-3"><span class="status-pill <?= payment_status_badge_class($row['payment_status']); ?>"><?= e(payment_status_label($row['payment_status'])); ?></span></td>
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
