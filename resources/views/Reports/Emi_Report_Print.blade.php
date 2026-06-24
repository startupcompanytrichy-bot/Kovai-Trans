<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>EMI Details Report — {{ now()->format('d M Y') }}</title>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{-webkit-print-color-adjust:exact;print-color-adjust:exact}
        body{font-family:"Segoe UI",Arial,sans-serif;font-size:12px;color:#1a1a1a;background:#fff;padding:10px 14px;}
        .np{text-align:center;margin-bottom:10px;}
        .np button{padding:6px 18px;border:none;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;background:#b91c1c;color:#fff;}
        .rp-hdr{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;padding-bottom:6px;border-bottom:2px solid #1a2340;}
        .rp-hdr h2{font-size:15px;font-weight:900;color:#1a2340;margin:0;}
        .rp-hdr .rp-date{font-size:10px;color:#8a94a6;}
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

<div class="rp-hdr">
    <h2>EMI Details Report</h2>
    <div class="rp-date">{{ $summary['total_loans'] }} loans &bull; Total Loan: ₹{{ number_format($summary['total_loan_amount'],0) }} &bull; {{ now()->format('d M Y') }}</div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:30px;" class="C">#</th>
            <th>Vehicle Register Number</th>
            <th>Financier</th>
            <th class="R">Loan Amount (₹)</th>
            <th class="R">Interest (₹)</th>
            <th class="R">Total Amount (₹)</th>
            <th class="C">Total EMIs</th>
            <th class="C">Total Number of Dues</th>
            <th class="C">Next Due Date</th>
            <th class="R">Monthly EMI (₹)</th>
            <th class="R">Total Paid Amount (₹)</th>
            <th class="R">Balance Amount (₹)</th>
            <th class="C">Loan Start Date</th>
            <th class="C">Loan End Date</th>
        </tr>
    </thead>
    <tbody>
        @php
        $stCol = ['active'=>'#667eea','overdue'=>'#e53e3e','closed'=>'#38a169'];
        @endphp
        @forelse($emis as $i => $e)
        @php
        $totalPaidFromPayments = (float) $e->payments->sum('amount_paid');
        $totalAmount = ($e->loan_amount ?? 0) + ($e->interest_amount ?? 0);
        $balanceAmount = $totalAmount - $totalPaidFromPayments;
        $balanceEmis = ($e->total_emis ?? 0) - ($e->paid_emis ?? 0);
        @endphp
        <tr>
            <td class="C" style="color:#b0bac9;">{{ $i+1 }}</td>
            <td>{{ optional($e->vehicle)->vehicle_number ?? '—' }}</td>
            <td><span style="font-family:monospace;font-weight:700;color:#667eea;">{{ $e->financier_name ?? '—' }}</span></td>
            <td class="R">₹{{ number_format($e->loan_amount,0) }}</td>
            <td class="R">₹{{ number_format($e->interest_amount ?? 0,0) }}</td>
            <td class="R" style="color:#4338ca;">₹{{ number_format($totalAmount,0) }}</td>
            <td class="C">{{ $e->total_emis ?? 0 }}</td>
            <td class="C" style="color:#e53e3e;">{{ $balanceEmis }}</td>
            <td class="C" style="{{ $e->is_overdue ? 'color:#e53e3e;font-weight:700;' : '' }}">
                {{ $e->next_due_date ? $e->next_due_date->format('d/m/Y') : '—' }}
            </td>
            <td class="R">₹{{ number_format($e->emi_amount,0) }}</td>
            <td class="R" style="color:#38a169;">₹{{ number_format($totalPaidFromPayments,0) }}</td>
            <td class="R" style="color:#d97706;">₹{{ number_format($balanceAmount,0) }}</td>
            <td class="C">{{ $e->loan_start_date ? $e->loan_start_date->format('d/m/Y') : '—' }}</td>
            <td class="C">{{ $e->loan_end_date ? $e->loan_end_date->format('d/m/Y') : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="14" style="text-align:center;padding:30px;color:#b0bac9;">No EMI records found.</td></tr>
        @endforelse
    </tbody>
    @if($emis->count() > 0)
    <tfoot>
        <tr>
            <td colspan="3">TOTALS — {{ $summary['total_loans'] }} loans</td>
            <td class="R">₹{{ number_format($summary['total_loan_amount'],0) }}</td>
            <td class="R">₹{{ number_format($summary['total_interest_amount'] ?? 0,0) }}</td>
            <td class="R" style="color:#4338ca;">₹{{ number_format($summary['total_amount'] ?? 0,0) }}</td>
            <td class="C">{{ $summary['total_emis'] ?? 0 }}</td>
            <td class="C" style="color:#e53e3e;">{{ $summary['total_balance_emis'] ?? 0 }}</td>
            <td></td>
            <td class="R" style="color:#d97706;">₹{{ number_format($summary['total_emi_monthly'],0) }}</td>
            <td class="R" style="color:#38a169;">₹{{ number_format($summary['total_paid_amount'] ?? 0,0) }}</td>
            <td class="R" style="color:#d97706;">₹{{ number_format($summary['total_balance_amount'] ?? 0,0) }}</td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

</body>
</html>
