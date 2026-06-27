@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/parties/parties.css') }}">

<style>
/* ── Header ── */
.drv-edit-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 14px; padding: 22px 28px;
    color: #fff; margin-bottom: 22px;
    position: relative; overflow: hidden;
}
.drv-edit-header::before { content:''; position:absolute; top:-50px; right:-50px; width:180px; height:180px; background:rgba(255,255,255,.06); border-radius:50%; }
.drv-edit-header h4 { font-size:20px; font-weight:800; margin:0 0 3px; position:relative; z-index:1; }
.drv-edit-header .sub { font-size:13px; opacity:.75; position:relative; z-index:1; }

/* ── Form cards ── */
.form-card {
    background: #fff; border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    margin-bottom: 20px; overflow: hidden;
}
.form-card-header {
    padding: 14px 20px; border-bottom: 1px solid #f0f2f7; background: #fafbff;
    display: flex; align-items: center; gap: 8px;
}
.form-card-header h6 { margin:0; font-size:13px; font-weight:700; color:#1a2340; }
.form-card-body { padding: 20px; }

/* ── Form fields ── */
.form-group-drv { margin-bottom: 16px; }
.form-group-drv label { display:block; font-size:12px; font-weight:700; color:#596579; margin-bottom:6px; }
.form-group-drv .req { color: #e53e3e; }
.form-group-drv .form-control {
    border-color: #d7dce5; border-radius: 8px;
    font-size: 13px; color: #303549; min-height: 42px;
    transition: border-color .15s, box-shadow .15s;
}
.form-group-drv .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.12); }
.form-group-drv .select2-container { width: 100% !important; }
.form-group-drv .select2-container--default .select2-selection--single {
    min-height: 42px !important; height: 42px !important;
    border-color: #d7dce5 !important; border-radius: 8px !important;
    display: flex !important; align-items: center !important;
}
.location-help { font-size: 11px; color: #6c757d; margin-top: 4px; }

/* ── Driver type cards ── */
.driver-type-card {
    display: inline-flex; align-items: center; gap: 10px;
    padding: 10px 16px 10px 12px; border-radius: 10px;
    border: 2px solid #e2e8f0; background: #f7f8fc;
    cursor: pointer; transition: all .18s; min-width: 160px; user-select: none;
}
.driver-type-card:hover { border-color: #b0bac9; background: #eef0f7; }
.driver-type-card.active[for="dt_own"]    { border-color: #38a169; background: #f0fff4; box-shadow: 0 2px 10px rgba(56,161,105,.12); }
.driver-type-card.active[for="dt_rental"] { border-color: #d97706; background: #fffbeb; box-shadow: 0 2px 10px rgba(217,119,6,.12); }
.dtc-icon { width:38px; height:38px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0; }
.dtc-label { font-size:13px; font-weight:700; color:#1a2340; }
.dtc-sub   { font-size:11px; color:#8a94a6; margin-top:1px; }

/* ── Photo upload cards ── */
.photo-upload-card {
    border: 2px dashed #d0d5e8; border-radius: 10px;
    padding: 16px 12px; text-align: center; background: #fafbff;
    transition: all .2s; position: relative;
}
.photo-upload-card:hover { border-color: #667eea; background: #f0f3ff; box-shadow: 0 4px 14px rgba(102,126,234,.15); }
.photo-upload-card.has-file { border-color: #38a169; background: #f0fff4; border-style: solid; }
.photo-upload-card.has-file:hover { box-shadow: 0 4px 14px rgba(40,167,69,.15); }
.photo-upload-card .card-icon { font-size: 28px; color: #667eea; margin-bottom: 6px; display: block; }
.photo-upload-card.has-file .card-icon { color: #38a169; }
.photo-upload-card .card-title { font-size: 12px; font-weight: 700; color: #303549; margin-bottom: 4px; display: block; }
.photo-upload-card .card-hint { font-size: 11px; color: #adb5bd; margin-bottom: 8px; }
.existing-photo-thumb { width:68px; height:68px; object-fit:cover; border-radius:8px; border:2px solid #38a169; margin-bottom:6px; cursor:pointer; }
.existing-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: #e9f7ef; color: #38a169; border: 1px solid #c3e6cb;
    border-radius: 20px; padding: 2px 10px; font-size: 11px; font-weight: 600;
    margin-bottom: 5px; cursor: pointer;
}
.existing-badge:hover { background: #d4edda; }
.btn-upload-badge {
    display: inline-block; background: #667eea; color: #fff;
    border-radius: 20px; font-size: 11px; font-weight: 600;
    padding: 4px 14px; margin-top: 7px; cursor: pointer; border: none; outline: none;
}
.btn-upload-badge:hover { background: #5a6fd6; }
.photo-upload-card.has-file .btn-upload-badge { background: #38a169; }
.photo-upload-card.has-file .btn-upload-badge:hover { background: #2f855a; }
.chosen-name { font-size: 11px; color: #667eea; font-weight: 600; margin-top: 5px; word-break: break-all; display: none; }

/* ── Action bar ── */
.action-bar {
    background: #fff; border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    padding: 14px 20px;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 10px;
}

/* Photo Preview Modal - right panel */
#photoPreviewModal .modal-dialog {
    position: fixed !important;
    right: 0 !important;
    top: 0 !important;
    margin: 0 !important;
    transform: none !important;
    max-width: 700px !important;
    width: 100% !important;
    height: 100% !important;
}
#photoPreviewModal .modal-content {
    height: 100vh !important;
    border-radius: 0 !important;
    border: none !important;
}
#photoPreviewModal .modal-body {
    background: #1a1a2e; padding: 0;
    display: flex; align-items: center; justify-content: center; min-height: 400px;
}
</style>

<div class="pcoded-inner-content">
    <div class="main-body"><div class="page-wrapper"><div class="page-body">

        {{-- Header --}}
        <div class="drv-edit-header">
            <div class="d-flex align-items-start justify-content-between flex-wrap" style="gap:12px;">
                <div style="position:relative;z-index:1;">
                    <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:4px 14px;font-size:12px;font-weight:700;letter-spacing:.5px;margin-bottom:8px;">
                        <i class="ti-pencil"></i> Edit Driver
                    </div>
                    <h4>{{ $driver->name }}</h4>
                    <div class="sub">Update driver details, documents &amp; photos.</div>
                </div>
                <div style="position:relative;z-index:1;">
                    <a href="{{ route('driver') }}"
                       style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
                        <i class="ti-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        @include('partials.flash')

        <form id="editDriverForm" action="{{ route('driver.update', $driver->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- 1. Personal Information --}}
            <div class="form-card">
                <div class="form-card-header">
                    <i class="ti-id-badge" style="color:#667eea;"></i>
                    <h6>Personal Information</h6>
                </div>
                <div class="form-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-drv">
                                <label>Driver Name <span class="req">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $driver->name) }}" placeholder="Enter full name" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-drv">
                                <label>Date of Birth <span class="req">*</span></label>
                                <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror"
                                    value="{{ old('dob', $driver->dob) }}" required>
                                @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Driver Type --}}
                    @php $currentType = old('driver_type', $driver->driver_type ?? 'own'); @endphp
                    <div class="form-group-drv">
                        <label>Driver Type</label>
                        <div class="d-flex" style="gap:10px;flex-wrap:wrap;">
                            <label class="driver-type-card {{ $currentType === 'own' ? 'active' : '' }}" for="dt_own">
                                <input type="radio" name="driver_type" id="dt_own" value="own"
                                    {{ $currentType === 'own' ? 'checked' : '' }} style="display:none;">
                                <div class="dtc-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-truck"></i></div>
                                <div><div class="dtc-label">Own Driver</div><div class="dtc-sub">Company payroll</div></div>
                            </label>
                            <label class="driver-type-card {{ $currentType === 'rental' ? 'active' : '' }}" for="dt_rental">
                                <input type="radio" name="driver_type" id="dt_rental" value="rental"
                                    {{ $currentType === 'rental' ? 'checked' : '' }} style="display:none;">
                                <div class="dtc-icon" style="background:#fffbeb;color:#d97706;"><i class="ti-refresh"></i></div>
                                <div><div class="dtc-label">Rental Driver</div><div class="dtc-sub">Hired / external</div></div>
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-drv">
                                <label>Mobile Number <span class="req">*</span></label>
                                <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                                    value="{{ old('mobile', $driver->mobile) }}" placeholder="Enter mobile number" required>
                                @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-drv">
                                <label>License Number <span class="req">*</span></label>
                                <input type="text" name="license_number" class="form-control @error('license_number') is-invalid @enderror"
                                    value="{{ old('license_number', $driver->license_number) }}" placeholder="License number" maxlength="16" required>
                                @error('license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-drv">
                                <label>Aadhar Number <span class="req">*</span></label>
                                <input type="text" name="aadhar_number" class="form-control @error('aadhar_number') is-invalid @enderror"
                                    value="{{ old('aadhar_number', $driver->aadhar_number) }}" placeholder="12-digit Aadhar" maxlength="12" required>
                                @error('aadhar_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-drv">
                                <label>PAN Number <span class="req">*</span></label>
                                <input type="text" name="pan_number" class="form-control @error('pan_number') is-invalid @enderror"
                                    value="{{ old('pan_number', $driver->pan_number) }}" placeholder="PAN number" maxlength="10" required>
                                @error('pan_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Address Details --}}
            <div class="form-card">
                <div class="form-card-header">
                    <i class="ti-map-alt" style="color:#667eea;"></i>
                    <h6>Address Details</h6>
                </div>
                <div class="form-card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group-drv">
                                <label>State</label>
                                <select name="state" id="stateSelect" class="form-control select2 @error('state') is-invalid @enderror" data-placeholder="Select State">
                                    <option value="">Loading states...</option>
                                </select>
                                @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group-drv">
                                <label>District</label>
                                <select name="district" id="districtSelect" class="form-control select2 @error('district') is-invalid @enderror" data-placeholder="Select District">
                                    <option value="">Select State First</option>
                                </select>
                                @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group-drv">
                                <label>City</label>
                                <input type="text" name="city" id="cityInput"
                                    class="form-control @error('city') is-invalid @enderror"
                                    value="{{ old('city', $driver->city) }}" placeholder="Enter city / town">
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group-drv">
                                <label>Postal Code</label>
                                <input type="text" name="postal_code" class="form-control"
                                    value="{{ old('postal_code', $driver->postal_code) }}" placeholder="PIN Code" maxlength="6">
                            </div>
                        </div>
                    </div>
                    <div class="form-group-drv mb-0">
                        <label>Full Address</label>
                        <textarea name="address" rows="2" class="form-control"
                            placeholder="Enter full address">{{ old('address', $driver->address) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- 3. Documents & Photos --}}
            <div class="form-card">
                <div class="form-card-header">
                    <i class="ti-files" style="color:#667eea;"></i>
                    <h6>Documents &amp; Photos</h6>
                    <small class="text-muted ml-auto">JPG / PNG / PDF — max 2 MB each</small>
                </div>
                <div class="form-card-body">
                    <div class="row">
                        @php
                            $photoFields = [
                                'driver_photo'  => ['Driver Photo',   'ti-user',       'jpg,jpeg,png'],
                                'aadhar_photo'  => ['Aadhar Card',    'ti-id-badge',   'jpg,jpeg,png,pdf'],
                                'pan_photo'     => ['PAN Card',       'ti-credit-card','jpg,jpeg,png,pdf'],
                                'license_photo' => ['Driving License','ti-car',        'jpg,jpeg,png,pdf'],
                            ];
                        @endphp
                        @foreach($photoFields as $field => [$label, $icon, $accept])
                        @php $existing = $driver->$field; $ext = $existing ? pathinfo($existing, PATHINFO_EXTENSION) : ''; @endphp
                        <div class="col-md-3 mb-3">
                            <div class="photo-upload-card {{ $existing ? 'has-file' : '' }}" id="card_{{ $field }}">
                                <i class="{{ $icon }} card-icon"></i>
                                <span class="card-title">{{ $label }}</span>
                                <p class="card-hint">{{ strtoupper(str_replace(',', ' / ', $accept)) }}</p>

                                @if($existing)
                                    @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif','webp']))
                                        <img src="{{ asset('storage/' . $existing) }}"
                                            class="existing-photo-thumb"
                                            onclick="previewPhoto('{{ asset('storage/' . $existing) }}', '{{ $label }}')"
                                            title="Click to preview">
                                    @else
                                        <div class="existing-badge"
                                            onclick="previewPhoto('{{ asset('storage/' . $existing) }}', '{{ $label }}')"
                                            title="Click to preview">
                                            <i class="ti-eye"></i> View File
                                        </div>
                                    @endif
                                @endif

                                <button type="button" class="btn-upload-badge"
                                    onclick="document.getElementById('file_{{ $field }}').click()">
                                    <i class="ti-upload mr-1"></i>{{ $existing ? 'Replace' : 'Upload' }}
                                </button>
                                <div class="chosen-name" id="name_{{ $field }}"></div>
                                <input type="file" name="{{ $field }}" id="file_{{ $field }}"
                                    accept=".{{ str_replace(',', ',.', $accept) }}"
                                    style="display:none;"
                                    onchange="onPhotoChosen(this, '{{ $field }}')">
                                <input type="hidden" name="{{ $field }}_temp" id="{{ $field }}_temp"
                                    value="{{ old($field . '_temp') }}">
                            </div>
                            @error($field)<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 4. Remarks --}}
            <div class="form-card">
                <div class="form-card-header">
                    <i class="ti-comment" style="color:#667eea;"></i>
                    <h6>Remarks</h6>
                </div>
                <div class="form-card-body">
                    <div class="form-group-drv mb-0">
                        <textarea name="remarks" rows="2" class="form-control"
                            placeholder="Any additional notes...">{{ old('remarks', $driver->remarks) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="action-bar">
                <a href="{{ route('driver') }}" class="btn btn-secondary btn-sm">
                    <i class="ti-close mr-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary btn-sm" id="updateDriverBtn">
                    <i class="ti-save mr-1"></i> Update Driver
                </button>
            </div>
        </form>

    </div></div></div>
</div>

{{-- Photo Preview Modal --}}
<div class="modal fade" id="photoPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a2340;padding:16px 20px;">
                <h5 class="modal-title text-white"><i class="ti-file mr-2"></i><span id="photoPreviewTitle">Preview</span></h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="photoPreviewBody" style="overflow-y:auto;padding:0;flex:1;background:#1a1a2e;display:flex;align-items:center;justify-content:center;"></div>
            <div class="modal-footer">
                <a id="photoPreviewLink" href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="ti-new-window mr-1"></i> Open in New Tab
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="ti-close mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
    var API_STATES    = @json(route('api.general.states'));
    var API_DISTRICTS = @json(route('api.general.districts'));

    var stateEl    = document.getElementById('stateSelect');
    var districtEl = document.getElementById('districtSelect');
    var savedState    = @json(old('state', $driver->state ?? ''));
    var savedDistrict = @json(old('district', $driver->district ?? ''));

    function apiFetch(url, params) {
        var qs = params ? '?' + new URLSearchParams(params) : '';
        return fetch(url + qs, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        }).then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        });
    }

    function fillSelect(el, placeholder, items, selectVal) {
        var $el = $(el);
        if ($el.data('select2')) {
            $el.select2('destroy');
        }
        $el.empty().append('<option value="">' + placeholder + '</option>');
        items.forEach(function (name) {
            var sel = name === selectVal;
            $el.append(new Option(name, name, false, sel));
        });
        $el.select2({ width: '100%', allowClear: true, placeholder: placeholder });
    }

    function loadDistricts(preselectDistrict) {
        var state = stateEl.value;
        if (!state) {
            fillSelect(districtEl, 'Select State First', [], null);
            return;
        }
        var $dist = $(districtEl);
        if ($dist.data('select2')) { $dist.select2('destroy'); }
        $dist.empty().append('<option value="">Loading…</option>');
        $dist.select2({ width: '100%', allowClear: true, placeholder: 'Loading…' });

        apiFetch(API_DISTRICTS, { state: state })
            .then(function (data) {
                var list = Array.isArray(data) ? data : (data.districts || []);
                if ($dist.data('select2')) { $dist.select2('destroy'); }
                $dist.empty().append('<option value="">Select District</option>');
                list.forEach(function (name) {
                    $dist.append(new Option(name, name, false, name === preselectDistrict));
                });
                $dist.select2({ width: '100%', allowClear: true, placeholder: 'Select District' });
            })
            .catch(function () {
                if ($dist.data('select2')) { $dist.select2('destroy'); }
                $dist.empty().append('<option value="">Error loading</option>');
                $dist.select2({ width: '100%', allowClear: true, placeholder: 'Error loading' });
            });
    }

    apiFetch(API_STATES)
        .then(function (data) {
            var list = Array.isArray(data) ? data : (data.states || []);
            fillSelect(stateEl, 'Select State', list, savedState || null);
            if (savedState) {
                loadDistricts(savedDistrict || null);
            }
        })
        .catch(function () {
            fillSelect(stateEl, 'Error loading states', [], null);
        });

    $(stateEl).on('select2:select', function () {
        loadDistricts(null);
    });
});

/* ── Photo upload ── */
var uploadXhr = {};
function onPhotoChosen(input, field) {
    var card   = document.getElementById('card_'  + field);
    var nameEl = document.getElementById('name_'  + field);
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    if (file.size > 2 * 1024 * 1024) { alert('File exceeds 2 MB limit.'); input.value = ''; return; }
    // Abort previous upload for this field
    if (uploadXhr[field]) uploadXhr[field].abort();

    nameEl.textContent = '📎 ' + file.name;
    nameEl.style.display = 'block';
    card.classList.add('has-file');

    // Upload via AJAX immediately
    var fd = new FormData();
    fd.append('file', file);
    var xhr = new XMLHttpRequest();
    uploadXhr[field] = xhr;
    xhr.open('POST', '{{ route("driver.upload-temp") }}');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Accept', 'application/json');
    var csrfToken = document.querySelector('input[name="_token"]');
    if (csrfToken) xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.value);

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var data = JSON.parse(xhr.responseText);
                document.getElementById(field + '_temp').value = data.path;
            } catch (e) {}
        }
    };
    xhr.send(fd);
}

// Restore file previews after validation error
document.addEventListener('DOMContentLoaded', function() {
    var fields = ['driver_photo', 'aadhar_photo', 'pan_photo', 'license_photo'];
    fields.forEach(function(field) {
        var tempPath = document.getElementById(field + '_temp');
        if (tempPath && tempPath.value) {
            var card = document.getElementById('card_' + field);
            var nameEl = document.getElementById('name_' + field);
            card.classList.add('has-file');
            nameEl.textContent = '📎 ' + tempPath.value.split('/').pop();
            nameEl.style.display = 'block';
        }
    });
});

/* ── Photo preview ── */
function previewPhoto(url, label) {
    document.getElementById('photoPreviewTitle').textContent = label;
    document.getElementById('photoPreviewLink').href = url;
    var ext  = url.split('.').pop().toLowerCase();
    var body = document.getElementById('photoPreviewBody');
    if (['jpg','jpeg','png','gif','webp'].indexOf(ext) !== -1) {
        body.innerHTML = '<img src="' + url + '" style="max-width:100%;max-height:calc(100vh - 160px);object-fit:contain;" />';
    } else if (ext === 'pdf') {
        body.innerHTML = '<iframe src="' + url + '" style="width:100%;height:calc(100vh - 160px);border:none;"></iframe>';
    } else {
        body.innerHTML = '<div style="color:#aaa;padding:40px;text-align:center;"><i class="ti-file" style="font-size:48px;display:block;margin-bottom:12px;"></i>Cannot preview.<br><a href="' + url + '" target="_blank" class="btn btn-primary btn-sm mt-3">Open File</a></div>';
    }
    $('#photoPreviewModal').modal('show');
}

$('#photoPreviewModal').on('hidden.bs.modal', function () {
    document.getElementById('photoPreviewBody').innerHTML = '';
});

/* ── Submit spinner ── */
$('#editDriverForm').on('submit', function () {
    var btn = document.getElementById('updateDriverBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="ti-reload mr-1"></i> Saving...';
});

/* ── Driver Type Toggle ── */
document.querySelectorAll('input[name="driver_type"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
        document.querySelectorAll('.driver-type-card').forEach(function (c) { c.classList.remove('active'); });
        if (this.checked) this.closest('.driver-type-card').classList.add('active');
    });
});
</script>
@endpush
