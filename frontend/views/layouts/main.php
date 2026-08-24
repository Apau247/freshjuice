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
                <?php
                $currentRoute = $_GET['route'] ?? 'dashboard';
                // Route matcher: exact match OR prefix match (e.g. "sales" hits "sales/receipt").
                $on = function (string ...$patterns) use ($currentRoute): bool {
                    foreach ($patterns as $p) {
                        if ($p === $currentRoute || str_starts_with($currentRoute, $p)) {
                            return true;
                        }
                    }
                    return false;
                };

                /*
                 * Accordion menu definition.
                 * Each group = one collapsible section in the sidebar; children
                 * are the links inside it. 'show' gates visibility by permission,
                 * 'on' decides whether a child is highlighted as active.
                 */
                $groups = [
                    'inventory' => [
                        'label' => 'Inventory Management', 'icon' => 'bi-boxes',
                        'show' => can('inventory_alerts'),
                        'items' => [
                            ['href' => 'alerts/low-stock',    'label' => 'Low Stock Alerts',    'icon' => 'bi-exclamation-octagon', 'show' => true, 'on' => fn () => $on('alerts/low-stock')],
                            ['href' => 'alerts/expiry',       'label' => 'Expiry Alerts',       'icon' => 'bi-hourglass-split',     'show' => true, 'on' => fn () => $on('alerts/expiry')],
                            ['href' => 'materials/raw',       'label' => 'Raw Materials',       'icon' => 'bi-bar-chart-steps',     'show' => can('materials'), 'on' => fn () => $on('materials/raw')],
                            ['href' => 'materials/packaging', 'label' => 'Packaging Materials', 'icon' => 'bi-box',                 'show' => can('materials'), 'on' => fn () => $on('materials/packaging')],
                            ['href' => 'finished-goods',      'label' => 'Finished Goods',      'icon' => 'bi-cup-straw',           'show' => can('finished_goods'), 'on' => fn () => $on('finished-goods')],
                            ['href' => 'prices',              'label' => 'Product Prices',      'icon' => 'bi-tags',                'show' => can('pricing'),   'on' => fn () => $on('prices')],
                        ],
                    ],
                    'production' => [
                        'label' => 'Production', 'icon' => 'bi-gear-wide-connected',
                        'show' => can('production'),
                        'items' => [
                            ['href' => 'production', 'label' => 'Batch Management', 'icon' => 'bi-box-seam', 'show' => true, 'on' => fn () => $on('production')],
                        ],
                    ],
                    'quality' => [
                        'label' => 'Quality Control', 'icon' => 'bi-patch-check',
                        'show' => can('quality') || can('improvement') || can('sops'),
                        'items' => [
                            ['href' => 'quality',                'label' => 'Inspections',       'icon' => 'bi-patch-check',   'show' => can('quality'),     'on' => fn () => $on('quality') && !$on('quality/traceability')],
                            ['href' => 'quality/traceability',   'label' => 'Traceability Logs', 'icon' => 'bi-diagram-3',     'show' => can('quality'),     'on' => fn () => $on('quality/traceability')],
                            ['href' => 'improvement',            'label' => 'CAPA',              'icon' => 'bi-lightbulb',     'show' => can('improvement'), 'on' => fn () => $on('improvement')],
                            ['href' => 'sops',                   'label' => 'SOP Checklists',    'icon' => 'bi-file-text',     'show' => can('sops'),        'on' => fn () => $on('sops')],
                        ],
                    ],
                    'safety' => [
                        'label' => 'Safety', 'icon' => 'bi-shield-check',
                        'show' => can('safety') || can('hazards') || can('accidents') || can('drills'),
                        'items' => [
                            ['href' => 'safety',           'label' => 'Inspections', 'icon' => 'bi-shield-check',        'show' => can('safety'),    'on' => fn () => $on('safety') && !$on('safety/hazards') && !$on('safety/accidents') && !$on('safety/drills')],
                            ['href' => 'safety/hazards',   'label' => 'Hazards',     'icon' => 'bi-exclamation-triangle','show' => can('hazards'),   'on' => fn () => $on('safety/hazards')],
                            ['href' => 'safety/accidents', 'label' => 'Accidents',   'icon' => 'bi-activity',            'show' => can('accidents'), 'on' => fn () => $on('safety/accidents')],
                            ['href' => 'safety/drills',    'label' => 'Drills',      'icon' => 'bi-alarm',               'show' => can('drills'),    'on' => fn () => $on('safety/drills')],
                        ],
                    ],
                    'staff' => [
                        'label' => 'Staff Management', 'icon' => 'bi-person-badge',
                        'show' => can('staff') || can('training') || can('ppe'),
                        'items' => [
                            ['href' => 'staff',             'label' => 'Staff Records',  'icon' => 'bi-person-badge',   'show' => can('staff'),     'on' => fn () => $on('staff') && !$on('staff/shifts') && !$on('staff/attendance')],
                            ['href' => 'staff/shifts',      'label' => 'Shift Schedule', 'icon' => 'bi-clock-history',  'show' => can('staff'),     'on' => fn () => $on('staff/shifts')],
                            ['href' => 'staff/attendance',  'label' => 'Attendance',     'icon' => 'bi-calendar-check', 'show' => can('staff'),     'on' => fn () => $on('staff/attendance')],
                            ['href' => 'training',          'label' => 'Training',       'icon' => 'bi-mortarboard',    'show' => can('training'),  'on' => fn () => $on('training')],
                            ['href' => 'ppe',               'label' => 'PPE',            'icon' => 'bi-shield-fill',    'show' => can('ppe'),       'on' => fn () => $on('ppe')],
                        ],
                    ],
                    'payroll' => [
                        'label' => 'Payroll', 'icon' => 'bi-cash-coin',
                        'show' => can('payroll'),
                        'items' => [
                            ['href' => 'payroll',          'label' => 'Payslips & Payments', 'icon' => 'bi-cash-stack',        'show' => true,             'on' => fn () => $on('payroll') && !$on('payroll/settings') && !$on('payroll/report')],
                            ['href' => 'payroll/report',   'label' => 'Payment Report',      'icon' => 'bi-file-earmark-bar-graph', 'show' => true,        'on' => fn () => $on('payroll/report')],
                            ['href' => 'payroll/settings', 'label' => 'Salary Settings',     'icon' => 'bi-sliders',           'show' => canEdit('payroll'), 'on' => fn () => $on('payroll/settings') || $on('payroll/generate')],
                        ],
                    ],
                    'sales' => [
                        'label' => 'Sales & Customers', 'icon' => 'bi-cart3',
                        'show' => can('customers') || can('sales') || can('invoicing'),
                        'items' => [
                            ['href' => 'customers', 'label' => 'Customer Records', 'icon' => 'bi-people',  'show' => can('customers'),  'on' => fn () => $on('customers')],
                            ['href' => 'sales',     'label' => 'Sales Orders',     'icon' => 'bi-cart3',   'show' => can('sales'),      'on' => fn () => $on('sales')],
                            ['href' => 'invoicing', 'label' => 'Invoices',         'icon' => 'bi-receipt', 'show' => can('invoicing'),  'on' => fn () => $on('invoicing')],
                        ],
                    ],
                    'suppliers' => [
                        'label' => 'Supplier Management', 'icon' => 'bi-truck',
                        'show' => can('suppliers') || can('supplier_eval'),
                        'items' => [
                            ['href' => 'suppliers',              'label' => 'Supplier Records',     'icon' => 'bi-truck',          'show' => can('suppliers'),      'on' => fn () => $on('suppliers') && !$on('suppliers/deliveries')],
                            ['href' => 'suppliers/deliveries',   'label' => 'Fruit Deliveries',     'icon' => 'bi-box-seam',       'show' => can('suppliers'),      'on' => fn () => $on('suppliers/deliveries')],
                            ['href' => 'supplier-evaluations',   'label' => 'Supplier Evaluations', 'icon' => 'bi-clipboard-data', 'show' => can('supplier_eval'),  'on' => fn () => $on('supplier-evaluations')],
                        ],
                    ],
                    'facilities' => [
                        'label' => 'Facilities & Utilities', 'icon' => 'bi-building-gear',
                        'show' => can('waste') || can('water') || can('power'),
                        'items' => [
                            ['href' => 'waste', 'label' => 'Waste Tracking',         'icon' => 'bi-trash',     'show' => can('waste'), 'on' => fn () => $on('waste')],
                            ['href' => 'water', 'label' => 'Water Usage & Quality',  'icon' => 'bi-droplet',   'show' => can('water'), 'on' => fn () => $on('water')],
                            ['href' => 'power', 'label' => 'Electricity & Generator','icon' => 'bi-lightning', 'show' => can('power'), 'on' => fn () => $on('power')],
                        ],
                    ],
                    'maintenance' => [
                        'label' => 'Maintenance', 'icon' => 'bi-wrench',
                        'show' => can('machines') || can('maintenance') || can('fat'),
                        'items' => [
                            ['href' => 'machines',    'label' => 'Machines',              'icon' => 'bi-cpu',               'show' => can('machines'),    'on' => fn () => $on('machines')],
                            ['href' => 'maintenance', 'label' => 'Maintenance Schedule',  'icon' => 'bi-wrench',            'show' => can('maintenance'), 'on' => fn () => $on('maintenance')],
                            ['href' => 'fat',         'label' => 'Factory Acceptance Test','icon' => 'bi-clipboard-check',  'show' => can('fat'),         'on' => fn () => $on('fat')],
                        ],
                    ],
                    'compliance' => [
                        'label' => 'Certification Management', 'icon' => 'bi-award',
                        'show' => can('certifications') || can('permits') || can('documents'),
                        'items' => [
                            ['href' => 'certifications', 'label' => 'Certifications',    'icon' => 'bi-award',              'show' => can('certifications'), 'on' => fn () => $on('certifications')],
                            ['href' => 'permits',        'label' => 'Permits',           'icon' => 'bi-file-earmark-check', 'show' => can('permits'),        'on' => fn () => $on('permits')],
                            ['href' => 'documents',      'label' => 'Digital Documents', 'icon' => 'bi-folder2-open',       'show' => can('documents'),      'on' => fn () => $on('documents')],
                        ],
                    ],
                    'reports' => [
                        'label' => 'Reports', 'icon' => 'bi-file-earmark-bar-graph',
                        'show' => ((hasRole('ROLE-001', 'ROLE-002')) && can('reports')) || can('efficiency'),
                        'items' => [
                            ['href' => 'reports',    'label' => 'All Reports', 'icon' => 'bi-file-earmark-bar-graph', 'show' => (hasRole('ROLE-001', 'ROLE-002')) && can('reports'), 'on' => fn () => $on('reports')],
                            ['href' => 'efficiency', 'label' => 'OEE',         'icon' => 'bi-speedometer2',           'show' => can('efficiency'),                                   'on' => fn () => $on('efficiency')],
                        ],
                    ],
                    'system' => [
                        'label' => 'System Settings', 'icon' => 'bi-sliders2',
                        'show' => can('users') || can('audit') || can('backup'),
                        'items' => [
                            ['href' => 'audit',            'label' => 'Audit Trail',      'icon' => 'bi-journal-text',     'show' => can('audit'),  'on' => fn () => $on('audit')],
                            ['href' => 'users',            'label' => 'User Management',  'icon' => 'bi-person-gear',      'show' => can('users'),  'on' => fn () => $on('users')],
                            ['href' => 'settings/backup',  'label' => 'Backup & Restore', 'icon' => 'bi-cloud-arrow-down', 'show' => can('backup'), 'on' => fn () => $on('settings/backup') || $on('settings/restore')],
                        ],
                    ],
                ];
                ?>

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

                <?php foreach ($groups as $gKey => $group):
                    $gItems = array_values(array_filter($group['items'], fn ($it) => $it['show']));
                    if (!$group['show'] || !$gItems) continue;
                    $hasActive = false;
                    foreach ($gItems as $gi => $it) {
                        $gItems[$gi]['active'] = ($it['on'])();
                        if ($gItems[$gi]['active']) $hasActive = true;
                    }
                    $isOpen = $hasActive; // JS re-applies remembered open states from localStorage
                ?>
                <li class="nav-item nav-group<?= $hasActive ? ' has-active' : '' ?><?= $isOpen ? ' open' : '' ?>" data-group="<?= sanitize($gKey) ?>">
                    <button type="button" class="nav-link nav-group-toggle" aria-expanded="<?= $isOpen ? 'true' : 'false' ?>" aria-controls="submenu-<?= sanitize($gKey) ?>">
                        <i class="nav-icon bi <?= $group['icon'] ?>"></i>
                        <span class="nav-label"><?= sanitize($group['label']) ?></span>
                        <i class="nav-arrow bi bi-chevron-down" aria-hidden="true"></i>
                    </button>
                    <span class="nav-tooltip"><?= sanitize($group['label']) ?></span>
                    <ul class="sub-menu" id="submenu-<?= sanitize($gKey) ?>">
                        <?php foreach ($gItems as $it): ?>
                        <li>
                            <a href="?route=<?= sanitize($it['href']) ?>" class="sub-link<?= $it['active'] ? ' active' : '' ?>">
                                <i class="sub-icon bi <?= $it['icon'] ?>"></i>
                                <span><?= sanitize($it['label']) ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php endforeach; ?>
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
