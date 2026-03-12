<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
ensure_authenticated();

$totals = [
    'incoming' => (int)db()->query('SELECT COUNT(*) c FROM dak_master')->fetch()['c'],
    'pending' => (int)db()->query("SELECT COUNT(*) c FROM dak_master WHERE status = 'pending'")->fetch()['c'],
    'speak' => (int)db()->query('SELECT COUNT(*) c FROM dak_master WHERE speak_case = 1')->fetch()['c'],
    'overdue' => (int)db()->query("SELECT COUNT(*) c FROM dak_master WHERE cutoff_date < CURDATE() AND status <> 'closed'")->fetch()['c'],
];

$monthly = db()->query("SELECT DATE_FORMAT(receipt_date, '%b') label, COUNT(*) total FROM dak_master WHERE YEAR(receipt_date)=YEAR(CURDATE()) GROUP BY MONTH(receipt_date) ORDER BY MONTH(receipt_date)")->fetchAll();
$labels = array_column($monthly, 'label');
$values = array_map('intval', array_column($monthly, 'total'));

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="main-content">
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>
    <main class="container-fluid p-4">
        <div class="row g-3 mb-3">
            <div class="col-md-3"><div class="card p-3"><small>Total Incoming DAK</small><h3><?= $totals['incoming'] ?></h3></div></div>
            <div class="col-md-3"><div class="card p-3"><small>Pending DAK</small><h3><?= $totals['pending'] ?></h3></div></div>
            <div class="col-md-3"><div class="card p-3"><small>Speak Cases</small><h3><?= $totals['speak'] ?></h3></div></div>
            <div class="col-md-3"><div class="card p-3"><small>Overdue DAK</small><h3 class="text-danger"><?= $totals['overdue'] ?></h3></div></div>
        </div>
        <div class="card p-3">
            <h5>Monthly DAK Statistics</h5>
            <canvas id="monthlyChart" height="100"></canvas>
        </div>
    </main>
</div>
<script>window.dashboardMonthlyData = <?= json_encode(['labels' => $labels, 'values' => $values], JSON_THROW_ON_ERROR) ?>;</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
