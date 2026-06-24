@extends('layouts.app')

@section('title', 'Authorize User Menus - ' . $user->email)

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
.form-group label{font-weight:600;font-size:12px;color:#1a2340;margin-bottom:4px;}
.btn-up{font-size:12px;font-weight:700;padding:8px 18px;border-radius:8px;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px;text-decoration:none;transition:background .2s,color .2s;}
.btn-up-primary{background:#7c3aed;color:#fff;}
.btn-up-primary:hover{background:#6d28d9;color:#fff;}
.btn-up-secondary{background:#e2e8f0;color:#475569;}
.btn-up-secondary:hover{background:#cbd5e1;color:#1e293b;}
.menu-table{width:100%;border-collapse:collapse;font-size:12px;}
.menu-table thead tr{background:#f8fafc;}
.menu-table th{padding:10px 12px;font-size:10.5px;font-weight:800;color:#1a2340;text-transform:uppercase;letter-spacing:.4px;border-bottom:2px solid #e2e8f0;text-align:left;}
.menu-table td{padding:10px 12px;border-bottom:1px solid #f0f2f7;color:#1e293b;vertical-align:middle;}
.menu-table tbody tr:hover td{background:#f8fafc;}
.menu-table tbody tr:last-child td{border-bottom:none;}
.menu-table input[type=checkbox]{width:18px;height:18px;cursor:pointer;accent-color:#7c3aed;}
</style>

<div class="pcoded-inner-content up-page">
    <div class="main-body"><div class="page-wrapper"><div class="page-body">

        <div class="up-header">
            <div class="row align-items-center">
                <div class="col-md-8" style="position:relative;z-index:1;">
                    <h4><i class="ti-layout mr-2"></i>Screen Permissions</h4>
                    <div class="sub">
                        {{ $user->email }}
                        &middot; <span class="badge" style="background:rgba(255,255,255,.2);color:#fff;padding:2px 8px;border-radius:4px;font-size:10px;">{{ ucfirst($user->role) }}</span>
                        &middot; <span class="badge" style="background:rgba(255,255,255,.2);color:#fff;padding:2px 8px;border-radius:4px;font-size:10px;">{{ $user->status ? 'Active' : 'Inactive' }}</span>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.flash')

        <div class="up-card">
            <div class="up-card-header">
                <h6><i class="ti-menu mr-2" style="color:#7c3aed;"></i>Menu Visibility</h6>
                <span style="font-size:11px;color:#94a3b8;">{{ $menus->count() }} menu(s)</span>
            </div>
            <div class="up-body">
                <form method="POST" action="{{ route('user-permissions.update', $user->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="redirect_to" value="authorize">

                    <div class="mb-3">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="select_all">
                            <span class="custom-control-label font-weight-bold" style="font-size:13px;font-weight:700;">Select All Menus</span>
                        </label>
                    </div>

                    <div style="overflow-x:auto;">
                        <table class="menu-table">
                            <thead>
                                <tr>
                                    <th style="width:50px;text-align:center;">
                                        <input type="checkbox" id="header_select_all">
                                    </th>
                                    <th style="width:120px;">Icon</th>
                                    <th>Menu Name</th>
                                    <th>Route</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($menus as $menu)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="menu-checkbox" name="menus[]" value="{{ $menu->id }}"
                                            {{ in_array($menu->id, $userMenuIds) ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        @if($menu->icon)
                                        <i class="{{ $menu->icon }}" style="font-size:16px;color:#7c3aed;"></i>
                                        @else
                                        <span style="color:#94a3b8;">—</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ $menu->display_name }}</strong></td>
                                    <td>
                                        @if($menu->route)
                                        <code style="background:#f1f5f9;padding:3px 8px;border-radius:4px;font-size:11px;">{{ $menu->route }}</code>
                                        @else
                                        <span style="color:#94a3b8;">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" style="text-align:center;padding:30px;color:#94a3b8;">No menus found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <hr style="border-color:#f0f2f7;margin:20px 0;">

                    <div class="form-group d-flex align-items-center gap-2">
                        <button type="submit" class="btn-up btn-up-primary">
                            <i class="feather icon-save"></i> Save Menu Permissions
                        </button>
                        <a href="{{ route('user-permissions.authorization') }}" class="btn-up btn-up-secondary">
                            <i class="feather icon-arrow-left"></i> Back to User List
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div></div></div></div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.querySelector('#select_all');
        const headerSelectAll = document.querySelector('#header_select_all');
        const menuCheckboxes = document.querySelectorAll('.menu-checkbox');

        function updateSelectAll() {
            const allChecked = Array.from(menuCheckboxes).every(cb => cb.checked);
            selectAll.checked = allChecked;
            headerSelectAll.checked = allChecked;
        }

        selectAll.addEventListener('change', function () {
            menuCheckboxes.forEach(cb => cb.checked = this.checked);
            headerSelectAll.checked = this.checked;
        });

        headerSelectAll.addEventListener('change', function () {
            menuCheckboxes.forEach(cb => cb.checked = this.checked);
            selectAll.checked = this.checked;
        });

        menuCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectAll);
        });

        updateSelectAll();
    });
</script>
@endsection