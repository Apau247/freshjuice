<?php
declare(strict_types=1);

abstract class Controller
{
    protected Model $model;
    protected string $viewPath;

    public function __construct()
    {
        requireLogin();
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = APP_ROOT . '/views/' . $this->viewPath . '/' . $view . '.php';
        if (!file_exists($viewFile)) throw new RuntimeException("View not found: {$view}");
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        require APP_ROOT . '/views/layouts/main.php';
    }

    protected function redirect(string $route): void
    {
        header("Location: ?route={$route}");
        exit;
    }

    protected function getInput(string $key, string $default = ''): string
    {
        return sanitize($_POST[$key] ?? $_GET[$key] ?? $default);
    }

    protected function requireRole(string ...$roleIds): void
    {
        if (!hasRole(...$roleIds)) {
            setFlash('error', 'Access denied. Insufficient permissions.');
            $this->redirect('dashboard');
        }
    }
}
