@extends('layouts.app')

@section('content')
<style>
.ps-bg { background:#f4f6fb; }
.ps-hero { background:linear-gradient(135deg,#9333ea 0%,#7c3aed 100%); border-radius:14px; padding:22px 26px; color:#fff; margin-bottom:20px; position:relative; overflow:hidden; }
.ps-hero::before { content:''; position:absolute; top:-40px; right:-20px; width:160px; height:160px; background:rgba(255,255,255,.07); border-radius:50%; }
.ps-hero h3 { font-size:20px; font-weight:800; margin:0 0 4px; }
.ps-hero .sub { font-size:12px; opacity:.8; }
.kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:10px; margin-bottom:18px; }
.kpi-card { background:#fff; border-radius:10px; padding:14px 16px; box-shadow:0 2px 8px rgba(0,0,0,.05); text-align:center; }
.kpi-card .kpi-label { font-size:10px; font-weight:700; color:#8a94a6; text-transform:uppercase; letter-spacing:.4px; }
.kpi-card .kpi-value { font-size:20px; font-weight:800; color:#1a2340; margin-top:2px; }
.kpi-card .kpi-sub { font-size:10px; color:#8a94a6; margin-top:2px; }
.split-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:18px; }
.split-card { background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.05); overflow:hidden; }
.split-card h6 { margin:0; padding:12px 16px; font-size:13px; font-weight:700; border-bottom:1px solid #f0f2f7; background:#fafbff; }
.split-card .sc-body { padding:12px 16px; }
.month-row { display:flex; justify-content:space-between; padding:6px 0; font-size:12px; border-bottom:1px solid #f5f6fa; }
.month-row:last-child { border-bottom:none; }
.month-row .month { font-weight:700; color:#1a2340; }
.month-row .mon-stat { color:#6b7280; }
.month-bar { width:80px; height:6px; background:#e5e7eb; border-radius:4px; overflow:hidden; display:inline-block; vertical-align:middle; }
.month-bar-fill { height:100%; border-radius:4px; background:linear-gradient(90deg,#9333ea,#a855f7); }
.top-party-item { display:flex; justify-content:space-between; align-items:center; padding:6px 0; font-size:12px; border-bottom:1px solid #f5f6fa; }
.top-party-item:last-child { border-bottom:none; }
.top-party-item .tp-name { font-weight:600; color:#1a2340; max-width:140px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.top-party-item .tp-stat { color:#6b7280; white-space:nowrap; }
.rct-card { background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.05); overflow:hidden; margin-bottom:18px; }
.rct-card h6 { margin:0; padding:12px 16px; font-size:13px; font-weight:700; border-bottom:1px solid #f0f2f7; background:#fafbff; }
.rct-body { padding:0; overflow-x:auto; }
#rctTable { min-width:800px; margin-bottom:0; }
#rctTable th, #rctTable td { height:40px; padding:5px 10px; vertical-align:middle; border-color:#f0f2f7; font-size:11px; }
#rctTable th { background:#f8fafc; color:#14213d; font-weight:800; font-size:10px; text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; }
.ps-badge { display:inline-flex; align-items:center; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700; }
.ps-btn-sm { border-radius:8px!important; padding:6px 14px!important; font-size:12px!important; font-weight:600!important; }
</style>

<div class="pcoded-inner-content ps-bg">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="ps-hero">
    <div class="row align-items-center">
        <div class="col-md-7" style="position:relative;z-index:1;">
            <h3><i class="ti-layout mr-2"></i>Packing Slip Dashboard</h3>
            <div class="sub">{{ $totalTrips }} trips &bull; {{ number_format($totalQty,0) }} Qty &bull; {{ $totalParties }} Parties</div>
        </div>
        <div class="col-md-5 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('reports.packing-slip-ledger') }}" class="btn btn-sm ps-btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);">
                <i class="ti-layout mr-1"></i> Full Ledger
            </a>
        </div>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-label">Total Trips</div>
        <div class="kpi-value" style="color:#9333ea;">{{ $totalTrips }}</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Total Quantity</div>
        <div class="kpi-value" style="color:#16a34a;">{{ number_format($totalQty,0) }}</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Parties</div>
        <div class="kpi-value" style="color:#2563eb;">{{ $totalParties }}</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Vehicles</div>
        <div class="kpi-value" style="color:#d97706;">{{ $totalVehicles }}</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Invoiced</div>
        <div class="kpi-value" style="color:#16a34a;">{{ $invoiced }}</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Pending</div>
        <div class="kpi-value" style="color:#dc2626;">{{ $pending }}</div>
        <div class="kpi-sub">{{ $totalTrips ? round($pending/$totalTrips*100) : 0 }}% of total</div>
    </div>
</div>

<div class="split-row">
    <div class="split-card">
        <h6><i class="ti-calendar mr-2" style="color:#9333ea;"></i>Monthly Trend (Last 6)</h6>
        <div class="sc-body">
            @php $maxCnt = $monthly->max('cnt') ?: 1; @endphp
            @forelse($monthly as $m)
            <div class="month-row">
                <span class="month">{{ \Carbon\Carbon::parse($m->ym.'-01')->format('M Y') }}</span>
                <span class="mon-stat">
                    {{ $m->cnt }} trips
                    <span class="month-bar"><span class="month-bar-fill" style="width:{{ round($m->cnt/$maxCnt*100) }}%;"></span></span>
                    <span style="font-weight:700;color:#16a34a;margin-left:4px;">{{ number_format($m->qty,0) }}</span>
                </span>
            </div>
            @empty
            <div style="text-align:center;padding:12px;color:#b0bac9;font-size:12px;">No data</div>
            @endforelse
        </div>
    </div>
    <div class="split-card">
        <h6><i class="ti-medall mr-2" style="color:#9333ea;"></i>Top 5 Parties</h6>
        <div class="sc-body">
            @php $maxCnt = $topParties->max('cnt') ?: 1; @endphp
            @forelse($topParties as $tp)
            @php $p = $partyNames->get($tp->party_id); @endphp
            <div class="top-party-item">
                <span class="tp-name">{{ $p ? ($p->company_name ?: $p->name) : '—' }}</span>
                <span class="tp-stat">
                    <span class="month-bar" style="width:60px;"><span class="month-bar-fill" style="width:{{ round($tp->cnt/$maxCnt*100) }}%;"></span></span>
                    {{ $tp->cnt }} trips &bull; {{ number_format($tp->qty,0) }} qty
                </span>
            </div>
            @empty
            <div style="text-align:center;padding:12px;color:#b0bac9;font-size:12px;">No parties</div>
            @endforelse
        </div>
    </div>
</div>

<div class="rct-card">
    <h6><i class="ti-timer mr-2" style="color:#9333ea;"></i>Recent Packing Slips</h6>
    <div class="rct-body">
        <table class="table" id="rctTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>LR No</th>
                    <th>Party</th>
                    <th>From → To</th>
                    <th>Material</th>
                    <th style="text-align:right;">Qty</th>
                    <th>Vehicle</th>
                    <th>Invoice</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $t)
                <tr>
                    <td>{{ $t->trip_date?->format('d/m/Y') }}</td>
                    <td><strong style="color:#9333ea;">{{ $t->lr_no }}</strong></td>
                    <td>{{ optional($t->party)->company_name ?: optional($t->party)->name ?: '—' }}</td>
                    <td style="font-size:10px;">{{ $t->from_location ?? '—' }} → {{ $t->to_location ?? '—' }}</td>
                    <td>{{ $t->material ?? '—' }}</td>
                    <td style="text-align:right;font-weight:700;">{{ $t->quantity ? number_format($t->quantity,0) : '—' }}</td>
                    <td>{{ optional($t->vehicle)->vehicle_number ?? '—' }}</td>
                    <td>
                        @if($t->invoice_no)
                        <span class="ps-badge" style="background:#eef2ff;color:#4338ca;">{{ $t->invoice_no }}</span>
                        @else
                        <span style="color:#b0bac9;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-3" style="color:#b0bac9;font-size:12px;">No packing slips yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div></div></div></div>
@endsection