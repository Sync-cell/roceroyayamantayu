
<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="bi bi-graph-up"></i> Reports & Analytics</h2>
        </div>
    </div>

    <!-- Key Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-lg h-100" style="border-left: 5px solid #00B4DB;">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark-text" style="font-size: 2rem; color: #00B4DB;"></i>
                    <h6 class="text-muted mt-3 text-uppercase fw-bold" style="font-size: 0.85rem;">Total Loans</h6>
                    <h2 class="mb-0" style="color: #00B4DB;"><?= $totalLoans ?? 0 ?></h2>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-lg h-100" style="border-left: 5px solid #28a745;">
                <div class="card-body text-center">
                    <i class="bi bi-cash-flow" style="font-size: 2rem; color: #28a745;"></i>
                    <h6 class="text-muted mt-3 text-uppercase fw-bold" style="font-size: 0.85rem;">Total Amount</h6>
                    <h2 class="mb-0" style="color: #28a745;">₱<?= number_format($totalAmount ?? 0, 2) ?></h2>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-lg h-100" style="border-left: 5px solid #0dcaf0;">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle" style="font-size: 2rem; color: #0dcaf0;"></i>
                    <h6 class="text-muted mt-3 text-uppercase fw-bold" style="font-size: 0.85rem;">Released Amount</h6>
                    <h2 class="mb-0" style="color: #0dcaf0;">₱<?= number_format($releasedAmount ?? 0, 2) ?></h2>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-lg h-100" style="border-left: 5px solid #FF6B6B;">
                <div class="card-body text-center">
                    <i class="bi bi-people" style="font-size: 2rem; color: #FF6B6B;"></i>
                    <h6 class="text-muted mt-3 text-uppercase fw-bold" style="font-size: 0.85rem;">Total Customers</h6>
                    <h2 class="mb-0" style="color: #FF6B6B;"><?= $totalCustomers ?? 0 ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h6 class="mb-0"><i class="bi bi-funnel"></i> Filter Reports</h6>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= current_url() ?>" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Loan Status</label>
                            <select name="loan_status" class="form-select" onchange="this.form.submit()">
                                <option value="all" <?= ($loanStatus ?? 'all') === 'all' ? 'selected' : '' ?>>All Loans</option>
                                <option value="Pending" <?= ($loanStatus ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Approved" <?= ($loanStatus ?? '') === 'Approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="Released" <?= ($loanStatus ?? '') === 'Released' ? 'selected' : '' ?>>Released</option>
                                <option value="Rejected" <?= ($loanStatus ?? '') === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">KYC Status</label>
                            <select name="kyc_status" class="form-select" onchange="this.form.submit()">
                                <option value="all" <?= ($kycStatus ?? 'all') === 'all' ? 'selected' : '' ?>>All KYC</option>
                                <option value="Pending" <?= ($kycStatus ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Approved" <?= ($kycStatus ?? '') === 'Approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="Rejected" <?= ($kycStatus ?? '') === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <a href="<?= current_url() ?>" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-clockwise"></i> Reset Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-lg h-100" style="cursor: pointer; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="card-body text-center py-5">
                    <i class="bi bi-file-earmark-spreadsheet" style="font-size: 3rem; color: #dc3545;"></i>
                    <h6 class="text-muted mt-3 text-uppercase fw-bold">Loan Report</h6>
                    <p class="text-muted small">Export all loan data</p>
                    <a href="<?= site_url('/admin/reports/loans/export') . '?loan_status=' . ($loanStatus ?? 'all') ?>" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-lg h-100" style="cursor: pointer; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="card-body text-center py-5">
                    <i class="bi bi-file-earmark-spreadsheet" style="font-size: 3rem; color: #28a745;"></i>
                    <h6 class="text-muted mt-3 text-uppercase fw-bold">KYC Report</h6>
                    <p class="text-muted small">Export KYC verification data</p>
                    <a href="<?= site_url('/admin/reports/kyc/export') . '?kyc_status=' . ($kycStatus ?? 'all') ?>" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-lg h-100" style="cursor: pointer; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="card-body text-center py-5">
                    <i class="bi bi-file-earmark-spreadsheet" style="font-size: 3rem; color: #0083B0;"></i>
                    <h6 class="text-muted mt-3 text-uppercase fw-bold">Customer Report</h6>
                    <p class="text-muted small">Export customer information</p>
                    <a href="<?= site_url('/admin/reports/customers/export') ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Section -->
    <div class="row mb-4">
        <!-- Loans Table -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #00B4DB 0%, #0083B0 100%); color: white;">
                    <h6 class="mb-0"><i class="bi bi-file-earmark"></i> Loan Records (<?= $totalLoans ?? 0 ?>)</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($loans)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 table-sm">
                                <thead style="background: #F5F5F5;">
                                    <tr>
                                        <th>ID</th>
                                        <th>Borrower</th>
                                        <th>Amount</th>
                                        <th>Term (Months)</th>
                                        <th>Interest Rate</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($loans, 0, 20) as $loan): ?>
                                        <tr>
                                            <td><strong>#<?= esc($loan['id'] ?? '-') ?></strong></td>
                                            <td>
                                                <div><?= esc($loan['customer_name'] ?? 'Unknown') ?></div>
                                                <small class="text-muted"><?= esc($loan['customer_email'] ?? '') ?></small>
                                            </td>
                                            <td><strong>₱<?= number_format($loan['amount'] ?? 0, 2) ?></strong></td>
                                            <td><?= esc($loan['term'] ?? '-') ?></td>
                                            <td><?= esc($loan['interest_rate'] ?? '-') ?>%</td>
                                            <td>
                                                <?php $status = $loan['status'] ?? '-'; ?>
                                                <span class="badge" style="background: <?= strtolower($status) === 'approved' ? '#28a745' : (strtolower($status) === 'released' ? '#0dcaf0' : (strtolower($status) === 'rejected' ? '#dc3545' : '#ffc107')); ?>">
                                                    <?= esc($status) ?>
                                                </span>
                                            </td>
                                            <td><small><?= esc(date('M d, Y', strtotime($loan['created_at'] ?? now()))) ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 2rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">No loan records found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- KYC Table -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #FF6B6B 0%, #FF4757 100%); color: white;">
                    <h6 class="mb-0"><i class="bi bi-card-checklist"></i> KYC Records (<?= count($kycs ?? []) ?>)</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($kycs)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 table-sm">
                                <thead style="background: #F5F5F5;">
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>ID Type</th>
                                        <th>ID Number</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($kycs, 0, 20) as $kyc): ?>
                                        <tr>
                                            <td><strong>#<?= esc($kyc['customer_id'] ?? '-') ?></strong></td>
                                            <td><?= esc($kyc['id_type'] ?? '-') ?></td>
                                            <td><?= esc($kyc['id_number'] ?? '-') ?></td>
                                            <td>
                                                <?php $kycSts = $kyc['status'] ?? '-'; ?>
                                                <span class="badge" style="background: <?= strtolower($kycSts) === 'approved' ? '#28a745' : (strtolower($kycSts) === 'rejected' ? '#dc3545' : '#ffc107'); ?>">
                                                    <?= esc($kycSts) ?>
                                                </span>
                                            </td>
                                            <td><small><?= esc(date('M d, Y', strtotime($kyc['created_at'] ?? now()))) ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 2rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">No KYC records found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="row mt-4">
        <div class="col-12">
            <a href="<?= site_url('/admin') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<style>
    .badge {
        font-weight: 600;
        padding: 0.5rem 0.75rem;
        border-radius: 20px;
        display: inline-block;
        font-size: 0.85rem;
    }
</style>

<?php $this->endSection(); ?>