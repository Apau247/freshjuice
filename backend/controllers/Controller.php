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
        $viewFile = APP_ROOT . '/frontend/views/' . $this->viewPath . '/' . $view . '.php';
        if (!file_exists($viewFile)) throw new RuntimeException("View not found: {$view}");
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        require APP_ROOT . '/frontend/views/layouts/main.php';
    }

    /**
     * Render a view into the bare print layout: company letterhead + document
     * title + content only -- no sidebar, navbar or app chrome. Used for
     * printable documents (reports, receipts) that must show nothing but the
     * results and their heading.
     */
    protected function renderPrint(string $view, array $data = [], string $docTitle = ''): void
    {
        extract($data);
        $viewFile = APP_ROOT . '/frontend/views/' . $this->viewPath . '/' . $view . '.php';
        if (!file_exists($viewFile)) throw new RuntimeException("View not found: {$view}");
        ob_start();
        require $viewFile;
        $printContent = ob_get_clean();
        $printTitle   = $docTitle;
        require APP_ROOT . '/frontend/views/layouts/print.php';
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

    /**
     * Trend analysis for "new record" forms: which values has the factory been
     * using lately? Returns [['value' => id-or-string, 'uses' => n], ...]
     * ordered by most-recently-used, then most-frequently-used. The create
     * forms feed this into trending_options()/trending_value_options() so the
     * dropdowns open in the direction the operation is already going.
     * Table/column names come only from this codebase -- never user input.
     */
    protected function trendIds(string $table, string $column, string $dateColumn, int $limit = 5): array
    {
        $t = str_replace('`', '', $table);
        $c = str_replace('`', '', $column);
        $d = str_replace('`', '', $dateColumn);
        try {
            $rows = $this->model->query(
                "SELECT `$c` AS val, COUNT(*) AS uses, MAX(`$d`) AS latest
                 FROM `$t`
                 WHERE `$c` IS NOT NULL AND `$c` <> ''
                 GROUP BY `$c`
                 ORDER BY latest DESC, uses DESC
                 LIMIT " . max(1, $limit)
            );
        } catch (\Throwable) {
            return [];
        }
        return array_map(fn($r) => ['value' => (string)$r['val'], 'uses' => (int)$r['uses']], $rows);
    }

    /**
     * Server-side range check for one numeric field. Returns a human-readable
     * error message, or null when the value is in range. Chain with ?? to
     * validate several fields and keep the first failure:
     *   $err = $this->checkNumber('Quantity', $qty, 0.01) ?? $this->checkNumber('Cost', $cost, 0);
     */
    protected function checkNumber(string $label, float $value, float $min, ?float $max = null): ?string
    {
        if (!is_finite($value)) return "$label must be a valid number.";
        if ($value < $min)      return "$label cannot be less than $min.";
        if ($max !== null && $value > $max) return "$label cannot be greater than $max.";
        return null;
    }

    protected function requireRole(string ...$roleIds): void
    {
        if (!hasRole(...$roleIds)) {
            setFlash('error', 'Access denied. Insufficient permissions.');
            $this->redirect('dashboard');
        }
    }

    protected function requireCan(string $module): void
    {
        if (!can($module)) {
            setFlash('error', 'Access denied. Insufficient permissions.');
            $this->redirect('dashboard');
        }
    }

    protected function requireCanCreate(string $module): void
    {
        if (!canCreate($module)) {
            setFlash('error', 'Access denied. Insufficient permissions.');
            $this->redirect('dashboard');
        }
    }

    protected function requireCanEdit(string $module): void
    {
        if (!canEdit($module)) {
            setFlash('error', 'Access denied. Insufficient permissions.');
            $this->redirect('dashboard');
        }
    }

    protected function validatePostCsrf(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !validateCsrf()) {
            setFlash('error', 'Invalid security token. Please try again.');
            $this->redirect('dashboard');
        }
    }

    protected function outputCsrfField(): void
    {
        echo csrfField();
    }
}
