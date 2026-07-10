<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/CertificationModel.php';

class CertificationController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new CertificationModel();
        $this->viewPath = 'certifications';
    }

    public function index(): void {
        $this->render('index', ['certs' => $this->model->all(), 'expiringSoon' => $this->model->getExpiringSoon()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create([
                'CertID' => $this->getInput('CertID'), 'CertName' => $this->getInput('CertName'),
                'CertType' => $this->getInput('CertType'),
                'IssuingAuthority' => $this->getInput('IssuingAuthority'),
                'IssueDate' => $this->getInput('IssueDate'), 'ExpiryDate' => $this->getInput('ExpiryDate'),
                'DocumentPath' => $this->getInput('DocumentPath'),
                'Status' => $this->getInput('Status', 'Active'),
                'Notes' => $this->getInput('Notes'),
            ]);
            setFlash('success', 'Certification added.');
            $this->redirect('certifications');
            return;
        }
        $this->render('form');
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $cert = $this->model->find($id);
        if (!$cert) { setFlash('error', 'Not found.'); $this->redirect('certifications'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, [
                'CertName' => $this->getInput('CertName'), 'CertType' => $this->getInput('CertType'),
                'IssuingAuthority' => $this->getInput('IssuingAuthority'),
                'IssueDate' => $this->getInput('IssueDate'), 'ExpiryDate' => $this->getInput('ExpiryDate'),
                'Status' => $this->getInput('Status'), 'Notes' => $this->getInput('Notes'),
            ]);
            setFlash('success', 'Certification updated.');
            $this->redirect('certifications');
            return;
        }
        $this->render('form', ['cert' => $cert]);
    }

    public function delete(): void {
        $this->model->delete($this->getInput('id'));
        setFlash('success', 'Certification deleted.');
        $this->redirect('certifications');
    }
}
