@extends('layouts.app')

@section('content')

<style>
/* ── Edit Company Page — Trip-style UI ────────────────────────────── */
.ec-page { background: #f4f6fb; }

.ec-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px; padding: 14px 22px; color: #fff;
    margin-bottom: 20px; position: relative; overflow: hidden;
}
.ec-header::before { content:''; position:absolute; top:-30px; right:-30px; width:120px; height:120px; background:rgba(255,255,255,.08); border-radius:50%; }
.ec-header::after  { content:''; position:absolute; bottom:-40px; right:50px; width:80px; height:80px; background:rgba(255,255,255,.05); border-radius:50%; }
.ec-header .badge-tag {
    display:inline-flex; align-items:center; gap:6px;
    background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.3);
    border-radius:20px; padding:3px 10px; font-size:11px; font-weight:700; letter-spacing:.5px; margin-bottom:4px;
}
.ec-header h4 { font-size:17px; font-weight:800; margin:0 0 2px; position:relative;z-index:1; }
.ec-header .sub { font-size:12px; opacity:.8; position:relative;z-index:1; }
.ec-header .header-actions { display:flex; gap:8px; flex-wrap:wrap; }
.ec-header .btn-header {
    display:inline-flex; align-items:center; gap:6px;
    padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
    border:none; cursor:pointer; transition:all .2s;
}
.ec-header .btn-header-white { background:rgba(255,255,255,.2); color:#fff; border:1px solid rgba(255,255,255,.35); }
.ec-header .btn-header-white:hover { background:rgba(255,255,255,.32); color:#fff; }
.ec-header .btn-header-solid { background:#fff; color:#667eea; }
.ec-header .btn-header-solid:hover { background:#f0f4ff; color:#5a6fd6; }

/* ── Section card ─────────────────────────────────────────────────── */
.ec-card { background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:20px; overflow:hidden; }
.ec-card-header {
    display:flex; align-items:center; gap:10px;
    padding:16px 20px; border-bottom:1px solid #f0f2f7; background:#fafbff;
}
.ec-card-header .card-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.ec-card-header h6 { margin:0; font-size:14px; font-weight:700; color:#1a2340; }
.ec-card-header .card-subtitle { font-size:11px; color:#8a94a6; margin:0; }
.ec-card-body { padding:22px; }

/* ── Section divider ──────────────────────────────────────────────── */
.ec-section-title {
    display:flex; align-items:center; gap:8px;
    margin:20px 0 14px; padding:10px 12px;
    border-left:3px solid #667eea; background:#f7f8fc;
    color:#303549; font-size:13px; font-weight:800; border-radius:0 6px 6px 0;
}
.ec-section-title:first-child { margin-top:0; }

/* ── Form fields ──────────────────────────────────────────────────── */
.ec-label { font-size:12px; font-weight:700; color:#596579; margin-bottom:6px; display:block; }
.ec-label .req { color:#e53e3e; }
.ec-input {
    min-height:44px; border-color:#d7dce5; color:#303549;
    font-size:14px; border-radius:8px; transition:border-color .2s,box-shadow .2s;
}
.ec-input:focus { border-color:#667eea; box-shadow:0 0 0 2px rgba(102,126,234,.12); }
.ec-input-group { display:flex; align-items:stretch; }
.ec-addon {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:42px; padding:0 12px; border:1px solid #d7dce5;
    background:#f8f9fa; color:#495057; font-weight:600; font-size:13px; min-height:44px;
}
.ec-addon:first-child { border-right:0; border-radius:8px 0 0 8px; }
.ec-addon:last-child  { border-left:0; border-radius:0 8px 8px 0; }
.ec-input-group .ec-input { border-radius:0; flex:1; }
.ec-input-group .ec-addon:first-child + .ec-input { border-radius:0 8px 8px 0; }
.ec-input-group .ec-input:not(:last-child) { border-radius:8px 0 0 8px; }

/* ── Bank detail highlight ────────────────────────────────────────── */
.bank-highlight {
    background:linear-gradient(135deg,#f0f4ff,#e8f0fe);
    border:1px solid #c7d7f5; border-radius:10px; padding:16px; margin-bottom:20px;
    display:flex; align-items:center; gap:14px;
}
.bank-highlight .bank-icon-wrap {
    width:44px; height:44px; border-radius:10px;
    background:linear-gradient(135deg,#667eea,#764ba2);
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:20px; flex-shrink:0;
}

/* ── Logo upload ──────────────────────────────────────────────────── */
.ec-upload-zone {
    border:2px dashed #c0d4f5; border-radius:10px;
    padding:20px; text-align:center; cursor:pointer;
    transition:all .2s; background:#f8fbff;
}
.ec-upload-zone:hover { border-color:#667eea; background:#eef2ff; }
.ec-upload-zone i { font-size:28px; color:#c0d4f5; margin-bottom:6px; display:block; }
.ec-upload-zone .uz-title { font-size:12px; font-weight:600; color:#667eea; }
.ec-upload-zone .uz-sub { font-size:11px; color:#b0bac9; }

/* ── Select2 overrides ────────────────────────────────────────────── */
.ec-card .select2-container { width:100% !important; }
.ec-card .select2-container--default .select2-selection--single { min-height:44px !important; height:44px !important; border-color:#d7dce5 !important; border-radius:8px !important; }
.ec-card .select2-container--default.select2-container--focus .select2-selection--single,
.ec-card .select2-container--default.select2-container--open .select2-selection--single { border-color:#667eea !important; box-shadow:0 0 0 2px rgba(102,126,234,.12) !important; }

@media(max-width:767.98px) { .ec-header { padding:12px 14px; } .ec-header h4 { font-size:14px; } }
</style>

<div class="pcoded-inner-content ec-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

{{-- ── HEADER ─────────────────────────────────────────────────── --}}
<div class="ec-header">
    <div class="row align-items-center" style="position:relative;z-index:1;">
        <div class="col-md-8">
            <div class="badge-tag"><i class="ti-pencil"></i> Edit Company</div>
            <h4>{{ $company->company_name }}</h4>
            <div class="sub"><i class="ti-tag mr-1"></i>{{ $company->company_code }}
                @if($company->gst) &bull; GST: {{ $company->gst }} @endif
            </div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0">
            <div class="header-actions justify-content-end">
                <a href="{{ route('company') }}" class="btn-header btn-header-white">
                    <i class="ti-arrow-left"></i> Back
                </a>
                <button type="submit" form="companyEditForm" class="btn-header btn-header-solid" id="saveCompanyBtn">
                    <i class="ti-save"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

@include('partials.flash')

<form method="POST" action="{{ route('company.update', $company->id) }}" enctype="multipart/form-data" id="companyEditForm">
    @csrf
    @method('PUT')

    @if ($errors->any())
    <div class="alert alert-danger mb-3" style="border-radius:10px;">
        <strong><i class="ti-alert mr-1"></i> Please fix the following errors:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        {{-- LEFT COLUMN ──────────────────────────────────────────── --}}
        <div class="col-lg-8">

            {{-- ① Company Identity --}}
            <div class="ec-card">
                <div class="ec-card-header">
                    <div class="card-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-id-badge"></i></div>
                    <div>
                        <h6>Company Identity</h6>
                        <p class="card-subtitle">Company code, name and business type</p>
                    </div>
                </div>
                <div class="ec-card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="ec-label">Company Code <span class="req">*</span></label>
                                <input type="text" name="company_code"
                                    class="form-control ec-input @error('company_code') is-invalid @enderror"
                                    value="{{ old('company_code', $company->company_code) }}" placeholder="e.g. TRP001" required>
                                @error('company_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="ec-label">Company Name <span class="req">*</span></label>
                                <input type="text" name="company_name"
                                    class="form-control ec-input @error('company_name') is-invalid @enderror"
                                    value="{{ old('company_name', $company->company_name) }}" placeholder="Full legal company name" required>
                                @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="ec-label">Business Type <span class="req">*</span></label>
                                <select name="business_type" class="form-control ec-input select2-ec @error('business_type') is-invalid @enderror" required>
                                    <option value="">Select Business Type</option>
                                    @foreach(['Transport','Logistics','Fleet Owner','Parcel Service','Courier Service','Truck Booking','Cargo Service','Warehouse','Import & Export','Others'] as $bt)
                                    <option value="{{ $bt }}" {{ old('business_type', $company->business_type) == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                                    @endforeach
                                </select>
                                @error('business_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="ec-label">Place of Supply</label>
                                <input type="text" name="place_of_supply"
                                    class="form-control ec-input @error('place_of_supply') is-invalid @enderror"
                                    value="{{ old('place_of_supply', $company->place_of_supply) }}" placeholder="e.g. Tamil Nadu">
                                @error('place_of_supply')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ② Tax & Contact --}}
            <div class="ec-card">
                <div class="ec-card-header">
                    <div class="card-icon" style="background:#fff8e1;color:#d97706;"><i class="ti-receipt"></i></div>
                    <div>
                        <h6>Tax & Contact Details</h6>
                        <p class="card-subtitle">PAN, GST number and contact information</p>
                    </div>
                </div>
                <div class="ec-card-body">
                    <div class="ec-section-title"><i class="ti-receipt"></i> Tax Registration</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="ec-label">PAN Number <span class="req">*</span></label>
                                <input type="text" name="pan" maxlength="10"
                                    class="form-control ec-input @error('pan') is-invalid @enderror"
                                    value="{{ old('pan', $company->pan) }}" placeholder="10-digit PAN" style="text-transform:uppercase;" required>
                                @error('pan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="ec-label">GST Number</label>
                                <input type="text" name="gst" maxlength="15"
                                    class="form-control ec-input @error('gst') is-invalid @enderror"
                                    value="{{ old('gst', $company->gst) }}" placeholder="15-digit GSTIN" style="text-transform:uppercase;">
                                @error('gst')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="ec-section-title"><i class="ti-mobile"></i> Contact Information</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="ec-label">Email Address <span class="req">*</span></label>
                                <div class="ec-input-group">
                                    <span class="ec-addon"><i class="ti-email" style="font-size:14px;"></i></span>
                                    <input type="email" name="email"
                                        class="form-control ec-input @error('email') is-invalid @enderror"
                                        value="{{ old('email', $company->email) }}" placeholder="company@example.com" required>
                                </div>
                                @error('email')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="ec-label">Primary Phone</label>
                                <div class="ec-input-group">
                                    <span class="ec-addon"><i class="ti-mobile" style="font-size:14px;"></i></span>
                                    <input type="text" name="phone"
                                        class="form-control ec-input @error('phone') is-invalid @enderror"
                                        value="{{ old('phone', $company->phone) }}" placeholder="+91 XXXXXXXXXX">
                                </div>
                                @error('phone')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="ec-label">Alternate Phone</label>
                                <div class="ec-input-group">
                                    <span class="ec-addon"><i class="ti-mobile" style="font-size:14px;"></i></span>
                                    <input type="text" name="phone2"
                                        class="form-control ec-input @error('phone2') is-invalid @enderror"
                                        value="{{ old('phone2', $company->phone2) }}" placeholder="+91 XXXXXXXXXX">
                                </div>
                                @error('phone2')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ③ Bank Account Details --}}
            <div class="ec-card">
                <div class="ec-card-header">
                    <div class="card-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-credit-card"></i></div>
                    <div>
                        <h6>Bank Account Details</h6>
                        <p class="card-subtitle">Account, IFSC and UPI information</p>
                    </div>
                </div>
                <div class="ec-card-body">
                    <div class="bank-highlight">
                        <div class="bank-icon-wrap"><i class="ti-credit-card"></i></div>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:#1a2340;margin-bottom:2px;">Bank Account Information</div>
                            <div style="font-size:12px;color:#596579;">Displayed on invoices and payment receipts.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="ec-label">Bank Name</label>
                                <div class="ec-input-group">
                                    <span class="ec-addon"><i class="ti-home" style="font-size:13px;"></i></span>
                                    <input type="text" name="bank_name"
                                        class="form-control ec-input @error('bank_name') is-invalid @enderror"
                                        value="{{ old('bank_name', $company->bank_name) }}" placeholder="e.g. State Bank of India">
                                </div>
                                @error('bank_name')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="ec-label">Branch Name</label>
                                <div class="ec-input-group">
                                    <span class="ec-addon"><i class="ti-location-pin" style="font-size:13px;"></i></span>
                                    <input type="text" name="branch_name"
                                        class="form-control ec-input @error('branch_name') is-invalid @enderror"
                                        value="{{ old('branch_name', $company->branch_name) }}" placeholder="e.g. Anna Nagar Branch">
                                </div>
                                @error('branch_name')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="ec-label">Account Holder Name</label>
                                <div class="ec-input-group">
                                    <span class="ec-addon"><i class="ti-user" style="font-size:13px;"></i></span>
                                    <input type="text" name="account_holder_name"
                                        class="form-control ec-input @error('account_holder_name') is-invalid @enderror"
                                        value="{{ old('account_holder_name', $company->account_holder_name) }}" placeholder="Name as per bank records">
                                </div>
                                @error('account_holder_name')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="ec-label">Account Number</label>
                                <div class="ec-input-group">
                                    <span class="ec-addon"><i class="ti-wallet" style="font-size:13px;"></i></span>
                                    <input type="text" name="account_number"
                                        class="form-control ec-input @error('account_number') is-invalid @enderror"
                                        value="{{ old('account_number', $company->account_number) }}" placeholder="Bank account number">
                                </div>
                                @error('account_number')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="ec-label">IFSC Code</label>
                                <div class="ec-input-group">
                                    <span class="ec-addon" style="font-size:11px;font-weight:800;letter-spacing:.5px;">IFSC</span>
                                    <input type="text" name="ifsc_code" maxlength="11"
                                        class="form-control ec-input @error('ifsc_code') is-invalid @enderror"
                                        value="{{ old('ifsc_code', $company->ifsc_code) }}" placeholder="e.g. SBIN0001234" style="text-transform:uppercase;">
                                </div>
                                @error('ifsc_code')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="ec-label">UPI ID</label>
                                <div class="ec-input-group">
                                    <span class="ec-addon" style="font-size:11px;font-weight:800;">UPI</span>
                                    <input type="text" name="upi_id"
                                        class="form-control ec-input @error('upi_id') is-invalid @enderror"
                                        value="{{ old('upi_id', $company->upi_id) }}" placeholder="e.g. company@upi">
                                </div>
                                @error('upi_id')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ④ Address --}}
            <div class="ec-card">
                <div class="ec-card-header">
                    <div class="card-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti-map-alt"></i></div>
                    <div>
                        <h6>Registered Address</h6>
                        <p class="card-subtitle">Company registered location details</p>
                    </div>
                </div>
                <div class="ec-card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="ec-label">Street Address <span class="req">*</span></label>
                                <textarea name="address" rows="3"
                                    class="form-control ec-input @error('address') is-invalid @enderror"
                                    placeholder="Door no, street, area, landmark" required>{{ old('address', $company->address) }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="ec-label">State <span class="req">*</span></label>
                                <input type="text" name="state"
                                    class="form-control ec-input @error('state') is-invalid @enderror"
                                    value="{{ old('state', $company->state) }}" placeholder="State" required>
                                @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="ec-label">District</label>
                                <input type="text" name="district"
                                    class="form-control ec-input @error('district') is-invalid @enderror"
                                    value="{{ old('district', $company->district) }}" placeholder="District">
                                @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="ec-label">Pincode <span class="req">*</span></label>
                                <input type="text" name="pincode" maxlength="6"
                                    class="form-control ec-input @error('pincode') is-invalid @enderror"
                                    value="{{ old('pincode', $company->pincode) }}" placeholder="6-digit PIN" required>
                                @error('pincode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="ec-label">Country <span class="req">*</span></label>
                                <input type="text" name="country"
                                    class="form-control ec-input @error('country') is-invalid @enderror"
                                    value="{{ old('country', $company->country) }}" placeholder="Country" required>
                                @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('company') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ti-close mr-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm" id="saveCompanyBtnForm">
                            <i class="ti-save mr-1"></i> Update Company
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN ─────────────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Company summary card --}}
            <div class="ec-card">
                <div class="ec-card-header">
                    <div class="card-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-building"></i></div>
                    <div>
                        <h6>Company Overview</h6>
                        <p class="card-subtitle">Quick summary</p>
                    </div>
                </div>
                <div class="ec-card-body p-0">
                    {{-- Logo --}}
                    <div style="text-align:center;padding:20px;border-bottom:1px solid #f0f2f7;">
                        @if($company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo"
                            style="max-height:80px;max-width:160px;border-radius:8px;object-fit:contain;" id="logoPreview">
                        @else
                        <div id="logoPreview" style="width:80px;height:80px;border-radius:12px;background:linear-gradient(135deg,#667eea,#764ba2);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:28px;font-weight:800;">
                            {{ strtoupper(substr($company->company_name, 0, 1)) }}
                        </div>
                        @endif
                        <div style="font-size:15px;font-weight:800;color:#1a2340;margin-top:10px;">{{ $company->company_name }}</div>
                        <div style="font-size:12px;color:#8a94a6;">{{ $company->company_code }}</div>
                    </div>

                    {{-- Info rows --}}
                    @foreach([
                        ['icon'=>'ti-briefcase','label'=>'Business','value'=> $company->business_types_display,'color'=>'#667eea','bg'=>'#eef2ff'],
                        ['icon'=>'ti-email','label'=>'Email','value'=>$company->email,'color'=>'#38a169','bg'=>'#f0fff4'],
                        ['icon'=>'ti-mobile','label'=>'Phone','value'=>$company->phone ?: '—','color'=>'#d97706','bg'=>'#fff8e1'],
                        ['icon'=>'ti-credit-card','label'=>'Bank','value'=>$company->bank_name ?: '—','color'=>'#7c3aed','bg'=>'#f5f3ff'],
                        ['icon'=>'ti-wallet','label'=>'A/C','value'=>$company->account_number ? substr_replace($company->account_number, str_repeat('*', max(0, strlen($company->account_number)-4)), 0, -4) : '—','color'=>'#0369a1','bg'=>'#f0f9ff'],
                    ] as $row)
                    <div style="display:flex;align-items:center;padding:10px 16px;border-bottom:1px solid #f4f6fb;">
                        <div style="width:30px;height:30px;border-radius:7px;background:{{ $row['bg'] }};color:{{ $row['color'] }};display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;margin-right:10px;">
                            <i class="{{ $row['icon'] }}"></i>
                        </div>
                        <div>
                            <div style="font-size:10px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.3px;">{{ $row['label'] }}</div>
                            <div style="font-size:12px;font-weight:600;color:#1a2340;word-break:break-all;">{{ $row['value'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Logo Upload --}}
            <div class="ec-card">
                <div class="ec-card-header">
                    <div class="card-icon" style="background:#f0f9ff;color:#0369a1;"><i class="ti-image"></i></div>
                    <div>
                        <h6>Company Logo</h6>
                        <p class="card-subtitle">Upload or replace logo</p>
                    </div>
                </div>
                <div class="ec-card-body">
                    <div class="ec-upload-zone" id="logoUploadZone" onclick="document.getElementById('logoInput').click()">
                        <i class="ti-cloud-up"></i>
                        <div class="uz-title">Click to change logo</div>
                        <div class="uz-sub">JPG, PNG, WEBP — max 2 MB</div>
                    </div>
                    <input id="logoInput" name="logo" type="file" class="d-none"
                        accept="image/jpeg,image/png,image/webp"
                        onchange="ecPreviewLogo(this)">
                    @error('logo')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
            </div>

            {{-- Status --}}
            <div class="ec-card">
                <div class="ec-card-header">
                    <div class="card-icon" style="background:{{ $company->status ? '#f0fff4' : '#fff5f5' }};color:{{ $company->status ? '#38a169' : '#e53e3e' }};"><i class="ti-settings"></i></div>
                    <div>
                        <h6>Company Status</h6>
                        <p class="card-subtitle">Enable or disable this company</p>
                    </div>
                </div>
                <div class="ec-card-body">
                    <div class="form-group mb-0">
                        <label class="ec-label">Status</label>
                        <select name="status" class="form-control ec-input select2-ec">
                            <option value="1" {{ old('status', $company->status ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $company->status ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>

</form>

</div></div></div></div>

@push('scripts')
<script>
$(document).ready(function () {

    if ($.fn.select2) {
        $('.select2-ec').select2({ width: '100%', allowClear: true });
    }

    // IFSC / PAN / GST uppercase
    $('input[name="ifsc_code"], input[name="pan"], input[name="gst"]').on('input', function () {
        this.value = this.value.toUpperCase();
    });

    // Logo preview
    window.ecPreviewLogo = function (input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var p = document.getElementById('logoPreview');
                if (p.tagName === 'IMG') {
                    p.src = e.target.result;
                } else {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.id = 'logoPreview';
                    img.style.cssText = 'max-height:80px;max-width:160px;border-radius:8px;object-fit:contain;';
                    p.replaceWith(img);
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    // Form submit guard
    $('#companyEditForm').on('submit', function () {
        $('#saveCompanyBtn, #saveCompanyBtnForm').prop('disabled', true)
            .html('<i class="ti-reload mr-1"></i> Saving...');
    });

});
</script>
@endpush
@endsection
