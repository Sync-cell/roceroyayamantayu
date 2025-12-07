
<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="<?= site_url('/admin/loans') ?>" class="btn btn-outline-secondary btn-sm mb-3">
                <i class="bi bi-arrow-left"></i> Back to Loans
            </a>
            <h2><i class="bi bi-file-earmark-check"></i> Review Loan Application #<?= esc($loan['id']) ?></h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Customer Information -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0"><i class="bi bi-person"></i> Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Full Name</small>
                            <p class="mb-3"><strong><?= esc($loan['fullname']) ?></strong></p>

                            <small class="text-muted d-block mb-1">Email</small>
                            <p class="mb-3"><?= esc($loan['email']) ?></p>

                            <small class="text-muted d-block mb-1">Contact Number</small>
                            <p><?= esc($loan['contact_number']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Loan Amount</small>
                            <p class="mb-3"><h4 style="color: #667eea;">₱<?= number_format($loan['amount'], 2) ?></h4></p>

                            <small class="text-muted d-block mb-1">Loan Term</small>
                            <p class="mb-3"><?= $loan['term_months'] ?> months</p>

                            <small class="text-muted d-block mb-1">Applied On</small>
                            <p><?= date('M d, Y H:i', strtotime($loan['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loan Details -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Loan Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Purpose</small>
                        <p><?= esc($loan['purpose']) ?></p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block mb-1">Payment Method</small>
                            <p><?= ucfirst(esc($loan['payment_method'])) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block mb-1">Disbursement Method</small>
                            <p><?= ucwords(str_replace('_', ' ', esc($loan['disbursement_method']))) ?></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Account Number</small>
                            <p><?= esc($loan['bank_gcash_account']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Processing Form -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0"><i class="bi bi-pencil"></i> Process Application</h6>
                </div>
                <div class="card-body">
                    <?= form_open("/admin/loan/process/{$loan['id']}") ?>
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Interest Rate (%)</label>
                        <input type="number" name="interest_rate" class="form-control form-control-lg" step="0.01" placeholder="5.00" value="<?= old('interest_rate', $loan['interest_rate'] ?? '5.00') ?>">
                        <small class="text-muted d-block mt-2">Enter the interest rate percentage</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Penalty Rate (%)</label>
                        <input type="number" name="penalty_rate" class="form-control form-control-lg" step="0.01" placeholder="2.00" value="<?= old('penalty_rate', $loan['penalty_rate'] ?? '2.00') ?>">
                        <small class="text-muted d-block mt-2">Enter the penalty rate for late payments</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Admin Notes</label>
                        <textarea name="admin_notes" class="form-control" rows="4" placeholder="Add any notes or comments..."></textarea>
                    </div>

                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                        <button type="submit" name="action" value="reject" class="btn btn-danger btn-lg">
                            <i class="bi bi-x-circle"></i> Reject
                        </button>
                        <button type="submit" name="action" value="approve" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle"></i> Approve
                        </button>
                    </div>

                    <?= form_close() ?>
                </div>
            </div>

            <!-- Release Loan (if approved) -->
            <?php if (strtolower($loan['status']) === 'approved'): ?>
                <div class="card border-0 shadow-lg border-start border-info" style="border-width: 4px !important;">
                    <div class="card-header bg-light border-bottom">
                        <h6 class="mb-0"><i class="bi bi-cash-flow"></i> Release Funds</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Disburse the approved loan funds to the customer's account.</p>

                        <?= form_open("/admin/loan/release/{$loan['id']}") ?>
                        <?= csrf_field() ?>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-info btn-lg fw-bold">
                                <i class="bi bi-cash-flow"></i> Release Funds Now
                            </button>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card border-0 shadow mb-4">
                <div class="card-body text-center">
                    <span class="badge" style="font-size: 1.1rem; padding: 0.6rem 1.2rem; background: <?= 
                        strtolower($loan['status']) === 'approved' ? '#28a745' : 
                        (strtolower($loan['status']) === 'rejected' ? '#dc3545' : 
                        (strtolower($loan['status']) === 'released' ? '#0dcaf0' : '#ffc107'))
                    ?>;">
                        <i class="bi <?= 
                            strtolower($loan['status']) === 'approved' ? 'bi-check-circle' : 
                            (strtolower($loan['status']) === 'rejected' ? 'bi-x-circle' : 
                            (strtolower($loan['status']) === 'released' ? 'bi-cash-flow' : 'bi-clock'))
                        ?>"></i> 
                        <?= ucfirst(esc($loan['status'])) ?>
                    </span>
                </div>
            </div>

            <!-- Document Checklist -->
            <div class="card border-0 shadow">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0"><i class="bi bi-checklist"></i> Checklist</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Application submitted</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Customer verified</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Documents reviewed</li>
                        <li><i class="bi bi-circle text-warning"></i> Awaiting approval decision</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>