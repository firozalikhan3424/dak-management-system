<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
ensure_role(['admin']);
?>
<?php include __DIR__ . '/../../includes/header.php'; include __DIR__ . '/../../includes/sidebar.php'; ?>
<div class="main-content"><?php include __DIR__ . '/../../includes/navbar.php'; ?><main class="container-fluid p-4"><div class="card p-4"><h5>System Settings</h5><p class="text-muted">Add SMTP, reminder scheduler, and retention policy settings here.</p></div></main></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
