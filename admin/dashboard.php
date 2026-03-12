<?php
require_once __DIR__ . '/../includes/auth.php';
require_roles(['admin']);
$pageTitle = 'Admin Dashboard';
$activeMenu = 'admin_dashboard';

$stats = [
    'users' => (int)db()->query('SELECT COUNT(*) c FROM users')->fetch()['c'],
    'branches' => (int)db()->query('SELECT COUNT(*) c FROM branches WHERE status = 1')->fetch()['c'],
    'dak' => (int)db()->query('SELECT COUNT(*) c FROM dak_master')->fetch()['c'],
    'pending' => (int)db()->query("SELECT COUNT(*) c FROM dak_master WHERE status <> 'closed'")->fetch()['c'],
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-panel">
    <div class="topbar p-3 d-flex justify-content-between"><h5>Admin Panel</h5><span><?= esc(auth_user()['name']) ?></span></div>
    <main class="p-4">
        <div class="row g-3">
            <div class="col-md-3"><div class="card p-3 kpi-card"><small>Total Users</small><h2><?= $stats['users'] ?></h2></div></div>
            <div class="col-md-3"><div class="card p-3 kpi-card"><small>Active Branches</small><h2><?= $stats['branches'] ?></h2></div></div>
            <div class="col-md-3"><div class="card p-3 kpi-card"><small>Total DAK</small><h2><?= $stats['dak'] ?></h2></div></div>
            <div class="col-md-3"><div class="card p-3 kpi-card"><small>Pending DAK</small><h2><?= $stats['pending'] ?></h2></div></div>
        </div>
    </main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
