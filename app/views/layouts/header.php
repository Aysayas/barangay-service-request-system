<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php
$user = auth_user();
$role = $user['role'] ?? null;

$nav_links = [];

if (!empty($user)) {
    $nav_links[] = ['label' => 'Dashboard', 'url' => site_url('dashboard')];
    $nav_links[] = ['label' => 'Assistant', 'url' => site_url('assistant')];

    if ($role !== 'admin') {
        $nav_links[] = ['label' => 'Community', 'url' => site_url('community')];
    }

    if ($role === 'resident') {
        $nav_links[] = ['label' => 'Services', 'url' => site_url('resident/services')];
        $nav_links[] = ['label' => 'My Requests', 'url' => site_url('resident/requests')];
        $nav_links[] = ['label' => 'Complaints', 'url' => site_url('resident/complaints')];
    }

    if ($role === 'staff') {
        $nav_links[] = ['label' => 'Request Queue', 'url' => site_url('staff/requests')];
        $nav_links[] = ['label' => 'Complaints', 'url' => site_url('staff/complaints')];
    }

    if ($role === 'admin') {
        $nav_links[] = ['label' => 'Requests', 'url' => site_url('admin/requests')];
        $nav_links[] = ['label' => 'Complaints', 'url' => site_url('admin/complaints')];
        $nav_links[] = ['label' => 'Community', 'url' => site_url('admin/community')];
        $nav_links[] = ['label' => 'Reports', 'url' => site_url('admin/reports')];
        $nav_links[] = ['label' => 'Services', 'url' => site_url('admin/services')];
        $nav_links[] = ['label' => 'Users', 'url' => site_url('admin/users')];
    }
} else {
    $nav_links[] = ['label' => 'Community', 'url' => site_url('community')];
    $nav_links[] = ['label' => 'Assistant', 'url' => site_url('assistant')];
}
?>
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
        <div class="app-shell flex items-center justify-between gap-4 py-3">
            <a href="<?= site_url('/'); ?>" class="brand-link">
                <span class="brand-mark">eB</span>
                <span class="min-w-0">
                    <span class="block truncate text-base font-bold tracking-normal">eBarangayHub</span>
                    <span class="hidden text-xs font-medium text-slate-500 sm:block">Centralized Barangay Services</span>
                </span>
            </a>

            <nav class="desktop-nav" aria-label="Primary navigation">
                <?php foreach ($nav_links as $link): ?>
                    <a class="nav-link" href="<?= $link['url']; ?>"><?= e($link['label']); ?></a>
                <?php endforeach; ?>

                <?php if (!empty($user)): ?>
                    <span class="role-pill">
                        <?= e(ucfirst((string) $role)); ?>
                    </span>
                    <form method="POST" action="<?= site_url('logout'); ?>">
                        <?php csrf_field(); ?>
                        <button type="submit" class="btn-danger px-3 py-2">
                            Logout
                        </button>
                    </form>
                <?php else: ?>
                    <a class="nav-link" href="<?= site_url('login'); ?>">Login</a>
                    <a class="btn-primary px-3 py-2" href="<?= site_url('register'); ?>">
                        Register
                    </a>
                <?php endif; ?>
            </nav>

            <button
                class="mobile-menu-button"
                type="button"
                data-mobile-menu-toggle
                aria-controls="mobile-navigation"
                aria-expanded="false"
            >
                <span class="sr-only">Open menu</span>
                <svg data-mobile-menu-open-icon xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
                <svg data-mobile-menu-close-icon xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6 6 18" />
                </svg>
                <span class="text-sm font-semibold">Menu</span>
            </button>
        </div>

        <div id="mobile-navigation" class="mobile-menu-panel hidden" data-mobile-menu-panel>
            <div class="app-shell py-3">
                <div class="mobile-menu-card">
                    <?php if (!empty($user)): ?>
                        <div class="mobile-menu-meta">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-normal text-slate-500">Signed in as</p>
                                <p class="mt-1 font-semibold text-slate-950">
                                    <?= e(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: ($user['email'] ?? 'User')); ?>
                                </p>
                            </div>
                            <span class="role-pill"><?= e(ucfirst((string) $role)); ?></span>
                        </div>
                    <?php else: ?>
                        <div class="mobile-menu-meta">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-normal text-slate-500">Welcome</p>
                                <p class="mt-1 font-semibold text-slate-950">Explore eBarangayHub</p>
                            </div>
                            <a class="btn-primary px-3 py-2" href="<?= site_url('register'); ?>">Register</a>
                        </div>
                    <?php endif; ?>

                    <nav class="mobile-nav-list" aria-label="Mobile navigation">
                        <?php foreach ($nav_links as $link): ?>
                            <a class="mobile-nav-link" href="<?= $link['url']; ?>"><?= e($link['label']); ?></a>
                        <?php endforeach; ?>
                    </nav>

                    <?php if (!empty($user)): ?>
                        <form class="mt-3" method="POST" action="<?= site_url('logout'); ?>">
                            <?php csrf_field(); ?>
                            <button type="submit" class="btn-danger w-full">
                                Logout
                            </button>
                        </form>
                    <?php else: ?>
                        <a class="btn-secondary mt-3 w-full" href="<?= site_url('login'); ?>">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="app-main">
        <?php require APP_DIR . 'views/layouts/messages.php'; ?>
