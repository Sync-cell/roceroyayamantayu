<?php

// ...existing code...
$this->extend('layout/customer');
helper('form');
?>

<?php $this->section('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <a href="<?= site_url('/customer/dashboard') ?>" class="btn btn-outline-secondary btn-sm mb-3">
                <i class="bi bi-chevron-left"></i> Back
            </a>

            <div class="panel">
                <h2 class="mb-4"><i class="bi bi-person-circle"></i> My Profile</h2>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($errors = session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?= form_open_multipart('/customer/profile/update') ?>

                <div class="row mb-3">
                    <div class="col-md-4 text-center">
                        <?php 
                            $profilePhoto = $customer['profile_photo'] ?? null;
                            $photoUrl = $profilePhoto ? base_url('uploads/profile/' . $profilePhoto) : base_url('assets/img/default-avatar.jpg');
                        ?>
                        <img src="<?= $photoUrl ?>" alt="Profile" class="rounded-circle mb-2" style="width:120px;height:120px;object-fit:cover;" onerror="this.src='<?= base_url('assets/img/default-avatar.jpg') ?>'">

                        <div class="mb-2">
                            <input type="file" name="profile_photo" class="form-control form-control-sm" accept="image/*">
                            <small class="text-muted">JPG, PNG (max 2MB)</small>
                        </div>

                        <p class="small text-muted mb-0">Account ID: <?= esc($customer['id'] ?? '—') ?></p>
                        <p class="small <?= ($customer['is_verified'] ?? false) ? 'text-success' : 'text-warning' ?>">
                            <?= ($customer['is_verified'] ?? false) ? '✓ Verified' : '⚠ Unverified' ?>
                        </p>
                    </div>

                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="fullname" class="form-control" value="<?= esc(old('fullname', $customer['fullname'] ?? '')) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= esc(old('email', $customer['email'] ?? '')) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="tel" name="contact_number" class="form-control" value="<?= esc(old('contact_number', $customer['contact_number'] ?? '')) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Registered On</label>
                            <input type="text" class="form-control" value="<?= ($customer['created_at'] ?? null) ? date('M d, Y', strtotime($customer['created_at'])) : '—' ?>" readonly>
                        </div>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Personal Details</h5>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Birthdate</label>
                        <input type="date" name="birthdate" class="form-control" value="<?= esc(old('birthdate', $customer['birthdate'] ?? '')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select gender</option>
                            <option value="male" <?= (old('gender', $customer['gender'] ?? '') === 'male') ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= (old('gender', $customer['gender'] ?? '') === 'female') ? 'selected' : '' ?>>Female</option>
                            <option value="other" <?= (old('gender', $customer['gender'] ?? '') === 'other') ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Civil Status</label>
                        <select name="civil_status" class="form-select">
                            <option value="">Select civil status</option>
                            <option value="single" <?= (old('civil_status', $customer['civil_status'] ?? '') === 'single') ? 'selected' : '' ?>>Single</option>
                            <option value="married" <?= (old('civil_status', $customer['civil_status'] ?? '') === 'married') ? 'selected' : '' ?>>Married</option>
                            <option value="divorced" <?= (old('civil_status', $customer['civil_status'] ?? '') === 'divorced') ? 'selected' : '' ?>>Divorced</option>
                            <option value="widowed" <?= (old('civil_status', $customer['civil_status'] ?? '') === 'widowed') ? 'selected' : '' ?>>Widowed</option>
                        </select>
                    </div>
                </div>

                <h5 class="mb-3 mt-4">Address</h5>

                <div class="mb-3">
                    <label class="form-label">Current Address</label>
                    <textarea name="address_current" class="form-control" rows="2"><?= esc(old('address_current', $customer['address_current'] ?? '')) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Permanent Address</label>
                    <textarea name="address_permanent" class="form-control" rows="2"><?= esc(old('address_permanent', $customer['address_permanent'] ?? '')) ?></textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-check-circle"></i> Save Changes
                    </button>
                    <a href="<?= site_url('/customer/dashboard') ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>

                <?= form_close() ?>
            </div>

            <!-- Change Password -->
            <div class="panel mt-4">
                <h5 class="mb-4">Change Password</h5>

                <?= form_open('/customer/profile/change-password') ?>

                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirm" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-warning w-100">
                        <i class="bi bi-lock"></i> Update Password
                    </button>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>