<?php $pageTitle = 'Login'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($pageTitle) ?> - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0d3625 0%, #1a6b4b 50%, #0d3625 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: rgba(255,255,255,0.95); border-radius: 1rem; box-shadow: 0 20px 60px rgba(0,0,0,0.4); max-width: 420px; width: 100%; }
        .brand-icon { font-size: 3rem; color: #198754; }
    </style>
</head>
<body>
    <div class="login-card p-5 mx-3">
        <div class="text-center mb-4">
            <div class="brand-icon mb-2"><i class="bi bi-droplet-half"></i></div>
            <h3 class="fw-bold text-dark"><?= APP_NAME ?></h3>
            <p class="text-muted small">Factory Management System</p>
        </div>
        <?php $flash = getFlash(); if ($flash): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ icon: '<?= $flash['type'] === 'success' ? 'success' : 'error' ?>', title: '<?= addslashes($flash['message']) ?>', toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
            });
        </script>
        <?php endif; ?>
        <form method="POST" action="?route=auth/login">
            <div class="mb-3">
                <label class="form-label fw-semibold"><i class="bi bi-person-badge me-1"></i> User ID</label>
                <input type="text" name="user_id" class="form-control form-control-lg" placeholder="Enter your User ID" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold"><i class="bi bi-lock me-1"></i> Password</label>
                <input type="password" name="password" class="form-control form-control-lg" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold"><i class="bi bi-box-arrow-in-right me-2"></i> Sign In</button>
        </form>
        <p class="text-center text-muted small mt-4 mb-0">&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
