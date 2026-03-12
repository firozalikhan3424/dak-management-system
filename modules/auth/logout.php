<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';

if (current_user()) {
    audit_log((int)current_user()['id'], 'auth', 'logout');
}

session_unset();
session_destroy();
session_start();
flash('error', 'Logged out successfully.');
redirect('modules/auth/login.php');
