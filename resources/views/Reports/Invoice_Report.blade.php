@extends('layouts.app')

@section('content')
<style>
.rpt-page { background:#f4f6fb; }
.rpt-header {
    background:linear-gradient(135deg,#1a2340 0%,#2d3a5e 60%,#4338ca 100%);
    border-radius:14px; padding:20px 24px; color:#fff;
    margin-bottom:20px; position:relative; overflow:hidden;
}
.rpt-header::before { content:''; position:absolute; top:-30px; right:-30px; width:120px; height:120px; background:rgba(255,255,255,.06); border-radius:50%; }
.rpt-header h4 { font-size:18px; font-weight:800; margin:0 0 4px; position:relative; z-index:1; }
.rpt-header .sub { font-size:12px; opacity:.8; position:relative; z-index:1; }
.inv-rpt-summary { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:16px; }
.inv-rpt-card { background:#fff; border-radius:10px; padding:14px 16px; box-shadow:0 1px 6px rgba(0,0,0,.06); display:flex; align-items:center; gap:12px; border-left:3px solid transparent; }
.inv-rpt-card .ic-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.inv-rpt-card .ic-label { font-size:10px; font-weight:700; color:#8a94a6; text-transform:uppercase; letter-spacing:.3px; }
.inv-rpt-card .ic-value { font-size:18px; font-weight:800; color:#1a2340; line-height:1.2; }
.rpt-filter { background:#fff; border-radius:12px; padding:14px 18px; box-shadow:0 2px 10px rgba(0,0,0,.06); margin-bottom:16px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.rpt-filter .form-control { min-height:40px; font-size:13px; border-color:#e2e8f0; border-radius:8px; }
.rpt-table-card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); overflow:hidden; }
.rpt-table-hdr { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid #f0f2f7; background:#fafbff; flex-wrap:wrap; gap:8px; }
.rpt-table-hdr h6 { margin:0; font-size:13px; font-weight:700; color:#1a2340; }
#invRptTable { min-width:1050px; margin-bottom:0; }
#invRptTable th { background:#f8fafc; color:#14213d; font-weight:800; font-size:11px; text-transform:uppercase; letter-spacing:.4px; padding:9px 12px; border-color:#f0f2f7; white-space:nowrap; }
#invRptTable td { padding:10px 12px; border-color:#f0f2f7; vertical-align:middle; font-size:13px; }
#invRptTable tfoot td { background:#f0f4ff; font-weight:800; border-top:2px solid #c7d2fe; }
.rpt-badge { display:inline-flex; align-items:center; gap:3px; padding:3px 8px; border-radius:20px; font-size:10px; font-weight:700; text-transform:uppercase; }
.inv-check { width:15px; height:15px; cursor:pointer; accent-color:#4338ca; }
/* bulk bar */
.sel-bar { display:none; align-items:center; gap:8px; background:#1a2340; border-radius:8px; padding:7px 14px; color:#fff; font-size:13px; font-weight:600; }
.sel-bar.show { display:flex; }
.sel-bar .sb-count { background:rgba(255,255,255,.2); padding:2px 10px; border-radius:20px; font-size:12px; }
/* print overlay */
#printOverlay { display:none; position:fixed; inset:0; background:#fff; z-index:9999; overflow-y:auto; padding:24px; }
#printOverlay.show { display:block; }
@media(max-width:991.98px) { .inv-rpt-summary { grid-template-columns:repeat(2,1fr); } }
@media print {
    #printOverlay { display:block !important; position:static !important; padding:0 !important; }
    body > *:not(#printOverlay) { display:none !important; }
    .pcoded-navbar,.pcoded-header,.pcoded-footer,.po-toolbar { display:none !important; }
}
</style>

<div class="pcoded-inner-content rpt-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="rpt-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:10px;">
        <div>
            <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;letter-spacing:.5px;margin-bottom:6px;">
                <i class="ti-receipt"></i> Invoice Report
            </div>
            <h4>Invoice Report</h4>
            <div class="sub">Select invoices and print with full trip details.</div>
        </div>
        <div style="position:relative;z-index:1;">
            <a href="{{ route('reports') }}" class="btn btn-sm" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);color:#fff;border-radius:8px;padding:7px 16px;font-weight:600;">
                <i class="ti-arrow-left mr-1"></i> Reports
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

<div class="inv-rpt-summary">
    <div class="inv-rpt-card" style="border-left-color:#4338ca;">
        <div class="ic-icon" style="background:#eef2ff;color:#4338ca;"><i class="ti-receipt"></i></div>
        <div><div class="ic-label">Total Invoices</div><div class="ic-value" style="color:#4338ca;">{{ $summary['total_invoices'] }}</div></div>
    </div>
    <div class="inv-rpt-card" style="border-left-color:#667eea;">
        <div class="ic-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-money"></i></div>
        <div><div class="ic-label">Grand Total (incl. Tax)</div><div class="ic-value" style="color:#667eea;font-size:15px;">₹{{ number_format($summary['total_grand'],0) }}</div></div>
    </div>
    <div class="inv-rpt-card" style="border-left-color:#38a169;">
        <div class="ic-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-check"></i></div>
        <div><div class="ic-label">Collected</div><div class="ic-value" style="color:#38a169;font-size:15px;">₹{{ number_format($summary['total_collected'],0) }}</div></div>
    </div>
    <div class="inv-rpt-card" style="border-left-color:#e53e3e;">
        <div class="ic-icon" style="background:#fff5f5;color:#e53e3e;"><i class="ti-alert"></i></div>
        <div><div class="ic-label">Balance Due</div><div class="ic-value" style="color:#e53e3e;font-size:15px;">₹{{ number_format($summary['total_balance'],0) }}</div></div>
    </div>
</div>

<form method="GET" action="{{ route('reports.invoices') }}" id="filterForm">
<div class="rpt-filter">
    <div style="flex:1;min-width:160px;">
        <label style="font-size:11px;font-weight:700;color:#596579;display:block;margin-bottom:4px;">From Date</label>
        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
    </div>
    <div style="flex:1;min-width:160px;">
        <label style="font-size:11px;font-weight:700;color:#596579;display:block;margin-bottom:4px;">To Date</label>
        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
    </div>
    <div style="min-width:140px;">
        <label style="font-size:11px;font-weight:700;color:#596579;display:block;margin-bottom:4px;">Invoice Type</label>
        <select name="invoice_type" class="form-control">
            <option value="">All Types</option>
            <option value="normal"  {{ request('invoice_type')==='normal'  ? 'selected' : '' }}>Normal</option>
            <option value="rcm"     {{ request('invoice_type')==='rcm'     ? 'selected' : '' }}>RCM</option>
            <option value="exempt"  {{ request('invoice_type')==='exempt'  ? 'selected' : '' }}>Exempt</option>
        </select>
    </div>
    <div style="min-width:140px;">
        <label style="font-size:11px;font-weight:700;color:#596579;display:block;margin-bottom:4px;">Payment Status</label>
        <select name="payment_status" class="form-control">
            @php $selStatus = request('payment_status', 'completed'); @endphp
            <option value="all"       {{ $selStatus === 'all'       ? 'selected' : '' }}>All Status</option>
            <option value="pending"   {{ $selStatus === 'pending'   ? 'selected' : '' }}>Pending</option>
            <option value="partial"   {{ $selStatus === 'partial'   ? 'selected' : '' }}>Partial</option>
            <option value="completed" {{ $selStatus === 'completed' ? 'selected' : '' }}>✓ Payment Collected</option>
        </select>
    </div>
    <div style="align-self:flex-end;display:flex;gap:6px;flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary btn-sm" style="border-radius:8px;min-height:40px;padding:0 16px;font-weight:700;">
            <i class="ti-search mr-1"></i> Filter
        </button>
        <a href="{{ route('reports.invoices') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;min-height:40px;padding:0 12px;display:inline-flex;align-items:center;justify-content:center;">
            <i class="ti-close"></i>
        </a>
    </div>
</div>
</form>

<div class="rpt-table-card">
    <div class="rpt-table-hdr">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <h6>
                <i class="ti-receipt mr-1" style="color:#4338ca;"></i> Invoice List
                <span style="background:#eef2ff;color:#4338ca;font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;margin-left:6px;">{{ $rows->count() }}</span>
            </h6>
            <div class="sel-bar" id="selBar">
                <span class="sb-count" id="selCount">0 selected</span>
                <button type="button" onclick="printSelected()"
                    style="background:#4338ca;color:#fff;border:none;border-radius:7px;padding:5px 14px;font-size:12px;font-weight:700;cursor:pointer;">
                    <i class="ti-printer mr-1"></i> Print Selected
                </button>
            </div>
        </div>
        <div style="display:flex;gap:6px;flex-wrap:wrap;">
            <a href="{{ route('reports.invoices.excel') }}?{{ http_build_query(request()->only(['date_from','date_to','invoice_type','payment_status'])) }}"
               class="btn btn-sm" style="background:#166534;color:#fff;border-radius:8px;font-weight:600;">
                <i class="ti-export mr-1"></i> Excel
            </a>
            <a href="{{ route('reports.invoices.pdf') }}?{{ http_build_query(request()->only(['date_from','date_to','invoice_type','payment_status'])) }}"
               target="_blank" class="btn btn-sm" style="background:#b91c1c;color:#fff;border-radius:8px;font-weight:600;">
                <i class="ti-printer mr-1"></i> PDF (All)
            </a>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="table table-hover" id="invRptTable">
            <thead>
                <tr>
                    <th style="width:36px;text-align:center;">
                        <input type="checkbox" id="checkAllInv" class="inv-check" title="Select all">
                    </th>
                    <th style="width:38px;text-align:center;">#</th>
                    <th>Invoice No</th>
                    <th>Party / Client</th>
                    <th style="text-align:center;">Type</th>
                    <th style="text-align:center;">Invoice Date</th>
                    <th style="text-align:center;">Payment Collected Date</th>
                    <th style="text-align:center;">Trips</th>
                    <th style="text-align:right;">Freight</th>
                    <th style="text-align:right;">Tax</th>
                    <th style="text-align:right;">Grand Total</th>
                    <th style="text-align:right;">Collected</th>
                    <th style="text-align:right;">Balance</th>
                    <th style="text-align:center;">Payment Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $r)
                @php
                    $typeMap = ['normal'=>['TAX','#eef2ff','#4338ca'],'rcm'=>['RCM','#fffbeb','#d97706'],'exempt'=>['EXEMPT','#ecfeff','#0891b2']];
                    [$tLabel,$tBg,$tCol] = $typeMap[$r->invoice_type] ?? $typeMap['normal'];
                    $payMap  = ['completed'=>['✓ Collected','#f0fff4','#38a169'],'partial'=>['⬤ Partial','#eff6ff','#3b82f6'],'pending'=>['○ Pending','#fffbeb','#d97706']];
                    [$pLabel,$pBg,$pCol] = $payMap[$r->payment_status] ?? $payMap['pending'];
                    $invTrips = $tripsByInvoice[$r->invoice_no] ?? collect();
                    $tripsJson = $invTrips->map(fn($t) => [
                        'trip_no'       => $t->trip_no,
                        'trip_date'     => $t->trip_date,
                        'vehicle'       => $t->vehicle,
                        'driver'        => $t->driver,
                        'from_location' => $t->from_location,
                        'to_location'   => $t->to_location,
                        'lr_no'         => $t->lr_no,
                        'freight_amount'=> $t->freight_amount,
                    ])->toJson();
                @endphp
                <tr class="inv-rpt-row" data-invoice-no="{{ $r->invoice_no }}">
                    <td style="text-align:center;" onclick="event.stopPropagation();">
                        <input type="checkbox" class="inv-check inv-row-check"
                            data-invoice-no="{{ $r->invoice_no }}"
                            data-invoice-date="{{ $r->invoiced_at ? $r->invoiced_at->format('d M Y') : '—' }}"
                            data-party="{{ addslashes($r->party_name) }}"
                            data-type="{{ $r->invoice_type }}"
                            data-type-label="{{ $tLabel }}"
                            data-collected-date="{{ $r->collection_due_date ? \Carbon\Carbon::parse($r->collection_due_date)->format('d M Y') : '—' }}"
                            data-freight="{{ $r->freight }}"
                            data-tax="{{ $r->tax }}"
                            data-grand="{{ $r->grand_total }}"
                            data-collected="{{ $r->collected_amount }}"
                            data-balance="{{ $r->balance }}"
                            data-pay-status="{{ $r->payment_status }}"
                            data-pay-label="{{ $pLabel }}"
                            data-trips="{{ htmlspecialchars($tripsJson, ENT_QUOTES) }}">
                    </td>
                    <td style="text-align:center;color:#b0bac9;font-size:12px;">{{ $i+1 }}</td>
                    <td>
                        <a href="{{ route('invoice.view', $r->invoice_no) }}"
                           style="font-family:monospace;font-weight:700;font-size:13px;color:#4338ca;background:#eef2ff;padding:3px 9px;border-radius:6px;border:1px solid #c7d2fe;text-decoration:none;">
                            {{ $r->invoice_no }}
                        </a>
                    </td>
                    <td style="font-weight:600;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $r->party_name }}">{{ $r->party_name }}</td>
                    <td style="text-align:center;"><span class="rpt-badge" style="background:{{ $tBg }};color:{{ $tCol }};">{{ $tLabel }}</span></td>
                    <td style="text-align:center;font-size:12px;color:#596579;">{{ $r->invoiced_at ? $r->invoiced_at->format('d M Y') : '—' }}</td>
                    <td style="text-align:center;font-size:12px;color:#596579;">{{ $r->collection_due_date ? \Carbon\Carbon::parse($r->collection_due_date)->format('d M Y') : '—' }}</td>
                    <td style="text-align:center;"><span style="background:#eef2ff;color:#667eea;font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;">{{ $r->trip_count }}</span></td>
                    <td style="text-align:right;font-weight:600;">₹{{ number_format($r->freight,2) }}</td>
                    <td style="text-align:right;color:#8a94a6;font-size:12px;">₹{{ number_format($r->tax,2) }}</td>
                    <td style="text-align:right;font-weight:800;color:#1a2340;">₹{{ number_format($r->grand_total,2) }}</td>
                    <td style="text-align:right;font-weight:700;color:#38a169;">₹{{ number_format($r->collected_amount,2) }}</td>
                    <td style="text-align:right;font-weight:700;color:{{ $r->balance > 0 ? '#e53e3e' : '#38a169' }};">₹{{ number_format($r->balance,2) }}</td>
                    <td style="text-align:center;"><span class="rpt-badge" style="background:{{ $pBg }};color:{{ $pCol }};">{{ $pLabel }}</span></td>
                </tr>
                @empty
                <tr><td colspan="14" class="text-center py-5" style="color:#b0bac9;">
                    <i class="ti-receipt" style="font-size:36px;display:block;margin-bottom:10px;opacity:.4;"></i>
                    <div style="font-size:14px;font-weight:600;">No invoices found for the selected filters.</div>
                </td></tr>
                @endforelse
            </tbody>
            @if($rows->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="8" style="padding:10px 12px;font-size:12px;color:#4338ca;letter-spacing:.3px;">
                        <i class="ti-receipt mr-1"></i> TOTALS — {{ $rows->count() }} invoices
                    </td>
                    <td style="text-align:right;padding:10px 12px;">₹{{ number_format($summary['total_freight'],2) }}</td>
                    <td style="text-align:right;padding:10px 12px;color:#8a94a6;">₹{{ number_format($summary['total_tax'],2) }}</td>
                    <td style="text-align:right;padding:10px 12px;color:#4338ca;">₹{{ number_format($summary['total_grand'],2) }}</td>
                    <td style="text-align:right;padding:10px 12px;color:#38a169;">₹{{ number_format($summary['total_collected'],2) }}</td>
                    <td style="text-align:right;padding:10px 12px;color:#e53e3e;">₹{{ number_format($summary['total_balance'],2) }}</td>
                    <td style="padding:10px 12px;"></td>
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
/* ── Checkbox logic ───────────────────────────────────────────── */
function updateSelBar() {
    var count = $('.inv-row-check:checked').length;
    if (count > 0) {
        $('#selBar').addClass('show');
        $('#selCount').text(count + ' selected');
    } else {
        $('#selBar').removeClass('show');
    }
}

$('#checkAllInv').on('change', function () {
    $('.inv-row-check').prop('checked', this.checked);
    updateSelBar();
});

$(document).on('change', '.inv-row-check', function () {
    var total   = $('.inv-row-check').length;
    var checked = $('.inv-row-check:checked').length;
    $('#checkAllInv').prop('indeterminate', checked > 0 && checked < total);
    $('#checkAllInv').prop('checked', total > 0 && checked === total);
    updateSelBar();
});

/* ── Print Selected ───────────────────────────────────────────── */
function fmt(n) { return '₹' + parseFloat(n).toLocaleString('en-IN', {minimumFractionDigits:2,maximumFractionDigits:2}); }

function printSelected() {
    var invoiceNos = [];
    $('.inv-row-check:checked').each(function () {
        invoiceNos.push($(this).data('invoice-no'));
    });

    if (!invoiceNos.length) { alert('Please select at least one invoice.'); return; }

    // POST invoice numbers to the print-selected route, open in new tab
    var form = $('<form method="POST" action="{{ route('reports.invoices.print') }}" target="_blank"></form>');
    form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
    invoiceNos.forEach(function(no) {
        form.append('<input type="hidden" name="invoice_nos[]" value="' + no + '">');
    });
    $('body').append(form);
    form.submit();
    form.remove();
}

function closePrintOverlay() {
    document.getElementById('printOverlay').classList.remove('show');
    document.body.style.overflow = '';
}

// ESC to close
document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closePrintOverlay();
});
</script>
@endpush
