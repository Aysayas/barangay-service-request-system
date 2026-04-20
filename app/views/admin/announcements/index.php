<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="management-page">
    <div class="management-header">
        <div>
            <p class="page-kicker">Admin</p>
            <h1 class="management-title">Announcements</h1>
            <p class="management-subtitle">Create and publish barangay announcements.</p>
        </div>
        <a class="btn-primary" href="<?= site_url('admin/announcements/create'); ?>">Create Announcement</a>
    </div>

    <?php if (empty($announcements)): ?>
        <div class="empty-state-strong mt-8">
            No announcements yet. Create one to show updates on the homepage.
        </div>
    <?php else: ?>
        <div class="management-table-wrap">
            <table class="management-table">
                <thead>
                    <tr>
                        <th class="px-4 py-3 font-medium">Title</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Published</th>
                        <th class="px-4 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $announcement): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-950"><?= e($announcement['title']); ?></p>
                                <p class="mt-1 text-xs text-slate-600"><?= e($announcement['slug']); ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-md px-2 py-1 text-xs font-medium <?= ((int) $announcement['is_published'] === 1) ? 'bg-teal-50 text-teal-900' : 'bg-slate-100 text-slate-800'; ?>">
                                    <?= ((int) $announcement['is_published'] === 1) ? 'Published' : 'Draft'; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <?= !empty($announcement['published_at']) ? e(date('M d, Y', strtotime($announcement['published_at']))) : 'Not published'; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a class="btn-secondary" href="<?= site_url('admin/announcements/edit/' . $announcement['id']); ?>">Edit</a>
                                    <form method="POST" action="<?= site_url('admin/announcements/toggle/' . $announcement['id']); ?>">
                                        <?php csrf_field(); ?>
                                        <button class="btn-secondary" type="submit" onclick="return confirm('Update this announcement publish status?');"><?= ((int) $announcement['is_published'] === 1) ? 'Unpublish' : 'Publish'; ?></button>
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
