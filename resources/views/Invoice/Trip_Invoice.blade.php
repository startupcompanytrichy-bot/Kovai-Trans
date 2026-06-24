@php
/* Title / filename — computed early so <title> can use it */
    $_ft = isset($trips) ? $trips->first() : ($trip ?? null);
    $_party = optional(optional($_ft)->party);
    $_invNo = $invoiceNo ?? (optional($_ft)->invoice_no ?: 'Invoice');
    $_partyName = $_party->company_name ?: ($_party->name ?? '');
    $_typeLabel = $invoiceTypeName ?? match($invoiceType ?? 'normal') {
    'rcm' => 'RCM INVOICE',
    'exempt' => 'EXEMPTED INVOICE',
    default => 'TAX INVOICE',
    };
    $_pageTitle = trim(implode(' - ', array_filter([$_typeLabel, $_invNo, $_partyName]))) ?: 'Tax Invoice';
    @endphp
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>{{ $_pageTitle }}</title>
        <style>
            *,
            *::before,
            *::after {
                box-sizing: border-box;
                margin: 0;
                padding: 0
            }

            html {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact
            }

            body {
                font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif;
                font-size: 11.5px;
                color: #1e293b;
                background: #e2e8f0;
                line-height: 1.5
            }

            .pw {
                max-width: 840px;
                margin: 0 auto;
                padding: 20px 12px 30px
            }

            .INV {
                width: 100%;
                border-collapse: collapse;
                border: 2px solid #1e3a5f;
                background: #fff;
                box-shadow: 0 6px 24px rgba(0, 0, 0, .1);
                border-radius: 4px;
                overflow: hidden
            }

            .INV td,
            .INV th {
                padding: 0;
                vertical-align: top
            }

            .BB {
                border-bottom: 1px solid #dce1ea
            }

            .BR {
                border-right: 1px solid #dce1ea
            }

            .IT {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed
            }

            .co-name {
                font-size: 22px;
                font-weight: 900;
                letter-spacing: .2px;
                color: #0f172a;
                line-height: 1.2;
                margin-bottom: 3px
            }

            .co-addr {
                font-size: 10px;
                color: #64748b;
                line-height: 1.8
            }

            .co-extra {
                font-size: 9.5px;
                color: #64748b;
                line-height: 1.7;
                margin-top: 3px;
                padding-top: 3px;
                border-top: 1px dashed #dce1ea
            }

            .inv-label {
                font-size: 20px;
                font-weight: 900;
                letter-spacing: 1px;
                color: #f8fafc;
                white-space: nowrap;
                line-height: 1;
                text-transform: uppercase
            }

            .inv-sub {
                font-size: 8.5px;
                font-weight: 700;
                color: #94a3b8;
                letter-spacing: 1.2px;
                text-transform: uppercase;
                margin-top: 5px
            }

            .mk {
                font-size: 10.5px;
                color: #64748b;
                white-space: nowrap
            }

            .mv {
                font-size: 11px;
                font-weight: 700;
                color: #0f172a;
                white-space: nowrap
            }

            .billing-hd {
                background: linear-gradient(135deg, #1e3a5f, #2d5a87);
                padding: 5px 14px;
                font-size: 9px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 1.2px;
                color: #f8fafc
            }

            .bt-nm {
                font-size: 13px;
                font-weight: 800;
                color: #0f172a;
                margin-bottom: 2px
            }

            .bt-ad {
                font-size: 10.5px;
                color: #475569;
                line-height: 1.75
            }

            .note-p {
                padding: 6px 14px;
                font-size: 10.5px;
                font-weight: 600;
                line-height: 1.5;
                border-left: 3px solid transparent
            }

            .note-p.rcm {
                border-left-color: #d97706;
                background: #fffbeb;
                color: #78350f
            }

            .note-p.exempt {
                border-left-color: #059669;
                background: #ecfdf5;
                color: #065f46
            }

            .ITEMS {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed
            }

            .ITEMS thead tr {
                background: linear-gradient(135deg, #1e3a5f, #2d5a87)
            }

            .ITEMS th {
                padding: 7px 8px;
                font-size: 9px;
                font-weight: 800;
                color: #f8fafc;
                border-bottom: none;
                border-right: 1px solid rgba(255, 255, 255, .1);
                text-align: center;
                white-space: nowrap;
                text-transform: uppercase;
                letter-spacing: .6px
            }

            .ITEMS th.L {
                text-align: left
            }

            .ITEMS th:last-child {
                border-right: none
            }

            .ITEMS td {
                padding: 6px 8px;
                font-size: 11px;
                color: #1e293b;
                border-bottom: 1px solid #e8ecf3;
                border-right: 1px solid #e8ecf3;
                vertical-align: middle
            }

            .ITEMS td:last-child {
                border-right: none
            }

            .ITEMS td.C {
                text-align: center
            }

            .ITEMS td.R {
                text-align: right;
                font-variant-numeric: tabular-nums;
                white-space: nowrap;
                font-weight: 600;
                font-size: 11px
            }

            .ITEMS tbody tr:nth-child(even) td {
                background: #f8fafc
            }

            .ITEMS tr.DL td {
                border-bottom: 2px solid #cbd5e1;
                background: #f1f5f9 !important
            }

            .ITEMS tr.P td {
                height: 18px;
                border-bottom: 1px solid #f1f5f9
            }

            .ITEMS tr.P0 td {
                height: 18px;
                border-bottom: none
            }

            .ITEMS tbody tr:last-child td {
                border-bottom: 1px solid #dce1ea
            }

            .TOTS {
                width: 100%;
                border-collapse: collapse
            }

            .TOTS td {
                padding: 4px 0;
                font-size: 11px;
                color: #1e293b;
                border-bottom: 1px solid #f1f5f9
            }

            .TOTS .TK {
                color: #64748b;
                padding-right: 10px
            }

            .TOTS .TV {
                text-align: right;
                font-weight: 700;
                font-variant-numeric: tabular-nums;
                white-space: nowrap
            }

            .TOTS tr.GR td {
                padding: 6px 0 3px;
                font-size: 14px;
                font-weight: 900;
                color: #1e3a5f;
                border-top: 2px solid #1e3a5f;
                border-bottom: none
            }

            .bk-thanks {
                font-size: 10.5px;
                color: #94a3b8;
                margin-bottom: 4px;
                font-style: italic
            }

            .bk-co {
                font-weight: 800;
                color: #dc2626;
                font-size: 11px
            }

            .bk-row {
                font-size: 10.5px;
                color: #334155;
                line-height: 1.85
            }

            .bk-upi {
                font-size: 10.5px;
                font-weight: 700;
                color: #2563eb;
                margin-top: 4px
            }

            .sig-nm {
                font-weight: 700;
                font-size: 11px;
                color: #1e293b;
                text-align: center;
                letter-spacing: .3px
            }

            .sig-ln {
                border-top: 1px solid #94a3b8;
                padding-top: 4px;
                text-align: center;
                font-size: 9.5px;
                color: #64748b;
                letter-spacing: .5px
            }

            .dec-h {
                font-weight: 800;
                font-size: 10.5px;
                color: #0f172a;
                margin-bottom: 2px;
                text-transform: uppercase;
                letter-spacing: .4px
            }

            .dec-b {
                font-size: 10px;
                color: #64748b;
                line-height: 1.7;
                text-align: justify
            }

            a.btn,
            button.btn {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 8px 18px;
                border-radius: 6px;
                font-size: 12px;
                font-weight: 700;
                font-family: inherit;
                border: none;
                cursor: pointer;
                text-decoration: none;
                letter-spacing: .2px
            }

            a.btn:hover,
            button.btn:hover {
                opacity: .82
            }

            .b-back {
                background: #fff;
                color: #4b5563;
                border: 1.5px solid #c8d3dd
            }

            .b-xl {
                background: #166534;
                color: #fff
            }

            .b-pr {
                background: #dc2626;
                color: #fff
            }

            .pill {
                display: inline-flex;
                align-items: center;
                padding: 4px 14px;
                border-radius: 20px;
                font-size: 10.5px;
                font-weight: 700;
                letter-spacing: .4px;
                border: 1.5px solid
            }

            .tb {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 16px;
                flex-wrap: wrap
            }

            .tbl {
                flex: 1;
                display: flex;
                align-items: center;
                gap: 8px
            }

            .tbr {
                display: flex;
                gap: 8px
            }

            @media print {

                a.btn,
                button.btn,
                .tb {
                    display: none !important
                }

                body {
                    background: #fff !important;
                    margin: 0
                }

                .pw {
                    padding: 0;
                    max-width: 100%
                }

                .INV {
                    border: 2px solid #1e3a5f !important;
                    box-shadow: none !important;
                    border-radius: 0 !important
                }

                @page {
                    margin: 6mm 8mm;
                    size: A4 portrait
                }
            }
        </style>
    </head>

    <body>

        @php
        if(!isset($trips)){$trips=collect([$trip]);}
        $ft = $trips->first();
        $party = optional($ft->party);

        $invType = $invoiceType ?? ($ft->invoice_type ?? 'normal');
        $invTypeName = $invoiceTypeName ?? 'TAX INVOICE';
        $titleText = ($invType==='exempt') ? 'BILL OF SUPPLY' : 'TAX INVOICE';
        $titleSub = match($invType){
        'rcm' => 'REVERSE CHARGE MECHANISM',
        'exempt' => 'GST EXEMPT SUPPLY',
        default => ''
        };

        [$cgR,$sgR] = match($invType){
        'rcm' => [2.5,2.5],
        'exempt' => [0.0,0.0],
        default => [9.0,9.0]
        };
        $sub = (float)$trips->sum('freight_amount');
        $cgst = round($sub*$cgR/100, 2);
        $sgst = round($sub*$sgR/100, 2);
        $grand = $sub + $cgst + $sgst;

        $invNo = $invoiceNo ?? ($ft->invoice_no ?: (($company->company_code??'INV').'-'.date('Y').'/'.$ft->id));
        $invDate = $ft->invoiced_at ? $ft->invoiced_at->format('d/m/Y') : date('d/m/Y');
        $plcSup = $company->place_of_supply ?? ($company->state ?? 'N/A');

        $coL1 = implode(', ', array_filter([$company->address??null, $company->district??null]));
        $coL2 = implode(', ', array_filter([$company->state??null, !empty($company->pincode)?$company->pincode:null]));
        $ptAddr = implode(', ', array_filter([
        $party->address??null, $party->city??null,
        $party->state??null, !empty($party->pincode)?$party->pincode:null,
        ]));

        function invAW(int $n):string{
        if($n===0)return 'Zero';
        $o=['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten',
        'Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
        $t=['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
        $w='';
        if($n>=10000000){$w.=invAW((int)($n/10000000)).' Crore '; $n%=10000000;}
        if($n>=100000) {$w.=invAW((int)($n/100000)).' Lakh '; $n%=100000; }
        if($n>=1000) {$w.=invAW((int)($n/1000)).' Thousand '; $n%=1000; }
        if($n>=100) {$w.=$o[(int)($n/100)].' Hundred '; $n%=100; }
        if($n>0) {$w.=$n<20?$o[$n].' ':$t[(int)($n/10)].' '.$o[$n%10].' ';}
    return trim($w);
    }
    $amtW  = invAW((int)round($grand)).' Only';
            $tids=$trips->pluck('id')->toArray();
            $tc = $trips->count();
            $padN = max(0, 5 - $tc);
            $totalCollected = (float)$trips->sum('collected_amount');
            $balanceDue = max(0, $grand - $totalCollected);

            $pC = match($invType){'rcm'=>'#92400e','exempt'=>'#155e75',default=>'#1e3a8a'};
            $pBg = match($invType){'rcm'=>'#fef9c3','exempt'=>'#ecfeff',default=>'#eff6ff'};
            $pBd = match($invType){'rcm'=>'#fde68a','exempt'=>'#a5f3fc',default=>'#bfdbfe'};

            $coAddrHtml = '';
            if ($coL1) $coAddrHtml .= e($coL1) . ',<br>';
            if ($coL2) $coAddrHtml .= e($coL2) . '<br>';
            if (!empty($company->phone)) {
            $phones = e($company->phone) . (!empty($company->phone2) ? ',' . e($company->phone2) : '');
            $coAddrHtml .= 'Phone: ' . $phones . '<br>';
            }
            if (!empty($company->gst)) $coAddrHtml .= 'GSTIN: ' . e($company->gst);

            $coExtraHtml = '';
            if (!empty($company->pan)) $coExtraHtml .= 'PAN: ' . e($company->pan);
            if (!empty($company->pan) && !empty($company->email)) $coExtraHtml .= ' &nbsp;|&nbsp; ';
            if (!empty($company->email)) $coExtraHtml .= 'Email: ' . e($company->email);

            $logoHtml = !empty($company->logo)
            ? '<img src="' . asset('storage/' . $company->logo) . '" style="width:80px;height:64px;object-fit:contain;display:block" alt="">'
            : '<div style="width:80px;height:64px;border:1px dashed #bec9d5;border-radius:4px;background:#f3f6f9;display:table">
                <div style="display:table-cell;vertical-align:middle;text-align:center;font-size:9px;color:#94a3b8;font-weight:700;letter-spacing:.5px">LOGO</div>
            </div>';

            $btAddrHtml = '';
            if ($ptAddr) $btAddrHtml .= e($ptAddr) . '<br>';
            if (!empty($party->gst_no)) {
            $btAddrHtml .= 'GSTIN: ' . e($party->gst_no);
            if (!empty($party->pan_no) || !empty($party->phone)) $btAddrHtml .= ' &nbsp;|&nbsp; ';
            }
            if (!empty($party->pan_no)) {
            $btAddrHtml .= 'PAN: ' . e($party->pan_no);
            if (!empty($party->phone)) $btAddrHtml .= ' &nbsp;|&nbsp; ';
            }
            if (!empty($party->phone)) $btAddrHtml .= 'Phone: ' . e($party->phone);
            @endphp

            {{-- ══ TOOLBAR ══ --}}
            <div class="pw" style="padding-bottom:0">
                <div class="tb">
                    <div class="tbl">
                        <a href="{{ route('invoice.index') }}" class="btn b-back">&#8592; Invoice List</a>
                        <span class="pill" style="color:{{$pC}};background:{{$pBg}};border-color:{{$pBd}}">
                            {{ strtoupper($invTypeName) }}
                            @if($invType==='rcm') &middot; 5% RCM
                            @elseif($invType==='exempt') &middot; 0% Exempt
                            @else &middot; 18% GST @endif
                        </span>
                    </div>
                    <div class="tbr">
                        <form method="POST" action="{{ route('invoice.excel') }}" style="display:inline">
                            @csrf
                            <input type="hidden" name="invoice_type" value="{{ $invType }}">
                            @foreach($tids as $tid)<input type="hidden" name="trip_ids[]" value="{{ $tid }}">@endforeach
                            <button type="submit" class="btn b-xl">&#8595; Excel</button>
                        </form>
                        <button onclick="var t=document.title;document.title='{{ addslashes($_pageTitle) }}';window.print();document.title=t;" class="btn b-pr">&#128424; Print / PDF</button>
                    </div>
                </div>
            </div>

            {{-- ══ INVOICE ══ --}}
            <div class="pw">
                <table class="INV" cellspacing="0" cellpadding="0">

                    {{-- ROW 1: COMPANY + TITLE --}}
                    <tr>
                        <td colspan="2" style="height:4px;background:linear-gradient(90deg,#1e3a5f,#3b82f6,#1e3a5f);padding:0;border:none"></td>
                    </tr>
                    <tr>
                        <td class="BB BR" style="padding:14px 0 12px 15px">
                            <table cellspacing="0" cellpadding="0" style="width:100%">
                                <tr>
                                    <td style="width:85px;vertical-align:top;padding-right:12px">{!! $logoHtml !!}</td>
                                    <td style="vertical-align:top;padding-right:15px">
                                        <div class="co-name">{{ strtoupper($company->company_name ?? 'Company Name') }}</div>
                                        <div class="co-addr">{!! $coAddrHtml !!}</div>
                                        @if($coExtraHtml)<div class="co-extra">{!! $coExtraHtml !!}</div>@endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="BB" style="width:220px;padding:12px 18px 10px;vertical-align:bottom;text-align:right;background:linear-gradient(135deg,#1e3a5f,#2d5a87)">
                            <div class="inv-label">{{ $titleText }}</div>
                            @if($titleSub)<div class="inv-sub">{{ $titleSub }}</div>@endif
                        </td>
                    </tr>

                    {{-- ROW 2: BILL NO / DATE / PLACE --}}
                    <tr>
                        <td colspan="2" class="BB" style="padding:0">
                            <table class="IT" cellspacing="0" cellpadding="0">
                                <colgroup>
                                    <col style="width:50%">
                                    <col style="width:50%">
                                </colgroup>
                                <tr>
                                    <td class="BR" style="padding:6px 14px;vertical-align:middle">
                                        <table cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="mk" style="padding-right:4px">Bill No</td>
                                                <td class="mk" style="padding-right:6px">:</td>
                                                <td class="mv">{{ $invNo }}</td>
                                            </tr>
                                            <tr>
                                                <td class="mk" style="padding-right:4px;padding-top:2px">Invoice Date</td>
                                                <td class="mk" style="padding-right:6px;padding-top:2px">:</td>
                                                <td class="mv" style="padding-top:2px">{{ $invDate }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="padding:6px 14px;vertical-align:middle">
                                        <table cellspacing="0" cellpadding="0">
                                            @php
                                            $fromPlaces = $trips->pluck('from_location')->unique()->filter()->values();
                                            $toPlaces = $trips->pluck('to_location')->unique()->filter()->values();
                                            @endphp
                                            <tr>
                                                <td class="mk" style="padding-right:4px">Place Of Supply</td>
                                                <td class="mk" style="padding-right:6px">:</td>
                                                <td class="mv">{{ $plcSup }}</td>
                                            </tr>

                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ROW 3: BILLING BAND --}}
                    <tr>
                        <td colspan="2" class="BB">
                            <div class="billing-hd">BILLING</div>
                        </td>
                    </tr>

                    {{-- ROW 4: BILL-TO --}}
                    <tr>
                        <td colspan="2" class="BB" style="padding:9px 14px;vertical-align:top">
                            <div class="bt-nm">{{ strtoupper($party->company_name ?: ($party->name ?? '—')) }}</div>
                            <div class="bt-ad">{!! $btAddrHtml !!}</div>
                        </td>
                    </tr>

                    {{-- ROW 4b: NOTE --}}
                    @if($invType==='rcm')
                    <tr>
                        <td colspan="2" class="BB">
                            <div class="note-p rcm">&#9888; Tax payable under Reverse Charge Mechanism — liability on recipient of service.</div>
                        </td>
                    </tr>
                    @elseif($invType==='exempt')
                    <tr>
                        <td colspan="2" class="BB">
                            <div class="note-p exempt">&#9432; GST Exempt Supply — No tax chargeable on this invoice.</div>
                        </td>
                    </tr>
                    @endif

                    {{-- ROW 5: ITEMS --}}
                    <tr>
                        <td colspan="2" class="BB" style="padding:0">
                            <table class="ITEMS" cellspacing="0" cellpadding="0">
                                <colgroup>
                                    <col style="width:34px">
                                    <col>
                                    <col style="width:72px">
                                    <col style="width:74px">
                                    <col style="width:108px">
                                    <col style="width:112px">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="L">Description</th>
                                        <th>HSN/SAC</th>
                                        <th>Total Box</th>
                                        <th style="text-align:right;padding-right:12px">Freight</th>
                                        <th style="text-align:right;padding-right:12px">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trips as $i => $t)
                                    @php
                                    $veh = optional($t->vehicle)->vehicle_number ?? '';
                                    $from = strtoupper($t->from_location ?? '');
                                    $to = strtoupper($t->to_location ?? '');
                                    $dateStr = $t->trip_date ? $t->trip_date->format('d/m/Y') : '';
                                    $desc = implode(' ', array_filter([
                                    $dateStr, $veh,
                                    ($from && $to) ? $from.' TO '.$to : null,
                                    !empty($t->lr_no) ? 'DC NO:-'.$t->lr_no : null,
                                    !empty($t->material) ? $t->material : null,
                                    !empty($t->quantity) ? $t->quantity.'BOXS' : null,
                                    ]));
                                    $isLast = ($i + 1 === $tc && $padN === 0);
                                    @endphp
                                    <tr class="{{ $isLast ? 'DL' : '' }}">
                                        <td class="C" style="font-weight:600">{{ $i + 1 }}</td>
                                        <td>{{ $desc }}</td>
                                        <td class="C" style="color:#444">996511</td>
                                        <td class="C" style="color:#444">1.00</td>
                                        <td class="R" style="padding-right:12px">{{ number_format($t->freight_amount, 2) }}</td>
                                        <td class="R" style="padding-right:12px;font-weight:700">{{ number_format($t->freight_amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                    @for($p = 0; $p < $padN; $p++)
                                        <tr class="{{ $p === $padN - 1 ? 'P0' : 'P' }}">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                    </tr>
                    @endfor
                    </tbody>
                </table>
                </td>
                </tr>

                {{-- ROW 6: TOTALS --}}
                <tr>
                    <td colspan="2" class="BB" style="padding:0">
                        <table class="IT" cellspacing="0" cellpadding="0">
                            <colgroup>
                                <col>
                                <col style="width:252px">
                            </colgroup>
                            <tr>
                                <td class="BR" style="padding:11px 14px;vertical-align:top;background:#f8fafc">
                                    <div style="font-size:10.5px;font-weight:800;color:#0f172a;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Total In Words</div>
                                    <div style="font-size:12px;font-weight:900;color:#1e293b;line-height:1.6;letter-spacing:.2px;">{{ $amtW }}</div>
                                </td>
                                <td style="padding:9px 14px;vertical-align:top;background:#f8fafc">
                                    <table class="TOTS" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td class="TK">Sub Total</td>
                                            <td class="TV">&#8377;{{ number_format($sub, 2) }}</td>
                                        </tr>
                                        @if($invType==='rcm')
                                        <tr>
                                            <td class="TK">CGST 2.5%</td>
                                            <td class="TV">{{ number_format($cgst, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="TK">SGST 2.5%</td>
                                            <td class="TV">{{ number_format($sgst, 2) }}</td>
                                        </tr>
                                        @elseif($invType==='exempt')
                                        <tr>
                                            <td class="TK">CGST 0%</td>
                                            <td class="TV">0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="TK">SGST 0%</td>
                                            <td class="TV">0.00</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td class="TK">CGST 9%</td>
                                            <td class="TV">{{ number_format($cgst, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="TK">SGST 9%</td>
                                            <td class="TV">{{ number_format($sgst, 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr class="GR">
                                            <td class="TK">Total</td>
                                            <td class="TV">&#8377;{{ number_format($grand, 2) }}</td>
                                        </tr>
                                        @if($totalCollected > 0)
                                        <tr>
                                            <td class="TK" style="color:#38a169;font-weight:700;">Collected</td>
                                            <td class="TV" style="color:#38a169;">&#8377;{{ number_format($totalCollected, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="TK" style="color:#c53030;font-weight:700;">Balance Due</td>
                                            <td class="TV" style="color:#c53030;">&#8377;{{ number_format($balanceDue, 2) }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- ROW 7: BANK + SIGNATURE --}}
                <tr>
                    <td colspan="2" class="BB" style="padding:0">
                        <table class="IT" cellspacing="0" cellpadding="0">
                            <colgroup>
                                <col>
                                <col style="width:252px">
                            </colgroup>
                            <tr>
                                <td class="BR" style="padding:11px 14px;vertical-align:top">
                                    <div style="font-size:10.5px;font-weight:800;color:#0f172a;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px">Bank Details</div>
                                    @php $_hasBank = !empty($company->bank_name)||!empty($company->account_number)||!empty($company->upi_id); @endphp
                                    @if($_hasBank)
                                    <div class="bk-co">{{ $company->company_name ?? '' }},</div>
                                    @if(!empty($company->account_holder_name))<div class="bk-row">A/C HOLDER:- {{ strtoupper($company->account_holder_name) }},</div>@endif
                                    @if(!empty($company->account_number))<div class="bk-row">A/C No:- {{ $company->account_number }},</div>@endif
                                    @if(!empty($company->ifsc_code))<div class="bk-row">IFSC CODE:- {{ $company->ifsc_code }},</div>@endif
                                    @if(!empty($company->bank_name))<div class="bk-row">BANK:- {{ strtoupper($company->bank_name) }},</div>@endif
                                    @if(!empty($company->branch_name))<div class="bk-row">BRANCH:- {{ strtoupper($company->branch_name) }}</div>@endif
                                    @if(!empty($company->upi_id))<div class="bk-upi">UPI ID:- {{ $company->upi_id }}</div>@endif
                                    @endif
                                    <div class="bk-thanks" style="margin-top:6px">Thanks for your business.</div>
                                </td>
                                <td style="padding:11px 16px;vertical-align:bottom;text-align:center">
                                    <div style="margin-bottom:50px">
                                        <div style="font-size:10.5px;color:#64748b;margin-bottom:4px">for</div>
                                        <div class="sig-nm">{{ $company->company_name ?? '' }}</div>
                                    </div>
                                    <div class="sig-ln">Authorised Signatory</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- ROW 8: DECLARATION --}}
                <tr>
                    <td colspan="2" style="padding:10px 14px;background:#f8fafc">
                        <div class="dec-h">Declaration</div>
                        <div class="dec-b">We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.
                            @if($invType === 'rcm') Tax is payable on reverse charge basis by the recipient of services. @endif
                        </div>
                    </td>
                </tr>

                </table>
            </div>
    </body>

    </html>