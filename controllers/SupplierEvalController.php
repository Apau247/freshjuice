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
            $this->render('evaluation_form', $data);
            return;
        }

        $this->requireRole('admin', 'manager');

        $id = generateId('SLE');
        $strengths = $this->getInput('Strengths');
        $weaknesses = $this->getInput('Weaknesses');
        $recommendations = $this->getInput('Recommendations');
        $comments = trim($strengths . "\n" . $weaknesses . "\n" . $recommendations);

        $q = (int)$this->getInput('QualityScore');
        $d = (int)$this->getInput('DeliveryScore');
        $p = (int)$this->getInput('PriceScore');
        $overall = ($q + $d + $p) / 3;

        $this->model->create([
            'EvalID' => $id,
            'SupplierID' => sanitize($this->getInput('SupplierID')),
            'EvaluationDate' => $this->getInput('EvaluationDate'),
            'QualityScore' => $q,
            'DeliveryScore' => $d,
            'PriceScore' => $p,
            'OverallScore' => round($overall, 1),
            'Comments' => sanitize($comments),
            'Evaluator' => $_SESSION['user_id'] ?? null,
        ]);

        logAudit($_SESSION['user_id'], 'create', 'supplier_eval', $id, 'Created supplier evaluation');
        setFlash('success', 'Supplier evaluation created successfully.');
        $this->redirect('suppliers/evaluations');
    }
}
