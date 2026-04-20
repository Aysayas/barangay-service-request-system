<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>
<?php $charts_json = json_encode($charts ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>

<section class="analytics-page">
    <div class="analytics-header">
        <div>
            <p class="page-kicker">Admin Reports</p>
            <h1 class="analytics-title">Reports and Summary Data</h1>
            <p class="analytics-subtitle">
                Review reusable report data for requests, payments, complaints, and community content. Each detailed report page can export its filtered table as a CSV file.
            </p>
        </div>
        <div class="analytics-actions">
            <a class="btn-primary" href="<?= site_url('admin/reports/export'); ?>">Export Summary CSV</a>
            <a class="btn-secondary" href="<?= site_url('admin/dashboard'); ?>">Back to Dashboard</a>
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
        <div class="metric-card">
            <p class="metric-label">Total Requests</p>
            <p class="metric-value"><?= e($summary['total_requests']); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Verified Payments</p>
            <p class="metric-value text-teal-700"><?= e($summary['verified_payments']); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Open Complaints</p>
            <p class="metric-value text-amber-700"><?= e($summary['open_complaints']); ?></p>
        </div>
        <div class="metric-card">
            <p class="metric-label">Published Community Posts</p>
            <p class="metric-value text-teal-700"><?= e($summary['published_posts']); ?></p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <a class="report-link-card" href="<?= site_url('admin/reports/requests'); ?>">
            <h2 class="text-lg font-semibold text-slate-950">Request Reports</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Filter service requests by date, service, and status. Includes payment and final document availability.</p>
        </a>
        <a class="report-link-card" href="<?= site_url('admin/reports/payments'); ?>">
            <h2 class="text-lg font-semibold text-slate-950">Payment Reports</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Track simulated payment records, expected amounts, verified amounts, and payment review status.</p>
        </a>
        <a class="report-link-card" href="<?= site_url('admin/reports/complaints'); ?>">
            <h2 class="text-lg font-semibold text-slate-950">Complaint Reports</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Summarize complaint categories, statuses, priorities, assignments, and active workload.</p>
        </a>
        <a class="report-link-card" href="<?= site_url('admin/reports/community'); ?>">
            <h2 class="text-lg font-semibold text-slate-950">Community Reports</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Review community posts by category, publishing state, featured state, and upcoming events.</p>
        </a>
    </div>

    <section>
        <div>
            <h2 class="text-2xl font-bold text-slate-950">Charts Dashboard</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                Visual summaries use live system data from the same reporting layer. Detailed filters and exports remain on each report page.
            </p>
        </div>

        <div class="mt-5 grid gap-6 lg:grid-cols-2">
            <div class="chart-card">
                <h3 class="text-lg font-semibold text-slate-950">Requests by Status</h3>
                <p class="mt-1 text-sm text-slate-600">Current service request workload by workflow state.</p>
                <div class="chart-frame">
                    <canvas id="requestStatusChart"></canvas>
                    <p id="requestStatusChartEmpty" class="compact-note hidden">No request status data yet.</p>
                </div>
            </div>

            <div class="chart-card">
                <h3 class="text-lg font-semibold text-slate-950">Requests by Service</h3>
                <p class="mt-1 text-sm text-slate-600">Most requested barangay services.</p>
                <div class="chart-frame">
                    <canvas id="requestServiceChart"></canvas>
                    <p id="requestServiceChartEmpty" class="compact-note hidden">No service request data yet.</p>
                </div>
            </div>

            <div class="chart-card">
                <h3 class="text-lg font-semibold text-slate-950">Requests by Month</h3>
                <p class="mt-1 text-sm text-slate-600">Monthly request volume for the last 12 months.</p>
                <div class="chart-frame">
                    <canvas id="requestMonthlyChart"></canvas>
                    <p id="requestMonthlyChartEmpty" class="compact-note hidden">No monthly request data yet.</p>
                </div>
            </div>

            <div class="chart-card">
                <h3 class="text-lg font-semibold text-slate-950">Payments by Status</h3>
                <p class="mt-1 text-sm text-slate-600">Simulated payment records by review state.</p>
                <div class="chart-frame">
                    <canvas id="paymentStatusChart"></canvas>
                    <p id="paymentStatusChartEmpty" class="compact-note hidden">No payment data yet.</p>
                </div>
            </div>

            <div class="chart-card">
                <h3 class="text-lg font-semibold text-slate-950">Complaints by Status</h3>
                <p class="mt-1 text-sm text-slate-600">Complaint workload by handling state.</p>
                <div class="chart-frame">
                    <canvas id="complaintStatusChart"></canvas>
                    <p id="complaintStatusChartEmpty" class="compact-note hidden">No complaint status data yet.</p>
                </div>
            </div>

            <div class="chart-card">
                <h3 class="text-lg font-semibold text-slate-950">Complaints by Category</h3>
                <p class="mt-1 text-sm text-slate-600">Common complaint types submitted by residents.</p>
                <div class="chart-frame">
                    <canvas id="complaintCategoryChart"></canvas>
                    <p id="complaintCategoryChartEmpty" class="compact-note hidden">No complaint category data yet.</p>
                </div>
            </div>

            <div class="chart-card">
                <h3 class="text-lg font-semibold text-slate-950">Community Posts by Category</h3>
                <p class="mt-1 text-sm text-slate-600">Published and managed community content grouped by type.</p>
                <div class="chart-frame">
                    <canvas id="communityCategoryChart"></canvas>
                    <p id="communityCategoryChartEmpty" class="compact-note hidden">No community category data yet.</p>
                </div>
            </div>

            <div class="chart-card">
                <h3 class="text-lg font-semibold text-slate-950">Community Publishing State</h3>
                <p class="mt-1 text-sm text-slate-600">Published, unpublished, and featured community posts.</p>
                <div class="chart-frame">
                    <canvas id="communityPublishChart"></canvas>
                    <p id="communityPublishChartEmpty" class="compact-note hidden">No community publishing data yet.</p>
                </div>
            </div>
        </div>
    </section>
</section>

<script src="<?= app_asset('js/chart.umd.js'); ?>"></script>
<script>
    (function () {
        var charts = <?= $charts_json ?: '{}'; ?>;
        var palette = [
            '#0f766e',
            '#f59e0b',
            '#e11d48',
            '#3f3f46',
            '#0284c7',
            '#16a34a',
            '#4d7c0f',
            '#dc2626',
            '#64748b',
            '#0891b2',
            '#ca8a04',
            '#be123c'
        ];

        function hasValues(chartData) {
            return chartData && Array.isArray(chartData.values) && chartData.values.some(function (value) {
                return Number(value) > 0;
            });
        }

        function showEmpty(canvasId, message) {
            var canvas = document.getElementById(canvasId);
            var empty = document.getElementById(canvasId + 'Empty');

            if (canvas) {
                canvas.classList.add('hidden');
            }

            if (empty) {
                empty.textContent = message || empty.textContent;
                empty.classList.remove('hidden');
            }
        }

        function renderChart(canvasId, chartKey, type, extraOptions) {
            var canvas = document.getElementById(canvasId);
            var chartData = charts[chartKey] || { labels: [], values: [] };
            var circular = ['doughnut', 'pie', 'polarArea'].indexOf(type) !== -1;
            var indexAxis = extraOptions && extraOptions.indexAxis ? extraOptions.indexAxis : 'x';

            if (!canvas) {
                return;
            }

            if (typeof Chart === 'undefined') {
                showEmpty(canvasId, 'Chart library could not load. Check public/assets/js/chart.umd.js and refresh the page.');
                return;
            }

            if (!hasValues(chartData)) {
                showEmpty(canvasId);
                return;
            }

            new Chart(canvas, {
                type: type,
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Count',
                        data: chartData.values,
                        backgroundColor: circular ? palette : palette[0],
                        borderColor: type === 'line' ? palette[0] : '#ffffff',
                        borderWidth: type === 'line' ? 2 : 1,
                        fill: type === 'line',
                        tension: 0.35,
                        pointBackgroundColor: palette[0],
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: indexAxis,
                    plugins: {
                        legend: {
                            display: circular,
                            position: 'bottom'
                        }
                    },
                    scales: circular ? {} : {
                        x: {
                            beginAtZero: indexAxis === 'y',
                            ticks: {
                                precision: 0
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        renderChart('requestStatusChart', 'request_status', 'doughnut');
        renderChart('requestServiceChart', 'request_service', 'bar', { indexAxis: 'y' });
        renderChart('requestMonthlyChart', 'request_monthly', 'line');
        renderChart('paymentStatusChart', 'payment_status', 'doughnut');
        renderChart('complaintStatusChart', 'complaint_status', 'bar');
        renderChart('complaintCategoryChart', 'complaint_category', 'bar', { indexAxis: 'y' });
        renderChart('communityCategoryChart', 'community_category', 'bar');
        renderChart('communityPublishChart', 'community_publish', 'doughnut');
    })();
</script>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
