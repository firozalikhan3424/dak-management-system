<?php
require_once __DIR__ . '/../includes/auth.php';
require_roles(['admin']);
$pageTitle = 'Manage Users';
$activeMenu = 'users';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $id = (int)($_POST['id'] ?? 0);
    $data = [
        ':name' => trim($_POST['name']),
        ':username' => trim($_POST['username']),
        ':role' => $_POST['role'],
        ':branch_id' => ($_POST['branch_id'] ?? '') === '' ? null : (int)$_POST['branch_id'],
        ':status' => (int)($_POST['status'] ?? 1),
    ];

    if ($id > 0) {
        $sql = 'UPDATE users SET name=:name, username=:username, role=:role, branch_id=:branch_id, status=:status WHERE id=:id';
        $stmt = db()->prepare($sql);
        $data[':id'] = $id;
        $stmt->execute($data);
    } else {
        $sql = 'INSERT INTO users (name, username, password, role, branch_id, status) VALUES (:name,:username,:password,:role,:branch_id,:status)';
        $stmt = db()->prepare($sql);
        $data[':password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt->execute($data);
    }
    header('Location: /admin/users.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM users WHERE id=:id');
    $stmt->execute([':id' => (int)$_GET['edit']]);
    $edit = $stmt->fetch();
}

$users = db()->query('SELECT u.*, b.branch_name FROM users u LEFT JOIN branches b ON b.id=u.branch_id ORDER BY u.id DESC')->fetchAll();
$branches = db()->query('SELECT id, branch_name FROM branches WHERE status=1 ORDER BY branch_name')->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-panel"><div class="topbar p-3 d-flex justify-content-between"><h5>User Management</h5><span><?= esc(auth_user()['name']) ?></span></div>
<main class="p-4">
<div class="card p-3 mb-3"><form method="post" class="row g-2"><?= csrf_field() ?>
<input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<div class="col-md-2"><input required name="name" value="<?= esc($edit['name'] ?? '') ?>" class="form-control" placeholder="Name"></div>
<div class="col-md-2"><input required name="username" value="<?= esc($edit['username'] ?? '') ?>" class="form-control" placeholder="Username"></div>
<div class="col-md-2"><input <?= $edit ? '' : 'required' ?> name="password" type="password" class="form-control" placeholder="Password<?= $edit ? ' (leave blank to keep)' : '' ?>"></div>
<div class="col-md-2"><select name="role" class="form-select"><?php foreach (['admin','dispatcher','head_clerk','branch_clerk','officer','co'] as $role): ?><option <?= (($edit['role'] ?? '') === $role) ? 'selected' : '' ?>><?= esc($role) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><select name="branch_id" class="form-select"><option value="">No Branch</option><?php foreach($branches as $b): ?><option value="<?= (int)$b['id'] ?>" <?= ((int)($edit['branch_id'] ?? 0)===(int)$b['id'])?'selected':'' ?>><?= esc($b['branch_name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-1"><select name="status" class="form-select"><option value="1">Active</option><option value="0" <?= (($edit['status'] ?? 1)==0)?'selected':'' ?>>Inactive</option></select></div>
<div class="col-md-1"><button class="btn btn-primary w-100"><?= $edit ? 'Update' : 'Create' ?></button></div>
</form></div>

<div class="card p-3"><table class="table table-striped table-sm align-middle"><thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Branch</th><th>Status</th><th></th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><?= esc($u['name']) ?></td><td><?= esc($u['username']) ?></td><td><?= esc($u['role']) ?></td><td><?= esc($u['branch_name'] ?? '-') ?></td><td><?= $u['status'] ? 'Active' : 'Inactive' ?></td><td><a class="btn btn-sm btn-outline-secondary" href="?edit=<?= (int)$u['id'] ?>">Edit</a></td></tr><?php endforeach; ?></tbody></table></div>
</main></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
