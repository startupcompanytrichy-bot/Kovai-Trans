@extends('layouts.app')

@section('content')

@php
$typeConfig = [
    'normal' => ['label'=>'Tax Invoice',      'color'=>'#3b82f6','bg'=>'#eff6ff','icon'=>'ti-receipt'],
    'rcm'    => ['label'=>'RCM Invoice',       'color'=>'#d97706','bg'=>'#fffbeb','icon'=>'ti-reload'],
    'exempt' => ['label'=>'Exempt Invoice',    'color'=>'#0891b2','bg'=>'#ecfeff','icon'=>'ti-tag'],
];
@endphp

<style>
.il-page { background:#f4f6fb; }

/* ── Header ── */
.il-header {
    background:linear-gradient(135deg,#1a2340 0%,#2d3a5e 60%,#667eea 100%);
    border-radius:10px; padding:14px 20px; color:#fff;
    margin-bottom:16px; position:relative; overflow:hidden;
}
.il-header h4 { font-size:16px; font-weight:800; margin:0 0 2px; }
.il-header .sub { font-size:12px; opacity:.75; }

/* ── Summary cards ── */
.il-summary { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:16px; }
.il-sum-card {
    background:#fff; border-radius:10px; padding:14px 16px;
    box-shadow:0 1px 6px rgba(0,0,0,.06);
    display:flex; align-items:center; gap:12px;
    border-left:3px solid transparent;
}
.il-sum-card .sc-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.il-sum-card .sc-label { font-size:10px; font-weight:700; color:#8a94a6; text-transform:uppercase; letter-spacing:.3px; }
.il-sum-card .sc-value { font-size:20px; font-weight:800; color:#1a2340; }

/* ── Filter / search bar ── */
.il-filter-bar {
    background:#fff; border-radius:10px; padding:12px 16px;
    box-shadow:0 1px 6px rgba(0,0,0,.06); margin-bottom:12px;
    display:flex; align-items:center; gap:8px; flex-wrap:wrap;
}
.il-search-wrap { flex:1; min-width:200px; position:relative; }
.il-search-wrap i { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#b0bac9; }
.il-search-wrap input { padding-left:30px; }
.il-filter-bar .form-control { min-height:36px; font-size:13px; border-color:#e2e8f0; border-radius:8px; }

/* ── Table card ── */
.il-table-card { background:#fff; border-radius:10px; box-shadow:0 1px 6px rgba(0,0,0,.06); overflow:hidden; }
.il-table-header { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid #f0f2f7; background:#fafbff; }
.il-table-header h6 { margin:0; font-size:13px; font-weight:700; color:#1a2340; }

#invoiceTable { min-width:700px; margin-bottom:0; }
#invoiceTable th { background:#f8fafc; color:#14213d; font-weight:800; font-size:11px; text-transform:uppercase; letter-spacing:.4px; padding:9px 12px; border-color:#f0f2f7; white-space:nowrap; }
#invoiceTable td { padding:10px 12px; border-color:#f0f2f7; vertical-align:middle; font-size:13px; }
#invoiceTable .inv-row { cursor:pointer; transition:background .12s; }
#invoiceTable .inv-row:hover td { background:#f4f7ff; }

.il-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.3px; }

/* ── Invoice number chip ── */
.inv-no-chip { font-family:monospace; font-weight:700; font-size:13px; color:#1a2340; background:#f0f4ff; padding:3px 10px; border-radius:6px; border:1px solid #c7d2fe; display:inline-block; }

/* ── Amount display ── */
.inv-amount { font-weight:700; font-size:13px; color:#1a2340; }
.inv-amount small { font-size:10px; color:#8a94a6; font-weight:500; }

/* ── Trip count badge ── */
.trip-count-badge { background:#eef2ff; color:#667eea; font-size:11px; font-weight:700; padding:3px 9px; border-radius:20px; display:inline-block; }

@media(max-width:767.98px) { .il-summary { grid-template-columns:repeat(2,1fr); } }

/* ── Pay slide-in panel ── */
.pay-backdrop {
    display:none; position:fixed; inset:0;
    background:rgba(26,35,64,.45); z-index:1040; backdrop-filter:blur(2px);
}
.pay-backdrop.show { display:block; }
.pay-panel {
    position:fixed; top:0; right:0; bottom:0;
    width:100%; max-width:460px; background:#fff; z-index:1050;
    display:flex; flex-direction:column;
    transform:translateX(100%); transition:transform .28s cubic-bezier(.4,0,.2,1);
    box-shadow:-8px 0 40px rgba(0,0,0,.18);
}
.pay-panel.open { transform:translateX(0); }
.pay-panel-hdr {
    background:linear-gradient(135deg,#1a2340 0%,#303f6e 100%);
    padding:16px 20px; display:flex; align-items:center;
    justify-content:space-between; flex-shrink:0;
}
.pay-panel-hdr h5 { color:#fff; font-size:15px; font-weight:700; margin:0; display:flex; align-items:center; gap:8px; }
.pay-panel-close {
    width:34px; height:34px; border-radius:8px; background:rgba(255,255,255,.15);
    border:none; color:#fff; font-size:18px; cursor:pointer;
    display:flex; align-items:center; justify-content:center; transition:background .15s;
}
.pay-panel-close:hover { background:#e53e3e; }
.pay-panel-body { flex:1; min-height:0; overflow-y:auto; padding:22px 20px; }
.pay-panel-footer {
    padding:14px 20px; border-top:1px solid #edf0f7; background:#fafbff;
    display:flex; align-items:center; justify-content:flex-end; gap:10px; flex-shrink:0;
}
.pay-field label { font-size:11px; font-weight:700; color:#596579; text-transform:uppercase; letter-spacing:.4px; display:block; margin-bottom:5px; }
.pay-field label .req { color:#e53e3e; }
.pay-field .form-control { border-color:#d7dce5; border-radius:8px; font-size:13px; min-height:40px; }
.pay-field .form-control:focus { border-color:#667eea; box-shadow:0 0 0 3px rgba(102,126,234,.12); }
.pay-status-pills { display:flex; gap:8px; flex-wrap:wrap; }
.pay-pill {
    padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700; cursor:pointer;
    border:2px solid #d7dce5; background:#fff; color:#596579; transition:all .15s;
}
.pay-pill.active-pending  { border-color:#d97706; background:#fffbeb; color:#d97706; }
.pay-pill.active-partial  { border-color:#3b82f6; background:#eff6ff; color:#3b82f6; }
.pay-pill.active-completed{ border-color:#38a169; background:#f0fff4; color:#38a169; }
@media(max-width:575px){ .pay-panel{ max-width:100%; } }
</style>

<div class="pcoded-inner-content il-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

{{-- Header --}}
<div class="il-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;letter-spacing:.5px;margin-bottom:6px;">
                <i class="ti-receipt"></i> Invoice Management
            </div>
            <h4>Invoice List</h4>
            <div class="sub">All generated invoices — click any row to view full invoice with trips.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('trip') }}" class="btn btn-sm" style="background:#fff;color:#667eea;font-weight:700;border-radius:8px;padding:7px 18px;box-shadow:0 2px 10px rgba(0,0,0,.15);">
                <i class="ti-location-arrow mr-1"></i> Back to Trips
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

{{-- Summary ─────────────────────────────────────────────────────────────── --}}
@php
$totalInvoices = $invoices->count();
$totalTrips    = $invoices->sum('trip_count');
$totalSubtotal = $invoices->sum('subtotal');
$normalCount   = $invoices->where('invoice_type','normal')->count();
$rcmCount      = $invoices->where('invoice_type','rcm')->count();
$exemptCount   = $invoices->where('invoice_type','exempt')->count();
@endphp

<div class="il-summary">
    <div class="il-sum-card" style="border-left-color:#667eea;">
        <div class="sc-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-receipt"></i></div>
        <div>
            <div class="sc-label">Total Invoices</div>
            <div class="sc-value" style="color:#667eea;">{{ $totalInvoices }}</div>
        </div>
    </div>
    <div class="il-sum-card" style="border-left-color:#48bb78;">
        <div class="sc-icon" style="background:#f0fff4;color:#48bb78;"><i class="ti-location-arrow"></i></div>
        <div>
            <div class="sc-label">Total Trips</div>
            <div class="sc-value" style="color:#48bb78;">{{ $totalTrips }}</div>
        </div>
    </div>
    <div class="il-sum-card" style="border-left-color:#4338ca;">
        <div class="sc-icon" style="background:#eef2ff;color:#4338ca;"><i class="ti-money"></i></div>
        <div>
            <div class="sc-label">Total Freight</div>
            <div class="sc-value" style="color:#4338ca;font-size:16px;">₹{{ number_format($totalSubtotal, 0) }}</div>
        </div>
    </div>
    <div class="il-sum-card" style="border-left-color:#d97706;">
        <div class="sc-icon" style="background:#fffbeb;color:#d97706;"><i class="ti-tag"></i></div>
        <div>
            <div class="sc-label">Normal / RCM / Exempt</div>
            <div class="sc-value" style="font-size:15px;color:#d97706;">{{ $normalCount }} / {{ $rcmCount }} / {{ $exemptCount }}</div>
        </div>
    </div>
</div>

{{-- Filter bar ──────────────────────────────────────────────────────────── --}}
<div class="il-filter-bar">
    <div class="il-search-wrap">
        <i class="ti-search"></i>
        <input type="text" id="invSearch" class="form-control" placeholder="Search invoice no, party...">
    </div>
    <select id="filterType" class="form-control" style="min-width:140px;max-width:160px;">
        <option value="">All Types</option>
        <option value="normal">Normal Invoice</option>
        <option value="rcm">RCM Invoice</option>
        <option value="exempt">Exempt Invoice</option>
    </select>
    <input type="date" id="filterDateFrom" class="form-control" style="max-width:145px;" title="From Date">
    <input type="date" id="filterDateTo"   class="form-control" style="max-width:145px;" title="To Date">
    <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;">
        <i class="ti-close mr-1"></i> Clear
    </button>
</div>

{{-- Invoice Table ────────────────────────────────────────────────────────── --}}
<div class="il-table-card">
    <div class="il-table-header">
        <h6>
            <i class="ti-receipt mr-1" style="color:#667eea;"></i> All Invoices
            <span id="invCountBadge" style="background:#eef2ff;color:#667eea;font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;margin-left:6px;">{{ $totalInvoices }}</span>
        </h6>
        <a href="{{ route('trip') }}" class="btn btn-primary btn-sm" style="border-radius:8px;">
            <i class="ti-plus mr-1"></i> Generate New Invoice
        </a>
    </div>

    <div style="overflow-x:auto;">
        <table class="table table-hover" id="invoiceTable">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center;">#</th>
                    <th>Invoice No</th>
                    <th>Party / Client</th>
                    <th style="text-align:center;">Trips</th>
                    <th style="text-align:center;">Type</th>
                    <th style="text-align:right;">Freight (Sub-Total)</th>
                    <th style="text-align:center;">Payment Status</th>
                    <th style="text-align:center;">Collected Date</th>
                    <th style="text-align:center;">Invoiced On</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $i => $inv)
                @php
                    $tc  = $typeConfig[$inv->invoice_type] ?? $typeConfig['normal'];
                    [$cgstR, $sgstR] = match($inv->invoice_type) {
                        'rcm'    => [2.5, 2.5],
                        'exempt' => [0.0, 0.0],
                        default  => [9.0, 9.0],
                    };
                    $taxAmt = round($inv->subtotal * ($cgstR + $sgstR) / 100, 2);
                    $grand  = $inv->subtotal + $taxAmt;

                    // Payment status badge
                    [$psColor, $psBg, $psLabel] = match($inv->payment_status ?? 'pending') {
                        'completed' => ['#38a169', '#f0fff4', '✓ Collected'],
                        'partial'   => ['#3b82f6', '#eff6ff', '⬤ Partial'],
                        default     => ['#d97706', '#fffbeb', '○ Pending'],
                    };
                    $collDateFmt = $inv->collection_due_date
                        ? \Carbon\Carbon::parse($inv->collection_due_date)->format('d M Y')
                        : '—';
                @endphp
                <tr class="inv-row"
                    data-url="{{ route('invoice.view', $inv->invoice_no) }}"
                    data-search="{{ strtolower($inv->invoice_no . ' ' . $inv->party_name) }}"
                    data-type="{{ $inv->invoice_type }}"
                    data-date="{{ $inv->invoiced_at ? $inv->invoiced_at->format('Y-m-d') : '' }}"
                    data-invoice-no="{{ $inv->invoice_no }}"
                    data-pay-status="{{ $inv->payment_status ?? 'pending' }}"
                    data-collected="{{ number_format((float)$inv->collected_amount, 2, '.', '') }}"
                    data-freight="{{ number_format((float)$inv->subtotal, 2, '.', '') }}"
                    data-tax="{{ number_format($taxAmt, 2, '.', '') }}"
                    data-grand="{{ number_format($grand, 2, '.', '') }}"
                    data-cgst-r="{{ $cgstR }}"
                    data-sgst-r="{{ $sgstR }}"
                    data-pay-mode="{{ $inv->payment_mode ?? '' }}"
                    data-coll-date="{{ $inv->collection_due_date ? \Carbon\Carbon::parse($inv->collection_due_date)->format('Y-m-d') : '' }}">
                    <td style="text-align:center;color:#b0bac9;font-size:12px;">{{ $i + 1 }}</td>
                    <td>
                        <span class="inv-no-chip">{{ $inv->invoice_no }}</span>
                    </td>
                    <td style="font-weight:600;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                        title="{{ $inv->party_name }}">
                        {{ $inv->party_name ?: '—' }}
                    </td>
                    <td style="text-align:center;">
                        <span class="trip-count-badge">{{ $inv->trip_count }} trip{{ $inv->trip_count > 1 ? 's' : '' }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="il-badge" style="background:{{ $tc['bg'] }};color:{{ $tc['color'] }};">
                            <i class="{{ $tc['icon'] }}" style="font-size:9px;"></i> {{ $tc['label'] }}
                        </span>
                    </td>
                    <td style="text-align:right;">
                        <div class="inv-amount">₹{{ number_format($grand, 2) }}</div>
                        <small style="font-size:10px;color:#8a94a6;">Freight: ₹{{ number_format($inv->subtotal, 2) }} + Tax: ₹{{ number_format($taxAmt, 2) }}</small>
                    </td>
                    <td style="text-align:center;" class="pay-status-cell">
                        <span class="il-badge" style="background:{{ $psBg }};color:{{ $psColor }};">
                            {{ $psLabel }}
                        </span>
                        @if((float)$inv->collected_amount > 0)
                        <div style="font-size:10px;color:#596579;margin-top:2px;">₹{{ number_format($inv->collected_amount, 0) }}</div>
                        @endif
                    </td>
                    <td style="text-align:center;font-size:12px;color:#596579;" class="coll-date-cell">
                        {{ $collDateFmt }}
                    </td>
                    <td style="text-align:center;font-size:12px;color:#596579;">
                        {{ $inv->invoiced_at ? $inv->invoiced_at->format('d M Y') : '—' }}
                    </td>
                    <td style="text-align:center;white-space:nowrap;" onclick="event.stopPropagation();">
                        @if(($inv->payment_status ?? 'pending') === 'completed')
                            <button type="button"
                                class="btn btn-sm"
                                disabled
                                style="background:#f4f6fb;color:#b0bac9;border:1px solid #e2e8f0;border-radius:7px;padding:4px 10px;font-size:12px;font-weight:600;cursor:not-allowed;"
                                title="Payment already completed">
                                <i class="ti-check mr-1"></i> Paid
                            </button>
                        @else
                            <button type="button"
                                class="btn btn-sm btn-pay-invoice"
                                style="background:#f0fff4;color:#38a169;border:1px solid #c6f6d5;border-radius:7px;padding:4px 10px;font-size:12px;font-weight:600;"
                                onclick="openPayPanel(this)">
                                <i class="ti-money mr-1"></i> Pay
                            </button>
                        @endif
                        <!-- <a href="{{ route('invoice.view', $inv->invoice_no) }}"
                            class="btn btn-sm" style="background:#eef2ff;color:#667eea;border:none;border-radius:7px;padding:4px 12px;font-size:12px;font-weight:600;">
                            <i class="ti-eye mr-1"></i> View
                        </a> -->
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5" style="color:#b0bac9;">
                        <i class="ti-receipt" style="font-size:36px;display:block;margin-bottom:10px;opacity:.4;"></i>
                        <div style="font-size:14px;font-weight:600;margin-bottom:4px;">No invoices generated yet</div>
                        <a href="{{ route('trip') }}" class="btn btn-primary btn-sm mt-2">Go to Trips to Generate</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($invoices->count() > 0)
            @php
                $grandTotalFreight = $invoices->sum('subtotal');
                $grandTotalTax     = $invoices->sum(function($inv) {
                    [$cgR, $sgR] = match($inv->invoice_type) {
                        'rcm'    => [2.5, 2.5],
                        'exempt' => [0.0, 0.0],
                        default  => [9.0, 9.0],
                    };
                    return round($inv->subtotal * ($cgR + $sgR) / 100, 2);
                });
                $grandTotalGrand = $grandTotalFreight + $grandTotalTax;
            @endphp
            <tfoot>
                <tr style="background:#f0f4ff;border-top:2px solid #c7d2fe;">
                    <td colspan="5" style="padding:10px 12px;font-size:12px;font-weight:800;color:#1a2340;text-align:right;letter-spacing:.3px;">
                        <i class="ti-receipt mr-1" style="color:#667eea;"></i> TOTAL ({{ $invoices->count() }} invoices)
                    </td>
                    <td style="padding:10px 12px;text-align:right;">
                        <div style="font-weight:800;font-size:13px;color:#4338ca;">₹{{ number_format($grandTotalGrand, 2) }}</div>
                        <small style="font-size:10px;color:#8a94a6;">Freight: ₹{{ number_format($grandTotalFreight, 2) }} + Tax: ₹{{ number_format($grandTotalTax, 2) }}</small>
                    </td>
                    <td colspan="4" style="padding:10px 12px;"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

</div></div></div></div>

{{-- ── Payment slide-in panel ── --}}
<div class="pay-backdrop" id="payBackdrop" onclick="closePayPanel()"></div>
<div class="pay-panel" id="payPanel">
    <div class="pay-panel-hdr">
        <h5><i class="ti-money"></i> Record Payment</h5>
        <button class="pay-panel-close" onclick="closePayPanel()"><i class="ti-close"></i></button>
    </div>
    <div class="pay-panel-body">
        {{-- Invoice info strip --}}
        <div id="payInvoiceInfo" style="background:#f4f7ff;border-radius:10px;padding:12px 14px;margin-bottom:20px;">
            <div style="font-size:11px;color:#8a94a6;font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Invoice</div>
            <div id="payInvoiceNo" style="font-family:monospace;font-weight:800;font-size:15px;color:#1a2340;"></div>
            <div id="payInvoiceParty" style="font-size:12px;color:#596579;margin-top:2px;"></div>
            <div style="display:flex;gap:16px;margin-top:8px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:10px;color:#8a94a6;font-weight:700;">Freight</div>
                    <div id="payFreightAmt" style="font-size:13px;font-weight:700;color:#1a2340;"></div>
                </div>
                <div>
                    <div style="font-size:10px;color:#8a94a6;font-weight:700;">Tax <span id="payTaxRate" style="color:#b0bac9;">(CGST+SGST)</span></div>
                    <div id="payTaxAmt" style="font-size:13px;font-weight:700;color:#d97706;"></div>
                </div>
                <div>
                    <div style="font-size:10px;color:#8a94a6;font-weight:700;">Grand Total</div>
                    <div id="payGrandAmt" style="font-size:13px;font-weight:700;color:#4338ca;"></div>
                </div>
                <div>
                    <div style="font-size:10px;color:#8a94a6;font-weight:700;">Collected</div>
                    <div id="payCollectedAmt" style="font-size:13px;font-weight:700;color:#38a169;"></div>
                </div>
                <div>
                    <div style="font-size:10px;color:#8a94a6;font-weight:700;">Balance</div>
                    <div id="payBalanceAmt" style="font-size:13px;font-weight:700;color:#e53e3e;"></div>
                </div>
            </div>
        </div>

        {{-- Payment Status --}}
        <div class="pay-field mb-3">
            <label>Payment Status <span class="req">*</span></label>
            <div class="pay-status-pills">
                <button type="button" class="pay-pill" data-val="pending"   onclick="selectPayStatus('pending')">○ Pending</button>
                <button type="button" class="pay-pill" data-val="partial"   onclick="selectPayStatus('partial')">⬤ Partial</button>
                <button type="button" class="pay-pill" data-val="completed" onclick="selectPayStatus('completed')">✓ Collected</button>
            </div>
            <input type="hidden" id="payStatusVal" value="pending">
        </div>

        {{-- Amount Collected --}}
        <div class="pay-field mb-3">
            <label>Amount Collected (₹) <span class="req">*</span></label>
            <input type="number" id="payAmtInput" class="form-control" placeholder="0.00" min="0" step="0.01">
        </div>

        {{-- Collected Date --}}
        <div class="pay-field mb-3">
            <label>Payment Collected Date</label>
            <input type="date" id="payDateInput" class="form-control">
        </div>

        {{-- Payment Mode --}}
        <div class="pay-field mb-3">
            <label>Payment Mode <span class="req">*</span></label>
            <select id="payModeInput" class="form-control" required>
                <option value="">— Select Mode —</option>
                <option value="cash">Cash</option>
                <option value="upi">UPI</option>
                <option value="bank">Bank Transfer</option>
                <option value="cheque">Cheque</option>
            </select>
        </div>

        {{-- Conditional: UPI details --}}
        <div id="payUpiFields" class="pay-field mb-3" style="display:none;">
            <label>UPI VPA / Transaction ID</label>
            <input type="text" id="payUpiInput" class="form-control" placeholder="e.g. example@upi / UPI1234">
        </div>

        {{-- Conditional: Bank / Cheque details --}}
        <div id="payBankFields" class="pay-field mb-3" style="display:none;">
            <label>Bank Name / Reference No</label>
            <input type="text" id="payBankInput" class="form-control" placeholder="e.g. HDFC / NEFT Ref No">
        </div>

        <div id="payErrorBox" class="alert alert-danger" style="display:none;"></div>
    </div>
    <div class="pay-panel-footer">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closePayPanel()" style="border-radius:8px;">
            <i class="ti-close mr-1"></i> Cancel
        </button>
        <button type="button" id="paySubmitBtn" class="btn btn-success btn-sm" onclick="submitPayment()" style="border-radius:8px;font-weight:700;">
            <i class="ti-save mr-1"></i> Save Payment
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // Row click → view invoice
    $('#invoiceTable').on('click', '.inv-row', function () {
        window.location.href = $(this).data('url');
    });

    // Search + filter
    function applyFilters() {
        var term     = $('#invSearch').val().toLowerCase();
        var type     = $('#filterType').val();
        var dateFrom = $('#filterDateFrom').val();
        var dateTo   = $('#filterDateTo').val();
        var visible  = 0;

        $('.inv-row').each(function () {
            var $r = $(this);
            var ok = true;
            if (term && !($r.attr('data-search') || '').includes(term)) ok = false;
            if (type && $r.attr('data-type') !== type) ok = false;
            var d = $r.attr('data-date') || '';
            if (dateFrom && d && d < dateFrom) ok = false;
            if (dateTo   && d && d > dateTo)   ok = false;
            $r.toggle(ok);
            if (ok) visible++;
        });
        $('#invCountBadge').text(visible);
    }

    $('#invSearch, #filterType, #filterDateFrom, #filterDateTo').on('input change', applyFilters);

    $('#clearFilters').on('click', function () {
        $('#invSearch').val('');
        $('#filterType').val('');
        $('#filterDateFrom, #filterDateTo').val('');
        applyFilters();
    });

    // Payment mode change → show/hide conditional fields
    $('#payModeInput').on('change', function () {
        togglePayModeFields();
    });
});

/* ── Pay panel ─────────────────────────────────────────── */
var _payRow = null;   // reference to the TR being paid

function openPayPanel(btn) {
    _payRow = $(btn).closest('tr');

    var invoiceNo = _payRow.data('invoice-no');
    var party     = _payRow.find('td:eq(2)').text().trim();
    var freight   = parseFloat(_payRow.data('freight') || 0);
    var tax       = parseFloat(_payRow.data('tax') || 0);
    var grand     = parseFloat(_payRow.data('grand') || 0);
    var collected = parseFloat(_payRow.data('collected') || 0);
    var balance   = Math.max(0, grand - collected);
    var cgstR     = parseFloat(_payRow.data('cgst-r') || 0);
    var sgstR     = parseFloat(_payRow.data('sgst-r') || 0);

    // Populate info strip
    document.getElementById('payInvoiceNo').textContent    = invoiceNo;
    document.getElementById('payInvoiceParty').textContent = party;
    document.getElementById('payFreightAmt').textContent   = '₹' + freight.toLocaleString('en-IN', {minimumFractionDigits:2});
    document.getElementById('payTaxRate').textContent       = '(CGST ' + cgstR + '% + SGST ' + sgstR + '%)';
    document.getElementById('payTaxAmt').textContent       = '₹' + tax.toLocaleString('en-IN', {minimumFractionDigits:2});
    document.getElementById('payGrandAmt').textContent     = '₹' + grand.toLocaleString('en-IN', {minimumFractionDigits:2});
    document.getElementById('payCollectedAmt').textContent = '₹' + collected.toLocaleString('en-IN', {minimumFractionDigits:2});
    document.getElementById('payBalanceAmt').textContent   = '₹' + balance.toLocaleString('en-IN', {minimumFractionDigits:2});

    // Pre-fill fields
    selectPayStatus(_payRow.data('pay-status') || 'pending');
    document.getElementById('payAmtInput').value  = collected > 0 ? collected : '';

    // Default collected date to today if not already set
    var existingDate = _payRow.data('coll-date') || '';
    var today = new Date().toISOString().split('T')[0]; // YYYY-MM-DD
    document.getElementById('payDateInput').value = existingDate || today;

    document.getElementById('payModeInput').value = _payRow.data('pay-mode') || '';
    document.getElementById('payUpiInput').value  = '';
    document.getElementById('payBankInput').value = '';
    document.getElementById('payErrorBox').style.display = 'none';
    togglePayModeFields();

    document.getElementById('payBackdrop').classList.add('show');
    document.getElementById('payPanel').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closePayPanel() {
    document.getElementById('payPanel').classList.remove('open');
    document.getElementById('payBackdrop').classList.remove('show');
    document.body.style.overflow = '';
    _payRow = null;
}

function togglePayModeFields() {
    var sel   = document.getElementById('payModeInput');
    var upiEl = document.getElementById('payUpiFields');
    var bankEl = document.getElementById('payBankFields');
    if (!sel || !upiEl || !bankEl) return;
    var mode = sel.value;
    upiEl.style.display  = (mode === 'upi') ? 'block' : 'none';
    bankEl.style.display = (mode === 'bank' || mode === 'cheque') ? 'block' : 'none';
}

function selectPayStatus(val) {
    document.getElementById('payStatusVal').value = val;
    document.querySelectorAll('.pay-pill').forEach(function(p) {
        p.classList.remove('active-pending','active-partial','active-completed');
    });
    var active = document.querySelector('.pay-pill[data-val="' + val + '"]');
    if (active) active.classList.add('active-' + val);

    // Auto-fill amount when "completed" — use grand total (freight + tax)
    if (val === 'completed' && _payRow) {
        var grand = parseFloat(_payRow.data('grand') || 0);
        document.getElementById('payAmtInput').value = grand > 0 ? grand.toFixed(2) : '';
    }
    if (val === 'pending') {
        document.getElementById('payAmtInput').value = '';
    }
}

function submitPayment() {
    if (!_payRow) return;

    var invoiceNo = _payRow.data('invoice-no');
    var status    = document.getElementById('payStatusVal').value;
    var amount    = document.getElementById('payAmtInput').value;
    var date      = document.getElementById('payDateInput').value;
    var mode      = document.getElementById('payModeInput').value;
    var errBox    = document.getElementById('payErrorBox');

    errBox.style.display = 'none';

    if (!amount || parseFloat(amount) < 0) {
        errBox.textContent = 'Please enter a valid collected amount.';
        errBox.style.display = 'block';
        return;
    }

    if (!mode) {
        errBox.textContent = 'Please select a Payment Mode.';
        errBox.style.display = 'block';
        return;
    }

    var $btn = $('#paySubmitBtn');
    $btn.prop('disabled', true).html('<i class="ti-reload mr-1"></i> Saving...');

    var upiDetail   = document.getElementById('payUpiInput').value;
    var bankDetail  = document.getElementById('payBankInput').value;

    $.ajax({
        url:  '/invoice/' + invoiceNo + '/payment',
        type: 'POST',
        data: {
            _token:               $('meta[name="csrf-token"]').attr('content'),
            payment_status:       status,
            collected_amount:     amount,
            collection_due_date:  date || null,
            payment_mode:         mode || null,
            upi_details:          (mode === 'upi') ? (upiDetail || null) : null,
            bank_details:         (mode === 'bank' || mode === 'cheque') ? (bankDetail || null) : null,
        },
        success: function(res) {
            if (res.success) {
                // Update row data attributes
                _payRow.attr('data-pay-status', res.payment_status);
                _payRow.attr('data-collected',  parseFloat(amount).toFixed(2));
                _payRow.attr('data-coll-date',  date || '');
                _payRow.attr('data-pay-mode',   res.payment_mode || '');

                // Update payment status cell
                var statusMap = {
                    completed: { bg:'#f0fff4', color:'#38a169', label:'✓ Collected' },
                    partial:   { bg:'#eff6ff', color:'#3b82f6', label:'⬤ Partial'  },
                    pending:   { bg:'#fffbeb', color:'#d97706', label:'○ Pending'   },
                };
                var s = statusMap[res.payment_status] || statusMap['pending'];
                var amtHtml = parseFloat(amount) > 0
                    ? '<div style="font-size:10px;color:#596579;margin-top:2px;">₹' + parseInt(amount).toLocaleString('en-IN') + '</div>'
                    : '';
                _payRow.find('.pay-status-cell').html(
                    '<span class="il-badge" style="background:' + s.bg + ';color:' + s.color + ';">' + s.label + '</span>' + amtHtml
                );

                // Update collected date cell
                _payRow.find('.coll-date-cell').text(res.collection_due_date || '—');

                // Disable Pay button if now completed
                if (res.payment_status === 'completed') {
                    _payRow.find('.btn-pay-invoice').replaceWith(
                        '<button type="button" class="btn btn-sm" disabled ' +
                        'style="background:#f4f6fb;color:#b0bac9;border:1px solid #e2e8f0;border-radius:7px;padding:4px 10px;font-size:12px;font-weight:600;cursor:not-allowed;" ' +
                        'title="Payment already completed">' +
                        '<i class="ti-check mr-1"></i> Paid</button>'
                    );
                }

                closePayPanel();

                // Brief success flash
                if (typeof toastr !== 'undefined') {
                    toastr.success('Payment updated for ' + invoiceNo);
                }
            } else {
                errBox.textContent = res.message || 'Failed to save.';
                errBox.style.display = 'block';
            }
        },
        error: function(xhr) {
            var msg = 'Something went wrong.';
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            errBox.textContent = msg;
            errBox.style.display = 'block';
        },
        complete: function() {
            $btn.prop('disabled', false).html('<i class="ti-save mr-1"></i> Save Payment');
        }
    });
}
</script>
@endpush
