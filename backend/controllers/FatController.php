<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/FatModel.php';
require_once __DIR__ . '/../models/MachineModel.php';

class FatController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new FatModel();
        $this->viewPath = 'machines';
    }

    public function index(): void
    {
        $data = [
            'records' => $this->model->getAllDetailed(),
        ];
        $this->render('fat_index', $data);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = generateId('FAT');
            $this->model->create([
                'FAT_ID' => $id,
                'MachineID' => $this->getInput('machine_id'),
                'TestDate' => $this->getInput('test_date'),
                'TestType' => $this->getInput('test_type'),
                'ExpectedResult' => $this->getInput('expected_result'),
                'ActualResult' => $this->getInput('actual_result'),
                'Result' => $this->getInput('result', 'Pending'),
                'DefectsFound' => $this->getInput('defects_found'),
                'TestedBy' => $_SESSION['user_id'] ?? null,
                'Notes' => $this->getInput('notes'),
                'Status' => $this->getInput('status', 'Pending'),
            ]);
            logAudit($_SESSION['user_id'], 'CREATE', 'FAT', $id, 'Created FAT record');
            setFlash('success', 'FAT record created.');
            $this->redirect('fat');
            return;
        }
        $this->render('fat_form', ['machines' => (new MachineModel())->all()]);
    }

    public function edit(): void
    {
        $id = $this->getInput('id');
        $item = $this->model->find($id);
        if (!$item) { setFlash('error', 'Not found.'); $this->redirect('fat'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, [
                'MachineID' => $this->getInput('machine_id'),
                'TestDate' => $this->getInput('test_date'),
                'TestType' => $this->getInput('test_type'),
                'ExpectedResult' => $this->getInput('expected_result'),
                'ActualResult' => $this->getInput('actual_result'),
                'Result' => $this->getInput('result'),
                'DefectsFound' => $this->getInput('defects_found'),
                'Notes' => $this->getInput('notes'),
                'Status' => $this->getInput('status'),
            ]);
            logAudit($_SESSION['user_id'], 'UPDATE', 'FAT', $id, 'Updated FAT record');
            setFlash('success', 'FAT record updated.');
            $this->redirect('fat');
            return;
        }
        $this->render('fat_form', ['record' => $item, 'machines' => (new MachineModel())->all()]);
    }

    public function delete(): void
    {
        $id = $this->getInput('id');
        $this->model->delete($id);
        logAudit($_SESSION['user_id'], 'DELETE', 'FAT', $id, 'Deleted FAT record');
        setFlash('success', 'FAT record deleted.');
        $this->redirect('fat');
    }
}
