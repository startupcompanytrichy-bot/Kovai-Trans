@extends('layouts.app')

@section('content')
<style>
.rpt-page{background:#f4f6fb;}
.rpt-header{background:linear-gradient(135deg,#38a169 0%,#2f855a 100%);border-radius:14px;padding:20px 24px;color:#fff;margin-bottom:20px;position:relative;overflow:hidden;}
.rpt-header::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.07);border-radius:50%;}
.rpt-header h4{font-size:18px;font-weight:800;margin:0 0 4px;}
.rpt-header .sub{font-size:12px;opacity:.8;}
.rpt-filter{background:#fff;border-radius:12px;padding:14px 18px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:16px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.rpt-filter .form-control{min-height:40px;font-size:13px;border-color:#e2e8f0;border-radius:8px;}
.pnl-summary{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:16px;}
.pnl-sum-card{background:#fff;border-radius:10px;padding:14px 16px;box-shadow:0 2px 8px rgba(0,0,0,.05);text-align:center;}
.pnl-sum-card .psc-label{font-size:10px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.4px;}
.pnl-sum-card .psc-value{font-size:18px;font-weight:800;color:#1a2340;margin-top:2px;}
.rpt-card{background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;}
.rpt-card-header{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f0f2f7;background:#fafbff;flex-wrap:wrap;gap:8px;}
.rpt-card-header h6{margin:0;font-size:14px;font-weight:700;color:#1a2340;}
.rpt-table-wrap{overflow-x:auto;}
#pnlTable{min-width:900px;margin-bottom:0;}
#pnlTable th,#pnlTable td{height:48px;padding:8px 12px;vertical-align:middle;border-color:#f0f2f7;font-size:12px;}
#pnlTable th{background:#f8fafc;color:#14213d;font-weight:800;font-size:11px;text-transform:uppercase;letter-spacing:.4px;}
.profit-row td{background:#f0fff4!important;}
.loss-row td{background:#fff5f5!important;}
@media print{.pcoded-navbar,.pcoded-header,.pcoded-footer,.rpt-filter,.rpt-card-header .btn{display:none!important;}.rpt-page,.pcoded-content,.pcoded-inner-content,.main-body,.page-wrapper,.page-body{background:#fff!important;padding:0!important;margin:0!important;}}
@media(max-width:991.98px){.pnl-summary{grid-template-columns:repeat(3,1fr);}}
@media(max-width:767.98px){.pnl-summary{grid-template-columns:repeat(2,1fr);}.rpt-filter{flex-direction:column;align-items:stretch;}}
</style>

<div class="pcoded-inner-content rpt-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="rpt-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <h4><i class="ti-stats-up mr-2"></i>Profit & Loss Report</h4>
            <div class="sub">Completed trips only &bull; Net: {{ $summary['net_profit'] >= 0 ? '+' : '' }}₹{{ number_format($summary['net_profit'],0) }}</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('reports') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;margin-right:6px;">
                <i class="ti-arrow-left mr-1"></i> Reports
            </a>
            <button onclick="exportPnlExcel()" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 14px;font-weight:600;margin-right:6px;">
                <i class="ti-export mr-1"></i> Excel
            </button>
            <button onclick="window.print()" class="btn btn-sm" style="background:#fff;color:#38a169;border-radius:8px;padding:7px 16px;font-weight:700;">
                <i class="ti-printer mr-1"></i> Print
            </button>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('reports.pnl') }}">
<div class="rpt-filter">
    <input type="date" name="date_from" class="form-control" style="max-width:150px;" value="{{ request('date_from') }}">
    <input type="date" name="date_to" class="form-control" style="max-width:150px;" value="{{ request('date_to') }}">
    <button type="submit" class="btn btn-success btn-sm" style="border-radius:8px;padding:8px 18px;white-space:nowrap;">
        <i class="ti-search mr-1"></i> Filter
    </button>
    <a href="{{ route('reports.pnl') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;padding:8px 14px;">
        <i class="ti-close mr-1"></i> Clear
    </a>
</div>
</form>

<div class="pnl-summary">
    <div class="pnl-sum-card"><div class="psc-label">Total Trips</div><div class="psc-value" style="color:#667eea;">{{ $summary['total_trips'] }}</div></div>
    <div class="pnl-sum-card"><div class="psc-label">Total Income</div><div class="psc-value" style="color:#4338ca;">₹{{ number_format($summary['total_income'],0) }}</div></div>
    <div class="pnl-sum-card"><div class="psc-label">Total Expenses</div><div class="psc-value" style="color:#d97706;">₹{{ number_format($summary['total_expenses'],0) }}</div></div>
    <div class="pnl-sum-card"><div class="psc-label">Net Profit/Loss</div><div class="psc-value" style="color:{{ $summary['net_profit'] >= 0 ? '#38a169' : '#e53e3e' }};">{{ $summary['net_profit'] >= 0 ? '+' : '' }}₹{{ number_format($summary['net_profit'],0) }}</div></div>
    <div class="pnl-sum-card">
        <div class="psc-label">Profit / Loss Trips</div>
        <div style="display:flex;justify-content:center;gap:8px;margin-top:4px;">
            <span style="font-size:14px;font-weight:800;color:#38a169;">{{ $summary['profit_trips'] }}↑</span>
            <span style="font-size:14px;font-weight:800;color:#e53e3e;">{{ $summary['loss_trips'] }}↓</span>
        </div>
    </div>
</div>

<div class="rpt-card">
    <div class="rpt-card-header">
        <h6><i class="ti-list mr-2" style="color:#38a169;"></i>P&L Details ({{ $trips->count() }} completed trips)</h6>
        <div style="display:flex;gap:8px;">
            <button onclick="exportPnlExcel()" class="btn btn-sm btn-outline-success" style="border-radius:8px;"><i class="ti-export mr-1"></i> Export Excel</button>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="ti-printer mr-1"></i> Print</button>
        </div>
    </div>
    <div class="rpt-table-wrap">
        <table class="table table-bordered" id="pnlTable">
            <thead>
                <tr>
                    <th>#</th><th>Trip No</th><th>Date</th><th>Party</th><th>Route</th>
                    <th style="text-align:right;">Freight</th>
                    <th style="text-align:right;">Expenses</th>
                    <th style="text-align:right;">Net P&L</th>
                    <th style="text-align:center;">Result</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trips as $i => $trip)
                @php $pnl = $trip->net_profit; $isP = $trip->is_profitable; @endphp
                <tr class="{{ $isP ? 'profit-row' : 'loss-row' }}">
                    <td>{{ $i+1 }}</td>
                    <td><strong style="color:#667eea;">{{ $trip->trip_no }}</strong></td>
                    <td>{{ $trip->trip_date?->format('d M Y') }}</td>
                    <td>{{ optional($trip->party)->company_name ?: optional($trip->party)->name }}</td>
                    <td>{{ $trip->from_location }} → {{ $trip->to_location }}</td>
                    <td style="text-align:right;font-weight:700;">₹{{ number_format($trip->freight_amount,0) }}</td>
                    <td style="text-align:right;color:#d97706;">₹{{ number_format($trip->total_expenses,0) }}</td>
                    <td style="text-align:right;font-weight:800;color:{{ $isP ? '#38a169' : '#e53e3e' }};">
                        {{ $isP ? '+' : '' }}₹{{ number_format($pnl,0) }}
                    </td>
                    <td style="text-align:center;">
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;background:{{ $isP ? '#f0fff4' : '#fff5f5' }};color:{{ $isP ? '#38a169' : '#e53e3e' }};">
                            <i class="ti-arrow-{{ $isP ? 'up' : 'down' }}" style="font-size:9px;"></i>
                            {{ $isP ? 'Profit' : 'Loss' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-3" style="color:#b0bac9;">No completed trips found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div></div></div></div>
@endsection

@push('scripts')
<script>
function exportPnlExcel() {
    var table = document.getElementById('pnlTable');
    var wb = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    wb += '<head><meta charset="UTF-8"></head><body><table>' + table.innerHTML + '</table></body></html>';
    var blob = new Blob([wb], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    var url  = URL.createObjectURL(blob);
    var a    = document.createElement('a');
    a.href   = url;
    a.download = 'PnL_Report_{{ now()->format("Y-m-d") }}.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endpush
