<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($pageTitle ?? APP_NAME) ?> - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="<?= APP_ROOT ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php if (isLoggedIn()): ?>
<?php $user = currentUser(); ?>
<div class="d-flex" id="wrapper">
    <div class="bg-dark sidebar" id="sidebar">
        <div class="sidebar-heading text-center py-4 text-light fs-4 fw-bold">
            <i class="bi bi-droplet-half"></i> FreshJuice
        </div>
        <hr class="text-secondary my-0">
        <ul class="nav flex-column">
            <?php if (can('dashboard')): ?>
            <li class="nav-item">
                <a href="?route=dashboard" class="nav-link sidebar-link"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            </li>
            <?php endif; ?>

            <?php if (can('suppliers') || can('materials')): ?>
            <li class="nav-item mt-2">
                <small class="text-secondary text-uppercase px-3 fw-bold">SUPPLIERS &amp; MATERIALS</small>
            </li>
            <?php if (can('suppliers')): ?>
            <li class="nav-item"><a href="?route=suppliers" class="nav-link sidebar-link"><i class="bi bi-truck me-2"></i>Suppliers</a></li>
            <li class="nav-item"><a href="?route=suppliers/deliveries" class="nav-link sidebar-link"><i class="bi bi-box-seam me-2"></i>Deliveries</a></li>
            <?php endif; ?>
            <?php if (can('materials')): ?>
            <li class="nav-item"><a href="?route=materials/raw" class="nav-link sidebar-link"><i class="bi bi-bar-chart-steps me-2"></i>Raw Materials</a></li>
            <li class="nav-item"><a href="?route=materials/packaging" class="nav-link sidebar-link"><i class="bi bi-box me-2"></i>Packaging Materials</a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('production') || can('finished_goods')): ?>
            <li class="nav-item mt-2">
                <small class="text-secondary text-uppercase px-3 fw-bold">PRODUCTION</small>
            </li>
            <?php if (can('production')): ?>
            <li class="nav-item"><a href="?route=production" class="nav-link sidebar-link"><i class="bi bi-gear me-2"></i>Production Batches</a></li>
            <?php endif; ?>
            <?php if (can('finished_goods')): ?>
            <li class="nav-item"><a href="?route=finished-goods" class="nav-link sidebar-link"><i class="bi bi-cup-straw me-2"></i>Finished Goods</a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('quality') || can('certifications')): ?>
            <li class="nav-item mt-2">
                <small class="text-secondary text-uppercase px-3 fw-bold">QUALITY</small>
            </li>
            <?php if (can('quality')): ?>
            <li class="nav-item"><a href="?route=quality" class="nav-link sidebar-link"><i class="bi bi-check-circle me-2"></i>Quality Inspections</a></li>
            <?php endif; ?>
            <?php if (can('certifications')): ?>
            <li class="nav-item"><a href="?route=certifications" class="nav-link sidebar-link"><i class="bi bi-award me-2"></i>Certifications</a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('customers') || can('sales') || can('invoicing')): ?>
            <li class="nav-item mt-2">
                <small class="text-secondary text-uppercase px-3 fw-bold">SALES</small>
            </li>
            <?php if (can('customers')): ?>
            <li class="nav-item"><a href="?route=customers" class="nav-link sidebar-link"><i class="bi bi-people me-2"></i>Customers</a></li>
            <?php endif; ?>
            <?php if (can('sales')): ?>
            <li class="nav-item"><a href="?route=sales" class="nav-link sidebar-link"><i class="bi bi-cart me-2"></i>Sales Orders</a></li>
            <?php endif; ?>
            <?php if (can('invoicing')): ?>
            <li class="nav-item"><a href="?route=invoicing" class="nav-link sidebar-link"><i class="bi bi-receipt me-2"></i>Invoicing</a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('efficiency')): ?>
            <li class="nav-item mt-2">
                <small class="text-secondary text-uppercase px-3 fw-bold">PRODUCTION</small>
            </li>
            <li class="nav-item"><a href="?route=efficiency" class="nav-link sidebar-link"><i class="bi bi-graph-up me-2"></i>OEE / Efficiency</a></li>
            <?php endif; ?>

            <?php if (can('safety') || can('hazards') || can('accidents') || can('drills') || can('permits') || can('documents')): ?>
            <li class="nav-item mt-2">
                <small class="text-secondary text-uppercase px-3 fw-bold">SAFETY &amp; COMPLIANCE</small>
            </li>
            <?php if (can('safety')): ?>
            <li class="nav-item"><a href="?route=safety" class="nav-link sidebar-link"><i class="bi bi-shield-check me-2"></i>Safety Inspections</a></li>
            <?php endif; ?>
            <?php if (can('hazards')): ?>
            <li class="nav-item"><a href="?route=safety/hazards" class="nav-link sidebar-link"><i class="bi bi-exclamation-triangle me-2"></i>Hazard Register</a></li>
            <?php endif; ?>
            <?php if (can('accidents')): ?>
            <li class="nav-item"><a href="?route=safety/accidents" class="nav-link sidebar-link"><i class="bi bi-activity me-2"></i>Accidents</a></li>
            <?php endif; ?>
            <?php if (can('drills')): ?>
            <li class="nav-item"><a href="?route=safety/drills" class="nav-link sidebar-link"><i class="bi bi-alarm me-2"></i>Emergency Drills</a></li>
            <?php endif; ?>
            <?php if (can('permits')): ?>
            <li class="nav-item"><a href="?route=permits" class="nav-link sidebar-link"><i class="bi bi-file-earmark-check me-2"></i>Permits &amp; Licenses</a></li>
            <?php endif; ?>
            <?php if (can('documents')): ?>
            <li class="nav-item"><a href="?route=documents" class="nav-link sidebar-link"><i class="bi bi-folder2-open me-2"></i>Document Control</a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('staff') || can('training') || can('ppe')): ?>
            <li class="nav-item mt-2">
                <small class="text-secondary text-uppercase px-3 fw-bold">WORKFORCE</small>
            </li>
            <?php if (can('staff')): ?>
            <li class="nav-item"><a href="?route=staff" class="nav-link sidebar-link"><i class="bi bi-person-badge me-2"></i>Staff &amp; Shifts</a></li>
            <?php endif; ?>
            <?php if (can('training')): ?>
            <li class="nav-item"><a href="?route=training" class="nav-link sidebar-link"><i class="bi bi-mortarboard me-2"></i>Training</a></li>
            <?php endif; ?>
            <?php if (can('ppe')): ?>
            <li class="nav-item"><a href="?route=ppe" class="nav-link sidebar-link"><i class="bi bi-shield-fill me-2"></i>PPE Tracking</a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('machines') || can('maintenance') || can('fat') || can('waste') || can('water') || can('power')): ?>
            <li class="nav-item mt-2">
                <small class="text-secondary text-uppercase px-3 fw-bold">ASSETS</small>
            </li>
            <?php if (can('machines')): ?>
            <li class="nav-item"><a href="?route=machines" class="nav-link sidebar-link"><i class="bi bi-cpu me-2"></i>Machines</a></li>
            <?php endif; ?>
            <?php if (can('maintenance')): ?>
            <li class="nav-item"><a href="?route=maintenance" class="nav-link sidebar-link"><i class="bi bi-wrench me-2"></i>Maintenance</a></li>
            <?php endif; ?>
            <?php if (can('fat')): ?>
            <li class="nav-item"><a href="?route=fat" class="nav-link sidebar-link"><i class="bi bi-clipboard-check me-2"></i>FAT Records</a></li>
            <?php endif; ?>
            <?php if (can('waste')): ?>
            <li class="nav-item"><a href="?route=waste" class="nav-link sidebar-link"><i class="bi bi-trash me-2"></i>Waste</a></li>
            <?php endif; ?>
            <?php if (can('water')): ?>
            <li class="nav-item"><a href="?route=water" class="nav-link sidebar-link"><i class="bi bi-droplet me-2"></i>Water</a></li>
            <?php endif; ?>
            <?php if (can('power')): ?>
            <li class="nav-item"><a href="?route=power" class="nav-link sidebar-link"><i class="bi bi-lightning me-2"></i>Power</a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('improvement') || can('supplier_eval')): ?>
            <li class="nav-item mt-2">
                <small class="text-secondary text-uppercase px-3 fw-bold">CONTINUOUS IMPROVEMENT</small>
            </li>
            <?php if (can('improvement')): ?>
            <li class="nav-item"><a href="?route=improvement" class="nav-link sidebar-link"><i class="bi bi-lightbulb me-2"></i>CAPA / Initiatives</a></li>
            <?php endif; ?>
            <?php if (can('supplier_eval')): ?>
            <li class="nav-item"><a href="?route=supplier-evaluations" class="nav-link sidebar-link"><i class="bi bi-clipboard-data me-2"></i>Supplier Evaluations</a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (can('users') || can('sops')): ?>
            <li class="nav-item mt-2">
                <small class="text-secondary text-uppercase px-3 fw-bold">ADMIN</small>
            </li>
            <?php if (can('users')): ?>
            <li class="nav-item"><a href="?route=users" class="nav-link sidebar-link"><i class="bi bi-shield-lock me-2"></i>Users</a></li>
            <?php endif; ?>
            <?php if (can('sops')): ?>
            <li class="nav-item"><a href="?route=sops" class="nav-link sidebar-link"><i class="bi bi-file-text me-2"></i>SOPs</a></li>
            <?php endif; ?>
            <?php endif; ?>
        </ul>
    </div>
    <div id="page-content-wrapper" class="w-100">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4 py-2">
            <div class="d-flex align-items-center w-100">
                <button class="btn btn-sm btn-outline-secondary me-3" id="sidebarToggle"><i class="bi bi-list"></i></button>
                <span class="fw-semibold fs-5"><?= sanitize($pageTitle ?? 'Dashboard') ?></span>
                <div class="ms-auto d-flex align-items-center">
                    <span class="badge bg-primary me-3"><i class="bi bi-person-circle me-1"></i><?= sanitize($user['name']) ?> (<?= sanitize($user['role_name']) ?>)</span>
                    <a href="?route=auth/logout" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
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
    });
</script>
</body>
</html>
