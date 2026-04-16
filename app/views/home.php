<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
    <div>
        <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Barangay services</p>
        <h1 class="mt-3 max-w-3xl text-3xl font-bold tracking-normal text-zinc-950 sm:text-4xl">
            Request certificates, submit requirements, and track updates in one place.
        </h1>
        <p class="mt-4 max-w-2xl text-base leading-7 text-zinc-700">
            Residents can submit requests, staff can review and process them, and admins can manage the system for a complete demo flow.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a class="rounded-md bg-teal-700 px-4 py-2 font-medium text-white hover:bg-teal-800" href="<?= site_url('register'); ?>">
                Create Resident Account
            </a>
            <a class="rounded-md border border-zinc-300 px-4 py-2 font-medium text-zinc-800 hover:border-teal-600 hover:text-teal-700" href="<?= site_url('login'); ?>">
                Login
            </a>
            <a class="rounded-md border border-zinc-300 px-4 py-2 font-medium text-zinc-800 hover:border-teal-600 hover:text-teal-700" href="<?= site_url('assistant'); ?>">
                Ask Assistant
            </a>
            <a class="rounded-md border border-zinc-300 px-4 py-2 font-medium text-zinc-800 hover:border-teal-600 hover:text-teal-700" href="<?= site_url('community'); ?>">
                View Community
            </a>
        </div>
    </div>

    <div class="rounded-md border border-zinc-200 bg-white p-5">
        <h2 class="text-lg font-semibold text-zinc-950">Demo Access</h2>
        <p class="mt-2 text-sm text-zinc-600">Use these seeded accounts after importing the SQL files in order.</p>
        <dl class="mt-4 space-y-4 text-sm">
            <div>
                <dt class="font-medium text-zinc-800">Resident</dt>
                <dd class="text-zinc-600">Register from the public form.</dd>
            </div>
            <div>
                <dt class="font-medium text-zinc-800">Staff</dt>
                <dd class="text-zinc-600">staff@barangay.local / password123</dd>
            </div>
            <div>
                <dt class="font-medium text-zinc-800">Admin</dt>
                <dd class="text-zinc-600">admin@barangay.local / password123</dd>
            </div>
        </dl>
    </div>
</section>

<section class="mt-10 rounded-md border border-zinc-200 bg-white p-5">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Community</p>
            <h2 class="mt-2 text-xl font-bold text-zinc-950">Barangay updates, events, and resources</h2>
            <p class="mt-2 text-sm leading-6 text-zinc-700">
                Visit the community section for announcements, advisories, upcoming events, programs, and public information.
            </p>
        </div>
        <a class="btn-primary" href="<?= site_url('community'); ?>">Open Community</a>
    </div>
</section>

<section class="mt-10 rounded-md border border-zinc-200 bg-white p-5">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Virtual Help Assistant</p>
            <h2 class="mt-2 text-xl font-bold text-zinc-950">Need help choosing a service?</h2>
            <p class="mt-2 text-sm leading-6 text-zinc-700">
                Ask the prototype assistant about requirements, fees, simulated payments, statuses, and final documents.
            </p>
        </div>
        <a class="btn-primary" href="<?= site_url('assistant'); ?>">Open Assistant</a>
    </div>
</section>

<section class="mt-10">
    <h2 class="text-xl font-bold text-zinc-950">Demo Flow</h2>
    <div class="mt-4 grid gap-4 md:grid-cols-3">
        <div class="rounded-md border border-zinc-200 bg-white p-5">
            <p class="font-semibold text-zinc-950">1. Resident</p>
            <p class="mt-2 text-sm leading-6 text-zinc-700">Register, choose a service, submit requirements, and add simulated payment proof when needed.</p>
        </div>
        <div class="rounded-md border border-zinc-200 bg-white p-5">
            <p class="font-semibold text-zinc-950">2. Staff</p>
            <p class="mt-2 text-sm leading-6 text-zinc-700">Review requests, verify payments, update statuses, and upload final documents after approval.</p>
        </div>
        <div class="rounded-md border border-zinc-200 bg-white p-5">
            <p class="font-semibold text-zinc-950">3. Admin</p>
            <p class="mt-2 text-sm leading-6 text-zinc-700">Manage services, users, announcements, and review audit logs for presentation traceability.</p>
        </div>
    </div>
</section>

<?php if (!empty($announcements)): ?>
    <section class="mt-10">
        <h2 class="text-xl font-bold text-zinc-950">Announcements</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-3">
            <?php foreach ($announcements as $announcement): ?>
                <article class="rounded-md border border-zinc-200 bg-white p-5">
                    <h3 class="font-semibold text-zinc-950"><?= e($announcement['title']); ?></h3>
                    <p class="mt-3 text-sm leading-6 text-zinc-700"><?= e($announcement['body']); ?></p>
                    <?php if (!empty($announcement['published_at'])): ?>
                        <p class="mt-4 text-xs text-zinc-600"><?= e(date('M d, Y', strtotime($announcement['published_at']))); ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($community_posts)): ?>
    <section class="mt-10">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-zinc-950">Community Highlights</h2>
            <a class="text-sm font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('community'); ?>">View all</a>
        </div>
        <div class="mt-4 grid gap-4 md:grid-cols-3">
            <?php foreach ($community_posts as $post): ?>
                <article class="rounded-md border border-zinc-200 bg-white p-5">
                    <span class="rounded-md px-2 py-1 text-xs font-medium <?= community_category_badge_class($post['category']); ?>">
                        <?= e(community_category_label($post['category'])); ?>
                    </span>
                    <h3 class="mt-3 font-semibold text-zinc-950"><?= e($post['title']); ?></h3>
                    <p class="mt-3 text-sm leading-6 text-zinc-700"><?= e(community_post_summary($post)); ?></p>
                    <a class="mt-4 inline-block font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('community/' . $post['slug']); ?>">
                        Open
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
