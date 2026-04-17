<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="mx-auto max-w-2xl">
    <div class="rounded-md border border-zinc-200 bg-white p-6">
        <h1 class="text-2xl font-bold text-zinc-950">Resident Registration</h1>
        <p class="mt-2 text-sm text-zinc-600">Create your eBarangayHub resident account for online barangay services.</p>

        <form class="mt-6 grid gap-5 sm:grid-cols-2" method="POST" action="<?= site_url('register'); ?>">
            <?php csrf_field(); ?>

            <div>
                <label class="form-label" for="first_name">First name</label>
                <input class="form-input" id="first_name" type="text" name="first_name" value="<?= old_value($old, 'first_name'); ?>" required>
            </div>

            <div>
                <label class="form-label" for="last_name">Last name</label>
                <input class="form-input" id="last_name" type="text" name="last_name" value="<?= old_value($old, 'last_name'); ?>" required>
            </div>

            <div class="sm:col-span-2">
                <label class="form-label" for="email">Email address</label>
                <input class="form-input" id="email" type="email" name="email" value="<?= old_value($old, 'email'); ?>" required>
            </div>

            <div>
                <label class="form-label" for="contact_number">Contact number</label>
                <input class="form-input" id="contact_number" type="text" name="contact_number" value="<?= old_value($old, 'contact_number'); ?>">
            </div>

            <div>
                <label class="form-label" for="address">Address</label>
                <input class="form-input" id="address" type="text" name="address" value="<?= old_value($old, 'address'); ?>" required>
            </div>

            <div>
                <label class="form-label" for="password">Password</label>
                <input class="form-input" id="password" type="password" name="password" data-caps-lock-input="password" minlength="8" required>
                <p id="password_caps_warning" class="mt-2 hidden rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900" data-caps-lock-warning="password">
                    Caps Lock is on. Passwords are case-sensitive.
                </p>
            </div>

            <div>
                <label class="form-label" for="password_confirm">Confirm password</label>
                <input class="form-input" id="password_confirm" type="password" name="password_confirm" data-caps-lock-input="password_confirm" minlength="8" required>
                <p id="password_confirm_caps_warning" class="mt-2 hidden rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900" data-caps-lock-warning="password_confirm">
                    Caps Lock is on. Passwords are case-sensitive.
                </p>
            </div>

            <div class="sm:col-span-2">
                <button class="btn-primary w-full" type="submit">Create Account</button>
            </div>
        </form>

        <p class="mt-5 text-center text-sm text-zinc-600">
            Already registered?
            <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('login'); ?>">Login</a>
        </p>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
