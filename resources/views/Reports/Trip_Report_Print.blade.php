<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Trip Report — {{ now()->format('d M Y') }}</title>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{-webkit-print-color-adjust:exact;print-color-adjust:exact}
        body{font-family:"Segoe UI",Arial,sans-serif;font-size:12px;color:#1a1a1a;background:#fff;padding:10px 14px;}
        .np{text-align:center;margin-bottom:10px;}
        .np button{padding:6px 18px;border:none;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;background:#b91c1c;color:#fff;}
        table{width:100%;border-collapse:collapse;font-size:10px;}
        thead tr{background:#1a2340;color:#fff;}
        thead th{padding:5px 6px;font-weight:700;text-align:left;white-space:nowrap;}
        thead th.R{text-align:right;}
        thead th.C{text-align:center;}
        tbody tr:nth-child(even) td{background:#f9fafc;}
        tbody td{padding:4px 6px;border-bottom:1px solid #edf0f7;vertical-align:middle;}
        tbody td.R{text-align:right;font-weight:600;}
        tbody td.C{text-align:center;}
        tfoot td{background:#eef2ff;font-weight:800;border-top:2px solid #c7d2fe;padding:5px 6px;font-size:11px;}
        tfoot td.R{text-align:right;}
        .badge{display:inline-block;padding:1px 5px;border-radius:8px;font-size:9px;font-weight:700;}
        @media print{
            .np{display:none!important;}
            body{padding:6mm;}
            @page{size:A4 landscape;margin:6mm;}
        }
    </style>
</head>
<body>

<div class="np"><button onclick="window.print()">🖨 Print / Save PDF</button></div>

<table>
    <thead>
        <tr>
            <th style="width:30px;" class="C">#</th>
            <th>Trip No</th>
            <th>Date</th>
            <th>Party</th>
            <th>Vehicle</th>
            <th>Driver</th>
            <th>Route</th>
            <th class="R">Freight (₹)</th>
            <th class="R">Expenses (₹)</th>
            <th class="R">P&amp;L (₹)</th>
            <th class="C">Status</th>
        </tr>
    </thead>
    <tbody>
        @php
        $stCol = ['planned'=>'#667eea','running'=>'#d97706','completed'=>'#38a169','cancelled'=>'#e53e3e'];
        @endphp
        @forelse($trips as $i => $trip)
        @php $pnl = $trip->net_profit; @endphp
        <tr>
            <td class="C" style="color:#b0bac9;">{{ $i+1 }}</td>
            <td><span style="font-family:monospace;font-weight:700;color:#667eea;">{{ $trip->trip_no }}</span></td>
            <td>{{ $trip->trip_date?->format('d/m/Y') }}</td>
            <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ optional($trip->party)->company_name ?: optional($trip->party)->name }}</td>
            <td>{{ optional($trip->vehicle)->vehicle_number }}</td>
            <td>{{ optional($trip->driver)->name ?: '—' }}</td>
            <td>{{ $trip->from_location }} → {{ $trip->to_location }}</td>
            <td class="R">₹{{ number_format($trip->freight_amount,0) }}</td>
            <td class="R" style="color:#d97706;">₹{{ number_format($trip->total_expenses,0) }}</td>
            <td class="R" style="color:{{ $pnl >= 0 ? '#38a169' : '#e53e3e' }};">
                {{ $pnl >= 0 ? '+' : '' }}₹{{ number_format($pnl,0) }}
            </td>
            <td class="C">
                <span class="badge" style="background:#f4f6fb;color:{{ $stCol[$trip->status] ?? '#8a94a6' }};">{{ ucfirst($trip->status) }}</span>
            </td>
        </tr>
        @empty
        <tr><td colspan="11" style="text-align:center;padding:30px;color:#b0bac9;">No trips found for the selected filters.</td></tr>
        @endforelse
    </tbody>
    @if($trips->count() > 0)
    <tfoot>
        <tr>
            <td colspan="7">TOTALS — {{ $trips->count() }} trips</td>
            <td class="R">₹{{ number_format($summary['total_freight'],0) }}</td>
            <td class="R" style="color:#d97706;">₹{{ number_format($summary['total_expenses'],0) }}</td>
            <td class="R" style="color:{{ $summary['net_profit'] >= 0 ? '#38a169' : '#e53e3e' }};">
                {{ $summary['net_profit'] >= 0 ? '+' : '' }}₹{{ number_format($summary['net_profit'],0) }}
            </td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

</body>
</html>