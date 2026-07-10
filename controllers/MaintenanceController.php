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
            $machineId = $this->getInput('machine_id');
            $this->model->create([
                'MaintenanceID' => $this->getInput('MaintenanceID'),
                'MaintenanceType' => $this->getInput('maintenance_type', 'Preventive'),
                'MaintenanceDate' => $this->getInput('maintenance_date'),
                'Downtime' => (float)$this->getInput('downtime', '0'),
                'Cost' => (float)$this->getInput('cost', '0'),
                'MachineID' => $machineId,
                'TechnicianID' => $this->getInput('technician_id') ?: ($_SESSION['user_id'] ?? null),
                'Description' => $this->getInput('description'),
                'SpareParts' => $this->getInput('spare_parts'),
                'Status' => $this->getInput('status', 'Scheduled'),
                'NextServiceDate' => $this->getInput('next_service_date'),
            ]);
            if ($machineId && $this->getInput('status') === 'In Progress') {
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
                'MaintenanceType' => $this->getInput('maintenance_type'),
                'MaintenanceDate' => $this->getInput('maintenance_date'),
                'Downtime' => (float)$this->getInput('downtime', '0'),
                'Cost' => (float)$this->getInput('cost', '0'),
                'MachineID' => $this->getInput('machine_id'),
                'Description' => $this->getInput('description'),
                'SpareParts' => $this->getInput('spare_parts'),
                'Status' => $this->getInput('status'),
                'NextServiceDate' => $this->getInput('next_service_date'),
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
