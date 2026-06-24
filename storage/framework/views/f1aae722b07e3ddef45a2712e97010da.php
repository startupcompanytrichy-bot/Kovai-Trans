<?php
$activeFY = \App\Models\FinancialYear::current();
$currentRoute = request()->route()?->getName() ?? '';
?>

<nav class="navbar header-navbar pcoded-header">
    <div class="navbar-wrapper">

        <div class="navbar-logo">
            <a class="mobile-menu" id="mobile-collapse" href="#!">
                <i class="ti-menu"></i>
            </a>
            <a class="mobile-search morphsearch-search" href="#">
                <i class="ti-search"></i>
            </a>
            <a href="index.html">
                <img class="img-fluid" src="<?php echo e(asset('assets/images/Original-Logo.png')); ?>?v=<?php echo e(file_exists(public_path('assets/images/T-Groups-Logo.png')) ? filemtime(public_path('assets/images/T-Groups-Logo.png')) : time()); ?>" alt="Theme-Logo" />
            </a>
            <a class="mobile-options">
                <i class="ti-more"></i>
            </a>
        </div>

        <div class="navbar-container container-fluid">

            
            <ul class="nav-left">
                <li>
                    <div class="sidebar_toggle">
                        <a href="javascript:void(0)"><i class="ti-menu"></i></a>
                    </div>
                </li>
                <li>
                    <a href="#!" onclick="javascript:toggleFullScreen()">
                        <i class="ti-fullscreen"></i>
                    </a>
                </li>
            </ul>

            
            <ul class="nav-right">

                
                <li class="fy-li">
                    <div style="
        display:flex;
        align-items:center;
        gap:10px;
        padding:6px 12px;
        border-radius:30px;
        background:#fff;
        border:1px solid #e5e7eb;
        box-shadow:0 2px 6px rgba(0,0,0,0.06);
        cursor:default;
    ">

                        <span style="
            width:28px;
            height:28px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            background:<?php echo e($activeFY ? '#e8f5e9' : '#fff3e0'); ?>;
            color:<?php echo e($activeFY ? '#16a34a' : '#f59e0b'); ?>;
            font-size:13px;
        ">
                            <i class="ti-calendar"></i>
                        </span>

                        <div style="line-height:1.1;">
                            <div style="font-size:10px;color:#6b7280;">
                                Financial Year
                            </div>
                            <div style="font-size:13px;font-weight:700;color:#111827;">
                                <?php echo e($activeFY ? $activeFY->label : 'Not Set'); ?>

                            </div>
                        </div>

                    </div>
                </li>

                <li class="header-notification">
                    <a href="<?php echo e(route('logout')); ?>"
                        title="Logout"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="ti-power-off"></i>
                    </a>

                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                        <?php echo csrf_field(); ?>
                    </form>
                </li>

                
                <li class="user-profile header-notification">
                    <a href="#!">
                        <span class="img-radius" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50px!important;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;font-size:14px;font-weight:700;text-transform:uppercase;"><?php echo e(strtoupper(substr(session('user_email') ?? 'U', 0, 1))); ?></span>
                        <span><?php echo e(ucfirst(session('role')) ?? 'User'); ?></span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<style>
    /* ── Override theme float with flex so all items sit on one line ── */
    .header-navbar .navbar-wrapper .navbar-container .nav-right {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        flex-wrap: nowrap !important;
        float: right;
        height: 70px;
        /* match theme nav height */
    }

    /* reset li so flex takes over */
    .header-navbar .navbar-wrapper .navbar-container .nav-right li {
        float: none !important;
        line-height: normal !important;
        padding: 0 6px;
        display: flex !important;
        align-items: center !important;
    }

    /* ── FY chip ──────────────────────────────────────────────────────── */
    .fy-li {
        padding: 0 4px !important;
    }

    .fy-chip {
        display: inline-flex !important;
        align-items: center;
        gap: 8px;
        height: 36px;
        padding: 0 12px 0 6px;
        background: #f8faff;
        border: 1.5px solid #e0e7ff;
        border-radius: 10px;
        text-decoration: none !important;
        transition: border-color .18s, box-shadow .18s, background .18s;
        white-space: nowrap;
        cursor: pointer;
    }

    .fy-chip:hover {
        background: #eef2ff;
        border-color: #667eea;
        box-shadow: 0 2px 12px rgba(102, 126, 234, .18);
        text-decoration: none !important;
    }

    /* icon bubble */
    .fy-chip-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
        transition: background .18s;
    }

    .fy-chip-icon.is-set {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
    }

    .fy-chip-icon.is-unset {
        background: #f1f5f9;
        color: #94a3b8;
    }

    /* text */
    .fy-chip-body {
        display: flex;
        flex-direction: column;
        gap: 1px;
        line-height: 1;
    }

    .fy-chip-sub {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: #94a3b8;
    }

    .fy-chip-val {
        font-size: 13px;
        font-weight: 800;
        color: #1e293b;
    }

    /* ── Mobile logo & nav-right toggle ────────────────────────────────── */
    @media (max-width: 575.98px) {
        .navbar-logo {
            display: flex !important;
            align-items: center !important;
            gap: 6px;
            padding: 0 10px !important;
        }

        .navbar-logo a {
            display: inline-flex !important;
            align-items: center !important;
            line-height: 1;
        }

        .navbar-logo a img {
            max-height: 32px !important;
            width: auto !important;
            object-fit: contain;
        }

        .mobile-menu,
        .mobile-search,
        .mobile-options {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .header-navbar .navbar-wrapper .navbar-container .nav-right {
            display: none !important;
            position: absolute;
            top: 100%;
            right: 0;
            flex-direction: column !important;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            padding: 8px;
            width: auto;
            min-width: 200px;
            z-index: 1000;
            height: auto !important;
        }

        .header-navbar .navbar-wrapper .navbar-container .nav-right.open {
            display: flex !important;
        }

        .header-navbar .navbar-wrapper .navbar-container .nav-right li {
            width: 100%;
            padding: 8px 12px;
            justify-content: flex-start !important;
        }

        .header-navbar .navbar-wrapper .navbar-container {
            position: relative;
        }
    }

    /* mobile: collapse fy chip to icon only */
    @media (max-width: 575.98px) {
        .fy-chip-body {
            display: none !important;
        }

        .fy-chip {
            padding: 0;
            width: 36px;
            height: 36px;
            justify-content: center;
            background: #eef2ff;
            border-color: #e0e7ff;
            border-radius: 50%;
        }

        .fy-chip-icon {
            width: 28px;
            height: 28px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggle = document.querySelector('.mobile-options');
        var menu = document.querySelector('.header-navbar .navbar-wrapper .navbar-container .nav-right');
        if (toggle && menu) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                menu.classList.toggle('open');
            });
            document.addEventListener('click', function(e) {
                if (!toggle.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.remove('open');
                }
            });
        }
    });
</script><?php /**PATH D:\laragon\www\Kovai-Trans\resources\views/partials/header.blade.php ENDPATH**/ ?>