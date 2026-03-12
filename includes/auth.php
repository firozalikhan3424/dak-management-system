<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const SESSION_TIMEOUT = 1800;

function app_url(string $path = ''): string
{
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function csrf_token_value(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . esc(csrf_token_value()) . '">';
}

function verify_csrf_token(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Invalid CSRF token.');
    }
}

function auth_user(): ?array
{
    return $_SESSION['auth_user'] ?? null;
}

function require_login(): void
{
    if (!isset($_SESSION['auth_user'])) {
        header('Location: ' . app_url('auth/login.php'));
        exit;
    }

    $last = $_SESSION['last_activity'] ?? time();
    if (time() - $last > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['flash_error'] = 'Session expired due to inactivity.';
        header('Location: ' . app_url('auth/login.php'));
        exit;
    }

    $_SESSION['last_activity'] = time();
}

function require_roles(array $roles): void
{
    require_login();
    $user = auth_user();
    if (!$user || !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('Access denied.');
    }
}

function log_audit(int $userId, string $module, string $action, ?string $oldValue = null, ?string $newValue = null): void
{
    $stmt = db()->prepare('INSERT INTO audit_logs (user_id, module, action, old_value, new_value, ip_address) VALUES (:user_id, :module, :action, :old_value, :new_value, :ip)');
    $stmt->execute([
        ':user_id' => $userId,
        ':module' => $module,
        ':action' => $action,
        ':old_value' => $oldValue,
        ':new_value' => $newValue,
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? 'cli',
    ]);
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION[$key] = $value;
        return null;
    }

    if (!isset($_SESSION[$key])) {
        return null;
    }

    $stored = (string)$_SESSION[$key];
    unset($_SESSION[$key]);
    return $stored;
}

function generate_control_number(): string
{
    $settings = db()->query('SELECT prefix, year, sequence_length FROM dak_number_settings ORDER BY id DESC LIMIT 1')->fetch();
    $prefix = $settings['prefix'] ?? 'DAK';
    $year = (string)($settings['year'] ?? date('Y'));
    $sequenceLength = (int)($settings['sequence_length'] ?? 4);

    $stmt = db()->prepare('SELECT COUNT(*) c FROM dak_master WHERE control_no LIKE :pattern');
    $stmt->execute([':pattern' => sprintf('%s-%s-%%', $prefix, $year)]);
    $next = (int)$stmt->fetch()['c'] + 1;

    return sprintf('%s-%s-%0' . $sequenceLength . 'd', $prefix, $year, $next);
}

function role_home(string $role): string
{
    return match ($role) {
        'admin' => app_url('admin/dashboard.php'),
        'dispatcher' => app_url('user/incoming_dak.php'),
        'head_clerk' => app_url('user/mark_dak.php'),
        'branch_clerk' => app_url('user/branch_action.php'),
        'officer', 'co' => app_url('user/dashboard.php'),
        default => app_url('auth/login.php'),
    };
}
