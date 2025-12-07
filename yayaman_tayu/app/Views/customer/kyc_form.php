
<?php $this->extend('layout/customer'); ?>
<?php helper('form'); ?>

<?php $this->section('content'); ?>

<?php
    $session = session();
    $customerId = $session->get('customer_id');
    
    // Check if customer has existing KYC (pass $kyc from controller)
    $hasKyc = !empty($kyc);
    $kycStatus = $kyc['status'] ?? null;
    $isApproved = $hasKyc && strtolower($kycStatus) === 'approved';
    $isRejected = $hasKyc && strtolower($kycStatus) === 'rejected';
    $isPending = $hasKyc && strtolower($kycStatus) === 'pending';
?>

<style>
    .kyc-container {
        max-width: 700px;
        margin: 0 auto;
    }
    
    .kyc-status-card {
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .status-approved {
        background: #d4edda;
        border: 2px solid #28a745;
        color: #155724;
    }
    
    .status-pending {
        background: #fff3cd;
        border: 2px solid #ffc107;
        color: #856404;
    }
    
    .status-rejected {
        background: #f8d7da;
        border: 2px solid #dc3545;
        color: #721c24;
    }
    
    .status-icon {
        font-size: 48px;
        margin-bottom: 12px;
    }
    
    .status-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    
    .status-subtitle {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 12px;
    }
    
    .kyc-form-card {
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    
    .kyc-form-header {
        background: linear-gradient(135deg, #00B4DB 0%, #0083B0 100%);
        color: white;
        padding: 20px;
    }
    
    .kyc-form-header h5 {
        margin: 0;
        font-weight: 700;
        font-size: 18px;
    }
    
    .form-group label {
        font-weight: 600;
        color: #0083B0;
        margin-bottom: 8px;
    }
    
    .form-control {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px 12px;
    }
    
    .form-control:focus {
        border-color: #00B4DB;
        box-shadow: 0 0 0 0.2rem rgba(0, 180, 219, 0.25);
    }
    
    .btn-submit {
        background: linear-gradient(90deg, #00b4db, #0083b0);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 700;
        width: 100%;
    }
    
    .btn-submit:hover {
        opacity: 0.9;
    }
    
    .file-input-wrapper {
        position: relative;
    }
    
    .file-input-label {
        display: block;
        padding: 20px;
        border: 2px dashed #00B4DB;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        background: #f0f8ff;
        transition: all 0.3s ease;
    }
    
    .file-input-label:hover {
        background: #e8f4ff;
        border-color: #0083B0;
    }
    
    .file-input-label i {
        font-size: 32px;
        color: #00B4DB;
        display: block;
        margin-bottom: 8px;
    }
    
    .file-name {
        font-size: 13px;
        color: #6b7280;
        margin-top: 8px;
    }
    
    input[type="file"] {
        display: none;
    }
</style>

<div class="container py-5">
    <div class="kyc-container">
        
        <?php if ($isApproved): ?>
            <!-- Approved Status -->
            <div class="kyc-status-card status-approved">
                <div class="status-icon">✓</div>
                <div class="status-title">Account Verified</div>
                <div class="status-subtitle">Your KYC has been approved. Your account is now fully verified.</div>
                <a href="<?= base_url('/customer/dashboard') ?>" class="btn btn-sm btn-outline-success">Back to Dashboard</a>
            </div>
            
        <?php elseif ($isPending): ?>
            <!-- Pending Status -->
            <div class="kyc-status-card status-pending">
                <div class="status-icon">⏳</div>
                <div class="status-title">Verification in Progress</div>
                <div class="status-subtitle">Your KYC submission is being reviewed. Please wait for admin approval.</div>
                <a href="<?= base_url('/customer/dashboard') ?>" class="btn btn-sm btn-outline-warning">Back to Dashboard</a>
            </div>
            
        <?php elseif ($isRejected): ?>
            <!-- Rejected Status - Show form to resubmit -->
            <div class="kyc-status-card status-rejected">
                <div class="status-icon">✗</div>
                <div class="status-title">Verification Rejected</div>
                <div class="status-subtitle">Your KYC was rejected. Please review and resubmit.</div>
                <?php if (!empty($kyc['notes'])): ?>
                    <div style="text-align: left; background: rgba(0,0,0,0.1); padding: 12px; border-radius: 8px; margin-top: 12px;">
                        <strong>Admin Notes:</strong><br>
                        <?= esc($kyc['notes']) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Show form only if no KYC or rejected -->
        <?php if (!$hasKyc || $isRejected): ?>
        
        <div class="kyc-form-card">
            <div class="kyc-form-header">
                <h5><i class="bi bi-person-badge"></i> Complete Your KYC Verification</h5>
            </div>
            
            <div class="card-body p-4">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <div>• <?= esc($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?= base_url('/customer/kyc-submit') ?>" method="post" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>
                    
                    <!-- ID Type -->
                    <div class="form-group mb-3">
                        <label for="id_type">Type of ID <span style="color: red;">*</span></label>
                        <select name="id_type" id="id_type" class="form-control" required>
                            <option value="">-- Select ID Type --</option>
                            <option value="passport" <?= old('id_type') === 'passport' ? 'selected' : '' ?>>Passport</option>
                            <option value="drivers_license" <?= old('id_type') === 'drivers_license' ? 'selected' : '' ?>>Driver's License</option>
                            <option value="national_id" <?= old('id_type') === 'national_id' ? 'selected' : '' ?>>National ID</option>
                            <option value="nbi" <?= old('id_type') === 'nbi' ? 'selected' : '' ?>>NBI Clearance</option>
                        </select>
                    </div>
                    
                    <!-- ID Number -->
                    <div class="form-group mb-3">
                        <label for="id_number">ID Number <span style="color: red;">*</span></label>
                        <input type="text" name="id_number" id="id_number" class="form-control" value="<?= old('id_number') ?>" required>
                    </div>
                    
                    <!-- ID Photo Upload -->
                    <div class="form-group mb-3">
                        <label>ID Photo <span style="color: red;">*</span></label>
                        <div class="file-input-wrapper">
                            <label for="id_photo" class="file-input-label">
                                <i class="bi bi-cloud-upload"></i>
                                <strong>Click to upload or drag & drop</strong>
                                <div class="file-name">JPEG, PNG (Max 2 MB)</div>
                            </label>
                            <input type="file" name="id_photo" id="id_photo" accept="image/jpeg,image/png" required>
                        </div>
                    </div>
                    
                    <!-- Employment Status -->
                    <div class="form-group mb-3">
                        <label for="employment_status">Employment Status <span style="color: red;">*</span></label>
                        <select name="employment_status" id="employment_status" class="form-control" required>
                            <option value="">-- Select Status --</option>
                            <option value="employed" <?= old('employment_status') === 'employed' ? 'selected' : '' ?>>Employed</option>
                            <option value="self_employed" <?= old('employment_status') === 'self_employed' ? 'selected' : '' ?>>Self-Employed</option>
                            <option value="unemployed" <?= old('employment_status') === 'unemployed' ? 'selected' : '' ?>>Unemployed</option>
                            <option value="student" <?= old('employment_status') === 'student' ? 'selected' : '' ?>>Student</option>
                        </select>
                    </div>
                    
                    <!-- Monthly Income -->
                    <div class="form-group mb-3">
                        <label for="monthly_income">Monthly Income <span style="color: red;">*</span></label>
                        <input type="number" name="monthly_income" id="monthly_income" class="form-control" value="<?= old('monthly_income') ?>" step="0.01" required>
                    </div>
                    
                    <!-- Proof of Income Upload -->
                    <div class="form-group mb-3">
                        <label>Proof of Income <span style="color: red;">*</span></label>
                        <div class="file-input-wrapper">
                            <label for="proof_of_income" class="file-input-label">
                                <i class="bi bi-cloud-upload"></i>
                                <strong>Click to upload or drag & drop</strong>
                                <div class="file-name">PDF, JPEG, PNG (Max 5 MB)</div>
                            </label>
                            <input type="file" name="proof_of_income" id="proof_of_income" accept="application/pdf,image/jpeg,image/png" required>
                        </div>
                    </div>
                    
                    <!-- Bank Details (Optional) -->
                    <div class="form-group mb-3">
                        <label for="bank_name">Bank Name (Optional)</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control" value="<?= old('bank_name') ?>">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="bank_account">Bank Account (Optional)</label>
                        <input type="text" name="bank_account" id="bank_account" class="form-control" value="<?= old('bank_account') ?>">
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit">Submit for Verification</button>
                </form>
            </div>
        </div>
        
        <?php endif; ?>
        
    </div>
</div>

<script>
    // File upload drag & drop
    document.querySelectorAll('.file-input-wrapper').forEach(wrapper => {
        const input = wrapper.querySelector('input[type="file"]');
        const label = wrapper.querySelector('.file-input-label');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            label.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            label.addEventListener(eventName, () => label.style.background = '#e8f4ff');
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            label.addEventListener(eventName, () => label.style.background = '#f0f8ff');
        });
        
        label.addEventListener('drop', (e) => {
            input.files = e.dataTransfer.files;
        });
    });
</script>

<?php $this->endSection(); ?>