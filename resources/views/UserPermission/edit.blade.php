@extends('layouts.app')

@section('title', 'Edit User')

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
.form-control{font-size:12px !important;border-radius:8px !important;border:1.5px solid #e2e8f0 !important;padding:9px 12px !important;height:38px !important;min-height:38px !important;line-height:1.2 !important;box-sizing:border-box !important;transition:border-color .2s;}
input.form-control{padding:0 12px !important;}
.form-control:focus{border-color:#7c3aed !important;box-shadow:0 0 0 3px rgba(124,58,237,.15) !important;}
.btn-up{font-size:12px;font-weight:700;padding:8px 18px;border-radius:8px;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px;text-decoration:none;}
.btn-up-primary{background:#7c3aed;color:#fff;}
.btn-up-primary:hover{background:#6d28d9;color:#fff;}
.btn-up-secondary{background:#e2e8f0;color:#475569;}
.btn-up-secondary:hover{background:#cbd5e1;color:#1e293b;}
.password-wrapper{position:relative;}
.password-wrapper .form-control{padding-right:38px !important;}
.password-toggle{position:absolute;right:8px;top:50%;transform:translateY(-50%);cursor:pointer;color:#94a3b8;font-size:16px;padding:4px;background:none;border:none;display:flex;align-items:center;justify-content:center;z-index:5;line-height:1;transition:color .2s;}
.password-toggle:hover{color:#7c3aed;}
input[type=password]::-ms-reveal,input[type=password]::-ms-clear{display:none;}
/* Select2 outside container match page style */
.up-body .select2-container--default .select2-selection--single{min-height:38px !important;height:38px !important;border:1.5px solid #e2e8f0 !important;border-radius:8px !important;padding:0 36px 0 10px !important;background:#fff !important;display:flex !important;align-items:center !important;}
.up-body .select2-container--default .select2-selection--single .select2-selection__rendered{font-size:12px !important;color:#1e293b !important;padding:0 !important;line-height:1 !important;}
.up-body .select2-container--default .select2-selection--single .select2-selection__placeholder{color:#94a3b8 !important;font-size:12px !important;}
.up-body .select2-container--default.select2-container--focus .select2-selection--single,.up-body .select2-container--default.select2-container--open .select2-selection--single{border-color:#7c3aed !important;box-shadow:0 0 0 3px rgba(124,58,237,.15) !important;}
.up-body .select2-container--default .select2-selection--single .select2-selection__arrow{right:6px !important;}
.up-body .select2-container--default .select2-selection--single .select2-selection__arrow b{border-width:4px 4px 0 4px !important;}
.up-body .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b{border-width:0 4px 4px 4px !important;}
.up-body .select2-container--default .select2-selection--multiple{min-height:38px !important;height:auto !important;border:1.5px solid #e2e8f0 !important;border-radius:8px !important;padding:5px 8px !important;background:#fff !important;box-sizing:border-box !important;}
.up-body .select2-container--default.select2-container--focus .select2-selection--multiple,.up-body .select2-container--default.select2-container--open .select2-selection--multiple{border-color:#7c3aed !important;box-shadow:0 0 0 3px rgba(124,58,237,.15) !important;}
.up-body .select2-container--default .select2-selection--multiple .select2-selection__choice{font-size:11px !important;padding:2px 20px 2px 6px !important;background:#667eea !important;border-color:#c7d2fe !important;color:#4f46e5 !important;border-radius:6px !important;max-width:160px !important;}
.up-body .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{color:#818cf8 !important;border-left-color:#c7d2fe !important;width:18px !important;font-size:12px !important;}
.up-body .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover{background:rgba(239,68,68,.1) !important;color:#dc2626 !important;}
.up-body .select2-container--default .select2-selection--multiple .select2-selection__placeholder{color:#94a3b8 !important;font-size:12px !important;}
.up-body .select2-container--default .select2-selection--multiple .select2-search__field{font-size:12px !important;height:22px !important;color:#1e293b !important;}
.up-body .select2-dropdown{border-radius:8px !important;border-color:#e2e8f0 !important;box-shadow:0 4px 16px rgba(0,0,0,.1) !important;}
.up-body .select2-results__option{font-size:12px !important;padding:7px 12px !important;}
.up-body .select2-results__option[aria-selected="true"]{background:#eef2ff !important;color:#4f46e5 !important;}
.up-body .select2-container--default .select2-results__option--highlighted[aria-selected]{background:#7c3aed !important;color:#fff !important;}
</style>

<div class="pcoded-inner-content up-page">
    <div class="main-body"><div class="page-wrapper"><div class="page-body">

        <div class="up-header">
            <div class="row align-items-center">
                <div class="col-md-8" style="position:relative;z-index:1;">
                    <h4><i class="ti-shield mr-2"></i>Edit User</h4>
                    <div class="sub">{{ $user->email }} &middot; {{ ucfirst($user->role) }}</div>
                </div>
            </div>
        </div>

        @include('partials.flash')

        <div class="up-card">
            <div class="up-card-header">
                <h6><i class="ti-user mr-2" style="color:#7c3aed;"></i>Basic Information</h6>
            </div>
            <div class="up-body">
                <form action="{{ route('user-permissions.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company">Company</label>
                                <select name="company" id="company" class="form-control select2">
                                    <option value="">Select Company</option>
                                    @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ $user->company == $company->id ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="branch">Branch</label>
                                <select name="branch[]" id="branch" class="form-control select2" multiple>
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" data-company-id="{{ $branch->company_id }}"
                                        {{ in_array($branch->id, explode(',', $user->branch ?? '')) ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mobile">Mobile</label>
                                <input type="text" class="form-control @error('mobile') is-invalid @enderror"
                                    id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}">
                                @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="password-wrapper">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Leave blank to keep current">
                                    <button type="button" id="togglePassword" class="password-toggle" tabindex="-1">
                                        <i class="ti-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Leave blank to keep current password</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-control select2 @error('role') is-invalid @enderror" required>
                                    <option value="">Select Role</option>
                                    <option value="super_admin" {{ $user->role == 'super_admin' ? 'selected' : '' }}>Super Admin (Full Access)</option>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="manager" {{ $user->role == 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="operator" {{ $user->role == 'operator' ? 'selected' : '' }}>Operator</option>
                                    <option value="accountant" {{ $user->role == 'accountant' ? 'selected' : '' }}>Accountant</option>
                                    <option value="viewer" {{ $user->role == 'viewer' ? 'selected' : '' }}>Viewer</option>
                                </select>
                                @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control select2 @error('status') is-invalid @enderror" required>
                                    <option value="1" {{ $user->status ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$user->status ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr style="border-color:#f0f2f7;margin:20px 0;">

                    <div class="form-group d-flex align-items-center gap-2">
                        <a href="{{ route('user-permissions.index') }}" class="btn-up btn-up-secondary">
                            <i class="feather icon-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn-up btn-up-primary">
                            <i class="feather icon-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div></div></div></div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // ── Password toggle ──
    $('#togglePassword').on('click', function () {
        var $input = $('#password');
        var $icon = $(this).find('i');
        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.removeClass('ti-eye').addClass('ti-unlock');
            $(this).attr('title', 'Hide password');
        } else {
            $input.attr('type', 'password');
            $icon.removeClass('ti-unlock').addClass('ti-eye');
            $(this).attr('title', 'Show password');
        }
    });

    // ── Company → Branch cascade (multi-select) ──
    var $company = $('#company');
    var $branch = $('#branch');
    if ($branch.length) {
        var allBranchOpts = $branch.find('option').clone();

        function refreshBranch() {
            var companyId = $company.val();
            var prevVals = $branch.val() || [];

            if ($branch.hasClass('select2-hidden-accessible')) {
                $branch.select2('destroy');
            }

            $branch.empty().append(allBranchOpts.clone());
            $branch.find('option').each(function () {
                var $opt = $(this);
                if (companyId && $opt.data('company-id') != companyId) {
                    $opt.remove();
                }
            });

            var keep = prevVals.filter(function (v) {
                return $branch.find('option[value="' + v + '"]').length;
            });
            $branch.val(keep);

            $branch.select2({ width: '100%', placeholder: 'Select Branch', allowClear: true });
        }

        $company.on('change', refreshBranch);
        refreshBranch();
    }
});
</script>
@endpush