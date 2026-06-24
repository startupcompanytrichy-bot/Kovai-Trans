<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
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
                <div class="title">Verify OTP</div>
                <div class="subtitle">Enter the 6-digit code sent to your email</div>

                @if(session('error'))
                    <div class="msg error">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="msg success">{{ session('success') }}</div>
                    <div style="font-size:12px; color:#dc2626; text-align:center; margin-bottom:16px; line-height:1.5; background:#fef2f2; padding:10px; border-radius:8px;">
                        <strong>Not received?</strong> Check your <strong>Spam folder</strong> &mdash; if found there, mark as <strong>"Not Spam"</strong> to whitelist future emails. Then enter the code below.
                    </div>
                @endif

                <form method="POST" action="{{ route('password.verify-otp') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="input">
                        <i class="fa fa-key"></i>
                        <input type="text" name="otp" class="form-control" placeholder="Enter 6-digit OTP" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required autofocus>
                    </div>
                    @error('otp')
                        <div class="msg error">{{ $message }}</div>
                    @enderror
                    <button type="submit" id="verifyBtn" class="btn-primary">Verify OTP</button>
                </form>

                <div style="text-align:center; margin-top: 14px;">
                    <span id="otpTimer" style="font-size:13px; color:#94a3b8;"></span>
                </div>

                <div style="text-align:center; margin-top: 6px;">
                    <a id="resendLink" href="{{ route('password.forgot') }}" style="font-size:13px; color:#2563eb; text-decoration:none; display:none;">Resend OTP</a>
                </div>

                <a href="{{ route('login') }}" class="btn-secondary"><i class="fa fa-arrow-left" style="margin-right:8px"></i> Back to Login</a>

                <div class="footer">&copy; {{ date('Y') }} Transport ERP System</div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var created = {{ $created_at_ts }};
            var expiresAt = created + 2 * 60 * 1000;
            var resendAfter = created + 30 * 1000;

            var timerEl = document.getElementById('otpTimer');
            var resendLinkEl = document.getElementById('resendLink');

            function tick() {
                var now = Date.now();
                var otpRemaining = expiresAt - now;
                var resendRemaining = resendAfter - now;

                if (otpRemaining > 0) {
                    if (resendRemaining > 0) {
                        var sec = Math.ceil(resendRemaining / 1000);
                        timerEl.textContent = 'Resend available in ' + sec + 's';
                        timerEl.style.color = '#94a3b8';
                    } else {
                        var sec = Math.ceil(otpRemaining / 1000);
                        timerEl.textContent = 'OTP expires in ' + sec + 's';
                        timerEl.style.color = sec <= 30 ? '#dc2626' : '#2563eb';
                    }
                    resendLinkEl.style.display = 'none';
                } else {
                    timerEl.textContent = 'OTP expired';
                    timerEl.style.color = '#dc2626';
                    resendLinkEl.style.display = 'inline';
                }
            }

            tick();
            setInterval(tick, 1000);

            document.querySelector('form')?.addEventListener('submit', function() {
                var btn = document.getElementById('verifyBtn');
                if (btn) { btn.disabled = true; btn.textContent = 'Verifying...'; btn.style.opacity = '0.7'; }
            });
        })();
    </script>
</body>
</html>
