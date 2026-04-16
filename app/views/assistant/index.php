<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Simulated AI Assistant</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950">Virtual Help Assistant</h1>
            <p class="mt-3 max-w-2xl text-zinc-700">
                Ask about services, requirements, request statuses, simulated payments, tracking, and final documents.
            </p>
            <p class="mt-3 rounded-md border border-amber-200 bg-amber-50 p-3 text-sm text-amber-950">
                Prototype notice: this is a rule-based help assistant. It does not use a real AI API.
            </p>
        </div>

        <div class="rounded-md border border-zinc-200 bg-white p-5">
            <h2 class="text-lg font-semibold text-zinc-950">Try These Questions</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                <?php foreach ($suggestions as $suggestion): ?>
                    <form method="POST" action="<?= site_url('assistant/ask'); ?>">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="question" value="<?= e($suggestion); ?>">
                        <button class="rounded-md bg-zinc-100 px-3 py-2 text-sm font-medium text-zinc-800 hover:text-teal-700" type="submit">
                            <?= e($suggestion); ?>
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_0.8fr]">
        <section class="rounded-md border border-zinc-200 bg-white p-5">
            <h2 class="text-lg font-semibold text-zinc-950">Ask A Question</h2>
            <form class="mt-5 space-y-4" method="POST" action="<?= site_url('assistant/ask'); ?>">
                <?php csrf_field(); ?>

                <div>
                    <label class="form-label" for="question">Your question</label>
                    <textarea class="form-input min-h-28" id="question" name="question" maxlength="<?= e($max_question_length); ?>" placeholder="Example: How do I request Barangay Clearance?"><?= e($question); ?></textarea>
                    <p class="mt-2 text-xs text-zinc-600">Keep it short. This prototype matches common barangay system questions.</p>
                </div>

                <button class="btn-primary" type="submit">Ask Assistant</button>
            </form>

            <div class="mt-6 space-y-4">
                <div class="rounded-md border border-zinc-200 bg-zinc-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-normal text-zinc-600">Assistant</p>
                    <p class="mt-2 text-sm leading-6 text-zinc-700"><?= e($welcome_message); ?></p>
                </div>

                <?php if (!empty($question)): ?>
                    <div class="rounded-md border border-teal-200 bg-teal-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-normal text-teal-800">You Asked</p>
                        <p class="mt-2 text-sm leading-6 text-teal-950"><?= e($question); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($answer)): ?>
                    <div class="rounded-md border border-zinc-200 bg-white p-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <p class="text-xs font-semibold uppercase tracking-normal text-zinc-600">Prototype Reply</p>
                            <span class="rounded-md bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-700">
                                <?= e(ucfirst(str_replace('_', ' ', $answer['category']))); ?>
                            </span>
                        </div>
                        <p class="mt-3 whitespace-pre-line text-sm leading-6 text-zinc-700"><?= e($answer['reply']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-md border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold text-zinc-950">Active Services</h2>
                <?php if (empty($services)): ?>
                    <p class="mt-3 text-sm text-zinc-600">No active services are available yet.</p>
                <?php else: ?>
                    <ul class="mt-4 divide-y divide-zinc-200">
                        <?php foreach ($services as $service): ?>
                            <li class="py-3">
                                <p class="font-medium text-zinc-950"><?= e($service['name']); ?></p>
                                <p class="mt-1 text-sm text-zinc-600">
                                    <?= e(format_money($service['fee'])); ?> -
                                    <?= ((int) $service['requires_payment'] === 1) ? 'Simulated payment required' : 'No payment required'; ?>
                                </p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <section class="rounded-md border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold text-zinc-950">Helpful Areas</h2>
                <ul class="mt-4 space-y-3 text-sm text-zinc-700">
                    <li>Services and requirements</li>
                    <li>Request tracking and status meanings</li>
                    <li>Simulated payment steps</li>
                    <li>Final document download rules</li>
                    <li>Resident registration and announcements</li>
                </ul>
            </section>
        </aside>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
