<?php
require_once __DIR__ . '/../includes/auth.php';
require_roles(['admin']);
$pageTitle = 'Sub Branches';
$activeMenu = 'sub_branches';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    db()->prepare('INSERT INTO sub_branches (branch_id, sub_branch_name, file_start, file_end, status) VALUES (:branch_id,:name,:start,:end,:status)')
        ->execute([':branch_id' => (int)$_POST['branch_id'], ':name' => trim($_POST['sub_branch_name']), ':start' => (int)$_POST['file_start'], ':end' => (int)$_POST['file_end'], ':status' => 1]);
    header('Location: ' . app_url('admin/sub_branches.php'));
    exit;
}
$branches = db()->query('SELECT id, branch_name FROM branches WHERE status=1 ORDER BY branch_name')->fetchAll();
$rows = db()->query('SELECT sb.*, b.branch_name FROM sub_branches sb JOIN branches b ON b.id=sb.branch_id ORDER BY sb.id DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-panel"><div class="topbar p-3"><h5>Sub Branches</h5></div><main class="p-4">
<div class="card p-3 mb-3"><form method="post" class="row g-2"><?= csrf_field() ?>
<div class="col-md-3"><select name="branch_id" class="form-select"><?php foreach($branches as $b): ?><option value="<?= (int)$b['id'] ?>"><?= esc($b['branch_name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><input required name="sub_branch_name" class="form-control" placeholder="Sub Branch (A1)"></div>
<div class="col-md-2"><input required type="number" name="file_start" class="form-control" placeholder="File Start"></div>
<div class="col-md-2"><input required type="number" name="file_end" class="form-control" placeholder="File End"></div>
<div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div></form></div>
<div class="card p-3"><table class="table table-sm"><tr><th>Branch</th><th>Sub Branch</th><th>File Range</th></tr><?php foreach($rows as $r): ?><tr><td><?= esc($r['branch_name']) ?></td><td><?= esc($r['sub_branch_name']) ?></td><td><?= (int)$r['file_start'] ?> - <?= (int)$r['file_end'] ?></td></tr><?php endforeach; ?></table></div>
</main></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
