<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { width: 100%; height: 100vh; overflow: hidden; background: #f1f5f9; }
        .wrapper { width: 100%; height: 100vh; display: flex; }
        .left { flex: 1; background: linear-gradient(135deg, #0f172a, #1e293b); color: white; display: flex; justify-content: center; align-items: center; padding: 60px; }
        .left-content { width: 100%; max-width: 550px; }
        .left h1 { font-size: 42px; font-weight: 700; line-height: 1.4; margin-bottom: 20px; }
        .desc { font-size: 15px; color: #cbd5e1; line-height: 1.8; margin-bottom: 40px; }
        .features { display: flex; flex-direction: column; gap: 20px; }
        .feature-box { display: flex; align-items: flex-start; gap: 15px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); padding: 20px; border-radius: 16px; transition: 0.3s; }
        .feature-box:hover { transform: translateY(-4px); background: rgba(255,255,255,0.08); }
        .feature-box i { font-size: 24px; color: #38bdf8; margin-top: 4px; }
        .feature-box h4 { font-size: 16px; font-weight: 600; margin-bottom: 6px; }
        .feature-box p { font-size: 13px; color: #cbd5e1; line-height: 1.7; margin: 0; }
        .right { flex: 1; background: #ffffff; position: relative; display: flex; justify-content: center; align-items: center; padding: 40px; }
        .login-box { width: 100%; max-width: 400px; position: relative; }
        .title { font-size: 25px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        .subtitle { font-size: 13px; color: #64748b; margin-bottom: 24px; }
        .msg { text-align: center; font-weight: 600; margin-bottom: 15px; }
        .success { color: #16a34a; }
        .error { color: #dc2626; }
        .input { position: relative; margin-bottom: 14px; }
        .input i { position: absolute; top: 15px; left: 15px; color: #94a3b8; }
        .form-control { height: 40px; border-radius: 8px; border: 1px solid #dbeafe; padding-left: 42px; box-shadow: none; font-size: 13px; }
        .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .pw-toggle { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: rgba(15,23,42,0.05); border: 1px solid rgba(15,23,42,0.08); color: #475569; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 0; }
        .pw-toggle i { font-size: 14px; color: #64748b; line-height: 1; width: 100%; text-align: center; left: 0; margin-top: -5px; }
        .btn-primary { width: 100%; height: 42px; border: none; border-radius: 8px; background: #2563eb; color: white; font-weight: 600; font-size: 14px; transition: 0.18s ease; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { width: 100%; height: 42px; border: 1px solid #dbeafe; border-radius: 8px; background: #fff; color: #475569; font-weight: 600; font-size: 14px; transition: 0.18s ease; text-align: center; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; margin-top: 12px; }
        .btn-secondary:hover { background: #f8fafc; border-color: #2563eb; color: #2563eb; }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 20px; }
        @media(max-width: 992px) { .wrapper { flex-direction: column; } .left { display: none; } .right { width: 100%; height: 100vh; } }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="left">
            <div class="left-content">
                <h1>Smart Transport & Fleet Management System</h1>
                <p class="desc">Centralized platform to manage vehicles, drivers, routes, logistics operations, fuel tracking and real-time fleet monitoring.</p>
                <div class="features">
                    <div class="feature-box"><i class="fa-solid fa-truck-fast"></i><div><h4>Fleet Tracking</h4><p>Monitor vehicles and delivery status in real-time.</p></div></div>
                    <div class="feature-box"><i class="fa-solid fa-route"></i><div><h4>Route Optimization</h4><p>Optimize routes and reduce operational costs efficiently.</p></div></div>
                    <div class="feature-box"><i class="fa-solid fa-user-shield"></i><div><h4>Driver Management</h4><p>Manage driver schedules, attendance and performance.</p></div></div>
                    <div class="feature-box"><i class="fa-solid fa-chart-line"></i><div><h4>Analytics Reports</h4><p>Generate instant transport analytics and performance reports.</p></div></div>
                </div>
            </div>
        </div>
        <div class="right">
            <div class="login-box">
                <div class="title">Reset Password</div>
                <div class="subtitle">Enter your new password</div>

                @if(session('error'))
                    <div class="msg error">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('password.reset') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="input" style="position:relative;">
                        <i class="fa fa-lock"></i>
                        <input id="new_password" type="password" name="password" class="form-control" placeholder="New Password" minlength="6" required autofocus>
                        <button type="button" class="pw-toggle" id="togglePassword" aria-label="Show password"><i class="fa-solid fa-eye"></i></button>
                    </div>
                    @error('password')
                        <div class="msg error">{{ $message }}</div>
                    @enderror
                    <div class="input" style="position:relative;">
                        <i class="fa fa-lock"></i>
                        <input id="confirm_password" type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" minlength="6" required>
                        <button type="button" class="pw-toggle" id="toggleConfirm" aria-label="Show password"><i class="fa-solid fa-eye"></i></button>
                    </div>
                    <button type="submit" class="btn-primary">Update Password</button>
                </form>

                <a href="{{ route('login') }}" class="btn-secondary"><i class="fa fa-arrow-left" style="margin-right:8px"></i> Back to Login</a>

                <div class="footer">&copy; {{ date('Y') }} Transport ERP System</div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function setupToggle(btnId, inputId) {
                const toggle = document.getElementById(btnId);
                const pwd = document.getElementById(inputId);
                if (toggle && pwd) {
                    toggle.addEventListener('click', function() {
                        const icon = toggle.querySelector('i');
                        if (pwd.type === 'password') {
                            pwd.type = 'text';
                            if (icon) { icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
                            toggle.setAttribute('aria-label', 'Hide password');
                        } else {
                            pwd.type = 'password';
                            if (icon) { icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
                            toggle.setAttribute('aria-label', 'Show password');
                        }
                    });
                }
            }
            setupToggle('togglePassword', 'new_password');
            setupToggle('toggleConfirm', 'confirm_password');
        });
    </script>
</body>
</html>
