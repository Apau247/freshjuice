<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/UserModel.php';

class UserController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new UserModel();
        $this->viewPath = 'users';
        $this->requireRole('ROLE-001');
    }

    public function index(): void {
        $this->render('index', ['users' => $this->model->getAllWithRoles()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pass = $_POST['password'] ?? '';
            if (empty($pass)) {
                setFlash('error', 'Password is required for new users.');
                $this->redirect('users');
                return;
            }
            $this->model->create([
                'UserID' => $this->getInput('UserID'),
                'Name' => $this->getInput('Name'),
                'RoleID' => $this->getInput('RoleID'),
                'password' => password_hash($pass, PASSWORD_DEFAULT),
            ]);
            logAudit($_SESSION['user_id'], 'CREATE', 'Users', $this->getInput('UserID'), 'Created user');
            setFlash('success', 'User created.');
            $this->redirect('users');
            return;
        }
        $this->render('form', ['roles' => $this->model->getRoles()]);
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $user = $this->model->find($id);
        if (!$user) { setFlash('error', 'Not found.'); $this->redirect('users'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'Name' => $this->getInput('Name'),
                'RoleID' => $this->getInput('RoleID'),
                'Status' => $this->getInput('Status', 'Active'),
            ];
            $pass = $_POST['password'] ?? '';
            if (!empty($pass)) {
                $data['password'] = password_hash($pass, PASSWORD_DEFAULT);
            }
            $this->model->update($id, $data);
            logAudit($_SESSION['user_id'], 'UPDATE', 'Users', $id, 'Updated user');
            setFlash('success', 'User updated.');
            $this->redirect('users');
            return;
        }
        $this->render('form', ['user' => $user, 'roles' => $this->model->getRoles()]);
    }

    public function delete(): void {
        $id = $this->getInput('id');
        if ($id === ($_SESSION['user_id'] ?? '')) {
            setFlash('error', 'Cannot delete your own account.');
            $this->redirect('users');
            return;
        }
        $this->model->delete($id);
        logAudit($_SESSION['user_id'], 'DELETE', 'Users', $id, 'Deleted user');
        setFlash('success', 'User deleted.');
        $this->redirect('users');
    }
}
