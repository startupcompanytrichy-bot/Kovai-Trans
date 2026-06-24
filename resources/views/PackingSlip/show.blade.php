@extends('layouts.app')

@section('content')
<style>
.ps-hero { background:linear-gradient(135deg,#9333ea 0%,#7c3aed 100%); border-radius:14px; padding:18px 22px; color:#fff; margin-bottom:16px; position:relative; overflow:hidden; }
.ps-hero::before { content:''; position:absolute; top:-30px; right:-30px; width:120px; height:120px; background:rgba(255,255,255,.07); border-radius:50%; }
.ps-hero h4 { font-size:17px; font-weight:800; margin:0 0 3px; }
.ps-hero .sub { font-size:11px; opacity:.8; }
.ps-card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); overflow:hidden; }
.ps-card-body { padding:22px; }
.ps-card-body .lbl { font-size:10px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.3px; margin-bottom:2px; }
.ps-card-body .val { font-size:14px; font-weight:600; color:#1a2340; }
.summary-row { display:flex; gap:10px; margin-bottom:16px; }
.summary-box { flex:1; background:#f8fafc; border-radius:10px; padding:10px 14px; text-align:center; border:1px solid #e8ecf4; }
.summary-box .lbl { font-size:9px; font-weight:700; color:#8a94a6; text-transform:uppercase; letter-spacing:.4px; }
.summary-box .val { font-size:18px; font-weight:800; margin-top:2px; }
.bale-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:10px; margin-bottom:10px; }
.bale-section { background:#fafbff; border:1px solid #e8ecf4; border-radius:10px; overflow:hidden; }
.bale-header { background:linear-gradient(135deg,#9333ea 0%,#7c3aed 100%); color:#fff; padding:5px 8px; font-size:10px; font-weight:800; text-align:center; }
.bale-table { width:100%; border-collapse:collapse; }
.bale-table th { background:#f0f2f7; color:#14213d; font-weight:800; font-size:9px; text-transform:uppercase; letter-spacing:.3px; padding:3px 5px; border-bottom:2px solid #dde1ea; text-align:center; }
.bale-table td { padding:2px 5px; border-bottom:1px solid #f0f2f7; font-size:10px; }
.bale-table .sno-cell { text-align:center; font-weight:700; color:#374151; }
.bale-table .num { text-align:right; }
.bale-total td { font-weight:800; font-size:10px; background:#f8fafc; border-top:2px solid #9333ea; padding:3px 5px; }
.bale-total .tv { color:#9333ea; text-align:right; }
@media print { .no-print { display:none !important; } body { background:#fff; } }
</style>

<div class="pcoded-inner-content">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="ps-hero">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h4><i class="ti-layout mr-2"></i>Packing Slip</h4>
            <div class="sub">{{ $slip->bill_no ?? 'No Bill' }} &mdash; {{ optional($slip->slip_date)->format('d/m/Y') }}</div>
        </div>
        <div class="col-md-6 text-right no-print">
            <a href="{{ route('packing-slip.index') }}" class="btn btn-sm" style="border-radius:8px;background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);padding:7px 14px;font-weight:600;"><i class="ti-arrow-left mr-1"></i> Back</a>
            <a href="{{ route('packing-slip.edit', $slip->id) }}" class="btn btn-sm" style="border-radius:8px;background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);padding:7px 14px;font-weight:600;"><i class="ti-pencil mr-1"></i> Edit</a>
            <button onclick="window.print()" class="btn btn-sm" style="border-radius:8px;background:#fff;color:#9333ea;border:none;padding:7px 14px;font-weight:700;"><i class="ti-printer mr-1"></i> Print</button>
        </div>
    </div>
</div>

<div class="ps-card">
    <div class="ps-card-body">
        <div class="row" style="margin-bottom:8px;">
            <div class="col-md-3"><div class="lbl">Bill No</div><div class="val">{{ $slip->bill_no ?? '—' }}</div></div>
            <div class="col-md-3"><div class="lbl">Lot No</div><div class="val">{{ $slip->lot_no ?? '—' }}</div></div>
            <div class="col-md-3"><div class="lbl">Date</div><div class="val">{{ optional($slip->slip_date)->format('d/m/Y') }}</div></div>
            <div class="col-md-3"><div class="lbl">Customer (TO)</div><div class="val">{{ optional($slip->customer)->name ?? '—' }}</div></div>
        </div>
        <div class="row" style="margin-bottom:4px;">
            <div class="col-md-3"><div class="lbl">Quality</div><div class="val">{{ $slip->quality ?? '—' }}</div></div>
            <div class="col-md-3"><div class="lbl">Invoice</div><div class="val">{{ $slip->invoice_no ?? '—' }}</div></div>
            <div class="col-md-3"><div class="lbl">Created By</div><div class="val">{{ $slip->created_by ?? '—' }}</div></div>
            <div class="col-md-3"></div>
        </div>
    </div>
</div>

<div class="summary-row" style="margin-top:16px;">
    @php $min = $slip->baleItems->min('bale_no'); $max = $slip->baleItems->max('bale_no'); @endphp
    <div class="summary-box"><div class="lbl">BALE NO'S</div><div class="val" style="color:#9333ea;">{{ $min && $max ? $min.' - '.$max : '—' }}</div></div>
    <div class="summary-box"><div class="lbl">NO OF BALE</div><div class="val" style="color:#1a2340;">{{ $slip->no_of_bale ?? 0 }}</div></div>
    <div class="summary-box"><div class="lbl">TOTAL METER</div><div class="val" style="color:#16a34a;">{{ number_format($slip->total_meter, 2) }}</div></div>
</div>

<hr style="border-color:#e8ecf4;margin:14px 0 16px;">

<h6 style="font-weight:800;color:#1a2340;margin:0 0 12px;font-size:13px;">Bale Entry</h6>

<div class="bale-grid">
    @php $groups = $slip->baleItems->groupBy('bale_no'); @endphp
    @foreach($groups as $baleNo => $items)
    <div class="bale-section">
        <div class="bale-header">BALE NO : {{ $baleNo }}</div>
        <table class="bale-table">
            <thead><tr><th style="width:30px;">S.No</th><th>Meter</th><th>Weight</th></tr></thead>
            <tbody>
                @foreach($items as $bi)
                <tr>
                    <td class="sno-cell">{{ $bi->s_no }}</td>
                    <td class="num">{{ number_format($bi->meter, 2) }}</td>
                    <td class="num">{{ number_format($bi->weight, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bale-total">
                    <td style="text-align:center;">TOTAL</td>
                    <td class="tv">{{ number_format($items->sum('meter'), 2) }}</td>
                    <td class="tv">{{ number_format($items->sum('weight'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endforeach
</div>

@if($slip->notes)
<div style="background:#f9fafb;border-radius:8px;padding:10px 14px;margin-top:8px;font-size:12px;color:#6b7280;">
    <strong style="color:#374151;">Notes:</strong> {{ $slip->notes }}
</div>
@endif

</div></div></div></div>
@endsection
