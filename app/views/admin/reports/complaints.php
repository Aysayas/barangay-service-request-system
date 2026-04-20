<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="analytics-page">
    <div class="analytics-header">
        <div>
            <p class="page-kicker">Admin Reports</p>
            <h1 class="analytics-title">Complaint Reports</h1>
            <p class="analytics-subtitle">Review complaint totals by status, priority, category, and assigned staff.</p>
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
                    <span class="status-pill border-teal-200 bg-teal-50 text-teal-800">AI-Assisted</span>
                <?php endif; ?>
            </div>
            <p class="report-summary-text"><?= e($report_summary['text']); ?></p>
            <?php if (($report_summary['source'] ?? '') === 'fallback' && !empty($report_summary['fallback_reason']) && !in_array($report_summary['fallback_reason'], ['disabled', 'incomplete_config'], true)): ?>
                <p class="mt-3 text-xs text-slate-500">AI summary was unavailable, so eBarangayHub used the local fallback summary.</p>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <div class="report-metric-grid">
        <div class="report-metric-card"><p class="metric-label">Total</p><strong><?= e($summary['total_complaints']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Open</p><strong class="text-amber-700"><?= e($summary['open_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Submitted</p><strong><?= e($summary['submitted_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Under Review</p><strong class="text-amber-700"><?= e($summary['under_review_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Needs Info</p><strong class="text-rose-700"><?= e($summary['needs_info_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Investigating</p><strong class="text-amber-700"><?= e($summary['investigating_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Resolved</p><strong class="text-teal-700"><?= e($summary['resolved_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Closed</p><strong class="text-teal-700"><?= e($summary['closed_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Dismissed</p><strong class="text-rose-700"><?= e($summary['dismissed_count']); ?></strong></div>
        <div class="report-metric-card">
            <p class="metric-label">Most Common Category</p>
            <strong class="text-base"><?= !empty($summary['most_common_category']) ? e(complaint_category_label($summary['most_common_category'])) : 'None yet'; ?></strong>
            <p class="mt-1 text-xs text-slate-600"><?= e($summary['most_common_category_total']); ?> complaint(s)</p>
        </div>
    </div>

    <form class="filter-card mt-8 grid gap-4 md:grid-cols-6" method="GET" action="<?= site_url('admin/reports/complaints'); ?>">
        <div>
            <label class="form-label" for="from_date">From</label>
            <input class="form-input" id="from_date" type="date" name="from_date" value="<?= e($filters['from_date']); ?>">
        </div>
        <div>
            <label class="form-label" for="to_date">To</label>
            <input class="form-input" id="to_date" type="date" name="to_date" value="<?= e($filters['to_date']); ?>">
        </div>
        <div>
            <label class="form-label" for="category">Category</label>
            <select class="form-input" id="category" name="category">
                <option value="">All</option>
                <?php foreach ($categories as $category => $label): ?>
                    <option value="<?= e($category); ?>" <?= ($filters['category'] === $category) ? 'selected' : ''; ?>><?= e($label); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label" for="priority">Priority</label>
            <select class="form-input" id="priority" name="priority">
                <option value="">All</option>
                <?php foreach ($priorities as $priority): ?>
                    <option value="<?= e($priority); ?>" <?= ($filters['priority'] === $priority) ? 'selected' : ''; ?>><?= e(complaint_priority_label($priority)); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label" for="status">Status</label>
            <select class="form-input" id="status" name="status">
                <option value="">All</option>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status); ?>" <?= ($filters['status'] === $status) ? 'selected' : ''; ?>><?= e(complaint_status_label($status)); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-end gap-3">
            <button class="btn-primary" type="submit">Apply</button>
            <a class="btn-secondary" href="<?= site_url('admin/reports/complaints'); ?>">Reset</a>
        </div>
    </form>

    <?php if (empty($rows)): ?>
        <div class="empty-state mt-8">No complaints matched the selected filters.</div>
    <?php else: ?>
        <div class="data-table-wrap mt-8">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="px-4 py-3 font-medium">Reference</th>
                        <th class="px-4 py-3 font-medium">Complainant</th>
                        <th class="px-4 py-3 font-medium">Subject</th>
                        <th class="px-4 py-3 font-medium">Category</th>
                        <th class="px-4 py-3 font-medium">Priority</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Assigned Staff</th>
                        <th class="px-4 py-3 font-medium">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-950"><?= e($row['reference_no']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e($row['complainant_name']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e($row['subject']); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e(complaint_category_label($row['category'])); ?></td>
                            <td class="px-4 py-3"><span class="status-pill <?= complaint_priority_badge_class($row['priority']); ?>"><?= e(complaint_priority_label($row['priority'])); ?></span></td>
                            <td class="px-4 py-3"><span class="status-pill <?= complaint_status_badge_class($row['status']); ?>"><?= e(complaint_status_label($row['status'])); ?></span></td>
                            <td class="px-4 py-3 text-slate-700"><?= !empty($row['assigned_to_name']) ? e($row['assigned_to_name']) : 'Unassigned'; ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e(date('M d, Y', strtotime($row['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
