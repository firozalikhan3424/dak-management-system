<header class="topbar d-flex justify-content-between align-items-center px-4 py-3">
    <h4 class="mb-0">Army DAK Management</h4>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-info text-dark"><?= e($user['role'] ?? 'guest') ?></span>
        <span><?= e($user['name'] ?? 'Guest') ?></span>
    </div>
</header>
