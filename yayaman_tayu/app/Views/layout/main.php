
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) . ' - ' : '' ?>Yayaman Tayu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --brand-1: #667eea;
            --brand-2: #764ba2;
            --accent: #00B4DB;
            --muted: #6c757d;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }

        .admin-sidebar {
            background: linear-gradient(135deg, var(--brand-1), var(--brand-2));
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            color: #fff;
            padding: 20px 12px;
            box-shadow: 2px 0 12px rgba(0,0,0,0.12);
            z-index: 1000;
            overflow-y: auto;
        }

        .admin-sidebar .logo {
            text-align: center;
            padding: 6px 12px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 8px;
        }

        .admin-sidebar .logo h4 {
            margin: 0;
            font-weight: 800;
        }

        .admin-sidebar .logo small {
            color: rgba(255,255,255,0.7);
        }

        .admin-sidebar .profile {
            padding: 18px 12px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 8px;
        }

        .admin-sidebar .profile .avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 8px;
            background-size: cover;
            background-position: center;
            color: #fff;
        }

        .admin-sidebar .profile .profile-name {
            margin: 0;
            font-weight: 600;
            color: #fff;
            font-size: 0.95rem;
        }

        .admin-sidebar .profile .profile-email {
            color: rgba(255,255,255,0.6);
            font-size: 0.8rem;
            margin: 3px 0 0 0;
        }

        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.9) !important;
            padding: 10px 12px;
            margin: 6px;
            border-radius: 6px;
            transition: all 0.15s;
            font-size: 0.9rem;
        }

        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            background: rgba(255,255,255,0.06);
            transform: translateX(6px);
            color: #fff !important;
        }

        .admin-sidebar hr {
            border-color: rgba(255,255,255,0.06);
            margin: 12px 0;
        }

        .admin-main {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-topbar {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            padding: 12px 20px;
        }

        main {
            flex: 1;
            padding: 28px;
        }

        footer {
            background: #fff;
            padding: 14px;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }

        /* Notification dropdown */
        .nav-notification {
            position: relative;
        }

        .nav-notification .badge-notify {
            position: absolute;
            top: 4px;
            right: 0;
            transform: translate(50%,-20%);
            font-size: 0.65rem;
            padding: 3px 6px;
            border-radius: 999px;
        }

        .notification-list {
            min-width: 320px;
            max-height: 420px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 0.6rem;
            border-bottom: 1px solid #f1f1f1;
        }

        .notification-item.unread {
            background: #f4f9ff;
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .admin-main {
                margin-left: 0;
            }

            main {
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <div class="logo">
                <h4><i class="bi bi-building"></i> Yayaman Tayu</h4>
                <small>Admin Portal</small>
            </div>

            <!-- Admin Profile Section -->
            <div class="profile">
                <?php
                    $adminPhoto = session()->get('profile_photo') 
                                ?? session()->get('admin_photo') 
                                ?? session()->get('photo') 
                                ?? null;

                    $publicPath = FCPATH . 'uploads/profile/';
                    $defaultAvatar = base_url('assets/img/default-avatar.png');

                    $profileUrl = $defaultAvatar;
                    if ($adminPhoto && file_exists($publicPath . $adminPhoto)) {
                        $profileUrl = base_url('uploads/profile/' . $adminPhoto);
                    }

                    $adminName = session()->get('admin_name')
                               ?? session()->get('user_name')
                               ?? session()->get('fullname')
                               ?? session()->get('name')
                               ?? 'Admin';

                    $adminEmail = session()->get('admin_email')
                                ?? session()->get('user_email')
                                ?? session()->get('email')
                                ?? '';
                ?>
                <div class="avatar" style="background-image: url('<?= esc($profileUrl) ?>');">
                    <?php if (empty($adminPhoto)): ?>
                        <i class="bi bi-person-fill"></i>
                    <?php endif; ?>
                </div>
                <p class="profile-name"><?= esc($adminName) ?></p>
                <p class="profile-email"><?= esc($adminEmail) ?></p>
            </div>

            <nav class="nav flex-column">
                <div class="mb-2"><small class="text-white-50 px-2">Main</small></div>
                <a class="nav-link" href="<?= site_url('/admin/dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a class="nav-link" href="<?= site_url('/admin/loan') ?>"><i class="bi bi-file-earmark-text me-2"></i> Loans</a>
                <a class="nav-link" href="<?= site_url('/admin/kyc') ?>"><i class="bi bi-card-checklist me-2"></i> KYC</a>
                <a class="nav-link" href="<?= site_url('/admin/customers') ?>"><i class="bi bi-people me-2"></i> Customers</a>
                <a class="nav-link" href="<?= site_url('/admin/reports') ?>"><i class="bi bi-graph-up me-2"></i> Reports</a>

                <hr>

                <div class="mt-2"><small class="text-white-50 px-2">Account</small></div>
                <a class="nav-link" href="<?= site_url('/admin/profile') ?>"><i class="bi bi-person-circle me-2"></i> Profile</a>
                <a class="nav-link" href="<?= site_url('/notifications') ?>"><i class="bi bi-bell me-2"></i> Notifications</a>
                <a class="nav-link" href="<?= site_url('/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="admin-main w-100">
            <!-- Top Bar -->
            <header class="admin-topbar d-flex align-items-center justify-content-between">
                <div>
                    <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" onclick="document.querySelector('.admin-sidebar').classList.toggle('show')">
                        <i class="bi bi-list"></i>
                    </button>
                    <span class="ms-2 h6 mb-0"><?= isset($title) ? esc($title) : 'Dashboard' ?></span>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <?php
                        $session = session();
                        $isAdmin = (bool) $session->get('admin_id');
                        $isMaintenanceOn = false;
                        try {
                            $settingModel = new \App\Models\SettingModel();
                            $rec = $settingModel->where('key','maintenance_mode')->first();
                            $isMaintenanceOn = $rec && $rec['value'] === '1';
                        } catch (\Throwable $e) {
                            // ignore if SettingModel not present
                        }
                    ?>

                    <!-- Maintenance toggle (visible to admins only) -->
                    <?php if ($isAdmin): ?>
                        <form method="post" action="<?= site_url('/admin/toggle-maintenance') ?>" class="d-inline-block me-2">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm <?= $isMaintenanceOn ? 'btn-danger' : 'btn-outline-secondary' ?>" title="Toggle maintenance">
                                <i class="bi bi-tools me-1"></i>
                                <?= $isMaintenanceOn ? 'Maintenance: ON' : 'Maintenance: OFF' ?>
                            </button>
                        </form>

                        <a href="<?= site_url('/admin/maintenance-settings') ?>" class="btn btn-sm btn-outline-primary me-2" title="Manage maintenance whitelist">
                            <i class="bi bi-people-fill me-1"></i> Manage Whitelist
                        </a>
                    <?php endif; ?>

                    <!-- Notifications Dropdown -->
                    <div class="nav-notification dropdown">
                        <a class="text-dark text-decoration-none" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell" style="font-size: 1.2rem;"></i>
                            <span id="notifCount" class="badge bg-danger badge-notify visually-hidden">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-0" style="width: 360px;">
                            <div class="p-2 border-bottom d-flex justify-content-between align-items-center" style="background: #f8f9fa;">
                                <strong>Notifications</strong>
                                <button id="markAllReadBtn" class="btn btn-sm btn-link">Mark all read</button>
                            </div>
                            <div id="notifList" class="notification-list"></div>
                            <div class="p-2 text-center">
                                <a href="<?= site_url('/notifications') ?>" class="small">View all notifications</a>
                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <a class="text-dark text-decoration-none" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <span class="ms-1 d-none d-md-inline"><?= esc($adminName ?? 'Admin') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= site_url('/admin/profile') ?>"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('/admin/settings') ?>"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= site_url('/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="container-fluid">
                <?= $this->renderSection('content') ?>
            </main>

            <!-- Footer -->
            <footer>
                <small class="text-muted">&copy; <?= date('Y') ?> Yayaman Tayu. All rights reserved.</small>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Fetch unread notifications
        async function fetchNotifications() {
            try {
                const res = await fetch('<?= site_url('/notifications/unread') ?>', {
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                const json = await res.json();
                if (!json.success) return;

                const count = json.count || 0;
                const list = json.data || [];
                const notifCountEl = document.getElementById('notifCount');
                const notifListEl = document.getElementById('notifList');

                if (count > 0) {
                    notifCountEl.textContent = count;
                    notifCountEl.classList.remove('visually-hidden');
                } else {
                    notifCountEl.classList.add('visually-hidden');
                }

                notifListEl.innerHTML = '';
                if (list.length === 0) {
                    notifListEl.innerHTML = '<div class="p-3 text-center text-muted small">No new notifications</div>';
                    return;
                }

                list.slice(0, 10).forEach(n => {
                    const div = document.createElement('div');
                    div.className = 'notification-item' + (n.is_read == 0 ? ' unread' : '');
                    div.innerHTML = `
                        <div class="d-flex justify-content-between">
                            <div class="small flex-grow-1">
                                <strong>${escapeHtml(n.title)}</strong>
                                <div class="text-muted mt-1">${escapeHtml(truncate(n.message, 80))}</div>
                            </div>
                            <div class="small text-muted ms-2">${timeAgo(n.created_at)}</div>
                        </div>
                        <div class="mt-2 text-end"><a href="<?= site_url('/notifications/view/') ?>${n.id}" class="btn btn-sm btn-outline-primary">View</a></div>
                    `;
                    notifListEl.appendChild(div);
                });
            } catch (e) {
                console.error(e);
            }
        }

        // Mark all as read
        document.getElementById('markAllReadBtn')?.addEventListener('click', async function(e) {
            e.preventDefault();
            if (!confirm('Mark all notifications as read?')) return;
            try {
                const res = await fetch('<?= site_url('/notifications/mark-all-read') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) fetchNotifications();
            } catch (e) {
                console.error(e);
            }
        });

        // Helpers
        function escapeHtml(s) {
            if (!s) return '';
            return String(s).replace(/[&<>"'`=\/]/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;","/":"&#x2F;","`":"&#60;","=":"&#61;"}[c]));
        }

        function truncate(t, l) {
            if (!t) return '';
            return t.length > l ? t.substring(0, l - 1) + '…' : t;
        }

        function timeAgo(d) {
            try {
                const date = new Date(d);
                const diff = Math.floor((Date.now() - date.getTime()) / 1000);
                if (diff < 60) return diff + 's';
                if (diff < 3600) return Math.floor(diff / 60) + 'm';
                if (diff < 86400) return Math.floor(diff / 3600) + 'h';
                return Math.floor(diff / 86400) + 'd';
            } catch {
                return '';
            }
        }

        // Load notifications on page load and poll every 30s
        document.addEventListener('DOMContentLoaded', function() {
            fetchNotifications();
            setInterval(fetchNotifications, 30000);
        });
    </script>
</body>
</html>