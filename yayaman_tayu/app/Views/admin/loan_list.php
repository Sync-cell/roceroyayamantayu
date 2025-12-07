
<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<style>
.table-pro { border-radius:10px; box-shadow:0 8px 30px rgba(0,0,0,0.05); overflow:hidden; }
.badge-pending { background:#fff3cd; color:#856404; padding:6px 10px; border-radius:16px; font-weight:700; }
.badge-approved { background:#d4edda; color:#155724; padding:6px 10px; border-radius:16px; font-weight:700; }
.badge-rejected { background:#f8d7da; color:#721c24; padding:6px 10px; border-radius:16px; font-weight:700; }
.small-muted { font-size:13px; color:#6b7280; }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="bi bi-receipt"></i> Loan Applications</h4>
        <a href="<?= base_url('/admin/loan/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Loan
        </a>
    </div>

    <div class="card table-pro">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Borrower</th>
                        <th>Amount</th>
                        <th>Term</th>
                        <th>KYC Status</th>
                        <th>Loan Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($loans)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 32px; color: #ccc;"></i>
                                <p class="small-muted mt-2">No loan applications found.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($loans as $loan): ?>
                            <tr>
                                <td><strong>#<?= esc($loan['id'] ?? '-') ?></strong></td>
                                <td>
                                    <div style="font-weight: 700; color: #0083B0;">
                                        <?= esc($loan['customer_name'] ?? 'Unknown Customer') ?>
                                    </div>
                                    <div class="small-muted"><?= esc($loan['customer_email'] ?? 'No email') ?></div>
                                </td>
                                <td>
                                    <strong>₱<?= number_format($loan['amount'] ?? 0, 2) ?></strong>
                                </td>
                                <td><?= esc($loan['term'] ?? '-') ?> months</td>
                                <td>
                                    <?php 
                                        $kycStatus = strtolower($loan['kyc_status'] ?? 'none');
                                        if ($kycStatus === 'approved'): 
                                    ?>
                                        <span class="badge-approved"><i class="bi bi-check-circle"></i> Verified</span>
                                    <?php elseif ($kycStatus === 'pending'): ?>
                                        <span class="badge-pending"><i class="bi bi-hourglass-split"></i> Pending</span>
                                    <?php elseif ($kycStatus === 'rejected'): ?>
                                        <span class="badge-rejected"><i class="bi bi-x-circle"></i> Rejected</span>
                                    <?php else: ?>
                                        <span class="small-muted"><i class="bi bi-dash-circle"></i> No KYC</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        $status = strtolower($loan['status'] ?? 'pending');
                                        if ($status === 'approved'): 
                                    ?>
                                        <span class="badge-approved">Approved</span>
                                    <?php elseif ($status === 'rejected'): ?>
                                        <span class="badge-rejected">Rejected</span>
                                    <?php elseif ($status === 'released'): ?>
                                        <span class="badge-approved">Released</span>
                                    <?php else: ?>
                                        <span class="badge-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small-muted">
                                    <?= esc(isset($loan['created_at']) ? date('M d, Y', strtotime($loan['created_at'])) : '-') ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= base_url('/admin/loan/' . ($loan['id'] ?? '#')) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Review
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>