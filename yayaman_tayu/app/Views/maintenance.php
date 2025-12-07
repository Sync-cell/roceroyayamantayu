<?php

// Variables passed from controller/filter: $currentIp, $appName, $supportEmail
$currentIp    = $currentIp ?? service('request')->getIPAddress() ?? ($_SERVER['REMOTE_ADDR'] ?? 'Unknown');
$appName      = $appName ?? 'Yayaman Tayu';
$supportEmail = $supportEmail ?? ($appName ? 'support@' . strtolower(str_replace(' ', '', $appName)) . '.com' : 'support@example.com');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title><?= esc($appName) ?> — Temporarily Unavailable</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root{
    --bg:#f6fbff;
    --card:#ffffff;
    --muted:#6b7280;
    --accent:#4f46e5;
    --danger:#ef4444;
  }
  html,body{height:100%}
  body{
    margin:0;
    font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    background: linear-gradient(180deg, #f8fbff 0%, #eef6ff 100%);
    color:#0f172a;
    -webkit-font-smoothing:antialiased;
    -moz-osx-font-smoothing:grayscale;
  }
  .wrap{
    max-width:1100px;
    margin:6vh auto;
    display:flex;
    gap:28px;
    align-items:stretch;
  }
  .card-left{
    flex:1.05;
    background:var(--card);
    border-radius:14px;
    padding:32px;
    box-shadow:0 10px 30px rgba(15,23,42,0.06);
  }
  .card-right{
    width:360px;
    background:linear-gradient(180deg,#fff 0%, #f8fbff 100%);
    border-radius:14px;
    padding:26px;
    box-shadow:0 6px 20px rgba(15,23,42,0.04);
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
  }
  .badge-status{display:inline-flex;gap:8px;align-items:center;padding:8px 12px;border-radius:999px;background:#fff;border:1px solid #eef2ff}
  h1{font-size:1.6rem;margin-bottom:6px}
  .lead{color:var(--muted);margin-bottom:18px}
  .ipbox{font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, "Roboto Mono", monospace;background:#fbfeff;border:1px solid #e6f0ff;padding:10px 12px;border-radius:8px;display:inline-block}
  .small-muted{color:var(--muted);font-size:0.92rem}
  .features{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-top:14px}
  .feature{background:#fbfeff;border:1px solid #eef6ff;padding:12px;border-radius:10px}
  .btn-home{min-width:150px}
  .logo-small{display:inline-flex;align-items:center;gap:10px}
  .logo-mark{width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg,var(--accent),#7c3aed);display:inline-flex;align-items:center;justify-content:center;color:white;font-weight:700}
  footer{margin-top:20px;text-align:center;color:var(--muted);font-size:0.88rem}
  @media (max-width:960px){
    .wrap{flex-direction:column;padding:18px}
    .card-right{width:100%}
  }
</style>
</head>
<body>
  <div class="wrap">
    <div class="card-left">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
          <div class="logo-small mb-2">
            <div class="logo-mark"><i class="bi bi-building"></i></div>
            <div class="ms-2">
              <div style="font-weight:700"><?= esc($appName) ?></div>
              <div class="small-muted">Service status</div>
            </div>
          </div>
        </div>

        <div class="text-end">
          <div class="badge-status">
            <i class="bi bi-wrench text-danger"></i>
            <div>
              <div style="font-weight:600">Under Maintenance</div>
              <div style="font-size:0.82rem;color:var(--muted)">Public access paused</div>
            </div>
          </div>
        </div>
      </div>

      <h1>We'll be back shortly</h1>
      <p class="lead">We're performing scheduled maintenance to improve the system. Public access is temporarily disabled. Administrators and whitelisted IP addresses can still access the site.</p>

      <div class="features">
        <div class="feature">
          <strong>Estimated time</strong>
          <div class="small-muted">Expected to finish within a few minutes. If longer, contact support.</div>
        </div>
        <div class="feature">
          <strong>Why this is happening</strong>
          <div class="small-muted">System updates, security patching, or data migration to improve reliability.</div>
        </div>
        <div class="feature">
          <strong>Admin access</strong>
          <div class="small-muted">Admins with active sessions or whitelisted IPs bypass this page.</div>
        </div>
        <div class="feature">
          <strong>Need help?</strong>
          <div class="small-muted">Email: <a href="mailto:<?= esc($supportEmail) ?>"><?= esc($supportEmail) ?></a></div>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <a class="btn btn-outline-primary btn-home" href="<?= site_url('/') ?>"><i class="bi bi-house-door me-2"></i>Home</a>
        <a class="btn btn-outline-secondary" href="javascript:void(0)" onclick="copyIp()"><i class="bi bi-clipboard me-1"></i>Copy my IP</a>
        <a class="btn btn-outline-success" href="<?= site_url('/login') ?>"><i class="bi bi-person-circle me-1"></i>Admin Login</a>
      </div>

      <footer>
        &copy; <?= date('Y') ?> <?= esc($appName) ?> — We apologize for the inconvenience.
      </footer>
    </div>

    <div class="card-right text-center">
      <div style="max-width:300px">
        <div class="mb-3">
          <div style="font-size:32px;color:var(--danger)"><i class="bi bi-shield-exclamation-fill"></i></div>
        </div>

        <h5 class="mb-1">Your IP address</h5>
        <div class="ipbox mb-2"><?= esc($currentIp) ?></div>
        <div class="small-muted mb-3">If you enabled maintenance from this IP it will be automatically whitelisted.</div>

        <div class="d-grid gap-2">
          <button class="btn btn-primary" onclick="copyIp()"><i class="bi bi-clipboard me-1"></i>Copy my IP</button>
          <a class="btn btn-outline-dark" href="mailto:<?= esc($supportEmail) ?>"><i class="bi bi-envelope me-1"></i>Contact Support</a>
        </div>
      </div>
    </div>
  </div>

<script>
  function copyIp() {
    const ip = <?= json_encode($currentIp) ?>;
    if (!ip) return;
    if (navigator.clipboard) {
      navigator.clipboard.writeText(ip).then(()=> {
        alert('IP copied to clipboard');
      }).catch(()=> {
        fallbackCopy(ip);
      });
    } else {
      fallbackCopy(ip);
    }

    function fallbackCopy(text) {
      const el = document.createElement('textarea');
      el.value = text;
      document.body.appendChild(el);
      el.select();
      document.execCommand('copy');
      document.body.removeChild(el);
      alert('IP copied to clipboard');
    }
  }
</script>
</body>
</html>
