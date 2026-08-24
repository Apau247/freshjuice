<?php
$t = microtime(true);
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=freshjuice;charset=utf8mb4', 'root', '', [PDO::ATTR_TIMEOUT => 5, PDO::ERRMODE_EXCEPTION => true]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo 'connected in ', round(microtime(true) - $t, 2), "s\n";
    echo 'batches: ', $pdo->query('SELECT COUNT(*) FROM production_batches')->fetchColumn(), "\n";
} catch (Throwable $e) {
    echo 'ERR: ', $e->getMessage(), "\n";
}
