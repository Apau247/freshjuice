<?php
declare(strict_types=1);

$envFile = dirname(__DIR__, 2) . '/.env';
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
if (!defined('APP_NAME')) define('APP_NAME', 'PROMOTORA FOODS');
if (!defined('APP_TAGLINE')) define('APP_TAGLINE', 'We give you the true taste of the fruit');
if (!defined('APP_ADDRESS')) define('APP_ADDRESS', 'P.O. BOX AN 6535, ACCRA - NORTH');
if (!defined('APP_PHONE')) define('APP_PHONE', '0277 541 253 / 0208 175 593 / 0240 645 157');
if (!defined('APP_EMAIL')) define('APP_EMAIL', 'propinefruity@gmail.com');
if (!defined('APP_URL')) define('APP_URL', 'http://localhost/freshjuice');
if (!defined('APP_ROOT')) define('APP_ROOT', dirname(__DIR__, 2));
if (!defined('APP_BANK')) define('APP_BANK', 'CBG Bank — Manet Towers | GT Bank — Comm. 10 Tema');

if (!defined('MAIL_HOST'))       define('MAIL_HOST', '');
if (!defined('MAIL_USERNAME'))   define('MAIL_USERNAME', '');
if (!defined('MAIL_PASSWORD'))   define('MAIL_PASSWORD', '');
if (!defined('MAIL_ENCRYPTION')) define('MAIL_ENCRYPTION', 'ssl');
if (!defined('MAIL_PORT'))       define('MAIL_PORT', '465');
if (!defined('MAIL_FROM'))       define('MAIL_FROM', '');

if (php_sapi_name() !== 'cli') {
    // ── Production error suppression ────────────────────────────────
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);

    // ── Security headers ────────────────────────────────────────────
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
    }

    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_lifetime', '0');
    ini_set('session.gc_maxlifetime', '1800');
    if (!headers_sent()) {
        ini_set('session.cookie_secure', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? '1' : '0');
    }
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
            PDO::ATTR_TIMEOUT            => 5,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Never let the raw exception surface: it prints the DSN, the DB user
            // and the full server paths straight into the browser.
            error_log('Database connection failed: ' . $e->getMessage());
            dbConnectionFailure();
        }
    }
    return $pdo;
}

/** Render a clean "database unavailable" page and stop. Never echoes credentials. */
function dbConnectionFailure(): never {
    if (php_sapi_name() === 'cli') {
        fwrite(STDERR, "Database connection failed. Check DB_HOST/DB_NAME/DB_USER in .env and that MySQL is running.\n");
        exit(1);
    }
    if (!headers_sent()) {
        http_response_code(503);
        header('Content-Type: text/html; charset=UTF-8');
        header('Retry-After: 30');
    }
    $appName = defined('APP_NAME') ? APP_NAME : 'PROMOTORA FOODS';
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
       . '<meta name="viewport" content="width=device-width,initial-scale=1">'
       . '<title>Service unavailable - ' . htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') . '</title>'
       . '<style>body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;'
       . 'font-family:Inter,system-ui,sans-serif;background:#f1f5f9;color:#334155;padding:24px}'
       . '.card{max-width:460px;background:#fff;border-radius:16px;padding:36px;text-align:center;'
       . 'box-shadow:0 4px 24px rgba(0,0,0,.08)}h1{font-size:1.15rem;margin:0 0 12px}'
       . 'p{font-size:.9rem;line-height:1.6;color:#64748b;margin:0 0 8px}'
       . 'code{background:#f1f5f9;padding:2px 6px;border-radius:6px;font-size:.82rem}</style></head><body>'
       . '<div class="card"><h1>We can&rsquo;t reach the database right now</h1>'
       . '<p>The application is running, but it could not connect to its database. '
       . 'Please try again in a moment.</p>'
       . '<p>If you administer this system: confirm MySQL is running and that '
       . '<code>DB_HOST</code>, <code>DB_NAME</code>, <code>DB_USER</code> and <code>DB_PASS</code> '
       . 'in your <code>.env</code> are correct. Details have been written to the PHP error log.</p>'
       . '</div></body></html>';
    exit;
}

/**
 * Public base URL of the app (scheme + host + the directory containing the
 * front controller's parent), e.g. "http://localhost/freshjuice".
 *
 * dirname() uses the platform separator, so on Windows dirname('/public')
 * returns "\" -- which rtrim($x, '/') does not strip. Left unnormalised that
 * trailing backslash ends up in every asset URL and the whole UI loads
 * unstyled when the app is served from the web root.
 */
function appBaseUrl(): string {
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir    = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')));
    $dir    = rtrim($dir, '/');
    return $scheme . '://' . $host . $dir;
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

function sanitize(mixed $input): string {
    if ($input === null) return '';
    return htmlspecialchars(trim((string)$input), ENT_QUOTES, 'UTF-8');
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

function sendNotification(string $userId, string $title, string $message, ?string $expiresAt = null): void {
    if ($userId === '' || $userId === null) return;
    try {
        $db = getDb();
        $stmt = $db->prepare("INSERT INTO notifications (NotificationID, UserID, Title, Message, expires_at) VALUES (?,?,?,?,?)");
        $stmt->execute([generateId('NTF'), $userId, $title, $message, $expiresAt]);
    } catch (\Exception $e) {
        error_log('Notification send failed: ' . $e->getMessage());
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
    $token = $_POST['csrf_token']
        ?? $_SERVER['HTTP_X_CSRF_TOKEN']
        ?? '';
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

// ── Global exception handler (production) ────────────────────────
if (php_sapi_name() !== 'cli' && !defined('INTEGRATION_TEST')) {
    set_exception_handler(function (Throwable $e): void {
        error_log('Unhandled exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
        }
        echo '<!DOCTYPE html><html><head><title>Error</title></head><body>'
            . '<h2>Something went wrong.</h2>'
            . '<p>Please try again. If the problem persists, contact your administrator.</p>'
            . '</body></html>';
        exit;
    });
}
