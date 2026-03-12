<?php
require_once __DIR__ . '/../includes/auth.php';
if (auth_user()) {
    log_audit((int)auth_user()['id'], 'auth', 'logout');
}
session_unset();
session_destroy();
header('Location: ' . app_url('auth/login.php'));
exit;
