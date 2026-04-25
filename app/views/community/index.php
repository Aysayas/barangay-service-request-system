<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php
$base_url = site_url('community');
$has_category_filter = ($current_category !== 'all');
?>

<section class="workflow-page">
    <div class="workflow-header">
        <div>
            <p class="workflow-kicker">Community</p>
            <h1 class="workflow-title">Community Updates and Resources</h1>
            <p class="workflow-subtitle">
                Read barangay updates, advisories, events, programs, and public resources in one place.
            </p>
        </div>
        <a class="btn-secondary" href="<?= site_url('assistant'); ?>">Ask Assistant</a>
    </div>

    <div class="queue-tabs">
        <a class="rounded-md px-3 py-2 <?= ($current_category === 'all') ? 'bg-teal-700 text-white' : 'bg-slate-100 text-slate-800 hover:text-teal-700'; ?>" href="<?= $base_url; ?>">
            All
        </a>
        <?php foreach ($categories as $value => $label): ?>
            <a class="rounded-md px-3 py-2 <?= ($current_category === $value) ? 'bg-teal-700 text-white' : 'bg-slate-100 text-slate-800 hover:text-teal-700'; ?>" href="<?= $base_url . '?category=' . rawurlencode($value); ?>">
                <?= e($label); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($featured_posts)): ?>
        <section>
            <h2 class="text-xl font-bold text-slate-950">Featured</h2>
            <div class="workflow-card-grid mt-4 md:grid-cols-3">
                <?php foreach ($featured_posts as $post): ?>
                    <article class="community-card">
                        <?php if (!empty($post['image_path']) && safe_storage_path($post['image_path'], 'runtime/uploads/community') !== null): ?>
                            <img class="h-40 w-full object-cover" src="<?= site_url('community/image/' . $post['id']); ?>" alt="<?= e($post['title']); ?>">
                        <?php endif; ?>
                        <div class="community-card-body">
                            <span class="status-pill <?= community_category_badge_class($post['category']); ?>">
                                <?= e(community_category_label($post['category'])); ?>
                            </span>
                            <h3 class="mt-3 font-semibold text-slate-950"><?= e($post['title']); ?></h3>
                            <p class="mt-2 text-sm leading-6 text-slate-700"><?= e(community_post_summary($post)); ?></p>
                            <a class="mt-4 inline-flex w-full justify-center font-medium text-teal-700 hover:text-teal-800 sm:w-auto" href="<?= site_url('community/' . $post['slug']); ?>">
                                Read more
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <div class="workflow-detail-grid lg:grid-cols-[1.2fr_0.8fr]">
        <section>
            <h2 class="text-xl font-bold text-slate-950">Latest Updates</h2>
            <?php if (empty($posts)): ?>
                <div class="empty-state-strong mt-4">
                    <?php if ($has_category_filter): ?>
                        <h3 class="text-lg font-semibold text-slate-950">No updates are available in this category.</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Try viewing all community updates or checking back after new barangay information is published.
                        </p>
                        <a class="btn-secondary mt-5" href="<?= $base_url; ?>">View All Updates</a>
                    <?php else: ?>
                        <h3 class="text-lg font-semibold text-slate-950">No community updates are available right now.</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Barangay announcements, events, advisories, programs, and resources will appear here when published.
                        </p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="mt-4 space-y-4">
                    <?php foreach ($posts as $post): ?>
                        <article class="surface-card-hover p-4 sm:p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <span class="status-pill <?= community_category_badge_class($post['category']); ?>">
                                        <?= e(community_category_label($post['category'])); ?>
                                    </span>
                                    <h3 class="mt-3 text-lg font-semibold text-slate-950"><?= e($post['title']); ?></h3>
                                </div>
                                <?php if (!empty($post['published_at'])): ?>
                                    <p class="text-sm text-slate-600"><?= e(date('M d, Y', strtotime($post['published_at']))); ?></p>
                                <?php endif; ?>
                            </div>

                            <p class="mt-3 text-sm leading-6 text-slate-700"><?= e(community_post_summary($post, 190)); ?></p>

                            <?php if ($post['category'] === 'event' && !empty($post['event_date'])): ?>
                                <p class="mt-3 text-sm font-medium text-amber-900">
                                    <?= e(community_event_schedule($post)); ?><?= !empty($post['venue']) ? ' at ' . e($post['venue']) : ''; ?>
                                </p>
                            <?php endif; ?>

                            <a class="mt-4 inline-flex w-full justify-center font-medium text-teal-700 hover:text-teal-800 sm:w-auto" href="<?= site_url('community/' . $post['slug']); ?>">
                                Open details
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <aside class="space-y-6">
            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Upcoming Events</h2>
                <?php if (empty($upcoming_events)): ?>
                    <div class="compact-note mt-3 text-sm">
                        No upcoming events have been posted yet. New schedules and barangay activities will appear here once published.
                    </div>
                <?php else: ?>
                    <ul class="mt-4 divide-y divide-slate-200 text-sm">
                        <?php foreach ($upcoming_events as $event): ?>
                            <li class="py-3">
                                <a class="inline-action-link font-semibold text-slate-950 hover:text-teal-700" href="<?= site_url('community/' . $event['slug']); ?>">
                                    <?= e($event['title']); ?>
                                </a>
                                <p class="mt-1 text-slate-600"><?= e(community_event_schedule($event)); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <section class="workflow-card">
                <h2 class="text-lg font-semibold text-slate-950">Resources and Advisories</h2>
                <?php if (empty($resources)): ?>
                    <div class="compact-note mt-3 text-sm">
                        No resources or advisories have been posted yet. Helpful public information will appear here once available.
                    </div>
                <?php else: ?>
                    <ul class="mt-4 divide-y divide-slate-200 text-sm">
                        <?php foreach ($resources as $resource): ?>
                            <li class="py-3">
                                <a class="inline-action-link font-semibold text-slate-950 hover:text-teal-700" href="<?= site_url('community/' . $resource['slug']); ?>">
                                    <?= e($resource['title']); ?>
                                </a>
                                <p class="mt-1 text-slate-600"><?= e(community_category_label($resource['category'])); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </aside>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
