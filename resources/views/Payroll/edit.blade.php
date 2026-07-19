@extends('layouts.app')
@section('title', 'Edit Payroll — '.$payroll->employee_name)
@section('content')
<style>
.pr-form-page{background:#f4f6fb;}
.pr-form-header{background:linear-gradient(135deg,#38a169 0%,#276749 100%);border-radius:10px;padding:14px 20px;color:#fff;margin-bottom:18px;position:relative;overflow:hidden;}
.pr-form-header::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.07);border-radius:50%;}
.pr-form-header h4{font-size:16px;font-weight:800;margin:0 0 2px;}
.pr-form-header .sub{font-size:12px;opacity:.8;}
.pf-card{background:#fff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:18px;overflow:hidden;}
.pf-card-header{display:flex;align-items:center;gap:10px;padding:12px 18px;border-bottom:1px solid #f0f2f7;background:#fafbff;}
.pf-card-header .ch-icon{width:32px;height:32px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;}
.pf-card-header h6{margin:0;font-size:13px;font-weight:700;color:#1a2340;}
.pf-card-body{padding:18px;}
.pf-label{font-size:12px;font-weight:700;color:#596579;margin-bottom:5px;display:block;}
.pf-label .req{color:#e53e3e;}
.pf-input{min-height:44px;border-color:#d7dce5;color:#303549;font-size:13px;border-radius:8px;}
.pf-input:focus{border-color:#38a169;box-shadow:0 0 0 2px rgba(56,161,105,.12);}
.section-divider{font-size:11px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.6px;padding:6px 0 10px;border-bottom:1px solid #f0f2f7;margin-bottom:14px;}
.section-earn{color:#38a169;border-color:#c6f6d5;}
.section-deduct{color:#e53e3e;border-color:#fca5a5;}
.salary-summary{background:linear-gradient(135deg,#f0fff4,#dcfce7);border-radius:10px;padding:16px 18px;border:1px solid #a7f3d0;}
.ss-row{display:flex;justify-content:space-between;align-items:center;font-size:13px;color:#596579;padding:5px 0;border-bottom:1px solid rgba(56,161,105,.1);}
.ss-row:last-child{border-bottom:none;}
.ss-row span:last-child{font-weight:700;color:#1a2340;}
.ss-total{display:flex;justify-content:space-between;align-items:center;padding:10px 0 0;margin-top:6px;border-top:2px solid #38a169;}
.ss-total .lbl{font-size:13px;font-weight:700;color:#38a169;}
.ss-total .val{font-size:20px;font-weight:800;color:#276749;}
.adv-badge{background:#fffbeb;color:#d97706;border:1px solid #fcd34d;border-radius:8px;padding:8px 14px;font-size:12px;font-weight:700;display:flex;align-items:center;gap:8px;margin-bottom:14px;}
.sticky-footer{position:sticky;bottom:0;background:#fff;border-top:2px solid #f0f2f7;padding:14px 18px;border-radius:10px 10px 0 0;box-shadow:0 -4px 16px rgba(0,0,0,.08);display:flex;justify-content:space-between;align-items:center;gap:12px;z-index:100;}
.btn-cancel-pr{background:#f4f6fb;color:#596579;border:1.5px solid #e5e8ee;border-radius:8px;padding:9px 20px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-cancel-pr:hover{background:#e8ecf3;color:#596579;text-decoration:none;}
.btn-save-pr{background:linear-gradient(135deg,#38a169,#276749);color:#fff;border:none;border-radius:8px;padding:10px 26px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(56,161,105,.35);display:inline-flex;align-items:center;gap:6px;}
.btn-save-pr:hover{box-shadow:0 6px 20px rgba(56,161,105,.45);transform:translateY(-1px);}
</style>
<div class="pcoded-inner-content pr-form-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="pr-form-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;letter-spacing:.5px;margin-bottom:5px;">
                <i class="ti-pencil"></i> Edit Payroll
            </div>
            <h4>{{ $payroll->employee_name }} — {{ $payroll->payroll_month_label }}</h4>
            <div class="sub">Update payroll record details.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('payroll') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;">
                <i class="ti-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

<form id="payrollForm" action="{{ route('payroll.update', $payroll->id) }}" method="POST" data-no-softnav>
@csrf
@method('PUT')
<div class="row">
<div class="col-lg-8">

{{-- Employee --}}
<div class="pf-card">
    <div class="pf-card-header">
        <div class="ch-icon" style="background:#eef4fd;color:#2c7be5;"><i class="ti-user"></i></div>
        <h6>Employee Details</h6>
    </div>
    <div class="pf-card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="pf-label">Driver / Employee (optional)</label>
                    <select name="driver_id" id="driverSelect" class="form-control pf-input select2" data-placeholder="— Select Driver —">
                        <option value=""></option>
                        @foreach($drivers as $d)
                        <option value="{{ $d->id }}" data-name="{{ $d->name }}" {{ $payroll->driver_id == $d->id ? 'selected':'' }}>
                            {{ $d->name }}{{ $d->mobile ? ' — '.$d->mobile : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="pf-label">Employee Name <span class="req">*</span></label>
                    <input type="text" name="employee_name" id="employeeName" class="form-control pf-input" value="{{ old('employee_name', $payroll->employee_name) }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-0">
                    <label class="pf-label">Payroll Month <span class="req">*</span></label>
                    <input type="month" name="payroll_month" class="form-control pf-input"
                        value="{{ old('payroll_month', $payroll->payroll_month->format('Y-m')) }}" required>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Earnings --}}
<div class="pf-card">
    <div class="pf-card-header">
        <div class="ch-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-plus-alt"></i></div>
        <h6>Earnings</h6>
    </div>
    <div class="pf-card-body">
        <div class="section-divider section-earn"><i class="ti-arrow-up mr-1"></i> Earnings / Allowances</div>
        <div class="row">
            @foreach(['basic_salary'=>'Basic Salary *','other_allowance'=>'Other Allowance','bonus'=>'Bonus'] as $field => $lbl)
            <div class="col-md-6">
                <div class="form-group">
                    <label class="pf-label">{{ $lbl }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text" style="min-height:44px;border-radius:8px 0 0 8px;font-weight:700;">₹</span></div>
                        <input type="number" name="{{ $field }}" id="{{ \Illuminate\Support\Str::camel($field) }}"
                            class="form-control pf-input earn-input"
                            value="{{ old($field, $payroll->$field) }}" placeholder="0.00" step="0.01" min="0"
                            {{ $field==='basic_salary' ? 'required' : '' }}
                            style="border-radius:0 8px 8px 0;">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Deductions --}}
<div class="pf-card">
    <div class="pf-card-header">
        <div class="ch-icon" style="background:#fff5f5;color:#e53e3e;"><i class="ti-minus-alt"></i></div>
        <h6>Deductions</h6>
    </div>
    <div class="pf-card-body">
        <div class="section-divider section-deduct"><i class="ti-arrow-down mr-1"></i> Deductions</div>
        <div class="row">
            @foreach(['pf'=>'PF','esi'=>'ESI','tds'=>'TDS','advance_deduction'=>'Advance Recovery','other_deduction'=>'Other Deduction'] as $field => $lbl)
            <div class="col-md-6">
                <div class="form-group">
                    <label class="pf-label">{{ $lbl }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text" style="min-height:44px;border-radius:8px 0 0 8px;font-weight:700;">₹</span></div>
                        <input type="number" name="{{ $field }}" id="{{ \Illuminate\Support\Str::camel($field) }}"
                            class="form-control pf-input ded-input"
                            value="{{ old($field, $payroll->$field) }}" placeholder="0.00" step="0.01" min="0"
                            style="border-radius:0 8px 8px 0;">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Payment --}}
<div class="pf-card">
    <div class="pf-card-header">
        <div class="ch-icon" style="background:#eef4fd;color:#2c7be5;"><i class="ti-credit-card"></i></div>
        <h6>Payment Information</h6>
    </div>
    <div class="pf-card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="pf-label">Payment Mode</label>
                    <select name="payment_mode" id="paymentMode" class="form-control pf-input select2" data-placeholder="— Select Mode —">
                        @foreach(['cash'=>'💵 Cash','upi'=>'📱 UPI','bank'=>'🏦 Bank Transfer','cheque'=>'📄 Cheque'] as $val => $lbl)
                        <option value="{{ $val }}" {{ old('payment_mode', $payroll->payment_mode) === $val ? 'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="pf-label">Reference No.</label>
                    <input type="text" name="reference_no" class="form-control pf-input" value="{{ old('reference_no', $payroll->reference_no) }}" placeholder="UPI / Cheque No.">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-0">
                    <label class="pf-label">Payment Date</label>
                    <input type="date" name="payment_date" class="form-control pf-input" value="{{ old('payment_date', $payroll->payment_date ? $payroll->payment_date->format('Y-m-d') : '') }}">
                    <small style="font-size:11px;color:#8a94a6;">Set to mark as Paid</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Notes --}}
<div class="pf-card">
    <div class="pf-card-header">
        <div class="ch-icon" style="background:#fffbeb;color:#d97706;"><i class="ti-comment-alt"></i></div>
        <h6>Notes</h6>
    </div>
    <div class="pf-card-body">
        <textarea name="notes" class="form-control pf-input" rows="3" placeholder="Any remarks…">{{ old('notes', $payroll->notes) }}</textarea>
    </div>
</div>

</div>{{-- end col-lg-8 --}}

{{-- SIDEBAR --}}
<div class="col-lg-4">
    <div class="pf-card">
        <div class="pf-card-header">
            <div class="ch-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-receipt"></i></div>
            <h6>Salary Calculation</h6>
        </div>
        <div class="pf-card-body">
            <div class="salary-summary">
                <div class="ss-row"><span>Basic Salary</span><span>₹ <span id="sumBasic">0.00</span></span></div>
                <div class="ss-row"><span>Other Allowance</span><span>+ ₹ <span id="sumOA">0.00</span></span></div>
                <div class="ss-row"><span>Bonus</span><span>+ ₹ <span id="sumBonus">0.00</span></span></div>
                <div class="ss-row" style="font-weight:700;color:#38a169;border-top:1px solid #c6f6d5;padding-top:8px;margin-top:4px;"><span>Gross</span><span id="sumGross">₹ 0.00</span></div>
                <div class="ss-row"><span>PF</span><span>- ₹ <span id="sumPF">0.00</span></span></div>
                <div class="ss-row"><span>ESI</span><span>- ₹ <span id="sumESI">0.00</span></span></div>
                <div class="ss-row"><span>TDS</span><span>- ₹ <span id="sumTDS">0.00</span></span></div>
                <div class="ss-row"><span>Advance</span><span>- ₹ <span id="sumAdv">0.00</span></span></div>
                <div class="ss-row"><span>Other Ded.</span><span>- ₹ <span id="sumOD">0.00</span></span></div>
                <div class="ss-row" style="font-weight:700;color:#e53e3e;border-top:1px solid #fca5a5;padding-top:8px;margin-top:4px;"><span>Total Deductions</span><span id="sumTotalDed">₹ 0.00</span></div>
                <div class="ss-total"><span class="lbl">Net Pay</span><span class="val">₹ <span id="sumNet">0.00</span></span></div>
            </div>
        </div>
    </div>

    <div class="pf-card">
        <div class="pf-card-header">
            <div class="ch-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-link"></i></div>
            <h6>Navigation</h6>
        </div>
        <div class="pf-card-body" style="padding:14px 18px;">
            <ul style="list-style:none;padding:0;margin:0;font-size:13px;">
                <li style="padding:5px 0;border-bottom:1px solid #f0f2f7;">
                    <a href="{{ route('payroll') }}" style="color:#596579;text-decoration:none;"><i class="ti-money mr-2" style="color:#38a169;"></i>Payroll List</a>
                </li>
                <li style="padding:5px 0;border-bottom:1px solid #f0f2f7;">
                    <a href="{{ route('payroll.view', $payroll->id) }}" style="color:#596579;text-decoration:none;"><i class="ti-eye mr-2" style="color:#38a169;"></i>View Record</a>
                </li>
                <li style="padding:5px 0;">
                    <span style="color:#1a2340;font-weight:700;"><i class="ti-pencil mr-2" style="color:#38a169;"></i>Edit Payroll</span>
                </li>
            </ul>
        </div>
    </div>
</div>{{-- end col-lg-4 --}}
</div>{{-- end row --}}

<div class="sticky-footer">
    <div style="font-size:11px;color:#8a94a6;"><kbd style="background:#f0f2f7;padding:2px 6px;border-radius:4px;font-family:monospace;font-size:10px;border:1px solid #d7dce5;">Ctrl+S</kbd> to save</div>
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="{{ route('payroll') }}" class="btn-cancel-pr"><i class="ti-arrow-left"></i> Back</a>
        <button type="submit" class="btn-save-pr"><i class="ti-save"></i><span>Update Payroll</span></button>
    </div>
</div>

</form>
</div></div></div></div>
@endsection

@push('scripts')
<script>
function fmt(n){ return parseFloat(n||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2}); }
function recalc(){
    var basic  = parseFloat($('#basicSalary').val())||0,
        oa     = parseFloat($('#otherAllowance').val())||0,
        bonus  = parseFloat($('#bonus').val())||0,
        gross  = basic+oa+bonus,
        pf     = parseFloat($('#pf').val())||0,
        esi    = parseFloat($('#esi').val())||0,
        tds    = parseFloat($('#tds').val())||0,
        adv    = parseFloat($('#advanceDeduction').val())||0,
        od     = parseFloat($('#otherDeduction').val())||0,
        totalDed = pf+esi+tds+adv+od,
        net    = Math.max(0, gross-totalDed);
    $('#sumBasic').text(fmt(basic));
    $('#sumOA').text(fmt(oa));
    $('#sumBonus').text(fmt(bonus));
    $('#sumGross').text('₹ '+fmt(gross));
    $('#sumPF').text(fmt(pf));
    $('#sumESI').text(fmt(esi));
    $('#sumTDS').text(fmt(tds));
    $('#sumAdv').text(fmt(adv));
    $('#sumOD').text(fmt(od));
    $('#sumTotalDed').text('₹ '+fmt(totalDed));
    $('#sumNet').text(fmt(net));
}
$('.earn-input,.ded-input').on('input change', recalc);
$('#driverSelect').on('change', function(){
    var name=$(this).find('option:selected').data('name')||'';
    if(name) $('#employeeName').val(name);
});
recalc();
$(document).on('keydown',function(e){ if(e.ctrlKey&&e.key==='s'){e.preventDefault();$('#payrollForm').submit();} });
</script>
@endpush
