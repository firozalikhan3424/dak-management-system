<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
ensure_role(['admin']);
if ($_SERVER['REQUEST_METHOD']==='POST') { verify_csrf(); db()->prepare('INSERT INTO sub_branches (branch_id, sub_branch_name, file_start, file_end, status) VALUES (:b,:n,:s,:e,1)')->execute([':b'=>(int)$_POST['branch_id'],':n'=>trim($_POST['sub_branch_name']),':s'=>(int)$_POST['file_start'],':e'=>(int)$_POST['file_end']]); redirect('modules/admin/sub_branches.php'); }
$branches = get_branches();
$rows = db()->query('SELECT sb.*, b.branch_name FROM sub_branches sb JOIN branches b ON b.id = sb.branch_id ORDER BY sb.id DESC')->fetchAll();
?>
<h3>Sub Branches</h3>
<form method="post"><?= csrf_input() ?><select name="branch_id"><?php foreach($branches as $b): ?><option value="<?= $b['id'] ?>"><?= e($b['branch_name']) ?></option><?php endforeach; ?></select><input name="sub_branch_name" placeholder="Sub Branch"><input name="file_start" type="number" placeholder="File Start"><input name="file_end" type="number" placeholder="File End"><button>Add</button></form>
<table border="1"><tr><th>Branch</th><th>Sub Branch</th><th>Range</th></tr><?php foreach($rows as $r): ?><tr><td><?= e($r['branch_name']) ?></td><td><?= e($r['sub_branch_name']) ?></td><td><?= (int)$r['file_start'] ?>-<?= (int)$r['file_end'] ?></td></tr><?php endforeach; ?></table>
