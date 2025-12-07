
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) . ' - ' : '' ?>Yayaman Tayu</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #00B4DB;
            --secondary-color: #0083B0;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Sidebar Styles */
        .customer-sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            color: white;
            overflow-y: auto;
            padding-top: 20px;
            box-shadow: 2px 0 12px rgba(0,0,0,0.15);
            z-index: 1000;
        }
        
        .customer-sidebar .logo {
            padding: 0 20px 30px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            text-align: center;
        }
        
        .customer-sidebar .logo h4 {
            color: white;
            font-weight: 800;
            margin: 0;
            font-size: 1.3rem;
            letter-spacing: 0.5px;
        }
        
        .customer-sidebar .logo small {
            color: rgba(255,255,255,0.7);
            display: block;
            margin-top: 5px;
            font-size: 0.8rem;
        }
        
        .customer-sidebar .profile-section {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 10px;
        }
        
        .customer-sidebar .profile-avatar {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 1.8rem;
        }
        
        .customer-sidebar .profile-name {
            color: white;
            font-weight: 600;
            margin: 0;
            font-size: 0.95rem;
        }
        
        .customer-sidebar .profile-status {
            color: rgba(255,255,255,0.7);
            font-size: 0.8rem;
            margin-top: 5px;
        }
        
        .customer-sidebar .nav-section {
            padding: 15px 0;
        }
        
        .customer-sidebar .nav-section-title {
            padding: 0 20px 10px 20px;
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .customer-sidebar .nav-link {
            color: rgba(255,255,255,0.85) !important;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
            margin: 5px 10px;
            font-size: 0.95rem;
            border-radius: 5px;
        }
        
        .customer-sidebar .nav-link:hover {
            background: rgba(255,255,255,0.15);
            border-left-color: #fff;
            color: white !important;
            transform: translateX(5px);
        }
        
        .customer-sidebar .nav-link.active {
            background: rgba(255,255,255,0.25);
            border-left-color: #fff;
            color: white !important;
            font-weight: 600;
        }
        
        .customer-sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        
        .customer-sidebar hr {
            background: rgba(255,255,255,0.2);
            margin: 15px 0;
        }
        
        .customer-sidebar .logout-section {
            padding: 15px;
            margin-top: auto;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        
        .customer-sidebar .logout-section .nav-link {
            margin: 0;
        }
        
        /* Main Content */
        .customer-main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Navbar */
        .customer-navbar {
            background: white;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            border-bottom: 1px solid #e9ecef;
            padding: 15px 30px;
        }
        
        .customer-navbar .navbar-brand {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        
        .customer-navbar .nav-link {
            color: #666 !important;
            margin: 0 10px;
            transition: all 0.3s;
        }
        
        .customer-navbar .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .customer-navbar .dropdown-menu {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: 0;
        }
        
        /* Main Content Area */
        main {
            flex: 1;
            padding: 30px 20px;
        }
        
        /* Footer */
        footer {
            margin-top: auto;
            border-top: 1px solid #ddd;
            background: white;
            padding: 20px;
        }
        
        /* Alert Customization */
        .alert {
            border-radius: 8px;
            border: 0;
        }
        
        /* Card Customization */
        .card {
            border-radius: 10px;
            border: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .customer-sidebar {
                width: 100%;
                min-height: auto;
                position: relative;
                padding-bottom: 20px;
            }
            
            .customer-main-content {
                margin-left: 0;
            }
            
            .customer-sidebar .profile-section {
                display: none;
            }
            
            main {
                padding: 20px 15px;
            }
            
            .customer-navbar {
                padding: 12px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Customer Sidebar Navigation -->
        <div class="customer-sidebar">
            <!-- Logo -->
            <div class="logo">
                <h4>
                    <i class="bi bi-piggy-bank"></i> Yayaman Tayu
                </h4>
                <small>Lending Platform</small>
            </div>
            
            <!-- Profile Section -->
            <div class="profile-section">
                <div class="profile-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <p class="profile-name"><?= session()->get('user_name') ?? 'Customer' ?></p>
                <div class="profile-status">
                    <span class="badge bg-success">Active</span>
                </div>
            </div>
            
            <!-- Main Navigation -->
            <nav class="nav flex-column">
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="bi bi-house"></i> Main
                    </div>
                    <a class="nav-link" href="<?= site_url('/customer/dashboard') ?>">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="bi bi-file-earmark"></i> Loans
                    </div>
                    <a class="nav-link" href="<?= site_url('/customer/loan/apply') ?>">
                        <i class="bi bi-plus-circle"></i> Apply for Loan
                    </a>
                    <a class="nav-link" href="<?= site_url('/customer/loans') ?>">
                        <i class="bi bi-list-check"></i> My Loans
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="bi bi-shield-check"></i> Verification
                    </div>
                    <a class="nav-link" href="<?= site_url('/customer/kyc') ?>">
                        <i class="bi bi-card-checklist"></i> KYC Verification
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="bi bi-gear"></i> Account
                    </div>
                    <a class="nav-link" href="<?= site_url('/customer/profile') ?>">
                        <i class="bi bi-person-circle"></i> Profile
                    </a>
                    <a class="nav-link" href="<?= site_url('/notifications') ?>">
                        <i class="bi bi-bell"></i> Notifications
                    </a>
                </div>
            </nav>
            
            <!-- Logout Section -->
            <div class="logout-section">
                <a class="nav-link" href="<?= site_url('/logout') ?>">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="customer-main-content w-100">
            <!-- Navbar -->
            <nav class="customer-navbar navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <span class="navbar-brand">
                        <i class="bi bi-list"></i> My Account
                    </span>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle"></i> Account
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?= site_url('/customer/profile') ?>">
                                        <i class="bi bi-person"></i> My Profile
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= site_url('/logout') ?>">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Content Area -->
            <main class="container-fluid">
                <?= $this->renderSection('content') ?>
            </main>

            <!-- Footer -->
            <footer class="text-center">
                <small class="text-muted">
                    &copy; 2025 Yayaman Tayu Lending System. All rights reserved. | 
                    <a href="#" class="text-muted text-decoration-none">Privacy Policy</a> | 
                    <a href="#" class="text-muted text-decoration-none">Terms of Service</a>
                </small>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</body>
</html>