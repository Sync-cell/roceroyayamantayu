
<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>

<?php
$session = session();

// Prefer values passed from controller; fallback to session
$fullname = isset($fullname) ? $fullname : ($session->get('admin_name') ?? $session->get('fullname') ?? $session->get('name') ?? '');
$email    = isset($email)    ? $email    : ($session->get('admin_email') ?? $session->get('email') ?? '');
$phone    = isset($phone)    ? $phone    : ($session->get('admin_phone') ?? $session->get('phone') ?? '');

// Profile photo resolution
$adminPhoto = $session->get('profile_photo') ?? $session->get('admin_photo') ?? $session->get('photo') ?? null;
$defaultAvatar = base_url('assets/img/default-avatar.png');
$profileUrl = $defaultAvatar;

if (! empty($adminPhoto)) {
    $publicPath = FCPATH . 'uploads/profile/';
    if (file_exists($publicPath . $adminPhoto)) {
        $profileUrl = base_url('uploads/profile/' . $adminPhoto);
    } elseif (filter_var($adminPhoto, FILTER_VALIDATE_URL)) {
        $profileUrl = $adminPhoto;
    } else {
        $altPath = FCPATH . 'uploads/' . $adminPhoto;
        if (file_exists($altPath)) {
            $profileUrl = base_url('uploads/' . $adminPhoto);
        }
    }
}
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-lg">
                <div class="card-header d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #00B4DB 0%, #0083B0 100%); color: white;">
                    <h4 class="mb-0"><i class="bi bi-person-circle"></i> Admin Profile</h4>
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?= esc($profileUrl) ?>" alt="avatar" style="width:48px;height:48px;border-radius:8px;object-fit:cover;border:2px solid rgba(255,255,255,0.12)">
                        <div class="text-end">
                            <div style="font-weight:700"><?= esc($fullname ?: 'Admin') ?></div>
                            <div style="font-size:0.85rem;opacity:0.9"><?= esc($email ?: '') ?></div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= site_url('/admin/profile') ?>">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="fullname" class="form-label fw-bold">Full Name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" value="<?= esc($fullname) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= esc($email) ?>" required>
                        </div>

                       

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">Change Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Leave empty to keep current password">
                            <small class="text-muted">Only fill if you want to change your password</small>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Profile
                            </button>
                            <a href="<?= site_url('/admin') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>
