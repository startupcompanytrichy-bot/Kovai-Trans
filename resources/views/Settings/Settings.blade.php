@extends('layouts.app')

@push('styles')
<style>
/* ════════════════════════════════════════════════════════════════════
   SETTINGS PAGE — Premium Layout
════════════════════════════════════════════════════════════════════ */
.st-page { background: #f0f2f8; min-height:100vh; }

/* ── Table scroll wrapper ───────────────────────────────────────── */
.st-tbl-wrap {
    overflow-x:auto; -webkit-overflow-scrolling:touch;
}
.st-tbl-wrap table { margin-bottom:0; }
.st-tbl-wrap + .st-card-bd { border-top:1px solid #f1f5f9; }

/* ── Page header ─────────────────────────────────────────────────── */
.st-hdr {
    background: linear-gradient(135deg, #0c1322 0%, #1a2340 40%, #3b4f8a 70%, #667eea 100%);
    border-radius: 16px; padding: 22px 28px; color: #fff;
    margin-bottom: 22px; position: relative; overflow: hidden;
}
.st-hdr::before {
    content:''; position:absolute; top:-60px; right:-60px;
    width:240px; height:240px; background:rgba(255,255,255,.04); border-radius:50%;
}
.st-hdr::after {
    content:''; position:absolute; bottom:-40px; right:-20px;
    width:140px; height:140px; background:rgba(255,255,255,.03); border-radius:50%;
}
.st-hdr-tag {
    display:inline-flex;align-items:center;gap:6px;
    background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18);
    border-radius:20px;padding:4px 14px;font-size:10px;font-weight:700;
    letter-spacing:.5px;margin-bottom:8px;backdrop-filter:blur(4px);
}
.st-hdr h4 { font-size:20px; font-weight:800; margin:0 0 3px; position:relative; z-index:1; }
.st-hdr .sub { font-size:12px; opacity:.65; position:relative; z-index:1; }

/* ── Sidebar navigation ──────────────────────────────────────────── */
.st-nav-wrap {
    background:#fff; border-radius:16px;
    box-shadow:0 2px 12px rgba(15,23,42,.07);
    overflow:hidden; position:sticky; top:20px;
}
.st-nav-item {
    display:flex; align-items:center; gap:12px;
    padding:14px 20px; cursor:pointer;
    border-left:3px solid transparent;
    transition:all .2s; font-size:13px; font-weight:600; color:#64748b;
    position:relative;
}
.st-nav-item:not(:last-child)::after {
    content:''; position:absolute; bottom:0; left:20px; right:20px;
    height:1px; background:#f1f5f9;
}
.st-nav-item:hover { background:#f8fafc; color:#1e293b; }
.st-nav-item.active { background:#eef2ff; border-left-color:#6366f1; color:#4f46e5; }
.st-nav-item .ni-ico {
    width:34px; height:34px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:14px; flex-shrink:0;
    background:#f1f5f9; color:#94a3b8; transition:all .2s;
}
.st-nav-item.active .ni-ico {
    background:linear-gradient(135deg,#6366f1,#818cf8); color:#fff;
    box-shadow:0 4px 10px rgba(99,102,241,.3);
}
.st-nav-item .ni-badge {
    margin-left:auto;
    background:#f1f5f9; color:#94a3b8;
    font-size:10px; font-weight:700; padding:2px 10px; border-radius:20px;
}
.st-nav-item.active .ni-badge { background:#c7d2fe; color:#4f46e5; }

/* ── Tab panes ────────────────────────────────────────────────────── */
.st-tab-pane { display:none; }
.st-tab-pane.active { display:block; animation:stFade .25s ease; }
@keyframes stFade { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }

/* ── Section heading ──────────────────────────────────────────────── */
.st-sec {
    display:flex; align-items:center; gap:10px;
    font-size:10px; font-weight:800; text-transform:uppercase;
    letter-spacing:.8px; color:#94a3b8; margin:0 0 14px;
}
.st-sec::after { content:''; flex:1; height:1px; background:#e2e8f0; }

/* ── Card ─────────────────────────────────────────────────────────── */
.st-card {
    background:#fff; border-radius:16px;
    box-shadow:0 1px 10px rgba(15,23,42,.05); overflow:hidden;
    margin-bottom:20px; transition:box-shadow .2s;
}
.st-card:hover { box-shadow:0 4px 20px rgba(15,23,42,.08); }
.st-card-hd {
    display:flex; align-items:center; justify-content:space-between;
    padding:16px 22px; border-bottom:1px solid #f1f5f9; background:#fafbff;
    flex-wrap:wrap; gap:8px;
}
.st-card-hd h6 {
    margin:0; font-size:13px; font-weight:700; color:#0f172a;
    display:flex; align-items:center; gap:8px;
}
.st-card-hd .hd-ico {
    width:28px; height:28px; border-radius:8px;
    display:inline-flex;align-items:center;justify-content:center;
    font-size:13px;
}
.st-card-bd { padding:22px; }

/* ── Active FY banner ────────────────────────────────────────────── */
.st-fy-act {
    display:flex; align-items:center; gap:12px;
    padding:12px 16px; border-radius:12px;
    background:linear-gradient(135deg,#f0fff4,#dcfce7); border:1px solid #86efac;
    margin-bottom:16px;
}
.st-fy-act .fa-ico {
    width:36px; height:36px; border-radius:10px;
    background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-size:15px; flex-shrink:0; box-shadow:0 3px 8px rgba(34,197,94,.3);
}
.st-fy-act .fa-lbl { font-size:10px; font-weight:700; color:#166534; text-transform:uppercase; letter-spacing:.3px; }
.st-fy-act .fa-val { font-size:16px; font-weight:800; color:#14532d; line-height:1.1; }
.st-fy-act .fa-rng { font-size:11px; color:#22c55e; }

.st-fy-miss {
    display:flex; align-items:center; gap:12px;
    padding:12px 16px; border-radius:12px;
    background:linear-gradient(135deg,#fffbeb,#fef3c7); border:1px solid #fcd34d;
    margin-bottom:16px;
}

/* ── FY table ─────────────────────────────────────────────────────── */
#fyTbl { min-width:520px; margin-bottom:0; }
#fyTbl th, #fyTbl td { height:46px; padding:8px 14px; vertical-align:middle; border-color:#f1f5f9; font-size:13px; }
#fyTbl th { background:#f8fafc; color:#0f172a; font-weight:700; font-size:11px; text-transform:uppercase; letter-spacing:.4px; }
#fyTbl tr.fy-on td { background:#f0fdf4 !important; }
#fyTbl tr.fy-on td:first-child { border-left:3px solid #22c55e; }
#fyTbl tbody tr.fy-hid { display:none; }

.fy-tw {
    max-height:320px; overflow-y:auto; overflow-x:auto;
    border:1px solid #f1f5f9; border-radius:10px;
}
.fy-tw::-webkit-scrollbar { width:4px; height:4px; }
.fy-tw::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:4px; }

.fy-more {
    display:flex; align-items:center; justify-content:center; gap:6px;
    padding:10px; border:1px solid #f1f5f9; border-top:none;
    font-size:12px; font-weight:700; color:#6366f1;
    cursor:pointer; background:#fafbff; transition:all .15s;
    border-radius:0 0 10px 10px;
}
.fy-more:hover { background:#eef2ff; }

/* ── Next FY preview ──────────────────────────────────────────────── */
.fy-next {
    background:linear-gradient(135deg,#eef2ff,#e0e7ff);
    border:1.5px dashed #a5b4fc; border-radius:12px;
    padding:16px; margin-bottom:16px; text-align:center;
}
.fy-next .fn-lbl { font-size:10px; font-weight:700; color:#6366f1; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
.fy-next .fn-val { font-size:24px; font-weight:800; color:#4338ca; letter-spacing:.5px; margin-bottom:2px; }
.fy-next .fn-rng { font-size:12px; color:#6366f1; opacity:.7; }

/* ── Form elements ────────────────────────────────────────────────── */
.st-f { font-size:12px; font-weight:600; color:#475569; margin-bottom:5px; display:block; }
.st-f .req { color:#ef4444; }
.st-i {
    height:42px; font-size:13px; border:1.5px solid #e2e8f0;
    border-radius:10px; padding:0 14px; width:100%; color:#0f172a;
    background:#fff; transition:all .15s;
}
.st-i:focus { border-color:#6366f1; outline:none; box-shadow:0 0 0 3px rgba(99,102,241,.12); }
select.st-i { padding:0 12px; }
.st-i-sm { height:36px; font-size:12px; border-radius:8px; }

/* ── Info box ─────────────────────────────────────────────────────── */
.st-inf {
    padding:14px 16px; border-radius:10px;
    background:#f8fafc; border:1px solid #e2e8f0;
    font-size:12px; color:#64748b; line-height:1.8;
}
.st-inf .if-t { font-size:10px; font-weight:700; color:#6366f1; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
.st-inf ul { margin:0; padding-left:18px; }

/* ── Buttons ──────────────────────────────────────────────────────── */
.st-btn {
    display:inline-flex;align-items:center;gap:6px;
    height:38px; padding:0 18px; border-radius:10px;
    font-size:12px; font-weight:700; border:none; cursor:pointer;
    transition:all .15s; text-decoration:none;
}
.st-btn-primary { background:linear-gradient(135deg,#6366f1,#818cf8); color:#fff; box-shadow:0 3px 10px rgba(99,102,241,.25); }
.st-btn-primary:hover { transform:translateY(-1px); box-shadow:0 5px 16px rgba(99,102,241,.35); }
.st-btn-success { background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; box-shadow:0 3px 10px rgba(34,197,94,.25); }
.st-btn-success:hover { transform:translateY(-1px); box-shadow:0 5px 16px rgba(34,197,94,.35); }
.st-btn-ghost { background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; }
.st-btn-ghost:hover { background:#e2e8f0; color:#0f172a; }

/* ── Icon buttons ─────────────────────────────────────────────────── */
.st-ib {
    width:32px; height:32px; border-radius:8px; border:none;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:13px; cursor:pointer; transition:all .15s;
}
.st-ib.edit { background:#eef2ff; color:#6366f1; }
.st-ib.edit:hover { background:#6366f1; color:#fff; }
.st-ib.del { background:#fef2f2; color:#ef4444; }
.st-ib.del:hover { background:#ef4444; color:#fff; }

/* ── Badges/pills ─────────────────────────────────────────────────── */
.st-pill {
    display:inline-flex; align-items:center; gap:4px;
    padding:3px 10px; border-radius:20px;
    font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.3px;
}

/* ── All Settings table ───────────────────────────────────────────── */
.st-tbl { width:100%; border-collapse:collapse; }
.st-tbl thead { border-bottom:2px solid #eef2ff; }
.st-tbl th {
    font-size:10px; font-weight:700; text-transform:uppercase;
    letter-spacing:.06em; color:#94a3b8; background:#f8fafc;
    padding:12px 18px; border:none; white-space:nowrap;
}
.st-tbl td {
    font-size:12.5px; padding:14px 18px;
    vertical-align:middle; border-bottom:1px solid #f1f5f9; border-top:none;
}
.st-tbl tr:last-child td { border-bottom:none; }
.st-tbl tbody tr { transition:background .15s; }
.st-tbl tbody tr:hover td { background:#fafbff; }
.st-tbl tbody tr:has(.sef[style*="block"]) td { background:#f8faff; }

/* ── Primary button full width ────────────────────────────────────── */
.st-btn-block {
    display:block; width:100%; height:44px; border-radius:10px;
    font-size:14px; font-weight:700; border:none; cursor:pointer;
    background:linear-gradient(135deg,#6366f1,#818cf8); color:#fff;
    box-shadow:0 3px 12px rgba(99,102,241,.3); transition:all .15s;
}
.st-btn-block:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(99,102,241,.4); }

/* ── Smooth value edit transition ─────────────────────────────────── */
.sv, .sef { transition:opacity .15s; }

/* ── Card body compact variant ────────────────────────────────────── */
.st-card-bd-compact { padding:16px 22px; }

/* ── Responsive ───────────────────────────────────────────────────── */
@media (max-width:767px) {
    .st-hdr { padding:16px 18px; }
    .st-hdr h4 { font-size:17px; }
    .st-card-hd { padding:14px 16px; }
    .st-card-bd { padding:16px; }
}
</style>
@endpush

@section('content')

<div class="pcoded-inner-content st-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body" style="padding:22px;background:#f0f2f8;min-height:100vh;">

{{-- ══ PAGE HEADER ═══════════════════════════════════════════════════════ --}}
<div class="st-hdr">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <div class="st-hdr-tag"><i class="ti-settings"></i> Settings</div>
            <h4>Application Settings</h4>
            <div class="sub">Configure financial years, branches, GST rates, and application preferences.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('dashboard') }}" class="btn btn-sm" style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.2);font-weight:700;border-radius:10px;padding:7px 18px;font-size:12px;backdrop-filter:blur(4px);">
                <i class="ti-home mr-1"></i> Dashboard
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" style="border-radius:12px;font-size:13px;font-weight:600;margin-bottom:18px;border:none;" role="alert">
    <i class="ti-check-box mr-2"></i>{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" style="border-radius:12px;font-size:13px;font-weight:600;margin-bottom:18px;border:none;" role="alert">
    <i class="ti-alert mr-2"></i>{{ $errors->first() }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

{{-- ══ LAYOUT: SIDEBAR + CONTENT ═══════════════════════════════════════ --}}
<div class="row">

    {{-- ─── Sidebar Navigation ─────────────────────────────────────── --}}
    <div class="col-md-3 mb-3 mb-md-0">
        <div class="st-nav-wrap" id="settingsNav">
            <div class="st-nav-item active" data-tab="tab-all-settings">
                <div class="ni-ico"><i class="ti-server"></i></div>
                <span>All Settings</span>
                <span class="ni-badge">{{ $allSettings->count() }}</span>
            </div>
            @if(userCan('view_settings_financial_year'))
            <div class="st-nav-item" data-tab="tab-financial-year">
                <div class="ni-ico"><i class="ti-calendar"></i></div>
                Financial Year
                <span class="ni-badge">{{ $financialYears->count() }}</span>
            </div>
            @endif
            @if(userCan('view_settings_branch_default'))
            <div class="st-nav-item" data-tab="tab-branch">
                <div class="ni-ico"><i class="ti-layers"></i></div>
                Branch Settings
            </div>
            @endif
            @if(showAllMenu())
            <div class="st-nav-item" data-tab="tab-gst">
                <div class="ni-ico"><i class="ti-receipt"></i></div>
                GST Settings
                <span class="ni-badge">{{ $gstSettings->count() }}</span>
            </div>
            @endif
            @if(showAllMenu())
            <div class="st-nav-item" data-tab="tab-limits">
                <div class="ni-ico"><i class="ti-lock"></i></div>
                Account Limits
            </div>
            @endif
        </div>
    </div>

    {{-- ─── Tab Content ────────────────────────────────────────────── --}}
    <div class="col-md-9">

        {{-- ═══ Tab: All Settings ═════════════════════════════════════ --}}
        <div class="st-tab-pane active" id="tab-all-settings">
            <div class="st-sec"><i class="ti-server" style="color:#6366f1;font-size:13px;"></i> All Settings</div>
            <div class="st-card">
                <div class="st-card-hd">
                    <h6><span class="hd-ico" style="background:#eef2ff;color:#6366f1;"><i class="ti-server"></i></span> Manage Settings</h6>
                </div>
                <div class="st-tbl-wrap">
                    <table class="st-tbl">
                        <thead>
                            <tr>
                                <th>Setting</th>
                                <th>Key</th>
                                <th>Value</th>
                                <th style="text-align:center;width:80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allSettings as $setting)
                            <tr id="sr-{{ $setting->id }}">
                                <td>
                                    <strong style="font-size:13px;">{{ $setting->label ?? $setting->key }}</strong>
                                    <div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.3px;">
                                        {{ $setting->group }}
                                    </div>
                                </td>
                                <td>
                                    <code style="font-size:11px;background:#f1f5f9;padding:2px 8px;border-radius:5px;color:#64748b;">{{ $setting->key }}</code>
                                </td>
                                <td>
                                <span class="sv" id="sv-{{ $setting->id }}"
                                      style="font-size:13px;color:#334155;font-weight:500;display:inline-flex;align-items:center;gap:6px;">
                                    {{ $setting->value ?? '<empty>' }}
                                </span>
                                <form method="POST" action="{{ route('settings.update') }}"
                                      id="sef-{{ $setting->id }}" style="display:none;" class="sef">
                                    @csrf
                                    <input type="hidden" name="key" value="{{ $setting->key }}">
                                    <div style="display:flex;gap:6px;flex-wrap:nowrap;align-items:center;">
                                        <input type="text" name="value" class="form-control"
                                               value="{{ $setting->value }}"
                                               style="height:34px;font-size:12px;border-radius:8px;border:1.5px solid #6366f1;width:180px;padding:0 12px;box-shadow:none;">
                                        <button type="submit" class="st-btn st-btn-primary" style="height:32px;padding:0 14px;font-size:11px;border-radius:8px;">
                                            <i class="ti-check" style="font-size:10px;"></i> Save
                                        </button>
                                        <button type="button" class="st-btn st-btn-ghost" style="height:32px;padding:0 10px;font-size:11px;border-radius:8px;"
                                                onclick="cancelEdit({{ $setting->id }})">
                                            <i class="ti-close" style="font-size:10px;"></i>
                                        </button>
                                    </div>
                                </form>
                                </td>
                                <td style="text-align:center;">
                                    <button class="st-ib edit" onclick="toggleEdit({{ $setting->id }})" title="Edit">
                                        <i class="ti-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4" style="color:#cbd5e1;">
                                    <i class="ti-server" style="font-size:32px;display:block;margin-bottom:10px;opacity:.4;"></i>
                                    <div style="font-size:14px;font-weight:600;">No settings found</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>{{-- /#tab-all-settings --}}

                @if(userCan('view_settings_financial_year'))
        <div class="st-tab-pane" id="tab-financial-year">
            <div class="st-sec"><i class="ti-calendar" style="color:#6366f1;font-size:13px;"></i> Financial Year</div>
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div class="st-card">
                        <div class="st-card-hd">
                            <h6><span class="hd-ico" style="background:#eef2ff;color:#6366f1;"><i class="ti-calendar"></i></span> Financial Years</h6>
                        </div>
                        <div class="st-card-bd">
                            @if($currentFY)
                            <div class="st-fy-act">
                                <div class="fa-ico"><i class="ti-check"></i></div>
                                <div>
                                    <div class="fa-lbl">Active Financial Year</div>
                                    <div class="fa-val">FY {{ $currentFY->label }}</div>
                                    <div class="fa-rng">{{ $currentFY->start_date->format('d M Y') }} &mdash; {{ $currentFY->end_date->format('d M Y') }}</div>
                                </div>
                            </div>
                            @else
                            <div class="st-fy-miss">
                                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;box-shadow:0 3px 8px rgba(245,158,11,.3);">
                                    <i class="ti-alert"></i>
                                </div>
                                <div>
                                    <div style="font-size:12px;font-weight:700;color:#92400e;">No Active Financial Year</div>
                                    <div style="font-size:11px;color:#b45309;">Data from all years shown. Create and activate one below.</div>
                                </div>
                            </div>
                            @endif
                            <div class="fy-tw">
                                <table class="table table-hover" id="fyTbl">
                                    <thead>
                                        <tr>
                                            <th>Financial Year</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($financialYears as $idx => $fy)
                                        <tr class="{{ $fy->is_default ? 'fy-on' : '' }} {{ $idx >= 3 ? 'fy-hid' : '' }}">
                                            <td>
                                                <strong style="color:{{ $fy->is_default ? '#16a34a' : '#0f172a' }};">FY {{ $fy->label }}</strong>
                                                @if($fy->is_default)
                                                    <span class="st-pill ml-1" style="background:#dcfce7;color:#16a34a;"><i class="ti-check" style="font-size:8px;"></i> Active</span>
                                                @endif
                                            </td>
                                            <td style="color:#64748b;">{{ $fy->start_date->format('d M Y') }}</td>
                                            <td style="color:#64748b;">{{ $fy->end_date->format('d M Y') }}</td>
                                            <td class="text-center">
                                                @if($fy->is_default)
                                                    <span class="st-pill" style="background:#dcfce7;color:#16a34a;">Current</span>
                                                @else
                                                    <span class="st-pill" style="background:#f1f5f9;color:#94a3b8;">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div style="display:inline-flex;gap:4px;align-items:center;">
                                                    @if(!$fy->is_default)
                                                    <form method="POST" action="{{ route('settings.fy.setDefault', $fy->id) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="st-btn st-btn-primary" style="height:32px;padding:0 12px;font-size:11px;"
                                                                onclick="return confirm('Set FY {{ $fy->label }} as active? All modules will filter data for this year only.')">
                                                            <i class="ti-check mr-1"></i> Set Active
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('settings.fy.destroy', $fy->id) }}" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="st-ib del" onclick="return confirm('Delete FY {{ $fy->label }}? This cannot be undone.')">
                                                            <i class="ti-trash"></i>
                                                        </button>
                                                    </form>
                                                    @else
                                                    <span style="font-size:11px;color:#94a3b8;">Active year</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4" style="color:#cbd5e1;">
                                                <i class="ti-calendar" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4;"></i>
                                                <div style="font-size:14px;font-weight:600;margin-bottom:4px;">No financial years yet</div>
                                                <div style="font-size:11px;">Use the form on the right to add one.</div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($financialYears->count() > 3)
                            <div class="fy-more" id="fyShowMore" onclick="toggleFY(this)">
                                <i class="ti-angle-down" id="fyTI"></i>
                                <span id="fyTT">Show {{ $financialYears->count() - 3 }} more</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="st-card">
                        <div class="st-card-hd">
                            <h6><span class="hd-ico" style="background:#dcfce7;color:#16a34a;"><i class="ti-plus"></i></span> Add Financial Year</h6>
                        </div>
                        <div class="st-card-bd">
                            @php
                                $lastFY       = $financialYears->sortByDesc('start_date')->first();
                                $nextStartYear = $lastFY
                                    ? $lastFY->start_date->year + 1
                                    : (now()->month >= 4 ? now()->year : now()->year - 1);
                                $nextLabel     = \App\Models\FinancialYear::generateLabel($nextStartYear);
                            @endphp
                            <div class="fy-next">
                                <div class="fn-lbl">Next Financial Year to Create</div>
                                <div class="fn-val">FY {{ $nextLabel }}</div>
                                <div class="fn-rng">01 Apr {{ $nextStartYear }} &mdash; 31 Mar {{ $nextStartYear + 1 }}</div>
                            </div>
                            <form method="POST" action="{{ route('settings.fy.store') }}">
                                @csrf
                                <input type="hidden" name="start_year" value="{{ $nextStartYear }}">
                                @error('start_year')
                                    <div style="color:#ef4444;font-size:11px;margin-bottom:8px;">{{ $message }}</div>
                                @enderror
                                <button type="submit" class="st-btn-block">
                                    <i class="ti-plus mr-1"></i> Create FY {{ $nextLabel }}
                                </button>
                            </form>
                            <div class="st-inf mt-3">
                                <div class="if-t"><i class="ti-info-alt mr-1"></i> How it works</div>
                                <ul>
                                    <li>Create financial years as needed</li>
                                    <li>Click <strong>Set Active</strong> to activate a year</li>
                                    <li>Trips, Expenses &amp; Reports filter automatically</li>
                                    <li>Switch years anytime — no data is lost</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- /#tab-financial-year --}}
        @endif

        @if(userCan('view_settings_branch_default'))
        <div class="st-tab-pane" id="tab-branch">
            <div class="st-sec"><i class="ti-layers" style="color:#6366f1;font-size:13px;"></i> Branch Settings</div>
            <div class="st-card">
                <div class="st-card-hd">
                    <h6><span class="hd-ico" style="background:#eef2ff;color:#6366f1;"><i class="ti-layers"></i></span> Default Branch &amp; Preferences</h6>
                </div>
                <div class="st-card-bd">
                    <form method="POST" action="{{ route('settings.branch.update') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label class="st-f" for="default_branch">Default Branch</label>
                                <select name="default_branch" id="default_branch" class="select2 form-control st-i">
                                    <option value="">— Select Default Branch —</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ ($branchSettings['default_branch']->value ?? '') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->branch_name }} ({{ optional($branch->company)->company_name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small style="color:#94a3b8;font-size:11px;margin-top:4px;display:block;">This branch will be pre-selected when creating new records.</small>
                            </div>
                        </div>
                        <div style="margin-top:18px;">
                            <button type="submit" class="st-btn st-btn-primary">
                                <i class="ti-check mr-1"></i> Save Branch Settings
                            </button>
                        </div>
                    </form>
                    <div class="st-inf mt-3">
                        <div class="if-t"><i class="ti-info-alt mr-1"></i> How it works</div>
                        <ul>
                            <li>Set a <strong>Default Branch</strong> to auto-select it on all forms</li>
                            <li>Branch settings are used globally across all modules</li>
                            <li>Change anytime — existing data is not affected</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>{{-- /#tab-branch --}}
        @endif

        @if(showAllMenu())
        <div class="st-tab-pane" id="tab-gst">
            <div class="st-sec"><i class="ti-receipt" style="color:#6366f1;font-size:13px;"></i> GST Settings</div>
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div class="st-card">
                        <div class="st-card-hd">
                            <h6><span class="hd-ico" style="background:#eef2ff;color:#6366f1;"><i class="ti-receipt"></i></span> GST Rates</h6>
                            <span id="gstCountBadge" class="ni-badge" style="background:#eef2ff;color:#4f46e5;font-size:10px;font-weight:700;padding:2px 12px;border-radius:20px;">{{ $gstSettings->count() }}</span>
                        </div>
                        <div class="st-tbl-wrap">
                            <table class="st-tbl">
                                <thead>
                                    <tr>
                                        <th style="width:38%;">GST Name</th>
                                        <th style="width:28%;">Type</th>
                                        <th style="width:18%;">Percentage</th>
                                        <th style="text-align:center;width:16%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="gstTableBody">
                                    @include('Settings._gst_rows')
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="st-card" id="gstFormCard">
                        <div class="st-card-hd">
                            <h6><span class="hd-ico" id="gstFormIcon" style="background:#dcfce7;color:#16a34a;"><i class="ti-plus"></i></span> <span id="gstFormTitle">Add GST</span></h6>
                        </div>
                        <div class="st-card-bd">
                            <form id="gstForm" onsubmit="return submitGst(event)">
                                @csrf
                                <input type="hidden" name="_method" id="gstMethod" value="POST">
                                <div class="form-group" style="margin-bottom:16px;">
                                    <label class="st-f" for="gstName">GST Name</label>
                                    <input type="text" name="name" id="gstName" class="form-control st-i"
                                           placeholder="e.g. Standard GST" required>
                                </div>
                                <div class="form-group" style="margin-bottom:16px;">
                                    <label class="st-f" for="gstType">GST Type</label>
                                    <select name="type" id="gstType" class="form-control st-i select2" required>
                                        <option value="">— Select Type —</option>
                                        <option value="CGST+SGST">CGST+SGST</option>
                                        <option value="IGST">IGST</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:20px;">
                                    <label class="st-f" for="gstPercentage">Percentage (%)</label>
                                    <input type="number" name="percentage" id="gstPercentage" class="form-control st-i"
                                           placeholder="e.g. 18" min="0" max="100" step="0.01" required>
                                </div>
                                <div style="display:flex;gap:8px;">
                                    <button type="submit" class="st-btn st-btn-primary" id="gstSubmitBtn" style="flex:1;height:44px;font-size:13px;border-radius:10px;">
                                        <i class="ti-plus mr-1"></i> Add GST
                                    </button>
                                    <button type="button" class="st-btn st-btn-ghost" id="gstCancelBtn" onclick="resetGstForm()" style="display:none;height:44px;padding:0 18px;border-radius:10px;font-size:12px;">
                                        <i class="ti-close mr-1"></i> Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- /#tab-gst --}}
        @endif

        @if(showAllMenu())
        <div class="st-tab-pane" id="tab-limits">
            <div class="st-sec"><i class="ti-lock" style="color:#6366f1;font-size:13px;"></i> Account Limits</div>
            <div class="st-card">
                <div class="st-card-hd">
                    <h6><span class="hd-ico" style="background:#eef2ff;color:#6366f1;"><i class="ti-lock"></i></span> Company &amp; Branch Add Limits</h6>
                </div>
                <div class="st-card-bd">
                    <form method="POST" action="{{ route('settings.limits.update') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <label class="st-f" for="company_limit">Company Add Limit</label>
                                <input type="number" name="company_limit" id="company_limit" class="form-control st-i"
                                       value="{{ $companyLimit }}" placeholder="e.g. 1" min="0">
                                <small style="color:#94a3b8;font-size:11px;margin-top:6px;display:block;line-height:1.4;">Maximum companies allowed. Leave empty for unlimited.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="st-f" for="branch_limit">Branch Add Limit</label>
                                <input type="number" name="branch_limit" id="branch_limit" class="form-control st-i"
                                       value="{{ $branchLimit }}" placeholder="e.g. 5" min="0">
                                <small style="color:#94a3b8;font-size:11px;margin-top:6px;display:block;line-height:1.4;">Maximum branches allowed. Leave empty for unlimited.</small>
                            </div>
                        </div>
                        <div style="margin-top:18px;">
                            <button type="submit" class="st-btn st-btn-primary">
                                <i class="ti-check mr-1"></i> Save Limits
                            </button>
                        </div>
                    </form>
                    <div class="st-inf mt-3">
                        <div class="if-t"><i class="ti-info-alt mr-1"></i> How it works</div>
                        <ul>
                            <li>Set a <strong>Company Add Limit</strong> to restrict company creation</li>
                            <li>Set a <strong>Branch Add Limit</strong> to restrict branch creation</li>
                            <li>When limit is reached, the "Add" button hides and users see a contact-support message</li>
                            <li>Leave a field empty for <strong>unlimited</strong> additions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>{{-- /#tab-limits --}}
        @endif

    </div>{{-- /.col-md-9 --}}
</div>{{-- /.row --}}

</div></div></div></div>
@endsection

@push('scripts')
<script>
function toggleEdit(id) {
    var v = document.getElementById('sv-' + id);
    var f = document.getElementById('sef-' + id);
    if (f.style.display === 'none') {
        f.style.display = 'block';
        v.style.display = 'none';
        var inp = f.querySelector('input[name="value"]');
        if (inp) { inp.focus(); inp.select(); }
    } else {
        f.style.display = 'none';
        v.style.display = 'inline-flex';
    }
}
function cancelEdit(id) {
    document.getElementById('sv-' + id).style.display = 'inline-flex';
    document.getElementById('sef-' + id).style.display = 'none';
}
function toggleFY(b) {
    var h = document.querySelectorAll('#fyTbl tbody tr.fy-hid');
    var i = document.getElementById('fyTI');
    var t = document.getElementById('fyTT');
    var w = document.querySelector('.fy-tw');
    if (h.length > 0) {
        h.forEach(function(r){ r.classList.remove('fy-hid'); });
        w.style.maxHeight = '320px';
        i.className = 'ti-angle-up';
        t.textContent = 'Show less';
    } else {
        var rows = document.querySelectorAll('#fyTbl tbody tr');
        rows.forEach(function(r, j){ if(j >= 3) r.classList.add('fy-hid'); });
        w.style.maxHeight = 'none';
        w.style.overflowY = 'visible';
        i.className = 'ti-angle-down';
        t.textContent = 'Show ' + (rows.length - 3) + ' more';
    }
}
(function() {
    var nav = document.querySelectorAll('#settingsNav .st-nav-item');
    function sw(t) {
        if (!t) return;
        nav.forEach(function(n){ n.classList.toggle('active', n.getAttribute('data-tab') === t); });
        document.querySelectorAll('.st-tab-pane').forEach(function(p){ p.classList.remove('active'); });
        var el = document.getElementById(t); if (el) el.classList.add('active');
    }
    nav.forEach(function(n){
        n.addEventListener('click', function(){
            var id = this.getAttribute('data-tab'); sw(id);
            if (history.pushState) history.pushState(null, null, '#' + id.replace('tab-', ''));
            if (id === 'tab-gst') setTimeout(initGstSelect2, 50);
        });
    });
    var h = window.location.hash.replace('#', '');
    if (h) { var m = 'tab-' + h; if (document.getElementById(m)) { sw(m); if (m === 'tab-gst') setTimeout(initGstSelect2, 50); } }
})();

// ── GST: fetch CRUD + toastr ─────────────────────────────────────
var gstEditId = null;

function initGstSelect2() {
    if (typeof $ !== 'undefined' && !$('#gstType').data('select2'))
        $('#gstType').select2({ width: '100%', dropdownParent: $('#gstForm').closest('.st-card-bd') });
}

function submitGst(e) {
    e.preventDefault();
    var fd = new FormData(document.getElementById('gstForm'));
    if (gstEditId) { fd.set('_method', 'PUT'); }
    var url = gstEditId ? '{{ url('settings/gst') }}/' + gstEditId : '{{ route('settings.gst.store') }}';
    fetch(url, { method: 'POST', body: fd, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r) { return r.json(); })
        .then(function(r) {
            document.getElementById('gstTableBody').innerHTML = r.rows;
            document.querySelector('[data-tab="tab-gst"] .ni-badge').textContent = r.count;
            document.getElementById('gstCountBadge').textContent = r.count;
            resetGstForm(); toastr.success(r.success);
        });
    return false;
}

function deleteGst(id) {
    var name = document.querySelector('#gst-row-' + id + ' td strong').textContent;
    document.getElementById('globalDelName').textContent = name;
    document.getElementById('globalDelType').textContent = 'GST';
    window._gstDelOrigConfirm = window.globalDelConfirm;
    window.globalDelConfirm = function() {
        clearInterval(globalDelTimer); $('#globalDeleteModal').modal('hide');
        var fd = new FormData(); fd.append('_token', '{{ csrf_token() }}'); fd.append('_method', 'DELETE');
        fetch('{{ url('settings/gst') }}/' + id, { method: 'POST', body: fd, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(r) {
                document.getElementById('gstTableBody').innerHTML = r.rows;
                document.querySelector('[data-tab="tab-gst"] .ni-badge').textContent = r.count;
                document.getElementById('gstCountBadge').textContent = r.count;
                toastr.success(r.success);
            });
        window.globalDelConfirm = window._gstDelOrigConfirm;
    };
    $('#globalDeleteModal').on('hidden.bs.modal.gst', function() { window.globalDelConfirm = window._gstDelOrigConfirm; $(this).off('hidden.bs.modal.gst'); });
    $('#globalDeleteModal').modal('show');
}

function openGstEdit(id, name, type, percentage) {
    gstEditId = id;
    document.getElementById('gstName').value = name;
    document.getElementById('gstType').value = type;
    if (typeof $ !== 'undefined') { var $el = $('#gstType'); if (!$el.data('select2')) $el.select2({ width: '100%', dropdownParent: $('#gstForm').closest('.st-card-bd') }); $el.trigger('change'); }
    document.getElementById('gstPercentage').value = percentage;
    document.getElementById('gstFormTitle').textContent = 'Edit GST';
    document.getElementById('gstFormIcon').className = 'ti-pencil';
    document.getElementById('gstFormIcon').style.background = '#eef2ff';
    document.getElementById('gstFormIcon').style.color = '#6366f1';
    document.getElementById('gstSubmitBtn').innerHTML = '<i class="ti-check mr-1"></i> Update GST';
    document.getElementById('gstCancelBtn').style.display = 'flex';
}

function resetGstForm() {
    gstEditId = null;
    document.getElementById('gstForm').reset();
    document.getElementById('gstMethod').value = 'POST';
    if (typeof $ !== 'undefined') { var $el = $('#gstType'); if ($el.data('select2')) { $el.val('').trigger('change'); } else { $el.select2({ width: '100%', dropdownParent: $('#gstForm').closest('.st-card-bd') }); } }
    document.getElementById('gstFormTitle').textContent = 'Add GST';
    document.getElementById('gstFormIcon').className = 'ti-plus';
    document.getElementById('gstFormIcon').style.background = '#dcfce7';
    document.getElementById('gstFormIcon').style.color = '#16a34a';
    document.getElementById('gstSubmitBtn').innerHTML = '<i class="ti-plus mr-1"></i> Add GST';
    document.getElementById('gstCancelBtn').style.display = 'none';
}
</script>
@endpush
