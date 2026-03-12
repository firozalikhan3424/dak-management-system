<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
ensure_authenticated();
$rows = db()->query("SELECT control_no, subject, originator, cutoff_date FROM dak_master WHERE speak_case=1 ORDER BY cutoff_date ASC")->fetchAll();
?>
<h3>Speak Cases</h3>
<table border="1" cellpadding="6"><tr><th>Control</th><th>Subject</th><th>Originator</th><th>Cutoff</th></tr><?php foreach($rows as $r): ?><tr><td><?= e($r['control_no']) ?></td><td><?= e($r['subject']) ?></td><td><?= e($r['originator']) ?></td><td><?= e($r['cutoff_date']) ?></td></tr><?php endforeach; ?></table>
