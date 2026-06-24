@extends('layouts.app')

@section('content')
<style>
.rpt-page { background:#f4f6fb; }
.rpt-header { background:linear-gradient(135deg,#d97706 0%,#b45309 100%); border-radius:14px; padding:20px 24px; color:#fff; margin-bottom:20px; position:relative; overflow:hidden; }
.rpt-header::before { content:''; position:absolute; top:-30px; right:-30px; width:120px; height:120px; background:rgba(255,255,255,.07); border-radius:50%; }
.rpt-header h4 { font-size:18px; font-weight:800; margin:0 0 4px; }
.rpt-header .sub { font-size:12px; opacity:.8; }
.rpt-filter { background:#fff; border-radius:12px; padding:14px 18px; box-shadow:0 2px 10px rgba(0,0,0,.06); margin-bottom:16px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.rpt-filter .form-control { min-height:40px; font-size:13px; border-color:#e2e8f0; border-radius:8px; }
.col-summary { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:16px; }
.col-sum-card { background:#fff; border-radius:10px; padding:14px 16px; box-shadow:0 2px 8px rgba(0,0,0,.05); text-align:center; }
.col-sum-card .csc-label { font-size:10px; font-weight:700; color:#8a94a6; text-transform:uppercase; letter-spacing:.4px; }
.col-sum-card .csc-value { font-size:20px; font-weight:800; color:#1a2340; margin-top:2px; }
.rpt-card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); overflow:hidden; }
.rpt-card-header { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid #f0f2f7; background:#fafbff; flex-wrap:wrap; gap:8px; }
.rpt-card-header h6 { margin:0; font-size:14px; font-weight:700; color:#1a2340; }
.rpt-table-wrap { overflow-x:auto; }
#colTable { min-width:900px; margin-bottom:0; }
#colTable th, #colTable td { height:48px; padding:8px 12px; vertical-align:middle; border-color:#f0f2f7; font-size:12px; }
#colTable th { background:#f8fafc; color:#14213d; font-weight:800; font-size:11px; text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; position:sticky; top:0; z-index:2; }
.overdue-row td { background:#fff5f5!important; }
.rpt-badge { display:inline-flex; align-items:center; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700; text-transform:uppercase; }
@media print {
    .pcoded-navbar,.pcoded-header,.pcoded-footer,.rpt-filter,.rpt-card-header .btn,.rpt-card-header .d-flex { display:none!important; }
    .rpt-page,.pcoded-content,.pcoded-inner-content,.main-body,.page-wrapper,.page-body { background:#fff!important; padding:0!important; margin:0!important; }
}
@media(max-width:767.98px) { .col-summary { grid-template-columns:repeat(2,1fr); } .rpt-filter { flex-direction:column; align-items:stretch; } }
</style>

<div class="pcoded-inner-content rpt-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="rpt-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <h4><i class="ti-credit-card mr-2"></i>Pending Collection Report</h4>
            <div class="sub">{{ $trips->count() }} pending &bull; Outstanding: ₹{{ number_format($summary['total_outstanding'],0) }}</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('reports') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;margin-right:6px;">
                <i class="ti-arrow-left mr-1"></i> Reports
            </a>
            <button onclick="exportColExcel()" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 14px;font-weight:600;margin-right:6px;">
                <i class="ti-export mr-1"></i> Excel
            </button>
            <button onclick="window.print()" class="btn btn-sm" style="background:#fff;color:#d97706;border-radius:8px;padding:7px 16px;font-weight:700;">
                <i class="ti-printer mr-1"></i> Print
            </button>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('reports.collection') }}">
<div class="rpt-filter">
    <input type="date" name="date_from" class="form-control" style="max-width:150px;" value="{{ request('date_from') }}" title="From Date">
    <input type="date" name="date_to" class="form-control" style="max-width:150px;" value="{{ request('date_to') }}" title="To Date">
    <button type="submit" class="btn btn-warning btn-sm" style="border-radius:8px;padding:8px 18px;white-space:nowrap;color:#fff;">
        <i class="ti-search mr-1"></i> Filter
    </button>
    <a href="{{ route('reports.collection') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;padding:8px 14px;">
        <i class="ti-close mr-1"></i> Clear
    </a>
</div>
</form>

<div class="col-summary">
    <div class="col-sum-card">
        <div class="csc-label">Pending Trips</div>
        <div class="csc-value" style="color:#d97706;">{{ $summary['pending_count'] }}</div>
    </div>
    <div class="col-sum-card">
        <div class="csc-label">Total Outstanding</div>
        <div class="csc-value" style="color:#e53e3e;">₹{{ number_format($summary['total_outstanding'],0) }}</div>
    </div>
    <div class="col-sum-card">
        <div class="csc-label">Overdue</div>
        <div class="csc-value" style="color:#e53e3e;">{{ $summary['overdue'] }} trips</div>
    </div>
</div>

<div class="rpt-card">
    <div class="rpt-card-header">
        <h6><i class="ti-list mr-2" style="color:#d97706;"></i>Pending Collections ({{ $trips->count() }} records)</h6>
        <div style="display:flex;gap:8px;">
            <button onclick="exportColExcel()" class="btn btn-sm btn-outline-success" style="border-radius:8px;"><i class="ti-export mr-1"></i> Export Excel</button>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="ti-printer mr-1"></i> Print</button>
        </div>
    </div>
    <div class="rpt-table-wrap">
        <table class="table table-striped table-bordered" id="colTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Trip No</th>
                    <th>Trip Date</th>
                    <th>Party</th>
                    <th>Vehicle</th>
                    <th style="text-align:right;">Freight</th>
                    <th style="text-align:right;">Collected</th>
                    <th style="text-align:right;">Outstanding</th>
                    <th>Due Date</th>
                    <th style="text-align:center;">Payment Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trips as $i => $trip)
                @php
                    $isOverdue = $trip->collection_due_date && $trip->collection_due_date->isPast();
                    $payColors = [
                        'pending' => ['#fc8181','#fff5f5'],
                        'partial' => ['#f6ad55','#fffbeb'],
                    ];
                    $pc = $payColors[$trip->payment_status ?? 'pending'] ?? ['#8a94a6','#f4f6fb'];
                @endphp
                <tr class="{{ $isOverdue ? 'overdue-row' : '' }}">
                    <td>{{ $i+1 }}</td>
                    <td><strong style="color:#d97706;">{{ $trip->trip_no }}</strong></td>
                    <td>{{ $trip->trip_date?->format('d M Y') }}</td>
                    <td>{{ optional($trip->party)->company_name ?: optional($trip->party)->name }}</td>
                    <td>{{ optional($trip->vehicle)->vehicle_number ?: '—' }}</td>
                    <td style="text-align:right;font-weight:700;">₹{{ number_format($trip->freight_amount,0) }}</td>
                    <td style="text-align:right;color:#38a169;font-weight:700;">₹{{ number_format($trip->collected_amount,0) }}</td>
                    <td style="text-align:right;font-weight:800;color:#e53e3e;">₹{{ number_format($trip->outstanding_amount,0) }}</td>
                    <td>
                        @if($trip->collection_due_date)
                            <span style="font-weight:700;color:{{ $isOverdue ? '#e53e3e' : '#1a2340' }};">
                                {{ $trip->collection_due_date->format('d M Y') }}
                            </span>
                            @if($isOverdue)
                            <div style="font-size:10px;color:#e53e3e;font-weight:700;">
                                <i class="ti-alert" style="font-size:9px;"></i> OVERDUE {{ $trip->collection_due_date->diffForHumans() }}
                            </div>
                            @endif
                        @else
                            <span style="color:#b0bac9;">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="rpt-badge" style="background:{{ $pc[1] }};color:{{ $pc[0] }};">
                            {{ ucfirst($trip->payment_status ?? 'pending') }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center py-4" style="color:#b0bac9;">
                    <i class="ti-check-box" style="font-size:32px;display:block;margin-bottom:8px;color:#38a169;opacity:.5;"></i>
                    No pending collections found. All payments are up to date!
                </td></tr>
                @endforelse
            </tbody>
            @if($trips->count())
            <tfoot>
                <tr style="background:#f8fafc;font-weight:800;">
                    <td colspan="5" style="text-align:right;font-size:13px;">Totals</td>
                    <td style="text-align:right;color:#4338ca;">₹{{ number_format($trips->sum('freight_amount'),0) }}</td>
                    <td style="text-align:right;color:#38a169;">₹{{ number_format($trips->sum('collected_amount'),0) }}</td>
                    <td style="text-align:right;color:#e53e3e;">₹{{ number_format($summary['total_outstanding'],0) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

</div></div></div></div>
@endsection

@push('scripts')
<script>
function exportColExcel() {
    var table = document.getElementById('colTable');
    var wb = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    wb += '<head><meta charset="UTF-8"></head><body><table>' + table.innerHTML + '</table></body></html>';
    var blob = new Blob([wb], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    var url  = URL.createObjectURL(blob);
    var a    = document.createElement('a');
    a.href   = url;
    a.download = 'Collection_Report_{{ now()->format("Y-m-d") }}.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endpush
