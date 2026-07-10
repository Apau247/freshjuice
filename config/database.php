<?php
declare(strict_types=1);

define('DB_HOST', 'localhost');
define('DB_NAME', 'freshjuice');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'FreshJuice Factory');
define('APP_URL', 'http://localhost/freshjuice');
define('APP_ROOT', dirname(__DIR__));

session_start();

function getDb(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ?route=auth/login');
        exit;
    }
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    return [
        'id'        => $_SESSION['user_id'],
        'name'      => $_SESSION['user_name'] ?? '',
        'role_id'   => $_SESSION['user_role_id'] ?? '',
        'role_name' => $_SESSION['user_role_name'] ?? '',
    ];
}

function hasRole(string ...$roleIds): bool {
    $u = currentUser();
    if (!$u) return false;
    return in_array($u['role_id'], $roleIds, true);
}

function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateId(string $prefix): string {
    $date = date('Ymd');
    $rand = strtoupper(substr(uniqid('', true), -5));
    return $prefix . '-' . $date . '-' . $rand;
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function logAudit(string $userId, string $action, string $module, ?string $recordId = null, ?string $details = null): void {
    try {
        $db = getDb();
        $stmt = $db->prepare("INSERT INTO audit_trail (UserID, Action, Module, RecordID, Details, IPAddress) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$userId, $action, $module, $recordId, $details, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);
    } catch (\Exception $e) {
        error_log('Audit log failed: ' . $e->getMessage());
    }
}

function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
