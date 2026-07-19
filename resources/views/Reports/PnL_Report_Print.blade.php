<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>P&L Report — {{ now()->format('d M Y') }}</title>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{-webkit-print-color-adjust:exact;print-color-adjust:exact}
        body{font-family:"Segoe UI",Arial,sans-serif;font-size:12px;color:#1a1a1a;background:#fff;padding:10px 14px;}
        .np{text-align:center;margin-bottom:10px;}
        .np button{padding:6px 18px;border:none;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;background:#2f855a;color:#fff;}
        table{width:100%;border-collapse:collapse;font-size:10px;}
        thead tr{background:#1a2340;color:#fff;}
        thead th{padding:5px 6px;font-weight:700;text-align:left;white-space:nowrap;}
        thead th.R{text-align:right;}
        thead th.C{text-align:center;}
        tbody tr:nth-child(even) td{background:#f9fafc;}
        tbody td{padding:4px 6px;border-bottom:1px solid #edf0f7;vertical-align:middle;}
        tbody td.R{text-align:right;font-weight:600;}
        tbody td.C{text-align:center;}
        tbody tr.profit td{background:#f0fff4!important;}
        tbody tr.loss td{background:#fff5f5!important;}
        tfoot td{background:#eef2ff;font-weight:800;border-top:2px solid #c7d2fe;padding:5px 6px;font-size:11px;}
        tfoot td.R{text-align:right;}
        .badge{display:inline-block;padding:1px 6px;border-radius:8px;font-size:9px;font-weight:700;}
        .rpt-summary{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px;}
        .rpt-summary .sm-card{background:#f8fafc;border-radius:6px;padding:8px 14px;text-align:center;flex:1;min-width:90px;}
        .rpt-summary .sm-card .sm-label{font-size:9px;font-weight:700;color:#8a94a6;text-transform:uppercase;}
        .rpt-summary .sm-card .sm-value{font-size:16px;font-weight:800;color:#1a2340;margin-top:2px;}
        @media print{
            .np{display:none!important;}
            body{padding:6mm;}
            @page{size:A4 landscape;margin:6mm;}
        }
    </style>
</head>
<body>

<div class="np"><button onclick="window.print()">🖨 Print / Save PDF</button></div>

<div class="rpt-summary">
    <div class="sm-card">
        <div class="sm-label">Total Trips</div>
        <div class="sm-value" style="color:#667eea;">{{ $summary['total_trips'] }}</div>
    </div>
    <div class="sm-card">
        <div class="sm-label">Total Income</div>
        <div class="sm-value" style="color:#4338ca;">₹{{ number_format($summary['total_income'],0) }}</div>
    </div>
    <div class="sm-card">
        <div class="sm-label">Total Expenses</div>
        <div class="sm-value" style="color:#b45309;">₹{{ number_format($summary['total_expenses'],0) }}</div>
    </div>
    <div class="sm-card" style="background:{{ $summary['net_profit'] >= 0 ? '#f0fff4' : '#fff5f5' }};">
        <div class="sm-label">Net P&amp;L</div>
        <div class="sm-value" style="color:{{ $summary['net_profit'] >= 0 ? '#38a169' : '#e53e3e' }};">
            {{ $summary['net_profit'] >= 0 ? '+' : '' }}₹{{ number_format($summary['net_profit'],0) }}
        </div>
    </div>
    <div class="sm-card">
        <div class="sm-label">Profit / Loss</div>
        <div style="display:flex;justify-content:center;gap:10px;margin-top:2px;">
            <span style="font-size:14px;font-weight:800;color:#38a169;">{{ $summary['profit_trips'] }}↑</span>
            <span style="font-size:14px;font-weight:800;color:#e53e3e;">{{ $summary['loss_trips'] }}↓</span>
        </div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:30px;" class="C">#</th>
            <th>Trip No</th>
            <th>Date</th>
            <th>Party</th>
            <th>Route</th>
            <th class="R">Freight (₹)</th>
            <th class="R">Expenses (₹)</th>
            <th class="R">Net P&amp;L (₹)</th>
            <th class="C">Result</th>
        </tr>
    </thead>
    <tbody>
        @forelse($trips as $i => $trip)
        @php $pnl = $trip->net_profit; $isP = $trip->is_profitable; @endphp
        <tr class="{{ $isP ? 'profit' : 'loss' }}">
            <td class="C" style="color:#b0bac9;">{{ $i+1 }}</td>
            <td><strong style="color:#667eea;">{{ $trip->trip_no }}</strong></td>
            <td>{{ $trip->trip_date?->format('d M Y') }}</td>
            <td>{{ optional($trip->party)->company_name ?: optional($trip->party)->name }}</td>
            <td>{{ $trip->from_location }} → {{ $trip->to_location }}</td>
            <td class="R">₹{{ number_format($trip->freight_amount,0) }}</td>
            <td class="R" style="color:#b45309;">₹{{ number_format($trip->total_expenses,0) }}</td>
            <td class="R" style="font-weight:800;color:{{ $isP ? '#38a169' : '#e53e3e' }};">
                {{ $isP ? '+' : '' }}₹{{ number_format($pnl,0) }}
            </td>
            <td class="C">
                <span class="badge" style="background:{{ $isP ? '#f0fff4' : '#fff5f5' }};color:{{ $isP ? '#38a169' : '#e53e3e' }};">
                    {{ $isP ? 'Profit' : 'Loss' }}
                </span>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;padding:30px;color:#b0bac9;">No completed trips found.</td></tr>
        @endforelse
    </tbody>
    @if($trips->count())
    <tfoot>
        <tr>
            <td colspan="5">TOTALS — {{ $trips->count() }} trips</td>
            <td class="R">₹{{ number_format($summary['total_income'],0) }}</td>
            <td class="R" style="color:#b45309;">₹{{ number_format($summary['total_expenses'],0) }}</td>
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