<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class NotificationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'notifications';
    }

    public function index(): void
    {
        $model = new NotificationModel();
        $model->cleanupExpired();
        $this->render('index', [
            'notifications' => $model->getAll(),
        ]);
    }

    public function markRead(): void
    {
        $id = $this->getInput('id');
        if ($id) {
            (new NotificationModel())->markRead($id);
        }
        $this->redirect('notifications');
    }
}
