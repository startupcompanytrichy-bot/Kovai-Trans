@extends('layouts.app')

@section('content')
<style>
.branch-view-page{background:#f4f6fb;}
.branch-view-header{background:linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%);border-radius:14px;padding:24px 28px;color:#fff;margin-bottom:24px;position:relative;overflow:hidden;}
.branch-view-header::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.08);border-radius:50%;}
.branch-view-header h3{font-size:20px;font-weight:800;margin:0 0 4px;position:relative;z-index:1;}
.branch-view-header .sub{font-size:12px;opacity:.85;position:relative;z-index:1;}
.view-card{background:#fff;border-radius:16px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,.06);margin-bottom:20px;}
.section-title{font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#3b82f6;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid #e5e7ff;display:flex;align-items:center;gap:8px;}
.section-title i{font-size:14px;}
.view-field{margin-bottom:16px;}
.view-field-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#8a94a6;margin-bottom:4px;}
.view-field-value{font-size:14px;font-weight:600;color:#0f172a;}
.view-field-empty{color:#b0bac9;font-style:italic;}
.field-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:24px;}
.action-buttons{display:flex;gap:10px;margin-top:28px;padding-top:24px;border-top:1px solid #e5e7eb;}
.btn-back{background:#f0f2f7;color:#596579;border:none;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;}
.btn-back:hover{background:#e2e8f0;color:#303549;}
.btn-edit{background:#3b82f6;color:#fff;border:none;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;}
.btn-edit:hover{background:#2563eb;transform:translateY(-1px);box-shadow:0 4px 12px rgba(59,130,246,.3);}
</style>

<div class="pcoded-inner-content branch-view-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="branch-view-header">
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div>
            <h3><i class="ti-location-pin mr-2"></i>{{ $branch->branch_name }}</h3>
            <div class="sub">Branch Code: {{ $branch->branch_code }}</div>
        </div>
        <a href="{{ route('branch') }}" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
            <i class="ti-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="view-card">
    <div class="section-title"><i class="ti-id-badge"></i> Identity & Status</div>
    <div class="field-row">
        <div class="view-field">
            <div class="view-field-label">Company</div>
            <div class="view-field-value">{{ $branch->company?->company_name ?? '—' }}</div>
        </div>
        <div class="view-field">
            <div class="view-field-label">Branch Code</div>
            <div class="view-field-value">{{ $branch->branch_code }}</div>
        </div>
        <div class="view-field">
            <div class="view-field-label">Head Office</div>
            <div class="view-field-value">
                @if($branch->head_office)
                    <span style="background:#dcfce7;color:#166534;padding:3px 8px;border-radius:6px;font-size:12px;font-weight:700;">Yes</span>
                @else
                    <span style="background:#f3f4f6;color:#6b7280;padding:3px 8px;border-radius:6px;font-size:12px;font-weight:700;">No</span>
                @endif
            </div>
        </div>
        <div class="view-field">
            <div class="view-field-label">Status</div>
            <div class="view-field-value">
                @if($branch->status)
                    <span style="background:#dbeafe;color:#1e40af;padding:3px 8px;border-radius:6px;font-size:12px;font-weight:700;">Active</span>
                @else
                    <span style="background:#fee2e2;color:#991b1b;padding:3px 8px;border-radius:6px;font-size:12px;font-weight:700;">Inactive</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="view-card">
    <div class="section-title"><i class="ti-headphone-alt"></i> Contact Details</div>
    <div class="field-row">
        <div class="view-field">
            <div class="view-field-label">Email Address</div>
            <div class="view-field-value{{ $branch->email ? '' : ' view-field-empty' }}">{{ $branch->email ?? '—' }}</div>
        </div>
        <div class="view-field">
            <div class="view-field-label">Mobile Number</div>
            <div class="view-field-value{{ $branch->mobile ? '' : ' view-field-empty' }}">{{ $branch->mobile ?? '—' }}</div>
        </div>
    </div>
</div>

<div class="view-card">
    <div class="section-title"><i class="ti-location-pin"></i> Address Details</div>
    <div class="view-field">
        <div class="view-field-label">Street Address</div>
        <div class="view-field-value{{ $branch->address ? '' : ' view-field-empty' }}" style="white-space:pre-wrap;line-height:1.6;">{{ $branch->address ?? '—' }}</div>
    </div>
    <div class="field-row" style="margin-top:16px;">
        <div class="view-field">
            <div class="view-field-label">City</div>
            <div class="view-field-value{{ $branch->city ? '' : ' view-field-empty' }}">{{ $branch->city ?? '—' }}</div>
        </div>
        <div class="view-field">
            <div class="view-field-label">State</div>
            <div class="view-field-value{{ $branch->state ? '' : ' view-field-empty' }}">{{ $branch->state ?? '—' }}</div>
        </div>
        <div class="view-field">
            <div class="view-field-label">Pincode</div>
            <div class="view-field-value{{ $branch->pincode ? '' : ' view-field-empty' }}">{{ $branch->pincode ?? '—' }}</div>
        </div>
        <div class="view-field">
            <div class="view-field-label">Country</div>
            <div class="view-field-value">{{ $branch->country ?? 'India' }}</div>
        </div>
    </div>
</div>

<div class="view-card">
    <div class="action-buttons">
        <a href="{{ route('branch') }}" class="btn-back"><i class="ti-arrow-left mr-1"></i> Back to List</a>
        <a href="{{ route('branch.edit', $branch->id) }}" class="btn-edit"><i class="ti-pencil mr-1"></i> Edit Branch</a>
    </div>
</div>

</div></div></div>
</div>
@endsection
