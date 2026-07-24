<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/DocumentModel.php';

class DocumentController extends Controller
{
    private const UPLOAD_DIR = __DIR__ . '/../../uploads/documents';
    private const MAX_FILE_SIZE = 10 * 1024 * 1024;
    private const ALLOWED_TYPES = [
        'application/pdf',
        'image/jpeg', 'image/png', 'image/gif',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain', 'text/csv',
    ];
    private const ALLOWED_EXTENSIONS = ['pdf','jpg','jpeg','png','gif','doc','docx','xls','xlsx','txt','csv'];

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
            $filePath = $this->handleUpload();

            $this->model->create([
                'DocID' => $id,
                'Title' => $this->getInput('title'),
                'DocType' => $this->getInput('doc_type'),
                'Version' => $this->getInput('version', '1.0'),
                'FilePath' => $filePath,
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
            $data = [
                'Title' => $this->getInput('title'),
                'DocType' => $this->getInput('doc_type'),
                'Version' => $this->getInput('version'),
                'Description' => $this->getInput('description'),
                'Department' => $this->getInput('department'),
                'EffectiveDate' => $this->getInput('effective_date'),
                'ReviewDate' => $this->getInput('review_date'),
                'Status' => $this->getInput('status'),
            ];

            if (!empty($_FILES['document_file']['name'])) {
                if ($doc['FilePath'] && file_exists(APP_ROOT . '/' . $doc['FilePath'])) {
                    unlink(APP_ROOT . '/' . $doc['FilePath']);
                }
                $data['FilePath'] = $this->handleUpload();
            }

            $this->model->update($id, $data);
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
        $doc = $this->model->find($id);
        if ($doc && $doc['FilePath'] && file_exists(APP_ROOT . '/' . $doc['FilePath'])) {
            unlink(APP_ROOT . '/' . $doc['FilePath']);
        }
        $this->model->delete($id);
        logAudit($_SESSION['user_id'], 'DELETE', 'Documents', $id, 'Deleted document');
        setFlash('success', 'Document deleted.');
        $this->redirect('documents');
    }

    public function download(): void
    {
        $id = $this->getInput('id');
        $doc = $this->model->find($id);
        if (!$doc || empty($doc['FilePath'])) {
            setFlash('error', 'File not found.');
            $this->redirect('documents');
            return;
        }

        $fullPath = APP_ROOT . '/' . $doc['FilePath'];
        if (!file_exists($fullPath)) {
            setFlash('error', 'File not found on disk.');
            $this->redirect('documents');
            return;
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mimeMap = [
            'pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png', 'gif' => 'image/gif', 'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'txt' => 'text/plain', 'csv' => 'text/csv',
        ];
        $mime = $mimeMap[$ext] ?? 'application/octet-stream';
        $filename = ($doc['Title'] ?: 'document') . '.' . $ext;

        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($fullPath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        readfile($fullPath);
        exit;
    }

    private function handleUpload(): string
    {
        if (empty($_FILES['document_file']['name'])) {
            return $this->getInput('file_path');
        }

        $file = $_FILES['document_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            setFlash('error', 'File upload failed (error code: ' . $file['error'] . ').');
            $this->redirect('documents');
            return '';
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            setFlash('error', 'File is too large. Maximum size is 10 MB.');
            $this->redirect('documents');
            return '';
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, self::ALLOWED_TYPES, true)) {
            setFlash('error', 'File type not allowed. Allowed: PDF, Images, Word, Excel, Text, CSV.');
            $this->redirect('documents');
            return '';
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            setFlash('error', 'File extension not allowed.');
            $this->redirect('documents');
            return '';
        }

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        $newName = generateId('DOC') . '.' . $ext;
        $dest = self::UPLOAD_DIR . '/' . $newName;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            setFlash('error', 'Failed to save uploaded file.');
            $this->redirect('documents');
            return '';
        }

        return 'uploads/documents/' . $newName;
    }
}
