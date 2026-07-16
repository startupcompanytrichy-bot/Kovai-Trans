<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>LR — {{ $trip->lr_no ?: $trip->trip_no }}</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-print-color-adjust:exact;print-color-adjust:exact}
body{
  font-family:'Segoe UI',Arial,sans-serif;
  font-size:10px;line-height:1.45;color:#1a1a2e;
  background:#c8d3e0;
  padding:24px 14px 40px;
}
.pw{max-width:820px;margin:0 auto}

/* toolbar */
.toolbar{display:flex;gap:8px;margin-bottom:16px;align-items:center}
.tbtn{display:inline-flex;align-items:center;gap:6px;padding:7px 18px;border-radius:7px;font-size:12px;font-weight:600;font-family:inherit;border:none;cursor:pointer;text-decoration:none;}
.tbtn-back{background:#fff;color:#334155;box-shadow:0 1px 4px rgba(0,0,0,.12)}
.tbtn-print{background:#1e3a7b;color:#fff;box-shadow:0 2px 8px rgba(30,58,123,.28)}

/* document */
.doc{background:#fff;border:1.5px solid #b8c8e0;border-radius:3px;box-shadow:0 4px 24px rgba(0,0,0,.12);overflow:hidden;position:relative;}
.stripe{height:5px;background:linear-gradient(90deg,#1e3a7b 0%,#3b6fd4 50%,#1e3a7b 100%)}
.wm{position:absolute;top:50%;left:38%;transform:translate(-50%,-50%) rotate(-28deg);font-size:76px;font-weight:900;letter-spacing:8px;color:rgba(30,58,123,.035);pointer-events:none;text-transform:uppercase;white-space:nowrap;z-index:0;}
.doc>*:not(.wm){position:relative;z-index:1}

/* ══ HEADER ══ */
.hdr{display:flex;align-items:stretch;border-bottom:1.5px solid #d0daea}

/* left fields */
.hdr-info{flex:1;border-right:1.5px solid #d0daea}
.hi-row{display:flex;align-items:stretch;border-bottom:1px solid #e8eef7}
.hi-row:last-child{border-bottom:none}
.hi-k{width:80px;flex-shrink:0;padding:5px 8px;font-size:7.5px;font-weight:700;color:#3b6fd4;text-transform:uppercase;letter-spacing:.4px;background:#f5f8ff;border-right:1px solid #e0e9f7;display:flex;align-items:center;}
.hi-v{flex:1;padding:5px 10px;font-size:10px;font-weight:600;color:#1a1a2e;display:flex;align-items:center;word-break:break-word;}
.hi-v.b{font-size:11px;font-weight:800;color:#0e1c3d;}
.hi-v.lr{font-size:14px;font-weight:900;color:#1e3a7b;letter-spacing:.5px;}

/* company column */
.hdr-co{width:230px;flex-shrink:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:12px 10px;background:linear-gradient(160deg,#f0f5ff,#e6eeff);}
.co-logo{width:72px;height:72px;display:flex;align-items:center;justify-content:center;margin-bottom:7px;}
.co-logo img{width:72px;height:72px;object-fit:contain;border-radius:8px;}
.co-mono{width:72px;height:72px;border-radius:12px;background:linear-gradient(135deg,#1e3a7b,#3b6fd4);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:900;color:#fff;box-shadow:0 3px 10px rgba(30,58,123,.25);}
.co-name{font-size:18px;font-weight:900;color:#1e3a7b;text-transform:uppercase;letter-spacing:.6px;line-height:1.1;margin-bottom:2px;}
.co-sub{font-size:7px;font-weight:700;color:#6b7fa3;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;}
.co-addr{font-size:7.5px;color:#4a5568;line-height:1.6;margin-bottom:4px;}
.co-phone{display:inline-flex;align-items:center;gap:3px;background:#1e3a7b;color:#fff;border-radius:20px;padding:3px 11px;font-size:8px;font-weight:700;}

/* ══ GOODS TABLE ══ */
.goods{border-bottom:1.5px solid #d0daea}
.gtbl{width:100%;border-collapse:collapse;table-layout:fixed}
.gtbl thead{background:#1e3a7b}
.gtbl th{font-size:7.5px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.4px;padding:6px 8px;text-align:center;border-right:1px solid rgba(255,255,255,.15);}
.gtbl th:first-child{text-align:left;padding-left:12px}
.gtbl th:last-child{border-right:none}
.gtbl td{padding:8px 8px;font-size:10px;color:#1a1a2e;text-align:center;border-bottom:1px solid #edf1f9;border-right:1px solid #edf1f9;vertical-align:middle;}
.gtbl td:first-child{text-align:left;padding-left:12px;font-weight:700}
.gtbl td:last-child{border-right:none}
.g-sub{font-size:7.5px;color:#94a3b8;margin-top:2px;font-weight:400}
.gtbl .er td{height:24px;border-bottom:1px solid #f0f4fb;}
.gtbl .er td:not(:last-child){border-right:1px solid #f0f4fb;}

/* ══ TRANSPORT STRIP ══ */
.tstrip{display:flex;align-items:stretch;background:#f5f8ff;border-bottom:1.5px solid #d0daea;}
.ts-c{flex:1;padding:5px 10px;border-right:1px solid #e0e9f7;display:flex;flex-direction:column;justify-content:center;}
.ts-c:last-child{border-right:none}
.ts-k{font-size:7px;font-weight:700;color:#3b6fd4;text-transform:uppercase;letter-spacing:.4px;margin-bottom:2px;}
.ts-v{font-size:9.5px;font-weight:700;color:#1a1a2e;}

/* ══ BOTTOM 3-COL ══ */
.bot{display:flex;align-items:stretch;}

/* col A */
.bot-a{flex:1;padding:10px 12px;border-right:1.5px solid #d0daea;display:flex;flex-direction:column;}
.bf{margin-bottom:8px;}
.bf:last-of-type{margin-bottom:0}
.bf-k{font-size:7px;font-weight:700;color:#3b6fd4;text-transform:uppercase;letter-spacing:.4px;margin-bottom:2px;}
.bf-v{font-size:9px;color:#374151;line-height:1.65;min-height:18px;}
.bf-div{height:1px;background:#e8eef7;margin:6px 0;}
.bf-words{margin-top:auto;padding:7px 9px;background:#f0f5ff;border-radius:5px;border-left:3px solid #3b6fd4;}
.bf-words .wk{font-size:7px;font-weight:700;color:#3b6fd4;text-transform:uppercase;letter-spacing:.3px;margin-bottom:2px;}
.bf-words .wv{font-size:8.5px;font-weight:700;color:#1e3a7b;font-style:italic;line-height:1.5;}

/* col B */
.bot-b{width:198px;flex-shrink:0;padding:10px 12px;border-right:1.5px solid #d0daea;display:flex;flex-direction:column;}
.bk-hd{font-size:7px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.4px;background:#1e3a7b;padding:4px 8px;border-radius:4px;margin-bottom:7px;text-align:center;}
.bk-r{display:flex;align-items:flex-start;font-size:8.5px;margin-bottom:3px;}
.bk-l{width:36px;flex-shrink:0;color:#94a3b8;font-weight:600;}
.bk-s{color:#c9d4e5;margin:0 3px;flex-shrink:0;}
.bk-v{font-weight:700;color:#1a1a2e;flex:1;word-break:break-all;}
.sig{margin-top:auto;padding-top:8px;border-top:1px dashed #d0daea;text-align:center;}
.sig-f{font-size:7.5px;color:#6b7fa3;margin-bottom:14px;}
.sig-f strong{color:#1e3a7b;text-transform:uppercase;}
.sig-ln{border-top:1.5px solid #1e3a7b;margin:0 14px 3px;}
.sig-lb{font-size:7px;font-weight:700;color:#1e3a7b;text-transform:uppercase;letter-spacing:.4px;}

/* col C */
.bot-c{width:165px;flex-shrink:0;}
.ctbl{width:100%;border-collapse:collapse;}
.ctbl td{padding:4px 9px;font-size:8.5px;border-bottom:1px solid #f0f3fa;vertical-align:middle;}
.ctbl .ck{color:#4a5568;}
.ctbl .cv{text-align:right;font-weight:700;color:#1a1a2e;width:52px;}
.grand td{background:#1e3a7b!important;border-bottom:none!important;padding:6px 9px!important;font-size:11px!important;font-weight:900!important;color:#fff!important;}

/* footer */
.doc-foot{background:#f5f8ff;border-top:1px solid #e0e9f7;padding:5px 13px;display:flex;align-items:center;justify-content:space-between;}
.doc-foot .ft{font-size:7px;color:#94a3b8;font-weight:600;letter-spacing:.3px;}

@media print{
  .toolbar{display:none!important}
  body{background:#fff!important;padding:0}
  .pw{max-width:100%}
  .doc{box-shadow:none;border-color:#c0ccd8}
  .stripe{height:4px}
  @page{margin:5mm 5mm;size:A4 portrait}
}
</style>
</head>
<body>
@php
$t  = $trip;
$p  = optional($t->party);
$v  = optional($t->vehicle);
$d  = optional($t->driver);
$s  = optional($t->supplier);
$co = $company;

$freight   = (float)($t->freight_amount    ?? 0);
$loading   = (float)($t->loading_charges   ?? 0);
$unloading = (float)($t->unloading_charges ?? 0);
$toll      = (float)($t->toll_charges      ?? 0);
$bata      = (float)($t->driver_bata       ?? 0);
$other     = (float)($t->other_expenses    ?? 0);
$lu        = $loading + $unloading;
$grand     = $freight + $lu + $toll + $bata + $other;

function lrN2W(int $n): string {
    if($n===0)return'Zero';
    $o=['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
    $t=['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
    $w='';
    if($n>=10000000){$w.=lrN2W((int)($n/10000000)).' Crore ';$n%=10000000;}
    if($n>=100000){$w.=lrN2W((int)($n/100000)).' Lakh ';$n%=100000;}
    if($n>=1000){$w.=lrN2W((int)($n/1000)).' Thousand ';$n%=1000;}
    if($n>=100){$w.=$o[(int)($n/100)].' Hundred ';$n%=100;}
    if($n>0){$w.=$n<20?$o[$n].' ':$t[(int)($n/10)].' '.$o[$n%10].' ';}
    return trim($w);
}
$amtWords = $grand > 0 ? 'Rupees '.lrN2W((int)round($grand)).' Only' : '—';

$wds  = array_filter(explode(' ', $co->company_name ?? 'KT'));
$mono = implode('', array_map(fn($w)=>strtoupper($w[0]), array_slice($wds,0,2)));

$aParts = array_filter([$co->address??null,$co->district??null,$co->state??null]);
$coAddr = implode(', ',$aParts);
if(!empty($co->pincode)) $coAddr .= ' – '.$co->pincode;

$bizType = '';
if(!empty($co->business_type)){
    $dec=json_decode($co->business_type,true);
    $bizType=is_array($dec)?implode(' & ',$dec):$co->business_type;
}
@endphp

<div class="pw">
  <div class="toolbar no-print">
    <a href="{{ route('trip') }}" class="tbtn tbtn-back">&#8592; Back</a>
    <button onclick="window.print()" class="tbtn tbtn-print">&#128424; Print LR</button>
  </div>

  <div class="doc">
    <div class="stripe"></div>
    <div class="wm">{{ strtoupper(substr($co->company_name??'LR',0,8)) }}</div>

    {{-- HEADER --}}
    <div class="hdr">

      {{-- Left: fields --}}
      <div class="hdr-info">
        <div class="hi-row">
          <div class="hi-k">Consignor</div>
          <div class="hi-v b">{{ strtoupper($p->company_name?:($p->name?:'—')) }}</div>
        </div>
        <div class="hi-row">
          <div class="hi-k">GSTIN</div>
          <div class="hi-v">{{ $p->gst_no?:'—' }}</div>
        </div>
        <div class="hi-row">
          <div class="hi-k">Consignee</div>
          <div class="hi-v b">{{ strtoupper($s->name?:($t->to_location?:'—')) }}</div>
        </div>
        <div class="hi-row">
          <div class="hi-k">GSTIN</div>
          <div class="hi-v">{{ $s->gst_no?:'—' }}</div>
        </div>
        <div class="hi-row">
          <div class="hi-k">From</div>
          <div class="hi-v">{{ $t->from_location??'—' }}</div>
        </div>
        <div class="hi-row">
          <div class="hi-k">To</div>
          <div class="hi-v">{{ $t->to_location??'—' }}</div>
        </div>
        <div class="hi-row">
          <div class="hi-k">LR No</div>
          <div class="hi-v lr">{{ $t->lr_no?:$t->trip_no }}</div>
        </div>
        <div class="hi-row">
          <div class="hi-k">Date</div>
          <div class="hi-v">{{ optional($t->trip_date)->format('d / m / Y') ?? '—' }}</div>
        </div>
      </div>

      {{-- Company --}}
      <div class="hdr-co">
        <div class="co-logo">
          @if(!empty($co->logo))
            <img src="{{ asset('storage/'.$co->logo) }}" alt="{{ $co->company_name }}">
          @else
            <div class="co-mono">{{ $mono }}</div>
          @endif
        </div>
        <div class="co-name">{{ $co->company_name??'Company' }}</div>
        @if($bizType)<div class="co-sub">{{ $bizType }}</div>@endif
        @if($coAddr)<div class="co-addr">{{ $coAddr }}</div>@endif
        @if(!empty($co->email))<div class="co-addr">{{ $co->email }}</div>@endif
        @if(!empty($co->phone))<div class="co-phone">&#128222; {{ $co->phone }}</div>@endif
      </div>

    </div>{{-- /hdr --}}

    {{-- GOODS TABLE --}}
    <div class="goods">
      <table class="gtbl" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th style="width:30%">Description of Goods</th>
            <th style="width:9%">PKGS</th>
            <th style="width:14%">Actual Weight</th>
            <th style="width:14%">Charged Weight</th>
            <th style="width:14%">Value of Goods</th>
            <th style="width:19%">Particulars</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{ $t->material?:'—' }}@if($t->load_type)<div class="g-sub">{{ $t->load_type }}</div>@endif</td>
            <td>{{ $t->quantity?number_format((float)$t->quantity,0):'' }}</td>
            <td></td><td></td><td></td><td></td>
          </tr>
          <tr class="er"><td></td><td></td><td></td><td></td><td></td><td></td></tr>
          <tr class="er"><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        </tbody>
      </table>
    </div>

    {{-- TRANSPORT STRIP --}}
    <div class="tstrip">
      <div class="ts-c"><div class="ts-k">Vehicle No.</div><div class="ts-v">{{ $v->vehicle_number?:'—' }}</div></div>
      <div class="ts-c"><div class="ts-k">Driver</div><div class="ts-v">{{ $d->name??'—' }}</div></div>
      <div class="ts-c"><div class="ts-k">Delivery Mode</div><div class="ts-v">{{ $t->billing_type?ucwords(str_replace('_',' ',$t->billing_type)):'Road' }}</div></div>
      <div class="ts-c"><div class="ts-k">Invoice No.</div><div class="ts-v">{{ $t->invoice_no?:'—' }}</div></div>
      <div class="ts-c"><div class="ts-k">E-Way Bill No.</div><div class="ts-v">{{ $t->document_number?:'—' }}</div></div>
    </div>

    {{-- BOTTOM --}}
    <div class="bot">

      {{-- Col A --}}
      <div class="bot-a">
        <div class="bf">
          <div class="bf-k">Remarks</div>
          <div class="bf-v">{{ $t->remarks?:'—' }}</div>
        </div>
        <div class="bf-div"></div>
        <div class="bf">
          <div class="bf-k">Private Marks</div>
          <div class="bf-v">—</div>
        </div>
        <div class="bf-div"></div>
        <div class="bf-words">
          <div class="wk">Amount in Words</div>
          <div class="wv">{{ $amtWords }}</div>
        </div>
      </div>

      {{-- Col B --}}
      <div class="bot-b">
        <div class="bk-hd">Payment via Bank Transfer Only</div>
        @if(!empty($co->bank_name))<div class="bk-r"><span class="bk-l">Bank</span><span class="bk-s">:</span><span class="bk-v">{{ strtoupper($co->bank_name) }}</span></div>@endif
        @if(!empty($co->account_holder_name))<div class="bk-r"><span class="bk-l">Name</span><span class="bk-s">:</span><span class="bk-v">{{ strtoupper($co->account_holder_name) }}</span></div>@endif
        @if(!empty($co->account_number))<div class="bk-r"><span class="bk-l">A/C No</span><span class="bk-s">:</span><span class="bk-v">{{ $co->account_number }}</span></div>@endif
        @if(!empty($co->ifsc_code))<div class="bk-r"><span class="bk-l">IFSC</span><span class="bk-s">:</span><span class="bk-v">{{ $co->ifsc_code }}</span></div>@endif
        @if(!empty($co->branch_name))<div class="bk-r"><span class="bk-l">Branch</span><span class="bk-s">:</span><span class="bk-v">{{ strtoupper($co->branch_name) }}</span></div>@endif
        <div class="sig">
          <div class="sig-f">For <strong>{{ $co->company_name??'' }}</strong></div>
          <div class="sig-ln"></div>
          <div class="sig-lb">Authorised Signatory</div>
        </div>
      </div>

      {{-- Col C --}}
      <div class="bot-c">
        <table class="ctbl" cellspacing="0" cellpadding="0">
          <tr><td class="ck">Freight</td><td class="cv">{{ $freight>0?'₹'.number_format($freight,0):'' }}</td></tr>
          <tr><td class="ck">Statistical</td><td class="cv"></td></tr>
          <tr><td class="ck">Loading/Unloading</td><td class="cv">{{ $lu>0?'₹'.number_format($lu,0):'' }}</td></tr>
          <tr><td class="ck">Delivery/Toll</td><td class="cv">{{ $toll>0?'₹'.number_format($toll,0):'' }}</td></tr>
          <tr><td class="ck">Insurance</td><td class="cv">{{ $bata>0?'₹'.number_format($bata,0):'' }}</td></tr>
          <tr><td class="ck">Extra Charges</td><td class="cv">{{ $other>0?'₹'.number_format($other,0):'' }}</td></tr>
          <tr><td class="ck">GST</td><td class="cv"></td></tr>
          <tr class="grand"><td class="ck">Grand Total</td><td class="cv">₹{{ number_format($grand,0) }}</td></tr>
        </table>
      </div>

    </div>{{-- /bot --}}

    {{-- FOOTER --}}
    <div class="doc-foot">
      <span class="ft">&#9679; Subject to jurisdiction</span>
      <span class="ft">{{ $co->company_name??'' }} — Lorry Receipt</span>
      <span class="ft">Goods received in apparent good condition</span>
    </div>

  </div>
</div>
</body>
</html>
