
<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-people"></i> Customers List</h2>
                <a href="<?= site_url('/admin') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #00B4DB 0%, #0083B0 100%); color: white;">
                    <h6 class="mb-0"><i class="bi bi-list"></i> All Customers</h6>
                </div>

                <div class="card-body p-0">
                    <?php if (!empty($customers) && is_array($customers)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background: #F5F5F5;">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customers as $customer): ?>
                                        <tr>
                                            <td><strong>#<?= esc($customer['id'] ?? '-') ?></strong></td>
                                            <td><?= esc($customer['name'] ?? '-') ?></td>
                                            <td><?= esc($customer['email'] ?? '-') ?></td>
                                            <td><?= esc($customer['phone'] ?? '-') ?></td>
                                            <td>
                                                <?php $isVerified = $customer['is_verified'] ?? 0; ?>
                                                <span class="badge" style="background: <?= $isVerified ? '#28a745' : '#ffc107' ?>;">
                                                    <?= $isVerified ? 'Verified' : 'Pending' ?>
                                                </span>
                                            </td>
                                            <td><?= isset($customer['created_at']) ? date('M d, Y', strtotime($customer['created_at'])) : '-' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (!empty($pager)): ?>
                            <nav aria-label="Page navigation" class="mt-3 p-3">
                                <?= $pager->links('bootstrap') ?>
                            </nav>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No customers found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>