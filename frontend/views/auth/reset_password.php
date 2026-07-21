<?php $pageTitle = 'Reset Password'; ?>
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
        .login-brand-icon i { font-size:1.6rem; color:white; }
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
        .login-success {
            background:rgba(34,197,94,0.12); border:1px solid rgba(34,197,94,0.2);
            color:#86efac; border-radius:10px; padding:0.6rem 0.8rem;
            font-size:0.8rem; margin-bottom:1rem;
        }
        .login-footer { text-align:center; margin-top:1.5rem; }
        .login-footer a { color:rgba(255,255,255,0.45); font-size:0.78rem; text-decoration:none; transition:color .15s; }
        .login-footer a:hover { color:#22c55e; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            <div class="login-brand-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h3>Reset Password</h3>
            <p>Enter your new password below</p>
        </div>

        <?php if (!empty($tokenInvalid)): ?>
        <div class="login-error">
            <i class="bi bi-exclamation-circle"></i> This reset link is invalid or has expired. Please request a new one.
        </div>
        <?php else: ?>

        <?php $flash = getFlash(); if ($flash): ?>
        <div class="<?= $flash['type'] === 'success' ? 'login-success' : 'login-error' ?>">
            <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle' ?>"></i> <?= sanitize($flash['message']) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="?route=auth/reset">
            <?= csrfField() ?>
            <input type="hidden" name="token" value="<?= sanitize($token) ?>">
            <div class="form-group">
                <label class="form-label">New Password</label>
                <div class="input-icon">
                    <input type="password" name="new_password" class="form-input" placeholder="Enter new password" required minlength="6" autocomplete="new-password">
                    <i class="bi bi-lock"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <div class="input-icon">
                    <input type="password" name="confirm_password" class="form-input" placeholder="Confirm new password" required minlength="6" autocomplete="new-password">
                    <i class="bi bi-lock-fill"></i>
                </div>
            </div>
            <button type="submit" class="login-btn">
                <i class="bi bi-check-lg"></i> Reset Password
            </button>
        </form>

        <?php endif; ?>

        <div class="login-footer">
            <a href="?route=auth/login"><i class="bi bi-arrow-left"></i> Back to Sign In</a>
        </div>
    </div>
</body>
</html>
