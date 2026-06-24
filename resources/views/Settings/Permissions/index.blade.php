@extends('layouts.app')

@section('title', 'Settings Permissions')

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
.btn-sp{font-size:12px;font-weight:700;padding:7px 14px;border-radius:8px;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px;text-decoration:none;transition:background .2s,color .2s;}
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
                    <i class="ti-settings"></i> Settings
                </div>
                <h4>Settings Permissions</h4>
                <div class="sub">Configure which settings sections each user can see.</div>
            </div>
            <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
                <a href="{{ route('settings') }}" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);font-weight:600;border-radius:8px;padding:6px 16px;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                    <i class="ti-settings mr-1"></i> Main Settings
                </a>
            </div>
        </div>
    </div>

    @include('partials.flash')

    <div class="sp-card">
        <div class="sp-card-head">
            <h6><i class="ti-user mr-2" style="color:#667eea;"></i>All Users</h6>
            <span style="font-size:11px;color:#94a3b8;">{{ $users->count() }} user(s)</span>
        </div>
        <div class="sp-body">
            <div style="overflow-x:auto;">
                <table class="sp-table">
                    <thead>
                        <tr>
                            <th style="width:220px;">User</th>
                            <th style="width:100px;">Role</th>
                            <th style="width:160px;">Current Access</th>
                            <th style="width:140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        @php
                            $userModel = \App\Models\User::where('email', $user->email)->first();
                            $grantedPerms = $userModel ? $userModel->permissions->whereIn('name', ['view_settings_financial_year', 'view_settings_branch_default', 'view_settings_account_limits']) : collect();
                        @endphp
                        <tr>
                            <td><strong style="color:#1a2340;">{{ $user->email }}</strong></td>
                            <td><span class="badge badge-info" style="font-size:10px;padding:3px 8px;">{{ ucfirst($user->role) }}</span></td>
                            <td>
                                @if($grantedPerms->count() > 0)
                                    @foreach($grantedPerms as $p)
                                        <span style="display:inline-block;background:#eef2ff;color:#4f46e5;font-size:10px;font-weight:600;padding:2px 8px;border-radius:4px;margin:1px 2px;">
                                            {{ str_replace(['view_settings_', '_'], ['', ' '], $p->name) }}
                                        </span>
                                    @endforeach
                                @else
                                    <span style="color:#94a3b8;font-size:11px;">No settings access</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('settings.permissions.edit', $user->id) }}" class="btn-sp btn-sp-primary" style="text-decoration:none;">
                                    <i class="ti-pencil-alt mr-1"></i> Manage
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4" style="color:#b0bac9;">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div></div></div></div>
@endsection