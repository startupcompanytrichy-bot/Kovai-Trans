@php
if (!isset($trips)) { $trips = collect([$trip]); }
$ft    = $trips->first();
$party = optional($ft->party);

$invType     = $invoiceType     ?? ($ft->invoice_type ?? 'normal');
$invTypeName = $invoiceTypeName ?? 'TAX INVOICE';
$titleText   = ($invType === 'exempt') ? 'BILL OF SUPPLY' : 'TAX INVOICE';
$titleSub    = match($invType) { 'rcm' => 'REVERSE CHARGE MECHANISM', 'exempt' => 'GST EXEMPT SUPPLY', default => '' };

$cgR   = (float)($cgstRate ?? 0);
$sgR   = (float)($sgstRate ?? 0);
$sub   = (float)$trips->sum('freight_amount');
$cgst  = round($sub * $cgR / 100, 2);
$sgst  = round($sub * $sgR / 100, 2);
$grand = $sub + $cgst + $sgst;

$invNo   = $invoiceNo ?? ($ft->invoice_no ?: 'INV');
$invDate = $ft->invoiced_at ? $ft->invoiced_at->format('d/m/Y') : date('d/m/Y');
$plcSup  = $company->place_of_supply ?? ($company->state ?? '');

$coAddr = implode(', ', array_filter([$company->address ?? null, $company->district ?? null]));
$coCity = implode(', ', array_filter([$company->state ?? null, !empty($company->pincode) ? $company->pincode : null]));
$ptAddr = implode(', ', array_filter([$party->address ?? null, $party->city ?? null, $party->state ?? null, !empty($party->pincode) ? $party->pincode : null]));

if (!function_exists('pdfInvAW2')) {
    function pdfInvAW2(int $n): string {
        if ($n === 0) return 'Zero';
        $o = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
        $t = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
        $w = '';
        if ($n >= 10000000) { $w .= pdfInvAW2((int)($n/10000000)).' Crore '; $n %= 10000000; }
        if ($n >= 100000)   { $w .= pdfInvAW2((int)($n/100000)).' Lakh ';   $n %= 100000;   }
        if ($n >= 1000)     { $w .= pdfInvAW2((int)($n/1000)).' Thousand '; $n %= 1000;     }
        if ($n >= 100)      { $w .= $o[(int)($n/100)].' Hundred ';           $n %= 100;      }
        if ($n > 0)         { $w .= $n < 20 ? $o[$n].' ' : $t[(int)($n/10)].' '.$o[$n%10].' '; }
        return trim($w);
    }
}
$amtW           = pdfInvAW2((int)round($grand)) . ' Only';
$tc             = $trips->count();
$padN           = max(0, 5 - $tc);
$totalCollected = (float)$trips->sum('collected_amount');
$balanceDue     = max(0, $grand - $totalCollected);
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }
table { border-collapse: collapse; width: 100%; }
.outer { border: 2px solid #1e3a5f; width: 100%; }
.hdr-bar { background: #1e3a5f; height: 4px; }
.co-cell { padding: 10px 0 10px 12px; vertical-align: top; border-bottom: 1px solid #dce1ea; border-right: 1px solid #dce1ea; }
.co-name { font-size: 18px; font-weight: 700; color: #0f172a; }
.co-sub  { font-size: 9px; color: #64748b; line-height: 1.8; margin-top: 3px; }
.co-extra{ font-size: 9px; color: #64748b; margin-top: 3px; border-top: 1px dashed #dce1ea; padding-top: 3px; }
.title-cell { background: #1e3a5f; color: #fff; text-align: right; padding: 10px 14px; vertical-align: bottom; border-bottom: 1px solid #1e3a5f; width: 200px; }
.title-main { font-size: 17px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
.title-sub  { font-size: 8px; font-weight: 700; color: #94a3b8; letter-spacing: 1px; text-transform: uppercase; margin-top: 4px; }
.meta-cell  { padding: 5px 12px; font-size: 10px; vertical-align: middle; border-bottom: 1px solid #dce1ea; }
.billing-hd { background: #1e3a5f; color: #fff; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 4px 12px; }
.bt-name { font-size: 12px; font-weight: 700; color: #0f172a; margin-bottom: 2px; }
.bt-addr { font-size: 9.5px; color: #475569; line-height: 1.7; }
.note-box { padding: 5px 10px; font-size: 9.5px; font-weight: 600; border-bottom: 1px solid #dce1ea; }
.note-rcm    { border-left: 3px solid #d97706; background: #fffbeb; color: #78350f; }
.note-exempt { border-left: 3px solid #059669; background: #ecfdf5; color: #065f46; }
.items-hdr th { background: #1e3a5f; color: #fff; font-size: 8.5px; font-weight: 700; padding: 5px 6px; text-align: center; text-transform: uppercase; letter-spacing: .5px; border-right: 1px solid rgba(255,255,255,.15); }
.items-hdr th.L { text-align: left; }
.items-hdr th:last-child { border-right: none; }
.item-td { padding: 5px 6px; font-size: 10px; border-bottom: 1px solid #e8ecf3; border-right: 1px solid #e8ecf3; vertical-align: middle; }
.item-td-last { border-right: none; }
.item-last { border-bottom: 2px solid #cbd5e1; background: #f1f5f9; }
.pad-row { height: 16px; border-bottom: 1px solid #f1f5f9; }
.pad-last { height: 16px; }
.C { text-align: center; }
.R { text-align: right; font-weight: 600; }
.totals-lhs { padding: 10px 12px; vertical-align: top; background: #f8fafc; border-right: 1px solid #dce1ea; border-bottom: 1px solid #dce1ea; }
.totals-rhs { padding: 8px 12px; vertical-align: top; background: #f8fafc; border-bottom: 1px solid #dce1ea; width: 230px; }
.amt-words-lbl { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; color: #0f172a; }
.amt-words-val { font-size: 11px; font-weight: 700; color: #1e293b; }
.tots-td { padding: 3px 0; font-size: 10px; border-bottom: 1px solid #f1f5f9; }
.tots-lbl { color: #64748b; }
.tots-val { text-align: right; font-weight: 700; }
.tots-grand { font-size: 13px; font-weight: 700; color: #1e3a5f; border-top: 2px solid #1e3a5f; padding: 5px 0 3px; }
.bank-cell { padding: 10px 12px; vertical-align: top; border-bottom: 1px solid #dce1ea; border-right: 1px solid #dce1ea; }
.sig-cell  { padding: 10px 14px; text-align: center; vertical-align: bottom; border-bottom: 1px solid #dce1ea; width: 230px; }
.bank-hd   { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #0f172a; margin-bottom: 5px; }
.bank-co   { font-weight: 700; color: #dc2626; font-size: 10px; }
.bank-row  { font-size: 9.5px; color: #334155; line-height: 1.8; }
.bank-upi  { font-size: 9.5px; font-weight: 700; color: #2563eb; margin-top: 3px; }
.bank-thanks { font-size: 9.5px; color: #94a3b8; font-style: italic; margin-top: 4px; }
.sig-name  { font-weight: 700; font-size: 10px; color: #1e293b; }
.sig-line  { border-top: 1px solid #94a3b8; padding-top: 3px; font-size: 8.5px; color: #64748b; letter-spacing: .5px; }
.dec-cell  { padding: 8px 12px; background: #f8fafc; }
.dec-hd    { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #0f172a; margin-bottom: 2px; }
.dec-body  { font-size: 9px; color: #64748b; line-height: 1.6; }
</style>
</head>
<body>
<table class="outer" cellspacing="0" cellpadding="0">

  <tr><td colspan="2" class="hdr-bar"></td></tr>

  {{-- Company + Title --}}
  <tr>
    <td class="co-cell">
      <table cellspacing="0" cellpadding="0" style="width:100%"><tr>
        @if(!empty($company->logo))
        @php
          $logoAbsPath = storage_path('app/public/' . $company->logo);
          $logoDataUri = '';
          if (file_exists($logoAbsPath)) {
              $logoMime    = mime_content_type($logoAbsPath);
              $logoDataUri = 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoAbsPath));
          }
        @endphp
        @if($logoDataUri)
        <td style="width:80px;vertical-align:top;padding-right:10px">
          <img src="{{ $logoDataUri }}" style="width:76px;height:60px;object-fit:contain;" alt="">
        </td>
        @endif
        @endif
        <td style="vertical-align:top">
          <div class="co-name">{{ strtoupper($company->company_name ?? '') }}</div>
          <div class="co-sub">
            @if($coAddr){{ $coAddr }},<br>@endif
            @if($coCity){{ $coCity }}<br>@endif
            @if(!empty($company->phone))
              Phone: {{ $company->phone }}{{ !empty($company->phone2) ? ','.$company->phone2 : '' }}<br>
            @endif
            @if(!empty($company->gst))GSTIN: {{ $company->gst }}@endif
          </div>
          @if(!empty($company->pan) || !empty($company->email))
          <div class="co-extra">
            @if(!empty($company->pan))PAN: {{ $company->pan }}@endif
            @if(!empty($company->pan) && !empty($company->email)) &nbsp;|&nbsp; @endif
            @if(!empty($company->email))Email: {{ $company->email }}@endif
          </div>
          @endif
        </td>
      </tr></table>
    </td>
    <td class="title-cell">
      <div class="title-main">{{ $titleText }}</div>
      @if($titleSub)<div class="title-sub">{{ $titleSub }}</div>@endif
    </td>
  </tr>

  {{-- Bill No / Date / Place --}}
  <tr>
    <td colspan="2" style="padding:0;border-bottom:1px solid #dce1ea;">
      <table cellspacing="0" cellpadding="0" style="width:100%;table-layout:fixed"><tr>
        <td class="meta-cell" style="border-right:1px solid #dce1ea;width:50%">
          <span style="color:#64748b">Bill No : </span><strong>{{ $invNo }}</strong>
          &nbsp;&nbsp;
          <span style="color:#64748b">Invoice Date : </span><strong>{{ $invDate }}</strong>
        </td>
        <td class="meta-cell" style="width:50%">
          <span style="color:#64748b">Place Of Supply : </span><strong>{{ $plcSup }}</strong>
        </td>
      </tr></table>
    </td>
  </tr>

  {{-- Billing header --}}
  <tr><td colspan="2"><div class="billing-hd">BILLING</div></td></tr>

  {{-- Bill-to --}}
  <tr>
    <td colspan="2" style="padding:8px 12px;border-bottom:1px solid #dce1ea;">
      <div class="bt-name">{{ strtoupper($party->company_name ?: ($party->name ?? '—')) }}</div>
      <div class="bt-addr">
        @if($ptAddr){{ $ptAddr }}<br>@endif
        @if(!empty($party->gst_no))GSTIN: {{ $party->gst_no }}{{ (!empty($party->pan_no) || !empty($party->phone)) ? ' &nbsp;|&nbsp; ' : '' }}@endif
        @if(!empty($party->pan_no))PAN: {{ $party->pan_no }}{{ !empty($party->phone) ? ' &nbsp;|&nbsp; ' : '' }}@endif
        @if(!empty($party->phone))Phone: {{ $party->phone }}@endif
      </div>
    </td>
  </tr>

  {{-- Note --}}
  @if($invType === 'rcm')
  <tr><td colspan="2"><div class="note-box note-rcm">Tax payable under Reverse Charge Mechanism — liability on recipient of service.</div></td></tr>
  @elseif($invType === 'exempt')
  <tr><td colspan="2"><div class="note-box note-exempt">GST Exempt Supply — No tax chargeable on this invoice.</div></td></tr>
  @endif

  {{-- Items --}}
  <tr>
    <td colspan="2" style="padding:0;border-bottom:1px solid #dce1ea;">
      <table cellspacing="0" cellpadding="0" style="width:100%;table-layout:fixed">
        <colgroup>
          <col style="width:32px"><col><col style="width:68px"><col style="width:70px"><col style="width:100px"><col style="width:105px">
        </colgroup>
        <thead class="items-hdr">
          <tr>
            <th>#</th><th class="L">Description</th><th>HSN/SAC</th>
            <th>Total Box</th>
            <th style="text-align:right;padding-right:8px">Freight</th>
            <th style="text-align:right;padding-right:8px">Amount</th>
          </tr>
        </thead>
        <tbody>
          @foreach($trips as $i => $t)
          @php
            $veh  = optional($t->vehicle)->vehicle_number ?? '';
            $from = strtoupper($t->from_location ?? '');
            $to   = strtoupper($t->to_location   ?? '');
            $ds   = $t->trip_date ? $t->trip_date->format('d/m/Y') : '';
            $desc = implode(' ', array_filter([
                $ds, $veh,
                ($from && $to) ? $from.' TO '.$to : null,
                !empty($t->lr_no)    ? 'DC NO:-'.$t->lr_no : null,
                !empty($t->material) ? $t->material          : null,
                !empty($t->quantity) ? $t->quantity.'BOXS'   : null,
            ]));
            $isLast = ($i + 1 === $tc && $padN === 0);
          @endphp
          <tr>
            <td class="item-td C {{ $isLast ? 'item-last' : '' }}" style="font-weight:700">{{ $i + 1 }}</td>
            <td class="item-td {{ $isLast ? 'item-last' : '' }}">{{ $desc }}</td>
            <td class="item-td C {{ $isLast ? 'item-last' : '' }}" style="color:#555">996511</td>
            <td class="item-td C {{ $isLast ? 'item-last' : '' }}" style="color:#555">1.00</td>
            <td class="item-td R {{ $isLast ? 'item-last' : '' }}" style="padding-right:8px">{{ number_format($t->freight_amount, 2) }}</td>
            <td class="item-td item-td-last R {{ $isLast ? 'item-last' : '' }}" style="padding-right:8px;font-weight:700">{{ number_format($t->freight_amount, 2) }}</td>
          </tr>
          @endforeach
          @for($p = 0; $p < $padN; $p++)
          <tr>
            @php $pLast = ($p === $padN - 1); @endphp
            <td class="{{ $pLast ? 'pad-last' : 'pad-row' }}"></td>
            <td class="{{ $pLast ? 'pad-last' : 'pad-row' }}"></td>
            <td class="{{ $pLast ? 'pad-last' : 'pad-row' }}"></td>
            <td class="{{ $pLast ? 'pad-last' : 'pad-row' }}"></td>
            <td class="{{ $pLast ? 'pad-last' : 'pad-row' }}"></td>
            <td class="{{ $pLast ? 'pad-last' : 'pad-row' }}"></td>
          </tr>
          @endfor
        </tbody>
      </table>
    </td>
  </tr>

  {{-- Totals --}}
  <tr>
    <td colspan="2" style="padding:0;border-bottom:1px solid #dce1ea;">
      <table cellspacing="0" cellpadding="0" style="width:100%;table-layout:fixed"><tr>
        <td class="totals-lhs">
          <div class="amt-words-lbl">Total In Words</div>
          <div class="amt-words-val">{{ $amtW }}</div>
        </td>
        <td class="totals-rhs">
          <table cellspacing="0" cellpadding="0" style="width:100%">
            <tr><td class="tots-td tots-lbl">Sub Total</td><td class="tots-td tots-val">&#8377;{{ number_format($sub, 2) }}</td></tr>
            @if($invType === 'rcm' || $invType === 'exempt')
            <tr><td class="tots-td tots-lbl">CGST 0%</td><td class="tots-td tots-val">0.00</td></tr>
            <tr><td class="tots-td tots-lbl">SGST 0%</td><td class="tots-td tots-val">0.00</td></tr>
            @else
            <tr><td class="tots-td tots-lbl">CGST {{ $cgR }}%</td><td class="tots-td tots-val">{{ number_format($cgst, 2) }}</td></tr>
            <tr><td class="tots-td tots-lbl">SGST {{ $sgR }}%</td><td class="tots-td tots-val">{{ number_format($sgst, 2) }}</td></tr>
            @endif
            <tr><td class="tots-grand tots-lbl">Total</td><td class="tots-grand tots-val">&#8377;{{ number_format($grand, 2) }}</td></tr>
            @if($totalCollected > 0)
            <tr><td class="tots-td" style="color:#38a169;font-weight:700">Collected</td><td class="tots-td tots-val" style="color:#38a169">&#8377;{{ number_format($totalCollected, 2) }}</td></tr>
            <tr><td class="tots-td" style="color:#c53030;font-weight:700">Balance Due</td><td class="tots-td tots-val" style="color:#c53030">&#8377;{{ number_format($balanceDue, 2) }}</td></tr>
            @endif
          </table>
        </td>
      </tr></table>
    </td>
  </tr>

  {{-- Bank + Signature --}}
  <tr>
    <td colspan="2" style="padding:0;border-bottom:1px solid #dce1ea;">
      <table cellspacing="0" cellpadding="0" style="width:100%;table-layout:fixed"><tr>
        <td class="bank-cell">
          <div class="bank-hd">Bank Details</div>
          @if(!empty($company->bank_name) || !empty($company->account_number) || !empty($company->upi_id))
          <div class="bank-co">{{ $company->company_name ?? '' }},</div>
          @if(!empty($company->account_holder_name))<div class="bank-row">A/C HOLDER:- {{ strtoupper($company->account_holder_name) }},</div>@endif
          @if(!empty($company->account_number))<div class="bank-row">A/C No:- {{ $company->account_number }},</div>@endif
          @if(!empty($company->ifsc_code))<div class="bank-row">IFSC CODE:- {{ $company->ifsc_code }},</div>@endif
          @if(!empty($company->bank_name))<div class="bank-row">BANK:- {{ strtoupper($company->bank_name) }},</div>@endif
          @if(!empty($company->branch_name))<div class="bank-row">BRANCH:- {{ strtoupper($company->branch_name) }}</div>@endif
          @if(!empty($company->upi_id))<div class="bank-upi">UPI ID:- {{ $company->upi_id }}</div>@endif
          @endif
          <div class="bank-thanks">Thanks for your business.</div>
        </td>
        <td class="sig-cell">
          <div style="height:44px"></div>
          <div style="font-size:9.5px;color:#64748b;margin-bottom:3px">for</div>
          <div class="sig-name">{{ $company->company_name ?? '' }}</div>
          <div style="height:8px"></div>
          <div class="sig-line">Authorised Signatory</div>
        </td>
      </tr></table>
    </td>
  </tr>

  {{-- Declaration --}}
  <tr>
    <td colspan="2" class="dec-cell">
      <div class="dec-hd">Declaration</div>
      <div class="dec-body">We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.
        @if($invType === 'rcm') Tax is payable on reverse charge basis by the recipient of services. @endif
      </div>
    </td>
  </tr>

</table>
</body>
</html>
