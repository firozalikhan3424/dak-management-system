<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
ensure_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $stmt = db()->prepare('INSERT INTO users (name, username, password, role, branch_id, status) VALUES (:name,:username,:password,:role,:branch_id,:status)');
    $stmt->execute([
        ':name' => trim($_POST['name']),
        ':username' => trim($_POST['username']),
        ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        ':role' => $_POST['role'],
        ':branch_id' => (int)($_POST['branch_id'] ?: 0),
        ':status' => 1,
    ]);
    audit_log((int)current_user()['id'], 'admin', 'create_user');
    flash('success', 'User created successfully.');
    redirect('modules/admin/users.php');
}

$users = db()->query('SELECT id,name,username,role,status FROM users ORDER BY id DESC')->fetchAll();
$branches = get_branches();
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="main-content"><?php include __DIR__ . '/../../includes/navbar.php'; ?>
<main class="container-fluid p-4"><div class="card p-4"><h5>User Management</h5>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<form method="post" class="row g-2 mb-3"><?= csrf_input() ?><div class="col"><input name="name" placeholder="Name" class="form-control" required></div><div class="col"><input name="username" placeholder="Username" class="form-control" required></div><div class="col"><input name="password" placeholder="Password" type="password" class="form-control" required></div><div class="col"><select name="role" class="form-select"><option>admin</option><option>dispatcher</option><option>head_clerk</option><option>branch_clerk</option><option>officer</option><option>co</option></select></div><div class="col"><select name="branch_id" class="form-select"><option value="0">No Branch</option><?php foreach($branches as $b): ?><option value="<?= $b['id'] ?>"><?= e($b['branch_name']) ?></option><?php endforeach; ?></select></div><div class="col"><button class="btn btn-primary">Add</button></div></form>
<table class="table table-sm"><tr><th>Name</th><th>Username</th><th>Role</th><th>Status</th></tr><?php foreach($users as $u): ?><tr><td><?= e($u['name']) ?></td><td><?= e($u['username']) ?></td><td><?= e($u['role']) ?></td><td><?= $u['status']?'Active':'Inactive' ?></td></tr><?php endforeach; ?></table>
</div></main></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
