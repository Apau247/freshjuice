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
                'CertID' => $this->getInput('CertID'), 'CertName' => $this->getInput('cert_name'),
                'CertType' => $this->getInput('cert_type'),
                'IssuingAuthority' => $this->getInput('issuing_authority'),
                'IssueDate' => $this->getInput('issue_date'), 'ExpiryDate' => $this->getInput('expiry_date'),
                'DocumentPath' => $this->getInput('document_path'),
                'Status' => $this->getInput('status', 'Active'),
                'Notes' => $this->getInput('notes'),
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
                'CertName' => $this->getInput('cert_name'), 'CertType' => $this->getInput('cert_type'),
                'IssuingAuthority' => $this->getInput('issuing_authority'),
                'IssueDate' => $this->getInput('issue_date'), 'ExpiryDate' => $this->getInput('expiry_date'),
                'Status' => $this->getInput('status'), 'Notes' => $this->getInput('notes'),
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
