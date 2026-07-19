<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Collection Report — {{ now()->format('d M Y') }}</title>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{-webkit-print-color-adjust:exact;print-color-adjust:exact}
        body{font-family:"Segoe UI",Arial,sans-serif;font-size:12px;color:#1a1a1a;background:#fff;padding:10px 14px;}
        .np{text-align:center;margin-bottom:10px;}
        .np button{padding:6px 18px;border:none;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;background:#b45309;color:#fff;}
        table{width:100%;border-collapse:collapse;font-size:10px;}
        thead tr{background:#1a2340;color:#fff;}
        thead th{padding:5px 6px;font-weight:700;text-align:left;white-space:nowrap;}
        thead th.R{text-align:right;}
        thead th.C{text-align:center;}
        tbody tr:nth-child(even) td{background:#f9fafc;}
        tbody td{padding:4px 6px;border-bottom:1px solid #edf0f7;vertical-align:middle;}
        tbody td.R{text-align:right;font-weight:600;}
        tbody td.C{text-align:center;}
        tbody tr.overdue td{background:#fff5f5!important;}
        tfoot td{background:#eef2ff;font-weight:800;border-top:2px solid #c7d2fe;padding:5px 6px;font-size:11px;}
        tfoot td.R{text-align:right;}
        .badge{display:inline-block;padding:1px 6px;border-radius:8px;font-size:9px;font-weight:700;}
        .rpt-summary{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px;}
        .rpt-summary .sm-card{background:#f8fafc;border-radius:6px;padding:8px 14px;text-align:center;flex:1;min-width:100px;}
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
        <div class="sm-label">Pending Trips</div>
        <div class="sm-value" style="color:#b45309;">{{ $summary['pending_count'] }}</div>
    </div>
    <div class="sm-card">
        <div class="sm-label">Total Outstanding</div>
        <div class="sm-value" style="color:#e53e3e;">₹{{ number_format($summary['total_outstanding'],0) }}</div>
    </div>
    <div class="sm-card" style="background:#fff5f5;">
        <div class="sm-label">Overdue</div>
        <div class="sm-value" style="color:#e53e3e;">{{ $summary['overdue'] }} trips</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:30px;" class="C">#</th>
            <th>Trip No</th>
            <th>Trip Date</th>
            <th>Party</th>
            <th>Vehicle</th>
            <th class="R">Freight (₹)</th>
            <th class="R">Collected (₹)</th>
            <th class="R">Outstanding (₹)</th>
            <th>Due Date</th>
            <th class="C">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($trips as $i => $trip)
        @php
            $isOverdue = $trip->collection_due_date && $trip->collection_due_date->isPast();
            $payColors = ['pending'=>['#fc8181','#fff5f5'],'partial'=>['#f6ad55','#fffbeb']];
            $pc = $payColors[$trip->payment_status ?? 'pending'] ?? ['#8a94a6','#f4f6fb'];
        @endphp
        <tr class="{{ $isOverdue ? 'overdue' : '' }}">
            <td class="C" style="color:#b0bac9;">{{ $i+1 }}</td>
            <td><strong style="color:#b45309;">{{ $trip->trip_no }}</strong></td>
            <td>{{ $trip->trip_date?->format('d M Y') }}</td>
            <td>{{ optional($trip->party)->company_name ?: optional($trip->party)->name }}</td>
            <td>{{ optional($trip->vehicle)->vehicle_number ?: '—' }}</td>
            <td class="R">₹{{ number_format($trip->freight_amount,0) }}</td>
            <td class="R" style="color:#38a169;">₹{{ number_format($trip->collected_amount,0) }}</td>
            <td class="R" style="color:#e53e3e;">₹{{ number_format($trip->outstanding_amount,0) }}</td>
            <td>
                @if($trip->collection_due_date)
                    <span style="color:{{ $isOverdue ? '#e53e3e' : '#1a2340' }};">{{ $trip->collection_due_date->format('d M Y') }}</span>
                    @if($isOverdue)
                    <div style="font-size:8px;color:#e53e3e;font-weight:700;">OVERDUE</div>
                    @endif
                @else
                    <span style="color:#b0bac9;">—</span>
                @endif
            </td>
            <td class="C">
                <span class="badge" style="background:{{ $pc[1] }};color:{{ $pc[0] }};">{{ ucfirst($trip->payment_status ?? 'pending') }}</span>
            </td>
        </tr>
        @empty
        <tr><td colspan="10" style="text-align:center;padding:30px;color:#b0bac9;">No pending collections found.</td></tr>
        @endforelse
    </tbody>
    @if($trips->count())
    <tfoot>
        <tr>
            <td colspan="5">TOTALS — {{ $trips->count() }} trips</td>
            <td class="R">₹{{ number_format($trips->sum('freight_amount'),0) }}</td>
            <td class="R" style="color:#38a169;">₹{{ number_format($trips->sum('collected_amount'),0) }}</td>
            <td class="R" style="color:#e53e3e;">₹{{ number_format($summary['total_outstanding'],0) }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
    @endif
</table>

</body>
</html>