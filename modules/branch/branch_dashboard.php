<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
ensure_role(['branch_clerk','officer','admin']);

$user = current_user();
$stmt = db()->prepare("SELECT id, control_no, subject, originator, dak_type, speak_case, cutoff_date FROM dak_master WHERE branch_id = :branch_id AND status IN ('marked','in_progress') ORDER BY cutoff_date ASC");
$stmt->execute([':branch_id' => (int)$user['branch_id']]);
$rows = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="main-content"><?php include __DIR__ . '/../../includes/navbar.php'; ?>
<main class="container-fluid p-4"><div class="card p-4"><h5>Assigned DAK</h5>
<table class="table table-sm"><thead><tr><th>Control</th><th>Subject</th><th>Originator</th><th>Priority</th><th>Speak</th><th>Cutoff</th><th></th></tr></thead><tbody>
<?php foreach($rows as $row): ?><tr class="<?= strtotime($row['cutoff_date']) < time() ? 'overdue' : '' ?>">
<td><?= e($row['control_no']) ?></td><td><?= e($row['subject']) ?></td><td><?= e($row['originator']) ?></td>
<td><span class="badge <?= $row['dak_type']==='Priority'?'bg-danger':'bg-secondary' ?>"><?= e($row['dak_type']) ?></span></td>
<td><?= $row['speak_case'] ? '<span class="badge bg-warning text-dark">Speak</span>' : '-' ?></td>
<td><?= e($row['cutoff_date']) ?></td>
<td><a class="btn btn-sm btn-primary" href="branch_action.php?dak_id=<?= (int)$row['id'] ?>">Action</a></td>
</tr><?php endforeach; ?></tbody></table>
</div></main></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
