
<?php $this->extend('layout/customer'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="<?= site_url('/customer/loans') ?>" class="btn btn-outline-secondary btn-sm mb-3">
                <i class="bi bi-arrow-left"></i> Back to My Loans
            </a>

            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="mb-1"><i class="bi bi-file-earmark-text"></i> Loan Application #<?= esc($loan['id']) ?></h2>
                    <p class="text-muted mb-0">Submitted on <?= date('M d, Y H:i', strtotime($loan['created_at'])) ?></p>
                </div>
                <span class="badge" style="font-size: 1rem; padding: 0.5rem 1rem; background: <?= 
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
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Loan Summary -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Loan Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Loan Amount</small>
                            <h4 style="color: #667eea;">₱<?= number_format($loan['amount'], 2) ?></h4>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Final Amount Due</small>
                            <h4 style="color: #28a745;">₱<?= $loan['final_amount'] ? number_format($loan['final_amount'], 2) : 'TBD' ?></h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Purpose</small>
                            <p class="mb-3"><?= esc($loan['purpose']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Loan Term</small>
                            <p class="mb-3"><?= esc($loan['term_months']) ?> months</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loan Terms -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0"><i class="bi bi-hourglass-split"></i> Loan Terms</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Payment Method</small>
                            <p><?= ucfirst(esc($loan['payment_method'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Disbursement Method</small>
                            <p><?= ucwords(str_replace('_', ' ', esc($loan['disbursement_method']))) ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Account Number</small>
                            <p><?= esc($loan['bank_gcash_account']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Interest Rate</small>
                            <p><?= $loan['interest_rate'] ?? 'Pending' ?><?= $loan['interest_rate'] ? '%' : '' ?></p>
                        </div>
                    </div>

                    <?php if ($loan['penalty_rate']): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Penalty Rate</small>
                                <p><?= $loan['penalty_rate'] ?>%</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Admin Notes -->
            <?php if ($loan['admin_notes']): ?>
                <div class="card border-0 shadow-lg mb-4 border-start border-info" style="border-width: 4px !important;">
                    <div class="card-header bg-light border-bottom">
                        <h6 class="mb-0"><i class="bi bi-chat-left-text"></i> Admin Notes</h6>
                    </div>
                    <div class="card-body">
                        <p><?= nl2br(esc($loan['admin_notes'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0"><i class="bi bi-clock-history"></i> Application Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item mb-3 pb-3 border-bottom">
                            <div class="d-flex">
                                <div class="timeline-marker bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;"><i class="bi bi-send"></i></div>
                                <div class="ms-3">
                                    <p class="mb-0"><strong>Submitted</strong></p>
                                    <small class="text-muted"><?= date('M d, Y H:i', strtotime($loan['created_at'])) ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item mb-3 pb-3 border-bottom">
                            <div class="d-flex">
                                <div class="timeline-marker bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;"><i class="bi bi-hourglass-split"></i></div>
                                <div class="ms-3">
                                    <p class="mb-0"><strong>Under Review</strong></p>
                                    <small class="text-muted">Waiting for approval</small>
                                </div>
                            </div>
                        </div>
                        <?php if (in_array(strtolower($loan['status']), ['approved', 'released'])): ?>
                            <div class="timeline-item">
                                <div class="d-flex">
                                    <div class="timeline-marker bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;"><i class="bi bi-check"></i></div>
                                    <div class="ms-3">
                                        <p class="mb-0"><strong>Approved</strong></p>
                                        <small class="text-muted">Loan has been approved</small>
                                    </div>
                                </div>
                            </div>
                        <?php elseif (strtolower($loan['status']) === 'rejected'): ?>
                            <div class="timeline-item">
                                <div class="d-flex">
                                    <div class="timeline-marker bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;"><i class="bi bi-x"></i></div>
                                    <div class="ms-3">
                                        <p class="mb-0"><strong>Rejected</strong></p>
                                        <small class="text-muted">Loan application was rejected</small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Need Help Card -->
            <div class="card border-0 shadow">
                <div class="card-body text-center">
                    <i class="bi bi-question-circle" style="font-size: 2rem; color: #667eea;"></i>
                    <h6 class="mt-3 mb-2">Need Help?</h6>
                    <p class="text-muted small mb-3">Contact our support team for assistance with your loan application.</p>
                    <a href="<?= site_url('/contact') ?>" class="btn btn-outline-primary btn-sm w-100">Contact Support</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>