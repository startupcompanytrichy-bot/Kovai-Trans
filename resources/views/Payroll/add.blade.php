@extends('layouts.fullscreen')
@section('title', 'Add Payroll')

@push('styles')
<style>
/* ── Shell ── */
html,body { background:#f0f4fa; }
.pr-shell  { display:flex; flex-direction:column; min-height:100vh; }

/* ── Top bar ── */
.pr-topbar{background:linear-gradient(135deg,#1a5bbf 0%,#2c7be5 100%);padding:10px 24px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 8px rgba(0,0,0,.15);}
.pr-topbar .tb-title{color:#fff;font-size:15px;font-weight:800;display:flex;align-items:center;gap:8px;}
.pr-topbar .tb-sub{color:rgba(255,255,255,.75);font-size:11px;margin-top:1px;}

/* ── Body ── */
.pr-body{flex:1;padding:18px 20px 100px;}

/* ── Fixed footer ── */
.pr-footer{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:2px solid #e2e8f0;padding:10px 24px;display:flex;justify-content:space-between;align-items:center;z-index:9999;box-shadow:0 -4px 16px rgba(0,0,0,.08);}

/* ── Cards ── */
.pf-card{background:#fff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:16px;overflow:hidden;}
.pf-card-header{display:flex;align-items:center;gap:10px;padding:11px 16px;border-bottom:1px solid #f0f2f7;background:#fafbff;}
.pf-card-header .ch-icon{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;}
.pf-card-header h6{margin:0;font-size:13px;font-weight:700;color:#1a2340;}
.pf-card-body{padding:16px;}
.pf-label{font-size:12px;font-weight:700;color:#596579;margin-bottom:4px;display:block;}
.pf-label .req{color:#e53e3e;}
.pf-input{min-height:42px;border-color:#d7dce5;color:#303549;font-size:13px;border-radius:8px;}
.pf-input:focus{border-color:#2c7be5;box-shadow:0 0 0 2px rgba(44,123,229,.12);}

/* ── Advance section ── */
.adv-section{border:1.5px solid #e2e8f0;border-radius:10px;overflow:hidden;margin-bottom:12px;}
.adv-section-hd{display:flex;align-items:center;gap:8px;padding:9px 14px;font-size:12px;font-weight:700;}
.adv-row{display:flex;align-items:center;gap:10px;padding:7px 14px;border-bottom:1px solid #f4f6fb;font-size:12px;}
.adv-row:last-child{border-bottom:none;}
.adv-row input[type=checkbox]{width:15px;height:15px;accent-color:#d97706;flex-shrink:0;cursor:pointer;}
.adv-tag{font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;white-space:nowrap;}
.adv-tag.trip{background:#f5f3ff;color:#7c3aed;}
.adv-tag.normal{background:#fffbeb;color:#d97706;}
.exp-inline{display:flex;flex-wrap:wrap;gap:4px;margin-top:3px;}
.exp-chip{font-size:10px;font-weight:700;background:#e0f2fe;color:#0369a1;border-radius:4px;padding:1px 6px;}

/* ── Summary panel ── */
.net-bar{background:linear-gradient(135deg,#1a5bbf,#2c7be5);border-radius:10px;padding:14px 18px;display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;}
.net-bar .nb-label{color:rgba(255,255,255,.85);font-size:12px;font-weight:700;}
.net-bar .nb-val{color:#fff;font-size:28px;font-weight:800;}
.sum-panel{background:#fff;border-radius:10px;border:1px solid #c5d9f5;overflow:hidden;}
.sum-panel .sp-head{background:linear-gradient(135deg,#1a5bbf,#2c7be5);color:#fff;padding:10px 14px;font-size:12px;font-weight:700;}
.sum-row{display:flex;justify-content:space-between;align-items:center;padding:9px 14px;border-bottom:1px solid #f0f2f7;font-size:13px;}
.sum-row:last-child{border-bottom:none;}
.sum-row .sr-label{color:#596579;font-weight:600;}
.sum-row .sr-val{font-weight:800;color:#1a2340;}
.sum-total{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;background:#f0fff4;border-top:2px solid #38a169;}
.sum-total .st-label{font-size:13px;font-weight:700;color:#166534;}
.sum-total .st-val{font-size:22px;font-weight:800;color:#166534;}

/* ── Trip table ── */
.trip-tbl{width:100%;border-collapse:collapse;font-size:12px;}
.trip-tbl th{padding:8px 10px;text-align:left;font-size:10px;font-weight:700;color:#8a94a6;text-transform:uppercase;background:#f4f6fb;border-bottom:2px solid #e2e8f0;white-space:nowrap;}
.trip-tbl td{padding:7px 10px;border-bottom:1px solid #f0f2f7;vertical-align:middle;}
.trip-tbl tbody tr:hover{background:#f8fafc !important;}
.trip-tbl tfoot td{background:#eef4fd;border-top:2px solid #c5d9f5;font-weight:800;padding:8px 10px;}
.editable-inp{height:34px;border:1.5px solid #d1d5db;border-radius:6px;padding:0 8px;font-size:13px;font-weight:700;text-align:right;width:105px;}
.editable-inp:focus{outline:none;border-color:#2c7be5;box-shadow:0 0 0 2px rgba(44,123,229,.12);}

/* ── Stat mini ── */
.stat-mini{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-bottom:12px;}
.stat-mini .sm-item{background:#f8fafc;border-radius:8px;padding:9px 10px;border:1px solid #e2e8f0;text-align:center;}
.stat-mini .sm-item .sl{font-size:9px;font-weight:700;color:#8a94a6;text-transform:uppercase;}
.stat-mini .sm-item .sv{font-size:13px;font-weight:800;line-height:1.3;margin-top:2px;}
@media(max-width:768px){.stat-mini{grid-template-columns:repeat(3,1fr);}}

/* ── Salary bar ── */
.salary-bar{display:flex;align-items:center;flex-wrap:wrap;gap:10px;background:#fff;border:2px dashed #2c7be5;border-radius:8px;padding:10px 14px;margin-top:4px;}
.salary-bar.locked{border:2px solid #86efac;background:#f0fff4;}

/* ── Buttons ── */
.btn-back-pr{background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-back-pr:hover{background:rgba(255,255,255,.25);color:#fff;text-decoration:none;}
.btn-cancel-pr{background:#f4f6fb;color:#596579;border:1.5px solid #e5e8ee;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-save-pr{background:linear-gradient(135deg,#2c7be5,#1a5bbf);color:#fff;border:none;border-radius:8px;padding:10px 24px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(44,123,229,.35);display:inline-flex;align-items:center;gap:6px;}
.btn-confirm{height:38px;padding:0 16px;background:linear-gradient(135deg,#38a169,#2d7a57);color:#fff;border:none;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;}
.btn-unlock{height:34px;padding:0 12px;background:#fffbeb;color:#d97706;border:1.5px solid #fcd34d;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;}
</style>
@endpush

@section('content')
<div class="pcoded-inner-content">
<div class="pr-shell">

{{-- TOP BAR --}}
<div class="pr-topbar">
    <div>
        <div class="tb-title"><i class="ti-wallet mr-2"></i>Add Payroll Record</div>
        <div class="tb-sub">Calculate salary — advances, bata and net pay in one place.</div>
    </div>
    <a href="{{ route('payroll') }}" class="btn-back-pr"><i class="ti-arrow-left mr-1"></i> Back to Payroll</a>
</div>

{{-- BODY --}}
<div class="pr-body">

@include('partials.flash')

<form id="payrollForm" action="{{ route('payroll.store') }}" method="POST" data-no-softnav>
@csrf

<div class="row">

{{-- ── LEFT COLUMN ── --}}
<div class="col-lg-8">

{{-- EMPLOYEE CARD --}}
<div class="pf-card">
    <div class="pf-card-header">
        <div class="ch-icon" style="background:#eef4fd;color:#2c7be5;"><i class="ti-user"></i></div>
        <h6>Employee Details</h6>
    </div>
    <div class="pf-card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="pf-label">Driver / Employee</label>
                    <select name="driver_id" id="driverSelect" class="form-control pf-input" data-placeholder="— Select Driver (optional) —">
                        <option value=""></option>
                        @foreach($drivers as $d)
                        <option value="{{ $d->id }}" data-name="{{ $d->name }}" {{ old('driver_id')==$d->id?'selected':'' }}>
                            {{ $d->name }}{{ $d->mobile?' — '.$d->mobile:'' }}
                        </option>
                        @endforeach
                    </select>
                    <small style="color:#8a94a6;font-size:11px;">Leave blank for non-driver staff</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="pf-label">Employee Name <span class="req">*</span></label>
                    <input type="text" name="employee_name" id="employeeName"
                        class="form-control pf-input @error('employee_name') is-invalid @enderror"
                        value="{{ old('employee_name', $presetEmployee ?? '') }}" placeholder="Full name" required>
                    @error('employee_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-0">
                    <label class="pf-label">Payroll Month <span class="req">*</span></label>
                    <input type="month" name="payroll_month" class="form-control pf-input @error('payroll_month') is-invalid @enderror"
                        value="{{ old('payroll_month', date('Y-m')) }}" required>
                    @error('payroll_month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-0">
                    <label class="pf-label">Payment Mode</label>
                    <select name="payment_mode" id="paymentModeSelect" class="form-control pf-input" data-placeholder="— Select Mode —">
                        <option value="cash"   {{ old('payment_mode','cash')==='cash'   ?'selected':'' }}>💵 Cash</option>
                        <option value="upi"    {{ old('payment_mode')==='upi'            ?'selected':'' }}>📱 UPI</option>
                        <option value="bank"   {{ old('payment_mode')==='bank'           ?'selected':'' }}>🏦 Bank Transfer</option>
                        <option value="cheque" {{ old('payment_mode')==='cheque'         ?'selected':'' }}>📄 Cheque</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ADVANCES CARD --}}
<div class="pf-card">
    <div class="pf-card-header">
        <div class="ch-icon" style="background:#fffbeb;color:#d97706;"><i class="ti-wallet"></i></div>
        <h6>Salary Advances</h6>
        <span id="advSummaryBadge" style="display:none;margin-left:auto;font-size:11px;font-weight:700;background:#fffbeb;color:#d97706;border:1px solid #fcd34d;border-radius:8px;padding:2px 10px;"></span>
    </div>
    <div class="pf-card-body" style="padding:10px 14px;">
        <div class="stat-mini">
            <div class="sm-item" style="border-left:3px solid #7c3aed;">
                <div class="sl">Trip Adv</div><div class="sv" style="color:#7c3aed;" id="biTripAdv">₹0</div>
            </div>
            <div class="sm-item" style="border-left:3px solid #d97706;">
                <div class="sl">Normal Adv</div><div class="sv" style="color:#d97706;" id="biNormAdv">₹0</div>
            </div>
            <div class="sm-item" style="border-left:3px solid #0369a1;">
                <div class="sl">Expenses</div><div class="sv" style="color:#0369a1;" id="biExpenses">₹0</div>
            </div>
            <div class="sm-item" style="border-left:3px solid #38a169;">
                <div class="sl">Recovered</div><div class="sv" style="color:#38a169;" id="biCollected">₹0</div>
            </div>
            <div class="sm-item" style="border-left:3px solid #e53e3e;">
                <div class="sl">Pending</div><div class="sv" style="color:#e53e3e;" id="biBalance">₹0</div>
            </div>
        </div>
        <div id="noAdvanceMsg" style="font-size:12px;color:#8a94a6;padding:4px 0;display:flex;align-items:center;gap:6px;">
            <i class="ti-info-alt" style="color:#2c7be5;"></i> No pending advances — select a driver or type employee name to load.
        </div>
        <div id="tripAdvSection" class="adv-section" style="display:none;">
            <div class="adv-section-hd" style="background:#f5f3ff;color:#7c3aed;">
                <i class="ti-truck"></i> Trip-Based Advances
                <span id="tripAdvCount" style="font-size:10px;color:#8a94a6;font-weight:400;margin-left:4px;"></span>
            </div>
            <div id="tripAdvList"></div>
        </div>
        <div id="normAdvSection" class="adv-section" style="display:none;">
            <div class="adv-section-hd" style="background:#fffbeb;color:#d97706;">
                <i class="ti-wallet"></i> Normal Advances
                <span id="normAdvCount" style="font-size:10px;color:#8a94a6;font-weight:400;margin-left:4px;"></span>
            </div>
            <div id="normAdvList"></div>
        </div>
        <div id="advDeductRow" style="display:none;margin-top:10px;align-items:center;gap:10px;background:#fffbeb;border:1.5px solid #fcd34d;border-radius:8px;padding:10px 14px;">
            <i class="ti-minus-alt" style="color:#d97706;font-size:15px;"></i>
            <span style="font-size:13px;font-weight:700;color:#1a2340;">Total Advance Deduction:</span>
            <span style="font-size:16px;font-weight:800;color:#d97706;margin-left:auto;" id="advTotalDeduct">₹ 0.00</span>
        </div>
        <input type="hidden" name="advance_deduction" id="advanceDeduction" value="0">
    </div>
</div>

{{-- PAYMENT DETAILS --}}
<div class="pf-card">
    <div class="pf-card-header">
        <div class="ch-icon" style="background:#eef4fd;color:#2c7be5;"><i class="ti-credit-card"></i></div>
        <h6>Payment Details</h6>
    </div>
    <div class="pf-card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="pf-label">Reference No.</label>
                    <input type="text" name="reference_no" class="form-control pf-input" value="{{ old('reference_no') }}" placeholder="UPI / Cheque No.">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-0">
                    <label class="pf-label">Payment Date <small style="color:#8a94a6;font-weight:400;">(marks as Paid)</small></label>
                    <input type="date" name="payment_date" class="form-control pf-input" value="{{ old('payment_date') }}">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- NOTES --}}
<div class="pf-card">
    <div class="pf-card-header">
        <div class="ch-icon" style="background:#fffbeb;color:#d97706;"><i class="ti-comment-alt"></i></div>
        <h6>Notes</h6>
    </div>
    <div class="pf-card-body">
        <textarea name="notes" class="form-control pf-input" rows="2" placeholder="Any remarks…">{{ old('notes') }}</textarea>
    </div>
</div>

</div>{{-- end col-lg-8 --}}


{{-- ── RIGHT COLUMN: Salary Summary ── --}}
<div class="col-lg-4">
    <div class="pf-card" style="position:sticky;top:16px;">
        <div class="pf-card-header">
            <div class="ch-icon" style="background:#eef4fd;color:#2c7be5;"><i class="ti-receipt"></i></div>
            <h6>Salary Summary</h6>
        </div>
        <div class="pf-card-body" style="padding:12px;">

            <div class="net-bar">
                <div>
                    <div class="nb-label">Net Pay (Take Home)</div>
                    <div class="nb-label" style="font-size:10px;opacity:.7;">Total Earned − Advance Deduction</div>
                </div>
                <div class="nb-val">₹ <span id="sumNet">0.00</span></div>
            </div>

            <div class="sum-panel">
                <div class="sp-head"><i class="ti-bar-chart mr-1"></i> Calculation Breakdown</div>
                <div class="sum-row">
                    <span class="sr-label"><i class="ti-check-box mr-1" style="color:#0369a1;"></i> Balance Amount</span>
                    <span class="sr-val" style="color:#0369a1;">₹ <span id="sumBalance">0.00</span></span>
                </div>
                <div class="sum-row">
                    <span class="sr-label" style="padding-left:8px;font-size:11px;">+ Driver Bata (TA/DA)</span>
                    <span class="sr-val" style="color:#7c3aed;">₹ <span id="sumBata">0.00</span></span>
                </div>
                <div class="sum-row">
                    <span class="sr-label" style="padding-left:8px;font-size:11px;">+ Salary Earned</span>
                    <span class="sr-val" style="color:#1a5bbf;">₹ <span id="sumSalary">0.00</span></span>
                </div>
                <div class="sum-total">
                    <span class="st-label"><i class="ti-crown mr-1"></i> Total Earned</span>
                    <span class="st-val">₹ <span id="sumTotalEarned">0.00</span></span>
                </div>
                <div class="sum-row" style="background:#fff5f5;">
                    <span class="sr-label" style="color:#e53e3e;"><i class="ti-minus mr-1"></i> Advance Deduction</span>
                    <span class="sr-val" style="color:#e53e3e;">− ₹ <span id="sumAdv">0.00</span></span>
                </div>
            </div>

            <div style="margin-top:10px;font-size:10px;color:#8a94a6;background:#f8fafc;border-radius:6px;padding:8px 10px;border:1px solid #e2e8f0;line-height:1.7;">
                <strong style="color:#596579;">Formula:</strong><br>
                Balance Amount = Total Advance − Recovered<br>
                Total Earned = Balance + Bata + Salary<br>
                Net Pay = Total Earned − Advance Deduction
            </div>

            <div id="advBreakdownBox" style="display:none;margin-top:10px;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                <div style="background:#f8fafc;padding:7px 12px;font-size:11px;font-weight:700;color:#8a94a6;text-transform:uppercase;">Advance Breakdown</div>
                <div id="advBreakdownList" style="padding:8px 12px;font-size:12px;"></div>
            </div>
            <div id="expInfoBox" style="display:none;margin-top:8px;border:1px solid #bae6fd;border-radius:8px;overflow:hidden;">
                <div style="background:#f0f9ff;padding:7px 12px;font-size:11px;font-weight:700;color:#0369a1;text-transform:uppercase;"><i class="ti-receipt mr-1"></i>Expenses on Advance</div>
                <div id="expInfoList" style="padding:8px 12px;font-size:12px;max-height:160px;overflow-y:auto;"></div>
            </div>
        </div>
    </div>
</div>{{-- end col-lg-4 --}}

</div>{{-- end employee+summary row --}}


{{-- ── TRIP BILLING — full width ── --}}
<div class="pf-card" id="tripBillingCard">
    <div class="pf-card-header">
        <div class="ch-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-truck"></i></div>
        <h6>Trip Billing &amp; Salary Calculation</h6>
        <span style="font-size:10px;color:#8a94a6;font-weight:600;background:#eef4fd;padding:2px 10px;border-radius:12px;margin-left:8px;">
            Total Earned = Balance Amount + Driver Bata + Salary Earned
        </span>
        <span id="tripLockBadge" style="display:none;margin-left:auto;font-size:11px;font-weight:700;background:#dcfce7;color:#166534;border:1px solid #86efac;border-radius:8px;padding:2px 10px;">
            <i class="ti-lock mr-1"></i> Confirmed &amp; Locked
        </span>
    </div>
    <div class="pf-card-body" style="padding:12px 14px;">

        <div id="noTripMsg" style="font-size:12px;color:#8a94a6;padding:8px 0;display:flex;align-items:center;gap:8px;">
            <i class="ti-info-alt" style="color:#2c7be5;font-size:15px;"></i>
            Select a driver above to load trips. Advance amounts are pulled from Salary Advances records.
        </div>

        <div id="tripListWrap" style="display:none;margin-bottom:12px;">
        <div style="overflow-x:auto;">
        <table class="trip-tbl">
            <thead>
                <tr>
                    <th style="width:32px;text-align:center;">✓</th>
                    <th>Trip Date</th>
                    <th>Route</th>
                    <th style="text-align:right;">
                        Total Advance<br><span style="font-size:9px;font-weight:400;color:#b0b8c9;">(Salary Advances)</span>
                    </th>
                    <th style="text-align:right;">
                        Recovered<br><span style="font-size:9px;font-weight:400;color:#b0b8c9;">(Already Deducted)</span>
                    </th>
                    <th style="text-align:right;">
                        Balance Amount<br><span style="font-size:9px;font-weight:400;color:#b0b8c9;">(Advance − Recovered)</span>
                    </th>
                    <th style="text-align:right;">
                        Driver Bata (TA/DA)<br><span style="font-size:9px;font-weight:400;color:#b0b8c9;">(Editable)</span>
                    </th>
                    <th style="text-align:right;">
                        Salary Earned<br><span style="font-size:9px;font-weight:400;color:#b0b8c9;">(Editable)</span>
                    </th>
                </tr>
            </thead>
            <tbody id="tripListItems"></tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="font-size:12px;color:#1a2340;">
                        <i class="ti-bar-chart mr-1" style="color:#2c7be5;"></i> Total
                        <span id="tripCheckedCount" style="font-size:10px;color:#8a94a6;margin-left:6px;"></span>
                    </td>
                    <td style="text-align:right;color:#d97706;" id="ftTotalAdv">₹ 0.00</td>
                    <td style="text-align:right;color:#38a169;" id="ftRecovered">₹ 0.00</td>
                    <td style="text-align:right;color:#0369a1;font-size:14px;" id="ftBalance">₹ 0.00</td>
                    <td style="text-align:right;color:#7c3aed;" id="ftBata">₹ 0.00</td>
                    <td style="text-align:right;color:#1a5bbf;font-size:14px;" id="ftSalary">₹ 0.00</td>
                </tr>
                <tr style="background:#f0fff4;">
                    <td colspan="6" style="font-size:12px;font-weight:700;color:#166534;border-top:1px dashed #86efac;">
                        <i class="ti-crown mr-1"></i>
                        Total Earned = Balance Amount + Driver Bata + Salary Earned
                    </td>
                    <td colspan="2" style="text-align:right;font-size:18px;font-weight:800;color:#166534;border-top:1px dashed #86efac;" id="ftTotalEarned">
                        ₹ 0.00
                    </td>
                </tr>
            </tfoot>
        </table>
        </div>
        </div>

        <div id="salaryInputWrap" class="salary-bar">
            <div style="display:flex;align-items:center;gap:6px;">
                <i class="ti-money" style="color:#2c7be5;font-size:15px;"></i>
                <span style="font-size:13px;font-weight:700;color:#1a2340;">Total Salary Earned</span>
            </div>
            <span style="font-size:18px;font-weight:800;color:#1a5bbf;">₹</span>
            <input type="number" name="basic_salary" id="basicSalary" value="{{ old('basic_salary',0) }}"
                placeholder="0.00" step="0.01" min="0" required
                style="width:150px;height:38px;border:1.5px solid #d1d5db;border-radius:6px;padding:0 10px;font-size:16px;font-weight:800;text-align:right;color:#1a5bbf;">
            <span style="font-size:11px;color:#8a94a6;flex:1;">← Auto-filled from Salary Earned column. You can also type manually.</span>
            <button type="button" id="confirmSalaryBtn" onclick="lockTripSection()" class="btn-confirm">
                <i class="ti-check mr-1"></i> Confirm &amp; Lock
            </button>
        </div>
    </div>
</div>

</form>
</div>{{-- end pr-body --}}

{{-- FIXED FOOTER --}}
<div class="pr-footer">
    <div style="font-size:11px;color:#8a94a6;">
        <kbd style="background:#f0f2f7;padding:2px 6px;border-radius:4px;font-size:10px;border:1px solid #d7dce5;">Ctrl+S</kbd> quick save
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="{{ route('payroll') }}" class="btn-cancel-pr"><i class="ti-arrow-left"></i> Back</a>
        <button type="submit" form="payrollForm" class="btn-save-pr"><i class="ti-save"></i> Save Payroll</button>
    </div>
</div>

</div>{{-- end pr-shell --}}
</div>{{-- end pcoded-inner-content --}}
@endsection


@push('scripts')
<script>
var advanceApiUrl     = '{{ route('payroll.driver.advance', '__ID__') }}';
var employeeAdvApiUrl = '{{ route('payroll.employee.advance') }}';
var driverTrips       = @json($driverTrips);
var employeeAdvData   = @json($employeeAdvData);
var employeeExpenses  = @json($employeeExpenses);
var employeeDetails   = @json($employeeDetails);

/* ── Helpers ── */
function fmt(n){ return parseFloat(n||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2}); }
function fmtS(n){ return '₹'+parseFloat(n||0).toLocaleString('en-IN',{minimumFractionDigits:0,maximumFractionDigits:0}); }

/* ── Raw totals stored in JS vars — updated by calcTripTotal ── */
var rawBalance = 0, rawBata = 0;

/* ── Master recalc ── */
function recalc(){
    var salary      = parseFloat($('#basicSalary').val())     || 0;
    var adv         = parseFloat($('#advanceDeduction').val()) || 0;
    var totalEarned = rawBalance + rawBata + salary;
    var netPay      = Math.max(0, totalEarned - adv);

    $('#sumBalance').text(fmt(rawBalance));
    $('#sumBata').text(fmt(rawBata));
    $('#sumSalary').text(fmt(salary));
    $('#sumTotalEarned').text(fmt(totalEarned));
    $('#sumAdv').text(fmt(adv));
    $('#sumNet').text(fmt(netPay));
}
$('#basicSalary').on('input change', recalc);

/* ── advancesByTrip: trip_id → {amount, recovered} ── */
var advancesByTrip = {};

/* ── Render trip rows ── */
function renderTrips(trips){
    if(!trips || !trips.length){ $('#tripListWrap').hide(); $('#noTripMsg').show(); return; }
    $('#noTripMsg').hide();
    var html = '';
    $.each(trips, function(i, t){
        var raw  = t.trip_date ? String(t.trip_date) : '';
        var dp   = raw.substring(0,10);
        var d    = new Date(dp + 'T00:00:00');
        var ds   = isNaN(d.getTime()) ? (dp||'—') : d.toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'});
        var route = (t.from_location||'—') + ' → ' + (t.to_location||'—');

        var advData   = advancesByTrip[t.id] || {amount:0, recovered:0};
        var advAmt    = parseFloat(advData.amount)    || 0;
        var recovered = parseFloat(advData.recovered) || 0;
        var balance   = Math.max(0, advAmt - recovered);
        var bata      = parseFloat(t.driver_bata) || 0;
        var bg        = i%2===0 ? '#fff' : '#fafbff';

        html += '<tr style="background:'+bg+';" data-trip-id="'+t.id+'" data-adv="'+advAmt+'" data-rec="'+recovered+'">';
        html += '<td style="text-align:center;"><input type="checkbox" class="ti-cbox" checked style="width:15px;height:15px;accent-color:#2c7be5;cursor:pointer;"></td>';
        html += '<td style="color:#596579;white-space:nowrap;">'+ds+'</td>';
        html += '<td style="color:#596579;" title="'+route.replace(/"/g,'&quot;')+'">'+route+'</td>';
        // Total Advance — read-only
        html += '<td style="text-align:right;font-weight:700;color:#d97706;">'+(advAmt>0?'₹ '+fmt(advAmt):'<span style="color:#c9d1db;font-size:11px;">—</span>')+'</td>';
        // Recovered — read-only
        html += '<td style="text-align:right;font-weight:700;color:#38a169;">'+(recovered>0?'₹ '+fmt(recovered):'<span style="color:#c9d1db;font-size:11px;">—</span>')+'</td>';
        // Balance — auto-calc, read-only
        html += '<td style="text-align:right;font-weight:800;color:#0369a1;" class="td-balance">₹ '+fmt(balance)+'</td>';
        // Bata — editable
        html += '<td style="text-align:right;"><input type="number" class="ti-bata-inp editable-inp" value="'+bata.toFixed(2)+'" step="0.01" min="0" style="color:#7c3aed;"></td>';
        // Salary — editable
        html += '<td style="text-align:right;"><input type="number" class="ti-salary-inp editable-inp" value="0.00" step="0.01" min="0" style="color:#1a5bbf;border-color:#2c7be5;"></td>';
        html += '</tr>';
    });

    $('#tripListItems').html(html);
    $('#tripListWrap').show();
    $('#tripListItems')
        .off('change input', '.ti-cbox, .ti-bata-inp, .ti-salary-inp')
        .on('change input',  '.ti-cbox, .ti-bata-inp, .ti-salary-inp', calcTripTotal);
    calcTripTotal();
}

/* ── calcTripTotal ── */
function calcTripTotal(){
    var totAdv=0, totRec=0, totBal=0, totBata=0, totSalary=0, checked=0;
    $('#tripListItems tr').each(function(){
        var $r        = $(this);
        var advAmt    = parseFloat($r.data('adv')) || 0;
        var recovered = parseFloat($r.data('rec')) || 0;
        var balance   = Math.max(0, advAmt - recovered);
        var bata      = parseFloat($r.find('.ti-bata-inp').val())   || 0;
        var salary    = parseFloat($r.find('.ti-salary-inp').val()) || 0;
        $r.find('.td-balance').text('₹ '+fmt(balance));
        if($r.find('.ti-cbox').is(':checked')){
            totAdv += advAmt; totRec += recovered; totBal += balance;
            totBata += bata;  totSalary += salary; checked++;
        }
    });

    rawBalance = totBal;
    rawBata    = totBata;
    var totalEarned = totBal + totBata + totSalary;

    $('#ftTotalAdv').text('₹ '+fmt(totAdv));
    $('#ftRecovered').text('₹ '+fmt(totRec));
    $('#ftBalance').text('₹ '+fmt(totBal));
    $('#ftBata').text('₹ '+fmt(totBata));
    $('#ftSalary').text('₹ '+fmt(totSalary));
    $('#ftTotalEarned').text('₹ '+fmt(totalEarned));
    $('#tripCheckedCount').text(checked ? '('+checked+' trip'+(checked>1?'s':'')+' selected)' : '');

    $('#basicSalary').val(totSalary.toFixed(2)).trigger('change');
}
</script>
@endpush

@push('scripts')
<script>
/* ── updateAdvancesByTrip ── */
function updateAdvancesByTrip(items){
    advancesByTrip = {};
    $.each(items, function(_, a){
        if(a.trip_id){
            if(!advancesByTrip[a.trip_id]) advancesByTrip[a.trip_id] = {amount:0, recovered:0};
            advancesByTrip[a.trip_id].amount    += parseFloat(a.amount)    || 0;
            advancesByTrip[a.trip_id].recovered += parseFloat(a.recovered) || 0;
        }
    });
    if($('#tripListItems tr').length){
        $('#tripListItems tr').each(function(){
            var $r     = $(this);
            var tripId = $r.data('trip-id');
            var dv     = advancesByTrip[tripId] || {amount:0, recovered:0};
            $r.data('adv', dv.amount).data('rec', dv.recovered);
            var $td = $r.find('td');
            $td.eq(3).html(dv.amount>0    ? '₹ '+fmt(dv.amount)    : '<span style="color:#c9d1db;font-size:11px;">—</span>');
            $td.eq(4).html(dv.recovered>0 ? '₹ '+fmt(dv.recovered) : '<span style="color:#c9d1db;font-size:11px;">—</span>');
        });
        calcTripTotal();
    }
}

/* ── Lock / Unlock ── */
var tripSectionLocked = false;

function lockTripSection(){
    if(tripSectionLocked) return;
    if((parseFloat($('#basicSalary').val())||0) <= 0){ alert('Please enter Salary Earned before confirming.'); return; }
    tripSectionLocked = true;
    $('#tripListItems .ti-cbox').prop('disabled', true);
    $('#tripListItems .ti-bata-inp, #tripListItems .ti-salary-inp')
        .prop('readonly', true).css({background:'#f4f6fb',color:'#8a94a6',cursor:'not-allowed',borderColor:'#e2e8f0'});
    $('#basicSalary').prop('readonly', true).css({background:'#f4f6fb',color:'#1a5bbf',cursor:'not-allowed'});
    $('.adv-cbox').prop('disabled', true);
    $('#tripLockBadge').show();
    $('#confirmSalaryBtn').hide();
    $('#salaryInputWrap').addClass('locked');
    if(!$('#tripUnlockBtn').length){
        $('<button type="button" id="tripUnlockBtn" class="btn-unlock"><i class="ti-unlock mr-1"></i>Unlock to Edit</button>')
            .appendTo('#salaryInputWrap').on('click', unlockTripSection);
    }
}
function unlockTripSection(){
    tripSectionLocked = false;
    $('#tripListItems .ti-cbox').prop('disabled', false);
    $('#tripListItems .ti-bata-inp, #tripListItems .ti-salary-inp')
        .prop('readonly', false).css({background:'',color:'',cursor:'',borderColor:''});
    $('#basicSalary').prop('readonly', false).css({background:'',color:'',cursor:''});
    $('.adv-cbox').prop('disabled', false);
    $('#tripLockBadge').hide(); $('#confirmSalaryBtn').show();
    $('#salaryInputWrap').removeClass('locked');
    $('#tripUnlockBtn').remove();
}

/* ── Form submit ── */
$('#payrollForm').on('submit', function(e){
    var hasRows = $('#tripListItems .ti-cbox').length > 0;
    if(hasRows && !tripSectionLocked){
        if((parseFloat($('#basicSalary').val())||0) <= 0){
            e.preventDefault(); alert('Please enter Salary Earned before saving.'); return false;
        }
        lockTripSection();
    }
    $('#tripListItems .ti-cbox, #tripListItems .ti-bata-inp, #tripListItems .ti-salary-inp')
        .prop('disabled',false).prop('readonly',false);
    $('.adv-cbox').prop('disabled',false);
});

/* ── renderAdvances ── */
function renderAdvances(res){
    var items = res.items||[];
    updateAdvancesByTrip(items);
    var tripItems=[], normItems=[];
    $.each(items, function(_,a){ if(a.trip_id) tripItems.push(a); else normItems.push(a); });
    var tripAdv=0,normAdv=0,expTotal=0,recovered=0,pending=0;
    $.each(tripItems,function(_,a){ tripAdv+=a.amount; recovered+=a.recovered; pending+=a.pending; });
    $.each(normItems,function(_,a){ normAdv+=a.amount; recovered+=a.recovered; pending+=a.pending; });
    expTotal = res.exp_total||0;
    $('#biTripAdv').text(fmtS(tripAdv)); $('#biNormAdv').text(fmtS(normAdv));
    $('#biExpenses').text(fmtS(expTotal)); $('#biCollected').text(fmtS(recovered)); $('#biBalance').text(fmtS(pending));
    if(pending>0){ $('#advSummaryBadge').show().text('Pending: '+fmtS(pending)); } else { $('#advSummaryBadge').hide(); }
    if(tripItems.length){ $('#tripAdvList').html(buildAdvRows(tripItems)); $('#tripAdvCount').text('('+tripItems.length+')'); $('#tripAdvSection').show(); } else { $('#tripAdvSection').hide(); }
    if(normItems.length){ $('#normAdvList').html(buildAdvRows(normItems)); $('#normAdvCount').text('('+normItems.length+')'); $('#normAdvSection').show(); } else { $('#normAdvSection').hide(); }
    if(tripItems.length||normItems.length){
        $('#noAdvanceMsg').hide(); $('#advDeductRow').css('display','flex'); $('#advBreakdownBox').show();
    } else {
        $('#noAdvanceMsg').show(); $('#advDeductRow').hide(); $('#advBreakdownBox').hide(); $('#expInfoBox').hide();
    }
    $('.adv-row input[type=checkbox]').on('change', calcAdvTotal);
    calcAdvTotal();
    renderExpInfo(res.expenses||[]);
}

function buildAdvRows(items){
    var html='';
    $.each(items, function(i,a){
        var isTrip=!!a.trip_id;
        var tag=isTrip ? '<span class="adv-tag trip"><i class="ti-truck"></i> '+(a.trip_no||'Trip')+'</span>'
                       : '<span class="adv-tag normal"><i class="ti-wallet"></i> Normal</span>';
        var chips=''; if(a.expenses&&a.expenses.length){ $.each(a.expenses,function(_,e){ chips+='<span class="exp-chip">'+e.category+': ₹'+parseFloat(e.amount).toFixed(0)+'</span>'; }); }
        var note=a.notes?' — <span style="color:#a0aec0;">'+a.notes+'</span>':'';
        html+='<div class="adv-row">'
            +'<input type="checkbox" class="adv-cbox" '+(a.pending>0?'checked':'disabled')+' data-id="'+a.id+'" data-pending="'+a.pending+'">'
            +tag+'<div style="flex:1;min-width:0;"><div style="font-size:12px;font-weight:600;color:#1a2340;">'+a.date+note+'</div>'
            +(chips?'<div class="exp-inline">'+chips+'</div>':'')+'</div>'
            +'<div style="text-align:right;flex-shrink:0;"><div style="font-size:11px;color:#8a94a6;">Total: ₹'+fmt(a.amount)+'</div>'
            +'<div style="font-size:13px;font-weight:800;color:#d97706;">Pending: ₹'+fmt(a.pending)+'</div></div></div>';
    });
    return html;
}

function calcAdvTotal(){
    var total=0; $('input[name="advance_ids[]"]').remove(); var lines='';
    $('.adv-cbox:checked').each(function(){
        var p=parseFloat($(this).data('pending'))||0, id=$(this).data('id');
        total+=p;
        $('<input>').attr({type:'hidden',name:'advance_ids[]',value:id}).appendTo('#payrollForm');
        lines+='<div style="display:flex;justify-content:space-between;padding:3px 0;border-bottom:1px solid #f0f2f7;color:#596579;"><span>Advance #'+id+'</span><span style="font-weight:700;color:#d97706;">₹ '+fmt(p)+'</span></div>';
    });
    $('#advTotalDeduct').text('₹ '+fmt(total)); $('#advanceDeduction').val(total.toFixed(2));
    $('#advBreakdownList').html(lines||'<span style="color:#8a94a6;font-size:11px;">No advances selected.</span>');
    recalc();
}

function renderExpInfo(expenses){
    if(!expenses||!expenses.length){ $('#expInfoBox').hide(); return; }
    var html='';
    $.each(expenses,function(i,e){
        html+='<div style="display:flex;gap:8px;align-items:center;padding:4px 0;border-bottom:1px solid #f0f2f7;background:'+(i%2===0?'#fff':'#f8fafc')+';">'
            +'<span style="font-size:10px;font-weight:700;color:#8a94a6;white-space:nowrap;">'+e.date+'</span>'
            +'<span class="exp-chip">'+e.category+'</span>'
            +'<span style="flex:1;font-size:11px;color:#596579;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">'+(e.notes||'')+'</span>'
            +'<span style="font-size:12px;font-weight:700;color:#0369a1;white-space:nowrap;">₹'+fmt(e.amount)+'</span></div>';
    });
    $('#expInfoList').html(html); $('#expInfoBox').show();
}

function clearAdvances(){
    advancesByTrip={}; rawBalance=0; rawBata=0;
    $('#tripAdvSection,#normAdvSection,#advDeductRow,#advBreakdownBox,#expInfoBox').hide();
    $('#noAdvanceMsg').show(); $('#advSummaryBadge').hide();
    $('input[name="advance_ids[]"]').remove(); $('#advanceDeduction').val(0);
    $('#biTripAdv,#biNormAdv,#biExpenses,#biCollected,#biBalance').text('₹0');
    recalc();
}

/* ── API ── */
function loadAdvances(driverId, employeeName){
    if(driverId)                           $.get(advanceApiUrl.replace('__ID__',driverId), function(r){ renderAdvances(r); });
    else if(employeeName&&employeeName.length>=2) $.get(employeeAdvApiUrl,{name:employeeName}, function(r){ renderAdvances(r); });
    else clearAdvances();
}

/* ── Wire driver select AFTER Select2 is initialized ── */
$(document).ready(function(){

    // Init Select2 on this page's selects
    $('#driverSelect').select2({ width:'100%', placeholder:'— Select Driver (optional) —', allowClear:true });
    $('#paymentModeSelect').select2({ width:'100%', placeholder:'— Select Mode —' });

    // Driver change — must bind on the original select element (Select2 proxies change to it)
    $('#driverSelect').on('change', function(){
        var driverId = $(this).val();
        var name     = $(this).find('option:selected').data('name') || '';
        if(name) $('#employeeName').val(name);
        renderTrips(driverTrips[driverId] || null);
        loadAdvances(driverId, name);
    });

    // Employee name (non-driver)
    var empTimer;
    $('#employeeName').on('input', function(){
        if($('#driverSelect').val()) return;
        clearTimeout(empTimer);
        var name = $.trim($(this).val());
        if(name.length < 2){ clearAdvances(); return; }
        empTimer = setTimeout(function(){ loadAdvances(null, name); }, 500);
    });

    // Ctrl+S
    $(document).on('keydown', function(e){ if(e.ctrlKey && e.key==='s'){ e.preventDefault(); $('#payrollForm').submit(); }});

    recalc();

    @if(!empty($presetEmployee))
    loadAdvances(null, '{{ $presetEmployee }}');
    @endif
});
</script>
@endpush
