<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
ensure_role(['admin']);
if ($_SERVER['REQUEST_METHOD']==='POST') { verify_csrf(); db()->prepare('INSERT INTO branches (branch_name, description, status) VALUES (:n,:d,1)')->execute([':n'=>trim($_POST['branch_name']),':d'=>trim($_POST['description'])]); redirect('modules/admin/branches.php'); }
$rows = db()->query('SELECT * FROM branches ORDER BY branch_name')->fetchAll();
?>
<h3>Branches</h3>
<form method="post"><?= csrf_input() ?><input name="branch_name" placeholder="Branch Name"><input name="description" placeholder="Description"><button>Add</button></form>
<ul><?php foreach($rows as $r): ?><li><?= e($r['branch_name']) ?></li><?php endforeach; ?></ul>
