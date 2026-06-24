@php
    $branch = $branch ?? null;
    $readonly = $readonly ?? false;
@endphp

@if ($errors->any())
<div class="alert alert-danger mb-3">
    <strong><i class="icofont icofont-warning mr-1"></i>Please fix the following errors:</strong>
    <ul class="mb-0 mt-1">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<h6 class="mb-3"><i class="icofont icofont-id-card mr-1"></i>Branch Identity</h6>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="company_id">Company <span class="text-danger">*</span></label>
            @if($readonly)
            <input type="text" class="form-control" readonly
                value="{{ $branch?->company?->company_name ?? '—' }}">
            @else
            <select id="company_id" name="company_id"
                class="form-control select2 @error('company_id') is-invalid @enderror" required>
                <option value="">Select Company</option>
                @foreach($companies as $company)
                <option value="{{ $company->id }}"
                    {{ old('company_id', $branch?->company_id) == $company->id ? 'selected' : '' }}>
                    {{ $company->company_name }}
                </option>
                @endforeach
            </select>
            @error('company_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="branch_code">Branch Code <span class="text-danger">*</span></label>
            <input id="branch_code" name="branch_code" type="text"
                class="form-control @error('branch_code') is-invalid @enderror"
                value="{{ old('branch_code', $branch?->branch_code) }}"
                placeholder="e.g. BR001"
                {{ $readonly ? 'readonly' : '' }} required>
            @error('branch_code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="branch_name">Branch Name <span class="text-danger">*</span></label>
            <input id="branch_name" name="branch_name" type="text"
                class="form-control @error('branch_name') is-invalid @enderror"
                value="{{ old('branch_name', $branch?->branch_name) }}"
                placeholder="Branch name"
                {{ $readonly ? 'readonly' : '' }} required>
            @error('branch_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="head_office">Head Office</label>
            <div class="mt-2">
                <div class="checkbox-fade fade-in-primary">
                    <label>
                        <input type="checkbox" id="head_office" name="head_office" value="1"
                            {{ old('head_office', $branch?->head_office) ? 'checked' : '' }}
                            {{ $readonly ? 'disabled' : '' }}>
                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                        <span>Mark as head office</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control select2" {{ $readonly ? 'disabled' : '' }}>
                <option value="1" {{ old('status', $branch?->status ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $branch?->status ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>
</div>

<hr class="my-4">

<h6 class="mb-3"><i class="icofont icofont-phone mr-1"></i>Contact Details</h6>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input id="email" name="email" type="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $branch?->email) }}"
                placeholder="branch@example.com"
                {{ $readonly ? 'readonly' : '' }}>
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="mobile">Mobile Number</label>
            <input id="mobile" name="mobile" type="text"
                class="form-control @error('mobile') is-invalid @enderror"
                value="{{ old('mobile', $branch?->mobile) }}"
                placeholder="10-digit mobile"
                {{ $readonly ? 'readonly' : '' }}>
            @error('mobile')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<hr class="my-4">

<h6 class="mb-3"><i class="icofont icofont-location-pin mr-1"></i>Address Details</h6>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="address">Street Address</label>
            <textarea id="address" name="address" class="form-control" rows="3"
                placeholder="Door no, street, area"
                {{ $readonly ? 'readonly' : '' }}>{{ old('address', $branch?->address) }}</textarea>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="city">City</label>
            <input id="city" name="city" type="text" class="form-control"
                value="{{ old('city', $branch?->city) }}" placeholder="City"
                {{ $readonly ? 'readonly' : '' }}>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="state">State</label>
            <input id="state" name="state" type="text" class="form-control"
                value="{{ old('state', $branch?->state) }}" placeholder="State"
                {{ $readonly ? 'readonly' : '' }}>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="pincode">Pincode</label>
            <input id="pincode" name="pincode" type="text"
                class="form-control @error('pincode') is-invalid @enderror"
                value="{{ old('pincode', $branch?->pincode) }}" placeholder="6-digit PIN"
                {{ $readonly ? 'readonly' : '' }}>
            @error('pincode')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="country">Country</label>
            <input id="country" name="country" type="text" class="form-control"
                value="{{ old('country', $branch?->country ?? 'India') }}" placeholder="Country"
                {{ $readonly ? 'readonly' : '' }}>
        </div>
    </div>
</div>
