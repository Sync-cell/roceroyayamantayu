
<?php $this->extend('layout/customer'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <a href="<?= site_url('/notifications') ?>" class="btn btn-secondary mb-3">Back</a>
    
    <div class="card border-0 shadow-lg">
        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h4 class="mb-0"><?= esc($notification['title']) ?></h4>
        </div>
        <div class="card-body">
            <p><?= nl2br(esc($notification['message'])) ?></p>
            <small class="text-muted">
                <?= date('M d, Y h:i A', strtotime($notification['created_at'])) ?>
            </small>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>