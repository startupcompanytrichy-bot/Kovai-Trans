@extends('layouts.app')
@section('title', 'Edit Advance')
@section('content')
<style>
.pr-form-page{background:#f4f6fb;}
.pr-form-header{background:linear-gradient(135deg,#d97706,#b45309);border-radius:10px;padding:14px 20px;color:#fff;margin-bottom:18px;position:relative;overflow:hidden;}
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
.pf-input:focus{border-color:#d97706;box-shadow:0 0 0 2px rgba(217,119,6,.12);}
.sticky-footer{position:sticky;bottom:0;background:#fff;border-top:2px solid #f0f2f7;padding:14px 18px;border-radius:10px 10px 0 0;box-shadow:0 -4px 16px rgba(0,0,0,.08);display:flex;justify-content:space-between;align-items:center;gap:12px;z-index:100;}
.btn-cancel-pr{background:#f4f6fb;color:#596579;border:1.5px solid #e5e8ee;border-radius:8px;padding:9px 20px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-cancel-pr:hover{background:#e8ecf3;color:#596579;text-decoration:none;}
.btn-save-pr{background:linear-gradient(135deg,#d97706,#b45309);color:#fff;border:none;border-radius:8px;padding:10px 26px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(217,119,6,.35);display:inline-flex;align-items:center;gap:6px;}
.btn-save-pr:hover{box-shadow:0 6px 20px rgba(217,119,6,.45);transform:translateY(-1px);}
</style>
<div class="pcoded-inner-content pr-form-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="pr-form-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;letter-spacing:.5px;margin-bottom:5px;">
                <i class="ti-pencil"></i> Edit Advance
            </div>
            <h4>Edit Salary Advance</h4>
            <div class="sub">Update advance record details.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('payroll', ['tab' => 'advances']) }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;">
                <i class="ti-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

<form action="{{ route('payroll.advance.update', $advance->id) }}" method="POST">
@csrf
@method('PUT')
<div class="row">
<div class="col-lg-8">

    <div class="pf-card">
        <div class="pf-card-header">
            <div class="ch-icon" style="background:#fffbeb;color:#d97706;"><i class="ti-wallet"></i></div>
            <h6>Advance Details</h6>
        </div>
        <div class="pf-card-body">
            <div class="row">

                <div class="col-12">
                    <div class="form-group">
                        <label class="pf-label"><i class="ti-location-arrow mr-1" style="color:#d97706;"></i>Link to Trip <span style="font-weight:400;color:#8a94a6;">(optional)</span></label>
                        <select name="trip_id" id="advTripId" class="form-control pf-input select2" data-placeholder="— Select Trip —">
                            <option value=""></option>
                            @foreach($trips as $t)
                            <option value="{{ $t->id }}"
                                data-driver-id="{{ $t->driver_id }}"
                                data-driver-name="{{ $t->driver?->name ?? '' }}"
                                {{ $advance->trip_id == $t->id ? 'selected' : '' }}>
                                {{ $t->trip_no }}
                                @if($t->trip_date) ({{ $t->trip_date->format('d M Y') }})@endif
                                — {{ $t->from_location }} → {{ $t->to_location }}
                                @if($t->driver) · {{ $t->driver->name }}@endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="pf-label">Driver / Employee</label>
                        <select name="driver_id" id="advDriverId" class="form-control pf-input select2" data-placeholder="— Select Driver —">
                            <option value=""></option>
                            @foreach($drivers as $d)
                            <option value="{{ $d->id }}" data-name="{{ $d->name }}" {{ $advance->driver_id == $d->id ? 'selected' : '' }}>
                                {{ $d->name }}{{ $d->mobile ? ' — '.$d->mobile : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="pf-label">Employee Name <span class="req">*</span></label>
                        <input type="text" name="employee_name" id="advEmpName" class="form-control pf-input" value="{{ old('employee_name', $advance->employee_name) }}" placeholder="Full name" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="pf-label">Amount <span class="req">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text" style="min-height:44px;border-radius:8px 0 0 8px;font-weight:700;">₹</span></div>
                            <input type="number" name="amount" class="form-control pf-input" value="{{ old('amount', $advance->amount) }}" placeholder="0.00" min="1" step="0.01" required style="border-radius:0 8px 8px 0;">
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="pf-label">Advance Date <span class="req">*</span></label>
                        <input type="date" name="advance_date" class="form-control pf-input" value="{{ old('advance_date', $advance->advance_date->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="pf-label">Payment Mode</label>
                        <select name="payment_mode" class="form-control pf-input select2" data-placeholder="— Select Mode —">
                            <option value="cash"   {{ $advance->payment_mode === 'cash'   ? 'selected' : '' }}>💵 Cash</option>
                            <option value="upi"    {{ $advance->payment_mode === 'upi'    ? 'selected' : '' }}>📱 UPI</option>
                            <option value="bank"   {{ $advance->payment_mode === 'bank'   ? 'selected' : '' }}>🏦 Bank Transfer</option>
                            <option value="cheque" {{ $advance->payment_mode === 'cheque' ? 'selected' : '' }}>📄 Cheque</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="pf-label">Reference No.</label>
                        <input type="text" name="reference_no" class="form-control pf-input" value="{{ old('reference_no', $advance->reference_no) }}" placeholder="UPI / Cheque No.">
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group mb-0">
                        <label class="pf-label">Notes</label>
                        <textarea name="notes" class="form-control pf-input" rows="3" placeholder="Reason for advance…">{{ old('notes', $advance->notes) }}</textarea>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<div class="col-lg-4">

    <div class="pf-card">
        <div class="pf-card-header">
            <div class="ch-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-info-alt"></i></div>
            <h6>Recovery Status</h6>
        </div>
        <div class="pf-card-body">
            <div style="background:#f8f9fb;border-radius:8px;padding:14px;border:1px solid #e2e8f0;">
                <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;color:#596579;">
                    <span>Total Amount</span>
                    <span style="font-weight:700;color:#d97706;">₹ {{ number_format($advance->amount,2) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;color:#596579;border-top:1px solid #e2e8f0;">
                    <span>Recovered</span>
                    <span style="font-weight:700;color:#38a169;">₹ {{ number_format($advance->recovered_amount,2) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;color:#596579;border-top:1px solid #e2e8f0;">
                    <span>Pending</span>
                    <span style="font-weight:800;color:#e53e3e;">₹ {{ number_format($advance->pending_amount,2) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;color:#596579;border-top:1px solid #e2e8f0;">
                    <span>Status</span>
                    <span>{!! $advance->status_badge !!}</span>
                </div>
            </div>
            <div style="margin-top:12px;font-size:11px;color:#8a94a6;line-height:1.6;">
                <i class="ti-info-alt mr-1"></i> Recovery amounts are updated automatically when payroll deductions are saved.
            </div>
        </div>
    </div>

    <div class="pf-card">
        <div class="pf-card-header">
            <div class="ch-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-link"></i></div>
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
                    <span style="color:#1a2340;font-weight:700;"><i class="ti-wallet mr-2" style="color:#d97706;"></i>Edit Advance</span>
                </li>
            </ul>
        </div>
    </div>

</div>
</div>

<div class="sticky-footer">
    <div style="font-size:11px;color:#8a94a6;"><kbd style="background:#f0f2f7;padding:2px 6px;border-radius:4px;font-family:monospace;font-size:10px;border:1px solid #d7dce5;">Ctrl+S</kbd> to save</div>
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="{{ route('payroll', ['tab' => 'advances']) }}" class="btn-cancel-pr"><i class="ti-arrow-left"></i> Back</a>
        <button type="submit" class="btn-save-pr"><i class="ti-save"></i><span>Update Advance</span></button>
    </div>
</div>

</form>
</div></div></div></div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    /* Trip → auto-fill driver & name */
    $(document).on('select2:select', '#advTripId', function () {
        var $opt       = $(this).find('option:selected');
        var driverId   = $opt.data('driver-id') || '';
        var driverName = $opt.data('driver-name') || '';
        if (driverId) { $('#advDriverId').val(String(driverId)).trigger('change.select2'); }
        if (driverName) { $('#advEmpName').val(driverName); }
    });

    /* Driver → auto-fill name */
    $(document).on('select2:select', '#advDriverId', function () {
        var name = $(this).find('option:selected').data('name') || '';
        if (name) { $('#advEmpName').val(name); }
    });

    $(document).on('keydown', function(e){ if(e.ctrlKey && e.key==='s'){ e.preventDefault(); $('form').submit(); } });
});
</script>
@endpush