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
            <p class="mt-2 text-xs text-slate-600">Use the official service name residents will recognize.</p>
        </div>
        <div>
            <label class="form-label" for="slug">Slug</label>
            <input class="form-input" id="slug" name="slug" value="<?= e($data['slug'] ?? ''); ?>" placeholder="Leave blank to generate from name">
            <p class="mt-2 text-xs text-slate-600">Optional. Leave blank unless you need a specific URL-friendly identifier.</p>
        </div>
        <div>
            <label class="form-label" for="description">Description</label>
            <textarea class="form-input min-h-32" id="description" name="description" required><?= e($data['description'] ?? ''); ?></textarea>
            <p class="mt-2 text-xs text-slate-600">Explain what the service is for and when residents should request it.</p>
        </div>
        <div>
            <label class="form-label" for="requirements_text">Requirements</label>
            <textarea class="form-input min-h-32" id="requirements_text" name="requirements_text" required><?= e($data['requirements_text'] ?? ''); ?></textarea>
            <p class="mt-2 text-xs text-slate-600">List the documents or details residents must upload before staff review.</p>
        </div>
        <div>
            <label class="form-label" for="fee">Fee</label>
            <input class="form-input" id="fee" type="number" step="0.01" min="0" name="fee" value="<?= e($data['fee'] ?? '0.00'); ?>" required>
            <p class="mt-2 text-xs text-slate-600">Use 0.00 for free services. If payment is required, residents will submit payment proof for staff review.</p>
        </div>
        <div class="management-checkbox-grid">
            <label class="flex items-center gap-3">
                <input type="checkbox" name="requires_payment" value="1" <?= ((int) ($data['requires_payment'] ?? 0) === 1) ? 'checked' : ''; ?>>
                Requires payment proof review
            </label>
            <label class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" <?= ((int) ($data['is_active'] ?? 1) === 1) ? 'checked' : ''; ?>>
                Active and visible to residents
            </label>
        </div>
        <div class="management-form-actions">
            <button class="btn-primary" type="submit"><?= $is_edit ? 'Save Changes' : 'Create Service'; ?></button>
        </div>
    </form>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
