<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<div class="home-page">
<section class="home-hero">
    <div class="home-hero-inner">
        <p class="hero-eyebrow">eBarangayHub</p>
        <h1 class="hero-title">Centralized Barangay Services, Reports, and Community Access</h1>
        <p class="hero-subtitle">
            A polished local service portal for resident requests, complaint tracking, community updates, simulated payments, final documents, reports, and guided assistance.
        </p>
        <p class="hero-support">
            Designed for a complete demo flow: residents submit and track, staff process and verify, and admins manage the full barangay service workspace.
        </p>
        <div class="hero-cta-row">
            <a class="btn-primary bg-white text-teal-800 hover:bg-teal-50" href="<?= site_url('register'); ?>">
                Create Resident Account
            </a>
            <a class="btn-secondary border-white/40 bg-white/10 text-white hover:bg-white hover:text-teal-800" href="<?= site_url('login'); ?>">
                Login
            </a>
            <a class="btn-secondary border-white/40 bg-white/10 text-white hover:bg-white hover:text-teal-800" href="<?= site_url('community'); ?>">
                View Community
            </a>
        </div>
        <div class="hero-stat-grid">
            <div class="hero-stat">
                <strong>Requests</strong>
                Services, requirements, payments, and final documents
            </div>
            <div class="hero-stat">
                <strong>Complaints</strong>
                Resident concerns with protected evidence review
            </div>
            <div class="hero-stat">
                <strong>Reports</strong>
                Admin summaries, charts, CSV exports, and AI insights
            </div>
        </div>
    </div>
</section>

<section class="grid gap-4 lg:grid-cols-[0.95fr_1.05fr]">
    <div class="section-panel">
        <p class="page-kicker">Quick Start</p>
        <h2 class="mt-2 text-2xl font-bold text-slate-950">Start with the right workspace</h2>
        <p class="mt-3 text-sm leading-6 text-slate-600">
            Residents can create an account, staff can open queues, and admins can review the full management and reporting area.
        </p>
        <div class="mt-5 grid gap-3 sm:grid-cols-2">
            <a class="action-tile" href="<?= site_url('resident/services'); ?>">
                <span class="action-tile-label">Browse Services</span>
                <span class="action-tile-text block">See available barangay services and request requirements.</span>
            </a>
            <a class="action-tile" href="<?= site_url('assistant'); ?>">
                <span class="action-tile-label">Ask Assistant</span>
                <span class="action-tile-text block">Get guided help for services, payments, complaints, and documents.</span>
            </a>
        </div>
    </div>

    <div class="section-panel">
        <div class="section-heading-row">
            <div>
                <p class="page-kicker">Demo Access</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-950">Ready for presentation walkthroughs</h2>
            </div>
            <a class="btn-secondary" href="<?= site_url('login'); ?>">Open Login</a>
        </div>
        <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-3">
            <div>
                <dt class="font-semibold text-slate-950">Resident</dt>
                <dd class="mt-1 text-slate-600">Register from the public form.</dd>
            </div>
            <div>
                <dt class="font-semibold text-slate-950">Staff</dt>
                <dd class="mt-1 text-slate-600">staff@barangay.local<br>password123</dd>
            </div>
            <div>
                <dt class="font-semibold text-slate-950">Admin</dt>
                <dd class="mt-1 text-slate-600">admin@barangay.local<br>password123</dd>
            </div>
        </dl>
    </div>
</section>

<section>
    <div class="section-heading-row">
        <div>
            <p class="page-kicker">Platform Flow</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-950">Built around the full barangay service cycle</h2>
        </div>
    </div>
    <div class="mt-5 grid gap-4 md:grid-cols-3">
        <div class="flow-step">
            <span class="flow-step-number">1</span>
            <h3 class="mt-4 font-semibold text-slate-950">Resident submits</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Create requests, upload requirements, submit complaints, and track progress from one place.</p>
        </div>
        <div class="flow-step">
            <span class="flow-step-number">2</span>
            <h3 class="mt-4 font-semibold text-slate-950">Staff processes</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Review queues, verify simulated payments, update statuses, and upload final documents.</p>
        </div>
        <div class="flow-step">
            <span class="flow-step-number">3</span>
            <h3 class="mt-4 font-semibold text-slate-950">Admin oversees</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Manage users, services, community content, reports, exports, charts, and audit activity.</p>
        </div>
    </div>
</section>

<section class="section-panel">
    <div class="section-heading-row">
        <div>
            <p class="page-kicker">Community</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-950">Updates, events, advisories, and resources</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Keep residents and visitors aligned with published barangay information.
            </p>
        </div>
        <a class="btn-primary" href="<?= site_url('community'); ?>">Open Community</a>
    </div>
</section>

<section class="grid gap-6 lg:grid-cols-2">
    <div>
        <div class="section-heading-row">
            <div>
                <p class="page-kicker">Announcements</p>
                <h2 class="mt-2 text-xl font-bold text-slate-950">Latest public notices</h2>
            </div>
        </div>

        <?php if (empty($announcements)): ?>
            <div class="empty-state mt-4">
                No published announcements yet. Admins can add notices from the announcements module.
            </div>
        <?php else: ?>
            <div class="mt-4 space-y-4">
                <?php foreach ($announcements as $announcement): ?>
                    <article class="surface-card-hover">
                        <h3 class="font-semibold text-slate-950"><?= e($announcement['title']); ?></h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600"><?= e($announcement['body']); ?></p>
                        <?php if (!empty($announcement['published_at'])): ?>
                            <p class="mt-4 text-xs font-medium text-slate-500"><?= e(date('M d, Y', strtotime($announcement['published_at']))); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <div class="section-heading-row">
            <div>
                <p class="page-kicker">Community Highlights</p>
                <h2 class="mt-2 text-xl font-bold text-slate-950">Featured public information</h2>
            </div>
            <a class="text-sm font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('community'); ?>">View all</a>
        </div>

        <?php if (empty($community_posts)): ?>
            <div class="empty-state mt-4">
                No community posts are published yet.
            </div>
        <?php else: ?>
            <div class="mt-4 space-y-4">
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
        <?php endif; ?>
    </div>
</section>
</div>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
