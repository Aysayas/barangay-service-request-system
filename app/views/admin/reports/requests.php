<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="analytics-page">
    <div class="analytics-header">
        <div>
            <p class="page-kicker">Admin Reports</p>
            <h1 class="analytics-title">Request Reports</h1>
            <p class="analytics-subtitle">Filter service requests and review request workflow totals.</p>
        </div>
        <div class="analytics-actions">
            <a class="btn-primary" href="<?= e($export_url); ?>">Export CSV</a>
            <a class="btn-secondary" href="<?= site_url('admin/reports'); ?>">Back to Reports</a>
        </div>
    </div>

    <?php if (!empty($report_summary['text'])): ?>
        <section class="report-summary-card">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="page-kicker">Report Summary</p>
                    <h2 class="mt-1 text-lg font-semibold text-slate-950"><?= e($report_summary['source_label'] ?? 'Summary'); ?></h2>
                </div>
                <?php if (($report_summary['source'] ?? '') === 'fallback'): ?>
                    <span class="status-pill border-slate-200 bg-slate-100 text-slate-700">Fallback</span>
                <?php else: ?>
                    <span class="status-pill border-blue-200 bg-blue-50 text-blue-800">AI-Assisted</span>
                <?php endif; ?>
            </div>
            <p class="report-summary-text"><?= e($report_summary['text']); ?></p>
            <?php if (($report_summary['source'] ?? '') === 'fallback' && !empty($report_summary['fallback_reason']) && !in_array($report_summary['fallback_reason'], ['disabled', 'incomplete_config'], true)): ?>
                <p class="mt-3 text-xs text-slate-500">AI summary was unavailable, so eBarangayHub used the local fallback summary.</p>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <div class="report-metric-grid">
        <div class="report-metric-card"><p class="metric-label">Total</p><strong><?= e($summary['total_requests']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Submitted</p><strong class="text-amber-700"><?= e($summary['submitted_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Under Review</p><strong class="text-amber-700"><?= e($summary['under_review_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Needs Info</p><strong class="text-rose-700"><?= e($summary['needs_info_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Approved</p><strong class="text-teal-700"><?= e($summary['approved_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Rejected</p><strong class="text-rose-700"><?= e($summary['rejected_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Ready for Pickup</p><strong class="text-amber-700"><?= e($summary['ready_for_pickup_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Released</p><strong class="text-teal-700"><?= e($summary['released_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">This Month</p><strong><?= e($summary['requests_this_month']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Most Requested</p><strong class="text-base"><?= e($summary['most_requested_service']); ?></strong><p class="mt-1 text-xs text-slate-600"><?= e($summary['most_requested_service_total']); ?> request(s)</p></div>
    </div>

    <form class="filter-card report-filter-grid report-filter-grid-5" method="GET" action="<?= site_url('admin/reports/requests'); ?>">
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
        <div class="report-filter-actions">
            <button class="btn-primary" type="submit">Apply</button>
            <a class="btn-secondary" href="<?= site_url('admin/reports/requests'); ?>">Reset</a>
        </div>
    </form>

    <?php if (empty($rows)): ?>
        <div class="empty-state mt-8">
            <h2 class="text-lg font-semibold text-slate-950">No request report data matches the selected filters.</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Adjust the date range, choose another service or status, or reset the filters to review all request records.
            </p>
            <div class="mt-5 flex flex-wrap justify-center gap-3">
                <a class="btn-secondary" href="<?= site_url('admin/reports/requests'); ?>">Reset Filters</a>
                <a class="btn-secondary" href="<?= site_url('admin/reports'); ?>">Back to Reports</a>
            </div>
        </div>
    <?php else: ?>
        <div class="data-table-wrap">
            <table class="data-table report-table-wide">
                <thead>
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
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-950"><?= e($row['reference_no']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e($row['resident_name']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e($row['service_name']); ?></td>
                            <td class="px-4 py-3"><span class="status-pill <?= status_badge_class($row['status']); ?>"><?= e(status_label($row['status'])); ?></span></td>
                            <td class="px-4 py-3">
                                <?php if ((int) $row['requires_payment'] === 1): ?>
                                    <span class="status-pill <?= payment_status_badge_class($row['payment_status']); ?>"><?= e(payment_status_label($row['payment_status'])); ?></span>
                                <?php else: ?>
                                    <span class="text-xs text-slate-600">Not required</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?= ((int) $row['has_final_document'] === 1) ? 'Available' : 'None'; ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e(date('M d, Y', strtotime($row['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
