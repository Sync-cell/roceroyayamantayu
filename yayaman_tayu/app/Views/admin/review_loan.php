
<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<?php
    // Resolve file URLs
    $idFile    = $kyc['id_photo'] ?? $kyc['document_path'] ?? null;
    $proofFile = $kyc['proof_of_income'] ?? null;

    $publicIdPath    = $idFile    ? FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'kyc' . DIRECTORY_SEPARATOR . $idFile : null;
    $writableIdPath  = $idFile    ? WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'kyc' . DIRECTORY_SEPARATOR . $idFile : null;

    $publicProofPath   = $proofFile ? FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'kyc' . DIRECTORY_SEPARATOR . $proofFile : null;
    $writableProofPath = $proofFile ? WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'kyc' . DIRECTORY_SEPARATOR . $proofFile : null;

    $idUrl = null;
    if ($idFile) {
        if ($publicIdPath && is_file($publicIdPath)) {
            $idUrl = base_url('uploads/kyc/' . $idFile);
        } elseif ($writableIdPath && is_file($writableIdPath)) {
            $idUrl = site_url('files/kyc/' . $idFile);
        }
    }

    $proofUrl = null;
    if ($proofFile) {
        if ($publicProofPath && is_file($publicProofPath)) {
            $proofUrl = base_url('uploads/kyc/' . $proofFile);
        } elseif ($writableProofPath && is_file($writableProofPath)) {
            $proofUrl = site_url('files/kyc/' . $proofFile);
        }
    }

    $kycStatus = strtolower($kyc['status'] ?? '');
    $canApprove = $kycStatus === 'approved';
    $customerId = $loan['customer_id'] ?? ($kyc['customer_id'] ?? ($customer['id'] ?? 0));
?>

<style>
/* Compact professional layout */
.card-compact { border-radius:10px; box-shadow:0 8px 30px rgba(2,6,23,0.06); overflow:hidden; }
.header { background:linear-gradient(135deg,#00B4DB,#0083B0); color:#fff; padding:14px; display:flex; align-items:center; gap:12px; }
.header h5 { margin:0; font-weight:700; }
.small-muted { color:#6b7280; font-size:13px; }
.panel { background:#fff; border-radius:8px; padding:14px; box-shadow:0 6px 18px rgba(2,6,23,0.03); }
.kv-label { color:#546e7a; font-weight:600; width:120px; display:inline-block; }
.thumb-small { width:120px; height:84px; object-fit:cover; border-radius:8px; border:1px solid #eef6fb; box-shadow:0 6px 18px rgba(2,6,23,0.04); }
.thumb-inline { display:flex; gap:12px; align-items:center; }
.badge { padding:6px 10px; border-radius:16px; font-weight:700; font-size:13px; }
.badge-pending { background:#fff8e1; color:#8a6d00; }
.badge-approved { background:#e6ffef; color:#117a48; }
.badge-rejected { background:#ffecec; color:#7a1f1f; }
.actions { display:flex; gap:8px; justify-content:flex-end; }
.alert-kyc { border-left:4px solid #ffc107; background:#fff8e6; padding:12px; border-radius:6px; margin-bottom:12px; }
.btn-warning-outline { border:1px solid #ffc107; color:#856404; background:transparent; }
[disabled].btn { opacity:0.6; pointer-events:none; }
@media (max-width:900px) {
    .thumb-small { width:100px; height:68px; }
    .kv-label { width:100px; }
}
</style>

<div class="container py-4">
    <div class="card card-compact">
        <div class="header">
            <div><i class="bi bi-file-earmark-text" style="font-size:20px;"></i></div>
            <div style="flex:1">
                <h5>Review Loan #<?= esc($loan['id'] ?? 'N/A') ?></h5>
                <div class="small-muted"><?= esc($loan['purpose'] ?? '-') ?> • <?= esc($loan['term'] ?? '-') ?> mo</div>
            </div>
            <div>
                <?php if ($kycStatus === 'pending'): ?>
                    <span class="badge badge-pending">KYC Pending</span>
                <?php elseif ($kycStatus === 'approved'): ?>
                    <span class="badge badge-approved">Verified</span>
                <?php elseif ($kycStatus === 'rejected'): ?>
                    <span class="badge badge-rejected">Rejected</span>
                <?php else: ?>
                    <span class="small-muted">No KYC</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body p-4">
            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-lg-7">
                    <div class="panel mb-3">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                            <div>
                                <div style="font-weight:800;font-size:16px;"><?= esc($loan['borrower_name'] ?? ($customer['fullname'] ?? 'N/A')) ?></div>
                                <div class="small-muted"><?= esc($loan['borrower_email'] ?? ($customer['email'] ?? '')) ?></div>
                            </div>
                            <div class="small-muted" style="text-align:right;">
                                <div>Customer ID</div>
                                <div style="font-weight:700;"><?= esc($customerId ?: '-') ?></div>
                            </div>
                        </div>

                        <hr/>

                        <div>
                            <div><span class="kv-label">Amount</span> <strong>₱<?= number_format($loan['amount'] ?? 0, 2) ?></strong></div>
                            <div><span class="kv-label">Term</span> <?= esc($loan['term'] ?? '-') ?> months</div>
                            <div><span class="kv-label">Payment</span> <?= esc($loan['payment_method'] ?? '-') ?></div>
                            <div><span class="kv-label">Disbursement</span> <?= esc($loan['disbursement_method'] ?? '-') ?></div>
                            <div><span class="kv-label">Status</span> <?= esc($loan['status'] ?? '-') ?></div>
                        </div>
                    </div>

                    <div class="panel">
                        <h6 style="margin-bottom:12px;">Review & Action</h6>

                        <?php if (empty($kyc) || $kycStatus === ''): ?>
                            <div class="alert-kyc">
                                <strong>Verification required</strong>
                                <div class="small-muted">Borrower has not completed KYC. You must request the borrower to complete verification before approving the loan.</div>
                                <div style="margin-top:8px;">
                                    <a href="<?= base_url('/admin/kyc/request/' . (int)$customerId) ?>" class="btn btn-warning-outline btn-sm">Send KYC reminder</a>
                                    <a href="<?= base_url('/customer/kyc') ?>" target="_blank" class="btn btn-outline-secondary btn-sm">Open customer KYC page</a>
                                </div>
                            </div>
                        <?php elseif ($kycStatus === 'rejected'): ?>
                            <div class="alert alert-danger">
                                KYC was rejected. Borrower must re-submit documents before approving.
                                <?php if (!empty($kyc['notes'])): ?>
                                    <div class="small-muted mt-2">Admin note: <?= esc($kyc['notes']) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('/admin/loan/process/' . ($loan['id'] ?? 0)) ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label">Review Notes</label>
                                <textarea name="notes" class="form-control" rows="4" placeholder="Add notes for borrower"><?= esc(old('notes')) ?></textarea>
                            </div>

                            <div class="actions">
                                <a href="<?= base_url('/admin/loans') ?>" class="btn btn-secondary">Back</a>

                                <button type="submit" name="action" value="reject" class="btn btn-outline-danger">Reject</button>

                                <button
                                    type="submit"
                                    name="action"
                                    value="approve"
                                    class="btn btn-success"
                                    <?= $canApprove ? '' : 'disabled' ?>
                                    data-bs-toggle="tooltip"
                                    data-bs-title="<?= $canApprove ? '' : 'Cannot approve: borrower not verified' ?>">
                                    Approve
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="panel mb-3">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                            <div>
                                <strong>ID Photo</strong>
                                <div class="small-muted">Uploaded identification</div>
                            </div>
                            <?php if ($idUrl): ?>
                                <a href="<?= esc($idUrl) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Open</a>
                            <?php endif; ?>
                        </div>

                        <div class="thumb-inline">
                            <?php if ($idUrl): ?>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#previewModal" data-src="<?= esc($idUrl) ?>" data-title="ID Photo">
                                    <img src="<?= esc($idUrl) ?>" alt="ID Photo" class="thumb-small">
                                </a>
                            <?php else: ?>
                                <img src="<?= base_url('assets/img/placeholder-id.png') ?>" alt="No ID" class="thumb-small">
                            <?php endif; ?>

                            <div style="flex:1;">
                                <div style="font-weight:700;"><?= esc($kyc['id_type'] ?? '-') ?></div>
                                <div class="small-muted"><?= esc($kyc['id_number'] ?? '-') ?></div>
                                <?php if (!empty($kyc['notes'])): ?>
                                    <div class="small-muted mt-2">Admin note: <?= esc($kyc['notes']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="panel">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                            <div>
                                <strong>Proof of Income</strong>
                                <div class="small-muted">Payslip / Bank proof</div>
                            </div>
                            <?php if ($proofUrl): ?>
                                <a href="<?= esc($proofUrl) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Open</a>
                            <?php endif; ?>
                        </div>

                        <?php if ($proofUrl):
                            $ext = strtolower(pathinfo(parse_url($proofUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg','jpeg','png','gif'])): ?>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#previewModal" data-src="<?= esc($proofUrl) ?>" data-title="Proof of Income">
                                    <img src="<?= esc($proofUrl) ?>" alt="Proof" class="thumb-small">
                                </a>
                            <?php else: ?>
                                <div class="small-muted">Document: <?= esc(strtoupper($ext ?: 'FILE')) ?></div>
                            <?php endif;
                        else: ?>
                            <div class="small-muted">No proof of income uploaded.</div>
                        <?php endif; ?>

                        <div style="margin-top:12px;">
                            <div><span class="kv-label">Employment</span> <?= esc($kyc['employment_status'] ?? '-') ?></div>
                            <div><span class="kv-label">Income</span> ₱<?= esc(number_format($kyc['monthly_income'] ?? 0, 2)) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="previewTitle"></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <img id="previewImage" src="" alt="" style="max-width:100%;height:auto;border-radius:8px;">
          </div>
        </div>
      </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var previewModal = document.getElementById('previewModal');
    if (previewModal) {
        previewModal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            var src = trigger.getAttribute('data-src');
            var title = trigger.getAttribute('data-title') || '';
            document.getElementById('previewImage').src = src;
            document.getElementById('previewTitle').textContent = title;
        });
        previewModal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('previewImage').src = '';
        });
    }

    // Enable bootstrap tooltips if available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
    }
});
</script>

<?php $this->endSection(); ?>