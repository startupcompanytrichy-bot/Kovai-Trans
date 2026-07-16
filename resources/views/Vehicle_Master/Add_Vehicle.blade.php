@extends('layouts.app')

@section('content')
<style>
.veh-header {
    background: linear-gradient(135deg, #1a2340 0%, #303f6e 100%);
    border-radius: 14px; padding: 14px 24px;
    color: #fff; margin-bottom: 18px;
    position: relative; overflow: hidden;
}
.veh-header::before {
    content: ''; position: absolute; top: -40px; right: -40px;
    width: 160px; height: 160px; background: rgba(255,255,255,.05); border-radius: 50%;
}
.veh-header h4 { font-size: 20px; font-weight: 800; margin: 0 0 3px; position: relative; z-index: 1; }
.veh-header .sub { font-size: 13px; opacity: .75; position: relative; z-index: 1; }

.form-card {
    background: #fff; border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    margin-bottom: 20px; overflow: hidden;
}
.form-card-header {
    padding: 14px 20px; border-bottom: 1px solid #f0f2f7;
    background: #fafbff; display: flex; align-items: center; gap: 8px;
}
.form-card-header h6 { margin: 0; font-size: 13px; font-weight: 700; color: #1a2340; }
.form-card-body { padding: 20px; }

.form-group-veh { margin-bottom: 16px; }
.form-group-veh label {
    display: block; font-size: 12px; font-weight: 700;
    color: #596579; margin-bottom: 6px;
}
.form-group-veh .req { color: #e53e3e; }
.form-group-veh .form-control {
    border-color: #d7dce5; border-radius: 8px;
    font-size: 13px; color: #303549; min-height: 42px;
    transition: border-color .15s, box-shadow .15s;
}
.form-group-veh .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102,126,234,.12);
}
.form-group-veh .select2-container { width: 100% !important; }

.action-bar {
    background: #fff; border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    padding: 14px 20px;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 10px;
}

.doc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 14px; }
.doc-card {
    border: 2px dashed #d0d5e8;
    border-radius: 10px; padding: 16px 12px;
    text-align: center; background: #fafbff;
    transition: all .2s; position: relative;
}
.doc-card:hover { border-color: #667eea; background: #f0f3ff; box-shadow: 0 4px 14px rgba(102,126,234,.12); }
.doc-card .doc-icon { font-size: 26px; color: #667eea; margin-bottom: 6px; display: block; }
.doc-card .doc-title { font-size: 12px; font-weight: 700; color: #303549; margin-bottom: 4px; display: block; }
.doc-card .doc-hint { font-size: 11px; color: #adb5bd; margin-bottom: 8px; }
.btn-upload-badge {
    display: inline-block; background: #667eea; color: #fff;
    border-radius: 20px; font-size: 11px; font-weight: 600;
    padding: 4px 14px; margin-top: 7px; cursor: pointer;
    border: none; outline: none; transition: background .15s;
}
.btn-upload-badge:hover { background: #5a6fd6; }
.chosen-name { font-size: 11px; color: #667eea; font-weight: 600; margin-top: 5px; word-break: break-all; display: none; }
</style>
<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">

                <div class="veh-header">
                    <div class="d-flex align-items-start justify-content-between flex-wrap" style="gap:12px;">
                        <div style="position:relative;z-index:1;">
                            <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:20px;padding:4px 14px;font-size:12px;font-weight:700;letter-spacing:.5px;margin-top: 6px;">
                                <i class="ti-plus"></i> Add Vehicle
                            </div>
                        </div>
                        <div style="position:relative;z-index:1;">
                            <a href="{{ route('vehicle') }}"
                                onclick="event.preventDefault(); softNav('{{ route('vehicle') }}');"
                                style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
                                <i class="ti-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <div id="vehFormErrors"></div>

                <form id="vehicleForm" method="POST" action="{{ route('vehicle.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Vehicle Information --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="ti-truck" style="color:#667eea;"></i>
                            <h6>Vehicle Information</h6>
                        </div>
                        <div class="form-card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>Owner Type <span class="req">*</span></label>
                                        <select name="owner_type" class="form-control select2-veh" required>
                                            <option value="Own" {{ old('owner_type', 'Own') == 'Own' ? 'selected' : '' }}>Own</option>
                                            <option value="Rental" {{ old('owner_type') == 'Rental' ? 'selected' : '' }}>Rental</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>Supplier</label>
                                        <select name="supplier_id" class="form-control select2-veh">
                                            <option value="">Select Supplier (Optional)</option>
                                            @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>Vehicle Type <span class="req">*</span></label>
                                        <select name="vehicle_type" class="form-control select2-veh" required>
                                            <option value="">Select Vehicle Type</option>
                                            @foreach(['lorry'=>'Lorry','truck'=>'Truck','trailer'=>'Trailer','mini_truck'=>'Mini Truck','container'=>'Container','tipper'=>'Tipper'] as $val => $lbl)
                                            <option value="{{ $val }}" {{ old('vehicle_type') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-veh">
                                        <label>Vehicle Model / Name <span class="req">*</span></label>
                                        <input type="text" name="vehicle_name" class="form-control"
                                            value="{{ old('vehicle_name') }}" placeholder="e.g. Ashok Leyland 2518" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-veh">
                                        <label>Vehicle Number <span class="req">*</span></label>
                                        <input type="text" name="vehicle_number" class="form-control"
                                            value="{{ old('vehicle_number') }}" placeholder="TN 01 AB 1234" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-veh">
                                        <label>Asset Make</label>
                                        <input type="text" name="asset_make" class="form-control"
                                            value="{{ old('asset_make') }}" placeholder="e.g. EICHER, TATA">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-veh">
                                        <label>Asset Type</label>
                                        <input type="text" name="asset_type" class="form-control"
                                            value="{{ old('asset_type') }}" placeholder="e.g. PRO 2110">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Technical Details --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="ti-settings" style="color:#667eea;"></i>
                            <h6>Technical Details</h6>
                        </div>
                        <div class="form-card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>Engine Number</label>
                                        <input type="text" name="engine_number" class="form-control"
                                            value="{{ old('engine_number') }}" placeholder="Engine Number" maxlength="50">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>Chassis Number</label>
                                        <input type="text" name="chassis_number" class="form-control"
                                            value="{{ old('chassis_number') }}" placeholder="Chassis Number" maxlength="50">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>RC Number</label>
                                        <input type="text" name="rc_number" class="form-control"
                                            value="{{ old('rc_number') }}" placeholder="RC Number" maxlength="50">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Vehicle Permit --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="ti-file" style="color:#667eea;"></i>
                            <h6>Vehicle Permit</h6>
                        </div>
                        <div class="form-card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>Permit Type <span class="req">*</span></label>
                                        <select name="permit_type" class="form-control select2-veh" required>
                                            <option value="">Select Permit Type</option>
                                            @foreach(['national'=>'National Permit','state'=>'State Permit','local'=>'Local Permit','annual'=>'Annual Permit','temporary'=>'Temporary Permit','others'=>'Others'] as $val => $lbl)
                                            <option value="{{ $val }}" {{ old('permit_type') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>Permit Number</label>
                                        <input type="text" name="permit_number" class="form-control"
                                            value="{{ old('permit_number') }}" placeholder="Permit Number" maxlength="50">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Expiry Dates --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="ti-calendar" style="color:#667eea;"></i>
                            <h6>Expiry Dates</h6>
                        </div>
                        <div class="form-card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>Insurance Expiry <span class="req">*</span></label>
                                        <input type="date" name="insurance_expiry_date" class="form-control"
                                            value="{{ old('insurance_expiry_date') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>Fitness Expiry <span class="req">*</span></label>
                                        <input type="date" name="fitness_expiry_date" class="form-control"
                                            value="{{ old('fitness_expiry_date') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>Permit Expiry</label>
                                        <input type="date" name="permit_expiry_date" class="form-control"
                                            value="{{ old('permit_expiry_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>PUC Expiry</label>
                                        <input type="date" name="puc_expiry_date" class="form-control"
                                            value="{{ old('puc_expiry_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>Permit Date</label>
                                        <input type="date" name="permit_date" class="form-control"
                                            value="{{ old('permit_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>National Permit Date</label>
                                        <input type="date" name="national_permit_date" class="form-control"
                                            value="{{ old('national_permit_date') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Documents --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="ti-files" style="color:#667eea;"></i>
                            <h6>Vehicle Documents</h6>
                            <small class="text-muted ml-auto">PDF / JPG / PNG — max 5 MB each</small>
                        </div>
                        <div class="form-card-body">
                            <div class="doc-grid">
                                @foreach($docTypes as $type => $label)
                                <div class="doc-card" id="card_{{ $type }}">
                                    <i class="{{ $docIcons[$type] ?? 'icofont icofont-file-document' }} doc-icon"></i>
                                    <span class="doc-title">{{ $label }}</span>
                                    <p class="doc-hint">PDF / JPG / PNG</p>
                                    <button type="button" class="btn-upload-badge"
                                        onclick="document.getElementById('file_{{ $type }}').click()">
                                        <i class="ti-upload mr-1"></i>Upload
                                    </button>
                                    <div class="chosen-name" id="name_{{ $type }}"></div>
                                    <input type="file" name="doc_{{ $type }}" id="file_{{ $type }}"
                                        accept=".pdf,.jpg,.jpeg,.png" style="display:none;"
                                        onchange="onFileChosen(this, '{{ $type }}')">
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="action-bar">
                        <a href="{{ route('vehicle') }}" class="btn btn-secondary btn-sm"
                           onclick="event.preventDefault(); softNav('{{ route('vehicle') }}');">
                            <i class="ti-close mr-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm" id="submitBtn">
                            <i class="ti-save mr-1"></i> Save Vehicle
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
    function onFileChosen(input, type) {
        var nameEl = document.getElementById('name_' + type);
        if (input.files && input.files[0]) {
            var file = input.files[0];
            if (file.size > 5 * 1024 * 1024) {
                alert('File "' + file.name + '" exceeds 5 MB limit.');
                input.value = '';
                return;
            }
            nameEl.textContent = file.name;
            nameEl.style.display = 'block';
        } else {
            nameEl.textContent = '';
            nameEl.style.display = 'none';
        }
    }

    $(document).ready(function() {
        $('.select2-veh').select2({
            width: '100%'
        });

        document.getElementById('vehicleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            var btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="ti-reload mr-1"></i> Saving...';
            document.getElementById('vehFormErrors').innerHTML = '';

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                })
                .then(function(r) {
                    if (r.ok) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Vehicle saved successfully', '', {
                                onHidden: function() {
                                    softNav('{{ route("vehicle") }}');
                                }
                            });
                        } else {
                            softNav('{{ route("vehicle") }}');
                        }
                        return;
                    }
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ti-save mr-1"></i> Save Vehicle';
                    r.json().then(function(data) {
                        var html = '<div class="alert alert-danger"><ul class="mb-0">';
                        if (data.errors) {
                            Object.keys(data.errors).forEach(function(f) {
                                html += '<li>' + data.errors[f][0] + '</li>';
                            });
                        } else if (data.message) {
                            html += '<li>' + data.message + '</li>';
                        } else {
                            html += '<li>Something went wrong.</li>';
                        }
                        html += '</ul></div>';
                        document.getElementById('vehFormErrors').innerHTML = html;
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }).catch(function() {
                        document.getElementById('vehFormErrors').innerHTML = '<div class="alert alert-danger">Error ' + r.status + ': Something went wrong.</div>';
                    });
                })
                .catch(function() {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ti-save mr-1"></i> Save Vehicle';
                    document.getElementById('vehFormErrors').innerHTML = '<div class="alert alert-danger">Network error. Please try again.</div>';
                });
        });
    });
</script>
@endpush