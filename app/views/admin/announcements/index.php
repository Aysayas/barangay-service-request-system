<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Admin</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950">Announcements</h1>
            <p class="mt-3 max-w-2xl text-zinc-700">Create and publish barangay announcements.</p>
        </div>
        <a class="btn-primary" href="<?= site_url('admin/announcements/create'); ?>">Create Announcement</a>
    </div>

    <?php if (empty($announcements)): ?>
        <div class="mt-8 rounded-md border border-zinc-200 bg-white p-5 text-sm text-zinc-600">
            No announcements yet. Create one to show updates on the homepage.
        </div>
    <?php else: ?>
        <div class="mt-8 overflow-x-auto rounded-md border border-zinc-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-100 text-zinc-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Title</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Published</th>
                        <th class="px-4 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    <?php foreach ($announcements as $announcement): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium text-zinc-950"><?= e($announcement['title']); ?></p>
                                <p class="mt-1 text-xs text-zinc-600"><?= e($announcement['slug']); ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-md px-2 py-1 text-xs font-medium <?= ((int) $announcement['is_published'] === 1) ? 'bg-teal-50 text-teal-900' : 'bg-zinc-100 text-zinc-800'; ?>">
                                    <?= ((int) $announcement['is_published'] === 1) ? 'Published' : 'Draft'; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-700">
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
