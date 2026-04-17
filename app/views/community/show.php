<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Community</p>
            <h1 class="mt-2 max-w-3xl text-3xl font-bold text-zinc-950"><?= e($post['title']); ?></h1>
            <div class="mt-4 flex flex-wrap items-center gap-3 text-sm">
                <span class="rounded-md px-2 py-1 font-medium <?= community_category_badge_class($post['category']); ?>">
                    <?= e(community_category_label($post['category'])); ?>
                </span>
                <?php if (!empty($post['published_at'])): ?>
                    <span class="text-zinc-600">Published <?= e(date('M d, Y', strtotime($post['published_at']))); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <a class="btn-secondary" href="<?= site_url('community'); ?>">Back to Community</a>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_0.35fr]">
        <article class="rounded-md border border-zinc-200 bg-white">
            <?php if (!empty($post['image_path']) && safe_storage_path($post['image_path'], 'runtime/uploads/community') !== null): ?>
                <img class="h-72 w-full rounded-t-md object-cover" src="<?= site_url('community/image/' . $post['id']); ?>" alt="<?= e($post['title']); ?>">
            <?php endif; ?>

            <div class="p-6">
                <?php if (!empty($post['excerpt'])): ?>
                    <p class="text-base leading-7 text-zinc-700"><?= e($post['excerpt']); ?></p>
                <?php endif; ?>

                <div class="mt-5 whitespace-pre-line text-sm leading-7 text-zinc-700"><?= e($post['content']); ?></div>

                <?php if (!empty($post['resource_link'])): ?>
                    <a class="mt-6 inline-flex rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800" href="<?= e($post['resource_link']); ?>" target="_blank" rel="noopener">
                        Open Resource Link
                    </a>
                <?php endif; ?>
            </div>
        </article>

        <aside class="space-y-6">
            <?php if ($post['category'] === 'event'): ?>
                <section class="rounded-md border border-zinc-200 bg-white p-5">
                    <h2 class="text-lg font-semibold text-zinc-950">Event Details</h2>
                    <dl class="mt-4 space-y-4 text-sm">
                        <div>
                            <dt class="font-medium text-zinc-800">Schedule</dt>
                            <dd class="mt-1 text-zinc-600"><?= e(community_event_schedule($post) ?: 'To be announced'); ?></dd>
                        </div>
                        <div>
                            <dt class="font-medium text-zinc-800">Venue</dt>
                            <dd class="mt-1 text-zinc-600"><?= e($post['venue'] ?: 'To be announced'); ?></dd>
                        </div>
                        <div>
                            <dt class="font-medium text-zinc-800">Organizer</dt>
                            <dd class="mt-1 text-zinc-600"><?= e($post['organizer'] ?: 'Barangay Office'); ?></dd>
                        </div>
                    </dl>
                </section>
            <?php endif; ?>

            <section class="rounded-md border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold text-zinc-950">Related Items</h2>
                <?php if (empty($related_posts)): ?>
                    <p class="mt-3 text-sm text-zinc-600">No related community posts yet.</p>
                <?php else: ?>
                    <ul class="mt-4 divide-y divide-zinc-200 text-sm">
                        <?php foreach ($related_posts as $related): ?>
                            <li class="py-3">
                                <a class="font-semibold text-zinc-950 hover:text-teal-700" href="<?= site_url('community/' . $related['slug']); ?>">
                                    <?= e($related['title']); ?>
                                </a>
                                <p class="mt-1 text-zinc-600"><?= e(date('M d, Y', strtotime($related['published_at'] ?: $related['created_at']))); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </aside>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
