@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/parties/parties.css') }}">

<style>
/* ── Page Header ── */
.veh-header {
    background: linear-gradient(135deg, #1a2340 0%, #303f6e 100%);
    border-radius: 14px; padding: 14px 24px;
    color: #fff; margin-bottom: 18px;
    position: relative; overflow: hidden;
}
.veh-header::before {
    content: ''; position: absolute; top: -50px; right: -50px;
    width: 180px; height: 180px; background: rgba(255,255,255,.05); border-radius: 50%;
}
.veh-header::after {
    content: ''; position: absolute; bottom: -30px; left: 80px;
    width: 120px; height: 120px; background: rgba(102,126,234,.18); border-radius: 50%;
}
.veh-header h4 { font-size: 20px; font-weight: 800; margin: 0 0 3px; position: relative; z-index: 1; }
.veh-header .sub { font-size: 13px; opacity: .75; position: relative; z-index: 1; }
.veh-header .header-actions { position: relative; z-index: 1; }

/* ── Stat cards ── */
.veh-stat {
    background: #fff; border-radius: 12px; padding: 16px 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    display: flex; align-items: center; gap: 14px;
    border-left: 4px solid transparent;
    transition: transform .2s, box-shadow .2s; height: 100%;
}
.veh-stat:hover { transform: translateY(-2px); box-shadow: 0 6px 22px rgba(0,0,0,.11); }
.veh-stat .sc-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.veh-stat .sc-label { font-size: 11px; font-weight: 700; color: #8a94a6; text-transform: uppercase; letter-spacing: .5px; }
.veh-stat .sc-value { font-size: 24px; font-weight: 800; line-height: 1; margin-top: 2px; }
.veh-stat.stat-total  { border-left-color: #667eea; }
.veh-stat.stat-total  .sc-icon  { background: #eef2ff; color: #667eea; }
.veh-stat.stat-total  .sc-value { color: #667eea; }
.veh-stat.stat-active { border-left-color: #38a169; }
.veh-stat.stat-active .sc-icon  { background: #f0fff4; color: #38a169; }
.veh-stat.stat-active .sc-value { color: #38a169; }
.veh-stat.stat-expiry { border-left-color: #e53e3e; }
.veh-stat.stat-expiry .sc-icon  { background: #fff5f5; color: #e53e3e; }
.veh-stat.stat-expiry .sc-value { color: #e53e3e; }

/* ── Filter bar ── */
.veh-filter-bar {
    background: #fff; border-radius: 12px; padding: 14px 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06); margin-bottom: 18px;
    display: flex; align-items: center; gap: 10px;
}
.veh-search-wrap { flex: 1; min-width: 140px; position: relative; }

/* ── Select2 in vehicle filter bar ───────────────────────────────── */
.veh-filter-bar .select2-container .select2-selection--single {
    height: 40px !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    padding: 0 30px 0 10px !important;
    background: #fff !important;
    font-size: 13px !important;
}
.veh-filter-bar .select2-container--default .select2-selection--single .select2-selection__rendered {
    font-size: 13px !important;
    color: #1e293b !important;
    line-height: 1.2 !important;
    padding: 0 !important;
}
.veh-filter-bar .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    right: 6px !important;
}
.veh-search-wrap .si { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #b0bac9; font-size: 14px; pointer-events: none; }
.veh-search-wrap input { padding-left: 36px; border-color: #e2e8f0; border-radius: 8px; min-height: 40px; font-size: 13px; }
.veh-search-wrap input:focus { border-color: #667eea; box-shadow: 0 0 0 2px rgba(102,126,234,.12); }

/* ── Table card ── */
.veh-table-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.07); overflow: hidden; }
.veh-table-card .card-header-custom {
    padding: 16px 20px; border-bottom: 1px solid #f0f2f7; background: #fafbff;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;
}
.veh-table-card .card-header-custom h5 { margin: 0; font-size: 15px; font-weight: 700; color: #1a2340; }
.veh-table-card .card-header-custom .sub { font-size: 12px; color: #8a94a6; margin-top: 2px; }

.veh-table { width: 100%; border-collapse: collapse; min-width: 900px; }
.veh-table thead th {
    background: #f8fafc; padding: 11px 14px; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px; color: #596579;
    border-bottom: 2px solid #edf0f7; white-space: nowrap;
}
.veh-table tbody td { padding: 12px 14px; font-size: 13px; color: #303549; border-bottom: 1px solid #f3f5f9; vertical-align: middle; }
.veh-table tbody tr:last-child td { border-bottom: none; }
.veh-table tbody tr:hover td { background: #f5f7ff; transition: background .12s; }

/* Vehicle number cell */
.veh-num-cell { display: flex; align-items: center; gap: 10px; }
.veh-avatar {
    width: 36px; height: 36px; border-radius: 9px;
    background: linear-gradient(135deg, #667eea, #1a2340);
    color: #fff; font-weight: 700; font-size: 12px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.veh-num-cell .vnum { font-weight: 700; color: #1a2340; font-size: 13px; }
.veh-num-cell .vname { font-size: 11px; color: #8a94a6; margin-top: 1px; }

/* Type badges */
.veh-type-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.badge-own     { background: #eef2ff; color: #667eea; }
.badge-rental  { background: #fff8e6; color: #d97706; }
.badge-type    { background: #f0fff4; color: #38a169; }
.badge-supplier{ background: #f5f3ff; color: #7c3aed; }

/* Expiry badges */
.exp-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.exp-ok      { background: #f0fff4; color: #38a169; }
.exp-warn    { background: #fff8e6; color: #d97706; }
.exp-expired { background: #fff5f5; color: #e53e3e; }
.exp-none    { background: #f4f6fb; color: #adb5bd; }

/* Action buttons */
.btn-action-view { background: #eef2ff; color: #667eea; border: none; border-radius: 6px; padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; }
.btn-action-edit { background: #fff8e6; color: #d97706; border: none; border-radius: 6px; padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; }
.btn-action-del  { background: #fff5f5; color: #e53e3e; border: none; border-radius: 6px; padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; }
.btn-action-view:hover { background: #667eea; color: #fff; }
.btn-action-edit:hover { background: #d97706; color: #fff; }
.btn-action-del:hover  { background: #e53e3e; color: #fff; }

/* Add button */
.btn-add-veh {
    background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.3);
    color: #fff; border-radius: 8px; padding: 9px 18px; font-size: 13px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 6px;
    cursor: pointer; transition: all .2s; white-space: nowrap; text-decoration: none;
}
.btn-add-veh:hover { background: rgba(255,255,255,.25); color: #fff; }

/* Empty state */
.empty-state-row td { padding: 48px 20px !important; text-align: center; background: #fff; }
.empty-state-inner .ei { font-size: 44px; color: #d7dce5; display: block; margin-bottom: 10px; }
.empty-state-inner .et { font-size: 15px; font-weight: 700; color: #8a94a6; margin-bottom: 4px; }
.empty-state-inner .es { font-size: 13px; color: #b0bac9; }

/* ── Slide-in Panel ── */
.veh-backdrop {
    display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(26,35,64,.45); z-index: 1040; backdrop-filter: blur(2px);
}
.veh-backdrop.show { display: block; }
.veh-panel {
    position: fixed; top: 0; right: 0; bottom: 0;
    width: 100%; max-width: 640px; background: #fff; z-index: 1050;
    display: flex; flex-direction: column;
    transform: translateX(100%); transition: transform .28s cubic-bezier(.4,0,.2,1);
    box-shadow: -8px 0 40px rgba(0,0,0,.16); overflow: hidden;
}
.veh-panel.open { transform: translateX(0); }
.veh-panel-header {
    background: linear-gradient(135deg, #1a2340 0%, #303f6e 100%);
    padding: 18px 20px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
}
.veh-panel-header h5 { color: #fff; font-size: 16px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; }
.veh-panel-header .panel-close {
    width: 36px; height: 36px; border-radius: 8px; background: rgba(255,255,255,.15);
    border: none; color: #fff; font-size: 18px; cursor: pointer;
    display: flex; align-items: center; justify-content: center; transition: background .15s;
}
.veh-panel-header .panel-close:hover { background: #e53e3e; }
#vehicleForm { display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden; }
.veh-panel-body { flex: 1; min-height: 0; overflow-y: auto; overflow-x: hidden; padding: 22px 20px; -webkit-overflow-scrolling: touch; }
.veh-section-title {
    font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px;
    color: #667eea; margin-bottom: 14px; display: flex; align-items: center; gap: 6px;
    padding-bottom: 8px; border-bottom: 1px solid #edf0f7;
}
.veh-section-title.mt-3 { margin-top: 20px; }
.form-group-veh { margin-bottom: 15px; }
.form-group-veh label { display: block; font-size: 12px; font-weight: 700; color: #596579; margin-bottom: 6px; }
.form-group-veh label .req { color: #e53e3e; }
.form-group-veh .form-control {
    border-color: #d7dce5; border-radius: 8px; font-size: 13px; color: #303549;
    min-height: 42px; transition: border-color .15s, box-shadow .15s;
}
.form-group-veh .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.12); }
.form-group-veh .select2-container { width: 100% !important; }
.veh-panel-footer {
    padding: 14px 20px; border-top: 1px solid #edf0f7; background: #fafbff;
    display: flex; align-items: center; justify-content: flex-end; gap: 10px; flex-shrink: 0;
}
.btn-panel-cancel { background: #f0f2f7; color: #596579; border: none; border-radius: 8px; padding: 9px 20px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .15s; }
.btn-panel-cancel:hover { background: #e2e8f0; color: #303549; }
.btn-panel-save {
    background: linear-gradient(135deg, #667eea 0%, #1a2340 100%); color: #fff; border: none;
    border-radius: 8px; padding: 9px 22px; font-size: 13px; font-weight: 600;
    cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all .2s;
}
.btn-panel-save:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(102,126,234,.4); }

@media(max-width:575px) { .veh-panel { max-width: 100%; } }
</style>

<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">

                {{-- Page Header --}}
                <div class="veh-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">
                        <div style="position:relative;z-index:1;">
                            <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:20px;padding:4px 14px;font-size:12px;font-weight:700;letter-spacing:.5px;margin-bottom:8px;">
                                <i class="ti-truck"></i> Vehicle Management
                            </div>
                            <h4>Vehicles</h4>
                            <div class="sub">Manage all registered vehicles, documents &amp; expiry dates.</div>
                        </div>
                        <div class="header-actions">
                            <a href="javascript:void(0);" class="btn-add-veh" onclick="openVehiclePanel()">
                                <i class="ti-plus"></i> Add Vehicle
                            </a>
                        </div>
                    </div>
                </div>

                @include('partials.flash')

                {{-- Stat Cards --}}
                @php
                    $expiredCount = $vehicles->filter(function($v) {
                        return ($v->insurance_expiry_date && \Carbon\Carbon::parse($v->insurance_expiry_date)->isPast())
                            || ($v->fitness_expiry_date && \Carbon\Carbon::parse($v->fitness_expiry_date)->isPast());
                    })->count();
                @endphp
                <div class="row mb-4">
                    <div class="col-sm-4 mb-3 mb-sm-0">
                        <div class="veh-stat stat-total">
                            <div class="sc-icon"><i class="ti-truck"></i></div>
                            <div>
                                <div class="sc-label">Total Vehicles</div>
                                <div class="sc-value">{{ $vehicles->count() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mb-3 mb-sm-0">
                        <div class="veh-stat stat-active">
                            <div class="sc-icon"><i class="ti-check-box"></i></div>
                            <div>
                                <div class="sc-label">Active</div>
                                <div class="sc-value">{{ $vehicles->where('status', 'active')->count() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="veh-stat stat-expiry">
                            <div class="sc-icon"><i class="ti-alert"></i></div>
                            <div>
                                <div class="sc-label">Expiry Alerts</div>
                                <div class="sc-value">{{ $expiredCount }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <div class="veh-filter-bar">
                    <div class="veh-search-wrap">
                        <i class="ti-search si"></i>
                        <input type="text" id="vehicleSearch" class="form-control"
                            placeholder="Search by name, number, type, owner, supplier...">
                    </div>
                    <select id="filterOwnerType" class="form-control" style="min-width:140px;max-width:160px;border-radius:8px;border-color:#e2e8f0;font-size:13px;min-height:40px;">
                        <option value="">All Owner Types</option>
                        <option value="own">Own</option>
                        <option value="rental">Rental</option>
                    </select>
                    <button type="button" id="clearVehFilters" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;white-space:nowrap;min-height:40px;font-size:13px;">
                        <i class="ti-close mr-1"></i> Clear
                    </button>
                </div>

                {{-- Table Card --}}
                <div class="veh-table-card">
                    <div class="card-header-custom">
                        <div>
                            <h5>
                                <i class="ti-truck mr-2" style="color:#667eea;"></i>Vehicle List
                                <span id="vehCountBadge" style="background:#eef2ff;color:#667eea;font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;margin-left:6px;">{{ $vehicles->count() }}</span>
                            </h5>
                            <div class="sub">All registered vehicles are shown below</div>
                        </div>
                        <button class="btn btn-primary btn-sm" style="border-radius:8px;" onclick="openVehiclePanel()">
                            <i class="ti-plus mr-1"></i> Add Vehicle
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="veh-table" id="vehiclesTable">
                            <thead>
                                <tr>
                                    <th style="width:46px;">#</th>
                                    <th>Vehicle</th>
                                    <th>Owner Type</th>
                                    <th>Supplier</th>
                                    <th>Vehicle Type</th>
                                    <th>Insurance Expiry</th>
                                    <th>Fitness Expiry</th>
                                    <th style="width:130px; text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicles ?? [] as $key => $vehicle)
                                @php
                                    $ownerLow     = strtolower($vehicle->owner_type ?? '');
                                    $supplierName = optional($vehicle->supplier)->name ?? '—';
                                @endphp
                                <tr class="vehicle-row"
                                    data-search="{{ strtolower($vehicle->vehicle_name . ' ' . $vehicle->vehicle_number . ' ' . $vehicle->vehicle_type . ' ' . ($vehicle->owner_type ?? '') . ' ' . $supplierName) }}"
                                    data-owner="{{ $ownerLow }}">
                                    <td style="color:#b0bac9;font-weight:600;font-size:12px;">{{ $key + 1 }}</td>
                                    <td>
                                        <div class="veh-num-cell">
                                            <div class="veh-avatar"><i class="ti-truck" style="font-size:14px;"></i></div>
                                            <div>
                                                <div class="vnum">{{ $vehicle->vehicle_number }}</div>
                                                @if($vehicle->vehicle_name)
                                                    <div class="vname">{{ $vehicle->vehicle_name }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="veh-type-badge {{ $ownerLow === 'rental' ? 'badge-rental' : 'badge-own' }}">
                                            {{ $vehicle->owner_type ?? '—' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($vehicle->supplier)
                                            <span class="veh-type-badge badge-supplier">
                                                <i class="ti-user" style="font-size:9px;"></i>
                                                {{ $vehicle->supplier->name }}
                                            </span>
                                        @else
                                            <span style="color:#d7dce5;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vehicle->vehicle_type)
                                            <span class="veh-type-badge badge-type">{{ ucfirst(str_replace('_', ' ', $vehicle->vehicle_type)) }}</span>
                                        @else
                                            <span style="color:#d7dce5;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vehicle->insurance_expiry_date)
                                            @php
                                                $d    = \Carbon\Carbon::parse($vehicle->insurance_expiry_date);
                                                $diff = $d->diffInDays(now(), false);
                                            @endphp
                                            <span class="exp-badge {{ $diff > 0 ? 'exp-expired' : ($d->diffInDays(now()) <= 30 ? 'exp-warn' : 'exp-ok') }}">
                                                <i class="ti-calendar" style="font-size:9px;"></i>
                                                {{ $d->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="exp-badge exp-none">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vehicle->fitness_expiry_date)
                                            @php
                                                $d    = \Carbon\Carbon::parse($vehicle->fitness_expiry_date);
                                                $diff = $d->diffInDays(now(), false);
                                            @endphp
                                            <span class="exp-badge {{ $diff > 0 ? 'exp-expired' : ($d->diffInDays(now()) <= 30 ? 'exp-warn' : 'exp-ok') }}">
                                                <i class="ti-calendar" style="font-size:9px;"></i>
                                                {{ $d->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="exp-badge exp-none">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        <div style="display:flex;align-items:center;justify-content:center;gap:5px;">
                                            <a href="javascript:void(0);" class="btn-action-view" title="View"
                                               onclick="openViewPanel({{ $vehicle->id }})">
                                                <i class="ti-eye"></i>
                                            </a>
                                            <a href="{{ route('vehicle.edit', $vehicle->id) }}" class="btn-action-edit" title="Edit">
                                                <i class="ti-pencil"></i>
                                            </a>
                                            <button class="btn-action-del" onclick="deleteVehicle({{ $vehicle->id }}, '{{ addslashes($vehicle->vehicle_number) }}')" title="Delete">
                                                <i class="ti-trash"></i>
                                            </button>
                                        </div>
                                        <form id="deleteFormVehicle{{ $vehicle->id }}" action="{{ route('vehicle.destroy', $vehicle->id) }}" method="POST" style="display:none;">
                                            @csrf @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr class="empty-state-row">
                                    <td colspan="8">
                                        <div class="empty-state-inner">
                                            <i class="ti-truck ei"></i>
                                            <div class="et">No vehicles registered yet</div>
                                            <div class="es">Click "Add Vehicle" to register your first vehicle</div>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ── Slide-in Panel (Add Vehicle) ── --}}
<div class="veh-backdrop" id="vehBackdrop" onclick="closeVehiclePanel()"></div>

<div class="veh-panel" id="vehPanel">
    <div class="veh-panel-header">
        <h5><i class="ti-truck"></i> Add New Vehicle</h5>
        <button type="button" class="panel-close" onclick="closeVehiclePanel()"><i class="ti-close"></i></button>
    </div>

    <form id="vehicleForm" method="POST" action="{{ route('vehicle.store') }}">
        @csrf
        <div class="veh-panel-body">
            <div id="errorContainer"></div>

            {{-- ── Section: Vehicle Information ── --}}
            <div class="veh-section-title"><i class="ti-truck"></i> Vehicle Information</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-veh">
                        <label>Owner Type <span class="req">*</span></label>
                        <select id="ownerType" name="owner_type" class="form-control select2-veh" required>
                            <option value="">Select Owner Type</option>
                            <option value="Own">Own</option>
                            <option value="Rental">Rental</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-veh">
                        <label>Supplier</label>
                        <select id="supplierSelect" name="supplier_id" class="form-control select2-veh">
                            <option value="">Select Supplier (Optional)</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-veh">
                        <label>Vehicle Model / Name</label>
                        <input type="text" name="vehicle_name" class="form-control" placeholder="e.g. Ashok Leyland 2518">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-veh">
                        <label>Vehicle Number <span class="req">*</span></label>
                        <input type="text" name="vehicle_number" class="form-control" placeholder="TN 01 AB 1234" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-veh">
                        <label>Vehicle Type</label>
                        <select name="vehicle_type" class="form-control select2-veh">
                            <option value="">Select Vehicle Type</option>
                            <option value="lorry">Lorry</option>
                            <option value="truck">Truck</option>
                            <option value="trailer">Trailer</option>
                            <option value="mini_truck">Mini Truck</option>
                            <option value="container">Container</option>
                            <option value="tipper">Tipper</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group-veh">
                        <label>Asset Make</label>
                        <input type="text" name="asset_make" class="form-control" placeholder="e.g. EICHER, TATA">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group-veh">
                        <label>Asset Type</label>
                        <input type="text" name="asset_type" class="form-control" placeholder="e.g. PRO 2110">
                    </div>
                </div>
            </div>

            {{-- ── Section: Technical Details ── --}}
            <div class="veh-section-title mt-3"><i class="ti-settings"></i> Technical Details</div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group-veh">
                        <label>Engine Number</label>
                        <input type="text" name="engine_number" class="form-control" placeholder="Engine No.">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group-veh">
                        <label>Chassis Number</label>
                        <input type="text" name="chassis_number" class="form-control" placeholder="Chassis No.">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group-veh">
                        <label>RC Number</label>
                        <input type="text" name="rc_number" class="form-control" placeholder="RC No.">
                    </div>
                </div>
            </div>

            {{-- ── Section: Expiry Dates ── --}}
            <div class="veh-section-title mt-3"><i class="ti-calendar"></i> Expiry Dates</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-veh">
                        <label>Insurance Expiry</label>
                        <input type="date" name="insurance_expiry_date" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-veh">
                        <label>Fitness Expiry</label>
                        <input type="date" name="fitness_expiry_date" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-veh">
                        <label>Permit Expiry</label>
                        <input type="date" name="permit_expiry_date" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-veh">
                        <label>PUC Expiry</label>
                        <input type="date" name="puc_expiry_date" class="form-control">
                    </div>
                </div>
            </div>
        </div>{{-- /veh-panel-body --}}

        <div class="veh-panel-footer">
            <button type="button" class="btn-panel-cancel" onclick="closeVehiclePanel()">
                <i class="ti-close mr-1"></i> Cancel
            </button>
            <button type="submit" class="btn-panel-save" id="submitVehicleBtn">
                <i class="ti-save mr-1"></i> Save Vehicle
            </button>
        </div>
    </form>
</div>

{{-- ── Slide-in View Panel (right side) ── --}}
<div class="veh-backdrop" id="vehViewBackdrop" onclick="closeViewPanel()"></div>

<div class="veh-panel" id="vehViewPanel" style="display:flex;flex-direction:column;">
    <div class="veh-panel-header" style="flex-shrink:0;">
        <h5 id="viewPanelTitle"><i class="ti-truck mr-2"></i> Vehicle Details</h5>
        <div style="display:flex;align-items:center;gap:8px;">
            <a id="viewPanelEditBtn" href="#"
               style="background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);color:#fff;border-radius:7px;padding:5px 12px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
                <i class="ti-pencil"></i> Edit
            </a>
            <button type="button" class="panel-close" onclick="closeViewPanel()"><i class="ti-close"></i></button>
        </div>
    </div>
    <div id="viewPanelBody" style="flex:1;min-height:0;overflow-y:auto;overflow-x:hidden;padding:20px;-webkit-overflow-scrolling:touch;">
        <div style="text-align:center;padding:60px 20px;color:#b0bac9;">
            <i class="ti-reload" style="font-size:28px;display:block;margin-bottom:10px;"></i>
            Loading...
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    /* ── Select2 for filter ─────────────────────────────────────────── */
    $('#filterOwnerType').select2({
        minimumResultsForSearch: -1,
        width: '150px',
    });

    /* ── CSRF for all AJAX ── */
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    /* ── Search & Filter ── */
    function applyVehFilters() {
        var term  = $('#vehicleSearch').val().toLowerCase();
        var owner = $('#filterOwnerType').val().toLowerCase();
        var visible = 0;
        $('.vehicle-row').each(function () {
            var $r = $(this);
            var ok = true;
            if (term  && !($r.attr('data-search') || '').includes(term))  ok = false;
            if (owner && !($r.attr('data-owner')  || '').includes(owner)) ok = false;
            $r.toggle(ok);
            if (ok) visible++;
        });
        $('#vehCountBadge').text(visible);
    }
    $('#vehicleSearch, #filterOwnerType').on('input change', applyVehFilters);
    $('#clearVehFilters').on('click', function () {
        $('#vehicleSearch').val('');
        $('#filterOwnerType').val('');
        applyVehFilters();
    });

    /* ── Init Select2 inside panel after open transition ── */
    $('#vehPanel').on('transitionend', function () {
        if ($(this).hasClass('open') && $.fn.select2) {
            $('.select2-veh').each(function () {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({ width: '100%', dropdownParent: $('#vehPanel') });
                }
            });
        }
    });

    /* ── AJAX form submit ── */
    $('#vehicleForm').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#submitVehicleBtn');
        $btn.prop('disabled', true).html('<i class="ti-reload mr-1"></i> Saving...');

        $.ajax({
            url:      '{{ route("vehicle.store") }}',
            type:     'POST',
            data:     $(this).serialize(),
            headers:  { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                closeVehiclePanel();
                if (typeof toastr !== 'undefined') toastr.success('Vehicle saved successfully');
                setTimeout(function () { location.reload(); }, 800);
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="ti-save mr-1"></i> Save Vehicle');
                var html = '<div class="alert alert-danger"><ul class="mb-0">';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (f, msgs) {
                        html += '<li>' + msgs[0] + '</li>';
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    html += '<li>' + xhr.responseJSON.message + '</li>';
                } else {
                    html += '<li>Error ' + xhr.status + ': Something went wrong. Please try again.</li>';
                }
                html += '</ul></div>';
                $('#errorContainer').html(html);
                document.querySelector('.veh-panel-body').scrollTop = 0;
            }
        });
    });
});

/* ── Open / Close panel ── */
function openVehiclePanel() {
    resetVehicleForm();
    document.getElementById('vehBackdrop').classList.add('show');
    document.getElementById('vehPanel').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeVehiclePanel() {
    document.getElementById('vehBackdrop').classList.remove('show');
    document.getElementById('vehPanel').classList.remove('open');
    document.body.style.overflow = '';
    setTimeout(resetVehicleForm, 300);
}

function resetVehicleForm() {
    document.getElementById('vehicleForm').reset();
    document.getElementById('errorContainer').innerHTML = '';
    var $btn = document.getElementById('submitVehicleBtn');
    $btn.disabled = false;
    $btn.innerHTML = '<i class="ti-save mr-1"></i> Save Vehicle';
    if ($.fn.select2) {
        $('#ownerType, [name="vehicle_type"], #supplierSelect').val('').trigger('change.select2');
    }
}

/* ── Delete ── */
function deleteVehicle(id, name) {
    showDeleteModal('deleteFormVehicle' + id, name, 'Vehicle');
}

/* ── View Panel ── */
function openViewPanel(id) {
    var panel    = document.getElementById('vehViewPanel');
    var backdrop = document.getElementById('vehViewBackdrop');
    var body     = document.getElementById('viewPanelBody');

    // Show loading state
    body.innerHTML = '<div style="text-align:center;padding:60px 20px;color:#b0bac9;">'
        + '<i class="ti-reload" style="font-size:28px;display:block;margin-bottom:10px;animation:spin 1s linear infinite;"></i>'
        + 'Loading...</div>';

    backdrop.classList.add('show');
    panel.classList.add('open');
    document.body.style.overflow = 'hidden';

    // Fetch vehicle data
    $.getJSON('{{ url("/vehicle/data") }}/' + id, function(v) {
        document.getElementById('viewPanelTitle').innerHTML = '<i class="ti-eye mr-2"></i>' + v.vehicle_number;
        document.getElementById('viewPanelEditBtn').href = v.edit_url;

        var expiryColors = { ok:'#38a169', warn:'#d97706', expired:'#e53e3e', none:'#adb5bd' };
        var expiryBgs    = { ok:'#f0fff4', warn:'#fff8e6', expired:'#fff5f5', none:'#f4f6fb' };

        var html = '';

        // ── Vehicle Info ──
        html += '<div style="margin-bottom:18px;">';
        html += '<div style="font-size:10px;font-weight:800;color:#667eea;text-transform:uppercase;letter-spacing:.8px;padding-bottom:8px;border-bottom:1px solid #edf0f7;margin-bottom:12px;"><i class="ti-truck mr-1"></i> Vehicle Information</div>';
        html += row2col('Vehicle Number', '<span style="font-family:monospace;font-size:15px;font-weight:800;color:#1a2340;">' + v.vehicle_number + '</span>', 'Vehicle Name', v.vehicle_name || '—');
        html += row2col('Owner Type',
            v.owner_type ? '<span style="background:#eef2ff;color:#667eea;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;">' + v.owner_type + '</span>' : '—',
            'Vehicle Type',
            v.vehicle_type ? '<span style="background:#f0fff4;color:#38a169;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;">' + v.vehicle_type + '</span>' : '—'
        );
        if (v.asset_make || v.asset_type) {
            html += row2col('Asset Make', v.asset_make || '—', 'Asset Type', v.asset_type || '—');
        }
        if (v.supplier) {
            html += rowFull('Supplier', v.supplier);
        }
        html += '</div>';

        // ── Technical Details ──
        html += '<div style="margin-bottom:18px;">';
        html += '<div style="font-size:10px;font-weight:800;color:#667eea;text-transform:uppercase;letter-spacing:.8px;padding-bottom:8px;border-bottom:1px solid #edf0f7;margin-bottom:12px;"><i class="ti-settings mr-1"></i> Technical Details</div>';
        html += row3col('Engine No.', v.engine_number || '—', 'Chassis No.', v.chassis_number || '—', 'RC Number', v.rc_number || '—');
        html += '</div>';

        // ── Expiry Dates ──
        html += '<div style="margin-bottom:18px;">';
        html += '<div style="font-size:10px;font-weight:800;color:#667eea;text-transform:uppercase;letter-spacing:.8px;padding-bottom:8px;border-bottom:1px solid #edf0f7;margin-bottom:12px;"><i class="ti-calendar mr-1"></i> Expiry Dates</div>';
        v.expiries.forEach(function(e) {
            var c = expiryColors[e.status] || '#adb5bd';
            var b = expiryBgs[e.status] || '#f4f6fb';
            html += '<div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid #f3f5f9;">'
                + '<span style="font-size:13px;font-weight:600;color:#596579;">' + e.label + '</span>'
                + '<span style="background:' + b + ';color:' + c + ';padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;">' + e.value + '</span>'
                + '</div>';
        });
        html += '</div>';

        // ── Documents ──
        if (v.documents && v.documents.length) {
            html += '<div style="margin-bottom:18px;">';
            html += '<div style="font-size:10px;font-weight:800;color:#667eea;text-transform:uppercase;letter-spacing:.8px;padding-bottom:8px;border-bottom:1px solid #edf0f7;margin-bottom:12px;"><i class="ti-files mr-1"></i> Documents</div>';
            html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">';
            v.documents.forEach(function(d) {
                html += '<div style="border:1px solid #c3e6cb;border-radius:8px;padding:10px;background:#f0fff4;cursor:pointer;" onclick="openDocPreview(\'' + d.url + '\',\'' + d.ext + '\',\'' + d.label + '\')">'
                    + '<div style="font-size:11px;font-weight:700;color:#276749;">' + d.label + '</div>'
                    + '<div style="font-size:10px;color:#8a94a6;margin-top:3px;">' + d.file_size + ' &bull; ' + d.date + '</div>'
                    + '</div>';
            });
            html += '</div></div>';
        }

        // ── Status ──
        html += '<div style="font-size:11px;color:#8a94a6;border-top:1px solid #f0f2f7;padding-top:12px;margin-top:4px;">'
            + 'Registered: <strong>' + v.created_at + '</strong>'
            + ' &bull; Updated: <strong>' + v.updated_at + '</strong>'
            + '</div>';

        body.innerHTML = html;
    }).fail(function() {
        body.innerHTML = '<div style="text-align:center;padding:40px;color:#e53e3e;"><i class="ti-alert" style="font-size:28px;display:block;margin-bottom:8px;"></i>Failed to load vehicle data.</div>';
    });
}

function closeViewPanel() {
    document.getElementById('vehViewPanel').classList.remove('open');
    document.getElementById('vehViewBackdrop').classList.remove('show');
    document.body.style.overflow = '';
}

/* ── Doc preview from view panel ── */
function openDocPreview(url, ext, label) {
    var imageExts = ['jpg','jpeg','png','gif','webp','bmp'];
    var extLower  = (ext||'').toLowerCase();
    var content   = '';
    if (imageExts.indexOf(extLower) !== -1) {
        content = '<img src="' + url + '" style="max-width:100%;max-height:80vh;object-fit:contain;display:block;margin:auto;" />';
    } else if (extLower === 'pdf') {
        content = '<iframe src="' + url + '" style="width:100%;height:80vh;border:none;"></iframe>';
    } else {
        content = '<div style="text-align:center;padding:40px;"><a href="' + url + '" target="_blank" class="btn btn-primary btn-sm">Open File</a></div>';
    }
    // Simple inline modal
    var overlay = document.createElement('div');
    overlay.id  = 'docOverlay';
    overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:2000;display:flex;align-items:center;justify-content:center;padding:20px;';
    overlay.innerHTML = '<div style="background:#fff;border-radius:12px;overflow:hidden;max-width:860px;width:100%;max-height:90vh;display:flex;flex-direction:column;">'
        + '<div style="background:linear-gradient(135deg,#1a2340,#303f6e);padding:12px 18px;display:flex;justify-content:space-between;align-items:center;">'
        + '<span style="color:#fff;font-weight:700;font-size:14px;">' + label + '</span>'
        + '<button onclick="document.getElementById(\'docOverlay\').remove()" style="background:rgba(255,255,255,.15);border:none;color:#fff;border-radius:6px;padding:4px 10px;cursor:pointer;font-size:16px;">&times;</button>'
        + '</div>'
        + '<div style="flex:1;overflow:auto;background:#1a1a2e;">' + content + '</div>'
        + '</div>';
    overlay.onclick = function(e){ if(e.target===overlay) overlay.remove(); };
    document.body.appendChild(overlay);
}

/* helper row builders */
function fieldBox(label, value) {
    return '<div><div style="font-size:10px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">' + label + '</div>'
        + '<div style="font-size:13px;font-weight:600;color:#303549;background:#f8fafc;border:1px solid #edf0f7;border-radius:7px;padding:8px 11px;min-height:38px;display:flex;align-items:center;">' + value + '</div></div>';
}
function row2col(l1,v1,l2,v2) {
    return '<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">' + fieldBox(l1,v1) + fieldBox(l2,v2) + '</div>';
}
function row3col(l1,v1,l2,v2,l3,v3) {
    return '<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:10px;">' + fieldBox(l1,v1) + fieldBox(l2,v2) + fieldBox(l3,v3) + '</div>';
}
function rowFull(label, value) {
    return '<div style="margin-bottom:10px;">' + fieldBox(label, value) + '</div>';
}
</script>
@endpush
