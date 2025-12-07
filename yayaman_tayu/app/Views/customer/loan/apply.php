
<?php $this->extend('layout/customer'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-file-earmark-plus"></i> Apply for Loan</h2>
                    <p class="text-muted mb-0">Complete the form below to submit your loan application</p>
                </div>
                <a href="<?= site_url('/customer/dashboard') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($errors = session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($errors as $field => $err): ?>
                            <li><?= esc(is_array($err) ? implode(', ', $err) : $err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Main Form Card -->
            <div class="card border-0 shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Loan Application Form</h5>
                </div>

                <div class="card-body p-4">
                    <?= form_open_multipart('/customer/loan/store') ?>
                    <?= csrf_field() ?>

                    <!-- Step 1: Loan Details -->
                    <div class="mb-5">
                        <h6 class="mb-3 pb-2 border-bottom"><i class="bi bi-info-circle"></i> Step 1: Loan Details</h6>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Loan Amount (₱)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-currency-peso"></i></span>
                                <input type="number" name="amount" class="form-control" id="loanAmount" step="1000" min="5000" max="500000" placeholder="Enter amount (min: ₱5,000)" required value="<?= old('amount') ?>">
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle"></i> Minimum: ₱5,000 | Maximum: ₱500,000
                            </small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Purpose of Loan</label>
                            <textarea name="purpose" class="form-control" rows="4" placeholder="Tell us why you need this loan and how you'll use the funds..." required><?= old('purpose') ?></textarea>
                            <small class="text-muted d-block mt-2">Be specific about how you'll use the funds</small>
                        </div>
                    </div>

                    <!-- Step 2: Loan Terms -->
                    <div class="mb-5">
                        <h6 class="mb-3 pb-2 border-bottom"><i class="bi bi-hourglass-split"></i> Step 2: Loan Terms</h6>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Loan Term (Months)</label>
                                <select name="term_months" id="loanTerm" class="form-select form-select-lg" required onchange="calculateInterest()">
                                    <option value="">-- Select Term --</option>
                                    <option value="3" <?= old('term_months') === '3' ? 'selected' : '' ?>>3 Months</option>
                                    <option value="6" <?= old('term_months') === '6' ? 'selected' : '' ?>>6 Months</option>
                                    <option value="12" <?= old('term_months') === '12' ? 'selected' : '' ?>>12 Months</option>
                                    <option value="18" <?= old('term_months') === '18' ? 'selected' : '' ?>>18 Months</option>
                                    <option value="24" <?= old('term_months') === '24' ? 'selected' : '' ?>>24 Months</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Payment Method</label>
                                <select name="payment_method" class="form-select form-select-lg" required>
                                    <option value="">-- Select Method --</option>
                                    <option value="weekly" <?= old('payment_method') === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                                    <option value="bi-weekly" <?= old('payment_method') === 'bi-weekly' ? 'selected' : '' ?>>Bi-weekly</option>
                                    <option value="monthly" <?= old('payment_method') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                                </select>
                            </div>
                        </div>

                        <!-- Interest Preview -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center">
                                        <small class="text-muted d-block">Interest Rate</small>
                                        <h4 class="mb-0" id="interestRateDisplay" style="color: #667eea;">5.00%</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center">
                                        <small class="text-muted d-block">Total Amount Due</small>
                                        <h4 class="mb-0" id="totalAmountDisplay" style="color: #28a745;">₱0.00</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Disbursement Details -->
                    <div class="mb-5">
                        <h6 class="mb-3 pb-2 border-bottom"><i class="bi bi-cash-flow"></i> Step 3: Disbursement Details</h6>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Disbursement Method</label>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="form-check p-3 border rounded" style="background: #f8f9fa;">
                                        <input class="form-check-input" type="radio" name="disbursement_method" id="bankTransfer" value="bank_transfer" required <?= old('disbursement_method') === 'bank_transfer' ? 'checked' : '' ?> onchange="updateAccountLabel()">
                                        <label class="form-check-label" for="bankTransfer">
                                            <i class="bi bi-bank2"></i> <strong>Bank Transfer</strong>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check p-3 border rounded" style="background: #f8f9fa;">
                                        <input class="form-check-input" type="radio" name="disbursement_method" id="gcash" value="gcash" required <?= old('disbursement_method') === 'gcash' ? 'checked' : '' ?> onchange="updateAccountLabel()">
                                        <label class="form-check-label" for="gcash">
                                            <i class="bi bi-phone"></i> <strong>GCash</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Account Holder Name</label>
                            <input type="text" name="account_holder_name" class="form-control form-control-lg" placeholder="Enter full name on the account" required value="<?= old('account_holder_name') ?>">
                            <small class="text-muted d-block mt-2">Full name as it appears on your bank account or GCash</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold" id="accountNumberLabel">Bank Account Number</label>
                            <input type="text" name="bank_gcash_account" class="form-control form-control-lg" id="accountInput" placeholder="Enter your account number or GCash number" required value="<?= old('bank_gcash_account') ?>">
                            <small class="text-muted d-block mt-2" id="accountHint">We'll use this to disburse your loan amount</small>
                        </div>
                    </div>

                    <!-- Step 4: Supporting Documents -->
                    <div class="mb-5">
                        <h6 class="mb-3 pb-2 border-bottom"><i class="bi bi-file-earmark-pdf"></i> Step 4: Supporting Documents</h6>

                        <div class="card border-2 border-dashed p-4 text-center" style="cursor: pointer;" onclick="document.getElementById('documentInput').click()">
                            <i class="bi bi-cloud-arrow-up" style="font-size: 2rem; color: #667eea;"></i>
                            <input type="file" name="supporting_documents[]" class="form-control d-none" id="documentInput" multiple accept=".pdf,.jpg,.jpeg,.png,.gif" onchange="displayFiles(event)">
                            <label for="documentInput" class="mb-0 mt-3" style="cursor: pointer;">
                                <strong style="color: #667eea;">Click to upload</strong> or drag and drop
                                <br>
                                <small class="text-muted">PNG, JPG, GIF, PDF up to 10MB each</small>
                            </label>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> Upload proof of income (payslip, bank statement), valid ID, and any other supporting documents
                        </small>
                        <div id="fileList" class="mt-3"></div>
                    </div>

                    <!-- Step 5: Terms & Conditions -->
                    <div class="mb-5">
                        <h6 class="mb-3 pb-2 border-bottom"><i class="bi bi-checkbox-circle"></i> Step 5: Agreement</h6>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agreeTerms" name="agree_terms" required <?= old('agree_terms') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="agreeTerms">
                                I agree to the <a href="#" target="_blank">Terms and Conditions</a>, <a href="#" target="_blank">Privacy Policy</a>, and loan agreement
                            </label>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                        <a href="<?= site_url('/customer/dashboard') ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg fw-bold">
                            <i class="bi bi-check-circle"></i> Submit Application
                        </button>
                    </div>

                    <?= form_close() ?>
                </div>
            </div>
        </div>

        <!-- Right Sidebar: Info & Timeline -->
        <div class="col-lg-4">
            <!-- How It Works Card -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0"><i class="bi bi-lightning"></i> How It Works</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item mb-3 pb-3 border-bottom">
                            <div class="d-flex">
                                <div class="timeline-marker bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem;">1</div>
                                <div class="ms-3">
                                    <p class="mb-0"><strong>Fill Form</strong></p>
                                    <small class="text-muted">Complete all required fields</small>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item mb-3 pb-3 border-bottom">
                            <div class="d-flex">
                                <div class="timeline-marker bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem;">2</div>
                                <div class="ms-3">
                                    <p class="mb-0"><strong>Upload Documents</strong></p>
                                    <small class="text-muted">Submit supporting files</small>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item mb-3 pb-3 border-bottom">
                            <div class="d-flex">
                                <div class="timeline-marker bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem;">3</div>
                                <div class="ms-3">
                                    <p class="mb-0"><strong>Review</strong></p>
                                    <small class="text-muted">Our team reviews (24-48 hrs)</small>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="d-flex">
                                <div class="timeline-marker bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;"><i class="bi bi-check"></i></div>
                                <div class="ms-3">
                                    <p class="mb-0"><strong>Disbursal</strong></p>
                                    <small class="text-muted">Funds to your account</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requirements Card -->
            <div class="card border-0 shadow">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0"><i class="bi bi-checklist"></i> Requirements</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Valid ID (Driver's License, SSS, etc.)</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Proof of Income (Payslip, ITR)</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Bank Statement (last 3 months)</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Billing Statement (proof of address)</li>
                        <li><i class="bi bi-check-circle text-success"></i> Completed KYC verification</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control-lg, .form-select-lg {
        height: 2.8rem;
        font-size: 1rem;
    }

    .border-dashed {
        border: 2px dashed #dee2e6 !important;
    }

    .timeline-marker {
        flex-shrink: 0;
    }

    #documentInput {
        display: none;
    }
</style>

<script>
function calculateInterest() {
    const amount = parseFloat(document.getElementById('loanAmount').value) || 0;
    const term = parseInt(document.getElementById('loanTerm').value) || 0;

    if (amount <= 0 || term <= 0) {
        document.getElementById('totalAmountDisplay').textContent = '₱0.00';
        return;
    }

    const baseRate = 5;
    const monthlyRate = 0.5;
    const totalRate = baseRate + (monthlyRate * term);
    const interest = (amount * totalRate) / 100;
    const totalAmount = amount + interest;

    document.getElementById('interestRateDisplay').textContent = totalRate.toFixed(2) + '%';
    document.getElementById('totalAmountDisplay').textContent = '₱' + totalAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function updateAccountLabel() {
    const bankRadio = document.getElementById('bankTransfer');
    const accountLabel = document.getElementById('accountNumberLabel');
    const accountInput = document.getElementById('accountInput');
    const accountHint = document.getElementById('accountHint');

    if (bankRadio.checked) {
        accountLabel.textContent = 'Bank Account Number';
        accountInput.placeholder = 'Enter your bank account number';
        accountHint.textContent = 'We\'ll use this bank account to disburse your loan amount';
    } else {
        accountLabel.textContent = 'GCash Number';
        accountInput.placeholder = 'Enter your GCash mobile number';
        accountHint.textContent = 'We\'ll transfer your loan amount to this GCash number';
    }
}

function displayFiles(event) {
    const fileList = document.getElementById('fileList');
    const files = event.target.files;

    fileList.innerHTML = '';

    if (files.length === 0) return;

    fileList.innerHTML = '<h6 class="mb-2"><i class="bi bi-file-earmark-check"></i> Selected Files:</h6>';

    Array.from(files).forEach((file) => {
        const fileSize = (file.size / 1024).toFixed(2);
        const fileElement = document.createElement('div');
        fileElement.className = 'alert alert-info alert-sm mb-2 py-2';
        fileElement.innerHTML = `
            <i class="bi bi-file"></i> ${file.name}
            <span class="badge bg-info ms-2">${fileSize} KB</span>
        `;
        fileList.appendChild(fileElement);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('loanAmount');
    if (amountInput) {
        amountInput.addEventListener('change', calculateInterest);
        amountInput.addEventListener('input', calculateInterest);
    }
    calculateInterest();
    updateAccountLabel();
});
</script>

<?php $this->endSection(); ?>