
<?php $this->extend('layout/customer'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-file-earmark-text"></i> My Loans</h2>
                    <p class="text-muted mb-0">Track and manage your loan applications</p>
                </div>
                <a href="<?= site_url('/customer/loan/apply') ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle"></i> Apply for Loan
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Empty State -->
    <?php if (empty($loans ?? [])): ?>
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                        <h5 class="mt-3 mb-2">No Loans Yet</h5>
                        <p class="text-muted mb-4">You haven't applied for any loans. Start by submitting your first loan application.</p>
                        <a href="<?= site_url('/customer/loan/apply') ?>" class="btn btn-primary btn-lg">
                            <i class="bi bi-plus-circle"></i> Apply for Loan Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Loans Grid -->
        <div class="row">
            <?php foreach ($loans as $loan): ?>
                <?php
                    $statusClass = '';
                    $statusIcon = '';
                    $statusColor = '';

                    $loanStatus = strtolower(trim($loan['status'] ?? 'pending'));
                    if (in_array($loanStatus, ['approved', 'approve'])) {
                        $statusClass = 'bg-success';
                        $statusIcon = 'bi-check-circle';
                        $statusColor = '#28a745';
                    } elseif (in_array($loanStatus, ['rejected', 'reject', 'declined'])) {
                        $statusClass = 'bg-danger';
                        $statusIcon = 'bi-x-circle';
                        $statusColor = '#dc3545';
                    } elseif (in_array($loanStatus, ['processing', 'under review'])) {
                        $statusClass = 'bg-info';
                        $statusIcon = 'bi-hourglass-split';
                        $statusColor = '#0dcaf0';
                    } else {
                        $statusClass = 'bg-warning';
                        $statusIcon = 'bi-clock';
                        $statusColor = '#ffc107';
                    }
                ?>
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card border-0 shadow-lg h-100 hover-shadow" style="transition: all 0.3s ease;">
                        <div class="card-body">
                            <!-- Status Badge -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge <?= $statusClass ?>">
                                        <i class="bi <?= $statusIcon ?>"></i> <?= ucfirst(esc($loan['status'] ?? 'Pending')) ?>
                                    </span>
                                </div>
                                <small class="text-muted"><?= date('M d, Y', strtotime($loan['created_at'])) ?></small>
                            </div>

                            <!-- Loan Amount -->
                            <div class="mb-3">
                                <small class="text-muted">Loan Amount</small>
                                <h4 class="mb-0" style="color: var(--brand-1);">
                                    ₱<?= number_format($loan['amount'] ?? 0, 2) ?>
                                </h4>
                            </div>

                            <!-- Loan Purpose -->
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Purpose</small>
                                <p class="mb-0"><?= esc(substr($loan['purpose'] ?? 'N/A', 0, 60)) ?>...</p>
                            </div>

                            <!-- Loan Details (if applicable) -->
                            <?php if (!empty($loan['term'])): ?>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Term</small>
                                        <p class="mb-0"><?= esc($loan['term']) ?> months</p>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Interest Rate</small>
                                        <p class="mb-0"><?= esc($loan['interest_rate'] ?? 'N/A') ?>%</p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Action Button -->
                            <div class="mt-4 pt-3 border-top">
                                <a href="<?= site_url('/customer/loan/details/' . $loan['id']) ?>" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination (if applicable) -->
        <?php if (!empty($pager)): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <?= $pager->links() ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
    .hover-shadow:hover {
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12) !important;
        transform: translateY(-4px);
    }

    .card {
        border-radius: 8px;
        overflow: hidden;
    }

    .badge {
        padding: 6px 12px;
        font-weight: 600;
    }
</style>

<?php $this->endSection(); ?>