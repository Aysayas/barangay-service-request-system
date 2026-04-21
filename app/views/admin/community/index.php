<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php $base_url = site_url('admin/community'); ?>

<section class="workflow-page">
    <div class="workflow-header">
        <div>
            <p class="workflow-kicker">Admin Community</p>
            <h1 class="workflow-title">Community Posts</h1>
            <p class="workflow-subtitle">
                Manage public updates, events, advisories, programs, and resources.
            </p>
        </div>
        <a class="btn-primary" href="<?= site_url('admin/community/create'); ?>">Create Post</a>
    </div>

    <div class="filter-card">
        <form class="grid gap-4 md:grid-cols-[0.7fr_1fr_auto]" method="GET" action="<?= $base_url; ?>">
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
            <p class="text-sm text-slate-600">No community posts yet.</p>
            <a class="mt-4 inline-action-link" href="<?= site_url('admin/community/create'); ?>">
                Create the first community post
            </a>
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
                            <td class="px-4 py-3 text-slate-700"><?= ((int) $post['is_published'] === 1) ? 'Yes' : 'No'; ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= ((int) $post['is_featured'] === 1) ? 'Yes' : 'No'; ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= e(date('M d, Y', strtotime($post['updated_at']))); ?></td>
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
