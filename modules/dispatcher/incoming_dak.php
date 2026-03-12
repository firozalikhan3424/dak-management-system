<?php
require_once __DIR__ . '/../../helpers/auth_helper.php';
require_once __DIR__ . '/../../helpers/validation_helper.php';
ensure_role(['dispatcher', 'admin']);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $required = ['letter_no','letter_date','originator','subject','security_class','receipt_date','receipt_mode','ihq'];
    $errors = validate_required($_POST, $required);

    if (!$errors) {
        $controlNo = generate_control_no();
        $stmt = db()->prepare('INSERT INTO dak_master (control_no, letter_no, letter_date, originator, subject, security_class, receipt_date, receipt_mode, ihq, status, created_by) VALUES (:control_no,:letter_no,:letter_date,:originator,:subject,:security_class,:receipt_date,:receipt_mode,:ihq,:status,:created_by)');
        $stmt->execute([
            ':control_no' => $controlNo,
            ':letter_no' => trim($_POST['letter_no']),
            ':letter_date' => $_POST['letter_date'],
            ':originator' => trim($_POST['originator']),
            ':subject' => trim($_POST['subject']),
            ':security_class' => $_POST['security_class'],
            ':receipt_date' => $_POST['receipt_date'],
            ':receipt_mode' => $_POST['receipt_mode'],
            ':ihq' => $_POST['ihq'] === 'Yes' ? 1 : 0,
            ':status' => 'pending',
            ':created_by' => current_user()['id'],
        ]);
        audit_log((int)current_user()['id'], 'dispatcher', 'create_dak', null, $controlNo);
        flash('success', "DAK created with control number {$controlNo}");
        redirect('modules/dispatcher/dak_list.php');
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="main-content"><?php include __DIR__ . '/../../includes/navbar.php'; ?>
<main class="container-fluid p-4">
    <div class="card p-4">
        <h5>Incoming DAK Entry</h5>
        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <form method="post" class="row g-3">
            <?= csrf_input() ?>
            <div class="col-md-4"><label class="form-label">Letter No</label><input name="letter_no" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Letter Date</label><input type="date" name="letter_date" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Originator</label><input name="originator" class="form-control" required></div>
            <div class="col-md-8"><label class="form-label">Subject</label><input name="subject" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Security</label><select name="security_class" class="form-select"><option>Unclassified</option><option>Confidential</option><option>Secret</option></select></div>
            <div class="col-md-4"><label class="form-label">Receipt Date</label><input type="date" name="receipt_date" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Receipt Mode</label><select name="receipt_mode" class="form-select"><option>Post</option><option>Hand</option></select></div>
            <div class="col-md-4"><label class="form-label">IHQ</label><select name="ihq" class="form-select"><option>Yes</option><option>No</option></select></div>
            <div class="col-12"><button class="btn btn-primary">Save Incoming DAK</button></div>
        </form>
    </div>
</main></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
