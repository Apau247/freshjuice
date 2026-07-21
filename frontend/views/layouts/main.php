<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($pageTitle ?? APP_NAME) ?> - <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="<?= APP_ROOT ?>/assets/css/glass.css" rel="stylesheet">
    <link href="<?= APP_ROOT ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php if (isLoggedIn()): ?>
<?php $user = currentUser(); ?>
<div class="d-flex" id="wrapper">
    <div class="bg-dark sidebar" id="sidebar">
        <div class="sidebar-heading text-center py-3 text-light fs-5 fw-bold">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" style="vertical-align:middle;margin-right:0.5rem;">
                <rect width="32" height="32" rx="10" fill="url(#brand-grad)"/>
                <path d="M16 6C11 6 8 10 8 15C8 20 11 26 16 26C21 26 24 20 24 15C24 10 21 6 16 6Z" fill="white" opacity="0.9"/>
                <path d="M13 14C13 14 14.5 18 16 18C17.5 18 19 14 19 14" stroke="url(#brand-grad)" stroke-width="1.5" stroke-linecap="round"/>
                <defs><linearGradient id="brand-grad" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#22c55e"/><stop offset="1" stop-color="#06b6d4"/></linearGradient></defs>
            </svg>
            FreshJuice
        </div>
        <?php $currentRoute = $_GET['route'] ?? 'dashboard'; ?>
        <ul class="nav flex-column">
            <?php if (can('dashboard')): ?>
            <li class="nav-item">
                <a href="?route=dashboard" class="nav-link sidebar-link<?= $currentRoute === 'dashboard' ? ' active' : '' ?>"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
            </li>
            <?php endif; ?>

            <?php if (can('suppliers') || can('materials')): ?>
            <li class="nav-item mt-2"><small class="sidebar-section">SUPPLIERS &amp; MATERIALS</small></li>
            <?php if (can('suppliers')): ?>
            <li class="nav-item"><a href="?route=suppliers" class="nav-link sidebar-link<?= $currentRoute === 'suppliers' ? ' active' : '' ?>"><i class="bi bi-truck"></i><span>Suppliers</span></a></li>
            <li class="nav-item"><a href="?route=suppliers/deliveries" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'suppliers/deliveries') ? ' active' : '' ?>"><i class="bi bi-box-seam"></i><span>Deliveries</span></a></li>
            <?php endif; ?>
            <?php if (can('materials')): ?>
            <li class="nav-item"><a href="?route=materials/raw" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'materials/raw') ? ' active' : '' ?>"><i class="bi bi-bar-chart-steps"></i><span>Raw Materials</span></a></li>
            <li class="nav-item"><a href="?route=materials/packaging" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'materials/packaging') ? ' active' : '' ?>"><i class="bi bi-box"></i><span>Packaging</span></a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('production') || can('finished_goods')): ?>
            <li class="nav-item mt-2"><small class="sidebar-section">PRODUCTION</small></li>
            <?php if (can('production')): ?>
            <li class="nav-item"><a href="?route=production" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'production') ? ' active' : '' ?>"><i class="bi bi-gear-wide-connected"></i><span>Batches</span></a></li>
            <?php endif; ?>
            <?php if (can('quality')): ?>
            <li class="nav-item"><a href="?route=quality" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'quality') ? ' active' : '' ?>"><i class="bi bi-patch-check"></i><span>Quality</span></a></li>
            <?php endif; ?>
            <?php if (can('finished_goods')): ?>
            <li class="nav-item"><a href="?route=finished-goods" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'finished-goods') ? ' active' : '' ?>"><i class="bi bi-cup-straw"></i><span>Finished Goods</span></a></li>
            <?php endif; ?>
            <?php if (can('efficiency')): ?>
            <li class="nav-item"><a href="?route=efficiency" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'efficiency') ? ' active' : '' ?>"><i class="bi bi-speedometer2"></i><span>OEE</span></a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('customers') || can('sales') || can('invoicing')): ?>
            <li class="nav-item mt-2"><small class="sidebar-section">SALES</small></li>
            <?php if (can('customers')): ?>
            <li class="nav-item"><a href="?route=customers" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'customers') ? ' active' : '' ?>"><i class="bi bi-people"></i><span>Customers</span></a></li>
            <?php endif; ?>
            <?php if (can('sales')): ?>
            <li class="nav-item"><a href="?route=sales" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'sales') ? ' active' : '' ?>"><i class="bi bi-cart3"></i><span>Orders</span></a></li>
            <?php endif; ?>
            <?php if (can('invoicing')): ?>
            <li class="nav-item"><a href="?route=invoicing" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'invoicing') ? ' active' : '' ?>"><i class="bi bi-receipt"></i><span>Invoicing</span></a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('safety') || can('hazards') || can('accidents') || can('drills') || can('permits') || can('documents')): ?>
            <li class="nav-item mt-2"><small class="sidebar-section">SAFETY &amp; COMPLIANCE</small></li>
            <?php if (can('safety')): ?>
            <li class="nav-item"><a href="?route=safety" class="nav-link sidebar-link<?= $currentRoute === 'safety' ? ' active' : '' ?>"><i class="bi bi-shield-check"></i><span>Inspections</span></a></li>
            <?php endif; ?>
            <?php if (can('hazards')): ?>
            <li class="nav-item"><a href="?route=safety/hazards" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'safety/hazards') ? ' active' : '' ?>"><i class="bi bi-exclamation-triangle"></i><span>Hazards</span></a></li>
            <?php endif; ?>
            <?php if (can('accidents')): ?>
            <li class="nav-item"><a href="?route=safety/accidents" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'safety/accidents') ? ' active' : '' ?>"><i class="bi bi-activity"></i><span>Accidents</span></a></li>
            <?php endif; ?>
            <?php if (can('drills')): ?>
            <li class="nav-item"><a href="?route=safety/drills" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'safety/drills') ? ' active' : '' ?>"><i class="bi bi-alarm"></i><span>Drills</span></a></li>
            <?php endif; ?>
            <?php if (can('permits')): ?>
            <li class="nav-item"><a href="?route=permits" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'permits') ? ' active' : '' ?>"><i class="bi bi-file-earmark-check"></i><span>Permits</span></a></li>
            <?php endif; ?>
            <?php if (can('documents')): ?>
            <li class="nav-item"><a href="?route=documents" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'documents') ? ' active' : '' ?>"><i class="bi bi-folder2-open"></i><span>Documents</span></a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('staff') || can('training') || can('ppe')): ?>
            <li class="nav-item mt-2"><small class="sidebar-section">WORKFORCE</small></li>
            <?php if (can('staff')): ?>
            <li class="nav-item"><a href="?route=staff" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'staff') ? ' active' : '' ?>"><i class="bi bi-person-badge"></i><span>Staff</span></a></li>
            <?php endif; ?>
            <?php if (can('training')): ?>
            <li class="nav-item"><a href="?route=training" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'training') ? ' active' : '' ?>"><i class="bi bi-mortarboard"></i><span>Training</span></a></li>
            <?php endif; ?>
            <?php if (can('ppe')): ?>
            <li class="nav-item"><a href="?route=ppe" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'ppe') ? ' active' : '' ?>"><i class="bi bi-shield-fill"></i><span>PPE</span></a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('machines') || can('maintenance') || can('fat') || can('waste') || can('water') || can('power')): ?>
            <li class="nav-item mt-2"><small class="sidebar-section">ASSETS</small></li>
            <?php if (can('machines')): ?>
            <li class="nav-item"><a href="?route=machines" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'machines') ? ' active' : '' ?>"><i class="bi bi-cpu"></i><span>Machines</span></a></li>
            <?php endif; ?>
            <?php if (can('maintenance')): ?>
            <li class="nav-item"><a href="?route=maintenance" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'maintenance') ? ' active' : '' ?>"><i class="bi bi-wrench"></i><span>Maintenance</span></a></li>
            <?php endif; ?>
            <?php if (can('fat')): ?>
            <li class="nav-item"><a href="?route=fat" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'fat') ? ' active' : '' ?>"><i class="bi bi-clipboard-check"></i><span>FAT</span></a></li>
            <?php endif; ?>
            <?php if (can('waste')): ?>
            <li class="nav-item"><a href="?route=waste" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'waste') ? ' active' : '' ?>"><i class="bi bi-trash"></i><span>Waste</span></a></li>
            <?php endif; ?>
            <?php if (can('water')): ?>
            <li class="nav-item"><a href="?route=water" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'water') ? ' active' : '' ?>"><i class="bi bi-droplet"></i><span>Water</span></a></li>
            <?php endif; ?>
            <?php if (can('power')): ?>
            <li class="nav-item"><a href="?route=power" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'power') ? ' active' : '' ?>"><i class="bi bi-lightning"></i><span>Power</span></a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('improvement') || can('supplier_eval')): ?>
            <li class="nav-item mt-2"><small class="sidebar-section">IMPROVEMENT</small></li>
            <?php if (can('improvement')): ?>
            <li class="nav-item"><a href="?route=improvement" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'improvement') ? ' active' : '' ?>"><i class="bi bi-lightbulb"></i><span>CAPA</span></a></li>
            <?php endif; ?>
            <?php if (can('supplier_eval')): ?>
            <li class="nav-item"><a href="?route=supplier-evaluations" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'supplier-evaluations') ? ' active' : '' ?>"><i class="bi bi-clipboard-data"></i><span>Evaluations</span></a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('certifications')): ?>
            <li class="nav-item mt-2"><small class="sidebar-section">COMPLIANCE</small></li>
            <li class="nav-item"><a href="?route=certifications" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'certifications') ? ' active' : '' ?>"><i class="bi bi-award"></i><span>Certifications</span></a></li>
            <?php endif; ?>

            <?php if (can('users') || can('sops')): ?>
            <li class="nav-item mt-2"><small class="sidebar-section">ADMIN</small></li>
            <?php if (can('users')): ?>
            <li class="nav-item"><a href="?route=users" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'users') ? ' active' : '' ?>"><i class="bi bi-person-gear"></i><span>Users</span></a></li>
            <?php endif; ?>
            <?php if (can('sops')): ?>
            <li class="nav-item"><a href="?route=sops" class="nav-link sidebar-link<?= str_starts_with($currentRoute, 'sops') ? ' active' : '' ?>"><i class="bi bi-file-text"></i><span>SOPs</span></a></li>
            <?php endif; ?>
            <?php endif; ?>
        </ul>
    </div>
    <div id="page-content-wrapper" class="w-100">
        <nav class="navbar navbar-expand-lg px-4 py-2">
            <div class="d-flex align-items-center w-100">
                <button class="btn btn-sm btn-outline-secondary me-3" id="sidebarToggle"><i class="bi bi-list"></i></button>
                <span class="fw-semibold fs-5"><?= sanitize($pageTitle ?? 'Dashboard') ?></span>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:30px;height:30px;border-radius:9px;background:var(--gradient-brand);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.75rem;"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
                        <span class="fw-medium text-muted" style="font-size:0.82rem;"><?= sanitize($user['name']) ?></span>
                    </div>
                    <a href="?route=auth/logout" class="btn btn-outline-danger" title="Sign out"><i class="bi bi-box-arrow-right"></i></a>
                </div>
            </div>
        </nav>
        <div class="container-fluid p-4">
            <?php $flash = getFlash(); if ($flash): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({ icon: '<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'error' : 'info') ?>', title: '<?= addslashes($flash['message']) ?>', toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
                });
            </script>
            <?php endif; ?>
            <?= $content ?>
        </div>
    </div>
</div>
<?php else: ?>
<?= $content ?>
<?php endif; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?= APP_ROOT ?>/assets/js/app.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $.fn.DataTable !== 'undefined') {
            $('.table').each(function() {
                if ($(this).find('th').length > 0 && !$(this).closest('.no-datatable').length) {
                    try { $(this).DataTable({ order: [], pageLength: 25, language: { search: '', searchPlaceholder: 'Search...' }, responsive: true }); } catch(e) {}
                }
            });
        }
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('wrapper').classList.toggle('toggled');
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                var wrapper = document.getElementById('wrapper');
                if (wrapper && wrapper.classList.contains('dashboard-fullscreen')) {
                    if (typeof toggleDashboardFullscreen === 'function') toggleDashboardFullscreen();
                }
            }
        });
    });
</script>
</body>
</html>
