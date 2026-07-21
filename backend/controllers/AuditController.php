<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/AuditTrailModel.php';

class AuditController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'audit';
    }

    public function index(): void
    {
        $this->requireRole('admin', 'manager', 'ROLE-001', 'ROLE-002');
        $model = new AuditTrailModel();
        $module = $this->getInput('module') ?? '';
        $action = $this->getInput('action') ?? '';
        $this->render('index', [
            'logs' => $model->getRecent(200, $module, $action),
            'modules' => ['supplier', 'safety', 'drill', 'accident', 'training', 'ppe', 'permit', 'maintenance', 'waste', 'production', 'quality', 'finished_goods', 'customer', 'sales', 'invoice', 'staff', 'machine', 'improvement', 'document', 'user', 'Deliveries', 'Suppliers'],
            'currentModule' => $module,
            'currentAction' => $action,
        ]);
    }
}
