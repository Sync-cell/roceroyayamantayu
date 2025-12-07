<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<?php
    $currentIp = service('request')->getIPAddress() ?? ($_SERVER['REMOTE_ADDR'] ?? 'Unknown');
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="flex-shrink-0">
                        <?php if ($isMaintenanceOn): ?>
                            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                                <i class="bi bi-wrench" style="font-size:1.6rem;"></i>
                            </div>
                        <?php else: ?>
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                                <i class="bi bi-check2-circle" style="font-size:1.6rem;"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex-grow-1">
                        <h4 class="mb-1"><?= $isMaintenanceOn ? 'Maintenance Mode Enabled' : 'Maintenance Mode Disabled' ?></h4>
                        <p class="text-muted mb-2">Toggle system-wide maintenance to prevent public access while performing updates. Administrators and whitelisted IPs will remain unaffected.</p>

                        <div class="d-flex gap-2 align-items-center">
                            <form method="post" action="<?= site_url('/admin/toggle-maintenance') ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-<?= $isMaintenanceOn ? 'danger' : 'outline-secondary' ?> btn-sm">
                                    <i class="bi bi-power me-1"></i>
                                    <?= $isMaintenanceOn ? 'Disable Maintenance' : 'Enable Maintenance' ?>
                                </button>
                            </form>

                            <a href="<?= site_url('/admin/maintenance-settings') ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-gear me-1"></i> Settings
                            </a>

                            <div class="ms-3">
                                <small class="text-muted">Your IP:</small>
                                <div class="d-inline-flex align-items-center ms-2">
                                    <code id="currentIp" class="px-2 py-1 bg-light border rounded"><?= esc($currentIp) ?></code>
                                    <button id="copyIpBtn" class="btn btn-sm btn-outline-secondary ms-2" title="Copy IP">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">Quick actions:</small>
                            <div class="mt-2 d-flex gap-2">
                                <form method="post" action="<?= site_url('/admin/maintenance-settings') ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="new_ip" value="<?= esc($currentIp) ?>">
                                    <button class="btn btn-sm btn-outline-success" title="Add your current IP to whitelist">
                                        <i class="bi bi-plus-lg me-1"></i> Add My IP
                                    </button>
                                </form>

                                <a href="<?= site_url('/admin/maintenance-settings') ?>" class="btn btn-sm btn-outline-info" title="Refresh list">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Whitelist management -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i> Whitelisted IPs</h5>
                <small class="text-muted">IPs here bypass maintenance page</small>
            </div>

            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <div class="mb-3">
                    <form method="post" action="<?= site_url('/admin/maintenance-settings') ?>" class="row gx-2 gy-2 align-items-center">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="add">
                        <div class="col-auto" style="flex:1">
                            <input name="new_ip" class="form-control form-control-sm" placeholder="Enter IP to whitelist (e.g. 203.0.113.5)" required>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i> Add IP</button>
                        </div>
                    </form>
                </div>

                <?php if (empty($whitelist)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-exclamation-circle me-2" style="font-size:1.2rem;"></i>
                        No whitelisted IPs yet. Use "Add My IP" to quickly add your current address.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:1%">#</th>
                                    <th>IP Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($whitelist as $idx => $ip): ?>
                                    <tr>
                                        <td><?= $idx + 1 ?></td>
                                        <td>
                                            <span class="fw-monospace"><?= esc($ip) ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-outline-secondary copy-btn" data-ip="<?= esc($ip) ?>" title="Copy IP">
                                                    <i class="bi bi-clipboard"></i>
                                                </button>

                                                <form method="post" action="<?= site_url('/admin/maintenance-settings') ?>" onsubmit="return confirm('Remove <?= esc($ip) ?> from whitelist?')" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="remove">
                                                    <input type="hidden" name="ip" value="<?= esc($ip) ?>">
                                                    <button class="btn btn-sm btn-outline-danger" title="Remove">
                                                        <i class="bi bi-trash"></i> Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <hr>

                <div class="small text-muted">
                    Tips:
                    <ul class="mb-0">
                        <li>Add administrators' public IPs to avoid accidental lockout when enabling maintenance.</li>
                        <li>Your session as an admin will also bypass maintenance while logged in.</li>
                        <li>Use the "Add My IP" button after enabling maintenance — it is also added automatically when you enable maintenance via the toggle.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Copy helpers
    function copyText(text) {
        if (!navigator.clipboard) {
            const tmp = document.createElement('textarea');
            tmp.value = text;
            document.body.appendChild(tmp);
            tmp.select();
            document.execCommand('copy');
            document.body.removeChild(tmp);
            return;
        }
        navigator.clipboard.writeText(text).catch(()=>{});
    }

    document.getElementById('copyIpBtn')?.addEventListener('click', function() {
        const ip = document.getElementById('currentIp')?.textContent?.trim();
        if (ip) {
            copyText(ip);
            this.classList.add('btn-success');
            setTimeout(()=> this.classList.remove('btn-success'), 1200);
        }
    });

    document.querySelectorAll('.copy-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            const ip = this.getAttribute('data-ip');
            if (ip) {
                copyText(ip);
                this.classList.add('btn-success');
                setTimeout(()=> this.classList.remove('btn-success'), 900);
            }
        });
    });
</script>

<?php $this->endSection(); ?>