@extends('layouts.app')

@section('title', 'User Permissions')

@section('content')
<style>
.up-page{background:#f4f6fb;}
.up-header{background:linear-gradient(135deg,#7c3aed 0%,#5b21b6 100%);border-radius:14px;padding:20px 24px;color:#fff;margin-bottom:20px;position:relative;overflow:hidden;}
.up-header::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.08);border-radius:50%;}
.up-header h4{font-size:18px;font-weight:800;margin:0 0 4px;}
.up-header .sub{font-size:12px;opacity:.85;}
.up-card{background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;}
.up-card-header{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f0f2f7;background:#fafbff;flex-wrap:wrap;gap:8px;}
.up-card-header h6{margin:0;font-size:14px;font-weight:700;color:#1a2340;}
.up-table-wrap{overflow-x:auto;}
#userTable{width:100%;border-collapse:collapse;font-size:12px;}
#userTable thead tr{background:#f8fafc;}
#userTable th{padding:10px 12px;font-size:10.5px;font-weight:800;color:#1a2340;text-transform:uppercase;letter-spacing:.4px;border-bottom:2px solid #e2e8f0;text-align:left;white-space:nowrap;}
#userTable td{padding:10px 12px;border-bottom:1px solid #f0f2f7;color:#1e293b;vertical-align:middle;}
#userTable tbody tr:hover td{background:#f8fafc;}
#userTable tbody tr:last-child td{border-bottom:none;}
@media(max-width:767.98px){#userTable{font-size:11px;}#userTable th,#userTable td{padding:6px 6px;}}
</style>

<div class="pcoded-inner-content up-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="up-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <h4><i class="ti-shield mr-2"></i>User Permissions</h4>
            <div class="sub">Manage system users and their role-based permissions.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('user-permissions.create') }}" class="btn btn-sm" style="background:#fff;color:#5b21b6;border-radius:8px;padding:7px 16px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
                <i class="ti-plus"></i> Add User
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

<div class="up-card">
    <div class="up-card-header">
        <h6><i class="ti-user mr-2" style="color:#7c3aed;"></i>All Users</h6>
        <span style="font-size:11px;color:#94a3b8;">{{ $users->count() }} user(s)</span>
    </div>
    <div class="up-table-wrap">
        <table id="userTable">
            <thead>
                <tr>
                    <th style="width:40px;">ID</th>
                    <th>Email</th>
                    <th style="width:100px;">Mobile</th>
                    <th style="width:90px;">Role</th>
                    <th style="width:80px;">Status</th>
                    <th style="width:110px;">Permissions</th>
                    <th style="width:130px;" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="font-weight:600;">{{ $user->id }}</td>
                    <td>{{ $user->email }}</td>
                    <td style="color:#64748b;">{{ $user->mobile ?? '—' }}</td>
                    <td>
                        <span class="badge badge-info" style="font-size:10px;padding:3px 8px;">{{ ucfirst($user->role) }}</span>
                    </td>
                    <td>
                        @if($user->status)
                        <span class="badge badge-success" style="font-size:10px;padding:3px 8px;">Active</span>
                        @else
                        <span class="badge badge-danger" style="font-size:10px;padding:3px 8px;">Inactive</span>
                        @endif
                    </td>
                    <td>
                        @if($user->permissions && $user->permissions->count() > 0)
                        <span class="badge badge-secondary" style="font-size:10px;padding:3px 8px;">{{ $user->permissions->count() }} granted</span>
                        @else
                        <span class="badge badge-warning" style="font-size:10px;padding:3px 8px;">None</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div style="display:flex;gap:4px;justify-content:center;">
                            <a href="{{ route('user-permissions.edit', $user->id) }}"
                               class="btn btn-sm" title="Edit"
                               style="background:#eef2ff;color:#4f46e5;border-radius:6px;padding:4px 8px;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;">
                                <i class="ti-pencil"></i>
                            </a>
                            <form action="{{ route('user-permissions.toggle-status', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" title="{{ $user->status ? 'Deactivate' : 'Activate' }}"
                                        class="btn btn-sm" style="background:{{ $user->status ? '#fffbeb' : '#f0fdf4' }};color:{{ $user->status ? '#b45309' : '#166534' }};border-radius:6px;padding:4px 8px;font-size:13px;border:none;cursor:pointer;display:inline-flex;align-items:center;">
                                    <i class="ti-{{ $user->status ? 'pause' : 'play' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('user-permissions.destroy', $user->id) }}"
                                  method="POST" style="display:inline;"
                                  id="deleteForm{{ $user->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" title="Delete"
                                        class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border-radius:6px;padding:4px 8px;font-size:13px;border:none;cursor:pointer;display:inline-flex;align-items:center;"
                                        onclick="showDeleteModal('deleteForm{{ $user->id }}', '{{ $user->email }}', 'User')"
                                        {{ $user->id == session('loginId') ? 'disabled' : '' }}>
                                    <i class="ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:30px;color:#94a3b8;">No users found. <a href="{{ route('user-permissions.create') }}">Create one</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div></div></div></div>
@endsection