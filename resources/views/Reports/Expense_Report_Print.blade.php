<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Expense Report — {{ now()->format('d M Y') }}</title>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{-webkit-print-color-adjust:exact;print-color-adjust:exact}
        body{font-family:"Segoe UI",Arial,sans-serif;font-size:12px;color:#1a1a1a;background:#fff;padding:10px 14px;}
        .np{text-align:center;margin-bottom:10px;}
        .np button{padding:6px 18px;border:none;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;background:#c53030;color:#fff;}
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
        .cat-chip{display:inline-block;padding:1px 6px;border-radius:8px;font-size:9px;font-weight:700;}
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
        <div class="sm-label">Total Entries</div>
        <div class="sm-value">{{ $expenses->count() }}</div>
    </div>
    @foreach($summary['by_category'] as $key => $amt)
    @php $cat = $categories[$key] ?? ['label'=>ucfirst($key),'color'=>'#8a94a6','bg'=>'#f4f6fb']; @endphp
    <div class="sm-card">
        <div class="sm-label">{{ $cat['label'] }}</div>
        <div class="sm-value" style="color:{{ $cat['color'] }};">₹{{ number_format($amt,0) }}</div>
    </div>
    @endforeach
    <div class="sm-card" style="background:#eef2ff;">
        <div class="sm-label">Grand Total</div>
        <div class="sm-value" style="color:#c53030;">₹{{ number_format($summary['total'],0) }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:30px;" class="C">#</th>
            <th>Date</th>
            <th>Category</th>
            <th>Trip</th>
            <th>Vehicle</th>
            <th>Driver</th>
            <th>Notes</th>
            <th class="R">Amount (₹)</th>
            <th class="C">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $i => $exp)
        @php $cat = $categories[$exp->category] ?? ['label'=>ucfirst($exp->category),'color'=>'#8a94a6','bg'=>'#f4f6fb']; @endphp
        <tr>
            <td class="C" style="color:#b0bac9;">{{ $i+1 }}</td>
            <td>{{ $exp->expense_date->format('d M Y') }}</td>
            <td><span class="cat-chip" style="background:{{ $cat['bg'] }};color:{{ $cat['color'] }};">{{ $cat['label'] }}</span></td>
            <td>{{ optional($exp->trip)->trip_no ?: '—' }}</td>
            <td>{{ optional($exp->vehicle)->vehicle_number ?: '—' }}</td>
            <td>{{ optional($exp->driver)->name ?: '—' }}</td>
            <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $exp->notes ?: '—' }}</td>
            <td class="R">₹{{ number_format($exp->amount,0) }}</td>
            <td class="C">
                @php $sc = ['pending'=>['#d97706','#fffbeb'],'approved'=>['#38a169','#f0fff4'],'rejected'=>['#e53e3e','#fff5f5']][$exp->status] ?? ['#8a94a6','#f4f6fb']; @endphp
                <span style="display:inline-block;padding:1px 6px;border-radius:8px;font-size:9px;font-weight:700;background:{{ $sc[1] }};color:{{ $sc[0] }};">{{ ucfirst($exp->status) }}</span>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;padding:30px;color:#b0bac9;">No expenses found.</td></tr>
        @endforelse
    </tbody>
    @if($expenses->count())
    <tfoot>
        <tr>
            <td colspan="7">TOTAL — {{ $expenses->count() }} entries</td>
            <td class="R" style="color:#c53030;">₹{{ number_format($summary['total'],0) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

</body>
</html>