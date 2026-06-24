@php
    $coL1 = implode(', ', array_filter([$company->address ?? null, $company->district ?? null]));
    $coL2 = implode(', ', array_filter([$company->state ?? null, !empty($company->pincode) ? $company->pincode : null]));
    $logoHtml = !empty($company->logo)
        ? '<img src="' . asset('storage/' . $company->logo) . '" style="width:75px;height:60px;object-fit:contain;display:block" alt="">'
        : '<div style="width:75px;height:60px;border:1px dashed #bec9d5;border-radius:4px;background:#f3f6f9;display:table">
            <div style="display:table-cell;vertical-align:middle;text-align:center;font-size:9px;color:#94a3b8;font-weight:700;letter-spacing:.5px">LOGO</div>
          </div>';
    $coAddrHtml = '';
    if ($coL1) $coAddrHtml .= e($coL1) . ',<br>';
    if ($coL2) $coAddrHtml .= e($coL2) . '<br>';
    if (!empty($company->phone)) {
        $phones = e($company->phone) . (!empty($company->phone2) ? ',' . e($company->phone2) : '');
        $coAddrHtml .= 'Phone: ' . $phones . '<br>';
    }
    if (!empty($company->gst)) $coAddrHtml .= 'GSTIN: ' . e($company->gst) . '<br>';
    $coExtraHtml = '';
    if (!empty($company->pan)) $coExtraHtml .= 'PAN: ' . e($company->pan);
    if (!empty($company->pan) && !empty($company->email)) $coExtraHtml .= ' &nbsp;|&nbsp; ';
    if (!empty($company->email)) $coExtraHtml .= 'Email: ' . e($company->email);
    $_pageTitle = 'Statement of Account - ' . ($filterLabel ?? 'All Dates');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>{{ $_pageTitle }}</title>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{-webkit-print-color-adjust:exact;print-color-adjust:exact}
        body{font-family:"Segoe UI","Helvetica Neue",Arial,sans-serif;font-size:11px;color:#1e293b;background:#e2e8f0;line-height:1.5}
        .pw{max-width:900px;margin:0 auto;padding:20px 12px 30px}

        .INV{width:100%;border-collapse:collapse;border:2px solid #1e3a5f;background:#fff;box-shadow:0 6px 24px rgba(0,0,0,.1);border-radius:4px;overflow:hidden}
        .INV td,.INV th{padding:0;vertical-align:top}
        .BB{border-bottom:1px solid #dce1ea}
        .BR{border-right:1px solid #dce1ea}
        .IT{width:100%;border-collapse:collapse;table-layout:fixed}

        .co-name{font-size:20px;font-weight:900;letter-spacing:.2px;color:#0f172a;line-height:1.2;margin-bottom:3px}
        .co-addr{font-size:9.5px;color:#64748b;line-height:1.8}
        .co-extra{font-size:9px;color:#64748b;line-height:1.7;margin-top:3px;padding-top:3px;border-top:1px dashed #dce1ea}

        .stmt-title{font-size:18px;font-weight:900;color:#1e3a5f;letter-spacing:.8px;text-transform:uppercase;white-space:nowrap;line-height:1}
        .stmt-sub{font-size:9px;font-weight:700;color:#94a3b8;letter-spacing:1px;text-transform:uppercase;margin-top:4px}

        .billing-hd{background:linear-gradient(135deg,#1e3a5f,#2d5a87);padding:5px 14px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:1.2px;color:#f8fafc}
        .bt-nm{font-size:13px;font-weight:800;color:#0f172a;margin-bottom:2px}
        .bt-ad{font-size:10.5px;color:#475569;line-height:1.75}

        .LDGR{width:100%;border-collapse:collapse;table-layout:fixed}
        .LDGR thead tr{background:linear-gradient(135deg,#1e3a5f,#2d5a87)}
        .LDGR th{padding:6px 8px;font-size:9px;font-weight:800;color:#f8fafc;border-bottom:none;border-right:1px solid rgba(255,255,255,.1);text-align:center;white-space:nowrap;text-transform:uppercase;letter-spacing:.6px}
        .LDGR th.L{text-align:left}
        .LDGR th:last-child{border-right:none}
        .LDGR td{padding:5px 8px;font-size:10px;color:#1e293b;border-bottom:1px solid #e8ecf3;border-right:1px solid #e8ecf3;vertical-align:middle}
        .LDGR td:last-child{border-right:none}
        .LDGR td.C{text-align:center}
        .LDGR td.R{text-align:right;font-variant-numeric:tabular-nums;white-space:nowrap;font-weight:600;font-size:10px}
        .LDGR tbody tr:nth-child(even) td{background:#f8fafc}
        .LDGR tbody tr.pay-row td{background:#fafbff!important}
        .LDGR tfoot td{background:#eef2ff;font-weight:800;border-top:2px solid #c7d2fe;padding:5px 8px;font-size:10px;border-right:1px solid #dce1ea}
        .LDGR tfoot td:last-child{border-right:none}

        .rpt-badge{display:inline-flex;align-items:center;padding:2px 7px;border-radius:10px;font-size:8px;font-weight:700;text-transform:uppercase}

        .mk{font-size:10px;color:#64748b;white-space:nowrap}
        .mv{font-size:10.5px;font-weight:700;color:#0f172a;white-space:nowrap}

        a.btn,button.btn{display:inline-flex;align-items:center;gap:5px;padding:8px 18px;border-radius:6px;font-size:12px;font-weight:700;font-family:inherit;border:none;cursor:pointer;text-decoration:none;letter-spacing:.2px}
        a.btn:hover,button.btn:hover{opacity:.82}
        .b-back{background:#fff;color:#4b5563;border:1.5px solid #c8d3dd}
        .b-pr{background:#dc2626;color:#fff}
        .tb{display:flex;align-items:center;gap:8px;margin-bottom:16px;flex-wrap:wrap}
        .tbl{flex:1;display:flex;align-items:center;gap:8px}
        .tbr{display:flex;gap:8px}

        @media print{
            a.btn,button.btn,.tb{display:none!important}
            body{background:#fff!important;margin:0}
            .pw{padding:0;max-width:100%}
            .INV{border:2px solid #1e3a5f!important;box-shadow:none!important;border-radius:0!important}
            @page{margin:6mm 8mm;size:A4 portrait}
        }
    </style>
</head>
<body>

{{-- ══ TOOLBAR ══ --}}
<div class="pw" style="padding-bottom:0">
    <div class="tb">
        <div class="tbl">
            <a href="{{ route('reports.parties-payment-ledger', request()->query()) }}" class="btn b-back">&#8592; Back to Ledger</a>
        </div>
        <div class="tbr">
            <button onclick="window.print()" class="btn b-pr">&#128424; Print / PDF</button>
        </div>
    </div>
</div>

{{-- ══ STATEMENT ══ --}}
<div class="pw">
    <table class="INV" cellspacing="0" cellpadding="0">

        {{-- ROW 1: Accent line --}}
        <tr>
            <td colspan="2" style="height:4px;background:linear-gradient(90deg,#1e3a5f,#3b82f6,#1e3a5f);padding:0;border:none"></td>
        </tr>

        {{-- ROW 2: Company Header (left) + Party Billing (right) --}}
        <tr>
            <td class="BB BR" style="padding:10px 0 8px 14px;width:60%">
                <table cellspacing="0" cellpadding="0" style="width:100%">
                    <tr>
                        <td style="width:78px;vertical-align:top;padding-right:10px">{!! $logoHtml !!}</td>
                        <td style="vertical-align:top;padding-right:12px">
                            <div class="co-name" style="font-size:18px;">{{ strtoupper($company->company_name ?? 'Company Name') }}</div>
                            <div class="co-addr">{!! $coAddrHtml !!}</div>
                            @if($coExtraHtml)<div class="co-extra">{!! $coExtraHtml !!}</div>@endif
                        </td>
                    </tr>
                </table>
            </td>
            <td class="BB" style="width:40%;padding:10px 14px 8px;vertical-align:top;">
                @if($selectedParty)
                @php
                    $partyName = $selectedParty->company_name ?: $selectedParty->name;
                    $ptParts = array_filter([
                        $selectedParty->address ?? null,
                        $selectedParty->city ?? null,
                        $selectedParty->state ?? null,
                        !empty($selectedParty->pincode) ? $selectedParty->pincode : null,
                    ]);
                    $btAddrHtml = '';
                    if ($ptParts) $btAddrHtml .= e(implode(', ', $ptParts)) . '<br>';
                    if (!empty($selectedParty->gst_no)) {
                        $btAddrHtml .= 'GSTIN: ' . e($selectedParty->gst_no);
                        if (!empty($selectedParty->pan_no) || !empty($selectedParty->phone)) $btAddrHtml .= ' &nbsp;|&nbsp; ';
                    }
                    if (!empty($selectedParty->pan_no)) {
                        $btAddrHtml .= 'PAN: ' . e($selectedParty->pan_no);
                        if (!empty($selectedParty->phone)) $btAddrHtml .= ' &nbsp;|&nbsp; ';
                    }
                    if (!empty($selectedParty->phone)) $btAddrHtml .= 'Phone: ' . e($selectedParty->phone);
                @endphp
                <div style="font-size:8px;font-weight:800;color:#667eea;text-transform:uppercase;letter-spacing:.8px;margin-bottom:3px;">BILLING</div>
                <div class="bt-nm" style="font-size:12px;">{{ strtoupper($partyName) }}</div>
                <div class="bt-ad" style="font-size:9px;">{!! $btAddrHtml !!}</div>
                @endif
            </td>
        </tr>

        {{-- ROW 3: Statements of Account band --}}
        <tr>
            <td colspan="2" class="BB">
                <div style="background:linear-gradient(135deg,#1e3a5f,#2d5a87);padding:5px 14px;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:1.2px;color:#f8fafc;">Statements of Account</span>
                    <span style="font-size:8px;font-weight:700;color:#94a3b8;">{{ $filterLabel ?? 'All Dates' }}</span>
                </div>
            </td>
        </tr>

        {{-- ROW 4: Account Summary heading --}}
        <tr>
            <td colspan="2" class="BB">
                <div class="billing-hd">Account Summary</div>
            </td>
        </tr>

        {{-- ROW 5: Ledger table --}}
        <tr>
            <td colspan="2" style="padding:0">
                <table class="LDGR" cellspacing="0" cellpadding="0">
                    <colgroup>
                        <col style="width:30px">
                        <col style="width:80px">
                        <col style="width:95px">
                        <col>
                        <col style="width:90px">
                        <col style="width:90px">
                        <col style="width:90px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Transaction</th>
                            <th class="L">Details</th>
                            <th style="text-align:right;padding-right:10px">Amount (₹)</th>
                            <th style="text-align:right;padding-right:10px">Payment (₹)</th>
                            <th style="text-align:right;padding-right:10px">Balance (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $i => $e)
                        @php $isInv = $e->transaction_type === 'Invoice'; @endphp
                        <tr class="{{ !$isInv ? 'pay-row' : '' }}">
                            <td class="C" style="color:#b0bac9;font-weight:600;">{{ $i + 1 }}</td>
                            <td>{{ $e->date?->format('d/m/Y') ?: '—' }}</td>
                            <td class="C">
                                @if($isInv)
                                <span class="rpt-badge" style="background:#eef2ff;color:#4338ca;">Invoice</span>
                                @else
                                <span class="rpt-badge" style="background:#f0fff4;color:#38a169;">Payment</span>
                                @endif
                            </td>
                            <td>
                                @if($isInv)
                                <strong style="font-size:10px;">{{ $e->details }}</strong>
                                @else
                                <span style="font-size:10px;">{{ $e->details }}</span>
                                @endif
                                <div style="font-size:9px;color:#8a94a6;margin-top:1px;">
                                    {{ optional($e->party)->company_name ?: optional($e->party)->name ?: '—' }}
                                    @if($e->vehicle) | {{ $e->vehicle->vehicle_number }} @endif
                                </div>
                            </td>
                            <td class="R" style="color:#4338ca;">
                                {{ $isInv ? '₹' . number_format($e->amount,0) : '—' }}
                            </td>
                            <td class="R" style="color:#38a169;">
                                {{ !$isInv ? '₹' . number_format($e->payment,0) : '—' }}
                            </td>
                            <td class="R" style="font-weight:800;color:{{ $e->balance > 0 ? '#e53e3e' : '#38a169' }};">
                                ₹{{ number_format($e->balance,0) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="C" style="padding:30px;color:#b0bac9;">No records found for the selected period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($entries->count())
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align:right;">Total</td>
                            <td style="text-align:right;color:#4338ca;">₹{{ number_format($totalAmount,0) }}</td>
                            <td style="text-align:right;color:#38a169;">₹{{ number_format($totalPayment,0) }}</td>
                            <td style="text-align:right;color:{{ $totalBalance > 0 ? '#e53e3e' : '#38a169' }};">₹{{ number_format($totalBalance,0) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </td>
        </tr>

    </table>
</div>

<script>
window.onload = function () {
    document.title = '{{ addslashes($_pageTitle) }}';
};
</script>
</body>
</html>
