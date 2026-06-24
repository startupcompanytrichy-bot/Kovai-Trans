@extends('layouts.app')

@section('content')

<style>
    /* ── Page gradient header ── */
    .par-header {
        background: linear-gradient(135deg, #303549 0%, #4a5080 100%);
        border-radius: 14px;
        padding: 22px 28px;
        color: #fff;
        margin-bottom: 22px;
        position: relative;
        overflow: hidden;
    }

    .par-header::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, .06);
        border-radius: 50%;
    }

    .par-header::after {
        content: '';
        position: absolute;
        bottom: -30px;
        left: 80px;
        width: 120px;
        height: 120px;
        background: rgba(118, 75, 162, .18);
        border-radius: 50%;
    }

    .par-header h4 {
        font-size: 20px;
        font-weight: 800;
        margin: 0 0 3px;
        position: relative;
        z-index: 1;
    }

    .par-header .sub {
        font-size: 13px;
        opacity: .75;
        position: relative;
        z-index: 1;
    }

    .par-header .header-actions {
        position: relative;
        z-index: 1;
    }

    /* ── Stat cards ── */
    .par-stat {
        background: #fff;
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
        display: flex;
        align-items: center;
        gap: 14px;
        border-left: 4px solid transparent;
        transition: transform .2s, box-shadow .2s;
        height: 100%;
    }

    .par-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 22px rgba(0, 0, 0, .11);
    }

    .par-stat .sc-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .par-stat .sc-label {
        font-size: 11px;
        font-weight: 700;
        color: #8a94a6;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .par-stat .sc-value {
        font-size: 26px;
        font-weight: 800;
        color: #1a2340;
        line-height: 1;
        margin-top: 2px;
    }

    .par-stat.stat-balance {
        border-left-color: #667eea;
    }

    .par-stat.stat-balance .sc-icon {
        background: #eef2ff;
        color: #667eea;
    }

    .par-stat.stat-balance .sc-value {
        color: #667eea;
    }

    .par-stat.stat-count {
        border-left-color: #764ba2;
    }

    .par-stat.stat-count .sc-icon {
        background: #f5f3ff;
        color: #764ba2;
    }

    /* ── Filter bar ── */
    .par-filter-bar {
        background: #fff;
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .par-search-wrap {
        flex: 1;
        min-width: 200px;
        position: relative;
    }

    .par-search-wrap .si {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #b0bac9;
        font-size: 14px;
        pointer-events: none;
    }

    .par-search-wrap input {
        padding-left: 36px;
        border-color: #e2e8f0;
        border-radius: 8px;
        min-height: 40px;
        font-size: 13px;
    }

    .par-search-wrap input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, .12);
    }

    /* ── Table card ── */
    .par-table-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
        overflow: hidden;
    }

    .par-table-card .card-header-custom {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f2f7;
        background: #fafbff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }

    .par-table-card .card-header-custom h5 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #1a2340;
    }

    .par-table-card .card-header-custom .sub {
        font-size: 12px;
        color: #8a94a6;
        margin-top: 2px;
    }

    .par-table-card .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    .par-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 780px;
    }

    .par-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 780px;
    }

    .par-table thead th {
        background: #f8fafc;
        padding: 11px 14px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #596579;
        border-bottom: 2px solid #edf0f7;
        white-space: nowrap;
    }

    .par-table tbody td {
        padding: 12px 14px;
        font-size: 13px;
        color: #303549;
        border-bottom: 1px solid #f3f5f9;
        vertical-align: middle;
    }

    .par-table tbody tr:last-child td {
        border-bottom: none;
    }

    .par-table tbody tr:hover td {
        background: #f5f7ff;
        transition: background .12s;
    }

    /* Name cell with avatar */
    .par-name-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .par-avatar {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
        font-weight: 700;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        text-transform: uppercase;
    }

    .par-name-cell .name {
        font-weight: 600;
        color: #1a2340;
        font-size: 13px;
    }

    .par-name-cell .company {
        font-size: 11px;
        color: #8a94a6;
        margin-top: 1px;
    }

    /* Party type badge */
    .party-type-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .3px;
        background: #eef2ff;
        color: #667eea;
    }

    /* Balance cell */
    .balance-cell {
        font-weight: 700;
        color: #667eea;
        font-size: 13px;
    }

    /* Action buttons */
    .btn-action-view {
        background: #eef2ff;
        color: #667eea;
        border: none;
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
    }

    .btn-action-edit {
        background: #fff8e6;
        color: #d97706;
        border: none;
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
    }

    .btn-action-del {
        background: #fff5f5;
        color: #e53e3e;
        border: none;
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
    }

    .btn-action-view:hover {
        background: #667eea;
        color: #fff;
    }

    .btn-action-edit:hover {
        background: #d97706;
        color: #fff;
    }

    .btn-action-del:hover {
        background: #e53e3e;
        color: #fff;
    }

    /* Add button */
    .btn-add-party {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 9px 18px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all .2s;
        white-space: nowrap;
    }

    .btn-add-party:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(102, 126, 234, .4);
        color: #fff;
    }

    /* Empty state */
    .empty-state-row td {
        padding: 48px 20px !important;
        text-align: center;
        background: #fff;
    }

    .empty-state-inner .ei {
        font-size: 44px;
        color: #d7dce5;
        display: block;
        margin-bottom: 10px;
    }

    .empty-state-inner .et {
        font-size: 15px;
        font-weight: 700;
        color: #8a94a6;
        margin-bottom: 4px;
    }

    .empty-state-inner .es {
        font-size: 13px;
        color: #b0bac9;
    }

    /* ── Slide-in modal ── */
    .par-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(26, 35, 64, .45);
        z-index: 1040;
        backdrop-filter: blur(2px);
    }

    .par-modal-backdrop.show {
        display: block;
    }

    .par-modal-panel {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        max-width: 620px;
        background: #fff;
        z-index: 1050;
        display: flex;
        flex-direction: column;
        transform: translateX(100%);
        transition: transform .28s cubic-bezier(.4, 0, .2, 1);
        box-shadow: -8px 0 40px rgba(0, 0, 0, .16);
        overflow: hidden;
    }

    .par-modal-panel.open {
        transform: translateX(0);
    }

    .par-panel-header {
        background: linear-gradient(135deg, #303549 0%, #4a5080 100%);
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .par-panel-header h5 {
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .par-panel-header .panel-close {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: rgba(255, 255, 255, .15);
        border: none;
        color: #fff;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .15s;
    }

    .par-panel-header .panel-close:hover {
        background: #e53e3e;
    }

    /* form fills remaining height */
    #partyForm {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }

    .par-panel-body {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 24px 20px;
        -webkit-overflow-scrolling: touch;
    }

    .par-section-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: #667eea;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
        padding-bottom: 8px;
        border-bottom: 1px solid #edf0f7;
    }

    .form-group-par {
        margin-bottom: 16px;
    }

    .form-group-par label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #596579;
        margin-bottom: 6px;
    }

    .form-group-par label .req {
        color: #e53e3e;
    }

    .form-group-par .form-control {
        border-color: #d7dce5;
        border-radius: 8px;
        font-size: 13px;
        color: #303549;
        min-height: 42px;
        transition: border-color .15s, box-shadow .15s;
    }

    .form-group-par .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, .12);
    }

    .form-group-par .form-control:disabled {
        background: #f8fafc;
        color: #596579;
        cursor: default;
    }

    .form-group-par textarea.form-control {
        min-height: 80px;
        resize: vertical;
    }

    .form-group-par .select2-container {
        width: 100% !important;
    }

    .par-panel-footer {
        padding: 14px 20px;
        border-top: 1px solid #edf0f7;
        background: #fafbff;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-shrink: 0;
    }

    .btn-panel-cancel {
        background: #f0f2f7;
        color: #596579;
        border: none;
        border-radius: 8px;
        padding: 9px 20px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
    }

    .btn-panel-cancel:hover {
        background: #e2e8f0;
        color: #303549;
    }

    .btn-panel-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 9px 22px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all .2s;
    }

    .btn-panel-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(102, 126, 234, .4);
    }

    /* row hidden */
    .party-row.hidden {
        display: none;
    }

    /* Mode badge */
    .panel-mode-badge {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 3px 8px;
        border-radius: 20px;
        letter-spacing: .4px;
    }

    .panel-mode-badge.add-mode {
        background: rgba(102, 126, 234, .25);
        color: #c5d0ff;
    }

    .panel-mode-badge.edit-mode {
        background: rgba(255, 193, 7, .2);
        color: #ffd86e;
    }

    .panel-mode-badge.view-mode {
        background: rgba(56, 161, 105, .2);
        color: #9be6b4;
    }

    /* error container */
    #parErrorContainer .alert {
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 16px;
    }
</style>

<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">

                {{-- Page Header --}}
                <div class="par-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h4><i class="ti-user mr-2"></i>Parties</h4>
                            <span class="sub">Manage all registered parties and their balances</span>
                        </div>
                        <div class="header-actions">
                            <button class="btn-add-party" onclick="openPartyPanel('add')">
                                <i class="ti-plus"></i> Add Party
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Stat Cards --}}
                <div class="row mb-4">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <div class="par-stat stat-balance">
                            <div class="sc-icon"><i class="ti-wallet"></i></div>
                            <div>
                                <div class="sc-label">Total Party Balance</div>
                                <div class="sc-value">₹ {{ number_format($parties->sum('opening_balance') ?? 0, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="par-stat stat-count">
                            <div class="sc-icon"><i class="ti-id-badge"></i></div>
                            <div>
                                <div class="sc-label">Total Parties</div>
                                <div class="sc-value">{{ $parties->count() ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('partials.flash')

                {{-- Filter Bar --}}
                <div class="par-filter-bar">
                    <div class="par-search-wrap">
                        <i class="ti-search si"></i>
                        <input type="text" id="partySearch" class="form-control"
                            placeholder="Search by name, email, phone...">
                    </div>
                </div>

                {{-- Table Card --}}
                <div class="par-table-card">
                    <div class="card-header-custom">
                        <div>
                            <h5>Parties List</h5>
                            <div class="sub">All registered parties are shown below</div>
                        </div>
                        <span class="badge badge-info" style="font-size:12px; padding:5px 10px; border-radius:20px;">
                            {{ $parties->count() }} records
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="par-table" id="partiesTable">
                            <thead>
                                <tr>
                                    <th style="width:48px;">#</th>
                                    <th>Party / Company</th>
                                    <th>Type</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th style="text-align:right;">Balance</th>
                                    <th style="width:140px; text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($parties ?? [] as $key => $party)
                                <tr class="party-row"
                                    data-search="{{ strtolower($party->name . ' ' . $party->email . ' ' . $party->phone . ' ' . $party->company_name) }}">
                                    <td style="color:#b0bac9; font-weight:600; font-size:12px;">{{ $key + 1 }}</td>
                                    <td>
                                        <div class="par-name-cell">
                                            <div class="par-avatar">{{ mb_substr($party->name, 0, 1) }}</div>
                                            <div>
                                                <div class="name">{{ $party->name }}</div>
                                                @if($party->company_name)
                                                <div class="company"><i class="ti-briefcase" style="font-size:10px;"></i> {{ $party->company_name }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="party-type-badge">{{ ucfirst($party->party_type ?? 'Party') }}</span>
                                    </td>
                                    <td>
                                        @if($party->phone)
                                        <span style="font-size:12px; color:#596579;"><i class="ti-mobile" style="color:#b0bac9;"></i> {{ $party->phone }}</span>
                                        @else
                                        <span style="color:#d7dce5;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($party->email)
                                        <span style="font-size:12px; color:#596579;"><i class="ti-email" style="color:#b0bac9;"></i> {{ $party->email }}</span>
                                        @else
                                        <span style="color:#d7dce5;">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right;">
                                        <span class="balance-cell">₹ {{ number_format($party->opening_balance ?? 0, 2) }}</span>
                                    </td>
                                    <td style="text-align:center;">
                                        <div style="display:flex; align-items:center; justify-content:center; gap:5px;">
                                            <button class="btn-action-view" onclick="openPartyPanel('view', {{ $party->id }})" title="View">
                                                <i class="ti-eye"></i>
                                            </button>
                                            <button class="btn-action-edit" onclick="openPartyPanel('edit', {{ $party->id }})" title="Edit">
                                                <i class="ti-pencil"></i>
                                            </button>
                                            <button class="btn-action-del" onclick="deleteParty({{ $party->id }}, '{{ addslashes($party->name) }}')" title="Delete">
                                                <i class="ti-trash"></i>
                                            </button>
                                        </div>
                                        <form id="deleteFormParty{{ $party->id }}"
                                            action="{{ route('parties.destroy', $party->id) }}"
                                            method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr class="empty-state-row">
                                    <td colspan="7">
                                        <div class="empty-state-inner">
                                            <i class="ti-user ei"></i>
                                            <div class="et">No parties yet</div>
                                            <div class="es">Click "Add Party" to register your first party</div>
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

{{-- ── Slide-in Panel ── --}}
<div class="par-modal-backdrop" id="parBackdrop" onclick="closePartyPanel()"></div>

<div class="par-modal-panel" id="parPanel">
    <div class="par-panel-header">
        <h5 id="parPanelTitle"><i class="ti-user"></i> Add Party</h5>
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="panel-mode-badge add-mode" id="parModeBadge">New</span>
            <button class="panel-close" onclick="closePartyPanel()">
                <i class="ti-close"></i>
            </button>
        </div>
    </div>

    <form id="partyForm" method="POST" action="{{ route('parties.store') }}">
        @csrf

        <div class="par-panel-body">
            <div id="parErrorContainer"></div>

            {{-- Section: Party Info --}}
            <div class="par-section-title"><i class="ti-user"></i> Party Information</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-par">
                        <label for="partyName">Party Name <span class="req">*</span></label>
                        <input type="text" id="partyName" name="name" class="form-control"
                            placeholder="Enter party name" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-par">
                        <label for="companyName">Company Name</label>
                        <input type="text" id="companyName" name="company_name" class="form-control"
                            placeholder="Enter company name">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-par">
                        <label for="partyType">Party Type</label>
                        <select id="partyType" name="party_type" class="form-control select2-par">
                            <option value="Parties">Parties</option>
                            <option value="Customer">Customer</option>
                            <option value="Vendor">Vendor</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Section: Contact --}}
            <div class="par-section-title" style="margin-top:20px;"><i class="ti-headphone-alt"></i> Contact Details</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-par">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control"
                            placeholder="e.g. 9876543210" maxlength="10">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-par">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control"
                            placeholder="name@example.com">
                    </div>
                </div>
            </div>
            <div class="form-group-par">
                <label for="address">Address</label>
                <textarea id="address" name="address" class="form-control"
                    placeholder="Enter full address..." rows="2"></textarea>
            </div>

            {{-- Section: Tax --}}
            <div class="par-section-title" style="margin-top:20px;"><i class="ti-receipt"></i> Tax Details</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-par">
                        <label for="gstNo">GST Number</label>
                        <input type="text" id="gstNo" name="gst_no" class="form-control"
                            placeholder="e.g. 22AAAAA0000A1Z5" maxlength="15">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-par">
                        <label for="panNo">PAN Number</label>
                        <input type="text" id="panNo" name="pan_no" class="form-control"
                            placeholder="e.g. AAAPL1234C" maxlength="10">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-par">
                        <label for="aadhaarNo">Aadhaar Number</label>
                        <input type="text" id="aadhaarNo" name="aadhaar_no" class="form-control"
                            placeholder="12-digit Aadhaar" maxlength="12">
                    </div>
                </div>
            </div>

            {{-- Section: Financial --}}
            <div class="par-section-title" style="margin-top:20px;"><i class="ti-money"></i> Financial Details</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-par">
                        <label for="openingBalance">Opening Balance (₹)</label>
                        <input type="number" id="openingBalance" name="opening_balance"
                            class="form-control" step="0.01" placeholder="0.00">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-par">
                        <label for="openingBalanceDate">Balance Date</label>
                        <input type="date" id="openingBalanceDate" name="opening_balance_date" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <div class="par-panel-footer">
            <button type="button" class="btn-panel-cancel" onclick="closePartyPanel()">
                <i class="ti-close"></i> Cancel
            </button>
            <button type="submit" class="btn-panel-save" id="parSaveBtn">
                <i class="ti-save"></i> Save Party
            </button>
        </div>
    </form>
</div>

<script>
    // ── State ────────────────────────────────────────────────────────────────────
    var parMode = 'add'; // 'add' | 'edit' | 'view'
    var parEditId = null; // current party ID when editing

    // ── Search ───────────────────────────────────────────────────────────────────
    document.getElementById('partySearch').addEventListener('keyup', function() {
        var term = this.value.toLowerCase();
        document.querySelectorAll('.party-row').forEach(function(row) {
            row.classList.toggle('hidden', row.dataset.search.indexOf(term) === -1);
        });
    });

    // ── Open panel ───────────────────────────────────────────────────────────────
    function openPartyPanel(mode, partyId) {
        parMode = mode;
        parEditId = partyId || null;

        parResetForm();
        parDestroySelect2();

        if (mode === 'add') {
            parSetHeader('<i class="ti-user"></i> Add New Party', 'New', 'add-mode');
            parSetReadonly(false);
            parShowSaveBtn(true);
            parOpenDrawer();
            parInitSelect2(false);
            return;
        }

        // edit or view — fetch from server
        var url = (mode === 'edit') ? '/parties/edit/' + partyId : '/parties/view/' + partyId;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                parFillForm(data);

                if (mode === 'view') {
                    parSetHeader('<i class="ti-eye"></i> View Party', 'View', 'view-mode');
                    parSetReadonly(true);
                    parShowSaveBtn(false);
                } else {
                    parSetHeader('<i class="ti-pencil"></i> Edit Party', 'Edit', 'edit-mode');
                    parSetReadonly(false);
                    parShowSaveBtn(true);
                }

                parOpenDrawer();
                parInitSelect2(mode === 'view');
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ?
                    xhr.responseJSON.message :
                    'Could not load party data. Please try again.';
                parToast('error', msg);
            }
        });
    }

    function parOpenDrawer() {
        document.getElementById('parBackdrop').classList.add('show');
        document.getElementById('parPanel').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closePartyPanel() {
        document.getElementById('parBackdrop').classList.remove('show');
        document.getElementById('parPanel').classList.remove('open');
        document.body.style.overflow = '';
        parDestroySelect2();
        // Delayed reset so closing animation plays first
        setTimeout(parResetForm, 300);
    }

    function parResetForm() {
        document.getElementById('partyForm').reset();
        document.getElementById('parErrorContainer').innerHTML = '';
        // Reset save button state
        var btn = document.getElementById('parSaveBtn');
        btn.disabled = false;
        btn.innerHTML = '<i class="ti-save"></i> Save Party';
    }

    function parSetHeader(title, badgeText, badgeClass) {
        document.getElementById('parPanelTitle').innerHTML = title;
        var b = document.getElementById('parModeBadge');
        b.className = 'panel-mode-badge ' + badgeClass;
        b.textContent = badgeText;
    }

    function parSetReadonly(readonly) {
        document.querySelectorAll(
            '#partyForm input:not([type=hidden]), #partyForm textarea, #partyForm select'
        ).forEach(function(el) {
            el.disabled = readonly;
        });
    }

    function parShowSaveBtn(show) {
        document.getElementById('parSaveBtn').style.display = show ? '' : 'none';
    }

    function parFillForm(data) {
        document.getElementById('partyName').value = data.name || '';
        document.getElementById('companyName').value = data.company_name || '';
        document.getElementById('partyType').value = data.party_type || 'Parties';
        document.getElementById('phone').value = data.phone || '';
        document.getElementById('email').value = data.email || '';
        document.getElementById('address').value = data.address || '';
        document.getElementById('gstNo').value = data.gst_no || '';
        document.getElementById('panNo').value = data.pan_no || '';
        document.getElementById('aadhaarNo').value = data.aadhaar_no || '';
        document.getElementById('openingBalance').value = data.opening_balance || '';
        document.getElementById('openingBalanceDate').value = data.opening_balance_date || '';
    }

    // ── Select2 ───────────────────────────────────────────────────────────────────
    function parInitSelect2(disabled) {
        if (!$.fn.select2) return;
        setTimeout(function() {
            $('#partyType').select2({
                dropdownParent: $('#parPanel'),
                width: '100%'
            });
            if (disabled) {
                $('#partyType').prop('disabled', true).trigger('change');
            }
        }, 80);
    }

    function parDestroySelect2() {
        if ($.fn.select2 && $('#partyType').data('select2')) {
            $('#partyType').select2('destroy');
        }
    }

    // ── Form submit (AJAX) ────────────────────────────────────────────────────────
    document.getElementById('partyForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        var formData = {
            _token: csrfToken,
            name: document.getElementById('partyName').value.trim(),
            company_name: document.getElementById('companyName').value.trim(),
            party_type: document.getElementById('partyType').value,
            phone: document.getElementById('phone').value.trim(),
            email: document.getElementById('email').value.trim(),
            address: document.getElementById('address').value.trim(),
            gst_no: document.getElementById('gstNo').value.trim(),
            pan_no: document.getElementById('panNo').value.trim(),
            aadhaar_no: document.getElementById('aadhaarNo').value.trim(),
            opening_balance: document.getElementById('openingBalance').value,
            opening_balance_date: document.getElementById('openingBalanceDate').value,
        };

        var url;

        if (parMode === 'edit' && parEditId) {
            url = '/parties/' + parEditId;
            formData._method = 'PUT'; // Laravel method spoofing
        } else {
            url = '{{ route("parties.store") }}';
        }

        var btn = document.getElementById('parSaveBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="ti-reload" style="animation:spin .8s linear infinite;display:inline-block;"></i> Saving...';

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            success: function(res) {
                closePartyPanel();
                parToast('success', res.message || 'Party saved successfully');
                setTimeout(function() {
                    location.reload();
                }, 900);
            },
            error: function(xhr) {
                btn.disabled = false;
                btn.innerHTML = '<i class="ti-save"></i> Save Party';
                parShowErrors(xhr);
            }
        });
    });

    function parShowErrors(xhr) {
        var html = '<div class="alert alert-danger"><ul class="mb-0">';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            $.each(xhr.responseJSON.errors, function(field, msgs) {
                html += '<li>' + msgs[0] + '</li>';
            });
        } else if (xhr.status === 419) {
            html += '<li>Session expired. Please refresh the page and try again.</li>';
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
            html += '<li>' + xhr.responseJSON.message + '</li>';
        } else {
            html += '<li>An unexpected error occurred. Please try again.</li>';
        }
        html += '</ul></div>';
        document.getElementById('parErrorContainer').innerHTML = html;
        document.getElementById('parErrorContainer').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    // ── Delete ────────────────────────────────────────────────────────────────────
    function deleteParty(id, name) {
        showDeleteModal('deleteFormParty' + id, name, 'Party');
    }

    // ── Toast ─────────────────────────────────────────────────────────────────────
    function parToast(type, msg) {
        if (typeof toastr !== 'undefined') {
            toastr[type](msg);
        } else {
            alert(msg);
        }
    }

    // ── ESC closes panel ──────────────────────────────────────────────────────────
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePartyPanel();
        }
    });

    // ── Spin animation ────────────────────────────────────────────────────────────
    var parStyleEl = document.createElement('style');
    parStyleEl.textContent = '@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
    document.head.appendChild(parStyleEl);
</script>

@endsection