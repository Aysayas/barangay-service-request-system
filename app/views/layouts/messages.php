<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php
$session = load_class('session', 'libraries');
$success = $session->flashdata('success');
$error = $session->flashdata('error');
$errors = $session->flashdata('errors') ?: [];
?>

<?php if (!empty($success)): ?>
    <div class="mb-6 rounded-md border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-950" role="status">
        <p class="font-semibold">Success</p>
        <p class="mt-1"><?= e($success); ?></p>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="mb-6 rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-950" role="alert">
        <p class="font-semibold">Action needed</p>
        <p class="mt-1"><?= e($error); ?></p>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="mb-6 rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-950" role="alert">
        <p class="font-medium">Please check the following:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            <?php foreach ($errors as $message): ?>
                <li><?= e($message); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
