<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/MaintenanceRecordModel.php';
require_once __DIR__ . '/../models/MachineModel.php';

class MaintenanceController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new MaintenanceRecordModel();
        $this->viewPath = 'maintenance';
    }

    public function index(): void {
        $this->render('index', ['records' => $this->model->getAllDetailed()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $machineId = $this->getInput('MachineID');
            $this->model->create([
                'MaintenanceID' => $this->getInput('MaintenanceID'),
                'MaintenanceType' => $this->getInput('MaintenanceType', 'Preventive'),
                'MaintenanceDate' => $this->getInput('MaintenanceDate'),
                'Downtime' => (float)$this->getInput('Downtime', '0'),
                'Cost' => (float)$this->getInput('Cost', '0'),
                'MachineID' => $machineId,
                'TechnicianID' => $_SESSION['user_id'] ?? null,
                'Description' => $this->getInput('Description'),
                'SpareParts' => $this->getInput('SpareParts'),
                'Status' => $this->getInput('Status', 'Scheduled'),
                'NextServiceDate' => $this->getInput('NextServiceDate'),
            ]);
            if ($machineId && $this->getInput('Status') === 'In Progress') {
                (new MachineModel())->update($machineId, ['Status' => 'Maintenance']);
            }
            setFlash('success', 'Maintenance record created.');
            $this->redirect('maintenance');
            return;
        }
        $this->render('form', ['machines' => (new MachineModel())->all()]);
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $rec = $this->model->find($id);
        if (!$rec) { setFlash('error', 'Not found.'); $this->redirect('maintenance'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, [
                'MaintenanceType' => $this->getInput('MaintenanceType'),
                'MaintenanceDate' => $this->getInput('MaintenanceDate'),
                'Downtime' => (float)$this->getInput('Downtime', '0'),
                'Cost' => (float)$this->getInput('Cost', '0'),
                'MachineID' => $this->getInput('MachineID'),
                'Description' => $this->getInput('Description'),
                'SpareParts' => $this->getInput('SpareParts'),
                'Status' => $this->getInput('Status'),
                'NextServiceDate' => $this->getInput('NextServiceDate'),
            ]);
            setFlash('success', 'Maintenance record updated.');
            $this->redirect('maintenance');
            return;
        }
        $this->render('form', ['record' => $rec, 'machines' => (new MachineModel())->all()]);
    }

    public function delete(): void {
        $this->model->delete($this->getInput('id'));
        setFlash('success', 'Record deleted.');
        $this->redirect('maintenance');
    }
}
