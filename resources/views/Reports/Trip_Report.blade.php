@extends('layouts.app')

@section('content')
<style>
    .rpt-page {
        background: #f4f6fb;
    }

    .rpt-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 14px;
        padding: 20px 24px;
        color: #fff;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .rpt-header::before {
        content: '';
        position: absolute;
        top: -30px;
        right: -30px;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, .07);
        border-radius: 50%;
    }

    .rpt-header h4 {
        font-size: 18px;
        font-weight: 800;
        margin: 0 0 4px;
    }

    .rpt-header .sub {
        font-size: 12px;
        opacity: .8;
    }

    .rpt-filter {
        background: #fff;
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .rpt-filter .form-control {
        min-height: 40px;
        font-size: 13px;
        border-color: #e2e8f0;
        border-radius: 8px;
    }

    .rpt-filter .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, .12);
    }

    .rpt-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }

    .rpt-sum-card {
        background: #fff;
        border-radius: 10px;
        padding: 14px 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
        text-align: center;
    }

    .rpt-sum-card .rsc-label {
        font-size: 10px;
        font-weight: 700;
        color: #8a94a6;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .rpt-sum-card .rsc-value {
        font-size: 20px;
        font-weight: 800;
        color: #1a2340;
        margin-top: 2px;
    }

    .rpt-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        overflow: hidden;
    }

    .rpt-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-bottom: 1px solid #f0f2f7;
        background: #fafbff;
        flex-wrap: wrap;
        gap: 8px;
    }

    .rpt-card-header h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #1a2340;
    }

    .rpt-table-wrap {
        overflow-x: auto;
    }

    #rptTable {
        min-width: 1000px;
        margin-bottom: 0;
    }

    #rptTable th,
    #rptTable td {
        height: 48px;
        padding: 8px 12px;
        vertical-align: middle;
        border-color: #f0f2f7;
        font-size: 12px;
    }

    #rptTable th {
        background: #f8fafc;
        color: #14213d;
        font-weight: 800;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .4px;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .rpt-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }

    @media print {

        .pcoded-navbar,
        .pcoded-header,
        .pcoded-footer,
        .rpt-filter,
        .rpt-card-header .btn,
        .rpt-card-header .d-flex {
            display: none !important;
        }

        .rpt-page,
        .pcoded-content,
        .pcoded-inner-content,
        .main-body,
        .page-wrapper,
        .page-body {
            background: #fff !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .rpt-card {
            box-shadow: none !important;
            border: 1px solid #e2e8f0;
        }

        #rptTable th,
        #rptTable td {
            font-size: 11px;
            padding: 6px 8px;
        }
    }

    @media(max-width:767.98px) {
        .rpt-summary {
            grid-template-columns: repeat(2, 1fr);
        }

        .rpt-filter {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="pcoded-inner-content rpt-page">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">

                <div class="rpt-header">
                    <div class="row align-items-center">
                        <div class="col-md-8" style="position:relative;z-index:1;">
                            <h4><i class="ti-location-arrow mr-2"></i>Trip Report</h4>
                            <div class="sub">{{ $trips->count() }} trips found &bull; Total Freight: ₹{{ number_format($summary['total_freight'],0) }}</div>
                        </div>
                        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
                            <a href="{{ route('reports') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;margin-right:6px;">
                                <i class="ti-arrow-left mr-1"></i> Reports
                            </a>
                            <button onclick="exportToExcel()" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 14px;font-weight:600;margin-right:6px;">
                                <i class="ti-export mr-1"></i> Excel
                            </button>
                            <a href="{{ route('reports.trips.pdf', request()->query()) }}" target="_blank" class="btn btn-sm" style="background:#fff;color:#667eea;border-radius:8px;padding:7px 16px;font-weight:700;display:inline-flex;align-items:center;text-decoration:none;">
                                <i class="ti-printer mr-1"></i> Print
                            </a>
                        </div>
                    </div>
                </div>

                <form method="GET" action="{{ route('reports.trips') }}">
                    <div class="rpt-filter">
                        <input type="date" name="date_from" class="form-control" style="max-width:150px;" value="{{ request('date_from') }}" title="From Date">
                        <input type="date" name="date_to" class="form-control" style="max-width:150px;" value="{{ request('date_to') }}" title="To Date">
                        <select name="status" class="form-control" style="min-width:120px;max-width:150px;">
                            <option value="">All Status</option>
                            <option value="planned" {{ request('status') === 'planned'   ? 'selected' : '' }}>Planned</option>
                            <option value="running" {{ request('status') === 'running'   ? 'selected' : '' }}>Running</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <select name="driver_id" class="form-control" style="min-width:140px;max-width:180px;">
                            <option value="">All Drivers</option>
                            @foreach($drivers as $d)
                            <option value="{{ $d->id }}" {{ request('driver_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                        <select name="vehicle_id" class="form-control" style="min-width:140px;max-width:180px;">
                            <option value="">All Vehicles</option>
                            @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->vehicle_number }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm" style="border-radius:8px;padding:8px 18px;white-space:nowrap;">
                            <i class="ti-search mr-1"></i> Filter
                        </button>
                        <a href="{{ route('reports.trips') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;padding:8px 14px;white-space:nowrap;">
                            <i class="ti-close mr-1"></i> Clear
                        </a>
                    </div>
                </form>

                <div class="rpt-summary">
                    <div class="rpt-sum-card">
                        <div class="rsc-label">Total Trips</div>
                        <div class="rsc-value" style="color:#667eea;">{{ $summary['total_trips'] }}</div>
                    </div>
                    <div class="rpt-sum-card">
                        <div class="rsc-label">Total Freight</div>
                        <div class="rsc-value" style="color:#4338ca;">₹{{ number_format($summary['total_freight'],0) }}</div>
                    </div>
                    <div class="rpt-sum-card">
                        <div class="rsc-label">Collected</div>
                        <div class="rsc-value" style="color:#38a169;">₹{{ number_format($summary['total_collected'],0) }}</div>
                    </div>
                    <div class="rpt-sum-card">
                        <div class="rsc-label">Outstanding</div>
                        <div class="rsc-value" style="color:#e53e3e;">₹{{ number_format($summary['outstanding'],0) }}</div>
                    </div>
                    <div class="rpt-sum-card">
                        <div class="rsc-label">Total Expenses</div>
                        <div class="rsc-value" style="color:#d97706;">₹{{ number_format($summary['total_expenses'],0) }}</div>
                    </div>
                    <div class="rpt-sum-card">
                        <div class="rsc-label">Net Profit</div>
                        <div class="rsc-value" style="color:{{ $summary['net_profit'] >= 0 ? '#38a169' : '#e53e3e' }};">{{ $summary['net_profit'] >= 0 ? '+' : '' }}₹{{ number_format($summary['net_profit'],0) }}</div>
                    </div>
                </div>

                <div class="rpt-card">
                    <div class="rpt-card-header">
                        <h6><i class="ti-list mr-2" style="color:#667eea;"></i>Trip Details ({{ $trips->count() }} records)</h6>
                        <div style="display:flex;gap:8px;">
                            <button onclick="exportToExcel()" class="btn btn-sm btn-outline-success" style="border-radius:8px;">
                                <i class="ti-export mr-1"></i> Export Excel
                            </button>
                            <a href="{{ route('reports.trips.pdf', request()->query()) }}" target="_blank" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;">
                                <i class="ti-printer mr-1"></i> Print
                            </a>
                        </div>
                    </div>
                    <div class="rpt-table-wrap">
                        <table class="table table-striped table-bordered" id="rptTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Trip No</th>
                                    <th>Date</th>
                                    <th>Party</th>
                                    <th>Vehicle</th>
                                    <th>Driver</th>
                                    <th>Route</th>
                                    <th style="text-align:right;">Freight</th>
                                    <th style="text-align:right;">Expenses</th>
                                    <th style="text-align:right;">P&L</th>
                                    <th style="text-align:center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $statusCfg = [
                                'planned' => ['#667eea','#eef2ff'],
                                'running' => ['#d97706','#fffbeb'],
                                'completed' => ['#38a169','#f0fff4'],
                                'cancelled' => ['#e53e3e','#fff5f5'],
                                ];
                                @endphp
                                @forelse($trips as $i => $trip)
                                @php
                                $sc = $statusCfg[$trip->status] ?? ['#8a94a6','#f4f6fb'];
                                $pnl = $trip->net_profit;
                                @endphp
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td><strong style="color:#667eea;">{{ $trip->trip_no }}</strong></td>
                                    <td>{{ $trip->trip_date?->format('d M Y') }}</td>
                                    <td>{{ optional($trip->party)->company_name ?: optional($trip->party)->name }}</td>
                                    <td>{{ optional($trip->vehicle)->vehicle_number }}</td>
                                    <td>{{ optional($trip->driver)->name ?: '—' }}</td>
                                    <td>{{ $trip->from_location }} → {{ $trip->to_location }}</td>
                                    <td style="text-align:right;font-weight:700;">₹{{ number_format($trip->freight_amount,0) }}</td>
                                    <td style="text-align:right;color:#d97706;">₹{{ number_format($trip->total_expenses,0) }}</td>
                                    <td style="text-align:right;font-weight:800;color:{{ $pnl >= 0 ? '#38a169' : '#e53e3e' }};">
                                        {{ $pnl >= 0 ? '+' : '' }}₹{{ number_format($pnl,0) }}
                                    </td>
                                    <td style="text-align:center;">
                                        <span class="rpt-badge" style="background:{{ $sc[1] }};color:{{ $sc[0] }};">{{ ucfirst($trip->status) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4" style="color:#b0bac9;">No trips found for the selected filters.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($trips->count())
                            <tfoot>
                                <tr style="background:#f8fafc;font-weight:800;">
                                    <td colspan="7" style="text-align:right;font-size:13px;">Totals</td>
                                    <td style="text-align:right;color:#4338ca;">₹{{ number_format($summary['total_freight'],0) }}</td>
                                    <td style="text-align:right;color:#d97706;">₹{{ number_format($summary['total_expenses'],0) }}</td>
                                    <td style="text-align:right;color:{{ $summary['net_profit'] >= 0 ? '#38a169' : '#e53e3e' }};">
                                        {{ $summary['net_profit'] >= 0 ? '+' : '' }}₹{{ number_format($summary['net_profit'],0) }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function exportToExcel() {
        var table = document.getElementById('rptTable');
        var wb = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        wb += '<head><meta charset="UTF-8"><!--[if gte mso 9]><xml><' + 'x:ExcelWorkbook><' + 'x:ExcelWorksheets><' + 'x:ExcelWorksheet><' + 'x:Name>Trip Report</' + 'x:Name><' + 'x:WorksheetOptions><' + 'x:DisplayGridlines/></' + 'x:WorksheetOptions></' + 'x:ExcelWorksheet></' + 'x:ExcelWorksheets></' + 'x:ExcelWorkbook></xml><![endif]--></head>';
        wb += '<body><table>' + table.innerHTML + '</table></body></html>';
        var blob = new Blob([wb], {
            type: 'application/vnd.ms-excel;charset=utf-8;'
        });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'Trip_Report_{{ now()->format("Y-m-d") }}.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
</script>
@endpush