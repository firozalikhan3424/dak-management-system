<?php
require_once __DIR__ . '/../includes/auth.php';
require_roles(['dispatcher','head_clerk','branch_clerk','officer','co','admin']);
$pageTitle = 'User Dashboard';
$activeMenu = 'user_dashboard';

$user = auth_user();
$branchFilter = '';
$params = [];
if (in_array($user['role'], ['branch_clerk','officer'], true) && $user['branch_id']) {
    $branchFilter = ' AND branch_id=:branch_id';
    $params[':branch_id'] = $user['branch_id'];
}

$stmt = db()->prepare("SELECT COUNT(*) c FROM dak_master WHERE 1=1 {$branchFilter}"); $stmt->execute($params); $total = (int)$stmt->fetch()['c'];
$stmt = db()->prepare("SELECT COUNT(*) c FROM dak_master WHERE status<>'closed' {$branchFilter}"); $stmt->execute($params); $pending = (int)$stmt->fetch()['c'];
$stmt = db()->prepare("SELECT COUNT(*) c FROM dak_master WHERE speak_case=1 {$branchFilter}"); $stmt->execute($params); $speak = (int)$stmt->fetch()['c'];
$stmt = db()->prepare("SELECT COUNT(*) c FROM dak_master WHERE cutoff_date < CURDATE() AND status<>'closed' {$branchFilter}"); $stmt->execute($params); $overdue = (int)$stmt->fetch()['c'];

$monthly = db()->query("SELECT DATE_FORMAT(receipt_date,'%b') m, COUNT(*) c FROM dak_master WHERE YEAR(receipt_date)=YEAR(CURDATE()) GROUP BY MONTH(receipt_date) ORDER BY MONTH(receipt_date)")->fetchAll();
$labels = array_column($monthly, 'm');
$values = array_map('intval', array_column($monthly, 'c'));

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-panel">
    <div class="topbar p-3 d-flex justify-content-between"><h5>Operational Dashboard</h5><span><?= esc($user['name']) ?> (<?= esc($user['role']) ?>)</span></div>
    <main class="p-4">
        <div class="row g-3 mb-3">
            <div class="col-md-3"><div class="card p-3 kpi-card"><small>Total Incoming DAK</small><h2><?= $total ?></h2></div></div>
            <div class="col-md-3"><div class="card p-3 kpi-card"><small>Pending DAK</small><h2><?= $pending ?></h2></div></div>
            <div class="col-md-3"><div class="card p-3 kpi-card"><small>Speak Cases</small><h2><?= $speak ?></h2></div></div>
            <div class="col-md-3"><div class="card p-3 kpi-card"><small>Overdue DAK</small><h2 class="text-danger"><?= $overdue ?></h2></div></div>
        </div>
        <div class="card p-3"><h6>Monthly Incoming Trend</h6><canvas id="dakChart" height="90"></canvas></div>
    </main>
</div>
<script>window.dashboardStats = <?= json_encode(['labels' => $labels, 'values' => $values], JSON_THROW_ON_ERROR) ?>;</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
