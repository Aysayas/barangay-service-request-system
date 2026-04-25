<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php
$data = array_merge($service ?? [], $old ?? []);
$is_edit = ($mode === 'edit');
$action = $is_edit ? site_url('admin/services/update/' . $service['id']) : site_url('admin/services/store');
?>

<section class="management-page mx-auto max-w-3xl">
    <div class="management-header">
        <div>
            <p class="page-kicker">Admin Service</p>
            <h1 class="management-title"><?= $is_edit ? 'Edit Service' : 'Create Service'; ?></h1>
        </div>
        <a class="btn-secondary" href="<?= site_url('admin/services'); ?>">Back to Services</a>
    </div>

    <form class="management-form space-y-5" method="POST" action="<?= $action; ?>">
        <?php csrf_field(); ?>

        <div>
            <label class="form-label" for="name">Name</label>
            <input class="form-input" id="name" name="name" value="<?= e($data['name'] ?? ''); ?>" required>
        </div>
        <div>
            <label class="form-label" for="slug">Slug</label>
            <input class="form-input" id="slug" name="slug" value="<?= e($data['slug'] ?? ''); ?>" placeholder="Leave blank to generate from name">
        </div>
        <div>
            <label class="form-label" for="description">Description</label>
            <textarea class="form-input min-h-32" id="description" name="description" required><?= e($data['description'] ?? ''); ?></textarea>
        </div>
        <div>
            <label class="form-label" for="requirements_text">Requirements</label>
            <textarea class="form-input min-h-32" id="requirements_text" name="requirements_text" required><?= e($data['requirements_text'] ?? ''); ?></textarea>
        </div>
        <div>
            <label class="form-label" for="fee">Fee</label>
            <input class="form-input" id="fee" type="number" step="0.01" min="0" name="fee" value="<?= e($data['fee'] ?? '0.00'); ?>" required>
        </div>
        <div class="management-checkbox-grid">
            <label class="flex items-center gap-3">
                <input type="checkbox" name="requires_payment" value="1" <?= ((int) ($data['requires_payment'] ?? 0) === 1) ? 'checked' : ''; ?>>
                Requires payment later
            </label>
            <label class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" <?= ((int) ($data['is_active'] ?? 1) === 1) ? 'checked' : ''; ?>>
                Active
            </label>
        </div>
        <div class="management-form-actions">
            <button class="btn-primary" type="submit"><?= $is_edit ? 'Save Changes' : 'Create Service'; ?></button>
        </div>
    </form>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
