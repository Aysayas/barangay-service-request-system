<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<?php $base_url = site_url('admin/complaints'); ?>

<section>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Admin Oversight</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950">Complaints</h1>
            <p class="mt-3 max-w-2xl text-zinc-700">
                Monitor resident complaints, staff handling, evidence, and resolution status.
            </p>
        </div>
        <a class="btn-secondary" href="<?= site_url('admin/dashboard'); ?>">Back to Dashboard</a>
    </div>

    <div class="mt-6 rounded-md border border-zinc-200 bg-white p-5">
        <form class="grid gap-4 md:grid-cols-[0.7fr_1fr_auto]" method="GET" action="<?= $base_url; ?>">
            <div>
                <label class="form-label" for="status">Status</label>
                <select class="form-input" id="status" name="status">
                    <option value="all" <?= ($current_status === 'all') ? 'selected' : ''; ?>>All</option>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= e($status); ?>" <?= ($current_status === $status) ? 'selected' : ''; ?>>
                            <?= e(complaint_status_label($status)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" for="search">Search</label>
                <input class="form-input" id="search" type="text" name="search" value="<?= e($search); ?>" placeholder="Reference, subject, complainant, or category">
            </div>
            <div class="flex items-end gap-3">
                <button class="btn-primary" type="submit">Apply</button>
                <a class="btn-secondary" href="<?= $base_url; ?>">Reset</a>
            </div>
        </form>
    </div>

    <?php if (empty($complaints)): ?>
        <div class="mt-8 rounded-md border border-zinc-200 bg-white p-5 text-sm text-zinc-600">
            No complaints matched your filters.
        </div>
    <?php else: ?>
        <div class="mt-8 overflow-x-auto rounded-md border border-zinc-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-100 text-zinc-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Reference No.</th>
                        <th class="px-4 py-3 font-medium">Complainant</th>
                        <th class="px-4 py-3 font-medium">Subject</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Priority</th>
                        <th class="px-4 py-3 font-medium">Assigned</th>
                        <th class="px-4 py-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    <?php foreach ($complaints as $complaint): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-zinc-950"><?= e($complaint['reference_no']); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e($complaint['complainant_name']); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e($complaint['subject']); ?></td>
                            <td class="px-4 py-3">
                                <span class="rounded-md px-2 py-1 text-xs font-medium <?= complaint_status_badge_class($complaint['status']); ?>">
                                    <?= e(complaint_status_label($complaint['status'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-md px-2 py-1 text-xs font-medium <?= complaint_priority_badge_class($complaint['priority']); ?>">
                                    <?= e(complaint_priority_label($complaint['priority'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-700"><?= e($complaint['assigned_to_name'] ?: 'Unassigned'); ?></td>
                            <td class="px-4 py-3">
                                <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('admin/complaints/' . $complaint['id']); ?>">
                                    Open
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
