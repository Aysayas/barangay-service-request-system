<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php $user = auth_user(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'eBarangayHub'); ?></title>
    <link rel="stylesheet" href="<?= app_asset('css/output.css'); ?>">
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-900">
    <header class="border-b border-zinc-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
            <a href="<?= site_url('/'); ?>" class="text-base font-semibold tracking-normal text-zinc-950">
                eBarangayHub
            </a>

            <nav class="flex items-center gap-3 text-sm">
                <?php if (!empty($user)): ?>
                    <a class="text-zinc-700 hover:text-teal-700" href="<?= site_url('dashboard'); ?>">Dashboard</a>
                    <?php if (($user['role'] ?? '') !== 'admin'): ?>
                        <a class="hidden text-zinc-700 hover:text-teal-700 sm:inline" href="<?= site_url('community'); ?>">Community</a>
                    <?php endif; ?>
                    <a class="hidden text-zinc-700 hover:text-teal-700 sm:inline" href="<?= site_url('assistant'); ?>">Assistant</a>
                    <?php if (($user['role'] ?? '') === 'resident'): ?>
                        <a class="hidden text-zinc-700 hover:text-teal-700 sm:inline" href="<?= site_url('resident/services'); ?>">Services</a>
                        <a class="hidden text-zinc-700 hover:text-teal-700 sm:inline" href="<?= site_url('resident/requests'); ?>">My Requests</a>
                        <a class="hidden text-zinc-700 hover:text-teal-700 sm:inline" href="<?= site_url('resident/complaints'); ?>">Complaints</a>
                    <?php endif; ?>
                    <?php if (($user['role'] ?? '') === 'staff'): ?>
                        <a class="hidden text-zinc-700 hover:text-teal-700 sm:inline" href="<?= site_url('staff/requests'); ?>">Request Queue</a>
                        <a class="hidden text-zinc-700 hover:text-teal-700 sm:inline" href="<?= site_url('staff/complaints'); ?>">Complaints</a>
                    <?php endif; ?>
                    <?php if (($user['role'] ?? '') === 'admin'): ?>
                        <a class="hidden text-zinc-700 hover:text-teal-700 sm:inline" href="<?= site_url('admin/requests'); ?>">Requests</a>
                        <a class="hidden text-zinc-700 hover:text-teal-700 sm:inline" href="<?= site_url('admin/complaints'); ?>">Complaints</a>
                        <a class="hidden text-zinc-700 hover:text-teal-700 sm:inline" href="<?= site_url('admin/community'); ?>">Community</a>
                        <a class="hidden text-zinc-700 hover:text-teal-700 sm:inline" href="<?= site_url('admin/reports'); ?>">Reports</a>
                        <a class="hidden text-zinc-700 hover:text-teal-700 sm:inline" href="<?= site_url('admin/services'); ?>">Services</a>
                        <a class="hidden text-zinc-700 hover:text-teal-700 sm:inline" href="<?= site_url('admin/users'); ?>">Users</a>
                    <?php endif; ?>
                    <span class="hidden rounded-md bg-amber-100 px-2 py-1 text-xs font-medium text-amber-900 sm:inline-block">
                        <?= e(ucfirst($user['role'])); ?>
                    </span>
                    <form method="POST" action="<?= site_url('logout'); ?>">
                        <?php csrf_field(); ?>
                        <button type="submit" class="rounded-md border border-zinc-300 px-3 py-2 text-zinc-700 hover:border-rose-300 hover:text-rose-700">
                            Logout
                        </button>
                    </form>
                <?php else: ?>
                    <a class="text-zinc-700 hover:text-teal-700" href="<?= site_url('community'); ?>">Community</a>
                    <a class="text-zinc-700 hover:text-teal-700" href="<?= site_url('assistant'); ?>">Assistant</a>
                    <a class="text-zinc-700 hover:text-teal-700" href="<?= site_url('login'); ?>">Login</a>
                    <a class="rounded-md bg-teal-700 px-3 py-2 font-medium text-white hover:bg-teal-800" href="<?= site_url('register'); ?>">
                        Register
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8">
        <?php require APP_DIR . 'views/layouts/messages.php'; ?>
