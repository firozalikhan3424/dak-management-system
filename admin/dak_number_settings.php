<?php
require_once __DIR__ . '/../includes/auth.php';
require_roles(['admin']);
$pageTitle = 'DAK Number Settings';
$activeMenu = 'dak_settings';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    db()->prepare('INSERT INTO dak_number_settings (prefix, year, sequence_length) VALUES (:prefix,:year,:sequence_length)')
        ->execute([
            ':prefix' => trim($_POST['prefix']),
            ':year' => (int)$_POST['year'],
            ':sequence_length' => (int)$_POST['sequence_length'],
        ]);
    header('Location: /admin/dak_number_settings.php');
    exit;
}

$current = db()->query('SELECT * FROM dak_number_settings ORDER BY id DESC LIMIT 1')->fetch();
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-panel"><div class="topbar p-3"><h5>DAK Number Settings</h5></div><main class="p-4">
<div class="card p-3">
    <p class="mb-1">Current format: <strong><?= esc(($current['prefix'] ?? 'DAK') . '-' . ($current['year'] ?? date('Y')) . '-0001') ?></strong></p>
    <form method="post" class="row g-2"><?= csrf_field() ?>
        <div class="col-md-4"><input name="prefix" value="<?= esc($current['prefix'] ?? 'DAK') ?>" class="form-control" required></div>
        <div class="col-md-4"><input name="year" type="number" value="<?= (int)($current['year'] ?? date('Y')) ?>" class="form-control" required></div>
        <div class="col-md-2"><input name="sequence_length" type="number" value="<?= (int)($current['sequence_length'] ?? 4) ?>" class="form-control" required></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Save</button></div>
    </form>
</div>
</main></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
