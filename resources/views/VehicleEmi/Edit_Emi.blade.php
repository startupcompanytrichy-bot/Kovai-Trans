@extends('layouts.app')

@section('content')
<style>
.emi-edit-page{background:#f4f6fb;}
/* header */
.emi-edit-hdr{background:linear-gradient(135deg,#1a2340 0%,#3730a3 60%,#4338ca 100%);border-radius:14px;padding:18px 24px;color:#fff;margin-bottom:18px;position:relative;overflow:hidden;}
.emi-edit-hdr::before{content:'';position:absolute;top:-40px;right:-40px;width:150px;height:150px;background:rgba(255,255,255,.06);border-radius:50%;}
.emi-edit-hdr h4{font-size:18px;font-weight:800;margin:0 0 3px;}
.emi-edit-hdr .sub{font-size:12px;opacity:.8;}
/* cards */
.ef-card{background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:18px;overflow:hidden;}
.ef-card-header{display:flex;align-items:center;gap:10px;padding:12px 18px;border-bottom:1px solid #f0f2f7;background:#fafbff;}
.ef-ch-icon{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;}
.ef-card-header h6{margin:0;font-size:13px;font-weight:700;color:#1a2340;}
.ef-card-body{padding:18px 20px;}
/* labels & inputs */
.ef-label{font-size:11px;font-weight:700;color:#596579;margin-bottom:5px;display:block;text-transform:uppercase;letter-spacing:.3px;}
.ef-label .req{color:#e53e3e;}
.ef-input{min-height:42px;border-color:#d7dce5;color:#303549;font-size:13px;border-radius:8px;}
.ef-input:focus{border-color:#4338ca;box-shadow:0 0 0 2px rgba(67,56,202,.1);}
/* prefix/suffix */
.ef-prefix{min-height:42px;background:#f1f3f8;border:1px solid #d7dce5;border-right:none;border-radius:8px 0 0 8px;padding:0 10px;font-size:13px;font-weight:700;color:#596579;display:flex;align-items:center;}
.ef-suffix{min-height:42px;background:#f1f3f8;border:1px solid #d7dce5;border-left:none;border-radius:0 8px 8px 0;padding:0 10px;font-size:13px;font-weight:700;color:#596579;display:flex;align-items:center;}
.ef-input.pfx{border-radius:0 8px 8px 0!important;}
.ef-input.sfx{border-radius:8px 0 0 8px!important;}
/* ro fields */
.ro-field{min-height:42px;background:#f8f9fb;border:1px solid #e8ecf2;border-radius:8px;padding:9px 12px;font-size:13px;font-weight:600;color:#1a2340;display:flex;align-items:center;}
.ro-field.pfx{border-radius:0 8px 8px 0;}
.ro-prefix{min-height:42px;background:#f1f3f8;border:1px solid #e8ecf2;border-right:none;border-radius:8px 0 0 8px;padding:0 10px;font-size:13px;font-weight:700;color:#596579;display:flex;align-items:center;}
/* section divider */
.ef-sdiv{font-size:10px;font-weight:800;color:#8a94a6;text-transform:uppercase;letter-spacing:.8px;padding:4px 0 8px;border-bottom:1px solid #f0f2f7;margin-bottom:12px;}
/* buttons */
.btn-pay{background:linear-gradient(135deg,#38a169,#2f855a);color:#fff;border:none;border-radius:8px;padding:8px 20px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 3px 10px rgba(56,161,105,.3);transition:all .15s;}
.btn-pay:hover{transform:translateY(-1px);box-shadow:0 5px 14px rgba(56,161,105,.45);}
.btn-save{background:linear-gradient(135deg,#4338ca,#3730a3);color:#fff;border:none;border-radius:8px;padding:8px 20px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 3px 10px rgba(67,56,202,.3);transition:all .15s;}
.btn-save:hover{transform:translateY(-1px);box-shadow:0 5px 14px rgba(67,56,202,.45);}
/* tabs */
.ef-tabs{display:flex;gap:6px;padding:12px 18px;border-bottom:1px solid #f0f2f7;background:#fafbff;flex-wrap:wrap;}
.ef-tab{padding:6px 14px;border-radius:8px;border:1.5px solid #d7dce5;background:#fff;font-size:12px;font-weight:700;cursor:pointer;color:#596579;transition:all .15s;}
.ef-tab.active{border-color:#4338ca;background:#eef2ff;color:#4338ca;}
/* summary card */
.sum-card{background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.07);margin-bottom:18px;overflow:hidden;}
.sum-hdr{background:linear-gradient(135deg,#4338ca 0%,#6d28d9 100%);padding:16px 18px 44px;position:relative;overflow:hidden;color:#fff;}
.sum-hdr::before{content:'';position:absolute;top:-25px;right:-25px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.07);}
.sum-hdr::after{content:'';position:absolute;bottom:-40px;left:-15px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,.05);}
.ring-wrap{position:relative;z-index:2;display:flex;align-items:center;}
.ring-svg{transform:rotate(-90deg);}
.ring-track{fill:none;stroke:rgba(255,255,255,.18);stroke-width:6;}
.ring-fill{fill:none;stroke:#fff;stroke-width:6;stroke-linecap:round;transition:stroke-dashoffset .8s ease;}
.ring-label{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;}
.ring-pct{font-size:15px;font-weight:900;color:#fff;line-height:1;}
.ring-sub{font-size:9px;color:rgba(255,255,255,.7);font-weight:700;text-transform:uppercase;letter-spacing:.4px;}
.sum-tiles{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin:-26px 12px 0;position:relative;z-index:3;}
.sum-tile{background:#fff;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,.1);padding:10px 12px;}
.sum-tile-icon{width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:13px;margin-bottom:3px;}
.sum-tile-lbl{font-size:10px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.4px;}
.sum-tile-val{font-size:14px;font-weight:900;line-height:1.1;}
.sum-stat-rows{padding:12px 16px 6px;}
.sum-stat-row{display:flex;align-items:center;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f4f6fb;}
.sum-stat-row:last-child{border-bottom:none;}
.sum-stat-left{display:flex;align-items:center;gap:7px;font-size:12px;font-weight:600;color:#596579;}
.sum-stat-left .sri{width:24px;height:24px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0;}
.sum-stat-val{font-size:12px;font-weight:800;color:#1a2340;}
.due-alert{margin:0 12px 12px;border-radius:10px;padding:9px 12px;display:flex;align-items:center;gap:9px;}
.due-alert.over{background:#fff5f5;border:1px solid #fed7d7;}
.due-alert.ok{background:#f0fff4;border:1px solid #b2f5cc;}
.due-icon{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;}
.lin-wrap{padding:0 12px 14px;}
.lin-hdr{display:flex;justify-content:space-between;font-size:11px;font-weight:700;color:#596579;margin-bottom:4px;}
.lin-bar{height:8px;background:#e8edf5;border-radius:4px;overflow:hidden;}
.lin-fill{height:100%;border-radius:4px;background:linear-gradient(90deg,#667eea,#764ba2);transition:width .8s ease;}
/* schedule table */
.sch-wrap{overflow-x:auto;max-height:460px;overflow-y:auto;}
.sch-wrap::-webkit-scrollbar{width:4px;height:4px;}
.sch-wrap::-webkit-scrollbar-thumb{background:#c5cde0;border-radius:4px;}
#schTable{min-width:700px;margin:0;font-size:12px;}
#schTable thead th{background:#f8fafc;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.4px;color:#14213d;border-color:#f0f2f7;padding:9px 10px;position:sticky;top:0;z-index:2;}
#schTable tbody td{padding:8px 10px;border-color:#f0f2f7;vertical-align:middle;}
#schTable tbody tr:hover td{background:#f4f7ff;}
.sch-no{width:26px;height:26px;border-radius:50%;background:#eef2ff;color:#4338ca;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;}
.sch-badge{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700;}
.sch-paid{background:#f0fff4;color:#276749;border:1px solid #b2f5cc;}
.sch-over{background:#fff5f5;color:#c53030;border:1px solid #fed7d7;}
.sch-curr{background:#fffbeb;color:#b45309;border:1px solid #fde68a;}
.sch-soon{background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe;}
.sch-pend{background:#f4f6fb;color:#8a94a6;border:1px solid #e2e8f0;}
.row-paid td{background:#f9fffb!important;}
.row-over td{background:#fff9f9!important;}
.row-curr td{background:#fffef5!important;}
/* payment history */
.pay-tl-wrap{max-height:320px;overflow-y:auto;padding-right:4px;}
.pay-tl-wrap::-webkit-scrollbar{width:4px;}
.pay-tl-wrap::-webkit-scrollbar-thumb{background:#c5cde0;border-radius:4px;}
.pay-tl{position:relative;padding:4px 0;}
.pay-tl::before{content:'';position:absolute;left:15px;top:0;bottom:0;width:2px;background:linear-gradient(to bottom,#38a169,#e2e8f0);z-index:0;}
.pay-tl-item{display:flex;align-items:flex-start;gap:12px;position:relative;z-index:1;padding:7px 0;}
.pay-tl-dot{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#38a169,#2f855a);color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.1);}
.pay-tl-amt{font-size:14px;font-weight:800;color:#38a169;}
.pay-tl-due{font-size:10px;font-weight:700;color:#4338ca;background:#eef2ff;border-radius:4px;padding:1px 6px;display:inline-block;margin-bottom:2px;}
.pay-tl-dt{font-size:11px;color:#8a94a6;}
.pay-mode{display:inline-block;padding:1px 7px;border-radius:8px;font-size:10px;font-weight:700;background:#f0fff4;color:#38a169;text-transform:uppercase;}
.load-more-btn{width:100%;background:none;border:1.5px dashed #c5cde0;border-radius:8px;padding:7px;font-size:12px;font-weight:700;color:#596579;cursor:pointer;margin-top:8px;transition:all .15s;}
.load-more-btn:hover{background:#f4f6fb;border-color:#4338ca;color:#4338ca;}
.hidden-pays{display:none;}
/* contract statement header box */
.stmt-box{background:#f8faff;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;margin-bottom:14px;}
.stmt-row{display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px dashed #edf0f7;font-size:12px;}
.stmt-row:last-child{border-bottom:none;}
.stmt-key{color:#596579;font-weight:600;}
.stmt-val{font-weight:800;color:#1a2340;text-align:right;}
</style>

<div class="pcoded-inner-content emi-edit-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

@php
    $pct            = $emi->total_emis > 0 ? round(($emi->paid_emis / $emi->total_emis) * 100) : 0;
    $sortedPays     = $emi->payments->sortByDesc('payment_date')->values();
    $initCount      = 5;
    $totalPaidAmt   = $emi->payments->sum('amount_paid');
    $circumference  = 2 * M_PI * 36;
    $dashOffset     = $circumference - ($pct / 100) * $circumference;
    $isOverdue      = $emi->status === 'overdue' || ($emi->next_due_date && $emi->next_due_date->isPast() && $emi->status === 'active');

    // Build schedule — use first_instalment_date if set, else start+1 month
    // Key payments by their due_month (Y-m) so we can match each row exactly
    $payByMonth = $emi->payments->keyBy(fn($p) => optional($p->due_month)->format('Y-m'));

    $scheduleRows = [];
    if ($emi->loan_start_date && ($emi->total_emis || $emi->loan_end_date)) {

        // Anchor: the date of instalment #1
        if ($emi->first_instalment_date) {
            $firstDue = $emi->first_instalment_date->copy();
        } elseif ($emi->next_due_date && $emi->paid_emis > 0) {
            // Rewind next_due_date back by paid count to find instalment #1
            $firstDue = $emi->next_due_date->copy()->subMonths($emi->paid_emis);
        } else {
            // Default: start date + 1 month
            $firstDue = $emi->loan_start_date->copy()->addMonth();
        }

        $total   = $emi->total_emis ?? (int) $emi->loan_start_date->diffInMonths($emi->loan_end_date);
        $balance = (float) $emi->loan_amount;
        $today   = \Carbon\Carbon::today();

        for ($i = 1; $i <= $total; $i++) {
            $due     = $firstDue->copy()->addMonths($i - 1);
            $mkey    = $due->format('Y-m');
            $payment = $payByMonth->get($mkey);
            $balance = max(0, $balance - (float) $emi->emi_amount);

            // Status logic:
            // paid      → payment record exists for that month
            // over      → due date is in a past month AND no payment
            // curr      → due month === today's month AND no payment
            // soon      → due within next 30 days (future month) AND no payment
            // pend      → future, beyond 30 days

            if ($payment) {
                $st = 'paid';
            } elseif ($due->format('Y-m') < $today->format('Y-m')) {
                $st = 'over';   // past month, unpaid
            } elseif ($due->format('Y-m') === $today->format('Y-m')) {
                $st = 'curr';   // current month
            } elseif ($due->diffInDays($today) <= 30) {
                $st = 'soon';
            } else {
                $st = 'pend';
            }

            $scheduleRows[] = compact('i', 'due', 'mkey', 'payment', 'balance', 'st');
        }
    }
    $overdueCount = count(array_filter($scheduleRows, fn($r) => $r['st'] === 'over'));
@endphp

{{-- Header --}}
<div class="emi-edit-hdr">
    <div class="row align-items-center">
        <div class="col-md-9" style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;margin-bottom:7px;">
                <i class="ti-car"></i> EMI Record
            </div>
            <h4>{{ optional($emi->vehicle)->vehicle_number }} — {{ $emi->financier_name }}</h4>
            <div class="sub">
                @if($emi->contract_no) Contract: {{ $emi->contract_no }} &bull; @endif
                EMI: ₹{{ number_format($emi->emi_amount,0) }}/mo &bull;
                {{ $emi->paid_emis }}/{{ $emi->total_emis ?? '?' }} paid &bull;
                Outstanding: ₹{{ number_format($emi->outstanding_balance,0) }}
            </div>
        </div>
        <div class="col-md-3 text-right" style="position:relative;z-index:1;">
            <a href="{{ route('emi') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;">
                <i class="ti-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

<div class="row">
{{-- ══ LEFT ══ --}}
<div class="col-lg-8">

    {{-- ── RECORD PAYMENT ── --}}
    <div class="ef-card">
        <div class="ef-card-header">
            <div class="ef-ch-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-credit-card"></i></div>
            <h6>Record EMI Payment</h6>
        </div>
        <div class="ef-card-body">
            <div style="background:#f0fff4;border-left:3px solid #38a169;padding:9px 12px;border-radius:6px;margin-bottom:14px;font-size:12px;color:#22543d;display:flex;gap:8px;">
                <i class="ti-alarm-clock" style="flex-shrink:0;margin-top:1px;"></i>
                <div><strong>Next Due:</strong> {{ $emi->next_due_date ? $emi->next_due_date->format('d M Y') : '—' }}
                &nbsp;|&nbsp; After recording, next due date auto-advances 1 month.</div>
            </div>
            <form id="payForm" action="{{ route('emi.pay', $emi->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Payment Date <span class="req">*</span></label>
                        <input type="date" name="payment_date" class="form-control ef-input" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Amount Paid <span class="req">*</span></label>
                        <div class="d-flex"><div class="ef-prefix">₹</div>
                        <input type="number" step="0.01" min="0" name="amount_paid" class="form-control ef-input pfx" value="{{ $emi->emi_amount }}" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Penalty</label>
                        <div class="d-flex"><div class="ef-prefix">₹</div>
                        <input type="number" step="0.01" min="0" name="penalty" class="form-control ef-input pfx" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Others (Bank Charges)</label>
                        <div class="d-flex"><div class="ef-prefix">₹</div>
                        <input type="number" step="0.01" min="0" name="others_amount" class="form-control ef-input pfx" value="0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Payment Mode</label>
                        <select name="payment_mode" class="form-control ef-input">
                            <option value="">Select</option>
                            <option value="cash">Cash</option>
                            <option value="upi">UPI</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Reference No.</label>
                        <input type="text" name="reference_no" class="form-control ef-input" placeholder="Ref / Cheque No.">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Particulars</label>
                        <input type="text" name="particulars" class="form-control ef-input" placeholder="e.g. Loan Instalment">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Receipt Image</label>
                        <input type="file" name="receipt_image" class="form-control ef-input" accept=".jpg,.jpeg,.png,.pdf" style="padding:8px;">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="ef-label">Notes</label>
                <input type="text" name="notes" class="form-control ef-input" placeholder="Payment notes...">
            </div>
            <div class="text-right">
                <button type="submit" class="btn-pay" id="payBtn"><i class="ti-check mr-1"></i> Record Payment</button>
            </div>
            </form>
        </div>
    </div>

    {{-- ── EMI SCHEDULE TABLE ── --}}
    <div class="ef-card">
        <div class="ef-card-header">
            <div class="ef-ch-icon" style="background:#eef2ff;color:#4338ca;"><i class="ti-list-ol"></i></div>
            <div style="flex:1;display:flex;align-items:center;justify-content:space-between;">
                <h6>Instalment Schedule</h6>
                <span style="font-size:11px;font-weight:600;color:#8a94a6;background:#f4f6fb;padding:2px 8px;border-radius:999px;">
                    {{ count($scheduleRows) }} instalments
                </span>
            </div>
        </div>
        <div class="ef-card-body" style="padding:0;">
            {{-- Legend --}}
            <div style="display:flex;flex-wrap:wrap;gap:6px;padding:10px 14px;border-bottom:1px solid #f0f2f7;">
                <span class="sch-badge sch-paid"><i class="ti-check"></i> Paid</span>
                <span class="sch-badge sch-curr"><i class="ti-alarm-clock"></i> Current Month</span>
                <span class="sch-badge sch-soon"><i class="ti-time"></i> Due Soon</span>
                <span class="sch-badge sch-over"><i class="ti-alert"></i> Overdue</span>
                <span class="sch-badge sch-pend"><i class="ti-calendar"></i> Pending</span>
            </div>
            @if(count($scheduleRows))
            <div class="sch-wrap">
                <table class="table table-bordered" id="schTable">
                    <thead>
                        <tr>
                            <th style="width:38px;text-align:center;">#</th>
                            <th style="width:110px;">Due Date</th>
                            <th style="width:72px;">Month</th>
                            <th style="text-align:right;width:110px;">Instalment (₹)</th>
                            <th style="text-align:right;width:110px;">Balance (₹)</th>
                            <th style="width:90px;text-align:center;">Status</th>
                            <th>Payment Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scheduleRows as $row)
                        @php
                            $rc = match($row['st']){ 'paid'=>'row-paid','over'=>'row-over','curr'=>'row-curr', default=>'' };
                            $bc = match($row['st']){ 'paid'=>'sch-paid','over'=>'sch-over','curr'=>'sch-curr','soon'=>'sch-soon', default=>'sch-pend' };
                            $bl = match($row['st']){ 'paid'=>'<i class="ti-check"></i> Paid','over'=>'<i class="ti-alert"></i> Overdue','curr'=>'<i class="ti-alarm-clock"></i> Current','soon'=>'<i class="ti-time"></i> Due Soon', default=>'<i class="ti-calendar"></i> Pending' };
                        @endphp
                        <tr class="{{ $rc }}">
                            <td style="text-align:center;"><span class="sch-no">{{ $row['i'] }}</span></td>
                            <td style="font-weight:700;color:#1a2340;">{{ $row['due']->format('d M Y') }}</td>
                            <td style="color:#596579;font-size:11px;">{{ $row['due']->format('M Y') }}</td>
                            <td style="text-align:right;font-weight:700;color:#4338ca;">{{ number_format($emi->emi_amount,2) }}</td>
                            <td style="text-align:right;font-weight:700;color:{{ $row['balance']<=0 ? '#38a169' : '#e53e3e' }};">
                                {{ number_format($row['balance'],2) }}
                            </td>
                            <td style="text-align:center;"><span class="sch-badge {{ $bc }}">{!! $bl !!}</span></td>
                            <td>
                                @if($row['payment'])
                                    @php $p = $row['payment']; @endphp
                                    <div style="font-size:12px;color:#38a169;font-weight:700;">
                                        ₹{{ number_format($p->amount_paid,2) }}
                                        @if($p->penalty>0) <span style="color:#e53e3e;"> +₹{{ number_format($p->penalty,2) }} penalty</span>@endif
                                        @if($p->others_amount>0) <span style="color:#d97706;"> +₹{{ number_format($p->others_amount,2) }} others</span>@endif
                                    </div>
                                    <div style="font-size:11px;color:#8a94a6;">
                                        {{ $p->payment_date->format('d M Y') }}
                                        @if($p->payment_mode) &bull; {{ ucfirst($p->payment_mode) }}@endif
                                        @if($p->reference_no) &bull; {{ $p->reference_no }}@endif
                                    </div>
                                    @if($p->particulars)<div style="font-size:10px;color:#b0bac9;">{{ $p->particulars }}</div>@endif
                                @elseif($row['st']==='over')
                                    <span style="font-size:11px;color:#e53e3e;font-weight:600;"><i class="ti-alert mr-1"></i>Overdue by {{ $row['due']->diffInDays(\Carbon\Carbon::today()) }} days</span>
                                @elseif($row['st']==='curr')
                                    <span style="font-size:11px;color:#b45309;font-weight:600;"><i class="ti-alarm-clock mr-1"></i>Due this month</span>
                                @elseif($row['st']==='soon')
                                    <span style="font-size:11px;color:#4338ca;">Due in {{ \Carbon\Carbon::today()->diffInDays($row['due']) }} days</span>
                                @else
                                    <span style="font-size:11px;color:#b0bac9;">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f8fafc;">
                            <td colspan="3" style="text-align:right;font-size:11px;font-weight:800;color:#596579;padding:8px 10px;">TOTAL</td>
                            <td style="text-align:right;font-weight:800;color:#4338ca;padding:8px 10px;">
                                {{ number_format($emi->emi_amount * count($scheduleRows), 2) }}
                            </td>
                            <td colspan="3" style="padding:8px 10px;">
                                <span style="font-size:11px;color:#8a94a6;">
                                    {{ $emi->payments->count() }} paid &bull;
                                    {{ $overdueCount }} overdue &bull;
                                    {{ count($scheduleRows) - $emi->payments->count() - $overdueCount }} remaining
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div style="text-align:center;padding:30px;color:#b0bac9;">
                <i class="ti-calendar" style="font-size:28px;display:block;margin-bottom:8px;"></i>
                <div style="font-size:13px;font-weight:600;">Schedule not available</div>
                <div style="font-size:12px;margin-top:4px;">Update loan start date, EMI amount &amp; total instalments below to generate.</div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── EDIT EMI DETAILS ── --}}
    <div class="ef-card">
        <div class="ef-card-header">
            <div class="ef-ch-icon" style="background:#eef2ff;color:#4338ca;"><i class="ti-pencil"></i></div>
            <h6>Edit EMI / Loan Details</h6>
        </div>
        <div class="ef-card-body">
            <form id="editForm" action="{{ route('emi.update', $emi->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="ef-sdiv">Finance & Contract</div>
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="ef-label">Financier / Bank <span class="req">*</span></label>
                        <input type="text" name="financier_name" class="form-control ef-input" value="{{ old('financier_name',$emi->financier_name) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="ef-label">Contract No.</label>
                        <input type="text" name="contract_no" class="form-control ef-input" value="{{ old('contract_no',$emi->contract_no) }}" placeholder="e.g. U001600133">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Agreement Date</label>
                        <input type="date" name="agreement_date" class="form-control ef-input" value="{{ old('agreement_date',$emi->agreement_date?->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>

            <div class="ef-sdiv">Vehicle & Asset</div>
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="ef-label">Vehicle <span class="req">*</span></label>
                        <select name="vehicle_id" class="form-control ef-input" required>
                            @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ $emi->vehicle_id==$v->id?'selected':'' }}>
                                {{ $v->vehicle_number }}{{ $v->vehicle_name ? ' — '.$v->vehicle_name : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="ef-label">Asset Make</label>
                        <input type="text" name="asset_make" class="form-control ef-input" value="{{ old('asset_make',$emi->asset_make) }}" placeholder="e.g. EICHER">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Asset Type</label>
                        <input type="text" name="asset_type" class="form-control ef-input" value="{{ old('asset_type',$emi->asset_type) }}" placeholder="e.g. PRO 2110">
                    </div>
                </div>
            </div>

            <div class="ef-sdiv">Loan Financials</div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Loan Amount <span class="req">*</span></label>
                        <div class="d-flex"><div class="ef-prefix">₹</div>
                        <input type="number" step="0.01" min="0" name="loan_amount" class="form-control ef-input pfx" value="{{ old('loan_amount',$emi->loan_amount) }}" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Interest Amount</label>
                        <div class="d-flex"><div class="ef-prefix">₹</div>
                        <input type="number" step="0.01" min="0" name="interest_amount" class="form-control ef-input pfx" value="{{ old('interest_amount',$emi->interest_amount) }}">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Insurance</label>
                        <div class="d-flex"><div class="ef-prefix">₹</div>
                        <input type="number" step="0.01" min="0" name="insurance_amount" class="form-control ef-input pfx" value="{{ old('insurance_amount',$emi->insurance_amount) }}">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Total Payable</label>
                        <div class="d-flex"><div class="ef-prefix">₹</div>
                        <input type="number" step="0.01" min="0" name="total_payable" class="form-control ef-input pfx" value="{{ old('total_payable',$emi->total_payable) }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">EMI / Month <span class="req">*</span></label>
                        <div class="d-flex"><div class="ef-prefix">₹</div>
                        <input type="number" step="0.01" min="0" name="emi_amount" class="form-control ef-input pfx" value="{{ old('emi_amount',$emi->emi_amount) }}" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Interest Rate (%)</label>
                        <div class="d-flex">
                        <input type="number" step="0.01" min="0" max="100" name="interest_rate" class="form-control ef-input sfx" value="{{ old('interest_rate',$emi->interest_rate) }}">
                        <div class="ef-suffix">%</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Total EMIs</label>
                        <input type="number" min="1" name="total_emis" class="form-control ef-input" value="{{ old('total_emis',$emi->total_emis) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Status</label>
                        <select name="status" class="form-control ef-input">
                            <option value="active"  {{ old('status',$emi->status)==='active'  ?'selected':'' }}>Active</option>
                            <option value="closed"  {{ old('status',$emi->status)==='closed'  ?'selected':'' }}>Closed</option>
                            <option value="overdue" {{ old('status',$emi->status)==='overdue' ?'selected':'' }}>Overdue</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="ef-sdiv">Dates</div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Loan Start <span class="req">*</span></label>
                        <input type="date" name="loan_start_date" class="form-control ef-input" value="{{ old('loan_start_date',$emi->loan_start_date?->format('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">First Instalment</label>
                        <input type="date" name="first_instalment_date" class="form-control ef-input" value="{{ old('first_instalment_date',$emi->first_instalment_date?->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Last Instalment</label>
                        <input type="date" name="last_instalment_date" class="form-control ef-input" value="{{ old('last_instalment_date',$emi->last_instalment_date?->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Next Due Date</label>
                        <input type="date" name="next_due_date" class="form-control ef-input" value="{{ old('next_due_date',$emi->next_due_date?->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Loan End Date</label>
                        <input type="date" name="loan_end_date" class="form-control ef-input" value="{{ old('loan_end_date',$emi->loan_end_date?->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="ef-label">Outstanding Balance</label>
                        <div class="d-flex"><div class="ef-prefix">₹</div>
                        <input type="number" step="0.01" min="0" name="outstanding_balance" class="form-control ef-input pfx" value="{{ old('outstanding_balance',$emi->outstanding_balance) }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group mb-0">
                <label class="ef-label">Notes</label>
                <textarea name="notes" rows="2" class="form-control ef-input">{{ old('notes',$emi->notes) }}</textarea>
            </div>
            <div class="text-right mt-3">
                <button type="submit" class="btn-save" id="saveBtn"><i class="ti-save mr-1"></i> Update EMI Details</button>
            </div>
            </form>
        </div>
    </div>

</div>

{{-- ══ RIGHT ══ --}}
<div class="col-lg-4">

    {{-- Summary --}}
    <div class="sum-card">
        <div class="sum-hdr">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;position:relative;z-index:2;">
                <div>
                    <div style="font-size:10px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;opacity:.8;margin-bottom:3px;"><i class="ti-stats-up mr-1"></i> EMI Summary</div>
                    <div style="font-size:16px;font-weight:800;line-height:1.2;">{{ optional($emi->vehicle)->vehicle_number }}</div>
                    <div style="font-size:12px;opacity:.75;">{{ $emi->financier_name }}</div>
                    @if($emi->contract_no)<div style="font-size:11px;opacity:.65;margin-top:2px;">Contract: {{ $emi->contract_no }}</div>@endif
                </div>
                <div class="ring-wrap" style="width:72px;height:72px;flex-shrink:0;">
                    <svg class="ring-svg" width="72" height="72" viewBox="0 0 72 72">
                        <circle class="ring-track" cx="36" cy="36" r="30"/>
                        <circle class="ring-fill" cx="36" cy="36" r="30"
                            stroke-dasharray="{{ number_format(2*M_PI*30,2) }}"
                            stroke-dashoffset="{{ number_format(2*M_PI*30 - ($pct/100)*2*M_PI*30,2) }}"/>
                    </svg>
                    <div class="ring-label"><span class="ring-pct">{{ $pct }}%</span><span class="ring-sub">done</span></div>
                </div>
            </div>
        </div>

        <div class="sum-tiles">
            <div class="sum-tile">
                <div class="sum-tile-icon" style="background:#eef2ff;color:#4338ca;"><i class="ti-money"></i></div>
                <div class="sum-tile-lbl">Loan</div>
                <div class="sum-tile-val" style="color:#4338ca;">₹{{ number_format($emi->loan_amount,0) }}</div>
            </div>
            <div class="sum-tile">
                <div class="sum-tile-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-calendar"></i></div>
                <div class="sum-tile-lbl">Monthly</div>
                <div class="sum-tile-val" style="color:#38a169;">₹{{ number_format($emi->emi_amount,0) }}</div>
            </div>
            <div class="sum-tile">
                <div class="sum-tile-icon" style="background:#fff5f5;color:#e53e3e;"><i class="ti-wallet"></i></div>
                <div class="sum-tile-lbl">Outstanding</div>
                <div class="sum-tile-val" style="color:#e53e3e;">₹{{ number_format($emi->outstanding_balance,0) }}</div>
            </div>
            <div class="sum-tile">
                <div class="sum-tile-icon" style="background:#fffbeb;color:#d97706;"><i class="ti-check-box"></i></div>
                <div class="sum-tile-lbl">Paid Total</div>
                <div class="sum-tile-val" style="color:#d97706;">₹{{ number_format($totalPaidAmt,0) }}</div>
            </div>
        </div>

        <div class="sum-stat-rows">
            <div class="sum-stat-row">
                <div class="sum-stat-left"><div class="sri" style="background:#eef2ff;color:#4338ca;"><i class="ti-check"></i></div>Paid EMIs</div>
                <div class="sum-stat-val" style="color:#4338ca;">{{ $emi->paid_emis }} <span style="font-size:10px;color:#8a94a6;">/ {{ $emi->total_emis??'?' }}</span></div>
            </div>
            <div class="sum-stat-row">
                <div class="sum-stat-left"><div class="sri" style="background:#fff7ed;color:#ea580c;"><i class="ti-time"></i></div>Pending EMIs</div>
                <div class="sum-stat-val" style="color:#ea580c;">{{ $emi->pending_emis }}</div>
            </div>
            @if($overdueCount)
            <div class="sum-stat-row">
                <div class="sum-stat-left"><div class="sri" style="background:#fff5f5;color:#e53e3e;"><i class="ti-alert"></i></div>Overdue</div>
                <div class="sum-stat-val" style="color:#e53e3e;">{{ $overdueCount }}</div>
            </div>
            @endif
            @if($emi->total_payable)
            <div class="sum-stat-row">
                <div class="sum-stat-left"><div class="sri" style="background:#f0fff4;color:#38a169;"><i class="ti-receipt"></i></div>Total Payable</div>
                <div class="sum-stat-val">₹{{ number_format($emi->total_payable,0) }}</div>
            </div>
            @endif
            @if($emi->interest_amount)
            <div class="sum-stat-row">
                <div class="sum-stat-left"><div class="sri" style="background:#eef2ff;color:#4338ca;"><i class="ti-stats-up"></i></div>Interest</div>
                <div class="sum-stat-val">₹{{ number_format($emi->interest_amount,0) }}</div>
            </div>
            @endif
        </div>

        @if($emi->next_due_date)
        <div class="due-alert {{ $isOverdue ? 'over' : 'ok' }}">
            <div class="due-icon" style="background:{{ $isOverdue?'#fed7d7':'#c6f6d5' }};color:{{ $isOverdue?'#c53030':'#276749' }};"><i class="ti-alarm-clock"></i></div>
            <div>
                <div style="font-size:10px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.4px;">{{ $isOverdue ? '⚠ Overdue' : 'Next Due' }}</div>
                <div style="font-size:13px;font-weight:900;color:{{ $isOverdue?'#c53030':'#276749' }};">{{ $emi->next_due_date->format('d M Y') }}</div>
            </div>
            <div style="margin-left:auto;">
                <span style="background:{{ $isOverdue?'#fed7d7':'#c6f6d5' }};color:{{ $isOverdue?'#c53030':'#276749' }};border-radius:999px;padding:2px 9px;font-size:10px;font-weight:800;white-space:nowrap;">
                    {{ $isOverdue ? 'OVERDUE' : ($emi->next_due_date->diffInDays(now())<=7 && $emi->next_due_date->isFuture() ? 'DUE SOON' : 'ON TRACK') }}
                </span>
            </div>
        </div>
        @endif

        <div class="lin-wrap">
            <div class="lin-hdr"><span>Repayment Progress</span><span style="color:#4338ca;">{{ $emi->paid_emis }} / {{ $emi->total_emis??'?' }}</span></div>
            <div class="lin-bar"><div class="lin-fill" style="width:{{ $pct }}%;"></div></div>
        </div>
    </div>

    {{-- Contract Statement Info --}}
    @if($emi->contract_no || $emi->agreement_date || $emi->first_instalment_date || $emi->asset_make)
    <div class="ef-card">
        <div class="ef-card-header">
            <div class="ef-ch-icon" style="background:#fffbeb;color:#d97706;"><i class="ti-receipt"></i></div>
            <h6>Finance Statement</h6>
        </div>
        <div class="ef-card-body" style="padding:14px;">
            <div class="stmt-box">
                @if($emi->contract_no)
                <div class="stmt-row"><span class="stmt-key">Contract No.</span><span class="stmt-val">{{ $emi->contract_no }}</span></div>
                @endif
                @if($emi->agreement_date)
                <div class="stmt-row"><span class="stmt-key">Agreement Date</span><span class="stmt-val">{{ $emi->agreement_date->format('d/m/Y') }}</span></div>
                @endif
                @if($emi->first_instalment_date)
                <div class="stmt-row"><span class="stmt-key">First Instalment</span><span class="stmt-val">{{ $emi->first_instalment_date->format('d/m/Y') }}</span></div>
                @endif
                @if($emi->last_instalment_date)
                <div class="stmt-row"><span class="stmt-key">Last Instalment</span><span class="stmt-val">{{ $emi->last_instalment_date->format('d/m/Y') }}</span></div>
                @endif
                @if($emi->loan_amount)
                <div class="stmt-row"><span class="stmt-key">Original Loan</span><span class="stmt-val">₹{{ number_format($emi->loan_amount,2) }}</span></div>
                @endif
                @if($emi->interest_amount)
                <div class="stmt-row"><span class="stmt-key">Interest Amount</span><span class="stmt-val">₹{{ number_format($emi->interest_amount,2) }}</span></div>
                @endif
                @if($emi->insurance_amount)
                <div class="stmt-row"><span class="stmt-key">Insurance</span><span class="stmt-val">₹{{ number_format($emi->insurance_amount,2) }}</span></div>
                @endif
                @if($emi->total_payable)
                <div class="stmt-row"><span class="stmt-key">Total Payable</span><span class="stmt-val" style="color:#4338ca;">₹{{ number_format($emi->total_payable,2) }}</span></div>
                @endif
                @if($emi->total_emis)
                <div class="stmt-row"><span class="stmt-key">Original Tenure</span><span class="stmt-val">{{ $emi->total_emis }} months</span></div>
                @endif
                @if($emi->asset_make)
                <div class="stmt-row"><span class="stmt-key">Asset Make</span><span class="stmt-val">{{ $emi->asset_make }}</span></div>
                @endif
                @if($emi->asset_type)
                <div class="stmt-row"><span class="stmt-key">Asset Type</span><span class="stmt-val">{{ $emi->asset_type }}</span></div>
                @endif
                @if(optional($emi->vehicle)->vehicle_number)
                <div class="stmt-row"><span class="stmt-key">Reg. Number</span><span class="stmt-val">{{ $emi->vehicle->vehicle_number }}</span></div>
                @endif
                @if(optional($emi->vehicle)->engine_number)
                <div class="stmt-row"><span class="stmt-key">Engine No.</span><span class="stmt-val">{{ $emi->vehicle->engine_number }}</span></div>
                @endif
                @if(optional($emi->vehicle)->chassis_number)
                <div class="stmt-row"><span class="stmt-key">Chassis No.</span><span class="stmt-val">{{ $emi->vehicle->chassis_number }}</span></div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Payment History --}}
    <div class="ef-card">
        <div class="ef-card-header">
            <div class="ef-ch-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-time"></i></div>
            <div style="flex:1;display:flex;align-items:center;justify-content:space-between;">
                <h6>Payment History</h6>
                @if($sortedPays->count())
                <span style="font-size:11px;font-weight:600;color:#8a94a6;background:#f4f6fb;padding:2px 8px;border-radius:999px;">{{ $sortedPays->count() }} records</span>
                @endif
            </div>
        </div>
        <div class="ef-card-body" style="padding:14px;">
            @if($sortedPays->count())
            <div class="pay-tl-wrap" id="payHistWrap">
                <div class="pay-tl">
                    @foreach($sortedPays as $idx => $pay)
                    <div class="pay-tl-item{{ $idx>=$initCount?' hidden-pays':'' }}">
                        <div class="pay-tl-dot"><span style="font-size:10px;font-weight:800;">{{ $sortedPays->count()-$idx }}</span></div>
                        <div style="flex:1;padding-top:3px;">
                            <div class="pay-tl-amt">₹{{ number_format($pay->amount_paid,0) }}
                                @if($pay->penalty>0)<span style="font-size:11px;color:#e53e3e;"> +₹{{ number_format($pay->penalty,0) }} pen</span>@endif
                                @if($pay->others_amount>0)<span style="font-size:11px;color:#d97706;"> +₹{{ number_format($pay->others_amount,0) }} others</span>@endif
                            </div>
                            @if($pay->due_month)<div class="pay-tl-due"><i class="ti-calendar mr-1"></i>Due: {{ $pay->due_month->format('M Y') }}</div>@endif
                            <div class="pay-tl-dt"><i class="ti-check mr-1" style="color:#38a169;"></i>{{ $pay->payment_date->format('d M Y') }}</div>
                            <div style="margin-top:3px;">
                                @if($pay->payment_mode)<span class="pay-mode">{{ ucfirst($pay->payment_mode) }}</span>@endif
                                @if($pay->reference_no)<span style="font-size:10px;color:#b0bac9;margin-left:4px;">{{ $pay->reference_no }}</span>@endif
                            </div>
                            @if($pay->particulars)<div style="font-size:10px;color:#b0bac9;margin-top:2px;">{{ $pay->particulars }}</div>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @if($sortedPays->count() > $initCount)
            <div style="text-align:center;margin-top:6px;">
                <button class="load-more-btn" id="loadMoreBtn" onclick="loadMore()">
                    <i class="ti-angle-down mr-1"></i> Load More
                    <span style="background:#4338ca;color:#fff;border-radius:999px;padding:1px 6px;font-size:10px;margin-left:4px;">{{ $sortedPays->count()-$initCount }}</span>
                </button>
                <button class="load-more-btn" id="showLessBtn" onclick="showLess()" style="display:none;">
                    <i class="ti-angle-up mr-1"></i> Show Less
                </button>
            </div>
            @endif
            @else
            <div style="text-align:center;padding:18px 0;color:#b0bac9;">
                <i class="ti-credit-card" style="font-size:26px;display:block;margin-bottom:7px;"></i>
                <div style="font-size:13px;font-weight:600;">No payments recorded yet</div>
            </div>
            @endif
        </div>
    </div>

</div>
</div>
</div></div></div></div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('#payBtn').on('click',function(){ $(this).prop('disabled',true).html('<i class="ti-reload mr-1"></i> Recording...'); $('#payForm').submit(); });
    $('#saveBtn').on('click',function(){ $(this).prop('disabled',true).html('<i class="ti-reload mr-1"></i> Updating...'); $('#editForm').submit(); });
});
function loadMore(){
    document.querySelectorAll('.hidden-pays').forEach(function(el){ el.style.display='flex'; });
    document.getElementById('loadMoreBtn').style.display='none';
    document.getElementById('showLessBtn').style.display='block';
}
function showLess(){
    var limit={{ $initCount }};
    document.querySelectorAll('.pay-tl-item').forEach(function(el,i){ if(i>=limit){ el.style.display='none'; el.classList.add('hidden-pays'); } });
    document.getElementById('showLessBtn').style.display='none';
    document.getElementById('loadMoreBtn').style.display='block';
}
</script>
@endpush
