<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $stored = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $stored;
}

function redirect(string $path): never
{
    header('Location: ' . APP_URL . '/' . ltrim($path, '/'));
    exit;
}

function paginate(int $defaultPerPage = 10): array
{
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = max(1, min(100, (int)($_GET['per_page'] ?? $defaultPerPage)));
    $offset = ($page - 1) * $perPage;

    return [$page, $perPage, $offset];
}

function audit_log(int $userId, string $module, string $action, ?string $old = null, ?string $new = null): void
{
    $stmt = db()->prepare(
        'INSERT INTO audit_logs (user_id, module, action, old_value, new_value, ip_address) VALUES (:user_id,:module,:action,:old_value,:new_value,:ip)'
    );
    $stmt->execute([
        ':user_id' => $userId,
        ':module' => $module,
        ':action' => $action,
        ':old_value' => $old,
        ':new_value' => $new,
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? 'cli',
    ]);
}

function generate_control_no(): string
{
    $year = date('Y');
    $stmt = db()->prepare('SELECT COUNT(*) AS total FROM dak_master WHERE YEAR(created_at) = :year');
    $stmt->execute([':year' => $year]);
    $count = (int)$stmt->fetch()['total'] + 1;

    return sprintf('DAK-%s-%04d', $year, $count);
}

function get_branches(): array
{
    return db()->query('SELECT id, branch_name FROM branches WHERE status = 1 ORDER BY branch_name')->fetchAll();
}

function get_sub_branches(int $branchId): array
{
    $stmt = db()->prepare('SELECT id, sub_branch_name, file_start, file_end FROM sub_branches WHERE branch_id = :branch_id AND status = 1');
    $stmt->execute([':branch_id' => $branchId]);
    return $stmt->fetchAll();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Invalid CSRF token.');
    }
}
