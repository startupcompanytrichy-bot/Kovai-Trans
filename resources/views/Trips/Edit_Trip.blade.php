@extends('layouts.app')

@section('content')

@php
    $statusList = [
        'planned'   => ['label' => 'Planned',   'color' => '#667eea', 'bg' => '#eef2ff', 'icon' => 'ti-clipboard'],
        'running'   => ['label' => 'Running',   'color' => '#f6ad55', 'bg' => '#fffbeb', 'icon' => 'ti-control-forward'],
        'completed' => ['label' => 'Completed', 'color' => '#48bb78', 'bg' => '#f0fff4', 'icon' => 'ti-check-box'],
        'cancelled' => ['label' => 'Cancelled', 'color' => '#fc8181', 'bg' => '#fff5f5', 'icon' => 'ti-close'],
    ];
    $currentStatus = $trip->status ?? 'planned';
    $statusOrder   = ['planned' => 0, 'running' => 1, 'completed' => 2, 'cancelled' => 99];
    $currentOrder  = $statusOrder[$currentStatus] ?? 0;

    // ── Fully dynamic timeline events ──────────────────────────────
    // Each event: [key, label, icon, done (bool), date (Carbon|null), meta (string)]
    $tlEvents = [];

    // 1. Trip Created — always done
    $tlEvents[] = [
        'key'   => 'created',
        'label' => 'Trip Created',
        'icon'  => 'ti-clipboard',
        'done'  => true,
        'date'  => $trip->created_at,
        'meta'  => 'Trip No: ' . $trip->trip_no,
    ];

    // 2. Driver Assigned — done when driver_id is set
    $driverAssigned = !empty($trip->driver_id);
    $tlEvents[] = [
        'key'   => 'driver',
        'label' => 'Driver Assigned',
        'icon'  => 'ti-user',
        'done'  => $driverAssigned,
        'date'  => $driverAssigned ? $trip->updated_at : null,
        'meta'  => $driverAssigned ? ('Driver: ' . ($trip->driver->name ?? 'N/A')) : 'No driver assigned yet',
    ];

    // 3. Loading / Vehicle Started — done when loading_date set or status >= running
    $loadingDone = !empty($trip->loading_date) || $currentOrder >= 1;
    $tlEvents[] = [
        'key'   => 'loading',
        'label' => 'Loading Started',
        'icon'  => 'ti-package',
        'done'  => $loadingDone,
        'date'  => $trip->loading_date ?? ($currentOrder >= 1 ? $trip->updated_at : null),
        'meta'  => $trip->loading_date ? 'Loading date recorded' : ($currentOrder >= 1 ? 'Started (no date recorded)' : 'Pending'),
    ];

    // 4. In Transit — done when status = running or completed
    $inTransitDone = $currentOrder >= 1;
    $tlEvents[] = [
        'key'   => 'transit',
        'label' => 'In Transit',
        'icon'  => 'ti-location-arrow',
        'done'  => $inTransitDone,
        'date'  => $inTransitDone ? ($trip->loading_date ?? $trip->updated_at) : null,
        'meta'  => $trip->from_location . ' → ' . $trip->to_location
                   . ($trip->distance_km ? ' (' . number_format($trip->distance_km, 0) . ' KM)' : ''),
    ];

    // 5. Delivered / Unloading — done when unloading_date set or completed
    $deliveredDone = !empty($trip->unloading_date) || $currentOrder >= 2;
    $tlEvents[] = [
        'key'   => 'delivered',
        'label' => 'Delivered',
        'icon'  => 'ti-check',
        'done'  => $deliveredDone,
        'date'  => $trip->unloading_date ?? ($currentOrder >= 2 ? $trip->updated_at : null),
        'meta'  => $trip->unloading_date ? 'Delivery confirmed' : ($currentOrder >= 2 ? 'Completed (no date recorded)' : 'Pending'),
    ];

    // 6. Trip Closed — done when status = completed
    $closedDone = $currentStatus === 'completed';
    $tlEvents[] = [
        'key'   => 'closed',
        'label' => 'Trip Closed',
        'icon'  => 'ti-lock',
        'done'  => $closedDone,
        'date'  => $closedDone ? $trip->updated_at : null,
        'meta'  => $closedDone ? 'Trip successfully completed' : 'Pending closure',
    ];

    // Cancelled replaces everything after creation if status=cancelled
    $isCancelled = $currentStatus === 'cancelled';

    // Active step = first NOT-done step (what's currently in progress/next)
    // For cancelled trips, active key is 'cancelled'
    $activeKey = null;
    foreach ($tlEvents as $ev) {
        if (!$ev['done']) {
            $activeKey = $ev['key'];
            break;
        }
    }
    // If all steps are done, last step is active
    if ($activeKey === null) {
        $activeKey = end($tlEvents)['key'];
    }
    if ($isCancelled) $activeKey = 'cancelled';

    // Financial calculations
    $freight   = (float) ($trip->freight_amount   ?? 0);
    $advance   = (float) ($trip->advance_amount   ?? 0);
    $balance   = (float) ($trip->balance_amount   ?? 0);
    // Always derive collected from the payments table sum for accuracy
    $collected = $trip->payments ? (float) $trip->payments->sum('amount') : (float) ($trip->collected_amount ?? 0);
    $driverPay = (float) ($trip->driver_bata      ?? 0);
    $fuel      = (float) ($trip->diesel_advance   ?? 0);
    $toll      = (float) ($trip->toll_charges     ?? 0);
    $loading   = (float) ($trip->loading_charges  ?? 0);
    $unloading = (float) ($trip->unloading_charges ?? 0);
    $other     = (float) ($trip->other_expenses   ?? 0);
    $totalExp  = $driverPay + $fuel + $toll + $loading + $unloading + $other;
    $profit    = $freight - $totalExp;
    $outstanding = $freight - $collected;
    $isProfit  = $profit >= 0;
    $barPct    = $freight > 0 ? min(100, round(($totalExp / $freight) * 100)) : 0;
@endphp

<style>
/* ── Edit Trip Page ─────────────────────────────────────────────────── */
.et-page { background: #f4f6fb; min-height: 100vh; }

/* Header gradient banner */
.et-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 14px 20px;
    color: #fff;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}
.et-header::before {
    content: '';
    position: absolute;
    top: -30px; right: -30px;
    width: 120px; height: 120px;
    background: rgba(255,255,255,.08);
    border-radius: 50%;
}
.et-header::after {
    content: '';
    position: absolute;
    bottom: -40px; right: 50px;
    width: 80px; height: 80px;
    background: rgba(255,255,255,.05);
    border-radius: 50%;
}
.et-header .trip-no-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 20px;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .5px;
    margin-bottom: 4px;
}
.et-header h4 { font-size: 17px; font-weight: 800; margin: 0 0 2px; }
.et-header .sub { font-size: 12px; opacity: .8; }
.et-header .header-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.et-header .btn-header {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;
    border: none; cursor: pointer; transition: all .2s;
}
.et-header .btn-header-white {
    background: rgba(255,255,255,.2); color: #fff; border: 1px solid rgba(255,255,255,.35);
}
.et-header .btn-header-white:hover { background: rgba(255,255,255,.32); color: #fff; }
.et-header .btn-header-solid { background: #fff; color: #667eea; }
.et-header .btn-header-solid:hover { background: #f0f4ff; color: #5a6fd6; }

/* ── Inline Status Switcher (above timeline) ───────────────────── */
.status-switcher {
    display: flex; gap: 0; border-radius: 10px; overflow: hidden;
    border: 1px solid #e8eaf2; background: #f7f8fc;
    margin-bottom: 20px;
}
.status-sw-btn {
    flex: 1; display: flex; flex-direction: column; align-items: center;
    justify-content: center; gap: 4px;
    padding: 10px 8px; cursor: pointer; border: none;
    background: transparent; transition: all .2s;
    border-right: 1px solid #e8eaf2; position: relative;
    font-family: inherit;
}
.status-sw-btn:last-child { border-right: none; }
.status-sw-btn .ssb-dot {
    width: 10px; height: 10px; border-radius: 50%;
    transition: transform .2s;
}
.status-sw-btn .ssb-label {
    font-size: 11px; font-weight: 700; color: #8a94a6; transition: color .2s;
    white-space: nowrap;
}
.status-sw-btn.active .ssb-label { color: #1a2340; }
.status-sw-btn.active { background: #fff; box-shadow: inset 0 -3px 0 var(--sw-color); }
.status-sw-btn.active .ssb-dot { transform: scale(1.3); }
.status-sw-btn:hover:not(.active) { background: #eef0f7; }
.status-sw-btn .ssb-spinner {
    position: absolute; top: 4px; right: 6px;
    width: 12px; height: 12px;
    border: 2px solid rgba(0,0,0,.1);
    border-top-color: #667eea;
    border-radius: 50%;
    animation: sw-spin .6s linear infinite;
    display: none;
}
@keyframes sw-spin { to { transform: rotate(360deg); } }
.status-sw-btn.loading .ssb-spinner { display: block; }
/* ── Section card */
.et-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    margin-bottom: 20px;
    overflow: hidden;
}
.et-card-header {
    display: flex; align-items: center; gap: 10px;
    padding: 16px 20px;
    border-bottom: 1px solid #f0f2f7;
    background: #fafbff;
}
.et-card-header .card-icon {
    width: 36px; height: 36px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
}
.et-card-header h6 { margin: 0; font-size: 14px; font-weight: 700; color: #1a2340; }
.et-card-header .card-subtitle { font-size: 11px; color: #8a94a6; margin: 0; }
.et-card-body { padding: 20px; }
</style>

<style>
/* ── Timeline ───────────────────────────────────────────────────────── */
.trip-timeline { position: relative; padding: 8px 0; }
.trip-timeline::before {
    content: '';
    position: absolute;
    left: 19px; top: 0; bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #667eea, #e2e8f0);
    z-index: 0;
}
.tl-step {
    display: flex; align-items: flex-start; gap: 16px;
    position: relative; z-index: 1; margin-bottom: 0;
    padding: 10px 0;
}
.tl-step:last-child { margin-bottom: 0; }
.tl-dot {
    width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,.12);
    transition: all .3s;
}
.tl-dot.done  { background: linear-gradient(135deg, #48bb78, #38a169); color: #fff; }
.tl-dot.active { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; animation: tl-pulse 2s infinite; }
.tl-dot.pending { background: #f0f2f7; color: #b0bac9; border-color: #e2e8f0; }
@keyframes tl-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(102,126,234,.4); }
    50% { box-shadow: 0 0 0 8px rgba(102,126,234,0); }
}
.tl-content { flex: 1; padding-top: 8px; }
.tl-label { font-size: 13px; font-weight: 700; color: #1a2340; margin-bottom: 2px; }
.tl-label.pending { color: #b0bac9; }
.tl-date { font-size: 11px; color: #8a94a6; }
.tl-badge {
    display: inline-block; padding: 2px 8px; border-radius: 10px;
    font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px;
}
.tl-badge.done    { background: #f0fff4; color: #38a169; }
.tl-badge.active  { background: #eef2ff; color: #667eea; }
.tl-badge.pending { background: #f7f8fc; color: #b0bac9; }

/* ── Finance cards ──────────────────────────────────────────────────── */
.fin-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 14px; }
.fin-card {
    border-radius: 10px; padding: 16px 14px;
    display: flex; flex-direction: column; gap: 6px;
    border: 1px solid transparent;
    transition: transform .2s, box-shadow .2s;
}
.fin-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.1); }
.fin-card .fc-icon {
    width: 36px; height: 36px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; margin-bottom: 4px;
}
.fin-card .fc-label { font-size: 11px; font-weight: 700; color: #8a94a6; text-transform: uppercase; letter-spacing: .4px; }
.fin-card .fc-value { font-size: 20px; font-weight: 800; color: #1a2340; line-height: 1; }
.fin-card .fc-sub { font-size: 11px; color: #b0bac9; }

/* P&L summary bar */
.pnl-bar-wrap { background: #f7f8fc; border-radius: 10px; padding: 16px; margin-top: 16px; }
.pnl-bar-track { height: 10px; border-radius: 5px; background: #e2e8f0; overflow: hidden; margin: 8px 0; }
.pnl-bar-fill { height: 100%; border-radius: 5px; transition: width .6s ease; }
.pnl-labels { display: flex; justify-content: space-between; font-size: 11px; color: #8a94a6; }

/* Payment tracker */
.pay-track { display: flex; gap: 0; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }
.pay-track-item {
    flex: 1; padding: 12px 10px; text-align: center;
    border-right: 1px solid #e2e8f0; background: #fff;
}
.pay-track-item:last-child { border-right: none; }
.pay-track-item .pt-label { font-size: 10px; font-weight: 700; color: #8a94a6; text-transform: uppercase; letter-spacing: .4px; }
.pay-track-item .pt-value { font-size: 16px; font-weight: 800; color: #1a2340; margin: 2px 0; }
.pay-track-item .pt-badge {
    display: inline-block; padding: 2px 8px; border-radius: 10px;
    font-size: 10px; font-weight: 700;
}

/* Info panel rows */
.info-row { display: flex; align-items: center; padding: 10px 0; border-bottom: 1px solid #f4f6fb; }
.info-row:last-child { border-bottom: none; }
.info-row .ir-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; margin-right: 12px; }
.info-row .ir-label { font-size: 11px; color: #8a94a6; font-weight: 600; }
.info-row .ir-value { font-size: 13px; color: #1a2340; font-weight: 700; }

/* GPS placeholder */
.gps-placeholder {
    background: linear-gradient(135deg, #1a2340 0%, #2d3a5e 100%);
    border-radius: 10px; padding: 28px 20px; text-align: center; color: #fff;
    position: relative; overflow: hidden;
}
.gps-placeholder::before {
    content: '';
    position: absolute; top: -20px; left: -20px;
    width: 100px; height: 100px;
    background: rgba(102,126,234,.15); border-radius: 50%;
}
.gps-dot { width: 12px; height: 12px; background: #48bb78; border-radius: 50%; display: inline-block; margin-right: 6px; animation: tl-pulse 2s infinite; }

/* Upload zone */
.doc-upload-zone {
    border: 2px dashed #c0d4f5; border-radius: 10px;
    padding: 24px; text-align: center; cursor: pointer;
    transition: all .2s; background: #f8fbff;
}
.doc-upload-zone:hover { border-color: #667eea; background: #eef2ff; }
.doc-upload-zone i { font-size: 32px; color: #c0d4f5; display: block; margin-bottom: 8px; }
.doc-upload-zone .duz-title { font-size: 13px; font-weight: 600; color: #667eea; }
.doc-upload-zone .duz-sub { font-size: 11px; color: #b0bac9; }

/* Responsive */
@media (max-width: 767.98px) {
    .et-header { padding: 12px 14px; }
    .et-header h4 { font-size: 14px; }
    .fin-grid { grid-template-columns: repeat(2, 1fr); }
    .pay-track { flex-direction: column; }
    .pay-track-item { border-right: none; border-bottom: 1px solid #e2e8f0; }
    .pay-track-item:last-child { border-bottom: none; }
    .trip-timeline::before { left: 17px; }
}

/* ── Horizontal Finance Summary Bar ───────────────────────────────── */
.fin-summary-bar {
    display: flex;
    gap: 0;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    overflow: hidden;
    margin-bottom: 20px;
    border: 1px solid #f0f2f7;
}
.fin-summary-item {
    flex: 1;
    padding: 14px 16px;
    border-right: 1px solid #f0f2f7;
    position: relative;
    transition: background .15s;
}
.fin-summary-item:last-child { border-right: none; }
.fin-summary-item:hover { background: #fafbff; }
.fin-summary-item .fsi-icon {
    width: 30px; height: 30px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; margin-bottom: 6px;
}
.fin-summary-item .fsi-label {
    font-size: 10px; font-weight: 700; color: #8a94a6;
    text-transform: uppercase; letter-spacing: .4px;
    white-space: nowrap; margin-bottom: 3px;
}
.fin-summary-item .fsi-value {
    font-size: 16px; font-weight: 800; color: #1a2340; line-height: 1;
}
.fin-summary-item .fsi-sub {
    font-size: 10px; color: #b0bac9; margin-top: 3px;
}
@media (max-width: 991.98px) {
    .fin-summary-bar { flex-wrap: wrap; }
    .fin-summary-item { flex: 0 0 33.333%; border-bottom: 1px solid #f0f2f7; }
    .fin-summary-item:nth-child(3) { border-right: none; }
    .fin-summary-item:nth-last-child(-n+3) { border-bottom: none; }
}
@media (max-width: 575.98px) {
    .fin-summary-item { flex: 0 0 50%; }
    .fin-summary-item:nth-child(3) { border-right: 1px solid #f0f2f7; }
    .fin-summary-item:nth-child(2n) { border-right: none; }
    .fin-summary-item:nth-last-child(-n+2) { border-bottom: none; }
}
</style>

<div class="pcoded-inner-content et-page">
<div class="main-body">
<div class="page-wrapper">
<div class="page-body">

{{-- ── HEADER BANNER (compact) ─────────────────────────────────── --}}
<div class="et-header">
    <div class="row align-items-center" style="position:relative;z-index:1;">

        {{-- Left: Trip identity --}}
        <div class="col-md-8">
            <div class="trip-no-badge">
                <i class="ti-location-arrow"></i> {{ $trip->trip_no }}
            </div>
            <h4>{{ $trip->from_location }} <span style="opacity:.6;font-weight:400;">→</span> {{ $trip->to_location }}</h4>
            <div class="sub">
                <i class="ti-calendar mr-1"></i>{{ $trip->trip_date ? $trip->trip_date->format('d M Y') : 'N/A' }}
                @if($trip->driver)
                &nbsp;&bull;&nbsp;<i class="ti-user mr-1"></i>{{ $trip->driver->name }}
                @endif
            </div>
        </div>

        {{-- Right: Action buttons --}}
        <div class="col-md-4 text-right mt-2 mt-md-0">
            <div class="header-actions justify-content-end">
                <a href="{{ route('trip') }}" class="btn-header btn-header-white">
                    <i class="ti-arrow-left"></i> Back
                </a>
                <button type="submit" form="tripEditForm" class="btn-header btn-header-solid" id="saveTripBtn">
                    <i class="ti-save"></i> Save
                </button>
            </div>
        </div>

    </div>
</div>

@include('partials.flash')

{{-- ── FINANCIAL SUMMARY BAR (horizontal, full-width) ──────────── --}}
<div class="fin-summary-bar">
    {{-- Freight --}}
    <div class="fin-summary-item">
        <div class="fsi-icon" style="background:#eef2ff;color:#4338ca;"><i class="ti-money"></i></div>
        <div class="fsi-label">Party Freight</div>
        <div class="fsi-value" style="color:#4338ca;">₹{{ number_format($freight, 0) }}</div>
        <div class="fsi-sub">Total billed</div>
    </div>
    {{-- Advance --}}
    <div class="fin-summary-item">
        <div class="fsi-icon" style="background:#fff8e1;color:#b45309;"><i class="ti-credit-card"></i></div>
        <div class="fsi-label">Advance Paid</div>
        <div class="fsi-value" style="color:#b45309;">₹{{ number_format($advance, 0) }}</div>
        <div class="fsi-sub">Paid upfront</div>
    </div>
    {{-- Driver Pay --}}
    <div class="fin-summary-item">
        <div class="fsi-icon" style="background:#f0fff4;color:#15803d;"><i class="ti-user"></i></div>
        <div class="fsi-label">Driver Bata</div>
        <div class="fsi-value" style="color:#15803d;">₹{{ number_format($driverPay, 0) }}</div>
        <div class="fsi-sub">Driver payment</div>
    </div>
    {{-- Fuel --}}
    <div class="fin-summary-item">
        <div class="fsi-icon" style="background:#fff1f2;color:#be123c;"><i class="ti-dropbox"></i></div>
        <div class="fsi-label">Diesel Advance</div>
        <div class="fsi-value" style="color:#be123c;">₹{{ number_format($fuel, 0) }}</div>
        <div class="fsi-sub">Fuel cost</div>
    </div>
    {{-- Toll --}}
    <div class="fin-summary-item">
        <div class="fsi-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti-map"></i></div>
        <div class="fsi-label">Toll Charges</div>
        <div class="fsi-value" style="color:#7c3aed;">₹{{ number_format($toll, 0) }}</div>
        <div class="fsi-sub">Toll expense</div>
    </div>
    {{-- Total Expenses --}}
    <div class="fin-summary-item">
        <div class="fsi-icon" style="background:#fff7ed;color:#c2410c;"><i class="ti-stats-up"></i></div>
        <div class="fsi-label">Total Expenses</div>
        <div class="fsi-value" style="color:#c2410c;">₹{{ number_format($totalExp, 0) }}</div>
        <div class="fsi-sub">{{ $barPct }}% of freight</div>
    </div>
    {{-- Profit / Loss --}}
    <div class="fin-summary-item">
        <div class="fsi-icon" style="background:{{ $isProfit ? '#f0fff4' : '#fff5f5' }};color:{{ $isProfit ? '#15803d' : '#e53e3e' }};"><i class="ti-arrow-{{ $isProfit ? 'up' : 'down' }}"></i></div>
        <div class="fsi-label">{{ $isProfit ? 'Net Profit' : 'Net Loss' }}</div>
        <div class="fsi-value" style="color:{{ $isProfit ? '#15803d' : '#e53e3e' }};">{{ $isProfit ? '+' : '-' }}₹{{ number_format(abs($profit), 0) }}</div>
        <div class="fsi-sub">Freight − expenses</div>
    </div>
    {{-- Outstanding --}}
    <div class="fin-summary-item">
        <div class="fsi-icon" style="background:{{ $outstanding > 0 ? '#fff5f5' : '#f0fff4' }};color:{{ $outstanding > 0 ? '#e53e3e' : '#15803d' }};"><i class="ti-wallet"></i></div>
        <div class="fsi-label">Outstanding</div>
        <div class="fsi-value" style="color:{{ $outstanding > 0 ? '#e53e3e' : '#15803d' }};">₹{{ number_format(abs($outstanding), 0) }}</div>
        <div class="fsi-sub">{{ $outstanding > 0 ? 'Pending collection' : 'Fully collected' }}</div>
    </div>
</div>

{{-- ── MAIN CONTENT GRID ─────────────────────────────────────────── --}}
<div class="row">

    {{-- LEFT COLUMN: Edit Form → Remarks → Status → Timeline --}}
    <div class="col-lg-8">

        {{-- ① Edit Trip Information Form (TOP) --}}
        <div class="et-card">
            <div class="et-card-header">
                <div class="card-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-pencil"></i></div>
                <div>
                    <h6>Edit Trip Information</h6>
                    <p class="card-subtitle">Update route, billing, and trip details</p>
                </div>
            </div>
            <div class="et-card-body">
                <form id="tripEditForm" action="{{ route('trip.update', $trip->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    {{-- Status is injected via hidden input updated by the pill selector below --}}
                    <input type="hidden" name="status" id="statusHidden" value="{{ $currentStatus }}">
                    @include('Trips._form')
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('trip') }}" class="btn btn-secondary btn-sm">
                            <i class="ti-close mr-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm" id="saveTripBtnForm">
                            <i class="ti-save mr-1"></i> Update Trip
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ② Notes & Remarks --}}
        <div class="et-card">
            <div class="et-card-header">
                <div class="card-icon" style="background:#f0fff4;color:#48bb78;"><i class="ti-comment-alt"></i></div>
                <div>
                    <h6>Notes & Remarks</h6>
                    <p class="card-subtitle">Internal notes for this trip</p>
                </div>
            </div>
            <div class="et-card-body">
                <div style="background:#f7f8fc;border-radius:8px;padding:14px;font-size:13px;color:#4a5568;line-height:1.7;min-height:60px;">
                    {{ $trip->remarks ?: 'No remarks added for this trip.' }}
                </div>
            </div>
        </div>

        {{-- ③ Trip Timeline with inline Status Switcher --}}
        <div class="et-card" id="timelineCard">
            <div class="et-card-header">
                <div class="card-icon" style="background:#fff8e1;color:#f6ad55;"><i class="ti-time"></i></div>
                <div>
                    <h6>Trip Timeline</h6>
                    <p class="card-subtitle">Live progress from creation to closure</p>
                </div>
            </div>
            <div class="et-card-body">

                {{-- ── Status Switcher (above timeline) ─────────────────── --}}
                @php
                    $swCfg = [
                        'planned'   => ['label' => 'Planned',   'color' => '#667eea'],
                        'running'   => ['label' => 'Running',   'color' => '#f6ad55'],
                        'completed' => ['label' => 'Completed', 'color' => '#48bb78'],
                        'cancelled' => ['label' => 'Cancelled', 'color' => '#fc8181'],
                    ];
                @endphp
                <div class="status-switcher"
                     id="statusSwitcher"
                     data-url="{{ route('trip.status', $trip->id) }}"
                     data-csrf="{{ csrf_token() }}">
                    @foreach($swCfg as $sk => $sv)
                    <button type="button"
                            class="status-sw-btn {{ $currentStatus === $sk ? 'active' : '' }}"
                            data-status="{{ $sk }}"
                            style="--sw-color:{{ $sv['color'] }};">
                        <span class="ssb-dot" style="background:{{ $sv['color'] }};"></span>
                        <span class="ssb-label">{{ $sv['label'] }}</span>
                        <span class="ssb-spinner"></span>
                    </button>
                    @endforeach
                </div>
                {{-- also keep hidden input in sync for the full form save --}}

                <div class="trip-timeline" id="tripTimeline">

                    @foreach($tlEvents as $ev)
                    @php
                        $isDone   = $ev['done'];
                        $isActive = !$isCancelled && ($activeKey === $ev['key']) && !$isDone;

                        $dotClass = $isDone ? 'done' : ($isActive ? 'active' : 'pending');

                        $dateStr = null;
                        if ($ev['date']) {
                            $dateStr = is_string($ev['date'])
                                ? $ev['date']
                                : $ev['date']->format('d M Y, h:i A');
                        }
                    @endphp
                    <div class="tl-step" data-key="{{ $ev['key'] }}">
                        <div class="tl-dot {{ $dotClass }}">
                            <i class="{{ $ev['icon'] }}"></i>
                        </div>
                        <div class="tl-content">
                            <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
                                <span class="tl-label {{ !$isDone && !$isActive ? 'pending' : '' }}">
                                    {{ $ev['label'] }}
                                </span>
                                @if($isCancelled && $ev['key'] !== 'created')
                                    <span class="tl-badge pending">Skipped</span>
                                @elseif($isDone)
                                    <span class="tl-badge done">Done</span>
                                @elseif($isActive)
                                    <span class="tl-badge active">In Progress</span>
                                @else
                                    <span class="tl-badge pending">Pending</span>
                                @endif
                            </div>
                            <div class="tl-date">
                                @if($dateStr && !$isCancelled)
                                    <i class="ti-calendar mr-1"></i>{{ $dateStr }}
                                @elseif(!$isCancelled)
                                    <span style="color:#d0d5e0;">Not yet reached</span>
                                @endif
                            </div>
                            @if(!empty($ev['meta']) && !$isCancelled)
                                <div style="font-size:11px;color:#8a94a6;margin-top:2px;">
                                    {{ $ev['meta'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    {{-- Cancelled step --}}
                    @if($isCancelled)
                    <div class="tl-step" data-key="cancelled">
                        <div class="tl-dot" style="background:linear-gradient(135deg,#fc8181,#e53e3e);color:#fff;border-color:#fff;">
                            <i class="ti-close"></i>
                        </div>
                        <div class="tl-content">
                            <div class="d-flex align-items-center" style="gap:8px;">
                                <span class="tl-label" style="color:#e53e3e;">Trip Cancelled</span>
                                <span class="tl-badge" style="background:#fff5f5;color:#e53e3e;">Cancelled</span>
                            </div>
                            <div class="tl-date">
                                @if($trip->updated_at)
                                    <i class="ti-calendar mr-1"></i>{{ $trip->updated_at->format('d M Y, h:i A') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- Toast notification (shared, appended to body via JS) --}}

    </div>{{-- /col-lg-8 --}}

    {{-- RIGHT COLUMN: Payment P&L + Driver + Vehicle + GPS + Docs --}}
    <div class="col-lg-4">

        {{-- ① Payment Collection Panel --}}
        @php
            $paidPct      = $freight > 0 ? min(100, round(($collected / $freight) * 100)) : 0;
            $pendingAmt   = max(0, $freight - $collected);
            $payHistory   = $trip->payments ?? collect();
            // Derive status from actual payments sum
            $derivedStatus = 'pending';
            if ($collected >= $freight && $collected > 0) $derivedStatus = 'completed';
            elseif ($collected > 0) $derivedStatus = 'partial';
            $payStatusColor = $derivedStatus === 'completed' ? '#38a169' : ($derivedStatus === 'partial' ? '#d97706' : '#e53e3e');
            $payStatusBg    = $derivedStatus === 'completed' ? '#f0fff4' : ($derivedStatus === 'partial' ? '#fffbeb' : '#fff5f5');
            $payBarColor    = $derivedStatus === 'completed' ? '#48bb78' : ($derivedStatus === 'partial' ? '#f6ad55' : '#fc8181');
        @endphp
        <div class="et-card" id="paymentCollectionCard">
            <div class="et-card-header">
                <div class="card-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-wallet"></i></div>
                <div>
                    <h6>Payment Collection</h6>
                    <p class="card-subtitle">Freight: ₹{{ number_format($freight, 0) }}</p>
                </div>
                <span id="payStatusBadge" class="ml-auto" style="font-size:11px;font-weight:700;padding:3px 11px;border-radius:10px;white-space:nowrap;
                    background:{{ $payStatusBg }};color:{{ $payStatusColor }};">
                    {{ ucfirst($derivedStatus) }}
                </span>
            </div>
            <div class="et-card-body">

                {{-- Two summary boxes --}}
                <div class="row mb-3" style="margin-left:-6px;margin-right:-6px;">
                    <div class="col-6" style="padding:0 6px;">
                        <div style="background:#f0fff4;border-radius:10px;padding:12px 14px;border:1px solid #c6f6d5;">
                            <div style="font-size:10px;font-weight:700;color:#38a169;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">
                                <i class="ti-check mr-1"></i>Collected
                            </div>
                            <div id="payCollectedDisplay" style="font-size:20px;font-weight:800;color:#276749;">
                                ₹{{ number_format($collected, 0) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6" style="padding:0 6px;">
                        <div style="background:{{ $pendingAmt > 0 ? '#fff5f5' : '#f0fff4' }};border-radius:10px;padding:12px 14px;border:1px solid {{ $pendingAmt > 0 ? '#fed7d7' : '#c6f6d5' }};">
                            <div style="font-size:10px;font-weight:700;color:{{ $pendingAmt > 0 ? '#e53e3e' : '#38a169' }};text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">
                                <i class="ti-time mr-1"></i>Pending
                            </div>
                            <div id="payPendingDisplay" style="font-size:20px;font-weight:800;color:{{ $pendingAmt > 0 ? '#c53030' : '#276749' }};">
                                ₹{{ number_format($pendingAmt, 0) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div style="height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;margin-bottom:18px;">
                    <div id="payProgressBar" style="height:100%;border-radius:3px;transition:width .4s;
                        width:{{ $paidPct }}%;background:{{ $payBarColor }};"></div>
                </div>

                {{-- Add payment form — hidden when fully paid --}}
                <div id="payAddFormWrap" @if($derivedStatus === 'completed') style="display:none;" @endif>
                <form id="paymentAddForm">
                    @csrf
                    <div class="form-group mb-2">
                        <label style="font-size:11px;font-weight:700;color:#596579;margin-bottom:5px;display:block;">
                            Pay Amount <span style="color:#dc3545;">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background:#f8f9fa;border-color:#d7dce5;font-weight:700;font-size:15px;">₹</span>
                            </div>
                            <input type="number" id="payAmountInput" name="amount"
                                step="0.01" min="0.01" max="{{ $pendingAmt }}"
                                class="form-control"
                                placeholder="Enter amount"
                                value="{{ $pendingAmt > 0 ? number_format($pendingAmt, 2, '.', '') : '' }}"
                                style="border-color:#d7dce5;min-height:44px;font-size:16px;font-weight:700;"
                                required>
                        </div>
                        <small style="font-size:10px;color:#8a94a6;margin-top:3px;display:block;">
                            Pending: ₹<span id="payPendingHint">{{ number_format($pendingAmt, 0) }}</span>
                        </small>
                    </div>

                    <div class="row mb-2" style="margin-left:-4px;margin-right:-4px;">
                        <div class="col-6" style="padding:0 4px;">
                            <select name="payment_mode" id="payModeInput"
                                class="form-control form-control-sm select2-pay"
                                style="border-color:#d7dce5;min-height:38px;width:100%;">
                                <option value="">Mode</option>
                                <option value="cash">💵 Cash</option>
                                <option value="upi">📱 UPI</option>
                                <option value="bank">🏦 Bank Transfer</option>
                                <option value="cheque">📄 Cheque</option>
                            </select>
                        </div>
                        <div class="col-6" style="padding:0 4px;">
                            <input type="date" name="paid_on" id="payDateInput"
                                class="form-control form-control-sm"
                                style="border-color:#d7dce5;min-height:38px;"
                                value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <input type="text" name="reference" id="payRefInput"
                            class="form-control form-control-sm"
                            style="border-color:#d7dce5;min-height:38px;"
                            placeholder="Reference / Txn ID (optional)">
                    </div>

                    <button type="submit" id="addPaymentBtn"
                        class="btn btn-success btn-block"
                        style="font-size:13px;font-weight:700;min-height:42px;border-radius:8px;">
                        <i class="ti-plus mr-1"></i> Add Payment
                    </button>
                </form>
                </div>

                {{-- Fully paid notice --}}
                <div id="payFullyPaidNotice" @if($derivedStatus !== 'completed') style="display:none;" @endif
                    style="text-align:center;padding:14px 10px;background:#f0fff4;border-radius:10px;border:1px solid #c6f6d5;margin-bottom:4px;">
                    <i class="ti-check-box" style="font-size:26px;color:#38a169;display:block;margin-bottom:6px;"></i>
                    <div style="font-size:13px;font-weight:700;color:#276749;">Fully Collected</div>
                    <div style="font-size:11px;color:#38a169;margin-top:2px;">No pending amount</div>
                </div>

                {{-- Payment History --}}
                <div class="mt-4">
                    <div style="font-size:12px;font-weight:700;color:#596579;margin-bottom:10px;display:flex;align-items:center;justify-content:space-between;">
                        <span><i class="ti-receipt mr-1"></i> Payment History</span>
                        <span id="payHistoryCount" style="background:#eef2ff;color:#667eea;padding:1px 8px;border-radius:10px;font-size:10px;">
                            {{ $payHistory->count() }} entries
                        </span>
                    </div>
                    <div id="payHistoryList">
                        @forelse($payHistory as $pay)
                        <div class="pay-hist-row" data-id="{{ $pay->id }}"
                            style="display:flex;align-items:center;gap:8px;padding:9px 10px;background:#f7f8fc;border-radius:8px;margin-bottom:6px;border-left:3px solid #48bb78;">
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:13px;font-weight:800;color:#1a2340;">₹{{ number_format($pay->amount, 0) }}</div>
                                <div style="font-size:10px;color:#8a94a6;margin-top:1px;">
                                    {{ $pay->paid_on->format('d M Y') }}
                                    @if($pay->payment_mode) &bull; {{ ucfirst($pay->payment_mode) }} @endif
                                    @if($pay->reference) &bull; {{ $pay->reference }} @endif
                                </div>
                            </div>

                            
                            <button type="button" class="pay-delete-btn"
                                data-id="{{ $pay->id }}"
                                style="background:#fff5f5;color:#e53e3e;border:none;border-radius:6px;width:26px;height:26px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;font-size:12px;"
                                title="Remove this payment">
                                <i class="ti-trash"></i>
                            </button>
                        </div>
                        @empty
                        <div id="payHistoryEmpty" style="text-align:center;padding:14px 0;color:#b0bac9;font-size:12px;">
                            <i class="ti-receipt" style="font-size:22px;display:block;margin-bottom:6px;"></i>
                            No payments recorded yet
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

        {{-- Driver Info Panel (editable) --}}
        <div class="et-card">
            <div class="et-card-header">
                <div class="card-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti-user"></i></div>
                <div>
                    <h6>Driver Information</h6>
                    <p class="card-subtitle">Change or view assigned driver</p>
                </div>
            </div>
            <div class="et-card-body">

                {{-- Driver avatar + name (live preview) --}}
                <div id="driverPreview" class="{{ $trip->driver ? '' : 'd-none' }} d-flex align-items-center mb-3" style="gap:12px;">
                    <div id="driverAvatar" style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;font-weight:800;flex-shrink:0;">
                        {{ $trip->driver ? strtoupper(substr($trip->driver->name, 0, 1)) : '' }}
                    </div>
                    <div>
                        <div id="driverPreviewName" style="font-size:15px;font-weight:800;color:#1a2340;">{{ $trip->driver->name ?? '' }}</div>
                        <div id="driverPreviewMobile" style="font-size:12px;color:#8a94a6;">{{ $trip->driver->mobile ?? '' }}</div>
                    </div>
                </div>

                <div id="driverNoAssigned" class="{{ $trip->driver ? 'd-none' : '' }}" style="text-align:center;padding:12px 0 16px;color:#b0bac9;">
                    <i class="ti-user" style="font-size:28px;display:block;margin-bottom:6px;"></i>
                    <div style="font-size:13px;font-weight:600;">No driver assigned</div>
                </div>

                {{-- Info rows (live preview) --}}
                <div id="driverInfoRows" class="{{ $trip->driver ? '' : 'd-none' }}">
                    <div class="info-row">
                        <div class="ir-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti-id-badge"></i></div>
                        <div><div class="ir-label">License No.</div><div class="ir-value" id="driverPreviewLicense">{{ $trip->driver->license_number ?? '-' }}</div></div>
                    </div>
                    <div class="info-row">
                        <div class="ir-icon" style="background:#fff8e1;color:#d97706;"><i class="ti-mobile"></i></div>
                        <div><div class="ir-label">Mobile</div><div class="ir-value" id="driverPreviewMobile2">{{ $trip->driver->mobile ?? '-' }}</div></div>
                    </div>
                    <div class="info-row">
                        <div class="ir-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-map-alt"></i></div>
                        <div><div class="ir-label">Location</div><div class="ir-value" id="driverPreviewLocation">{{ implode(', ', array_filter([$trip->driver->city ?? '', $trip->driver->state ?? ''])) ?: '-' }}</div></div>
                    </div>
                </div>

                {{-- Edit driver link --}}
                @if($trip->driver)
                <div class="mt-3">
                    <a href="{{ route('driver.edit', $trip->driver->id) }}" target="_blank"
                       class="btn btn-sm btn-outline-primary btn-block" style="font-size:12px;">
                        <i class="ti-pencil mr-1"></i> Edit Driver Profile
                    </a>
                </div>
                @endif

            </div>
        </div>

        {{-- Vehicle Info Panel --}}
        <div class="et-card">
            <div class="et-card-header">
                <div class="card-icon" style="background:#fff8e1;color:#d97706;"><i class="ti-truck"></i></div>
                <div>
                    <h6>Vehicle Information</h6>
                    <p class="card-subtitle">Assigned vehicle details</p>
                </div>
            </div>
            <div class="et-card-body">
                @if($trip->vehicle)
                <div class="info-row">
                    <div class="ir-icon" style="background:#fff8e1;color:#d97706;"><i class="ti-truck"></i></div>
                    <div><div class="ir-label">Registration No.</div><div class="ir-value" style="font-size:15px;">{{ $trip->vehicle->vehicle_number }}</div></div>
                </div>
                @if($trip->vehicle->vehicle_name)
                <div class="info-row">
                    <div class="ir-icon" style="background:#f0f9ff;color:#0369a1;"><i class="ti-tag"></i></div>
                    <div><div class="ir-label">Vehicle Name</div><div class="ir-value">{{ $trip->vehicle->vehicle_name }}</div></div>
                </div>
                @endif
                @if($trip->vehicle->vehicle_type)
                <div class="info-row">
                    <div class="ir-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti-layout-list-thumb"></i></div>
                    <div><div class="ir-label">Type</div><div class="ir-value">{{ ucfirst($trip->vehicle->vehicle_type) }}</div></div>
                </div>
                @endif
                @if($trip->start_kms_reading)
                <div class="info-row">
                    <div class="ir-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-dashboard"></i></div>
                    <div><div class="ir-label">Start KMs Reading</div><div class="ir-value">{{ number_format($trip->start_kms_reading, 1) }} KMs</div></div>
                </div>
                @endif
                @else
                <div style="text-align:center;padding:20px 0;color:#b0bac9;">
                    <i class="ti-truck" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    <div style="font-size:13px;font-weight:600;">No vehicle assigned</div>
                </div>
                @endif
            </div>
        </div>

        {{-- GPS Tracking Placeholder --}}
        <div class="et-card">
            <div class="et-card-header">
                <div class="card-icon" style="background:#f0f9ff;color:#0369a1;"><i class="ti-location-pin"></i></div>
                <div>
                    <h6>GPS Tracking</h6>
                    <p class="card-subtitle">Live vehicle location</p>
                </div>
            </div>
            <div class="et-card-body p-0">
                <div class="gps-placeholder">
                    <div style="position:relative;z-index:1;">
                        <i class="ti-location-pin" style="font-size:36px;opacity:.5;display:block;margin-bottom:10px;"></i>
                        <div style="font-size:14px;font-weight:700;margin-bottom:6px;">Live Tracking</div>
                        <div style="font-size:12px;opacity:.6;margin-bottom:14px;">GPS integration coming soon</div>
                        <div style="font-size:12px;background:rgba(255,255,255,.1);border-radius:8px;padding:8px 12px;display:inline-flex;align-items:center;gap:6px;">
                            <span class="gps-dot"></span>
                            {{ $trip->from_location }} &rarr; {{ $trip->to_location }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Document Upload --}}
        <div class="et-card">
            <div class="et-card-header">
                <div class="card-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-files"></i></div>
                <div>
                    <h6>Trip Documents</h6>
                    <p class="card-subtitle">Upload invoices, LR, POD etc.</p>
                </div>
            </div>
            <div class="et-card-body">
                <div class="doc-upload-zone" onclick="document.getElementById('docFileInput').click()">
                    <input type="file" id="docFileInput" style="display:none;" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    <i class="ti-cloud-up"></i>
                    <div class="duz-title">Click to upload documents</div>
                    <div class="duz-sub">PDF, JPG, PNG, DOC — Max 10MB each</div>
                </div>
                <div id="docFileList" class="mt-2"></div>
                <div class="mt-3" style="font-size:12px;color:#8a94a6;">
                    <div class="d-flex align-items-center" style="gap:6px;margin-bottom:6px;">
                        <i class="ti-file" style="color:#667eea;"></i>
                        <span>LR No: <strong style="color:#1a2340;">{{ $trip->lr_no ?: 'Not assigned' }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /col-lg-4 --}}
</div>{{-- /row --}}

</div>{{-- /page-body --}}
</div>{{-- /page-wrapper --}}
</div>{{-- /main-body --}}
</div>{{-- /pcoded-inner-content --}}

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    /* ══════════════════════════════════════════════════════════════
       STATUS SWITCHER — AJAX auto-update (no full page reload)
    ══════════════════════════════════════════════════════════════ */

    // Timeline step order — used to animate dots when status changes
    var tlOrder = ['created','driver','loading','transit','delivered','closed'];

    var statusConfig = {
        planned:   { color: '#667eea', label: 'Planned',   tlReach: 'created'   },
        running:   { color: '#f6ad55', label: 'Running',   tlReach: 'transit'   },
        completed: { color: '#48bb78', label: 'Completed', tlReach: 'closed'    },
        cancelled: { color: '#fc8181', label: 'Cancelled', tlReach: 'cancelled' },
    };

    var $switcher   = $('#statusSwitcher');
    var statusUrl   = $switcher.data('url');
    var csrfToken   = $switcher.data('csrf');
    var currentStatus = $('#statusHidden').val();

    // ── Flash message via toastr ──────────────────────────────────
    function showToast(msg, type) {
        if (typeof toastr === 'undefined') return;
        toastr[type === 'error' ? 'error' : 'success'](msg, type === 'error' ? 'Error' : 'Success');
    }

    // ── Timeline dots live update ─────────────────────────────────
    function refreshTimeline(newStatus) {
        var cfg       = statusConfig[newStatus] || {};
        var reachKey  = cfg.tlReach;
        var reachIdx  = tlOrder.indexOf(reachKey);
        var cancelled = newStatus === 'cancelled';

        // For each step: done if idx < reachIdx, active if idx === reachIdx (first pending), pending if idx > reachIdx
        // Special: cancelled = all pending except created
        var firstPendingSet = false;

        $('#tripTimeline .tl-step').each(function () {
            var key = $(this).data('key');
            if (key === 'cancelled') return; // handled separately

            var idx    = tlOrder.indexOf(key);
            var $dot   = $(this).find('.tl-dot');
            var $label = $(this).find('.tl-label');
            var $badge = $(this).find('.tl-badge');

            if (cancelled && key !== 'created') {
                $dot.removeClass('done active').addClass('pending');
                $badge.removeClass('done active').addClass('pending').text('Skipped');
                $label.addClass('pending');
                return;
            }

            if (idx < reachIdx) {
                // done
                $dot.removeClass('active pending').addClass('done');
                $badge.removeClass('active pending').addClass('done').text('Done');
                $label.removeClass('pending');
            } else if (idx === reachIdx && !firstPendingSet) {
                // first pending = active (in progress)
                firstPendingSet = true;
                $dot.removeClass('done pending').addClass('active');
                $badge.removeClass('done pending').addClass('active').text('In Progress');
                $label.removeClass('pending');
            } else {
                // pending
                $dot.removeClass('done active').addClass('pending');
                $badge.removeClass('done active').addClass('pending').text('Pending');
                $label.addClass('pending');
            }
        });

        // Show / hide the cancelled step row
        var $cancelRow = $('#tripTimeline .tl-step[data-key="cancelled"]');
        if (cancelled) {
            if (!$cancelRow.length) {
                var now = new Date();
                var fmt = now.toLocaleString('en-IN', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
                $('#tripTimeline').append(
                    '<div class="tl-step" data-key="cancelled">' +
                    '<div class="tl-dot" style="background:linear-gradient(135deg,#fc8181,#e53e3e);color:#fff;border-color:#fff;"><i class="ti-close"></i></div>' +
                    '<div class="tl-content">' +
                    '<div class="d-flex align-items-center" style="gap:8px;">' +
                    '<span class="tl-label" style="color:#e53e3e;">Trip Cancelled</span>' +
                    '<span class="tl-badge" style="background:#fff5f5;color:#e53e3e;">Cancelled</span>' +
                    '</div>' +
                    '<div class="tl-date"><i class="ti-calendar mr-1"></i>' + fmt + '</div>' +
                    '</div></div>'
                );
            }
        } else {
            $cancelRow.remove();
        }
    }

    // ── AJAX status PATCH ─────────────────────────────────────────
    function doStatusUpdate(newStatus, $btn) {
        if (newStatus === currentStatus) return;

        $btn.addClass('loading');
        $('.status-sw-btn').prop('disabled', true);

        $.ajax({
            url:    statusUrl,
            method: 'POST',           // PATCH via _method spoof
            data: {
                _method:  'PATCH',
                _token:   csrfToken,
                status:   newStatus,
            },
            success: function (res) {
                if (res.success) {
                    currentStatus = newStatus;
                    $('#statusHidden').val(newStatus);

                    // Update switcher active state
                    $('.status-sw-btn').each(function () {
                        $(this).toggleClass('active', $(this).data('status') === newStatus);
                    });

                    // Animate timeline
                    refreshTimeline(newStatus);

                    showToast('Status updated to ' + res.message.split(' to ')[1], 'success');
                } else {
                    showToast('Update failed. Please try again.', 'error');
                }
            },
            error: function () {
                showToast('Network error. Status not saved.', 'error');
            },
            complete: function () {
                $btn.removeClass('loading');
                $('.status-sw-btn').prop('disabled', false);
            }
        });
    }

    // ── Click handler ─────────────────────────────────────────────
    $(document).on('click', '.status-sw-btn', function () {
        doStatusUpdate($(this).data('status'), $(this));
    });


    /* ══════════════════════════════════════════════════════════════
       DRIVER DROPDOWN → live preview panel
    ══════════════════════════════════════════════════════════════ */
    function updateDriverPanel(val, text, $option) {
        if (!val) {
            $('#driverPreview').addClass('d-none');
            $('#driverInfoRows').addClass('d-none');
            $('#driverNoAssigned').removeClass('d-none');
            return;
        }
        var mobile     = $option.data('mobile')  || '';
        var license    = $option.data('license') || '-';
        var city       = $option.data('city')    || '';
        var state      = $option.data('state')   || '';
        var location   = [city, state].filter(Boolean).join(', ') || '-';
        var initial    = (text || '?').trim().charAt(0).toUpperCase();
        var driverName = (text || '').split(' — ')[0].trim();

        $('#driverAvatar').text(initial);
        $('#driverPreviewName').text(driverName);
        $('#driverPreviewMobile').text(mobile || 'No mobile');
        $('#driverPreviewLicense').text(license);
        $('#driverPreviewMobile2').text(mobile || '-');
        $('#driverPreviewLocation').text(location);

        $('#driverNoAssigned').addClass('d-none');
        $('#driverPreview').removeClass('d-none');
        $('#driverInfoRows').removeClass('d-none');
    }

    (function () {
        var $sel = $('#driverIdSelect');
        if ($sel.val()) updateDriverPanel($sel.val(), $sel.find('option:selected').text(), $sel.find('option:selected'));
    })();

    $(document).on('change', '#driverIdSelect', function () {
        updateDriverPanel($(this).val(), $(this).find('option:selected').text(), $(this).find('option:selected'));
    });


    /* ══════════════════════════════════════════════════════════════
       FORM SUBMIT GUARD
    ══════════════════════════════════════════════════════════════ */
    $('#tripEditForm').on('submit', function () {
        $('#saveTripBtn, #saveTripBtnForm').prop('disabled', true)
            .html('<i class="ti-reload mr-1"></i> Saving...');
    });


    /* ══════════════════════════════════════════════════════════════
       DOCUMENT FILE PREVIEW
    ══════════════════════════════════════════════════════════════ */
    $('#docFileInput').on('change', function () {
        var $list = $('#docFileList').empty();
        $.each(this.files, function (i, f) {
            $list.append(
                '<div style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:#f7f8fc;border-radius:6px;margin-bottom:6px;font-size:12px;">' +
                '<i class="ti-file" style="color:#667eea;font-size:16px;"></i>' +
                '<span style="flex:1;font-weight:600;color:#1a2340;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + f.name + '</span>' +
                '<span style="color:#8a94a6;">' + (f.size/1024).toFixed(1) + ' KB</span>' +
                '</div>'
            );
        });
    });


    /* ══════════════════════════════════════════════════════════════
       P&L BAR ANIMATION
    ══════════════════════════════════════════════════════════════ */
    setTimeout(function () {
        $('.pnl-bar-fill').each(function () {
            var w = $(this).css('width');
            $(this).css('width', '0').animate({ width: w }, 800);
        });
    }, 300);


    /* ══════════════════════════════════════════════════════════════
       PAYMENT COLLECTION PANEL
    ══════════════════════════════════════════════════════════════ */
    var freightTotal    = {{ (float)$freight }};
    var addPayUrl       = '{{ route("trip.payment.add", $trip->id) }}';
    var deletePayUrlTpl = '{{ url("trip/".$trip->id."/payments") }}/';
    var csrfToken2      = '{{ csrf_token() }}';

    /* Init Select2 on payment mode */
    if ($.fn.select2) {
        $('#payModeInput').select2({
            width: '100%',
            minimumResultsForSearch: Infinity,
            placeholder: 'Mode'
        });
    }

    function updatePayUI(totalCollected, status) {
        var pending = Math.max(0, freightTotal - totalCollected);
        var pct     = freightTotal > 0 ? Math.min(100, Math.round(totalCollected / freightTotal * 100)) : 0;
        var color   = status === 'completed' ? '#48bb78' : (status === 'partial' ? '#f6ad55' : '#fc8181');

        $('#payCollectedDisplay').text('₹' + Math.round(totalCollected).toLocaleString('en-IN'));
        $('#payPendingDisplay').text('₹' + Math.round(pending).toLocaleString('en-IN'));
        $('#payPendingHint').text(Math.round(pending).toLocaleString('en-IN'));
        $('#payProgressBar').css({ width: pct + '%', background: color });

        /* Prefill amount with pending */
        $('#payAmountInput').attr('max', pending).val(pending > 0 ? pending.toFixed(2) : '');

        var badgeColor = status === 'completed' ? '#38a169' : (status === 'partial' ? '#d97706' : '#e53e3e');
        var badgeBg    = status === 'completed' ? '#f0fff4' : (status === 'partial' ? '#fffbeb' : '#fff5f5');
        $('#payStatusBadge').text(status.charAt(0).toUpperCase() + status.slice(1)).css({ color: badgeColor, background: badgeBg });

        /* Pending box styling */
        var $pendingBox = $('#payPendingDisplay').closest('div');
        $pendingBox.css({ background: pending > 0 ? '#fff5f5' : '#f0fff4', 'border-color': pending > 0 ? '#fed7d7' : '#c6f6d5' });
        $('#payPendingDisplay').css('color', pending > 0 ? '#c53030' : '#276749');

        /* Show form only when not fully paid */
        if (status === 'completed') {
            $('#payAddFormWrap').hide();
            $('#payFullyPaidNotice').show();
        } else {
            $('#payAddFormWrap').show();
            $('#payFullyPaidNotice').hide();
        }
    }

    /* ── Add payment ── */
    $('#paymentAddForm').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#addPaymentBtn');
        var amt  = parseFloat($('#payAmountInput').val());
        if (!amt || amt <= 0) { showToast('Enter a valid amount.', 'error'); return; }

        $btn.prop('disabled', true).html('<i class="ti-reload mr-1"></i> Saving…');

        $.ajax({
            url    : addPayUrl,
            method : 'POST',
            data   : {
                _token       : csrfToken2,
                amount       : amt,
                payment_mode : $('#payModeInput').val(),
                paid_on      : $('#payDateInput').val(),
                reference    : $('#payRefInput').val(),
            },
            success: function (r) {
                if (!r.success) { showToast('Save failed.', 'error'); return; }

                /* Update summary */
                updatePayUI(r.total_collected, r.payment_status);

                /* Prepend new row to history */
                var modeStr = r.payment.payment_mode ? ' &bull; ' + r.payment.payment_mode.charAt(0).toUpperCase() + r.payment.payment_mode.slice(1) : '';
                var refStr  = r.payment.reference   ? ' &bull; ' + r.payment.reference : '';
                var $row = $(
                    '<div class="pay-hist-row" data-id="' + r.payment.id + '" ' +
                    'style="display:flex;align-items:center;gap:8px;padding:9px 10px;background:#f7f8fc;border-radius:8px;margin-bottom:6px;border-left:3px solid #48bb78;">' +
                    '<div style="flex:1;min-width:0;">' +
                    '<div style="font-size:13px;font-weight:800;color:#1a2340;">₹' + r.payment.amount_fmt + '</div>' +
                    '<div style="font-size:10px;color:#8a94a6;margin-top:1px;">' + r.payment.paid_on + modeStr + refStr + '</div>' +
                    '</div>' +
                    '<button type="button" class="pay-delete-btn" data-id="' + r.payment.id + '" ' +
                    'style="background:#fff5f5;color:#e53e3e;border:none;border-radius:6px;width:26px;height:26px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;font-size:12px;">' +
                    '<i class="ti-trash"></i></button></div>'
                );
                $('#payHistoryEmpty').hide();
                $('#payHistoryList').prepend($row);

                /* Update count */
                var cnt = $('#payHistoryList .pay-hist-row').length;
                $('#payHistoryCount').text(cnt + ' ' + (cnt === 1 ? 'entry' : 'entries'));

                /* Reset form */
                $('#payAmountInput').val('').focus();
                $('#payRefInput').val('');

                showToast('Payment of ₹' + r.payment.amount_fmt + ' recorded!', 'success');
            },
            error: function (xhr) {
                var msg = 'Save failed.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                showToast(msg, 'error');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="ti-plus mr-1"></i> Add Payment');
            }
        });
    });

    /* ── Delete payment ── */
    $(document).on('click', '.pay-delete-btn', function () {
        var pid  = $(this).data('id');
        var $row = $(this).closest('.pay-hist-row');
        if (!confirm('Remove this payment entry?')) return;

        $.ajax({
            url    : deletePayUrlTpl + pid,
            method : 'POST',
            data   : { _token: csrfToken2, _method: 'DELETE' },
            success: function (r) {
                if (!r.success) { showToast('Delete failed.', 'error'); return; }
                $row.fadeOut(200, function () {
                    $(this).remove();
                    var cnt = $('#payHistoryList .pay-hist-row').length;
                    $('#payHistoryCount').text(cnt + ' ' + (cnt === 1 ? 'entry' : 'entries'));
                    if (cnt === 0) $('#payHistoryEmpty').show();
                });
                updatePayUI(r.total_collected, r.payment_status);
                showToast('Payment removed.', 'success');
            },
            error: function () { showToast('Delete failed.', 'error'); }
        });
    });

});
</script>
@endpush
