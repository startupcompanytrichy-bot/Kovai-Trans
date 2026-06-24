@extends('layouts.app')

@section('content')
<style>
/* ── Header ── */
.veh-view-header{background:linear-gradient(135deg,#1a2340 0%,#303f6e 100%);border-radius:14px;padding:22px 28px;color:#fff;margin-bottom:22px;position:relative;overflow:hidden;}
.veh-view-header::before{content:'';position:absolute;top:-40px;right:-40px;width:160px;height:160px;background:rgba(255,255,255,.05);border-radius:50%;}
.veh-view-header h4{font-size:20px;font-weight:800;margin:0 0 3px;position:relative;z-index:1;}
.veh-view-header .sub{font-size:13px;opacity:.75;position:relative;z-index:1;}
/* ── Info Card ── */
.info-card{background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.07);margin-bottom:20px;overflow:hidden;}
.info-card-header{padding:14px 20px;border-bottom:1px solid #f0f2f7;background:#fafbff;display:flex;align-items:center;gap:8px;}
.info-card-header h6{margin:0;font-size:13px;font-weight:700;color:#1a2340;}
.info-card-body{padding:20px;}
/* ── Field display ── */
.field-group{margin-bottom:16px;}
.field-label{font-size:11px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;}
.field-value{font-size:13px;font-weight:600;color:#303549;padding:9px 12px;background:#f8fafc;border:1px solid #edf0f7;border-radius:8px;min-height:40px;display:flex;align-items:center;word-break:break-all;}
.field-value.empty{color:#b0bac9;font-weight:400;}
/* ── Expiry badge ── */
.exp-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;}
.exp-ok{background:#f0fff4;color:#38a169;}
.exp-warn{background:#fff8e6;color:#d97706;}
.exp-expired{background:#fff5f5;color:#e53e3e;}
.exp-none{background:#f4f6fb;color:#adb5bd;}
/* ── Type badge ── */
.veh-type-badge{display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;}
.badge-own{background:#eef2ff;color:#667eea;}
.badge-rental{background:#fff8e6;color:#d97706;}
.badge-type{background:#f0fff4;color:#38a169;}
/* ── Document grid ── */
.doc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;}
.doc-card{border:2px dashed #d0d5e8;border-radius:10px;padding:16px 10px;text-align:center;background:#fafbff;transition:all .2s;}
.doc-card.has-file{border-color:#38a169;background:#f0fff4;border-style:solid;cursor:pointer;}
.doc-card.has-file:hover{border-color:#2f855a;box-shadow:0 4px 14px rgba(40,167,69,.18);transform:translateY(-2px);}
.doc-icon{font-size:26px;color:#667eea;margin-bottom:6px;display:block;}
.doc-card.has-file .doc-icon{color:#38a169;}
.doc-title{font-size:11px;font-weight:700;color:#303549;margin-bottom:4px;display:block;}
.doc-uploaded{display:inline-flex;align-items:center;gap:4px;background:#e9f7ef;color:#38a169;border:1px solid #c3e6cb;border-radius:20px;padding:3px 10px;font-size:10px;font-weight:600;margin-top:4px;}
.doc-hint{font-size:11px;color:#adb5bd;margin-top:6px;}
/* ── Footer ── */
.action-bar{background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.07);padding:14px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;}
/* ── Preview Modal — centered, not side-panel ── */
#docPreviewModal .modal-dialog{
    margin: 30px auto;
    max-width: 860px;
    width: 96%;
}
#docPreviewModal .modal-content{
    border-radius: 12px;
    overflow: hidden;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,.3);
}
#docPreviewModal .modal-header{
    background: linear-gradient(135deg,#1a2340,#303f6e);
    padding: 14px 20px;
    border-bottom: none;
}
#docPreviewModal .modal-body{
    background: #1a1a2e;
    padding: 0;
    min-height: 480px;
    max-height: 70vh;
    overflow-y: auto;
    display: flex;
    align-items: center;
    justify-content: center;
}
#docPreviewModal .modal-footer{
    background: #f8fafc;
    border-top: 1px solid #edf0f7;
    padding: 10px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>

<div class="pcoded-inner-content">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

{{-- Header --}}
<div class="veh-view-header">
    <div class="d-flex align-items-start justify-content-between flex-wrap" style="gap:12px;">
        <div style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:20px;padding:4px 14px;font-size:12px;font-weight:700;letter-spacing:.5px;margin-bottom:8px;">
                <i class="ti-eye"></i> View Vehicle
            </div>
            <h4>{{ $vehicle->vehicle_number }}</h4>
            <div class="sub">{{ $vehicle->vehicle_name ?? 'Vehicle Details' }}</div>
        </div>
        <div style="position:relative;z-index:1;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('vehicle.edit', $vehicle->id) }}"
               style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);color:#fff;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
                <i class="ti-pencil"></i> Edit
            </a>
            <a href="{{ route('vehicle') }}"
               style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:#fff;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
                <i class="ti-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

<div class="row">
{{-- ── LEFT COLUMN ── --}}
<div class="col-lg-8">

    {{-- Vehicle Information --}}
    <div class="info-card">
        <div class="info-card-header">
            <i class="ti-truck" style="color:#667eea;"></i>
            <h6>Vehicle Information</h6>
        </div>
        <div class="info-card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="field-group">
                        <div class="field-label">Vehicle Number</div>
                        <div class="field-value" style="font-family:monospace;font-size:15px;font-weight:800;color:#1a2340;letter-spacing:.5px;">
                            {{ $vehicle->vehicle_number }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field-group">
                        <div class="field-label">Vehicle Name / Model</div>
                        <div class="field-value {{ !$vehicle->vehicle_name ? 'empty' : '' }}">
                            {{ $vehicle->vehicle_name ?? '—' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-group">
                        <div class="field-label">Owner Type</div>
                        <div class="field-value">
                            @if($vehicle->owner_type)
                                <span class="veh-type-badge {{ strtolower($vehicle->owner_type) === 'rental' ? 'badge-rental' : 'badge-own' }}">
                                    {{ $vehicle->owner_type === 'Own' ? 'Own Vehicle' : 'Rental Vehicle' }}
                                </span>
                            @else <span class="empty">—</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-group">
                        <div class="field-label">Vehicle Type</div>
                        <div class="field-value">
                            @php $typeLabels = ['lorry'=>'Lorry','truck'=>'Truck','trailer'=>'Trailer','mini_truck'=>'Mini Truck','container'=>'Container','tipper'=>'Tipper']; @endphp
                            @if($vehicle->vehicle_type)
                                <span class="veh-type-badge badge-type">{{ $typeLabels[$vehicle->vehicle_type] ?? ucfirst($vehicle->vehicle_type) }}</span>
                            @else <span class="empty">—</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-group">
                        <div class="field-label">Supplier</div>
                        <div class="field-value {{ !optional($vehicle->supplier)->name ? 'empty' : '' }}">
                            {{ optional($vehicle->supplier)->name ?? '—' }}
                        </div>
                    </div>
                </div>
                @if($vehicle->asset_make || $vehicle->asset_type)
                <div class="col-md-6">
                    <div class="field-group">
                        <div class="field-label">Asset Make</div>
                        <div class="field-value {{ !$vehicle->asset_make ? 'empty' : '' }}">
                            {{ $vehicle->asset_make ?? '—' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field-group">
                        <div class="field-label">Asset Type</div>
                        <div class="field-value {{ !$vehicle->asset_type ? 'empty' : '' }}">
                            {{ $vehicle->asset_type ?? '—' }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Technical Details --}}
    <div class="info-card">
        <div class="info-card-header">
            <i class="ti-settings" style="color:#667eea;"></i>
            <h6>Technical Details</h6>
        </div>
        <div class="info-card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="field-group">
                        <div class="field-label">Engine Number</div>
                        <div class="field-value {{ !$vehicle->engine_number ? 'empty' : '' }}">
                            {{ $vehicle->engine_number ?? '—' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-group">
                        <div class="field-label">Chassis Number</div>
                        <div class="field-value {{ !$vehicle->chassis_number ? 'empty' : '' }}">
                            {{ $vehicle->chassis_number ?? '—' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-group">
                        <div class="field-label">RC Number</div>
                        <div class="field-value {{ !$vehicle->rc_number ? 'empty' : '' }}">
                            {{ $vehicle->rc_number ?? '—' }}
                        </div>
                    </div>
                </div>
                @if($vehicle->permit_number)
                <div class="col-md-4">
                    <div class="field-group">
                        <div class="field-label">Permit Number</div>
                        <div class="field-value">{{ $vehicle->permit_number }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Documents --}}
    <div class="info-card">
        <div class="info-card-header">
            <i class="ti-files" style="color:#667eea;"></i>
            <h6>Vehicle Documents</h6>
            <small class="text-muted ml-auto" style="font-size:11px;">Click a document to preview</small>
        </div>
        <div class="info-card-body">
            <div class="doc-grid">
                @foreach($docTypes as $type => $label)
                @php $existing = $documents->get($type); @endphp
                <div class="doc-card {{ $existing ? 'has-file' : '' }}"
                    @if($existing) onclick="previewDoc('{{ asset('storage/' . $existing->file_path) }}', '{{ $existing->file_extension }}', '{{ addslashes($label) }}')" @endif>
                    <i class="{{ $docIcons[$type] ?? 'icofont icofont-file-document' }} doc-icon"></i>
                    <span class="doc-title">{{ $label }}</span>
                    @if($existing)
                        <div class="doc-uploaded"><i class="ti-check" style="font-size:9px;"></i> Uploaded</div>
                        <div style="font-size:10px;color:#aaa;margin-top:4px;">{{ $existing->file_size_human }}</div>
                        <div style="font-size:10px;color:#aaa;">{{ $existing->created_at->format('d M Y') }}</div>
                    @else
                        <div class="doc-hint"><i class="ti-alert" style="color:#ffc107;"></i> Not uploaded</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- ── RIGHT COLUMN ── --}}
<div class="col-lg-4">

    {{-- Expiry Dates --}}
    <div class="info-card">
        <div class="info-card-header">
            <i class="ti-calendar" style="color:#667eea;"></i>
            <h6>Expiry Dates</h6>
        </div>
        <div class="info-card-body" style="padding:14px 16px;">
            @php
                $expiryFields = [
                    'insurance_expiry_date' => ['Insurance',  'ti-shield'],
                    'fitness_expiry_date'   => ['Fitness',    'ti-heart'],
                    'permit_expiry_date'    => ['Permit',     'ti-bookmark'],
                    'puc_expiry_date'       => ['PUC',        'ti-leaf'],
                ];
            @endphp
            @foreach($expiryFields as $field => [$label, $icon])
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f5f9;">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#596579;">
                    <i class="{{ $icon }}" style="font-size:14px;color:#b0bac9;width:16px;text-align:center;"></i>
                    {{ $label }}
                </div>
                <div>
                    @if($vehicle->$field)
                        @php
                            $d    = \Carbon\Carbon::parse($vehicle->$field);
                            $diff = $d->diffInDays(now(), false);
                            $cls  = $diff > 0 ? 'exp-expired' : ($d->diffInDays(now()) <= 30 ? 'exp-warn' : 'exp-ok');
                        @endphp
                        <span class="exp-badge {{ $cls }}">
                            <i class="ti-calendar" style="font-size:9px;"></i>
                            {{ $d->format('d M Y') }}
                        </span>
                    @else
                        <span class="exp-badge exp-none">—</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Record Info --}}
    <div class="info-card">
        <div class="info-card-header">
            <i class="ti-info-alt" style="color:#667eea;"></i>
            <h6>Record Info</h6>
        </div>
        <div class="info-card-body" style="padding:14px 16px;">
            <div style="font-size:12px;line-height:2.2;">
                <div style="display:flex;justify-content:space-between;border-bottom:1px solid #f3f5f9;padding:4px 0;">
                    <span style="color:#8a94a6;font-weight:600;">Status</span>
                    <span style="background:{{ $vehicle->status === 'active' ? '#f0fff4' : '#f4f6fb' }};color:{{ $vehicle->status === 'active' ? '#38a169' : '#8a94a6' }};padding:2px 10px;border-radius:10px;font-weight:700;font-size:11px;">
                        {{ ucfirst($vehicle->status ?? 'active') }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;border-bottom:1px solid #f3f5f9;padding:4px 0;">
                    <span style="color:#8a94a6;font-weight:600;">Registered</span>
                    <span style="font-weight:700;color:#303549;">{{ $vehicle->created_at->format('d M Y') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:4px 0;">
                    <span style="color:#8a94a6;font-weight:600;">Last Updated</span>
                    <span style="font-weight:700;color:#303549;">{{ $vehicle->updated_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="info-card">
        <div class="info-card-body" style="padding:14px 16px;">
            <a href="{{ route('vehicle.edit', $vehicle->id) }}" class="btn btn-primary btn-sm btn-block mb-2" style="border-radius:8px;font-weight:700;">
                <i class="ti-pencil mr-1"></i> Edit This Vehicle
            </a>
            <a href="{{ route('vehicle') }}" class="btn btn-secondary btn-sm btn-block" style="border-radius:8px;font-weight:700;">
                <i class="ti-arrow-left mr-1"></i> Back to Vehicles List
            </a>
        </div>
    </div>

</div>
</div>

</div></div></div></div>

{{-- ── Document Preview Modal — centered ── --}}
<div class="modal fade" id="docPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a2340,#303f6e);">
                <h5 class="modal-title text-white" style="font-size:14px;font-weight:700;display:flex;align-items:center;gap:8px;">
                    <i class="ti-file"></i>
                    <span id="docPreviewTitle">Document Preview</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" style="opacity:1;text-shadow:none;font-size:20px;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="docPreviewBody"></div>
            <div class="modal-footer" style="background:#f8fafc;border-top:1px solid #edf0f7;padding:10px 16px;">
                <a id="docPreviewOpenLink" href="#" target="_blank" class="btn btn-sm btn-outline-primary" style="border-radius:7px;font-weight:600;">
                    <i class="ti-new-window mr-1"></i> Open in New Tab
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius:7px;font-weight:600;">
                    <i class="ti-close mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('#docPreviewModal').on('hidden.bs.modal', function(){
        document.getElementById('docPreviewBody').innerHTML = '';
    });
});

function previewDoc(url, ext, label) {
    document.getElementById('docPreviewTitle').textContent = label;
    document.getElementById('docPreviewOpenLink').href = url;
    var body = document.getElementById('docPreviewBody');
    var imageExts = ['jpg','jpeg','png','gif','webp','bmp'];
    var extLower  = (ext || '').toLowerCase();
    if (imageExts.indexOf(extLower) !== -1) {
        body.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;padding:16px;background:#1a1a2e;min-height:400px;">'
            + '<img src="' + url + '" style="max-width:100%;max-height:60vh;object-fit:contain;border-radius:6px;" />'
            + '</div>';
    } else if (extLower === 'pdf') {
        body.innerHTML = '<iframe src="' + url + '" style="width:100%;height:60vh;border:none;display:block;"></iframe>';
    } else {
        body.innerHTML = '<div style="text-align:center;padding:48px 24px;background:#1a1a2e;color:#aaa;">'
            + '<i class="ti-file" style="font-size:48px;display:block;margin-bottom:14px;color:#667eea;"></i>'
            + '<div style="font-size:14px;margin-bottom:16px;">Preview not available for this file type.</div>'
            + '<a href="' + url + '" target="_blank" class="btn btn-sm btn-primary" style="border-radius:7px;">'
            + '<i class="ti-new-window mr-1"></i> Open File</a></div>';
    }
    $('#docPreviewModal').modal('show');
}
</script>
@endpush
