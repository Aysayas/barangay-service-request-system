<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php
$selected_id = (int) ($old['service_id'] ?? ($selected_service['id'] ?? 0));
?>

<section class="workflow-page mx-auto max-w-3xl">
    <div class="workflow-header">
        <div>
            <p class="workflow-kicker">New Request</p>
            <h1 class="workflow-title">Submit Service Request</h1>
            <p class="workflow-subtitle">Fill in the request details and upload your requirement files.</p>
        </div>
        <a class="btn-secondary" href="<?= site_url('resident/services'); ?>">Back to Services</a>
    </div>

    <?php if (!empty($selected_service)): ?>
        <div class="rounded-md border border-teal-200 bg-teal-50 p-4 text-sm text-teal-900">
            <p class="font-semibold"><?= e($selected_service['name']); ?> requirements</p>
            <p class="mt-1 leading-6"><?= e($selected_service['requirements_text']); ?></p>
        </div>
    <?php endif; ?>

    <form class="workflow-form-card space-y-5" method="POST" action="<?= site_url('resident/requests/store'); ?>" enctype="multipart/form-data">
        <?php csrf_field(); ?>

        <div class="alert-info">
            Complete all required fields and upload clear, readable requirement files so barangay staff can review your request without delay.
        </div>

        <div>
            <label class="form-label" for="service_id">Service <span class="text-rose-700">(Required)</span></label>
            <select class="form-input" id="service_id" name="service_id" required>
                <option value="">Choose a service</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= e($service['id']); ?>" <?= ((int) $service['id'] === $selected_id) ? 'selected' : ''; ?>>
                        <?= e($service['name']); ?> - <?= e(format_money($service['fee'])); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="mt-2 text-xs text-slate-600">Choose the barangay service that matches your request purpose.</p>
        </div>

        <div>
            <label class="form-label" for="purpose">Purpose <span class="text-rose-700">(Required)</span></label>
            <textarea class="form-input min-h-32" id="purpose" name="purpose" required><?= old_value($old, 'purpose'); ?></textarea>
            <p class="mt-2 text-xs text-slate-600">State why you need the document or service. Example: employment, school requirement, medical assistance, or permit renewal.</p>
        </div>

        <div>
            <label class="form-label" for="remarks">Remarks <span class="text-slate-500">(Optional)</span></label>
            <textarea class="form-input min-h-24" id="remarks" name="remarks"><?= old_value($old, 'remarks'); ?></textarea>
            <p class="mt-2 text-xs text-slate-600">Optional notes that may help barangay staff review your request.</p>
        </div>

        <div>
            <label class="form-label" for="attachments">Requirement Attachments <span class="text-rose-700">(Required)</span></label>
            <input class="form-input" id="attachments" type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
            <p class="mt-2 text-xs text-slate-600">
                Allowed: PDF, JPG, PNG, DOC, DOCX. Maximum <?= e($max_upload_mb); ?>MB per file.
                Upload readable scans or photos of the exact requirements listed for the selected service.
            </p>
        </div>

        <div class="mobile-action-row">
            <button class="btn-primary" type="submit">Submit Request</button>
            <a class="btn-secondary" href="<?= site_url('resident/requests'); ?>">Cancel</a>
        </div>
    </form>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
