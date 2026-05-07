<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Community Report</title>
    <style>
        body { color: #263238; font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; line-height: 1.45; margin: 0; }
        .page { padding: 24px; }
        .header { border-bottom: 3px solid #0097A7; margin-bottom: 18px; padding-bottom: 14px; }
        .brand { display: table; width: 100%; }
        .brand-logo { display: table-cell; vertical-align: middle; width: 220px; }
        .brand-logo img { max-height: 50px; max-width: 210px; }
        .brand-text { display: table-cell; text-align: right; vertical-align: middle; }
        .brand-title { color: #0D47A1; font-size: 17px; font-weight: bold; margin: 0; }
        .brand-subtitle { color: #455A64; font-size: 10px; margin: 4px 0 0; }
        h1 { color: #0D47A1; font-size: 21px; margin: 16px 0 4px; }
        h2 { border-bottom: 1px solid #D9E2EC; color: #0D47A1; font-size: 13px; margin: 18px 0 9px; padding-bottom: 5px; }
        .muted { color: #607D8B; }
        table { border-collapse: collapse; margin-bottom: 10px; width: 100%; }
        th, td { border: 1px solid #D9E2EC; padding: 6px 7px; text-align: left; vertical-align: top; }
        th { background: #EEF2F7; color: #263238; font-weight: bold; }
        .metric th { width: 35%; }
        .rows { font-size: 9px; }
        .badge { background: #EEF2F7; border: 1px solid #CBD5E1; border-radius: 4px; display: inline-block; font-size: 9px; font-weight: bold; padding: 2px 5px; }
        .section { page-break-inside: avoid; }
        .footer { border-top: 1px solid #D9E2EC; color: #607D8B; font-size: 9px; margin-top: 22px; padding-top: 9px; }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="brand">
                <div class="brand-logo">
                    <?php if (!empty($brand_logo_data_uri)): ?>
                        <img src="<?= e($brand_logo_data_uri); ?>" alt="eBarangayHub">
                    <?php else: ?>
                        <p class="brand-title">eBarangayHub</p>
                    <?php endif; ?>
                </div>
                <div class="brand-text">
                    <p class="brand-title">Community Report</p>
                    <p class="brand-subtitle">Centralized Barangay Services, Reports, and Community Access</p>
                </div>
            </div>
            <h1>Filtered Community Report</h1>
            <p class="muted">Generated on <?= e(date('M d, Y h:i A', strtotime($generated_at))); ?></p>
        </div>

        <div class="section">
            <h2>Filter Context</h2>
            <table class="metric">
                <?php foreach ($filter_context as $filter): ?>
                    <tr>
                        <th><?= e($filter['label']); ?></th>
                        <td><?= e($filter['value']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="section">
            <h2>Summary Metrics</h2>
            <table class="metric">
                <tr><th>Total Posts</th><td><?= e($summary['total_posts']); ?></td></tr>
                <tr><th>Published</th><td><?= e($summary['published_count']); ?></td></tr>
                <tr><th>Unpublished</th><td><?= e($summary['unpublished_count']); ?></td></tr>
                <tr><th>Featured</th><td><?= e($summary['featured_count']); ?></td></tr>
                <tr><th>Announcements</th><td><?= e($summary['announcement_count']); ?></td></tr>
                <tr><th>Events</th><td><?= e($summary['event_count']); ?></td></tr>
                <tr><th>Advisories</th><td><?= e($summary['advisory_count']); ?></td></tr>
                <tr><th>Programs</th><td><?= e($summary['program_count']); ?></td></tr>
                <tr><th>Resources</th><td><?= e($summary['resource_count']); ?></td></tr>
                <tr><th>Upcoming Events</th><td><?= e($summary['upcoming_event_count']); ?></td></tr>
            </table>
        </div>

        <div class="section">
            <h2>Community Rows</h2>
            <?php if (empty($rows)): ?>
                <p class="muted">No community records match the selected report filters.</p>
            <?php else: ?>
                <table class="rows">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Published</th>
                            <th>Featured</th>
                            <th>Published Date</th>
                            <th>Created</th>
                            <th>Event Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?= e($row['title']); ?></td>
                                <td><span class="badge"><?= e(community_category_label($row['category'])); ?></span></td>
                                <td><?= ((int) $row['is_published'] === 1) ? 'Published' : 'Unpublished'; ?></td>
                                <td><?= ((int) $row['is_featured'] === 1) ? 'Featured' : 'Standard'; ?></td>
                                <td><?= !empty($row['published_at']) ? e(date('M d, Y', strtotime($row['published_at']))) : 'Not published'; ?></td>
                                <td><?= e(date('M d, Y', strtotime($row['created_at']))); ?></td>
                                <td><?= !empty($row['event_date']) ? e(date('M d, Y', strtotime($row['event_date']))) : 'Not set'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="footer">
            PDF rows follow the web report table limit of up to <?= e($row_limit); ?> recent filtered records. Use CSV export for full spreadsheet-style analysis.
        </div>
    </div>
</body>
</html>
