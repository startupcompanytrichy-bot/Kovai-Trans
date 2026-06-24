@extends('layouts.app')

@section('content')
<style>
.rpt-page{background:#f4f6fb;}
.rpt-header{background:linear-gradient(135deg,#e53e3e 0%,#c53030 100%);border-radius:14px;padding:20px 24px;color:#fff;margin-bottom:20px;position:relative;overflow:hidden;}
.rpt-header::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.07);border-radius:50%;}
.rpt-header h4{font-size:18px;font-weight:800;margin:0 0 4px;}
.rpt-header .sub{font-size:12px;opacity:.8;}
.rpt-filter{background:#fff;border-radius:12px;padding:14px 18px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:16px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.rpt-filter .form-control{min-height:40px;font-size:13px;border-color:#e2e8f0;border-radius:8px;}
.rpt-card{background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;}
.rpt-card-header{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f0f2f7;background:#fafbff;flex-wrap:wrap;gap:8px;}
.rpt-card-header h6{margin:0;font-size:14px;font-weight:700;color:#1a2340;}
.rpt-table-wrap{overflow-x:auto;}
#expRptTable{min-width:800px;margin-bottom:0;}
#expRptTable th,#expRptTable td{height:48px;padding:8px 12px;vertical-align:middle;border-color:#f0f2f7;font-size:12px;}
#expRptTable th{background:#f8fafc;color:#14213d;font-weight:800;font-size:11px;text-transform:uppercase;letter-spacing:.4px;}
.cat-chip{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;}
@media print{.pcoded-navbar,.pcoded-header,.pcoded-footer,.rpt-filter,.rpt-card-header .btn{display:none!important;}.rpt-page,.pcoded-content,.pcoded-inner-content,.main-body,.page-wrapper,.page-body{background:#fff!important;padding:0!important;margin:0!important;}}
@media(max-width:767.98px){.rpt-filter{flex-direction:column;align-items:stretch;}}
</style>

<div class="pcoded-inner-content rpt-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="rpt-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <h4><i class="ti-receipt mr-2"></i>Expense Report</h4>
            <div class="sub">{{ $expenses->count() }} entries &bull; Total: ₹{{ number_format($summary['total'],0) }}</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('reports') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;margin-right:6px;">
                <i class="ti-arrow-left mr-1"></i> Reports
            </a>
            <button onclick="exportExpExcel()" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 14px;font-weight:600;margin-right:6px;">
                <i class="ti-export mr-1"></i> Excel
            </button>
            <button onclick="window.print()" class="btn btn-sm" style="background:#fff;color:#e53e3e;border-radius:8px;padding:7px 16px;font-weight:700;">
                <i class="ti-printer mr-1"></i> Print
            </button>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('reports.expenses') }}">
<div class="rpt-filter">
    <input type="date" name="date_from" class="form-control" style="max-width:150px;" value="{{ request('date_from') }}">
    <input type="date" name="date_to" class="form-control" style="max-width:150px;" value="{{ request('date_to') }}">
    <select name="category" class="form-control" style="min-width:130px;max-width:160px;">
        <option value="">All Categories</option>
        @foreach($categories as $key => $cat)
        <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $cat['label'] }}</option>
        @endforeach
    </select>
    <select name="vehicle_id" class="form-control" style="min-width:140px;max-width:180px;">
        <option value="">All Vehicles</option>
        @foreach($vehicles as $v)
        <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->vehicle_number }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-danger btn-sm" style="border-radius:8px;padding:8px 18px;white-space:nowrap;">
        <i class="ti-search mr-1"></i> Filter
    </button>
    <a href="{{ route('reports.expenses') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;padding:8px 14px;">
        <i class="ti-close mr-1"></i> Clear
    </a>
</div>
</form>

{{-- Category breakdown --}}
<div class="row mb-3">
    @foreach($summary['by_category'] as $key => $amt)
    @php $cat = $categories[$key] ?? ['label'=>ucfirst($key),'icon'=>'ti-more-alt','color'=>'#8a94a6','bg'=>'#f4f6fb']; @endphp
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
    @endforeach
</div>

<div class="rpt-card">
    <div class="rpt-card-header">
        <h6><i class="ti-list mr-2" style="color:#e53e3e;"></i>Expense Details ({{ $expenses->count() }} records)</h6>
        <div style="display:flex;gap:8px;">
            <button onclick="exportExpExcel()" class="btn btn-sm btn-outline-success" style="border-radius:8px;"><i class="ti-export mr-1"></i> Export Excel</button>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="ti-printer mr-1"></i> Print</button>
        </div>
    </div>
    <div class="rpt-table-wrap">
        <table class="table table-striped table-bordered" id="expRptTable">
            <thead>
                <tr>
                    <th>#</th><th>Date</th><th>Category</th><th>Trip</th>
                    <th>Vehicle</th><th>Driver</th><th>Notes</th>
                    <th style="text-align:right;">Amount</th><th style="text-align:center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $i => $exp)
                @php $cat = $categories[$exp->category] ?? ['label'=>ucfirst($exp->category),'icon'=>'ti-more-alt','color'=>'#8a94a6','bg'=>'#f4f6fb']; @endphp
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $exp->expense_date->format('d M Y') }}</td>
                    <td><span class="cat-chip" style="background:{{ $cat['bg'] }};color:{{ $cat['color'] }};"><i class="{{ $cat['icon'] }}" style="font-size:9px;"></i> {{ $cat['label'] }}</span></td>
                    <td>{{ optional($exp->trip)->trip_no ?: '—' }}</td>
                    <td>{{ optional($exp->vehicle)->vehicle_number ?: '—' }}</td>
                    <td>{{ optional($exp->driver)->name ?: '—' }}</td>
                    <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $exp->notes ?: '—' }}</td>
                    <td style="text-align:right;font-weight:800;color:#e53e3e;">₹{{ number_format($exp->amount,0) }}</td>
                    <td style="text-align:center;">
                        @php $sc = ['pending'=>['#d97706','#fffbeb'],'approved'=>['#38a169','#f0fff4'],'rejected'=>['#e53e3e','#fff5f5']][$exp->status] ?? ['#8a94a6','#f4f6fb']; @endphp
                        <span style="display:inline-block;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;background:{{ $sc[1] }};color:{{ $sc[0] }};">{{ ucfirst($exp->status) }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-3" style="color:#b0bac9;">No expenses found.</td></tr>
                @endforelse
            </tbody>
            @if($expenses->count())
            <tfoot>
                <tr style="background:#f8fafc;">
                    <td colspan="7" style="text-align:right;font-weight:800;font-size:13px;">Total</td>
                    <td style="text-align:right;font-weight:800;font-size:14px;color:#e53e3e;">₹{{ number_format($summary['total'],0) }}</td>
                    <td></td>
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
function exportExpExcel() {
    var table = document.getElementById('expRptTable');
    var wb = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    wb += '<head><meta charset="UTF-8"></head><body><table>' + table.innerHTML + '</table></body></html>';
    var blob = new Blob([wb], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    var url  = URL.createObjectURL(blob);
    var a    = document.createElement('a');
    a.href   = url;
    a.download = 'Expense_Report_{{ now()->format("Y-m-d") }}.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endpush
