<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php
$base_url = site_url('admin/community');
$has_filters = ($current_category !== 'all') || ($search !== '');
?>

<section class="workflow-page">
    <div class="workflow-header">
        <div>
            <p class="workflow-kicker">Admin Community</p>
            <h1 class="workflow-title">Community Posts</h1>
            <p class="workflow-subtitle">
                Manage public updates, events, advisories, programs, resources, and featured placements.
            </p>
        </div>
        <a class="btn-primary" href="<?= site_url('admin/community/create'); ?>">Create Post</a>
    </div>

    <div class="filter-card">
        <form class="grid gap-4 md:grid-cols-[0.7fr_1fr_auto]" method="GET" action="<?= $base_url; ?>">
            <div class="md:col-span-3">
                <p class="compact-note">Use filters to find public content by category or keyword while keeping publishing actions unchanged.</p>
            </div>
            <div>
                <label class="form-label" for="category">Category</label>
                <select class="form-input" id="category" name="category">
                    <option value="all" <?= ($current_category === 'all') ? 'selected' : ''; ?>>All</option>
                    <?php foreach ($categories as $value => $label): ?>
                        <option value="<?= e($value); ?>" <?= ($current_category === $value) ? 'selected' : ''; ?>>
                            <?= e($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" for="search">Search</label>
                <input class="form-input" id="search" type="text" name="search" value="<?= e($search); ?>" placeholder="Title, excerpt, or content">
            </div>
            <div class="workflow-filter-actions">
                <button class="btn-primary" type="submit">Apply</button>
                <a class="btn-secondary" href="<?= $base_url; ?>">Reset</a>
            </div>
        </form>
    </div>

    <?php if (empty($posts)): ?>
        <div class="empty-state-strong mt-8">
            <?php if ($has_filters): ?>
                <h2 class="text-lg font-semibold text-slate-950">No community posts match the current filters.</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Try another category, search with different words, or reset the filters.
                </p>
                <div class="mt-5 flex flex-wrap justify-center gap-3">
                    <a class="btn-secondary" href="<?= $base_url; ?>">Reset Filters</a>
                    <a class="btn-primary" href="<?= site_url('admin/community/create'); ?>">Create Post</a>
                </div>
            <?php else: ?>
                <h2 class="text-lg font-semibold text-slate-950">No community posts have been created yet.</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Create public updates, events, advisories, programs, or resources for residents.
                </p>
                <a class="btn-primary mt-5" href="<?= site_url('admin/community/create'); ?>">Create First Post</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="workflow-table-wrap">
            <table class="workflow-table workflow-table-wide">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Title</th>
                        <th class="px-4 py-3 font-medium">Category</th>
                        <th class="px-4 py-3 font-medium">Published</th>
                        <th class="px-4 py-3 font-medium">Featured</th>
                        <th class="px-4 py-3 font-medium">Updated</th>
                        <th class="px-4 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-950"><?= e($post['title']); ?></p>
                                <p class="mt-1 text-xs text-slate-600"><?= e($post['slug']); ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="status-pill <?= community_category_badge_class($post['category']); ?>">
                                    <?= e(community_category_label($post['category'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="status-pill <?= ((int) $post['is_published'] === 1) ? 'badge-success' : 'badge-neutral'; ?>">
                                    <?= ((int) $post['is_published'] === 1) ? 'Published' : 'Unpublished'; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="status-pill <?= ((int) $post['is_featured'] === 1) ? 'badge-info' : 'badge-neutral'; ?>">
                                    <?= ((int) $post['is_featured'] === 1) ? 'Featured' : 'Standard'; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <p><?= e(date('M d, Y', strtotime($post['updated_at']))); ?></p>
                                <?php if (!empty($post['updated_by_name'])): ?>
                                    <p class="mt-1 text-xs text-slate-500">by <?= e($post['updated_by_name']); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="management-row-actions">
                                    <a class="btn-secondary" href="<?= site_url('admin/community/edit/' . $post['id']); ?>">Edit</a>
                                    <form method="POST" action="<?= site_url('admin/community/toggle/' . $post['id']); ?>">
                                        <?php csrf_field(); ?>
                                        <button class="btn-secondary" type="submit"><?= ((int) $post['is_published'] === 1) ? 'Unpublish' : 'Publish'; ?></button>
                                    </form>
                                    <form method="POST" action="<?= site_url('admin/community/feature/' . $post['id']); ?>">
                                        <?php csrf_field(); ?>
                                        <button class="btn-secondary" type="submit"><?= ((int) $post['is_featured'] === 1) ? 'Unfeature' : 'Feature'; ?></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
