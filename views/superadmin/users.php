<?php
$layout = 'app';
$pageTitle = 'User Management - Superadmin';
$breadcrumbs = [['label' => 'Superadmin', 'url' => '/superadmin/dashboard'], ['label' => 'User Management']];
$sidebarNav = '
<a class="nav-link" href="/superadmin/dashboard"><i class="bi bi-shield-lock"></i> Superadmin</a>
<a class="nav-link active" href="/superadmin/users"><i class="bi bi-people-fill"></i> User Management</a>
<hr class="my-2">
<a class="nav-link" href="/admin/dashboard"><i class="bi bi-speedometer2"></i> Admin Dashboard</a>
<a class="nav-link" href="/admin/users"><i class="bi bi-people"></i> Admin Users</a>
<a class="nav-link" href="/admin/activity-logs"><i class="bi bi-journal-text"></i> Activity Logs</a>
<a class="nav-link" href="/admin/settings"><i class="bi bi-gear"></i> Clinic Settings</a>
';

$roleBadgeColor = fn(string $r): string => match ($r) {
    'SUPERADMIN' => '#6f42c1',
    'ADMIN' => '#dc3545',
    'DOCTOR', 'DOCTOR_OWNER' => '#007bff',
    'PARENT' => '#28a745',
    default => '#6c757d',
};
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>User Management</h5>
        <small class="text-muted">Change roles, toggle status and remove non-doctor accounts.</small>
    </div>
    <a href="/admin/export/users?<?= http_build_query($filters) ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-1"></i>Export CSV</a>
</div>

<div class="alert alert-warning small mb-3">
    <i class="bi bi-shield-exclamation me-1"></i>
    <strong>Doctor protection:</strong> existing doctor accounts cannot have their role changed or be deleted from this page. Provision new doctors through the Admin &rarr; <em>Add Doctor</em> flow so license details are captured.
</div>

<!-- Filters -->
<div class="stat-card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small">Search</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Name or email..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small">Role</label>
            <select name="user_type" class="form-select form-select-sm">
                <option value="">All Roles</option>
                <option value="PARENT" <?= ($filters['user_type'] ?? '') === 'PARENT' ? 'selected' : '' ?>>Parent</option>
                <option value="DOCTOR" <?= ($filters['user_type'] ?? '') === 'DOCTOR' ? 'selected' : '' ?>>Doctor</option>
                <option value="DOCTOR_OWNER" <?= ($filters['user_type'] ?? '') === 'DOCTOR_OWNER' ? 'selected' : '' ?>>Doctor Owner</option>
                <option value="ADMIN" <?= ($filters['user_type'] ?? '') === 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                <option value="SUPERADMIN" <?= ($filters['user_type'] ?? '') === 'SUPERADMIN' ? 'selected' : '' ?>>Superadmin</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="suspended" <?= ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="stat-card p-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="6">
                    <div class="empty-state">
                        <i class="bi bi-people"></i>
                        <h5>No users found</h5>
                        <p>Try adjusting your filters.</p>
                    </div>
                </td></tr>
            <?php else: ?>
                <?php foreach ($users as $u):
                    $isDoctor = in_array($u['user_type'], ['DOCTOR', 'DOCTOR_OWNER'], true);
                    $isSelf = (int) $u['id'] === (int) ($user['id'] ?? 0);
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></strong><?= $isSelf ? ' <span class="badge bg-secondary ms-1">you</span>' : '' ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge" style="background:<?= $roleBadgeColor($u['user_type']) ?>"><?= htmlspecialchars($u['user_type']) ?></span></td>
                    <td><span class="badge" style="background:<?= match($u['status']) { 'active' => '#28a745', 'inactive' => '#ffc107', default => '#dc3545' } ?>"><?= htmlspecialchars($u['status']) ?></span></td>
                    <td style="font-size:.85rem;"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if ($isDoctor): ?>
                                    <li><span class="dropdown-item-text text-muted small"><i class="bi bi-lock me-1"></i>Doctor account — role locked</span></li>
                                <?php elseif ($isSelf): ?>
                                    <li><span class="dropdown-item-text text-muted small"><i class="bi bi-lock me-1"></i>You can't modify your own account</span></li>
                                <?php else: ?>
                                    <li><h6 class="dropdown-header">Change role to</h6></li>
                                    <?php foreach (['PARENT', 'ADMIN', 'SUPERADMIN'] as $role): ?>
                                        <?php if ($role === $u['user_type']) continue; ?>
                                        <li><a class="dropdown-item" href="#" onclick="changeRole(<?= $u['id'] ?>, '<?= $role ?>'); return false;"><i class="bi bi-arrow-repeat me-2"></i><?= $role ?></a></li>
                                    <?php endforeach; ?>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <?php if (!$isSelf): ?>
                                    <?php if ($u['status'] === 'active'): ?>
                                        <li><a class="dropdown-item" href="#" onclick="toggleStatus(<?= $u['id'] ?>, 'inactive'); return false;"><i class="bi bi-x-circle me-2"></i>Deactivate</a></li>
                                    <?php else: ?>
                                        <li><a class="dropdown-item" href="#" onclick="toggleStatus(<?= $u['id'] ?>, 'active'); return false;"><i class="bi bi-check-circle me-2"></i>Activate</a></li>
                                    <?php endif; ?>
                                    <li><a class="dropdown-item text-warning" href="#" onclick="toggleStatus(<?= $u['id'] ?>, 'suspended'); return false;"><i class="bi bi-ban me-2"></i>Suspend</a></li>
                                    <?php if (!$isDoctor): ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteUser(<?= $u['id'] ?>); return false;"><i class="bi bi-trash me-2"></i>Delete user</a></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (($pagination['total_pages'] ?? 0) > 1): ?>
    <div class="p-3 d-flex justify-content-between align-items-center border-top">
        <small class="text-muted">Showing <?= count($users) ?> of <?= $pagination['total'] ?> users</small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php ob_start(); ?>
<script>
async function changeRole(userId, newRole) {
    showConfirm(`Change this user's role to "${newRole}"?`, async () => {
        const result = await apiRequest('/superadmin/users/change-role', 'POST', { user_id: userId, user_type: newRole });
        if (result.success) { showToast(result.message, 'success'); setTimeout(() => location.reload(), 800); }
    });
}

async function toggleStatus(userId, status) {
    showConfirm(`Change user status to "${status}"?`, async () => {
        const result = await apiRequest('/superadmin/users/toggle-status', 'POST', { user_id: userId, status });
        if (result.success) { showToast(result.message, 'success'); setTimeout(() => location.reload(), 800); }
    });
}

async function deleteUser(userId) {
    showConfirm('Delete this user? This action cannot be undone.', async () => {
        const result = await apiRequest('/superadmin/users/delete', 'POST', { user_id: userId });
        if (result.success) { showToast(result.message, 'success'); setTimeout(() => location.reload(), 800); }
    });
}
</script>
<?php $extraScripts = ob_get_clean(); ?>
