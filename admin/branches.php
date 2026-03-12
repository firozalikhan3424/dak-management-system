<?php
require_once __DIR__ . '/../includes/auth.php';
require_roles(['admin']);
$pageTitle = 'Branches';
$activeMenu = 'branches';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    db()->prepare('INSERT INTO branches (branch_name, description, status) VALUES (:name,:description,:status)')
        ->execute([':name' => trim($_POST['branch_name']), ':description' => trim($_POST['description']), ':status' => (int)$_POST['status']]);
    header('Location: /admin/branches.php');
    exit;
}
$rows = db()->query('SELECT * FROM branches ORDER BY id DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-panel"><div class="topbar p-3"><h5>Branches</h5></div><main class="p-4">
<div class="card p-3 mb-3"><form method="post" class="row g-2"><?= csrf_field() ?>
<div class="col-md-4"><input name="branch_name" required class="form-control" placeholder="Branch Name"></div>
<div class="col-md-5"><input name="description" class="form-control" placeholder="Description"></div>
<div class="col-md-2"><select name="status" class="form-select"><option value="1">Active</option><option value="0">Inactive</option></select></div>
<div class="col-md-1"><button class="btn btn-primary w-100">Add</button></div></form></div>
<div class="card p-3"><table class="table table-sm"><tr><th>Name</th><th>Description</th><th>Status</th></tr><?php foreach($rows as $r): ?><tr><td><?= esc($r['branch_name']) ?></td><td><?= esc($r['description'] ?? '') ?></td><td><?= $r['status']?'Active':'Inactive' ?></td></tr><?php endforeach; ?></table></div>
</main></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
