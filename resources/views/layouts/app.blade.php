<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title', config('app.name')) — {{ config('app.name') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="CodedThemes">
    <meta name="keywords" content=" Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="CodedThemes">
    @php $__sidebarCompany = \App\Models\Company::whereNotNull('sidebar_logo')->orderBy('id')->first(); @endphp
    <meta name="sidebar-logo-url" content="{{ $__sidebarCompany ? asset('storage/' . $__sidebarCompany->sidebar_logo) : asset('assets/images/Original-Logo.png') }}">
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/icofont/css/icofont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/jquery.mCustomScrollbar.css') }}">
    {{-- Select2 --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/select2/select2.min.css') }}">
    {{-- DataTables --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatable/dataTables.bootstrap4.min.css') }}">
    {{-- Toastr --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/toastr/toastr.min.css') }}">
    {{-- Select2 theme overrides --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/select2-theme.css') }}?v={{ filemtime(public_path('assets/css/select2-theme.css')) }}">
    {{-- App custom styles --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/app-custom.css') }}?v={{ filemtime(public_path('assets/css/app-custom.css')) }}">

    @stack('styles')
    <style>
        body, .pcoded-main-container, .pcoded-content, .pcoded-inner-content, .main-body, .page-wrapper, .page-body { background: #e8e8e8 !important; }
        /* ── Remove tick/checkmark on selected option in Select2 dropdown ── */
    .select2-results__option[aria-selected="true"]::before,
    .select2-results__option--selected::before {
        display: none !important;
    }
    .select2-results__option[aria-selected="true"] {
        background-color: #f0f4ff !important;
        color: #1a2340 !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #667eea !important;
        color: #fff !important;
    }
    .select2-results__option--highlighted[aria-selected] span,
    .select2-results__option--highlighted[aria-selected] div {
        color: #fff !important;
    }
    </style>
</head>

<body>
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->

    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            {{-- header partial --}}
            @include('partials.header')

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">

                    {{-- sidebar partial --}}
                    @include('partials.sidebar')

                    <div class="pcoded-content" id="pcoded-content" style="position:relative;">
                        <div id="pcoded-content-inner">
                            @yield('content')
                        </div>
                    </div>

                </div>
            </div>

            {{-- footer (scripts + fixed button) --}}
            @include('partials.footer')

            {{-- Global Delete Confirmation Modal --}}
            @include('partials.delete-modal')

            {{-- WhatsApp Disconnect Modal --}}
            <div class="modal fade" id="waDisconnectModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:400px;">
                    <div class="modal-content" style="border:none;border-radius:14px;box-shadow:0 20px 60px rgba(0,0,0,0.15);overflow:hidden;">
                        <div style="height:4px;background:linear-gradient(90deg,#f59e0b,#f97316,#ef4444);"></div>
                        <div style="padding:28px 24px 20px;text-align:center;">
                            <div class="wa-modal-icon" style="width:64px;height:64px;border-radius:50%;background:#fffbeb;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                                <i class="ti-plug" style="font-size:28px;color:#f59e0b;"></i>
                            </div>
                            <h6 class="wa-modal-title" style="font-size:16px;font-weight:700;color:#0f172a;margin:0 0 6px;">WhatsApp Disconnected</h6>
                            <p class="wa-modal-desc" style="font-size:13px;color:#64748b;margin:0 0 6px;line-height:1.6;">
                                Please connect WhatsApp to continue<br>receiving automated messages.
                            </p>
                            <div style="margin:16px 0 4px;display:flex;gap:8px;justify-content:center;">
                                <a href="{{ url('/settings#tab-whatsapp') }}" class="btn" style="background:linear-gradient(135deg,#f59e0b,#f97316);color:#fff;border:none;padding:8px 20px;font-size:12px;font-weight:600;border-radius:8px;cursor:pointer;text-decoration:none;">
                                    <i class="ti-settings" style="margin-right:4px;"></i> Settings
                                </a>
                                <button type="button" onclick="$('#waDisconnectModal').modal('hide')" style="background:#f1f5f9;border:1px solid #e2e8f0;padding:8px 20px;font-size:12px;font-weight:600;border-radius:8px;cursor:pointer;color:#475569;">Later</button>
                                <button type="button" onclick="waSkipDisconnect()" style="background:#fff;border:1px solid #e2e8f0;padding:8px 20px;font-size:12px;font-weight:600;border-radius:8px;cursor:pointer;color:#94a3b8;">Skip</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Page-level scripts pushed via @push('scripts') --}}
    <div id="softnav-stack">
    @stack('scripts')
    </div>

    <script>
    /* ═══════════════════════════════════════════════════════════════════
       SPA NAVIGATION — softNav system
       - Intercepts sidebar links, pagination, and form submissions
       - Swaps only #pcoded-content-inner (header/sidebar stay intact)
       - Shows skeleton loader during fetch
       - Syncs sidebar active state after each navigation
       - Handles browser back/forward via popstate
    ═══════════════════════════════════════════════════════════════════ */

    /* ── Skeleton HTML injected during load ────────────────────────── */
    var SKEL_ROW = '<div style="height:44px;margin-top:1px;background:linear-gradient(90deg,#f8fafc 25%,#f0f2f7 50%,#f8fafc 75%);background-size:200% 100%;animation:skel-shimmer 1.2s infinite;"></div>';
    var SKEL_COL = '<div style="height:70px;border-radius:10px;background:linear-gradient(90deg,#f0f2f7 25%,#e4e8f0 50%,#f0f2f7 75%);background-size:200% 100%;animation:skel-shimmer 1.2s infinite;"></div>';
    var SKELETON_HTML =
        '<div class="pcoded-inner-content" style="padding:20px;">' +
            '<div style="height:80px;border-radius:12px;background:linear-gradient(90deg,#f0f2f7 25%,#e4e8f0 50%,#f0f2f7 75%);background-size:200% 100%;animation:skel-shimmer 1.2s infinite;margin-bottom:16px;"></div>' +
            '<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px;">' +
                SKEL_COL + SKEL_COL + SKEL_COL + SKEL_COL +
            '</div>' +
            '<div style="height:52px;border-radius:10px;background:linear-gradient(90deg,#f0f2f7 25%,#e4e8f0 50%,#f0f2f7 75%);background-size:200% 100%;animation:skel-shimmer 1.2s infinite;margin-bottom:16px;"></div>' +
            '<div style="border-radius:10px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.06);">' +
                '<div style="height:48px;background:linear-gradient(90deg,#f0f2f7 25%,#e4e8f0 50%,#f0f2f7 75%);background-size:200% 100%;animation:skel-shimmer 1.2s infinite;"></div>' +
                SKEL_ROW + SKEL_ROW + SKEL_ROW + SKEL_ROW + SKEL_ROW + SKEL_ROW +
            '</div>' +
        '</div>';

    /* Inject skeleton keyframe once */
    (function() {
        if (document.getElementById('skel-style')) return;
        var s = document.createElement('style');
        s.id = 'skel-style';
        s.textContent = '@@keyframes skel-shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}';
        document.head.appendChild(s);
    })();

    function showSkeleton() {
        var inner = document.getElementById('pcoded-content-inner');
        if (inner) inner.innerHTML = SKELETON_HTML;
    }

    function reExecScripts(container) {
        container.querySelectorAll('script').forEach(function(old) {
            var s = document.createElement('script');
            if (old.src) { s.src = old.src; s.async = false; }
            else { s.textContent = old.textContent; }
            old.parentNode.replaceChild(s, old);
        });
        container.querySelectorAll('style').forEach(function(old) {
            var s = document.createElement('style');
            s.textContent = old.textContent;
            old.parentNode.replaceChild(s, old);
        });
    }

    /* Remove page-specific styles pushed into <head> by previous page */
    function cleanupPageStyles() {
        document.querySelectorAll('head style[data-page-style]').forEach(function(s) {
            s.remove();
        });
    }

    /* Move inline <style> tags from content area to <head> with a marker */
    function hoistContentStyles(container) {
        container.querySelectorAll('style').forEach(function(s) {
            var h = document.createElement('style');
            h.setAttribute('data-page-style', '1');
            h.textContent = s.textContent;
            document.head.appendChild(h);
            s.remove();
        });
    }

    /* ── Update sidebar active state without reloading sidebar ─────── */
    function syncSidebarActive(url) {
        var path = url.replace(window.location.origin, '').split('?')[0];
        // Remove all current active classes
        document.querySelectorAll('.pcoded-navbar .pcoded-item li').forEach(function(li) {
            li.classList.remove('active');
        });
        document.querySelectorAll('.pcoded-navbar .pcoded-hasmenu').forEach(function(li) {
            li.classList.remove('pcoded-trigger');
        });
        // Find matching link and set active
        document.querySelectorAll('.pcoded-navbar a[href]').forEach(function(a) {
            var href = a.getAttribute('href');
            if (!href || href === '#' || href === 'javascript:void(0)') return;
            var aPath = href.replace(window.location.origin, '').split('?')[0];
            if (aPath === path || (aPath !== '/' && path.startsWith(aPath))) {
                var li = a.closest('li');
                if (li) {
                    li.classList.add('active');
                    // Open parent submenu if nested
                    var parent = li.closest('.pcoded-submenu');
                    if (parent) {
                        var parentLi = parent.closest('.pcoded-hasmenu');
                        if (parentLi) {
                            parentLi.classList.add('pcoded-trigger', 'active');
                            parent.style.display = 'block';
                        }
                    }
                }
            }
        });
    }

    /* ── Core softNav ──────────────────────────────────────────────── */
    window.softNav = function(url, fromPopState) {
        // Show skeleton immediately
        showSkeleton();
        window.scrollTo(0, 0);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) {
                // If server redirected (e.g. after form POST → GET), follow it
                if (r.redirected) {
                    return fetch(r.url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function(r2) { return r2.text().then(function(t) { return {html: t, finalUrl: r2.url}; }); });
                }
                return r.text().then(function(t) { return {html: t, finalUrl: r.url || url}; });
            })
            .then(function(res) {
                var html = res.html;
                var finalUrl = res.finalUrl || url;
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var newInner = doc.getElementById('pcoded-content-inner');
                var curInner = document.getElementById('pcoded-content-inner');

                if (newInner && curInner) {
                    cleanupPageStyles();
                    curInner.innerHTML = newInner.innerHTML;
                    if (!fromPopState) {
                        history.pushState({sn: true, url: finalUrl}, '', finalUrl);
                    }
                    document.title = doc.title;
                    // Re-exec content scripts
                    try { reExecScripts(curInner); } catch(e) {}
                    // Hoist page-specific styles to <head>
                    try { hoistContentStyles(curInner); } catch(e) {}
                    // Re-exec @push('scripts') stack
                    var newStack = doc.getElementById('softnav-stack');
                    var curStack = document.getElementById('softnav-stack');
                    if (newStack && curStack) {
                        curStack.innerHTML = newStack.innerHTML;
                        try { reExecScripts(curStack); } catch(e) {}
                    }
                    // Sync sidebar active state
                    syncSidebarActive(finalUrl);
                    // Update CSRF token if refreshed
                    var newCsrf = doc.querySelector('meta[name="csrf-token"]');
                    var curCsrf = document.querySelector('meta[name="csrf-token"]');
                    if (newCsrf && curCsrf) curCsrf.setAttribute('content', newCsrf.getAttribute('content'));
                    // Update sidebar logo image if the new content provides one
                    var logoMeta = (newInner || doc).querySelector('meta[name="sidebar-logo-url"]');
                    if (logoMeta) {
                        var logoImg = document.getElementById('sidebar-logo-img');
                        if (logoImg) logoImg.src = logoMeta.getAttribute('content') + '?v=' + Date.now();
                    }
                } else {
                    window.location.href = url;
                }
            })
            .catch(function() {
                window.location.href = url;
            });
    };

    /* ── softNavForm: submit a form via fetch, then softNav to redirect ── */
    window.softNavForm = function(form) {
        var method = (form.getAttribute('method') || 'POST').toUpperCase();
        var action = form.getAttribute('action') || window.location.href;
        var formData = new FormData(form);

        // Show skeleton while waiting
        showSkeleton();
        window.scrollTo(0, 0);

        fetch(action, {
            method: method,
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            redirect: 'follow'
        })
        .then(function(r) {
            // Laravel redirects after POST — fetch follows them automatically
            return r.text().then(function(t) { return { html: t, finalUrl: r.url }; });
        })
        .then(function(res) {
            var html = res.html;
            var finalUrl = res.finalUrl || action;
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newInner = doc.getElementById('pcoded-content-inner');
            var curInner = document.getElementById('pcoded-content-inner');

            if (newInner && curInner) {
                cleanupPageStyles();
                curInner.innerHTML = newInner.innerHTML;
                history.pushState({sn: true, url: finalUrl}, '', finalUrl);
                document.title = doc.title;
                try { reExecScripts(curInner); } catch(e) {}
                try { hoistContentStyles(curInner); } catch(e) {}
                var newStack = doc.getElementById('softnav-stack');
                var curStack = document.getElementById('softnav-stack');
                if (newStack && curStack) {
                    curStack.innerHTML = newStack.innerHTML;
                    try { reExecScripts(curStack); } catch(e) {}
                }
                syncSidebarActive(finalUrl);
                var newCsrf = doc.querySelector('meta[name="csrf-token"]');
                var curCsrf = document.querySelector('meta[name="csrf-token"]');
                if (newCsrf && curCsrf) curCsrf.setAttribute('content', newCsrf.getAttribute('content'));
            } else {
                window.location.href = finalUrl;
            }
        })
        .catch(function() {
            // On network error fall back to normal submit
            form.submit();
        });
    };

    /* ── Back/Forward navigation ────────────────────────────────────── */
    window.addEventListener('popstate', function() {
        softNav(window.location.href, true);
    });

    /* ── Wire up sidebar links once DOM is ready ────────────────────── */
    document.addEventListener('DOMContentLoaded', function() {

        /* Sidebar nav links */
        document.querySelectorAll('.pcoded-navbar a[href]').forEach(function(a) {
            var href = a.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript')) return;
            a.addEventListener('click', function(e) {
                e.preventDefault();
                softNav(a.href);
            });
        });

        /* Keep submenu open for active parent */
        document.querySelectorAll('.pcoded-hasmenu.pcoded-trigger').forEach(function(li) {
            var sub = li.querySelector('.pcoded-submenu');
            if (sub) sub.style.display = 'block';
        });

        if (typeof initSelect2Events === 'function') initSelect2Events();
    });

    /* ── Global: intercept ALL internal <a> clicks in content area ──── */
    document.addEventListener('click', function(e) {
        var a = e.target.closest('a[href]');
        if (!a) return;
        var href = a.getAttribute('href');
        if (!href || href === '#' || href.startsWith('javascript') || href.startsWith('mailto') || href.startsWith('tel')) return;
        // Skip links that open new tabs, download, or are outside the app
        if (a.target === '_blank' || a.hasAttribute('download')) return;
        // Skip external URLs
        try {
            var url = new URL(href, window.location.href);
            if (url.origin !== window.location.origin) return;
        } catch(e) { return; }
        // Skip print URLs
        if (href.includes('/print') || href.includes('/pdf') || href.includes('/download')) return;
        // Only intercept links inside the content area or sidebar
        var inContent = a.closest('#pcoded-content-inner');
        var inSidebar = a.closest('.pcoded-navbar');
        if (!inContent && !inSidebar) return;

        e.preventDefault();
        var url = new URL(href, window.location.href);
        softNav(url.pathname + url.search);
    });

    /* ── Global: intercept form submissions inside content area ─────── */
    document.addEventListener('submit', function(e) {
        var form = e.target;
        if (!form.closest('#pcoded-content-inner')) return;
        // Skip forms with data-no-softNav, multipart/file-only, or target=_blank
        if (form.hasAttribute('data-no-softnav')) return;
        if (form.target === '_blank') return;
        // Skip delete forms (they use their own modal confirm flow)
        if (form.id && form.id.startsWith('delete')) return;
        // Skip logout form
        if (form.id === 'logout-form') return;
        e.preventDefault();
        softNavForm(form);
    });

    /* ── Pagination softNav ─────────────────────────────────────────── */
    document.addEventListener('click', function(e) {
        var a = e.target.closest('.pagination a');
        if (!a) return;
        e.preventDefault();
        var href = a.getAttribute('href');
        if (!href) return;
        var url = new URL(href, window.location.href);
        softNav(url.pathname + url.search);
    });

    /* ═══════════════════════════════════════════════════════════════════
       WhatsApp Disconnect Global Watcher
       - Polls Baileys status every 15s
       - Shows a modal popup when disconnected
       - Auto-dismisses when reconnected
       - "Skip" → localStorage flag, won't show again until next login
       - "Later" → dismiss now, reappears on next poll
       - Hides when on Settings WhatsApp tab (already there)
    ═══════════════════════════════════════════════════════════════════ */
    // Clear WhatsApp skip flag on fresh login (dashboard is first page after login)
    if (window.location.pathname.indexOf('/dashboard') !== -1) {
        localStorage.removeItem('wa_skip');
    }

    function waSkipDisconnect() {
        localStorage.setItem('wa_skip', '1');
        try { $('#waDisconnectModal').modal('hide'); } catch(e) {}
    }

    function waUpdateModalMsg(connected, reachable) {
        var icon = document.querySelector('#waDisconnectModal .wa-modal-icon');
        var title = document.querySelector('#waDisconnectModal .wa-modal-title');
        var desc = document.querySelector('#waDisconnectModal .wa-modal-desc');
        if (!icon || !title || !desc) return;
        if (reachable === false) {
            icon.style.color = '#ef4444';
            icon.style.background = '#fef2f2';
            title.textContent = 'WhatsApp Service Unreachable';
            desc.innerHTML = 'Baileys service is not running.<br>Contact your system administrator.';
        } else if (connected) {
            icon.style.color = '#38a169';
            icon.style.background = '#f0fff4';
            title.textContent = 'WhatsApp Connected';
            desc.innerHTML = 'WhatsApp is connected and running.';
        } else {
            icon.style.color = '#f59e0b';
            icon.style.background = '#fffbeb';
            title.textContent = 'WhatsApp Disconnected';
            desc.innerHTML = 'Please connect WhatsApp to continue<br>receiving automated messages.';
        }
    }

    (function() {
        var wasConnected = true;
        var modalId = 'waDisconnectModal';
        var waCheckUrl = '{{ route("settings.whatsapp.qr") }}';

        function onSettingsPage() {
            var h = window.location.hash;
            var p = window.location.pathname;
            return p.indexOf('/settings') !== -1 && (!h || h.indexOf('tab-whatsapp') !== -1);
        }

        function checkWaStatus() {
            // Skip if user chose "Skip" — persists until next login
            if (localStorage.getItem('wa_skip')) return;
            // Skip if already on Settings WhatsApp tab
            if (onSettingsPage()) {
                wasConnected = true;
                try { $('#' + modalId).modal('hide'); } catch(e) {}
                return;
            }

            fetch(waCheckUrl)
                .then(function(r) {
                    if (!r.ok) throw new Error('Unreachable');
                    return r.json();
                })
                .then(function(data) {
                    if (data.connected) {
                        if (!wasConnected) {
                            wasConnected = true;
                            waUpdateModalMsg(true, true);
                            try { $('#' + modalId).modal('hide'); } catch(e) {}
                        }
                    } else {
                        waUpdateModalMsg(false, true);
                        if (wasConnected) {
                            wasConnected = false;
                            if (!localStorage.getItem('wa_skip')) {
                                try { $('#' + modalId).modal('show'); } catch(e) {}
                            }
                        }
                    }
                })
                .catch(function() {
                    // Baileys service unreachable
                    waUpdateModalMsg(false, false);
                    if (wasConnected !== false) {
                        wasConnected = false;
                        if (!localStorage.getItem('wa_skip')) {
                            try { $('#' + modalId).modal('show'); } catch(e) {}
                        }
                    }
                });
        }

        // Check once after page load, then every 15s
        setTimeout(checkWaStatus, 2000);
        setInterval(checkWaStatus, 15000);
    })();
    </script>

</body>

</html>