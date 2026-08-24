<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/WaterModel.php';

class WaterController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new WaterModel();
        $this->viewPath = 'water';
    }

    public function index(): void {
        $this->render('index', [
            'usageRecords' => $this->model->getUsageRecords(),
            'qualityTests' => $this->model->getQualityTests(),
            'usageByType' => $this->model->getUsageByType(),
        ]);
    }

    public function createUsage(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $err = $this->checkNumber('Quantity', (float)$this->getInput('quantity', '0'), 0);
            if ($err) {
                setFlash('error', $err);
                $this->redirect('water/usage/form');
                return;
            }
            $id = generateId('WU');
            $this->model->getDb()->prepare(
                "INSERT INTO water_usage (WaterUsageID, Date, UsageType, Quantity, Unit, Purpose, RecordedBy) VALUES (?,?,?,?,?,?,?)"
            )->execute([
                $id, $this->getInput('date'),
                $this->getInput('usage_type'), (float)$this->getInput('quantity', '0'),
                $this->getInput('unit', 'litres'), $this->getInput('purpose'),
                $_SESSION['user_id'] ?? null,
            ]);
            setFlash('success', 'Water usage recorded.');
            $this->redirect('water');
            return;
        }
        $this->render('usage_form');
    }

    public function editUsage(): void {
        $id = $this->getInput('id');
        $record = $this->model->findUsage($id);
        if (!$record) { setFlash('error', 'Not found.'); $this->redirect('water'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $err = $this->checkNumber('Quantity', (float)$this->getInput('quantity', '0'), 0);
            if ($err) {
                setFlash('error', $err);
                $this->redirect('water/usage/edit&id=' . urlencode($id));
                return;
            }
            $this->model->updateUsage($id, [
                'Date' => $this->getInput('date'),
                'UsageType' => $this->getInput('usage_type'),
                'Quantity' => (float)$this->getInput('quantity', '0'),
                'Unit' => $this->getInput('unit', 'litres'),
                'Purpose' => $this->getInput('purpose'),
            ]);
            setFlash('success', 'Water usage updated.');
            $this->redirect('water');
            return;
        }
        $this->render('usage_form', ['record' => $record]);
    }

    public function deleteUsage(): void {
        $this->model->deleteUsage($this->getInput('id'));
        setFlash('success', 'Water usage deleted.');
        $this->redirect('water');
    }

    public function createTest(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $err = $this->validateTestValues();
            if ($err !== null) {
                setFlash('error', $err);
                $this->redirect('water/test/form');
                return;
            }
            $id = generateId('WT');
            $this->model->getDb()->prepare(
                "INSERT INTO water_quality_tests (WaterTestID, TestDate, TestType, pH_Level, Turbidity, TDS, Chlorine, BacteriaCount, Result, Notes, TestedBy) VALUES (?,?,?,?,?,?,?,?,?,?,?)"
            )->execute([
                $id, $this->getInput('test_date'),
                $this->getInput('test_type'),
                $this->getInput('ph_level') ?: null, $this->getInput('turbidity') ?: null,
                $this->getInput('tds') ?: null, $this->getInput('chlorine') ?: null,
                $this->getInput('bacteria_count') ?: null,
                $this->getInput('result', 'Pending'), $this->getInput('notes'),
                $this->getInput('tested_by') ?: ($_SESSION['user_id'] ?? null),
            ]);
            setFlash('success', 'Water quality test recorded.');
            $this->redirect('water');
            return;
        }
        $this->render('test_form', ['users' => (new UserModel())->all()]);
    }

    public function editTest(): void {
        $id = $this->getInput('id');
        $test = $this->model->findTest($id);
        if (!$test) { setFlash('error', 'Not found.'); $this->redirect('water'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $err = $this->validateTestValues();
            if ($err !== null) {
                setFlash('error', $err);
                $this->redirect('water/test/edit&id=' . urlencode($id));
                return;
            }
            $this->model->updateTest($id, [
                'TestDate' => $this->getInput('test_date'),
                'TestType' => $this->getInput('test_type'),
                'pH_Level' => $this->getInput('ph_level') ?: null,
                'Turbidity' => $this->getInput('turbidity') ?: null,
                'TDS' => $this->getInput('tds') ?: null,
                'Chlorine' => $this->getInput('chlorine') ?: null,
                'BacteriaCount' => $this->getInput('bacteria_count') ?: null,
                'Result' => $this->getInput('result', 'Pending'),
                'Notes' => $this->getInput('notes'),
            ]);
            setFlash('success', 'Water quality test updated.');
            $this->redirect('water');
            return;
        }
        $this->render('test_form', ['test' => $test, 'users' => (new UserModel())->all()]);
    }

    public function deleteTest(): void {
        $this->model->deleteTest($this->getInput('id'));
        setFlash('success', 'Water quality test deleted.');
        $this->redirect('water');
    }

    /** Water test values are optional, but any provided value must be sane. */
    private function validateTestValues(): ?string {
        $ph = $this->getInput('ph_level');
        if ($ph !== '' && ($v = (float)$ph) !== null) {
            if ($e = $this->checkNumber('pH level', $v, 0, 14)) return $e;
        }
        foreach (['Turbidity' => 'turbidity', 'TDS' => 'tds', 'Chlorine' => 'chlorine', 'Bacteria count' => 'bacteria_count'] as $label => $key) {
            $raw = $this->getInput($key);
            if ($raw !== '') {
                if ($e = $this->checkNumber($label, (float)$raw, 0)) return $e;
            }
        }
        return null;
    }
}
