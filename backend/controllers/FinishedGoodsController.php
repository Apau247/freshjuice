<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/FinishedGoodModel.php';
require_once __DIR__ . '/../models/ProductionBatchModel.php';

class FinishedGoodsController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new FinishedGoodModel();
        $this->viewPath = 'finished_goods';
    }

    public function index(): void {
        $this->render('index', ['goods' => $this->model->getAllDetailed()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create([
                'FG_ID' => $this->getInput('FG_ID'),
                'BatchID' => $this->getInput('batch_id') ?: null,
                'Flavour' => $this->getInput('flavour'),
                'ExpiryDate' => $this->getInput('expiry_date'),
                'QuantityAvailable' => (float)$this->getInput('quantity_available', '0'),
                'Unit' => $this->getInput('unit', 'bottles'),
                'StorageLocation' => $this->getInput('storage_location'),
            ]);
            setFlash('success', 'Finished good created.');
            $this->redirect('finished-goods');
            return;
        }
        $this->render('form', ['batches' => (new ProductionBatchModel())->all()]);
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $item = $this->model->find($id);
        if (!$item) { setFlash('error', 'Not found.'); $this->redirect('finished-goods'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, [
                'ExpiryDate' => $this->getInput('expiry_date'),
                'QuantityAvailable' => (float)$this->getInput('quantity_available', '0'),
                'StorageLocation' => $this->getInput('storage_location'),
            ]);
            setFlash('success', 'Finished good updated.');
            $this->redirect('finished-goods');
            return;
        }
        $this->render('form', ['good' => $item]);
    }

    public function delete(): void {
        $this->model->delete($this->getInput('id'));
        setFlash('success', 'Finished good deleted.');
        $this->redirect('finished-goods');
    }
}
