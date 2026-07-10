<?php
class DocumentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new DocumentModel();
        $this->viewPath = 'documents';
    }

    public function index()
    {
        $data = [
            'documents' => $this->model->getAllDetailed(),
            'underReview' => $this->model->getUnderReview(),
        ];
        $this->render('index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('documents');
            return;
        }

        $this->requireRole('admin', 'manager');

        $id = generateId('DOC');
        $this->model->create([
            'DocumentID' => $id,
            'Title' => sanitize($this->getInput('Title')),
            'DocumentType' => sanitize($this->getInput('DocumentType')),
            'Version' => sanitize($this->getInput('Version')),
            'Author' => sanitize($this->getInput('Author')),
            'CreatedDate' => $this->getInput('CreatedDate'),
            'ReviewDate' => $this->getInput('ReviewDate'),
            'Status' => sanitize($this->getInput('Status')),
            'FilePath' => sanitize($this->getInput('FilePath')),
            'Notes' => sanitize($this->getInput('Notes')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'document', $id, 'Created document record');
        setFlash('success', 'Document created successfully.');
        $this->redirect('documents');
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('documents');
            return;
        }

        $this->requireRole('admin', 'manager');
        $id = $this->getInput('DocumentID');

        $this->model->update($id, [
            'Title' => sanitize($this->getInput('Title')),
            'DocumentType' => sanitize($this->getInput('DocumentType')),
            'Version' => sanitize($this->getInput('Version')),
            'Author' => sanitize($this->getInput('Author')),
            'CreatedDate' => $this->getInput('CreatedDate'),
            'ReviewDate' => $this->getInput('ReviewDate'),
            'Status' => sanitize($this->getInput('Status')),
            'FilePath' => sanitize($this->getInput('FilePath')),
            'Notes' => sanitize($this->getInput('Notes')),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'document', $id, 'Updated document record');
        setFlash('success', 'Document updated successfully.');
        $this->redirect('documents');
    }

    public function delete()
    {
        $id = $this->getInput('DocumentID');
        $this->model->delete($id);

        logAudit($_SESSION['user_id'], 'delete', 'document', $id, 'Deleted document record');
        setFlash('success', 'Document deleted successfully.');
        $this->redirect('documents');
    }
}
