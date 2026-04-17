<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php $base_url = site_url('community'); ?>

<section>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Community</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950">Community Updates and Resources</h1>
            <p class="mt-3 max-w-2xl text-zinc-700">
                Read barangay updates, advisories, events, programs, and public resources in one place.
            </p>
        </div>
        <a class="btn-secondary" href="<?= site_url('assistant'); ?>">Ask Assistant</a>
    </div>

    <div class="mt-6 flex flex-wrap gap-2 text-sm">
        <a class="rounded-md px-3 py-2 <?= ($current_category === 'all') ? 'bg-teal-700 text-white' : 'bg-zinc-100 text-zinc-800 hover:text-teal-700'; ?>" href="<?= $base_url; ?>">
            All
        </a>
        <?php foreach ($categories as $value => $label): ?>
            <a class="rounded-md px-3 py-2 <?= ($current_category === $value) ? 'bg-teal-700 text-white' : 'bg-zinc-100 text-zinc-800 hover:text-teal-700'; ?>" href="<?= $base_url . '?category=' . rawurlencode($value); ?>">
                <?= e($label); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($featured_posts)): ?>
        <section class="mt-8">
            <h2 class="text-xl font-bold text-zinc-950">Featured</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <?php foreach ($featured_posts as $post): ?>
                    <article class="overflow-hidden rounded-md border border-zinc-200 bg-white">
                        <?php if (!empty($post['image_path']) && safe_storage_path($post['image_path'], 'runtime/uploads/community') !== null): ?>
                            <img class="h-40 w-full object-cover" src="<?= site_url('community/image/' . $post['id']); ?>" alt="<?= e($post['title']); ?>">
                        <?php endif; ?>
                        <div class="p-5">
                            <span class="rounded-md px-2 py-1 text-xs font-medium <?= community_category_badge_class($post['category']); ?>">
                                <?= e(community_category_label($post['category'])); ?>
                            </span>
                            <h3 class="mt-3 font-semibold text-zinc-950"><?= e($post['title']); ?></h3>
                            <p class="mt-2 text-sm leading-6 text-zinc-700"><?= e(community_post_summary($post)); ?></p>
                            <a class="mt-4 inline-block font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('community/' . $post['slug']); ?>">
                                Read more
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <div class="mt-8 grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <section>
            <h2 class="text-xl font-bold text-zinc-950">Latest Updates</h2>
            <?php if (empty($posts)): ?>
                <div class="mt-4 rounded-md border border-zinc-200 bg-white p-5 text-sm text-zinc-600">
                    No published community posts are available yet.
                </div>
            <?php else: ?>
                <div class="mt-4 space-y-4">
                    <?php foreach ($posts as $post): ?>
                        <article class="rounded-md border border-zinc-200 bg-white p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <span class="rounded-md px-2 py-1 text-xs font-medium <?= community_category_badge_class($post['category']); ?>">
                                        <?= e(community_category_label($post['category'])); ?>
                                    </span>
                                    <h3 class="mt-3 text-lg font-semibold text-zinc-950"><?= e($post['title']); ?></h3>
                                </div>
                                <?php if (!empty($post['published_at'])): ?>
                                    <p class="text-sm text-zinc-600"><?= e(date('M d, Y', strtotime($post['published_at']))); ?></p>
                                <?php endif; ?>
                            </div>

                            <p class="mt-3 text-sm leading-6 text-zinc-700"><?= e(community_post_summary($post, 190)); ?></p>

                            <?php if ($post['category'] === 'event' && !empty($post['event_date'])): ?>
                                <p class="mt-3 text-sm font-medium text-amber-900">
                                    <?= e(community_event_schedule($post)); ?><?= !empty($post['venue']) ? ' at ' . e($post['venue']) : ''; ?>
                                </p>
                            <?php endif; ?>

                            <a class="mt-4 inline-block font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('community/' . $post['slug']); ?>">
                                Open details
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <aside class="space-y-6">
            <section class="rounded-md border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold text-zinc-950">Upcoming Events</h2>
                <?php if (empty($upcoming_events)): ?>
                    <p class="mt-3 text-sm text-zinc-600">No upcoming events have been posted yet.</p>
                <?php else: ?>
                    <ul class="mt-4 divide-y divide-zinc-200 text-sm">
                        <?php foreach ($upcoming_events as $event): ?>
                            <li class="py-3">
                                <a class="font-semibold text-zinc-950 hover:text-teal-700" href="<?= site_url('community/' . $event['slug']); ?>">
                                    <?= e($event['title']); ?>
                                </a>
                                <p class="mt-1 text-zinc-600"><?= e(community_event_schedule($event)); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <section class="rounded-md border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold text-zinc-950">Resources and Advisories</h2>
                <?php if (empty($resources)): ?>
                    <p class="mt-3 text-sm text-zinc-600">No resources or advisories have been posted yet.</p>
                <?php else: ?>
                    <ul class="mt-4 divide-y divide-zinc-200 text-sm">
                        <?php foreach ($resources as $resource): ?>
                            <li class="py-3">
                                <a class="font-semibold text-zinc-950 hover:text-teal-700" href="<?= site_url('community/' . $resource['slug']); ?>">
                                    <?= e($resource['title']); ?>
                                </a>
                                <p class="mt-1 text-zinc-600"><?= e(community_category_label($resource['category'])); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </aside>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
