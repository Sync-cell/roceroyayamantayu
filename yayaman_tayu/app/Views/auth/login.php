<?php

helper('form'); // ensure set_value() and other form helpers are available
$this->extend('layout/auth');
?>

<?php $this->section('content'); ?>
<?php $validation = \Config\Services::validation(); ?>
<h4 class="mb-3">Sign in to your account</h4>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<form method="post" action="<?= site_url('/login') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control <?php if ($validation->hasError('email')) echo 'is-invalid' ?>" value="<?= set_value('email') ?>" required>
        <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <input name="password" type="password" class="form-control <?php if ($validation->hasError('password')) echo 'is-invalid' ?>" required>
        <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" <?= set_value('remember') ? 'checked' : '' ?>>
            <label class="form-check-label small" for="remember">Remember me</label>
        </div>
        <a href="<?= site_url('/forgot-password') ?>" class="small">Forgot?</a>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">Sign in</button>
    </div>

    <div class="text-center mt-3 small-muted">
        Don't have an account? <a href="<?= site_url('/register') ?>">Register</a>
    </div>
</form>
<?php $this->endSection(); ?>