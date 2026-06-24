@extends('layouts.app')

@section('title', 'User Screen Permissions')

@section('content')
<style>
.up-page{background:#f4f6fb;}
.up-header{background:linear-gradient(135deg,#7c3aed 0%,#5b21b6 100%);border-radius:14px;padding:20px 24px;color:#fff;margin-bottom:20px;position:relative;overflow:hidden;}
.up-header::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.08);border-radius:50%;}
.up-header h4{font-size:18px;font-weight:800;margin:0 0 4px;}
.up-header .sub{font-size:12px;opacity:.85;}
.up-card{background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;}
.up-card-header{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f0f2f7;background:#fafbff;}
.up-card-header h6{margin:0;font-size:14px;font-weight:700;color:#1a2340;}
.up-body{padding:20px;}
.user-card{background:#fff;border:1.5px solid #eef2f7;border-radius:12px;transition:box-shadow .2s,border-color .2s;height:100%;}
.user-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.06);border-color:#d4dae8;}
.user-card-body{padding:18px;}
.user-card h6{font-size:13px;font-weight:700;color:#1a2340;margin:0 0 10px;word-break:break-all;}
.user-role{font-size:10px;font-weight:700;padding:3px 8px;border-radius:6px;display:inline-block;}
.user-role-admin{background:#eef2ff;color:#4f46e5;}
.user-role-manager{background:#fef3c7;color:#b45309;}
.user-role-operator{background:#dbeafe;color:#2563eb;}
.user-role-accountant{background:#d1fae5;color:#166534;}
.user-role-viewer{background:#f3f4f6;color:#6b7280;}
.user-status{font-size:10px;font-weight:700;padding:3px 8px;border-radius:6px;display:inline-block;}
.user-status-active{background:#d1fae5;color:#166534;}
.user-status-inactive{background:#fee2e2;color:#dc2626;}
.btn-up{font-size:12px;font-weight:700;padding:7px 14px;border-radius:8px;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px;text-decoration:none;transition:background .2s,color .2s;}
.btn-up-primary{background:#7c3aed;color:#fff;}
.btn-up-primary:hover{background:#6d28d9;color:#fff;}
</style>

<div class="pcoded-inner-content up-page">
    <div class="main-body"><div class="page-wrapper"><div class="page-body">

        <div class="up-header">
            <div class="row align-items-center">
                <div class="col-md-8" style="position:relative;z-index:1;">
                    <h4><i class="ti-layout mr-2"></i>Screen Permissions</h4>
                    <div class="sub">Select a user to manage which screen menus they can see.</div>
                </div>
            </div>
        </div>

        @include('partials.flash')

        <div class="up-card">
            <div class="up-card-header">
                <h6><i class="ti-user mr-2" style="color:#7c3aed;"></i>All Users</h6>
                <span style="font-size:11px;color:#94a3b8;">{{ $users->count() }} user(s)</span>
            </div>
            <div class="up-body">
                <div class="row">
                    @forelse($users as $user)
                    @php
                        $userModel = \App\Models\User::where('email', $user->email)->first();
                        $menuCount = $userModel ? $userModel->menus->count() : 0;
                    @endphp
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="user-card">
                            <div class="user-card-body d-flex flex-column">
                                <h6>{{ $user->email }}</h6>
                                <div class="mb-2 d-flex gap-2" style="gap:6px;flex-wrap:wrap;">
                                    <span class="user-role user-role-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                                    <span class="user-status user-status-{{ $user->status ? 'active' : 'inactive' }}">{{ $user->status ? 'Active' : 'Inactive' }}</span>
                                </div>
                                <div class="mb-3">
                                    <small style="color:#94a3b8;font-size:11px;">
                                        @if($menuCount > 0)
                                            <strong style="color:#7c3aed;">{{ $menuCount }}</strong> menus visible
                                        @else
                                            <span style="color:#f59e0b;">No menus assigned</span>
                                        @endif
                                    </small>
                                </div>
                                <div style="margin-top:auto;">
                                    <a href="{{ route('user-permissions.authorize', $user->id) }}" class="btn-up btn-up-primary btn-block" style="width:100%;justify-content:center;">
                                        <i class="feather icon-edit"></i> Manage Menus
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-sm-12">
                        <div class="alert alert-info" style="font-size:12px;border-radius:8px;">
                            No users found. <a href="{{ route('user-permissions.create') }}">Create a new user</a>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div></div></div></div>
@endsection