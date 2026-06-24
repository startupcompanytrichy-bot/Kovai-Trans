<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $catLabel }} Ledger — {{ now()->format('d M Y') }}</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-print-color-adjust:exact;print-color-adjust:exact}
body{font-family:"Segoe UI",Arial,sans-serif;font-size:11px;color:#1a1a1a;background:#fff;padding:10px 14px;}
.np{text-align:center;margin-bottom:10px;}
.np button{padding:6px 18px;border:none;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;background:#b91c1c;color:#fff;}
h2{font-size:16px;text-align:center;margin-bottom:4px;}
.sub-h{text-align:center;font-size:11px;color:#64748b;margin-bottom:12px;}
table{width:100%;border-collapse:collapse;font-size:10px;}
thead tr{background:#1a2340;color:#fff;}
thead th{padding:5px 6px;font-weight:700;text-align:left;white-space:nowrap;font-size:9px;text-transform:uppercase;letter-spacing:.3px;}
thead th.R{text-align:right;}
thead th.C{text-align:center;}
tbody td{padding:4px 6px;border-bottom:1px solid #e2e8f0;vertical-align:middle;}
tbody td.R{text-align:right;font-weight:600;}
tbody td.C{text-align:center;}
tbody tr:nth-child(even) td{background:#f8fafc;}
.total-row td{font-weight:800;padding:6px;border-top:2px solid #1a2340;font-size:11px;}
.total-row td.R{font-size:12px;}
.pill{display:inline-block;padding:1px 6px;border-radius:999px;font-size:8px;font-weight:700;text-transform:uppercase;}
.pill.pending{background:#fef3cd;color:#856404;}
.pill.paid{background:#d4edda;color:#155724;}
.pill.partial{background:#cce5ff;color:#004085;}
.pill.credit{background:#f3e8ff;color:#6b21a8;}
@media print{.np{display:none!important;}@page{margin:8mm 10mm;}}
</style>
</head>
<body>

<div class="np">
    <button onclick="window.print()">&#128424; Print / Save PDF</button>
</div>

<h2>{{ $catLabel }} Ledger</h2>
<div class="sub-h">
    {{ $summary['count'] }} records
    @if($dateFrom || $dateTo) &middot; {{ $dateFrom ?: '∞' }} to {{ $dateTo ?: '∞' }} @endif
    &middot; Total: ₹{{ number_format($summary['total'],0) }}
</div>

<table>
    <thead>
        <tr>
            <th style="width:30px;">#</th>
            <th style="width:70px;">Date</th>
            <th>Description</th>
            <th style="width:100px;">Driver</th>
            <th style="width:80px;">Vehicle</th>
            <th style="width:60px;" class="C">Status</th>
            <th style="width:70px;" class="R">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses as $i => $exp)
        @php
            $payStatus = $exp->payment_status ?: 'pending';
            $payLabel  = match($payStatus){'paid'=>'Paid','partial'=>'Partial','credit'=>'Credit',default=>'Pending'};
            $payClass  = match($payStatus){'paid'=>'paid','partial'=>'partial','credit'=>'credit',default=>'pending'};
            $refText   = $exp->notes ?: ($exp->trip?->trip_no ? 'Trip: '.$exp->trip->trip_no : ($exp->vehicle?->vehicle_number ? 'Veh: '.$exp->vehicle->vehicle_number : 'Expense #'.$exp->id));
        @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td style="white-space:nowrap;">{{ $exp->expense_date->format('d/m/Y') }}</td>
            <td>{{ $refText }}</td>
            <td>{{ optional($exp->driver)->name ?: '—' }}</td>
            <td>{{ optional($exp->vehicle)->vehicle_number ?: '—' }}</td>
            <td class="C"><span class="pill {{ $payClass }}">{{ $payLabel }}</span></td>
            <td class="R">₹{{ number_format($exp->amount,2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="6" style="text-align:right;">Grand Total</td>
            <td class="R">₹{{ number_format($summary['total'],2) }}</td>
        </tr>
    </tfoot>
</table>

</body>
</html>