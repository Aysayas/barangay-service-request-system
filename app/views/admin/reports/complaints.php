<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Admin Reports</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950">Complaint Reports</h1>
            <p class="mt-3 max-w-2xl text-zinc-700">Review complaint totals by status, priority, category, and assigned staff.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="btn-primary" href="<?= e($export_url); ?>">Export CSV</a>
            <a class="btn-secondary" href="<?= site_url('admin/reports'); ?>">Back to Reports</a>
        </div>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Total</p><p class="mt-2 text-2xl font-bold text-zinc-950"><?= e($summary['total_complaints']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Open</p><p class="mt-2 text-2xl font-bold text-amber-700"><?= e($summary['open_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Submitted</p><p class="mt-2 text-2xl font-bold text-zinc-950"><?= e($summary['submitted_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Under Review</p><p class="mt-2 text-2xl font-bold text-amber-700"><?= e($summary['under_review_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Needs Info</p><p class="mt-2 text-2xl font-bold text-rose-700"><?= e($summary['needs_info_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Investigating</p><p class="mt-2 text-2xl font-bold text-amber-700"><?= e($summary['investigating_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Resolved</p><p class="mt-2 text-2xl font-bold text-teal-700"><?= e($summary['resolved_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Closed</p><p class="mt-2 text-2xl font-bold text-teal-700"><?= e($summary['closed_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5"><p class="text-sm font-medium text-zinc-600">Dismissed</p><p class="mt-2 text-2xl font-bold text-rose-700"><?= e($summary['dismissed_count']); ?></p></div>
        <div class="rounded-md border border-zinc-200 bg-white p-5">
            <p class="text-sm font-medium text-zinc-600">Most Common Category</p>
            <p class="mt-2 text-base font-bold text-zinc-950"><?= !empty($summary['most_common_category']) ? e(complaint_category_label($summary['most_common_category'])) : 'None yet'; ?></p>
            <p class="mt-1 text-xs text-zinc-600"><?= e($summary['most_common_category_total']); ?> complaint(s)</p>
        </div>
    </div>

    <form class="mt-8 grid gap-4 rounded-md border border-zinc-200 bg-white p-5 md:grid-cols-6" method="GET" action="<?= site_url('admin/reports/complaints'); ?>">
        <div>
            <label class="form-label" for="from_date">From</label>
            <input class="form-input" id="from_date" type="date" name="from_date" value="<?= e($filters['from_date']); ?>">
        </div>
        <div>
            <label class="form-label" for="to_date">To</label>
            <input class="form-input" id="to_date" type="date" name="to_date" value="<?= e($filters['to_date']); ?>">
        </div>
        <div>
            <label class="form-label" for="category">Category</label>
            <select class="form-input" id="category" name="category">
                <option value="">All</option>
                <?php foreach ($categories as $category => $label): ?>
                    <option value="<?= e($category); ?>" <?= ($filters['category'] === $category) ? 'selected' : ''; ?>><?= e($label); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label" for="priority">Priority</label>
            <select class="form-input" id="priority" name="priority">
                <option value="">All</option>
                <?php foreach ($priorities as $priority): ?>
                    <option value="<?= e($priority); ?>" <?= ($filters['priority'] === $priority) ? 'selected' : ''; ?>><?= e(complaint_priority_label($priority)); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label" for="status">Status</label>
            <select class="form-input" id="status" name="status">
                <option value="">All</option>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status); ?>" <?= ($filters['status'] === $status) ? 'selected' : ''; ?>><?= e(complaint_status_label($status)); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-end gap-3">
            <button class="btn-primary" type="submit">Apply</button>
            <a class="btn-secondary" href="<?= site_url('admin/reports/complaints'); ?>">Reset</a>
        </div>
    </form>

    <?php if (empty($rows)): ?>
        <div class="mt-8 rounded-md border border-zinc-200 bg-white p-5 text-sm text-zinc-600">No complaints matched the selected filters.</div>
    <?php else: ?>
        <div class="mt-8 overflow-x-auto rounded-md border border-zinc-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-100 text-zinc-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Reference</th>
                        <th class="px-4 py-3 font-medium">Complainant</th>
                        <th class="px-4 py-3 font-medium">Subject</th>
                        <th class="px-4 py-3 font-medium">Category</th>
                        <th class="px-4 py-3 font-medium">Priority</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Assigned Staff</th>
                        <th class="px-4 py-3 font-medium">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-zinc-950"><?= e($row['reference_no']); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e($row['complainant_name']); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e($row['subject']); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e(complaint_category_label($row['category'])); ?></td>
                            <td class="px-4 py-3"><span class="rounded-md px-2 py-1 text-xs font-medium <?= complaint_priority_badge_class($row['priority']); ?>"><?= e(complaint_priority_label($row['priority'])); ?></span></td>
                            <td class="px-4 py-3"><span class="rounded-md px-2 py-1 text-xs font-medium <?= complaint_status_badge_class($row['status']); ?>"><?= e(complaint_status_label($row['status'])); ?></span></td>
                            <td class="px-4 py-3 text-zinc-700"><?= !empty($row['assigned_to_name']) ? e($row['assigned_to_name']) : 'Unassigned'; ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e(date('M d, Y', strtotime($row['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
