@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/parties/parties.css') }}">

<style>
    /* ── Header ── */
    .drv-add-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 14px;
        padding: 22px 28px;
        color: #fff;
        margin-bottom: 22px;
        position: relative;
        overflow: hidden;
    }

    .drv-add-header::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, .06);
        border-radius: 50%;
    }

    .drv-add-header h4 {
        font-size: 20px;
        font-weight: 800;
        margin: 0 0 3px;
        position: relative;
        z-index: 1;
    }

    .drv-add-header .sub {
        font-size: 13px;
        opacity: .75;
        position: relative;
        z-index: 1;
    }

    /* ── Form cards ── */
    .form-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .form-card-header {
        padding: 14px 20px;
        border-bottom: 1px solid #f0f2f7;
        background: #fafbff;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-card-header h6 {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #1a2340;
    }

    .form-card-body {
        padding: 20px;
    }

    /* ── Form fields ── */
    .form-group-drv {
        margin-bottom: 16px;
    }

    .form-group-drv label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #596579;
        margin-bottom: 6px;
    }

    .form-group-drv .req {
        color: #e53e3e;
    }

    .form-group-drv .form-control {
        border-color: #d7dce5;
        border-radius: 8px;
        font-size: 13px;
        color: #303549;
        min-height: 42px;
        transition: border-color .15s, box-shadow .15s;
    }

    .form-group-drv .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, .12);
    }

    .form-group-drv .select2-container {
        width: 100% !important;
    }

    .form-group-drv .select2-container--default .select2-selection--single {
        min-height: 42px !important;
        height: 42px !important;
        border-color: #d7dce5 !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
    }

    .location-help {
        font-size: 11px;
        color: #6c757d;
        margin-top: 4px;
    }

    /* ── Driver type cards ── */
    .driver-type-card {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px 10px 12px;
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        background: #f7f8fc;
        cursor: pointer;
        transition: all .18s;
        min-width: 160px;
        user-select: none;
    }

    .driver-type-card:hover {
        border-color: #b0bac9;
        background: #eef0f7;
    }

    .driver-type-card.active[for="dt_own"] {
        border-color: #38a169;
        background: #f0fff4;
        box-shadow: 0 2px 10px rgba(56, 161, 105, .12);
    }

    .driver-type-card.active[for="dt_rental"] {
        border-color: #d97706;
        background: #fffbeb;
        box-shadow: 0 2px 10px rgba(217, 119, 6, .12);
    }

    .dtc-icon {
        width: 38px;
        height: 38px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
    }

    .dtc-label {
        font-size: 13px;
        font-weight: 700;
        color: #1a2340;
    }

    .dtc-sub {
        font-size: 11px;
        color: #8a94a6;
        margin-top: 1px;
    }

    /* ── Photo upload cards ── */
    .photo-upload-card {
        border: 2px dashed #d0d5e8;
        border-radius: 10px;
        padding: 16px 12px;
        text-align: center;
        background: #fafbff;
        transition: all .2s;
        cursor: pointer;
    }

    .photo-upload-card:hover {
        border-color: #667eea;
        background: #f0f3ff;
        box-shadow: 0 4px 14px rgba(102, 126, 234, .15);
    }

    .photo-upload-card.has-file {
        border-color: #38a169;
        background: #f0fff4;
        border-style: solid;
    }

    .photo-upload-card .card-icon {
        font-size: 28px;
        color: #667eea;
        margin-bottom: 6px;
        display: block;
    }

    .photo-upload-card.has-file .card-icon {
        color: #38a169;
    }

    .photo-upload-card .card-title {
        font-size: 12px;
        font-weight: 700;
        color: #303549;
        margin-bottom: 4px;
        display: block;
    }

    .photo-upload-card .card-hint {
        font-size: 11px;
        color: #adb5bd;
        margin-bottom: 8px;
    }

    .photo-preview-img {
        width: 72px;
        height: 72px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #38a169;
        margin-bottom: 6px;
        display: none;
    }

    .btn-upload-badge {
        display: inline-block;
        background: #667eea;
        color: #fff;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 14px;
        margin-top: 7px;
        cursor: pointer;
        border: none;
        outline: none;
    }

    .btn-upload-badge:hover {
        background: #5a6fd6;
    }

    .photo-upload-card.has-file .btn-upload-badge {
        background: #38a169;
    }

    .photo-upload-card.has-file .btn-upload-badge:hover {
        background: #2f855a;
    }

    .chosen-name {
        font-size: 11px;
        color: #667eea;
        font-weight: 600;
        margin-top: 5px;
        word-break: break-all;
        display: none;
    }

    /* ── Action bar ── */
    .action-bar {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
</style>

<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">

                {{-- Header --}}
                <div class="drv-add-header">
                    <div class="d-flex align-items-start justify-content-between flex-wrap" style="gap:12px;">
                        <div style="position:relative;z-index:1;">
                            <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:4px 14px;font-size:12px;font-weight:700;letter-spacing:.5px;margin-bottom:8px;">
                                <i class="ti-plus"></i> Add Driver
                            </div>
                            <h4>Register New Driver</h4>
                            <div class="sub">Fill in the details to register a new driver.</div>
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

                <form id="addDriverForm" action="{{ route('driver.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

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
                                            value="{{ old('name') }}" placeholder="Enter full name" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-drv">
                                        <label>Date of Birth <span class="req">*</span></label>
                                        <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror"
                                            value="{{ old('dob') }}" required>
                                        @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Driver Type --}}
                            <div class="form-group-drv">
                                <label>Driver Type</label>
                                <div class="d-flex" style="gap:10px;flex-wrap:wrap;">
                                    <label class="driver-type-card {{ old('driver_type','own') === 'own' ? 'active' : '' }}" for="dt_own">
                                        <input type="radio" name="driver_type" id="dt_own" value="own"
                                            {{ old('driver_type','own') === 'own' ? 'checked' : '' }} style="display:none;">
                                        <div class="dtc-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-truck"></i></div>
                                        <div>
                                            <div class="dtc-label">Own Driver</div>
                                            <div class="dtc-sub">Company payroll</div>
                                        </div>
                                    </label>
                                    <label class="driver-type-card {{ old('driver_type') === 'rental' ? 'active' : '' }}" for="dt_rental">
                                        <input type="radio" name="driver_type" id="dt_rental" value="rental"
                                            {{ old('driver_type') === 'rental' ? 'checked' : '' }} style="display:none;">
                                        <div class="dtc-icon" style="background:#fffbeb;color:#d97706;"><i class="ti-refresh"></i></div>
                                        <div>
                                            <div class="dtc-label">Rental Driver</div>
                                            <div class="dtc-sub">Hired / external</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-drv">
                                        <label>Mobile Number <span class="req">*</span></label>
                                        <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                                            value="{{ old('mobile') }}" placeholder="Enter mobile number" required>
                                        @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-drv">
                                        <label>License Number <span class="req">*</span></label>
                                        <input type="text" name="license_number" class="form-control @error('license_number') is-invalid @enderror"
                                            value="{{ old('license_number') }}" placeholder="e.g. TN0120230001234" required>
                                        @error('license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-drv">
                                        <label>Aadhar Number <span class="req">*</span></label>
                                        <input type="text" name="aadhar_number" class="form-control @error('aadhar_number') is-invalid @enderror"
                                            value="{{ old('aadhar_number') }}" placeholder="12-digit Aadhar number" required>
                                        @error('aadhar_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-drv">
                                        <label>PAN Number <span class="req">*</span></label>
                                        <input type="text" name="pan_number" class="form-control @error('pan_number') is-invalid @enderror"
                                            value="{{ old('pan_number') }}" placeholder="e.g. AAAPL1234C" required>
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
                                {{-- STATE --}}
                                <div class="col-md-3">
                                    <div class="form-group-drv">
                                        <label>State</label>
                                        <select name="state" id="stateSelect"
                                            class="form-control select2 @error('state') is-invalid @enderror"
                                            data-placeholder="Select State">
                                            <option value="">Loading states...</option>
                                        </select>
                                        @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- DISTRICT (loads after State) --}}
                                <div class="col-md-3">
                                    <div class="form-group-drv">
                                        <label>District</label>
                                        <select name="district" id="districtSelect"
                                            class="form-control select2 @error('district') is-invalid @enderror"
                                            data-placeholder="Select District">
                                            <option value="">Select State First</option>
                                        </select>
                                        @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- CITY — free text input --}}
                                <div class="col-md-3">
                                    <div class="form-group-drv">
                                        <label>City</label>
                                        <input type="text" name="city" id="cityInput"
                                            class="form-control @error('city') is-invalid @enderror"
                                            value="{{ old('city') }}" placeholder="Enter city name">
                                        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group-drv">
                                        <label>Postal Code</label>
                                        <input type="text" name="postal_code" class="form-control"
                                            value="{{ old('postal_code') }}" placeholder="PIN Code">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group-drv mb-0">
                                <label>Full Address</label>
                                <textarea name="address" rows="2" class="form-control"
                                    placeholder="Enter full address">{{ old('address') }}</textarea>
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
                                'driver_photo' => ['Driver Photo', 'ti-user', 'jpg,jpeg,png'],
                                'aadhar_photo' => ['Aadhar Card', 'ti-id-badge', 'jpg,jpeg,png,pdf'],
                                'pan_photo' => ['PAN Card', 'ti-credit-card','jpg,jpeg,png,pdf'],
                                'license_photo' => ['Driving License','ti-car', 'jpg,jpeg,png,pdf'],
                                ];
                                @endphp
                                @foreach($photoFields as $field => [$label, $icon, $accept])
                                <div class="col-md-3 mb-3">
                                    <div class="photo-upload-card" id="card_{{ $field }}"
                                        onclick="document.getElementById('file_{{ $field }}').click()">
                                        <img id="preview_{{ $field }}" class="photo-preview-img" src="#" alt="{{ $label }}">
                                        <i class="{{ $icon }} card-icon" id="icon_{{ $field }}"></i>
                                        <span class="card-title">{{ $label }}</span>
                                        <p class="card-hint">{{ strtoupper(str_replace(',', ' / ', $accept)) }}</p>
                                        <span class="btn-upload-badge"><i class="ti-upload mr-1"></i> Upload</span>
                                        <div class="chosen-name" id="name_{{ $field }}"></div>
                                    </div>
                                    <input type="file" name="{{ $field }}" id="file_{{ $field }}"
                                        accept=".{{ str_replace(',', ',.', $accept) }}"
                                        style="display:none;"
                                        onchange="onPhotoChosen(this, '{{ $field }}')">
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
                                    placeholder="Any additional notes...">{{ old('remarks') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="action-bar">
                        <a href="{{ route('driver') }}" class="btn btn-secondary btn-sm">
                            <i class="ti-close mr-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm" id="addDriverBtn">
                            <i class="ti-save mr-1"></i> Save Driver
                        </button>
                    </div>
                </form>

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
        var savedState    = @json(old('state', ''));
        var savedDistrict = @json(old('district', ''));

        /* ── plain fetch → JSON ── */
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

        /* ── load districts for chosen state ── */
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

        /* ── load states on page load ── */
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

        /* ── state change → reload districts ── */
        $(stateEl).on('select2:select', function () {
            loadDistricts(null);
        });

    });


    function onPhotoChosen(input, field) {
        var card = document.getElementById('card_' + field);
        var nameEl = document.getElementById('name_' + field);
        var preview = document.getElementById('preview_' + field);
        var iconEl = document.getElementById('icon_' + field);
        if (input.files && input.files[0]) {
            var file = input.files[0];
            if (file.size > 2 * 1024 * 1024) {
                alert('File exceeds 2 MB limit.');
                input.value = '';
                return;
            }
            nameEl.textContent = '📎 ' + file.name;
            nameEl.style.display = 'block';
            card.classList.add('has-file');
            var ext = file.name.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].indexOf(ext) !== -1) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    iconEl.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                iconEl.style.display = 'block';
            }
        }
    }

    $('#addDriverForm').on('submit', function() {
        var btn = document.getElementById('addDriverBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="ti-reload mr-1"></i> Saving...';
    });

    /* Driver Type Card Toggle */
    document.querySelectorAll('input[name="driver_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.driver-type-card').forEach(function(card) {
                card.classList.remove('active');
            });
            if (this.checked) this.closest('.driver-type-card').classList.add('active');
        });
    });
</script>
@endpush