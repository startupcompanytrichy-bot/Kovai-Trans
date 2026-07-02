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
  font-family:'Inter','Segoe UI',Arial,sans-serif;
  font-size:9.5px;color:#1e293b;background:#eef2f6;
  padding:32px 16px 48px;line-height:1.5;
}
.pw{max-width:820px;margin:0 auto}

.toolbar{display:flex;align-items:center;gap:10px;margin-bottom:20px}
.btn{
  display:inline-flex;align-items:center;gap:6px;
  padding:8px 18px;border-radius:6px;
  font-size:12px;font-weight:500;font-family:inherit;
  border:1px solid transparent;cursor:pointer;text-decoration:none;
  transition:all .15s;
}
.btn-back{background:#fff;color:#475569;border-color:#d1d5db}
.btn-back:hover{background:#f9fafb}
.btn-pr{background:#0891b2;color:#fff;border-color:#0891b2;box-shadow:0 2px 8px rgba(8,145,178,.2)}
.btn-pr:hover{background:#0e7490;border-color:#0e7490}

.doc{background:#fff;border-radius:0;box-shadow:0 2px 20px rgba(0,0,0,.05);position:relative}
.doc::before{content:'';display:block;height:4px;background:linear-gradient(90deg,#0891b2,#22d3ee,#0891b2)}

.doc-in{padding:36px 40px 32px}

/* ── HEADER ── */
.hr{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0}
.hl{display:flex;align-items:flex-start;gap:14px;min-width:0;flex:1}
.hl-l{
  width:46px;height:46px;border-radius:10px;flex-shrink:0;
  background:linear-gradient(135deg,#0891b2,#06b6d4);
  display:flex;align-items:center;justify-content:center;
  font-size:17px;font-weight:800;color:#fff;
  box-shadow:0 2px 8px rgba(8,145,178,.2);
}
.hl img{width:46px;height:46px;border-radius:10px;object-fit:contain;flex-shrink:0}
.hl-c{min-width:0;flex:1}
.hl-n{font-size:17px;font-weight:800;color:#0f172a;letter-spacing:-.3px;overflow-wrap:break-word}
.hl-i{font-size:8px;color:#94a3b8;margin-top:2px;overflow-wrap:break-word}
.hr-r{flex-shrink:0;margin-left:24px}
.hr-t{font-size:7px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px}
.hr-n{font-size:22px;font-weight:800;color:#0f172a;letter-spacing:-.4px;margin-top:1px}
.hr-m{font-size:8.5px;color:#94a3b8;margin-top:3px;text-align:right}
.hr-m em{font-style:normal;color:#0891b2;font-weight:600}

.hr-div{height:1px;background:#e2e8f0;margin:20px 0 24px}

/* ── PARTY CARDS ── */
.pb{display:flex;gap:24px;margin-bottom:28px}
.pb-c{flex:1;min-width:0;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden}
.pb-ch{background:#f8fafc;padding:7px 14px;border-bottom:1px solid #e2e8f0}
.pb-ch-t{font-size:7.5px;font-weight:700;color:#0891b2;text-transform:uppercase;letter-spacing:.7px}
.pb-cb{padding:10px 14px;min-height:56px}
.pb-n{font-size:11px;font-weight:700;color:#0f172a;margin-bottom:4px;overflow-wrap:break-word}
.pb-d{font-size:9px;color:#64748b;line-height:1.6;overflow-wrap:break-word}
.pb-d em{font-style:normal;color:#1e293b;font-weight:600}
.pb-d .sep{color:#d1d5db;margin:0 4px}

/* ── GOODS TABLE ── */
.t{width:100%;border-collapse:collapse;margin-bottom:0}
.t th{
  font-size:7.5px;font-weight:700;color:#475569;
  text-transform:uppercase;letter-spacing:.6px;
  padding:8px 10px;text-align:center;
  background:#f8fafc;
  border-bottom:2px solid #0891b2;
}
.t th:first-child{text-align:left;padding-left:14px;width:34%}
.t td{
  padding:9px 10px;font-size:9.5px;text-align:center;color:#1e293b;
  border-bottom:1px solid #f1f5f9;overflow-wrap:break-word;
}
.t td:first-child{text-align:left;padding-left:14px;font-weight:600;color:#0f172a;overflow-wrap:break-word}
.t tbody tr:last-child td{border-bottom:none}
.t .s{font-size:8px;font-weight:500;color:#94a3b8;margin-top:1px}

/* ── INFO BAND ── */
.ib{display:flex;margin:20px 0;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden}
.ib-c{flex:1;min-width:0;padding:10px 16px;display:flex;gap:20px}
.ib-c:first-child{border-right:1px solid #e2e8f0}
.ib-g{display:flex;gap:16px;flex:1}
.ib-g span{flex:1;min-width:0}
.ib-l{font-size:7px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px}
.ib-v{font-size:10px;font-weight:700;color:#0f172a;margin-top:1px;overflow-wrap:break-word}

/* ── BOTTOM ── */
.bb{display:flex;gap:28px;margin-top:20px;border-top:1px solid #e2e8f0;padding-top:20px}
.bb-l{flex:1;min-width:0}
.bb-h{font-size:7px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px}
.bb-t{font-size:9px;color:#64748b;line-height:1.6;overflow-wrap:break-word;min-height:32px}
.bb-aw{margin-top:10px;padding-top:10px;border-top:1px solid #f1f5f9;font-size:9px;overflow-wrap:break-word}
.bb-aw b{color:#475569}
.bb-aw i{color:#0891b2;font-weight:700;font-style:italic}

/* ── SIDEBAR ── */
.bb-r{width:215px;flex-shrink:0;min-width:0}
.bb-r .s-h{font-size:7px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px}
.bb-r .s-v{font-size:10.5px;font-weight:700;color:#0f172a;margin-bottom:12px;overflow-wrap:break-word}

.bb-r .s-f{text-align:right;margin-bottom:12px}
.bb-r .s-f-l{font-size:7px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px}
.bb-r .s-f-v{font-size:18px;font-weight:800;color:#0f172a;margin-top:1px}

.div-s{height:1px;background:#e2e8f0;margin:8px 0 10px}

.bb-b{display:flex;padding:2px 0;font-size:8.5px;min-width:0}
.bb-bl{color:#94a3b8;width:44px;flex-shrink:0}
.bb-be{color:#d1d5db;margin:0 3px;flex-shrink:0}
.bb-bv{font-weight:600;color:#1e293b;overflow-wrap:break-word;flex:1;min-width:0}

.ct{width:100%;border-collapse:collapse;margin:8px 0 0}
.ct td{padding:3px 0;font-size:9px;border-bottom:1px solid #f1f5f9;overflow-wrap:break-word}
.ct .n{color:#94a3b8}
.ct .v{text-align:right;font-weight:600;color:#1e293b;width:56px}
.ct tr:last-child td{border-bottom:none}
.ct .gt td{
  border-top:2px solid #0891b2!important;
  border-bottom:none!important;
  padding:5px 0;font-size:11px;font-weight:800;color:#0f172a;
}

.sg{margin-top:14px;padding-top:8px;text-align:right}
.sg-f{font-size:8px;color:#94a3b8;margin-bottom:14px}
.sg-c{font-size:10px;font-weight:800;color:#0f172a;overflow-wrap:break-word}
.sg-l{margin-top:3px;font-size:7px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px}

@media print{
  .toolbar,.no-print{display:none!important}
  body{background:#fff!important;padding:0}
  .pw{max-width:100%}
  .doc{box-shadow:none}
  .doc::before{height:3px}
  .doc-in{padding:28px 32px}
  @page{margin:3mm 4mm;size:A4 portrait}
}
</style>
</head>
<body>
@php
$t=$trip;$p=optional($t->party);$v=optional($t->vehicle);$d=optional($t->driver);$s=optional($t->supplier);$co=$company;
$freight=(float)($t->freight_amount??0);$loading=(float)($t->loading_charges??0);$unloading=(float)($t->unloading_charges??0);
$toll=(float)($t->toll_charges??0);$db=(float)($t->driver_bata??0);$other=(float)($t->other_expenses??0);
$lu=$loading+$unloading;$grand=$freight+$lu+$toll+$db+$other;
function n2w(int $n):string{if($n===0)return'Zero';$o=['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];$t=['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];$w='';if($n>=10000000){$w.=n2w((int)($n/10000000)).' Crore ';$n%=10000000;}if($n>=100000){$w.=n2w((int)($n/100000)).' Lakh ';$n%=100000;}if($n>=1000){$w.=n2w((int)($n/1000)).' Thousand ';$n%=1000;}if($n>=100){$w.=$o[(int)($n/100)].' Hundred ';$n%=100;}if($n>0){$w.=$n<20?$o[$n].' ':$t[(int)($n/10)].' '.$o[$n%10].' ';}return trim($w);}
$amtWords=n2w((int)round($grand)).' Only';
$words=array_filter(explode(' ',$co->company_name??'KT'));$mono=implode('',array_map(fn($w)=>strtoupper($w[0]),array_slice($words,0,2)));
$bizType='';if(!empty($co->business_type)){$dec=json_decode($co->business_type,true);$bizType=is_array($dec)?implode(' & ',$dec):$co->business_type;}
$addrParts=array_filter([$co->address??null,$co->district??null,$co->state??null]);$coAddr=implode(', ',$addrParts);if(!empty($co->pincode))$coAddr.=' – '.$co->pincode;
@endphp
<div class="pw">

  <div class="toolbar no-print">
    <a href="{{ route('trip') }}" class="btn btn-back">&#8592; Back</a>
    <button onclick="window.print()" class="btn btn-pr">&#128424; Print</button>
  </div>

  <div class="doc">
    <div class="doc-in">

      {{-- Header --}}
      <div class="hr">
        <div class="hl">
          @if(!empty($co->logo))
            <img src="{{ asset('storage/'.$co->logo) }}" alt="">
          @else
            <div class="hl-l">{{ $mono }}</div>
          @endif
          <div class="hl-c">
            <div class="hl-n">{{ strtoupper($co->company_name??'Kovai Trans') }}</div>
            <div class="hl-i">{{ $coAddr }}@if(!empty($co->email)) &middot; {{ $co->email }}@endif @if(!empty($co->phone)) &middot; {{ $co->phone }}@endif</div>
          </div>
        </div>
        <div class="hr-r">
          <div class="hr-t">Lorry Receipt</div>
          <div class="hr-n">{{ $t->lr_no?:$t->trip_no }}</div>
          <div class="hr-m">{{ optional($t->trip_date)->format('d/m/Y') }} &middot; <em>{{ $t->from_location }} → {{ $t->to_location }}</em></div>
        </div>
      </div>

      <div class="hr-div"></div>

      {{-- Party cards --}}
      <div class="pb">
        <div class="pb-c">
          <div class="pb-ch"><span class="pb-ch-t">Consignor (Sender)</span></div>
          <div class="pb-cb">
            <div class="pb-n">{{ strtoupper($p->company_name?:($p->name?:'—')) }}</div>
            <div class="pb-d">
              @if($p->address){{ $p->address }}<br>@endif
              <em>GSTIN</em> {{ $p->gst_no?:'—' }}<span class="sep">|</span><em>Phone</em> {{ $p->phone??'—' }}
            </div>
          </div>
        </div>
        <div class="pb-c">
          <div class="pb-ch"><span class="pb-ch-t">Consignee (Receiver)</span></div>
          <div class="pb-cb">
            <div class="pb-n">{{ strtoupper($s->name?:($t->to_location?:'—')) }}</div>
            <div class="pb-d">
              @if($s->address){{ $s->address }}<br>@endif
              @if($s->gst_no)<em>GSTIN</em> {{ $s->gst_no }}<span class="sep">|</span>@endif
              <em>Phone</em> {{ $s->mobile??'—' }}
            </div>
          </div>
        </div>
      </div>

      {{-- Goods table --}}
      <table class="t" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th>Description</th>
            <th>Qty</th>
            <th>Actual Wt.</th>
            <th>Charged Wt.</th>
            <th>Value</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              {{ $t->material?:'—' }}
              @if($t->load_type)<div class="s">{{ $t->load_type }}</div>@endif
            </td>
            <td>{{ $t->quantity?number_format((float)$t->quantity,0):'—' }}</td>
            <td>&mdash;</td>
            <td>&mdash;</td>
            <td>&mdash;</td>
          </tr>
        </tbody>
      </table>

      {{-- Transport & Invoice --}}
      <div class="ib">
        <div class="ib-c">
          <div class="ib-g">
            <span><div class="ib-l">Vehicle</div><div class="ib-v">{{ $v->vehicle_number?:'—' }}</div></span>
            <span><div class="ib-l">Driver</div><div class="ib-v">{{ $d->name??'—' }}</div></span>
            <span><div class="ib-l">Mode</div><div class="ib-v">{{ $t->billing_type?:$t->load_type?:'Road' }}</div></span>
          </div>
        </div>
        <div class="ib-c">
          <div class="ib-g">
            <span><div class="ib-l">Invoice</div><div class="ib-v">{{ $t->invoice_no?:'—' }}</div></span>
            <span><div class="ib-l">E-Way Bill</div><div class="ib-v">{{ $t->document_number?:'—' }}</div></span>
          </div>
        </div>
      </div>

      {{-- Bottom --}}
      <div class="bb">
        <div class="bb-l">
          <div class="bb-h">Remarks</div>
          <div class="bb-t">{{ $t->remarks?:'—' }}</div>
          <div class="bb-aw"><b>Amount in Words :</b> <i>{{ $amtWords }}</i></div>
        </div>
        <div class="bb-r">
          <div class="s-h">Consignee GSTIN</div>
          <div class="s-v">{{ $s->gst_no?:'—' }}</div>

          <div class="s-f">
            <div class="s-f-l">Freight</div>
            <div class="s-f-v">@if($freight>0) &#8377; {{ number_format($freight,0) }} @else &mdash; @endif</div>
          </div>

          <div class="s-h">Bank Details</div>
          @if(!empty($co->bank_name))<div class="bb-b"><span class="bb-bl">Bank</span><span class="bb-be">:</span><span class="bb-bv">{{ strtoupper($co->bank_name) }}</span></div>@endif
          @if(!empty($co->account_holder_name))<div class="bb-b"><span class="bb-bl">Name</span><span class="bb-be">:</span><span class="bb-bv">{{ strtoupper($co->account_holder_name) }}</span></div>@endif
          @if(!empty($co->account_number))<div class="bb-b"><span class="bb-bl">A/C</span><span class="bb-be">:</span><span class="bb-bv">{{ $co->account_number }}</span></div>@endif
          @if(!empty($co->ifsc_code))<div class="bb-b"><span class="bb-bl">IFSC</span><span class="bb-be">:</span><span class="bb-bv">{{ $co->ifsc_code }}</span></div>@endif
          @if(!empty($co->branch_name))<div class="bb-b"><span class="bb-bl">Branch</span><span class="bb-be">:</span><span class="bb-bv">{{ strtoupper($co->branch_name) }}</span></div>@endif

          <div class="div-s"></div>

          <table class="ct" cellspacing="0" cellpadding="0">
            <tr><td class="n">Freight</td><td class="v">{{ $freight>0?number_format($freight,0):'' }}</td></tr>
            <tr><td class="n">Statistical Charge</td><td class="v"></td></tr>
            <tr><td class="n">Loading / Unloading</td><td class="v">{{ $lu>0?number_format($lu,0):'' }}</td></tr>
            <tr><td class="n">Delivery / Toll</td><td class="v">{{ $toll>0?number_format($toll,0):'' }}</td></tr>
            <tr><td class="n">Insurance</td><td class="v">{{ $db>0?number_format($db,0):'' }}</td></tr>
            <tr><td class="n">GST</td><td class="v">{{ $other>0?number_format($other,0):'' }}</td></tr>
            <tr class="gt"><td class="n">Grand Total</td><td class="v">&#8377; {{ number_format($grand,0) }}</td></tr>
          </table>

          <div class="sg">
            <div class="sg-f">For <span class="sg-c">{{ strtoupper($co->company_name??'') }}</span></div>
            <div style="margin-bottom:14px"></div>
            <div class="sg-l">Authorised Signatory</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
</body>
</html>