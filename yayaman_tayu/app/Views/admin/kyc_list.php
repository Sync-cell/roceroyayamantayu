
<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-card-checklist"></i> KYC Verification List</h2>
                <a href="<?= site_url('/admin') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #FF6B6B 0%, #FF4757 100%); color: white;">
                    <h6 class="mb-0"><i class="bi bi-list"></i> All KYC Records</h6>
                </div>

                <div class="card-body p-0">
                    <?php if (!empty($kycs) && is_array($kycs)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background: #F5F5F5;">
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>ID Type</th>
                                        <th>ID Number</th>
                                        <th>Status</th>
                                        <th>Submitted Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kycs as $kyc): ?>
                                        <tr>
                                            <td><strong>#<?= esc($kyc['customer_id'] ?? '-') ?></strong></td>
                                            <td><?= esc($kyc['id_type'] ?? '-') ?></td>
                                            <td><?= esc(substr($kyc['id_number'] ?? '-', 0, 12) . '...') ?></td>
                                            <td>
                                                <?php $status = $kyc['status'] ?? '-'; ?>
                                                <span class="badge" style="background: <?= $status === 'Approved' ? '#28a745' : ($status === 'Rejected' ? '#dc3545' : '#ffc107') ?>;">
                                                    <?= esc($status) ?>
                                                </span>
                                            </td>
                                            <td><?= isset($kyc['created_at']) ? date('M d, Y', strtotime($kyc['created_at'])) : '-' ?></td>
                                            <td>
                                                <a href="<?= site_url('/admin/kyc/review/' . ($kyc['id'] ?? '')) ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Review
                                                </a>
                                            </td>
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
                            <p class="text-muted mt-3">No KYC records found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>