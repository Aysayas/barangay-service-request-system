<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php
$session = load_class('session', 'libraries');
$success = $session->flashdata('success');
$error = $session->flashdata('error');
$errors = $session->flashdata('errors') ?: [];
?>

<?php if (!empty($success)): ?>
    <div class="message-card border-teal-200 text-teal-950" role="status">
        <div class="flex gap-3">
            <span class="message-dot bg-teal-600"></span>
            <div class="min-w-0">
                <p class="font-semibold">Success</p>
                <p class="mt-1 text-teal-900"><?= e($success); ?></p>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="message-card border-rose-200 text-rose-950" role="alert">
        <div class="flex gap-3">
            <span class="message-dot bg-rose-600"></span>
            <div class="min-w-0">
                <p class="font-semibold">Action needed</p>
                <p class="mt-1 text-rose-900"><?= e($error); ?></p>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="message-card border-rose-200 text-rose-950" role="alert">
        <div class="flex gap-3">
            <span class="message-dot bg-rose-600"></span>
            <div class="min-w-0">
                <p class="font-semibold">Please check the following</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-rose-900">
                    <?php foreach ($errors as $message): ?>
                        <li><?= e($message); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>
