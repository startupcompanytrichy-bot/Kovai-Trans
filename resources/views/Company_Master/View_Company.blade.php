@extends('layouts.app')

@section('content')

<style>
/* ── View Company Page — Trip-style UI ────────────────────────────── */
.vc-page { background: #f4f6fb; }

.vc-header {
    background: linear-gradient(135deg, #1a2340 0%, #2d3a5e 60%, #667eea 100%);
    border-radius: 12px; padding: 14px 22px; color: #fff;
    margin-bottom: 20px; position: relative; overflow: hidden;
}
.vc-header::before { content:''; position:absolute; top:-40px; right:-40px; width:140px; height:140px; background:rgba(255,255,255,.05); border-radius:50%; }
.vc-header::after  { content:''; position:absolute; bottom:-30px; right:70px; width:90px; height:90px; background:rgba(102,126,234,.12); border-radius:50%; }
.vc-header .badge-tag {
    display:inline-flex; align-items:center; gap:6px;
    background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);
    border-radius:20px; padding:3px 10px; font-size:11px; font-weight:700; letter-spacing:.5px; margin-bottom:4px;
}
.vc-header h4 { font-size:17px; font-weight:800; margin:0 0 2px; position:relative;z-index:1; }
.vc-header .sub { font-size:12px; opacity:.75; position:relative;z-index:1; }
.vc-header .header-actions { display:flex; gap:8px; flex-wrap:wrap; }
.vc-header .btn-header {
    display:inline-flex; align-items:center; gap:6px;
    padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
    border:none; cursor:pointer; transition:all .2s; text-decoration:none;
}
.vc-header .btn-header-white { background:rgba(255,255,255,.18); color:#fff; border:1px solid rgba(255,255,255,.3); }
.vc-header .btn-header-white:hover { background:rgba(255,255,255,.3); color:#fff; }
.vc-header .btn-header-solid { background:#fff; color:#667eea; }
.vc-header .btn-header-solid:hover { background:#f0f4ff; }

/* ── Info summary strip ───────────────────────────────────────────── */
.vc-summary-strip {
    display:grid; grid-template-columns:repeat(5,1fr);
    gap:10px; margin-bottom:20px;
}
.vc-sum-item {
    background:#fff; border-radius:10px; padding:12px 14px;
    box-shadow:0 1px 6px rgba(0,0,0,.06);
    display:flex; align-items:center; gap:10px;
    border-left:3px solid transparent;
}
.vc-sum-item .vsi-icon { width:32px; height:32px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
.vc-sum-item .vsi-label { font-size:10px; font-weight:700; color:#8a94a6; text-transform:uppercase; letter-spacing:.3px; }
.vc-sum-item .vsi-value { font-size:13px; font-weight:700; color:#1a2340; word-break:break-all; }

/* ── Section card ─────────────────────────────────────────────────── */
.vc-card { background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:20px; overflow:hidden; }
.vc-card-header {
    display:flex; align-items:center; gap:10px;
    padding:14px 20px; border-bottom:1px solid #f0f2f7; background:#fafbff;
}
.vc-card-header .card-icon { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; }
.vc-card-header h6 { margin:0; font-size:13px; font-weight:700; color:#1a2340; }
.vc-card-header .card-subtitle { font-size:11px; color:#8a94a6; margin:0; }
.vc-card-body { padding:20px; }

/* ── Info rows ────────────────────────────────────────────────────── */
.info-row { display:flex; align-items:flex-start; padding:11px 0; border-bottom:1px solid #f4f6fb; }
.info-row:last-child { border-bottom:none; }
.info-row .ir-icon { width:30px; height:30px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; margin-right:12px; margin-top:1px; }
.info-row .ir-label { font-size:11px; color:#8a94a6; font-weight:600; text-transform:uppercase; letter-spacing:.3px; margin-bottom:2px; }
.info-row .ir-value { font-size:13px; color:#1a2340; font-weight:700; word-break:break-all; }

/* ── Bank detail card ─────────────────────────────────────────────── */
.bank-detail-card {
    background:linear-gradient(135deg,#f8faff,#eef2ff);
    border:1px solid #d0dcf5; border-radius:10px; padding:16px;
}
.bank-detail-row { display:flex; align-items:center; padding:8px 0; border-bottom:1px solid rgba(102,126,234,.1); }
.bank-detail-row:last-child { border-bottom:none; }
.bank-detail-row .bdr-icon { width:28px; height:28px; border-radius:6px; background:rgba(102,126,234,.12); color:#667eea; display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0; margin-right:10px; }
.bank-detail-row .bdr-label { font-size:11px; font-weight:700; color:#8a94a6; text-transform:uppercase; letter-spacing:.3px; min-width:130px; }
.bank-detail-row .bdr-value { font-size:13px; font-weight:700; color:#1a2340; flex:1; }

/* ── Status badge ─────────────────────────────────────────────────── */
.vc-status-badge {
    display:inline-flex; align-items:center; gap:5px;
    padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700;
}

@media(max-width:1199.98px) { .vc-summary-strip { grid-template-columns:repeat(3,1fr); } }
@media(max-width:767.98px)  { .vc-summary-strip { grid-template-columns:repeat(2,1fr); } .vc-header { padding:12px 14px; } }
</style>

<div class="pcoded-inner-content vc-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

{{-- ── HEADER ─────────────────────────────────────────────────── --}}
<div class="vc-header">
    <div class="row align-items-center" style="position:relative;z-index:1;">
        <div class="col-md-8">
            <div class="badge-tag"><i class="ti-building"></i> Company Details</div>
            <h4>{{ $company->company_name }}</h4>
            <div class="sub">
                <i class="ti-tag mr-1"></i>{{ $company->company_code }}
                @if($company->gst) &bull; <i class="ti-receipt mr-1"></i>{{ $company->gst }} @endif
                @if($company->phone) &bull; <i class="ti-mobile mr-1"></i>{{ $company->phone }} @endif
            </div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0">
            <div class="header-actions justify-content-end">
                <a href="{{ route('company') }}" class="btn-header btn-header-white">
                    <i class="ti-arrow-left"></i> Back
                </a>
                <a href="{{ route('company.edit', $company->id) }}" class="btn-header btn-header-solid">
                    <i class="ti-pencil"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>

@include('partials.flash')

{{-- ── SUMMARY STRIP ──────────────────────────────────────────── --}}
<div class="vc-summary-strip">
    <div class="vc-sum-item" style="border-left-color:#667eea;">
        <div class="vsi-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-briefcase"></i></div>
        <div>
            <div class="vsi-label">Business Type</div>
            <div class="vsi-value" style="color:#667eea;">{{ $company->business_types_display }}</div>
        </div>
    </div>
    <div class="vc-sum-item" style="border-left-color:#38a169;">
        <div class="vsi-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-receipt"></i></div>
        <div>
            <div class="vsi-label">GST Number</div>
            <div class="vsi-value">{{ $company->gst ?: '—' }}</div>
        </div>
    </div>
    <div class="vc-sum-item" style="border-left-color:#d97706;">
        <div class="vsi-icon" style="background:#fff8e1;color:#d97706;"><i class="ti-mobile"></i></div>
        <div>
            <div class="vsi-label">Phone</div>
            <div class="vsi-value">{{ $company->phone ?: '—' }}</div>
        </div>
    </div>
    <div class="vc-sum-item" style="border-left-color:#7c3aed;">
        <div class="vsi-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti-credit-card"></i></div>
        <div>
            <div class="vsi-label">Bank</div>
            <div class="vsi-value">{{ $company->bank_name ?: '—' }}</div>
        </div>
    </div>
    <div class="vc-sum-item" style="border-left-color:{{ $company->status ? '#48bb78' : '#fc8181' }};">
        <div class="vsi-icon" style="background:{{ $company->status ? '#f0fff4' : '#fff5f5' }};color:{{ $company->status ? '#38a169' : '#e53e3e' }};"><i class="ti-{{ $company->status ? 'check' : 'close' }}"></i></div>
        <div>
            <div class="vsi-label">Status</div>
            <div class="vsi-value" style="color:{{ $company->status ? '#38a169' : '#e53e3e' }};">{{ $company->status ? 'Active' : 'Inactive' }}</div>
        </div>
    </div>
</div>

<div class="row">
    {{-- LEFT COLUMN ──────────────────────────────────────────────── --}}
    <div class="col-lg-8">

        {{-- ① Company Identity --}}
        <div class="vc-card">
            <div class="vc-card-header">
                <div class="card-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-id-badge"></i></div>
                <div><h6>Company Identity</h6><p class="card-subtitle">Registration and business details</p></div>
            </div>
            <div class="vc-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="ir-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-tag"></i></div>
                            <div><div class="ir-label">Company Code</div><div class="ir-value">{{ $company->company_code }}</div></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="ir-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-building"></i></div>
                            <div><div class="ir-label">Company Name</div><div class="ir-value">{{ $company->company_name }}</div></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="ir-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti-briefcase"></i></div>
                            <div><div class="ir-label">Business Type</div><div class="ir-value">{{ $company->business_types_display }}</div></div>
                        </div>
                    </div>
                    @if($company->place_of_supply)
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="ir-icon" style="background:#fff8e1;color:#d97706;"><i class="ti-map-alt"></i></div>
                            <div><div class="ir-label">Place of Supply</div><div class="ir-value">{{ $company->place_of_supply }}</div></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ② Tax & Contact --}}
        <div class="vc-card">
            <div class="vc-card-header">
                <div class="card-icon" style="background:#fff8e1;color:#d97706;"><i class="ti-receipt"></i></div>
                <div><h6>Tax & Contact Details</h6><p class="card-subtitle">Tax registrations and contact info</p></div>
            </div>
            <div class="vc-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="ir-icon" style="background:#fff8e1;color:#d97706;"><i class="ti-receipt"></i></div>
                            <div><div class="ir-label">PAN Number</div><div class="ir-value">{{ $company->pan ?: '—' }}</div></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="ir-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-receipt"></i></div>
                            <div><div class="ir-label">GST Number</div><div class="ir-value">{{ $company->gst ?: '—' }}</div></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="ir-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-email"></i></div>
                            <div><div class="ir-label">Email Address</div><div class="ir-value">{{ $company->email }}</div></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-row">
                            <div class="ir-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti-mobile"></i></div>
                            <div><div class="ir-label">Primary Phone</div><div class="ir-value">{{ $company->phone ?: '—' }}</div></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-row">
                            <div class="ir-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti-mobile"></i></div>
                            <div><div class="ir-label">Alternate Phone</div><div class="ir-value">{{ $company->phone2 ?: '—' }}</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ③ Bank Account Details --}}
        <div class="vc-card">
            <div class="vc-card-header">
                <div class="card-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-credit-card"></i></div>
                <div><h6>Bank Account Details</h6><p class="card-subtitle">Account information for payments</p></div>
            </div>
            <div class="vc-card-body">
                <div class="bank-detail-card">
                    <div class="bank-detail-row">
                        <div class="bdr-icon"><i class="ti-home"></i></div>
                        <div class="bdr-label">Bank Name</div>
                        <div class="bdr-value">{{ $company->bank_name ?: '—' }}</div>
                    </div>
                    <div class="bank-detail-row">
                        <div class="bdr-icon"><i class="ti-location-pin"></i></div>
                        <div class="bdr-label">Branch Name</div>
                        <div class="bdr-value">{{ $company->branch_name ?: '—' }}</div>
                    </div>
                    <div class="bank-detail-row">
                        <div class="bdr-icon"><i class="ti-user"></i></div>
                        <div class="bdr-label">Account Holder</div>
                        <div class="bdr-value">{{ $company->account_holder_name ?: '—' }}</div>
                    </div>
                    <div class="bank-detail-row">
                        <div class="bdr-icon"><i class="ti-wallet"></i></div>
                        <div class="bdr-label">Account Number</div>
                        <div class="bdr-value" style="font-family:monospace;letter-spacing:1px;">
                            @if($company->account_number)
                                <span style="color:#8a94a6;">{{ str_repeat('•', max(0, strlen($company->account_number) - 4)) }}</span>{{ substr($company->account_number, -4) }}
                            @else —
                            @endif
                        </div>
                    </div>
                    <div class="bank-detail-row">
                        <div class="bdr-icon" style="font-size:10px;font-weight:800;letter-spacing:.3px;">IFSC</div>
                        <div class="bdr-label">IFSC Code</div>
                        <div class="bdr-value" style="font-family:monospace;font-weight:800;color:#667eea;">{{ $company->ifsc_code ?: '—' }}</div>
                    </div>
                    <div class="bank-detail-row">
                        <div class="bdr-icon" style="font-size:10px;font-weight:800;">UPI</div>
                        <div class="bdr-label">UPI ID</div>
                        <div class="bdr-value">{{ $company->upi_id ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ④ Address --}}
        <div class="vc-card">
            <div class="vc-card-header">
                <div class="card-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti-map-alt"></i></div>
                <div><h6>Registered Address</h6><p class="card-subtitle">Company registered location</p></div>
            </div>
            <div class="vc-card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-row">
                            <div class="ir-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti-map-alt"></i></div>
                            <div><div class="ir-label">Street Address</div><div class="ir-value">{{ $company->address }}</div></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-row">
                            <div class="ir-icon" style="background:#f0f9ff;color:#0369a1;"><i class="ti-map"></i></div>
                            <div><div class="ir-label">State</div><div class="ir-value">{{ $company->state }}</div></div>
                        </div>
                    </div>
                    @if($company->district)
                    <div class="col-md-3">
                        <div class="info-row">
                            <div class="ir-icon" style="background:#f0f9ff;color:#0369a1;"><i class="ti-map-alt"></i></div>
                            <div><div class="ir-label">District</div><div class="ir-value">{{ $company->district }}</div></div>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <div class="info-row">
                            <div class="ir-icon" style="background:#fef3c7;color:#d97706;"><i class="ti-tag"></i></div>
                            <div><div class="ir-label">Pincode</div><div class="ir-value">{{ $company->pincode }}</div></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-row">
                            <div class="ir-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-world"></i></div>
                            <div><div class="ir-label">Country</div><div class="ir-value">{{ $company->country }}</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN ─────────────────────────────────────────────── --}}
    <div class="col-lg-4">

        {{-- Logo & identity card --}}
        <div class="vc-card">
            <div class="vc-card-header">
                <div class="card-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-building"></i></div>
                <div><h6>Company Profile</h6><p class="card-subtitle">Logo and quick info</p></div>
            </div>
            <div class="vc-card-body" style="text-align:center;">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo"
                        style="max-height:100px;max-width:180px;border-radius:10px;object-fit:contain;margin-bottom:14px;">
                @else
                    <div style="width:80px;height:80px;border-radius:14px;background:linear-gradient(135deg,#667eea,#764ba2);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:30px;font-weight:800;margin-bottom:14px;">
                        {{ strtoupper(substr($company->company_name, 0, 1)) }}
                    </div>
                @endif
                <div style="font-size:16px;font-weight:800;color:#1a2340;margin-bottom:4px;">{{ $company->company_name }}</div>
                <div style="font-size:12px;color:#8a94a6;margin-bottom:10px;">{{ $company->company_code }}</div>
                <span class="vc-status-badge" style="background:{{ $company->status ? '#f0fff4' : '#fff5f5' }};color:{{ $company->status ? '#38a169' : '#e53e3e' }};border:1px solid {{ $company->status ? '#9ae6b4' : '#feb2b2' }};">
                    <i class="ti-{{ $company->status ? 'check' : 'close' }}" style="font-size:9px;"></i>
                    {{ $company->status ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        {{-- Quick bank summary --}}
        @if($company->bank_name || $company->account_number)
        <div class="vc-card">
            <div class="vc-card-header">
                <div class="card-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-wallet"></i></div>
                <div><h6>Payment Summary</h6><p class="card-subtitle">Quick bank reference</p></div>
            </div>
            <div class="vc-card-body" style="background:linear-gradient(135deg,#1a2340,#2d3a5e);border-radius:0 0 12px 12px;color:#fff;padding:20px;">
                <div style="font-size:11px;opacity:.6;letter-spacing:.5px;text-transform:uppercase;margin-bottom:4px;">Bank</div>
                <div style="font-size:15px;font-weight:800;margin-bottom:12px;">{{ $company->bank_name ?: 'Not set' }}</div>

                @if($company->account_holder_name)
                <div style="font-size:11px;opacity:.6;letter-spacing:.5px;text-transform:uppercase;margin-bottom:2px;">Account Holder</div>
                <div style="font-size:13px;font-weight:700;margin-bottom:10px;">{{ $company->account_holder_name }}</div>
                @endif

                @if($company->account_number)
                <div style="font-size:11px;opacity:.6;letter-spacing:.5px;text-transform:uppercase;margin-bottom:2px;">Account No.</div>
                <div style="font-family:monospace;font-size:14px;letter-spacing:2px;margin-bottom:10px;">
                    {{ str_repeat('•', max(0, strlen($company->account_number) - 4)) }}{{ substr($company->account_number, -4) }}
                </div>
                @endif

                @if($company->ifsc_code)
                <div style="display:flex;gap:8px;align-items:center;">
                    <span style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);border-radius:6px;padding:4px 10px;font-size:11px;font-weight:800;letter-spacing:.5px;">{{ $company->ifsc_code }}</span>
                    @if($company->branch_name)
                    <span style="font-size:11px;opacity:.7;">{{ $company->branch_name }}</span>
                    @endif
                </div>
                @endif

                @if($company->upi_id)
                <div style="margin-top:10px;padding-top:10px;border-top:1px solid rgba(255,255,255,.1);">
                    <span style="font-size:10px;opacity:.6;text-transform:uppercase;letter-spacing:.5px;">UPI</span>
                    <div style="font-size:12px;font-weight:700;margin-top:2px;">{{ $company->upi_id }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Metadata --}}
        <div class="vc-card">
            <div class="vc-card-header">
                <div class="card-icon" style="background:#f0f9ff;color:#0369a1;"><i class="ti-time"></i></div>
                <div><h6>Record Info</h6><p class="card-subtitle">Creation and update timestamps</p></div>
            </div>
            <div class="vc-card-body">
                <div class="info-row">
                    <div class="ir-icon" style="background:#f0f9ff;color:#0369a1;"><i class="ti-calendar"></i></div>
                    <div><div class="ir-label">Created</div><div class="ir-value" style="font-size:12px;">{{ $company->created_at ? $company->created_at->format('d M Y, h:i A') : '—' }}</div></div>
                </div>
                <div class="info-row">
                    <div class="ir-icon" style="background:#fef3c7;color:#d97706;"><i class="ti-reload"></i></div>
                    <div><div class="ir-label">Last Updated</div><div class="ir-value" style="font-size:12px;">{{ $company->updated_at ? $company->updated_at->format('d M Y, h:i A') : '—' }}</div></div>
                </div>
            </div>
        </div>

    </div>
</div>

</div></div></div></div>
@endsection
