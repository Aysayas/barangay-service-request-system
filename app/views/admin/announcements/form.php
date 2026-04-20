<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php
$data = array_merge($announcement ?? [], $old ?? []);
$is_edit = ($mode === 'edit');
$action = $is_edit ? site_url('admin/announcements/update/' . $announcement['id']) : site_url('admin/announcements/store');
?>

<section class="management-page mx-auto max-w-3xl">
    <div class="management-header">
        <div>
            <p class="page-kicker">Admin Announcement</p>
            <h1 class="management-title"><?= $is_edit ? 'Edit Announcement' : 'Create Announcement'; ?></h1>
        </div>
        <a class="btn-secondary" href="<?= site_url('admin/announcements'); ?>">Back to Announcements</a>
    </div>

    <form class="management-form space-y-5" method="POST" action="<?= $action; ?>">
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
        <label class="flex items-center gap-3 text-sm text-slate-700">
            <input type="checkbox" name="is_published" value="1" <?= ((int) ($data['is_published'] ?? 0) === 1) ? 'checked' : ''; ?>>
            Published
        </label>
        <button class="btn-primary" type="submit"><?= $is_edit ? 'Save Changes' : 'Create Announcement'; ?></button>
    </form>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
