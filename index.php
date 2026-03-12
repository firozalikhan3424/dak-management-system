<?php
require_once __DIR__ . '/helpers/auth_helper.php';

if (current_user()) {
    redirect(role_home(current_user()['role']));
}

redirect('modules/auth/login.php');
