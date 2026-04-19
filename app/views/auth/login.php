<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="mx-auto max-w-md">
    <div class="surface-card p-6">
        <p class="page-kicker">Account Access</p>
        <h1 class="mt-2 text-2xl font-bold text-slate-950">Login</h1>
        <p class="mt-2 text-sm text-slate-600">Access eBarangayHub using your resident, staff, or admin account.</p>

        <form class="mt-6 space-y-5" method="POST" action="<?= site_url('login'); ?>">
            <?php csrf_field(); ?>

            <div>
                <label class="form-label" for="email">Email address</label>
                <input class="form-input" id="email" type="email" name="email" value="<?= old_value($old, 'email'); ?>" required>
            </div>

            <div>
                <label class="form-label" for="password">Password</label>
                <div class="relative">
                    <input class="form-input pr-12" id="password" type="password" name="password" data-caps-lock-input="login_password" data-password-toggle-input="login_password" required>
                    <button class="password-toggle" type="button" data-password-toggle-button="login_password" aria-label="Show password" aria-pressed="false">
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
                <p id="password_caps_warning" class="mt-2 hidden rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900" data-caps-lock-warning="login_password">
                    Caps Lock is on. Passwords are case-sensitive.
                </p>
            </div>

            <button class="btn-primary w-full" type="submit">Login</button>
        </form>

        <p class="mt-5 text-center text-sm text-slate-600">
            Need a resident account?
            <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('register'); ?>">Register</a>
        </p>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
