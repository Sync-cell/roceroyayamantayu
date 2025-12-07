
<?php $this->extend('layout/auth'); ?>
<?php $this->section('content'); ?>
<?php helper('form'); $validation = \Config\Services::validation(); ?>

<h4 class="mb-3">Reset your password</h4>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<form method="post" action="<?= site_url('forgot-password') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control <?= $validation->hasError('email') ? 'is-invalid' : '' ?>" value="<?= set_value('email') ?>" required>
        <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">Send reset link</button>
    </div>

    <div class="text-center mt-3 small-muted">
        <a href="<?= site_url('/login') ?>">Back to sign in</a>
    </div>
</form>

<?php $this->endSection(); ?>
// ...existing code...