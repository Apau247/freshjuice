<?php
class FatController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new FatModel();
        $this->viewPath = 'machines';
    }

    public function index()
    {
        $data = [
            'records' => $this->model->getAllDetailed(),
        ];
        $this->render('fat_index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $machineModel = new MachineModel();
            $data = [
                'machines' => $machineModel->all(),
            ];
            $this->render('fat_form', $data);
            return;
        }

        $this->requireRole('admin', 'manager');

        $id = generateId('FAT');
        $this->model->create([
            'FatID' => $id,
            'MachineID' => sanitize($this->getInput('MachineID')),
            'TestDate' => $this->getInput('TestDate'),
            'TestType' => sanitize($this->getInput('TestType')),
            'Result' => sanitize($this->getInput('Result')),
            'PerformedBy' => sanitize($_SESSION['user_id'] ?? ''),
            'Notes' => sanitize($this->getInput('Notes')),
            'Status' => sanitize($this->getInput('Status')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'fat', $id, 'Created FAT record');
        setFlash('success', 'FAT record created successfully.');
        $this->redirect('machines/fat');
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('machines/fat');
            return;
        }

        $this->requireRole('admin', 'manager');
        $id = $this->getInput('FatID');

        $this->model->update($id, [
            'MachineID' => sanitize($this->getInput('MachineID')),
            'TestDate' => $this->getInput('TestDate'),
            'TestType' => sanitize($this->getInput('TestType')),
            'Result' => sanitize($this->getInput('Result')),
            'PerformedBy' => sanitize($_SESSION['user_id'] ?? ''),
            'Notes' => sanitize($this->getInput('Notes')),
            'Status' => sanitize($this->getInput('Status')),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'fat', $id, 'Updated FAT record');
        setFlash('success', 'FAT record updated successfully.');
        $this->redirect('machines/fat');
    }

    public function delete()
    {
        $id = $this->getInput('FatID');
        $this->model->delete($id);

        logAudit($_SESSION['user_id'], 'delete', 'fat', $id, 'Deleted FAT record');
        setFlash('success', 'FAT record deleted successfully.');
        $this->redirect('machines/fat');
    }
}
