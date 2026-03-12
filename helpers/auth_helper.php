<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function ensure_authenticated(): void
{
    if (!isset($_SESSION['user'])) {
        flash('error', 'Please login first.');
        redirect('modules/auth/login.php');
    }

    $lastActivity = $_SESSION['last_activity'] ?? time();
    if (time() - $lastActivity > SESSION_TIMEOUT_SECONDS) {
        session_unset();
        session_destroy();
        session_start();
        flash('error', 'Session expired due to inactivity.');
        redirect('modules/auth/login.php');
    }

    $_SESSION['last_activity'] = time();
}

function ensure_role(array $roles): void
{
    ensure_authenticated();
    $user = current_user();

    if ($user === null || !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('Unauthorized access.');
    }
}

function role_home(string $role): string
{
    return match ($role) {
        'admin', 'co' => 'modules/dashboard/dashboard.php',
        'dispatcher' => 'modules/dispatcher/incoming_dak.php',
        'head_clerk' => 'modules/head_clerk/mark_dak.php',
        'branch_clerk', 'officer' => 'modules/branch/branch_dashboard.php',
        default => 'modules/dashboard/dashboard.php',
    };
}
