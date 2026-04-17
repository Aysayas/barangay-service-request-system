<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Admin Reports</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950">Community Reports</h1>
            <p class="mt-3 max-w-2xl text-zinc-700">Review public community content by category, publish state, featured state, and upcoming events.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="btn-primary" href="<?= e($export_url); ?>">Export CSV</a>
            <a class="btn-secondary" href="<?= site_url('admin/reports'); ?>">Back to Reports</a>
        </div>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Total Posts</p><p class="mt-2 text-2xl font-bold text-zinc-950"><?= e($summary['total_posts']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Published</p><p class="mt-2 text-2xl font-bold text-teal-700"><?= e($summary['published_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Unpublished</p><p class="mt-2 text-2xl font-bold text-amber-700"><?= e($summary['unpublished_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Featured</p><p class="mt-2 text-2xl font-bold text-teal-700"><?= e($summary['featured_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Announcements</p><p class="mt-2 text-2xl font-bold text-zinc-950"><?= e($summary['announcement_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Events</p><p class="mt-2 text-2xl font-bold text-amber-700"><?= e($summary['event_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Advisories</p><p class="mt-2 text-2xl font-bold text-rose-700"><?= e($summary['advisory_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Programs</p><p class="mt-2 text-2xl font-bold text-zinc-950"><?= e($summary['program_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Resources</p><p class="mt-2 text-2xl font-bold text-zinc-950"><?= e($summary['resource_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Upcoming Events</p><p class="mt-2 text-2xl font-bold text-amber-700"><?= e($summary['upcoming_event_count']); ?></p></div>
    </div>

    <form class="mt-8 grid gap-4 rounded-md border border-zinc-200 bg-white p-5 md:grid-cols-6" method="GET" action="<?= site_url('admin/reports/community'); ?>">
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
        <div class="mt-8 rounded-md border border-zinc-200 bg-white p-5 text-sm text-zinc-600">No community posts matched the selected filters.</div>
    <?php else: ?>
        <div class="mt-8 overflow-x-auto rounded-md border border-zinc-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-100 text-zinc-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Title</th>
                        <th class="px-4 py-3 font-medium">Category</th>
                        <th class="px-4 py-3 font-medium">Published</th>
                        <th class="px-4 py-3 font-medium">Featured</th>
                        <th class="px-4 py-3 font-medium">Published Date</th>
                        <th class="px-4 py-3 font-medium">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-zinc-950"><?= e($row['title']); ?></td>
                            <td class="px-4 py-3"><span class="rounded-md px-2 py-1 text-xs font-medium <?= community_category_badge_class($row['category']); ?>"><?= e(community_category_label($row['category'])); ?></span></td>
                            <td class="px-4 py-3 text-zinc-700"><?= ((int) $row['is_published'] === 1) ? 'Published' : 'Unpublished'; ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= ((int) $row['is_featured'] === 1) ? 'Featured' : 'Not featured'; ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= !empty($row['published_at']) ? e(date('M d, Y', strtotime($row['published_at']))) : 'Not published'; ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e(date('M d, Y', strtotime($row['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
