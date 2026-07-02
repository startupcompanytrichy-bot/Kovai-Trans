@extends('layouts.app')
@section('title', 'Add Payroll')
@section('content')
<style>
.pr-form-page{background:#f4f6fb;}
.pr-form-header{background:linear-gradient(135deg,#2c7be5 0%,#1a5bbf 100%);border-radius:10px;padding:14px 20px;color:#fff;margin-bottom:18px;position:relative;overflow:hidden;}
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
.pf-input:focus{border-color:#2c7be5;box-shadow:0 0 0 2px rgba(44,123,229,.12);}
.section-divider{font-size:11px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.6px;padding:6px 0 10px;border-bottom:1px solid #f0f2f7;margin-bottom:14px;}
.section-earn{color:#38a169;border-color:#c6f6d5;}
.salary-summary{background:linear-gradient(135deg,#eef4fd,#ddeaf9);border-radius:10px;padding:16px 18px;border:1px solid #c5d9f5;}
.ss-row{display:flex;justify-content:space-between;align-items:center;font-size:13px;color:#596579;padding:5px 0;border-bottom:1px solid rgba(44,123,229,.1);}
.ss-row:last-child{border-bottom:none;}
.ss-row span:last-child{font-weight:700;color:#1a2340;}
.ss-total{display:flex;justify-content:space-between;align-items:center;padding:10px 0 0;margin-top:6px;border-top:2px solid #2c7be5;}
.ss-total .lbl{font-size:13px;font-weight:700;color:#2c7be5;}
.ss-total .val{font-size:20px;font-weight:800;color:#1a5bbf;}
.adv-badge{background:#fffbeb;color:#d97706;border:1px solid #fcd34d;border-radius:8px;padding:8px 14px;font-size:12px;font-weight:700;display:flex;align-items:center;gap:8px;margin-bottom:14px;}
.sticky-footer{position:sticky;bottom:0;background:#fff;border-top:2px solid #f0f2f7;padding:14px 18px;border-radius:10px 10px 0 0;box-shadow:0 -4px 16px rgba(0,0,0,.08);display:flex;justify-content:space-between;align-items:center;gap:12px;z-index:100;}
.btn-cancel-pr{background:#f4f6fb;color:#596579;border:1.5px solid #e5e8ee;border-radius:8px;padding:9px 20px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-cancel-pr:hover{background:#e8ecf3;color:#596579;text-decoration:none;}
.btn-save-pr{background:linear-gradient(135deg,#2c7be5,#1a5bbf);color:#fff;border:none;border-radius:8px;padding:10px 26px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(44,123,229,.35);display:inline-flex;align-items:center;gap:6px;}
.btn-save-pr:hover{box-shadow:0 6px 20px rgba(44,123,229,.45);transform:translateY(-1px);}
</style>
<div class="pcoded-inner-content pr-form-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="pr-form-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;letter-spacing:.5px;margin-bottom:5px;">
                <i class="ti-plus"></i> New Payroll
            </div>
            <h4>Add Payroll Record</h4>
            <div class="sub">Calculate and save employee salary for a specific month.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('payroll') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;">
                <i class="ti-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

<form id="payrollForm" action="{{ route('payroll.store') }}" method="POST">
@csrf
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
                        <option value="{{ $d->id }}" data-name="{{ $d->name }}" {{ old('driver_id') == $d->id ? 'selected':'' }}>
                            {{ $d->name }}{{ $d->mobile ? ' — '.$d->mobile : '' }}
                        </option>
                        @endforeach
                    </select>
                    <small style="color:#8a94a6;font-size:11px;margin-top:3px;display:block;">Leave blank for non-driver staff</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="pf-label">Employee Name <span class="req">*</span></label>
                    <input type="text" name="employee_name" id="employeeName"
                        class="form-control pf-input @error('employee_name') is-invalid @enderror"
                        value="{{ old('employee_name') }}" placeholder="Full name" required>
                    @error('employee_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-0">
                    <label class="pf-label">Payroll Month <span class="req">*</span></label>
                    <input type="month" name="payroll_month"
                        class="form-control pf-input @error('payroll_month') is-invalid @enderror"
                        value="{{ old('payroll_month', date('Y-m')) }}" required>
                    @error('payroll_month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="pf-label">Outstanding Advance</label>
                    <div id="advanceBadge" class="adv-badge" style="display:none;">
                        <i class="ti-wallet"></i> Pending Advance: <strong id="advanceAmt">₹ 0</strong>
                    </div>
                    <div id="noAdvanceBadge" style="font-size:12px;color:#8a94a6;padding:8px 0;"><i class="ti-check mr-1" style="color:#38a169;"></i>No pending advance</div>
                </div>
            </div>
        </div>
        <div id="advanceCheckboxWrap" style="display:none;">
            <div class="row">
                <div class="col-12">
                    <div class="form-group mb-0">
                        <label class="pf-label">Select Advances to Deduct</label>
                        <div id="advanceCheckboxList" style="border:1px solid #e2e8f0;border-radius:8px;padding:10px 14px;background:#fafbff;max-height:180px;overflow-y:auto;"></div>
                        <div style="margin-top:8px;display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:#1a2340;">
                            <span>Total to deduct:</span>
                            <span style="color:#d97706;font-size:15px;" id="advTotalDeduct">₹ 0.00</span>
                        </div>
                        <input type="hidden" name="advance_deduction" id="advanceDeduction" value="0">
                    </div>
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
            <div class="col-md-6">
                <div class="form-group">
                    <label class="pf-label">Basic Salary <span class="req">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text" style="min-height:44px;border-radius:8px 0 0 8px;font-weight:700;">₹</span></div>
                        <input type="number" name="basic_salary" id="basicSalary" class="form-control pf-input" value="{{ old('basic_salary',0) }}" placeholder="0.00" step="0.01" min="0" required style="border-radius:0 8px 8px 0;">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="pf-label">Other Allowance</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text" style="min-height:44px;border-radius:8px 0 0 8px;font-weight:700;">₹</span></div>
                        <input type="number" name="other_allowance" id="otherAllowance" class="form-control pf-input" value="{{ old('other_allowance',0) }}" placeholder="0.00" step="0.01" min="0" style="border-radius:0 8px 8px 0;">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-0">
                    <label class="pf-label">Bonus / Incentive</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text" style="min-height:44px;border-radius:8px 0 0 8px;font-weight:700;">₹</span></div>
                        <input type="number" name="bonus" id="bonus" class="form-control pf-input" value="{{ old('bonus',0) }}" placeholder="0.00" step="0.01" min="0" style="border-radius:0 8px 8px 0;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Payment Info --}}
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
                        <option value="cash"   {{ old('payment_mode','cash') === 'cash'   ? 'selected':'' }}>💵 Cash</option>
                        <option value="upi"    {{ old('payment_mode') === 'upi'    ? 'selected':'' }}>📱 UPI</option>
                        <option value="bank"   {{ old('payment_mode') === 'bank'   ? 'selected':'' }}>🏦 Bank Transfer</option>
                        <option value="cheque" {{ old('payment_mode') === 'cheque' ? 'selected':'' }}>📄 Cheque</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="pf-label">Reference No.</label>
                    <input type="text" name="reference_no" class="form-control pf-input" value="{{ old('reference_no') }}" placeholder="UPI/Cheque No.">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-0">
                    <label class="pf-label">Payment Date</label>
                    <input type="date" name="payment_date" class="form-control pf-input" value="{{ old('payment_date') }}">
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
        <textarea name="notes" class="form-control pf-input" rows="3" placeholder="Any remarks about this payroll entry…">{{ old('notes') }}</textarea>
    </div>
</div>

</div>{{-- end col-lg-8 --}}

{{-- SIDEBAR --}}
<div class="col-lg-4">

    {{-- Net Pay Summary --}}
    <div class="pf-card">
        <div class="pf-card-header">
            <div class="ch-icon" style="background:#eef4fd;color:#2c7be5;"><i class="ti-receipt"></i></div>
            <h6>Salary Calculation</h6>
        </div>
        <div class="pf-card-body">
            <div class="salary-summary">
                <div class="ss-row"><span>Basic Salary</span><span>₹ <span id="sumBasic">0.00</span></span></div>
                <div class="ss-row"><span>Other Allowance</span><span>+ ₹ <span id="sumOA">0.00</span></span></div>
                <div class="ss-row"><span>Bonus</span><span>+ ₹ <span id="sumBonus">0.00</span></span></div>
                <div class="ss-row" style="font-weight:700;color:#38a169;border-top:1px solid #c6f6d5;padding-top:8px;margin-top:4px;">
                    <span>Gross Salary</span><span id="sumGross">₹ 0.00</span>
                </div>
                <div class="ss-row" style="color:#e53e3e;border-top:1px solid #f0f2f7;padding-top:6px;margin-top:4px;">
                    <span>Advance Deduction</span><span>- ₹ <span id="sumAdv">0.00</span></span>
                </div>
                <div class="ss-total">
                    <span class="lbl">Net Pay</span>
                    <span class="val">₹ <span id="sumNet">0.00</span></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tips --}}
    <div class="pf-card">
        <div class="pf-card-header">
            <div class="ch-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-info-alt"></i></div>
            <h6>Quick Tips</h6>
        </div>
        <div class="pf-card-body">
            <div style="font-size:12px;color:#8a94a6;line-height:2;">
                <div><i class="ti-check mr-1" style="color:#38a169;"></i> Select a driver to auto-fill name</div>
                <div><i class="ti-check mr-1" style="color:#38a169;"></i> Check which advances to deduct when a driver is selected</div>
                <div><i class="ti-check mr-1" style="color:#38a169;"></i> Set payment date to mark record as Paid</div>
                <div><i class="ti-check mr-1" style="color:#38a169;"></i> Net = Gross &minus; Advance Deduction</div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="pf-card">
        <div class="pf-card-header">
            <div class="ch-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-link"></i></div>
            <h6>Navigation</h6>
        </div>
        <div class="pf-card-body" style="padding:14px 18px;">
            <ul style="list-style:none;padding:0;margin:0;font-size:13px;">
                <li style="padding:5px 0;border-bottom:1px solid #f0f2f7;">
                    <a href="{{ route('dashboard') }}" style="color:#596579;text-decoration:none;"><i class="feather icon-home mr-2" style="color:#2c7be5;"></i>Dashboard</a>
                </li>
                <li style="padding:5px 0;border-bottom:1px solid #f0f2f7;">
                    <a href="{{ route('payroll') }}" style="color:#596579;text-decoration:none;"><i class="ti-money mr-2" style="color:#2c7be5;"></i>Payroll List</a>
                </li>
                <li style="padding:5px 0;">
                    <span style="color:#1a2340;font-weight:700;"><i class="ti-plus mr-2" style="color:#2c7be5;"></i>Add Payroll</span>
                </li>
            </ul>
        </div>
    </div>

</div>{{-- end col-lg-4 --}}
</div>{{-- end row --}}

{{-- STICKY FOOTER --}}
<div class="sticky-footer">
    <div style="font-size:11px;color:#8a94a6;"><kbd style="background:#f0f2f7;padding:2px 6px;border-radius:4px;font-family:monospace;font-size:10px;border:1px solid #d7dce5;">Ctrl+S</kbd> to save</div>
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="{{ route('payroll') }}" class="btn-cancel-pr"><i class="ti-arrow-left"></i> Back</a>
        <button type="submit" class="btn-save-pr"><i class="ti-save"></i><span>Save Payroll</span></button>
    </div>
</div>

</form>
</div></div></div></div>

@push('scripts')
<script>
var advanceApiUrl = '{{ route('payroll.driver.advance', '__ID__') }}';

function fmt(n) {
    return parseFloat(n||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
}

function advanceTotal() {
    var total = 0;
    $('#advanceCheckboxList input:checked').each(function () {
        total += parseFloat($(this).data('pending')) || 0;
    });
    $('#advTotalDeduct').text('₹ ' + fmt(total));
    $('#advanceDeduction').val(total.toFixed(2));
    recalc();
}

function renderAdvanceCheckboxes(items) {
    var html = '';
    $.each(items, function (i, a) {
        html += '<label style="display:flex;align-items:center;gap:10px;padding:5px 0;border-bottom:1px solid #f0f2f7;cursor:pointer;">';
        html += '<input type="checkbox" class="adv-checkbox" data-pending="' + a.pending + '" checked style="width:16px;height:16px;accent-color:#d97706;">';
        html += '<span style="font-size:12px;color:#596579;flex:1;">' + a.date + '</span>';
        html += '<span style="font-size:12px;color:#8a94a6;">₹ ' + fmt(a.amount) + '</span>';
        html += '<span style="font-size:13px;font-weight:700;color:#d97706;min-width:70px;text-align:right;">₹ ' + fmt(a.pending) + '</span>';
        html += '</label>';
    });
    $('#advanceCheckboxList').html(html);
    $('#advanceCheckboxList input').on('change', advanceTotal);
    advanceTotal();
}

function recalc() {
    var basic  = parseFloat($('#basicSalary').val())||0;
    var oa     = parseFloat($('#otherAllowance').val())||0;
    var bonus  = parseFloat($('#bonus').val())||0;
    var gross  = basic + oa + bonus;
    var adv    = parseFloat($('#advanceDeduction').val())||0;
    var net    = Math.max(0, gross - adv);

    $('#sumBasic').text(fmt(basic));
    $('#sumOA').text(fmt(oa));
    $('#sumBonus').text(fmt(bonus));
    $('#sumGross').text('₹ ' + fmt(gross));
    $('#sumAdv').text(fmt(adv));
    $('#sumNet').text(fmt(net));
}

$('#basicSalary,#otherAllowance,#bonus').on('input change', recalc);

$('#driverSelect').on('change', function () {
    var driverId = $(this).val();
    var name = $(this).find('option:selected').data('name') || '';
    if (name) { $('#employeeName').val(name); }

    if (driverId) {
        $.get(advanceApiUrl.replace('__ID__', driverId), function (res) {
            var bal = parseFloat(res.balance) || 0;
            if (bal > 0 && res.items && res.items.length) {
                $('#advanceBadge').show().find('#advanceAmt').text('₹ ' + fmt(bal));
                $('#noAdvanceBadge').hide();
                $('#advanceCheckboxWrap').show();
                renderAdvanceCheckboxes(res.items);
            } else {
                $('#advanceBadge').hide();
                $('#noAdvanceBadge').show();
                $('#advanceCheckboxWrap').hide();
                $('#advanceDeduction').val(0);
                recalc();
            }
        });
    } else {
        $('#advanceBadge').hide();
        $('#noAdvanceBadge').show();
        $('#advanceCheckboxWrap').hide();
        $('#advanceDeduction').val(0);
        recalc();
    }
});

recalc();

$(document).on('keydown', function(e){ if(e.ctrlKey && e.key==='s'){ e.preventDefault(); $('#payrollForm').submit(); } });
</script>
@endpush
@endsection
