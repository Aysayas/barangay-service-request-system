<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Resident Services</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950">Available Services</h1>
            <p class="mt-3 max-w-2xl text-zinc-700">
                Choose a service, review the requirements, and submit your request with attachments.
            </p>
        </div>
        <a class="btn-secondary" href="<?= site_url('resident/requests'); ?>">My Requests</a>
    </div>

    <?php if (empty($services)): ?>
        <div class="mt-8 rounded-md border border-zinc-200 bg-white p-5 text-sm text-zinc-600">
            No active services are available right now.
        </div>
    <?php else: ?>
        <div class="mt-8 grid gap-4 md:grid-cols-2">
            <?php foreach ($services as $service): ?>
                <article class="rounded-md border border-zinc-200 bg-white p-5">
                    <div class="flex items-start justify-between gap-4">
                        <h2 class="text-lg font-semibold text-zinc-950"><?= e($service['name']); ?></h2>
                        <span class="rounded-md bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-800">
                            <?= ((int) $service['requires_payment'] === 1) ? 'Payment Later' : 'No Payment'; ?>
                        </span>
                    </div>

                    <p class="mt-3 text-sm leading-6 text-zinc-700"><?= e($service['description']); ?></p>

                    <div class="mt-4">
                        <p class="text-sm font-medium text-zinc-800">Requirements</p>
                        <p class="mt-1 text-sm leading-6 text-zinc-600"><?= e($service['requirements_text']); ?></p>
                    </div>

                    <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-zinc-950"><?= e(format_money($service['fee'])); ?></p>
                        <a class="btn-primary" href="<?= site_url('resident/requests/create/' . $service['id']); ?>">Request Now</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
