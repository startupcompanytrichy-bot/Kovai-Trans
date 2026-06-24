@extends('layouts.app')

@section('content')

@php
$statusConfig = [
    'planned'   => ['label'=>'Planned',   'color'=>'#667eea','bg'=>'#eef2ff','icon'=>'ti-clipboard'],
    'running'   => ['label'=>'Running',   'color'=>'#f6ad55','bg'=>'#fffbeb','icon'=>'ti-control-forward'],
    'completed' => ['label'=>'Completed', 'color'=>'#48bb78','bg'=>'#f0fff4','icon'=>'ti-check-box'],
    'cancelled' => ['label'=>'Cancelled', 'color'=>'#fc8181','bg'=>'#fff5f5','icon'=>'ti-close'],
];
$payConfig = [
    'pending'   => ['label'=>'Pending',   'color'=>'#fc8181','bg'=>'#fff5f5'],
    'partial'   => ['label'=>'Partial',   'color'=>'#f6ad55','bg'=>'#fffbeb'],
    'completed' => ['label'=>'Collected', 'color'=>'#48bb78','bg'=>'#f0fff4'],
];
@endphp

<style>
.td-page { background: #f4f6fb; }

/* ── Compact header ───────────────────────────────────────────────── */
.td-page-header {
    background: linear-gradient(135deg, #1a2340 0%, #2d3a5e 60%, #667eea 100%);
    border-radius: 10px; padding: 14px 20px; color: #fff;
    margin-bottom: 16px; position: relative; overflow: hidden;
}
.td-page-header::before { content:''; position:absolute; top:-40px; right:-40px; width:140px; height:140px; background:rgba(255,255,255,.05); border-radius:50%; }
.td-page-header h4 { font-size:16px; font-weight:800; margin:0 0 2px; }
.td-page-header .sub { font-size:12px; opacity:.75; }

/* ── Compact stats grid ───────────────────────────────────────────── */
.td-stats-grid { display:grid; grid-template-columns:repeat(8,1fr); gap:10px; margin-bottom:14px; }
.td-stat {
    background:#fff; border-radius:10px; padding:10px 12px;
    box-shadow:0 1px 6px rgba(0,0,0,.06);
    display:flex; align-items:center; gap:10px;
    border-left:3px solid transparent;
}
.td-stat .ts-icon { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; }
.td-stat .ts-label { font-size:10px; font-weight:700; color:#8a94a6; text-transform:uppercase; letter-spacing:.3px; white-space:nowrap; }
.td-stat .ts-value { font-size:18px; font-weight:800; color:#1a2340; line-height:1.1; }

/* ── Finance strip ────────────────────────────────────────────────── */
.td-finance-strip { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:14px; }
.td-fin-card { background:#fff; border-radius:10px; padding:10px 14px; box-shadow:0 1px 6px rgba(0,0,0,.06); display:flex; align-items:center; gap:10px; }
.td-fin-card .tfc-icon { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.td-fin-card .tfc-label { font-size:10px; font-weight:700; color:#8a94a6; text-transform:uppercase; letter-spacing:.3px; }
.td-fin-card .tfc-value { font-size:17px; font-weight:800; color:#1a2340; }

/* ── Filter bar ───────────────────────────────────────────────────── */
.td-filter-bar { background:#fff; border-radius:10px; padding:12px 16px; box-shadow:0 1px 6px rgba(0,0,0,.06); margin-bottom:12px; display:flex; align-items:center; gap:8px; }
.td-filter-bar .form-control { min-height:38px; font-size:13px; border-color:#e2e8f0; border-radius:8px; }
.td-search-wrap { flex:1; min-width:140px; position:relative; }
.td-search-wrap .ti-search { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#b0bac9; font-size:13px; }
.td-search-wrap input { padding-left:30px; }
.td-status-filters { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:12px; }
.td-sf-pill { display:inline-flex; align-items:center; gap:5px; padding:4px 11px; border-radius:20px; font-size:11px; font-weight:600; cursor:pointer; border:2px solid transparent; transition:all .15s; background:#f4f6fb; color:#8a94a6; }
.td-sf-pill.active { border-color:currentColor; }
.td-sf-pill .sf-dot { width:6px; height:6px; border-radius:50%; }

/* ── Invoice type modal ──────────────────────────────────────────── */
.inv-type-card { border:2px solid #e2e8f0; border-radius:12px; padding:16px 14px; cursor:pointer; transition:all .2s; text-align:center; }
.inv-type-card:hover { border-color:#667eea; background:#f4f6ff; }
.inv-type-card.selected { border-color:#667eea; background:#eef2ff; }
.inv-type-card .itc-icon { font-size:26px; margin-bottom:6px; }
.inv-type-card .itc-title { font-weight:800; font-size:13px; color:#1a2340; margin-bottom:2px; }
.inv-type-card .itc-tax { font-size:11px; color:#667eea; font-weight:700; }
.inv-type-card .itc-desc { font-size:10px; color:#8a94a6; margin-top:3px; }

/* invoiced row tint */
tr.invoiced-row td { background:#f0fff4 !important; }
tr.invoiced-row td:first-child { border-left:3px solid #48bb78; }

/* ── Table ────────────────────────────────────────────────────────── */
.td-table-card { background:#fff; border-radius:10px; box-shadow:0 1px 6px rgba(0,0,0,.06); overflow:hidden; }
.td-table-header { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid #f0f2f7; background:#fafbff; flex-wrap:wrap; gap:8px; }
.td-table-header h6 { margin:0; font-size:13px; font-weight:700; color:#1a2340; }
.td-table-wrap { overflow-x:auto; }
#tripsTable { min-width:900px; margin-bottom:0; }
#tripsTable th, #tripsTable td { height:48px; padding:7px 10px; vertical-align:middle; border-color:#f0f2f7; font-size:13px; }
#tripsTable th { background:#f8fafc; color:#14213d; font-weight:800; font-size:11px; text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; position:sticky; top:0; z-index:2; }
#tripsTable .trip-row { cursor:pointer; }
#tripsTable .trip-row:hover td { background:#f4f7ff; }
.td-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:20px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.3px; }

/* action icon buttons */
.td-action-btns { display:inline-flex; gap:4px; align-items:center; }
.td-icon-btn { width:30px; height:30px; border-radius:7px; border:none; display:inline-flex; align-items:center; justify-content:center; font-size:13px; cursor:pointer; transition:all .15s; }
.td-icon-btn.edit  { background:#eef2ff; color:#667eea; }
.td-icon-btn.edit:hover  { background:#667eea; color:#fff; }
.td-icon-btn.del   { background:#fff5f5; color:#e53e3e; }
.td-icon-btn.del:hover   { background:#e53e3e; color:#fff; }

/* row checkbox */
.td-row-check { width:16px; height:16px; cursor:pointer; accent-color:#667eea; }

/* pagination */
.td-pagination { display:flex; }
.td-page-btn { transition:all .15s; }
.td-page-btn:hover:not(:disabled) { background:#eef2ff; border-color:#667eea; color:#667eea; }
.td-page-btn.active { background:#667eea; border-color:#667eea; color:#fff; font-weight:700; }
.td-page-btn:disabled { opacity:.4; cursor:default; }

/* bulk action bar */
.td-bulk-bar {
    display:none; align-items:center; gap:10px;
    padding:8px 14px; background:#1a2340; border-radius:8px;
    color:#fff; font-size:13px; font-weight:600;
}
.td-bulk-bar.show { display:flex; }
.td-bulk-bar .bb-count { background:rgba(255,255,255,.2); padding:2px 10px; border-radius:20px; font-size:12px; }

/* ── Select2 in filter bar (match native style exactly) ──────────── */
.td-filter-bar .select2-container .select2-selection--single {
    height: 38px !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    padding: 0 30px 0 10px !important;
    background: #fff !important;
    font-size: 13px !important;
}
.td-filter-bar .select2-container--default .select2-selection--single .select2-selection__rendered {
    font-size: 13px !important;
    color: #1e293b !important;
    line-height: 1.2 !important;
    padding: 0 !important;
}
/* ── Select2 for perPage (compact) ───────────────────────────────── */
#perPage + .select2-container .select2-selection--single {
    height: 32px !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    padding: 0 24px 0 8px !important;
    font-size: 12px !important;
}
#perPage + .select2-container--default .select2-selection--single .select2-selection__rendered {
    font-size: 12px !important;
    color: #1e293b !important;
    line-height: 1 !important;
    padding: 0 !important;
}
#perPage + .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 30px !important;
    right: 2px !important;
}

.td-filter-bar .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
    right: 6px !important;
}

@media(max-width:1199.98px) { .td-stats-grid { grid-template-columns:repeat(4,1fr); } }
@media(max-width:767.98px)  { .td-stats-grid { grid-template-columns:repeat(2,1fr); } .td-finance-strip { grid-template-columns:1fr; } }
@media print {
    .pcoded-navbar,.pcoded-header,.pcoded-footer,.td-filter-bar,.td-table-header,
    .td-status-filters,.td-stats-grid,.td-finance-strip,.td-page-header,.modal-header,
    .btn-close,.modal-footer { display:none!important; }
    .modal-content { box-shadow:none!important; border:none!important; }
    .modal { position:static!important; }
    .modal-dialog { max-width:100%!important; margin:0!important; }
}
</style>

<div class="pcoded-inner-content td-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

{{-- PAGE HEADER (compact) --}}
<div class="td-page-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;letter-spacing:.5px;margin-bottom:6px;">
                <i class="ti-location-arrow"></i> Trip Management
            </div>
            <h4>Trip Dashboard</h4>
            <div class="sub">Manage trips, track finances and collections.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('trip.create') }}" class="btn btn-sm" style="background:#fff;color:#667eea;font-weight:700;border-radius:8px;padding:7px 18px;box-shadow:0 2px 10px rgba(0,0,0,.15);">
                <i class="ti-plus mr-1"></i> New Trip
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

{{-- STAT CARDS (compact 8-col grid) --}}
<div class="td-stats-grid">
@php
$statCards = [
    ['label'=>'Total',      'value'=>$stats['total'],       'icon'=>'ti-location-arrow','color'=>'#667eea','bg'=>'#eef2ff','border'=>'#667eea'],
    ['label'=>'Active',     'value'=>$stats['active'],      'icon'=>'ti-control-forward','color'=>'#f6ad55','bg'=>'#fffbeb','border'=>'#f6ad55'],
    ['label'=>'Running',    'value'=>$stats['running'],     'icon'=>'ti-truck',         'color'=>'#7c3aed','bg'=>'#f5f3ff','border'=>'#7c3aed'],
    ['label'=>'Completed',  'value'=>$stats['completed'],   'icon'=>'ti-check-box',     'color'=>'#48bb78','bg'=>'#f0fff4','border'=>'#48bb78'],
    ['label'=>'Cancelled',  'value'=>$stats['cancelled'],   'icon'=>'ti-close',         'color'=>'#fc8181','bg'=>'#fff5f5','border'=>'#fc8181'],
    ['label'=>'Profit',     'value'=>$stats['profit'],      'icon'=>'ti-arrow-up',      'color'=>'#38a169','bg'=>'#f0fff4','border'=>'#38a169'],
    ['label'=>'Loss',       'value'=>$stats['loss'],        'icon'=>'ti-arrow-down',    'color'=>'#e53e3e','bg'=>'#fff5f5','border'=>'#e53e3e'],
    ['label'=>'Pending Col','value'=>$stats['pending_col'], 'icon'=>'ti-credit-card',   'color'=>'#d97706','bg'=>'#fffbeb','border'=>'#d97706'],
];
@endphp
@foreach($statCards as $sc)
<div class="td-stat" style="border-left-color:{{ $sc['border'] }};">
    <div class="ts-icon" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};"><i class="{{ $sc['icon'] }}"></i></div>
    <div>
        <div class="ts-label">{{ $sc['label'] }}</div>
        <div class="ts-value" style="color:{{ $sc['color'] }};">{{ $sc['value'] }}</div>
    </div>
</div>
@endforeach
</div>

{{-- FINANCE STRIP --}}
<div class="td-finance-strip">
    <div class="td-fin-card">
        <div class="tfc-icon" style="background:#eef2ff;color:#4338ca;"><i class="ti-money"></i></div>
        <div><div class="tfc-label">Total Freight</div><div class="tfc-value" style="color:#4338ca;">₹{{ number_format($stats['total_freight'],0) }}</div></div>
    </div>
    <div class="td-fin-card">
        <div class="tfc-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-check"></i></div>
        <div><div class="tfc-label">Total Collected</div><div class="tfc-value" style="color:#38a169;">₹{{ number_format($stats['total_collected'],0) }}</div></div>
    </div>
    <div class="td-fin-card">
        <div class="tfc-icon" style="background:#fff5f5;color:#e53e3e;"><i class="ti-alert"></i></div>
        <div><div class="tfc-label">Outstanding</div><div class="tfc-value" style="color:#e53e3e;">₹{{ number_format($stats['total_outstanding'],0) }}</div></div>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="td-filter-bar">
    <div class="td-search-wrap">
        <i class="ti-search"></i>
        <input type="text" id="tripSearch" class="form-control" placeholder="Search trip no, party, truck, route...">
    </div>
    <select id="filterStatus" class="form-control" style="min-width:120px;max-width:150px;">
        <option value="">All Status</option>
        <option value="planned">Planned</option>
        <option value="running">Running</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
    </select>
    <select id="filterPayment" class="form-control" style="min-width:120px;max-width:150px;">
        <option value="">All Payments</option>
        <option value="pending">Pending</option>
        <option value="partial">Partial</option>
        <option value="completed">Collected</option>
    </select>
    <input type="date" id="filterDateFrom" class="form-control" style="max-width:145px;" title="From Date">
    <input type="date" id="filterDateTo"   class="form-control" style="max-width:145px;" title="To Date">
    <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm" style="white-space:nowrap;border-radius:8px;">
        <i class="ti-close mr-1"></i> Clear
    </button>
</div>

{{-- STATUS QUICK FILTERS --}}
<div class="td-status-filters">
    <span class="td-sf-pill active" data-filter="all" style="color:#667eea;border-color:#667eea;background:#eef2ff;">
        <span class="sf-dot" style="background:#667eea;"></span> All ({{ $stats['total'] }})
    </span>
    @foreach($statusConfig as $sk => $sv)
    <span class="td-sf-pill" data-filter="{{ $sk }}" style="color:{{ $sv['color'] }};">
        <span class="sf-dot" style="background:{{ $sv['color'] }};"></span>
        {{ $sv['label'] }} ({{ $trips->where('status', $sk)->count() }})
    </span>
    @endforeach
    <span class="td-sf-pill" id="pillInvoiceList" style="color:#38a169;cursor:pointer;"
        onclick="window.location.href='{{ route('invoice.index') }}'">
        <span class="sf-dot" style="background:#38a169;"></span>
        <i class="ti-receipt" style="font-size:9px;"></i>
        Invoice List ({{ $trips->where('invoice_status', 'invoiced')->count() }})
    </span></div>

{{-- TRIPS TABLE --}}
<div class="td-table-card">
    <div class="td-table-header">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <h6><i class="ti-list mr-1" style="color:#667eea;"></i>All Trips
                <span id="tripCountBadge" style="background:#eef2ff;color:#667eea;font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;margin-left:6px;">{{ $trips->count() }}</span>
            </h6>
            {{-- Bulk action bar (hidden until rows selected) --}}
            <div class="td-bulk-bar" id="bulkBar">
                <span class="bb-count" id="bulkCount">0 selected</span>
                <button type="button" id="btnGenerateInvoice" class="btn btn-sm" style="background:#667eea;color:#fff;border-radius:7px;font-weight:600;"
                    data-toggle="modal" data-target="#invoiceTypeModal">
                    <i class="ti-file mr-1"></i> Generate Invoice
                </button>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <select id="perPage" class="form-control" style="min-width:80px;max-width:100px;min-height:32px;font-size:12px;border-radius:8px;">
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="75">75</option>
                <option value="100">100</option>
                <option value="0">All</option>
            </select>
            <a href="{{ route('reports.trips') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;">
                <i class="ti-bar-chart mr-1"></i> Report
            </a>
            <a href="{{ route('trip.create') }}" class="btn btn-primary btn-sm" style="border-radius:8px;">
                <i class="ti-plus mr-1"></i> Add Trip
            </a>
        </div>
    </div>

    <div class="td-table-wrap">
        <table class="table table-hover" id="tripsTable">
            <thead>
                <tr>
                    <th style="width:36px;text-align:center;">
                        <input type="checkbox" id="checkAll" class="td-row-check" title="Select all">
                    </th>
                    <th style="width:36px;text-align:center;">#</th>
                    <th style="width:130px;">Trip No</th>
                    <th style="width:100px;">Date</th>
                    <th style="width:160px;">Party</th>
                    <th style="width:110px;">Vehicle</th>
                    <th style="width:200px;">Route</th>
                    <th style="width:110px;text-align:right;">Freight</th>
                    <th style="width:100px;text-align:center;">Payment</th>
                    <th style="width:100px;text-align:center;">Status</th>
                    <th style="width:70px;text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trips as $key => $trip)
                @php
                    $vehicleText = optional($trip->vehicle)->vehicle_number ?? '';
                    $partyText   = optional($trip->party)->company_name ?: optional($trip->party)->name;
                    $sc          = $statusConfig[$trip->status] ?? ['color'=>'#8a94a6','bg'=>'#f4f6fb','label'=>ucfirst($trip->status),'icon'=>'ti-more-alt'];
                    $pc          = $payConfig[$trip->payment_status ?? 'pending'] ?? ['color'=>'#8a94a6','bg'=>'#f4f6fb','label'=>'Pending'];
                @endphp
                <tr class="trip-row{{ $trip->invoice_status === 'invoiced' ? ' invoiced-row' : '' }}"
                    data-id="{{ $trip->id }}"
                    data-edit-url="{{ route('trip.edit', $trip->id) }}"
                    data-invoice-url="{{ route('invoice.trip', $trip->id) }}"
                    data-status="{{ $trip->status }}"
                    data-payment="{{ $trip->payment_status ?? 'pending' }}"
                    data-date="{{ $trip->trip_date ? $trip->trip_date->format('Y-m-d') : '' }}"
                    data-invoice-status="{{ $trip->invoice_status ?? 'not_invoiced' }}"
                    data-search="{{ strtolower($trip->trip_no . ' ' . $vehicleText . ' ' . $partyText . ' ' . $trip->from_location . ' ' . $trip->to_location . ' ' . $trip->status . ' ' . ($trip->invoice_no ?? '')) }}"
                    data-trip-no="{{ $trip->trip_no }}"
                    data-party="{{ $partyText }}"
                    data-vehicle="{{ $vehicleText }}"
                    data-from="{{ $trip->from_location }}"
                    data-to="{{ $trip->to_location }}"
                    data-freight="{{ $trip->freight_amount }}"
                    data-advance="{{ $trip->advance_amount }}"
                    data-outstanding="{{ $trip->outstanding_amount }}"
                    data-trip-date="{{ $trip->trip_date ? $trip->trip_date->format('d M Y') : '' }}"
                    data-status-label="{{ $sc['label'] }}"
                    data-pay-label="{{ $pc['label'] }}">
                    <td style="text-align:center;" onclick="event.stopPropagation();">
                        @if($trip->invoice_status === 'invoiced')
                            <input type="checkbox" class="td-row-check trip-check" data-id="{{ $trip->id }}"
                                disabled title="Already invoiced"
                                style="opacity:.3;cursor:not-allowed;">
                        @else
                            <input type="checkbox" class="td-row-check trip-check" data-id="{{ $trip->id }}">
                        @endif
                    </td>
                    <td style="text-align:center;color:#b0bac9;font-size:12px;">{{ $key+1 }}</td>
                    <td>
                        <strong style="color:#667eea;font-size:12px;">{{ $trip->trip_no }}</strong>
                        @if($trip->lr_no)<div style="font-size:10px;color:#b0bac9;">LR: {{ $trip->lr_no }}</div>@endif
                        @if($trip->invoice_status === 'invoiced')
                            <div style="font-size:10px;color:#38a169;font-weight:700;margin-top:2px;">
                                <i class="ti-receipt" style="font-size:9px;"></i> {{ $trip->invoice_no }}
                            </div>
                        @endif
                    </td>
                    <td style="font-size:12px;color:#596579;">{{ $trip->trip_date ? $trip->trip_date->format('d M Y') : '-' }}</td>
                    <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px;" title="{{ $partyText }}">{{ $partyText ?: '-' }}</td>
                    <td>
                        <div style="font-weight:600;font-size:12px;">{{ $vehicleText ?: '-' }}</div>
                        @if($trip->driver)<div style="font-size:10px;color:#8a94a6;">{{ $trip->driver->name }}</div>@endif
                    </td>
                    <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px;" title="{{ $trip->from_location }} → {{ $trip->to_location }}">
                        {{ $trip->from_location }} <i class="ti-arrow-right" style="font-size:9px;color:#b0bac9;"></i> {{ $trip->to_location }}
                    </td>
                    <td style="text-align:right;font-weight:700;font-size:12px;">₹{{ number_format($trip->freight_amount,0) }}</td>
                    <td style="text-align:center;">
                        <span class="td-badge" style="background:{{ $pc['bg'] }};color:{{ $pc['color'] }};">{{ $pc['label'] }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="td-badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">
                            <i class="{{ $sc['icon'] }}" style="font-size:8px;"></i> {{ $sc['label'] }}
                        </span>
                    </td>
                    <td style="text-align:center;" onclick="event.stopPropagation();">
                        <div class="td-action-btns">
                            <a href="{{ route('trip.edit', $trip->id) }}" class="td-icon-btn edit" title="Edit">
                                <i class="ti-pencil"></i>
                            </a>
                            <button type="button" class="td-icon-btn del" title="Delete"
                                onclick="showDeleteModal('deleteTripForm{{ $trip->id }}','{{ addslashes($trip->trip_no) }}','Trip')">
                                <i class="ti-trash"></i>
                            </button>
                        </div>
                        <form id="deleteTripForm{{ $trip->id }}" action="{{ route('trip.destroy', $trip->id) }}" method="POST" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" class="text-center py-5" style="color:#b0bac9;">
                    <i class="ti-location-arrow" style="font-size:36px;display:block;margin-bottom:10px;opacity:.4;"></i>
                    <div style="font-size:14px;font-weight:600;margin-bottom:4px;">No trips found</div>
                    <a href="{{ route('trip.create') }}" class="btn btn-primary btn-sm mt-2">Create your first trip</a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination footer --}}
    <div class="td-pagination" id="tdPagination" style="display:none;padding:10px 16px;border-top:1px solid #f0f2f7;background:#fafbff;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <div style="font-size:12px;color:#596579;">
            Showing <strong id="tdPageStart">0</strong>&ndash;<strong id="tdPageEnd">0</strong> of <strong id="tdTotalVisible">0</strong>
        </div>
        <div style="display:flex;align-items:center;gap:4px;">
            <button type="button" class="td-page-btn" id="tdPrevPage" style="padding:4px 10px;border:1px solid #e2e8f0;border-radius:6px;background:#fff;font-size:12px;cursor:pointer;color:#596579;" disabled>&laquo; Prev</button>
            <span id="tdPageNumbers" style="display:flex;align-items:center;gap:3px;"></span>
            <button type="button" class="td-page-btn" id="tdNextPage" style="padding:4px 10px;border:1px solid #e2e8f0;border-radius:6px;background:#fff;font-size:12px;cursor:pointer;color:#596579;" disabled>Next &raquo;</button>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════════
     INVOICE TYPE MODAL
═══════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="invoiceTypeModal" tabindex="-1" role="dialog" aria-labelledby="invoiceTypeModalLabel">
    <div class="modal-dialog modal-dialog-centered" style="max-width:560px;" role="document">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a2340,#667eea);border-radius:16px 16px 0 0;border:none;padding:18px 22px;">
                <h5 class="modal-title" id="invoiceTypeModalLabel" style="color:#fff;font-weight:800;font-size:15px;">
                    <i class="ti-receipt mr-2"></i> Select Invoice Type
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"
                    style="background:rgba(255,255,255,.2);border:none;width:28px;height:28px;border-radius:8px;color:#fff;font-size:16px;cursor:pointer;"
                    onclick="$('#invoiceTypeModal').modal('hide')">×</button>
            </div>
            <div class="modal-body" style="padding:22px;">
                <p style="font-size:12px;color:#8a94a6;margin-bottom:16px;">
                    <span id="invoiceSelCount" style="font-weight:700;color:#1a2340;">0 trips</span> selected.
                    Choose the invoice type to generate:
                </p>

                {{-- 3 Invoice Type Cards --}}
                <div class="row g-3" style="margin:0 -8px;">
                    <div class="col-4" style="padding:0 8px;">
                        <div class="inv-type-card selected" data-type="normal" onclick="selectInvType(this,'normal')">
                            <div class="itc-icon">🧾</div>
                            <div class="itc-title">Normal Invoice</div>
                            <div class="itc-tax">GST 18%</div>
                            <div class="itc-desc">CGST 9% + SGST 9%</div>
                        </div>
                    </div>
                    <div class="col-4" style="padding:0 8px;">
                        <div class="inv-type-card" data-type="rcm" onclick="selectInvType(this,'rcm')">
                            <div class="itc-icon">🔄</div>
                            <div class="itc-title">RCM Invoice</div>
                            <div class="itc-tax">Tax 5%</div>
                            <div class="itc-desc">Reverse Charge Mechanism</div>
                        </div>
                    </div>
                    <div class="col-4" style="padding:0 8px;">
                        <div class="inv-type-card" data-type="exempt" onclick="selectInvType(this,'exempt')">
                            <div class="itc-icon">🆓</div>
                            <div class="itc-title">Exempted Invoice</div>
                            <div class="itc-tax">Tax 0%</div>
                            <div class="itc-desc">GST Exempt Supply</div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="selectedInvType" value="normal">
                <input type="hidden" id="selectedInvTypeLabel" value="TAX INVOICE">

                <div style="margin-top:20px;padding:12px;background:#fffbeb;border-radius:8px;border:1px solid #fde68a;display:none;" id="invTypeNote">
                    <div id="invTypeNoteText" style="font-size:11.5px;color:#92400e;font-weight:600;"></div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f2f7;padding:12px 22px;gap:8px;justify-content:space-between;">
                <button type="button" onclick="$('#invoiceTypeModal').modal('hide')"
                    style="background:#f4f6fb;border:none;padding:8px 20px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;color:#596579;">
                    Cancel
                </button>
                <div style="display:flex;gap:8px;">
                    <!-- <button type="button" id="btnExcelDownload"
                        style="background:#1a7f4e;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                        <i class="ti-export mr-1"></i> Excel
                    </button> -->
                    <button type="button" id="btnPdfDownload"
                        style="background:#cc0000;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                        <i class="ti-printer mr-1"></i> PDF
                    </button>
                    <button type="button" id="btnInvoiceGenerate"
                        style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;border:none;padding:8px 22px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
                        <i class="ti-file mr-1"></i> Generate Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

</div></div></div></div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    /* ── Select2 for filter dropdowns ──────────────────────────────── */
    $('#filterStatus, #filterPayment').select2({
        minimumResultsForSearch: -1,
        width: '150px',
    });
    $('#perPage').select2({
        minimumResultsForSearch: -1,
        width: '80px',
    });

    /* ── Row click → edit ─────────────────────────────────────────── */
    $('#tripsTable').on('click', '.trip-row', function () {
        window.location.href = $(this).data('edit-url');
    });

    /* ── Checkbox logic ───────────────────────────────────────────── */
    function updateBulkBar() {
        var count = $('.trip-check:checked:not(:disabled)').length;
        if (count > 0) {
            $('#bulkBar').addClass('show');
            $('#bulkCount').text(count + ' selected');
            $('#invoiceSelCount').text(count + ' trip' + (count > 1 ? 's' : ''));
        } else {
            $('#bulkBar').removeClass('show');
        }
    }

    $('#checkAll').on('change', function () {
        $('#tripsTable tbody .trip-row:visible .trip-check:not(:disabled)').prop('checked', this.checked);
        updateBulkBar();
    });

    $(document).on('change', '.trip-check:not(:disabled)', function () {
        var total   = $('#tripsTable tbody .trip-row:visible .trip-check:not(:disabled)').length;
        var checked = $('#tripsTable tbody .trip-row:visible .trip-check:not(:disabled):checked').length;
        $('#checkAll').prop('indeterminate', checked > 0 && checked < total);
        $('#checkAll').prop('checked', total > 0 && checked === total);
        updateBulkBar();
    });

    /* ── Filter logic ─────────────────────────────────────────────── */
    var quickFilterActive = 'all';

    function applyFilters() {
        var term        = $('#tripSearch').val().toLowerCase();
        var status      = $('#filterStatus').val();
        var payment     = $('#filterPayment').val();
        var dateFrom    = $('#filterDateFrom').val();
        var dateTo      = $('#filterDateTo').val();
        var visible = 0;

        $('.trip-row').each(function () {
            var $row = $(this);
            var ok = true;
            if (term && !($row.attr('data-search') || '').toLowerCase().includes(term)) ok = false;
            if (status  && $row.attr('data-status')  !== status)  ok = false;
            if (payment && $row.attr('data-payment') !== payment) ok = false;

            // Quick pill filters (status only)
            if (quickFilterActive !== 'all') {
                if ($row.attr('data-status') !== quickFilterActive) ok = false;
            }

            var rowDate = $row.attr('data-date') || '';
            if (dateFrom && rowDate && rowDate < dateFrom) ok = false;
            if (dateTo   && rowDate && rowDate > dateTo)   ok = false;
            $row.toggle(ok);
            if (ok) visible++;
        });
        $('#tripCountBadge').text(visible);
        $('#tripsTable tbody .trip-row:hidden .trip-check:not(:disabled)').prop('checked', false);
        updateBulkBar();
    }

    $('#tripSearch, #filterStatus, #filterPayment, #filterDateFrom, #filterDateTo').on('input change', function () {
        applyFilters();
        renderPagination();
    });

    $('#clearFilters').on('click', function () {
        $('#tripSearch').val('');
        $('#filterStatus, #filterPayment').val('');
        $('#filterDateFrom, #filterDateTo').val('');
        quickFilterActive = 'all';
        $('.td-sf-pill[data-filter]').removeClass('active').css({'border-color':'transparent','background':'#f4f6fb'});
        $('.td-sf-pill[data-filter="all"]').addClass('active').css({'border-color':'#667eea','background':'#eef2ff'});
        applyFilters();
        renderPagination();
    });

    /* ── Quick status filter pills (all except Invoice List) ────── */
    var pillColors = {all:'#667eea',planned:'#667eea',running:'#f6ad55',completed:'#48bb78',cancelled:'#fc8181'};
    var pillBgs    = {all:'#eef2ff',planned:'#eef2ff',running:'#fffbeb',completed:'#f0fff4',cancelled:'#fff5f5'};
    $('.td-sf-pill[data-filter]').on('click', function () {
        var filter = $(this).data('filter');
        quickFilterActive = filter;
        $('.td-sf-pill[data-filter]').removeClass('active').css({'border-color':'transparent','background':'#f4f6fb'});
        $(this).addClass('active').css({'border-color':pillColors[filter]||'#667eea','background':pillBgs[filter]||'#eef2ff'});
        $('#filterStatus').val(filter === 'all' ? '' : filter);
        applyFilters();
        renderPagination();
    });

    /* ── Invoice type modal open ──────────────────────────────────── */
    // Sync count when modal opens
    $('#invoiceTypeModal').on('show.bs.modal', function () {
        var count = $('.trip-check:checked').length;
        $('#invoiceSelCount').text(count + ' trip' + (count > 1 ? 's' : ''));
    });

    /* ── Generate Invoice button ─────────────────────────────────── */
    $('#btnInvoiceGenerate').on('click', function () {
        var $checked = $('.trip-check:checked');
        var ids = [];
        $checked.each(function () { ids.push($(this).data('id')); });
        if (!ids.length) { alert('Please select at least one trip.'); return; }

        var type      = $('#selectedInvType').val() || 'normal';
        var typeLabel = $('#selectedInvTypeLabel').val() || 'TAX INVOICE';

        // POST to generate in new tab (saves invoice + opens invoice view)
        var form = $('<form method="POST" action="{{ route('invoice.generate') }}" target="_blank"></form>');
        form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
        form.append('<input type="hidden" name="invoice_type" value="' + type + '">');
        form.append('<input type="hidden" name="invoice_type_label" value="' + typeLabel + '">');
        ids.forEach(function (id) {
            form.append('<input type="hidden" name="trip_ids[]" value="' + id + '">');
        });
        $('body').append(form);
        form.submit();
        form.remove();

        // Fade out selected rows
        $checked.each(function () {
            $(this).closest('tr.trip-row').fadeOut(300, function () { $(this).remove(); });
        });
        setTimeout(function () {
            $('#tripCountBadge').text($('#tripsTable tbody .trip-row').length);
        }, 400);

        $('#invoiceTypeModal').modal('hide');
        $('#bulkBar').removeClass('show');
        $('#checkAll').prop('checked', false).prop('indeterminate', false);
    });

    /* ── Excel Download ──────────────────────────────────────────── */
    $('#btnExcelDownload').on('click', function () {
        var ids  = [];
        $('.trip-check:checked').each(function () { ids.push($(this).data('id')); });
        if (!ids.length) { alert('Please select at least one trip.'); return; }

        var type      = $('#selectedInvType').val() || 'normal';
        var typeLabel = $('#selectedInvTypeLabel').val() || 'TAX INVOICE';
        var form = $('<form method="POST" action="{{ route('invoice.excel') }}"></form>');
        form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
        form.append('<input type="hidden" name="invoice_type" value="' + type + '">');
        form.append('<input type="hidden" name="invoice_type_label" value="' + typeLabel + '">');
        ids.forEach(function (id) {
            form.append('<input type="hidden" name="trip_ids[]" value="' + id + '">');
        });
        $('body').append(form);
        form.submit();
        form.remove();
        $('#invoiceTypeModal').modal('hide');
    });

    /* ── PDF Download ────────────────────────────────────────────── */
    $('#btnPdfDownload').on('click', function () {
        var ids  = [];
        $('.trip-check:checked').each(function () { ids.push($(this).data('id')); });
        if (!ids.length) { alert('Please select at least one trip.'); return; }

        var type      = $('#selectedInvType').val() || 'normal';
        var typeLabel = $('#selectedInvTypeLabel').val() || 'TAX INVOICE';
        var form = $('<form method="POST" action="{{ route('invoice.pdf') }}" target="_blank"></form>');
        form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
        form.append('<input type="hidden" name="invoice_type" value="' + type + '">');
        form.append('<input type="hidden" name="invoice_type_label" value="' + typeLabel + '">');
        ids.forEach(function (id) {
            form.append('<input type="hidden" name="trip_ids[]" value="' + id + '">');
        });
        $('body').append(form);
        form.submit();
        form.remove();
        $('#invoiceTypeModal').modal('hide');
    });

    /* ── Pagination ────────────────────────────────────────────────── */
    var currentPage = 1;
    var perPage = 25;

    function getVisibleRows() {
        return $('#tripsTable tbody .trip-row:visible');
    }

    function renderPagination() {
        var $visible = getVisibleRows();
        var total = $visible.length;
        perPage = parseInt($('#perPage').val()) || 0;

        if (total === 0 || perPage === 0 || perPage >= total) {
            $('#tdPagination').hide();
            $visible.show();
            return;
        }

        $('#tdPagination').css('display','flex');
        var totalPages = Math.ceil(total / perPage);
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        var start = (currentPage - 1) * perPage;
        var end = Math.min(start + perPage, total);

        // Show only rows for current page
        $visible.each(function (i) {
            $(this).toggle(i >= start && i < end);
        });

        $('#tdPageStart').text(total === 0 ? 0 : start + 1);
        $('#tdPageEnd').text(end);
        $('#tdTotalVisible').text(total);

        $('#tdPrevPage').prop('disabled', currentPage <= 1);
        $('#tdNextPage').prop('disabled', currentPage >= totalPages);

        // Build page numbers
        var $nums = $('#tdPageNumbers').empty();
        var maxButtons = 7;
        var half = Math.floor(maxButtons / 2);
        var from = Math.max(1, currentPage - half);
        var to = Math.min(totalPages, currentPage + half);
        if (to - from + 1 < maxButtons) {
            if (from === 1) to = Math.min(totalPages, from + maxButtons - 1);
            else if (to === totalPages) from = Math.max(1, to - maxButtons + 1);
        }

        if (from > 1) {
            $nums.append('<button type="button" class="td-page-btn" style="padding:4px 8px;border:1px solid #e2e8f0;border-radius:6px;background:#fff;font-size:12px;cursor:pointer;color:#596579;" data-page="1">1</button>');
            if (from > 2) $nums.append('<span style="padding:0 4px;color:#b0bac9;font-size:11px;">...</span>');
        }
        for (var p = from; p <= to; p++) {
            var active = p === currentPage ? ' active' : '';
            $nums.append('<button type="button" class="td-page-btn' + active + '" style="padding:4px 8px;border:1px solid #e2e8f0;border-radius:6px;background:#fff;font-size:12px;cursor:pointer;color:#596579;" data-page="' + p + '">' + p + '</button>');
        }
        if (to < totalPages) {
            if (to < totalPages - 1) $nums.append('<span style="padding:0 4px;color:#b0bac9;font-size:11px;">...</span>');
            $nums.append('<button type="button" class="td-page-btn" style="padding:4px 8px;border:1px solid #e2e8f0;border-radius:6px;background:#fff;font-size:12px;cursor:pointer;color:#596579;" data-page="' + totalPages + '">' + totalPages + '</button>');
        }
    }

    // Pagination event handlers
    $(document).on('click', '#tdPageNumbers .td-page-btn', function () {
        currentPage = parseInt($(this).data('page'));
        renderPagination();
    });

    $('#tdPrevPage').on('click', function () {
        if (currentPage > 1) { currentPage--; renderPagination(); }
    });

    $('#tdNextPage').on('click', function () {
        var total = getVisibleRows().length;
        var pages = Math.ceil(total / perPage);
        if (currentPage < pages) { currentPage++; renderPagination(); }
    });

    $('#perPage').on('change', function () {
        currentPage = 1;
        applyFilters();
        renderPagination();
    });

    // Init
    renderPagination();

});

/* ── Invoice type card selector ───────────────────────────────────── */
var invTypeNotes = {
    normal:  '🧾 Normal Tax Invoice: CGST 9% + SGST 9% = GST 18% on freight amount.',
    rcm:     '🔄 RCM Invoice: Tax 5% under Reverse Charge Mechanism. Recipient pays GST.',
    exempt:  '🆓 Exempted Invoice: Zero-rated supply. No GST applicable (0%).'
};
var invTypeLabels = {
    normal: 'TAX INVOICE',
    rcm:    'RCM INVOICE',
    exempt: 'EXEMPTED INVOICE'
};
function selectInvType(el, type) {
    $('.inv-type-card').removeClass('selected');
    $(el).addClass('selected');
    $('#selectedInvType').val(type);
    $('#selectedInvTypeLabel').val(invTypeLabels[type] || 'TAX INVOICE');
    $('#invTypeNote').show();
    $('#invTypeNoteText').text(invTypeNotes[type] || '');
}
// Show note on load
$(document).ready(function () {
    $('#invTypeNote').show();
    $('#invTypeNoteText').text(invTypeNotes['normal']);
});
</script>
@endpush
