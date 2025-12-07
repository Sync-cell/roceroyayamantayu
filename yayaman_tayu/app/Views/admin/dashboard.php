

<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= session()->getFlashdata('success') ?>',
                confirmButtonColor: '#00B4DB'
            });
        </script>
    <?php endif; ?>

    <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Admin Dashboard</h2>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #00B4DB;">
                <div class="card-body">
                    <h6 class="text-muted">Pending Loans</h6>
                    <h3 style="color: #00B4DB;"><?= $pendingLoans ?? 0 ?></h3>
                    <small class="text-muted">Awaiting review</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745;">
                <div class="card-body">
                    <h6 class="text-muted">Approved Loans</h6>
                    <h3 style="color: #28a745;"><?= $approvedLoans ?? 0 ?></h3>
                    <small class="text-muted">Ready to release</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #ffc107;">
                <div class="card-body">
                    <h6 class="text-muted">Released Loans</h6>
                    <h3 style="color: #ffc107;"><?= $releasedLoans ?? 0 ?></h3>
                    <small class="text-muted">Disbursed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #FF6B6B;">
                <div class="card-body">
                    <h6 class="text-muted">Pending KYC</h6>
                    <h3 style="color: #FF6B6B;"><?= $pendingKyc ?? 0 ?></h3>
                    <small class="text-muted">Waiting verification</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Customers</h6>
                    <h2 style="color: #0083B0;"><?= $totalCustomers ?? 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Loans</h6>
                    <h2 style="color: #0083B0;"><?= $totalLoans ?? 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Approved Loan Amount</h6>
                    <h2 style="color: #0083B0;">₱<?= number_format($approvedLoanAmount ?? 0, 2) ?></h2>
                    <small class="text-muted">Only approved loans</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Loan Applications -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header" style="background: #F5F5F5; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                    <h6 class="mb-0" style="color: #0083B0;"><i class="bi bi-file-earmark"></i> Recent Loan Applications</h6>

                    <div style="display:flex; gap:8px; align-items:center;">
                        <!-- Status filter form (GET) -->
                        <form method="get" action="<?= current_url() ?>" class="d-flex align-items-center">
                            <?php $s = $loanStatusFilter ?? ''; ?>
                            <select name="loan_status" class="form-select form-select-sm" style="min-width:150px; margin-right:8px;">
                                <option value="all" <?= ($s === 'all' || $s === '') ? 'selected' : '' ?>>All</option>
                                <option value="Pending" <?= $s === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Approved" <?= $s === 'Approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="Released" <?= $s === 'Released' ? 'selected' : '' ?>>Released</option>
                                <option value="Rejected" <?= $s === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                        </form>

                        <?php
                            // export link includes loan_status query if present
                            $exportUrl = site_url('/admin/reports/loans/export') . (!empty($loanStatusFilter) ? ('?status=' . urlencode($loanStatusFilter)) : '');
                        ?>
                        <a href="<?= $exportUrl ?>" class="btn btn-sm btn-outline-primary" title="Download CSV">
                            <i class="bi bi-download"></i> Export CSV
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <?php if (!empty($recentLoans) && is_array($recentLoans)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background: #F5F5F5;">
                                    <tr>
                                        <th>ID</th>
                                        <th>Amount</th>
                                        <th>Purpose</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentLoans as $loan): ?>
                                        <tr>
                                            <td>#<?= esc($loan['id'] ?? '-') ?></td>
                                            <td><strong>₱<?= number_format($loan['amount'] ?? 0, 2) ?></strong></td>
                                            <td><?= esc(substr($loan['purpose'] ?? '-', 0, 25)) ?></td>
                                            <td>
                                                <?php $status = $loan['status'] ?? '-'; ?>
                                                <span class="badge" style="background: <?= $status === 'Approved' ? '#28a745' : ($status === 'Rejected' ? '#dc3545' : '#ffc107') ?>;">
                                                    <?= esc($status) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($status === 'Pending'): ?>
                                                    <a href="<?= site_url('/admin/loan/review/' . ($loan['id'] ?? '')) ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> Review
                                                    </a>
                                                <?php elseif ($status === 'Approved'): ?>
                                                    <form method="post" action="<?= site_url('/admin/loan/release/' . ($loan['id'] ?? '')) ?>" style="display:inline;">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-success" onclick="confirmRelease(event)">
                                                            <i class="bi bi-cash-coin"></i> Release
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted small">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if (!empty($loanPager)): ?>
                            <nav aria-label="Page navigation" class="mt-3">
                                <?= $loanPager->links('bootstrap') ?>
                            </nav>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No loan applications</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- KYC List -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header" style="background: #F5F5F5; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                    <h6 class="mb-0" style="color: #FF6B6B;"><i class="bi bi-card-checklist"></i> KYC Verification</h6>

                    <div style="display:flex; gap:8px; align-items:center;">
                        <!-- KYC Status filter form (GET) -->
                        <form method="get" action="<?= current_url() ?>" class="d-flex align-items-center">
                            <?php $ks = $kycStatusFilter ?? ''; ?>
                            <select name="kyc_status" class="form-select form-select-sm" style="min-width:120px; margin-right:6px;">
                                <option value="all" <?= ($ks === 'all' || $ks === '') ? 'selected' : '' ?>>All</option>
                                <option value="Pending" <?= $ks === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Approved" <?= $ks === 'Approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="Rejected" <?= $ks === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                        </form>

                        <?php
                            // export link includes kyc_status query if present
                            $kycExportUrl = site_url('/admin/reports/kyc/export') . (!empty($kycStatusFilter) ? ('?status=' . urlencode($kycStatusFilter)) : '');
                        ?>
                        <a href="<?= $kycExportUrl ?>" class="btn btn-sm btn-outline-danger" title="Download CSV">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recentKyc) && is_array($recentKyc)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead style="background: #F5F5F5;">
                                    <tr>
                                        <th>Customer</th>
                                        <th>ID Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentKyc as $kyc): ?>
                                        <tr>
                                            <td><small><strong>#<?= esc($kyc['customer_id'] ?? '-') ?></strong></small></td>
                                            <td><small><?= esc($kyc['id_type'] ?? '-') ?></small></td>
                                            <td>
                                                <?php $kycStatus = $kyc['status'] ?? '-'; ?>
                                                <span class="badge" style="background: <?= $kycStatus === 'Approved' ? '#28a745' : ($kycStatus === 'Rejected' ? '#dc3545' : '#ffc107') ?>;">
                                                    <?= esc($kycStatus) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= site_url('/admin/kyc/review/' . ($kyc['id'] ?? '')) ?>" class="btn btn-xs btn-outline-danger">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if (!empty($kycPager)): ?>
                            <nav aria-label="Page navigation" class="mt-3">
                                <?= $kycPager->links('bootstrap') ?>
                            </nav>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle" style="font-size: 2rem; color: #28a745;"></i>
                            <p class="text-muted mt-2">No KYC records found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
function confirmRelease(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Release Loan?',
        text: 'Are you sure you want to release this loan to the customer?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#999',
        confirmButtonText: 'Yes, Release',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            let form = e.target;
            while (form && form.nodeName !== 'FORM') form = form.parentElement;
            if (form) form.submit();
        }
    });
}
</script>
<?php $this->endSection(); ?>