<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            width: 100%;
            height: 100vh;
            overflow: hidden;
            background: #f1f5f9;
        }

        /* MAIN WRAPPER */
        .wrapper {
            width: 100%;
            height: 100vh;
            display: flex;
        }

        /* LEFT SIDE */
        .left {
            flex: 1;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 60px;
        }

        .left-content {
            width: 100%;
            max-width: 550px;
        }

        .left h1 {
            font-size: 42px;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 20px;
        }

        .desc {
            font-size: 15px;
            color: #cbd5e1;
            line-height: 1.8;
            margin-bottom: 40px;
        }

        /* FEATURES */
        .features {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .feature-box {
            display: flex;
            align-items: flex-start;
            gap: 15px;

            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);

            padding: 20px;
            border-radius: 16px;

            transition: 0.3s;
            backdrop-filter: blur(10px);
        }

        .feature-box:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.08);
        }

        .feature-box i {
            font-size: 24px;
            color: #38bdf8;
            margin-top: 4px;
        }

        .feature-box h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .feature-box p {
            font-size: 13px;
            color: #cbd5e1;
            line-height: 1.7;
            margin: 0;
        }

        /* RIGHT SIDE */
        .right {
            flex: 1;
            background: #ffffff;
            position: relative;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 40px;
        }

        .right>.lang-component {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 100;
        }

        .left {
            position: relative;
        }

        .lang-component {
            position: relative;
            width: auto;
            z-index: 0;
            display: inline-flex;
            align-items: center;
        }

        .lang-component .lang-btn {
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            padding: 7px 10px;
            border-radius: 10px;
            border: 1px solid #e6edf8;
            background: #ffffff;
            color: #0f172a;
            font-size: 13px;
            min-width: 120px;
            cursor: pointer;
            line-height: 1;
        }

        .lang-component .lang-btn svg {
            margin-left: auto;
        }

        .lang-component .lang-label {
            white-space: nowrap;
        }

        .lang-component .lang-list {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            display: none;
            list-style: none;
            margin: 0;
            padding: 8px;
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid #e6edf8;
            box-shadow: 0 8px 20px rgba(2, 6, 23, 0.08);
            min-width: 150px;
            z-index: 50;
        }

        .lang-component .lang-list li {
            padding: 10px 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border-radius: 8px;
            color: #0f172a;
            font-size: 13px;
        }

        .lang-component .lang-list li span {
            display: inline-flex;
            align-items: center;
        }

        .lang-component .lang-list li span:first-child {
            min-width: 18px;
            min-height: 12px;
        }

        .lang-component .lang-list li:hover {
            background: #f8fafc;
        }

        .lang-component .lang-flag,
        .lang-component .flag-us,
        .lang-component .flag-in {
            width: 18px;
            height: 12px;
            display: inline-block;
        }

        .network-toast {
            display: none;
            opacity: 0;
            align-items: center;
            justify-content: center;
            min-width: 180px;
            max-width: 260px;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid transparent;
            font-size: 13px;
            line-height: 1.4;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
            text-align: left;
            white-space: nowrap;
            background: #f8fafc;
            transition: opacity 0.2s ease;
        }

        .network-toast.show {
            display: inline-flex;
        }

        .network-toast.offline {
            background: #fee2e2;
            border-color: #fecaca;
            color: #b91c1c;
        }

        .network-toast.online {
            background: #dcfce7;
            border-color: #bbf7d0;
            color: #166534;
        }

        /* LOGIN BOX */
        .login-box {
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        .login-top-row {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            margin-bottom: 22px;
            width: 100%;
        }

        .login-top-row .network-toast {
            flex: 1;
            min-width: 180px;
            display: inline-flex;
            visibility: hidden;
            opacity: 0;
        }

        .network-toast.show {
            visibility: visible;
            opacity: 1;
        }

        .title {
            font-size: 25px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 24px;
        }

        /* ALERT */
        .msg {
            text-align: center;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .success {
            color: #16a34a;
        }

        .error {
            color: #dc2626;
        }

        /* INPUT */
        .input {
            position: relative;
            margin-bottom: 14px;
        }

        .input i {
            position: absolute;
            top: 15px;
            left: 15px;
            color: #94a3b8;
        }

        .form-control {
            height: 40px;
            border-radius: 8px;
            border: 1px solid #dbeafe;
            padding-left: 42px;
            box-shadow: none;
            font-size: 13px;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* password toggle button */
        .pw-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(15, 23, 42, 0.05);
            border: 1px solid rgba(15, 23, 42, 0.08);
            color: #475569;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
        }

        .pw-toggle i {
            font-size: 14px;
            color: #64748b;
            line-height: 1;
            width: 100%;
            text-align: center;
            left: 0px;
            margin-top: -5px;
        }

        /* OPTIONS */
        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember input {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .remember label {
            font-size: 14px;
            color: #475569;
            cursor: pointer;
            margin: 0;
        }

        .forgot-password {
            font-size: 14px;
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        /* BUTTON */
        .btn-login {
            width: 100%;
            height: 42px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            transition: 0.18s ease;
            font-size: 14px;
        }

        .btn-login:hover {
            background: #1d4ed8;
        }

        .btn-login.done {
            background: #16a34a;
        }

        .btn-text {
            position: relative;
            z-index: 2;
        }

        /* TRUCK ANIMATION */
        .truck-box {
            position: absolute;
            top: 50%;
            left: -40px;
            transform: translateY(-50%);
            opacity: 0;
        }

        .track {
            position: absolute;
            bottom: 6px;
            left: 10px;
            right: 10px;
            height: 3px;
            background: rgba(255, 255, 255, 0.2);
            overflow: hidden;
            border-radius: 10px;
        }

        .track::after {
            content: "";
            position: absolute;
            width: 40%;
            height: 100%;
            background: #38bdf8;
            left: -40%;
        }

        .btn-login.run .truck-box {
            opacity: 1;
            animation: moveTruck 1.8s linear forwards;
        }

        .btn-login.run .track::after {
            animation: loading 1.8s linear forwards;
        }

        @keyframes moveTruck {
            0% {
                left: -40px;
            }

            100% {
                left: calc(100% - 35px);
            }
        }

        @keyframes loading {
            0% {
                left: -40%;
            }

            100% {
                left: 100%;
            }
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 20px;
        }

        /* MOBILE */
        @media(max-width: 992px) {

            .wrapper {
                flex-direction: column;
            }

            .left {
                display: none;
            }

            .right {
                width: 100%;
                height: 100vh;
            }
        }
    </style>

</head>

<body>

    <div class="wrapper">

        <!-- LEFT SIDE -->
        <div class="left">

            <div class="left-content">

                <h1>
                    Smart Transport & Fleet
                    Management System
                </h1>

                <p class="desc">
                    Centralized platform to manage vehicles,
                    drivers, routes, logistics operations,
                    fuel tracking and real-time fleet monitoring.
                </p>

                <!-- FEATURES -->
                <div class="features">

                    <div class="feature-box">
                        <i class="fa-solid fa-truck-fast"></i>

                        <div>
                            <h4>Fleet Tracking</h4>

                            <p>
                                Monitor vehicles and delivery
                                status in real-time.
                            </p>
                        </div>
                    </div>

                    <div class="feature-box">
                        <i class="fa-solid fa-route"></i>

                        <div>
                            <h4>Route Optimization</h4>

                            <p>
                                Optimize routes and reduce
                                operational costs efficiently.
                            </p>
                        </div>
                    </div>

                    <div class="feature-box">
                        <i class="fa-solid fa-user-shield"></i>

                        <div>
                            <h4>Driver Management</h4>

                            <p>
                                Manage driver schedules,
                                attendance and performance.
                            </p>
                        </div>
                    </div>

                    <div class="feature-box">
                        <i class="fa-solid fa-chart-line"></i>

                        <div>
                            <h4>Analytics Reports</h4>

                            <p>
                                Generate instant transport
                                analytics and performance reports.
                            </p>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <!-- RIGHT SIDE -->
        <div class="right">
            <?php if (isset($component)) {
                $__componentOriginal = $component;
            } ?>
            <?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.lang-select', 'data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
            <?php $component->withName('lang-select'); ?>
            <?php if ($component->shouldRender()): ?>
                <?php $__env->startComponent($component->resolveView(), $component->data()); ?>
                <?php $component->withAttributes([]); ?>
                <?php echo $__env->renderComponent(); ?>
            <?php endif; ?>
            <?php if (isset($__componentOriginal)): ?>
                <?php $component = $__componentOriginal; ?>
                <?php unset($__componentOriginal); ?>
            <?php endif; ?>
            <div class="login-box">
                <div class="login-top-row">
                    <div id="network-toast" class="network-toast" role="status" aria-live="polite"></div>
                </div>
                <div class="title">
                    Welcome Back
                </div>

                <div class="subtitle">
                    Sign in to continue
                </div>
                {{-- SUCCESS: show on login, then redirect to home/dashboard after animation --}}
                @if(session('success'))

                <div id="success-msg" class="msg success" style="display:none" data-server-text="{{ session('success') }}">
                    {{ session('success') }}
                </div>

                <script>
                    window.addEventListener('load', function() {

                        const btn = document.getElementById("loginBtn");
                        if (!btn) return;

                        const text = btn.querySelector('.btn-text');
                        const s = document.getElementById('success-msg');
                        const successText = s ? (s.dataset.serverText || s.textContent.trim()) : 'Login Successful';

                        // Hide the inline success message until animation completes
                        if (s) s.style.display = 'none';

                        // Start truck animation
                        btn.classList.add('run');

                        // Wait for the truck animation to finish, then show success
                        const truck = btn.querySelector('.truck-box');
                        const showSuccess = () => {
                            // Show server success message inside the button
                            if (text) text.innerText = successText;
                            if (s) s.style.display = 'block';
                            btn.classList.remove('run');
                            btn.classList.add('done');

                            // Keep success visible for 5s, then redirect to dashboard
                            setTimeout(() => {
                                window.location.href = "{{ route('dashboard') }}";
                            }, 5000);
                        };

                        if (truck) {
                            truck.addEventListener('animationend', showSuccess, {
                                once: true
                            });
                            truck.addEventListener('webkitAnimationEnd', showSuccess, {
                                once: true
                            });
                        } else {
                            // fallback: use timeout
                            setTimeout(showSuccess, 1800);
                        }

                    });
                </script>

                @endif


                {{-- LOGOUT MESSAGE --}}
                @if(session('logout_success'))

                <div id="logout-msg" class="msg success">
                    {{ session('logout_success') }}
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const logoutMsg = document.getElementById('logout-msg');
                        if (!logoutMsg) return;
                        setTimeout(function() {
                            logoutMsg.style.display = 'none';
                        }, 5000);
                    });
                </script>

                @endif

                {{-- ERROR --}}
                @if(session('error'))

                <div id="error-msg" class="msg error">
                    {{ session('error') }}
                </div>

                <script>
                    window.addEventListener('load', function() {

                        const btn = document.getElementById("loginBtn");

                        // START ANIMATION
                        btn.classList.add("run");

                        setTimeout(() => {

                            btn.classList.remove("run");

                            // ERROR BUTTON COLOR
                            btn.style.background = "#dc2626";

                            btn.querySelector(".btn-text").innerText = "Login Failed";

                        }, 1800);

                        // RESET AFTER 10 SECONDS
                        setTimeout(() => {

                            document.getElementById("error-msg").style.display = "none";

                            btn.style.background = "#2563eb";

                            btn.querySelector(".btn-text").innerText = "Sign In";

                        }, 5000);

                    });
                </script>

                @endif
                <!-- LOGIN FORM -->
                <form method="POST" action="{{ route('Login') }}">

                    @csrf

                    <!-- USERNAME -->
                    <div class="input">

                        <i class="fa fa-user"></i>

                        <input type="text"
                            name="username"
                            id="username"
                            class="form-control"
                            placeholder="Email or Mobile Number"
                            autocomplete="username"
                            required>

                    </div>

                    <!-- PASSWORD -->
                    <div class="input" style="position:relative;">

                        <i class="fa fa-lock"></i>

                        <input id="password" type="password"
                            name="password"
                            class="form-control"
                            placeholder="Password"
                            autocomplete="current-password"
                            required>

                        <button type="button" id="togglePassword" class="pw-toggle" aria-label="Toggle password"><i class="fa-solid fa-eye" aria-hidden="true"></i></button>

                    </div>

                    <!-- OPTIONS -->
                    <div class="options">

                        <div class="remember">

                            <input type="checkbox"
                                id="remember"
                                name="remember"
                                value="1"
                                checked>

                            <label for="remember">
                                Stay Login In
                            </label>

                        </div>

                        <a href="{{ route('password.forgot') }}"
                            class="forgot-password">

                            Forgot Password?

                        </a>

                    </div>

                    <!-- BUTTON -->
                    <button type="submit"
                        class="btn-login"
                        id="loginBtn">

                        <span class="btn-text">
                            Sign In
                        </span>

                        <!-- <span class="truck-box">
                            <i class="fa fa-truck"></i>
                        </span> -->

                        <!-- <span class="track"></span> -->

                    </button>

                </form>

                <div class="footer">
                    © 2026 Transport ERP System
                </div>

            </div>

        </div>

    </div>

</body>

</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('togglePassword');
        const pwd = document.getElementById('password');
        if (toggle && pwd) {
            toggle.addEventListener('click', function() {
                const icon = toggle.querySelector('i');
                if (pwd.type === 'password') {
                    pwd.type = 'text';
                    if (icon) {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    }
                    toggle.setAttribute('aria-label', 'Hide password');
                } else {
                    pwd.type = 'password';
                    if (icon) {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                    toggle.setAttribute('aria-label', 'Show password');
                }
            });
        }

        // Remember Me — restore saved credentials
        (function() {
            const saved = localStorage.getItem('remember_me');
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    const pwd = document.getElementById('password');
                    const usr = document.getElementById('username');
                    if (data.u && usr) usr.value = data.u;
                    if (data.p && pwd) {
                        pwd.value = atob(data.p);
                        pwd.type = 'password';
                        // reset eye toggle to match hidden state
                        var icon = document.querySelector('#togglePassword i');
                        if (icon) { icon.className = 'fa-solid fa-eye'; }
                        var tb = document.getElementById('togglePassword');
                        if (tb) tb.setAttribute('aria-label', 'Show password');
                    }
                    if (data.r) document.getElementById('remember').checked = true;
                    // re-apply after browser autofill may override
                    setTimeout(function() {
                        if (data.p && pwd) {
                            pwd.value = atob(data.p);
                            pwd.type = 'password';
                            var icon2 = document.querySelector('#togglePassword i');
                            if (icon2) { icon2.className = 'fa-solid fa-eye'; }
                        }
                    }, 150);
                } catch (e) {}
            }
        })();

        // Save credentials on form submit if Remember Me is checked
        document.querySelector('form').addEventListener('submit', function() {
            const cb = document.getElementById('remember');
            const usr = document.getElementById('username');
            const pwd = document.getElementById('password');
            if (cb && cb.checked && usr && pwd) {
                localStorage.setItem('remember_me', JSON.stringify({
                    u: usr.value,
                    p: btoa(pwd.value),
                    r: true
                }));
            } else {
                localStorage.removeItem('remember_me');
            }
        });

        // translations
        const translations = {
            en: {
                title: 'Welcome Back',
                subtitle: 'Sign in to continue',
                usernamePlaceholder: 'Email or Mobile Number',
                passwordPlaceholder: 'Password',
                remember: 'Remember Me',
                forgot: 'Forgot Password?',
                signIn: 'Sign In',
                successFallback: 'Login successful!'
            },
            ta: {
                title: 'மீண்டும் வரவேற்கிறோம்',
                subtitle: 'தொடர உள்நுழைக',
                usernamePlaceholder: 'மின்னஞ்சல் அல்லது தொலைபேசி',
                passwordPlaceholder: 'கடவுச்சொல்',
                remember: 'என்னை நினைவில் வைக்கவும்',
                forgot: 'கடவுச்சொல்லை மறந்துவிட்டீர்களா?',
                signIn: 'உள்நுழைக',
                successFallback: 'உள்நுழைவு வெற்றி'
            }
        };

        function applyLanguage(lang) {
            const t = translations[lang] || translations.en;
            const titleEl = document.querySelector('.title');
            const subtitleEl = document.querySelector('.subtitle');
            const userInput = document.querySelector('input[name="username"]');
            const passInput = document.getElementById('password');
            const rememberLabel = document.querySelector('label[for="remember"]');
            const forgotLink = document.querySelector('.forgot-password');
            const btnText = document.querySelector('.btn-text');
            const successDiv = document.getElementById('success-msg');

            if (titleEl) titleEl.innerText = t.title;
            if (subtitleEl) subtitleEl.innerText = t.subtitle;
            if (userInput) userInput.placeholder = t.usernamePlaceholder;
            if (passInput) passInput.placeholder = t.passwordPlaceholder;
            if (rememberLabel) rememberLabel.innerText = t.remember;
            if (forgotLink) forgotLink.innerText = t.forgot;
            if (btnText && (!successDiv || successDiv.style.display === 'none')) btnText.innerText = t.signIn;
            if (successDiv && successDiv.dataset.serverText === undefined) {
                // store server text if present
                successDiv.dataset.serverText = successDiv.textContent.trim();
            }
        }

        // expose to components
        window.applyLanguage = applyLanguage;

        const langSelect = document.getElementById('langSelect');
        if (langSelect) {
            const saved = localStorage.getItem('site_lang') || 'en';
            langSelect.value = saved;
            applyLanguage(saved);
            document.documentElement.lang = saved === 'ta' ? 'ta' : 'en';

            langSelect.addEventListener('change', function() {
                const v = this.value || 'en';
                localStorage.setItem('site_lang', v);
                document.documentElement.lang = v === 'ta' ? 'ta' : 'en';
                applyLanguage(v);
            });
        } else {
            applyLanguage('en');
        }

        // If a success message exists from server, prefer it when showing success later
        const s = document.getElementById('success-msg');
        if (s && s.dataset.serverText !== undefined && s.dataset.serverText !== '') {
            // noop; server text will be used in the success flow
        }

        function showNetworkToast(message, type = 'offline') {
            const toast = document.getElementById('network-toast');
            if (!toast) return;

            toast.textContent = message;
            toast.classList.remove('offline', 'online');
            toast.classList.add(type);
            toast.classList.add('show');

            requestAnimationFrame(() => {
                toast.style.opacity = '1';
            });

            clearTimeout(window.networkToastTimer);
            window.networkToastTimer = setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    if (toast.style.opacity === '0') {
                        toast.classList.remove('show');
                    }
                }, 250);
            }, 5000);
        }

        function handleConnectionStatus(showStatus = false) {
            if (navigator.onLine) {
                if (window.wasOffline) {
                    showNetworkToast('Internet connection restored', 'online');
                }
                window.wasOffline = false;
            } else {
                showNetworkToast('Internet connection lost', 'offline');
                window.wasOffline = true;
            }
        }

        window.addEventListener('offline', handleConnectionStatus);
        window.addEventListener('online', function() {
            handleConnectionStatus(true);
        });

        // Do NOT show toast on initial page load
    });
</script>