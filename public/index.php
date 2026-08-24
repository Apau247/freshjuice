<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/config/permissions.php';
require_once __DIR__ . '/../backend/config/ui.php';
require_once APP_ROOT . '/backend/models/Model.php';
require_once APP_ROOT . '/backend/controllers/Controller.php';

spl_autoload_register(function (string $class): void {
    $file = APP_ROOT . '/backend/models/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

$route = $_GET['route'] ?? 'dashboard';

$map = [
    'auth/login'                => ['auth',   'AuthController',   'login'],
    'auth/logout'               => ['auth',   'AuthController',   'logout'],
    'auth/forgot'               => ['auth',   'AuthController',   'forgotPassword'],
    'auth/reset'                => ['auth',   'AuthController',   'resetPassword'],
    'dashboard'                 => ['dash',   'DashboardController','index'],

    'alerts/low-stock'          => ['stock',  'InventoryController','lowStock'],
    'alerts/expiry'             => ['stock',  'InventoryController','expiry'],

    'reports'                   => ['rep',    'ReportController',   'index'],
    'reports/view'              => ['rep',    'ReportController',   'show'],
    'reports/print'             => ['rep',    'ReportController',   'printView'],
    'reports/export'            => ['rep',    'ReportController',   'exportCsv'],

    'settings/backup'           => ['set',    'SettingsController', 'index'],
    'settings/restore'          => ['set',    'SettingsController', 'restore'],

    'suppliers'                 => ['sup',    'SupplierController','index'],
    'suppliers/create'          => ['sup',    'SupplierController','create'],
    'suppliers/edit'            => ['sup',    'SupplierController','edit'],
    'suppliers/delete'          => ['sup',    'SupplierController','delete'],
    'suppliers/deliveries'      => ['sup',    'SupplierController','deliveries'],
    'suppliers/delivery/create' => ['sup',    'SupplierController','createDelivery'],
    'suppliers/delivery/edit'   => ['sup',    'SupplierController','editDelivery'],
    'suppliers/delivery/delete' => ['sup',    'SupplierController','deleteDelivery'],

    'materials/raw'             => ['rm',     'RawMaterialController','index'],
    'materials/raw/create'      => ['rm',     'RawMaterialController','create'],
    'materials/raw/edit'        => ['rm',     'RawMaterialController','edit'],
    'materials/raw/delete'      => ['rm',     'RawMaterialController','delete'],

    'materials/packaging'       => ['pm',     'PackagingMaterialController','index'],
    'materials/packaging/create'=> ['pm',     'PackagingMaterialController','create'],
    'materials/packaging/edit'  => ['pm',     'PackagingMaterialController','edit'],
    'materials/packaging/delete'=> ['pm',     'PackagingMaterialController','delete'],

    'production'                => ['prod',   'ProductionController','index'],
    'production/create'         => ['prod',   'ProductionController','create'],
    'production/edit'           => ['prod',   'ProductionController','edit'],
    'production/delete'         => ['prod',   'ProductionController','delete'],
    'production/label'          => ['prod',   'ProductionController','label'],

    'quality'                   => ['qi',     'QualityController','index'],
    'quality/create'            => ['qi',     'QualityController','create'],
    'quality/edit'              => ['qi',     'QualityController','edit'],
    'quality/delete'            => ['qi',     'QualityController','delete'],
    'quality/traceability'      => ['qi',     'QualityController','traceability'],

    'finished-goods'            => ['fg',     'FinishedGoodsController','index'],
    'finished-goods/create'     => ['fg',     'FinishedGoodsController','create'],
    'finished-goods/edit'       => ['fg',     'FinishedGoodsController','edit'],
    'finished-goods/delete'     => ['fg',     'FinishedGoodsController','delete'],

    'customers'                 => ['cust',   'CustomerController','index'],
    'customers/create'          => ['cust',   'CustomerController','create'],
    'customers/edit'            => ['cust',   'CustomerController','edit'],
    'customers/delete'          => ['cust',   'CustomerController','delete'],

    'sales'                     => ['sales',  'SalesController','index'],
    'sales/create'              => ['sales',  'SalesController','create'],
    'sales/edit'                => ['sales',  'SalesController','edit'],
    'sales/delete'              => ['sales',  'SalesController','delete'],
    'sales/receipt'             => ['sales',  'SalesController','receipt'],

    'invoicing'                 => ['inv',    'InvoiceController','index'],
    'invoicing/create'          => ['inv',    'InvoiceController','create'],
    'invoicing/edit'            => ['inv',    'InvoiceController','edit'],
    'invoicing/delete'          => ['inv',    'InvoiceController','delete'],

    'staff'                     => ['staff',  'StaffController','index'],
    'staff/create'              => ['staff',  'StaffController','create'],
    'staff/edit'                => ['staff',  'StaffController','edit'],
    'staff/delete'              => ['staff',  'StaffController','delete'],
    'staff/shifts'              => ['staff',  'StaffController','shifts'],
    'staff/shifts/create'       => ['staff',  'StaffController','createShift'],
    'staff/shifts/edit'         => ['staff',  'StaffController','editShift'],
    'staff/shifts/delete'       => ['staff',  'StaffController','deleteShift'],
    'staff/attendance'          => ['staff',  'StaffController','attendance'],
    'staff/attendance/form'     => ['staff',  'StaffController','attendance'],
    'staff/attendance/edit'     => ['staff',  'StaffController','editAttendance'],
    'staff/attendance/delete'   => ['staff',  'StaffController','deleteAttendance'],

    'machines'                  => ['mach',   'MachineController','index'],
    'machines/create'           => ['mach',   'MachineController','create'],
    'machines/edit'             => ['mach',   'MachineController','edit'],
    'machines/delete'           => ['mach',   'MachineController','delete'],

    'maintenance'               => ['maint',  'MaintenanceController','index'],
    'maintenance/create'        => ['maint',  'MaintenanceController','create'],
    'maintenance/edit'          => ['maint',  'MaintenanceController','edit'],
    'maintenance/delete'        => ['maint',  'MaintenanceController','delete'],

    'waste'                     => ['waste',  'WasteController','index'],
    'waste/create'              => ['waste',  'WasteController','create'],
    'waste/edit'                => ['waste',  'WasteController','edit'],
    'waste/delete'              => ['waste',  'WasteController','delete'],

    'water'                     => ['water',  'WaterController','index'],
    'water/usage/create'        => ['water',  'WaterController','createUsage'],
    'water/usage/form'          => ['water',  'WaterController','createUsage'],
    'water/usage/edit'          => ['water',  'WaterController','editUsage'],
    'water/usage/delete'        => ['water',  'WaterController','deleteUsage'],
    'water/test/create'         => ['water',  'WaterController','createTest'],
    'water/test/form'           => ['water',  'WaterController','createTest'],
    'water/test/edit'           => ['water',  'WaterController','editTest'],
    'water/test/delete'         => ['water',  'WaterController','deleteTest'],

    'power'                     => ['power',  'PowerController','index'],
    'power/usage/create'        => ['power',  'PowerController','createUsage'],
    'power/usage/form'          => ['power',  'PowerController','createUsage'],
    'power/usage/edit'          => ['power',  'PowerController','editUsage'],
    'power/usage/delete'        => ['power',  'PowerController','deleteUsage'],
    'power/generator/create'    => ['power',  'PowerController','createGeneratorLog'],
    'power/generator/form'      => ['power',  'PowerController','createGeneratorLog'],
    'power/generator/edit'      => ['power',  'PowerController','editGeneratorLog'],
    'power/generator/delete'    => ['power',  'PowerController','deleteGeneratorLog'],

    'certifications'            => ['cert',   'CertificationController','index'],
    'certifications/create'     => ['cert',   'CertificationController','create'],
    'certifications/edit'       => ['cert',   'CertificationController','edit'],
    'certifications/delete'     => ['cert',   'CertificationController','delete'],

    'sops'                      => ['sops',   'SopController','index'],
    'sops/template/create'      => ['sops',   'SopController','createTemplate'],
    'sops/template/edit'        => ['sops',   'SopController','editTemplate'],
    'sops/template/delete'      => ['sops',   'SopController','deleteTemplate'],
    'sops/checklist/create'     => ['sops',   'SopController','createChecklist'],
    'sops/checklist/edit'       => ['sops',   'SopController','editChecklist'],
    'sops/checklist/delete'     => ['sops',   'SopController','deleteChecklist'],
    'sops/template/form'        => ['sops',   'SopController','createTemplate'],
    'sops/checklist/form'       => ['sops',   'SopController','createChecklist'],

    'users'                     => ['users',  'UserController','index'],
    'users/create'              => ['users',  'UserController','create'],
    'users/edit'                => ['users',  'UserController','edit'],
    'users/delete'              => ['users',  'UserController','delete'],
    'profile'                   => ['users',  'UserController','profile'],

    'safety'                    => ['safety', 'SafetyController','index'],
    'safety/create'             => ['safety', 'SafetyController','create'],
    'safety/edit'               => ['safety', 'SafetyController','edit'],
    'safety/delete'             => ['safety', 'SafetyController','delete'],
    'safety/hazards'            => ['hazard', 'HazardController','index'],
    'safety/hazards/create'     => ['hazard', 'HazardController','create'],
    'safety/hazards/edit'       => ['hazard', 'HazardController','edit'],
    'safety/hazards/delete'     => ['hazard', 'HazardController','delete'],
    'safety/accidents'          => ['accident','AccidentController','index'],
    'safety/accidents/create'   => ['accident','AccidentController','create'],
    'safety/accidents/edit'     => ['accident','AccidentController','edit'],
    'safety/accidents/delete'   => ['accident','AccidentController','delete'],
    'safety/drills'             => ['drill', 'DrillController','index'],
    'safety/drills/create'      => ['drill', 'DrillController','create'],
    'safety/drills/edit'        => ['drill', 'DrillController','edit'],
    'safety/drills/delete'      => ['drill', 'DrillController','delete'],

    'permits'                   => ['permit', 'PermitController','index'],
    'permits/create'            => ['permit', 'PermitController','create'],
    'permits/edit'              => ['permit', 'PermitController','edit'],
    'permits/delete'            => ['permit', 'PermitController','delete'],

    'training'                  => ['train', 'TrainingController','index'],
    'training/create'           => ['train', 'TrainingController','create'],
    'training/edit'             => ['train', 'TrainingController','edit'],
    'training/delete'           => ['train', 'TrainingController','delete'],

    'ppe'                       => ['ppe',   'PpeController','index'],
    'ppe/create'                => ['ppe',   'PpeController','create'],
    'ppe/edit'                  => ['ppe',   'PpeController','edit'],
    'ppe/delete'                => ['ppe',   'PpeController','delete'],

    'efficiency'                => ['eff',   'EfficiencyController','index'],
    'efficiency/create'         => ['eff',   'EfficiencyController','create'],
    'efficiency/edit'           => ['eff',   'EfficiencyController','edit'],
    'efficiency/delete'         => ['eff',   'EfficiencyController','delete'],

    'improvement'               => ['imp',   'ImprovementController','index'],
    'improvement/create'        => ['imp',   'ImprovementController','create'],
    'improvement/edit'          => ['imp',   'ImprovementController','edit'],
    'improvement/delete'        => ['imp',   'ImprovementController','delete'],

    'documents'                 => ['doc',   'DocumentController','index'],
    'documents/create'          => ['doc',   'DocumentController','create'],
    'documents/edit'            => ['doc',   'DocumentController','edit'],
    'documents/delete'          => ['doc',   'DocumentController','delete'],
    'documents/download'        => ['doc',   'DocumentController','download'],

    'supplier-evaluations'      => ['seval', 'SupplierEvalController','index'],
    'supplier-evaluations/create'=>['seval', 'SupplierEvalController','create'],
    'supplier-evaluations/edit' => ['seval', 'SupplierEvalController','edit'],
    'supplier-evaluations/delete'=> ['seval', 'SupplierEvalController','delete'],

    'fat'                       => ['fat',   'FatController','index'],
    'fat/create'                => ['fat',   'FatController','create'],
    'fat/edit'                  => ['fat',   'FatController','edit'],
    'fat/delete'                => ['fat',   'FatController','delete'],

    'notifications'             => ['notif', 'NotificationController','index'],
    'audit'                     => ['audit', 'AuditController','index'],
    'scan'                      => ['scan',  'ScanController','lookup'],
];

if (isset($map[$route])) {
    [$prefix, $class, $method] = $map[$route];

    if ($prefix !== 'auth' && !isLoggedIn()) {
        header('Location: ?route=auth/login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $prefix !== 'auth') {
        if (!validateCsrf()) {
            setFlash('error', 'Invalid security token. Please try again.');
            header('Location: ?route=' . $route);
            exit;
        }
    }

    if (str_ends_with($route, '/delete') && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        setFlash('error', 'Invalid request method.');
        header('Location: ?route=' . dirname($route));
        exit;
    }

    if ($prefix !== 'auth' && isLoggedIn()) {
        $module = getModuleForRoute($route);
        if ($module && !can($module)) {
            setFlash('error', 'You do not have permission to access this page.');
            header('Location: ?route=dashboard');
            exit;
        }

        // can() only proves read access. Write routes need the write level too --
        // enforced here so every module is covered, rather than relying on each
        // controller to remember to call requireCanEdit()/requireCanCreate().
        if ($module) {
            $isCreate = str_ends_with($route, '/create') || str_ends_with($route, '/form');
            $isWrite  = str_ends_with($route, '/edit') || str_ends_with($route, '/delete');

            if ($isCreate && !canCreate($module)) {
                setFlash('error', 'You do not have permission to create records in this module.');
                header('Location: ?route=dashboard');
                exit;
            }
            if ($isWrite && !canEdit($module)) {
                setFlash('error', 'You do not have permission to modify records in this module.');
                header('Location: ?route=dashboard');
                exit;
            }
        }
    }

    $fileMap = [
        'auth'     => 'AuthController',
        'dash'     => 'DashboardController',
        'stock'    => 'InventoryController',
        'rep'      => 'ReportController',
        'set'      => 'SettingsController',
        'scan'     => 'ScanController',
        'sup'      => 'SupplierController',
        'rm'       => 'RawMaterialController',
        'pm'       => 'PackagingMaterialController',
        'prod'     => 'ProductionController',
        'qi'       => 'QualityController',
        'fg'       => 'FinishedGoodsController',
        'cust'     => 'CustomerController',
        'sales'    => 'SalesController',
        'inv'      => 'InvoiceController',
        'staff'    => 'StaffController',
        'mach'     => 'MachineController',
        'maint'    => 'MaintenanceController',
        'waste'    => 'WasteController',
        'water'    => 'WaterController',
        'power'    => 'PowerController',
        'cert'     => 'CertificationController',
        'sops'     => 'SopController',
        'users'    => 'UserController',
        'safety'   => 'SafetyController',
        'hazard'   => 'HazardController',
        'accident' => 'AccidentController',
        'drill'    => 'DrillController',
        'permit'   => 'PermitController',
        'train'    => 'TrainingController',
        'ppe'      => 'PpeController',
        'eff'      => 'EfficiencyController',
        'imp'      => 'ImprovementController',
        'doc'      => 'DocumentController',
        'seval'    => 'SupplierEvalController',
        'fat'      => 'FatController',
        'notif'    => 'NotificationController',
        'audit'    => 'AuditController',
    ];
    if ($prefix === 'auth') {
        require_once APP_ROOT . '/backend/auth/' . $fileMap[$prefix] . '.php';
    } else {
        require_once APP_ROOT . '/backend/controllers/' . $fileMap[$prefix] . '.php';
    }

    $controller = new $class();
    $controller->$method();
} else {
    header('Location: ?route=dashboard');
    exit;
}
