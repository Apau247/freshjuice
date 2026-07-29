<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$errors = 0;
$warnings = 0;

echo "=== FreshJuice Validation Script ===\n\n";

// 1. PHP syntax check on all .php files
echo "--- PHP Syntax Check ---\n";
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$phpFiles = [];
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $phpFiles[] = $file->getRealPath();
    }
}
sort($phpFiles);

foreach ($phpFiles as $file) {
    $relative = str_replace($root . DIRECTORY_SEPARATOR, '', $file);
    $output = [];
    $returnCode = 0;
    exec('"' . PHP_BINARY . '" -l "' . $file . '" 2>&1', $output, $returnCode);
    if ($returnCode !== 0) {
        echo "  FAIL: $relative - " . trim(implode("\n", $output)) . "\n";
        $errors++;
    }
}
echo "  Checked " . count($phpFiles) . " PHP files.\n\n";

// 2. Route registration validation
echo "--- Route Registration Check ---\n";
$routerFile = $root . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'index.php';
if (!file_exists($routerFile)) {
    echo "  FAIL: Router file not found at $routerFile\n";
    $errors++;
} else {
    $routerContent = file_get_contents($routerFile);
    preg_match_all("/'([a-z\/-]+)'\s*=>\s*\[.*?'(\w+)'\s*,\s*'(\w+)'\s*,\s*'(\w+)'\]/", $routerContent, $routeMatches, PREG_SET_ORDER);
    $registeredRoutes = [];
    foreach ($routeMatches as $m) {
        $registeredRoutes[$m[1]] = ['class' => $m[3], 'method' => $m[4]];
    }
    echo "  Found " . count($registeredRoutes) . " registered routes.\n";

    // Collect all route references from views
    $viewDir = $root . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'views';
    $viewRoutes = [];
    if (is_dir($viewDir)) {
        $viewIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewDir));
        foreach ($viewIterator as $vf) {
            if ($vf->isFile() && $vf->getExtension() === 'php') {
                $content = file_get_contents($vf->getRealPath());
                preg_match_all('/\?route=([a-z\/-]+)/', $content, $vrMatches);
                foreach ($vrMatches[1] as $r) {
                    $viewRoutes[$r] = true;
                }
            }
        }
    }
    echo "  Found " . count($viewRoutes) . " unique route references in views.\n";

    $missing = [];
    foreach (array_keys($viewRoutes) as $vr) {
        if (!isset($registeredRoutes[$vr])) {
            $missing[] = $vr;
        }
    }
    if ($missing) {
        echo "  WARNING: " . count($missing) . " view-referenced routes not in router:\n";
        foreach ($missing as $m) {
            echo "    - $m\n";
        }
        $warnings++;
    } else {
        echo "  All view-referenced routes are registered.\n";
    }

    // Verify controller methods exist
    echo "\n--- Controller Method Existence ---\n";
    $controllerDir = $root . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'controllers';
    $methodErrors = 0;
    foreach ($registeredRoutes as $route => $info) {
        $controllerFile = $controllerDir . DIRECTORY_SEPARATOR . $info['class'] . '.php';
        if (!file_exists($controllerFile)) {
            // Check auth directory
            $controllerFile = $root . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'auth' . DIRECTORY_SEPARATOR . $info['class'] . '.php';
        }
        if (!file_exists($controllerFile)) {
            echo "  FAIL: Controller file not found for $route: {$info['class']}.php\n";
            $errors++;
            continue;
        }
        $controllerContent = file_get_contents($controllerFile);
        if (!preg_match('/function\s+' . preg_quote($info['method'], '/') . '\s*\(/', $controllerContent)) {
            echo "  FAIL: Method {$info['method']} not found in {$info['class']}.php for route $route\n";
            $methodErrors++;
        }
    }
    if ($methodErrors === 0) {
        echo "  All controller methods exist.\n";
    } else {
        $errors += $methodErrors;
    }
}

// 3. Summary
echo "\n=== Summary ===\n";
echo "  Errors:   $errors\n";
echo "  Warnings: $warnings\n";
exit($errors > 0 ? 1 : 0);
