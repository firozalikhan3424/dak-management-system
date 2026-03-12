<?php
require_once __DIR__ . '/../includes/auth.php';
require_roles(['head_clerk','admin']);
$pageTitle = 'Mark DAK';
$activeMenu = 'mark';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    db()->prepare('UPDATE dak_master SET branch_id=:branch_id, sub_branch_id=:sub_branch_id, dak_type=:dak_type, cutoff_date=:cutoff_date, speak_case=:speak_case, status=:status WHERE id=:id AND status=:pending')
        ->execute([
            ':branch_id' => (int)$_POST['branch_id'],
            ':sub_branch_id' => (int)$_POST['sub_branch_id'],
            ':dak_type' => $_POST['dak_type'],
            ':cutoff_date' => $_POST['cutoff_date'],
            ':speak_case' => (int)$_POST['speak_case'],
            ':status' => 'marked',
            ':id' => (int)$_POST['dak_id'],
            ':pending' => 'pending',
        ]);
    header('Location: ' . app_url('user/mark_dak.php'));
    exit;
}

$daks = db()->query("SELECT id, control_no, subject FROM dak_master WHERE status='pending' ORDER BY id DESC")->fetchAll();
$branches = db()->query('SELECT id, branch_name FROM branches WHERE status=1 ORDER BY branch_name')->fetchAll();
$subs = db()->query('SELECT id, sub_branch_name, branch_id FROM sub_branches WHERE status=1 ORDER BY sub_branch_name')->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-panel"><div class="topbar p-3"><h5>Head Clerk Marking Panel</h5></div><main class="p-4"><div class="card p-3">
<form method="post" class="row g-3"><?= csrf_field() ?>
<div class="col-md-12"><label class="form-label">Pending DAK</label><select name="dak_id" class="form-select"><?php foreach($daks as $d): ?><option value="<?= (int)$d['id'] ?>"><?= esc($d['control_no']) ?> - <?= esc($d['subject']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Branch</label><select name="branch_id" class="form-select" required><?php foreach($branches as $b): ?><option value="<?= (int)$b['id'] ?>"><?= esc($b['branch_name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Sub Branch</label><select name="sub_branch_id" class="form-select" required><?php foreach($subs as $s): ?><option value="<?= (int)$s['id'] ?>"><?= esc($s['sub_branch_name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Dak Type</label><select name="dak_type" class="form-select"><option>Normal</option><option>Priority</option></select></div>
<div class="col-md-4"><label class="form-label">Cutoff Date</label><input type="date" name="cutoff_date" required class="form-control"></div>
<div class="col-md-4"><label class="form-label">Speak Case</label><select name="speak_case" class="form-select"><option value="0">No</option><option value="1">Yes</option></select></div>
<div class="col-12"><button class="btn btn-primary">Mark DAK</button></div>
</form>
</div></main></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
