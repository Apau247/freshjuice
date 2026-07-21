<?php $pageTitle = 'Login'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($pageTitle) ?> - <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        html, body { height:100%; }
        body {
            font-family:'Inter',system-ui,sans-serif;
            min-height:100vh; display:flex; align-items:center; justify-content:center;
            background:linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f766e 100%);
            position:relative; overflow:hidden; padding:1.5rem;
        }
        body::before {
            content:''; position:absolute; top:-20%; right:-10%; width:600px; height:600px;
            border-radius:50%; background:rgba(34,197,94,0.08); filter:blur(80px);
        }
        body::after {
            content:''; position:absolute; bottom:-20%; left:-5%; width:500px; height:500px;
            border-radius:50%; background:rgba(6,182,212,0.06); filter:blur(60px);
        }
        .login-card {
            width:100%; max-width:400px; position:relative; z-index:1;
            background:rgba(255,255,255,0.08); backdrop-filter:blur(24px);
            -webkit-backdrop-filter:blur(24px); border:1px solid rgba(255,255,255,0.12);
            border-radius:20px; padding:2.5rem; box-shadow:0 24px 64px rgba(0,0,0,0.3);
            animation:cardIn .5s ease;
        }
        @keyframes cardIn { from { opacity:0; transform:translateY(20px) scale(.97); } to { opacity:1; transform:none; } }
        .login-brand { text-align:center; margin-bottom:2rem; }
        .login-brand-icon {
            width:56px; height:56px; border-radius:16px;
            background:linear-gradient(135deg, #22c55e 0%, #06b6d4 100%);
            display:inline-flex; align-items:center; justify-content:center;
            margin-bottom:0.75rem; box-shadow:0 8px 24px rgba(34,197,94,0.3);
        }
        .login-brand-icon svg { width:32px; height:32px; }
        .login-brand h3 { color:white; font-size:1.3rem; font-weight:800; letter-spacing:-0.03em; }
        .login-brand p { color:rgba(255,255,255,0.45); font-size:0.82rem; margin-top:0.25rem; }
        .form-group { margin-bottom:1.15rem; }
        .form-label {
            display:block; font-size:0.72rem; font-weight:600; color:rgba(255,255,255,0.6);
            margin-bottom:0.35rem; text-transform:uppercase; letter-spacing:0.04em;
        }
        .form-input {
            width:100%; padding:0.65rem 0.85rem; border:1.5px solid rgba(255,255,255,0.12);
            border-radius:10px; font-size:0.85rem; color:white; font-family:inherit;
            background:rgba(255,255,255,0.06); transition:all .15s;
        }
        .form-input::placeholder { color:rgba(255,255,255,0.3); }
        .form-input:focus { outline:none; border-color:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,0.2); background:rgba(255,255,255,0.1); }
        .input-icon { position:relative; }
        .input-icon i { position:absolute; right:0.85rem; top:50%; transform:translateY(-50%); color:rgba(255,255,255,0.3); font-size:1rem; pointer-events:none; }
        .input-icon .form-input { padding-right:2.5rem; }
        .login-btn {
            width:100%; padding:0.72rem; border:none; border-radius:10px;
            background:linear-gradient(135deg, #22c55e 0%, #06b6d4 100%);
            color:white; font-size:0.85rem; font-weight:700; cursor:pointer;
            transition:all .2s; box-shadow:0 4px 16px rgba(34,197,94,0.3);
            margin-top:0.5rem; font-family:inherit;
        }
        .login-btn:hover { box-shadow:0 8px 24px rgba(34,197,94,0.4); transform:translateY(-1px); }
        .login-btn:active { transform:translateY(0); }
        .login-error {
            background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.2);
            color:#fca5a5; border-radius:10px; padding:0.6rem 0.8rem;
            font-size:0.8rem; margin-bottom:1rem;
        }
        .login-footer { text-align:center; margin-top:1.5rem; }
        .login-footer p { color:rgba(255,255,255,0.3); font-size:0.72rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            <div class="login-brand-icon">
                <svg viewBox="0 0 32 32" fill="none">
                    <rect width="32" height="32" rx="10" fill="url(#lg)"/>
                    <path d="M16 6C11 6 8 10 8 15C8 20 11 26 16 26C21 26 24 20 24 15C24 10 21 6 16 6Z" fill="white" opacity="0.9"/>
                    <path d="M13 14C13 14 14.5 18 16 18C17.5 18 19 14 19 14" stroke="url(#lg)" stroke-width="1.5" stroke-linecap="round"/>
                    <defs><linearGradient id="lg" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#22c55e"/><stop offset="1" stop-color="#06b6d4"/></linearGradient></defs>
                </svg>
            </div>
            <h3><?= APP_NAME ?></h3>
            <p>Factory Management System</p>
        </div>

        <?php $flash = getFlash(); if ($flash): ?>
        <div class="login-error">
            <i class="bi bi-exclamation-circle"></i> <?= sanitize($flash['message']) ?>
        </div>
        <?php endif; ?>

        <?php if ($error = getFlash('login_error')): ?>
        <div class="login-error">
            <i class="bi bi-exclamation-circle"></i> <?= sanitize($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="?route=auth/login">
            <?= csrfField() ?>
            <div class="form-group">
                <label class="form-label">User ID</label>
                <div class="input-icon">
                    <input type="text" name="user_id" class="form-input" placeholder="Enter your User ID" required autofocus autocomplete="username" value="<?= sanitize($_POST['user_id'] ?? '') ?>">
                    <i class="bi bi-person"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-icon">
                    <input type="password" name="password" class="form-input" placeholder="Enter your password" required autocomplete="current-password">
                    <i class="bi bi-lock"></i>
                </div>
            </div>
            <button type="submit" class="login-btn">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </button>
        </form>

        <div class="login-footer">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
