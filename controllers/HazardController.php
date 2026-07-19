<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/HazardModel.php';

class HazardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new HazardModel();
        $this->viewPath = 'safety';
    }

    public function index(): void
    {
        $data = [
            'hazards' => $this->model->getAllActive(),
            'highRisk' => $this->model->getHighRisk(),
        ];
        $this->render('hazard_index', $data);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = generateId('HAZ');
            $likelihood = $this->getInput('likelihood', 'Possible');
            $consequence = $this->getInput('consequence', 'Moderate');
            $this->model->create([
                'HazardID' => $id,
                'HazardDescription' => $this->getInput('description'),
                'RiskCategory' => $this->getInput('risk_category'),
                'Likelihood' => $likelihood,
                'Consequence' => $consequence,
                'RiskRating' => $this->calculateRiskRating($likelihood, $consequence),
                'ControlMeasures' => $this->getInput('control_measures'),
                'ResponsiblePerson' => $this->getInput('responsible_person'),
                'ReviewDate' => $this->getInput('review_date'),
                'Status' => $this->getInput('status', 'Active'),
            ]);
            logAudit($_SESSION['user_id'], 'CREATE', 'Hazards', $id, 'Created hazard record');
            setFlash('success', 'Hazard recorded.');
            $this->redirect('safety/hazards');
            return;
        }
        $this->render('hazard_form');
    }

    public function edit(): void
    {
        $id = $this->getInput('id');
        $item = $this->model->find($id);
        if (!$item) { setFlash('error', 'Not found.'); $this->redirect('safety/hazards'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $likelihood = $this->getInput('likelihood');
            $consequence = $this->getInput('consequence');
            $this->model->update($id, [
                'HazardDescription' => $this->getInput('description'),
                'RiskCategory' => $this->getInput('risk_category'),
                'Likelihood' => $likelihood,
                'Consequence' => $consequence,
                'RiskRating' => $this->calculateRiskRating($likelihood, $consequence),
                'ControlMeasures' => $this->getInput('control_measures'),
                'ResponsiblePerson' => $this->getInput('responsible_person'),
                'ReviewDate' => $this->getInput('review_date'),
                'Status' => $this->getInput('status'),
            ]);
            logAudit($_SESSION['user_id'], 'UPDATE', 'Hazards', $id, 'Updated hazard record');
            setFlash('success', 'Hazard updated.');
            $this->redirect('safety/hazards');
            return;
        }
        $this->render('hazard_form', ['hazard' => $item]);
    }

    public function delete(): void
    {
        $id = $this->getInput('id');
        $this->model->delete($id);
        logAudit($_SESSION['user_id'], 'DELETE', 'Hazards', $id, 'Deleted hazard record');
        setFlash('success', 'Hazard deleted.');
        $this->redirect('safety/hazards');
    }

    private function calculateRiskRating(string $likelihood, string $consequence): int
    {
        $lScores = ['Rare' => 1, 'Unlikely' => 2, 'Possible' => 3, 'Likely' => 4, 'Almost Certain' => 5];
        $cScores = ['Insignificant' => 1, 'Minor' => 2, 'Moderate' => 3, 'Major' => 4, 'Catastrophic' => 5];
        return ($lScores[$likelihood] ?? 3) * ($cScores[$consequence] ?? 3);
    }
}
