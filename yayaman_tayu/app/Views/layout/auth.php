
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= isset($title) ? esc($title) . ' - ' : '' ?>Yayaman Tayu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body{background:#f4f6fb;height:100vh;display:flex;align-items:center;justify-content:center;font-family:Inter,system-ui,Segoe UI,Roboto,Helvetica,Arial}
        .auth-card{max-width:420px;width:100%;background:#fff;border-radius:10px;box-shadow:0 6px 24px rgba(18,38,63,0.08);padding:28px}
        .brand{display:flex;align-items:center;gap:.6rem;margin-bottom:1rem}
        .brand .logo{width:44px;height:44px;border-radius:8px;background:linear-gradient(135deg,#667eea,#764ba2);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700}
        .small-muted{color:#6c757d;font-size:.9rem}
        footer.auth-foot{margin-top:18px;text-align:center;color:#98a0b3;font-size:.875rem}
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="brand">
            <div class="logo"><i class="bi bi-bank2"></i></div>
            <div>
                <div style="font-weight:700">Yayaman Tayu</div>
                <div class="small-muted">Financial services platform</div>
            </div>
        </div>

        <?= $this->renderSection('content') ?>

        <footer class="auth-foot">
            &copy; <?= date('Y') ?> Yayaman Tayu. All rights reserved.
        </footer>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>