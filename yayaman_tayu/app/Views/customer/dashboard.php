
<?php $this->extend('layout/customer'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
            <p class="text-muted">Welcome back, <?= esc(session()->get('customer_name') ?? 'Customer') ?>!</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-lg h-100" style="border-left: 5px solid #00B4DB;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <small class="text-muted">Total Loans</small>
                            <h4 class="mb-0"><?= isset($total_loans) ? $total_loans : 0 ?></h4>
                        </div>
                        <i class="bi bi-file-earmark-text" style="font-size: 2.5rem; color: #00B4DB; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-lg h-100" style="border-left: 5px solid #28a745;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <small class="text-muted">Total Borrowed</small>
                            <h4 class="mb-0">₱<?= isset($total_borrowed) ? number_format($total_borrowed, 2) : '0.00' ?></h4>
                        </div>
                        <i class="bi bi-cash-flow" style="font-size: 2.5rem; color: #28a745; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-lg h-100" style="border-left: 5px solid #ffc107;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <small class="text-muted">Pending Loans</small>
                            <h4 class="mb-0"><?= isset($pending_loans) ? $pending_loans : 0 ?></h4>
                        </div>
                        <i class="bi bi-hourglass-split" style="font-size: 2.5rem; color: #ffc107; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <?php
            // Use the KYC status passed from controller
            $kyc_status = isset($kyc_status) ? $kyc_status : 'pending';
            $kyc_normal = strtolower(trim($kyc_status));
            $is_verified = in_array($kyc_normal, ['verified', 'approve', 'approved', 'approved_by_admin', 'approved_by_system']);
        ?>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-lg h-100" style="border-left: 5px solid <?= $is_verified ? '#28a745' : '#0dcaf0' ?>;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <small class="text-muted">KYC Status</small>
                            <?php if ($is_verified): ?>
                                <h4 class="mb-0" style="color: #28a745;">
                                    <i class="bi bi-check-circle"></i> Verified
                                </h4>
                            <?php else: ?>
                                <h4 class="mb-0" style="color: #dc3545;">
                                    <i class="bi bi-clock"></i> <?= ucfirst(esc($kyc_normal)) ?>
                                </h4>
                            <?php endif; ?>
                        </div>
                        <i class="bi <?= $is_verified ? 'bi-shield-check' : 'bi-hourglass-split' ?>" style="font-size: 2.5rem; color: <?= $is_verified ? '#28a745' : '#0dcaf0' ?>; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h6 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <a href="<?= site_url('/customer/loan/apply') ?>" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-plus-circle"></i> Apply for Loan
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="<?= site_url('/customer/kyc') ?>" class="btn btn-outline-primary btn-lg w-100">
                                <i class="bi bi-card-checklist"></i> Complete KYC
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>