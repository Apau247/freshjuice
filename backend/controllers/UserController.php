<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/UserModel.php';

class UserController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new UserModel();
        $this->viewPath = 'users';
        $route = $_GET['route'] ?? '';
        if ($route !== 'profile') {
            $this->requireRole('ROLE-001');
        }
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
                'UserID' => $this->getInput('user_id'),
                'Name' => $this->getInput('name'),
                'RoleID' => $this->getInput('role_id'),
                'password' => password_hash($pass, PASSWORD_DEFAULT),
            ]);
            logAudit($_SESSION['user_id'], 'CREATE', 'Users', $this->getInput('user_id'), 'Created user');
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
                'Name' => $this->getInput('name'),
                'RoleID' => $this->getInput('role_id'),
                'Status' => $this->getInput('status', 'Active'),
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

    public function profile(): void {
        $userId = $_SESSION['user_id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['form_action'] ?? '';
            if ($action === 'change_password') {
                $this->changePassword($userId);
                return;
            }
            $name = $this->getInput('name');
            $email = $this->getInput('email');
            if (empty($name)) {
                setFlash('error', 'Name is required.');
                $this->redirect('profile');
                return;
            }
            $data = ['Name' => $name, 'Email' => $email];
            $this->model->update($userId, $data);
            $_SESSION['user_name'] = $name;
            logAudit($userId, 'UPDATE', 'Users', $userId, 'Updated profile');
            setFlash('success', 'Profile updated.');
            $this->redirect('profile');
            return;
        }
        $user = $this->model->find($userId);
        if (!$user) { setFlash('error', 'User not found.'); $this->redirect('dashboard'); return; }
        $this->render('profile', ['profileUser' => $user]);
    }

    private function changePassword(string $userId): void {
        $current = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (empty($current) || empty($newPass) || empty($confirm)) {
            setFlash('error', 'All password fields are required.');
            $this->redirect('profile');
            return;
        }
        $user = $this->model->find($userId);
        if (!$user || !password_verify($current, $user['password'])) {
            setFlash('error', 'Current password is incorrect.');
            $this->redirect('profile');
            return;
        }
        if ($newPass !== $confirm) {
            setFlash('error', 'New passwords do not match.');
            $this->redirect('profile');
            return;
        }
        $this->model->update($userId, ['password' => password_hash($newPass, PASSWORD_DEFAULT)]);
        logAudit($userId, 'UPDATE', 'Users', $userId, 'Changed password');
        setFlash('success', 'Password changed successfully.');
        $this->redirect('profile');
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
