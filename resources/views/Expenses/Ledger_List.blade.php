@extends('layouts.app')

@section('content')
<style>
.rpt-page{background:#f4f6fb;}
.rpt-header{background:linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);border-radius:14px;padding:20px 24px;color:#fff;margin-bottom:20px;position:relative;overflow:hidden;}
.rpt-header::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.08);border-radius:50%;}
.rpt-header h4{font-size:18px;font-weight:800;margin:0 0 4px;}
.rpt-header .sub{font-size:12px;opacity:.85;}
.rpt-filter{background:#fff;border-radius:12px;padding:14px 18px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:16px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.rpt-filter .form-control{min-height:40px;font-size:13px;border-color:#e2e8f0;border-radius:8px;}
.rpt-card{background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;}
.rpt-card-header{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f0f2f7;background:#fafbff;flex-wrap:wrap;gap:8px;}
.rpt-card-header h6{margin:0;font-size:14px;font-weight:700;color:#1a2340;}
.rpt-table-wrap{overflow-x:auto;}
#ledgerTable{width:100%;border-collapse:collapse;font-size:12px;}
#ledgerTable thead tr{background:#f8fafc;}
#ledgerTable th{padding:10px 10px;font-size:10.5px;font-weight:800;color:#1a2340;text-transform:uppercase;letter-spacing:.4px;border-bottom:2px solid #e2e8f0;text-align:left;white-space:nowrap;}
#ledgerTable th.R{text-align:right;}
#ledgerTable th.C{text-align:center;}
#ledgerTable td{padding:9px 10px;border-bottom:1px solid #f0f2f7;color:#1e293b;vertical-align:middle;}
#ledgerTable td.R{text-align:right;font-weight:700;font-variant-numeric:tabular-nums;white-space:nowrap;}
#ledgerTable td.C{text-align:center;}
#ledgerTable tbody tr:hover td{background:#f8fafc;}
#ledgerTable tbody tr:last-child td{border-bottom:none;}
.exp-pill{display:inline-flex;align-items:center;padding:2px 8px;border-radius:999px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;}
.exp-pill.pending{background:#fef3cd;color:#856404;}
.exp-pill.paid{background:#d4edda;color:#155724;}
.exp-pill.partial{background:#cce5ff;color:#004085;}
.exp-pill.credit{background:#f3e8ff;color:#6b21a8;}
.stats-row{display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;}
.stat-block{background:#fff;border-radius:10px;padding:12px 18px;box-shadow:0 2px 10px rgba(0,0,0,.06);flex:1;min-width:140px;display:flex;align-items:center;gap:12px;}
.stat-block .stat-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
.stat-block .stat-num{font-size:20px;font-weight:800;color:#0f172a;line-height:1.2;}
.stat-block .stat-lbl{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.4px;}
@media print{.pcoded-navbar,.pcoded-header,.pcoded-footer,.rpt-filter,.rpt-card-header .btn{display:none!important;}.rpt-page,.pcoded-content,.pcoded-inner-content,.main-body,.page-wrapper,.page-body{background:#fff!important;padding:0!important;margin:0!important;}}
@media(max-width:767.98px){.rpt-filter{flex-direction:column;align-items:stretch;}#ledgerTable{font-size:11px;}#ledgerTable th,#ledgerTable td{padding:6px 6px;}}
</style>

@php
    $catDef = $categories[$category] ?? ['label'=>ucfirst($category), 'icon'=>'ti-receipt', 'color'=>'#2563eb', 'bg'=>'#eff6ff'];
    $catColor = $catDef['color'] ?? '#2563eb';
    $catBg    = $catDef['bg'] ?? '#eff6ff';
    $catIcon  = $catDef['icon'] ?? 'ti-receipt';
    $catLabel = $catDef['label'] ?? ucfirst($category);
    $dateFrom = request('date_from');
    $dateTo   = request('date_to');
@endphp

<div class="pcoded-inner-content rpt-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="rpt-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <h4><i class="{{ $catIcon }} mr-2"></i>{{ $catLabel }} Ledger</h4>
            <div class="sub">Showing {{ $summary['count'] }} records · Total: ₹{{ number_format($summary['total'],0) }}.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('expense.ledger.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;margin-right:6px;">
                <i class="ti-angle-left mr-1"></i> Ledger Home
            </a>
            <a href="{{ route('expense') }}" class="btn btn-sm" style="background:#fff;color:#1d4ed8;border-radius:8px;padding:7px 16px;font-weight:700;">
                <i class="ti-receipt mr-1"></i> All Expenses
            </a>
        </div>
    </div>
</div>

{{-- Stats row --}}
<div class="stats-row">
    <div class="stat-block">
        <div class="stat-icon" style="background:{{ $catBg }};color:{{ $catColor }};"><i class="{{ $catIcon }}"></i></div>
        <div><div class="stat-num">{{ $summary['count'] }}</div><div class="stat-lbl">Total Entries</div></div>
    </div>
    <div class="stat-block">
        <div class="stat-icon" style="background:#fef2f2;color:#e53e3e;"><i class="ti-credit-card"></i></div>
        <div><div class="stat-num">₹{{ number_format($summary['total'],0) }}</div><div class="stat-lbl">Total Amount</div></div>
    </div>
    @if($dateFrom || $dateTo)
    <div class="stat-block">
        <div class="stat-icon" style="background:#f0f9ff;color:#0369a1;"><i class="ti-calendar"></i></div>
        <div><div class="stat-num" style="font-size:13px;">{{ $dateFrom ?: '∞' }} – {{ $dateTo ?: '∞' }}</div><div class="stat-lbl">Filter Range</div></div>
    </div>
    @endif
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('expense.ledger.category', $category) }}">
<div class="rpt-filter">
    <input type="date" name="date_from" class="form-control" style="max-width:150px;" value="{{ $dateFrom }}">
    <input type="date" name="date_to" class="form-control" style="max-width:150px;" value="{{ $dateTo }}">
    <button type="submit" class="btn btn-danger btn-sm" style="border-radius:8px;padding:8px 18px;white-space:nowrap;">
        <i class="ti-search mr-1"></i> Filter
    </button>
    <a href="{{ route('expense.ledger.category', $category) }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;padding:8px 14px;">
        <i class="ti-close mr-1"></i> Clear
    </a>
</div>
</form>

{{-- Table --}}
<div class="rpt-card">
    <div class="rpt-card-header">
        <h6><i class="{{ $catIcon }} mr-2" style="color:{{ $catColor }};"></i>{{ $catLabel }} Records</h6>
        <div style="display:flex;gap:8px;align-items:center;">
            <a href="{{ route('expense.ledger.pdf', ['category' => $category, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
               class="btn btn-sm btn-danger" style="border-radius:8px;padding:6px 14px;font-weight:700;color:#fff;background:#b91c1c;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
                <i class="ti-file"></i> PDF
            </a>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;padding:6px 14px;">
                <i class="ti-printer mr-1"></i> Print
            </button>
        </div>
    </div>
    <div class="rpt-table-wrap">
        <table id="ledgerTable">
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th style="width:90px;">Date</th>
                    <th>Description</th>
                    <th style="width:110px;">Driver</th>
                    <th style="width:90px;">Vehicle</th>
                    <th style="width:80px;" class="C">Status</th>
                    <th style="width:50px;" class="C">Bill</th>
                    <th style="width:90px;" class="R">Amount</th>
                    <th style="width:50px;" class="C"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $i => $exp)
                @php
                    $payStatus = $exp->payment_status ?: 'pending';
                    $payLabel  = match($payStatus){'paid'=>'Paid','partial'=>'Partial','credit'=>'Credit',default=>'Pending'};
                    $payClass  = match($payStatus){'paid'=>'paid','partial'=>'partial','credit'=>'credit',default=>'pending'};
                    $hasBill   = !empty($exp->bill_image);
                    $refText   = $exp->notes ?: ($exp->trip?->trip_no ? 'Trip: '.$exp->trip->trip_no : ($exp->vehicle?->vehicle_number ? 'Veh: '.$exp->vehicle->vehicle_number : 'Expense #'.$exp->id));
                    $driverName = optional($exp->driver)->name;
                    $vehNumber  = optional($exp->vehicle)->vehicle_number;
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="white-space:nowrap;">{{ $exp->expense_date->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('expense.edit', $exp->id) }}" style="color:inherit;text-decoration:none;font-weight:600;">
                            {{ $refText }}
                        </a>
                    </td>
                    <td style="color:#64748b;">{{ $driverName ?: '—' }}</td>
                    <td style="color:#64748b;">{{ $vehNumber ?: '—' }}</td>
                    <td class="C"><span class="exp-pill {{ $payClass }}">{{ $payLabel }}</span></td>
                    <td class="C">@if($hasBill)<span style="color:#0369a1;font-size:14px;"><i class="ti-file"></i></span>@else<span style="color:#d1d5db;">—</span>@endif</td>
                    <td class="R">₹{{ number_format($exp->amount,0) }}</td>
                    <td class="C">
                        <a href="{{ route('expense.edit', $exp->id) }}" style="color:#2563eb;font-size:12px;">
                            <i class="ti-pencil-alt"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;padding:30px;color:#94a3b8;">No expenses found for this category.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($expenses->count())
        <div style="border-top:1px solid #e2e8f0;padding:10px 14px;text-align:right;font-weight:800;font-size:14px;color:{{ $catColor }};">Grand Total: ₹{{ number_format($summary['total'],0) }}</div>
        @endif
    </div>
</div>

</div></div></div></div>
@endsection