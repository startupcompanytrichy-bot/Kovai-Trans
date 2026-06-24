@extends('layouts.app')

@section('title', 'Settings Permissions - ' . $user->email)

@section('content')
<style>
.sp-page{background:#f4f6fb;}
.sp-header{background:linear-gradient(135deg,#1a2340 0%,#2d3a5e 60%,#667eea 100%);border-radius:10px;padding:14px 20px;color:#fff;margin-bottom:16px;position:relative;overflow:hidden;}
.sp-header::before{content:'';position:absolute;top:-40px;right:-40px;width:140px;height:140px;background:rgba(255,255,255,.05);border-radius:50%;}
.sp-header h4{font-size:16px;font-weight:800;margin:0 0 2px;}
.sp-header .sub{font-size:12px;opacity:.75;}
.sp-card{background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,.06);overflow:hidden;margin-bottom:14px;}
.sp-card-head{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #f0f2f7;background:#fafbff;flex-wrap:wrap;gap:8px;}
.sp-card-head h6{margin:0;font-size:13px;font-weight:800;color:#1a2340;display:flex;align-items:center;gap:6px;}
.sp-body{padding:16px;}
.sp-table{width:100%;border-collapse:collapse;font-size:12px;}
.sp-table thead tr{background:#f8fafc;}
.sp-table th{padding:10px 12px;font-size:10.5px;font-weight:800;color:#1a2340;text-transform:uppercase;letter-spacing:.4px;border-bottom:2px solid #e2e8f0;text-align:left;}
.sp-table td{padding:10px 12px;border-bottom:1px solid #f0f2f7;color:#1e293b;vertical-align:middle;}
.sp-table tbody tr:hover td{background:#f8fafc;}
.sp-table tbody tr:last-child td{border-bottom:none;}
.sp-table input[type=checkbox]{width:18px;height:18px;cursor:pointer;accent-color:#667eea;}
.btn-sp{font-size:12px;font-weight:700;padding:8px 18px;border-radius:8px;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px;text-decoration:none;transition:background .2s,color .2s;}
.btn-sp-primary{background:#667eea;color:#fff;}
.btn-sp-primary:hover{background:#5a67d8;color:#fff;}
.btn-sp-secondary{background:#e2e8f0;color:#475569;}
.btn-sp-secondary:hover{background:#cbd5e1;color:#1e293b;}
</style>

<div class="pcoded-inner-content sp-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

    <div class="sp-header">
        <div class="row align-items-center">
            <div class="col-md-8" style="position:relative;z-index:1;">
                <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;letter-spacing:.5px;margin-bottom:6px;">
                    <i class="ti-settings"></i> Settings Permissions
                </div>
                <h4>{{ $user->email }}</h4>
                <div class="sub">
                    <span class="badge" style="background:rgba(255,255,255,.2);color:#fff;padding:2px 8px;border-radius:4px;font-size:10px;">{{ ucfirst($user->role) }}</span>
                    &middot; <span class="badge" style="background:rgba(255,255,255,.2);color:#fff;padding:2px 8px;border-radius:4px;font-size:10px;">{{ $user->status ? 'Active' : 'Inactive' }}</span>
                </div>
            </div>
        </div>
    </div>

    @include('partials.flash')

    <div class="sp-card">
        <div class="sp-card-head">
            <h6><i class="ti-layers mr-2" style="color:#667eea;"></i>Settings Section Access</h6>
            <span style="font-size:11px;color:#94a3b8;">{{ $permissions->count() }} section(s)</span>
        </div>
        <div class="sp-body">
            <form method="POST" action="{{ route('settings.permissions.update', $user->id) }}">
                @csrf

                <div class="mb-3">
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="select_all">
                        <span class="custom-control-label font-weight-bold" style="font-size:13px;">Select All Sections</span>
                    </label>
                </div>

                <div style="overflow-x:auto;">
                    <table class="sp-table">
                        <thead>
                            <tr>
                                <th style="width:50px;text-align:center;"><input type="checkbox" id="header_select_all"></th>
                                <th>Settings Section</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permissions as $perm)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="perm-checkbox" name="permissions[]" value="{{ $perm->id }}"
                                        {{ in_array($perm->id, $userPermIds) ? 'checked' : '' }}>
                                </td>
                                <td><strong>{{ $perm->display_name }}</strong></td>
                                <td style="color:#64748b;">
                                    @switch($perm->name)
                                        @case('view_settings_financial_year')
                                            Financial year management (add, activate, delete years)
                                            @break
                                        @case('view_settings_branch_default')
                                            Default branch selection and branch preferences
                                            @break
                                        @case('view_settings_account_limits')
                                            Company &amp; Branch add limits configuration
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4" style="color:#b0bac9;">No settings permissions found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <hr style="border-color:#f0f2f7;margin:20px 0;">

                <div class="form-group d-flex align-items-center gap-2">
                    <button type="submit" class="btn-sp btn-sp-primary">
                        <i class="feather icon-save"></i> Save Permissions
                    </button>
                    <a href="{{ route('settings.permissions.index') }}" class="btn-sp btn-sp-secondary">
                        <i class="feather icon-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>

</div></div></div></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var selectAll = document.querySelector('#select_all');
    var headerSelectAll = document.querySelector('#header_select_all');
    var checkboxes = document.querySelectorAll('.perm-checkbox');

    function updateSelectAll() {
        var allChecked = Array.from(checkboxes).every(function (cb) { return cb.checked; });
        selectAll.checked = allChecked;
        headerSelectAll.checked = allChecked;
    }

    selectAll.addEventListener('change', function () {
        checkboxes.forEach(function (cb) { cb.checked = selectAll.checked; });
        headerSelectAll.checked = selectAll.checked;
    });

    headerSelectAll.addEventListener('change', function () {
        checkboxes.forEach(function (cb) { cb.checked = headerSelectAll.checked; });
        selectAll.checked = headerSelectAll.checked;
    });

    checkboxes.forEach(function (cb) {
        cb.addEventListener('change', updateSelectAll);
    });

    updateSelectAll();
});
</script>
@endpush
@endsection