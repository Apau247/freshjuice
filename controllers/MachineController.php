<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/MachineModel.php';

class MachineController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new MachineModel();
        $this->viewPath = 'machines';
    }

    public function index(): void {
        $this->render('index', ['machines' => $this->model->all()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create([
                'MachineID' => $this->getInput('MachineID'), 'Name' => $this->getInput('Name'),
                'Type' => $this->getInput('Type'), 'Location' => $this->getInput('Location'),
                'Status' => $this->getInput('Status', 'Operational'),
                'InstallDate' => $this->getInput('InstallDate'),
            ]);
            setFlash('success', 'Machine created.');
            $this->redirect('machines');
            return;
        }
        $this->render('form');
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $machine = $this->model->find($id);
        if (!$machine) { setFlash('error', 'Not found.'); $this->redirect('machines'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, [
                'Name' => $this->getInput('Name'), 'Type' => $this->getInput('Type'),
                'Location' => $this->getInput('Location'),
                'Status' => $this->getInput('Status'),
                'LastService' => $this->getInput('LastService'),
            ]);
            setFlash('success', 'Machine updated.');
            $this->redirect('machines');
            return;
        }
        $this->render('form', ['machine' => $machine]);
    }

    public function delete(): void {
        $this->model->delete($this->getInput('id'));
        setFlash('success', 'Machine deleted.');
        $this->redirect('machines');
    }
}
