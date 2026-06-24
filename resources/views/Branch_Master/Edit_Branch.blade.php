@extends('layouts.app')

@section('content')
<style>
.branch-edit-page{background:#f4f6fb;}
.branch-edit-header{background:linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%);border-radius:14px;padding:24px 28px;color:#fff;margin-bottom:24px;position:relative;overflow:hidden;}
.branch-edit-header::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.08);border-radius:50%;}
.branch-edit-header h3{font-size:20px;font-weight:800;margin:0 0 4px;position:relative;z-index:1;}
.branch-edit-header .sub{font-size:12px;opacity:.85;position:relative;z-index:1;}
.edit-card{background:#fff;border-radius:16px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,.06);margin-bottom:20px;}
.section-title{font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#3b82f6;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid #e5e7ff;display:flex;align-items:center;gap:8px;}
.section-title i{font-size:14px;}
.form-group-custom{margin-bottom:18px;}
.form-group-custom label{display:block;font-size:12px;font-weight:700;color:#596579;margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px;}
.form-group-custom label .required{color:#dc2626;}
.form-control-custom{width:100%;border:1px solid #d7dce5;border-radius:8px;padding:9px 12px;font-size:13px;color:#303549;background:#fff;transition:border-color .15s,box-shadow .15s;}
.form-control-custom:focus{outline:none;border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.12);}
.form-control-custom:disabled{background:#f8fafc;color:#596579;cursor:default;}
.form-control-custom.is-invalid{border-color:#dc2626;}
.invalid-feedback{color:#dc2626;font-size:12px;margin-top:4px;display:block;}
.field-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px;}
.action-buttons{display:flex;gap:10px;margin-top:28px;padding-top:24px;border-top:1px solid #e5e7eb;}
.btn-cancel{background:#f0f2f7;color:#596579;border:none;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;}
.btn-cancel:hover{background:#e2e8f0;color:#303549;}
.btn-submit{background:#10b981;color:#fff;border:none;border-radius:8px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;}
.btn-submit:hover{background:#059669;transform:translateY(-1px);box-shadow:0 4px 12px rgba(16,185,129,.3);}
.checkbox-group{display:flex;align-items:center;gap:10px;margin-top:8px;}
.checkbox-custom{width:18px;height:18px;border:2px solid #d7dce5;border-radius:4px;cursor:pointer;accent-color:#3b82f6;}
.alert-custom{background:#fee2e2;color:#991b1b;border:1px solid #fecaca;border-radius:8px;padding:12px 14px;margin-bottom:20px;font-size:13px;}
.alert-custom i{margin-right:8px;font-size:14px;}
</style>

<div class="pcoded-inner-content branch-edit-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="branch-edit-header">
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div>
            <h3><i class="ti-pencil mr-2"></i>Edit Branch</h3>
            <div class="sub">{{ $branch->branch_name }}</div>
        </div>
        <a href="{{ route('branch') }}" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
            <i class="ti-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<form method="POST" action="{{ route('branch.update', $branch->id) }}" id="branchEditForm">
    @csrf
    @method('PUT')

    @if ($errors->any())
    <div class="alert-custom">
        <i class="ti-alert"></i><strong>Please fix the following errors:</strong>
        <ul style="margin:8px 0 0 24px;">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="edit-card">
        <div class="section-title"><i class="ti-id-badge"></i> Identity & Status</div>
        <div class="field-row">
            <div class="form-group-custom">
                <label>Company <span class="required">*</span></label>
                <select name="company_id" class="form-control-custom @error('company_id') is-invalid @enderror" required>
                    <option value="">Select Company</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ old('company_id', $branch->company_id) == $company->id ? 'selected' : '' }}>
                        {{ $company->company_name }}
                    </option>
                    @endforeach
                </select>
                @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group-custom">
                <label>Branch Code <span class="required">*</span></label>
                <input type="text" name="branch_code" class="form-control-custom @error('branch_code') is-invalid @enderror" value="{{ old('branch_code', $branch->branch_code) }}" placeholder="e.g. BR001" required>
                @error('branch_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group-custom">
                <label>Branch Name <span class="required">*</span></label>
                <input type="text" name="branch_name" class="form-control-custom @error('branch_name') is-invalid @enderror" value="{{ old('branch_name', $branch->branch_name) }}" placeholder="Branch name" required>
                @error('branch_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="field-row" style="margin-top:16px;">
            <div class="form-group-custom">
                <label>Head Office</label>
                <div class="checkbox-group">
                    <input type="checkbox" name="head_office" value="1" class="checkbox-custom" {{ old('head_office', $branch->head_office) ? 'checked' : '' }}>
                    <span style="font-size:13px;color:#596579;">Mark as head office</span>
                </div>
            </div>
            <div class="form-group-custom">
                <label>Status</label>
                <select name="status" class="form-control-custom">
                    <option value="1" {{ old('status', $branch->status ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $branch->status ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="edit-card">
        <div class="section-title"><i class="ti-headphone-alt"></i> Contact Details</div>
        <div class="field-row">
            <div class="form-group-custom">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control-custom @error('email') is-invalid @enderror" value="{{ old('email', $branch->email) }}" placeholder="branch@example.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group-custom">
                <label>Mobile Number</label>
                <input type="text" name="mobile" class="form-control-custom @error('mobile') is-invalid @enderror" value="{{ old('mobile', $branch->mobile) }}" placeholder="10-digit mobile">
                @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="edit-card">
        <div class="section-title"><i class="ti-location-pin"></i> Address Details</div>
        <div class="form-group-custom">
            <label>Street Address</label>
            <textarea name="address" class="form-control-custom" rows="3" placeholder="Door no, street, area">{{ old('address', $branch->address) }}</textarea>
        </div>
        <div class="field-row">
            <div class="form-group-custom">
                <label>City</label>
                <input type="text" name="city" class="form-control-custom" value="{{ old('city', $branch->city) }}" placeholder="City">
            </div>
            <div class="form-group-custom">
                <label>State</label>
                <input type="text" name="state" class="form-control-custom" value="{{ old('state', $branch->state) }}" placeholder="State">
            </div>
            <div class="form-group-custom">
                <label>Pincode</label>
                <input type="text" name="pincode" class="form-control-custom @error('pincode') is-invalid @enderror" value="{{ old('pincode', $branch->pincode) }}" placeholder="6-digit PIN">
                @error('pincode')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group-custom">
                <label>Country</label>
                <input type="text" name="country" class="form-control-custom" value="{{ old('country', $branch->country ?? 'India') }}" placeholder="Country">
            </div>
        </div>
    </div>

    <div class="edit-card">
        <div class="action-buttons">
            <a href="{{ route('branch') }}" class="btn-cancel"><i class="ti-arrow-left mr-1"></i> Cancel</a>
            <button type="submit" class="btn-submit"><i class="ti-check mr-1"></i> Update Branch</button>
        </div>
    </div>
</form>

</div></div></div>
</div>
@endsection
