@extends('layouts.app')

@section('content')
<style>
    .rpt-page {
        background: #f4f6fb;
    }

    .rpt-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 14px;
        padding: 14px 24px;
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
        align-items: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    .rpt-filter .filter-group {
        display: flex;
        flex-direction: column;
        gap: 3px;
        min-width: 130px;
    }

    .rpt-filter .filter-group label {
        font-size: 10px;
        font-weight: 700;
        color: #8a94a6;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .rpt-filter .form-control {
        min-height: 38px;
        font-size: 13px;
        border-color: #e2e8f0;
        border-radius: 8px;
    }

    .rpt-filter .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, .12);
    }

    .rpt-filter .filter-actions {
        display: flex;
        gap: 6px;
        align-items: center;
        padding-bottom: 1px;
    }

    .rpt-filter .filter-actions .btn {
        border-radius: 8px;
        padding: 8px 16px;
        white-space: nowrap;
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
        min-width: 800px;
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

    .progress-emi {
        height: 6px;
        border-radius: 4px;
        background: #edf2f7;
        min-width: 80px;
    }

    .progress-emi .bar {
        height: 100%;
        border-radius: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        transition: width .3s;
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
                            <h4><i class="ti-calendar mr-2"></i>EMI Details Report</h4>
                            <div class="sub">{{ $summary['total_loans'] }} loans &bull; Total Loan: ₹{{ number_format($summary['total_loan_amount'],0) }} &bull; Outstanding: ₹{{ number_format($summary['total_outstanding'],0) }}</div>
                        </div>
                        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
                            <a href="{{ route('reports') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;margin-right:6px;">
                                <i class="ti-arrow-left mr-1"></i> Reports
                            </a>
                            <button onclick="exportToExcel()" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 14px;font-weight:600;margin-right:6px;">
                                <i class="ti-export mr-1"></i> Excel
                            </button>
                            <a href="{{ route('reports.emi.pdf', request()->query()) }}" target="_blank" class="btn btn-sm" style="background:#fff;color:#667eea;border-radius:8px;padding:7px 16px;font-weight:700;display:inline-flex;align-items:center;text-decoration:none;">
                                <i class="ti-printer mr-1"></i> Print
                            </a>
                        </div>
                    </div>
                </div>

                <form method="GET" action="{{ route('reports.emi') }}" id="filterForm">
                    <div class="rpt-filter">
                        <div class="filter-group">
                            <label>From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="filter-group">
                            <label>To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="filter-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Vehicle</label>
                            <select name="vehicle_id" class="form-control">
                                <option value="">All Vehicles</option>
                                @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->vehicle_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Vendor</label>
                            <select name="financier" class="form-control">
                                <option value="">All Vendors</option>
                                @foreach($financiers as $f)
                                <option value="{{ $f }}" {{ request('financier') === $f ? 'selected' : '' }}>{{ $f }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti-search mr-1"></i> Filter
                            </button>
                            <a href="{{ route('reports.emi') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti-close mr-1"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>

                <div class="rpt-card">
                    <div class="rpt-card-header">
                        <h6><i class="ti-list mr-2" style="color:#667eea;"></i>EMI Loan Details ({{ $summary['total_loans'] }} records)</h6>
                        <div style="display:flex;gap:8px;">
                            <button onclick="exportToExcel()" class="btn btn-sm btn-outline-success" style="border-radius:8px;">
                                <i class="ti-export mr-1"></i> Export Excel
                            </button>
                            <a href="{{ route('reports.emi.pdf', request()->query()) }}" target="_blank" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;">
                                <i class="ti-printer mr-1"></i> Print
                            </a>
                        </div>
                    </div>
                    <div class="rpt-table-wrap">
                        <table class="table table-striped table-bordered" id="rptTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Vehicle Register Number</th>
                                    <th>Financier</th>
                                    <th style="text-align:right;">Loan Amount (₹)</th>
                                    <th style="text-align:right;">Interest (₹)</th>
                                    <th style="text-align:right;">Total Amount (₹)</th>
                                    <th style="text-align:center;">Total EMIs</th>
                                    <th style="text-align:center;">Total Number of Dues</th>
                                    <th style="text-align:center;">Next Due Date</th>
                                    <th style="text-align:right;">Monthly EMI (₹)</th>
                                    <th style="text-align:right;">Total Paid Amount (₹)</th>
                                    <th style="text-align:right;">Balance Amount (₹)</th>
                                    <th style="text-align:center;">Loan Start Date</th>
                                    <th style="text-align:center;">Loan End Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $statusColors = [
                                'active' => ['#667eea','#eef2ff'],
                                'overdue' => ['#e53e3e','#fff5f5'],
                                'closed' => ['#38a169','#f0fff4'],
                                ];
                                @endphp
                                @forelse($emis as $i => $e)
                                @php
                                $sc = $statusColors[$e->status] ?? ['#8a94a6','#f4f6fb'];
                                $totalPaidFromPayments = (float) $e->payments->sum('amount_paid');
                                $balanceEmis = ($e->total_emis ?? 0) - ($e->paid_emis ?? 0);
                                $totalAmount = ($e->loan_amount ?? 0) + ($e->interest_amount ?? 0);
                                $balanceAmount = $totalAmount - $totalPaidFromPayments;
                                @endphp
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ optional($e->vehicle)->vehicle_number ?? '—' }}</td>
                                    <td><strong style="color:#667eea;">{{ $e->financier_name ?? '—' }}</strong></td>
                                    <td style="text-align:right;font-weight:700;">₹{{ number_format($e->loan_amount,0) }}</td>
                                    <td style="text-align:right;font-weight:700;">₹{{ number_format($e->interest_amount ?? 0,0) }}</td>
                                    <td style="text-align:right;font-weight:700;color:#4338ca;">₹{{ number_format($totalAmount,0) }}</td>
                                    <td style="text-align:center;font-weight:600;">{{ $e->total_emis ?? 0 }}</td>
                                    <td style="text-align:center;font-weight:600;color:#e53e3e;">{{ $balanceEmis }}</td>
                                    <td style="text-align:center;{{ $e->is_overdue ? 'color:#e53e3e;font-weight:700;' : '' }}">
                                        {{ $e->next_due_date ? $e->next_due_date->format('d M Y') : '—' }}
                                    </td>
                                    <td style="text-align:right;font-weight:700;">₹{{ number_format($e->emi_amount,0) }}</td>
                                    <td style="text-align:right;font-weight:700;color:#38a169;">₹{{ number_format($totalPaidFromPayments,0) }}</td>
                                    <td style="text-align:right;font-weight:700;color:#d97706;">₹{{ number_format($balanceAmount,0) }}</td>
                                    <td style="text-align:center;">
                                        {{ $e->loan_start_date ? $e->loan_start_date->format('d M Y') : '—' }}
                                    </td>
                                    <td style="text-align:center;">
                                        {{ $e->loan_end_date ? $e->loan_end_date->format('d M Y') : '—' }}
                                    </td>
                                    @empty
                                <tr>
                                    <td colspan="14" class="text-center py-4" style="color:#b0bac9;">No EMI records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($emis->count())
                            <tfoot>
                                <tr style="background:#f8fafc;font-weight:800;">
                                    <td colspan="3" style="text-align:right;font-size:13px;">Totals</td>
                                    <td style="text-align:right;color:#4338ca;">₹{{ number_format($summary['total_loan_amount'],0) }}</td>
                                    <td style="text-align:right;color:#d97706;">₹{{ number_format($summary['total_interest_amount'] ?? 0,0) }}</td>
                                    <td style="text-align:right;color:#4338ca;">₹{{ number_format($summary['total_amount'] ?? 0,0) }}</td>
                                    <td style="text-align:center;">{{ $summary['total_emis'] ?? 0 }}</td>
                                    <td style="text-align:center;color:#e53e3e;">{{ $summary['total_balance_emis'] ?? 0 }}</td>
                                    <td></td>
                                    <td style="text-align:right;color:#d97706;">₹{{ number_format($summary['total_emi_monthly'],0) }}</td>
                                    <td style="text-align:right;color:#38a169;">₹{{ number_format($summary['total_paid_amount'] ?? 0,0) }}</td>
                                    <td style="text-align:right;color:#d97706;">₹{{ number_format($summary['total_balance_amount'] ?? 0,0) }}</td>
                                    <td></td>
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
        wb += '<head><meta charset="UTF-8"><!--[if gte mso 9]><xml><' + 'x:ExcelWorkbook><' + 'x:ExcelWorksheets><' + 'x:ExcelWorksheet><' + 'x:Name>EMI Details</' + 'x:Name><' + 'x:WorksheetOptions><' + 'x:DisplayGridlines/></' + 'x:WorksheetOptions></' + 'x:ExcelWorksheet></' + 'x:ExcelWorksheets></' + 'x:ExcelWorkbook></xml><![endif]--></head>';
        wb += '<body><table>' + table.innerHTML + '</table></body></html>';
        var blob = new Blob([wb], {
            type: 'application/vnd.ms-excel;charset=utf-8;'
        });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'EMI_Details_Report_{{ now()->format("Y-m-d") }}.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
</script>
@endpush