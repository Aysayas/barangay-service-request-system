<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="workflow-page mx-auto max-w-3xl">
    <div class="workflow-header">
        <div>
            <p class="workflow-kicker">New Complaint</p>
            <h1 class="workflow-title">Submit Complaint</h1>
            <p class="workflow-subtitle">
                Use this for barangay concerns such as noise, sanitation, disputes, disturbance, or property issues.
            </p>
        </div>
        <a class="btn-secondary" href="<?= site_url('resident/complaints'); ?>">Back to Complaints</a>
    </div>

    <form class="workflow-form-card space-y-5" method="POST" action="<?= site_url('resident/complaints/store'); ?>" enctype="multipart/form-data">
        <?php csrf_field(); ?>

        <div class="alert-info">
            Complaints are submitted through resident accounts so barangay staff can review details, follow up, and keep records organized.
        </div>

        <div>
            <label class="form-label" for="subject">Subject <span class="text-rose-700">(Required)</span></label>
            <input class="form-input" id="subject" type="text" name="subject" value="<?= old_value($old, 'subject'); ?>" maxlength="160" required>
            <p class="mt-2 text-xs text-slate-600">Use a short, specific title for the concern.</p>
        </div>

        <div>
            <label class="form-label" for="category">Category <span class="text-rose-700">(Required)</span></label>
            <select class="form-input" id="category" name="category" required>
                <option value="">Choose a category</option>
                <?php foreach ($categories as $value => $label): ?>
                    <option value="<?= e($value); ?>" <?= (($old['category'] ?? '') === $value) ? 'selected' : ''; ?>>
                        <?= e($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label" for="description">Description <span class="text-rose-700">(Required)</span></label>
            <textarea class="form-input min-h-40" id="description" name="description" required><?= old_value($old, 'description'); ?></textarea>
            <p class="mt-2 text-xs text-slate-600">Explain what happened, who was involved, and what help you need from the barangay.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="form-label" for="incident_date">Incident Date <span class="text-slate-500">(Optional)</span></label>
                <input class="form-input" id="incident_date" type="date" name="incident_date" value="<?= old_value($old, 'incident_date'); ?>" max="<?= e(date('Y-m-d')); ?>">
            </div>
            <div>
                <label class="form-label" for="complainant_contact">Contact Number <span class="text-rose-700">(Required)</span></label>
                <input class="form-input" id="complainant_contact" type="text" name="complainant_contact" value="<?= old_value($old, 'complainant_contact', $user['contact_number'] ?? ''); ?>" maxlength="30" required>
                <p class="mt-2 text-xs text-slate-600">Barangay staff may use this number for follow-up questions.</p>
            </div>
        </div>

        <div>
            <label class="form-label" for="location">Incident Location <span class="text-rose-700">(Required)</span></label>
            <input class="form-input" id="location" type="text" name="location" value="<?= old_value($old, 'location'); ?>" maxlength="255" required>
        </div>

        <div>
            <label class="form-label" for="respondent_name">Respondent Name <span class="text-slate-500">(Optional)</span></label>
            <input class="form-input" id="respondent_name" type="text" name="respondent_name" value="<?= old_value($old, 'respondent_name'); ?>" maxlength="160">
            <p class="mt-2 text-xs text-slate-600">Optional. Add this only if you know the person or business involved.</p>
        </div>

        <div>
            <label class="form-label" for="attachments">Evidence Attachments <span class="text-slate-500">(Optional)</span></label>
            <input class="form-input" id="attachments" type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
            <p class="mt-2 text-xs text-slate-600">
                Optional. Allowed: PDF, JPG, PNG, DOC, DOCX. Maximum <?= e($max_upload_mb); ?>MB per file.
                Upload clear photos, scans, or documents that support the concern.
            </p>
        </div>

        <div class="mobile-action-row">
            <button class="btn-primary" type="submit">Submit Complaint</button>
            <a class="btn-secondary" href="<?= site_url('resident/complaints'); ?>">Cancel</a>
        </div>
    </form>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
