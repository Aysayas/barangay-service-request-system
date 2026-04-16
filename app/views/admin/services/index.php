<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-normal text-teal-700">Admin</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-950">Services</h1>
            <p class="mt-3 max-w-2xl text-zinc-700">Create, edit, and enable or disable resident services.</p>
        </div>
        <a class="btn-primary" href="<?= site_url('admin/services/create'); ?>">Create Service</a>
    </div>

    <?php if (empty($services)): ?>
        <div class="mt-8 rounded-md border border-zinc-200 bg-white p-5 text-sm text-zinc-600">
            No services yet. Create the first service to let residents submit requests.
        </div>
    <?php else: ?>
        <div class="mt-8 overflow-x-auto rounded-md border border-zinc-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-100 text-zinc-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Name</th>
                        <th class="px-4 py-3 font-medium">Fee</th>
                        <th class="px-4 py-3 font-medium">Payment</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium text-zinc-950"><?= e($service['name']); ?></p>
                                <p class="mt-1 text-xs text-zinc-600"><?= e($service['slug']); ?></p>
                            </td>
                            <td class="px-4 py-3 text-zinc-700"><?= e(format_money($service['fee'])); ?></td>
                            <td class="px-4 py-3 text-zinc-700"><?= ((int) $service['requires_payment'] === 1) ? 'Required' : 'No'; ?></td>
                            <td class="px-4 py-3">
                                <span class="rounded-md px-2 py-1 text-xs font-medium <?= ((int) $service['is_active'] === 1) ? 'bg-teal-50 text-teal-900' : 'bg-zinc-100 text-zinc-800'; ?>">
                                    <?= ((int) $service['is_active'] === 1) ? 'Active' : 'Disabled'; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a class="btn-secondary" href="<?= site_url('admin/services/edit/' . $service['id']); ?>">Edit</a>
                                    <form method="POST" action="<?= site_url('admin/services/toggle/' . $service['id']); ?>">
                                        <?php csrf_field(); ?>
                                        <button class="btn-secondary" type="submit" onclick="return confirm('Update this service availability?');"><?= ((int) $service['is_active'] === 1) ? 'Disable' : 'Enable'; ?></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require APP_DIR . 'views/layouts/footer.php'; ?>
