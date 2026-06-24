@extends('layouts.app')

@section('content')

<style>
/* ── Page gradient header ── */
.trd-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    border-radius: 14px;
    padding: 22px 28px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
}
.trd-header::before {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,.06);
    border-radius: 50%;
}
.trd-header::after {
    content: '';
    position: absolute;
    bottom: -30px; left: 60px;
    width: 100px; height: 100px;
    background: rgba(59,130,246,.15);
    border-radius: 50%;
}
.trd-header h4 { font-size: 20px; font-weight: 800; margin: 0 0 3px; position: relative; z-index: 1; }
.trd-header .sub { font-size: 13px; opacity: .75; position: relative; z-index: 1; }
.trd-header .header-actions { position: relative; z-index: 1; }

/* ── Stat cards ── */
.trd-stat {
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
.trd-stat:hover { transform: translateY(-2px); box-shadow: 0 6px 22px rgba(0,0,0,.11); }
.trd-stat .sc-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.trd-stat .sc-label { font-size: 11px; font-weight: 700; color: #8a94a6; text-transform: uppercase; letter-spacing: .5px; }
.trd-stat .sc-value { font-size: 26px; font-weight: 800; color: #1a2340; line-height: 1; margin-top: 2px; }
.trd-stat.stat-total { border-left-color: #3b82f6; }
.trd-stat.stat-total .sc-icon { background: #eff6ff; color: #3b82f6; }
.trd-stat.stat-active { border-left-color: #10b981; }
.trd-stat.stat-active .sc-icon { background: #ecfdf5; color: #10b981; }

/* ── Filter bar ── */
.trd-filter-bar {
    background: #fff;
    border-radius: 12px;
    padding: 14px 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.trd-filter-bar .select2-container {
    width: 200px !important;
    flex-shrink: 0;
}
.trd-filter-bar .select2-container--default .select2-selection--single {
    min-height: 40px; height: 40px;
    display: flex; align-items: center;
    border-color: #e2e8f0; border-radius: 8px;
}
.trd-filter-bar .select2-container--default.select2-container--focus .select2-selection--single,
.trd-filter-bar .select2-container--default.select2-container--open .select2-selection--single {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59,130,246,.12);
}
.trd-filter-bar .select2-container--default .select2-selection--single .select2-selection__rendered {
    font-size: 13px; padding-left: 12px;
}
.trd-filter-bar .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px;
}
.trd-search-wrap {
    flex: 1; min-width: 200px; position: relative;
}
.trd-search-wrap .si { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #b0bac9; font-size: 14px; pointer-events: none; }
.trd-search-wrap input { padding-left: 36px; border-color: #e2e8f0; border-radius: 8px; min-height: 40px; font-size: 13px; }
.trd-search-wrap input:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,.12); }

/* ── Table card ── */
.trd-table-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    overflow: hidden;
}
.trd-table-card .card-header-custom {
    padding: 16px 20px;
    border-bottom: 1px solid #f0f2f7;
    background: #fafbff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.trd-table-card .card-header-custom h5 { margin: 0; font-size: 15px; font-weight: 700; color: #1a2340; }
.trd-table-card .card-header-custom .sub { font-size: 12px; color: #8a94a6; margin-top: 2px; }

.trd-table-card .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
}

.trd-table { width: 100%; border-collapse: collapse; min-width: 620px; }
.trd-table thead th {
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
.trd-table tbody td {
    padding: 12px 14px;
    font-size: 13px;
    color: #303549;
    border-bottom: 1px solid #f3f5f9;
    vertical-align: middle;
}
.trd-table tbody tr:last-child td { border-bottom: none; }
.trd-table tbody tr:hover td { background: #f5f7ff; transition: background .12s; }

/* Name cell with avatar */
.trd-name-cell { display: flex; align-items: center; gap: 10px; }
.trd-avatar {
    width: 34px; height: 34px; border-radius: 8px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #fff; font-weight: 700; font-size: 13px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; text-transform: uppercase;
}
.trd-name-cell .name { font-weight: 600; color: #1a2340; font-size: 13px; }

/* Contact badge */
.contact-text { font-size: 12px; color: #596579; }
.contact-text i { color: #b0bac9; margin-right: 4px; font-size: 11px; }

/* Action buttons */
.btn-action-view  { background: #eff6ff; color: #3b82f6; border: none; border-radius: 6px; padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; }
.btn-action-edit  { background: #fff8e6; color: #d97706; border: none; border-radius: 6px; padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; }
.btn-action-del   { background: #fff5f5; color: #e53e3e; border: none; border-radius: 6px; padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; }
.btn-action-view:hover { background: #3b82f6; color: #fff; }
.btn-action-edit:hover  { background: #d97706; color: #fff; }
.btn-action-del:hover   { background: #e53e3e; color: #fff; }

/* Add button */
.btn-add-trader {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: #fff; border: none; border-radius: 8px;
    padding: 9px 18px; font-size: 13px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 6px;
    cursor: pointer; transition: all .2s; white-space: nowrap;
}
.btn-add-trader:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(59,130,246,.4); color: #fff; }

/* Empty state */
.empty-state-row td { padding: 48px 20px !important; text-align: center; background: #fff; }
.empty-state-inner .ei { font-size: 44px; color: #d7dce5; display: block; margin-bottom: 10px; }
.empty-state-inner .et { font-size: 15px; font-weight: 700; color: #8a94a6; margin-bottom: 4px; }
.empty-state-inner .es { font-size: 13px; color: #b0bac9; }

/* ── Select2 overrides ── */
.form-group-modern .select2-container { width: 100% !important; }
.form-group-modern .select2-container--default .select2-selection--single {
    min-height: 42px; height: 42px;
    border-color: #d7dce5; border-radius: 8px;
    display: flex; align-items: center;
}
.form-group-modern .select2-container--default .select2-selection--single .select2-selection__rendered {
    font-size: 13px; color: #303549; padding-left: 12px;
}
.form-group-modern .select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #8a94a6;
}
.form-group-modern .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
}
.form-group-modern .select2-container--default.select2-container--focus .select2-selection--single,
.form-group-modern .select2-container--default.select2-container--open .select2-selection--single {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}

/* ── Slide-in modal ── */
.trd-modal-backdrop {
    display: none;
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(26,35,64,.45);
    z-index: 1040;
    backdrop-filter: blur(2px);
    animation: fadeInBd .2s ease;
}
.trd-modal-backdrop.show { display: block; }
@keyframes fadeInBd { from { opacity: 0; } to { opacity: 1; } }

.trd-modal-panel {
    position: fixed; top: 0; right: 0; bottom: 0;
    width: 100%; max-width: 560px;
    background: #fff;
    z-index: 1050;
    display: flex; flex-direction: column;
    transform: translateX(100%);
    transition: transform .28s cubic-bezier(.4,0,.2,1);
    box-shadow: -8px 0 40px rgba(0,0,0,.16);
}
.trd-modal-panel.open { transform: translateX(0); }

.trd-panel-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    padding: 18px 20px;
    display: flex; align-items: center; justify-content: space-between;
    flex-shrink: 0;
}
.trd-panel-header h5 { color: #fff; font-size: 16px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; }
.trd-panel-header .panel-close {
    width: 36px; height: 36px; border-radius: 8px;
    background: rgba(255,255,255,.15); border: none;
    color: #fff; font-size: 18px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.trd-panel-header .panel-close:hover { background: #e53e3e; }

/* form fills remaining height */
#traderForm {
    display: flex; flex-direction: column;
    flex: 1; min-height: 0; overflow: hidden;
}

.trd-panel-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 24px 20px;
    -webkit-overflow-scrolling: touch;
}

.trd-section-title {
    font-size: 11px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .8px; color: #3b82f6; margin-bottom: 14px;
    display: flex; align-items: center; gap: 6px;
    padding-bottom: 8px; border-bottom: 1px solid #edf0f7;
}

.form-group-modern { margin-bottom: 16px; }
.form-group-modern label {
    display: block; font-size: 12px; font-weight: 700;
    color: #596579; margin-bottom: 6px;
}
.form-group-modern label .req { color: #e53e3e; }
.form-group-modern .form-control {
    border-color: #d7dce5; border-radius: 8px;
    font-size: 13px; color: #303549; min-height: 42px;
    transition: border-color .15s, box-shadow .15s;
}
.form-group-modern .form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}
.form-group-modern .form-control:disabled {
    background: #f8fafc; color: #596579; cursor: default;
}
.form-group-modern textarea.form-control { min-height: 80px; resize: vertical; }

.trd-panel-footer {
    padding: 14px 20px;
    border-top: 1px solid #edf0f7;
    background: #fafbff;
    display: flex; align-items: center; justify-content: flex-end; gap: 10px;
    flex-shrink: 0;
}
.btn-panel-cancel {
    background: #f0f2f7; color: #596579; border: none; border-radius: 8px;
    padding: 9px 20px; font-size: 13px; font-weight: 600; cursor: pointer;
    transition: all .15s;
}
.btn-panel-cancel:hover { background: #e2e8f0; color: #303549; }
.btn-panel-save {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: #fff; border: none; border-radius: 8px;
    padding: 9px 22px; font-size: 13px; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px; transition: all .2s;
}
.btn-panel-save:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(59,130,246,.4); }

/* row hidden for search */
.trader-row.hidden { display: none; }

/* error container */
#trdErrorContainer .alert { border-radius: 8px; font-size: 13px; margin-bottom: 16px; }

/* View mode badge in header */
.panel-mode-badge {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    padding: 3px 8px; border-radius: 20px; letter-spacing: .4px;
}
.panel-mode-badge.add-mode  { background: rgba(59,130,246,.25); color: #c5d0ff; }
.panel-mode-badge.edit-mode { background: rgba(255,193,7,.2); color: #ffd86e; }
.panel-mode-badge.view-mode { background: rgba(56,161,105,.2); color: #9be6b4; }
</style>

<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">

                {{-- Page Header --}}
                <div class="trd-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h4><i class="ti-package mr-2"></i>Traders</h4>
                            <span class="sub">Manage all registered traders in one place</span>
                        </div>
                        <div class="header-actions">
                            <button class="btn-add-trader" onclick="openTraderPanel('add')">
                                <i class="ti-plus"></i> Add Trader
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Stat Cards --}}
                <div class="row mb-4">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <div class="trd-stat stat-total">
                            <div class="sc-icon"><i class="ti-package"></i></div>
                            <div>
                                <div class="sc-label">Total Traders</div>
                                <div class="sc-value">{{ $traders->count() ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="trd-stat stat-active">
                            <div class="sc-icon"><i class="ti-check-box"></i></div>
                            <div>
                                <div class="sc-label">Active Traders</div>
                                <div class="sc-value">{{ $traders->where('is_active', true)->count() ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('partials.flash')

                {{-- Filter Bar --}}
                <div class="trd-filter-bar">
                    <div class="trd-search-wrap">
                        <i class="ti-search si"></i>
                        <input type="text" id="traderSearch" class="form-control"
                               placeholder="Search by name, phone, category...">
                    </div>
                    <select id="traderCatFilter" class="form-control select2" data-placeholder="All Categories" style="min-width:170px;max-width:200px;border-radius:8px;font-size:13px;border-color:#e2e8f0;min-height:40px;">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $cat)
                        <option value="{{ strtolower($cat['label']) }}">{{ $cat['label'] }}</option>
                        @endforeach
                        <option value="all categories">Global (All)</option>
                    </select>
                </div>

                {{-- Table Card --}}
                <div class="trd-table-card">
                    <div class="card-header-custom">
                        <div>
                            <h5>Traders List</h5>
                            <div class="sub">All registered traders are shown below</div>
                        </div>
                        <span class="badge badge-info" style="font-size:12px; padding:5px 10px; border-radius:20px;">
                            {{ $traders->count() }} records
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="trd-table" id="tradersTable">
                            <thead>
                                <tr>
                                    <th style="width:48px;">#</th>
                                    <th>Trader</th>
                                    <th>Category</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th style="width:140px; text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($traders ?? [] as $key => $trader)
                                @php
                                    $cat = $trader->category ? ($categories[$trader->category] ?? null) : null;
                                @endphp
                                <tr class="trader-row"
                                    data-search="{{ strtolower($trader->name . ' ' . $trader->phone . ' ' . ($cat ? $cat['label'] : '')) }}">
                                    <td style="color:#b0bac9; font-weight:600; font-size:12px;">{{ $key + 1 }}</td>
                                    <td>
                                        <div class="trd-name-cell">
                                            <div class="trd-avatar">{{ mb_substr($trader->name, 0, 1) }}</div>
                                            <span class="name">{{ $trader->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($cat)
                                            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $cat['bg'] }};color:{{ $cat['color'] }};">
                                                <i class="{{ $cat['icon'] }}"></i> {{ $cat['label'] }}
                                            </span>
                                        @elseif($trader->category)
                                            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#f4f6fb;color:#596579;">
                                                {{ $trader->category }}
                                            </span>
                                        @else
                                            <span style="font-size:12px;color:#b0bac9;font-style:italic;">All Categories</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($trader->phone)
                                            <div class="contact-text"><i class="ti-mobile"></i>{{ $trader->phone }}</div>
                                        @else
                                            <span style="color:#d7dce5;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($trader->address)
                                            <span style="font-size:12px; color:#596579;">{{ Str::limit($trader->address, 60) }}</span>
                                        @else
                                            <span style="color:#d7dce5;">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        <div style="display:flex; align-items:center; justify-content:center; gap:5px;">
                                            <button class="btn-action-view" onclick="openTraderPanel('view', {{ $trader->id }})" title="View">
                                                <i class="ti-eye"></i>
                                            </button>
                                            <button class="btn-action-edit" onclick="openTraderPanel('edit', {{ $trader->id }})" title="Edit">
                                                <i class="ti-pencil"></i>
                                            </button>
                                            <button class="btn-action-del" onclick="deleteTrader({{ $trader->id }}, '{{ addslashes($trader->name) }}')" title="Delete">
                                                <i class="ti-trash"></i>
                                            </button>
                                        </div>
                                        <form id="deleteFormTrader{{ $trader->id }}"
                                              action="{{ route('trader.destroy', $trader->id) }}"
                                              method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr class="empty-state-row">
                                    <td colspan="6">
                                        <div class="empty-state-inner">
                                            <i class="ti-package ei"></i>
                                            <div class="et">No traders yet</div>
                                            <div class="es">Click "Add Trader" to register your first trader</div>
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
<div class="trd-modal-backdrop" id="trdBackdrop" onclick="closeTraderPanel()"></div>

<div class="trd-modal-panel" id="trdPanel">
    <div class="trd-panel-header">
        <h5 id="trdPanelTitle"><i class="ti-package"></i> Add Trader</h5>
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="panel-mode-badge add-mode" id="trdModeBadge">New</span>
            <button class="panel-close" onclick="closeTraderPanel()">
                <i class="ti-close"></i>
            </button>
        </div>
    </div>

    <form id="traderForm" method="POST" action="{{ route('trader.store') }}">
        @csrf

        <div class="trd-panel-body">
            <div id="trdErrorContainer"></div>

            <div class="trd-section-title"><i class="ti-package"></i> Trader Information</div>
            <div class="form-group-modern">
                <label for="traderName">Trader Name <span class="req">*</span></label>
                <input type="text" id="traderName" name="name" class="form-control"
                       placeholder="Enter trader name" required>
            </div>

            <div class="form-group-modern">
                <label for="traderCategory">Expense Category</label>
                <select id="traderCategory" name="category" class="form-control select2-tr" data-placeholder="All Categories (Global)">
                    <option value="">— All Categories (Global) —</option>
                    @foreach($categories as $key => $cat)
                    <option value="{{ $key }}"
                        data-color="{{ $cat['color'] }}"
                        data-bg="{{ $cat['bg'] }}"
                        data-icon="{{ $cat['icon'] }}">
                        {{ $cat['label'] }}
                    </option>
                    @endforeach
                </select>
                <small style="color:#8a94a6;font-size:11px;margin-top:4px;display:block;">
                    <i class="ti-info-alt mr-1"></i>Leave blank to show this trader under every category.
                </small>
            </div>

            <div class="trd-section-title" style="margin-top:20px;"><i class="ti-headphone-alt"></i> Contact Details</div>
            <div class="form-group-modern">
                <label for="traderPhone">Phone Number</label>
                <input type="text" id="traderPhone" name="phone" class="form-control"
                       placeholder="e.g. 9876543210">
            </div>
            <div class="form-group-modern">
                <label for="traderAddress">Address</label>
                <textarea id="traderAddress" name="address" class="form-control"
                          placeholder="Enter address..." rows="3"></textarea>
            </div>
        </div>

        <div class="trd-panel-footer">
            <button type="button" class="btn-panel-cancel" onclick="closeTraderPanel()">
                <i class="ti-close"></i> Cancel
            </button>
            <button type="submit" class="btn-panel-save" id="trdSaveBtn">
                <i class="ti-save"></i> Save Trader
            </button>
        </div>
    </form>
</div>

<script>
// ── State ────────────────────────────────────────────────────────────────────
var trdMode       = 'add';   // 'add' | 'edit' | 'view'
var trdEditId     = null;    // current trader ID when editing

// ── Search & Filter ───────────────────────────────────────────────────────────
function applyTraderFilter() {
    var term    = document.getElementById('traderSearch').value.toLowerCase();
    var catFilter = document.getElementById('traderCatFilter').value.toLowerCase();
    document.querySelectorAll('.trader-row').forEach(function (row) {
        var searchText = row.dataset.search;
        var matchTerm  = !term    || searchText.indexOf(term) !== -1;
        var matchCat   = !catFilter || searchText.indexOf(catFilter) !== -1;
        row.classList.toggle('hidden', !(matchTerm && matchCat));
    });
}

document.getElementById('traderSearch').addEventListener('keyup', applyTraderFilter);
document.getElementById('traderCatFilter').addEventListener('change', applyTraderFilter);

// ── Open panel ───────────────────────────────────────────────────────────────
function openTraderPanel(mode, traderId) {
    trdMode   = mode;
    trdEditId = traderId || null;

    // Always start fresh
    trdResetForm();

    if (mode === 'add') {
        trdSetHeader('<i class="ti-package"></i> Add New Trader', 'New', 'add-mode');
        trdSetReadonly(false);
        trdShowSaveBtn(true);
        trdOpenDrawer();
        return;
    }

    // edit or view — fetch from server
    var url = (mode === 'edit') ? '/trader/edit/' + traderId : '/trader/view/' + traderId;

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            trdFillForm(data);

            if (mode === 'view') {
                trdSetHeader('<i class="ti-eye"></i> View Trader', 'View', 'view-mode');
                trdSetReadonly(true);
                trdShowSaveBtn(false);
            } else {
                // edit
                trdSetHeader('<i class="ti-pencil"></i> Edit Trader', 'Edit', 'edit-mode');
                trdSetReadonly(false);
                trdShowSaveBtn(true);
            }

            trdOpenDrawer();
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message)
                ? xhr.responseJSON.message
                : 'Could not load trader data. Please try again.';
            trdToast('error', msg);
        }
    });
}

function trdOpenDrawer() {
    document.getElementById('trdBackdrop').classList.add('show');
    document.getElementById('trdPanel').classList.add('open');
    document.body.style.overflow = 'hidden';
    // Init Select2 inside the panel
    var $cat = $('#traderCategory');
    if ($.fn.select2 && !$cat.data('select2')) {
        $cat.select2({ width: '100%', allowClear: true, dropdownParent: $('#trdPanel') });
    }
}

function closeTraderPanel() {
    // Destroy Select2 before hiding panel
    var $cat = $('#traderCategory');
    if ($cat.data('select2')) { $cat.select2('destroy'); }
    document.getElementById('trdBackdrop').classList.remove('show');
    document.getElementById('trdPanel').classList.remove('open');
    document.body.style.overflow = '';
    // Delayed reset so closing animation plays first
    setTimeout(trdResetForm, 300);
}

function trdResetForm() {
    document.getElementById('traderForm').reset();
    document.getElementById('trdErrorContainer').innerHTML = '';
    // Reset save button state
    var btn = document.getElementById('trdSaveBtn');
    btn.disabled = false;
    btn.innerHTML = '<i class="ti-save"></i> Save Trader';
}

function trdSetHeader(title, badgeText, badgeClass) {
    document.getElementById('trdPanelTitle').innerHTML = title;
    var b = document.getElementById('trdModeBadge');
    b.className  = 'panel-mode-badge ' + badgeClass;
    b.textContent = badgeText;
}

function trdSetReadonly(readonly) {
    var fields = document.querySelectorAll(
        '#traderForm input:not([type=hidden]), #traderForm textarea, #traderForm select'
    );
    fields.forEach(function (el) { el.disabled = readonly; });
}

function trdShowSaveBtn(show) {
    document.getElementById('trdSaveBtn').style.display = show ? '' : 'none';
}

function trdFillForm(data) {
    document.getElementById('traderName').value     = data.name     || '';
    document.getElementById('traderPhone').value    = data.phone    || '';
    document.getElementById('traderAddress').value  = data.address  || '';
    document.getElementById('traderCategory').value = data.category || '';
}

// ── Form submit (AJAX) ────────────────────────────────────────────────────────
document.getElementById('traderForm').addEventListener('submit', function (e) {
    e.preventDefault();

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var formData = {
        _token:   csrfToken,
        name:     document.getElementById('traderName').value.trim(),
        phone:    document.getElementById('traderPhone').value.trim(),
        address:  document.getElementById('traderAddress').value.trim(),
        category: document.getElementById('traderCategory').value || '',
    };

    var url, httpMethod;

    if (trdMode === 'edit' && trdEditId) {
        url        = '/trader/' + trdEditId;
        httpMethod = 'POST';
        formData._method = 'PUT';   // Laravel method spoofing
    } else {
        url        = '{{ route("trader.store") }}';
        httpMethod = 'POST';
    }

    var btn = document.getElementById('trdSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="ti-reload" style="animation:spin .8s linear infinite;display:inline-block;"></i> Saving...';

    $.ajax({
        url:  url,
        type: httpMethod,
        data: formData,
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        success: function (res) {
            closeTraderPanel();
            trdToast('success', res.message || 'Trader saved successfully');
            setTimeout(function () { location.reload(); }, 900);
        },
        error: function (xhr) {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti-save"></i> Save Trader';
            trdShowErrors(xhr);
        }
    });
});

function trdShowErrors(xhr) {
    var html = '<div class="alert alert-danger"><ul class="mb-0">';
    if (xhr.responseJSON && xhr.responseJSON.errors) {
        $.each(xhr.responseJSON.errors, function (field, msgs) {
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
    document.getElementById('trdErrorContainer').innerHTML = html;
    // Scroll error into view
    document.getElementById('trdErrorContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── Delete ────────────────────────────────────────────────────────────────────
function deleteTrader(id, name) {
    showDeleteModal('deleteFormTrader' + id, name, 'Trader');
}

// ── Toast ─────────────────────────────────────────────────────────────────────
function trdToast(type, msg) {
    if (typeof toastr !== 'undefined') { toastr[type](msg); }
    else { alert(msg); }
}

// ── ESC closes panel ──────────────────────────────────────────────────────────
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeTraderPanel(); }
});

// ── Spin animation for loading state ─────────────────────────────────────────
var styleEl = document.createElement('style');
styleEl.textContent = '@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
document.head.appendChild(styleEl);
</script>

@endsection
