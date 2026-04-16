<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="mx-auto max-w-md">
    <div class="rounded-md border border-zinc-200 bg-white p-6">
        <h1 class="text-2xl font-bold text-zinc-950">Login</h1>
        <p class="mt-2 text-sm text-zinc-600">Use your resident, staff, or admin account.</p>

        <form class="mt-6 space-y-5" method="POST" action="<?= site_url('login'); ?>">
            <?php csrf_field(); ?>

            <div>
                <label class="form-label" for="email">Email address</label>
                <input class="form-input" id="email" type="email" name="email" value="<?= old_value($old, 'email'); ?>" required>
            </div>

            <div>
                <label class="form-label" for="password">Password</label>
                <input class="form-input" id="password" type="password" name="password" required>
            </div>

            <button class="btn-primary w-full" type="submit">Login</button>
        </form>

        <p class="mt-5 text-center text-sm text-zinc-600">
            Need a resident account?
            <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('register'); ?>">Register</a>
        </p>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
