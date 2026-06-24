@extends('layouts.app')

@section('content')
<style>
.rpt-page { background:#f4f6fb; }
.rpt-header { background:linear-gradient(135deg,#9333ea 0%,#7c3aed 100%); border-radius:14px; padding:20px 24px; color:#fff; margin-bottom:20px; position:relative; overflow:hidden; }
.rpt-header::before { content:''; position:absolute; top:-30px; right:-30px; width:120px; height:120px; background:rgba(255,255,255,.07); border-radius:50%; }
.rpt-header h4 { font-size:18px; font-weight:800; margin:0 0 4px; }
.rpt-header .sub { font-size:12px; opacity:.8; }
.rpt-filter { background:#fff; border-radius:12px; padding:14px 18px; box-shadow:0 2px 10px rgba(0,0,0,.06); margin-bottom:16px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.rpt-filter .form-control { min-height:40px; font-size:13px; border-color:#e2e8f0; border-radius:8px; }
.rpt-filter .form-control:focus { border-color:#9333ea; box-shadow:0 0 0 2px rgba(147,51,234,.12); }
.rpt-card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); overflow:hidden; }
.rpt-card-header { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid #f0f2f7; background:#fafbff; flex-wrap:wrap; gap:8px; }
.rpt-card-header h6 { margin:0; font-size:14px; font-weight:700; color:#1a2340; }
.rpt-table-wrap { overflow-x:auto; }
#ldgTable { min-width:900px; margin-bottom:0; }
#ldgTable th, #ldgTable td { height:44px; padding:6px 12px; vertical-align:middle; border-color:#f0f2f7; font-size:12px; }
#ldgTable th { background:#f8fafc; color:#14213d; font-weight:800; font-size:11px; text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; position:sticky; top:0; z-index:2; }
.rpt-badge { display:inline-flex; align-items:center; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700; text-transform:uppercase; }
.date-type-opt { display:none; }
.date-type-opt.active { display:inline-flex; flex-shrink:0; }

/* ── Print / PDF Styles (Invoice Format) ── */
@media print {
    body { background:#fff!important; font-family:"Segoe UI","Helvetica Neue",Arial,sans-serif; font-size:11px; color:#1e293b; margin:0; padding:0; }
    .pcoded-navbar,.pcoded-header,.pcoded-footer,.rpt-filter,.rpt-card-header .btn,.rpt-card-header .d-flex,.rpt-header .col-md-4 { display:none!important; }
    .rpt-page,.pcoded-content,.pcoded-inner-content,.main-body,.page-wrapper,.page-body { background:#fff!important; padding:0!important; margin:0!important; }
    .rpt-header { background:none!important; border-radius:0!important; padding:0!important; margin-bottom:0!important; color:#1e293b!important; }
    .rpt-header::before { display:none!important; }
    .rpt-header .col-md-8 { max-width:100%!important; flex:0 0 100%!important; }
    .rpt-header h4 { display:none!important; }
    .rpt-header .sub { display:none!important; }

    .rpt-card { border-radius:0!important; box-shadow:none!important; border:2px solid #1e3a5f!important; }
    .rpt-card-header { background:#f8fafc!important; border-bottom:1px solid #dce1ea!important; padding:8px 14px!important; }
    .rpt-card-header h6 { font-size:13px!important; }

    #ldgTable th { background:#1e3a5f!important; color:#fff!important; font-size:9px!important; padding:5px 8px!important; }
    #ldgTable td { padding:5px 8px!important; font-size:10px!important; }
    #ldgTable tfoot tr { background:#f1f5f9!important; }

    /* Invoice-style header */
    .pdf-header { display:block!important; border-bottom:1px solid #dce1ea!important; }
    .co-name { font-size:20px; font-weight:900; color:#0f172a; }
    .co-addr { font-size:9.5px; color:#64748b; line-height:1.7; }
    .co-extra { font-size:9px; color:#64748b; line-height:1.6; margin-top:2px; padding-top:2px; border-top:1px dashed #dce1ea; }
    .bt-nm{font-size:13px;font-weight:800;color:#0f172a;margin-bottom:2px}
    .bt-ad{font-size:10.5px;color:#475569;line-height:1.75}

    @page { margin:6mm 8mm; size:A4 portrait; }
}
@media screen {
    .pdf-header { display:none; }
    .pdf-header + div { display:none; }
}
</style>

<div class="pcoded-inner-content rpt-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

{{-- ══ PDF HEADER (screen hidden, print visible) ══ --}}
@php
    $coL1 = implode(', ', array_filter([$company->address ?? null, $company->district ?? null]));
    $coL2 = implode(', ', array_filter([$company->state ?? null, !empty($company->pincode) ? $company->pincode : null]));
    $logoHtml = !empty($company->logo)
        ? '<img src="' . asset('storage/' . $company->logo) . '" style="width:70px;height:56px;object-fit:contain;display:block" alt="">'
        : '<div style="width:70px;height:56px;border:1px dashed #bec9d5;border-radius:4px;background:#f3f6f9;display:table">
            <div style="display:table-cell;vertical-align:middle;text-align:center;font-size:9px;color:#94a3b8;font-weight:700;letter-spacing:.5px">LOGO</div>
          </div>';
    $coAddrHtml = '';
    if ($coL1) $coAddrHtml .= e($coL1) . ',<br>';
    if ($coL2) $coAddrHtml .= e($coL2) . '<br>';
    if (!empty($company->phone)) {
        $phones = e($company->phone) . (!empty($company->phone2) ? ',' . e($company->phone2) : '');
        $coAddrHtml .= 'Phone: ' . $phones . '<br>';
    }
    if (!empty($company->gst)) $coAddrHtml .= 'GSTIN: ' . e($company->gst) . '<br>';
    $coExtraHtml = '';
    if (!empty($company->pan)) $coExtraHtml .= 'PAN: ' . e($company->pan);
    if (!empty($company->pan) && !empty($company->email)) $coExtraHtml .= ' &nbsp;|&nbsp; ';
    if (!empty($company->email)) $coExtraHtml .= 'Email: ' . e($company->email);

@endphp

@php
    $btAddrHtml = '';
    $partyName = '';
    if ($selectedParty) {
        $partyName = $selectedParty->company_name ?: $selectedParty->name;
        $ptParts = array_filter([
            $selectedParty->address ?? null,
            $selectedParty->city ?? null,
            $selectedParty->state ?? null,
            !empty($selectedParty->pincode) ? $selectedParty->pincode : null,
        ]);
        if ($ptParts) $btAddrHtml .= e(implode(', ', $ptParts)) . '<br>';
        if (!empty($selectedParty->gst_no)) {
            $btAddrHtml .= 'GSTIN: ' . e($selectedParty->gst_no);
            if (!empty($selectedParty->pan_no) || !empty($selectedParty->phone)) $btAddrHtml .= ' &nbsp;|&nbsp; ';
        }
        if (!empty($selectedParty->pan_no)) {
            $btAddrHtml .= 'PAN: ' . e($selectedParty->pan_no);
            if (!empty($selectedParty->phone)) $btAddrHtml .= ' &nbsp;|&nbsp; ';
        }
        if (!empty($selectedParty->phone)) $btAddrHtml .= 'Phone: ' . e($selectedParty->phone);
    }
@endphp

<div class="pdf-header" style="border-bottom:1px solid #dce1ea;margin-bottom:0;">
    <table cellspacing="0" cellpadding="0" style="width:100%;">
        <tr>
            <td style="width:60%;padding:10px 0 8px 14px;vertical-align:top;">
                <table cellspacing="0" cellpadding="0" style="width:100%;">
                    <tr>
                        <td style="width:75px;vertical-align:top;padding-right:10px;">{!! $logoHtml !!}</td>
                        <td style="vertical-align:top;">
                            <div class="co-name" style="font-size:18px;">{{ strtoupper($company->company_name ?? 'Company Name') }}</div>
                            <div class="co-addr">{!! $coAddrHtml !!}</div>
                            @if($coExtraHtml)<div class="co-extra">{!! $coExtraHtml !!}</div>@endif
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:40%;padding:10px 14px 8px;vertical-align:top;">
                @if($selectedParty)
                <div style="font-size:8px;font-weight:800;color:#667eea;text-transform:uppercase;letter-spacing:.8px;margin-bottom:3px;">BILLING</div>
                <div class="bt-nm" style="font-size:12px;">{{ strtoupper($partyName) }}</div>
                <div class="bt-ad" style="font-size:9px;">{!! $btAddrHtml !!}</div>
                @endif
            </td>
        </tr>
    </table>
</div>

<div style="background:linear-gradient(135deg,#1e3a5f,#2d5a87);padding:5px 14px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #dce1ea;margin-bottom:10px;">
    <span style="font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:1.2px;color:#f8fafc;">Statements of Account</span>
    <span style="font-size:8px;font-weight:700;color:#94a3b8;">{{ $filterLabel ?? 'All Dates' }}</span>
</div>

{{-- ══ PAGE HEADER ══ --}}
<div class="rpt-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <h4><i class="ti-wallet mr-2"></i>Parties Payment Ledger</h4>
            <div class="sub">{{ $entries->count() }} entries &bull; Total Invoice: ₹{{ number_format($totalAmount,0) }}</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('reports') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;">
                <i class="ti-arrow-left mr-1"></i> Reports
            </a>
            <button onclick="exportLdgExcel()" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 14px;font-weight:600;">
                <i class="ti-export mr-1"></i> Excel
            </button>
            <button onclick="openPdf()" class="btn btn-sm" style="background:#fff;color:#9333ea;border-radius:8px;padding:7px 16px;font-weight:700;">
                <i class="ti-printer mr-1"></i> Print / PDF
            </button>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('reports.parties-payment-ledger') }}">
<div class="rpt-filter" style="flex-wrap:nowrap;overflow-x:auto;">
    <select name="party_id" class="form-control select2" data-placeholder="All Parties" style="min-width:180px;width:auto;">
        <option value=""></option>
        @foreach($parties as $p)
        <option value="{{ $p->id }}" {{ request('party_id') == $p->id ? 'selected' : '' }}>{{ $p->company_name ?: $p->name }}</option>
        @endforeach
    </select>

    <select name="date_type" id="dateType" class="form-control select2" data-placeholder="Date Filter" style="min-width:130px;width:auto;">
        <option value="">Financial Year</option>
        <option value="month" {{ request('date_type') === 'month' ? 'selected' : '' }}>Month</option>
        <option value="year" {{ request('date_type') === 'year' ? 'selected' : '' }}>Year</option>
        <option value="date" {{ request('date_type') === 'date' ? 'selected' : '' }}>Date</option>
        <option value="range" {{ request('date_type') === 'range' ? 'selected' : '' }}>Range</option>
    </select>

    <div id="optMonth" class="date-type-opt {{ request('date_type') === 'month' ? 'active' : '' }}">
        <input type="month" name="month" class="form-control" style="max-width:160px;" value="{{ request('month') }}">
    </div>
    <div id="optYear" class="date-type-opt {{ request('date_type') === 'year' ? 'active' : '' }}">
        <select name="year" class="form-control" style="max-width:120px;">
            <option value="">Select Year</option>
            @for($y = date('Y'); $y >= date('Y') - 10; $y--)
            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </div>
    <div id="optDate" class="date-type-opt {{ request('date_type') === 'date' ? 'active' : '' }}">
        <input type="date" name="exact_date" class="form-control" style="max-width:160px;" value="{{ request('exact_date') }}">
    </div>
    <div id="optRange" class="date-type-opt {{ request('date_type') === 'range' ? 'active' : '' }}">
        <input type="date" name="date_from" class="form-control" style="max-width:150px;" value="{{ request('date_from') }}" placeholder="From">
        <input type="date" name="date_to" class="form-control" style="max-width:150px;" value="{{ request('date_to') }}" placeholder="To">
    </div>

    <button type="submit" class="btn btn-primary btn-sm" style="border-radius:8px;padding:8px 18px;white-space:nowrap;background:#9333ea;border-color:#9333ea;">
        <i class="ti-search mr-1"></i> Filter
    </button>
    <a href="{{ route('reports.parties-payment-ledger') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;padding:8px 14px;">
        <i class="ti-close mr-1"></i> Clear
    </a>
</div>
</form>

@if($selectedParty)
<div style="background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);padding:12px 18px;margin-bottom:12px;display:flex;align-items:flex-start;gap:20px;flex-wrap:wrap;">
    <div>
        <div style="font-size:10px;font-weight:800;color:#667eea;text-transform:uppercase;letter-spacing:.6px;margin-bottom:2px;">BILLING</div>
        <div style="font-size:14px;font-weight:900;color:#0f172a;">{{ strtoupper($partyName) }}</div>
    </div>
    @if($btAddrHtml)
    <div style="font-size:11px;color:#475569;line-height:1.7;">{!! $btAddrHtml !!}</div>
    @endif
</div>
@endif

<div class="rpt-card">
    <div class="rpt-card-header">
        <h6><i class="ti-list mr-2" style="color:#9333ea;"></i>Account Summary ({{ $entries->count() }} entries)</h6>
        <div style="display:flex;gap:8px;">
            <button onclick="exportLdgExcel()" class="btn btn-sm btn-outline-success" style="border-radius:8px;">
                <i class="ti-export mr-1"></i> Export Excel
            </button>
            <button onclick="openPdf()" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">
                <i class="ti-printer mr-1"></i> Print / PDF
            </button>
        </div>
    </div>
    <div class="rpt-table-wrap">
        <table class="table table-bordered" id="ldgTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Transaction</th>
                    <th>Details</th>
                    <th style="text-align:right;">Amount (₹)</th>
                    <th style="text-align:right;">Payment (₹)</th>
                    <th style="text-align:right;">Balance (₹)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $i => $e)
                @php $isInv = $e->transaction_type === 'Invoice'; @endphp
                <tr style="{{ !$isInv ? 'background:#fafbff;' : '' }}">
                    <td>{{ $i+1 }}</td>
                    <td>{{ $e->date?->format('d M Y') ?: '—' }}</td>
                    <td>
                        @if($isInv)
                        <span class="rpt-badge" style="background:#eef2ff;color:#4338ca;">Invoice</span>
                        @else
                        <span class="rpt-badge" style="background:#f0fff4;color:#38a169;">Payment Received</span>
                        @endif
                    </td>
                    <td>
                        @if($isInv)
                        <strong>{{ $e->details }}</strong>
                        @else
                        {{ $e->details }}
                        @endif
                        <div style="font-size:10px;color:#8a94a6;margin-top:2px;">
                            {{ optional($e->party)->company_name ?: optional($e->party)->name ?: '—' }}
                            @if($e->vehicle) | {{ $e->vehicle->vehicle_number }} @endif
                        </div>
                    </td>
                    <td style="text-align:right;font-weight:700;color:#4338ca;">
                        {{ $isInv ? '₹' . number_format($e->amount,0) : '—' }}
                    </td>
                    <td style="text-align:right;font-weight:700;color:#38a169;">
                        {{ !$isInv ? '₹' . number_format($e->payment,0) : '—' }}
                    </td>
                    <td style="text-align:right;font-weight:800;color:{{ $e->balance > 0 ? '#e53e3e' : '#38a169' }};">
                        ₹{{ number_format($e->balance,0) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4" style="color:#b0bac9;">No records found for the selected filters.</td>
                </tr>
                @endforelse
            </tbody>
            @if($entries->count())
            <tfoot>
                <tr style="background:#f8fafc;font-weight:800;font-size:13px;">
                    <td colspan="4" style="text-align:right;">Total</td>
                    <td style="text-align:right;color:#4338ca;">₹{{ number_format($totalAmount,0) }}</td>
                    <td style="text-align:right;color:#38a169;">₹{{ number_format($totalPayment,0) }}</td>
                    <td style="text-align:right;color:{{ $totalBalance > 0 ? '#e53e3e' : '#38a169' }};">₹{{ number_format($totalBalance,0) }}</td>
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
$(function () {
    if ($.fn.select2) {
        $('.rpt-filter select.select2').select2({
            width: 'style',
            allowClear: true,
            placeholder: function () { return $(this).data('placeholder'); }
        });
        $('#dateType').on('select2:select select2:clear', function () {
            toggleDateOpts();
        });
    }
});
function toggleDateOpts() {
    var t = document.getElementById('dateType').value;
    ['optMonth','optYear','optDate','optRange'].forEach(function(id) {
        document.getElementById(id).classList.toggle('active', id.replace('opt','').toLowerCase() === t);
    });
}
function openPdf() {
    var params = new URLSearchParams(window.location.search);
    window.open('{{ route('reports.parties-payment-ledger.pdf') }}?' + params.toString(), '_blank');
}
function exportLdgExcel() {
    var table = document.getElementById('ldgTable');
    var wb = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    wb += '<head><meta charset="UTF-8"><!--[if gte mso 9]><xml><' + 'x:ExcelWorkbook><' + 'x:ExcelWorksheets><' + 'x:ExcelWorksheet><' + 'x:Name>Payment Ledger</' + 'x:Name><' + 'x:WorksheetOptions><' + 'x:DisplayGridlines/></' + 'x:WorksheetOptions></' + 'x:ExcelWorksheet></' + 'x:ExcelWorksheets></' + 'x:ExcelWorkbook></xml><![endif]--></head>';
    wb += '<body><table>' + table.innerHTML + '</table></body></html>';
    var blob = new Blob([wb], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'Parties_Payment_Ledger_{{ now()->format("Y-m-d") }}.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endpush
