<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
require_once __DIR__ . '/../../helpers/validation_helper.php';
ensure_role(['branch_clerk','officer','admin']);

$dakId = (int)($_GET['dak_id'] ?? $_POST['dak_id'] ?? 0);
$dakStmt = db()->prepare('SELECT d.*, sb.file_start, sb.file_end FROM dak_master d LEFT JOIN sub_branches sb ON sb.id = d.sub_branch_id WHERE d.id=:id');
$dakStmt->execute([':id' => $dakId]);
$dak = $dakStmt->fetch();
if (!$dak) { exit('DAK not found'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $fileNo = (int)$_POST['file_no'];
    if (!validate_file_no_in_range($fileNo, (int)$dak['file_start'], (int)$dak['file_end'])) {
        flash('error', sprintf('File number must be in range %d-%d', (int)$dak['file_start'], (int)$dak['file_end']));
        redirect('modules/branch/branch_action.php?dak_id=' . $dakId);
    }

    $stmt = db()->prepare('INSERT INTO dak_action (dak_id, file_no, action_taken, action_date, reply_ref, remarks, updated_by) VALUES (:dak_id,:file_no,:action_taken,:action_date,:reply_ref,:remarks,:updated_by)');
    $stmt->execute([
        ':dak_id' => $dakId,
        ':file_no' => $fileNo,
        ':action_taken' => trim($_POST['action_taken']),
        ':action_date' => $_POST['action_date'],
        ':reply_ref' => trim($_POST['reply_ref']),
        ':remarks' => trim($_POST['remarks']),
        ':updated_by' => current_user()['id'],
    ]);

    $refs = array_filter(array_map('trim', explode(',', $_POST['references'] ?? '')));
    $refStmt = db()->prepare('INSERT INTO dak_references (dak_id, reference_no) VALUES (:dak_id,:reference_no)');
    foreach ($refs as $ref) {
        $refStmt->execute([':dak_id' => $dakId, ':reference_no' => $ref]);
    }

    db()->prepare("UPDATE dak_master SET status='in_progress' WHERE id=:id")->execute([':id' => $dakId]);
    audit_log((int)current_user()['id'], 'branch', 'dak_action');
    flash('success', 'Action saved successfully.');
    redirect('modules/branch/branch_dashboard.php');
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="main-content"><?php include __DIR__ . '/../../includes/navbar.php'; ?>
<main class="container-fluid p-4"><div class="card p-4"><h5>Branch Action - <?= e($dak['control_no']) ?></h5>
<?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
<form method="post" class="row g-3"><?= csrf_input() ?><input type="hidden" name="dak_id" value="<?= $dakId ?>">
<div class="col-md-4"><label>File Number</label><input type="number" name="file_no" class="form-control" required></div>
<div class="col-md-8"><label>Action Taken</label><input name="action_taken" class="form-control" required></div>
<div class="col-md-4"><label>Action Date</label><input type="date" name="action_date" class="form-control" required></div>
<div class="col-md-4"><label>Reply Reference</label><input name="reply_ref" class="form-control"></div>
<div class="col-md-4"><label>Backward References</label><input name="references" placeholder="BR1,BR2,BR3" class="form-control"></div>
<div class="col-12"><label>Remarks</label><textarea name="remarks" class="form-control"></textarea></div>
<div class="col-12"><button class="btn btn-primary">Save Action</button></div>
</form></div></main></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
