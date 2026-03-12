<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
ensure_role(['head_clerk','admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $stmt = db()->prepare('UPDATE dak_master SET branch_id=:branch_id, sub_branch_id=:sub_branch_id, dak_type=:dak_type, speak_case=:speak_case, cutoff_date=:cutoff_date, status=:status WHERE id=:id');
    $stmt->execute([
        ':branch_id' => (int)$_POST['branch_id'],
        ':sub_branch_id' => (int)$_POST['sub_branch_id'],
        ':dak_type' => $_POST['dak_type'],
        ':speak_case' => ($_POST['speak_case'] ?? '0') === '1' ? 1 : 0,
        ':cutoff_date' => $_POST['cutoff_date'],
        ':status' => 'marked',
        ':id' => (int)$_POST['dak_id'],
    ]);
    audit_log((int)current_user()['id'], 'head_clerk', 'mark_dak');
    flash('success', 'DAK marked successfully');
    redirect('modules/head_clerk/mark_dak.php');
}

$daks = db()->query("SELECT id, control_no, subject FROM dak_master WHERE status='pending' ORDER BY id DESC")->fetchAll();
$branches = get_branches();
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="main-content"><?php include __DIR__ . '/../../includes/navbar.php'; ?>
<main class="container-fluid p-4">
<div class="card p-4"><h5>DAK Marking</h5>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<form method="post" class="row g-3"><?= csrf_input() ?>
<div class="col-md-6"><label>Pending DAK</label><select name="dak_id" class="form-select"><?php foreach($daks as $d): ?><option value="<?= (int)$d['id'] ?>"><?= e($d['control_no'].' - '.$d['subject']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><label>Branch</label><select name="branch_id" class="form-select"><?php foreach($branches as $b): ?><option value="<?= (int)$b['id'] ?>"><?= e($b['branch_name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><label>Sub Branch ID</label><input type="number" name="sub_branch_id" class="form-control" required></div>
<div class="col-md-3"><label>DAK Type</label><select name="dak_type" class="form-select"><option>Normal</option><option>Priority</option></select></div>
<div class="col-md-3"><label>Speak Case</label><select name="speak_case" class="form-select"><option value="0">No</option><option value="1">Yes</option></select></div>
<div class="col-md-3"><label>Cutoff Date</label><input type="date" name="cutoff_date" class="form-control" required></div>
<div class="col-12"><button class="btn btn-primary">Mark DAK</button></div>
</form></div>
</main></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
