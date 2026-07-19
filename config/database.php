<?php
declare(strict_types=1);

$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if (!defined($key)) {
            define($key, $value);
        }
    }
}

if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'freshjuice');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');
if (!defined('APP_NAME')) define('APP_NAME', 'FreshJuice Factory');
if (!defined('APP_URL')) define('APP_URL', 'http://localhost/freshjuice');
if (!defined('APP_ROOT')) define('APP_ROOT', dirname(__DIR__));

if (php_sapi_name() !== 'cli') {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_lifetime', '0');
    ini_set('session.gc_maxlifetime', '1800');
    session_start();
}

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
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
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

function sanitizeArray(array $data): array {
    return array_map(function ($v) {
        return is_string($v) ? sanitize($v) : $v;
    }, $data);
}

function generateId(string $prefix): string {
    $date = date('Ymd');
    $rand = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
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

function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . generateCsrfToken() . '">';
}

function validateCsrf(): bool {
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function checkLoginRateLimit(string $userId): bool {
    $key = 'login_attempts_' . $userId;
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'last_attempt' => 0];
    }
    $attempts = &$_SESSION[$key];

    if (time() - $attempts['last_attempt'] > 900) {
        $attempts['count'] = 0;
    }

    return $attempts['count'] < 5;
}

function recordLoginAttempt(string $userId): void {
    $key = 'login_attempts_' . $userId;
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'last_attempt' => 0];
    }
    $_SESSION[$key]['count']++;
    $_SESSION[$key]['last_attempt'] = time();
}

function resetLoginAttempts(string $userId): void {
    $key = 'login_attempts_' . $userId;
    unset($_SESSION[$key]);
}
