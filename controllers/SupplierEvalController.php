<?php
class SupplierEvalController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new SupplierEvalModel();
        $this->viewPath = 'suppliers';
    }

    public function index()
    {
        $data = [
            'evaluations' => $this->model->getAllDetailed(),
        ];
        $this->render('evaluations', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $supplierModel = new SupplierModel();
            $data = [
                'suppliers' => $supplierModel->all(),
            ];
            $this->render('create', $data);
            return;
        }

        $this->requireRole('admin', 'manager');

        $id = generateId('SLE');
        $this->model->create([
            'EvalID' => $id,
            'SupplierID' => sanitize($this->getInput('SupplierID')),
            'EvaluationDate' => $this->getInput('EvaluationDate'),
            'QualityScore' => $this->getInput('QualityScore'),
            'DeliveryScore' => $this->getInput('DeliveryScore'),
            'PriceScore' => $this->getInput('PriceScore'),
            'OverallScore' => $this->getInput('OverallScore'),
            'Comments' => sanitize($this->getInput('Comments')),
            'Evaluator' => sanitize($this->getInput('Evaluator')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'supplier_eval', $id, 'Created supplier evaluation');
        setFlash('success', 'Supplier evaluation created successfully.');
        $this->redirect('suppliers');
    }
}
