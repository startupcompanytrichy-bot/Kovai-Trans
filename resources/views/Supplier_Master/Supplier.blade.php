@extends('layouts.app')

@section('content')

<style>
/* ── Page gradient header ── */
.sup-header {
    background: linear-gradient(135deg, #303549 0%, #4a5080 100%);
    border-radius: 14px;
    padding: 22px 28px;
    color: #fff;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
}
.sup-header::before {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,.06);
    border-radius: 50%;
}
.sup-header::after {
    content: '';
    position: absolute;
    bottom: -30px; left: 60px;
    width: 100px; height: 100px;
    background: rgba(102,126,234,.15);
    border-radius: 50%;
}
.sup-header h4 { font-size: 20px; font-weight: 800; margin: 0 0 3px; position: relative; z-index: 1; }
.sup-header .sub { font-size: 13px; opacity: .75; position: relative; z-index: 1; }
.sup-header .header-actions { position: relative; z-index: 1; }

/* ── Stat cards ── */
.sup-stat {
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
.sup-stat:hover { transform: translateY(-2px); box-shadow: 0 6px 22px rgba(0,0,0,.11); }
.sup-stat .sc-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.sup-stat .sc-label { font-size: 11px; font-weight: 700; color: #8a94a6; text-transform: uppercase; letter-spacing: .5px; }
.sup-stat .sc-value { font-size: 26px; font-weight: 800; color: #1a2340; line-height: 1; margin-top: 2px; }
.sup-stat.stat-total { border-left-color: #667eea; }
.sup-stat.stat-total .sc-icon { background: #eef2ff; color: #667eea; }
.sup-stat.stat-active { border-left-color: #38a169; }
.sup-stat.stat-active .sc-icon { background: #f0fff4; color: #38a169; }

/* ── Filter bar ── */
.sup-filter-bar {
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
.sup-search-wrap {
    flex: 1; min-width: 200px; position: relative;
}
.sup-search-wrap .si { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #b0bac9; font-size: 14px; pointer-events: none; }
.sup-search-wrap input { padding-left: 36px; border-color: #e2e8f0; border-radius: 8px; min-height: 40px; font-size: 13px; }
.sup-search-wrap input:focus { border-color: #667eea; box-shadow: 0 0 0 2px rgba(102,126,234,.12); }

/* ── Table card ── */
.sup-table-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    overflow: hidden;
}
.sup-table-card .card-header-custom {
    padding: 16px 20px;
    border-bottom: 1px solid #f0f2f7;
    background: #fafbff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.sup-table-card .card-header-custom h5 { margin: 0; font-size: 15px; font-weight: 700; color: #1a2340; }
.sup-table-card .card-header-custom .sub { font-size: 12px; color: #8a94a6; margin-top: 2px; }

.sup-table-card .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
}

.sup-table { width: 100%; border-collapse: collapse; min-width: 620px; }
.sup-table thead th {
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
.sup-table tbody td {
    padding: 12px 14px;
    font-size: 13px;
    color: #303549;
    border-bottom: 1px solid #f3f5f9;
    vertical-align: middle;
}
.sup-table tbody tr:last-child td { border-bottom: none; }
.sup-table tbody tr:hover td { background: #f5f7ff; transition: background .12s; }

/* Name cell with avatar */
.sup-name-cell { display: flex; align-items: center; gap: 10px; }
.sup-avatar {
    width: 34px; height: 34px; border-radius: 8px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff; font-weight: 700; font-size: 13px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; text-transform: uppercase;
}
.sup-name-cell .name { font-weight: 600; color: #1a2340; font-size: 13px; }

/* Contact badge */
.contact-text { font-size: 12px; color: #596579; }
.contact-text i { color: #b0bac9; margin-right: 4px; font-size: 11px; }

/* Action buttons */
.btn-action-view  { background: #eef2ff; color: #667eea; border: none; border-radius: 6px; padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; }
.btn-action-edit  { background: #fff8e6; color: #d97706; border: none; border-radius: 6px; padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; }
.btn-action-del   { background: #fff5f5; color: #e53e3e; border: none; border-radius: 6px; padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; }
.btn-action-view:hover { background: #667eea; color: #fff; }
.btn-action-edit:hover  { background: #d97706; color: #fff; }
.btn-action-del:hover   { background: #e53e3e; color: #fff; }

/* Add button */
.btn-add-supplier {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff; border: none; border-radius: 8px;
    padding: 9px 18px; font-size: 13px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 6px;
    cursor: pointer; transition: all .2s; white-space: nowrap;
}
.btn-add-supplier:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(102,126,234,.4); color: #fff; }

/* Empty state */
.empty-state-row td { padding: 48px 20px !important; text-align: center; background: #fff; }
.empty-state-inner .ei { font-size: 44px; color: #d7dce5; display: block; margin-bottom: 10px; }
.empty-state-inner .et { font-size: 15px; font-weight: 700; color: #8a94a6; margin-bottom: 4px; }
.empty-state-inner .es { font-size: 13px; color: #b0bac9; }

/* ── Slide-in modal ── */
.sup-modal-backdrop {
    display: none;
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(26,35,64,.45);
    z-index: 1040;
    backdrop-filter: blur(2px);
    animation: fadeInBd .2s ease;
}
.sup-modal-backdrop.show { display: block; }
@keyframes fadeInBd { from { opacity: 0; } to { opacity: 1; } }

.sup-modal-panel {
    position: fixed; top: 0; right: 0; bottom: 0;
    width: 100%; max-width: 560px;
    background: #fff;
    z-index: 1050;
    display: flex; flex-direction: column;
    transform: translateX(100%);
    transition: transform .28s cubic-bezier(.4,0,.2,1);
    box-shadow: -8px 0 40px rgba(0,0,0,.16);
    overflow: hidden;
}
.sup-modal-panel.open { transform: translateX(0); }

.sup-panel-header {
    background: linear-gradient(135deg, #303549 0%, #4a5080 100%);
    padding: 18px 20px;
    display: flex; align-items: center; justify-content: space-between;
    flex-shrink: 0;
}
.sup-panel-header h5 { color: #fff; font-size: 16px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; }
.sup-panel-header .panel-close {
    width: 36px; height: 36px; border-radius: 8px;
    background: rgba(255,255,255,.15); border: none;
    color: #fff; font-size: 18px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.sup-panel-header .panel-close:hover { background: #e53e3e; }

/* form fills remaining height */
#supplierForm {
    display: flex; flex-direction: column;
    flex: 1; min-height: 0; overflow: hidden;
}

.sup-panel-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 24px 20px;
    -webkit-overflow-scrolling: touch;
}

.sup-section-title {
    font-size: 11px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .8px; color: #667eea; margin-bottom: 14px;
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
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102,126,234,.12);
}
.form-group-modern .form-control:disabled {
    background: #f8fafc; color: #596579; cursor: default;
}
.form-group-modern textarea.form-control { min-height: 80px; resize: vertical; }

.sup-panel-footer {
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff; border: none; border-radius: 8px;
    padding: 9px 22px; font-size: 13px; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px; transition: all .2s;
}
.btn-panel-save:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(102,126,234,.4); }

/* row hidden for search */
.supplier-row.hidden { display: none; }

/* error container */
#supErrorContainer .alert { border-radius: 8px; font-size: 13px; margin-bottom: 16px; }

/* View mode badge in header */
.panel-mode-badge {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    padding: 3px 8px; border-radius: 20px; letter-spacing: .4px;
}
.panel-mode-badge.add-mode  { background: rgba(102,126,234,.25); color: #c5d0ff; }
.panel-mode-badge.edit-mode { background: rgba(255,193,7,.2); color: #ffd86e; }
.panel-mode-badge.view-mode { background: rgba(56,161,105,.2); color: #9be6b4; }
</style>

<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">

                {{-- Page Header --}}
                <div class="sup-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h4><i class="ti-truck mr-2"></i>Suppliers</h4>
                            <span class="sub">Manage all registered suppliers in one place</span>
                        </div>
                        <div class="header-actions">
                            <button class="btn-add-supplier" onclick="openSupplierPanel('add')">
                                <i class="ti-plus"></i> Add Supplier
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Stat Cards --}}
                <div class="row mb-4">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <div class="sup-stat stat-total">
                            <div class="sc-icon"><i class="ti-truck"></i></div>
                            <div>
                                <div class="sc-label">Total Suppliers</div>
                                <div class="sc-value">{{ $suppliers->count() ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="sup-stat stat-active">
                            <div class="sc-icon"><i class="ti-check-box"></i></div>
                            <div>
                                <div class="sc-label">Active Suppliers</div>
                                <div class="sc-value">{{ $suppliers->where('is_active', true)->count() ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('partials.flash')

                {{-- Filter Bar --}}
                <div class="sup-filter-bar">
                    <div class="sup-search-wrap">
                        <i class="ti-search si"></i>
                        <input type="text" id="supplierSearch" class="form-control"
                               placeholder="Search by name, email, mobile...">
                    </div>
                </div>

                {{-- Table Card --}}
                <div class="sup-table-card">
                    <div class="card-header-custom">
                        <div>
                            <h5>Suppliers List</h5>
                            <div class="sub">All registered suppliers are shown below</div>
                        </div>
                        <span class="badge badge-info" style="font-size:12px; padding:5px 10px; border-radius:20px;">
                            {{ $suppliers->count() }} records
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="sup-table" id="suppliersTable">
                            <thead>
                                <tr>
                                    <th style="width:48px;">#</th>
                                    <th>Supplier</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th style="width:140px; text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suppliers ?? [] as $key => $supplier)
                                <tr class="supplier-row"
                                    data-search="{{ strtolower($supplier->name . ' ' . $supplier->email . ' ' . $supplier->mobile) }}">
                                    <td style="color:#b0bac9; font-weight:600; font-size:12px;">{{ $key + 1 }}</td>
                                    <td>
                                        <div class="sup-name-cell">
                                            <div class="sup-avatar">{{ mb_substr($supplier->name, 0, 1) }}</div>
                                            <span class="name">{{ $supplier->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contact-text"><i class="ti-mobile"></i>{{ $supplier->mobile }}</div>
                                    </td>
                                    <td>
                                        @if($supplier->email)
                                            <div class="contact-text"><i class="ti-email"></i>{{ $supplier->email }}</div>
                                        @else
                                            <span style="color:#d7dce5;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($supplier->address)
                                            <span style="font-size:12px; color:#596579;">{{ Str::limit($supplier->address, 40) }}</span>
                                        @else
                                            <span style="color:#d7dce5;">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        <div style="display:flex; align-items:center; justify-content:center; gap:5px;">
                                            <button class="btn-action-view" onclick="openSupplierPanel('view', {{ $supplier->id }})" title="View">
                                                <i class="ti-eye"></i>
                                            </button>
                                            <button class="btn-action-edit" onclick="openSupplierPanel('edit', {{ $supplier->id }})" title="Edit">
                                                <i class="ti-pencil"></i>
                                            </button>
                                            <button class="btn-action-del" onclick="deleteSupplier({{ $supplier->id }}, '{{ addslashes($supplier->name) }}')" title="Delete">
                                                <i class="ti-trash"></i>
                                            </button>
                                        </div>
                                        <form id="deleteFormSupplier{{ $supplier->id }}"
                                              action="{{ route('supplier.destroy', $supplier->id) }}"
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
                                            <i class="ti-truck ei"></i>
                                            <div class="et">No suppliers yet</div>
                                            <div class="es">Click "Add Supplier" to register your first supplier</div>
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
<div class="sup-modal-backdrop" id="supBackdrop" onclick="closeSupplierPanel()"></div>

<div class="sup-modal-panel" id="supPanel">
    <div class="sup-panel-header">
        <h5 id="supPanelTitle"><i class="ti-truck"></i> Add Supplier</h5>
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="panel-mode-badge add-mode" id="supModeBadge">New</span>
            <button class="panel-close" onclick="closeSupplierPanel()">
                <i class="ti-close"></i>
            </button>
        </div>
    </div>

    <form id="supplierForm" method="POST" action="{{ route('supplier.store') }}">
        @csrf

        <div class="sup-panel-body">
            <div id="supErrorContainer"></div>

            <div class="sup-section-title"><i class="ti-truck"></i> Supplier Information</div>
            <div class="form-group-modern">
                <label for="supplierName">Supplier Name <span class="req">*</span></label>
                <input type="text" id="supplierName" name="name" class="form-control"
                       placeholder="Enter supplier name" required>
            </div>

            <div class="sup-section-title" style="margin-top:20px;"><i class="ti-headphone-alt"></i> Contact Details</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-modern">
                        <label for="supplierMobile">Mobile Number <span class="req">*</span></label>
                        <input type="text" id="supplierMobile" name="mobile" class="form-control"
                               placeholder="e.g. 9876543210" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-modern">
                        <label for="supplierEmail">Email Address</label>
                        <input type="email" id="supplierEmail" name="email" class="form-control"
                               placeholder="name@example.com">
                    </div>
                </div>
            </div>
            <div class="form-group-modern">
                <label for="supplierAddress">Address</label>
                <textarea id="supplierAddress" name="address" class="form-control"
                          placeholder="Enter full address..." rows="3"></textarea>
            </div>
        </div>

        <div class="sup-panel-footer">
            <button type="button" class="btn-panel-cancel" onclick="closeSupplierPanel()">
                <i class="ti-close"></i> Cancel
            </button>
            <button type="submit" class="btn-panel-save" id="supSaveBtn">
                <i class="ti-save"></i> Save Supplier
            </button>
        </div>
    </form>
</div>

<script>
// ── State ────────────────────────────────────────────────────────────────────
var supMode       = 'add';   // 'add' | 'edit' | 'view'
var supEditId     = null;    // current supplier ID when editing

// ── Search ───────────────────────────────────────────────────────────────────
document.getElementById('supplierSearch').addEventListener('keyup', function () {
    var term = this.value.toLowerCase();
    document.querySelectorAll('.supplier-row').forEach(function (row) {
        row.classList.toggle('hidden', row.dataset.search.indexOf(term) === -1);
    });
});

// ── Open panel ───────────────────────────────────────────────────────────────
function openSupplierPanel(mode, supplierId) {
    supMode   = mode;
    supEditId = supplierId || null;

    // Always start fresh
    supResetForm();

    if (mode === 'add') {
        supSetHeader('<i class="ti-truck"></i> Add New Supplier', 'New', 'add-mode');
        supSetReadonly(false);
        supShowSaveBtn(true);
        supOpenDrawer();
        return;
    }

    // edit or view — fetch from server
    var url = (mode === 'edit') ? '/supplier/edit/' + supplierId : '/supplier/view/' + supplierId;

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            supFillForm(data);

            if (mode === 'view') {
                supSetHeader('<i class="ti-eye"></i> View Supplier', 'View', 'view-mode');
                supSetReadonly(true);
                supShowSaveBtn(false);
            } else {
                // edit
                supSetHeader('<i class="ti-pencil"></i> Edit Supplier', 'Edit', 'edit-mode');
                supSetReadonly(false);
                supShowSaveBtn(true);
            }

            supOpenDrawer();
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message)
                ? xhr.responseJSON.message
                : 'Could not load supplier data. Please try again.';
            supToast('error', msg);
        }
    });
}

function supOpenDrawer() {
    document.getElementById('supBackdrop').classList.add('show');
    document.getElementById('supPanel').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeSupplierPanel() {
    document.getElementById('supBackdrop').classList.remove('show');
    document.getElementById('supPanel').classList.remove('open');
    document.body.style.overflow = '';
    // Delayed reset so closing animation plays first
    setTimeout(supResetForm, 300);
}

function supResetForm() {
    document.getElementById('supplierForm').reset();
    document.getElementById('supErrorContainer').innerHTML = '';
    // Reset save button state
    var btn = document.getElementById('supSaveBtn');
    btn.disabled = false;
    btn.innerHTML = '<i class="ti-save"></i> Save Supplier';
}

function supSetHeader(title, badgeText, badgeClass) {
    document.getElementById('supPanelTitle').innerHTML = title;
    var b = document.getElementById('supModeBadge');
    b.className  = 'panel-mode-badge ' + badgeClass;
    b.textContent = badgeText;
}

function supSetReadonly(readonly) {
    var fields = document.querySelectorAll(
        '#supplierForm input:not([type=hidden]), #supplierForm textarea'
    );
    fields.forEach(function (el) { el.disabled = readonly; });
}

function supShowSaveBtn(show) {
    document.getElementById('supSaveBtn').style.display = show ? '' : 'none';
}

function supFillForm(data) {
    document.getElementById('supplierName').value    = data.name    || '';
    document.getElementById('supplierMobile').value  = data.mobile  || '';
    document.getElementById('supplierEmail').value   = data.email   || '';
    document.getElementById('supplierAddress').value = data.address || '';
}

// ── Form submit (AJAX) ────────────────────────────────────────────────────────
document.getElementById('supplierForm').addEventListener('submit', function (e) {
    e.preventDefault();

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var formData = {
        _token:  csrfToken,
        name:    document.getElementById('supplierName').value.trim(),
        mobile:  document.getElementById('supplierMobile').value.trim(),
        email:   document.getElementById('supplierEmail').value.trim(),
        address: document.getElementById('supplierAddress').value.trim(),
    };

    var url, httpMethod;

    if (supMode === 'edit' && supEditId) {
        url        = '/supplier/' + supEditId;
        httpMethod = 'POST';
        formData._method = 'PUT';   // Laravel method spoofing
    } else {
        url        = '{{ route("supplier.store") }}';
        httpMethod = 'POST';
    }

    var btn = document.getElementById('supSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="ti-reload" style="animation:spin .8s linear infinite;display:inline-block;"></i> Saving...';

    $.ajax({
        url:  url,
        type: httpMethod,
        data: formData,
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        success: function (res) {
            closeSupplierPanel();
            supToast('success', res.message || 'Supplier saved successfully');
            setTimeout(function () { location.reload(); }, 900);
        },
        error: function (xhr) {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti-save"></i> Save Supplier';
            supShowErrors(xhr);
        }
    });
});

function supShowErrors(xhr) {
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
    document.getElementById('supErrorContainer').innerHTML = html;
    // Scroll error into view
    document.getElementById('supErrorContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── Delete ────────────────────────────────────────────────────────────────────
function deleteSupplier(id, name) {
    showDeleteModal('deleteFormSupplier' + id, name, 'Supplier');
}

// ── Toast ─────────────────────────────────────────────────────────────────────
function supToast(type, msg) {
    if (typeof toastr !== 'undefined') { toastr[type](msg); }
    else { alert(msg); }
}

// ── ESC closes panel ──────────────────────────────────────────────────────────
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeSupplierPanel(); }
});

// ── Spin animation for loading state ─────────────────────────────────────────
var styleEl = document.createElement('style');
styleEl.textContent = '@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
document.head.appendChild(styleEl);
</script>

@endsection
