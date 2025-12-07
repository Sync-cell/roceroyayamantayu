
<?php $this->extend('layout/customer'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="bi bi-bell"></i> Notifications</h2>
            <p class="text-muted">Stay updated with your loan applications and account activities</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (empty($notifications) || !is_array($notifications)): ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            <p class="text-muted mt-3">No notifications yet</p>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-lg">
            <div class="card-body p-0">
                <?php foreach ($notifications as $notif): ?>
                    <div class="notification-item border-bottom p-4" style="background: <?= empty($notif['is_read']) ? '#f0f7ff' : 'white' ?>;">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-1"><?= esc($notif['title']) ?> <?= empty($notif['is_read']) ? '<span class="badge bg-info ms-2">New</span>' : '' ?></h6>
                                <p class="mb-2 text-muted"><?= esc($notif['message']) ?></p>
                                <small class="text-muted"><?= date('M d, Y h:i A', strtotime($notif['created_at'])) ?></small>
                            </div>
                            <div class="text-end">
                                <a href="<?= site_url('/notifications/view/' . $notif['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
                                <form method="POST" action="<?= site_url('/notifications/delete/' . $notif['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this notification?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!empty($pagerLinks)): ?>
            <div class="mt-3"><?= $pagerLinks ?></div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $this->endSection(); ?>