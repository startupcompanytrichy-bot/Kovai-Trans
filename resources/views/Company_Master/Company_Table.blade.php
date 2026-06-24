@extends('layouts.app')

@section('content')

@php
$totalCompanies = $companies->count();
$activeCompanies = $companies->where('status', true)->count();
$inactiveCompanies = $companies->where('status', false)->count();
$withBank = $companies->filter(fn($c) => !empty($c->bank_name))->count();
@endphp

<style>
/* ── Company Index Page ────────────────────────────────────────────── */
.co-page { background: #f4f6fb; }

/* ── Page Header ───────────────────────────────────────────────────── */
.co-header {
    background: linear-gradient(135deg, #1a2340 0%, #2d3a5e 60%, #667eea 100%);
    border-radius: 12px; padding: 14px 22px; color: #fff;
    margin-bottom: 16px; position: relative; overflow: hidden;
}
.co-header::before { content:''; position:absolute; top:-40px; right:-40px; width:140px; height:140px; background:rgba(255,255,255,.05); border-radius:50%; }
.co-header::after  { content:''; position:absolute; bottom:-30px; right:80px; width:90px; height:90px; background:rgba(102,126,234,.1); border-radius:50%; }
.co-header h4 { font-size:16px; font-weight:800; margin:0 0 2px; position:relative;z-index:1; }
.co-header .sub { font-size:12px; opacity:.75; position:relative;z-index:1; }

/* ── Stats grid ────────────────────────────────────────────────────── */
.co-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 14px;
}
.co-stat {
    background: #fff; border-radius: 10px; padding: 12px 14px;
    box-shadow: 0 1px 6px rgba(0,0,0,.06);
    display: flex; align-items: center; gap: 12px;
    border-left: 3px solid transparent;
    transition: transform .15s, box-shadow .15s;
}
.co-stat:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(0,0,0,.1); }
.co-stat .cs-icon {
    width: 36px; height: 36px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
}
.co-stat .cs-label { font-size: 10px; font-weight: 700; color: #8a94a6; text-transform: uppercase; letter-spacing: .4px; }
.co-stat .cs-value { font-size: 22px; font-weight: 800; color: #1a2340; line-height: 1.1; }

/* ── Filter bar ────────────────────────────────────────────────────── */
.co-filter-bar {
    background: #fff; border-radius: 10px; padding: 12px 16px;
    box-shadow: 0 1px 6px rgba(0,0,0,.06); margin-bottom: 12px;
    display: flex; align-items: center; gap: 10px;
}
.co-filter-bar .form-control { height: 40px; font-size: 13px; border-color: #e2e8f0; border-radius: 8px; }
.co-filter-bar .select2-container {
    flex-shrink: 0;
    align-self: center;
}
.co-filter-bar .select2-container--default .select2-selection--single {
    height: 40px;
    display: flex; align-items: center;
    border-color: #e2e8f0; border-radius: 8px;
    padding: 0;
}
.co-filter-bar .select2-container--default .select2-selection--single .select2-selection__rendered {
    font-size: 13px; line-height: 40px; padding-left: 12px; padding-right: 30px;
}
.co-filter-bar .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px; width: 28px;
}
.co-filter-bar .select2-container--default.select2-container--focus .select2-selection--single,
.co-filter-bar .select2-container--default.select2-container--open .select2-selection--single {
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102,126,234,.12);
}
.co-filter-bar .btn {
    height: 40px; display: inline-flex; align-items: center; white-space: nowrap;
}
.co-search-wrap { flex: 1; min-width: 200px; position: relative; height: 40px; }
.co-search-wrap .ti-search { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #b0bac9; font-size: 13px; pointer-events: none; z-index: 1; }
.co-search-wrap input { padding-left: 32px; height: 40px !important; }

/* ── Status quick filter pills ─────────────────────────────────────── */
.co-status-filters { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 12px; }
.co-sf-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;
    cursor: pointer; border: 2px solid transparent; transition: all .15s;
    background: #f4f6fb; color: #8a94a6;
}
.co-sf-pill .sf-dot { width: 6px; height: 6px; border-radius: 50%; }
.co-sf-pill.active { border-color: currentColor; }

/* ── Table card ────────────────────────────────────────────────────── */
.co-table-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(0,0,0,.06); overflow: hidden; }
.co-table-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 18px; border-bottom: 1px solid #f0f2f7; background: #fafbff;
    flex-wrap: wrap; gap: 8px;
}
.co-table-header h6 { margin: 0; font-size: 13px; font-weight: 700; color: #1a2340; }
.co-table-wrap { overflow-x: auto; }

#companiesTable { min-width: 860px; margin-bottom: 0; }
#companiesTable th,
#companiesTable td { height: 48px; padding: 8px 12px; vertical-align: middle; border-color: #f0f2f7; font-size: 13px; }
#companiesTable th {
    background: #f8fafc; color: #14213d; font-weight: 800;
    font-size: 11px; text-transform: uppercase; letter-spacing: .4px;
    white-space: nowrap; position: sticky; top: 0; z-index: 2;
}
#companiesTable .co-row { cursor: pointer; }
#companiesTable .co-row:hover td { background: #f4f7ff; }

/* ── Badges ────────────────────────────────────────────────────────── */
.co-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 9px; border-radius: 20px;
    font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px;
}

/* ── Action buttons ────────────────────────────────────────────────── */
.co-action-btns { display: inline-flex; gap: 4px; align-items: center; }
.co-icon-btn {
    width: 30px; height: 30px; border-radius: 7px; border: none;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 13px; cursor: pointer; transition: all .15s; text-decoration: none;
}
.co-icon-btn.view { background: #eef2ff; color: #667eea; }
.co-icon-btn.view:hover { background: #667eea; color: #fff; }
.co-icon-btn.edit { background: #fff8e1; color: #d97706; }
.co-icon-btn.edit:hover { background: #d97706; color: #fff; }
.co-icon-btn.del  { background: #fff5f5; color: #e53e3e; }
.co-icon-btn.del:hover  { background: #e53e3e; color: #fff; }

/* ── Company avatar ────────────────────────────────────────────────── */
.co-avatar {
    width: 34px; height: 34px; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 800; color: #fff; flex-shrink: 0;
}

/* ── Bank indicator dot ─────────────────────────────────────────────── */
.bank-dot {
    width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 4px;
}

@media (max-width: 1199.98px) { .co-stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 575.98px)  { .co-stats-grid { grid-template-columns: repeat(2, 1fr); } }
</style>

<div class="pcoded-inner-content co-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

{{-- ── PAGE HEADER ────────────────────────────────────────────── --}}
<div class="co-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;letter-spacing:.5px;margin-bottom:6px;">
                <i class="ti-building"></i> Company Master
            </div>
            <h4>Company Management</h4>
            <div class="sub">Manage registered companies, bank details and business profiles.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('company.add') }}"
               class="btn btn-sm"
               style="background:#fff;color:#667eea;font-weight:700;border-radius:8px;padding:7px 18px;box-shadow:0 2px 10px rgba(0,0,0,.15);">
                <i class="ti-plus mr-1"></i> New Company
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

{{-- ── STAT CARDS ──────────────────────────────────────────────── --}}
<div class="co-stats-grid">
    <div class="co-stat" style="border-left-color:#667eea;">
        <div class="cs-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-building"></i></div>
        <div>
            <div class="cs-label">Total Companies</div>
            <div class="cs-value" style="color:#667eea;">{{ $totalCompanies }}</div>
        </div>
    </div>
    <div class="co-stat" style="border-left-color:#48bb78;">
        <div class="cs-icon" style="background:#f0fff4;color:#48bb78;"><i class="ti-check-box"></i></div>
        <div>
            <div class="cs-label">Active</div>
            <div class="cs-value" style="color:#48bb78;">{{ $activeCompanies }}</div>
        </div>
    </div>
    <div class="co-stat" style="border-left-color:#fc8181;">
        <div class="cs-icon" style="background:#fff5f5;color:#fc8181;"><i class="ti-close"></i></div>
        <div>
            <div class="cs-label">Inactive</div>
            <div class="cs-value" style="color:#fc8181;">{{ $inactiveCompanies }}</div>
        </div>
    </div>
    <div class="co-stat" style="border-left-color:#38a169;">
        <div class="cs-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-credit-card"></i></div>
        <div>
            <div class="cs-label">With Bank Details</div>
            <div class="cs-value" style="color:#38a169;">{{ $withBank }}</div>
        </div>
    </div>
</div>

{{-- ── FILTER BAR ──────────────────────────────────────────────── --}}
<div class="co-filter-bar">
    <div class="co-search-wrap">
        <i class="ti-search"></i>
        <input type="text" id="coSearch" class="form-control"
               placeholder="Search company name, code, GST, PAN, email...">
    </div>
    <select id="filterStatus" class="form-control select2" data-placeholder="All Status" style="min-width:120px;max-width:150px;">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select>
    <select id="filterBusiness" class="form-control select2" data-placeholder="All Types" style="min-width:140px;max-width:180px;">
        <option value="">All Types</option>
        @foreach(['Transport','Logistics','Fleet Owner','Parcel Service','Courier Service','Truck Booking','Cargo Service','Warehouse','Import & Export','Others'] as $bt)
        <option value="{{ strtolower($bt) }}">{{ $bt }}</option>
        @endforeach
    </select>
    <button type="button" id="clearFilters"
            class="btn btn-outline-secondary btn-sm"
            style="white-space:nowrap;border-radius:8px;">
        <i class="ti-close mr-1"></i> Clear
    </button>
</div>

{{-- ── QUICK FILTER PILLS ──────────────────────────────────────── --}}
<div class="co-status-filters">
    <span class="co-sf-pill active" data-filter="all"
          style="color:#667eea;border-color:#667eea;background:#eef2ff;">
        <span class="sf-dot" style="background:#667eea;"></span>
        All ({{ $totalCompanies }})
    </span>
    <span class="co-sf-pill" data-filter="active" style="color:#48bb78;">
        <span class="sf-dot" style="background:#48bb78;"></span>
        Active ({{ $activeCompanies }})
    </span>
    <span class="co-sf-pill" data-filter="inactive" style="color:#fc8181;">
        <span class="sf-dot" style="background:#fc8181;"></span>
        Inactive ({{ $inactiveCompanies }})
    </span>
    <span class="co-sf-pill" data-filter="bank" style="color:#38a169;">
        <span class="sf-dot" style="background:#38a169;"></span>
        With Bank ({{ $withBank }})
    </span>
</div>

{{-- ── COMPANIES TABLE ─────────────────────────────────────────── --}}
<div class="co-table-card">
    <div class="co-table-header">
        <h6>
            <i class="ti-list mr-1" style="color:#667eea;"></i>
            All Companies
            <span id="coCountBadge"
                  style="background:#eef2ff;color:#667eea;font-size:11px;font-weight:700;padding:2px 9px;border-radius:12px;margin-left:6px;">
                {{ $totalCompanies }}
            </span>
        </h6>
        <a href="{{ route('company.add') }}" class="btn btn-primary btn-sm" style="border-radius:8px;">
            <i class="ti-plus mr-1"></i> Add Company
        </a>
    </div>

    <div class="co-table-wrap">
        <table class="table table-hover" id="companiesTable">
            <thead>
                <tr>
                    <th style="width:36px;text-align:center;">#</th>
                    <th style="width:220px;">Company</th>
                    <th style="width:120px;">Code</th>
                    <th style="width:130px;">Business Type</th>
                    <th style="width:140px;">GST / PAN</th>
                    <th style="width:180px;">Email</th>
                    <th style="width:120px;">Phone</th>
                    <th style="width:140px;">Bank</th>
                    <th style="width:80px;text-align:center;">Status</th>
                    <th style="width:90px;text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                @php
                    $initial  = strtoupper(substr($company->company_name, 0, 1));
                    $colors   = ['#667eea','#48bb78','#f6ad55','#fc8181','#7c3aed','#0369a1','#d97706','#38a169'];
                    $avatarBg = $colors[crc32($company->company_code) % count($colors)];
                    $hasBank  = !empty($company->bank_name);
                    $isActive = $company->status;
                @endphp
                <tr class="co-row"
                    data-view-url="{{ route('company.view', $company->id) }}"
                    data-status="{{ $isActive ? 'active' : 'inactive' }}"
                    data-bank="{{ $hasBank ? 'yes' : 'no' }}"
                    data-business="{{ strtolower($company->business_types_display) }}"
                    data-search="{{ strtolower($company->company_name . ' ' . $company->company_code . ' ' . $company->gst . ' ' . $company->pan . ' ' . $company->email . ' ' . $company->business_types_display) }}">

                    <td style="text-align:center;color:#b0bac9;font-size:12px;">{{ $loop->iteration }}</td>

                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            {{-- Avatar / Logo --}}
                            @if($company->logo)
                                <img src="{{ asset('storage/' . $company->logo) }}" alt=""
                                     style="width:34px;height:34px;border-radius:8px;object-fit:cover;flex-shrink:0;">
                            @else
                                <div class="co-avatar" style="background:{{ $avatarBg }};">{{ $initial }}</div>
                            @endif
                            <div style="min-width:0;">
                                <div style="font-size:13px;font-weight:700;color:#1a2340;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;"
                                     title="{{ $company->company_name }}">
                                    {{ $company->company_name }}
                                </div>
                                @if($company->place_of_supply)
                                <div style="font-size:10px;color:#8a94a6;">{{ $company->place_of_supply }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td>
                        <span style="background:#eef2ff;color:#667eea;padding:3px 9px;border-radius:6px;font-size:11px;font-weight:700;letter-spacing:.4px;">
                            {{ $company->company_code }}
                        </span>
                    </td>

                    <td style="font-size:12px;color:#596579;font-weight:600;">
                        {{ $company->business_types_display }}
                    </td>

                    <td>
                        <div style="font-size:12px;font-weight:700;color:#1a2340;">{{ $company->gst ?: '—' }}</div>
                        <div style="font-size:10px;color:#8a94a6;margin-top:1px;">{{ $company->pan ?: '' }}</div>
                    </td>

                    <td style="font-size:12px;color:#596579;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                        title="{{ $company->email }}">
                        {{ $company->email }}
                    </td>

                    <td style="font-size:12px;color:#596579;">
                        {{ $company->phone ?: '—' }}
                        @if($company->phone2)
                        <div style="font-size:10px;color:#b0bac9;">{{ $company->phone2 }}</div>
                        @endif
                    </td>

                    <td>
                        @if($hasBank)
                            <div style="display:flex;align-items:center;gap:0;">
                                <span class="bank-dot" style="background:#48bb78;"></span>
                                <span style="font-size:11px;font-weight:600;color:#1a2340;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100px;" title="{{ $company->bank_name }}">
                                    {{ $company->bank_name }}
                                </span>
                            </div>
                            @if($company->ifsc_code)
                            <div style="font-size:10px;color:#8a94a6;font-family:monospace;">{{ $company->ifsc_code }}</div>
                            @endif
                        @else
                            <span style="font-size:11px;color:#b0bac9;"><span class="bank-dot" style="background:#e2e8f0;"></span>Not set</span>
                        @endif
                    </td>

                    <td style="text-align:center;">
                        <span class="co-badge"
                              style="background:{{ $isActive ? '#f0fff4' : '#fff5f5' }};color:{{ $isActive ? '#38a169' : '#e53e3e' }};">
                            <i class="ti-{{ $isActive ? 'check' : 'close' }}" style="font-size:8px;"></i>
                            {{ $isActive ? 'Active' : 'Inactive' }}
                        </span>
                    </td>

                    <td style="text-align:center;" onclick="event.stopPropagation();">
                        <div class="co-action-btns">
                            <a href="{{ route('company.view', $company->id) }}"
                               class="co-icon-btn view" title="View">
                                <i class="ti-eye"></i>
                            </a>
                            <a href="{{ route('company.edit', $company->id) }}"
                               class="co-icon-btn edit" title="Edit">
                                <i class="ti-pencil"></i>
                            </a>
                            <button type="button" class="co-icon-btn del" title="Delete"
                                    onclick="showDeleteModal('deleteFormCompany{{ $company->id }}','{{ addslashes($company->company_name) }}','Company')">
                                <i class="ti-trash"></i>
                            </button>
                        </div>
                        <form id="deleteFormCompany{{ $company->id }}"
                              action="{{ route('company.destroy', $company->id) }}"
                              method="POST" style="display:none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5" style="color:#b0bac9;">
                        <i class="ti-building" style="font-size:38px;display:block;margin-bottom:10px;opacity:.35;"></i>
                        <div style="font-size:14px;font-weight:600;margin-bottom:4px;">No companies found</div>
                        <div style="font-size:12px;margin-bottom:14px;">Start by adding your first company.</div>
                        <a href="{{ route('company.add') }}" class="btn btn-primary btn-sm">
                            <i class="ti-plus mr-1"></i> Add Company
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div></div></div></div>

@push('scripts')
<script>
$(document).ready(function () {

    /* ── Row click → view ─────────────────────────────────────────── */
    $('#companiesTable').on('click', '.co-row', function () {
        window.location.href = $(this).data('view-url');
    });

    /* ── Filter logic ─────────────────────────────────────────────── */
    function applyFilters() {
        var term     = $('#coSearch').val().toLowerCase();
        var status   = $('#filterStatus').val();   // 'active' | 'inactive' | ''
        var business = $('#filterBusiness').val(); // lowercase type or ''
        var quick    = $('.co-sf-pill.active').data('filter') || 'all';
        var visible  = 0;

        $('.co-row').each(function () {
            var $row = $(this);
            var ok   = true;

            if (term && !($row.attr('data-search') || '').includes(term)) ok = false;

            if (status) {
                if ($row.attr('data-status') !== status) ok = false;
            }

            if (business) {
                if (!($row.attr('data-business') || '').includes(business)) ok = false;
            }

            // Quick pill overrides status dropdown
            if (quick === 'active'   && $row.attr('data-status') !== 'active')   ok = false;
            if (quick === 'inactive' && $row.attr('data-status') !== 'inactive') ok = false;
            if (quick === 'bank'     && $row.attr('data-bank')   !== 'yes')      ok = false;

            $row.toggle(ok);
            if (ok) visible++;
        });

        $('#coCountBadge').text(visible);
    }

    $('#coSearch, #filterStatus, #filterBusiness').on('input change', applyFilters);

    $('#clearFilters').on('click', function () {
        $('#coSearch').val('');
        $('#filterStatus, #filterBusiness').val('').trigger('change');
        $('.co-sf-pill').removeClass('active')
            .css({ 'border-color': 'transparent', 'background': '#f4f6fb' });
        $('.co-sf-pill[data-filter="all"]')
            .addClass('active')
            .css({ 'border-color': '#667eea', 'background': '#eef2ff' });
        applyFilters();
    });

    /* ── Quick pill colors map ────────────────────────────────────── */
    var pillColors = { all: '#667eea', active: '#48bb78', inactive: '#fc8181', bank: '#38a169' };
    var pillBgs    = { all: '#eef2ff', active: '#f0fff4', inactive: '#fff5f5', bank: '#f0fff4' };

    $('.co-sf-pill').on('click', function () {
        var filter = $(this).data('filter');
        $('.co-sf-pill').removeClass('active')
            .css({ 'border-color': 'transparent', 'background': '#f4f6fb' });
        $(this).addClass('active')
            .css({ 'border-color': pillColors[filter] || '#667eea', 'background': pillBgs[filter] || '#eef2ff' });

        // Sync status dropdown only for active/inactive
        if (filter === 'active' || filter === 'inactive') {
            $('#filterStatus').val(filter).trigger('change');
        } else {
            $('#filterStatus').val('').trigger('change');
        }

        applyFilters();
    });

});
</script>
@endpush
@endsection
