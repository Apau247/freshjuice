<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — FreshJuice</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        html, body { height:100%; }
        body { font-family:'Inter',system-ui,sans-serif; overflow:hidden; }
        .login-page {
            min-height:100vh; display:flex; align-items:center; justify-content:center;
            background:linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f766e 100%);
            position:relative; padding:1.5rem;
        }
        .login-page::before {
            content:''; position:absolute; top:-20%; right:-10%; width:600px; height:600px;
            border-radius:50%; background:rgba(34,197,94,0.08); filter:blur(80px);
        }
        .login-page::after {
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
        .login-brand-icon i { font-size:1.5rem; color:white; }
        .login-brand h1 { color:white; font-size:1.3rem; font-weight:800; letter-spacing:-0.03em; }
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
            animation:shake .4s ease;
        }
        @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-6px)} 75%{transform:translateX(6px)} }
        .login-footer { text-align:center; margin-top:1.5rem; }
        .login-footer p { color:rgba(255,255,255,0.3); font-size:0.72rem; }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-brand">
                <div class="login-brand-icon">
                    <i class="bi bi-cup-straw"></i>
                </div>
                <h1>FreshJuice</h1>
                <p>Factory Management System</p>
            </div>

            @if($errors->any())
                <div class="login-error">
                    <i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">User ID</label>
                    <div class="input-icon">
                        <input type="text" name="user_id" class="form-input" placeholder="Enter your User ID"
                               value="{{ old('user_id') }}" autofocus required>
                        <i class="bi bi-person"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-icon">
                        <input type="password" name="password" class="form-input" placeholder="Enter your password" required>
                        <i class="bi bi-lock"></i>
                    </div>
                </div>
                <button type="submit" class="login-btn">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>
            </form>

            <div class="login-footer">
                <p>FreshJuice Factory Management &copy; 2026</p>
            </div>
        </div>
    </div>
</body>
</html>
