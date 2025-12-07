
<?php $this->extend('layout/main'); ?>

<?php $this->section('content'); ?>

<?php
$session = session();

// Try to load current admin record from AdminModel if available, else fallback to session
$fullname = '';
$email = '';
$phone = '';
$adminId = $session->get('admin_id');

try {
    $adminModelClass = '\App\Models\AdminModel';
    if ($adminId && class_exists($adminModelClass)) {
        $adminModel = new $adminModelClass();
        $admin = $adminModel->find($adminId);
        if ($admin) {
            $fullname = $admin['name'] ?? $admin['fullname'] ?? '';
            $email    = $admin['email'] ?? '';
            $phone    = $admin['phone'] ?? $admin['contact'] ?? '';
        }
    }
} catch (\Throwable $e) {
    // ignore
}

if (empty($fullname)) $fullname = $session->get('admin_name') ?? $session->get('fullname') ?? '';
if (empty($email))    $email    = $session->get('admin_email') ?? $session->get('email') ?? '';
if (empty($phone))    $phone    = $session->get('admin_phone') ?? $session->get('phone') ?? '';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #00B4DB 0%, #0083B0 100%); color: white;">
                    <h4 class="mb-0"><i class="bi bi-person-circle"></i> Admin Profile</h4>
                </div>

                <div class="card-body p-4">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
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

                        <div class="mb-3">
                            <label for="phone" class="form-label fw-bold">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= esc($phone) ?>" placeholder="+63 9XX XXX XXXX">
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