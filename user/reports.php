<?php
require_once __DIR__ . '/../includes/auth.php';
require_roles(['admin','head_clerk','officer','co','branch_clerk']);
$pageTitle = 'Reports';
$activeMenu = 'reports';

$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');
$branchId = (int)($_GET['branch_id'] ?? 0);
$subBranchId = (int)($_GET['sub_branch_id'] ?? 0);

$where = ' WHERE d.receipt_date BETWEEN :from AND :to';
$params = [':from' => $from, ':to' => $to];
if ($branchId > 0) { $where .= ' AND d.branch_id=:branch_id'; $params[':branch_id'] = $branchId; }
if ($subBranchId > 0) { $where .= ' AND d.sub_branch_id=:sub_branch_id'; $params[':sub_branch_id'] = $subBranchId; }

$sql = "SELECT d.control_no,d.letter_no,d.originator,d.subject,d.receipt_date,d.cutoff_date,d.status,d.speak_case,b.branch_name,sb.sub_branch_name FROM dak_master d LEFT JOIN branches b ON b.id=d.branch_id LEFT JOIN sub_branches sb ON sb.id=d.sub_branch_id {$where} ORDER BY d.receipt_date DESC";
$stmt = db()->prepare($sql); $stmt->execute($params); $rows = $stmt->fetchAll();

if (($_GET['export'] ?? '') === 'excel') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="dak_report.csv"');
    $out = fopen('php://output', 'wb');
    fputcsv($out, ['Control', 'Letter', 'Originator', 'Subject', 'Branch', 'Sub Branch', 'Receipt Date', 'Cutoff', 'Status']);
    foreach ($rows as $r) {
        fputcsv($out, [$r['control_no'], $r['letter_no'], $r['originator'], $r['subject'], $r['branch_name'], $r['sub_branch_name'], $r['receipt_date'], $r['cutoff_date'], $r['status']]);
    }
    fclose($out);
    exit;
}

$branches = db()->query('SELECT id, branch_name FROM branches WHERE status=1 ORDER BY branch_name')->fetchAll();
$subs = db()->query('SELECT id, sub_branch_name FROM sub_branches WHERE status=1 ORDER BY sub_branch_name')->fetchAll();

$pending7 = array_filter($rows, static fn($r) => strtotime($r['receipt_date']) < strtotime('-7 days') && $r['status'] !== 'closed');
$speakCases = array_filter($rows, static fn($r) => (int)$r['speak_case'] === 1);
$replyPending = db()->prepare("SELECT d.control_no,d.subject FROM dak_master d LEFT JOIN dak_action a ON a.dak_id=d.id {$where} AND (a.reply_ref IS NULL OR a.reply_ref='')");
$replyPending->execute($params);
$replyRows = $replyPending->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-panel"><div class="topbar p-3"><h5>Reports & Monitoring</h5></div><main class="p-4">
<div class="card p-3 mb-3"><form class="row g-2"><div class="col-md-2"><input type="date" name="from" value="<?= esc($from) ?>" class="form-control"></div><div class="col-md-2"><input type="date" name="to" value="<?= esc($to) ?>" class="form-control"></div><div class="col-md-3"><select name="branch_id" class="form-select"><option value="0">All Branches</option><?php foreach($branches as $b): ?><option value="<?= (int)$b['id'] ?>" <?= $branchId===(int)$b['id']?'selected':'' ?>><?= esc($b['branch_name']) ?></option><?php endforeach; ?></select></div><div class="col-md-3"><select name="sub_branch_id" class="form-select"><option value="0">All Sub Branches</option><?php foreach($subs as $s): ?><option value="<?= (int)$s['id'] ?>" <?= $subBranchId===(int)$s['id']?'selected':'' ?>><?= esc($s['sub_branch_name']) ?></option><?php endforeach; ?></select></div><div class="col-md-2"><button class="btn btn-primary w-100">Apply</button></div></form>
<div class="mt-2 d-flex gap-2"><a class="btn btn-outline-success btn-sm" href="?<?= http_build_query(array_merge($_GET,['export'=>'excel'])) ?>">Export Excel</a><button onclick="window.print()" class="btn btn-outline-danger btn-sm">Export PDF</button></div></div>

<div class="row g-3 mb-3"><div class="col-md-4"><div class="card p-3"><h6>Pending DAK</h6><div><?= count(array_filter($rows, static fn($r) => $r['status'] !== 'closed')) ?></div></div></div><div class="col-md-4"><div class="card p-3"><h6>Speak Cases</h6><div><?= count($speakCases) ?></div></div></div><div class="col-md-4"><div class="card p-3"><h6>Pending > 7 Days</h6><div><?= count($pending7) ?></div></div></div></div>

<div class="card p-3 mb-3"><h6>Date Wise Incoming / Branch Pending / Dak Summary</h6><table class="table table-sm"><thead><tr><th>Control</th><th>Originator</th><th>Subject</th><th>Branch</th><th>Sub Branch</th><th>Status</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= esc($r['control_no']) ?></td><td><?= esc($r['originator']) ?></td><td><?= esc($r['subject']) ?></td><td><?= esc($r['branch_name'] ?? '-') ?></td><td><?= esc($r['sub_branch_name'] ?? '-') ?></td><td><?= esc($r['status']) ?></td></tr><?php endforeach; ?></tbody></table></div>

<div class="card p-3"><h6>Reply Pending</h6><table class="table table-sm"><tr><th>Control</th><th>Subject</th></tr><?php foreach($replyRows as $r): ?><tr><td><?= esc($r['control_no']) ?></td><td><?= esc($r['subject']) ?></td></tr><?php endforeach; ?></table></div>
</main></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
