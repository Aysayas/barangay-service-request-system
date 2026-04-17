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
            <div class="relative">
                <input class="form-input pr-12" id="password" type="password" name="password" data-caps-lock-input="admin_user_password" data-password-toggle-input="admin_user_password" <?= $is_edit ? '' : 'required'; ?>>
                <button class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-zinc-500 hover:text-teal-700" type="button" data-password-toggle-button="admin_user_password" aria-label="Show password" aria-pressed="false">
                    <svg data-password-eye xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <svg data-password-eye-off xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.58 10.58A2 2 0 0 0 13.42 13.42" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.88 5.42A9.63 9.63 0 0 1 12 5.25C18 5.25 21.75 12 21.75 12a18.5 18.5 0 0 1-3.18 4.12" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.62 6.62C3.87 8.44 2.25 12 2.25 12S6 18.75 12 18.75a9.8 9.8 0 0 0 4.06-.88" />
                    </svg>
                </button>
            </div>
            <p class="mt-2 text-xs text-zinc-600"><?= $is_edit ? 'Leave blank to keep the current password.' : 'Use at least 8 characters.'; ?></p>
            <p id="admin_user_password_caps_warning" class="mt-2 hidden rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900" data-caps-lock-warning="admin_user_password">
                Caps Lock is on. Passwords are case-sensitive.
            </p>
        </div>
        <div class="sm:col-span-2">
            <button class="btn-primary" type="submit"><?= $is_edit ? 'Save Changes' : 'Create Account'; ?></button>
        </div>
    </form>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
