<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/SupplierEvalModel.php';
require_once __DIR__ . '/../models/SupplierModel.php';

class SupplierEvalController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new SupplierEvalModel();
        $this->viewPath = 'suppliers';
    }

    public function index(): void
    {
        $data = [
            'evaluations' => $this->model->getAllDetailed(),
        ];
        $this->render('evaluations', $data);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('evaluation_form', [
                'suppliers' => (new SupplierModel())->all(),
            ]);
            return;
        }

        $id = generateId('SLE');
        $q = (float)$this->getInput('quality_score', '0');
        $d = (float)$this->getInput('delivery_score', '0');
        $p = (float)$this->getInput('price_score', '0');
        $overall = round(($q + $d + $p) / 3, 1);

        $this->model->create([
            'EvaluationID' => $id,
            'SupplierID' => $this->getInput('supplier_id'),
            'EvaluationDate' => $this->getInput('evaluation_date'),
            'QualityScore' => $q,
            'DeliveryScore' => $d,
            'PriceScore' => $p,
            'OverallScore' => $overall,
            'Strengths' => $this->getInput('strengths'),
            'Weaknesses' => $this->getInput('weaknesses'),
            'Recommendations' => $this->getInput('recommendations'),
            'EvaluatedBy' => $_SESSION['user_id'] ?? null,
        ]);

        logAudit($_SESSION['user_id'], 'CREATE', 'Supplier Eval', $id, 'Created supplier evaluation');
        setFlash('success', 'Evaluation created.');
        $this->redirect('supplier-evaluations');
    }

    public function edit(): void
    {
        $id = $this->getInput('id');
        $item = $this->model->find($id);
        if (!$item) { setFlash('error', 'Not found.'); $this->redirect('supplier-evaluations'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $q = (float)$this->getInput('quality_score', '0');
            $d = (float)$this->getInput('delivery_score', '0');
            $p = (float)$this->getInput('price_score', '0');
            $overall = round(($q + $d + $p) / 3, 1);

            $this->model->update($id, [
                'SupplierID' => $this->getInput('supplier_id'),
                'EvaluationDate' => $this->getInput('evaluation_date'),
                'QualityScore' => $q,
                'DeliveryScore' => $d,
                'PriceScore' => $p,
                'OverallScore' => $overall,
                'Strengths' => $this->getInput('strengths'),
                'Weaknesses' => $this->getInput('weaknesses'),
                'Recommendations' => $this->getInput('recommendations'),
            ]);
            logAudit($_SESSION['user_id'], 'UPDATE', 'Supplier Eval', $id, 'Updated evaluation');
            setFlash('success', 'Evaluation updated.');
            $this->redirect('supplier-evaluations');
            return;
        }
        $this->render('evaluation_form', [
            'evaluation' => $item,
            'suppliers' => (new SupplierModel())->all(),
        ]);
    }

    public function delete(): void
    {
        $id = $this->getInput('id');
        $this->model->delete($id);
        logAudit($_SESSION['user_id'], 'DELETE', 'Supplier Eval', $id, 'Deleted evaluation');
        setFlash('success', 'Evaluation deleted.');
        $this->redirect('supplier-evaluations');
    }
}
