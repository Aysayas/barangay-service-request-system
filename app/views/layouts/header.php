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
<body class="min-h-screen text-slate-900">
    <header class="app-header">
        <div class="app-shell flex items-center justify-between gap-5 py-3">
            <a href="<?= site_url('/'); ?>" class="brand-link">
                <span class="brand-mark">eB</span>
                <span>
                    <span class="block text-base font-bold tracking-normal">eBarangayHub</span>
                    <span class="hidden text-xs font-medium text-slate-500 sm:block">Centralized Barangay Services</span>
                </span>
            </a>

            <nav class="flex flex-wrap items-center justify-end gap-1.5 text-sm">
                <?php if (!empty($user)): ?>
                    <a class="nav-link" href="<?= site_url('dashboard'); ?>">Dashboard</a>
                    <?php if (($user['role'] ?? '') !== 'admin'): ?>
                        <a class="nav-link hidden sm:inline-flex" href="<?= site_url('community'); ?>">Community</a>
                    <?php endif; ?>
                    <a class="nav-link hidden sm:inline-flex" href="<?= site_url('assistant'); ?>">Assistant</a>
                    <?php if (($user['role'] ?? '') === 'resident'): ?>
                        <a class="nav-link hidden sm:inline-flex" href="<?= site_url('resident/services'); ?>">Services</a>
                        <a class="nav-link hidden sm:inline-flex" href="<?= site_url('resident/requests'); ?>">My Requests</a>
                        <a class="nav-link hidden sm:inline-flex" href="<?= site_url('resident/complaints'); ?>">Complaints</a>
                    <?php endif; ?>
                    <?php if (($user['role'] ?? '') === 'staff'): ?>
                        <a class="nav-link hidden sm:inline-flex" href="<?= site_url('staff/requests'); ?>">Request Queue</a>
                        <a class="nav-link hidden sm:inline-flex" href="<?= site_url('staff/complaints'); ?>">Complaints</a>
                    <?php endif; ?>
                    <?php if (($user['role'] ?? '') === 'admin'): ?>
                        <a class="nav-link hidden sm:inline-flex" href="<?= site_url('admin/requests'); ?>">Requests</a>
                        <a class="nav-link hidden sm:inline-flex" href="<?= site_url('admin/complaints'); ?>">Complaints</a>
                        <a class="nav-link hidden sm:inline-flex" href="<?= site_url('admin/community'); ?>">Community</a>
                        <a class="nav-link hidden sm:inline-flex" href="<?= site_url('admin/reports'); ?>">Reports</a>
                        <a class="nav-link hidden sm:inline-flex" href="<?= site_url('admin/services'); ?>">Services</a>
                        <a class="nav-link hidden sm:inline-flex" href="<?= site_url('admin/users'); ?>">Users</a>
                    <?php endif; ?>
                    <span class="role-pill hidden sm:inline-block">
                        <?= e(ucfirst($user['role'])); ?>
                    </span>
                    <form method="POST" action="<?= site_url('logout'); ?>">
                        <?php csrf_field(); ?>
                        <button type="submit" class="btn-danger px-3 py-2">
                            Logout
                        </button>
                    </form>
                <?php else: ?>
                    <a class="nav-link" href="<?= site_url('community'); ?>">Community</a>
                    <a class="nav-link" href="<?= site_url('assistant'); ?>">Assistant</a>
                    <a class="nav-link" href="<?= site_url('login'); ?>">Login</a>
                    <a class="btn-primary px-3 py-2" href="<?= site_url('register'); ?>">
                        Register
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="app-main">
        <?php require APP_DIR . 'views/layouts/messages.php'; ?>
