<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/DocumentModel.php';

class DocumentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new DocumentModel();
        $this->viewPath = 'documents';
    }

    public function index(): void
    {
        $data = [
            'documents' => $this->model->getAllDetailed(),
            'underReview' => $this->model->getUnderReview(),
        ];
        $this->render('index', $data);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = generateId('DOC');
            $this->model->create([
                'DocID' => $id,
                'Title' => $this->getInput('title'),
                'DocType' => $this->getInput('doc_type'),
                'Version' => $this->getInput('version', '1.0'),
                'FilePath' => $this->getInput('file_path'),
                'Description' => $this->getInput('description'),
                'Department' => $this->getInput('department'),
                'EffectiveDate' => $this->getInput('effective_date'),
                'ReviewDate' => $this->getInput('review_date'),
                'Status' => $this->getInput('status', 'Draft'),
                'ApprovedBy' => $_SESSION['user_id'] ?? null,
            ]);
            logAudit($_SESSION['user_id'], 'CREATE', 'Documents', $id, 'Created document');
            setFlash('success', 'Document created.');
            $this->redirect('documents');
            return;
        }
        $this->render('form');
    }

    public function edit(): void
    {
        $id = $this->getInput('id');
        $doc = $this->model->find($id);
        if (!$doc) { setFlash('error', 'Not found.'); $this->redirect('documents'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, [
                'Title' => $this->getInput('title'),
                'DocType' => $this->getInput('doc_type'),
                'Version' => $this->getInput('version'),
                'FilePath' => $this->getInput('file_path'),
                'Description' => $this->getInput('description'),
                'Department' => $this->getInput('department'),
                'EffectiveDate' => $this->getInput('effective_date'),
                'ReviewDate' => $this->getInput('review_date'),
                'Status' => $this->getInput('status'),
            ]);
            logAudit($_SESSION['user_id'], 'UPDATE', 'Documents', $id, 'Updated document');
            setFlash('success', 'Document updated.');
            $this->redirect('documents');
            return;
        }
        $this->render('form', ['document' => $doc]);
    }

    public function delete(): void
    {
        $id = $this->getInput('id');
        $this->model->delete($id);
        logAudit($_SESSION['user_id'], 'DELETE', 'Documents', $id, 'Deleted document');
        setFlash('success', 'Document deleted.');
        $this->redirect('documents');
    }
}
