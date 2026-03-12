<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
ensure_authenticated();
$rows = db()->query("SELECT b.branch_name, COUNT(*) pending_total FROM dak_master d JOIN branches b ON b.id=d.branch_id WHERE d.status <> 'closed' GROUP BY d.branch_id ORDER BY pending_total DESC")->fetchAll();
?>
<h3>Branch Wise Pending Report</h3>
<table border="1" cellpadding="6"><tr><th>Branch</th><th>Pending</th></tr><?php foreach($rows as $r): ?><tr><td><?= e($r['branch_name']) ?></td><td><?= (int)$r['pending_total'] ?></td></tr><?php endforeach; ?></table>
