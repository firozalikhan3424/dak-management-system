<?php
require_once __DIR__ . '/../includes/auth.php';
require_roles(['branch_clerk','officer','admin']);
$pageTitle = 'Branch Action';
$activeMenu = 'branch_action';
$user = auth_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $dakId = (int)$_POST['dak_id'];

    $range = db()->prepare('SELECT sb.file_start, sb.file_end FROM dak_master d JOIN sub_branches sb ON sb.id=d.sub_branch_id WHERE d.id=:id');
    $range->execute([':id' => $dakId]);
    $r = $range->fetch();
    $fileNo = (int)$_POST['file_no'];
    if (!$r || $fileNo < (int)$r['file_start'] || $fileNo > (int)$r['file_end']) {
        flash('flash_error', 'Invalid file number for assigned sub branch range.');
        header('Location: /user/branch_action.php?dak_id=' . $dakId);
        exit;
    }

    db()->prepare('INSERT INTO dak_action (dak_id,file_no,action_taken,action_date,reply_ref,remarks,updated_by) VALUES (:dak_id,:file_no,:action_taken,:action_date,:reply_ref,:remarks,:updated_by)')
        ->execute([
            ':dak_id' => $dakId,
            ':file_no' => $fileNo,
            ':action_taken' => trim($_POST['action_taken']),
            ':action_date' => $_POST['action_date'],
            ':reply_ref' => trim($_POST['reply_ref']),
            ':remarks' => trim($_POST['remarks']),
            ':updated_by' => $user['id'],
        ]);

    $refs = array_filter(array_map('trim', explode(',', $_POST['references'] ?? '')));
    $rStmt = db()->prepare('INSERT INTO dak_references (dak_id, reference_no) VALUES (:dak_id,:reference_no)');
    foreach ($refs as $ref) {
        $rStmt->execute([':dak_id' => $dakId, ':reference_no' => $ref]);
    }

    db()->prepare("UPDATE dak_master SET status='in_progress' WHERE id=:id")->execute([':id' => $dakId]);
    header('Location: /user/branch_action.php');
    exit;
}

$where = "WHERE d.status IN ('marked','in_progress')";
$params = [];
if ($user['role'] !== 'admin') {
    $where .= ' AND d.branch_id=:branch_id';
    $params[':branch_id'] = $user['branch_id'];
}
$list = db()->prepare("SELECT d.id,d.control_no,d.subject,d.originator,d.cutoff_date,d.dak_type,d.speak_case,sb.sub_branch_name,sb.file_start,sb.file_end FROM dak_master d LEFT JOIN sub_branches sb ON sb.id=d.sub_branch_id {$where} ORDER BY d.cutoff_date ASC");
$list->execute($params);
$rows = $list->fetchAll();

$selectedId = (int)($_GET['dak_id'] ?? ($rows[0]['id'] ?? 0));
$selected = null;
foreach ($rows as $row) {
    if ((int)$row['id'] === $selectedId) {
        $selected = $row;
        break;
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-panel"><div class="topbar p-3"><h5>Branch Clerk Action Desk</h5></div><main class="p-4">
<?php if ($err = flash('flash_error')): ?><div class="alert alert-danger"><?= esc($err) ?></div><?php endif; ?>
<div class="row g-3">
<div class="col-lg-7"><div class="card p-3"><h6>Assigned DAK</h6><table class="table table-sm align-middle"><thead><tr><th>Control</th><th>Subject</th><th>Priority</th><th>Speak</th><th>Cutoff</th><th></th></tr></thead><tbody><?php foreach($rows as $r): ?><tr class="<?= ($r['cutoff_date'] && strtotime($r['cutoff_date']) < time()) ? 'overdue-row' : '' ?>"><td><?= esc($r['control_no']) ?></td><td><?= esc($r['subject']) ?></td><td><span class="badge <?= $r['dak_type']==='Priority' ? 'badge-priority' : 'bg-secondary' ?>"><?= esc((string)$r['dak_type']) ?></span></td><td><?= $r['speak_case'] ? '<span class="badge badge-speak">Speak</span>' : '-' ?></td><td><?= esc((string)$r['cutoff_date']) ?></td><td><a class="btn btn-sm btn-outline-primary" href="?dak_id=<?= (int)$r['id'] ?>">Open</a></td></tr><?php endforeach; ?></tbody></table></div></div>
<div class="col-lg-5"><div class="card p-3"><h6>Action Form</h6><?php if ($selected): ?><p class="text-muted">Range for <?= esc($selected['sub_branch_name'] ?? 'sub-branch') ?>: <?= (int)$selected['file_start'] ?> - <?= (int)$selected['file_end'] ?></p><form method="post" class="row g-2"><?= csrf_field() ?><input type="hidden" name="dak_id" value="<?= (int)$selected['id'] ?>"><div class="col-6"><input type="number" name="file_no" class="form-control" placeholder="File Number" required></div><div class="col-6"><input type="date" name="action_date" class="form-control" required></div><div class="col-12"><input name="action_taken" class="form-control" placeholder="Action Taken" required></div><div class="col-12"><input name="reply_ref" class="form-control" placeholder="Reply Reference"></div><div class="col-12"><input name="references" class="form-control" placeholder="BR1, BR2, BR3"></div><div class="col-12"><textarea name="remarks" class="form-control" placeholder="Remarks"></textarea></div><div class="col-12"><button class="btn btn-primary w-100">Save Action</button></div></form><?php else: ?><p class="text-muted">No assigned DAK found.</p><?php endif; ?></div></div>
</div>
</main></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
