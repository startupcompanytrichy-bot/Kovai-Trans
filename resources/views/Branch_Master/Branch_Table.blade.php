@extends('layouts.app')

@section('content')

<style>
/* ── Page gradient header ── */
.br-header {
    background: linear-gradient(135deg, #303549 0%, #4a5080 100%);
    border-radius: 14px;
    padding: 22px 28px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
}
.br-header::before {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,.06);
    border-radius: 50%;
}
.br-header::after {
    content: '';
    position: absolute;
    bottom: -30px; left: 80px;
    width: 120px; height: 120px;
    background: rgba(59,130,246,.18);
    border-radius: 50%;
}
.br-header h4  { font-size: 20px; font-weight: 800; margin: 0 0 3px; position: relative; z-index: 1; }
.br-header .sub { font-size: 13px; opacity: .75; position: relative; z-index: 1; }
.br-header .header-actions { position: relative; z-index: 1; }

/* ── Stat cards ── */
.br-stat {
    background: #fff;
    border-radius: 12px;
    padding: 18px 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    display: flex;
    align-items: center;
    gap: 14px;
    border-left: 4px solid transparent;
    transition: transform .2s, box-shadow .2s;
    height: 100%;
}
.br-stat:hover { transform: translateY(-2px); box-shadow: 0 6px 22px rgba(0,0,0,.11); }
.br-stat .sc-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.br-stat .sc-label { font-size: 11px; font-weight: 700; color: #8a94a6; text-transform: uppercase; letter-spacing: .5px; }
.br-stat .sc-value { font-size: 26px; font-weight: 800; color: #1a2340; line-height: 1; margin-top: 2px; }
.br-stat.stat-total  { border-left-color: #3b82f6; }
.br-stat.stat-total  .sc-icon { background: #eff6ff; color: #3b82f6; }
.br-stat.stat-active { border-left-color: #10b981; }
.br-stat.stat-active .sc-icon { background: #ecfdf5; color: #10b981; }
.br-stat.stat-active .sc-value { color: #10b981; }
.br-stat.stat-ho     { border-left-color: #764ba2; }
.br-stat.stat-ho     .sc-icon { background: #f5f3ff; color: #764ba2; }

/* ── Filter bar ── */
.br-filter-bar {
    background: #fff;
    border-radius: 12px;
    padding: 14px 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.br-search-wrap {
    flex: 1; min-width: 200px; position: relative;
}
.br-search-wrap .si { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #b0bac9; font-size: 14px; pointer-events: none; }
.br-search-wrap input { padding-left: 36px; border-color: #e2e8f0; border-radius: 8px; min-height: 40px; font-size: 13px; }
.br-search-wrap input:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,.12); }

/* ── Table card ── */
.br-table-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    overflow: hidden;
}
.br-table-card .card-header-custom {
    padding: 16px 20px;
    border-bottom: 1px solid #f0f2f7;
    background: #fafbff;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 8px;
}
.br-table-card .card-header-custom h5 { margin: 0; font-size: 15px; font-weight: 700; color: #1a2340; }
.br-table-card .card-header-custom .sub { font-size: 12px; color: #8a94a6; margin-top: 2px; }
.br-table-card .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%; }

.br-table { width: 100%; border-collapse: collapse; min-width: 780px; }
.br-table thead th {
    background: #f8fafc;
    padding: 11px 14px;
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: #596579;
    border-bottom: 2px solid #edf0f7; white-space: nowrap;
}
.br-table tbody td {
    padding: 12px 14px; font-size: 13px; color: #303549;
    border-bottom: 1px solid #f3f5f9; vertical-align: middle;
}
.br-table tbody tr:last-child td { border-bottom: none; }
.br-table tbody tr:hover td { background: #f5f7ff; transition: background .12s; }

/* Name cell with avatar */
.br-name-cell { display: flex; align-items: center; gap: 10px; }
.br-avatar {
    width: 34px; height: 34px; border-radius: 8px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #fff; font-weight: 700; font-size: 13px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; text-transform: uppercase;
}
.br-name-cell .name    { font-weight: 600; color: #1a2340; font-size: 13px; }
.br-name-cell .sub-txt { font-size: 11px; color: #8a94a6; margin-top: 1px; }

/* Status badge */
.status-badge {
    display: inline-flex; align-items: center;
    padding: 3px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px;
}
.status-badge.active   { background: #dcfce7; color: #166534; }
.status-badge.inactive { background: #fee2e2; color: #991b1b; }
.status-badge.ho-yes   { background: #dbeafe; color: #1e40af; }

/* Action buttons */
.btn-action-view { background: #eff6ff; color: #3b82f6; border: none; border-radius: 6px; padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; }
.btn-action-edit { background: #fff8e6; color: #d97706; border: none; border-radius: 6px; padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; }
.btn-action-del  { background: #fff5f5; color: #e53e3e; border: none; border-radius: 6px; padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; }
.btn-action-view:hover { background: #3b82f6; color: #fff; }
.btn-action-edit:hover { background: #d97706; color: #fff; }
.btn-action-del:hover  { background: #e53e3e; color: #fff; }

/* Add button */
.btn-add-branch {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: #fff; border: none; border-radius: 8px;
    padding: 9px 18px; font-size: 13px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 6px;
    cursor: pointer; transition: all .2s; white-space: nowrap;
}
.btn-add-branch:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(59,130,246,.4); color: #fff; }

/* Empty state */
.empty-state-row td { padding: 48px 20px !important; text-align: center; background: #fff; }
.empty-state-inner .ei { font-size: 44px; color: #d7dce5; display: block; margin-bottom: 10px; }
.empty-state-inner .et { font-size: 15px; font-weight: 700; color: #8a94a6; margin-bottom: 4px; }
.empty-state-inner .es { font-size: 13px; color: #b0bac9; }

/* ── Slide-in panel ── */
.br-modal-backdrop {
    display: none;
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(26,35,64,.45);
    z-index: 1040;
    backdrop-filter: blur(2px);
}
.br-modal-backdrop.show { display: block; }

.br-modal-panel {
    position: fixed; top: 0; right: 0; bottom: 0;
    width: 100%; max-width: 620px;
    background: #fff;
    z-index: 1050;
    display: flex; flex-direction: column;
    transform: translateX(100%);
    transition: transform .28s cubic-bezier(.4,0,.2,1);
    box-shadow: -8px 0 40px rgba(0,0,0,.16);
    overflow: hidden;
}
.br-modal-panel.open { transform: translateX(0); }

.br-panel-header {
    background: linear-gradient(135deg, #303549 0%, #4a5080 100%);
    padding: 18px 20px;
    display: flex; align-items: center; justify-content: space-between;
    flex-shrink: 0;
}
.br-panel-header h5 { color: #fff; font-size: 16px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; }
.br-panel-header .panel-close {
    width: 36px; height: 36px; border-radius: 8px;
    background: rgba(255,255,255,.15); border: none;
    color: #fff; font-size: 18px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.br-panel-header .panel-close:hover { background: #e53e3e; }

#branchForm { display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden; }

.br-panel-body {
    flex: 1; min-height: 0;
    overflow-y: auto; overflow-x: hidden;
    padding: 24px 20px;
    -webkit-overflow-scrolling: touch;
}

.br-section-title {
    font-size: 11px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .8px; color: #3b82f6; margin-bottom: 14px;
    display: flex; align-items: center; gap: 6px;
    padding-bottom: 8px; border-bottom: 1px solid #edf0f7;
}

.form-group-br { margin-bottom: 16px; }
.form-group-br label { display: block; font-size: 12px; font-weight: 700; color: #596579; margin-bottom: 6px; }
.form-group-br label .req { color: #e53e3e; }
.form-group-br .form-control {
    border-color: #d7dce5; border-radius: 8px;
    font-size: 13px; color: #303549; min-height: 42px;
    transition: border-color .15s, box-shadow .15s;
}
.form-group-br .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
.form-group-br .form-control:disabled { background: #f8fafc; color: #596579; cursor: default; }
.form-group-br textarea.form-control { min-height: 80px; resize: vertical; }
.form-group-br .select2-container { width: 100% !important; }

.br-panel-footer {
    padding: 14px 20px;
    border-top: 1px solid #edf0f7;
    background: #fafbff;
    display: flex; align-items: center; justify-content: flex-end; gap: 10px;
    flex-shrink: 0;
}
.btn-panel-cancel {
    background: #f0f2f7; color: #596579; border: none; border-radius: 8px;
    padding: 9px 20px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .15s;
}
.btn-panel-cancel:hover { background: #e2e8f0; color: #303549; }
.btn-panel-save {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: #fff; border: none; border-radius: 8px;
    padding: 9px 22px; font-size: 13px; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px; transition: all .2s;
}
.btn-panel-save:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(59,130,246,.4); }

/* Mode badge */
.panel-mode-badge { font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 20px; letter-spacing: .4px; }
.panel-mode-badge.add-mode  { background: rgba(59,130,246,.25); color: #bfdbfe; }
.panel-mode-badge.edit-mode { background: rgba(255,193,7,.2);   color: #ffd86e; }
.panel-mode-badge.view-mode { background: rgba(56,161,105,.2);  color: #9be6b4; }

.branch-row.hidden { display: none; }

#brErrorContainer .alert { border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
</style>

<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">

                {{-- Page Header --}}
                <div class="br-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h4><i class="ti-location-pin mr-2"></i>Branches</h4>
                            <span class="sub">Manage all registered branches</span>
                        </div>
                        <div class="header-actions">
                            <button class="btn-add-branch" onclick="openBranchPanel('add')" id="btnAddBranch">
                                <i class="ti-plus"></i> Add Branch
                            </button>
                        </div>
                        @if($branchLimitReached)
                        <script>
                        document.getElementById('btnAddBranch').onclick = function(e) {
                            e.preventDefault();
                            toastr.error('This basic version allows limited branches. Please contact support team for more.');
                        };
                        </script>
                        @endif
                    </div>
                </div>

                {{-- Stat Cards --}}
                <div class="row mb-4">
                    <div class="col-sm-4 mb-3 mb-sm-0">
                        <div class="br-stat stat-total">
                            <div class="sc-icon"><i class="ti-location-pin"></i></div>
                            <div>
                                <div class="sc-label">Total Branches</div>
                                <div class="sc-value">{{ $branches->count() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mb-3 mb-sm-0">
                        <div class="br-stat stat-active">
                            <div class="sc-icon"><i class="ti-check-box"></i></div>
                            <div>
                                <div class="sc-label">Active</div>
                                <div class="sc-value">{{ $branches->where('status', true)->count() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="br-stat stat-ho">
                            <div class="sc-icon"><i class="ti-home"></i></div>
                            <div>
                                <div class="sc-label">Head Office</div>
                                <div class="sc-value">{{ $branches->where('head_office', true)->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('partials.flash')

                {{-- Filter Bar --}}
                <div class="br-filter-bar">
                    <div class="br-search-wrap">
                        <i class="ti-search si"></i>
                        <input type="text" id="branchSearch" class="form-control"
                               placeholder="Search by name, code, city, state...">
                    </div>
                </div>

                {{-- Table Card --}}
                <div class="br-table-card">
                    <div class="card-header-custom">
                        <div>
                            <h5>Branch List</h5>
                            <div class="sub">All registered branches are shown below</div>
                        </div>
                        <span class="badge badge-info" style="font-size:12px; padding:5px 10px; border-radius:20px;">
                            {{ $branches->count() }} records
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="br-table" id="branchTable">
                            <thead>
                                <tr>
                                    <th style="width:48px;">#</th>
                                    <th>Branch</th>
                                    <th>Company</th>
                                    <th>Mobile</th>
                                    <th>City / State</th>
                                    <th style="text-align:center;">Head Office</th>
                                    <th style="text-align:center;">Status</th>
                                    <th style="width:140px; text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branches as $key => $branch)
                                <tr class="branch-row"
                                    data-search="{{ strtolower($branch->branch_name . ' ' . $branch->branch_code . ' ' . $branch->city . ' ' . $branch->state . ' ' . ($branch->company?->company_name ?? '')) }}">
                                    <td style="color:#b0bac9; font-weight:600; font-size:12px;">{{ $key + 1 }}</td>
                                    <td>
                                        <div class="br-name-cell">
                                            <div class="br-avatar">{{ mb_substr($branch->branch_name, 0, 1) }}</div>
                                            <div>
                                                <div class="name">{{ $branch->branch_name }}</div>
                                                <div class="sub-txt"><i class="ti-tag" style="font-size:10px;"></i> {{ $branch->branch_code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($branch->company)
                                            <span style="font-size:12px; color:#596579;"><i class="ti-briefcase" style="color:#b0bac9;"></i> {{ $branch->company->company_name }}</span>
                                        @else
                                            <span style="color:#d7dce5;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($branch->mobile)
                                            <span style="font-size:12px; color:#596579;"><i class="ti-mobile" style="color:#b0bac9;"></i> {{ $branch->mobile }}</span>
                                        @else
                                            <span style="color:#d7dce5;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($branch->city || $branch->state)
                                            <span style="font-size:12px; color:#596579;">{{ implode(', ', array_filter([$branch->city, $branch->state])) }}</span>
                                        @else
                                            <span style="color:#d7dce5;">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        @if($branch->head_office)
                                            <span class="status-badge ho-yes">Yes</span>
                                        @else
                                            <span style="color:#d7dce5; font-size:12px;">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        @if($branch->status)
                                            <span class="status-badge active">Active</span>
                                        @else
                                            <span class="status-badge inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        <div style="display:flex; align-items:center; justify-content:center; gap:5px;">
                                            <button class="btn-action-view" onclick="openBranchPanel('view', {{ $branch->id }})" title="View">
                                                <i class="ti-eye"></i>
                                            </button>
                                            <button class="btn-action-edit" onclick="openBranchPanel('edit', {{ $branch->id }})" title="Edit">
                                                <i class="ti-pencil"></i>
                                            </button>
                                            <button class="btn-action-del" onclick="showDeleteModal('deleteFormBranch{{ $branch->id }}', '{{ addslashes($branch->branch_name) }}', 'Branch')" title="Delete">
                                                <i class="ti-trash"></i>
                                            </button>
                                        </div>
                                        <form id="deleteFormBranch{{ $branch->id }}"
                                              action="{{ route('branch.destroy', $branch->id) }}"
                                              method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr class="empty-state-row">
                                    <td colspan="8">
                                        <div class="empty-state-inner">
                                            <i class="ti-location-pin ei"></i>
                                            <div class="et">No branches yet</div>
                                            <div class="es">Click "Add Branch" to register your first branch</div>
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

{{-- ── Delete Modal ── --}}
@include('partials.delete-modal')

{{-- ── Slide-in Panel ── --}}
<div class="br-modal-backdrop" id="brBackdrop" onclick="closeBranchPanel()"></div>

<div class="br-modal-panel" id="brPanel">
    <div class="br-panel-header">
        <h5 id="brPanelTitle"><i class="ti-location-pin"></i> Add Branch</h5>
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="panel-mode-badge add-mode" id="brModeBadge">New</span>
            <button class="panel-close" onclick="closeBranchPanel()">
                <i class="ti-close"></i>
            </button>
        </div>
    </div>

    <form id="branchForm" method="POST" action="{{ route('branch.store') }}">
        @csrf
        <input type="hidden" name="_method" id="brFormMethod" value="POST">

        <div class="br-panel-body">
            <div id="brErrorContainer"></div>

            {{-- Section: Identity --}}
            <div class="br-section-title"><i class="ti-id-badge"></i> Identity & Status</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-br">
                        <label>Company <span class="req">*</span></label>
                        <select name="company_id" id="brCompanyId" class="form-control select2-br" required>
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-br">
                        <label>Branch Code <span class="req">*</span></label>
                        <input type="text" id="brBranchCode" name="branch_code" class="form-control"
                               placeholder="e.g. BR001" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group-br">
                        <label>Branch Name <span class="req">*</span></label>
                        <input type="text" id="brBranchName" name="branch_name" class="form-control"
                               placeholder="Branch name" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group-br">
                        <label>Status</label>
                        <select name="status" id="brStatus" class="form-control select2-br">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group-br">
                <div style="display:flex; align-items:center; gap:10px; margin-top:4px;">
                    <input type="checkbox" name="head_office" id="brHeadOffice" value="1"
                           style="width:18px; height:18px; border-radius:4px; accent-color:#3b82f6; cursor:pointer;">
                    <label for="brHeadOffice" style="margin:0; font-size:13px; color:#596579; cursor:pointer; font-weight:600;">Mark as Head Office</label>
                </div>
            </div>

            {{-- Section: Contact --}}
            <div class="br-section-title" style="margin-top:20px;"><i class="ti-headphone-alt"></i> Contact Details</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-br">
                        <label>Email Address</label>
                        <input type="email" id="brEmail" name="email" class="form-control"
                               placeholder="branch@example.com">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-br">
                        <label>Mobile Number</label>
                        <input type="text" id="brMobile" name="mobile" class="form-control"
                               placeholder="10-digit mobile">
                    </div>
                </div>
            </div>

            {{-- Section: Address --}}
            <div class="br-section-title" style="margin-top:20px;"><i class="ti-location-pin"></i> Address Details</div>
            <div class="form-group-br">
                <label>Street Address</label>
                <textarea id="brAddress" name="address" class="form-control"
                          placeholder="Door no, street, area" rows="2"></textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-br">
                        <label>City</label>
                        <input type="text" id="brCity" name="city" class="form-control" placeholder="City">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-br">
                        <label>State</label>
                        <input type="text" id="brState" name="state" class="form-control" placeholder="State">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-br">
                        <label>Pincode</label>
                        <input type="text" id="brPincode" name="pincode" class="form-control" placeholder="6-digit PIN">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-br">
                        <label>Country</label>
                        <input type="text" id="brCountry" name="country" class="form-control" placeholder="Country" value="India">
                    </div>
                </div>
            </div>
        </div>

        <div class="br-panel-footer">
            <button type="button" class="btn-panel-cancel" onclick="closeBranchPanel()">
                <i class="ti-close"></i> Cancel
            </button>
            <button type="submit" class="btn-panel-save" id="brSaveBtn">
                <i class="ti-save"></i> Save Branch
            </button>
        </div>
    </form>
</div>

<script>
// ── State ────────────────────────────────────────────────────────────────────
var brMode   = 'add';
var brEditId = null;

// ── Search ───────────────────────────────────────────────────────────────────
document.getElementById('branchSearch').addEventListener('keyup', function () {
    var term = this.value.toLowerCase();
    document.querySelectorAll('.branch-row').forEach(function (row) {
        row.classList.toggle('hidden', row.dataset.search.indexOf(term) === -1);
    });
});

// ── Select2 ───────────────────────────────────────────────────────────────────
function brInitSelect2(disabled) {
    if (!$.fn.select2) return;
    setTimeout(function () {
        $('.select2-br').select2({ dropdownParent: $('#brPanel'), width: '100%' });
        if (disabled) {
            $('.select2-br').prop('disabled', true).trigger('change');
        }
    }, 50);
}

function brDestroySelect2() {
    if (!$.fn.select2) return;
    $('.select2-br').each(function () {
        if ($(this).data('select2')) {
            $(this).select2('destroy');
        }
    });
}

// ── Open panel ───────────────────────────────────────────────────────────────
function openBranchPanel(mode, branchId) {
    brMode   = mode;
    brEditId = branchId || null;

    brResetForm();
    brDestroySelect2();

    if (mode === 'add') {
        brSetHeader('<i class="ti-location-pin"></i> Add New Branch', 'New', 'add-mode');
        brSetReadonly(false);
        brShowSaveBtn(true);
        document.getElementById('branchForm').action = '{{ route("branch.store") }}';
        document.getElementById('brFormMethod').value = 'POST';
        brOpenDrawer();
        brInitSelect2(false);
        return;
    }

    // edit or view — fetch JSON
    var url = (mode === 'edit')
        ? '/branch/edit/' + branchId
        : '/branch/view/' + branchId;

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            // Always enable first so selects can receive values
            brSetReadonly(false);
            brFillForm(data);

            if (mode === 'view') {
                brSetHeader('<i class="ti-eye"></i> View Branch', 'View', 'view-mode');
                brSetReadonly(true);
                brShowSaveBtn(false);
                brOpenDrawer();
                brInitSelect2(true);
            } else {
                brSetHeader('<i class="ti-pencil"></i> Edit Branch', 'Edit', 'edit-mode');
                brShowSaveBtn(true);
                document.getElementById('branchForm').action = '/branch/' + branchId;
                document.getElementById('brFormMethod').value = 'PUT';
                brOpenDrawer();
                brInitSelect2(false);
            }
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message)
                ? xhr.responseJSON.message
                : 'Could not load branch data. Please try again.';
            alert(msg);
        }
    });
}

function brOpenDrawer() {
    document.getElementById('brBackdrop').classList.add('show');
    document.getElementById('brPanel').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeBranchPanel() {
    document.getElementById('brBackdrop').classList.remove('show');
    document.getElementById('brPanel').classList.remove('open');
    document.body.style.overflow = '';
    brDestroySelect2();
    setTimeout(brResetForm, 300);
}

function brResetForm() {
    // Re-enable all fields first so reset() works cleanly
    brSetReadonly(false);
    document.getElementById('branchForm').reset();
    document.getElementById('brErrorContainer').innerHTML = '';
    document.getElementById('brCountry').value = 'India';
    var btn = document.getElementById('brSaveBtn');
    btn.disabled = false;
    btn.innerHTML = '<i class="ti-save"></i> Save Branch';
}

function brSetHeader(title, badgeText, badgeClass) {
    document.getElementById('brPanelTitle').innerHTML = title;
    var b = document.getElementById('brModeBadge');
    b.className   = 'panel-mode-badge ' + badgeClass;
    b.textContent = badgeText;
}

function brSetReadonly(readonly) {
    document.querySelectorAll(
        '#branchForm input:not([type=hidden]), #branchForm textarea, #branchForm select'
    ).forEach(function (el) { el.disabled = readonly; });
}

function brShowSaveBtn(show) {
    document.getElementById('brSaveBtn').style.display = show ? '' : 'none';
}

function brFillForm(data) {
    // Use jQuery val + trigger('change') so Select2 reflects the correct value
    $('#brCompanyId').val(data.company_id  || '').trigger('change');
    $('#brStatus').val(data.status ? '1' : '0').trigger('change');
    document.getElementById('brBranchCode').value    = data.branch_code || '';
    document.getElementById('brBranchName').value    = data.branch_name || '';
    document.getElementById('brHeadOffice').checked  = !!data.head_office;
    document.getElementById('brEmail').value         = data.email   || '';
    document.getElementById('brMobile').value        = data.mobile  || '';
    document.getElementById('brAddress').value       = data.address || '';
    document.getElementById('brCity').value          = data.city    || '';
    document.getElementById('brState').value         = data.state   || '';
    document.getElementById('brPincode').value       = data.pincode || '';
    document.getElementById('brCountry').value       = data.country || 'India';
}

// ── Form submit via AJAX ──────────────────────────────────────────────────────
document.getElementById('branchForm').addEventListener('submit', function (e) {
    e.preventDefault();

    var form   = this;
    var btn    = document.getElementById('brSaveBtn');
    var method = document.getElementById('brFormMethod').value;
    var url    = form.action;

    btn.disabled = true;
    btn.innerHTML = '<i class="ti-reload"></i> Saving...';
    document.getElementById('brErrorContainer').innerHTML = '';

    var formData = new FormData(form);

    // HEAD OFFICE: ensure unchecked sends 0
    if (!document.getElementById('brHeadOffice').checked) {
        formData.set('head_office', '0');
    }

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        success: function () {
            closeBranchPanel();
            location.reload();
        },
        error: function (xhr) {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti-save"></i> Save Branch';

            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                var html = '<div class="alert alert-danger"><ul style="margin:0;padding-left:18px;">';
                $.each(xhr.responseJSON.errors, function (k, msgs) {
                    html += '<li>' + msgs[0] + '</li>';
                });
                html += '</ul></div>';
                document.getElementById('brErrorContainer').innerHTML = html;
            } else {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'An error occurred. Please try again.';
                document.getElementById('brErrorContainer').innerHTML =
                    '<div class="alert alert-danger">' + msg + '</div>';
            }
        }
    });
});
</script>

@endsection
