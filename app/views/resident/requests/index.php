<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Request History</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950">My Requests</h1>
            <p class="mt-3 max-w-2xl text-zinc-700">
                Track the status of every service request you submitted.
            </p>
        </div>
        <a class="btn-primary" href="<?= site_url('resident/services'); ?>">New Request</a>
    </div>

    <?php if (empty($requests)): ?>
        <div class="mt-8 rounded-md border border-zinc-200 bg-white p-5">
            <p class="text-sm text-zinc-600">You have not submitted any requests yet.</p>
            <a class="mt-4 inline-block font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('resident/services'); ?>">
                Browse available services
            </a>
        </div>
    <?php else: ?>
        <div class="mt-8 overflow-x-auto rounded-md border border-zinc-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-100 text-zinc-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Reference No.</th>
                        <th class="px-4 py-3 font-medium">Service</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Created</th>
                        <th class="px-4 py-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-zinc-950"><?= e($request['reference_no']); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= e($request['service_name']); ?></td>
                            <td class="px-4 py-3">
                                <span class="rounded-md px-2 py-1 text-xs font-medium <?= status_badge_class($request['status']); ?>">
                                    <?= e(status_label($request['status'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-700"><?= e(date('M d, Y h:i A', strtotime($request['created_at']))); ?></td>
                            <td class="px-4 py-3">
                                <a class="font-medium text-teal-700 hover:text-teal-800" href="<?= site_url('resident/requests/' . $request['id']); ?>">
                                    View Details
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
