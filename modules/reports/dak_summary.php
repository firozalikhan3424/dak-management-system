<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
ensure_role(['admin','co','head_clerk']);

$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');
$branch = (int)($_GET['branch_id'] ?? 0);
$params = [':from' => $from, ':to' => $to];
$where = 'WHERE d.receipt_date BETWEEN :from AND :to';
if ($branch > 0) {
    $where .= ' AND d.branch_id = :branch_id';
    $params[':branch_id'] = $branch;
}
$sql = "SELECT b.branch_name, COUNT(*) total, SUM(d.speak_case=1) speak_cases, SUM(d.cutoff_date < CURDATE() AND d.status <> 'closed') overdue FROM dak_master d LEFT JOIN branches b ON b.id = d.branch_id {$where} GROUP BY d.branch_id ORDER BY total DESC";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
$branches = get_branches();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="main-content"><?php include __DIR__ . '/../../includes/navbar.php'; ?>
<main class="container-fluid p-4"><div class="card p-4"><h5>DAK Summary Report</h5>
<form class="row g-2 mb-3"><div class="col"><input type="date" name="from" value="<?= e($from) ?>" class="form-control"></div><div class="col"><input type="date" name="to" value="<?= e($to) ?>" class="form-control"></div><div class="col"><select name="branch_id" class="form-select"><option value="0">All Branches</option><?php foreach($branches as $b): ?><option value="<?= $b['id'] ?>" <?= $branch===$b['id']?'selected':'' ?>><?= e($b['branch_name']) ?></option><?php endforeach; ?></select></div><div class="col"><button class="btn btn-primary">Filter</button></div></form>
<table class="table"><thead><tr><th>Branch</th><th>Total</th><th>Speak Cases</th><th>Overdue</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= e($r['branch_name'] ?? 'Unassigned') ?></td><td><?= (int)$r['total'] ?></td><td><?= (int)$r['speak_cases'] ?></td><td class="text-danger"><?= (int)$r['overdue'] ?></td></tr><?php endforeach; ?></tbody></table>
<a class="btn btn-outline-secondary" href="pending_report.php">Pending >7 Days</a>
<a class="btn btn-outline-secondary" href="branch_report.php">Branch Wise Pending</a>
<a class="btn btn-outline-secondary" href="speak_cases.php">Speak Cases</a>
</div></main></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
