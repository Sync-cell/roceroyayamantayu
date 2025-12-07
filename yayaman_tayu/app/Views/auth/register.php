<?php

helper('form'); // ensure set_value() and other form helpers are available
$this->extend('layout/auth');
?>

<?php $this->section('content'); ?>
<?php $validation = \Config\Services::validation(); ?>
<h4 class="mb-3">Create your account</h4>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<form method="post" action="<?= site_url('/register') ?>">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label class="form-label">Full name</label>
        <input name="name" type="text" class="form-control <?php if ($validation->hasError('name')) echo 'is-invalid' ?>" value="<?= set_value('name') ?>" required>
        <div class="invalid-feedback"><?= $validation->getError('name') ?></div>
    </div>

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

    <div class="mb-3">
        <label class="form-label">Confirm password</label>
        <input name="password_confirm" type="password" class="form-control <?php if ($validation->hasError('password_confirm')) echo 'is-invalid' ?>" required>
        <div class="invalid-feedback"><?= $validation->getError('password_confirm') ?></div>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">Register</button>
    </div>

    <div class="text-center mt-3 small-muted">
        Already registered? <a href="<?= site_url('/login') ?>">Sign in</a>
    </div>
</form>
<?php $this->endSection(); ?>