<?php
require_once __DIR__ . '/includes/auth.php';
if (auth_user()) {
    header('Location: ' . role_home(auth_user()['role']));
    exit;
}
header('Location: ' . app_url('auth/login.php'));
exit;
