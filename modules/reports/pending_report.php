<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
ensure_authenticated();
$rows = db()->query("SELECT control_no, subject, cutoff_date, DATEDIFF(CURDATE(), receipt_date) age_days FROM dak_master WHERE status <> 'closed' AND DATEDIFF(CURDATE(), receipt_date) > 7 ORDER BY age_days DESC")->fetchAll();
header('Content-Type: text/html; charset=utf-8');
?>
<h3>Pending DAK (>7 Days)</h3>
<table border="1" cellpadding="6"><tr><th>Control No</th><th>Subject</th><th>Age</th><th>Cutoff</th></tr><?php foreach($rows as $r): ?><tr><td><?= e($r['control_no']) ?></td><td><?= e($r['subject']) ?></td><td><?= (int)$r['age_days'] ?></td><td><?= e($r['cutoff_date']) ?></td></tr><?php endforeach; ?></table>
