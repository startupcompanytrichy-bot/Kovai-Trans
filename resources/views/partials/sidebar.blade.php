@php
$currentRoute = request()->route()?->getName() ?? '';

$isDashboard = $currentRoute === 'dashboard';
$isCompany = str_starts_with($currentRoute, 'company');
$isBranch = str_starts_with($currentRoute, 'branch');
$isOrgMaster = $isCompany || $isBranch;
$isParties = str_starts_with($currentRoute, 'parties');
$isVehicle = str_starts_with($currentRoute, 'vehicle') && !str_starts_with($currentRoute, 'vehicle.emi');
$isSupplier = str_starts_with($currentRoute, 'supplier');
$isTrader = str_starts_with($currentRoute, 'trader');
$isDriver = str_starts_with($currentRoute, 'driver');
$isTrip = str_starts_with($currentRoute, 'trip');
$isDailyCheckIn = str_starts_with($currentRoute, 'daily-check-in');
$isExpense = str_starts_with($currentRoute, 'expense') || str_starts_with($currentRoute, 'expense.ledger') || $currentRoute === 'reports.expenses';
$isEmi = str_starts_with($currentRoute, 'emi');
$isPayroll = str_starts_with($currentRoute, 'payroll');
$isReports = str_starts_with($currentRoute, 'reports');
$isPackingSlip = str_contains($currentRoute, 'packing-slip');
$isInvoice = str_starts_with($currentRoute, 'invoice');
$isUserPermissions = str_starts_with($currentRoute, 'user-permissions');
$isUserPermissionsCreate = $currentRoute === 'user-permissions.create';
$isUserPermissionsIndex = $currentRoute === 'user-permissions.index';
$isUserPermissionsAuthorization = $currentRoute === 'user-permissions.authorization';
$isSettings = $currentRoute === 'settings';
$isSettingsPermissions = str_starts_with($currentRoute, 'settings.permissions');

// Check if user should see all menu items (Super Admin only)
$showAll = showAllMenu();

// Sidebar mode: transport (default) or packing
$sidebarMode = $isPackingSlip ? 'packing' : 'transport';
@endphp

<nav class="pcoded-navbar">
    <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>

    <div class="pcoded-inner-navbar main-menu">

        {{-- ── MODE SELECTOR ─────────────────────────────────────────--}}
        <ul class="pcoded-item pcoded-left-item" style="padding:8px 12px;display:flex;gap:4px;">
            @php $isPackingRoute = request()->routeIs('*packing-slip*'); @endphp
            <li style="flex:1;list-style:none;">
                <a href="{{ route('dashboard') }}"
                   class="ps-mode-tab {{ !$isPackingRoute ? 'ps-mode-active' : '' }}"
                   style="display:block;text-align:center;padding:5px 0;border-radius:8px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;transition:all .15s;">
                    Transport
                </a>
            </li>
            <li style="flex:1;list-style:none;">
                <a href="{{ route('packing-slip.index') }}"
                   class="ps-mode-tab {{ $isPackingRoute ? 'ps-mode-active' : '' }}"
                   style="display:block;text-align:center;padding:5px 0;border-radius:8px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;transition:all .15s;">
                    Packing
                </a>
            </li>
        </ul>

        @if(!$isPackingSlip)
        {{-- Dashboard (Transport mode only) --}}
        <ul class="pcoded-item pcoded-left-item">
            @if($showAll || userCanSeeMenu('dashboard'))
            <li class="{{ $isDashboard ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <span class="pcoded-micon"><i class="ti-home"></i><b>D</b></span>
                    <span class="pcoded-mtext">Dashboard</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            @endif
        </ul>
        @endif

        @if($sidebarMode === 'transport')
        {{-- ── OPERATIONS ─────────────────────────────────────────── --}}
        <ul class="pcoded-item pcoded-left-item">
            @if($showAll || userCanSeeAnyMenu(['trips', 'daily_check_in', 'expenses']))
            <!-- <li class="pcoded-hasmenu-label"><span>Operations</span></li> -->
            @endif

            {{-- Trips --}}
            @if($showAll || userCanSeeMenu('trips'))
            <li class="pcoded-hasmenu {{ $isTrip ? 'pcoded-trigger active' : '' }}">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="ti-location-arrow"></i><b>T</b></span>
                    <span class="pcoded-mtext">Trip Management</span>
                    <span class="pcoded-mcaret"></span>
                </a>
                <ul class="pcoded-submenu">
                    <li class="{{ $currentRoute === 'trip' ? 'active' : '' }}">
                        <a href="{{ route('trip') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext">All Trips</span>
                        </a>
                    </li>
                    <li class="{{ $currentRoute === 'trip.create' ? 'active' : '' }}">
                        <a href="{{ route('trip.create') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext">Add Trip</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- Expenses --}}
            @if($showAll || userCanSeeAnyMenu(['expenses', 'reports']))
            <li class="pcoded-hasmenu {{ $isExpense ? 'pcoded-trigger active' : '' }}">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="ti-receipt"></i><b>E</b></span>
                    <span class="pcoded-mtext">Expenses</span>
                    <span class="pcoded-mcaret"></span>
                </a>
                <ul class="pcoded-submenu">
                    @if($showAll || userCanSeeMenu('expenses'))
                    <li class="{{ $currentRoute === 'expense' ? 'active' : '' }}">
                        <a href="{{ route('expense') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext">All Expenses</span>
                        </a>
                    </li>
                    @endif
                    @if($showAll || userCanSeeMenu('reports'))
                    <li class="{{ in_array($currentRoute, ['expense.ledger.index', 'expense.ledger.category', 'reports.expenses']) ? 'active' : '' }}">
                        <a href="{{ route('expense.ledger.index') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext">Expense Ledger</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- Daily Check In --}}
            @if($showAll || userCanSeeMenu('daily_check_in'))
            <li class="{{ $isDailyCheckIn ? 'active' : '' }}">
                <a href="{{ route('daily-check-in') }}">
                    <span class="pcoded-micon"><i class="ti-clipboard"></i><b>D</b></span>
                    <span class="pcoded-mtext">Daily Check In</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            @endif
        </ul>
        @endif

        @if($sidebarMode === 'transport')
        {{-- ── FINANCE ─────────────────────────────────────────────── --}}
        <ul class="pcoded-item pcoded-left-item">
            @if($showAll || userCanSeeAnyMenu(['vehicle_emi', 'reports']))
            <!-- <li class="pcoded-hasmenu-label"><span>Finance</span></li> -->
            @endif

            {{-- Vehicle EMI --}}
            @if($showAll || userCanSeeMenu('vehicle_emi'))
            <li class="{{ $isEmi ? 'active' : '' }}">
                <a href="{{ route('emi') }}">
                    <span class="pcoded-micon"><i class="ti-calendar"></i><b>M</b></span>
                    <span class="pcoded-mtext">Vehicle EMI</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            @endif

            {{-- Payroll --}}
            @if($showAll || userCanSeeMenu('payroll'))
            <li class="pcoded-hasmenu {{ $isPayroll ? 'pcoded-trigger active' : '' }}">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="ti-money"></i><b>P</b></span>
                    <span class="pcoded-mtext">Payroll</span>
                    <span class="pcoded-mcaret"></span>
                </a>
                <ul class="pcoded-submenu">
                    <li class="{{ $currentRoute === 'payroll' ? 'active' : '' }}">
                        <a href="{{ route('payroll') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext">All Payroll</span>
                        </a>
                    </li>
                    <li class="{{ $currentRoute === 'payroll.create' ? 'active' : '' }}">
                        <a href="{{ route('payroll.create') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext">Add Payroll</span>
                        </a>
                    </li>
                    <li class="{{ $currentRoute === 'payroll.advances' ? 'active' : '' }}">
                        <a href="{{ route('payroll.advances') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext">Salary Advances</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- Reports --}}
            @if($showAll || userCanSeeMenu('reports'))
            <li class="{{ $isReports ? 'active' : '' }}">
                <a href="{{ route('reports') }}">
                    <span class="pcoded-micon"><i class="ti-bar-chart"></i><b>R</b></span>
                    <span class="pcoded-mtext">Reports</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            @endif
        </ul>
        @endif

        @if($sidebarMode === 'packing')
        {{-- Customer --}}
        <ul class="pcoded-item pcoded-left-item">
            <li class="{{ $currentRoute === 'packing-slip.customers' ? 'active' : '' }}">
                <a href="{{ route('packing-slip.customers') }}">
                    <span class="pcoded-micon"><i class="ti-layers"></i><b>C</b></span>
                    <span class="pcoded-mtext">Customer</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
        </ul>

        {{-- Quality --}}
        @php $isQuality = $currentRoute === 'packing-slip.qualities'; @endphp
        <ul class="pcoded-item pcoded-left-item">
            <li class="{{ $isQuality ? 'active' : '' }}">
                <a href="{{ route('packing-slip.qualities') }}">
                    <span class="pcoded-micon"><i class="ti-star"></i><b>Q</b></span>
                    <span class="pcoded-mtext">Quality</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
        </ul>

        {{-- Packing Slip --}}
        <ul class="pcoded-item pcoded-left-item">
            <li class="{{ in_array($currentRoute, ['packing-slip.index', 'packing-slip.create', 'packing-slip.edit', 'packing-slip.show', 'packing-slip.print']) ? 'active' : '' }}">
                <a href="{{ route('packing-slip.index') }}">
                    <span class="pcoded-micon"><i class="ti-layout"></i><b>P</b></span>
                    <span class="pcoded-mtext">Packing Slip</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
        </ul>
        @endif

        @if($sidebarMode === 'transport')
        {{-- ── MASTERS ─────────────────────────────────────────────── --}}
        <ul class="pcoded-item pcoded-left-item">
            @if($showAll || userCanSeeAnyMenu(['parties', 'vehicles', 'drivers', 'suppliers', 'traders', 'organization']))
            <!-- <li class="pcoded-hasmenu-label"><span>Masters</span></li> -->
            @endif

            {{-- Parties --}}
            @if($showAll || userCanSeeMenu('parties'))
            <li class="{{ $isParties ? 'active' : '' }}">
                <a href="{{ route('parties') }}">
                    <span class="pcoded-micon"><i class="ti-layers"></i><b>P</b></span>
                    <span class="pcoded-mtext">Parties</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            @endif

            {{-- Vehicle Management --}}
            @if($showAll || userCanSeeMenu('vehicles'))
            <li class="{{ $isVehicle ? 'active' : '' }}">
                <a href="{{ route('vehicle') }}">
                    <span class="pcoded-micon"><i class="ti-truck"></i><b>V</b></span>
                    <span class="pcoded-mtext">Vehicles</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            @endif

            {{-- Driver --}}
            @if($showAll || userCanSeeMenu('drivers'))
            <li class="{{ $isDriver ? 'active' : '' }}">
                <a href="{{ route('driver') }}">
                    <span class="pcoded-micon"><i class="ti-id-badge"></i><b>D</b></span>
                    <span class="pcoded-mtext">Drivers</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            @endif

            {{-- Supplier --}}
            @if($showAll || userCanSeeMenu('suppliers'))
            <li class="{{ $isSupplier ? 'active' : '' }}">
                <a href="{{ route('supplier') }}">
                    <span class="pcoded-micon"><i class="ti-user"></i><b>S</b></span>
                    <span class="pcoded-mtext">Suppliers</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            @endif

            {{-- Traders --}}
            @if($showAll || userCanSeeMenu('traders'))
            <li class="{{ $isTrader ? 'active' : '' }}">
                <a href="{{ route('trader') }}">
                    <span class="pcoded-micon"><i class="ti-package"></i><b>T</b></span>
                    <span class="pcoded-mtext">Traders</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            @endif

            {{-- Organization Masters --}}
            @if($showAll || userCanSeeMenu('organization'))
            <li class="pcoded-hasmenu {{ $isOrgMaster ? 'pcoded-trigger active' : '' }}">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="ti-layout-grid2-alt"></i></span>
                    <span class="pcoded-mtext">Organization</span>
                    <span class="pcoded-mcaret"></span>
                </a>
                <ul class="pcoded-submenu">
                    @if($showAll || userCanSeeMenu('organization'))
                    <li class="{{ $isCompany ? 'active' : '' }}">
                        <a href="{{ route('company') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext">Company Master</span>
                        </a>
                    </li>
                    @endif
                    @if($showAll || userCanSeeMenu('organization'))
                    <li class="{{ $isBranch ? 'active' : '' }}">
                        <a href="{{ route('branch') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext">Branch Master</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
        </ul>
        @endif

        {{-- ── ADMIN ─────────────────────────────────────────────── --}}
        @if($showAll && !$isPackingSlip)
        <ul class="pcoded-item pcoded-left-item">
            <li class="pcoded-hasmenu-label"><span>Admin</span></li>
            <li class="pcoded-hasmenu {{ ($isUserPermissionsIndex || $isUserPermissionsCreate) ? 'pcoded-trigger active' : '' }}">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="ti-lock"></i><b>A</b></span>
                    <span class="pcoded-mtext">Users</span>
                    <span class="pcoded-mcaret"></span>
                </a>
                <ul class="pcoded-submenu">
                    <li class="{{ $isUserPermissionsIndex ? 'active' : '' }}">
                        <a href="{{ route('user-permissions.index') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext">User List</span>
                        </a>
                    </li>
                    <li class="{{ $isUserPermissionsCreate ? 'active' : '' }}">
                        <a href="{{ route('user-permissions.create') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext">Create User</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="{{ $isUserPermissionsAuthorization ? 'active' : '' }}">
                <a href="{{ route('user-permissions.authorization') }}">
                    <span class="pcoded-micon"><i class="ti-lock"></i><b>P</b></span>
                    <span class="pcoded-mtext">Screen Permissions</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>

            {{-- Settings --}}
            <li class="{{ $isSettings ? 'active' : '' }}">
                <a href="{{ route('settings') }}">
                    <span class="pcoded-micon"><i class="ti-settings"></i><b>S</b></span>
                    <span class="pcoded-mtext">Settings</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
        </ul>
        @endif

    </div>
</nav>

<style>
    .ps-mode-tab { background:rgba(255,255,255,.08); color:rgba(255,255,255,.6); }
    .ps-mode-active { background:#9333ea!important; color:#fff!important; }

    @media (max-width: 575px) {
        .ps-mode-tab {
            font-size: 10px !important;
            padding: 4px 0 !important;
        }
    }

    @media (max-width: 992px) {
        .pcoded-overlay-box {
            display: block !important;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity .25s ease, visibility .25s ease;
        }
        .pcoded-overlay-box.overlay-on {
            opacity: 1 !important;
            visibility: visible !important;
            z-index: 1040 !important;
        }
        .pcoded-navbar {
            position: fixed !important;
            top: 0;
            left: 0 !important;
            width: 270px !important;
            height: 100vh !important;
            margin-left: 0 !important;
            z-index: 1050 !important;
            transform: translate3d(-100%,0,0);
            transition: transform .25s ease;
            will-change: transform;
        }
        .pcoded-navbar.sidebar-on {
            transform: translate3d(0,0,0) !important;
            margin-left: 0 !important;
        }
        .pcoded-main-container {
            margin-left: 0 !important;
        }
        #mobile-collapse {
            display: inline-flex !important;
        }
    }

    @media (min-width: 993px) {
        #mobile-collapse {
            display: none !important;
        }
    }
</style>

<script>
(function() {
    'use strict';
    var initialized = false;

    function initSidebar() {
        if (initialized) return;
        initialized = true;

        var toggle = document.getElementById('mobile-collapse');
        var sidebar = document.querySelector('.pcoded-navbar');
        var overlay = document.querySelector('.pcoded-overlay-box');
        if (!toggle || !sidebar) return;

        function open() {
            sidebar.classList.add('sidebar-on');
            if (overlay) overlay.classList.add('overlay-on');
        }

        function close() {
            sidebar.classList.remove('sidebar-on');
            if (overlay) overlay.classList.remove('overlay-on');
        }

        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (sidebar.classList.contains('sidebar-on')) {
                close();
            } else {
                open();
            }
        });

        if (overlay) {
            overlay.addEventListener('click', close);
        }

        var closeBtn = sidebar.querySelector('.sidebar_toggle a');
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                close();
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebar);
    } else {
        initSidebar();
    }
})();
</script>