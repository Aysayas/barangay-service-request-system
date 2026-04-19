<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="grid gap-8 py-4 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
    <div>
        <p class="page-kicker">eBarangayHub</p>
        <h1 class="page-title max-w-3xl">
            eBarangayHub
        </h1>
        <p class="mt-4 max-w-2xl text-lg font-semibold leading-7 text-slate-800">
            Centralized Barangay Services, Reports, and Community Access
        </p>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
            A centralized platform for resident requests, complaint tracking, simulated payments, final documents, reports, announcements, and community updates.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a class="btn-primary" href="<?= site_url('register'); ?>">
                Create Resident Account
            </a>
            <a class="btn-secondary" href="<?= site_url('login'); ?>">
                Login
            </a>
            <a class="btn-secondary" href="<?= site_url('assistant'); ?>">
                Ask Assistant
            </a>
            <a class="btn-secondary" href="<?= site_url('community'); ?>">
                View Community
            </a>
        </div>
    </div>

    <div class="surface-card">
        <h2 class="section-title">Demo Access</h2>
        <p class="mt-2 text-sm text-slate-600">Use these seeded accounts after importing the SQL files in order.</p>
        <dl class="mt-4 space-y-4 text-sm">
            <div>
                <dt class="font-semibold text-slate-800">Resident</dt>
                <dd class="text-slate-600">Register from the public form.</dd>
            </div>
            <div>
                <dt class="font-semibold text-slate-800">Staff</dt>
                <dd class="text-slate-600">staff@barangay.local / password123</dd>
            </div>
            <div>
                <dt class="font-semibold text-slate-800">Admin</dt>
                <dd class="text-slate-600">admin@barangay.local / password123</dd>
            </div>
        </dl>
    </div>
</section>

<section class="surface-card mt-10">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="page-kicker">Community</p>
            <h2 class="mt-2 text-xl font-bold text-slate-950">Community updates, events, and resources</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Visit the community section for announcements, advisories, upcoming events, programs, and public information.
            </p>
        </div>
        <a class="btn-primary" href="<?= site_url('community'); ?>">Open Community</a>
    </div>
</section>

<section class="surface-card mt-10">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="page-kicker">Virtual Help Assistant</p>
            <h2 class="mt-2 text-xl font-bold text-slate-950">Need help choosing a service?</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Ask the prototype assistant about requirements, fees, simulated payments, statuses, and final documents.
            </p>
        </div>
        <a class="btn-primary" href="<?= site_url('assistant'); ?>">Open Assistant</a>
    </div>
</section>

<section class="mt-10">
    <h2 class="section-title">Demo Flow</h2>
    <div class="mt-4 grid gap-4 md:grid-cols-3">
        <div class="surface-card-hover">
            <p class="font-semibold text-slate-950">1. Resident</p>
            <p class="mt-2 text-sm leading-6 text-slate-600">Register, choose a service, submit requirements, and add simulated payment proof when needed.</p>
        </div>
        <div class="surface-card-hover">
            <p class="font-semibold text-slate-950">2. Staff</p>
            <p class="mt-2 text-sm leading-6 text-slate-600">Review requests, verify payments, update statuses, and upload final documents after approval.</p>
        </div>
        <div class="surface-card-hover">
            <p class="font-semibold text-slate-950">3. Admin</p>
            <p class="mt-2 text-sm leading-6 text-slate-600">Manage services, users, community posts, reports, exports, charts, and audit logs for presentation traceability.</p>
        </div>
    </div>
</section>

<?php if (!empty($announcements)): ?>
    <section class="mt-10">
        <h2 class="section-title">Announcements</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-3">
            <?php foreach ($announcements as $announcement): ?>
                <article class="surface-card-hover">
                    <h3 class="font-semibold text-slate-950"><?= e($announcement['title']); ?></h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600"><?= e($announcement['body']); ?></p>
                    <?php if (!empty($announcement['published_at'])): ?>
                        <p class="mt-4 text-xs text-slate-500"><?= e(date('M d, Y', strtotime($announcement['published_at']))); ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($community_posts)): ?>
    <section class="mt-10">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="section-title">Community Highlights</h2>
            <a class="text-sm font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('community'); ?>">View all</a>
        </div>
        <div class="mt-4 grid gap-4 md:grid-cols-3">
            <?php foreach ($community_posts as $post): ?>
                <article class="surface-card-hover">
                    <span class="status-pill <?= community_category_badge_class($post['category']); ?>">
                        <?= e(community_category_label($post['category'])); ?>
                    </span>
                    <h3 class="mt-3 font-semibold text-slate-950"><?= e($post['title']); ?></h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600"><?= e(community_post_summary($post)); ?></p>
                    <a class="mt-4 inline-block font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('community/' . $post['slug']); ?>">
                        Open
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
