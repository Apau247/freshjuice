<?php
declare(strict_types=1);

require_once APP_ROOT . '/models/Model.php';

class AuthController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDb();
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = trim($_POST['user_id'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($userId) || empty($password)) {
                setFlash('error', 'Please enter both User ID and password.');
                header('Location: ?route=auth/login');
                exit;
            }

            $stmt = $this->db->prepare(
                "SELECT u.*, r.RoleName FROM users u JOIN roles r ON u.RoleID = r.RoleID WHERE u.UserID = ? AND u.Status = 'Active'"
            );
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']        = $user['UserID'];
                $_SESSION['user_name']      = $user['Name'];
                $_SESSION['user_role_id']   = $user['RoleID'];
                $_SESSION['user_role_name'] = $user['RoleName'];

                logAudit($user['UserID'], 'LOGIN', 'Auth', $user['UserID'], 'User logged in');
                setFlash('success', 'Welcome back, ' . $user['Name'] . '!');
                header('Location: ?route=dashboard');
                exit;
            }

            setFlash('error', 'Invalid User ID or password.');
            header('Location: ?route=auth/login');
            exit;
        }

        require APP_ROOT . '/views/auth/login.php';
    }

    public function logout(): void
    {
        $uid = $_SESSION['user_id'] ?? null;
        if ($uid) logAudit($uid, 'LOGOUT', 'Auth', $uid, 'User logged out');
        session_destroy();
        header('Location: ?route=auth/login');
        exit;
    }
}
