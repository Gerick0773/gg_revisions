<?php
$layout = 'app';
$pageTitle = 'Children - Superadmin';
$breadcrumbs = [['label' => 'Superadmin', 'url' => '/superadmin/dashboard'], ['label' => 'Children']];
$sidebarNav = '
<a class="nav-link" href="/superadmin/dashboard"><i class="bi bi-shield-lock"></i> Superadmin</a>
<a class="nav-link" href="/superadmin/users"><i class="bi bi-people-fill"></i> User Management</a>
<a class="nav-link" href="/superadmin/appointments"><i class="bi bi-calendar-event"></i> Appointments</a>
<a class="nav-link active" href="/superadmin/children"><i class="bi bi-heart"></i> Children</a>
<hr class="my-2">
<a class="nav-link" href="/admin/dashboard"><i class="bi bi-speedometer2"></i> Admin Dashboard</a>
';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0"><i class="bi bi-heart me-2"></i>My Children Registry</h5>
        <small class="text-muted">Every patient (child) on the platform across all parent accounts.</small>
    </div>
    <a href="/superadmin/appointments" class="btn btn-primary btn-sm"><i class="bi bi-calendar-plus me-1"></i>Book Appointment</a>
</div>

<div class="stat-card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-9">
            <label class="form-label small">Search by child name, parent name or parent email</label>
            <input type="text" name="search" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="e.g. Maria, mom@example.com">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary btn-sm w-100">Search</button>
        </div>
    </form>
</div>

<div class="row g-3">
    <?php if (empty($children)): ?>
        <div class="col-12">
            <div class="stat-card text-center py-5">
                <i class="bi bi-person-heart" style="font-size:4rem;color:#ddd;"></i>
                <h5 class="text-muted mt-3">No children found</h5>
                <p class="text-muted">Try a different search.</p>
            </div>
        </div>
    <?php else: foreach ($children as $c): ?>
        <div class="col-md-6 col-lg-4">
            <div class="stat-card h-100">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:54px;height:54px;background:<?= $c['gender'] === 'MALE' ? 'rgba(0,123,255,.1)' : 'rgba(255,107,154,.1)' ?>;">
                        <i class="bi bi-person-fill" style="color:<?= $c['gender'] === 'MALE' ? '#007bff' : '#FF6B9A' ?>;font-size:1.5rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1"><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></h6>
                        <div class="text-muted small mb-1">
                            <?= date('M j, Y', strtotime($c['date_of_birth'])) ?>
                            (<?= (int)((time()-strtotime($c['date_of_birth']))/31557600) ?>y)
                            • <?= htmlspecialchars($c['gender']) ?>
                            <?php if ($c['blood_type']): ?> • <?= htmlspecialchars($c['blood_type']) ?><?php endif; ?>
                        </div>
                        <?php if (!empty($c['allergies'])): ?>
                            <div class="small text-danger"><i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($c['allergies']) ?></div>
                        <?php endif; ?>
                        <hr class="my-2">
                        <div class="small">
                            <div class="text-muted">Parent</div>
                            <strong><?= htmlspecialchars($c['parent_first_name'] . ' ' . $c['parent_last_name']) ?></strong><br>
                            <a href="mailto:<?= htmlspecialchars($c['parent_email']) ?>" class="text-decoration-none"><?= htmlspecialchars($c['parent_email']) ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<?php if (($pagination['total_pages'] ?? 0) > 1): ?>
<nav class="mt-3">
    <ul class="pagination pagination-sm justify-content-center mb-0">
        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
