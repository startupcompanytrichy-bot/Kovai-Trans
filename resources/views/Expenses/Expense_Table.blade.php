@extends('layouts.app')

@section('content')
@php
$statusCfg = [
    'pending'  => ['label'=>'Pending',  'color'=>'#d97706','bg'=>'#fffbeb'],
    'approved' => ['label'=>'Approved', 'color'=>'#38a169','bg'=>'#f0fff4'],
    'rejected' => ['label'=>'Rejected', 'color'=>'#e53e3e','bg'=>'#fff5f5'],
];
@endphp
<style>
.exp-page{background:#f4f6fb;}
.exp-header{background:linear-gradient(135deg,#e53e3e 0%,#c53030 60%,#9b2c2c 100%);border-radius:10px;padding:14px 20px;color:#fff;margin-bottom:16px;position:relative;overflow:hidden;}
.exp-header::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.07);border-radius:50%;}
.exp-header h4{font-size:16px;font-weight:800;margin:0 0 2px;}
.exp-header .sub{font-size:12px;opacity:.8;}
.exp-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;}
.exp-stat{background:#fff;border-radius:12px;padding:16px;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;align-items:center;gap:12px;border-left:4px solid transparent;}
.exp-stat:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.1);transition:all .2s;}
.exp-stat .es-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
.exp-stat .es-label{font-size:11px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.4px;}
.exp-stat .es-value{font-size:22px;font-weight:800;color:#1a2340;line-height:1.1;}
.exp-filter{background:#fff;border-radius:12px;padding:14px 18px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:16px;display:flex;align-items:center;gap:10px;}
.exp-filter .form-control{min-height:40px;font-size:13px;border-color:#e2e8f0;border-radius:8px;}
.exp-filter .form-control:focus{border-color:#e53e3e;box-shadow:0 0 0 2px rgba(229,62,62,.12);}
.exp-search-wrap{flex:1;min-width:140px;position:relative;}

/* ── Select2 in expense filter bar ───────────────────────────────── */
.exp-filter .select2-container .select2-selection--single {
    height: 40px !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    padding: 0 30px 0 10px !important;
    background: #fff !important;
    font-size: 13px !important;
}
.exp-filter .select2-container--default .select2-selection--single .select2-selection__rendered {
    font-size: 13px !important;
    color: #1e293b !important;
    line-height: 1.2 !important;
    padding: 0 !important;
}
.exp-filter .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    right: 6px !important;
}
.exp-search-wrap .ti-search{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#b0bac9;font-size:14px;}
.exp-search-wrap input{padding-left:34px;}
.exp-card{background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;}
.exp-card-header{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f0f2f7;background:#fafbff;flex-wrap:wrap;gap:8px;}
.exp-card-header h6{margin:0;font-size:14px;font-weight:700;color:#1a2340;}
.exp-table-wrap{overflow-x:auto;}
#expTable{min-width:900px;margin-bottom:0;}
#expTable th,#expTable td{height:50px;padding:8px 14px;vertical-align:middle;border-color:#f0f2f7;font-size:13px;}
#expTable th{background:#f8fafc;color:#14213d;font-weight:800;font-size:11px;text-transform:uppercase;letter-spacing:.4px;}
#expTable .exp-row:hover td{background:#fff8f8;}
.exp-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;}
.cat-chip{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;}
.td-action-btns{display:inline-flex;gap:4px;align-items:center;}
.td-icon-btn{width:30px;height:30px;border-radius:7px;border:none;display:inline-flex;align-items:center;justify-content:center;font-size:13px;cursor:pointer;transition:all .15s;text-decoration:none;}
.td-icon-btn.edit   {background:#eef2ff;color:#667eea;} .td-icon-btn.edit:hover   {background:#667eea;color:#fff;}
.td-icon-btn.approve{background:#f0fff4;color:#38a169;} .td-icon-btn.approve:hover{background:#38a169;color:#fff;}
.td-icon-btn.reject {background:#fffbeb;color:#d97706;} .td-icon-btn.reject:hover {background:#d97706;color:#fff;}
.td-icon-btn.bill   {background:#f0f9ff;color:#0369a1;} .td-icon-btn.bill:hover   {background:#0369a1;color:#fff;}
.td-icon-btn.del    {background:#fff5f5;color:#e53e3e;} .td-icon-btn.del:hover    {background:#e53e3e;color:#fff;}
.exp-fab{position:fixed;bottom:28px;right:28px;z-index:999;width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#e53e3e,#c53030);color:#fff;font-size:24px;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px rgba(229,62,62,.5);text-decoration:none;transition:all .2s;}
.exp-fab:hover{transform:scale(1.1) rotate(90deg);color:#fff;}
@media(max-width:767.98px){.exp-stats{grid-template-columns:repeat(2,1fr);}.exp-filter{flex-direction:column;align-items:stretch;}}

/* ── Payment Panel ── */
.pay-backdrop{display:none;position:fixed;inset:0;background:rgba(26,35,64,.45);z-index:1040;backdrop-filter:blur(2px);}
.pay-backdrop.show{display:block;}
.pay-panel{position:fixed;top:0;right:0;bottom:0;width:100%;max-width:500px;background:#fff;z-index:1050;display:flex;flex-direction:column;transform:translateX(100%);transition:transform .28s cubic-bezier(.4,0,.2,1);box-shadow:-8px 0 40px rgba(0,0,0,.16);}
.pay-panel.open{transform:translateX(0);}
.pay-panel-header{background:linear-gradient(135deg,#e53e3e,#c53030);padding:18px 20px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;}
.pay-panel-header h5{color:#fff;font-size:15px;font-weight:700;margin:0;}
.pay-panel-close{width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;}
.pay-panel-close:hover{background:#c53030;}
.pay-panel-body{flex:1;overflow-y:auto;padding:20px;}
.pay-section-title{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.7px;color:#e53e3e;margin-bottom:12px;padding-bottom:6px;border-bottom:1px solid #f0f2f7;display:flex;align-items:center;gap:6px;}
.pay-summary-row{display:flex;gap:10px;margin-bottom:16px;}
.pay-summary-card{flex:1;border-radius:10px;padding:12px 14px;text-align:center;}
.pay-summary-card .psc-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#8a94a6;margin-bottom:3px;}
.pay-summary-card .psc-value{font-size:18px;font-weight:800;}
.pay-form-group{margin-bottom:14px;}
.pay-form-group label{display:block;font-size:12px;font-weight:700;color:#596579;margin-bottom:5px;}
.pay-form-group label .req{color:#e53e3e;}
.pay-form-control{width:100%;min-height:42px;border:1.5px solid #d7dce5;border-radius:8px;font-size:13px;color:#303549;padding:8px 12px;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s;}
.pay-form-control:focus{border-color:#e53e3e;box-shadow:0 0 0 2px rgba(229,62,62,.12);}
.pay-quick-btns{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:6px;}
.pay-quick-btn{padding:5px 12px;border-radius:6px;border:1.5px solid #d7dce5;background:#f8fafc;font-size:12px;font-weight:700;cursor:pointer;color:#596579;transition:all .15s;}
.pay-quick-btn:hover,.pay-quick-btn.active{background:#e53e3e;border-color:#e53e3e;color:#fff;}
.pay-panel-footer{padding:14px 20px;border-top:1px solid #f0f2f7;background:#fafbff;display:flex;gap:10px;flex-shrink:0;}
.pay-btn-cancel{flex:0;padding:9px 18px;border-radius:8px;border:1.5px solid #e5e8ee;background:#f4f6fb;color:#596579;font-size:13px;font-weight:700;cursor:pointer;}
.pay-btn-save{flex:1;padding:10px;border-radius:8px;border:none;background:linear-gradient(135deg,#e53e3e,#c53030);color:#fff;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(229,62,62,.3);}
.pay-btn-save:disabled{opacity:.6;cursor:not-allowed;}

/* ── Payment History ── */
.pay-hist-row{display:flex;align-items:flex-start;gap:10px;padding:10px 0;border-bottom:1px solid #f3f5f9;}
.pay-hist-row:last-child{border-bottom:none;}
.pay-hist-dot{width:10px;height:10px;border-radius:50%;background:#e53e3e;margin-top:4px;flex-shrink:0;}
.pay-hist-amt{font-size:14px;font-weight:800;color:#1a2340;}
.pay-hist-meta{font-size:11px;color:#8a94a6;margin-top:2px;}</style>

<div class="pcoded-inner-content exp-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="exp-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;letter-spacing:.5px;margin-bottom:5px;">
                <i class="ti-receipt"></i> Expense Management
            </div>
            <h4>Expense Dashboard</h4>
            <div class="sub">Track, manage and approve all trip and vehicle expenses.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <div style="display:inline-flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
                <a href="{{ route('expense.ledger.index') }}" class="btn btn-sm btn-outline-secondary" style="background:#fff;color:#1a2340;font-weight:700;border-radius:8px;padding:7px 18px;border:1px solid rgba(0,0,0,.08);">
                    <i class="ti-list mr-1"></i> Expense Ledger
                </a>
                <a href="{{ route('expense.create') }}" class="btn btn-sm" style="background:#fff;color:#e53e3e;font-weight:700;border-radius:8px;padding:7px 18px;box-shadow:0 2px 10px rgba(0,0,0,.15);">
                    <i class="ti-plus mr-1"></i> Add Expense
                </a>
            </div>
        </div>
    </div>
</div>

@include('partials.flash')

<div class="exp-stats">
    <div class="exp-stat" style="border-left-color:#e53e3e;">
        <div class="es-icon" style="background:#fff5f5;color:#e53e3e;"><i class="ti-receipt"></i></div>
        <div><div class="es-label">Total Expenses</div><div class="es-value" style="color:#e53e3e;">₹{{ number_format($stats['total'],0) }}</div></div>
    </div>
    <div class="exp-stat" style="border-left-color:#d97706;">
        <div class="es-icon" style="background:#fffbeb;color:#d97706;"><i class="ti-time"></i></div>
        <div><div class="es-label">Pending Approval</div><div class="es-value" style="color:#d97706;">₹{{ number_format($stats['pending'],0) }}</div></div>
    </div>
    <div class="exp-stat" style="border-left-color:#38a169;">
        <div class="es-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-check"></i></div>
        <div><div class="es-label">Approved</div><div class="es-value" style="color:#38a169;">₹{{ number_format($stats['approved'],0) }}</div></div>
    </div>
    <div class="exp-stat" style="border-left-color:#667eea;">
        <div class="es-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-list"></i></div>
        <div><div class="es-label">Total Entries</div><div class="es-value" style="color:#667eea;">{{ $stats['count'] }}</div></div>
    </div>
</div>

{{-- Category breakdown --}}
<div class="row mb-4">
    @foreach($categories as $key => $cat)
    @php $amt = $stats['by_category'][$key] ?? 0; @endphp
    @if($amt > 0)
    <div class="col-6 col-md-3 mb-3">
        <div style="background:#fff;border-radius:10px;padding:12px 14px;box-shadow:0 2px 8px rgba(0,0,0,.05);display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;border-radius:8px;background:{{ $cat['bg'] }};color:{{ $cat['color'] }};display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;">
                <i class="{{ $cat['icon'] }}"></i>
            </div>
            <div>
                <div style="font-size:10px;font-weight:700;color:#8a94a6;text-transform:uppercase;">{{ $cat['label'] }}</div>
                <div style="font-size:15px;font-weight:800;color:{{ $cat['color'] }};">₹{{ number_format($amt,0) }}</div>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>

<div class="exp-filter">
    <div class="exp-search-wrap">
        <i class="ti-search"></i>
        <input type="text" id="expSearch" class="form-control" placeholder="Search by trip, vehicle, driver, notes...">
    </div>
    <select id="filterCat" class="form-control" style="min-width:130px;max-width:160px;">
        <option value="">All Categories</option>
        @foreach($categories as $key => $cat)
        <option value="{{ $key }}">{{ $cat['label'] }}</option>
        @endforeach
    </select>
    <select id="filterStatus" class="form-control" style="min-width:120px;max-width:150px;">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
    </select>
    <input type="date" id="filterFrom" class="form-control" style="max-width:150px;" title="From Date">
    <input type="date" id="filterTo" class="form-control" style="max-width:150px;" title="To Date">
    <button type="button" id="clearExpFilters" class="btn btn-outline-secondary btn-sm" style="white-space:nowrap;">
        <i class="ti-close mr-1"></i> Clear
    </button>
</div>

<div class="exp-card">
    <div class="exp-card-header">
        <h6><i class="ti-list mr-2" style="color:#e53e3e;"></i>All Expenses</h6>
        <a href="{{ route('expense.create') }}" class="btn btn-sm" style="background:#e53e3e;color:#fff;border-radius:8px;font-weight:600;padding:6px 16px;">
            <i class="ti-plus mr-1"></i> Add Expense
        </a>
    </div>
    <div class="exp-table-wrap">
        <table class="table table-striped table-bordered" id="expTable">
            <thead>
                <tr>
                    <th style="width:50px;text-align:center;">#</th>
                    <th style="width:120px;">Date</th>
                    <th style="width:130px;">Category</th>
                    <th style="width:160px;">Trip / Vehicle</th>
                    <th style="width:150px;">Driver</th>
                    <th>Notes</th>
                    <th style="width:120px;text-align:right;">Amount</th>
                    <th style="width:110px;text-align:center;">Status</th>
                    <th style="width:130px;text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $i => $exp)
                @php
                    $cat = $categories[$exp->category] ?? ['label'=>ucfirst($exp->category),'icon'=>'ti-more-alt','color'=>'#8a94a6','bg'=>'#f4f6fb'];
                    $sc  = $statusCfg[$exp->status] ?? ['label'=>ucfirst($exp->status),'color'=>'#8a94a6','bg'=>'#f4f6fb'];
                @endphp
                <tr class="exp-row"
                    data-expense-id="{{ $exp->id }}"
                    data-search="{{ strtolower(optional($exp->trip)->trip_no . ' ' . optional($exp->vehicle)->vehicle_number . ' ' . optional($exp->driver)->name . ' ' . $exp->notes . ' ' . $exp->category) }}"
                    data-cat="{{ $exp->category }}"
                    data-status="{{ $exp->status }}"
                    data-date="{{ $exp->expense_date->format('Y-m-d') }}">
                    <td style="text-align:center;">{{ $i+1 }}</td>
                    <td>{{ $exp->expense_date->format('d M Y') }}</td>
                    <td>
                        <span class="cat-chip" style="background:{{ $cat['bg'] }};color:{{ $cat['color'] }};">
                            <i class="{{ $cat['icon'] }}" style="font-size:10px;"></i> {{ $cat['label'] }}
                        </span>
                    </td>
                    <td>
                        @if($exp->trip)<div style="font-size:12px;font-weight:700;color:#667eea;">{{ $exp->trip->trip_no }}</div>@endif
                        @if($exp->vehicle)<div style="font-size:11px;color:#8a94a6;">{{ $exp->vehicle->vehicle_number }}</div>@endif
                        @if(!$exp->trip && !$exp->vehicle)<span style="color:#b0bac9;">—</span>@endif
                    </td>
                    <td>{{ optional($exp->driver)->name ?: '—' }}</td>
                    <td style="max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $exp->notes }}">
                        @if($exp->category === 'accessories')
                            <div style="font-weight:700;color:#d97706;font-size:11px;">
                                Trader: {{ optional($exp->trader)->name ?: '—' }}
                            </div>
                            <div style="font-size:11px;color:#596579;margin-top:2px;" title="{{ $exp->accessories->pluck('accessory_name')->implode(', ') }}">
                                {{ $exp->accessories->map(fn($a) => $a->accessory_name . ' (' . $a->quantity . 'x ₹' . number_format($a->price, 0) . ')')->implode(', ') }}
                            </div>
                            @if($exp->notes)
                                <div style="font-size:10px;color:#8a94a6;margin-top:2px;">{{ $exp->notes }}</div>
                            @endif
                        @else
                            {{ $exp->notes ?: '—' }}
                        @endif
                    </td>
                    <td style="text-align:right;font-weight:800;color:#1a2340;">₹{{ number_format($exp->amount,0) }}</td>
                    <td style="text-align:center;" class="pay-status-cell">
                        <span class="exp-badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">{{ $sc['label'] }}</span>
                        <div style="margin-top:4px;">
                            @if($exp->payment_mode === 'credit')
                                @php
                                    $balance = max(0, $exp->amount - $exp->paid_amount);
                                    $ps = $exp->payment_status ?? 'unpaid';
                                    $psCfg = [
                                        'unpaid'  => ['label'=>'Unpaid',   'color'=>'#e53e3e','bg'=>'#fff5f5'],
                                        'partial' => ['label'=>'Partial',  'color'=>'#d97706','bg'=>'#fffbeb'],
                                        'paid'    => ['label'=>'Settled',  'color'=>'#38a169','bg'=>'#f0fff4'],
                                    ];
                                    $pc = $psCfg[$ps] ?? $psCfg['unpaid'];
                                @endphp
                                <span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;background:#eef2ff;color:#667eea;">
                                    💳 Credit
                                </span>
                                <span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;background:{{ $pc['bg'] }};color:{{ $pc['color'] }};margin-top:2px;">
                                    {{ $pc['label'] }}
                                    @if($ps !== 'paid') · ₹{{ number_format($balance,0) }} due @endif
                                </span>
                            @else
                                <span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;background:#f0fff4;color:#38a169;">
                                    💵 Cash
                                </span>
                            @endif
                        </div>
                    </td>
                    <td style="text-align:center;" onclick="event.stopPropagation();">
                        <div style="display:inline-flex;gap:4px;align-items:center;flex-wrap:wrap;justify-content:center;">
                            <a href="{{ route('expense.edit', $exp->id) }}" class="td-icon-btn edit" title="Edit"><i class="ti-pencil"></i></a>
                            @if($exp->payment_mode === 'credit')
                            @php
                                $isApproved  = $exp->status === 'approved';
                                $isSettled   = ($exp->payment_status ?? 'unpaid') === 'paid';
                                $btnTitle    = $isSettled  ? 'View Payment History'
                                            : ($isApproved ? 'Collect Payment'
                                                           : 'Approve expense first to collect payment');
                                $btnBg       = $isSettled  ? '#f0fff4'
                                            : ($isApproved ? '#fff5f5' : '#f8fafc');
                                $btnColor    = $isSettled  ? '#38a169'
                                            : ($isApproved ? '#e53e3e' : '#b0bac9');
                                $btnIcon     = $isSettled  ? 'ti-check-box'
                                            : ($isApproved ? 'ti-money' : 'ti-lock');
                            @endphp
                            <button type="button" class="td-icon-btn"
                                title="{{ $btnTitle }}"
                                style="background:{{ $btnBg }};color:{{ $btnColor }};{{ !$isApproved && !$isSettled ? 'cursor:not-allowed;opacity:.6;' : '' }}"
                                {{ !$isApproved && !$isSettled ? 'disabled' : '' }}
                                @if($isApproved || $isSettled)
                                onclick="openPaymentPanel({{ $exp->id }}, '{{ number_format($exp->amount,2) }}', '{{ number_format($exp->paid_amount ?? 0,2) }}', '{{ $exp->payment_status ?? 'unpaid' }}', {{ $isApproved ? 'true' : 'false' }})"
                                @endif>
                                <i class="{{ $btnIcon }}"></i>
                            </button>
                            @endif
                            @if($exp->status === 'pending')
                            <form action="{{ route('expense.approve', $exp->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="td-icon-btn approve" title="Approve"><i class="ti-check"></i></button>
                            </form>
                            <form action="{{ route('expense.reject', $exp->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="td-icon-btn reject" title="Reject"><i class="ti-close"></i></button>
                            </form>
                            @endif
                            @if($exp->bill_image)
                            <a href="{{ asset('storage/'.$exp->bill_image) }}" target="_blank" class="td-icon-btn bill" title="View Bill"><i class="ti-file"></i></a>
                            @endif
                            <button type="button" class="td-icon-btn del" title="Delete"
                                onclick="showDeleteModal('delExpForm{{ $exp->id }}','Expense #{{ $i+1 }}','Expense')">
                                <i class="ti-trash"></i>
                            </button>
                        </div>
                        <form id="delExpForm{{ $exp->id }}" action="{{ route('expense.destroy', $exp->id) }}" method="POST" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4" style="color:#b0bac9;">
                    <i class="ti-receipt" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    No expenses found. <a href="{{ route('expense.create') }}">Add your first expense</a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- FAB removed: use header button instead --}}

</div></div></div></div>

{{-- ── Payment Collection Panel ───────────────────────────────────────── --}}
<div class="pay-backdrop" id="payBackdrop" onclick="closePaymentPanel()"></div>
<div class="pay-panel" id="payPanel">
    <div class="pay-panel-header">
        <h5><i class="ti-money mr-2"></i>Collect Payment</h5>
        <button class="pay-panel-close" onclick="closePaymentPanel()"><i class="ti-close"></i></button>
    </div>
    <div class="pay-panel-body" id="payPanelBody">
        <div style="text-align:center;padding:40px 0;color:#b0bac9;"><i class="ti-reload" style="font-size:24px;display:block;margin-bottom:8px;"></i>Loading...</div>
    </div>
    <div class="pay-panel-footer">
        <button class="pay-btn-cancel" onclick="closePaymentPanel()"><i class="ti-close mr-1"></i>Cancel</button>
        <button class="pay-btn-save" id="paySubmitBtn" onclick="submitPayment()">
            <i class="ti-save mr-1"></i> Record Payment
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    /* ── Select2 for filter dropdowns ──────────────────────────────── */
    $('#filterCat, #filterStatus').select2({
        minimumResultsForSearch: -1,
        width: '150px',
    });

    function applyExpFilters() {
        var term   = $('#expSearch').val().toLowerCase();
        var cat    = $('#filterCat').val();
        var status = $('#filterStatus').val();
        var from   = $('#filterFrom').val();
        var to     = $('#filterTo').val();
        $('.exp-row').each(function () {
            var $r = $(this);
            var ok = true;
            if (term   && !($r.attr('data-search')||'').includes(term)) ok = false;
            if (cat    && $r.attr('data-cat')    !== cat)    ok = false;
            if (status && $r.attr('data-status') !== status) ok = false;
            if (from   && $r.attr('data-date')   < from)     ok = false;
            if (to     && $r.attr('data-date')   > to)       ok = false;
            $r.toggle(ok);
        });
    }
    $('#expSearch,#filterCat,#filterStatus,#filterFrom,#filterTo').on('input change', applyExpFilters);
    $('#clearExpFilters').on('click', function () {
        $('#expSearch').val('');
        $('#filterCat,#filterStatus').val('');
        $('#filterFrom,#filterTo').val('');
        applyExpFilters();
    });
});

/* ══════════════════════════════════════════════════════
   PAYMENT COLLECTION PANEL
   ══════════════════════════════════════════════════════ */
var _payExpenseId   = null;
var _payTotalAmount = 0;
var _payPaidAmount  = 0;
var _payBalance     = 0;

function openPaymentPanel(expId, totalAmt, paidAmt, payStatus, isApproved) {
    _payExpenseId   = expId;
    _payTotalAmount = parseFloat(totalAmt.replace(/,/g,''));
    _payPaidAmount  = parseFloat(paidAmt.replace(/,/g,''));
    _payBalance     = Math.max(0, _payTotalAmount - _payPaidAmount);

    // Show panel skeleton first
    $('#payPanelBody').html('<div style="text-align:center;padding:40px 0;color:#b0bac9;"><i class="ti-reload" style="font-size:24px;display:block;margin-bottom:8px;animation:spin .8s linear infinite;"></i>Loading...</div>');
    $('#paySubmitBtn').prop('disabled', false).html('<i class="ti-save mr-1"></i> Record Payment');

    // Show/hide footer record button based on payment status and approval
    if (payStatus === 'paid' || !isApproved) {
        $('#paySubmitBtn').hide();
        var headerIcon = payStatus === 'paid'
            ? '<i class="ti-check-box mr-2" style="color:#9be6b4;"></i>Payment History'
            : '<i class="ti-lock mr-2" style="color:#ffd86e;"></i>Payment Collection';
        $('#payPanel .pay-panel-header h5').html(headerIcon);
    } else {
        $('#paySubmitBtn').show();
        $('#payPanel .pay-panel-header h5').html('<i class="ti-money mr-2"></i>Collect Payment');
    }

    // Open drawer
    document.getElementById('payBackdrop').classList.add('show');
    document.getElementById('payPanel').classList.add('open');
    document.body.style.overflow = 'hidden';

    // Fetch history + render form
    $.ajax({
        url: '/expense/' + expId + '/payments',
        type: 'GET',
        success: function (res) {
            renderPaymentPanel(res, payStatus);
        },
        error: function () {
            $('#payPanelBody').html('<div class="alert alert-danger" style="border-radius:8px;">Failed to load payment data.</div>');
        }
    });
}

function renderPaymentPanel(res, payStatus) {
    var isFullyPaid = res.payment_status === 'paid';
    var balance     = parseFloat(res.balance.replace(/,/g,''));
    var paid        = parseFloat(res.paid_amount.replace(/,/g,''));
    var total       = parseFloat(res.total_amount.replace(/,/g,''));
    var pct         = total > 0 ? Math.min(100, Math.round(paid / total * 100)) : 0;

    var statusCfg = {
        unpaid:  { label: 'Unpaid',   color: '#e53e3e', bg: '#fff5f5' },
        partial: { label: 'Partial',  color: '#d97706', bg: '#fffbeb' },
        paid:    { label: 'Settled',  color: '#38a169', bg: '#f0fff4' }
    };
    var sc = statusCfg[res.payment_status] || statusCfg.unpaid;

    var html = '';

    /* ── Summary cards ── */
    html += '<div class="pay-summary-row">';
    html += '<div class="pay-summary-card" style="background:#fff5f5;">';
    html += '<div class="psc-label">Total</div>';
    html += '<div class="psc-value" style="color:#e53e3e;">₹' + res.total_amount + '</div></div>';

    html += '<div class="pay-summary-card" style="background:#f0fff4;">';
    html += '<div class="psc-label">Collected</div>';
    html += '<div class="psc-value" style="color:#38a169;">₹' + res.paid_amount + '</div></div>';

    html += '<div class="pay-summary-card" style="background:' + sc.bg + ';">';
    html += '<div class="psc-label">Balance</div>';
    html += '<div class="psc-value" style="color:' + sc.color + ';">₹' + res.balance + '</div></div>';
    html += '</div>';

    /* ── Progress bar ── */
    html += '<div style="margin-bottom:18px;">';
    html += '<div style="display:flex;justify-content:space-between;font-size:11px;font-weight:700;color:#8a94a6;margin-bottom:5px;">';
    html += '<span>Payment Progress</span>';
    html += '<span style="color:' + sc.color + ';">' + pct + '% — <span style="background:' + sc.bg + ';padding:2px 8px;border-radius:10px;">' + sc.label + '</span></span></div>';
    html += '<div style="height:8px;border-radius:4px;background:#f0f2f7;overflow:hidden;">';
    html += '<div style="height:100%;border-radius:4px;background:linear-gradient(90deg,#38a169,' + (pct < 100 ? '#48bb78' : '#38a169') + ');width:' + pct + '%;transition:width .5s;"></div></div>';
    html += '</div>';

    /* ── New payment form (only when not fully paid) ── */
    if (!isFullyPaid) {
        // Check approval gate — server passes this via paymentHistory response
        var notApproved = (res.expense_status && res.expense_status !== 'approved');

        if (notApproved) {
            html += '<div style="background:#fffbeb;border:1.5px solid #fed7aa;border-radius:10px;padding:14px 16px;margin-bottom:18px;display:flex;align-items:center;gap:10px;">';
            html += '<i class="ti-lock" style="font-size:20px;color:#d97706;flex-shrink:0;"></i>';
            html += '<div><div style="font-weight:700;color:#d97706;font-size:13px;">Approval Required</div>';
            html += '<div style="font-size:12px;color:#92400e;margin-top:2px;">This expense must be <strong>approved</strong> before payments can be collected.</div></div>';
            html += '</div>';
        } else {
        html += '<div class="pay-section-title"><i class="ti-plus"></i> New Payment</div>';
        html += '<div id="payFormWrap">';

        /* Quick amount buttons */
        html += '<div class="pay-form-group">';
        html += '<label>Amount <span class="req">*</span></label>';
        html += '<div class="pay-quick-btns" id="quickAmtBtns">';
        if (balance > 0) {
            html += '<button type="button" class="pay-quick-btn" onclick="setPayAmt(' + balance.toFixed(2) + ', this)">Full (₹' + balance.toFixed(0) + ')</button>';
            if (balance >= 500)  html += '<button type="button" class="pay-quick-btn" onclick="setPayAmt(500, this)">₹500</button>';
            if (balance >= 1000) html += '<button type="button" class="pay-quick-btn" onclick="setPayAmt(1000, this)">₹1,000</button>';
            if (balance >= 2000) html += '<button type="button" class="pay-quick-btn" onclick="setPayAmt(2000, this)">₹2,000</button>';
            if (balance >= 5000) html += '<button type="button" class="pay-quick-btn" onclick="setPayAmt(5000, this)">₹5,000</button>';
        }
        html += '</div>';
        html += '<div style="display:flex;align-items:center;gap:6px;">';
        html += '<span style="font-weight:700;color:#596579;font-size:14px;">₹</span>';
        html += '<input type="number" id="payAmtInput" class="pay-form-control" step="0.01" min="0.01" max="' + balance.toFixed(2) + '" placeholder="0.00" oninput="onPayAmtChange()">';
        html += '</div>';
        html += '<div id="payAmtError" style="font-size:11px;color:#e53e3e;margin-top:3px;display:none;"></div>';
        html += '</div>';

        /* Remaining preview */
        html += '<div id="payRemaining" style="display:none;background:#fffbeb;border-radius:8px;padding:10px 14px;font-size:12px;color:#d97706;font-weight:600;margin-bottom:14px;">';
        html += '<i class="ti-info-alt mr-1"></i>After this payment, remaining balance: <strong id="payRemAmt">₹0</strong></div>';

        /* Date */
        html += '<div class="pay-form-group">';
        html += '<label>Payment Date <span class="req">*</span></label>';
        html += '<input type="date" id="payDateInput" class="pay-form-control" value="' + new Date().toISOString().split('T')[0] + '">';
        html += '</div>';

        /* Mode */
        html += '<div class="pay-form-group">';
        html += '<label>Payment Mode <span class="req">*</span></label>';
        html += '<div style="display:flex;gap:8px;flex-wrap:wrap;" id="payModeGroup">';
        [['cash','💵 Cash'],['upi','📱 UPI'],['bank','🏦 Bank'],['cheque','📄 Cheque']].forEach(function(m) {
            html += '<label style="cursor:pointer;"><input type="radio" name="payMode" value="' + m[0] + '" style="display:none;" onchange="updatePayMode()"> <span class="pay-mode-chip" data-mode="' + m[0] + '" style="display:inline-block;padding:6px 14px;border-radius:8px;border:2px solid #d7dce5;background:#f8fafc;font-size:12px;font-weight:700;color:#596579;transition:all .15s;">' + m[1] + '</span></label>';
        });
        html += '</div></div>';

        /* Reference */
        html += '<div class="pay-form-group" id="payRefGroup" style="display:none;">';
        html += '<label>Reference No.</label>';
        html += '<input type="text" id="payRefInput" class="pay-form-control" placeholder="UPI ID / Cheque No. / Bank Ref...">';
        html += '</div>';

        /* Notes */
        html += '<div class="pay-form-group">';
        html += '<label>Notes</label>';
        html += '<textarea id="payNotesInput" class="pay-form-control" rows="2" placeholder="Optional remarks..."></textarea>';
        html += '</div>';

        html += '</div>'; /* end payFormWrap */
        } // end else (approved)
    } // end if !isFullyPaid

    /* ── Payment History ── */
    html += '<div class="pay-section-title" style="margin-top:18px;"><i class="ti-list"></i> Payment History</div>';
    if (res.payments.length === 0) {
        html += '<div style="text-align:center;padding:20px 0;color:#b0bac9;font-size:13px;"><i class="ti-receipt" style="display:block;font-size:28px;margin-bottom:6px;"></i>No payments recorded yet.</div>';
    } else {
        res.payments.forEach(function (p, idx) {
            var modeLabel = {cash:'💵 Cash', upi:'📱 UPI', bank:'🏦 Bank', cheque:'📄 Cheque'}[p.payment_mode] || p.payment_mode;
            html += '<div class="pay-hist-row">';
            html += '<div class="pay-hist-dot"></div>';
            html += '<div style="flex:1;">';
            html += '<div style="display:flex;justify-content:space-between;align-items:center;">';
            html += '<span class="pay-hist-amt">₹' + p.amount + '</span>';
            html += '<span style="font-size:11px;font-weight:700;background:#f0fff4;color:#38a169;padding:2px 8px;border-radius:10px;">' + modeLabel + '</span>';
            html += '</div>';
            html += '<div class="pay-hist-meta">' + p.payment_date;
            if (p.reference_no) html += ' · Ref: ' + p.reference_no;
            html += ' · By: ' + p.created_by + '</div>';
            if (p.notes) html += '<div style="font-size:11px;color:#596579;margin-top:2px;">' + p.notes + '</div>';
            html += '</div></div>';
        });
    }

    $('#payPanelBody').html(html);

    // Auto-select Cash mode
    if (!isFullyPaid) {
        $('input[name="payMode"][value="cash"]').prop('checked', true);
        updatePayMode();
    }

    _payBalance = balance;
}

function setPayAmt(amt, btn) {
    $('#payAmtInput').val(parseFloat(amt).toFixed(2));
    $('#quickAmtBtns .pay-quick-btn').removeClass('active');
    $(btn).addClass('active');
    onPayAmtChange();
}

function onPayAmtChange() {
    var val     = parseFloat($('#payAmtInput').val()) || 0;
    var balance = _payBalance;
    $('#quickAmtBtns .pay-quick-btn').removeClass('active');
    if (val <= 0) {
        $('#payAmtError').show().text('Amount must be greater than 0.');
        $('#payRemaining').hide();
    } else if (val > balance + 0.001) {
        $('#payAmtError').show().text('Cannot exceed balance of ₹' + balance.toFixed(2));
        $('#payRemaining').hide();
    } else {
        $('#payAmtError').hide();
        var rem = Math.max(0, balance - val);
        if (rem > 0) {
            $('#payRemaining').show();
            $('#payRemAmt').text('₹' + rem.toFixed(2));
        } else {
            $('#payRemaining').hide();
        }
    }
}

function updatePayMode() {
    var mode = $('input[name="payMode"]:checked').val();
    $('.pay-mode-chip').each(function () {
        var isSelected = $(this).data('mode') === mode;
        $(this).css({
            'background':    isSelected ? '#e53e3e' : '#f8fafc',
            'border-color':  isSelected ? '#e53e3e' : '#d7dce5',
            'color':         isSelected ? '#fff'    : '#596579'
        });
    });
    // Show reference field for non-cash
    if (mode && mode !== 'cash') {
        $('#payRefGroup').show();
    } else {
        $('#payRefGroup').hide();
    }
}

// Make mode chip clickable to select radio
$(document).on('click', '.pay-mode-chip', function () {
    var mode = $(this).data('mode');
    $('input[name="payMode"][value="' + mode + '"]').prop('checked', true);
    updatePayMode();
});

function submitPayment() {
    var amt  = parseFloat($('#payAmtInput').val()) || 0;
    var date = $('#payDateInput').val();
    var mode = $('input[name="payMode"]:checked').val();
    var ref  = $('#payRefInput').val().trim();
    var note = $('#payNotesInput').val().trim();

    // Validate
    var valid = true;
    if (amt <= 0 || amt > _payBalance + 0.001) {
        $('#payAmtError').show().text(amt <= 0 ? 'Amount is required.' : 'Cannot exceed balance of ₹' + _payBalance.toFixed(2));
        valid = false;
    } else {
        $('#payAmtError').hide();
    }
    if (!date) { alert('Please enter the payment date.'); return; }
    if (!mode) { alert('Please select a payment mode.'); return; }
    if (!valid) return;

    var $btn = $('#paySubmitBtn').prop('disabled', true).html('<i class="ti-reload mr-1"></i> Saving...');

    $.ajax({
        url:  '/expense/' + _payExpenseId + '/pay',
        type: 'POST',
        data: {
            _token:       $('meta[name="csrf-token"]').attr('content'),
            payment_date: date,
            amount:       amt,
            payment_mode: mode,
            reference_no: ref,
            notes:        note
        },
        success: function (res) {
            if (res.success) {
                // Update table row status cell inline (no full page reload for partial)
                var $row = $('[data-expense-id="' + _payExpenseId + '"]');
                if ($row.length) {
                    $row.find('.pay-status-cell .exp-badge').after(''); // keep approval badge
                    $row.find('.pay-status-cell div').html(buildStatusCell(res.payment_status, res.balance));
                }

                // Toast
                showPayToast('success', res.message);

                // Reload panel with fresh server data
                setTimeout(function () {
                    $.ajax({
                        url: '/expense/' + _payExpenseId + '/payments',
                        type: 'GET',
                        success: function (freshRes) {
                            renderPaymentPanel(freshRes, freshRes.payment_status);
                            $('#paySubmitBtn').prop('disabled', false).html('<i class="ti-save mr-1"></i> Record Payment');
                            if (freshRes.payment_status === 'paid') {
                                $('#paySubmitBtn').hide();
                                $('#payPanel .pay-panel-header h5').html('<i class="ti-check-box mr-2" style="color:#9be6b4;"></i>Payment History');
                            }
                        }
                    });
                }, 500);

                // Full page reload if settled (to refresh stats)
                if (res.payment_status === 'paid') {
                    setTimeout(function () { location.reload(); }, 2200);
                }
            }
        },
        error: function (xhr) {
            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to record payment.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                var errs = Object.values(xhr.responseJSON.errors).flat();
                msg = errs[0];
            }
            showPayToast('error', msg);
            $btn.prop('disabled', false).html('<i class="ti-save mr-1"></i> Record Payment');
        }
    });
}

function buildStatusCell(payStatus, balance) {
    var cfg = {
        unpaid:  { label:'Unpaid',  color:'#e53e3e', bg:'#fff5f5' },
        partial: { label:'Partial', color:'#d97706', bg:'#fffbeb' },
        paid:    { label:'Settled', color:'#38a169', bg:'#f0fff4' }
    }[payStatus] || { label:'Unpaid', color:'#e53e3e', bg:'#fff5f5' };

    var html = '<span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;background:#eef2ff;color:#667eea;">💳 Credit</span>';
    html += '<span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;background:' + cfg.bg + ';color:' + cfg.color + ';margin-top:2px;">' + cfg.label;
    if (payStatus !== 'paid') html += ' · ₹' + balance + ' due';
    html += '</span>';
    return html;
}

function closePaymentPanel() {
    document.getElementById('payBackdrop').classList.remove('show');
    document.getElementById('payPanel').classList.remove('open');
    document.body.style.overflow = '';
}

function showPayToast(type, msg) {
    if (typeof toastr !== 'undefined') {
        toastr[type](msg);
    } else {
        alert(msg);
    }
}

// ESC key closes panel
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closePaymentPanel();
});

// Spin keyframe for loader
(function () {
    var s = document.createElement('style');
    s.textContent = '@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}';
    document.head.appendChild(s);
})();
</script>
@endpush
