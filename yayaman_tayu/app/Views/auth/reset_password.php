
<?php $this->extend('layout/auth'); ?>
<?php $this->section('content'); ?>
<?php helper('form'); $validation = \Config\Services::validation(); ?>

<h4 class="mb-3">Set a new password</h4>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<form method="post" action="<?= site_url('reset-password') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="token" value="<?= isset($token) ? esc($token) : '' ?>">

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" value="<?= isset($email) ? esc($email) : set_value('email') ?>" name="email" required <?= isset($email) ? 'readonly' : '' ?>>
    </div>

    <div class="mb-3">
        <label class="form-label">New password</label>
        <input name="password" type="password" class="form-control <?= $validation->hasError('password') ? 'is-invalid' : '' ?>" required>
        <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Confirm new password</label>
        <input name="password_confirm" type="password" class="form-control <?= $validation->hasError('password_confirm') ? 'is-invalid' : '' ?>" required>
        <div class="invalid-feedback"><?= $validation->getError('password_confirm') ?></div>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">Update password</button>
    </div>

    <div class="text-center mt-3 small-muted">
        <a href="<?= site_url('/login') ?>">Back to sign in</a>
    </div>
</form>

<?php $this->endSection(); ?>