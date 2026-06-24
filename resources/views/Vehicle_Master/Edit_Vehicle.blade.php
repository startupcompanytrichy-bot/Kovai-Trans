@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/parties/parties.css') }}">

<style>
/* ── Header ── */
.veh-edit-header {
    background: linear-gradient(135deg, #1a2340 0%, #303f6e 100%);
    border-radius: 14px; padding: 22px 28px;
    color: #fff; margin-bottom: 22px;
    position: relative; overflow: hidden;
}
.veh-edit-header::before {
    content: ''; position: absolute; top: -40px; right: -40px;
    width: 160px; height: 160px; background: rgba(255,255,255,.05); border-radius: 50%;
}
.veh-edit-header h4 { font-size: 20px; font-weight: 800; margin: 0 0 3px; position: relative; z-index: 1; }
.veh-edit-header .sub { font-size: 13px; opacity: .75; position: relative; z-index: 1; }

/* ── Form Card ── */
.form-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    margin-bottom: 20px;
    overflow: hidden;
}
.form-card-header {
    padding: 14px 20px;
    border-bottom: 1px solid #f0f2f7;
    background: #fafbff;
    display: flex; align-items: center; gap: 8px;
}
.form-card-header h6 { margin: 0; font-size: 13px; font-weight: 700; color: #1a2340; }
.form-card-body { padding: 20px; }

/* ── Form fields ── */
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

/* ── Expiry status ── */
.expiry-expired { color: #e53e3e; font-weight: 600; font-size: 11px; margin-top: 4px; display: block; }
.expiry-warn    { color: #d97706; font-weight: 600; font-size: 11px; margin-top: 4px; display: block; }
.expiry-ok      { color: #38a169; font-weight: 600; font-size: 11px; margin-top: 4px; display: block; }

/* ── Document cards ── */
.doc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 14px; }
.doc-card {
    border: 2px dashed #d0d5e8;
    border-radius: 10px; padding: 16px 12px;
    text-align: center; background: #fafbff;
    transition: all .2s; position: relative;
}
.doc-card:hover { border-color: #667eea; background: #f0f3ff; box-shadow: 0 4px 14px rgba(102,126,234,.12); }
.doc-card.has-file { border-color: #38a169; background: #f0fff4; border-style: solid; }
.doc-card.has-file:hover { box-shadow: 0 4px 14px rgba(40,167,69,.15); }
.doc-card .doc-icon { font-size: 26px; color: #667eea; margin-bottom: 6px; display: block; }
.doc-card.has-file .doc-icon { color: #38a169; }
.doc-card .doc-title { font-size: 12px; font-weight: 700; color: #303549; margin-bottom: 4px; display: block; }
.doc-card .doc-hint { font-size: 11px; color: #adb5bd; margin-bottom: 8px; }
.existing-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: #e9f7ef; color: #38a169; border: 1px solid #c3e6cb;
    border-radius: 20px; padding: 2px 10px; font-size: 11px; font-weight: 600;
    margin-bottom: 5px; cursor: pointer;
}
.existing-badge:hover { background: #d4edda; }
.delete-doc-row { margin-top: 6px; display: flex; align-items: center; justify-content: center; gap: 5px; }
.delete-doc-row label { font-size: 11px; color: #e53e3e; margin: 0; cursor: pointer; }
.delete-doc-row input[type="checkbox"] { cursor: pointer; accent-color: #e53e3e; }
.btn-upload-badge {
    display: inline-block; background: #667eea; color: #fff;
    border-radius: 20px; font-size: 11px; font-weight: 600;
    padding: 4px 14px; margin-top: 7px; cursor: pointer;
    border: none; outline: none; transition: background .15s;
}
.btn-upload-badge:hover { background: #5a6fd6; }
.doc-card.has-file .btn-upload-badge { background: #38a169; }
.doc-card.has-file .btn-upload-badge:hover { background: #2f855a; }
.chosen-name { font-size: 11px; color: #667eea; font-weight: 600; margin-top: 5px; word-break: break-all; display: none; }

/* ── Footer action bar ── */
.action-bar {
    background: #fff; border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    padding: 14px 20px;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 10px;
}

/* ── Doc Preview Modal ── */
#docPreviewModal .modal-body {
    background: #1a1a2e; padding: 0;
    display: flex; align-items: center; justify-content: center; min-height: 400px;
}
</style>

<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">

                {{-- Header --}}
                <div class="veh-edit-header">
                    <div class="d-flex align-items-start justify-content-between flex-wrap" style="gap:12px;">
                        <div style="position:relative;z-index:1;">
                            <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:20px;padding:4px 14px;font-size:12px;font-weight:700;letter-spacing:.5px;margin-bottom:8px;">
                                <i class="ti-pencil"></i> Edit Vehicle
                            </div>
                            <h4>{{ $vehicle->vehicle_number }}</h4>
                            <div class="sub">{{ $vehicle->vehicle_name ?? 'Update details, documents &amp; expiry dates' }}</div>
                        </div>
                        <div style="position:relative;z-index:1;">
                            <a href="{{ route('vehicle') }}"
                               style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
                                <i class="ti-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                @include('partials.flash')

                <form id="editForm" action="{{ route('vehicle.update', $vehicle->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Vehicle Information --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="ti-truck" style="color:#667eea;"></i>
                            <h6>Vehicle Information</h6>
                        </div>
                        <div class="form-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-veh">
                                        <label>Vehicle Name</label>
                                        <input type="text" name="vehicle_name" class="form-control"
                                            value="{{ old('vehicle_name', $vehicle->vehicle_name) }}"
                                            placeholder="e.g. Ashok Leyland">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-veh">
                                        <label>Vehicle Number <span class="req">*</span></label>
                                        <input type="text" name="vehicle_number" class="form-control"
                                            value="{{ old('vehicle_number', $vehicle->vehicle_number) }}"
                                            placeholder="TN 01 AB 1234" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-veh">
                                        <label>Owner Type</label>
                                        <select name="owner_type" class="form-control select2-edit">
                                            <option value="">Select Owner Type</option>
                                            <option value="Own"    {{ old('owner_type', $vehicle->owner_type) == 'Own'    ? 'selected' : '' }}>Own Vehicle</option>
                                            <option value="Rental" {{ old('owner_type', $vehicle->owner_type) == 'Rental' ? 'selected' : '' }}>Rental Vehicle</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-veh">
                                        <label>Supplier</label>
                                        <select name="supplier_id" class="form-control select2-edit">
                                            <option value="">Select Supplier (Optional)</option>
                                            @foreach($suppliers as $s)
                                                <option value="{{ $s->id }}" {{ old('supplier_id', $vehicle->supplier_id) == $s->id ? 'selected' : '' }}>
                                                    {{ $s->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-veh">
                                        <label>Vehicle Type</label>
                                        <select name="vehicle_type" class="form-control select2-edit">
                                            <option value="">Select Vehicle Type</option>
                                            @foreach(['lorry'=>'Lorry','truck'=>'Truck','trailer'=>'Trailer','mini_truck'=>'Mini Truck','container'=>'Container','tipper'=>'Tipper'] as $val => $lbl)
                                            <option value="{{ $val }}" {{ old('vehicle_type', $vehicle->vehicle_type) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group-veh">
                                        <label>Asset Make</label>
                                        <input type="text" name="asset_make" class="form-control"
                                            value="{{ old('asset_make', $vehicle->asset_make) }}"
                                            placeholder="e.g. EICHER, TATA">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group-veh">
                                        <label>Asset Type</label>
                                        <input type="text" name="asset_type" class="form-control"
                                            value="{{ old('asset_type', $vehicle->asset_type) }}"
                                            placeholder="e.g. PRO 2110">
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
                                            value="{{ old('engine_number', $vehicle->engine_number) }}" placeholder="Engine Number">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>Chassis Number</label>
                                        <input type="text" name="chassis_number" class="form-control"
                                            value="{{ old('chassis_number', $vehicle->chassis_number) }}" placeholder="Chassis Number">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-veh">
                                        <label>RC Number</label>
                                        <input type="text" name="rc_number" class="form-control"
                                            value="{{ old('rc_number', $vehicle->rc_number) }}" placeholder="RC Number">
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
                                @php
                                    $expiryFields = [
                                        'insurance_expiry_date' => 'Insurance Expiry',
                                        'fitness_expiry_date'   => 'Fitness Expiry',
                                        'permit_expiry_date'    => 'Permit Expiry',
                                        'puc_expiry_date'       => 'PUC Expiry',
                                    ];
                                @endphp
                                @foreach($expiryFields as $field => $label)
                                <div class="col-md-3">
                                    <div class="form-group-veh">
                                        <label>{{ $label }}</label>
                                        <input type="date" name="{{ $field }}" class="form-control expiry-input" id="{{ $field }}"
                                            value="{{ old($field, $vehicle->$field ? date('Y-m-d', strtotime($vehicle->$field)) : '') }}">
                                        <span class="expiry-status" id="status_{{ $field }}"></span>
                                    </div>
                                </div>
                                @endforeach
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
                                @php $existing = $documents->get($type); @endphp
                                <div class="doc-card {{ $existing ? 'has-file' : '' }}" id="card_{{ $type }}">
                                    <i class="{{ $docIcons[$type] ?? 'icofont icofont-file-document' }} doc-icon"></i>
                                    <span class="doc-title">{{ $label }}</span>
                                    <p class="doc-hint">PDF / JPG / PNG</p>

                                    @if($existing)
                                    <div class="existing-badge"
                                        onclick="previewDoc('{{ asset('storage/' . $existing->file_path) }}', '{{ $existing->file_extension }}', '{{ $label }}')"
                                        title="Click to preview">
                                        <i class="ti-eye"></i>
                                        {{ Str::limit($existing->file_name, 16) }}
                                    </div>
                                    <div style="font-size:10px;color:#aaa;margin-bottom:4px;">
                                        {{ $existing->file_size_human }} &bull; {{ $existing->created_at->format('d M Y') }}
                                    </div>
                                    <div class="delete-doc-row">
                                        <input type="checkbox" name="delete_doc_{{ $type }}" id="del_{{ $type }}" value="1"
                                            onchange="toggleDeleteDoc('{{ $type }}', this.checked)">
                                        <label for="del_{{ $type }}"><i class="ti-trash text-danger"></i> Remove</label>
                                    </div>
                                    @endif

                                    <button type="button" class="btn-upload-badge"
                                        onclick="document.getElementById('file_{{ $type }}').click()">
                                        <i class="ti-upload mr-1"></i>{{ $existing ? 'Replace' : 'Upload' }}
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
                        <a href="{{ route('vehicle') }}" class="btn btn-secondary btn-sm">
                            <i class="ti-close mr-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm" id="updateBtn">
                            <i class="ti-save mr-1"></i> Update Vehicle
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{{-- Doc Preview Modal --}}
<div class="modal fade" id="docPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document" style="margin:0 0 0 auto;width:100%;max-width:700px;height:100%;">
        <div class="modal-content" style="height:100vh;border-radius:0;display:flex;flex-direction:column;border:none;">
            <div class="modal-header" style="background:#1a2340;padding:16px 20px;">
                <h5 class="modal-title text-white"><i class="ti-file mr-2"></i><span id="docPreviewTitle">Document Preview</span></h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="docPreviewBody" style="overflow-y:auto;padding:0;flex:1;background:#1a1a2e;display:flex;align-items:center;justify-content:center;"></div>
            <div class="modal-footer">
                <a id="docPreviewOpenLink" href="#" target="_blank" class="btn btn-sm btn-outline-primary">
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
$(document).ready(function () {
    $('.select2-edit').select2({ width: '100%' });
    $('.expiry-input').each(function () { checkExpiry(this); });
    $('.expiry-input').on('change', function () { checkExpiry(this); });
    $('#editForm').on('submit', function () {
        var btn = document.getElementById('updateBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="ti-reload mr-1"></i> Saving...';
    });
    $('#docPreviewModal').on('hidden.bs.modal', function () {
        document.getElementById('docPreviewBody').innerHTML = '';
    });
});

function onFileChosen(input, type) {
    var card = document.getElementById('card_' + type);
    var nameEl = document.getElementById('name_' + type);
    if (input.files && input.files[0]) {
        var file = input.files[0];
        if (file.size > 5 * 1024 * 1024) {
            alert('File "' + file.name + '" exceeds 5 MB limit.');
            input.value = '';
            return;
        }
        nameEl.textContent = '📎 ' + file.name;
        nameEl.style.display = 'block';
        card.classList.add('has-file');
        var delChk = document.getElementById('del_' + type);
        if (delChk) delChk.checked = false;
        card.style.opacity = '1';
    } else {
        nameEl.textContent = '';
        nameEl.style.display = 'none';
    }
}

function toggleDeleteDoc(type, checked) {
    document.getElementById('card_' + type).style.opacity = checked ? '0.5' : '1';
}

function checkExpiry(input) {
    var statusEl = document.getElementById('status_' + input.id);
    if (!statusEl) return;
    if (!input.value) { statusEl.textContent = ''; return; }
    var today  = new Date(); today.setHours(0,0,0,0);
    var expiry = new Date(input.value);
    var diff   = Math.ceil((expiry - today) / 86400000);
    if (diff < 0) {
        statusEl.className = 'expiry-expired';
        statusEl.textContent = '⚠ Expired ' + Math.abs(diff) + ' day(s) ago';
    } else if (diff <= 30) {
        statusEl.className = 'expiry-warn';
        statusEl.textContent = '⚡ Expires in ' + diff + ' day(s)';
    } else {
        statusEl.className = 'expiry-ok';
        statusEl.textContent = '✓ Valid — ' + diff + ' day(s) left';
    }
}

function previewDoc(url, ext, label) {
    document.getElementById('docPreviewTitle').textContent = label;
    document.getElementById('docPreviewOpenLink').href = url;
    var body = document.getElementById('docPreviewBody');
    var imageExts = ['jpg','jpeg','png','gif','webp','bmp'];
    var extLower = (ext || '').toLowerCase();
    if (imageExts.indexOf(extLower) !== -1) {
        body.innerHTML = '<img src="' + url + '" style="max-width:100%;max-height:calc(100vh - 160px);object-fit:contain;" />';
    } else if (extLower === 'pdf') {
        body.innerHTML = '<iframe src="' + url + '" style="width:100%;height:calc(100vh - 160px);border:none;"></iframe>';
    } else {
        body.innerHTML = '<div style="color:#aaa;padding:40px;text-align:center;"><i class="ti-file" style="font-size:48px;display:block;margin-bottom:12px;"></i>Preview not available.<br><a href="' + url + '" target="_blank" class="btn btn-primary btn-sm mt-3"><i class="ti-new-window mr-1"></i> Open File</a></div>';
    }
    $('#docPreviewModal').modal('show');
}
</script>
@endpush
