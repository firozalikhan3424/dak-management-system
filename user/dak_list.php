<?php
require_once __DIR__ . '/../includes/auth.php';
require_roles(['dispatcher','head_clerk','branch_clerk','officer','co','admin']);
$pageTitle = 'DAK List';
$activeMenu = 'dak_list';

$user = auth_user();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['role'] === 'dispatcher') {
    verify_csrf_token();
    db()->prepare('UPDATE dak_master SET subject=:subject, originator=:originator WHERE id=:id AND status=:status')
        ->execute([':subject' => trim($_POST['subject']), ':originator' => trim($_POST['originator']), ':id' => (int)$_POST['id'], ':status' => 'pending']);
    header('Location: ' . app_url('user/dak_list.php'));
    exit;
}

$q = trim($_GET['q'] ?? '');
$where = ' WHERE 1=1 ';
$params = [];
if ($q !== '') {
    $where .= ' AND (control_no LIKE :q OR letter_no LIKE :q OR originator LIKE :q OR subject LIKE :q)';
    $params[':q'] = "%{$q}%";
}
if (in_array($user['role'], ['branch_clerk','officer'], true) && $user['branch_id']) {
    $where .= ' AND branch_id=:branch_id';
    $params[':branch_id'] = $user['branch_id'];
}
$sql = "SELECT * FROM dak_master {$where} ORDER BY id DESC LIMIT 200";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$edit = null;
if (isset($_GET['edit']) && $user['role'] === 'dispatcher') {
    $es = db()->prepare('SELECT * FROM dak_master WHERE id=:id');
    $es->execute([':id' => (int)$_GET['edit']]);
    $edit = $es->fetch();
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-panel"><div class="topbar p-3 d-flex justify-content-between"><h5>DAK Register</h5><form><input name="q" value="<?= esc($q) ?>" class="form-control" placeholder="Search control / letter / originator / subject"></form></div>
<main class="p-4">
<?php if ($edit): ?><div class="card p-3 mb-3"><h6>Edit DAK (Dispatcher only, pending only)</h6><form method="post" class="row g-2"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><div class="col-md-4"><input class="form-control" name="originator" value="<?= esc($edit['originator']) ?>"></div><div class="col-md-6"><input class="form-control" name="subject" value="<?= esc($edit['subject']) ?>"></div><div class="col-md-2"><button class="btn btn-primary w-100">Update</button></div></form></div><?php endif; ?>
<div class="card p-3">
<table class="table table-sm align-middle"><thead><tr><th>Control #</th><th>Letter #</th><th>Subject</th><th>Originator</th><th>Status</th><th>Cutoff</th><th></th></tr></thead><tbody><?php foreach($rows as $r): ?><tr class="<?= (($r['cutoff_date'] && strtotime($r['cutoff_date']) < time()) && $r['status'] !== 'closed') ? 'overdue-row' : '' ?>"><td><?= esc($r['control_no']) ?></td><td><?= esc($r['letter_no']) ?></td><td><?= esc($r['subject']) ?></td><td><?= esc($r['originator']) ?></td><td><span class="badge bg-secondary"><?= esc($r['status']) ?></span></td><td><?= esc((string)$r['cutoff_date']) ?></td><td><?php if ($user['role']==='dispatcher' && $r['status']==='pending'): ?><a class="btn btn-sm btn-outline-primary" href="?edit=<?= (int)$r['id'] ?>">Edit</a><?php endif; ?></td></tr><?php endforeach; ?></tbody></table>
</div>
</main></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
