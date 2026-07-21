<?php
$assetBase = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
    . rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');
?>
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
    <link href="<?= $assetBase ?>/frontend/assets/css/glass.css" rel="stylesheet">
    <link href="<?= $assetBase ?>/frontend/assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php if (isLoggedIn()): ?>
<?php $user = currentUser(); ?>
<div id="wrapper">
    <aside class="sidebar" id="sidebar">
        <header class="sidebar-header">
            <a href="?route=dashboard" class="header-logo">
                <svg width="40" height="40" viewBox="0 0 32 32" fill="none">
                    <rect width="32" height="32" rx="10" fill="url(#brand-grad)"/>
                    <path d="M16 6C11 6 8 10 8 15C8 20 11 26 16 26C21 26 24 20 24 15C24 10 21 6 16 6Z" fill="white" opacity="0.9"/>
                    <path d="M13 14C13 14 14.5 18 16 18C17.5 18 19 14 19 14" stroke="url(#brand-grad)" stroke-width="1.5" stroke-linecap="round"/>
                    <defs><linearGradient id="brand-grad" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#22c55e"/><stop offset="1" stop-color="#06b6d4"/></linearGradient></defs>
                </svg>
            </a>
            <button class="toggler sidebar-toggler"><i class="bi bi-chevron-left"></i></button>
            <button class="toggler menu-toggler"><i class="bi bi-list"></i></button>
        </header>

        <nav class="sidebar-nav">
            <ul class="nav-list primary-nav">
                <?php $currentRoute = $_GET['route'] ?? 'dashboard'; ?>

                <?php if (can('dashboard')): ?>
                <li class="nav-item">
                    <a href="?route=dashboard" class="nav-link<?= $currentRoute === 'dashboard' ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-grid-1x2-fill"></i>
                        <span class="nav-label">Dashboard</span>
                    </a>
                    <span class="nav-tooltip">Dashboard</span>
                </li>
                <?php endif; ?>

                <?php if (can('suppliers') || can('materials')): ?>
                <li class="nav-item"><span class="nav-section">SUPPLIERS & MATERIALS</span></li>
                <?php if (can('suppliers')): ?>
                <li class="nav-item">
                    <a href="?route=suppliers" class="nav-link<?= $currentRoute === 'suppliers' ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-truck"></i>
                        <span class="nav-label">Suppliers</span>
                    </a>
                    <span class="nav-tooltip">Suppliers</span>
                </li>
                <li class="nav-item">
                    <a href="?route=suppliers/deliveries" class="nav-link<?= str_starts_with($currentRoute, 'suppliers/deliveries') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-box-seam"></i>
                        <span class="nav-label">Deliveries</span>
                    </a>
                    <span class="nav-tooltip">Deliveries</span>
                </li>
                <?php endif; ?>
                <?php if (can('materials')): ?>
                <li class="nav-item">
                    <a href="?route=materials/raw" class="nav-link<?= str_starts_with($currentRoute, 'materials/raw') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-bar-chart-steps"></i>
                        <span class="nav-label">Raw Materials</span>
                    </a>
                    <span class="nav-tooltip">Raw Materials</span>
                </li>
                <li class="nav-item">
                    <a href="?route=materials/packaging" class="nav-link<?= str_starts_with($currentRoute, 'materials/packaging') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-box"></i>
                        <span class="nav-label">Packaging</span>
                    </a>
                    <span class="nav-tooltip">Packaging</span>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if (can('production') || can('finished_goods')): ?>
                <li class="nav-item"><span class="nav-section">PRODUCTION</span></li>
                <?php if (can('production')): ?>
                <li class="nav-item">
                    <a href="?route=production" class="nav-link<?= str_starts_with($currentRoute, 'production') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-gear-wide-connected"></i>
                        <span class="nav-label">Batches</span>
                    </a>
                    <span class="nav-tooltip">Batches</span>
                </li>
                <?php endif; ?>
                <?php if (can('quality')): ?>
                <li class="nav-item">
                    <a href="?route=quality" class="nav-link<?= str_starts_with($currentRoute, 'quality') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-patch-check"></i>
                        <span class="nav-label">Quality</span>
                    </a>
                    <span class="nav-tooltip">Quality</span>
                </li>
                <?php endif; ?>
                <?php if (can('finished_goods')): ?>
                <li class="nav-item">
                    <a href="?route=finished-goods" class="nav-link<?= str_starts_with($currentRoute, 'finished-goods') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-cup-straw"></i>
                        <span class="nav-label">Finished Goods</span>
                    </a>
                    <span class="nav-tooltip">Finished Goods</span>
                </li>
                <?php endif; ?>
                <?php if (can('efficiency')): ?>
                <li class="nav-item">
                    <a href="?route=efficiency" class="nav-link<?= str_starts_with($currentRoute, 'efficiency') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <span class="nav-label">OEE</span>
                    </a>
                    <span class="nav-tooltip">OEE</span>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if (can('customers') || can('sales') || can('invoicing')): ?>
                <li class="nav-item"><span class="nav-section">SALES</span></li>
                <?php if (can('customers')): ?>
                <li class="nav-item">
                    <a href="?route=customers" class="nav-link<?= str_starts_with($currentRoute, 'customers') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-people"></i>
                        <span class="nav-label">Customers</span>
                    </a>
                    <span class="nav-tooltip">Customers</span>
                </li>
                <?php endif; ?>
                <?php if (can('sales')): ?>
                <li class="nav-item">
                    <a href="?route=sales" class="nav-link<?= str_starts_with($currentRoute, 'sales') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-cart3"></i>
                        <span class="nav-label">Orders</span>
                    </a>
                    <span class="nav-tooltip">Orders</span>
                </li>
                <?php endif; ?>
                <?php if (can('invoicing')): ?>
                <li class="nav-item">
                    <a href="?route=invoicing" class="nav-link<?= str_starts_with($currentRoute, 'invoicing') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-receipt"></i>
                        <span class="nav-label">Invoicing</span>
                    </a>
                    <span class="nav-tooltip">Invoicing</span>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if (can('safety') || can('hazards') || can('accidents') || can('drills') || can('permits') || can('documents')): ?>
                <li class="nav-item"><span class="nav-section">SAFETY & COMPLIANCE</span></li>
                <?php if (can('safety')): ?>
                <li class="nav-item">
                    <a href="?route=safety" class="nav-link<?= $currentRoute === 'safety' ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-shield-check"></i>
                        <span class="nav-label">Inspections</span>
                    </a>
                    <span class="nav-tooltip">Inspections</span>
                </li>
                <?php endif; ?>
                <?php if (can('hazards')): ?>
                <li class="nav-item">
                    <a href="?route=safety/hazards" class="nav-link<?= str_starts_with($currentRoute, 'safety/hazards') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-exclamation-triangle"></i>
                        <span class="nav-label">Hazards</span>
                    </a>
                    <span class="nav-tooltip">Hazards</span>
                </li>
                <?php endif; ?>
                <?php if (can('accidents')): ?>
                <li class="nav-item">
                    <a href="?route=safety/accidents" class="nav-link<?= str_starts_with($currentRoute, 'safety/accidents') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-activity"></i>
                        <span class="nav-label">Accidents</span>
                    </a>
                    <span class="nav-tooltip">Accidents</span>
                </li>
                <?php endif; ?>
                <?php if (can('drills')): ?>
                <li class="nav-item">
                    <a href="?route=safety/drills" class="nav-link<?= str_starts_with($currentRoute, 'safety/drills') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-alarm"></i>
                        <span class="nav-label">Drills</span>
                    </a>
                    <span class="nav-tooltip">Drills</span>
                </li>
                <?php endif; ?>
                <?php if (can('permits')): ?>
                <li class="nav-item">
                    <a href="?route=permits" class="nav-link<?= str_starts_with($currentRoute, 'permits') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-file-earmark-check"></i>
                        <span class="nav-label">Permits</span>
                    </a>
                    <span class="nav-tooltip">Permits</span>
                </li>
                <?php endif; ?>
                <?php if (can('documents')): ?>
                <li class="nav-item">
                    <a href="?route=documents" class="nav-link<?= str_starts_with($currentRoute, 'documents') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-folder2-open"></i>
                        <span class="nav-label">Documents</span>
                    </a>
                    <span class="nav-tooltip">Documents</span>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if (can('staff') || can('training') || can('ppe')): ?>
                <li class="nav-item"><span class="nav-section">WORKFORCE</span></li>
                <?php if (can('staff')): ?>
                <li class="nav-item">
                    <a href="?route=staff" class="nav-link<?= str_starts_with($currentRoute, 'staff') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-person-badge"></i>
                        <span class="nav-label">Staff</span>
                    </a>
                    <span class="nav-tooltip">Staff</span>
                </li>
                <?php endif; ?>
                <?php if (can('training')): ?>
                <li class="nav-item">
                    <a href="?route=training" class="nav-link<?= str_starts_with($currentRoute, 'training') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-mortarboard"></i>
                        <span class="nav-label">Training</span>
                    </a>
                    <span class="nav-tooltip">Training</span>
                </li>
                <?php endif; ?>
                <?php if (can('ppe')): ?>
                <li class="nav-item">
                    <a href="?route=ppe" class="nav-link<?= str_starts_with($currentRoute, 'ppe') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-shield-fill"></i>
                        <span class="nav-label">PPE</span>
                    </a>
                    <span class="nav-tooltip">PPE</span>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if (can('machines') || can('maintenance') || can('fat') || can('waste') || can('water') || can('power')): ?>
                <li class="nav-item"><span class="nav-section">ASSETS</span></li>
                <?php if (can('machines')): ?>
                <li class="nav-item">
                    <a href="?route=machines" class="nav-link<?= str_starts_with($currentRoute, 'machines') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-cpu"></i>
                        <span class="nav-label">Machines</span>
                    </a>
                    <span class="nav-tooltip">Machines</span>
                </li>
                <?php endif; ?>
                <?php if (can('maintenance')): ?>
                <li class="nav-item">
                    <a href="?route=maintenance" class="nav-link<?= str_starts_with($currentRoute, 'maintenance') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-wrench"></i>
                        <span class="nav-label">Maintenance</span>
                    </a>
                    <span class="nav-tooltip">Maintenance</span>
                </li>
                <?php endif; ?>
                <?php if (can('fat')): ?>
                <li class="nav-item">
                    <a href="?route=fat" class="nav-link<?= str_starts_with($currentRoute, 'fat') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-clipboard-check"></i>
                        <span class="nav-label">FAT</span>
                    </a>
                    <span class="nav-tooltip">FAT</span>
                </li>
                <?php endif; ?>
                <?php if (can('waste')): ?>
                <li class="nav-item">
                    <a href="?route=waste" class="nav-link<?= str_starts_with($currentRoute, 'waste') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-trash"></i>
                        <span class="nav-label">Waste</span>
                    </a>
                    <span class="nav-tooltip">Waste</span>
                </li>
                <?php endif; ?>
                <?php if (can('water')): ?>
                <li class="nav-item">
                    <a href="?route=water" class="nav-link<?= str_starts_with($currentRoute, 'water') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-droplet"></i>
                        <span class="nav-label">Water</span>
                    </a>
                    <span class="nav-tooltip">Water</span>
                </li>
                <?php endif; ?>
                <?php if (can('power')): ?>
                <li class="nav-item">
                    <a href="?route=power" class="nav-link<?= str_starts_with($currentRoute, 'power') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-lightning"></i>
                        <span class="nav-label">Power</span>
                    </a>
                    <span class="nav-tooltip">Power</span>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if (can('improvement') || can('supplier_eval')): ?>
                <li class="nav-item"><span class="nav-section">IMPROVEMENT</span></li>
                <?php if (can('improvement')): ?>
                <li class="nav-item">
                    <a href="?route=improvement" class="nav-link<?= str_starts_with($currentRoute, 'improvement') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-lightbulb"></i>
                        <span class="nav-label">CAPA</span>
                    </a>
                    <span class="nav-tooltip">CAPA</span>
                </li>
                <?php endif; ?>
                <?php if (can('supplier_eval')): ?>
                <li class="nav-item">
                    <a href="?route=supplier-evaluations" class="nav-link<?= str_starts_with($currentRoute, 'supplier-evaluations') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-clipboard-data"></i>
                        <span class="nav-label">Evaluations</span>
                    </a>
                    <span class="nav-tooltip">Evaluations</span>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if (can('certifications')): ?>
                <li class="nav-item"><span class="nav-section">COMPLIANCE</span></li>
                <li class="nav-item">
                    <a href="?route=certifications" class="nav-link<?= str_starts_with($currentRoute, 'certifications') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-award"></i>
                        <span class="nav-label">Certifications</span>
                    </a>
                    <span class="nav-tooltip">Certifications</span>
                </li>
                <?php endif; ?>

                <?php if (can('users') || can('sops')): ?>
                <li class="nav-item"><span class="nav-section">ADMIN</span></li>
                <?php if (can('users')): ?>
                <li class="nav-item">
                    <a href="?route=users" class="nav-link<?= str_starts_with($currentRoute, 'users') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-person-gear"></i>
                        <span class="nav-label">Users</span>
                    </a>
                    <span class="nav-tooltip">Users</span>
                </li>
                <?php endif; ?>
                <?php if (can('sops')): ?>
                <li class="nav-item">
                    <a href="?route=sops" class="nav-link<?= str_starts_with($currentRoute, 'sops') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-file-text"></i>
                        <span class="nav-label">SOPs</span>
                    </a>
                    <span class="nav-tooltip">SOPs</span>
                </li>
                <?php endif; ?>
                <?php endif; ?>
            </ul>

            <ul class="nav-list secondary-nav">
                <li class="nav-item">
                    <a href="?route=dashboard" class="nav-link">
                        <i class="nav-icon bi bi-person-circle"></i>
                        <span class="nav-label"><?= sanitize($user['name']) ?></span>
                    </a>
                    <span class="nav-tooltip"><?= sanitize($user['name']) ?></span>
                </li>
                <li class="nav-item">
                    <a href="?route=auth/logout" class="nav-link">
                        <i class="nav-icon bi bi-box-arrow-right"></i>
                        <span class="nav-label">Sign Out</span>
                    </a>
                    <span class="nav-tooltip">Sign Out</span>
                </li>
            </ul>
        </nav>
    </aside>

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
<script src="<?= $assetBase ?>/frontend/assets/js/app.js"></script>
<script>
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var wrapper = document.getElementById('wrapper');
            if (wrapper && wrapper.classList.contains('dashboard-fullscreen')) {
                if (typeof toggleDashboardFullscreen === 'function') toggleDashboardFullscreen();
            }
        }
    });
</script>
</body>
</html>
