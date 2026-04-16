<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php
$data = array_merge($user_record ?? [], $old ?? []);
$is_edit = ($mode === 'edit');
$action = $is_edit ? site_url('admin/users/update/' . $user_record['id']) : site_url('admin/users/store');
$role_options = $is_edit ? ['resident', 'staff', 'admin'] : ['staff', 'admin'];
?>

<section class="mx-auto max-w-3xl">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Admin User</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950"><?= $is_edit ? 'Edit User' : 'Create Staff/Admin'; ?></h1>
        </div>
        <a class="btn-secondary" href="<?= site_url('admin/users'); ?>">Back to Users</a>
    </div>

    <form class="mt-6 grid gap-5 rounded-md border border-zinc-200 bg-white p-6 sm:grid-cols-2" method="POST" action="<?= $action; ?>">
        <?php csrf_field(); ?>

        <div>
            <label class="form-label" for="first_name">First name</label>
            <input class="form-input" id="first_name" name="first_name" value="<?= e($data['first_name'] ?? ''); ?>" required>
        </div>
        <div>
            <label class="form-label" for="last_name">Last name</label>
            <input class="form-input" id="last_name" name="last_name" value="<?= e($data['last_name'] ?? ''); ?>" required>
        </div>
        <div class="sm:col-span-2">
            <label class="form-label" for="email">Email</label>
            <input class="form-input" id="email" type="email" name="email" value="<?= e($data['email'] ?? ''); ?>" required>
        </div>
        <div>
            <label class="form-label" for="role">Role</label>
            <select class="form-input" id="role" name="role" required>
                <?php foreach ($role_options as $role): ?>
                    <option value="<?= e($role); ?>" <?= (($data['role'] ?? 'staff') === $role) ? 'selected' : ''; ?>><?= e(ucfirst($role)); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label" for="status">Status</label>
            <select class="form-input" id="status" name="status" required>
                <option value="active" <?= (($data['status'] ?? 'active') === 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?= (($data['status'] ?? '') === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
        <div>
            <label class="form-label" for="contact_number">Contact number</label>
            <input class="form-input" id="contact_number" name="contact_number" value="<?= e($data['contact_number'] ?? ''); ?>">
        </div>
        <div>
            <label class="form-label" for="address">Address</label>
            <input class="form-input" id="address" name="address" value="<?= e($data['address'] ?? ''); ?>">
        </div>
        <div class="sm:col-span-2">
            <label class="form-label" for="password"><?= $is_edit ? 'New password' : 'Password'; ?></label>
            <input class="form-input" id="password" type="password" name="password" <?= $is_edit ? '' : 'required'; ?>>
            <p class="mt-2 text-xs text-zinc-600"><?= $is_edit ? 'Leave blank to keep the current password.' : 'Use at least 8 characters.'; ?></p>
        </div>
        <div class="sm:col-span-2">
            <button class="btn-primary" type="submit"><?= $is_edit ? 'Save Changes' : 'Create Account'; ?></button>
        </div>
    </form>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
