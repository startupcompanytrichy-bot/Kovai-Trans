@extends('layouts.app')

@section('content')

<style>
/* ── New Company Page — Trip-style UI ─────────────────────────────── */
.nc-page { background: #f4f6fb; }

.nc-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px; padding: 16px 22px; color: #fff;
    margin-bottom: 20px; position: relative; overflow: hidden;
}
.nc-header::before {
    content:''; position:absolute; top:-40px; right:-40px;
    width:160px; height:160px; background:rgba(255,255,255,.07); border-radius:50%;
}
.nc-header::after {
    content:''; position:absolute; bottom:-30px; right:60px;
    width:100px; height:100px; background:rgba(255,255,255,.05); border-radius:50%;
}
.nc-header h4 { font-size:18px; font-weight:800; margin:0 0 3px; position:relative;z-index:1; }
.nc-header .sub { font-size:12px; opacity:.8; position:relative;z-index:1; }
.nc-header .badge-tag {
    display:inline-flex; align-items:center; gap:6px;
    background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.3);
    border-radius:20px; padding:3px 12px; font-size:11px; font-weight:700;
    letter-spacing:.5px; margin-bottom:8px; position:relative;z-index:1;
}

/* ── Progress bar ─────────────────────────────────────────────────── */
.nc-progress { height:4px; background:#e2e8f0; border-radius:2px; margin-bottom:20px; overflow:hidden; }
.nc-progress-fill { height:100%; background:linear-gradient(90deg,#667eea,#764ba2); border-radius:2px; transition:width .4s ease; }

/* ── Step indicator ───────────────────────────────────────────────── */
.nc-steps {
    display:flex; align-items:center; background:#fff;
    border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06);
    overflow:hidden; margin-bottom:20px;
}
.nc-step {
    flex:1; display:flex; align-items:center; gap:10px;
    padding:14px 16px; cursor:pointer; transition:all .2s;
    border-right:1px solid #f0f2f7;
}
.nc-step:last-child { border-right:none; }
.nc-step .step-num {
    width:30px; height:30px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:13px; font-weight:800; background:#f0f2f7; color:#b0bac9; transition:all .2s;
}
.nc-step .step-label { font-size:12px; font-weight:600; color:#8a94a6; }
.nc-step .step-sub   { font-size:10px; color:#b0bac9; }
.nc-step.active .step-num { background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; box-shadow:0 3px 10px rgba(102,126,234,.4); }
.nc-step.active .step-label { color:#1a2340; }
.nc-step.done .step-num { background:#48bb78; color:#fff; }
.nc-step.done .step-label { color:#38a169; }
.nc-step:hover:not(.active) { background:#f8faff; }

/* ── Section card ─────────────────────────────────────────────────── */
.nc-card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); margin-bottom:20px; overflow:hidden; display:none; }
.nc-card.active { display:block; }
.nc-card-header {
    display:flex; align-items:center; gap:10px;
    padding:16px 20px; border-bottom:1px solid #f0f2f7; background:#fafbff;
}
.nc-card-header .ch-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:16px; }
.nc-card-header h6 { margin:0; font-size:14px; font-weight:700; color:#1a2340; }
.nc-card-header .ch-sub { font-size:11px; color:#8a94a6; margin:0; }
.nc-card-body { padding:22px; }

/* ── Section divider ──────────────────────────────────────────────── */
.nc-section-title {
    display:flex; align-items:center; gap:8px;
    margin:20px 0 14px; padding:10px 12px;
    border-left:3px solid #667eea; background:#f7f8fc;
    color:#303549; font-size:13px; font-weight:800; border-radius:0 6px 6px 0;
}
.nc-section-title:first-child { margin-top:0; }

/* ── Form fields ──────────────────────────────────────────────────── */
.nc-label { font-size:12px; font-weight:700; color:#596579; margin-bottom:6px; display:block; }
.nc-label .req { color:#e53e3e; }
.nc-input {
    min-height:44px; border-color:#d7dce5; color:#303549;
    font-size:14px; border-radius:8px; transition:border-color .2s,box-shadow .2s;
}
.nc-input:focus { border-color:#667eea; box-shadow:0 0 0 2px rgba(102,126,234,.12); }
.nc-input-group { display:flex; align-items:stretch; }
.nc-addon {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:42px; padding:0 12px; border:1px solid #d7dce5;
    background:#f8f9fa; color:#495057; font-weight:600; font-size:13px; min-height:44px;
}
.nc-addon:first-child { border-right:0; border-radius:8px 0 0 8px; }
.nc-addon:last-child  { border-left:0; border-radius:0 8px 8px 0; }
.nc-input-group .nc-input { border-radius:0; flex:1; }
.nc-input-group .nc-addon:first-child + .nc-input { border-radius:0 8px 8px 0; }
.nc-input-group .nc-input:not(:last-child) { border-radius:8px 0 0 8px; }

/* ── Bank detail card highlight ───────────────────────────────────── */
.bank-highlight {
    background:linear-gradient(135deg,#f0f4ff,#e8f0fe);
    border:1px solid #c7d7f5; border-radius:10px;
    padding:16px; margin-bottom:20px;
}
.bank-highlight .bank-icon-wrap {
    width:44px; height:44px; border-radius:10px;
    background:linear-gradient(135deg,#667eea,#764ba2);
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:20px; flex-shrink:0;
}

/* ── Logo upload ──────────────────────────────────────────────────── */
.nc-upload-zone {
    border:2px dashed #c0d4f5; border-radius:10px;
    padding:28px; text-align:center; cursor:pointer;
    transition:all .2s; background:#f8fbff; min-height:120px;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
}
.nc-upload-zone:hover { border-color:#667eea; background:#eef2ff; }
.nc-upload-zone i { font-size:36px; color:#c0d4f5; margin-bottom:8px; }
.nc-upload-zone.has-preview { padding:10px; }
.nc-upload-zone.has-preview i { display:none; }
.nc-upload-zone .uz-title { font-size:13px; font-weight:600; color:#667eea; }
.nc-upload-zone .uz-sub { font-size:11px; color:#b0bac9; }
.logo-preview-img { max-height:120px; max-width:100%; border-radius:8px; display:none; }

/* ── Nav buttons ──────────────────────────────────────────────────── */
.nc-nav { display:flex; justify-content:space-between; align-items:center; padding:16px 22px; border-top:1px solid #f0f2f7; background:#fafbff; }
.nc-btn-prev { background:#f4f6fb; color:#596579; border:1.5px solid #e2e8f0; border-radius:8px; padding:9px 20px; font-size:13px; font-weight:600; cursor:pointer; transition:all .15s; }
.nc-btn-prev:hover { background:#e2e8f0; }
.nc-btn-next { background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; border:none; border-radius:8px; padding:9px 24px; font-size:13px; font-weight:700; cursor:pointer; box-shadow:0 3px 12px rgba(102,126,234,.35); transition:all .15s; }
.nc-btn-next:hover { box-shadow:0 5px 18px rgba(102,126,234,.5); transform:translateY(-1px); }
.nc-btn-submit { background:linear-gradient(135deg,#48bb78,#38a169); color:#fff; border:none; border-radius:8px; padding:9px 24px; font-size:13px; font-weight:700; cursor:pointer; box-shadow:0 3px 12px rgba(72,187,120,.35); transition:all .15s; }
.nc-btn-submit:hover { box-shadow:0 5px 18px rgba(72,187,120,.5); transform:translateY(-1px); }

/* ── Select2 overrides ────────────────────────────────────────────── */
.nc-card .select2-container { width:100% !important; }
.nc-card .select2-container--default .select2-selection--single { min-height:44px !important; height:44px !important; border-color:#d7dce5 !important; border-radius:8px !important; }
.nc-card .select2-container--default.select2-container--focus .select2-selection--single,
.nc-card .select2-container--default.select2-container--open .select2-selection--single { border-color:#667eea !important; box-shadow:0 0 0 2px rgba(102,126,234,.12) !important; }

@media(max-width:767.98px) {
    .nc-steps { flex-direction:column; }
    .nc-step { border-right:none; border-bottom:1px solid #f0f2f7; }
    .nc-step:last-child { border-bottom:none; }
    .nc-header { padding:12px 14px; }
    .nc-header h4 { font-size:15px; }
}
</style>

<div class="pcoded-inner-content nc-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

{{-- ── HEADER ─────────────────────────────────────────────────── --}}
<div class="nc-header">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="badge-tag"><i class="ti-plus"></i> New Company</div>
            <h4>Create New Company</h4>
            <div class="sub">Fill in all sections to register a complete company profile.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('company') }}" class="btn btn-sm"
               style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:8px 18px;font-weight:600;">
                <i class="ti-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

{{-- ── PROGRESS BAR ─────────────────────────────────────────── --}}
<div class="nc-progress"><div class="nc-progress-fill" id="ncProgressFill" style="width:25%;"></div></div>

{{-- ── STEP INDICATORS ──────────────────────────────────────── --}}
<div class="nc-steps" id="ncSteps">
    <div class="nc-step active" data-step="1">
        <div class="step-num">1</div>
        <div><div class="step-label">Company Info</div><div class="step-sub">Identity & Type</div></div>
    </div>
    <div class="nc-step" data-step="2">
        <div class="step-num">2</div>
        <div><div class="step-label">Tax & Contact</div><div class="step-sub">PAN, GST & Phone</div></div>
    </div>
    <div class="nc-step" data-step="3">
        <div class="step-num">3</div>
        <div><div class="step-label">Bank Details</div><div class="step-sub">Account & IFSC</div></div>
    </div>
    <div class="nc-step" data-step="4">
        <div class="step-num">4</div>
        <div><div class="step-label">Address & Logo</div><div class="step-sub">Location & Media</div></div>
    </div>
</div>

<form method="POST" action="{{ route('company.store') }}" enctype="multipart/form-data" id="companyForm">
    @csrf

    @if ($errors->any())
    <div class="alert alert-danger mb-3" style="border-radius:10px;">
        <strong><i class="ti-alert mr-1"></i> Please fix the following errors:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- ── STEP 1: Company Identity ─────────────────────────────── --}}
    <div class="nc-card active" id="step1">
        <div class="nc-card-header">
            <div class="ch-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-id-badge"></i></div>
            <div>
                <h6>Company Identity</h6>
                <p class="ch-sub">Basic company identification and business type</p>
            </div>
        </div>
        <div class="nc-card-body">

            <div class="nc-section-title"><i class="ti-id-badge"></i> Company Identification</div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="nc-label">Company Code <span class="req">*</span></label>
                        <input type="text" name="company_code"
                            class="form-control nc-input @error('company_code') is-invalid @enderror"
                            value="{{ old('company_code') }}" placeholder="e.g. TRP001" required>
                        @error('company_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted" style="font-size:11px;">Unique short identifier for this company.</small>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="nc-label">Company Name <span class="req">*</span></label>
                        <input type="text" name="company_name"
                            class="form-control nc-input @error('company_name') is-invalid @enderror"
                            value="{{ old('company_name') }}" placeholder="Full legal company name" required>
                        @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="nc-section-title"><i class="ti-briefcase"></i> Business Type</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="nc-label">Business Type <span class="req">*</span></label>
                        <select name="business_type" class="form-control nc-input select2-nc @error('business_type') is-invalid @enderror" required>
                            <option value="">Select Business Type</option>
                            @foreach(['Transport','Logistics','Fleet Owner','Parcel Service','Courier Service','Truck Booking','Cargo Service','Warehouse','Import & Export','Others'] as $bt)
                            <option value="{{ $bt }}" {{ old('business_type') == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                            @endforeach
                        </select>
                        @error('business_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="nc-label">Place of Supply</label>
                        <input type="text" name="place_of_supply"
                            class="form-control nc-input @error('place_of_supply') is-invalid @enderror"
                            value="{{ old('place_of_supply') }}" placeholder="e.g. Tamil Nadu">
                        @error('place_of_supply')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

        </div>
        <div class="nc-nav">
            <a href="{{ route('company') }}" class="nc-btn-prev"><i class="ti-close mr-1"></i> Cancel</a>
            <button type="button" class="nc-btn-next" onclick="ncGoTo(2)">Next: Tax & Contact <i class="ti-arrow-right ml-1"></i></button>
        </div>
    </div>

    {{-- ── STEP 2: Tax & Contact ────────────────────────────────── --}}
    <div class="nc-card" id="step2">
        <div class="nc-card-header">
            <div class="ch-icon" style="background:#fff8e1;color:#d97706;"><i class="ti-receipt"></i></div>
            <div>
                <h6>Tax & Contact Details</h6>
                <p class="ch-sub">PAN, GST number and contact information</p>
            </div>
        </div>
        <div class="nc-card-body">

            <div class="nc-section-title"><i class="ti-receipt"></i> Tax Registration</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="nc-label">PAN Number <span class="req">*</span></label>
                        <input type="text" name="pan" maxlength="10"
                            class="form-control nc-input @error('pan') is-invalid @enderror"
                            value="{{ old('pan') }}" placeholder="10-digit PAN" style="text-transform:uppercase;" required>
                        @error('pan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="nc-label">GST Number</label>
                        <input type="text" name="gst" maxlength="15"
                            class="form-control nc-input @error('gst') is-invalid @enderror"
                            value="{{ old('gst') }}" placeholder="15-digit GSTIN" style="text-transform:uppercase;">
                        @error('gst')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="nc-section-title"><i class="ti-mobile"></i> Contact Information</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="nc-label">Email Address <span class="req">*</span></label>
                        <div class="nc-input-group">
                            <span class="nc-addon"><i class="ti-email" style="font-size:14px;"></i></span>
                            <input type="email" name="email"
                                class="form-control nc-input @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" placeholder="company@example.com" required>
                        </div>
                        @error('email')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="nc-label">Primary Phone</label>
                        <div class="nc-input-group">
                            <span class="nc-addon"><i class="ti-mobile" style="font-size:14px;"></i></span>
                            <input type="text" name="phone"
                                class="form-control nc-input @error('phone') is-invalid @enderror"
                                value="{{ old('phone') }}" placeholder="+91 XXXXXXXXXX">
                        </div>
                        @error('phone')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="nc-label">Alternate Phone</label>
                        <div class="nc-input-group">
                            <span class="nc-addon"><i class="ti-mobile" style="font-size:14px;"></i></span>
                            <input type="text" name="phone2"
                                class="form-control nc-input @error('phone2') is-invalid @enderror"
                                value="{{ old('phone2') }}" placeholder="+91 XXXXXXXXXX">
                        </div>
                        @error('phone2')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

        </div>
        <div class="nc-nav">
            <button type="button" class="nc-btn-prev" onclick="ncGoTo(1)"><i class="ti-arrow-left mr-1"></i> Back</button>
            <button type="button" class="nc-btn-next" onclick="ncGoTo(3)">Next: Bank Details <i class="ti-arrow-right ml-1"></i></button>
        </div>
    </div>

    {{-- ── STEP 3: Bank Details ─────────────────────────────────── --}}
    <div class="nc-card" id="step3">
        <div class="nc-card-header">
            <div class="ch-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-credit-card"></i></div>
            <div>
                <h6>Bank Account Details</h6>
                <p class="ch-sub">Bank account information for payments and transactions</p>
            </div>
        </div>
        <div class="nc-card-body">

            <div class="bank-highlight d-flex align-items-start gap-3 mb-4">
                <div class="bank-icon-wrap mr-3"><i class="ti-credit-card"></i></div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:#1a2340;margin-bottom:3px;">Bank Account Information</div>
                    <div style="font-size:12px;color:#596579;">This information will be used on invoices and payment receipts.</div>
                </div>
            </div>

            <div class="nc-section-title"><i class="ti-credit-card"></i> Primary Bank Account</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="nc-label">Bank Name</label>
                        <div class="nc-input-group">
                            <span class="nc-addon"><i class="ti-home" style="font-size:13px;"></i></span>
                            <input type="text" name="bank_name"
                                class="form-control nc-input @error('bank_name') is-invalid @enderror"
                                value="{{ old('bank_name') }}" placeholder="e.g. State Bank of India">
                        </div>
                        @error('bank_name')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="nc-label">Branch Name</label>
                        <div class="nc-input-group">
                            <span class="nc-addon"><i class="ti-location-pin" style="font-size:13px;"></i></span>
                            <input type="text" name="branch_name"
                                class="form-control nc-input @error('branch_name') is-invalid @enderror"
                                value="{{ old('branch_name') }}" placeholder="e.g. Anna Nagar Branch">
                        </div>
                        @error('branch_name')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="nc-label">Account Holder Name</label>
                        <div class="nc-input-group">
                            <span class="nc-addon"><i class="ti-user" style="font-size:13px;"></i></span>
                            <input type="text" name="account_holder_name"
                                class="form-control nc-input @error('account_holder_name') is-invalid @enderror"
                                value="{{ old('account_holder_name') }}" placeholder="Name as per bank records">
                        </div>
                        @error('account_holder_name')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="nc-label">Account Number</label>
                        <div class="nc-input-group">
                            <span class="nc-addon"><i class="ti-wallet" style="font-size:13px;"></i></span>
                            <input type="text" name="account_number"
                                class="form-control nc-input @error('account_number') is-invalid @enderror"
                                value="{{ old('account_number') }}" placeholder="Bank account number">
                        </div>
                        @error('account_number')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="nc-label">IFSC Code</label>
                        <div class="nc-input-group">
                            <span class="nc-addon" style="font-size:11px;font-weight:800;letter-spacing:.5px;">IFSC</span>
                            <input type="text" name="ifsc_code" maxlength="11"
                                class="form-control nc-input @error('ifsc_code') is-invalid @enderror"
                                value="{{ old('ifsc_code') }}" placeholder="e.g. SBIN0001234" style="text-transform:uppercase;">
                        </div>
                        @error('ifsc_code')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="nc-label">UPI ID</label>
                        <div class="nc-input-group">
                            <span class="nc-addon" style="font-size:11px;font-weight:800;">UPI</span>
                            <input type="text" name="upi_id"
                                class="form-control nc-input @error('upi_id') is-invalid @enderror"
                                value="{{ old('upi_id') }}" placeholder="e.g. company@upi">
                        </div>
                        @error('upi_id')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

        </div>
        <div class="nc-nav">
            <button type="button" class="nc-btn-prev" onclick="ncGoTo(2)"><i class="ti-arrow-left mr-1"></i> Back</button>
            <button type="button" class="nc-btn-next" onclick="ncGoTo(4)">Next: Address & Logo <i class="ti-arrow-right ml-1"></i></button>
        </div>
    </div>

    {{-- ── STEP 4: Address & Logo & Status ──────────────────────── --}}
    <div class="nc-card" id="step4">
        <div class="nc-card-header">
            <div class="ch-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti-location-pin"></i></div>
            <div>
                <h6>Address, Logo & Status</h6>
                <p class="ch-sub">Location details, company logo and account status</p>
            </div>
        </div>
        <div class="nc-card-body">

            <div class="nc-section-title"><i class="ti-map-alt"></i> Registered Address</div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="nc-label">Street Address <span class="req">*</span></label>
                        <textarea name="address" rows="3"
                            class="form-control nc-input @error('address') is-invalid @enderror"
                            placeholder="Door no, street, area, landmark" required>{{ old('address') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="nc-label">State <span class="req">*</span></label>
                        <input type="text" name="state"
                            class="form-control nc-input @error('state') is-invalid @enderror"
                            value="{{ old('state') }}" placeholder="State" required>
                        @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="nc-label">District</label>
                        <input type="text" name="district"
                            class="form-control nc-input @error('district') is-invalid @enderror"
                            value="{{ old('district') }}" placeholder="District">
                        @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="nc-label">Pincode <span class="req">*</span></label>
                        <input type="text" name="pincode" maxlength="6"
                            class="form-control nc-input @error('pincode') is-invalid @enderror"
                            value="{{ old('pincode') }}" placeholder="6-digit PIN" required>
                        @error('pincode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="nc-label">Country <span class="req">*</span></label>
                        <input type="text" name="country"
                            class="form-control nc-input @error('country') is-invalid @enderror"
                            value="{{ old('country','India') }}" placeholder="Country" required>
                        @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="nc-section-title"><i class="ti-image"></i> Company Logo</div>
            <div class="row">
                <div class="col-md-5">
                    <div class="nc-upload-zone" id="logoZone" onclick="document.getElementById('logo').click()">
                        <i class="ti-cloud-up"></i>
                        <img id="logoPreview" class="logo-preview-img" src="" alt="Logo preview">
                        <div class="uz-title">Click to upload logo</div>
                        <div class="uz-sub">JPG, PNG, WEBP — max 2 MB</div>
                    </div>
                    <input id="logo" name="logo" type="file" class="d-none"
                        accept="image/jpeg,image/png,image/webp"
                        onchange="ncPreviewLogo(this)">
                    @error('logo')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
                <div class="col-md-7">
                    <div class="nc-section-title" style="margin-top:0;"><i class="ti-settings"></i> Company Status</div>
                    <div class="form-group">
                        <label class="nc-label">Status</label>
                        <select name="status" class="form-control nc-input select2-nc">
                            <option value="1" {{ old('status','1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>
        <div class="nc-nav">
            <button type="button" class="nc-btn-prev" onclick="ncGoTo(3)"><i class="ti-arrow-left mr-1"></i> Back</button>
            <button type="submit" class="nc-btn-submit" id="submitCompanyBtn">
                <i class="ti-save mr-1"></i> Save Company
            </button>
        </div>
    </div>

</form>

</div></div></div></div>

@push('scripts')
<script>
$(document).ready(function () {

    var totalSteps  = 4;
    var currentStep = 1;

    window.ncGoTo = function (step) {
        if (step < 1 || step > totalSteps) return;

        for (var i = 1; i < step; i++) {
            $('#ncSteps .nc-step[data-step="' + i + '"]').removeClass('active').addClass('done')
                .find('.step-num').html('<i class="ti-check" style="font-size:12px;"></i>');
        }
        $('#ncSteps .nc-step[data-step="' + step + '"]').removeClass('done').addClass('active')
            .find('.step-num').text(step);
        for (var j = step + 1; j <= totalSteps; j++) {
            $('#ncSteps .nc-step[data-step="' + j + '"]').removeClass('active done')
                .find('.step-num').text(j);
        }

        $('.nc-card').removeClass('active');
        $('#step' + step).addClass('active');

        var pct = Math.round((step / totalSteps) * 100);
        $('#ncProgressFill').css('width', pct + '%');
        currentStep = step;

        $('html,body').animate({ scrollTop: $('.nc-page').offset().top - 20 }, 200);
    };

    // Select2
    if ($.fn.select2) {
        $('.select2-nc').select2({ width: '100%', allowClear: true });
    }

    // Logo preview
    window.ncPreviewLogo = function (input) {
        var preview = document.getElementById('logoPreview');
        var zone    = document.getElementById('logoZone');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                zone.classList.add('has-preview');
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    // IFSC auto-uppercase
    $('input[name="ifsc_code"], input[name="pan"], input[name="gst"]').on('input', function () {
        this.value = this.value.toUpperCase();
    });

    // Form submit guard
    $('#companyForm').on('submit', function () {
        $('#submitCompanyBtn').prop('disabled', true)
            .html('<i class="ti-reload mr-1"></i> Saving...');
    });

    // If errors, jump to first step with an error
    @if($errors->any())
        var fields = {
            1: ['company_code','company_name','business_type','place_of_supply'],
            2: ['pan','gst','email','phone','phone2'],
            3: ['bank_name','branch_name','account_holder_name','account_number','ifsc_code','upi_id'],
            4: ['address','state','district','pincode','country','logo','status'],
        };
        var errorFields = @json($errors->keys());
        var targetStep = 4;
        for (var s = 1; s <= 4; s++) {
            var intersect = errorFields.filter(function(f){ return fields[s].indexOf(f) !== -1; });
            if (intersect.length) { targetStep = s; break; }
        }
        ncGoTo(targetStep);
    @endif

});
</script>
@endpush
@endsection
