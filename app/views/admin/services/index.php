<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php require APP_DIR . 'views/layouts/header.php'; ?>

<section class="management-page">
    <div class="management-header">
        <div>
            <p class="page-kicker">Admin</p>
            <h1 class="management-title">Services</h1>
            <p class="management-subtitle">Create, edit, and enable or disable resident services.</p>
        </div>
        <a class="btn-primary" href="<?= site_url('admin/services/create'); ?>">Create Service</a>
    </div>

    <?php if (empty($services)): ?>
        <div class="empty-state-strong mt-8">
            <h2 class="text-lg font-semibold text-slate-950">No resident services have been configured yet.</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Create the first barangay service so residents can submit requests with the right requirements and fees.
            </p>
            <a class="btn-primary mt-5" href="<?= site_url('admin/services/create'); ?>">Create First Service</a>
        </div>
    <?php else: ?>
        <div class="management-table-wrap">
            <table class="management-table management-table-wide">
                <thead>
                    <tr>
                        <th class="px-4 py-3 font-medium">Name</th>
                        <th class="px-4 py-3 font-medium">Fee</th>
                        <th class="px-4 py-3 font-medium">Payment</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-950"><?= e($service['name']); ?></p>
                                <p class="mt-1 text-xs text-slate-600"><?= e($service['slug']); ?></p>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?= e(format_money($service['fee'])); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= ((int) $service['requires_payment'] === 1) ? 'Required' : 'No'; ?></td>
                            <td class="px-4 py-3">
                                <span class="status-pill <?= ((int) $service['is_active'] === 1) ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-slate-200 bg-slate-100 text-slate-800'; ?>">
                                    <?= ((int) $service['is_active'] === 1) ? 'Active' : 'Disabled'; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="management-row-actions">
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
