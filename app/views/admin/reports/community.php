<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="analytics-page">
    <div class="analytics-header">
        <div>
            <p class="page-kicker">Admin Reports</p>
            <h1 class="analytics-title">Community Reports</h1>
            <p class="analytics-subtitle">Review public community content by category, publish state, featured state, and upcoming events.</p>
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

    <div class="report-metric-grid-wide">
        <div class="report-metric-card"><p class="metric-label">Total Posts</p><strong><?= e($summary['total_posts']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Published</p><strong class="text-teal-700"><?= e($summary['published_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Unpublished</p><strong class="text-amber-700"><?= e($summary['unpublished_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Featured</p><strong class="text-teal-700"><?= e($summary['featured_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Announcements</p><strong><?= e($summary['announcement_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Events</p><strong class="text-amber-700"><?= e($summary['event_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Advisories</p><strong class="text-rose-700"><?= e($summary['advisory_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Programs</p><strong><?= e($summary['program_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Resources</p><strong><?= e($summary['resource_count']); ?></strong></div>
        <div class="report-metric-card"><p class="metric-label">Upcoming Events</p><strong class="text-amber-700"><?= e($summary['upcoming_event_count']); ?></strong></div>
    </div>

    <form class="filter-card mt-8 grid gap-4 md:grid-cols-6" method="GET" action="<?= site_url('admin/reports/community'); ?>">
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
            <label class="form-label" for="is_published">Published</label>
            <select class="form-input" id="is_published" name="is_published">
                <option value="">All</option>
                <option value="1" <?= ($filters['is_published'] === '1') ? 'selected' : ''; ?>>Published</option>
                <option value="0" <?= ($filters['is_published'] === '0') ? 'selected' : ''; ?>>Unpublished</option>
            </select>
        </div>
        <div>
            <label class="form-label" for="is_featured">Featured</label>
            <select class="form-input" id="is_featured" name="is_featured">
                <option value="">All</option>
                <option value="1" <?= ($filters['is_featured'] === '1') ? 'selected' : ''; ?>>Featured</option>
                <option value="0" <?= ($filters['is_featured'] === '0') ? 'selected' : ''; ?>>Not Featured</option>
            </select>
        </div>
        <div class="flex items-end gap-3">
            <button class="btn-primary" type="submit">Apply</button>
            <a class="btn-secondary" href="<?= site_url('admin/reports/community'); ?>">Reset</a>
        </div>
    </form>

    <?php if (empty($rows)): ?>
        <div class="empty-state mt-8">No community posts matched the selected filters.</div>
    <?php else: ?>
        <div class="data-table-wrap mt-8">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="px-4 py-3 font-medium">Title</th>
                        <th class="px-4 py-3 font-medium">Category</th>
                        <th class="px-4 py-3 font-medium">Published</th>
                        <th class="px-4 py-3 font-medium">Featured</th>
                        <th class="px-4 py-3 font-medium">Published Date</th>
                        <th class="px-4 py-3 font-medium">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-950"><?= e($row['title']); ?></td>
                            <td class="px-4 py-3"><span class="status-pill <?= community_category_badge_class($row['category']); ?>"><?= e(community_category_label($row['category'])); ?></span></td>
                            <td class="px-4 py-3 text-slate-700"><?= ((int) $row['is_published'] === 1) ? 'Published' : 'Unpublished'; ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= ((int) $row['is_featured'] === 1) ? 'Featured' : 'Not featured'; ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= !empty($row['published_at']) ? e(date('M d, Y', strtotime($row['published_at']))) : 'Not published'; ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e(date('M d, Y', strtotime($row['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
