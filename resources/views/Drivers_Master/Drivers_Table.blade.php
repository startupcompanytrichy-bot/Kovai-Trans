@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/parties/parties.css') }}">

<style>
/* ── Page Header ── */
.drv-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 14px; padding: 22px 28px;
    color: #fff; margin-bottom: 22px;
    position: relative; overflow: hidden;
}
.drv-header::before { content:''; position:absolute; top:-50px; right:-50px; width:180px; height:180px; background:rgba(255,255,255,.06); border-radius:50%; }
.drv-header::after  { content:''; position:absolute; bottom:-30px; left:80px; width:120px; height:120px; background:rgba(118,75,162,.18); border-radius:50%; }
.drv-header h4 { font-size:20px; font-weight:800; margin:0 0 3px; position:relative; z-index:1; }
.drv-header .sub { font-size:13px; opacity:.75; position:relative; z-index:1; }

/* ── Stat cards ── */
.drv-stat {
    background: #fff; border-radius: 12px; padding: 16px 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    display: flex; align-items: center; gap: 14px;
    border-left: 4px solid transparent;
    transition: transform .2s, box-shadow .2s; height: 100%;
}
.drv-stat:hover { transform: translateY(-2px); box-shadow: 0 6px 22px rgba(0,0,0,.11); }
.drv-stat .sc-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
.drv-stat .sc-label { font-size:11px; font-weight:700; color:#8a94a6; text-transform:uppercase; letter-spacing:.5px; }
.drv-stat .sc-value { font-size:24px; font-weight:800; line-height:1; margin-top:2px; }
.drv-stat.stat-total  { border-left-color:#667eea; }
.drv-stat.stat-total .sc-icon  { background:#eef2ff; color:#667eea; }
.drv-stat.stat-total .sc-value { color:#667eea; }
.drv-stat.stat-active { border-left-color:#38a169; }
.drv-stat.stat-active .sc-icon  { background:#f0fff4; color:#38a169; }
.drv-stat.stat-active .sc-value { color:#38a169; }
.drv-stat.stat-own    { border-left-color:#d97706; }
.drv-stat.stat-own .sc-icon    { background:#fffbeb; color:#d97706; }
.drv-stat.stat-own .sc-value   { color:#d97706; }

/* ── Filter bar ── */
.drv-filter-bar {
    background:#fff; border-radius:12px; padding:14px 18px;
    box-shadow:0 2px 10px rgba(0,0,0,.06); margin-bottom:18px;
    display:flex; align-items:center; gap:10px;
}
.drv-search-wrap { flex:1; min-width:140px; position:relative; }

/* ── Select2 in driver filter bar ───────────────────────────────── */
.drv-filter-bar .select2-container .select2-selection--single {
    height: 40px !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    padding: 0 30px 0 10px !important;
    background: #fff !important;
    font-size: 13px !important;
}
.drv-filter-bar .select2-container--default .select2-selection--single .select2-selection__rendered {
    font-size: 13px !important;
    color: #1e293b !important;
    line-height: 1.2 !important;
    padding: 0 !important;
}
.drv-filter-bar .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    right: 6px !important;
}
.drv-search-wrap .si { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#b0bac9; font-size:14px; pointer-events:none; }
.drv-search-wrap input { padding-left:36px; border-color:#e2e8f0; border-radius:8px; min-height:40px; font-size:13px; }
.drv-search-wrap input:focus { border-color:#667eea; box-shadow:0 0 0 2px rgba(102,126,234,.12); }

/* ── Table card ── */
.drv-table-card { background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.07); overflow:hidden; }
.drv-table-card .card-header-custom {
    padding:16px 20px; border-bottom:1px solid #f0f2f7; background:#fafbff;
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;
}
.drv-table-card .card-header-custom h5 { margin:0; font-size:15px; font-weight:700; color:#1a2340; }
.drv-table-card .card-header-custom .sub { font-size:12px; color:#8a94a6; margin-top:2px; }

.drv-table { width:100%; border-collapse:collapse; min-width:780px; }
.drv-table thead th {
    background:#f8fafc; padding:11px 14px;
    font-size:11px; font-weight:700; text-transform:uppercase;
    letter-spacing:.5px; color:#596579;
    border-bottom:2px solid #edf0f7; white-space:nowrap;
}
.drv-table tbody td { padding:12px 14px; font-size:13px; color:#303549; border-bottom:1px solid #f3f5f9; vertical-align:middle; }
.drv-table tbody tr:last-child td { border-bottom:none; }
.drv-table tbody tr:hover td { background:#f5f7ff; transition:background .12s; }

/* Driver name cell */
.drv-name-cell { display:flex; align-items:center; gap:10px; }
.drv-avatar-img { width:36px; height:36px; border-radius:50%; object-fit:cover; border:2px solid #c4b5fd; cursor:pointer; transition:transform .15s; }
.drv-avatar-img:hover { transform:scale(1.1); }
.drv-avatar-ph { width:36px; height:36px; border-radius:50%; background:#f5f3ff; display:flex; align-items:center; justify-content:center; border:2px solid #e9d5ff; flex-shrink:0; }
.drv-name-cell .dname { font-weight:700; color:#1a2340; font-size:13px; }
.drv-name-cell .daadhar { font-size:11px; color:#8a94a6; margin-top:1px; }

/* Badges */
.drv-type-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-own    { background:#f0fff4; color:#38a169; }
.badge-rental { background:#fffbeb; color:#d97706; }
.drv-status-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
.status-active   { background:#f0fff4; color:#38a169; }
.status-inactive { background:#f4f6fb; color:#8a94a6; }

/* Action buttons */
.btn-action-view { background:#eef2ff; color:#667eea; border:none; border-radius:6px; padding:5px 10px; font-size:12px; font-weight:600; cursor:pointer; transition:all .15s; }
.btn-action-edit { background:#fff8e6; color:#d97706; border:none; border-radius:6px; padding:5px 10px; font-size:12px; font-weight:600; cursor:pointer; transition:all .15s; }
.btn-action-del  { background:#fff5f5; color:#e53e3e; border:none; border-radius:6px; padding:5px 10px; font-size:12px; font-weight:600; cursor:pointer; transition:all .15s; }
.btn-action-view:hover { background:#667eea; color:#fff; }
.btn-action-edit:hover  { background:#d97706; color:#fff; }
.btn-action-del:hover   { background:#e53e3e; color:#fff; }

/* Add button */
.btn-add-drv {
    background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.3);
    color:#fff; border-radius:8px; padding:9px 18px;
    font-size:13px; font-weight:600;
    display:inline-flex; align-items:center; gap:6px;
    cursor:pointer; transition:all .2s; white-space:nowrap; text-decoration:none;
}
.btn-add-drv:hover { background:rgba(255,255,255,.25); color:#fff; }

/* Empty state */
.empty-state-row td { padding:48px 20px !important; text-align:center; background:#fff; }
.empty-state-inner .ei { font-size:44px; color:#d7dce5; display:block; margin-bottom:10px; }
.empty-state-inner .et { font-size:15px; font-weight:700; color:#8a94a6; margin-bottom:4px; }
.empty-state-inner .es { font-size:13px; color:#b0bac9; }

/* ── View Driver right slide-in panel ── */
.drv-view-backdrop {
    display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(26,35,64,.45); z-index: 1040; backdrop-filter: blur(2px);
}
.drv-view-backdrop.show { display: block; }
.drv-view-panel {
    position: fixed; top: 0; right: 0; bottom: 0;
    width: 100%; max-width: 680px; background: #fff; z-index: 1050;
    display: flex; flex-direction: column;
    transform: translateX(100%); transition: transform .28s cubic-bezier(.4,0,.2,1);
    box-shadow: -8px 0 40px rgba(0,0,0,.18);
}
.drv-view-panel.open { transform: translateX(0); }
.drv-view-panel-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 18px 20px; display: flex; align-items: center;
    justify-content: space-between; flex-shrink: 0;
}
.drv-view-panel-header h5 { color: #fff; font-size: 16px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; }
.drv-view-panel-close {
    width: 36px; height: 36px; border-radius: 8px; background: rgba(255,255,255,.15);
    border: none; color: #fff; font-size: 18px; cursor: pointer;
    display: flex; align-items: center; justify-content: center; transition: background .15s;
}
.drv-view-panel-close:hover { background: #e53e3e; }
.drv-view-panel-body {
    flex: 1; min-height: 0; overflow-y: auto; overflow-x: hidden;
    padding: 22px 20px; -webkit-overflow-scrolling: touch;
}
.drv-view-panel-footer {
    padding: 14px 20px; border-top: 1px solid #edf0f7; background: #fafbff;
    display: flex; align-items: center; justify-content: flex-end;
    gap: 10px; flex-shrink: 0;
}
@keyframes drv-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
@media(max-width:575px) { .drv-view-panel { max-width: 100%; } }
.view-label { font-size:11px; font-weight:700; color:#adb5bd; text-transform:uppercase; letter-spacing:.5px; margin-bottom:3px; }
.view-value { font-size:13px; font-weight:600; color:#303549; padding:7px 10px; background:#f8fafc; border:1px solid #edf0f7; border-radius:7px; margin-bottom:12px; }
</style>

<div class="pcoded-inner-content">
    <div class="main-body"><div class="page-wrapper"><div class="page-body">

        {{-- Page Header --}}
        <div class="drv-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">
                <div style="position:relative;z-index:1;">
                    <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:4px 14px;font-size:12px;font-weight:700;letter-spacing:.5px;margin-bottom:8px;">
                        <i class="ti-id-badge"></i> Driver Management
                    </div>
                    <h4>Drivers</h4>
                    <div class="sub">Manage all registered drivers, licenses, and documents.</div>
                </div>
                <div style="position:relative;z-index:1;">
                    <a href="{{ route('driver.create') }}" class="btn-add-drv">
                        <i class="ti-plus"></i> Add Driver
                    </a>
                </div>
            </div>
        </div>

        @include('partials.flash')

        {{-- Stat Cards --}}
        <div class="row mb-4">
            <div class="col-sm-4 mb-3 mb-sm-0">
                <div class="drv-stat stat-total">
                    <div class="sc-icon"><i class="ti-id-badge"></i></div>
                    <div>
                        <div class="sc-label">Total Drivers</div>
                        <div class="sc-value">{{ $drivers->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 mb-3 mb-sm-0">
                <div class="drv-stat stat-active">
                    <div class="sc-icon"><i class="ti-check-box"></i></div>
                    <div>
                        <div class="sc-label">Active</div>
                        <div class="sc-value">{{ $drivers->where('is_active', true)->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="drv-stat stat-own">
                    <div class="sc-icon"><i class="ti-truck"></i></div>
                    <div>
                        <div class="sc-label">Own Drivers</div>
                        <div class="sc-value">{{ $drivers->where('driver_type', 'own')->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="drv-filter-bar">
            <div class="drv-search-wrap">
                <i class="ti-search si"></i>
                <input type="text" id="driverSearch" class="form-control" placeholder="Search by name, mobile, license, city...">
            </div>
            <select id="filterDriverStatus" class="form-control" style="min-width:130px;max-width:160px;border-radius:8px;border-color:#e2e8f0;font-size:13px;min-height:40px;">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <select id="filterDriverType" class="form-control" style="min-width:130px;max-width:160px;border-radius:8px;border-color:#e2e8f0;font-size:13px;min-height:40px;">
                <option value="">All Types</option>
                <option value="own">Own</option>
                <option value="rental">Rental</option>
            </select>
            <button type="button" id="clearDrvFilters" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;white-space:nowrap;min-height:40px;font-size:13px;">
                <i class="ti-close mr-1"></i> Clear
            </button>
        </div>

        {{-- Table Card --}}
        <div class="drv-table-card">
            <div class="card-header-custom">
                <div>
                    <h5><i class="ti-id-badge mr-2" style="color:#667eea;"></i>Driver List
                        <span id="drvCountBadge" style="background:#eef2ff;color:#667eea;font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;margin-left:6px;">{{ $drivers->count() }}</span>
                    </h5>
                    <div class="sub">All registered drivers are shown below</div>
                </div>
                <a href="{{ route('driver.create') }}" class="btn btn-primary btn-sm" style="border-radius:8px;">
                    <i class="ti-plus mr-1"></i> Add Driver
                </a>
            </div>

            <div class="table-responsive">
                <table class="drv-table" id="driversTable">
                    <thead>
                        <tr>
                            <th style="width:46px;">#</th>
                            <th style="width:48px;text-align:center;">Photo</th>
                            <th>Driver</th>
                            <th>Mobile</th>
                            <th>License No.</th>
                            <th>Type</th>
                            <th>City</th>
                            <th style="text-align:center;">Status</th>
                            <th style="width:130px;text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers ?? [] as $key => $driver)
                        <tr class="drv-row"
                            data-search="{{ strtolower($driver->name . ' ' . $driver->mobile . ' ' . $driver->license_number . ' ' . $driver->city) }}"
                            data-status="{{ $driver->is_active ? 'active' : 'inactive' }}"
                            data-type="{{ $driver->driver_type ?? 'own' }}">
                            <td style="color:#b0bac9;font-weight:600;font-size:12px;">{{ $key + 1 }}</td>
                            <td style="text-align:center;">
                                @if($driver->driver_photo)
                                    <img src="{{ asset('storage/' . $driver->driver_photo) }}"
                                         class="drv-avatar-img"
                                         onclick="viewDriver({{ $driver->id }})"
                                         alt="{{ $driver->name }}" title="Click to view">
                                @else
                                    <div class="drv-avatar-ph" style="margin:0 auto;">
                                        <i class="ti-user" style="color:#c4b5fd;font-size:14px;"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="drv-name-cell">
                                    <div>
                                        <div class="dname">{{ $driver->name }}</div>
                                        @if($driver->aadhar_number)
                                        <div class="daadhar">Aadhar: {{ $driver->aadhar_number }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:12px;color:#596579;"><i class="ti-mobile" style="color:#b0bac9;margin-right:3px;"></i>{{ $driver->mobile }}</span>
                            </td>
                            <td>
                                <span style="font-family:monospace;font-size:12px;color:#303549;">{{ $driver->license_number ?: '—' }}</span>
                            </td>
                            <td>
                                <span class="drv-type-badge {{ ($driver->driver_type ?? 'own') === 'rental' ? 'badge-rental' : 'badge-own' }}">
                                    {{ ucfirst($driver->driver_type ?? 'own') }}
                                </span>
                            </td>
                            <td>{{ $driver->city ?? '—' }}</td>
                            <td style="text-align:center;">
                                @if($driver->is_active)
                                <span class="drv-status-badge status-active"><i class="ti-check" style="font-size:9px;"></i> Active</span>
                                @else
                                <span class="drv-status-badge status-inactive"><i class="ti-close" style="font-size:9px;"></i> Inactive</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;align-items:center;justify-content:center;gap:5px;">
                                    <button class="btn-action-view" onclick="viewDriver({{ $driver->id }})" title="View">
                                        <i class="ti-eye"></i>
                                    </button>
                                    <a href="{{ route('driver.edit', $driver->id) }}" class="btn-action-edit" title="Edit">
                                        <i class="ti-pencil"></i>
                                    </a>
                                    <button class="btn-action-del" onclick="deleteDriver({{ $driver->id }}, '{{ addslashes($driver->name) }}')" title="Delete">
                                        <i class="ti-trash"></i>
                                    </button>
                                </div>
                                <form id="deleteFormDriver{{ $driver->id }}" action="{{ route('driver.destroy', $driver->id) }}" method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr class="empty-state-row">
                            <td colspan="9">
                                <div class="empty-state-inner">
                                    <i class="ti-id-badge ei"></i>
                                    <div class="et">No drivers registered yet</div>
                                    <div class="es">Click "Add Driver" to register your first driver</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div></div></div>
</div>

{{-- VIEW DRIVER right slide-in panel --}}
<div class="drv-view-backdrop" id="drvViewBackdrop" onclick="closeDriverPanel()"></div>

<div class="drv-view-panel" id="drvViewPanel">
    <div class="drv-view-panel-header">
        <h5><i class="ti-id-badge"></i><span id="viewDriverTitle">Driver Details</span></h5>
        <button type="button" class="drv-view-panel-close" onclick="closeDriverPanel()"><i class="ti-close"></i></button>
    </div>
    <div class="drv-view-panel-body" id="viewDriverContent">
        <div class="text-center py-5">
            <i class="ti-reload" style="font-size:32px;color:#667eea;display:block;margin-bottom:10px;"></i>
            <span class="text-muted">Loading...</span>
        </div>
    </div>
    <div class="drv-view-panel-footer">
        <button type="button" id="viewDriverEditBtn" class="btn btn-primary btn-sm">
            <i class="ti-pencil mr-1"></i> Edit Driver
        </button>
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeDriverPanel()">
            <i class="ti-close mr-1"></i> Close
        </button>
    </div>
</div>

{{-- PHOTO PREVIEW — inline overlay (no Bootstrap modal) --}}

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // ── Select2 for filters ────────────────────────────────
    $('#filterDriverStatus, #filterDriverType').select2({
        minimumResultsForSearch: -1,
        width: '150px',
    });

    // ── Search & Filter ────────────────────────────────────
    function applyDrvFilters() {
        var term   = $('#driverSearch').val().toLowerCase();
        var status = $('#filterDriverStatus').val();
        var type   = $('#filterDriverType').val();
        var visible = 0;
        $('.drv-row').each(function () {
            var $r = $(this);
            var ok = true;
            if (term   && !($r.attr('data-search')||'').includes(term))    ok = false;
            if (status && $r.attr('data-status') !== status)               ok = false;
            if (type   && ($r.attr('data-type')||'') !== type)             ok = false;
            $r.toggle(ok);
            if (ok) visible++;
        });
        $('#drvCountBadge').text(visible);
    }
    $('#driverSearch, #filterDriverStatus, #filterDriverType').on('input change', applyDrvFilters);
    $('#clearDrvFilters').on('click', function () {
        $('#driverSearch').val('');
        $('#filterDriverStatus, #filterDriverType').val('');
        applyDrvFilters();
    });

    $('#photoPreviewModal').on('hidden.bs.modal', function () {
        // cleanup handled inline
    });
});

// ── View driver ────────────────────────────────────────────
function viewDriver(driverId) {
    var panel    = document.getElementById('drvViewPanel');
    var backdrop = document.getElementById('drvViewBackdrop');
    var content  = document.getElementById('viewDriverContent');

    content.innerHTML = '<div class="text-center py-5"><i class="ti-reload" style="font-size:32px;color:#667eea;display:block;margin-bottom:10px;animation:drv-spin 1s linear infinite;"></i><span class="text-muted">Loading...</span></div>';
    backdrop.classList.add('show');
    panel.classList.add('open');
    document.body.style.overflow = 'hidden';

    $.ajax({
        url: '/driver/view/' + driverId,
        type: 'GET', dataType: 'json',
        success: function (d) {
            document.getElementById('viewDriverTitle').textContent = d.name;
            document.getElementById('viewDriverEditBtn').onclick = function() { window.location = '/driver/edit/' + driverId; };

            function field(label, value) {
                return '<div class="col-md-6 mb-3"><div class="view-label">' + label + '</div><div class="view-value">' + (value || '—') + '</div></div>';
            }
            function photoCard(label, url, icon) {
                if (url) {
                    return '<div class="col-6 col-md-3 text-center mb-3">'
                        + '<div class="view-label mb-2">' + label + '</div>'
                        + '<img src="' + url + '" style="width:72px;height:72px;object-fit:cover;border-radius:10px;border:2px solid #38a169;cursor:pointer;" onclick="previewPhoto(\'' + url + '\',\'' + label + '\')" title="Click to preview">'
                        + '<div style="font-size:10px;color:#38a169;margin-top:4px;"><i class="ti-check"></i> Uploaded</div>'
                        + '</div>';
                }
                return '<div class="col-6 col-md-3 text-center mb-3">'
                    + '<div class="view-label mb-2">' + label + '</div>'
                    + '<div style="width:72px;height:72px;border-radius:10px;border:2px dashed #dee2e6;background:#f8f9fa;display:flex;align-items:center;justify-content:center;margin:0 auto;">'
                    + '<i class="' + icon + '" style="font-size:22px;color:#adb5bd;"></i></div>'
                    + '<div style="font-size:10px;color:#adb5bd;margin-top:4px;">Not uploaded</div>'
                    + '</div>';
            }

            var html = '<div class="row mb-2">';
            html += photoCard('Driver Photo',   d.driver_photo_url,  'ti-user');
            html += photoCard('Aadhar Card',    d.aadhar_photo_url,  'ti-id-badge');
            html += photoCard('PAN Card',       d.pan_photo_url,     'ti-credit-card');
            html += photoCard('License',        d.license_photo_url, 'ti-car');
            html += '</div>';

            var typeBg  = d.driver_type === 'rental' ? '#fffbeb' : '#f0fff4';
            var typeCol = d.driver_type === 'rental' ? '#d97706' : '#38a169';
            html += '<div style="margin-bottom:14px;">'
                + '<span style="background:' + typeBg + ';color:' + typeCol + ';font-size:11px;font-weight:700;padding:3px 12px;border-radius:20px;">'
                + (d.driver_type === 'rental' ? '🔄 Rental Driver' : '🚛 Own Driver')
                + '</span></div>';

            html += '<div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#667eea;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #edf0f7;display:flex;align-items:center;gap:6px;"><i class="ti-id-badge"></i> Personal Information</div>';
            html += '<div class="row">';
            html += field('Driver Name', d.name);
            html += field('Date of Birth', d.dob);
            html += field('Mobile Number', d.mobile);
            html += field('License Number', d.license_number);
            html += field('Aadhar Number', d.aadhar_number);
            html += field('PAN Number', d.pan_number);
            html += '</div>';

            html += '<div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#667eea;margin-bottom:12px;margin-top:4px;padding-bottom:8px;border-bottom:1px solid #edf0f7;display:flex;align-items:center;gap:6px;"><i class="ti-map-alt"></i> Address Details</div>';
            html += '<div class="row">';
            html += field('State', d.state);
            html += field('District', d.district);
            html += field('City', d.city);
            html += field('Postal Code', d.postal_code);
            html += '<div class="col-md-12 mb-3"><div class="view-label">Full Address</div><div class="view-value">' + (d.address || '—') + '</div></div>';
            html += '</div>';

            if (d.remarks) {
                html += '<div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#667eea;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid #edf0f7;"><i class="ti-comment mr-1"></i> Remarks</div>';
                html += '<div class="view-value">' + d.remarks + '</div>';
            }

            content.innerHTML = html;
        },
        error: function () {
            content.innerHTML = '<div class="alert alert-danger"><i class="ti-alert mr-1"></i> Error loading driver details. Please try again.</div>';
        }
    });
}

function closeDriverPanel() {
    document.getElementById('drvViewPanel').classList.remove('open');
    document.getElementById('drvViewBackdrop').classList.remove('show');
    document.body.style.overflow = '';
}

// ── Delete ─────────────────────────────────────────────────
function deleteDriver(id, name) {
    showDeleteModal('deleteFormDriver' + id, name, 'Driver');
}

// ── Photo preview ──────────────────────────────────────────
function previewPhoto(url, label) {
    var ext = url.split('.').pop().toLowerCase();
    var content = '';
    if (['jpg','jpeg','png','gif','webp'].indexOf(ext) !== -1) {
        content = '<img src="' + url + '" style="max-width:100%;max-height:80vh;object-fit:contain;display:block;margin:auto;" />';
    } else if (ext === 'pdf') {
        content = '<iframe src="' + url + '" style="width:100%;height:80vh;border:none;"></iframe>';
    } else {
        content = '<div style="color:#aaa;padding:40px;text-align:center;"><i class="ti-file" style="font-size:48px;display:block;margin-bottom:12px;"></i>Cannot preview.<br><a href="' + url + '" target="_blank" class="btn btn-primary btn-sm mt-3">Open File</a></div>';
    }

    var existing = document.getElementById('drvPhotoOverlay');
    if (existing) existing.remove();

    var overlay = document.createElement('div');
    overlay.id  = 'drvPhotoOverlay';
    overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.8);z-index:2000;display:flex;align-items:center;justify-content:center;padding:20px;';
    overlay.innerHTML =
        '<div style="background:#fff;border-radius:12px;overflow:hidden;max-width:800px;width:100%;max-height:90vh;display:flex;flex-direction:column;">'
        + '<div style="background:linear-gradient(135deg,#1a2340,#303f6e);padding:12px 18px;display:flex;justify-content:space-between;align-items:center;flex-shrink:0;">'
        + '<span style="color:#fff;font-weight:700;font-size:14px;"><i class="ti-file" style="margin-right:6px;"></i>' + label + '</span>'
        + '<div style="display:flex;gap:8px;align-items:center;">'
        + '<a href="' + url + '" target="_blank" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);color:#fff;border-radius:6px;padding:4px 10px;font-size:12px;text-decoration:none;"><i class="ti-new-window" style="margin-right:4px;"></i>Open</a>'
        + '<button onclick="document.getElementById(\'drvPhotoOverlay\').remove()" style="background:rgba(255,255,255,.15);border:none;color:#fff;border-radius:6px;padding:4px 10px;cursor:pointer;font-size:16px;">&times;</button>'
        + '</div></div>'
        + '<div style="flex:1;overflow:auto;background:#1a1a2e;display:flex;align-items:center;justify-content:center;">' + content + '</div>'
        + '</div>';
    overlay.onclick = function(e) { if (e.target === overlay) overlay.remove(); };
    document.body.appendChild(overlay);
}
</script>
@endpush
