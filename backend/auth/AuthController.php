<?php
declare(strict_types=1);

require_once APP_ROOT . '/backend/models/Model.php';
require_once APP_ROOT . '/backend/auth/MailHelper.php';

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
            if (!validateCsrf()) {
                setFlash('error', 'Invalid security token. Please try again.');
                header('Location: ?route=auth/login');
                exit;
            }

            $userId = trim($_POST['user_id'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($userId) || empty($password)) {
                setFlash('error', 'Please enter both User ID and password.');
                header('Location: ?route=auth/login');
                exit;
            }

            if (!checkLoginRateLimit($userId)) {
                setFlash('error', 'Too many login attempts. Please wait 15 minutes.');
                header('Location: ?route=auth/login');
                exit;
            }

            $stmt = $this->db->prepare(
                "SELECT u.*, r.RoleName FROM users u JOIN roles r ON u.RoleID = r.RoleID WHERE u.UserID = ? AND u.Status = 'Active'"
            );
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                $_SESSION['user_id']        = $user['UserID'];
                $_SESSION['user_name']      = $user['Name'];
                $_SESSION['user_role_id']   = $user['RoleID'];
                $_SESSION['user_role_name'] = $user['RoleName'];

                resetLoginAttempts($userId);
                logAudit($user['UserID'], 'LOGIN', 'Auth', $user['UserID'], 'User logged in');
                setFlash('success', 'Welcome back, ' . $user['Name'] . '!');
                header('Location: ?route=dashboard');
                exit;
            }

            recordLoginAttempt($userId);
            logAudit($userId, 'LOGIN_FAILED', 'Auth', $userId, 'Invalid credentials');
            setFlash('error', 'Invalid User ID or password.');
            header('Location: ?route=auth/login');
            exit;
        }

        require APP_ROOT . '/frontend/views/auth/login.php';
    }

    public function logout(): void
    {
        $uid = $_SESSION['user_id'] ?? null;
        if ($uid) logAudit($uid, 'LOGOUT', 'Auth', $uid, 'User logged out');
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        header('Location: ?route=auth/login');
        exit;
    }

    public function forgotPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrf()) {
                setFlash('error', 'Invalid security token. Please try again.');
                header('Location: ?route=auth/forgot');
                exit;
            }

            $userId = trim($_POST['user_id'] ?? '');

            if (empty($userId)) {
                setFlash('error', 'Please enter your User ID.');
                header('Location: ?route=auth/forgot');
                exit;
            }

            $stmt = $this->db->prepare("SELECT UserID, Name FROM users WHERE UserID = ? AND Status = 'Active'");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + 3600);

                $update = $this->db->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE UserID = ?");
                $update->execute([$token, $expires, $user['UserID']]);

                logAudit($user['UserID'], 'PASSWORD_RESET_REQUEST', 'Auth', $user['UserID'], 'Password reset requested');

                $resetUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
                    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
                    . rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/')
                    . '?route=auth/reset&token=' . $token;

                MailHelper::sendPasswordReset($user['Name'], $user['UserID'], $resetUrl);

                setFlash('success', 'Password reset link has been generated. You can now reset your password.');
                header('Location: ?route=auth/reset&token=' . $token);
                exit;
            }

            setFlash('error', 'No account found with that User ID.');
            header('Location: ?route=auth/forgot');
            exit;
        }

        require APP_ROOT . '/frontend/views/auth/forgot_password.php';
    }

    public function resetPassword(): void
    {
        $token = $_GET['token'] ?? $_POST['token'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrf()) {
                setFlash('error', 'Invalid security token. Please try again.');
                header('Location: ?route=auth/reset&token=' . $token);
                exit;
            }

            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $token = $_POST['token'] ?? '';

            if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
                setFlash('error', 'Please fill in all fields.');
                header('Location: ?route=auth/reset&token=' . $token);
                exit;
            }

            if ($newPassword !== $confirmPassword) {
                setFlash('error', 'Passwords do not match.');
                header('Location: ?route=auth/reset&token=' . $token);
                exit;
            }

            if (strlen($newPassword) < 6) {
                setFlash('error', 'Password must be at least 6 characters.');
                header('Location: ?route=auth/reset&token=' . $token);
                exit;
            }

            $stmt = $this->db->prepare("SELECT UserID FROM users WHERE reset_token = ? AND reset_expires > NOW() AND Status = 'Active'");
            $stmt->execute([$token]);
            $user = $stmt->fetch();

            if (!$user) {
                setFlash('error', 'Invalid or expired reset token.');
                header('Location: ?route=auth/forgot');
                exit;
            }

            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $this->db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE UserID = ?");
            $update->execute([$hashed, $user['UserID']]);

            logAudit($user['UserID'], 'PASSWORD_RESET', 'Auth', $user['UserID'], 'Password was reset');

            setFlash('success', 'Password has been reset. Please sign in.');
            header('Location: ?route=auth/login');
            exit;
        }

        if (empty($token)) {
            setFlash('error', 'No reset token provided.');
            header('Location: ?route=auth/forgot');
            exit;
        }

        $stmt = $this->db->prepare("SELECT UserID FROM users WHERE reset_token = ? AND reset_expires > NOW() AND Status = 'Active'");
        $stmt->execute([$token]);
        $validToken = $stmt->fetch();

        if (!$validToken) {
            $tokenInvalid = true;
        } else {
            $tokenInvalid = false;
        }

        require APP_ROOT . '/frontend/views/auth/reset_password.php';
    }
}
