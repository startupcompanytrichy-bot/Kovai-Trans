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
$isExpense = str_starts_with($currentRoute, 'expense') || str_starts_with($currentRoute, 'expense.ledger') || $currentRoute === 'reports.expenses';
$isEmi = str_starts_with($currentRoute, 'emi');
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
            @if($showAll || userCanSeeAnyMenu(['trips', 'expenses']))
            <li class="pcoded-hasmenu-label"><span>Operations</span></li>
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
        </ul>
        @endif

        @if($sidebarMode === 'transport')
        {{-- ── FINANCE ─────────────────────────────────────────────── --}}
        <ul class="pcoded-item pcoded-left-item">
            @if($showAll || userCanSeeAnyMenu(['vehicle_emi', 'reports']))
            <li class="pcoded-hasmenu-label"><span>Finance</span></li>
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
            <li class="pcoded-hasmenu-label"><span>Masters</span></li>
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
                    @if($showAll || userCanSeeMenu('organization'))
                    <li class="{{ $isSettings ? 'active' : '' }}">
                        <a href="{{ route('settings') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext">Default Branch</span>
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
    /* Mode selector tabs */
    .ps-mode-tab { background:rgba(255,255,255,.08); color:rgba(255,255,255,.6); }
    .ps-mode-active { background:#9333ea!important; color:#fff!important; }
    /* Sidebar section labels - dark theme (default) */
    .pcoded-navbar:not([navbar-theme*="themelight"]) .pcoded-hasmenu-label span {
        color: rgba(255, 255, 255, .8);
    }
    /* Sidebar section labels - light theme */
    .pcoded-navbar[navbar-theme*="themelight"] .pcoded-hasmenu-label span {
        color: rgba(0, 0, 0, .7);
    }
    .pcoded-navbar .pcoded-hasmenu-label {
        padding: 14px 20px 4px;
        pointer-events: none;
    }
</style>