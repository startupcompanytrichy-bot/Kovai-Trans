@extends('layouts.app')

@push('styles')
<style>
    /* ════════════════════════════════════════════════════════════════════
   SETTINGS PAGE — Premium Layout
════════════════════════════════════════════════════════════════════ */
    .st-page {
        background: #f0f2f8;
        min-height: 100vh;
    }

    /* ── Table scroll wrapper ───────────────────────────────────────── */
    .st-tbl-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .st-tbl-wrap table {
        margin-bottom: 0;
    }

    .st-tbl-wrap+.st-card-bd {
        border-top: 1px solid #f1f5f9;
    }

    /* ── Page header ─────────────────────────────────────────────────── */
    .st-hdr {
        background: linear-gradient(135deg, #0c1322 0%, #1a2340 40%, #3b4f8a 70%, #667eea 100%);
        border-radius: 16px;
        padding: 22px 28px;
        color: #fff;
        margin-bottom: 22px;
        position: relative;
        overflow: hidden;
    }

    .st-hdr::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 240px;
        height: 240px;
        background: rgba(255, 255, 255, .04);
        border-radius: 50%;
    }

    .st-hdr::after {
        content: '';
        position: absolute;
        bottom: -40px;
        right: -20px;
        width: 140px;
        height: 140px;
        background: rgba(255, 255, 255, .03);
        border-radius: 50%;
    }

    .st-hdr-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, .1);
        border: 1px solid rgba(255, 255, 255, .18);
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .5px;
        margin-bottom: 8px;
        backdrop-filter: blur(4px);
    }

    .st-hdr h4 {
        font-size: 20px;
        font-weight: 800;
        margin: 0 0 3px;
        position: relative;
        z-index: 1;
    }

    .st-hdr .sub {
        font-size: 12px;
        opacity: .65;
        position: relative;
        z-index: 1;
    }

    /* ── Sidebar navigation ──────────────────────────────────────────── */
    .st-nav-wrap {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(15, 23, 42, .07);
        overflow: hidden;
        position: sticky;
        top: 20px;
    }

    .st-nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        cursor: pointer;
        border-left: 3px solid transparent;
        transition: all .2s;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        position: relative;
    }

    .st-nav-item:not(:last-child)::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 20px;
        right: 20px;
        height: 1px;
        background: #f1f5f9;
    }

    .st-nav-item:hover {
        background: #f8fafc;
        color: #1e293b;
    }

    .st-nav-item.active {
        background: #eef2ff;
        border-left-color: #6366f1;
        color: #4f46e5;
    }

    .st-nav-item .ni-ico {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
        background: #f1f5f9;
        color: #94a3b8;
        transition: all .2s;
    }

    .st-nav-item.active .ni-ico {
        background: linear-gradient(135deg, #6366f1, #818cf8);
        color: #fff;
        box-shadow: 0 4px 10px rgba(99, 102, 241, .3);
    }

    .st-nav-item .ni-badge {
        margin-left: auto;
        background: #f1f5f9;
        color: #94a3b8;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 10px;
        border-radius: 20px;
    }

    .st-nav-item.active .ni-badge {
        background: #c7d2fe;
        color: #4f46e5;
    }

    /* ── Tab panes ────────────────────────────────────────────────────── */
    .st-tab-pane {
        display: none;
    }

    .st-tab-pane.active {
        display: block;
        animation: stFade .25s ease;
    }

    @keyframes stFade {
        from {
            opacity: 0;
            transform: translateY(8px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    /* ── Section heading ──────────────────────────────────────────────── */
    .st-sec {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: #94a3b8;
        margin: 0 0 14px;
    }

    .st-sec::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    /* ── Card ─────────────────────────────────────────────────────────── */
    .st-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 10px rgba(15, 23, 42, .05);
        overflow: hidden;
        margin-bottom: 20px;
        transition: box-shadow .2s;
    }

    .st-card:hover {
        box-shadow: 0 4px 20px rgba(15, 23, 42, .08);
    }

    .st-card-hd {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 22px;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbff;
        flex-wrap: wrap;
        gap: 8px;
    }

    .st-card-hd h6 {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .st-card-hd .hd-ico {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }

    .st-card-bd {
        padding: 22px;
    }

    /* ── Active FY banner ────────────────────────────────────────────── */
    .st-fy-act {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f0fff4, #dcfce7);
        border: 1px solid #86efac;
        margin-bottom: 16px;
    }

    .st-fy-act .fa-ico {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(34, 197, 94, .3);
    }

    .st-fy-act .fa-lbl {
        font-size: 10px;
        font-weight: 700;
        color: #166534;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .st-fy-act .fa-val {
        font-size: 16px;
        font-weight: 800;
        color: #14532d;
        line-height: 1.1;
    }

    .st-fy-act .fa-rng {
        font-size: 11px;
        color: #22c55e;
    }

    .st-fy-miss {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 12px;
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 1px solid #fcd34d;
        margin-bottom: 16px;
    }

    /* ── FY table ─────────────────────────────────────────────────────── */
    #fyTbl {
        min-width: 520px;
        margin-bottom: 0;
    }

    #fyTbl th,
    #fyTbl td {
        height: 46px;
        padding: 8px 14px;
        vertical-align: middle;
        border-color: #f1f5f9;
        font-size: 13px;
    }

    #fyTbl th {
        background: #f8fafc;
        color: #0f172a;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    #fyTbl tr.fy-on td {
        background: #f0fdf4 !important;
    }

    #fyTbl tr.fy-on td:first-child {
        border-left: 3px solid #22c55e;
    }

    #fyTbl tbody tr.fy-hid {
        display: none;
    }

    .fy-tw {
        max-height: 320px;
        overflow-y: auto;
        overflow-x: auto;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
    }

    .fy-tw::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }

    .fy-tw::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .fy-more {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px;
        border: 1px solid #f1f5f9;
        border-top: none;
        font-size: 12px;
        font-weight: 700;
        color: #6366f1;
        cursor: pointer;
        background: #fafbff;
        transition: all .15s;
        border-radius: 0 0 10px 10px;
    }

    .fy-more:hover {
        background: #eef2ff;
    }

    /* ── Next FY preview ──────────────────────────────────────────────── */
    .fy-next {
        background: linear-gradient(135deg, #eef2ff, #e0e7ff);
        border: 1.5px dashed #a5b4fc;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 16px;
        text-align: center;
    }

    .fy-next .fn-lbl {
        font-size: 10px;
        font-weight: 700;
        color: #6366f1;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 6px;
    }

    .fy-next .fn-val {
        font-size: 24px;
        font-weight: 800;
        color: #4338ca;
        letter-spacing: .5px;
        margin-bottom: 2px;
    }

    .fy-next .fn-rng {
        font-size: 12px;
        color: #6366f1;
        opacity: .7;
    }

    /* ── Form elements ────────────────────────────────────────────────── */
    .st-f {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 5px;
        display: block;
    }

    .st-f .req {
        color: #ef4444;
    }

    .st-i {
        height: 42px;
        font-size: 13px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 0 14px;
        width: 100%;
        color: #0f172a;
        background: #fff;
        transition: all .15s;
    }

    .st-i:focus {
        border-color: #6366f1;
        outline: none;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
    }

    textarea.st-i {
        height: auto;
        min-height: 200px;
        padding: 12px 14px;
    }

    select.st-i {
        padding: 0 12px;
    }

    .st-i-sm {
        height: 36px;
        font-size: 12px;
        border-radius: 8px;
    }

    /* ── Info box ─────────────────────────────────────────────────────── */
    .st-inf {
        padding: 14px 16px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 12px;
        color: #64748b;
        line-height: 1.8;
    }

    .st-inf .if-t {
        font-size: 10px;
        font-weight: 700;
        color: #6366f1;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 6px;
    }

    .st-inf ul {
        margin: 0;
        padding-left: 18px;
    }

    /* ── Buttons ──────────────────────────────────────────────────────── */
    .st-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 38px;
        padding: 0 18px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all .15s;
        text-decoration: none;
    }

    .st-btn-primary {
        background: linear-gradient(135deg, #6366f1, #818cf8);
        color: #fff;
        box-shadow: 0 3px 10px rgba(99, 102, 241, .25);
    }

    .st-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 16px rgba(99, 102, 241, .35);
    }

    .st-btn-success {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: #fff;
        box-shadow: 0 3px 10px rgba(34, 197, 94, .25);
    }

    .st-btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 16px rgba(34, 197, 94, .35);
    }

    .st-btn-ghost {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .st-btn-ghost:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* ── Icon buttons ─────────────────────────────────────────────────── */
    .st-ib {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        cursor: pointer;
        transition: all .15s;
    }

    .st-ib.edit {
        background: #eef2ff;
        color: #6366f1;
    }

    .st-ib.edit:hover {
        background: #6366f1;
        color: #fff;
    }

    .st-ib.del {
        background: #fef2f2;
        color: #ef4444;
    }

    .st-ib.del:hover {
        background: #ef4444;
        color: #fff;
    }

    /* ── Badges/pills ─────────────────────────────────────────────────── */
    .st-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    /* ── All Settings table ───────────────────────────────────────────── */
    .st-tbl {
        width: 100%;
        border-collapse: collapse;
    }

    .st-tbl thead {
        border-bottom: 2px solid #eef2ff;
    }

    .st-tbl th {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #94a3b8;
        background: #f8fafc;
        padding: 12px 18px;
        border: none;
        white-space: nowrap;
    }

    .st-tbl td {
        font-size: 12.5px;
        padding: 14px 18px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        border-top: none;
    }

    .st-tbl tr:last-child td {
        border-bottom: none;
    }

    .st-tbl tbody tr {
        transition: background .15s;
    }

    .st-tbl tbody tr:hover td {
        background: #fafbff;
    }

    .st-tbl tbody tr:has(.sef[style*="block"]) td {
        background: #f8faff;
    }

    /* ── Primary button full width ────────────────────────────────────── */
    .st-btn-block {
        display: block;
        width: 100%;
        height: 44px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        background: linear-gradient(135deg, #6366f1, #818cf8);
        color: #fff;
        box-shadow: 0 3px 12px rgba(99, 102, 241, .3);
        transition: all .15s;
    }

    .st-btn-block:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, .4);
    }

    /* ── Smooth value edit transition ─────────────────────────────────── */
    .sv,
    .sef {
        transition: opacity .15s;
    }

    /* ── Card body compact variant ────────────────────────────────────── */
    .st-card-bd-compact {
        padding: 16px 22px;
    }

    /* ── Responsive ───────────────────────────────────────────────────── */
    @media (max-width:767px) {
        .st-hdr {
            padding: 16px 18px;
        }

        .st-hdr h4 {
            font-size: 17px;
        }

        .st-card-hd {
            padding: 14px 16px;
        }

        .st-card-bd {
            padding: 16px;
        }
    }

    .wa-delete-toast {
        max-width: 340px !important;
        text-align: center;
    }

    .wa-delete-toast .toast-message {
        font-size: 13px;
    }
</style>
@endpush

@section('content')

<div class="pcoded-inner-content st-page">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body" style="padding:22px;background:#f0f2f8;min-height:100vh;">

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
                            <div class="st-nav-item" data-tab="tab-whatsapp">
                                <div class="ni-ico"><i class="ti-comment-alt"></i></div>
                                WhatsApp Integration
                            </div>
                            <div class="st-nav-item" data-tab="tab-reminder-contacts">
                                <div class="ni-ico"><i class="ti-user"></i></div>
                                WhatsApp Reminder Contacts
                            </div>
                            <div class="st-nav-item" data-tab="tab-message-template">
                                <div class="ni-ico"><i class="ti-comment"></i></div>
                                Message Template
                                <span class="ni-badge">{{ $messageTemplates->count() }}</span>
                            </div>
                            <div class="st-nav-item" data-tab="tab-vehicle-reminder">
                                <div class="ni-ico"><i class="ti-truck"></i></div>
                                Vehicle Reminder
                                <span class="ni-badge">{{ $vehicleConfigs->count() }}</span>
                            </div>
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
                                            $lastFY = $financialYears->sortByDesc('start_date')->first();
                                            $nextStartYear = $lastFY
                                            ? $lastFY->start_date->year + 1
                                            : (now()->month >= 4 ? now()->year : now()->year - 1);
                                            $nextLabel = \App\Models\FinancialYear::generateLabel($nextStartYear);
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

                        {{-- ═══ Tab: WhatsApp Integration ═══════════════════════════════ --}}
                        <div class="st-tab-pane" id="tab-whatsapp">
                            <div class="st-sec"><i class="ti-comment-alt" style="color:#25d366;font-size:13px;"></i> WhatsApp Integration</div>

                            @php
                            $svc = app(\App\Services\WhatsAppService::class);
                            $status = $svc->getBaileysStatus();
                            $baileysConnected = $status['connected'] ?? false;
                            $connectedNumber = $whatsappSettings['whatsapp_connected_number']->value ?? '';
                            @endphp

                            {{-- Status Bar --}}
                            <div id="wa-status-bar" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;padding:10px 14px;background:#f8fafc;border-radius:6px;margin-bottom:14px;">
                                <span style="font-size:13px;font-weight:600;">Status:</span>
                                <span id="wa-status-text" style="font-weight:600;" data-connected="{{ $baileysConnected ? '1' : '0' }}">
                                    @if($baileysConnected)
                                    <span style="color:#22c55e;">✅ Connected</span>
                                    @else
                                    <span style="color:#ef4444;">❌ Not connected</span>
                                    @endif
                                </span>
                                <span id="wa-status-number" style="font-size:13px;color:#475569;{{ $connectedNumber ? '' : 'display:none;' }}">
                                    @if($connectedNumber)
                                    — Number: <strong>+91 {{ $connectedNumber }}</strong>
                                    @endif
                                </span>
                            </div>

                            {{-- QR Scan Section (always in DOM, toggled by JS) --}}
                            <div id="wa-qr-section" style="{{ $baileysConnected ? 'display:none;' : '' }}">
                                <div class="st-card">
                                    <div class="st-card-bd" style="text-align:center;padding:30px;">
                                        <p style="font-size:14px;font-weight:600;color:#1e293b;margin-bottom:6px;">Scan QR Code to Connect</p>
                                        <p style="font-size:13px;color:#64748b;margin-bottom:16px;">Open WhatsApp → Settings → Linked Devices → Link a Device</p>
                                        <div id="wa-qr-box" style="display:inline-block;background:#fff;padding:16px;border:2px dashed #25d366;border-radius:8px;">
                                            <div id="wa-qr-container" style="width:220px;height:220px;margin:0 auto;">
                                                <div style="padding:80px 0;color:#94a3b8;font-size:13px;">Loading QR...</div>
                                            </div>
                                        </div>
                                        <div id="wa-qr-actions" style="margin-top:14px;display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
                                            <button type="button" class="st-btn st-btn-secondary" onclick="refreshWaQr()" style="font-size:12px;padding:6px 16px;">
                                                <i class="ti-reload mr-1"></i> Refresh QR
                                            </button>
                                            <button type="button" id="wa-reconnect-btn" class="st-btn" style="display:none;background:#f59e0b;color:#fff;font-size:12px;padding:6px 16px;" onclick="reconnectWhatsApp()">
                                                <i class="ti-restart mr-1"></i> Reconnect
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Settings Form (always in DOM, toggled by JS) --}}
                            <div id="wa-settings-section" class="st-card mt-4" style="{{ $baileysConnected ? '' : 'display:none;' }}">
                                <div class="st-card-hd">
                                    <h6><span class="hd-ico" style="background:#f0fdf4;color:#25d366;"><i class="ti-settings"></i></span> Settings</h6>
                                </div>
                                <div class="st-card-bd">
                                    <form method="POST" action="{{ route('settings.whatsapp.update') }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="st-f" for="whatsapp_connected_number">WhatsApp Connected Number</label>
                                                <div style="display:flex;gap:0;">
                                                    <span style="display:inline-flex;align-items:center;padding:0 10px;background:#f1f5f9;border:1px solid #d1d5db;border-right:none;border-radius:4px 0 0 4px;font-size:14px;font-weight:600;color:#475569;">+91</span>
                                                    <input type="text" name="whatsapp_connected_number" id="whatsapp_connected_number" class="form-control st-i" style="border-radius:0 4px 4px 0;{{ $baileysConnected ? 'background:#f1f5f9;color:#6b7280;cursor:not-allowed;' : '' }}"
                                                        value="{{ $connectedNumber }}"
                                                        placeholder="e.g., 9025392100"
                                                        maxlength="10" pattern="[0-9]{10}" inputmode="numeric"
                                                        {{ $baileysConnected ? 'readonly' : '' }}>
                                                </div>
                                                <small style="color:#94a3b8;font-size:11px;margin-top:4px;display:block;">{{ $baileysConnected ? 'Number is locked while connected. Disconnect to change.' : 'Your WhatsApp number (10 digits only).' }}</small>
                                            </div>
                                        </div>
                                        <div style="margin-top:18px;display:flex;gap:8px;flex-wrap:wrap;">
                                            <button type="button" class="st-btn" style="background:#ef4444;color:#fff;" onclick="disconnectWhatsApp()">
                                                <i class="ti-close mr-1"></i> Disconnect
                                            </button>
                                            <button type="submit" class="st-btn st-btn-primary">
                                                <i class="ti-check mr-1"></i> Save Settings
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Today's Usage & Test Send (always in DOM, toggled by JS) --}}
                            <div id="wa-usage-section" class="st-card mt-4" style="{{ $baileysConnected ? '' : 'display:none;' }}">
                                <div class="st-card-hd">
                                    <h6><span class="hd-ico" style="background:#f0fdf4;color:#25d366;"><i class="ti-bar-chart-alt"></i></span> Today's Usage</h6>
                                </div>
                                <div class="st-card-bd">
                                    @php
                                    $remaining = $svc->getRemainingCount();
                                    $limit = (int) ($whatsappSettings['whatsapp_daily_limit']->value ?? 100);
                                    $sent = max(0, $limit - $remaining);
                                    $pct = $limit > 0 ? round(($sent / $limit) * 100) : 0;
                                    @endphp
                                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                                        <span id="wa-usage-text" style="font-size:14px;font-weight:600;">
                                            {{ $sent }} / {{ $limit }} messages sent today
                                        </span>
                                        <div style="flex:1;min-width:120px;height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;">
                                            <div id="wa-usage-bar" style="width:{{ $pct }}%;height:100%;background:{{ $pct >= 90 ? '#ef4444' : ($pct >= 70 ? '#f59e0b' : '#22c55e') }};border-radius:4px;transition:width .3s;"></div>
                                        </div>
                                        @if($remaining === 0)
                                        <span id="wa-limit-reached" style="color:#ef4444;font-size:13px;font-weight:600;">Limit reached!</span>
                                        @endif
                                    </div>
                                    <hr style="margin:14px 0;border-color:#e2e8f0;">
                                    <form method="POST" action="{{ route('settings.whatsapp.test') }}" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                                        @csrf
                                        <span style="font-size:13px;font-weight:500;">Test Send:</span>
                                        <div style="display:flex;gap:0;">
                                            <span style="display:inline-flex;align-items:center;padding:0 10px;background:#f1f5f9;border:1px solid #d1d5db;border-right:none;border-radius:4px 0 0 4px;font-size:14px;font-weight:600;color:#475569;">+91</span>
                                            <input type="text" name="test_number" class="form-control st-i" style="border-radius:0 4px 4px 0;max-width:180px;"
                                                placeholder="e.g., 9025392100"
                                                maxlength="10" pattern="[0-9]{10}" inputmode="numeric"
                                                value="{{ $whatsappSettings['whatsapp_connected_number']->value ?? '' }}">
                                        </div>
                                        <button type="submit" class="st-btn st-btn-secondary">
                                            <i class="ti-comment mr-1"></i> Send Test
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>{{-- /#tab-whatsapp --}}

                        <div class="st-tab-pane" id="tab-reminder-contacts">
                            <div class="st-sec"><i class="ti-user" style="color:#25d366;font-size:13px;"></i> WhatsApp Reminder Contacts</div>

                            <div id="wa-contacts-section" class="st-card">
                                <div class="st-card-hd">
                                    <h6><span class="hd-ico" style="background:#f0fdf4;color:#25d366;"><i class="ti-user"></i></span> WhatsApp Reminder Contacts</h6>
                                    <span id="waContactCount" class="ni-badge" style="background:#dcfce7;color:#16a34a;font-size:10px;font-weight:700;padding:2px 12px;border-radius:20px;">{{ $waContacts->count() }}</span>
                                </div>
                                <div class="st-card-bd" style="padding-bottom:0;">
                                    <p style="font-size:12px;color:#94a3b8;margin:0 0 12px;">Contacts who receive EMI and expiry reminders via WhatsApp.</p>
                                    {{-- Add Form --}}
                                    <form id="waContactForm" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:16px;">
                                        @csrf
                                        <div style="flex:1;min-width:140px;">
                                            <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:3px;">Name <span style="color:#ef4444;">*</span></label>
                                            <input type="text" id="wacName" class="form-control st-i" placeholder="e.g. Ramesh Kumar" maxlength="100" style="font-size:12px;">
                                        </div>
                                        <div style="flex:1;min-width:130px;">
                                            <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:3px;">Mobile Number <span style="color:#ef4444;">*</span></label>
                                            <input type="text" id="wacMobile" class="form-control st-i" placeholder="e.g. 9876543210" maxlength="10" inputmode="numeric" style="font-size:12px;">
                                        </div>
                                        <div style="display:flex;gap:6px;">
                                            <button type="submit" id="wacSubmitBtn" class="st-btn st-btn-primary" style="padding:7px 16px;font-size:12px;white-space:nowrap;">
                                                <i class="ti-plus mr-1"></i> Add
                                            </button>
                                            <button type="button" id="wacCancelBtn" class="st-btn st-btn-secondary" style="display:none;padding:7px 12px;font-size:12px;" onclick="resetWaContactForm()">
                                                <i class="ti-close"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div style="padding:0;">
                                    @if($waContacts->isEmpty())
                                    <div id="waContactEmpty" style="text-align:center;padding:28px 20px;color:#94a3b8;font-size:13px;">
                                        <i class="ti-user" style="font-size:28px;display:block;margin-bottom:6px;opacity:.3;"></i>
                                        No contacts added yet
                                    </div>
                                    @endif
                                    <div style="overflow-x:auto;{{ $waContacts->isEmpty() ? 'display:none;' : '' }}" id="waContactTableWrap">
                                        <table class="st-tbl">
                                            <thead>
                                                <tr>
                                                    <th style="width:5%;">#</th>
                                                    <th style="width:22%;">Name</th>
                                                    <th style="width:18%;">Mobile</th>
                                                    <th style="width:12%;text-align:center;">Status</th>
                                                    <th style="width:12%;text-align:center;">Send Status</th>
                                                    <th style="width:31%;text-align:center;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="waContactBody">
                                                @foreach($waContacts as $idx => $c)
                                                @php
                                                $parts = explode(' ', trim($c->name));
                                                $initials = '';
                                                foreach ($parts as $p) { $initials .= strtoupper(substr($p, 0, 1)); }
                                                $initials = substr($initials, 0, 2);
                                                $colors = ['#25d366','#075e54','#128c7e','#f57c00','#7b1fa2','#c62828','#1565c0','#00838f','#6a1b9a','#2e7d32'];
                                                $colorIdx = crc32($c->name) % count($colors);
                                                $bg = $colors[$colorIdx];
                                                @endphp
                                                <tr id="waCrow-{{ $c->id }}">
                                                    <td style="color:#94a3b8;font-size:12px;">{{ $idx + 1 }}</td>
                                                    <td>
                                                        <div style="display:flex;align-items:center;gap:10px;">
                                                            <div style="width:25px;height:25px;border-radius:50%;background:{{ $bg }};color:#fff;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;letter-spacing:0.5px;">{{ $initials }}</div>
                                                            <span style="font-size:13px;font-weight:600;">{{ $c->name }}</span>
                                                        </div>
                                                    </td>
                                                    <td style="font-size:13px;font-family:monospace;color:#475569;">+91 {{ $c->mobile }}</td>
                                                    <td style="text-align:center;">
                                                        <span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:10px;font-weight:600;{{ $c->is_active ? 'background:#dcfce7;color:#16a34a;' : 'background:#fee2e2;color:#ef4444;' }}" id="waCStatus-{{ $c->id }}">
                                                            {{ $c->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                    <td style="text-align:center;">
                                                        @if($c->last_send_status === 'sent')
                                                        <span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:10px;font-weight:600;background:#dcfce7;color:#16a34a;"><i class="ti-check"></i> Sent</span>
                                                        @elseif($c->last_send_status === 'failed')
                                                        <span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:10px;font-weight:600;background:#fee2e2;color:#ef4444;"><i class="ti-close"></i> Failed</span>
                                                        @else
                                                        <span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:10px;font-weight:600;background:#f1f5f9;color:#94a3b8;">—</span>
                                                        @endif
                                                    </td>
                                                    <td style="text-align:center;">
                                                        <div style="display:flex;gap:6px;justify-content:center;">
                                                            <button type="button" class="st-btn st-btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="editWaContact('{{ $c->id }}', '{!! addslashes($c->name) !!}', '{{ $c->mobile }}')" title="Edit">
                                                                <i class="ti-pencil"></i>
                                                            </button>
                                                            <button type="button" class="st-btn st-btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="toggleWaContact('{{ $c->id }}')" title="Toggle">
                                                                <i class="ti-{{ $c->is_active ? 'power-off' : 'check' }}"></i>
                                                            </button>
                                                            <button type="button" class="st-btn" style="padding:4px 10px;font-size:11px;background:#fee2e2;color:#ef4444;border:1px solid #fca5a5;" onclick="deleteWaContact('{{ $c->id }}', '{!! addslashes($c->name) !!}')" title="Delete">
                                                                <i class="ti-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>{{-- /#tab-reminder-contacts --}}

                        <div class="st-tab-pane" id="tab-message-template">
                            <div class="st-sec"><i class="ti-comment" style="color:#25d366;font-size:13px;"></i> Message Template</div>

                            {{-- ── Add / Edit Form ─────────────────────────────────────────── --}}
                            <div class="st-card">
                                <div class="st-card-hd">
                                    <h6><span class="hd-ico" id="mtFormIcon" style="background:#dcfce7;color:#16a34a;"><i class="ti-plus"></i></span> <span id="mtFormTitle">Add Template</span></h6>
                                </div>
                                <div class="st-card-bd">
                                    <form id="mtForm" onsubmit="return submitMsgTemplate(event)">
                                        @csrf
                                        <input type="hidden" name="_method" id="mtMethod" value="POST">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group" style="margin-bottom:16px;">
                                                    <label class="st-f" for="mtName">Template Name <span class="req">*</span></label>
                                                    <input type="text" name="template_name" id="mtName" class="form-control st-i"
                                                        placeholder="e.g. EMI Reminder" maxlength="150" required>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group" style="margin-bottom:16px;">
                                                    <label class="st-f" for="mtMessage">Message <span class="req">*</span></label>
                                                    <textarea name="message" id="mtMessage" class="form-control st-i" rows="3"
                                                        placeholder="Type your message template here..." required style="resize:vertical;"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="display:flex;gap:8px;">
                                            <button type="submit" class="st-btn st-btn-primary" id="mtSubmitBtn" style="height:42px;font-size:13px;border-radius:10px;padding:0 22px;">
                                                <i class="ti-plus mr-1"></i> Add Template
                                            </button>
                                            <button type="button" class="st-btn st-btn-ghost" id="mtCancelBtn" onclick="resetMsgTemplateForm()" style="display:none;height:42px;padding:0 18px;border-radius:10px;font-size:12px;">
                                                <i class="ti-close mr-1"></i> Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- ── List / Table ─────────────────────────────────────────── --}}
                            <div class="st-card mt-3">
                                <div class="st-card-hd">
                                    <h6><span class="hd-ico" style="background:#eef2ff;color:#4f46e5;"><i class="ti-list"></i></span> Saved Templates</h6>
                                    <span id="mtCountBadge" class="ni-badge" style="background:#dcfce7;color:#16a34a;font-size:10px;font-weight:700;padding:2px 12px;border-radius:20px;">{{ $messageTemplates->count() }}</span>
                                </div>
                                <div class="st-tbl-wrap">
                                    <table class="st-tbl">
                                        <thead>
                                            <tr>
                                                <th style="width:5%;">#</th>
                                                <th style="width:20%;">Template Name</th>
                                                <th style="width:48%;">Message</th>
                                                <th style="text-align:center;width:12%;">Status</th>
                                                <th style="text-align:center;width:15%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="mtTableBody">
                                            @include('Settings._message_template_rows')
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>{{-- /#tab-message-template --}}

                        <div class="st-tab-pane" id="tab-vehicle-reminder">
                            <div class="st-sec"><i class="ti-truck" style="color:#6366f1;font-size:13px;"></i> Vehicle Reminder Settings</div>

                                {{-- ── Add / Edit Config ─────────────────────────────────────── --}}
                                <div class="st-card">
                                        <div class="st-card-hd">
                                            <h6><span class="hd-ico" id="vrcFormIcon" style="background:#dcfce7;color:#16a34a;"><i class="ti-plus"></i></span> <span id="vrcFormTitle">Add Reminder Config</span></h6>
                                        </div>
                                        <div class="st-card-bd">
                                            <form id="vrcForm" onsubmit="return submitVrc(event)">
                                                @csrf
                                                <input type="hidden" name="_method" id="vrcMethod" value="POST">
                                                <div class="form-group" style="margin-bottom:14px;">
                                                    <label class="st-f" for="vrc_template">Template <span class="req">*</span></label>
                                                    <select name="template_id" id="vrc_template" class="form-control st-i select2" style="width:100%;" required>
                                                        <option value="">— Select Template —</option>
                                                        @foreach($messageTemplates as $tmpl)
                                                            @if($tmpl->status)
                                                            <option value="{{ $tmpl->id }}">{{ $tmpl->template_name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <small style="color:#94a3b8;font-size:11px;margin-top:4px;display:block;">Message content is pulled from <strong>Message Template</strong> settings.</small>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6" style="margin-bottom:14px;">
                                                        <label class="st-f" for="vrc_duration">Duration <span class="req">*</span></label>
                                                        <select name="duration" id="vrc_duration" class="form-control st-i select2" style="width:100%;" required>
                                                            <option value="">— Duration —</option>
                                                            <option value="Last 30 Days">Last 30 Days</option>
                                                            <option value="Last 15 Days">Last 15 Days</option>
                                                            <option value="Last 10 Days">Last 10 Days</option>
                                                            <option value="Last 5 Days">Last 5 Days</option>
                                                            <option value="Last 1 Day">Last 1 Day</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6" style="margin-bottom:14px;">
                                                        <label class="st-f" for="vrc_time">Time <span class="req">*</span></label>
                                                        <input type="text" name="time" id="vrc_time" class="form-control st-i timepicker" style="width:100%;" placeholder="e.g. 02:30 PM" required>
                                                    </div>
                                                </div>
                                                <div style="display:flex;gap:8px;">
                                                    <button type="submit" class="st-btn st-btn-primary" id="vrcSubmitBtn" style="height:42px;font-size:13px;border-radius:10px;padding:0 22px;">
                                                        <i class="ti-plus mr-1"></i> Add Config
                                                    </button>
                                                    <button type="button" class="st-btn st-btn-ghost" id="vrcCancelBtn" onclick="resetVrcForm()" style="display:none;height:42px;padding:0 18px;border-radius:10px;font-size:12px;">
                                                        <i class="ti-close mr-1"></i> Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                </div>

                                {{-- ── Existing Configs Table ────────────────────────────────── --}}
                                <div class="st-card mt-3">
                                    <div class="st-card-hd">
                                        <h6><span class="hd-ico" style="background:#eef2ff;color:#6366f1;"><i class="ti-list"></i></span> Saved Configurations</h6>
                                        <span id="vrcCountBadge" class="ni-badge">{{ $vehicleConfigs->count() }}</span>
                                    </div>
                                    <div class="st-tbl-wrap">
                                        <table class="st-tbl">
                                            <thead>
                                                <tr>
                                                    <th>Template</th>
                                                    <th>Duration</th>
                                                    <th>Time</th>
                                                    <th style="text-align:center;width:60px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="vrcTableBody">
                                                @include('Settings._vehicle_reminder_rows')
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                        </div>{{-- /#tab-vehicle-reminder --}}

                    </div>{{-- /.col-md-9 --}}
                </div>{{-- /.row --}}

            </div>
        </div>
    </div>
</div>
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
            if (inp) {
                inp.focus();
                inp.select();
            }
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
            h.forEach(function(r) {
                r.classList.remove('fy-hid');
            });
            w.style.maxHeight = '320px';
            i.className = 'ti-angle-up';
            t.textContent = 'Show less';
        } else {
            var rows = document.querySelectorAll('#fyTbl tbody tr');
            rows.forEach(function(r, j) {
                if (j >= 3) r.classList.add('fy-hid');
            });
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
            nav.forEach(function(n) {
                n.classList.toggle('active', n.getAttribute('data-tab') === t);
            });
            document.querySelectorAll('.st-tab-pane').forEach(function(p) {
                p.classList.remove('active');
            });
            var el = document.getElementById(t);
            if (el) el.classList.add('active');
        }

        function initVrcSelect2() {
            if (typeof $ === 'undefined' || !$.fn.select2) return;
            $('#tab-vehicle-reminder .select2').each(function() {
                // Destroy first to force correct width recalculation after tab becomes visible
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
                $(this).select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: $(this).find('option:first').text()
                });
            });
        }

        nav.forEach(function(n) {
            n.addEventListener('click', function() {
                var id = this.getAttribute('data-tab');
                sw(id);
                if (history.pushState) history.pushState(null, null, '#' + id.replace('tab-', ''));
                if (id === 'tab-gst') setTimeout(initGstSelect2, 50);
                if (id === 'tab-vehicle-reminder') setTimeout(initVrcSelect2, 50);
            });
        });

        // Handle direct URL hash on page load
        var h = window.location.hash.replace('#', '');
        if (h) {
            var m = 'tab-' + h;
            if (document.getElementById(m)) {
                sw(m);
                if (m === 'tab-gst') setTimeout(initGstSelect2, 100);
                if (m === 'tab-vehicle-reminder') setTimeout(initVrcSelect2, 100);
            }
        }
    })();

    // ── GST: fetch CRUD + toastr ─────────────────────────────────────
    var gstEditId = null;

    function initGstSelect2() {
        if (typeof $ !== 'undefined' && !$('#gstType').data('select2'))
            $('#gstType').select2({
                width: '100%',
                dropdownParent: $('#gstForm').closest('.st-card-bd')
            });
    }

    function submitGst(e) {
        e.preventDefault();
        var fd = new FormData(document.getElementById('gstForm'));
        if (gstEditId) {
            fd.set('_method', 'PUT');
        }
        var url = gstEditId ? '{{ url("settings/gst") }}/' + gstEditId : '{{ route("settings.gst.store") }}';
        fetch(url, {
                method: 'POST',
                body: fd,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) {
                return r.json();
            })
            .then(function(r) {
                document.getElementById('gstTableBody').innerHTML = r.rows;
                document.querySelector('[data-tab="tab-gst"] .ni-badge').textContent = r.count;
                document.getElementById('gstCountBadge').textContent = r.count;
                resetGstForm();
                toastr.success(r.success);
            });
        return false;
    }

    function deleteGst(id) {
        var name = document.querySelector('#gst-row-' + id + ' td strong').textContent;
        document.getElementById('globalDelName').textContent = name;
        document.getElementById('globalDelType').textContent = 'GST';
        window._gstDelOrigConfirm = window.globalDelConfirm;
        window.globalDelConfirm = function() {
            clearInterval(globalDelTimer);
            $('#globalDeleteModal').modal('hide');
            var fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('_method', 'DELETE');
            fetch('{{ url("settings/gst") }}/' + id, {
                        method: 'POST',
                        body: fd,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                .then(function(r) {
                    return r.json();
                })
                .then(function(r) {
                    document.getElementById('gstTableBody').innerHTML = r.rows;
                    document.querySelector('[data-tab="tab-gst"] .ni-badge').textContent = r.count;
                    document.getElementById('gstCountBadge').textContent = r.count;
                    toastr.success(r.success);
                });
            window.globalDelConfirm = window._gstDelOrigConfirm;
        };
        $('#globalDeleteModal').on('hidden.bs.modal.gst', function() {
            window.globalDelConfirm = window._gstDelOrigConfirm;
            $(this).off('hidden.bs.modal.gst');
        });
        $('#globalDeleteModal').modal('show');
    }

    function openGstEdit(id, name, type, percentage) {
        gstEditId = id;
        document.getElementById('gstName').value = name;
        document.getElementById('gstType').value = type;
        if (typeof $ !== 'undefined') {
            var $el = $('#gstType');
            if (!$el.data('select2')) $el.select2({
                width: '100%',
                dropdownParent: $('#gstForm').closest('.st-card-bd')
            });
            $el.trigger('change');
        }
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
        if (typeof $ !== 'undefined') {
            var $el = $('#gstType');
            if ($el.data('select2')) {
                $el.val('').trigger('change');
            } else {
                $el.select2({
                    width: '100%',
                    dropdownParent: $('#gstForm').closest('.st-card-bd')
                });
            }
        }
        document.getElementById('gstFormTitle').textContent = 'Add GST';
        document.getElementById('gstFormIcon').className = 'ti-plus';
        document.getElementById('gstFormIcon').style.background = '#dcfce7';
        document.getElementById('gstFormIcon').style.color = '#16a34a';
        document.getElementById('gstSubmitBtn').innerHTML = '<i class="ti-plus mr-1"></i> Add GST';
        document.getElementById('gstCancelBtn').style.display = 'none';
    }

    function validateNumber(input) {
        input.value = input.value.replace(/[^0-9]/g, '').substring(0, 10);
    }

    var waConnected = {{ $baileysConnected ? 'true' : 'false' }};

    function setWaConnectedState(isConnected, number) {
        var qrSection = document.getElementById('wa-qr-section');
        var settingsSection = document.getElementById('wa-settings-section');
        var usageSection = document.getElementById('wa-usage-section');
        var statusText = document.getElementById('wa-status-text');
        if (!qrSection || !settingsSection || !usageSection || !statusText) return;

        if (isConnected && waQrTimer) {
            clearTimeout(waQrTimer);
            waQrTimer = null;
        }

        waConnected = isConnected;
        if (isConnected) {
            qrSection.style.display = 'none';
            settingsSection.style.display = '';
            usageSection.style.display = '';
            statusText.innerHTML = '<span style="color:#22c55e;">✅ Connected</span>';
            if (number) {
                var numField = document.getElementById('whatsapp_connected_number');
                var statusNum = document.getElementById('wa-status-number');
                if (numField) {
                    numField.value = number;
                    numField.readOnly = true;
                    numField.style.background = '#f1f5f9';
                    numField.style.color = '#6b7280';
                    numField.style.cursor = 'not-allowed';
                }
                if (statusNum) {
                    statusNum.style.display = '';
                    statusNum.innerHTML = '— Number: <strong>+91 ' + number + '</strong>';
                }
            }
        } else {
            qrSection.style.display = '';
            settingsSection.style.display = 'none';
            usageSection.style.display = 'none';
            statusText.innerHTML = '<span style="color:#ef4444;">❌ Not connected</span>';
            refreshWaQr();
        }
    }

    function pollWaStatus() {
        if (typeof setInterval === 'undefined') return;
        setInterval(function() {
            fetch('{{ route("settings.whatsapp.qr") }}')
                .then(function(r) {
                    return r.json();
                })
                .then(function(data) {
                    if (data.connected && !waConnected) {
                        setWaConnectedState(true, data.number);
                    } else if (!data.connected && waConnected) {
                        setWaConnectedState(false);
                    }
                })
                .catch(function() {});
        }, 5000);
    }

    var waQrTimer = null;
    var waQrRetries = 0;

    function refreshWaQr() {
        var container = document.getElementById('wa-qr-container');
        var reconnectBtn = document.getElementById('wa-reconnect-btn');
        if (!container) {
            if (waQrTimer) {
                clearTimeout(waQrTimer);
                waQrTimer = null;
            }
            return;
        }
        container.innerHTML = '<div style="padding:80px 0;color:#f59e0b;font-size:13px;">Loading QR...</div>';

        fetch('{{ route("settings.whatsapp.qr") }}')
            .then(function(r) {
                return r.json();
            })
            .then(function(data) {
                if (data.connected) {
                    waQrRetries = 0;
                    setWaConnectedState(true, data.number);
                } else if (data.dataUrl) {
                    waQrRetries = 0;
                    if (reconnectBtn) reconnectBtn.style.display = 'none';
                    container.innerHTML = '<img src="' + data.dataUrl + '" width="220" height="220" alt="QR Code" style="image-rendering:pixelated;border-radius:4px;">';
                } else {
                    waQrRetries++;
                    if (waQrRetries >= 10) {
                        if (waQrTimer) { clearTimeout(waQrTimer); waQrTimer = null; }
                        container.innerHTML =
                            '<div style="padding:30px 0;text-align:center;">' +
                            '<div style="font-size:36px;margin-bottom:10px;opacity:0.4;">&#128241;</div>' +
                            '<div style="font-size:14px;font-weight:600;color:#64748b;margin-bottom:4px;">WhatsApp Service Unavailable</div>' +
                            '<div style="font-size:12px;color:#94a3b8;max-width:280px;margin:0 auto;margin-bottom:14px;">The Baileys service is not responding.</div>' +
                            '<button onclick="retryWaQr()" style="padding:8px 20px;border-radius:8px;border:none;background:#0ea5e9;color:#fff;font-size:12px;font-weight:700;cursor:pointer;">Try Again</button>' +
                            '</div>';
                        if (reconnectBtn) reconnectBtn.style.display = 'none';
                        return;
                    }
                    var dots = '';
                    for (var i = 0; i < (waQrRetries % 4); i++) dots += '.';
                    container.innerHTML =
                        '<div style="padding:30px 0;text-align:center;">' +
                        '<div style="font-size:36px;margin-bottom:10px;opacity:0.4;">&#9203;</div>' +
                        '<div style="font-size:14px;font-weight:600;color:#f59e0b;margin-bottom:4px;">Starting' + dots + '</div>' +
                        '<div style="font-size:12px;color:#94a3b8;">' + (data.message || 'Connecting to WhatsApp') + '</div>' +
                        '</div>';
                    if (waQrRetries >= 3 && reconnectBtn) {
                        reconnectBtn.style.display = '';
                    }
                    waQrTimer = setTimeout(refreshWaQr, 5000);
                }
            })
            .catch(function() {
                waQrRetries++;
                if (waQrRetries >= 5) {
                    if (waQrTimer) { clearTimeout(waQrTimer); waQrTimer = null; }
                    container.innerHTML =
                        '<div style="padding:30px 0;text-align:center;">' +
                        '<div style="font-size:36px;margin-bottom:10px;opacity:0.4;">&#128241;</div>' +
                        '<div style="font-size:14px;font-weight:600;color:#64748b;margin-bottom:4px;">WhatsApp Service Unavailable</div>' +
                        '<div style="font-size:12px;color:#94a3b8;max-width:280px;margin:0 auto;margin-bottom:14px;">The WhatsApp Baileys service is not reachable.</div>' +
                        '<button onclick="retryWaQr()" style="padding:8px 20px;border-radius:8px;border:none;background:#0ea5e9;color:#fff;font-size:12px;font-weight:700;cursor:pointer;">Try Again</button>' +
                        '</div>';
                    if (reconnectBtn) reconnectBtn.style.display = 'none';
                    return;
                }
                container.innerHTML =
                    '<div style="padding:30px 0;text-align:center;">' +
                    '<div style="font-size:36px;margin-bottom:10px;opacity:0.4;">&#9203;</div>' +
                    '<div style="font-size:14px;font-weight:600;color:#f59e0b;margin-bottom:4px;">Connecting...</div>' +
                    '<div style="font-size:12px;color:#94a3b8;">Checking WhatsApp service</div>' +
                    '</div>';
                waQrTimer = setTimeout(refreshWaQr, 5000);
            });
    }

    function retryWaQr() {
        waQrRetries = 0;
        refreshWaQr();
    }

    function disconnectWhatsApp() {
        if (!confirm('Disconnect WhatsApp?')) return;
        fetch('/settings/whatsapp/disconnect', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(function(r) {
                return r.json();
            })
            .then(function() {
                var statusNum = document.getElementById('wa-status-number');
                if (statusNum) statusNum.style.display = 'none';
                setWaConnectedState(false);
            });
    }

    function reconnectWhatsApp() {
        var container = document.getElementById('wa-qr-container');
        if (container) container.innerHTML = '<div style="padding:80px 0;color:#f59e0b;font-size:13px;">Reconnecting...</div>';
        var reconnectBtn = document.getElementById('wa-reconnect-btn');
        if (reconnectBtn) reconnectBtn.style.display = 'none';
        waQrRetries = 0;

        fetch('http://localhost:3001/reconnect', {
                method: 'POST'
            })
            .then(function() {
                setTimeout(refreshWaQr, 3000);
            })
            .catch(function() {
                if (container) container.innerHTML = '<div style="padding:60px 0;color:#ef4444;font-size:13px;">Could not reach Baileys service</div>';
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        pollWaStatus();
        if (!waConnected) refreshWaQr();
    });

    document.querySelectorAll('input[name="whatsapp_connected_number"]').forEach(function(el) {
        el.addEventListener('input', function() {
            validateNumber(this);
        });
    });



    // ── WhatsApp Reminder Contacts CRUD ───────────────────────────────────────
    var waContactUrl = '{{ url("/whatsapp-contacts") }}';
    var waContactCount = {{ $waContacts->count() }};

    document.getElementById('waContactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var name = document.getElementById('wacName');
        var mobile = document.getElementById('wacMobile');
        var valid = true;
        name.style.borderColor = '';
        mobile.style.borderColor = '';

        if (!name.value.trim() || name.value.trim().length > 100) {
            name.style.borderColor = '#ef4444';
            valid = false;
        }
        if (!/^[6-9][0-9]{9}$/.test(mobile.value.trim())) {
            mobile.style.borderColor = '#ef4444';
            valid = false;
        }
        if (!valid) return;

        var editId = document.getElementById('wacCancelBtn').dataset.editId || '';
        var url = editId ? waContactUrl + '/' + editId : waContactUrl;
        var fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('name', name.value.trim());
        fd.append('mobile', mobile.value.trim());
        if (editId) fd.append('_method', 'PUT');

        fetch(url, {
                method: 'POST',
                body: fd,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) {
                return r.json();
            })
            .then(function() {
                var msg = editId ? 'Contact updated successfully.' : 'Contact added successfully.';
                if (typeof toastr !== 'undefined') toastr['success'](msg, 'Success');
                setTimeout(function() {
                    location.reload();
                }, 500);
            })
            .catch(function() {
                if (typeof toastr !== 'undefined') toastr['error']('Something went wrong.', 'Error');
            });
    });

    function editWaContact(id, name, mobile) {
        document.getElementById('wacName').value = name;
        document.getElementById('wacMobile').value = mobile;
        document.getElementById('wacSubmitBtn').innerHTML = '<i class="ti-check mr-1"></i> Update';
        document.getElementById('wacCancelBtn').style.display = '';
        document.getElementById('wacCancelBtn').dataset.editId = id;
        document.getElementById('wacName').focus();
    }

    function resetWaContactForm() {
        document.getElementById('wacName').value = '';
        document.getElementById('wacMobile').value = '';
        document.getElementById('wacSubmitBtn').innerHTML = '<i class="ti-plus mr-1"></i> Add';
        document.getElementById('wacCancelBtn').style.display = 'none';
        delete document.getElementById('wacCancelBtn').dataset.editId;
        document.getElementById('wacName').style.borderColor = '';
        document.getElementById('wacMobile').style.borderColor = '';
    }

    function toggleWaContact(id) {
        var fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fetch(waContactUrl + '/' + id + '/toggle', {
            method: 'POST',
            body: fd,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function() {
            if (typeof toastr !== 'undefined') toastr['success']('Status updated.', 'Updated');
            setTimeout(function() {
                location.reload();
            }, 500);
        });
    }

    function deleteWaContact(id, name) {
        if (typeof toastr === 'undefined') {
            if (!confirm('Delete "' + name + '"?')) return;
            doDeleteWaContact(id);
            return;
        }
        toastr.clear();
        var $toast = toastr['warning'](
            'Delete "' + name + '"? This cannot be undone.',
            'Confirm Delete', {
                timeOut: 0,
                extendedTimeOut: 0,
                closeButton: true,
                tapToDismiss: false,
                positionClass: 'toast-top-center',
                toastClass: 'wa-delete-toast',
            }
        );
        var $btnRow = $('<div style="margin-top:10px;display:flex;gap:8px;justify-content:center;"></div>');
        var $yes = $('<button class="btn btn-sm" style="background:#ef4444;color:#fff;border:none;padding:4px 18px;border-radius:4px;font-size:12px;font-weight:600;cursor:pointer;">Yes, Delete</button>');
        var $no = $('<button class="btn btn-sm" style="background:#e2e8f0;color:#475569;border:none;padding:4px 18px;border-radius:4px;font-size:12px;font-weight:600;cursor:pointer;">Cancel</button>');
        $yes.on('click', function() {
            toastr.clear($toast);
            doDeleteWaContact(id);
        });
        $no.on('click', function() {
            toastr.clear($toast);
        });
        $btnRow.append($yes).append($no);
        $toast.append($btnRow);
    }

    function doDeleteWaContact(id) {
        var fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('_method', 'DELETE');
        fetch(waContactUrl + '/' + id, {
            method: 'POST',
            body: fd,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function(r) {
            return r.json();
        }).then(function() {
            if (typeof toastr !== 'undefined') toastr['success']('Contact deleted successfully.', 'Deleted');
            setTimeout(function() {
                location.reload();
            }, 500);
        }).catch(function() {
            if (typeof toastr !== 'undefined') toastr['error']('Failed to delete contact.', 'Error');
        });
    }

    document.getElementById('wacMobile').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
    });

    // ── Message Template CRUD ────────────────────────────────────────────
    var mtEditId = null;

    function submitMsgTemplate(e) {
        e.preventDefault();
        var fd = new FormData(document.getElementById('mtForm'));
        if (mtEditId) fd.set('_method', 'PUT');
        var url = mtEditId ? '{{ url("settings/message-template") }}/' + mtEditId : '{{ route("settings.message-template.store") }}';
        fetch(url, {
                method: 'POST',
                body: fd,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) { return r.json(); })
            .then(function(r) {
                document.getElementById('mtTableBody').innerHTML = r.rows;
                document.querySelector('[data-tab="tab-message-template"] .ni-badge').textContent = r.count;
                document.getElementById('mtCountBadge').textContent = r.count;
                resetMsgTemplateForm();
                refreshVrcTemplateOptions();
                toastr.success(r.success);
            });
        return false;
    }

    function openMsgTemplateEdit(id, name, message, status) {
        mtEditId = id;
        document.getElementById('mtName').value = name;
        document.getElementById('mtMessage').value = message;
        document.getElementById('mtFormTitle').textContent = 'Edit Template';
        document.getElementById('mtFormIcon').innerHTML = '<i class="ti-pencil"></i>';
        document.getElementById('mtFormIcon').style.background = '#eef2ff';
        document.getElementById('mtFormIcon').style.color = '#6366f1';
        document.getElementById('mtSubmitBtn').innerHTML = '<i class="ti-check mr-1"></i> Update Template';
        document.getElementById('mtCancelBtn').style.display = 'flex';
    }

    function resetMsgTemplateForm() {
        mtEditId = null;
        document.getElementById('mtForm').reset();
        document.getElementById('mtMethod').value = 'POST';
        document.getElementById('mtFormTitle').textContent = 'Add Template';
        document.getElementById('mtFormIcon').innerHTML = '<i class="ti-plus"></i>';
        document.getElementById('mtFormIcon').style.background = '#dcfce7';
        document.getElementById('mtFormIcon').style.color = '#16a34a';
        document.getElementById('mtSubmitBtn').innerHTML = '<i class="ti-plus mr-1"></i> Add Template';
        document.getElementById('mtCancelBtn').style.display = 'none';
    }

    function toggleMsgTemplate(id) {
        var fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fetch('{{ url("settings/message-template") }}/' + id + '/toggle', {
                method: 'POST',
                body: fd,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) { return r.json(); })
            .then(function(r) {
                // Update the toggle button in-place without page reload
                var btn = document.querySelector('#mt-row-' + id + ' .toggle-status');
                if (btn) {
                    var on = r.status;
                    btn.textContent = on ? 'ON' : 'OFF';
                    btn.style.background = on ? '#dcfce7' : '#fee2e2';
                    btn.style.color = on ? '#16a34a' : '#ef4444';
                    btn.title = on ? 'Active' : 'Inactive';
                }
                // Sync available templates in Vehicle Reminder form
                refreshVrcTemplateOptions();
                toastr.success(r.success);
            });
    }

    function deleteMsgTemplate(id) {
        var name = document.querySelector('#mt-row-' + id + ' td strong').textContent;
        if (!confirm('Delete template "' + name + '"?')) return;
        var fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('_method', 'DELETE');
        fetch('{{ url("settings/message-template") }}/' + id, {
                method: 'POST',
                body: fd,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
            .then(function(res) {
                if (!res.ok) {
                    toastr.error(res.data.error || 'Cannot delete this template.', 'Error');
                    return;
                }
                document.getElementById('mtTableBody').innerHTML = res.data.rows;
                document.querySelector('[data-tab="tab-message-template"] .ni-badge').textContent = res.data.count;
                document.getElementById('mtCountBadge').textContent = res.data.count;
                refreshVrcTemplateOptions();
                toastr.success(res.data.success);
            });
    }

    /**
     * Reload the active template options in the Vehicle Reminder form
     * after a template is added, edited, or deleted.
     */
    function refreshVrcTemplateOptions() {
        var sel = document.getElementById('vrc_template');
        if (!sel) return;
        var currentVal = sel.value;
        // Collect active template names/ids from the message template table
        var options = '<option value="">— Select Template —</option>';
        document.querySelectorAll('#mtTableBody tr[id^="mt-row-"]').forEach(function(row) {
            var btn = row.querySelector('.toggle-status');
            if (!btn || btn.textContent.trim() !== 'ON') return; // skip inactive
            var idMatch = row.id.match(/mt-row-(\d+)/);
            var nameEl = row.querySelector('td strong');
            if (!idMatch || !nameEl) return;
            options += '<option value="' + idMatch[1] + '">' + nameEl.textContent + '</option>';
        });
        // Destroy select2 and rebuild
        if (typeof $ !== 'undefined' && $('#vrc_template').data('select2')) {
            $('#vrc_template').select2('destroy');
        }
        sel.innerHTML = options;
        // Restore previous selection if still valid
        if (currentVal) sel.value = currentVal;
        // Re-init select2
        if (typeof $ !== 'undefined') {
            $('#vrc_template').select2({ width: '100%', allowClear: true, placeholder: '— Select Template —' });
        }
    }

    // ── Vehicle Reminder: 12-hour time input ────────────────────────────
    (function() {
        var timeInput = document.getElementById('vrc_time');
        if (!timeInput) return;

        timeInput.addEventListener('input', function() {
            var v = this.value.replace(/[^0-9APMapm]/g, '').toUpperCase();
            if (v.length > 2 && v.length <= 4) {
                v = v.substring(0, 2) + ':' + v.substring(2);
            }
            if (v.length > 5) v = v.substring(0, 5);
            if (v.length === 5 && !v.includes(':')) {
                v = v.substring(0, 2) + ':' + v.substring(2);
            }
            this.value = v;
        });

        timeInput.addEventListener('blur', function() {
            var v = this.value.trim();
            if (!v) return;
            var parts = v.match(/^(\d{1,2}):(\d{2})\s*(AM|PM|am|pm)?$/);
            if (parts) {
                var h = parseInt(parts[1]);
                var m = parts[2];
                var ap = (parts[3] || '').toUpperCase();
                if (!ap) {
                    ap = h >= 12 ? 'PM' : 'AM';
                    if (h === 0) h = 12;
                    else if (h > 12) h = h - 12;
                }
                this.value = h + ':' + m + ' ' + ap;
            }
        });
    })();

    // ── Vehicle Reminder Config CRUD (AJAX) ─────────────────────────────
    var vrcUrl = '{{ url("/settings/vehicle-reminder") }}';
    var vrcEditId = null;

    function submitVrc(e) {
        e.preventDefault();
        var fd = new FormData(document.getElementById('vrcForm'));
        if (vrcEditId) fd.set('_method', 'PUT');
        var url = vrcEditId ? vrcUrl + '/' + vrcEditId : vrcUrl;
        fetch(url, {
                method: 'POST',
                body: fd,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) {
                return r.json().then(function(data) {
                    return { ok: r.ok, status: r.status, data: data };
                });
            })
            .then(function(res) {
                if (!res.ok) {
                    var msg = res.data.message || 'Validation failed.';
                    if (res.data.errors) {
                        msg = Object.values(res.data.errors).flat().join('\n');
                    }
                    toastr.error(msg, 'Error');
                    return;
                }
                document.getElementById('vrcTableBody').innerHTML = res.data.rows;
                document.querySelector('[data-tab="tab-vehicle-reminder"] .ni-badge').textContent = res.data.count;
                document.getElementById('vrcCountBadge').textContent = res.data.count;
                resetVrcForm();
                toastr.success(res.data.success);
            })
            .catch(function(err) {
                console.error('VRC submit error:', err);
                toastr.error('Something went wrong.', 'Error');
            });
        return false;
    }

    function editVrc(id, template, duration, time) {
        vrcEditId = id;
        document.getElementById('vrcMethod').value = 'PUT';
        $('#vrc_template').val(template).trigger('change');
        $('#vrc_duration').val(duration).trigger('change');
        document.getElementById('vrc_time').value = time || '';
        document.getElementById('vrcFormTitle').textContent = 'Edit Reminder Config';
        document.getElementById('vrcFormIcon').innerHTML = '<i class="ti-pencil"></i>';
        document.getElementById('vrcFormIcon').style.background = '#eef2ff';
        document.getElementById('vrcFormIcon').style.color = '#6366f1';
        document.getElementById('vrcSubmitBtn').innerHTML = '<i class="ti-check mr-1"></i> Update Config';
        document.getElementById('vrcCancelBtn').style.display = 'flex';
    }

    function resetVrcForm() {
        vrcEditId = null;
        document.getElementById('vrcForm').reset();
        document.getElementById('vrcMethod').value = 'POST';
        $('#vrc_template').val(null).trigger('change');
        $('#vrc_duration').val(null).trigger('change');
        document.getElementById('vrcFormTitle').textContent = 'Add Reminder Config';
        document.getElementById('vrcFormIcon').innerHTML = '<i class="ti-plus"></i>';
        document.getElementById('vrcFormIcon').style.background = '#dcfce7';
        document.getElementById('vrcFormIcon').style.color = '#16a34a';
        document.getElementById('vrcSubmitBtn').innerHTML = '<i class="ti-plus mr-1"></i> Add Config';
        document.getElementById('vrcCancelBtn').style.display = 'none';
    }

    function deleteVrc(id) {
        if (!confirm('Delete this config?')) return;
        var fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('_method', 'DELETE');
        fetch(vrcUrl + '/' + id, {
                method: 'POST',
                body: fd,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) {
                return r.json().then(function(data) {
                    return { ok: r.ok, status: r.status, data: data };
                });
            })
            .then(function(res) {
                if (!res.ok) {
                    toastr.error(res.data.message || 'Failed to delete.', 'Error');
                    return;
                }
                document.getElementById('vrcTableBody').innerHTML = res.data.rows;
                document.querySelector('[data-tab="tab-vehicle-reminder"] .ni-badge').textContent = res.data.count;
                document.getElementById('vrcCountBadge').textContent = res.data.count;
                toastr.success(res.data.success);
            })
            .catch(function(err) {
                console.error('VRC delete error:', err);
                toastr.error('Failed to delete config.', 'Error');
            });
    }
</script>
@endpush