<?php
require_once __DIR__ . '/../includes/auth.php';

if (auth_user()) {
    header('Location: ' . role_home(auth_user()['role']));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM users WHERE username = :username AND status = 1 LIMIT 1');
    $stmt->execute([':username' => $username]);
    $row = $stmt->fetch();

    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['auth_user'] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'role' => $row['role'],
            'branch_id' => $row['branch_id'] ? (int)$row['branch_id'] : null,
        ];
        $_SESSION['last_activity'] = time();
        log_audit((int)$row['id'], 'auth', 'login');
        header('Location: ' . role_home($row['role']));
        exit;
    }

    flash('flash_error', 'Invalid username or password.');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Army DAK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="d-flex justify-content-center align-items-center" style="min-height:100vh;background:#e2e8f0;">
<div class="card p-4" style="width:420px;">
    <h4 class="mb-1">Army DAK Management</h4>
    <p class="text-muted">Secure command correspondence tracking</p>
    <?php if ($err = flash('flash_error')): ?><div class="alert alert-danger"><?= esc($err) ?></div><?php endif; ?>
    <form method="post">
        <?= csrf_field() ?>
        <div class="mb-3"><label class="form-label">Username</label><input name="username" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Password</label><input name="password" type="password" class="form-control" required></div>
        <button class="btn btn-dark w-100">Sign In</button>
    </form>
</div>
</body>
</html>
