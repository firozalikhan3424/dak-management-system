<aside class="sidebar text-white p-3">
    <div class="d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-shield-check fs-3 text-info"></i>
        <div>
            <div class="fw-bold">Army DAK</div>
            <small class="text-secondary"><?= $isAdminPanel ? 'Admin Panel' : 'User Panel' ?></small>
        </div>
    </div>

    <?php if ($isAdminPanel): ?>
        <a class="menu-link <?= $activeMenu === 'admin_dashboard' ? 'active' : '' ?>" href="<?= app_url('admin/dashboard.php') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a class="menu-link <?= $activeMenu === 'users' ? 'active' : '' ?>" href="<?= app_url('admin/users.php') ?>"><i class="bi bi-people"></i> Users</a>
        <a class="menu-link <?= $activeMenu === 'branches' ? 'active' : '' ?>" href="<?= app_url('admin/branches.php') ?>"><i class="bi bi-diagram-3"></i> Branches</a>
        <a class="menu-link <?= $activeMenu === 'sub_branches' ? 'active' : '' ?>" href="<?= app_url('admin/sub_branches.php') ?>"><i class="bi bi-diagram-2"></i> Sub Branches</a>
        <a class="menu-link <?= $activeMenu === 'dak_settings' ? 'active' : '' ?>" href="<?= app_url('admin/dak_number_settings.php') ?>"><i class="bi bi-123"></i> DAK Number Settings</a>
    <?php else: ?>
        <a class="menu-link <?= $activeMenu === 'user_dashboard' ? 'active' : '' ?>" href="<?= app_url('user/dashboard.php') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a class="menu-link <?= $activeMenu === 'incoming' ? 'active' : '' ?>" href="<?= app_url('user/incoming_dak.php') ?>"><i class="bi bi-envelope-plus"></i> Incoming DAK</a>
        <a class="menu-link <?= $activeMenu === 'mark' ? 'active' : '' ?>" href="<?= app_url('user/mark_dak.php') ?>"><i class="bi bi-check2-square"></i> Mark DAK</a>
        <a class="menu-link <?= $activeMenu === 'branch_action' ? 'active' : '' ?>" href="<?= app_url('user/branch_action.php') ?>"><i class="bi bi-journal-check"></i> Branch Action</a>
        <a class="menu-link <?= $activeMenu === 'dak_list' ? 'active' : '' ?>" href="<?= app_url('user/dak_list.php') ?>"><i class="bi bi-list-ul"></i> DAK List</a>
        <a class="menu-link <?= $activeMenu === 'reports' ? 'active' : '' ?>" href="<?= app_url('user/reports.php') ?>"><i class="bi bi-bar-chart"></i> Reports</a>
    <?php endif; ?>

    <hr class="border-secondary">
    <a class="menu-link text-danger" href="<?= app_url('auth/logout.php') ?>"><i class="bi bi-box-arrow-right"></i> Logout</a>
</aside>
