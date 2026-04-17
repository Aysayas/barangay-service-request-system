<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php
$data = array_merge($post ?? [], $old ?? []);
$is_edit = ($mode === 'edit');
$action = $is_edit ? site_url('admin/community/update/' . $post['id']) : site_url('admin/community/store');
$event_time = !empty($data['event_time']) ? substr($data['event_time'], 0, 5) : '';
?>

<section class="mx-auto max-w-4xl">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Admin Community</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950"><?= $is_edit ? 'Edit Community Post' : 'Create Community Post'; ?></h1>
            <p class="mt-3 text-zinc-700">Use one form for updates, events, programs, advisories, and resources.</p>
        </div>
        <a class="btn-secondary" href="<?= site_url('admin/community'); ?>">Back to Community</a>
    </div>

    <form class="mt-6 space-y-5 rounded-md border border-zinc-200 bg-white p-6" method="POST" enctype="multipart/form-data" action="<?= $action; ?>">
        <?php csrf_field(); ?>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="form-label" for="title">Title</label>
                <input class="form-input" id="title" name="title" value="<?= e($data['title'] ?? ''); ?>" maxlength="180" required>
            </div>
            <div>
                <label class="form-label" for="category">Category</label>
                <select class="form-input" id="category" name="category" required>
                    <option value="">Choose category</option>
                    <?php foreach ($categories as $value => $label): ?>
                        <option value="<?= e($value); ?>" <?= (($data['category'] ?? '') === $value) ? 'selected' : ''; ?>>
                            <?= e($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="form-label" for="slug">Slug</label>
            <input class="form-input" id="slug" name="slug" value="<?= e($data['slug'] ?? ''); ?>" placeholder="Leave blank to generate from title">
        </div>

        <div>
            <label class="form-label" for="excerpt">Excerpt</label>
            <textarea class="form-input min-h-24" id="excerpt" name="excerpt" maxlength="255"><?= e($data['excerpt'] ?? ''); ?></textarea>
            <p class="mt-2 text-xs text-zinc-600">Short summary used on community cards. Maximum 255 characters.</p>
        </div>

        <div>
            <label class="form-label" for="content">Content</label>
            <textarea class="form-input min-h-56" id="content" name="content" required><?= e($data['content'] ?? ''); ?></textarea>
        </div>

        <section class="rounded-md border border-zinc-200 bg-zinc-50 p-4">
            <h2 class="text-sm font-semibold text-zinc-950">Optional Event Details</h2>
            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="form-label" for="event_date">Event Date</label>
                    <input class="form-input" id="event_date" type="date" name="event_date" value="<?= e($data['event_date'] ?? ''); ?>">
                </div>
                <div>
                    <label class="form-label" for="event_time">Event Time</label>
                    <input class="form-input" id="event_time" type="time" name="event_time" value="<?= e($event_time); ?>">
                </div>
                <div>
                    <label class="form-label" for="venue">Venue</label>
                    <input class="form-input" id="venue" name="venue" value="<?= e($data['venue'] ?? ''); ?>" maxlength="160">
                </div>
                <div>
                    <label class="form-label" for="organizer">Organizer</label>
                    <input class="form-input" id="organizer" name="organizer" value="<?= e($data['organizer'] ?? ''); ?>" maxlength="160">
                </div>
            </div>
        </section>

        <div>
            <label class="form-label" for="resource_link">Resource Link</label>
            <input class="form-input" id="resource_link" type="url" name="resource_link" value="<?= e($data['resource_link'] ?? ''); ?>" placeholder="https://example.com/resource">
        </div>

        <div>
            <label class="form-label" for="image">Community Image</label>
            <input class="form-input" id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
            <p class="mt-2 text-xs text-zinc-600">Optional. Allowed: JPG, PNG, WEBP. Maximum <?= e($max_upload_mb); ?>MB.</p>
            <?php if ($is_edit && !empty($post['image_path']) && safe_storage_path($post['image_path'], 'runtime/uploads/community') !== null): ?>
                <div class="mt-4 rounded-md border border-zinc-200 bg-white p-3">
                    <p class="text-sm font-medium text-zinc-800">Current image</p>
                    <img class="mt-3 h-40 w-full rounded-md object-cover" src="<?= site_url('admin/community/image/' . $post['id']); ?>" alt="<?= e($post['title']); ?>">
                    <p class="mt-2 text-xs text-zinc-600">Uploading a new image replaces this image.</p>
                </div>
            <?php elseif ($is_edit && !empty($post['image_path'])): ?>
                <p class="mt-3 rounded-md border border-amber-200 bg-amber-50 p-3 text-sm text-amber-950">
                    A saved image record exists, but the file is missing. Upload a new image to replace it.
                </p>
            <?php endif; ?>
        </div>

        <div class="flex flex-wrap gap-5 text-sm text-zinc-700">
            <label class="flex items-center gap-3">
                <input type="checkbox" name="is_published" value="1" <?= ((int) ($data['is_published'] ?? 0) === 1) ? 'checked' : ''; ?>>
                Published
            </label>
            <label class="flex items-center gap-3">
                <input type="checkbox" name="is_featured" value="1" <?= ((int) ($data['is_featured'] ?? 0) === 1) ? 'checked' : ''; ?>>
                Featured
            </label>
        </div>

        <div class="flex flex-wrap gap-3">
            <button class="btn-primary" type="submit"><?= $is_edit ? 'Save Changes' : 'Create Post'; ?></button>
            <a class="btn-secondary" href="<?= site_url('admin/community'); ?>">Cancel</a>
        </div>
    </form>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
