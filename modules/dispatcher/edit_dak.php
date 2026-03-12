<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
ensure_role(['dispatcher','admin']);

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM dak_master WHERE id=:id');
$stmt->execute([':id' => $id]);
$dak = $stmt->fetch();
if (!$dak) { exit('Record not found'); }
if ($dak['status'] !== 'pending') { exit('Marked DAK cannot be modified by dispatcher.'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $update = db()->prepare('UPDATE dak_master SET subject=:subject, originator=:originator WHERE id=:id');
    $update->execute([':subject' => trim($_POST['subject']), ':originator' => trim($_POST['originator']), ':id' => $id]);
    audit_log((int)current_user()['id'], 'dispatcher', 'edit_dak', json_encode($dak), json_encode($_POST));
    redirect('modules/dispatcher/dak_list.php');
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="main-content"><?php include __DIR__ . '/../../includes/navbar.php'; ?>
<main class="container-fluid p-4"><div class="card p-4"><h5>Edit DAK</h5>
<form method="post"><?= csrf_input() ?>
<div class="mb-3"><label>Originator</label><input name="originator" value="<?= e($dak['originator']) ?>" class="form-control"></div>
<div class="mb-3"><label>Subject</label><input name="subject" value="<?= e($dak['subject']) ?>" class="form-control"></div>
<button class="btn btn-primary">Update</button>
</form></div></main></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
