<?php $flash = take_flash(); $u = Auth::user(); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Hotel Manager') ?></title>
<style>
:root { --bg:#0f172a; --panel:#1e293b; --line:#334155; --text:#e2e8f0; --muted:#94a3b8; --accent:#38bdf8; --ok:#22c55e; --warn:#f59e0b; --bad:#ef4444; }
* { box-sizing: border-box; }
body { margin:0; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background:#f8fafc; color:#0f172a; }
.app { display:flex; min-height:100vh; }
.sidebar { width:230px; background:var(--bg); color:var(--text); padding:20px 0; flex-shrink:0; }
.sidebar h1 { font-size:18px; margin:0 20px 16px; color:#fff; letter-spacing:0.5px; }
.sidebar .who { font-size:12px; color:var(--muted); margin:0 20px 20px; }
.sidebar nav a { display:block; padding:10px 20px; color:var(--text); text-decoration:none; font-size:14px; border-left:3px solid transparent; }
.sidebar nav a:hover { background:#0b1220; }
.sidebar nav a.active { background:#0b1220; border-left-color:var(--accent); color:#fff; }
.sidebar .group { font-size:11px; text-transform:uppercase; color:var(--muted); padding:14px 20px 6px; letter-spacing:1px; }
.main { flex:1; padding:24px 32px; overflow:auto; }
.topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
h2 { margin:0; font-size:22px; }
.btn { display:inline-block; padding:8px 14px; border-radius:6px; background:#0f172a; color:#fff; text-decoration:none; font-size:14px; border:0; cursor:pointer; }
.btn:hover { background:#1e293b; }
.btn-primary { background:#0284c7; }
.btn-primary:hover { background:#0369a1; }
.btn-danger { background:#dc2626; }
.btn-danger:hover { background:#b91c1c; }
.btn-success { background:#16a34a; }
.btn-success:hover { background:#15803d; }
.btn-sm { padding:5px 9px; font-size:12px; }
.card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:18px; margin-bottom:18px; box-shadow:0 1px 2px rgba(0,0,0,0.04); }
.card h3 { margin:0 0 12px; font-size:16px; }
table { width:100%; border-collapse:collapse; }
th, td { text-align:left; padding:10px 12px; border-bottom:1px solid #e2e8f0; font-size:14px; }
th { background:#f1f5f9; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; color:#475569; }
tr:hover td { background:#f8fafc; }
.badge { display:inline-block; padding:3px 8px; border-radius:12px; font-size:11px; text-transform:uppercase; font-weight:600; letter-spacing:0.5px; }
.badge-ok { background:#dcfce7; color:#166534; }
.badge-warn { background:#fef3c7; color:#92400e; }
.badge-bad { background:#fee2e2; color:#991b1b; }
.badge-info { background:#dbeafe; color:#1e40af; }
.badge-muted { background:#e2e8f0; color:#475569; }
form .row { margin-bottom:14px; }
label { display:block; font-size:13px; font-weight:600; margin-bottom:5px; color:#334155; }
input[type=text], input[type=email], input[type=password], input[type=number], input[type=date], select, textarea {
  width:100%; padding:9px 11px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; font-family:inherit;
}
input:focus, select:focus, textarea:focus { outline:0; border-color:var(--accent); box-shadow:0 0 0 3px rgba(56,189,248,0.2); }
.grid { display:grid; gap:16px; }
.grid-2 { grid-template-columns:repeat(2, 1fr); }
.grid-3 { grid-template-columns:repeat(3, 1fr); }
.grid-4 { grid-template-columns:repeat(4, 1fr); }
@media (max-width:760px) { .grid-2,.grid-3,.grid-4 { grid-template-columns:1fr; } .sidebar { width:60px; } .sidebar h1, .sidebar .who, .sidebar .group, .sidebar nav a span { display:none; } }
.stat { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:18px; }
.stat .label { font-size:12px; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; }
.stat .value { font-size:28px; font-weight:700; margin-top:6px; color:#0f172a; }
.stat .sub { font-size:12px; color:#64748b; margin-top:4px; }
.flash { padding:12px 16px; border-radius:6px; margin-bottom:16px; font-size:14px; }
.flash-success { background:#dcfce7; color:#166534; border:1px solid #86efac; }
.flash-error { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
.flash-info { background:#dbeafe; color:#1e40af; border:1px solid #93c5fd; }
.actions { display:flex; gap:6px; }
.muted { color:#64748b; font-size:13px; }
.empty { text-align:center; padding:30px; color:#64748b; }
.right { text-align:right; }
.center { text-align:center; }
.flex-between { display:flex; justify-content:space-between; align-items:center; }
.login-wrap { min-height:100vh; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#0f172a,#1e3a8a); }
.login-card { background:#fff; padding:32px; border-radius:10px; box-shadow:0 10px 40px rgba(0,0,0,0.2); width:360px; }
.login-card h1 { margin:0 0 6px; font-size:22px; }
.login-card .hint { font-size:12px; color:#64748b; margin-bottom:18px; }
.login-card .seed { font-size:11px; color:#64748b; margin-top:14px; padding:10px; background:#f1f5f9; border-radius:6px; line-height:1.6; }
</style>
</head>
<body>
<?php if (!Auth::check() || ($noLayout ?? false)): ?>
<?= $content ?>
<?php else: ?>
<div class="app">
  <aside class="sidebar">
    <h1>Hotel Manager</h1>
    <div class="who"><?= e($u['name']) ?> &middot; <?= e(ucfirst($u['role'])) ?></div>
    <nav>
      <a href="/" class="<?= active('/') ?>"><span>Dashboard</span></a>
      <div class="group">Operations</div>
      <a href="/bookings" class="<?= active('/bookings') ?>"><span>Bookings</span></a>
      <a href="/calendar" class="<?= active('/calendar') ?>"><span>Calendar</span></a>
      <a href="/clients" class="<?= active('/clients') ?>"><span>Clients</span></a>
      <a href="/invoices" class="<?= active('/invoices') ?>"><span>Invoices</span></a>
      <a href="/rooms" class="<?= active('/rooms') ?>"><span>Rooms</span></a>
      <?php if (Auth::isAdmin()): ?>
        <div class="group">Admin</div>
        <a href="/staff" class="<?= active('/staff') ?>"><span>Staff</span></a>
        <a href="/reports" class="<?= active('/reports') ?>"><span>Reports</span></a>
      <?php endif; ?>
      <div class="group">Account</div>
      <a href="/profile" class="<?= active('/profile') ?>"><span>My Profile</span></a>
      <a href="/logout"><span>Logout</span></a>
    </nav>
  </aside>
  <main class="main">
    <?php foreach ($flash as $type => $msg): ?>
      <div class="flash flash-<?= e($type) ?>"><?= e($msg) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
  </main>
</div>
<?php endif; ?>
</body>
</html>
