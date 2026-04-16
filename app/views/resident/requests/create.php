<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php
$selected_id = (int) ($old['service_id'] ?? ($selected_service['id'] ?? 0));
?>

<section class="mx-auto max-w-3xl">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">New Request</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950">Submit Service Request</h1>
            <p class="mt-3 text-zinc-700">Fill in the request details and upload your requirement files.</p>
        </div>
        <a class="btn-secondary" href="<?= site_url('resident/services'); ?>">Back to Services</a>
    </div>

    <?php if (!empty($selected_service)): ?>
        <div class="mt-6 rounded-md border border-teal-200 bg-teal-50 p-4 text-sm text-teal-900">
            <p class="font-semibold"><?= e($selected_service['name']); ?> requirements</p>
            <p class="mt-1 leading-6"><?= e($selected_service['requirements_text']); ?></p>
        </div>
    <?php endif; ?>

    <form class="mt-6 space-y-5 rounded-md border border-zinc-200 bg-white p-6" method="POST" action="<?= site_url('resident/requests/store'); ?>" enctype="multipart/form-data">
        <?php csrf_field(); ?>

        <div>
            <label class="form-label" for="service_id">Service</label>
            <select class="form-input" id="service_id" name="service_id" required>
                <option value="">Choose a service</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= e($service['id']); ?>" <?= ((int) $service['id'] === $selected_id) ? 'selected' : ''; ?>>
                        <?= e($service['name']); ?> - <?= e(format_money($service['fee'])); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label" for="purpose">Purpose</label>
            <textarea class="form-input min-h-32" id="purpose" name="purpose" required><?= old_value($old, 'purpose'); ?></textarea>
            <p class="mt-2 text-xs text-zinc-600">Example: employment, school requirement, medical assistance, permit renewal.</p>
        </div>

        <div>
            <label class="form-label" for="remarks">Remarks</label>
            <textarea class="form-input min-h-24" id="remarks" name="remarks"><?= old_value($old, 'remarks'); ?></textarea>
            <p class="mt-2 text-xs text-zinc-600">Optional notes that may help barangay staff review your request.</p>
        </div>

        <div>
            <label class="form-label" for="attachments">Requirement Attachments</label>
            <input class="form-input" id="attachments" type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
            <p class="mt-2 text-xs text-zinc-600">
                Allowed: PDF, JPG, PNG, DOC, DOCX. Maximum <?= e($max_upload_mb); ?>MB per file.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <button class="btn-primary" type="submit">Submit Request</button>
            <a class="btn-secondary" href="<?= site_url('resident/requests'); ?>">Cancel</a>
        </div>
    </form>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
