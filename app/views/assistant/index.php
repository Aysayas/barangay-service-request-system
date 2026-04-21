<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="assistant-page">
    <div class="assistant-hero-grid">
        <div>
            <p class="page-kicker">Virtual Help Assistant</p>
            <h1 class="assistant-title">Virtual Help Assistant</h1>
            <p class="assistant-subtitle">
                Ask about services, requirements, request statuses, simulated payments, complaints, reports, community updates, and final documents.
            </p>
            <p class="assistant-mode-card">
                Current mode: <span class="font-semibold text-slate-950"><?= e($assistant_mode_label); ?></span>.
                Real AI is used first when configured; the rule-based fallback stays available if AI is disabled or unavailable.
            </p>
        </div>

        <div class="assistant-panel">
            <h2 class="section-title">Try These Questions</h2>
            <div class="assistant-suggestion-list mt-4 flex flex-wrap gap-2">
                <?php foreach ($suggestions as $suggestion): ?>
                    <form method="POST" action="<?= site_url('assistant/ask'); ?>">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="question" value="<?= e($suggestion); ?>">
                        <button class="chip" type="submit">
                            <?= e($suggestion); ?>
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="grid gap-5 sm:gap-6 lg:grid-cols-[1fr_0.8fr]">
        <section class="assistant-panel">
            <h2 class="section-title">Ask A Question</h2>
            <form class="assistant-question-form mt-5 space-y-4" method="POST" action="<?= site_url('assistant/ask'); ?>">
                <?php csrf_field(); ?>

                <div>
                    <label class="form-label" for="question">Your question</label>
                    <textarea class="form-input min-h-28" id="question" name="question" maxlength="<?= e($max_question_length); ?>" placeholder="Example: How do I request Barangay Clearance?"><?= e($question); ?></textarea>
                    <p class="form-help">
                        Keep it short. The assistant is scoped to eBarangayHub services, requests, payments, complaints, reports, community updates, and document release.
                    </p>
                </div>

                <button class="btn-primary w-full sm:w-auto" type="submit">Ask Assistant</button>
            </form>

            <div class="mt-6 space-y-4">
                <div class="assistant-prompt-card">
                    <p class="text-xs font-semibold uppercase tracking-normal text-slate-600">Assistant</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600"><?= e($welcome_message); ?></p>
                </div>

                <?php if (!empty($question)): ?>
                    <div class="assistant-question-card">
                        <p class="text-xs font-semibold uppercase tracking-normal text-teal-800">You Asked</p>
                        <p class="mt-2 text-sm leading-6 text-teal-950"><?= e($question); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($answer)): ?>
                    <div class="assistant-answer-card">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <p class="text-xs font-semibold uppercase tracking-normal text-slate-600"><?= e($answer['source_label'] ?? 'Assistant Reply'); ?></p>
                            <span class="status-pill border-slate-200 bg-slate-100 text-slate-700">
                                <?= e(ucfirst(str_replace('_', ' ', $answer['category'] ?? 'assistant'))); ?>
                            </span>
                        </div>
                        <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-700"><?= e($answer['reply']); ?></p>
                        <?php if (($answer['source'] ?? '') === 'fallback' && !empty($answer['fallback_reason']) && !in_array($answer['fallback_reason'], ['disabled', 'incomplete_config'], true)): ?>
                            <p class="mt-3 text-xs text-slate-500">
                                AI was unavailable for this request, so the assistant used the local fallback answer.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <aside class="space-y-6">
            <section class="assistant-panel">
                <h2 class="section-title">Active Services</h2>
                <?php if (empty($services)): ?>
                    <p class="mt-3 text-sm text-slate-600">No active services are available yet.</p>
                <?php else: ?>
                    <ul class="mt-4 divide-y divide-slate-200">
                        <?php foreach ($services as $service): ?>
                            <li class="py-3">
                                <p class="font-semibold text-slate-950"><?= e($service['name']); ?></p>
                                <p class="mt-1 text-sm text-slate-600">
                                    <?= e(format_money($service['fee'])); ?> -
                                    <?= ((int) $service['requires_payment'] === 1) ? 'Simulated payment required' : 'No payment required'; ?>
                                </p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <section class="assistant-panel">
                <h2 class="section-title">Helpful Areas</h2>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li>Services and requirements</li>
                    <li>Request tracking and status meanings</li>
                    <li>Simulated payment steps</li>
                    <li>Complaint filing and complaint statuses</li>
                    <li>Final document download rules</li>
                    <li>Community updates, reports, and navigation help</li>
                </ul>
            </section>
        </aside>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
