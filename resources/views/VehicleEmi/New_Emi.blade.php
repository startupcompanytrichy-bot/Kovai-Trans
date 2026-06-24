@extends('layouts.app')

@section('content')
<style>
.emi-form-page{background:#f4f6fb;}
.emi-form-header{background:linear-gradient(135deg,#1a2340 0%,#3730a3 60%,#4338ca 100%);border-radius:14px;padding:20px 26px;color:#fff;margin-bottom:20px;position:relative;overflow:hidden;}
.emi-form-header::before{content:'';position:absolute;top:-40px;right:-40px;width:160px;height:160px;background:rgba(255,255,255,.06);border-radius:50%;}
.emi-form-header h4{font-size:20px;font-weight:800;margin:0 0 3px;}
.emi-form-header .sub{font-size:12px;opacity:.8;}
/* cards */
.ef-card{background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:18px;overflow:hidden;}
.ef-card-header{display:flex;align-items:center;gap:10px;padding:12px 18px;border-bottom:1px solid #f0f2f7;background:#fafbff;}
.ef-ch-icon{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;}
.ef-card-header h6{margin:0;font-size:13px;font-weight:700;color:#1a2340;}
.ef-card-body{padding:18px 20px;}
/* inputs */
.ef-label{font-size:11px;font-weight:700;color:#596579;margin-bottom:5px;display:block;text-transform:uppercase;letter-spacing:.3px;}
.ef-label .req{color:#e53e3e;}
.ef-input{min-height:42px;border-color:#d7dce5;color:#303549;font-size:13px;border-radius:8px;}
.ef-input:focus{border-color:#4338ca;box-shadow:0 0 0 2px rgba(67,56,202,.1);}
/* select2 */
.ef-s2 .select2-container--default .select2-selection--single{height:42px!important;border-color:#d7dce5!important;border-radius:8px!important;}
.ef-s2 .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:42px!important;font-size:13px!important;padding-left:10px!important;}
.ef-s2 .select2-container--default .select2-selection--single .select2-selection__arrow{height:40px!important;}
/* prefix */
.ef-prefix{min-height:42px;background:#f1f3f8;border:1px solid #d7dce5;border-right:none;border-radius:8px 0 0 8px;padding:0 11px;font-size:13px;font-weight:700;color:#596579;display:flex;align-items:center;}
.ef-suffix{min-height:42px;background:#f1f3f8;border:1px solid #d7dce5;border-left:none;border-radius:0 8px 8px 0;padding:0 11px;font-size:13px;font-weight:700;color:#596579;display:flex;align-items:center;}
.ef-input.pfx{border-radius:0 8px 8px 0!important;}
.ef-input.sfx{border-radius:8px 0 0 8px!important;}
/* section divider */
.ef-section-divider{font-size:10px;font-weight:800;color:#8a94a6;text-transform:uppercase;letter-spacing:.8px;padding:4px 0 8px;border-bottom:1px solid #f0f2f7;margin-bottom:14px;}
/* summary calc box */
.calc-box{background:linear-gradient(135deg,#eef2ff,#f4f7ff);border:1px solid #c7d2fe;border-radius:10px;padding:14px;}
.calc-row{display:flex;justify-content:space-between;align-items:center;padding:5px 0;font-size:12px;}
.calc-row:not(:last-child){border-bottom:1px dashed #e0e7ff;}
.calc-label{color:#596579;font-weight:600;}
.calc-val{font-weight:800;color:#1a2340;}
/* schedule table */
.sch-wrap{overflow-x:auto;max-height:380px;overflow-y:auto;}
.sch-wrap::-webkit-scrollbar{width:4px;height:4px;}
.sch-wrap::-webkit-scrollbar-thumb{background:#c5cde0;border-radius:4px;}
#schTable{min-width:600px;margin:0;font-size:12px;}
#schTable thead th{background:#f8fafc;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.4px;color:#14213d;border-color:#f0f2f7;padding:9px 10px;position:sticky;top:0;z-index:2;}
#schTable tbody td{padding:8px 10px;border-color:#f0f2f7;vertical-align:middle;}
#schTable tbody tr:hover td{background:#f4f7ff;}
.sch-no{width:26px;height:26px;border-radius:50%;background:#eef2ff;color:#4338ca;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;}
.sch-empty{text-align:center;padding:28px;color:#b0bac9;}
/* nav */
.ef-nav{display:flex;justify-content:space-between;align-items:center;padding:12px 20px;border-top:1px solid #f0f2f7;background:#fafbff;}
.btn-save{background:linear-gradient(135deg,#4338ca,#3730a3);color:#fff;border:none;border-radius:8px;padding:9px 22px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 3px 10px rgba(67,56,202,.3);transition:all .15s;}
.btn-save:hover{transform:translateY(-1px);box-shadow:0 5px 16px rgba(67,56,202,.45);}
/* loan type toggle */
.loan-type-btn{padding:7px 16px;border-radius:8px;border:2px solid #d7dce5;background:#fff;font-size:12px;font-weight:700;cursor:pointer;color:#596579;transition:all .15s;}
.loan-type-btn.active{border-color:#4338ca;background:#eef2ff;color:#4338ca;}
</style>

<div class="pcoded-inner-content emi-form-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="emi-form-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;margin-bottom:7px;">
                <i class="ti-plus"></i> New EMI Record
            </div>
            <h4>Add Vehicle EMI / Loan</h4>
            <div class="sub">Enter all finance statement details — schedule auto-generated month-wise.</div>
        </div>
        <div class="col-md-4 text-right" style="position:relative;z-index:1;">
            <a href="{{ route('emi') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;">
                <i class="ti-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

<form id="emiForm" action="{{ route('emi.store') }}" method="POST">
@csrf

<div class="row">
{{-- ══ LEFT COLUMN ══ --}}
<div class="col-lg-8">

{{-- Loan Type Toggle --}}
<div class="ef-card">
    <div class="ef-card-body" style="padding:14px 18px;">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <span style="font-size:12px;font-weight:700;color:#596579;">Loan Type:</span>
            <button type="button" class="loan-type-btn active" id="btnNew" onclick="setLoanType('new')">
                <i class="ti-plus mr-1"></i> New Loan
            </button>
            <button type="button" class="loan-type-btn" id="btnExisting" onclick="setLoanType('existing')">
                <i class="ti-import mr-1"></i> Existing Loan (Mid-Import)
            </button>
            <input type="hidden" name="loan_type" id="loanTypeInput" value="new">
        </div>
        <div id="existingNote" style="display:none;margin-top:10px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;">
            <i class="ti-info-alt mr-1"></i>
            <strong>Existing Loan:</strong> Enter the current state — set <em>Paid EMIs</em> and <em>Outstanding Balance</em> to match your finance statement. The schedule will show historical instalments as already paid.
        </div>
    </div>
</div>

{{-- Finance / Contract Details --}}
<div class="ef-card">
    <div class="ef-card-header">
        <div class="ef-ch-icon" style="background:#eef2ff;color:#4338ca;"><i class="ti-briefcase"></i></div>
        <h6>Finance & Contract Details</h6>
    </div>
    <div class="ef-card-body">
        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label class="ef-label">Financier / Bank <span class="req">*</span></label>
                    <input type="text" name="financier_name" class="form-control ef-input @error('financier_name') is-invalid @enderror"
                        value="{{ old('financier_name') }}" placeholder="e.g. Sundaram Finance, HDFC" required>
                    @error('financier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="ef-label">Contract No.</label>
                    <input type="text" name="contract_no" class="form-control ef-input" value="{{ old('contract_no') }}" placeholder="e.g. U001600133">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="ef-label">Agreement Date</label>
                    <input type="date" name="agreement_date" class="form-control ef-input" value="{{ old('agreement_date') }}">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Vehicle Details --}}
<div class="ef-card ef-s2">
    <div class="ef-card-header">
        <div class="ef-ch-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-car"></i></div>
        <h6>Vehicle / Asset Details</h6>
    </div>
    <div class="ef-card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="ef-label">Vehicle <span class="req">*</span></label>
                    <select name="vehicle_id" id="vehicleSelect" class="form-control ef-input @error('vehicle_id') is-invalid @enderror" required>
                        <option value="">Select Vehicle</option>
                        @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}
                            data-engine="{{ $v->engine_number }}" data-chassis="{{ $v->chassis_number }}"
                            data-regno="{{ $v->vehicle_number }}"
                            data-make="{{ $v->asset_make }}" data-type="{{ $v->asset_type }}">
                            {{ $v->vehicle_number }}{{ $v->vehicle_name ? ' — '.$v->vehicle_name : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="ef-label">Asset Make</label>
                    <input type="text" name="asset_make" id="assetMake" class="form-control ef-input" value="{{ old('asset_make') }}" placeholder="e.g. EICHER">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="ef-label">Asset Type</label>
                    <input type="text" name="asset_type" id="assetType" class="form-control ef-input" value="{{ old('asset_type') }}" placeholder="e.g. PRO 2110">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-0">
                    <label class="ef-label">Registration No.</label>
                    <input type="text" id="regNo" class="form-control ef-input" readonly style="background:#f8f9fb;color:#596579;">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-0">
                    <label class="ef-label">Engine Number</label>
                    <input type="text" id="engineNo" class="form-control ef-input" readonly style="background:#f8f9fb;color:#596579;">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-0">
                    <label class="ef-label">Chassis Number</label>
                    <input type="text" id="chassisNo" class="form-control ef-input" readonly style="background:#f8f9fb;color:#596579;">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Loan Financial Details --}}
<div class="ef-card">
    <div class="ef-card-header">
        <div class="ef-ch-icon" style="background:#fff8e1;color:#d97706;"><i class="ti-money"></i></div>
        <h6>Loan Financial Details</h6>
    </div>
    <div class="ef-card-body">

        <div class="ef-section-divider">Original Loan Amounts</div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="ef-label">Loan Amount <span class="req">*</span></label>
                    <div class="d-flex"><div class="ef-prefix">₹</div>
                    <input type="number" step="0.01" min="0" name="loan_amount" id="loanAmt"
                        class="form-control ef-input pfx @error('loan_amount') is-invalid @enderror"
                        value="{{ old('loan_amount') }}" placeholder="0.00" required>
                    </div>
                    @error('loan_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="ef-label">Original Interest Amount</label>
                    <div class="d-flex"><div class="ef-prefix">₹</div>
                    <input type="number" step="0.01" min="0" name="interest_amount" id="interestAmt"
                        class="form-control ef-input pfx" value="{{ old('interest_amount') }}" placeholder="0.00">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="ef-label">Insurance Amount</label>
                    <div class="d-flex"><div class="ef-prefix">₹</div>
                    <input type="number" step="0.01" min="0" name="insurance_amount" id="insuranceAmt"
                        class="form-control ef-input pfx" value="{{ old('insurance_amount', 0) }}" placeholder="0.00">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="ef-label">Original Amount Payable</label>
                    <div class="d-flex"><div class="ef-prefix">₹</div>
                    <input type="number" step="0.01" min="0" name="total_payable" id="totalPayable"
                        class="form-control ef-input pfx" value="{{ old('total_payable') }}" placeholder="Auto-calculated"
                        style="background:#f0f4ff;font-weight:700;">
                    </div>
                    <small class="text-muted" style="font-size:10px;">= Loan + Interest + Insurance</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="ef-label">Interest Rate (%)</label>
                    <div class="d-flex">
                    <input type="number" step="0.01" min="0" max="100" name="interest_rate" id="interestRate"
                        class="form-control ef-input sfx" value="{{ old('interest_rate') }}" placeholder="0.00">
                    <div class="ef-suffix">%</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="ef-label">Status</label>
                    <select name="status" class="form-control ef-input">
                        <option value="active" selected>Active</option>
                        <option value="closed">Closed</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="ef-section-divider mt-2">EMI Schedule</div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="ef-label">EMI Amount / Month <span class="req">*</span></label>
                    <div class="d-flex"><div class="ef-prefix">₹</div>
                    <input type="number" step="0.01" min="0" name="emi_amount" id="emiAmt"
                        class="form-control ef-input pfx @error('emi_amount') is-invalid @enderror"
                        value="{{ old('emi_amount') }}" placeholder="0.00" required>
                    </div>
                    @error('emi_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="ef-label">Total EMIs (Tenure) <span class="req">*</span></label>
                    <input type="number" min="1" name="total_emis" id="totalEmis"
                        class="form-control ef-input" value="{{ old('total_emis') }}" placeholder="e.g. 48">
                    <small class="text-muted" style="font-size:10px;">Auto-filled if start &amp; end date given</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="ef-label">No. of Original Instalments</label>
                    <input type="number" min="1" name="total_emis_original" id="totalEmisOrig"
                        class="form-control ef-input" value="{{ old('total_emis') }}" placeholder="e.g. 43"
                        style="background:#f8f9fb;color:#596579;" readonly
                        title="Auto-filled from total EMIs">
                    <small class="text-muted" style="font-size:10px;">As per finance statement</small>
                </div>
            </div>
        </div>

        <div class="ef-section-divider mt-2">Dates</div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="ef-label">Agreement Date</label>
                    <input type="date" name="agreement_date" id="agreementDate"
                        class="form-control ef-input" value="{{ old('agreement_date') }}">
                    <small class="text-muted" style="font-size:10px;">Date loan was sanctioned / signed</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="ef-label">Start Date <span class="req">*</span></label>
                    <input type="date" name="loan_start_date" id="loanStart"
                        class="form-control ef-input @error('loan_start_date') is-invalid @enderror"
                        value="{{ old('loan_start_date', date('Y-m-d')) }}" required>
                    <small class="text-muted" style="font-size:10px;">Loan disbursement / first instalment month</small>
                    @error('loan_start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="ef-label">End Date</label>
                    <input type="date" name="loan_end_date" id="loanEnd"
                        class="form-control ef-input" value="{{ old('loan_end_date') }}">
                    <small class="text-muted" style="font-size:10px;">Auto-filled from tenure (start + total EMIs)</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="ef-label">Next Due Date</label>
                    <input type="date" name="next_due_date" id="nextDueDateTop"
                        class="form-control ef-input" value="{{ old('next_due_date') }}">
                    <small class="text-muted" style="font-size:10px;">Auto-set to first unpaid instalment date</small>
                </div>
            </div>
        </div>
        {{-- Hidden fields — computed automatically, not shown to user --}}
        <input type="hidden" name="first_instalment_date" id="firstInstDate" value="{{ old('first_instalment_date') }}">
        <input type="hidden" name="last_instalment_date"  id="lastInstDate"  value="{{ old('last_instalment_date') }}">

        {{-- Existing loan fields --}}
        <div id="existingFields" style="display:none;">
            <div class="ef-section-divider mt-2" style="color:#d97706;border-color:#fde68a;">
                <i class="ti-import mr-1"></i> Existing Loan — Previous Payments
            </div>
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:12px;color:#92400e;">
                <i class="ti-info-alt mr-1"></i>
                Enter how many instalments have <strong>already been paid</strong> before this record.
                Those months will be marked <strong style="color:#276749;">✓ Paid</strong> in the schedule automatically.
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="ef-label">Paid EMIs So Far</label>
                        <input type="number" min="0" name="paid_emis" id="paidEmis"
                            class="form-control ef-input" value="{{ old('paid_emis', 0) }}" placeholder="0"
                            style="background:#f0f4ff;color:#276749;font-weight:700;" readonly>
                        <small class="text-muted" style="font-size:10px;">
                            <i class="ti-check" style="color:#38a169;"></i> Auto-counted: start month → current month
                        </small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="ef-label">Outstanding Balance</label>
                        <div class="d-flex"><div class="ef-prefix">₹</div>
                        <input type="number" step="0.01" min="0" name="outstanding_balance" id="outstandingBal"
                            class="form-control ef-input pfx" value="{{ old('outstanding_balance') }}" placeholder="Current balance">
                        </div>
                        <small class="text-muted" style="font-size:10px;">As per latest finance statement</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="ef-label">Calculated Closing Balance</label>
                        <div class="d-flex"><div class="ef-prefix">₹</div>
                        <input type="number" step="0.01" min="0" id="closingBalDisplay"
                            class="form-control ef-input pfx" placeholder="Auto-calculated"
                            style="background:#f0f4ff;color:#4338ca;font-weight:700;" readonly>
                        </div>
                        <small class="text-muted" style="font-size:10px;">= Loan − (Paid EMIs × EMI amount)</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group mb-0">
            <label class="ef-label">Notes</label>
            <textarea name="notes" rows="2" class="form-control ef-input" placeholder="e.g. Borrower code, loan reference...">{{ old('notes') }}</textarea>
        </div>
    </div>
</div>

{{-- EMI Schedule Preview --}}
<div class="ef-card">
    <div class="ef-card-header">
        <div class="ef-ch-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-list-ol"></i></div>
        <div style="flex:1;display:flex;align-items:center;justify-content:space-between;">
            <h6>Instalment Schedule Preview</h6>
            <span id="schCount" style="font-size:11px;font-weight:600;color:#8a94a6;background:#f4f6fb;padding:2px 8px;border-radius:999px;"></span>
        </div>
    </div>
    <div class="ef-card-body" style="padding:0;">
        <div style="background:#fffbeb;border-bottom:1px solid #fde68a;padding:10px 16px;font-size:11px;color:#92400e;">
            <i class="ti-info-alt mr-1"></i> Enter EMI amount + start date + total EMIs (or end date) to auto-generate the monthly schedule below.
        </div>
        <div class="sch-wrap">
            <table class="table table-bordered" id="schTable">
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center;">#</th>
                        <th>Due Date</th>
                        <th style="text-align:right;">Instalment (₹)</th>
                        <th style="text-align:right;">Balance After (₹)</th>
                        <th>Month</th>
                        <th style="text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody id="schBody">
                    <tr><td colspan="6" class="sch-empty"><i class="ti-calendar" style="font-size:24px;display:block;margin-bottom:6px;"></i>Enter loan details above to preview</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>

{{-- ══ RIGHT COLUMN ══ --}}
<div class="col-lg-4">

{{-- Summary Calc Box --}}
<div class="ef-card">
    <div class="ef-card-header">
        <div class="ef-ch-icon" style="background:#eef2ff;color:#4338ca;"><i class="ti-calculator"></i></div>
        <h6>Loan Summary</h6>
    </div>
    <div class="ef-card-body">
        <div class="calc-box">
            <div class="calc-row"><span class="calc-label">Original Loan</span><span class="calc-val" id="cLoan">₹0</span></div>
            <div class="calc-row"><span class="calc-label">Interest</span><span class="calc-val" id="cInterest">₹0</span></div>
            <div class="calc-row"><span class="calc-label">Insurance</span><span class="calc-val" id="cInsurance">₹0</span></div>
            <div class="calc-row" style="background:#eef2ff;border-radius:6px;padding:6px 8px;margin-top:4px;">
                <span class="calc-label" style="color:#4338ca;font-weight:800;">Total Payable</span>
                <span class="calc-val" style="color:#4338ca;font-size:15px;" id="cTotal">₹0</span>
            </div>
            <div class="calc-row mt-2"><span class="calc-label">Monthly EMI</span><span class="calc-val" style="color:#d97706;" id="cEmi">₹0</span></div>
            <div class="calc-row"><span class="calc-label">Tenure</span><span class="calc-val" id="cTenure">— months</span></div>
            <div class="calc-row"><span class="calc-label">Total via EMI</span><span class="calc-val" style="color:#e53e3e;" id="cEmiTotal">₹0</span></div>
        </div>
    </div>
</div>

{{-- Finance Statement Info --}}
<div class="ef-card">
    <div class="ef-card-header">
        <div class="ef-ch-icon" style="background:#fff8e1;color:#d97706;"><i class="ti-receipt"></i></div>
        <h6>Statement Fields Guide</h6>
    </div>
    <div class="ef-card-body" style="padding:14px;">
        <div style="font-size:11px;color:#596579;line-height:2.1;">
            <div><span style="background:#eef2ff;color:#4338ca;border-radius:4px;padding:1px 6px;font-weight:700;font-size:10px;">Contract No</span> — Loan reference number</div>
            <div><span style="background:#eef2ff;color:#4338ca;border-radius:4px;padding:1px 6px;font-weight:700;font-size:10px;">Agreement Date</span> — Date loan was approved</div>
            <div><span style="background:#eef2ff;color:#4338ca;border-radius:4px;padding:1px 6px;font-weight:700;font-size:10px;">Interest Amount</span> — Total interest over loan tenure</div>
            <div><span style="background:#eef2ff;color:#4338ca;border-radius:4px;padding:1px 6px;font-weight:700;font-size:10px;">Total Payable</span> — Loan + Interest + Insurance</div>
            <div><span style="background:#eef2ff;color:#4338ca;border-radius:4px;padding:1px 6px;font-weight:700;font-size:10px;">First Instalment</span> — Date of 1st EMI due</div>
            <div><span style="background:#eef2ff;color:#4338ca;border-radius:4px;padding:1px 6px;font-weight:700;font-size:10px;">Asset Make/Type</span> — Vehicle make and model</div>
            <div><span style="background:#f0fff4;color:#276749;border-radius:4px;padding:1px 6px;font-weight:700;font-size:10px;">Existing Loan</span> — Use when importing mid-loan</div>
        </div>
    </div>
</div>

</div>
</div>

<div class="ef-card">
    <div class="ef-nav">
        <a href="{{ route('emi') }}" class="btn btn-secondary btn-sm"><i class="ti-close mr-1"></i> Cancel</a>
        <button type="submit" class="btn-save" id="saveBtn"><i class="ti-save mr-1"></i> Save EMI Record</button>
    </div>
</div>

</form>
</div></div></div></div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    // Select2 for vehicle
    if($.fn.select2){ $('#vehicleSelect').select2({width:'100%'}); }
    // Select2 for status
    if($.fn.select2){ $('select[name="status"]').select2({minimumResultsForSearch:-1,width:'100%'}); }

    // On vehicle select — auto-fill reg/engine/chassis + asset make/type
    $('#vehicleSelect').on('change', function(){
        var opt = $(this).find(':selected');
        $('#regNo').val(opt.data('regno') || '');
        $('#engineNo').val(opt.data('engine') || '');
        $('#chassisNo').val(opt.data('chassis') || '');
        if(!$('#assetMake').val()) $('#assetMake').val(opt.data('make') || '');
        if(!$('#assetType').val()) $('#assetType').val(opt.data('type') || '');
    });

    // Loan type toggle
    window.setLoanType = function(type){
        $('#loanTypeInput').val(type);
        if(type === 'existing'){
            $('#btnNew').removeClass('active');
            $('#btnExisting').addClass('active');
            $('#existingFields').show();
            $('#existingNote').show();
        } else {
            $('#btnExisting').removeClass('active');
            $('#btnNew').addClass('active');
            $('#existingFields').hide();
            $('#existingNote').hide();
        }
        renderSchedule();
    };

    var MN = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    function inr(n){ return parseFloat(n||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2}); }
    function fmtDate(d){ return d.getDate().toString().padStart(2,'0')+' '+MN[d.getMonth()]+' '+d.getFullYear(); }
    function addMonths(d,n){ var x=new Date(d.getTime()); x.setMonth(x.getMonth()+n); return x; }
    function toISO(d){ return d.getFullYear()+'-'+(d.getMonth()+1).toString().padStart(2,'0')+'-'+d.getDate().toString().padStart(2,'0'); }

    // ── Loan Amount fields → auto total_payable ──────────────────────────────
    $('#loanAmt, #interestAmt, #insuranceAmt').on('input', function(){
        var l=parseFloat($('#loanAmt').val())||0;
        var i=parseFloat($('#interestAmt').val())||0;
        var ins=parseFloat($('#insuranceAmt').val())||0;
        $('#totalPayable').val((l+i+ins).toFixed(2));
        updateSummary();
        renderSchedule();
    });

    // ── Start date changed → auto end date from tenure ───────────────────────
    $('#loanStart').on('change', function(){
        var s=$('#loanStart').val(), t=parseInt($('#totalEmis').val())||0;
        if(s && t>0){
            var end=addMonths(new Date(s), t);
            $('#loanEnd').val(toISO(end));
        }
        renderSchedule();
    });

    // ── End date changed → auto total EMIs from start → end ──────────────────
    $('#loanEnd').on('change', function(){
        var s=$('#loanStart').val(), e=$('#loanEnd').val();
        if(s && e){
            var sd=new Date(s), ed=new Date(e);
            var m=(ed.getFullYear()-sd.getFullYear())*12+(ed.getMonth()-sd.getMonth());
            if(m>0){ $('#totalEmis').val(m); $('#totalEmisOrig').val(m); }
        }
        renderSchedule();
    });

    // ── Total EMIs changed → auto end date, auto last instalment ─────────────
    $('#totalEmis').on('input', function(){
        var s=$('#loanStart').val(), t=parseInt($(this).val())||0;
        if(s && t>0){
            // End date = start + t months
            var end=addMonths(new Date(s), t);
            $('#loanEnd').val(toISO(end));
        }
        $('#totalEmisOrig').val($(this).val());
        updateClosingBalance();
        renderSchedule();
    });

    // ── Paid EMIs / EMI amount changed → update closing balance ──────────────
    $('#paidEmis, #emiAmt').on('input', function(){
        updateClosingBalance();
        updateSummary();
        renderSchedule();
    });

    $('#interestRate').on('input', function(){ updateSummary(); });

    function updateClosingBalance(){
        var loan=parseFloat($('#loanAmt').val())||0;
        var emi=parseFloat($('#emiAmt').val())||0;
        var paid=parseInt($('#paidEmis').val())||0;
        var closing=Math.max(0, loan-(emi*paid));
        $('#closingBalDisplay').val(closing.toFixed(2));
        if(!$('#outstandingBal').val()) $('#outstandingBal').val(closing.toFixed(2));
    }

    function updateSummary(){
        var l=parseFloat($('#loanAmt').val())||0;
        var i=parseFloat($('#interestAmt').val())||0;
        var ins=parseFloat($('#insuranceAmt').val())||0;
        var tp=parseFloat($('#totalPayable').val())||0;
        var emi=parseFloat($('#emiAmt').val())||0;
        var t=parseInt($('#totalEmis').val())||0;
        $('#cLoan').text('₹'+inr(l));
        $('#cInterest').text('₹'+inr(i));
        $('#cInsurance').text('₹'+inr(ins));
        $('#cTotal').text('₹'+inr(tp||(l+i+ins)));
        $('#cEmi').text('₹'+inr(emi));
        $('#cTenure').text(t ? t+' months' : '— months');
        $('#cEmiTotal').text('₹'+inr(emi*t));
    }

    function renderSchedule(){
        var emi    = parseFloat($('#emiAmt').val())||0;
        var total  = parseInt($('#totalEmis').val())||0;
        var startVal = $('#loanStart').val();
        var loan   = parseFloat($('#loanAmt').val())||0;
        var paid   = parseInt($('#paidEmis').val())||0;
        var isExisting = ($('#loanTypeInput').val()==='existing');

        if(!emi || !total || !startVal){
            $('#schBody').html('<tr><td colspan="6" class="sch-empty"><i class="ti-calendar" style="font-size:26px;display:block;margin-bottom:8px;"></i>Enter EMI amount, start date &amp; total EMIs to preview</td></tr>');
            $('#schCount').text('');
            return;
        }

        // First due = start + 1 month (always computed, not user-entered)
        var dueStart = addMonths(new Date(startVal), 1);

        // Store hidden fields for controller
        $('#firstInstDate').val(toISO(dueStart));
        $('#lastInstDate').val(toISO(addMonths(dueStart, total-1)));

        var today   = new Date(); today.setHours(0,0,0,0);
        var todayYM = today.getFullYear()*100 + today.getMonth();

        // For existing loans: auto-count paid = all months from dueStart up to
        // and INCLUDING current month (start month → current month all paid)
        var autoPaid = 0;
        if(isExisting){
            for(var k=0; k<total; k++){
                var kDt  = addMonths(dueStart, k);
                var kYM  = kDt.getFullYear()*100 + kDt.getMonth();
                if(kYM <= todayYM){ autoPaid++; } else { break; }
            }
            // Keep within total
            autoPaid = Math.min(autoPaid, total);
            // Sync the paid EMIs input so the value is saved correctly
            $('#paidEmis').val(autoPaid);
            updateClosingBalance();
        }
        var effectivePaid = isExisting ? autoPaid : (parseInt($('#paidEmis').val())||0);

        // Next Due Date = first instalment AFTER the paid ones
        var firstUnpaidDue = addMonths(dueStart, effectivePaid);
        $('#nextDueDateTop').val(toISO(firstUnpaidDue));

        var balance = loan;
        var html = '', paidCount = 0, overdueCount = 0;

        for(var i=1; i<=total; i++){
            var dt    = addMonths(dueStart, i-1);
            balance   = Math.max(0, balance-emi);
            var dtYM  = dt.getFullYear()*100 + dt.getMonth();
            var mnLbl = MN[dt.getMonth()]+' '+dt.getFullYear();

            // For existing loans: all months from start up to current month = Paid
            var isPaidHist = isExisting && (dtYM <= todayYM) && (i <= effectivePaid);
            var statusHtml, rowStyle='', paidNote='';

            if(isPaidHist){
                paidCount++;
                statusHtml = '<span style="font-size:10px;font-weight:700;background:#f0fff4;color:#276749;border:1px solid #b2f5cc;border-radius:12px;padding:2px 8px;white-space:nowrap;">✓ Paid</span>';
                rowStyle   = 'background:#f9fffb;';
                paidNote   = '<div style="font-size:10px;color:#38a169;margin-top:2px;">Already paid</div>';
            } else if(dtYM < todayYM){
                overdueCount++;
                var daysOver = Math.round((today - dt)/86400000);
                statusHtml = '<span style="font-size:10px;font-weight:700;background:#fff5f5;color:#c53030;border:1px solid #fed7d7;border-radius:12px;padding:2px 8px;white-space:nowrap;">⚠ Overdue</span>';
                rowStyle   = 'background:#fff9f9;';
                paidNote   = '<div style="font-size:10px;color:#e53e3e;margin-top:2px;">'+daysOver+' days overdue</div>';
            } else if(dtYM === todayYM){
                statusHtml = '<span style="font-size:10px;font-weight:700;background:#fffbeb;color:#b45309;border:1px solid #fde68a;border-radius:12px;padding:2px 8px;white-space:nowrap;">⏰ Current</span>';
                rowStyle   = 'background:#fffef5;';
                paidNote   = '<div style="font-size:10px;color:#b45309;margin-top:2px;">Due this month</div>';
            } else {
                var daysLeft = Math.round((dt-today)/86400000);
                statusHtml = '<span style="font-size:10px;font-weight:700;background:#f4f6fb;color:#8a94a6;border:1px solid #e2e8f0;border-radius:12px;padding:2px 8px;white-space:nowrap;">Pending</span>';
                paidNote   = daysLeft<=30 ? '<div style="font-size:10px;color:#4338ca;margin-top:2px;">Due in '+daysLeft+' days</div>' : '';
            }

            html += '<tr style="'+rowStyle+'">';
            html += '<td style="text-align:center;"><span class="sch-no">'+i+'</span></td>';
            html += '<td style="font-weight:700;color:#1a2340;">'+fmtDate(dt)+paidNote+'</td>';
            html += '<td style="text-align:right;font-weight:700;color:#4338ca;">'+inr(emi)+'</td>';
            html += '<td style="text-align:right;font-weight:'+(balance<=0?'800':'700')+';color:'+(balance<=0?'#38a169':'#e53e3e')+';">'+inr(balance)+'</td>';
            html += '<td style="color:#596579;font-size:11px;">'+mnLbl+'</td>';
            html += '<td style="text-align:center;">'+statusHtml+'</td>';
            html += '</tr>';
        }

        $('#schBody').html(html);
        var badge = total+' instalments';
        if(paidCount)    badge += ' &bull; <span style="color:#276749;">'+paidCount+' paid</span>';
        if(overdueCount) badge += ' &bull; <span style="color:#e53e3e;">'+overdueCount+' overdue</span>';
        $('#schCount').html(badge);
    }

    // init
    updateSummary();
    renderSchedule();

    $('#emiForm').on('submit',function(){ $('#saveBtn').prop('disabled',true).html('<i class="ti-reload mr-1"></i> Saving...'); });
});
</script>
@endpush
