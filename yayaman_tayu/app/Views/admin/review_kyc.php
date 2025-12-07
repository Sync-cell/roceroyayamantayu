
<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>

<?php
    // Build file URLs
    $idFile    = $kyc['id_photo'] ?? $kyc['document_path'] ?? null;
    $proofFile = $kyc['proof_of_income'] ?? $kyc['document_path_proof'] ?? null;

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

    $status = $kyc['status'] ?? 'Pending';
?>

<style>
    .kyc-header {
        background: linear-gradient(135deg, #00B4DB 0%, #0083B0 100%);
        color: #fff;
        padding: 18px;
        border-radius: 8px 8px 0 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .kyc-header .title {
        font-size: 18px;
        font-weight: 700;
    }

    .kyc-badge {
        font-weight: 700;
        font-size: 13px;
        padding: 6px 10px;
        border-radius: 20px;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-approved {
        background: #d4edda;
        color: #155724;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .card-kyc {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
    }

    .kyc-grid {
        display: flex;
        gap: 18px;
        flex-wrap: wrap;
    }

    .kyc-left {
        flex: 1 1 420px;
        min-width: 320px;
    }

    .kyc-right {
        width: 360px;
        flex-shrink: 0;
    }

    .file-thumb {
        width: 100%;
        border-radius: 8px;
        border: 1px solid #eef2f5;
        box-shadow: 0 6px 18px rgba(2, 6, 23, 0.06);
        object-fit: cover;
        max-height: 300px;
    }

    .btn-gcash {
        background: linear-gradient(90deg, #00b4db, #0083b0);
        border: none;
        color: #fff;
        padding: 10px 14px;
        border-radius: 8px;
        font-weight: 700;
        box-shadow: 0 6px 18px rgba(2, 6, 23, 0.08);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-gcash:hover {
        box-shadow: 0 8px 24px rgba(2, 6, 23, 0.15);
        transform: translateY(-2px);
    }

    .btn-outline-neutral {
        border: 1px solid #e6eef6;
        background: #fff;
        color: #0b5f7a;
        padding: 10px 14px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-outline-neutral:hover {
        background: #f5f8fb;
        border-color: #0b5f7a;
    }

    .small-muted {
        font-size: 13px;
        color: #6b7280;
    }

    .table-compact td,
    .table-compact th {
        padding: 0.45rem 0.6rem;
        vertical-align: middle;
    }

    @media (max-width: 768px) {
        .kyc-right {
            width: 100%;
        }

        .kyc-grid {
            flex-direction: column;
        }
    }
</style>

<div class="container py-4">
    <div class="card card-kyc">
        <div class="kyc-header">
            <div class="title"><i class="bi bi-person-badge-fill"></i> Review KYC #<?= esc($kyc['id'] ?? 'N/A') ?></div>
            <div style="margin-left: auto;">
                <?php if (strtolower($status) === 'pending'): ?>
                    <span class="kyc-badge status-pending">Pending</span>
                <?php elseif (strtolower($status) === 'approved'): ?>
                    <span class="kyc-badge status-approved">✓ Verified</span>
                <?php else: ?>
                    <span class="kyc-badge status-rejected">✗ Rejected</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- Alert Messages -->
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

            <div class="kyc-grid">
                <!-- Left Column: Details -->
                <div class="kyc-left">
                    <!-- Customer Header -->
                    <div class="mb-4">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 64px; height: 64px; border-radius: 12px; background: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 18px rgba(2, 6, 23, 0.06);">
                                <i class="bi bi-person-circle" style="font-size: 28px; color: #0083B0;"></i>
                            </div>
                            <div>
                                <div style="font-weight: 800; font-size: 16px;"><?= esc($customer['fullname'] ?? $customer['name'] ?? 'N/A') ?></div>
                                <div class="small-muted"><?= esc($customer['email'] ?? '') ?></div>
                            </div>
                            <div style="margin-left: auto; text-align: right;">
                                <div class="small-muted">Customer ID</div>
                                <div style="font-weight: 700;">#<?= esc($kyc['customer_id'] ?? '-') ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- ID Details Table -->
                    <div class="mb-4">
                        <h6 style="margin-bottom: 12px; font-weight: 700;">ID Details</h6>
                        <table class="table table-borderless table-compact">
                            <tr>
                                <th style="width: 160px;">ID Type</th>
                                <td><?= esc($kyc['id_type'] ?? $kyc['document_type'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>ID Number</th>
                                <td><?= esc($kyc['id_number'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>Employment</th>
                                <td><?= esc($kyc['employment_status'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>Monthly Income</th>
                                <td><?= esc($kyc['monthly_income'] ?? '-') ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Review Form -->
                    <div class="mb-3">
                        <h6 style="margin-bottom: 12px; font-weight: 700;">Review Notes</h6>
                        <form action="<?= site_url('/admin/kyc/process/' . ($kyc['id'] ?? 0)) ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <textarea name="notes" class="form-control" rows="4" placeholder="Add review notes (optional)..."><?= esc(old('notes')) ?></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <?php if ($status === 'Pending'): ?>
                                    <button type="submit" name="action" value="approve" class="btn btn-gcash">
                                        <i class="bi bi-check-circle"></i> Approve & Verify
                                    </button>
                                    <button type="submit" name="action" value="reject" class="btn btn-outline-neutral" onclick="return confirm('Are you sure you want to reject this KYC?');">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                <?php else: ?>
                                    <div class="alert alert-info w-100 mb-0">
                                        <i class="bi bi-info-circle"></i> This KYC has already been reviewed.
                                    </div>
                                <?php endif; ?>
                                <a href="<?= site_url('/admin/kyc') ?>" class="btn btn-outline-neutral">Back</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Documents -->
                <div class="kyc-right">
                    <!-- ID Photo -->
                    <div class="mb-4">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <div>
                                <strong>ID Photo</strong>
                                <div class="small-muted">Uploaded identification</div>
                            </div>
                            <?php if ($idUrl): ?>
                                <a href="<?= esc($idUrl) ?>" target="_blank" class="btn btn-sm btn-outline-neutral">
                                    <i class="bi bi-box-arrow-up-right"></i> Open
                                </a>
                            <?php endif; ?>
                        </div>

                        <div style="margin-top: 10px;">
                            <?php if ($idUrl): ?>
                                <img src="<?= esc($idUrl) ?>" class="file-thumb" alt="ID Photo" onerror="this.src='<?= base_url('assets/img/placeholder.png') ?>'">
                            <?php else: ?>
                                <div style="padding: 40px; text-align: center; background: #f5f5f5; border-radius: 8px;">
                                    <i class="bi bi-image" style="font-size: 32px; color: #ccc;"></i>
                                    <div class="small-muted mt-2">No ID photo available</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Proof of Income -->
                    <div class="mb-3">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <div>
                                <strong>Proof of Income</strong>
                                <div class="small-muted">Payslip or bank proof</div>
                            </div>
                            <?php if ($proofUrl): ?>
                                <a href="<?= esc($proofUrl) ?>" target="_blank" class="btn btn-sm btn-outline-neutral">
                                    <i class="bi bi-box-arrow-up-right"></i> Open
                                </a>
                            <?php endif; ?>
                        </div>

                        <div style="margin-top: 10px;">
                            <?php if ($proofUrl): ?>
                                <?php $proofExt = strtolower(pathinfo(parse_url($proofUrl, PHP_URL_PATH), PATHINFO_EXTENSION)); ?>
                                <?php if (in_array($proofExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                    <img src="<?= esc($proofUrl) ?>" class="file-thumb" alt="Proof of income" onerror="this.src='<?= base_url('assets/img/placeholder.png') ?>'">
                                <?php else: ?>
                                    <div style="padding: 18px; border-radius: 8px; border: 1px solid #eef2f5; background: #fafcff; text-align: center;">
                                        <i class="bi bi-file-earmark" style="font-size: 28px; color: #0083B0;"></i>
                                        <div style="font-weight: 700; margin-top: 8px;"><?= strtoupper(esc($proofExt ?: 'FILE')) ?></div>
                                        <div class="small-muted">Click open to view document</div>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div style="padding: 40px; text-align: center; background: #f5f5f5; border-radius: 8px;">
                                    <i class="bi bi-file-text" style="font-size: 32px; color: #ccc;"></i>
                                    <div class="small-muted mt-2">No proof of income uploaded</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>