<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Invoice Report — {{ now()->format('d M Y') }}</title>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{-webkit-print-color-adjust:exact;print-color-adjust:exact}
        body{font-family:"Segoe UI",Arial,sans-serif;font-size:11px;color:#1a1a1a;background:#fff;padding:16px 20px;}
        table{width:100%;border-collapse:collapse;font-size:10.5px;}
        thead tr{background:#1a2340;color:#fff;}
        thead th{padding:6px 7px;font-weight:700;text-align:left;white-space:nowrap;}
        thead th.R{text-align:right;}
        thead th.C{text-align:center;}
        tbody tr:nth-child(even) td{background:#f9fafc;}
        tbody td{padding:5px 7px;border-bottom:1px solid #edf0f7;vertical-align:middle;}
        tbody td.R{text-align:right;}
        tbody td.C{text-align:center;}
        tfoot td{background:#eef2ff;font-weight:800;border-top:2px solid #c7d2fe;padding:6px 7px;}
        tfoot td.R{text-align:right;}
        .badge{display:inline-block;padding:1px 6px;border-radius:10px;font-size:9px;font-weight:700;}
        .toolbar{margin-bottom:10px;display:flex;gap:6px;}
        .toolbar button{padding:5px 14px;border:none;border-radius:5px;font-size:11px;font-weight:700;cursor:pointer;}
        .btn-print{background:#b91c1c;color:#fff;}
        .btn-back{background:#f4f6fb;color:#596579;}
        @media print{
            .toolbar{display:none!important;}
            body{padding:6mm;}
            @page{size:A4 landscape;margin:6mm;}
        }
    </style>
</head>
<body>

<div class="toolbar">
    <button class="btn-print" onclick="window.print()">🖨 Print / Save PDF</button>
    <button class="btn-back" onclick="window.close()">✕ Close</button>
</div>

<table>
    <thead>
        <tr>
            <th style="width:30px;" class="C">#</th>
            <th>Invoice No</th>
            <th>Party / Client</th>
            <th class="C">Type</th>
            <th class="C">Invoice Date</th>
            <th class="C">Payment Collected Date</th>
            <th class="C">Trips</th>
            <th class="R">Freight (₹)</th>
            <th class="R">Tax (₹)</th>
            <th class="R">Grand Total (₹)</th>
            <th class="R">Collected (₹)</th>
            <th class="R">Balance (₹)</th>
            <th class="C">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rows as $i => $r)
        @php
            $typeColors = ['normal'=>['#eef2ff','#4338ca'],'rcm'=>['#fffbeb','#d97706'],'exempt'=>['#ecfeff','#0891b2']];
            [$tBg,$tCol] = $typeColors[$r->invoice_type] ?? $typeColors['normal'];
            $typeLabel   = strtoupper($r->invoice_type);
            $payColors   = ['completed'=>['#f0fff4','#38a169','✓ Collected'],'partial'=>['#eff6ff','#3b82f6','⬤ Partial'],'pending'=>['#fffbeb','#d97706','○ Pending']];
            [$pBg,$pCol,$pLabel] = $payColors[$r->payment_status] ?? $payColors['pending'];
        @endphp
        <tr>
            <td class="C" style="color:#b0bac9;">{{ $i+1 }}</td>
            <td><span style="font-family:monospace;font-weight:700;color:#4338ca;">{{ $r->invoice_no }}</span></td>
            <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $r->party_name }}</td>
            <td class="C"><span class="badge" style="background:{{ $tBg }};color:{{ $tCol }};">{{ $typeLabel }}</span></td>
            <td class="C">{{ $r->invoiced_at ? $r->invoiced_at->format('d/m/Y') : '—' }}</td>
            <td class="C">{{ $r->collection_due_date ? \Carbon\Carbon::parse($r->collection_due_date)->format('d/m/Y') : '—' }}</td>
            <td class="C">{{ $r->trip_count }}</td>
            <td class="R">{{ number_format($r->freight,2) }}</td>
            <td class="R" style="color:#8a94a6;">{{ number_format($r->tax,2) }}</td>
            <td class="R" style="font-weight:700;">{{ number_format($r->grand_total,2) }}</td>
            <td class="R" style="color:#38a169;">{{ number_format($r->collected_amount,2) }}</td>
            <td class="R" style="color:{{ $r->balance > 0 ? '#e53e3e' : '#38a169' }};">{{ number_format($r->balance,2) }}</td>
            <td class="C"><span class="badge" style="background:{{ $pBg }};color:{{ $pCol }};">{{ $pLabel }}</span></td>
        </tr>
        @empty
        <tr><td colspan="13" style="text-align:center;padding:30px;color:#b0bac9;">No records found.</td></tr>
        @endforelse
    </tbody>
    @if($rows->count() > 0)
    <tfoot>
        <tr>
            <td colspan="7" style="color:#4338ca;">TOTALS — {{ $rows->count() }} invoices</td>
            <td class="R">{{ number_format($summary['total_freight'],2) }}</td>
            <td class="R" style="color:#8a94a6;">{{ number_format($summary['total_tax'],2) }}</td>
            <td class="R" style="color:#4338ca;">{{ number_format($summary['total_grand'],2) }}</td>
            <td class="R" style="color:#38a169;">{{ number_format($summary['total_collected'],2) }}</td>
            <td class="R" style="color:#e53e3e;">{{ number_format($summary['total_balance'],2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

</body>
</html>
