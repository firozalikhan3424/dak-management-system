<?php

declare(strict_types=1);

const APP_NAME = 'Army DAK Management System';
const APP_URL = 'http://localhost/dak_management/';
const SESSION_TIMEOUT_SECONDS = 1800;

date_default_timezone_set('Asia/Karachi');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
