<?php $assetBase = appBaseUrl(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= generateCsrfToken() ?>">
    <title><?= sanitize($pageTitle ?? APP_NAME) ?> - <?= APP_NAME ?></title>
    <link href="<?= $assetBase ?>/frontend/assets/vendor/fonts/inter.css" rel="stylesheet">
    <link href="<?= $assetBase ?>/frontend/assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $assetBase ?>/frontend/assets/vendor/icons/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= $assetBase ?>/frontend/assets/vendor/datatables/dataTables.bootstrap5.min.css" rel="stylesheet">
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
                <li class="nav-item">
                    <a href="?route=notifications" class="nav-link<?= str_starts_with($currentRoute, 'notifications') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-bell"></i>
                        <span class="nav-label">Notifications</span>
                    </a>
                    <span class="nav-tooltip">Notifications</span>
                </li>

                <?php if (can('inventory_alerts')): ?>
                <li class="nav-item"><span class="nav-section">INVENTORY MANAGEMENT</span></li>
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
                        <span class="nav-label">Packaging Materials</span>
                    </a>
                    <span class="nav-tooltip">Packaging Materials</span>
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
                <li class="nav-item">
                    <a href="?route=alerts/low-stock" class="nav-link<?= str_starts_with($currentRoute, 'alerts/low-stock') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-exclamation-octagon"></i>
                        <span class="nav-label">Low Stock Alerts</span>
                    </a>
                    <span class="nav-tooltip">Low Stock Alerts</span>
                </li>
                <li class="nav-item">
                    <a href="?route=alerts/expiry" class="nav-link<?= str_starts_with($currentRoute, 'alerts/expiry') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-hourglass-split"></i>
                        <span class="nav-label">Expiry Alerts</span>
                    </a>
                    <span class="nav-tooltip">Expiry Alerts</span>
                </li>
                <?php endif; ?>

                <?php if (can('production')): ?>
                <li class="nav-item"><span class="nav-section">PRODUCTION</span></li>
                <li class="nav-item">
                    <a href="?route=production" class="nav-link<?= str_starts_with($currentRoute, 'production') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-gear-wide-connected"></i>
                        <span class="nav-label">Batch Management</span>
                    </a>
                    <span class="nav-tooltip">Batch Management</span>
                </li>
                <?php endif; ?>

                <?php if (can('quality') || can('improvement')): ?>
                <li class="nav-item"><span class="nav-section">QUALITY CONTROL</span></li>
                <?php if (can('quality')): ?>
                <li class="nav-item">
                    <a href="?route=quality" class="nav-link<?= str_starts_with($currentRoute, 'quality') && !str_starts_with($currentRoute, 'quality/traceability') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-patch-check"></i>
                        <span class="nav-label">Inspections</span>
                    </a>
                    <span class="nav-tooltip">Inspections</span>
                </li>
                <li class="nav-item">
                    <a href="?route=quality/traceability" class="nav-link<?= str_starts_with($currentRoute, 'quality/traceability') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-diagram-3"></i>
                        <span class="nav-label">Traceability Logs</span>
                    </a>
                    <span class="nav-tooltip">Traceability Logs</span>
                </li>
                <?php endif; ?>
                <?php if (can('improvement')): ?>
                <li class="nav-item">
                    <a href="?route=improvement" class="nav-link<?= str_starts_with($currentRoute, 'improvement') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-lightbulb"></i>
                        <span class="nav-label">CAPA</span>
                    </a>
                    <span class="nav-tooltip">CAPA</span>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if (can('sops')): ?>
                <li class="nav-item"><span class="nav-section">SOP CHECKLISTS</span></li>
                <li class="nav-item">
                    <a href="?route=sops" class="nav-link<?= str_starts_with($currentRoute, 'sops') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-file-text"></i>
                        <span class="nav-label">SOP Checklists</span>
                    </a>
                    <span class="nav-tooltip">SOP Checklists</span>
                </li>
                <?php endif; ?>

                <?php if (can('safety') || can('hazards') || can('accidents') || can('drills')): ?>
                <li class="nav-item"><span class="nav-section">SAFETY</span></li>
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
                <?php endif; ?>

                <?php if (can('staff') || can('training') || can('ppe')): ?>
                <li class="nav-item"><span class="nav-section">STAFF MANAGEMENT</span></li>
                <?php if (can('staff')): ?>
                <li class="nav-item">
                    <a href="?route=staff" class="nav-link<?= $currentRoute === 'staff' || str_starts_with($currentRoute, 'staff/create') || str_starts_with($currentRoute, 'staff/edit') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-person-badge"></i>
                        <span class="nav-label">Staff Records</span>
                    </a>
                    <span class="nav-tooltip">Staff Records</span>
                </li>
                <li class="nav-item">
                    <a href="?route=staff/shifts" class="nav-link<?= str_starts_with($currentRoute, 'staff/shifts') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-clock-history"></i>
                        <span class="nav-label">Shift Schedule</span>
                    </a>
                    <span class="nav-tooltip">Shift Schedule</span>
                </li>
                <li class="nav-item">
                    <a href="?route=staff/attendance" class="nav-link<?= str_starts_with($currentRoute, 'staff/attendance') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-calendar-check"></i>
                        <span class="nav-label">Attendance</span>
                    </a>
                    <span class="nav-tooltip">Attendance</span>
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

                <?php if (can('customers') || can('sales') || can('invoicing')): ?>
                <li class="nav-item"><span class="nav-section">SALES & CUSTOMERS</span></li>
                <?php if (can('customers')): ?>
                <li class="nav-item">
                    <a href="?route=customers" class="nav-link<?= str_starts_with($currentRoute, 'customers') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-people"></i>
                        <span class="nav-label">Customer Records</span>
                    </a>
                    <span class="nav-tooltip">Customer Records</span>
                </li>
                <?php endif; ?>
                <?php if (can('sales')): ?>
                <li class="nav-item">
                    <a href="?route=sales" class="nav-link<?= str_starts_with($currentRoute, 'sales') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-cart3"></i>
                        <span class="nav-label">Sales Orders</span>
                    </a>
                    <span class="nav-tooltip">Sales Orders</span>
                </li>
                <?php endif; ?>
                <?php if (can('invoicing')): ?>
                <li class="nav-item">
                    <a href="?route=invoicing" class="nav-link<?= str_starts_with($currentRoute, 'invoicing') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-receipt"></i>
                        <span class="nav-label">Invoices</span>
                    </a>
                    <span class="nav-tooltip">Invoices</span>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if (can('suppliers') || can('supplier_eval')): ?>
                <li class="nav-item"><span class="nav-section">SUPPLIER MANAGEMENT</span></li>
                <?php if (can('suppliers')): ?>
                <li class="nav-item">
                    <a href="?route=suppliers" class="nav-link<?= $currentRoute === 'suppliers' || str_starts_with($currentRoute, 'suppliers/create') || str_starts_with($currentRoute, 'suppliers/edit') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-truck"></i>
                        <span class="nav-label">Supplier Records</span>
                    </a>
                    <span class="nav-tooltip">Supplier Records</span>
                </li>
                <li class="nav-item">
                    <a href="?route=suppliers/deliveries" class="nav-link<?= str_starts_with($currentRoute, 'suppliers/deliveries') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-box-seam"></i>
                        <span class="nav-label">Fruit Deliveries</span>
                    </a>
                    <span class="nav-tooltip">Fruit Deliveries</span>
                </li>
                <?php endif; ?>
                <?php if (can('supplier_eval')): ?>
                <li class="nav-item">
                    <a href="?route=supplier-evaluations" class="nav-link<?= str_starts_with($currentRoute, 'supplier-evaluations') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-clipboard-data"></i>
                        <span class="nav-label">Supplier Evaluations</span>
                    </a>
                    <span class="nav-tooltip">Supplier Evaluations</span>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if (can('waste')): ?>
                <li class="nav-item"><span class="nav-section">WASTE MANAGEMENT</span></li>
                <li class="nav-item">
                    <a href="?route=waste" class="nav-link<?= str_starts_with($currentRoute, 'waste') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-trash"></i>
                        <span class="nav-label">Waste Tracking</span>
                    </a>
                    <span class="nav-tooltip">Waste Tracking</span>
                </li>
                <?php endif; ?>

                <?php if (can('water')): ?>
                <li class="nav-item"><span class="nav-section">WATER MANAGEMENT</span></li>
                <li class="nav-item">
                    <a href="?route=water" class="nav-link<?= str_starts_with($currentRoute, 'water') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-droplet"></i>
                        <span class="nav-label">Water Usage & Quality</span>
                    </a>
                    <span class="nav-tooltip">Water Usage & Quality</span>
                </li>
                <?php endif; ?>

                <?php if (can('machines') || can('maintenance') || can('fat')): ?>
                <li class="nav-item"><span class="nav-section">MAINTENANCE</span></li>
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
                        <span class="nav-label">Maintenance Schedule</span>
                    </a>
                    <span class="nav-tooltip">Maintenance Schedule</span>
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
                <?php endif; ?>

                <?php if (can('power')): ?>
                <li class="nav-item"><span class="nav-section">POWER MANAGEMENT</span></li>
                <li class="nav-item">
                    <a href="?route=power" class="nav-link<?= str_starts_with($currentRoute, 'power') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-lightning"></i>
                        <span class="nav-label">Electricity & Generator</span>
                    </a>
                    <span class="nav-tooltip">Electricity & Generator</span>
                </li>
                <?php endif; ?>

                <?php if (can('certifications') || can('permits') || can('documents')): ?>
                <li class="nav-item"><span class="nav-section">CERTIFICATION MANAGEMENT</span></li>
                <?php if (can('certifications')): ?>
                <li class="nav-item">
                    <a href="?route=certifications" class="nav-link<?= $currentRoute === 'certifications' || str_starts_with($currentRoute, 'certifications/create') || str_starts_with($currentRoute, 'certifications/edit') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-award"></i>
                        <span class="nav-label">Certifications</span>
                    </a>
                    <span class="nav-tooltip">Certifications</span>
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
                        <span class="nav-label">Digital Documents</span>
                    </a>
                    <span class="nav-tooltip">Digital Documents</span>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if ((hasRole('ROLE-001', 'ROLE-002')) && can('reports') || can('efficiency')): ?>
                <li class="nav-item"><span class="nav-section">REPORTS</span></li>
                <?php if ((hasRole('ROLE-001', 'ROLE-002')) && can('reports')): ?>
                <li class="nav-item">
                    <a href="?route=reports" class="nav-link<?= str_starts_with($currentRoute, 'reports') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-file-earmark-bar-graph"></i>
                        <span class="nav-label">All Reports</span>
                    </a>
                    <span class="nav-tooltip">All Reports</span>
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

                <?php if (can('users') || can('audit') || can('backup')): ?>
                <li class="nav-item"><span class="nav-section">SYSTEM SETTINGS</span></li>
                <?php if (can('audit')): ?>
                <li class="nav-item">
                    <a href="?route=audit" class="nav-link<?= str_starts_with($currentRoute, 'audit') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-journal-text"></i>
                        <span class="nav-label">Audit Trail</span>
                    </a>
                    <span class="nav-tooltip">Audit Trail</span>
                </li>
                <?php endif; ?>
                <?php if (can('users')): ?>
                <li class="nav-item">
                    <a href="?route=users" class="nav-link<?= str_starts_with($currentRoute, 'users') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-person-gear"></i>
                        <span class="nav-label">User Management</span>
                    </a>
                    <span class="nav-tooltip">User Management</span>
                </li>
                <?php endif; ?>
                <?php if (can('backup')): ?>
                <li class="nav-item">
                    <a href="?route=settings/backup" class="nav-link<?= str_starts_with($currentRoute, 'settings/backup') || str_starts_with($currentRoute, 'settings/restore') ? ' active' : '' ?>">
                        <i class="nav-icon bi bi-cloud-arrow-down"></i>
                        <span class="nav-label">Backup & Restore</span>
                    </a>
                    <span class="nav-tooltip">Backup & Restore</span>
                </li>
                <?php endif; ?>
                <?php endif; ?>
            </ul>

            <ul class="nav-list secondary-nav">
                <li class="nav-item">
                    <a href="?route=auth/logout" class="nav-link logout-link">
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
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#scanModal" title="Scan Barcode (batches, materials, goods)">
                        <i class="bi bi-upc-scan"></i><span class="d-none d-lg-inline ms-1">Scan</span>
                    </button>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                            <div style="width:32px;height:32px;border-radius:10px;background:var(--gradient-brand);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.8rem;"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
                            <span class="fw-medium text-muted d-none d-md-inline" style="font-size:0.82rem;"><?= sanitize($user['name'] ?? 'User') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width:180px;border-radius:12px;border:1px solid rgba(0,0,0,0.08);box-shadow:0 8px 32px rgba(0,0,0,0.12);padding:0.4rem;">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-semibold" style="font-size:0.85rem;"><?= sanitize($user['name'] ?? 'User') ?></div>
                                <div class="text-muted" style="font-size:0.72rem;"><?= sanitize($user['role_name'] ?? '') ?></div>
                            </li>
                            <li><a class="dropdown-item d-flex align-items-center gap-2" href="?route=profile" style="font-size:0.82rem;padding:0.5rem 0.85rem;border-radius:8px;"><i class="bi bi-person-circle"></i> My Profile</a></li>
                            <li><hr class="dropdown-divider" style="margin:0.25rem 0;"></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="?route=auth/logout" style="font-size:0.82rem;padding:0.5rem 0.85rem;border-radius:8px;"><i class="bi bi-box-arrow-right"></i> Sign Out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <div class="container-fluid p-4">
            <?php $flash = getFlash(); if ($flash): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({ icon: <?= json_encode($flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'error' : 'info')) ?>, title: <?= json_encode($flash['message']) ?>, toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
                });
            </script>
            <?php endif; ?>
            <?= $content ?>
        </div>
    </div>
</div>

<div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <form method="get" action="">
                <input type="hidden" name="route" value="scan">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold" id="scanModalLabel"><i class="bi bi-upc-scan me-2"></i>Scan Barcode</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-2" style="font-size:0.82rem;">Scan or type a batch number, material ID, finished goods ID or certificate ID, then press <kbd>Enter</kbd>.</p>
                    <input type="text" class="form-control" name="code" id="scanInput" placeholder="e.g. BAT-000001 / RM-0001 / FG-000001" autocomplete="off" autofocus>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-search"></i> Look Up</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php else: ?>
<?= $content ?>
<?php endif; ?>
<script src="<?= $assetBase ?>/frontend/assets/vendor/jquery/jquery-3.6.0.min.js"></script>
<script src="<?= $assetBase ?>/frontend/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
<script src="<?= $assetBase ?>/frontend/assets/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $assetBase ?>/frontend/assets/vendor/datatables/dataTables.bootstrap5.min.js"></script>
<script src="<?= $assetBase ?>/frontend/assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
<script src="<?= $assetBase ?>/frontend/assets/vendor/chartjs/chart.umd.min.js"></script>
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
