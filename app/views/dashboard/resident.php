<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Resident</p>
    <h1 class="mt-2 text-3xl font-bold text-zinc-950">Welcome, <?= e($user['name'] ?? 'Resident'); ?></h1>
    <p class="mt-3 max-w-2xl text-zinc-700">
        Submit service requests, upload requirements, and track your request status.
    </p>

    <div class="mt-6 flex flex-wrap gap-3">
        <a class="btn-primary" href="<?= site_url('resident/services'); ?>">Browse Services</a>
        <a class="btn-secondary" href="<?= site_url('resident/requests'); ?>">View My Requests</a>
        <a class="btn-secondary" href="<?= site_url('assistant'); ?>">Ask Assistant</a>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-4">
        <div class="rounded-md border border-zinc-200 bg-white p-5">
            <p class="text-sm font-medium text-zinc-600">Total Requests</p>
            <p class="mt-2 text-2xl font-bold text-zinc-950"><?= e($counts['total_requests'] ?? 0); ?></p>
        </div>
        <div class="rounded-md border border-zinc-200 bg-white p-5">
            <p class="text-sm font-medium text-zinc-600">Submitted</p>
            <p class="mt-2 text-2xl font-bold text-amber-700"><?= e($counts['submitted_count'] ?? 0); ?></p>
        </div>
        <div class="rounded-md border border-zinc-200 bg-white p-5">
            <p class="text-sm font-medium text-zinc-600">Under Review</p>
            <p class="mt-2 text-2xl font-bold text-amber-700"><?= e($counts['under_review_count'] ?? 0); ?></p>
        </div>
        <div class="rounded-md border border-zinc-200 bg-white p-5">
            <p class="text-sm font-medium text-zinc-600">Approved/Released</p>
            <p class="mt-2 text-2xl font-bold text-teal-700"><?= e($counts['completed_count'] ?? 0); ?></p>
        </div>
    </div>

    <div class="mt-8">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-zinc-950">Recent Requests</h2>
            <a class="text-sm font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('resident/requests'); ?>">View all</a>
        </div>

        <?php if (empty($recent_requests)): ?>
            <div class="mt-4 rounded-md border border-zinc-200 bg-white p-5 text-sm text-zinc-600">
                No requests yet. Browse services to submit your first request.
            </div>
        <?php else: ?>
            <div class="mt-4 overflow-x-auto rounded-md border border-zinc-200 bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="bg-zinc-100 text-zinc-700">
                        <tr>
                            <th class="px-4 py-3 font-medium">Reference</th>
                            <th class="px-4 py-3 font-medium">Service</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Date</th>
                            <th class="px-4 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        <?php foreach ($recent_requests as $request): ?>
                            <tr>
                                <td class="px-4 py-3 font-medium text-zinc-950"><?= e($request['reference_no']); ?></td>
                                <td class="px-4 py-3 text-zinc-700"><?= e($request['service_name']); ?></td>
                                <td class="px-4 py-3">
                                    <span class="rounded-md px-2 py-1 text-xs font-medium <?= status_badge_class($request['status']); ?>">
                                        <?= e(status_label($request['status'])); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-zinc-700"><?= e(date('M d, Y', strtotime($request['created_at']))); ?></td>
                                <td class="px-4 py-3">
                                    <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('resident/requests/' . $request['id']); ?>">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
