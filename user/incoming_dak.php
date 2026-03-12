<?php
require_once __DIR__ . '/../includes/auth.php';
require_roles(['dispatcher','admin']);
$pageTitle = 'Incoming DAK';
$activeMenu = 'incoming';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $control = generate_control_number();
    $stmt = db()->prepare('INSERT INTO dak_master (control_no, letter_no, letter_date, originator, subject, security_class, receipt_date, receipt_mode, ihq, status, created_by) VALUES (:control_no,:letter_no,:letter_date,:originator,:subject,:security_class,:receipt_date,:receipt_mode,:ihq,:status,:created_by)');
    $stmt->execute([
        ':control_no' => $control,
        ':letter_no' => trim($_POST['letter_no']),
        ':letter_date' => $_POST['letter_date'],
        ':originator' => trim($_POST['originator']),
        ':subject' => trim($_POST['subject']),
        ':security_class' => $_POST['security_class'],
        ':receipt_date' => $_POST['receipt_date'],
        ':receipt_mode' => $_POST['receipt_mode'],
        ':ihq' => ($_POST['ihq'] ?? 'No') === 'Yes' ? 1 : 0,
        ':status' => 'pending',
        ':created_by' => auth_user()['id'],
    ]);
    log_audit((int)auth_user()['id'], 'incoming_dak', 'create', null, $control);
    flash('flash_success', "DAK created successfully. Control Number: {$control}");
    header('Location: ' . app_url('user/incoming_dak.php'));
    exit;
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-panel"><div class="topbar p-3"><h5>Incoming DAK Entry</h5></div><main class="p-4">
<?php if ($msg = flash('flash_success')): ?><div class="alert alert-success"><?= esc($msg) ?></div><?php endif; ?>
<div class="card p-3">
<form method="post" class="row g-3"><?= csrf_field() ?>
<div class="col-md-4"><label class="form-label">Letter Number</label><input required name="letter_no" class="form-control"></div>
<div class="col-md-4"><label class="form-label">Letter Date</label><input required type="date" name="letter_date" class="form-control"></div>
<div class="col-md-4"><label class="form-label">Originator</label><input required name="originator" class="form-control"></div>
<div class="col-md-8"><label class="form-label">Subject</label><input required name="subject" class="form-control"></div>
<div class="col-md-4"><label class="form-label">Security Classification</label><select name="security_class" class="form-select"><option>Unclassified</option><option>Confidential</option><option>Secret</option></select></div>
<div class="col-md-4"><label class="form-label">Receipt Date</label><input required type="date" name="receipt_date" class="form-control"></div>
<div class="col-md-4"><label class="form-label">Receipt Mode</label><select name="receipt_mode" class="form-select"><option>Post</option><option>By Hand</option></select></div>
<div class="col-md-4"><label class="form-label">IHQ</label><select name="ihq" class="form-select"><option>No</option><option>Yes</option></select></div>
<div class="col-12"><button class="btn btn-primary">Save Incoming DAK</button></div>
</form>
</div>
</main></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
