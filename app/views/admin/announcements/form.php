<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php
$data = array_merge($announcement ?? [], $old ?? []);
$is_edit = ($mode === 'edit');
$action = $is_edit ? site_url('admin/announcements/update/' . $announcement['id']) : site_url('admin/announcements/store');
?>

<section class="mx-auto max-w-3xl">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Admin Announcement</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950"><?= $is_edit ? 'Edit Announcement' : 'Create Announcement'; ?></h1>
        </div>
        <a class="btn-secondary" href="<?= site_url('admin/announcements'); ?>">Back to Announcements</a>
    </div>

    <form class="mt-6 space-y-5 rounded-md border border-zinc-200 bg-white p-6" method="POST" action="<?= $action; ?>">
        <?php csrf_field(); ?>

        <div>
            <label class="form-label" for="title">Title</label>
            <input class="form-input" id="title" name="title" value="<?= e($data['title'] ?? ''); ?>" required>
        </div>
        <div>
            <label class="form-label" for="slug">Slug</label>
            <input class="form-input" id="slug" name="slug" value="<?= e($data['slug'] ?? ''); ?>" placeholder="Leave blank to generate from title">
        </div>
        <div>
            <label class="form-label" for="body">Content</label>
            <textarea class="form-input min-h-48" id="body" name="body" required><?= e($data['body'] ?? ''); ?></textarea>
        </div>
        <label class="flex items-center gap-3 text-sm text-zinc-700">
            <input type="checkbox" name="is_published" value="1" <?= ((int) ($data['is_published'] ?? 0) === 1) ? 'checked' : ''; ?>>
            Published
        </label>
        <button class="btn-primary" type="submit"><?= $is_edit ? 'Save Changes' : 'Create Announcement'; ?></button>
    </form>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
