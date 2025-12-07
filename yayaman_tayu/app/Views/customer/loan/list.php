<?php
$this->extend('layout/customer'); ?>

<?php $this->section('content'); ?>
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-list-check"></i> My Loan Applications</h2>
                <a href="<?= site_url('/customer/loan/apply') ?>" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> New Application
                </a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($loans ?? [])): ?>
                <div class="panel text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="mt-3 text-muted">You have not applied for any loans yet.</p>
                    <a href="<?= site_url('/customer/loan/apply') ?>" class="btn btn-success">Apply Now</a>
                </div>
            <?php else: ?>
                <div class="panel table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Amount</th>
                                <th>Purpose</th>
                                <th>Term</th>
                                <th>Status</th>
                                <th>Applied</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loans as $loan): ?>
                                <tr>
                                    <td><?= esc($loan['id']) ?></td>
                                    <td>₱<?= number_format($loan['amount'], 2) ?></td>
                                    <td><?= esc(strlen($loan['purpose']) > 60 ? substr($loan['purpose'],0,57) . '...' : $loan['purpose']) ?></td>
                                    <td><?= esc($loan['term_months'] ?? ($loan['term_days'] . ' days')) ?> <?= isset($loan['term_months']) ? 'months' : '' ?></td>
                                    <td>
                                        <span class="badge bg-<?= $loan['status'] === 'Approved' ? 'success' : ($loan['status'] === 'Rejected' ? 'danger' : ($loan['status'] === 'Released' ? 'info' : 'warning')) ?>">
                                            <?= esc($loan['status']) ?>
                                        </span>
                                    </td>
                                    <td><small class="text-muted"><?= date('M d, Y', strtotime($loan['created_at'])) ?></small></td>
                                    <td class="text-end">
                                        <a href="<?= site_url('/customer/loan/details/' . $loan['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($pager) && method_exists($pager, 'links')): ?>
                    <div class="mt-3">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
```// filepath: c:\xampp\htdocs\yayaman_tayu\app\Views\customer\loan\list.php
<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-list-check"></i> My Loan Applications</h2>
                <a href="<?= site_url('/customer/loan/apply') ?>" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> New Application
                </a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($loans ?? [])): ?>
                <div class="panel text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="mt-3 text-muted">You have not applied for any loans yet.</p>
                    <a href="<?= site_url('/customer/loan/apply') ?>" class="btn btn-success">Apply Now</a>
                </div>
            <?php else: ?>
                <div class="panel table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Amount</th>
                                <th>Purpose</th>
                                <th>Term</th>
                                <th>Status</th>
                                <th>Applied</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loans as $loan): ?>
                                <tr>
                                    <td><?= esc($loan['id']) ?></td>
                                    <td>₱<?= number_format($loan['amount'], 2) ?></td>
                                    <td><?= esc(strlen($loan['purpose']) > 60 ? substr($loan['purpose'],0,57) . '...' : $loan['purpose']) ?></td>
                                    <td><?= esc($loan['term_months'] ?? ($loan['term_days'] . ' days')) ?> <?= isset($loan['term_months']) ? 'months' : '' ?></td>
                                    <td>
                                        <span class="badge bg-<?= $loan['status'] === 'Approved' ? 'success' : ($loan['status'] === 'Rejected' ? 'danger' : ($loan['status'] === 'Released' ? 'info' : 'warning')) ?>">
                                            <?= esc($loan['status']) ?>
                                        </span>
                                    </td>
                                    <td><small class="text-muted"><?= date('M d, Y', strtotime($loan['created_at'])) ?></small></td>
                                    <td class="text-end">
                                        <a href="<?= site_url('/customer/loan/details/' . $loan['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($pager) && method_exists($pager, 'links')): ?>
                    <div class="mt-3">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>