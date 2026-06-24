@extends('layouts.app')

@section('content')

@php
$billingTypes = [
'fixed'=>'Fixed','per_tonne'=>'Per Tonne','per_kg'=>'Per Kg','per_km'=>'Per Km',
'per_trip'=>'Per Trip','per_day'=>'Per Day','per_hour'=>'Per Hour',
'per_litre'=>'Per Litre','per_bag'=>'Per Bag',
];
$loadTypes = ['Full Load','Part Load','Container','Tanker','Refrigerated','Flatbed','Other'];
$selectedBilling = old('billing_type','fixed');
@endphp

<style>
    /* ── New Trip Page ──────────────────────────────────────────────────── */
    .nt-page {
        background: #f4f6fb;
    }

    /* Gradient header */
    .nt-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        padding: 14px 22px;
        color: #fff;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .nt-header::before {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 160px;
        height: 160px;
        background: rgba(255, 255, 255, .07);
        border-radius: 50%;
    }

    .nt-header h4 {
        font-size: 17px;
        font-weight: 800;
        margin: 0 0 2px;
    }

    .nt-header .sub {
        font-size: 12px;
        opacity: .8;
    }

    /* Step indicator */
    .nt-steps {
        display: flex;
        align-items: center;
        gap: 0;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        padding: 0;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .nt-step {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        cursor: pointer;
        transition: all .2s;
        border-right: 1px solid #f0f2f7;
        position: relative;
    }

    .nt-step:last-child {
        border-right: none;
    }

    .nt-step .step-num {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
        background: #f0f2f7;
        color: #b0bac9;
        transition: all .2s;
    }

    .nt-step .step-label {
        font-size: 12px;
        font-weight: 600;
        color: #8a94a6;
        line-height: 1.3;
    }

    .nt-step .step-sub {
        font-size: 10px;
        color: #b0bac9;
    }

    .nt-step.active .step-num {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
        box-shadow: 0 3px 10px rgba(102, 126, 234, .4);
    }

    .nt-step.active .step-label {
        color: #1a2340;
    }

    .nt-step.done .step-num {
        background: #48bb78;
        color: #fff;
    }

    .nt-step.done .step-label {
        color: #38a169;
    }

    .nt-step:hover:not(.active) {
        background: #f8faff;
    }

    /* Section card */
    .nt-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        margin-bottom: 20px;
        overflow: hidden;
        display: none;
    }

    .nt-card.active {
        display: block;
    }

    .nt-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 20px;
        border-bottom: 1px solid #f0f2f7;
        background: #fafbff;
    }

    .nt-card-header .ch-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .nt-card-header h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #1a2340;
    }

    .nt-card-header .ch-sub {
        font-size: 11px;
        color: #8a94a6;
        margin: 0;
    }

    .nt-card-body {
        padding: 22px;
    }

    /* Form fields */
    .nt-label {
        font-size: 12px;
        font-weight: 700;
        color: #596579;
        margin-bottom: 6px;
        display: block;
    }

    .nt-label .req {
        color: #e53e3e;
    }

    .nt-input {
        min-height: 44px;
        border-color: #d7dce5;
        color: #303549;
        font-size: 14px;
        border-radius: 8px;
        transition: border-color .2s, box-shadow .2s;
    }

    .nt-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, .12);
    }

    .nt-input-group {
        display: flex;
        align-items: stretch;
    }

    .nt-addon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        padding: 0 10px;
        border: 1px solid #d7dce5;
        background: #f8f9fa;
        color: #495057;
        font-weight: 600;
        min-height: 44px;
    }

    .nt-addon:first-child {
        border-right: 0;
        border-radius: 8px 0 0 8px;
    }

    .nt-addon:last-child {
        border-left: 0;
        border-radius: 0 8px 8px 0;
    }

    .nt-input-group .nt-input {
        border-radius: 0;
        flex: 1;
    }

    .nt-input-group .nt-addon:first-child+.nt-input {
        border-radius: 0 8px 8px 0;
    }

    .nt-input-group .nt-input:not(:last-child) {
        border-radius: 8px 0 0 8px;
    }

    .nt-card-body .row>[class*="col-"] {
        margin-bottom: 18px;
    }

    .nt-card .form-group {
        margin-bottom: 1rem;
    }

    /* Billing type pills */
    .billing-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .billing-pill input {
        display: none;
    }

    .billing-pill span {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 6px 14px;
        border-radius: 8px;
        background: #e9ecef;
        color: #6c7890;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        border: 1.5px solid transparent;
        transition: all .15s;
    }

    .billing-pill input:checked+span {
        background: #eaf1ff;
        border-color: #667eea;
        color: #4338ca;
        box-shadow: 0 2px 8px rgba(102, 126, 234, .2);
    }

    /* Section divider */
    .nt-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 20px 0 14px;
        padding: 10px 12px;
        border-left: 3px solid #667eea;
        background: #f7f8fc;
        color: #303549;
        font-size: 13px;
        font-weight: 800;
        border-radius: 0 6px 6px 0;
    }

    .nt-section-title:first-child {
        margin-top: 0;
    }

    /* Navigation buttons */
    .nt-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 22px;
        border-top: 1px solid #f0f2f7;
        background: #fafbff;
    }

    .nt-btn-prev {
        background: #f4f6fb;
        color: #596579;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 9px 20px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
    }

    .nt-btn-prev:hover {
        background: #e2e8f0;
    }

    .nt-btn-next {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 9px 24px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 3px 12px rgba(102, 126, 234, .35);
        transition: all .15s;
    }

    .nt-btn-next:hover {
        box-shadow: 0 5px 18px rgba(102, 126, 234, .5);
        transform: translateY(-1px);
    }

    .nt-btn-submit {
        background: linear-gradient(135deg, #48bb78, #38a169);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 9px 24px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 3px 12px rgba(72, 187, 120, .35);
        transition: all .15s;
    }

    .nt-btn-submit:hover {
        box-shadow: 0 5px 18px rgba(72, 187, 120, .5);
        transform: translateY(-1px);
    }

    /* Progress bar */
    .nt-progress {
        height: 4px;
        background: #e2e8f0;
        border-radius: 2px;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .nt-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 2px;
        transition: width .4s ease;
    }

    /* Select2 overrides */
    .nt-card .select2-container {
        width: 100% !important;
    }
    .nt-card .select2-container--default .select2-selection--single {
        min-height: 44px !important;
        height: 44px !important;
        border-color: #d7dce5 !important;
        border-radius: 8px !important;
    }
    .nt-card .select2-container--default.select2-container--focus .select2-selection--single,
    .nt-card .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #667eea !important;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, .12) !important;
    }

    /* Payment status pills */
    .pay-status-pills {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .pay-status-pill input {
        display: none;
    }

    .pay-status-pill span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        border: 2px solid transparent;
        font-size: 13px;
        font-weight: 600;
        background: #f4f6fb;
        color: #8a94a6;
        transition: all .15s;
    }

    .pay-status-pill input:checked+span {
        box-shadow: 0 2px 8px rgba(0, 0, 0, .1);
    }

    .pay-status-pill.psp-pending input:checked+span {
        background: #fff5f5;
        border-color: #fc8181;
        color: #e53e3e;
    }

    .pay-status-pill.psp-partial input:checked+span {
        background: #fffbeb;
        border-color: #f6ad55;
        color: #d97706;
    }

    .pay-status-pill.psp-completed input:checked+span {
        background: #f0fff4;
        border-color: #48bb78;
        color: #38a169;
    }

    @media (max-width: 767.98px) {
        .nt-steps {
            flex-direction: column;
        }

        .nt-step {
            border-right: none;
            border-bottom: 1px solid #f0f2f7;
        }

        .nt-step:last-child {
            border-bottom: none;
        }

        .nt-header {
            padding: 10px 14px;
        }
    }

    /* ── Vehicle / Driver Type Toggle (New Trip) ─────────────────── */
    .nt-type-toggle {
        display: inline-flex;
        gap: 0;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #d7dce5;
        background: #f7f8fc;
    }

    .ntt-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 700;
        color: #8a94a6;
        background: transparent;
        border: none;
        border-right: 1px solid #d7dce5;
        cursor: pointer;
        transition: all .18s;
        font-family: inherit;
        white-space: nowrap;
    }

    .ntt-btn:last-child {
        border-right: none;
    }

    .ntt-btn:hover:not(.active) {
        background: #eef0f7;
        color: #4a5568;
    }

    .ntt-btn.active {
        background: #fff;
        color: #1a2340;
        box-shadow: inset 0 -2px 0 #667eea;
    }

    .ntt-btn.active[data-vtype="own"] {
        box-shadow: inset 0 -2px 0 #0d6efd;
        color: #0d47c2;
    }

    .ntt-btn.active[data-vtype="market"] {
        box-shadow: inset 0 -2px 0 #d97706;
        color: #92400e;
    }

    .ntt-btn.active[data-dtype="own"] {
        box-shadow: inset 0 -2px 0 #38a169;
        color: #276749;
    }

    .ntt-btn.active[data-dtype="rental"] {
        box-shadow: inset 0 -2px 0 #d97706;
        color: #92400e;
    }

    .ntt-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    .ntt-dot.own {
        background: #38a169;
    }

    .ntt-dot.market {
        background: #d97706;
    }

    .ntt-dot.rental {
        background: #d97706;
    }

    .ntt-driver-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px 4px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .ntt-driver-badge.type-own {
        background: #f0fff4;
        color: #276749;
        border: 1px solid #9ae6b4;
    }

    .ntt-driver-badge.type-rental {
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fcd34d;
    }


    /* ── Select2 search/results overrides ────────────────────────────── */
    .select2-results__option { padding: 6px 12px !important; }
    .select2-search--dropdown .select2-search__field {
        border-color: #d7dce5 !important;
        border-radius: 6px !important;
        padding: 6px 10px !important;
        font-size: 13px !important;
    }

    /* ── Distance auto-calculation spinner ───────────────────────────── */
    @keyframes locSpin { to { transform: rotate(360deg); } }
    @keyframes plSpin   { to { transform: rotate(360deg); } }

    /* ── Document upload drop zone ────────────────────────────────── */
    .doc-drop-zone {
        border: 2px dashed #c0d4f5;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all .2s;
        background: #f8fbff;
        min-height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .doc-drop-zone:hover { border-color: #667eea; background: #eef2ff; }
    .doc-drop-zone.drag-over { border-color: #667eea; background: #eef2ff; transform: scale(1.01); }
    .doc-drop-zone.has-file { border-color: #48bb78; background: #f0fff4; text-align: left; justify-content: flex-start; padding: 12px 16px; }
</style>

<div class="pcoded-inner-content nt-page">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">

                {{-- Header --}}
                <div class="nt-header">
                    <div class="row align-items-center">
                        <div class="col-md-8" style="position:relative;z-index:1;">
                            <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:4px 14px;font-size:12px;font-weight:700;letter-spacing:.5px;margin-bottom:8px;">
                                <i class="ti-plus"></i> New Trip
                            </div>
                            <h4>Create New Trip</h4>
                            <div class="sub">Fill in all sections to create a complete trip record.</div>
                        </div>
                        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
                            <a href="{{ route('trip') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:8px 18px;font-weight:600;">
                                <i class="ti-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                @include('partials.flash')

                {{-- Progress bar --}}
                <div class="nt-progress">
                    <div class="nt-progress-fill" id="ntProgressFill" style="width:20%;"></div>
                </div>

                {{-- Step indicators --}}
                <div class="nt-steps" id="ntSteps">
                    <div class="nt-step active" data-step="1">
                        <div class="step-num">1</div>
                        <div>
                            <div class="step-label">Basic Info</div>
                            <div class="step-sub">Trip & Route</div>
                        </div>
                    </div>
                    <div class="nt-step" data-step="2">
                        <div class="step-num">2</div>
                        <div>
                            <div class="step-label">Vehicle & Driver</div>
                            <div class="step-sub">Allocation</div>
                        </div>
                    </div>
                    <div class="nt-step" data-step="3">
                        <div class="step-num">3</div>
                        <div>
                            <div class="step-label">Financials</div>
                            <div class="step-sub">Amounts & Expenses</div>
                        </div>
                    </div>
                    <div class="nt-step" data-step="4">
                        <div class="step-num">4</div>
                        <div>
                            <div class="step-label">Review</div>
                            <div class="step-sub">Confirm & Save</div>
                        </div>
                    </div>
                </div>

                <form id="newTripForm" action="{{ route('trip.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="trip_no" value="">
                    <input type="hidden" name="status" value="planned">
                    <input type="hidden" name="workflow_status" value="pending">
                    <input type="hidden" name="payment_status" value="pending">
                    <input type="hidden" name="collected_amount" value="0">
                    <input type="hidden" name="remarks" value="">

                    {{-- ── STEP 1: Basic Trip Information ───────────────────────────── --}}
                    <div class="nt-card active" id="step1">
                        <div class="nt-card-header">
                            <div class="ch-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-clipboard"></i></div>
                            <div>
                                <h6>Basic Trip Information</h6>
                                <p class="ch-sub">Trip ID, dates, customer, route and material details</p>
                            </div>
                        </div>
                        <div class="nt-card-body">

                            <div class="nt-section-title"><i class="ti-id-badge"></i> Trip Identity</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="nt-label">Booking Date</label>
                                        <input type="date" name="booking_date" class="form-control nt-input @error('booking_date') is-invalid @enderror"
                                            value="{{ old('booking_date', date('Y-m-d')) }}">
                                        @error('booking_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="nt-label">Trip Start Date <span class="req">*</span></label>
                                        <input type="date" name="trip_date" class="form-control nt-input @error('trip_date') is-invalid @enderror"
                                            value="{{ old('trip_date', date('Y-m-d')) }}" required>
                                        @error('trip_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="nt-label">Expected Delivery Date</label>
                                        <input type="date" name="expected_delivery_date" class="form-control nt-input @error('expected_delivery_date') is-invalid @enderror"
                                            value="{{ old('expected_delivery_date') }}">
                                        @error('expected_delivery_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="nt-section-title"><i class="ti-layers"></i> Customer (Party)</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="nt-label">Customer / Party <span class="req">*</span></label>
                                        <select name="party_id" class="form-control nt-input select2-nt @error('party_id') is-invalid @enderror" required>
                                            <option value="">Select Customer / Party</option>
                                            @foreach($parties as $party)
                                            <option value="{{ $party->id }}" {{ old('party_id') == $party->id ? 'selected' : '' }}>
                                                {{ $party->company_name ?: $party->name }}{{ $party->phone ? ' — '.$party->phone : '' }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('party_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="nt-label">LR Number</label>
                                        <div class="nt-input-group">
                                            <input type="text" name="lr_no" id="lrNumberInput" class="form-control nt-input @error('lr_no') is-invalid @enderror"
                                                value="{{ old('lr_no') }}" placeholder="Auto-generated">
                                            <button type="button" id="lrGenerateBtn" class="nt-addon" style="cursor:pointer;background:#667eea;color:#fff;border-color:#667eea;font-weight:700;" title="Generate new LR Number">
                                                <i class="ti-reload" style="font-size:13px;"></i>
                                            </button>
                                        </div>
                                        @error('lr_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="nt-label">Load Type</label>
                                        <select name="load_type" class="form-control select2-nt nt-input @error('load_type') is-invalid @enderror">
                                            <option value="">Select Load Type</option>
                                            @foreach($loadTypes as $lt)
                                            <option value="{{ $lt }}" {{ old('load_type') == $lt ? 'selected' : '' }}>{{ $lt }}</option>
                                            @endforeach
                                        </select>
                                        @error('load_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="nt-section-title"><i class="ti-map-alt"></i> Route Details</div>

                            <input type="hidden" name="from_location" id="fromLocationVal" value="{{ old('from_location') }}">
                            <input type="hidden" name="to_location"   id="toLocationVal"   value="{{ old('to_location') }}">

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="nt-label">From Place <span class="req">*</span></label>
                                        <select id="fromPlaceSelect" class="form-control nt-input" style="width:100%;" required>
                                            <option value="">Search state, district or city…</option>
                                            @if(old('from_location'))
                                                <option value="{{ old('from_location') }}" selected>{{ old('from_location') }}</option>
                                            @endif
                                        </select>
                                        @error('from_location')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="nt-label">To Place <span class="req">*</span></label>
                                        <select id="toPlaceSelect" class="form-control nt-input" style="width:100%;" required>
                                            <option value="">Search state, district or city…</option>
                                            @if(old('to_location'))
                                                <option value="{{ old('to_location') }}" selected>{{ old('to_location') }}</option>
                                            @endif
                                        </select>
                                        @error('to_location')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="nt-label">
                                            Distance (KM)
                                            <span id="distAutoTag" style="display:none;background:#eef2ff;color:#667eea;font-size:9px;padding:1px 6px;border-radius:10px;font-weight:700;margin-left:4px;">AUTO</span>
                                        </label>
                                        <div class="nt-input-group">
                                            <input type="number" step="0.1" min="0" name="distance_km" id="distKmInput"
                                                class="form-control nt-input @error('distance_km') is-invalid @enderror"
                                                value="{{ old('distance_km') }}" placeholder="0">
                                            <button type="button" id="recalcDistBtn" class="nt-addon"
                                                style="cursor:pointer;background:#667eea;color:#fff;border-color:#667eea;min-width:38px;"
                                                title="Recalculate distance">
                                                <i class="ti-reload" id="recalcDistIcon" style="font-size:12px;"></i>
                                            </button>
                                        </div>
                                        <small id="distNote" style="font-size:10px;color:#8a94a6;margin-top:3px;display:none;"></small>
                                        @error('distance_km')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="nt-section-title"><i class="ti-package"></i> Material Details</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="nt-label">Material Description</label>
                                        <input type="text" name="material" class="form-control nt-input @error('material') is-invalid @enderror"
                                            value="{{ old('material') }}" placeholder="e.g. Steel Rods, Rice Bags, Cement">
                                        @error('material')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="nt-label">Quantity</label>
                                        <input type="number" step="0.01" min="0" name="quantity" class="form-control nt-input @error('quantity') is-invalid @enderror"
                                            value="{{ old('quantity') }}" placeholder="0.00">
                                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="nt-label">Loading Date</label>
                                        <input type="date" name="loading_date" class="form-control nt-input @error('loading_date') is-invalid @enderror"
                                            value="{{ old('loading_date') }}">
                                        @error('loading_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="nt-label">Document / Invoice Number</label>
                                        <div class="nt-input-group">
                                            <span class="nt-addon"><i class="ti-file" style="font-size:13px;"></i></span>
                                            <input type="text" name="document_number" class="form-control nt-input @error('document_number') is-invalid @enderror"
                                                value="{{ old('document_number') }}" placeholder="e.g. INV-2024-001">
                                        </div>
                                        @error('document_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Document Upload --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="nt-label"><i class="ti-files mr-1"></i> Upload Document <span style="color:#8a94a6;font-weight:400;">(Invoice / LR / POD / E-Way Bill)</span></label>
                                        <div class="doc-drop-zone" id="docDropZone" onclick="document.getElementById('docFileInput').click()">
                                            <input type="file" id="docFileInput" name="document_file"
                                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display:none;"
                                                onchange="handleDocUpload(this)">
                                            <div id="docDropContent">
                                                <i class="ti-cloud-up" style="font-size:30px;color:#c0d4f5;display:block;margin-bottom:8px;"></i>
                                                <div style="font-size:13px;font-weight:600;color:#667eea;">Click or drag a file here</div>
                                                <div style="font-size:11px;color:#b0bac9;margin-top:3px;">PDF, JPG, PNG, DOC — max 10 MB</div>
                                            </div>
                                            <div id="docFilePreview" style="display:none;">
                                                <div style="display:flex;align-items:center;gap:10px;">
                                                    <div id="docFileIcon" style="width:40px;height:40px;border-radius:8px;background:#eef2ff;color:#667eea;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
                                                        <i class="ti-file"></i>
                                                    </div>
                                                    <div>
                                                        <div id="docFileName" style="font-size:13px;font-weight:700;color:#1a2340;"></div>
                                                        <div id="docFileSize" style="font-size:11px;color:#8a94a6;"></div>
                                                    </div>
                                                    <button type="button" onclick="clearDocUpload(event)"
                                                        style="margin-left:auto;background:#fff5f5;color:#e53e3e;border:none;border-radius:6px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:13px;">
                                                        <i class="ti-close"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @error('document_file')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="nt-nav">
                            <a href="{{ route('trip') }}" class="nt-btn-prev"><i class="ti-close mr-1"></i> Cancel</a>
                            <button type="button" class="nt-btn-next" onclick="ntGoTo(2)">Next: Vehicle & Driver <i class="ti-arrow-right ml-1"></i></button>
                        </div>
                    </div>

                    {{-- ── STEP 2: Vehicle & Driver Allocation ──────────────────────── --}}
                    <div class="nt-card" id="step2">
                        <div class="nt-card-header">
                            <div class="ch-icon" style="background:#fff8e1;color:#d97706;"><i class="ti-truck"></i></div>
                            <div>
                                <h6>Vehicle & Driver Allocation</h6>
                                <p class="ch-sub">Assign vehicle, driver, and transport vendor</p>
                            </div>
                        </div>
                        <div class="nt-card-body">

                            <div class="nt-section-title"><i class="ti-truck"></i> Vehicle Details</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="nt-label">Truck / Vehicle <span class="req">*</span></label>

                                        <select name="vehicle_id" id="ntVehicleIdSelect"
                                            class="form-control nt-input @error('vehicle_id') is-invalid @enderror" required>
                                            <option value="">Eg: KA 02 Q 1234</option>
                                            @foreach($vehicles as $v)
                                            @php $ts = $v->trip_status ?? null; @endphp
                                            <option value="{{ $v->id }}"
                                                data-vtype="{{ strtolower($v->owner_type ?? 'own') }}"
                                                data-vstatus="{{ strtolower($v->status ?? 'available') }}"
                                                data-trip-status="{{ $ts ?? '' }}"
                                                data-supplier-id="{{ $v->supplier_id ?? '' }}"
                                                {{ $ts === 'running' ? 'disabled' : '' }}
                                                {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                                                {{ $v->vehicle_number }}{{ $v->vehicle_name ? ' — '.$v->vehicle_name : '' }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="nt-label">Start KMs Reading</label>
                                        <div class="nt-input-group">
                                            <input type="number" step="0.01" min="0" name="start_kms_reading" class="form-control nt-input @error('start_kms_reading') is-invalid @enderror"
                                                value="{{ old('start_kms_reading') }}" placeholder="0">
                                            <span class="nt-addon">KM</span>
                                        </div>
                                        @error('start_kms_reading')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="nt-label">Unloading Date</label>
                                        <input type="date" name="unloading_date" class="form-control nt-input @error('unloading_date') is-invalid @enderror"
                                            value="{{ old('unloading_date') }}">
                                        @error('unloading_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="nt-section-title"><i class="ti-user"></i> Driver Details</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="nt-label">Assign Driver</label>

                                        <select name="driver_id" id="ntDriverIdSelect"
                                            class="form-control nt-input @error('driver_id') is-invalid @enderror">
                                            <option value="">— No Driver —</option>
                                            @foreach($drivers as $d)
                                            @php $dts = $d->trip_status ?? null; @endphp
                                            <option value="{{ $d->id }}"
                                                data-dtype="{{ $d->driver_type ?? 'own' }}"
                                                data-mobile="{{ $d->mobile }}"
                                                data-license="{{ $d->license_number }}"
                                                data-trip-status="{{ $dts ?? '' }}"
                                                {{ $dts === 'running' ? 'disabled' : '' }}
                                                {{ old('driver_id') == $d->id ? 'selected' : '' }}>
                                                {{ $d->name }}{{ $d->mobile ? ' — '.$d->mobile : '' }}
                                            </option>
                                            @endforeach
                                        </select>

                                        {{-- Selected Driver Badge --}}
                                        <div id="ntDriverBadgeWrap" class="mt-2" style="display:none;">
                                            <span id="ntDriverBadge" class="ntt-driver-badge"></span>
                                        </div>
                                        @error('driver_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="nt-label">Driver Mobile</label>
                                        <input type="text" id="driverMobileDisplay" class="form-control nt-input" placeholder="Auto-filled from driver" readonly style="background:#f8f9fa;">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="nt-label">Transport Vendor</label>
                                        <select name="supplier_id" id="ntSupplierSelect" class="form-control nt-input select2-nt @error('supplier_id') is-invalid @enderror">
                                            <option value="">Select Vendor (Optional)</option>
                                            @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="nt-nav">
                            <button type="button" class="nt-btn-prev" onclick="ntGoTo(1)"><i class="ti-arrow-left mr-1"></i> Back</button>
                            <button type="button" class="nt-btn-next" onclick="ntGoTo(3)">Next: Financials <i class="ti-arrow-right ml-1"></i></button>
                        </div>
                    </div>

                    {{-- ── STEP 3: Financial Details ─────────────────────────────────── --}}
                    <div class="nt-card" id="step3">
                        <div class="nt-card-header">
                            <div class="ch-icon" style="background:#fef3c7;color:#d97706;"><i class="ti-wallet"></i></div>
                            <div>
                                <h6>Financial Details</h6>
                                <p class="ch-sub">Trip amount, advances, and expense breakdown</p>
                            </div>
                        </div>
                        <div class="nt-card-body">

                            <div class="nt-section-title"><i class="ti-money"></i> Billing & Freight</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="nt-label">Billing Type <span class="req">*</span></label>
                                        <div class="billing-pills">
                                            @foreach($billingTypes as $val => $lbl)
                                            <label class="billing-pill">
                                                <input type="radio" name="billing_type" value="{{ $val }}" {{ $selectedBilling === $val ? 'checked' : '' }}>
                                                <span>{{ $lbl }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                        @error('billing_type')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="nt-label">Trip / Freight Amount <span class="req">*</span></label>
                                        <div class="nt-input-group">
                                            <span class="nt-addon">₹</span>
                                            <input type="number" step="0.01" min="0" name="freight_amount" id="freightAmt"
                                                class="form-control nt-input @error('freight_amount') is-invalid @enderror"
                                                value="{{ old('freight_amount') }}" placeholder="0.00" required>
                                        </div>
                                        @error('freight_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            {{-- hidden fields to keep form submission intact --}}
                            <input type="hidden" name="advance_amount" value="0">
                            <input type="hidden" name="diesel_advance" value="0">
                            <input type="hidden" name="driver_bata" value="0">
                            <input type="hidden" name="toll_charges" value="0">
                            <input type="hidden" name="loading_charges" value="0">
                            <input type="hidden" name="unloading_charges" value="0">
                            <input type="hidden" name="other_expenses" value="0">
                            <input type="hidden" name="expense_notes" value="">

                        </div>
                        <div class="nt-nav">
                            <button type="button" class="nt-btn-prev" onclick="ntGoTo(2)"><i class="ti-arrow-left mr-1"></i> Back</button>
                            <button type="button" class="nt-btn-next" onclick="ntGoTo(4)">Review Trip <i class="ti-arrow-right ml-1"></i></button>
                        </div>
                    </div>

                    {{-- ── STEP 4: Review & Submit ───────────────────────────────────── --}}
                    <div class="nt-card" id="step4">
                        <div class="nt-card-header">
                            <div class="ch-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-check-box"></i></div>
                            <div>
                                <h6>Review & Confirm</h6>
                                <p class="ch-sub">Review all details before saving the trip</p>
                            </div>
                        </div>
                        <div class="nt-card-body">
                            <div id="reviewContent">
                                <div class="text-center py-4" style="color:#b0bac9;">
                                    <i class="ti-reload" style="font-size:28px;display:block;margin-bottom:8px;"></i>
                                    Loading review...
                                </div>
                            </div>
                        </div>
                        <div class="nt-nav">
                            <button type="button" class="nt-btn-prev" onclick="ntGoTo(3)"><i class="ti-arrow-left mr-1"></i> Back</button>
                            <button type="submit" class="nt-btn-submit" id="submitTripBtn">
                                <i class="ti-save mr-1"></i> Save Trip
                            </button>
                        </div>
                    </div>

                </form>{{-- /newTripForm --}}

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {

        /* ── Step navigation ──────────────────────────────────────────── */
        var currentStep = 1;
        var totalSteps = 4;
        var SESSION_KEY = 'nt_active_step';

        window.ntGoTo = function(step) {
            if (step < 1 || step > totalSteps) return;

            // Mark previous steps as done
            for (var i = 1; i < step; i++) {
                $('#ntSteps .nt-step[data-step="' + i + '"]').removeClass('active').addClass('done')
                    .find('.step-num').html('<i class="ti-check" style="font-size:12px;"></i>');
            }
            // Mark current step as active
            $('#ntSteps .nt-step[data-step="' + step + '"]').removeClass('done').addClass('active')
                .find('.step-num').text(step);
            // Mark future steps as pending
            for (var j = step + 1; j <= totalSteps; j++) {
                $('#ntSteps .nt-step[data-step="' + j + '"]').removeClass('active done')
                    .find('.step-num').text(j);
            }

            // Show/hide cards
            $('.nt-card').removeClass('active');
            $('#step' + step).addClass('active');

            // Update progress bar
            var pct = Math.round((step / totalSteps) * 100);
            $('#ntProgressFill').css('width', pct + '%');

            currentStep = step;

            // Persist so refresh restores this step
            try { sessionStorage.setItem(SESSION_KEY, step); } catch(e) {}

            // Build review on step 4
            if (step === 4) buildReview();

            // Scroll to top
            $('html, body').animate({
                scrollTop: $('.nt-page').offset().top - 20
            }, 200);
        };

        // Always start at step 1 on a fresh page load (new trip)
        try { sessionStorage.removeItem(SESSION_KEY); } catch(e) {}

        // Clear session step on form submit so /trip/add always opens fresh
        $('#newTripForm').on('submit', function () {
            try { sessionStorage.removeItem(SESSION_KEY); } catch(e) {}
        });

        /* ── Select2 helpers ───────────────────────────────────────────── */
        function syncSelectAllOption($select) {
            var $allOption = $select.find('option[data-select-all]');
            if (!$allOption.length) return;

            var $realOptions = $select.find('option').not('[data-select-all]');
            var total = $realOptions.length;
            var selected = $realOptions.filter(':selected').length;
            $allOption.prop('selected', total > 0 && selected === total);
        }

        function selectAllOptions($select) {
            $select.find('option').not('[data-select-all]').prop('selected', true);
            $select.trigger('change.select2');
        }

        /* ── Init standard Select2 fields (.select2-nt) — excludes vehicle/driver/supplier which have dedicated inits ── */
        $('.select2-nt').not('#ntVehicleIdSelect, #ntDriverIdSelect, #ntSupplierSelect').each(function () {
            var $el = $(this);
            if ($el.hasClass('select2-hidden-accessible')) return;
            $el.select2({
                width: '100%',
                allowClear: true,
                placeholder: $el.find('option:first').text() || 'Select…'
            });
        });

        /* ══════════════════════════════════════════════════════════════
           VEHICLE SELECT — rich dropdown with Own/Rental + status badge
        ══════════════════════════════════════════════════════════════ */
        // Inject CSS so badge colors survive Select2's highlighted-row override
        $('<style>')
            .text(
                '.veh-opt-own   { background:#dcfce7 !important; color:#16a34a !important; border:1px solid #16a34a !important; }' +
                '.veh-opt-rental{ background:#fee2e2 !important; color:#dc2626 !important; border:1px solid #dc2626 !important; }' +
                '.veh-opt-run   { background:#dbeafe !important; color:#1e40af !important; }' +
                '.veh-opt-plan  { background:#fef9c3 !important; color:#854d0e !important; }' +
                '.veh-opt-avail { background:#dcfce7 !important; color:#166534 !important; }' +
                '.veh-opt-badge { display:inline-flex;align-items:center;gap:4px;padding:2px 9px;border-radius:20px;font-size:10px;font-weight:700;white-space:nowrap; }' +
                '.veh-opt-dot   { width:6px;height:6px;border-radius:50%;display:inline-block; }'
            )
            .appendTo('head');

        function fmtVehicleResult(option) {
            if (!option.id) return $('<span style="color:#b0bac9;font-size:13px;">Search or select a vehicle…</span>');
            var el         = option.element;
            var vtype      = ($(el).data('vtype')       || 'own').toLowerCase();
            var tripStatus = ($(el).data('trip-status') || '').toLowerCase();
            var isOwn      = vtype !== 'rental' && vtype !== 'market';
            var isRunning  = tripStatus === 'running';
            var isPlanned  = tripStatus === 'planned';

            /* Owner badge class — Own=green, Rental=red */
            var ownerClass = isOwn ? 'veh-opt-own' : 'veh-opt-rental';
            var tLabel     = isOwn ? 'Own' : 'Rental';

            /* Status badge class — Running=blue, Planned=yellow, Available=green */
            var statusClass, sDotColor, sLabel;
            if (isRunning) {
                statusClass = 'veh-opt-run';   sDotColor = '#2563eb'; sLabel = 'Running';
            } else if (isPlanned) {
                statusClass = 'veh-opt-plan';  sDotColor = '#ca8a04'; sLabel = 'Planned';
            } else {
                statusClass = 'veh-opt-avail'; sDotColor = '#16a34a'; sLabel = 'Available';
            }

            var rawReg   = (option.text || '').split(' — ')[0].trim();
            var namePart = option.text.indexOf(' — ') !== -1 ? option.text.split(' — ').slice(1).join(' — ') : '';
            var opacity  = isRunning ? 'opacity:0.6;' : '';
            var note     = isRunning
                ? '<div style="font-size:10px;color:#1e40af;margin-top:2px;"><i class="ti-info-alt" style="margin-right:3px;"></i>On a running trip — cannot select</div>'
                : (isPlanned ? '<div style="font-size:10px;color:#854d0e;margin-top:2px;"><i class="ti-info-alt" style="margin-right:3px;"></i>Assigned to a planned trip</div>' : '');

            return $(
                '<div style="display:flex;align-items:center;gap:10px;padding:8px 4px;' + opacity + '">' +
                    '<div style="width:38px;height:38px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;" class="' + ownerClass + '">' +
                        '<i class="ti-truck" style="font-size:15px;"></i>' +
                    '</div>' +
                    '<div style="flex:1;min-width:0;">' +
                        '<div style="font-size:13px;font-weight:700;color:#1a2340;">' + rawReg + '</div>' +
                        (namePart ? '<div style="font-size:11px;color:#8a94a6;margin-top:1px;">' + namePart + '</div>' : '') +
                        note +
                    '</div>' +
                    '<div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;">' +
                        '<span class="veh-opt-badge ' + ownerClass + '">' + tLabel + '</span>' +
                        '<span class="veh-opt-badge ' + statusClass + '">' +
                            '<span class="veh-opt-dot" style="background:' + sDotColor + ';"></span>' + sLabel +
                        '</span>' +
                    '</div>' +
                '</div>'
            );
        }

        function fmtVehicleSelection(option) {
            if (!option.id) return $('<span style="color:#b0bac9;">Search or select a vehicle…</span>');
            var rawReg = (option.text || '').split(' — ')[0].trim();
            return $('<span style="font-size:13px;font-weight:700;color:#1a2340;">' + rawReg + '</span>');
        }

        $('#ntVehicleIdSelect').select2({
            width:             '100%',
            allowClear:        true,
            placeholder:       'Search or select a vehicle…',
            templateResult:    fmtVehicleResult,
            templateSelection: fmtVehicleSelection,
            escapeMarkup:      function(m) { return m; },
        });

        /* ── Auto-fill Transport Vendor when a Rental vehicle is selected ── */
        $('#ntVehicleIdSelect').on('change', function () {
            var $opt       = $(this).find('option:selected');
            var vtype      = ($opt.data('vtype') || '').toLowerCase();
            var supplierId = $opt.data('supplier-id') || '';

            if (vtype === 'rental' && supplierId) {
                // Set supplier select to the vehicle's linked supplier
                $('#ntSupplierSelect').val(supplierId).trigger('change.select2');
            } else if (vtype !== 'rental') {
                // Own vehicle — clear the supplier field
                $('#ntSupplierSelect').val('').trigger('change.select2');
            }
        });

        /* ══════════════════════════════════════════════════════════════
           DRIVER SELECT2
        ══════════════════════════════════════════════════════════════ */
        function fmtDriverResult(option) {
            if (!option.id) return $('<span style="color:#b0bac9;font-size:13px;">— No Driver —</span>');
            var el         = option.element;
            var dtype      = ($(el).data('dtype') || 'own').toLowerCase();
            var tripStatus = ($(el).data('trip-status') || '').toLowerCase();
            var mobile     = $(el).data('mobile') || '';
            var isOwn      = dtype !== 'rental';
            var isRunning  = tripStatus === 'running';
            var isPlanned  = tripStatus === 'planned';
            var dColor     = isOwn ? '#16a34a' : '#d97706';
            var dBg        = isOwn ? '#dcfce7' : '#fff8ed';
            var dLabel     = isOwn ? 'Own'     : 'Rental';
            var name       = (option.text || '').split(' — ')[0].trim();
            var initials   = name.split(' ').slice(0,2).map(function(w){ return w.charAt(0); }).join('').toUpperCase();

            var statusClass, sDotColor, sLabel;
            if (isRunning) {
                statusClass = 'veh-opt-run';   sDotColor = '#2563eb'; sLabel = 'Running';
            } else if (isPlanned) {
                statusClass = 'veh-opt-plan';  sDotColor = '#ca8a04'; sLabel = 'Planned';
            } else {
                statusClass = 'veh-opt-avail'; sDotColor = '#16a34a'; sLabel = 'Available';
            }

            var opacity = isRunning ? 'opacity:0.6;' : '';
            var note    = isRunning
                ? '<div style="font-size:10px;color:#1e40af;margin-top:2px;"><i class="ti-info-alt" style="margin-right:3px;"></i>On a running trip — cannot select</div>'
                : (isPlanned ? '<div style="font-size:10px;color:#854d0e;margin-top:2px;"><i class="ti-info-alt" style="margin-right:3px;"></i>Assigned to a planned trip</div>' : '');

            return $(
                '<div style="display:flex;align-items:center;gap:10px;padding:7px 4px;' + opacity + '">' +
                    '<div style="width:36px;height:36px;border-radius:50%;background:' + dBg + ';border:2px solid ' + dColor + ';display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
                        '<span style="font-size:12px;font-weight:800;color:' + dColor + ';">' + initials + '</span>' +
                    '</div>' +
                    '<div style="flex:1;min-width:0;">' +
                        '<div style="font-size:13px;font-weight:700;color:#1a2340;">' + name + '</div>' +
                        (mobile ? '<div style="font-size:11px;color:#8a94a6;">' + mobile + '</div>' : '') +
                        note +
                    '</div>' +
                    '<div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;">' +
                        '<span class="veh-opt-badge" style="background:' + dBg + ';color:' + dColor + ';border:1px solid ' + dColor + ';">' + dLabel + '</span>' +
                        '<span class="veh-opt-badge ' + statusClass + '">' +
                            '<span class="veh-opt-dot" style="background:' + sDotColor + ';"></span>' + sLabel +
                        '</span>' +
                    '</div>' +
                '</div>'
            );
        }
        function fmtDriverSelection(option) {
            if (!option.id) return $('<span style="color:#b0bac9;">— No Driver —</span>');
            var name = (option.text || '').split(' — ')[0].trim();
            return $('<span style="font-size:13px;font-weight:700;color:#1a2340;">' + name + '</span>');
        }
        $('#ntDriverIdSelect').select2({
            width:             '100%',
            allowClear:        true,
            placeholder:       '— No Driver —',
            templateResult:    fmtDriverResult,
            templateSelection: fmtDriverSelection,
            escapeMarkup:      function(m) { return m; },
        });

        /* ── Driver mobile auto-fill ── */
        $('#ntDriverIdSelect').on('change', function () {
            var mobile = $(this).find('option:selected').data('mobile') || '';
            $('#driverMobileDisplay').val(mobile);
        });

        /* ── Supplier (Transport Vendor) Select2 ── */
        $('#ntSupplierSelect').select2({
            width:       '100%',
            allowClear:  true,
            placeholder: 'Select Vendor (Optional)',
        });

        /* ══════════════════════════════════════════════════════════════
           BUILD REVIEW (Step 4)
        ══════════════════════════════════════════════════════════════ */
        function buildReview() {
            var rows = [];

            function val(name) {
                var $el = $('[name="' + name + '"]');
                if ($el.is('select')) {
                    var txt = $el.find('option:selected').text().trim();
                    return txt && txt !== $el.find('option:first').text() ? txt : '—';
                }
                return $el.val() || '—';
            }
            function selText(id) {
                var $s = $(id);
                if (!$s.length) return '—';
                var txt = $s.find('option:selected').text().trim();
                return txt && txt !== $s.find('option:first').text() ? txt : '—';
            }

            // Step 1 — Basic Info
            rows.push({ section: '📋 Basic Info' });
            rows.push({ label: 'Booking Date',    value: val('booking_date') });
            rows.push({ label: 'Trip Start Date', value: val('trip_date') });
            rows.push({ label: 'LR Number',       value: val('lr_no') });
            rows.push({ label: 'Party / Customer',value: selText('[name="party_id"]') });
            rows.push({ label: 'From',            value: $('#fromLocationVal').val() || '—' });
            rows.push({ label: 'To',              value: $('#toLocationVal').val()   || '—' });
            rows.push({ label: 'Distance (KM)',   value: val('distance_km') });
            rows.push({ label: 'Material',        value: val('material') });
            rows.push({ label: 'Load Type',       value: selText('[name="load_type"]') });

            // Step 2 — Vehicle & Driver
            rows.push({ section: '🚛 Vehicle & Driver' });
            rows.push({ label: 'Vehicle',          value: selText('#ntVehicleIdSelect') });
            rows.push({ label: 'Driver',           value: selText('#ntDriverIdSelect') });
            rows.push({ label: 'Transport Vendor', value: selText('#ntSupplierSelect') });
            rows.push({ label: 'Start KMs',        value: val('start_kms_reading') });

            // Step 3 — Financials
            rows.push({ section: '💰 Financials' });
            rows.push({ label: 'Billing Type',    value: $('[name="billing_type"]:checked').val() || '—' });
            rows.push({ label: 'Freight Amount',  value: val('freight_amount') !== '—' ? '₹' + parseFloat(val('freight_amount')).toLocaleString('en-IN') : '—' });

            // Build HTML
            var html = '<div style="display:grid;grid-template-columns:1fr 1fr;gap:0;">';
            var inGrid = false;
            rows.forEach(function(r) {
                if (r.section) {
                    if (inGrid) { html += '</div>'; inGrid = false; }
                    html += '<div style="grid-column:1/-1;font-size:12px;font-weight:800;color:#667eea;text-transform:uppercase;letter-spacing:.6px;padding:14px 0 8px;border-bottom:1px solid #eef2ff;margin-bottom:4px;">' + r.section + '</div>';
                    inGrid = false;
                } else {
                    html +=
                        '<div style="padding:8px 10px 8px 0;border-bottom:1px solid #f4f6fb;">' +
                            '<div style="font-size:11px;color:#8a94a6;font-weight:600;">' + r.label + '</div>' +
                            '<div style="font-size:13px;font-weight:700;color:#1a2340;margin-top:2px;">' + r.value + '</div>' +
                        '</div>';
                }
            });
            html += '</div>';

            $('#reviewContent').html(html);
        }

        /* ── From / To Place — Select2 with live Nominatim search ─────────
         * Format shown in dropdown : State → District → Locality
         * e.g.  Tamil Nadu → Chennai → Tharamani
         * Stored value (hidden input): "Tharamani, Chennai, Tamil Nadu"
         * ─────────────────────────────────────────────────────────────── */
        (function () {
            var NOM_URL = 'https://nominatim.openstreetmap.org/search';

            /* ── Extract 3-level hierarchy from Nominatim address object ── */
            function parseAddr(r) {
                var addr = r.address || {};

                /* Level 1 — State */
                var state    = addr.state || '';

                /* Level 2 — District / City */
                var district = addr.state_district || addr.county || addr.city || addr.town || '';

                /* Level 3 — Locality (most specific first) */
                var locality = addr.suburb       ||
                               addr.neighbourhood||
                               addr.quarter      ||
                               addr.village      ||
                               addr.town         ||
                               addr.city_district||
                               '';

                /* If locality == district collapse it (avoid "Chennai → Chennai") */
                if (locality && locality.toLowerCase() === district.toLowerCase()) {
                    locality = '';
                }

                /* If we have no locality, promote city/town to locality */
                if (!locality) {
                    locality = addr.city || addr.town || (r.display_name || '').split(',')[0].trim();
                    /* and let district be state_district or county */
                    district = addr.state_district || addr.county || '';
                }

                /* Fallback — use first segment of display_name */
                if (!locality) {
                    locality = (r.display_name || '').split(',')[0].trim();
                }

                return {
                    locality : locality,
                    district : district,
                    state    : state
                };
            }

            /* ── Dropdown result card ─────────────────────────────────── */
            function fmtResult(item) {
                if (item.loading) {
                    return $('<div style="padding:6px 4px;color:#8a94a6;font-size:12px;"><i class="ti-reload" style="margin-right:6px;"></i>Searching…</div>');
                }
                if (!item.id) {
                    return $('<div style="padding:6px 4px;color:#b0bac9;font-size:12px;">' + item.text + '</div>');
                }

                var loc  = item.locality  || '';
                var dist = item.district  || '';
                var st   = item.state     || '';

                /* Breadcrumb: Tamil Nadu → Chennai → Tharamani */
                var crumbs = [];
                if (st)   crumbs.push(st);
                if (dist && dist !== st)   crumbs.push(dist);
                if (loc  && loc  !== dist) crumbs.push(loc);

                var breadcrumb = crumbs.join(
                    '<i class="ti-angle-right" style="font-size:8px;color:#b0bac9;margin:0 4px;vertical-align:middle;"></i>'
                );

                /* Type badge (suburb / town / village / city …) */
                var typeLabel = (item.osm_type || '').replace(/_/g,' ');
                var typeColor = '#8a94a6';
                if (typeLabel === 'suburb' || typeLabel === 'neighbourhood') { typeColor = '#667eea'; }
                else if (typeLabel === 'city' || typeLabel === 'town')       { typeColor = '#38a169'; }
                else if (typeLabel === 'village')                            { typeColor = '#d97706'; }

                return $(
                    '<div style="padding:6px 2px;">' +
                        /* Main locality name */
                        '<div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">' +
                            '<div style="font-size:13px;font-weight:700;color:#1a2340;line-height:1.2;">' + loc + '</div>' +
                            (typeLabel ? '<span style="font-size:9px;font-weight:700;color:' + typeColor + ';background:rgba(0,0,0,.04);padding:2px 7px;border-radius:8px;white-space:nowrap;text-transform:capitalize;">' + typeLabel + '</span>' : '') +
                        '</div>' +
                        /* Breadcrumb trail */
                        '<div style="font-size:11px;color:#8a94a6;margin-top:3px;line-height:1.3;">' + breadcrumb + '</div>' +
                    '</div>'
                );
            }

            /* ── Selected value shown in the input box ────────────────── */
            function fmtSelection(item) {
                if (!item.id) return item.text;
                /* Show: Tharamani, Chennai, Tamil Nadu */
                var parts = [];
                if (item.locality)                                    parts.push(item.locality);
                if (item.district && item.district !== item.locality) parts.push(item.district);
                if (item.state    && item.state    !== item.district) parts.push(item.state);
                return parts.length ? parts.join(', ') : item.text;
            }

            /* ── Init one place select ────────────────────────────────── */
            function initPlaceSelect(selector, hiddenId, placeholder) {
                var $sel     = $(selector);
                if (!$sel.length) return;
                var savedVal = $('#' + hiddenId).val() || '';

                $sel.select2({
                    width:              '100%',
                    placeholder:        placeholder,
                    allowClear:         true,
                    minimumInputLength: 2,
                    templateResult:     fmtResult,
                    templateSelection:  fmtSelection,
                    language: {
                        inputTooShort: function () { return 'Type at least 2 characters…'; },
                        noResults:     function () { return 'No places found. Try a different spelling.'; },
                        searching:     function () { return 'Searching India locations…'; }
                    },
                    ajax: {
                        url:      NOM_URL,
                        dataType: 'json',
                        delay:    350,
                        headers:  { 'Accept-Language': 'en' },
                        data: function (params) {
                            return {
                                q:              (params.term || '') + ', India',
                                format:         'json',
                                limit:          25,
                                countrycodes:   'in',
                                addressdetails: 1,
                                featuretype:    'settlement'
                            };
                        },
                        processResults: function (data) {
                            var seen  = {};
                            var items = [];
                            (data || []).forEach(function (r) {
                                var p = parseAddr(r);
                                if (!p.locality) return;

                                /* Unique key = locality + district + state */
                                var key = p.locality + '||' + p.district + '||' + p.state;
                                if (seen[key]) return;
                                seen[key] = 1;

                                /* Stored value: "Locality, District, State" */
                                var fullValue = [p.locality, p.district, p.state]
                                    .filter(Boolean).join(', ');

                                items.push({
                                    id       : key,
                                    text     : fullValue,   /* fallback / search text */
                                    locality : p.locality,
                                    district : p.district,
                                    state    : p.state,
                                    osm_type : r.type || r.addresstype || ''
                                });
                            });
                            return { results: items };
                        },
                        cache: true
                    }
                });

                /* ── Restore saved value on page reload / validation fail ── */
                if (savedVal) {
                    var sp       = savedVal.split(', ');
                    var loc      = sp[0] || savedVal;
                    var dist     = sp[1] || '';
                    var st       = sp.slice(2).join(', ') || '';
                    var optKey   = loc + '||' + dist + '||' + st;
                    var opt      = new Option(savedVal, optKey, true, true);
                    /* Attach extra data so fmtSelection works */
                    var optData  = { id: optKey, text: savedVal, locality: loc, district: dist, state: st, osm_type: '' };
                    $sel.append(opt).trigger('change.select2');
                    $sel.data('select2').selection.update([optData]);
                }

                /* ── Sync hidden input when user selects ── */
                $sel.on('select2:select', function (e) {
                    var d = e.params.data;
                    var parts = [];
                    if (d.locality)                                    parts.push(d.locality);
                    if (d.district && d.district !== d.locality)       parts.push(d.district);
                    if (d.state    && d.state    !== d.district)       parts.push(d.state);
                    var fullVal = parts.join(', ');
                    $('#' + hiddenId).val(fullVal);
                    scheduleDistCalc();
                });
                $sel.on('select2:clear', function () {
                    $('#' + hiddenId).val('');
                });
            }

            /* ── Distance calculation via Laravel backend ── */
            var _distTimer = null;

            function scheduleDistCalc() {
                clearTimeout(_distTimer);
                if ($('#fromLocationVal').val() && $('#toLocationVal').val()) {
                    _distTimer = setTimeout(calcDist, 800);
                }
            }

            function calcDist() {
                var from = $('#fromLocationVal').val();
                var to   = $('#toLocationVal').val();
                if (!from || !to) return;

                var $btn  = $('#recalcDistBtn');
                var $icon = $('#recalcDistIcon');
                $btn.prop('disabled', true);
                $icon.css({ animation: 'plSpin .7s linear infinite', display: 'inline-block' });
                $('#distNote').show().text('Calculating distance…');

                $.ajax({
                    url:      '{{ route("api.general.distance") }}',
                    method:   'GET',
                    dataType: 'json',
                    data:     { from: from, to: to },
                    timeout:  15000,
                    success:  function (r) {
                        $btn.prop('disabled', false);
                        $icon.css('animation', '');
                        if (r && r.km) {
                            $('#distKmInput').val(r.km);
                            $('#distAutoTag').show();
                            var src = r.source === 'google' ? 'Google Maps' : (r.source === 'geoapify' ? 'Geoapify' : r.source === 'ors' ? 'OpenRouteService' : 'OSRM routing');
                            $('#distNote').show().text('Road distance via ' + src + '. You can edit.');
                        } else {
                            $('#distNote').text('Could not calculate — enter distance manually.');
                        }
                    },
                    error: function () {
                        $btn.prop('disabled', false);
                        $icon.css('animation', '');
                        $('#distNote').text('Could not calculate — enter distance manually.');
                    }
                });
            }

            $('#recalcDistBtn').on('click', calcDist);

            /* Boot — wait for Select2 to be available */
            function bootLocationSelects() {
                if (typeof $.fn.select2 === 'undefined') {
                    setTimeout(bootLocationSelects, 100);
                    return;
                }
                initPlaceSelect('#fromPlaceSelect', 'fromLocationVal', 'Type to search origin (e.g. Chennai)');
                initPlaceSelect('#toPlaceSelect',   'toLocationVal',   'Type to search destination (e.g. Delhi)');
            }
            bootLocationSelects();

        }()); // end place-select IIFE

        /* Document File Upload Handler ────────────────────────────── */
        window.handleDocUpload = function(input) {
            if (!input.files || !input.files[0]) return;
            var file = input.files[0];
            var maxSize = 10 * 1024 * 1024; // 10MB
            if (file.size > maxSize) {
                alert('File too large. Maximum size is 10 MB.');
                input.value = '';
                return;
            }
            var icons = { pdf:'ti-file-text', jpg:'ti-image', jpeg:'ti-image', png:'ti-image', doc:'ti-files', docx:'ti-files' };
            var ext   = file.name.split('.').pop().toLowerCase();
            var icon  = icons[ext] || 'ti-file';
            var sizeStr = file.size > 1024*1024
                ? (file.size / 1024 / 1024).toFixed(1) + ' MB'
                : Math.round(file.size / 1024) + ' KB';

            $('#docFileName').text(file.name);
            $('#docFileSize').text(sizeStr);
            $('#docFileIcon').html('<i class="' + icon + '"></i>');
            $('#docDropContent').hide();
            $('#docFilePreview').show();
            $('#docDropZone').addClass('has-file').css('border-color','#48bb78');
        };

        window.clearDocUpload = function(e) {
            e.stopPropagation();
            $('#docFileInput').val('');
            $('#docDropContent').show();
            $('#docFilePreview').hide();
            $('#docDropZone').removeClass('has-file').css('border-color','');
        };

        // Drag-and-drop support
        var $dropZone = $('#docDropZone');
        $dropZone.on('dragover dragenter', function(e) {
            e.preventDefault(); e.stopPropagation();
            $(this).addClass('drag-over');
        }).on('dragleave dragend drop', function(e) {
            e.preventDefault(); e.stopPropagation();
            $(this).removeClass('drag-over');
        }).on('drop', function(e) {
            var files = e.originalEvent.dataTransfer.files;
            if (files.length) {
                $('#docFileInput')[0].files = files;
                handleDocUpload($('#docFileInput')[0]);
            }
        });

        /* ── LR Number Auto-Generation ────────────────────────────────── */
        function generateLRNumber() {
            var year = new Date().getFullYear();
            var lrNo = '';
            // Use AJAX to get server-side sequential number (LRN2026001 format)
            $.ajax({
                url: '{{ route("trip.generate-lr") }}',
                method: 'GET',
                async: false,
                success: function(res) { lrNo = res.lr_no; },
                error: function() {
                    // fallback: LRN + year + random 3-digit
                    lrNo = 'LRN' + year + String(Math.floor(Math.random() * 900) + 100);
                }
            });
            return lrNo;
        }

        // Auto-generate LR Number on page load if empty
        function initLRNumber() {
            var $lrInput = $('#lrNumberInput');
            if (!$lrInput.val() || $lrInput.val().trim() === '') {
                $lrInput.val(generateLRNumber());
            }
        }

        // Generate new LR Number on button click
        $('#lrGenerateBtn').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var originalHtml = $btn.html();
            $btn.html('<i class="ti-reload" style="font-size:13px;animation:sw-spin .6s linear infinite;display:inline-block;"></i>');
            $.ajax({
                url: '{{ route("trip.generate-lr") }}',
                method: 'GET',
                success: function(res) {
                    $('#lrNumberInput').val(res.lr_no).focus();
                    $btn.html('<i class="ti-check" style="font-size:13px;"></i>');
                    setTimeout(function() { $btn.html(originalHtml); }, 800);
                },
                error: function() { $btn.html(originalHtml); }
            });
        });

        // Initialize on document ready
        initLRNumber();
    });
</script>
@endpush