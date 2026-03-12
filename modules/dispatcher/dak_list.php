<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
ensure_role(['dispatcher','admin','head_clerk']);
[$page, $perPage, $offset] = paginate();
$search = trim($_GET['q'] ?? '');
$where = '';
$params = [];
if ($search !== '') {
    $where = 'WHERE control_no LIKE :q OR letter_no LIKE :q OR originator LIKE :q OR subject LIKE :q';
    $params[':q'] = "%{$search}%";
}
$stmt = db()->prepare("SELECT * FROM dak_master {$where} ORDER BY id DESC LIMIT :limit OFFSET :offset");
foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="main-content"><?php include __DIR__ . '/../../includes/navbar.php'; ?>
<main class="container-fluid p-4">
    <div class="card p-4">
        <div class="d-flex justify-content-between mb-3"><h5>DAK List</h5><form><input name="q" value="<?= e($search) ?>" class="form-control" placeholder="Global search"></form></div>
        <table class="table table-sm align-middle">
            <thead><tr><th>Control No</th><th>Letter No</th><th>Subject</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr class="<?= (strtotime($row['cutoff_date'] ?? '') < time() && ($row['status'] ?? '') !== 'closed') ? 'overdue' : '' ?>">
                    <td><?= e($row['control_no']) ?></td><td><?= e($row['letter_no']) ?></td><td><?= e($row['subject']) ?></td><td><span class="badge bg-secondary"><?= e($row['status']) ?></span></td>
                    <td><a href="edit_dak.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
