<?php
$layout = 'app';
$pageTitle = 'Superadmin Dashboard - PediCare';
$breadcrumbs = [['label' => 'Superadmin'], ['label' => 'Dashboard']];
$sidebarNav = '
<a class="nav-link active" href="/superadmin/dashboard"><i class="bi bi-shield-lock"></i> Superadmin</a>
<a class="nav-link" href="/superadmin/users"><i class="bi bi-people-fill"></i> User Management</a>
<a class="nav-link" href="/superadmin/appointments"><i class="bi bi-calendar-event"></i> Appointments</a>
<a class="nav-link" href="/superadmin/children"><i class="bi bi-heart"></i> Children</a>
<hr class="my-2">
<div class="small text-muted px-3 mb-1">Admin tools</div>
<a class="nav-link" href="/admin/dashboard"><i class="bi bi-speedometer2"></i> Admin Dashboard</a>
<a class="nav-link" href="/admin/users"><i class="bi bi-people"></i> Admin Users</a>
<a class="nav-link" href="/admin/activity-logs"><i class="bi bi-journal-text"></i> Activity Logs</a>
<a class="nav-link" href="/admin/settings"><i class="bi bi-gear"></i> Clinic Settings</a>
<hr class="my-2">
<div class="small text-muted px-3 mb-1">Browse other roles</div>
<a class="nav-link" href="/doctor/dashboard"><i class="bi bi-prescription2"></i> Doctor View</a>
<a class="nav-link" href="/parent/dashboard"><i class="bi bi-heart-pulse"></i> Parent View</a>
';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Superadmin Console</h5>
        <small class="text-muted">Full access to every role and feature in PediCare.</small>
    </div>
    <a href="/superadmin/users" class="btn btn-primary btn-sm"><i class="bi bi-people-fill me-1"></i>Manage Users</a>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(255,107,154,.1);color:#FF6B9A;"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div style="font-size:.85rem;color:#888;">Total Users</div>
                    <div style="font-size:1.5rem;font-weight:700;"><?= $stats['total_users'] ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(0,123,255,.1);color:#007bff;"><i class="bi bi-prescription2"></i></div>
                <div>
                    <div style="font-size:.85rem;color:#888;">Doctors</div>
                    <div style="font-size:1.5rem;font-weight:700;"><?= $stats['total_doctors'] ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(40,167,69,.1);color:#28a745;"><i class="bi bi-heart-pulse"></i></div>
                <div>
                    <div style="font-size:.85rem;color:#888;">Parents</div>
                    <div style="font-size:1.5rem;font-weight:700;"><?= $stats['total_parents'] ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(220,53,69,.1);color:#dc3545;"><i class="bi bi-shield-check"></i></div>
                <div>
                    <div style="font-size:.85rem;color:#888;">Admins</div>
                    <div style="font-size:1.5rem;font-weight:700;"><?= $stats['total_admins'] ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Feature shortcuts: parent → admin -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card p-3">
            <h6 class="mb-3"><i class="bi bi-heart-pulse me-1"></i>Parent Features</h6>
            <div class="d-grid gap-2">
                <a class="btn btn-outline-secondary btn-sm text-start" href="/parent/dashboard"><i class="bi bi-house me-2"></i>Parent Dashboard</a>
                <a class="btn btn-outline-secondary btn-sm text-start" href="/parent/children"><i class="bi bi-people me-2"></i>Children</a>
                <a class="btn btn-outline-secondary btn-sm text-start" href="/parent/appointments"><i class="bi bi-calendar me-2"></i>Appointments</a>
                <a class="btn btn-outline-secondary btn-sm text-start" href="/parent/doctors"><i class="bi bi-prescription2 me-2"></i>Doctors</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card p-3">
            <h6 class="mb-3"><i class="bi bi-prescription2 me-1"></i>Doctor Features</h6>
            <div class="d-grid gap-2">
                <a class="btn btn-outline-secondary btn-sm text-start" href="/doctor/dashboard"><i class="bi bi-house me-2"></i>Doctor Dashboard</a>
                <a class="btn btn-outline-secondary btn-sm text-start" href="/doctor/appointments"><i class="bi bi-calendar-check me-2"></i>Appointments</a>
                <a class="btn btn-outline-secondary btn-sm text-start" href="/doctor/patients"><i class="bi bi-clipboard-pulse me-2"></i>Patients</a>
                <a class="btn btn-outline-secondary btn-sm text-start" href="/doctor/availability"><i class="bi bi-clock me-2"></i>Availability</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card p-3">
            <h6 class="mb-3"><i class="bi bi-speedometer2 me-1"></i>Admin Features</h6>
            <div class="d-grid gap-2">
                <a class="btn btn-outline-secondary btn-sm text-start" href="/admin/dashboard"><i class="bi bi-house me-2"></i>Admin Dashboard</a>
                <a class="btn btn-outline-secondary btn-sm text-start" href="/admin/users"><i class="bi bi-people me-2"></i>Manage Users</a>
                <a class="btn btn-outline-secondary btn-sm text-start" href="/admin/activity-logs"><i class="bi bi-journal-text me-2"></i>Activity Logs</a>
                <a class="btn btn-outline-secondary btn-sm text-start" href="/admin/settings"><i class="bi bi-gear me-2"></i>Clinic Settings</a>
            </div>
        </div>
    </div>
</div>

<!-- Superadmin-only actions + activity feed -->
<div class="row g-3">
    <div class="col-md-4">
        <div class="stat-card p-3">
            <h6 class="mb-3"><i class="bi bi-shield-lock me-1"></i>Superadmin Actions</h6>
            <div class="d-grid gap-2">
                <a class="btn btn-primary btn-sm text-start" href="/superadmin/users"><i class="bi bi-people-fill me-2"></i>User Management</a>
                <a class="btn btn-primary btn-sm text-start" href="/superadmin/appointments"><i class="bi bi-calendar-plus me-2"></i>Appointments</a>
                <a class="btn btn-primary btn-sm text-start" href="/superadmin/children"><i class="bi bi-heart me-2"></i>My Children</a>
                <a class="btn btn-outline-primary btn-sm text-start" href="/admin/export/users"><i class="bi bi-download me-2"></i>Export Users CSV</a>
                <a class="btn btn-outline-primary btn-sm text-start" href="/admin/activity-logs"><i class="bi bi-journal me-2"></i>View Audit Trail</a>
            </div>
            <div class="alert alert-warning small mt-3 mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Doctor roles are managed via the "Add Doctor" admin flow and cannot be changed or deleted from user management.
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="stat-card p-3">
            <h6 class="mb-3"><i class="bi bi-clock-history me-1"></i>Recent System Activity</h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>User</th><th>Action</th><th>Details</th><th>Time</th></tr></thead>
                    <tbody>
                    <?php if (empty($recentActivity)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No recent activity</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentActivity as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')) ?></td>
                            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($log['action']) ?></span></td>
                            <td style="max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($log['details'] ?? '') ?>"><?= htmlspecialchars($log['details'] ?? '-') ?></td>
                            <td style="font-size:.8rem;color:#888;"><?= date('M j, g:i A', strtotime($log['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
