@extends('layouts.app')
@section('content')
<style>
.exp-form-page { background:#f4f6fb; }

/* ── Compact header ── */
.exp-form-header {
    background: linear-gradient(135deg,#e53e3e 0%,#c53030 100%);
    border-radius: 10px; padding: 14px 20px; color: #fff;
    margin-bottom: 18px; position: relative; overflow: hidden;
}
.exp-form-header::before { content:''; position:absolute; top:-30px; right:-30px; width:120px; height:120px; background:rgba(255,255,255,.07); border-radius:50%; }
.exp-form-header h4 { font-size: 16px; font-weight: 800; margin: 0 0 2px; }
.exp-form-header .sub { font-size: 12px; opacity: .8; }

/* ── Cards ── */
.ef-card { background:#fff; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,.06); margin-bottom:18px; overflow:hidden; }
.ef-card-header { display:flex; align-items:center; gap:10px; padding:12px 18px; border-bottom:1px solid #f0f2f7; background:#fafbff; }
.ef-card-header .ch-icon { width:32px; height:32px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; }
.ef-card-header h6 { margin:0; font-size:13px; font-weight:700; color:#1a2340; }
.ef-card-body { padding:18px; }
.ef-label { font-size:12px; font-weight:700; color:#596579; margin-bottom:5px; display:block; }
.ef-label .req { color:#e53e3e; }
.ef-input { min-height:44px; border-color:#d7dce5; color:#303549; font-size:13px; border-radius:8px; }
.ef-input:focus { border-color:#e53e3e; box-shadow:0 0 0 2px rgba(229,62,62,.12); }

/* ── Category pills ── */
.cat-pills { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px; }
.cat-pill { cursor:pointer; }
.cat-pill input { display:none; }
.cat-pill span {
    display:inline-flex; align-items:center; gap:6px;
    padding:7px 14px; border-radius:8px;
    background:#f4f6fb; color:#8a94a6;
    font-size:12px; font-weight:700;
    border:2px solid transparent; transition:all .15s;
    white-space:nowrap;
}
.cat-pill input:checked + span { border-color:currentColor; box-shadow:0 2px 8px rgba(0,0,0,.1); }

/* ── Add category modal ── */
.color-swatch { width:36px; height:36px; border-radius:7px; cursor:pointer; display:flex; align-items:center; justify-content:center; border:3px solid transparent; transition:all .15s; font-size:16px; }
.color-swatch.selected, .color-swatch:hover { border-color:#1a2340; transform:scale(1.1); }

/* ── Bill upload ── */
.bill-upload-zone { border:2px dashed #fecaca; border-radius:10px; padding:18px; text-align:center; cursor:pointer; transition:all .2s; background:#fff5f5; }
.bill-upload-zone:hover { border-color:#e53e3e; background:#fff0f0; }
.bill-upload-zone i { font-size:26px; color:#fecaca; display:block; margin-bottom:6px; }
.bill-upload-zone .buz-title { font-size:13px; font-weight:600; color:#e53e3e; }
.bill-upload-zone .buz-sub { font-size:11px; color:#b0bac9; }

/* ── Sticky footer ── */
.sticky-footer { position:sticky; bottom:0; background:#fff; border-top:2px solid #f0f2f7; padding:14px 18px; border-radius:10px 10px 0 0; box-shadow:0 -4px 16px rgba(0,0,0,.08); display:flex; justify-content:space-between; align-items:center; gap:12px; z-index:100; }
.btn-cancel-exp { background:#f4f6fb; color:#596579; border:1.5px solid #e5e8ee; border-radius:8px; padding:9px 20px; font-size:13px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
.btn-cancel-exp:hover { background:#e8ecf3; color:#596579; text-decoration:none; }
.btn-save-exp { background:linear-gradient(135deg,#e53e3e,#c53030); color:#fff; border:none; border-radius:8px; padding:10px 26px; font-size:13px; font-weight:700; cursor:pointer; box-shadow:0 4px 14px rgba(229,62,62,.35); display:inline-flex; align-items:center; gap:6px; }
.btn-save-exp:hover:not(:disabled) { box-shadow:0 6px 20px rgba(229,62,62,.45); transform:translateY(-1px); }
.btn-save-exp:disabled { opacity:.6; cursor:not-allowed; }

/* ── Select2 within this page ── */
.ef-select2 .select2-container--default .select2-selection--single {
    height:44px !important; min-height:44px !important;
    border-color:#d7dce5 !important; border-radius:8px !important;
}
.ef-select2 .select2-container--default.select2-container--focus .select2-selection--single,
.ef-select2 .select2-container--default.select2-container--open  .select2-selection--single {
    border-color:#e53e3e !important; box-shadow:0 0 0 2px rgba(229,62,62,.12) !important;
}
</style>

<div class="pcoded-inner-content exp-form-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

{{-- COMPACT HEADER --}}
<div class="exp-form-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;letter-spacing:.5px;margin-bottom:5px;">
                <i class="ti-plus"></i> New Expense
            </div>
            <h4>Add Expense Entry</h4>
            <div class="sub">Record a new expense with category, amount and bill.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('expense') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;">
                <i class="ti-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

<form id="expForm" action="{{ route('expense.store') }}" method="POST" enctype="multipart/form-data">
@csrf

<div class="row">
<div class="col-lg-8">

    {{-- EXPENSE DETAILS --}}
    <div class="ef-card ef-select2">
        <div class="ef-card-header">
            <div class="ch-icon" style="background:#fff5f5;color:#e53e3e;"><i class="ti-receipt"></i></div>
            <h6>Expense Details</h6>
        </div>
        <div class="ef-card-body">

            {{-- CATEGORY + TRADER — same row ─────────────────── --}}
            <script id="tradersByCategoryData" type="application/json">{!! json_encode($tradersByCategory) !!}</script>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="ef-label">Expense Category <span class="req">*</span></label>
                        <select name="category" id="categorySelect" class="form-control ef-input select2-exp" required>
                            <option value="">— Select Category —</option>
                            @foreach($categories as $key => $cat)
                            <option value="{{ $key }}"
                                data-icon="{{ $cat['icon'] }}"
                                data-color="{{ $cat['color'] }}"
                                data-bg="{{ $cat['bg'] }}"
                                {{ old('category') === $key ? 'selected' : '' }}>
                                {{ $cat['label'] }}
                            </option>
                            @endforeach
                            <option value="__add_new__" data-icon="ti-plus" data-color="#e53e3e" data-bg="#fff5f5">＋ Add New Category</option>
                        </select>
                        <input type="hidden" id="categoryHidden" value="{{ old('category') }}">
                        @error('category')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group" id="traderGroup">
                        <label class="ef-label" style="display:flex;align-items:center;justify-content:space-between;">
                            <span>Trader <span id="traderCategoryBadge" style="display:none;margin-left:6px;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;background:#fff5f5;color:#e53e3e;border:1px solid #fca5a5;"></span></span>
                            <button type="button" id="quickAddTraderBtn"
                                style="background:none;border:none;padding:0;font-size:11px;font-weight:700;color:#e53e3e;cursor:pointer;display:flex;align-items:center;gap:3px;">
                                <i class="ti-plus"></i> Add Trader
                            </button>
                        </label>
                        <select name="trader_id" id="traderSelect" class="form-control ef-input" disabled>
                            <option value="">— Select category first —</option>
                        </select>
                        <div id="noTradersMsg" style="display:none;font-size:11px;color:#e53e3e;margin-top:4px;">
                            <i class="ti-info-alt mr-1"></i>No traders for this category. Click <strong>Add Trader</strong> to create one.
                        </div>
                        @error('trader_id')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>



            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="ef-label">Amount <span class="req">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="min-height:44px;border-color:#d7dce5;background:#f8f9fa;font-weight:700;border-radius:8px 0 0 8px;">₹</span>
                            </div>
                            <input type="number" step="0.01" min="0" name="amount" id="amountInput"
                                class="form-control ef-input @error('amount') is-invalid @enderror"
                                value="{{ old('amount') }}" placeholder="0.00" required
                                style="border-radius:0 8px 8px 0;">
                        </div>
                        @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="ef-label">Payment Mode</label>
                        <select name="payment_mode" class="form-control ef-input select2-exp">
                            <option value="cash"   {{ old('payment_mode','cash') === 'cash'   ? 'selected' : '' }}>💵 Cash</option>
                            <option value="credit" {{ old('payment_mode') === 'credit' ? 'selected' : '' }}>💳 Credit</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="ef-label">Expense Date <span class="req">*</span></label>
                        <input type="date" name="expense_date"
                            class="form-control ef-input @error('expense_date') is-invalid @enderror"
                            value="{{ old('expense_date', date('Y-m-d')) }}" required>
                        @error('expense_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="ef-label">Link to Trip</label>
                        <select name="trip_id" id="tripSelect" class="form-control ef-input select2-exp @error('trip_id') is-invalid @enderror">
                            <option value="">— Select Trip (Optional) —</option>
                            @foreach($trips as $t)
                            <option value="{{ $t->id }}"
                                data-date="{{ $t->trip_date ? $t->trip_date->format('Y-m-d') : '' }}"
                                data-vehicle="{{ $t->vehicle_id ?? '' }}"
                                data-driver="{{ $t->driver_id ?? '' }}"
                                {{ old('trip_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->trip_no }}
                                @if($t->trip_date) ({{ $t->trip_date->format('d M Y') }})@endif
                                — {{ $t->from_location }} → {{ $t->to_location }}
                            </option>
                            @endforeach
                        </select>
                        <div id="tripAutoFillMsg" style="display:none;font-size:11px;color:#38a169;margin-top:4px;">
                            <i class="ti-check mr-1"></i>Date, Vehicle &amp; Driver auto-filled from trip.
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="ef-label">Vehicle</label>
                        <select name="vehicle_id" id="vehicleSelect" class="form-control ef-input select2-exp @error('vehicle_id') is-invalid @enderror">
                            <option value="">— Select Vehicle (Optional) —</option>
                            @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->vehicle_number }}{{ $v->vehicle_name ? ' — '.$v->vehicle_name : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="ef-label">Driver</label>
                        <select name="driver_id" id="driverSelect" class="form-control ef-input select2-exp @error('driver_id') is-invalid @enderror">
                            <option value="">— Select Driver (Optional) —</option>
                            @foreach($drivers as $d)
                            <option value="{{ $d->id }}" {{ old('driver_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->name }}{{ $d->mobile ? ' — '.$d->mobile : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="ef-label">Approval Status</label>
                        <select name="status" class="form-control ef-input select2-exp">
                            <option value="pending"   {{ old('status','pending') === 'pending'   ? 'selected' : '' }}>Pending Approval</option>
                            <option value="approved"  {{ old('status') === 'approved'  ? 'selected' : '' }}>Approved</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="ef-label">Notes</label>
                <textarea name="notes" rows="3" class="form-control ef-input"
                    placeholder="Describe the expense...">{{ old('notes') }}</textarea>
            </div>

        </div>
    </div>

</div>
<div class="col-lg-4">

    {{-- BILL UPLOAD --}}
    <div class="ef-card">
        <div class="ef-card-header">
            <div class="ch-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-files"></i></div>
            <h6>Bill / Receipt</h6>
        </div>
        <div class="ef-card-body">
            <div class="bill-upload-zone" onclick="document.getElementById('billFileInput').click()">
                <input type="file" id="billFileInput" name="bill_image" style="display:none;" accept=".jpg,.jpeg,.png,.pdf">
                <i class="ti-cloud-up"></i>
                <div class="buz-title">Upload Bill / Receipt</div>
                <div class="buz-sub">JPG, PNG, PDF — Max 5MB</div>
            </div>
            <div id="billPreview" class="mt-2"></div>
        </div>
    </div>

    {{-- TIPS --}}
    <div class="ef-card">
        <div class="ef-card-header">
            <div class="ch-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-info-alt"></i></div>
            <h6>Quick Tips</h6>
        </div>
        <div class="ef-card-body">
            <div style="font-size:12px;color:#8a94a6;line-height:1.9;">
                <div><i class="ti-check mr-1" style="color:#38a169;"></i> Attach a bill for fuel expenses</div>
                <div><i class="ti-check mr-1" style="color:#38a169;"></i> Link to a trip for better tracking</div>
                <div><i class="ti-check mr-1" style="color:#38a169;"></i> Pending expenses need manager approval</div>
                <div><i class="ti-check mr-1" style="color:#38a169;"></i> Add notes for clarity</div>
            </div>
        </div>
    </div>

</div>
</div>

{{-- STICKY FOOTER --}}
<div class="sticky-footer">
    <div style="font-size:11px;color:#8a94a6;"><kbd style="background:#f0f2f7;padding:2px 6px;border-radius:4px;font-family:monospace;font-size:10px;border:1px solid #d7dce5;">Ctrl+S</kbd> to save</div>
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="{{ route('expense') }}" class="btn-cancel-exp"><i class="ti-arrow-left"></i> Back</a>
        <button type="submit" class="btn-save-exp" id="saveExpBtn">
            <i class="ti-save" id="saveBtnIcon"></i>
            <span id="saveBtnText">Save Expense</span>
        </button>
    </div>
</div>

</form>

{{-- ADD CATEGORY MODAL --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;" role="document">
        <div class="modal-content" style="border:none;border-radius:12px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#e53e3e,#c53030);color:#fff;border:none;padding:14px 20px;">
                <h6 class="modal-title" style="font-weight:700;margin:0;"><i class="ti-plus mr-2"></i>Add Expense Category</h6>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="padding:20px;">

                <div class="form-group">
                    <label class="ef-label">Category Name <span class="req">*</span></label>
                    <input type="text" id="newCatName" class="form-control ef-input"
                        placeholder="e.g. Insurance, Cleaning..." maxlength="50">
                    <div id="newCatNameError" class="text-danger" style="font-size:11px;display:none;">Name is required.</div>
                </div>

                <div class="form-group">
                    <label class="ef-label">Icon <span class="req">*</span></label>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;" id="iconPicker">
                        @foreach([
                            'ti-dropbox'=>'Fuel','ti-map'=>'Map','ti-user'=>'Person',
                            'ti-cup'=>'Food','ti-settings'=>'Settings','ti-wrench'=>'Tools',
                            'ti-location-pin'=>'Location','ti-bag'=>'Bag','ti-home'=>'Home',
                            'ti-package'=>'Package','ti-credit-card'=>'Card','ti-more-alt'=>'Other'
                        ] as $ic => $il)
                        <div class="color-swatch" data-icon="{{ $ic }}" title="{{ $il }}"
                            style="background:#f4f6fb;color:#596579;width:42px;height:42px;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;border:2px solid transparent;">
                            <i class="{{ $ic }}" style="font-size:17px;pointer-events:none;"></i>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" id="newCatIcon" value="">
                    <div id="newCatIconError" class="text-danger" style="font-size:11px;display:none;">Select an icon.</div>
                </div>

                <div class="form-group">
                    <label class="ef-label">Color <span class="req">*</span></label>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;" id="colorPicker">
                        @foreach([
                            '#e53e3e'=>'#fff5f5','#7c3aed'=>'#f5f3ff','#38a169'=>'#f0fff4',
                            '#d97706'=>'#fffbeb','#0369a1'=>'#f0f9ff','#b45309'=>'#fff8e1',
                            '#667eea'=>'#eef2ff','#8a94a6'=>'#f4f6fb'
                        ] as $clr => $clrbg)
                        <div class="color-swatch" data-color="{{ $clr }}" data-bg="{{ $clrbg }}"
                            style="background:{{ $clrbg }};border-color:{{ $clr }};width:38px;height:38px;border-radius:8px;border:2px solid {{ $clr }};cursor:pointer;">
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" id="newCatColor" value="">
                    <input type="hidden" id="newCatBg" value="">
                    <div id="newCatColorError" class="text-danger" style="font-size:11px;display:none;">Select a color.</div>
                </div>

                {{-- Preview --}}
                <div id="newCatPreview" style="margin-top:4px;min-height:38px;"></div>

            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f2f7;padding:12px 20px;gap:8px;">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius:8px;">Cancel</button>
                <button type="button" id="saveCategoryBtn"
                    class="btn btn-sm" style="background:linear-gradient(135deg,#e53e3e,#c53030);color:#fff;border:none;border-radius:8px;font-weight:600;padding:7px 18px;">
                    <i class="ti-save mr-1"></i> Save Category
                </button>
            </div>
        </div>
    </div>
</div>

{{-- QUICK ADD TRADER MODAL --}}
<div class="modal fade" id="addTraderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;" role="document">
        <div class="modal-content" style="border:none;border-radius:12px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#e53e3e,#c53030);color:#fff;border:none;padding:14px 20px;">
                <h6 class="modal-title" style="font-weight:700;margin:0;"><i class="ti-plus mr-2"></i><span id="addTraderModalTitle">Quick Add Trader</span></h6>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <div class="form-group">
                    <label class="ef-label">Expense Category</label>
                    <select id="newTraderCategory" class="form-control ef-input" data-placeholder="— All Categories (Global) —">
                        <option value="">— All Categories (Global) —</option>
                        @foreach($categories as $key => $cat)
                        <option value="{{ $key }}">{{ $cat['label'] }}</option>
                        @endforeach
                    </select>
                    <small style="color:#8a94a6;font-size:11px;margin-top:3px;display:block;">
                        <i class="ti-info-alt mr-1"></i>Leave blank to show under all categories.
                    </small>
                </div>
                <div class="form-group">
                    <label class="ef-label">Trader Name <span class="req">*</span></label>
                    <input type="text" id="newTraderName" class="form-control ef-input" placeholder="Enter trader name" maxlength="255">
                    <div id="newTraderNameError" class="text-danger" style="font-size:11px;display:none;">Name is required.</div>
                </div>
                <div class="form-group">
                    <label class="ef-label">Phone</label>
                    <input type="text" id="newTraderPhone" class="form-control ef-input" placeholder="e.g. 9876543210" maxlength="20">
                </div>
                <div class="form-group">
                    <label class="ef-label">Address</label>
                    <textarea id="newTraderAddress" class="form-control ef-input" placeholder="Enter address..." rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f2f7;padding:12px 20px;gap:8px;">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius:8px;">Cancel</button>
                <button type="button" id="saveQuickTraderBtn" class="btn btn-sm" style="background:linear-gradient(135deg,#e53e3e,#c53030);color:#fff;border:none;border-radius:8px;font-weight:600;padding:7px 18px;">
                    <i class="ti-save mr-1"></i> Save Trader
                </button>
            </div>
        </div>
    </div>
</div>

</div></div></div></div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    /* ── Category dropdown: select2 with formatted options ── */
    function formatCategoryOption(opt) {
        if (!opt.id) return opt.text;
        var icon  = $(opt.element).data('icon')  || 'ti-tag';
        var color = $(opt.element).data('color') || '#596579';
        var bg    = $(opt.element).data('bg')    || '#f4f6fb';

        if (opt.id === '__add_new__') {
            return $('<span style="display:inline-flex;align-items:center;gap:8px;color:#e53e3e;font-weight:700;font-size:13px;border-top:1px dashed #fca5a5;padding-top:4px;margin-top:4px;">' +
                '<span style="width:28px;height:28px;border-radius:6px;background:#fff5f5;display:inline-flex;align-items:center;justify-content:center;"><i class="ti-plus"></i></span>' +
                ' Add New Category</span>');
        }

        return $('<span style="display:inline-flex;align-items:center;gap:8px;">' +
            '<span style="width:28px;height:28px;border-radius:6px;background:' + bg + ';color:' + color + ';display:inline-flex;align-items:center;justify-content:center;font-size:13px;"><i class="' + icon + '"></i></span>' +
            '<span style="font-weight:600;font-size:13px;">' + opt.text + '</span>' +
            '</span>');
    }

    function formatCategorySelection(opt) {
        if (!opt.id || opt.id === '__add_new__') return opt.text;
        var icon  = $(opt.element).data('icon')  || 'ti-tag';
        var color = $(opt.element).data('color') || '#596579';
        var bg    = $(opt.element).data('bg')    || '#f4f6fb';
        return $('<span style="display:inline-flex;align-items:center;gap:7px;">' +
            '<span style="width:22px;height:22px;border-radius:5px;background:' + bg + ';color:' + color + ';display:inline-flex;align-items:center;justify-content:center;font-size:12px;"><i class="' + icon + '"></i></span>' +
            '<span style="font-weight:600;">' + opt.text + '</span>' +
            '</span>');
    }

    if ($.fn.select2) {
        $('#categorySelect').select2({
            width: '100%',
            placeholder: '— Select Category —',
            allowClear: true,
            templateResult:    formatCategoryOption,
            templateSelection: formatCategorySelection,
        });
    }

    /* ── Category change handler — use select2:select/clear to avoid init-fire ── */
    $('#categorySelect').on('select2:select select2:clear', function () {
        var val = $(this).val() || '';
        if (val === '__add_new__') {
            $(this).val($('#categoryHidden').val() || '').trigger('change');
            openAddCategoryModal();
            return;
        }
        $('#categoryHidden').val(val);
        toggleAccessoriesFields();
    });

    /* ── Select2 for other selects (not category or trader) ── */
    if ($.fn.select2) {
        $('.select2-exp').not('#traderSelect').not('#categorySelect').each(function () {
            $(this).select2({ width: '100%', allowClear: true, placeholder: $(this).find('option:first').text() });
        });
    }

    /* ── Trip → auto-fill Date, Vehicle, Driver ─────────────── */
    $('#tripSelect').on('change', function () {
        var $opt       = $(this).find('option:selected');
        var tripDate   = $opt.data('date')    || '';
        var vehicleId  = $opt.data('vehicle') || '';
        var driverId   = $opt.data('driver')  || '';

        if (!$(this).val()) {
            // Cleared — reset helper message only, leave fields as-is
            $('#tripAutoFillMsg').hide();
            return;
        }

        // Set expense date
        if (tripDate) {
            $('input[name="expense_date"]').val(tripDate);
        }

        // Set vehicle
        if (vehicleId) {
            if ($.fn.select2 && $('#vehicleSelect').data('select2')) {
                $('#vehicleSelect').val(String(vehicleId)).trigger('change');
            } else {
                $('#vehicleSelect').val(String(vehicleId));
            }
        }

        // Set driver
        if (driverId) {
            if ($.fn.select2 && $('#driverSelect').data('select2')) {
                $('#driverSelect').val(String(driverId)).trigger('change');
            } else {
                $('#driverSelect').val(String(driverId));
            }
        }

        if (tripDate || vehicleId || driverId) {
            $('#tripAutoFillMsg').show();
        }
    });

    // On page load, if old trip_id is set (validation re-render), trigger auto-fill
    if ($('#tripSelect').val()) {
        $('#tripSelect').trigger('change');
    }

    /* ── Bill preview ───────────────────────────────────────── */
    $('#billFileInput').on('change', function () {
        var file = this.files[0];
        if (!file) return;
        var ext  = file.name.split('.').pop().toLowerCase();
        var size = (file.size / 1024).toFixed(1) + ' KB';
        var $p   = $('#billPreview').empty();
        if (['jpg','jpeg','png'].indexOf(ext) !== -1) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $p.html('<img src="' + e.target.result + '" style="width:100%;border-radius:8px;border:2px solid #fecaca;">');
            };
            reader.readAsDataURL(file);
        } else {
            $p.html('<div style="background:#f4f6fb;border-radius:8px;padding:10px;font-size:12px;display:flex;align-items:center;gap:8px;"><i class="ti-file" style="color:#e53e3e;font-size:18px;"></i><span style="font-weight:600;">' + file.name + '</span><span style="color:#8a94a6;">' + size + '</span></div>');
        }
    });

    /* ── Add Category Modal ─────────────────────────────────── */
    function openAddCategoryModal() {
        $('#newCatName').val('');
        $('#newCatIcon').val('');
        $('#newCatColor').val('');
        $('#newCatBg').val('');
        $('#newCatPreview').html('');
        $('#iconPicker .color-swatch').css('border-color','transparent').removeClass('selected');
        $('#colorPicker .color-swatch').css('outline','').removeClass('selected');
        $('#newCatNameError,#newCatIconError,#newCatColorError').hide();
        $('#addCategoryModal').modal('show');
    }

    // Legacy button (kept in modal area) also triggers the same function
    $('#addNewCategoryBtn').on('click', openAddCategoryModal);

    /* Icon picker */
    $('#iconPicker').on('click', '.color-swatch', function () {
        $('#iconPicker .color-swatch').css({'border-color':'transparent','background':'#f4f6fb','color':'#596579'});
        $(this).css({'border-color':'#e53e3e','background':'#fff5f5','color':'#e53e3e'}).addClass('selected');
        $('#newCatIcon').val($(this).data('icon'));
        updateNewCatPreview();
    });

    /* Color picker */
    $('#colorPicker').on('click', '.color-swatch', function () {
        $('#colorPicker .color-swatch').css('outline','').removeClass('selected');
        $(this).css('outline','3px solid #1a2340').addClass('selected');
        $('#newCatColor').val($(this).data('color'));
        $('#newCatBg').val($(this).data('bg'));
        updateNewCatPreview();
    });

    /* Live preview inside modal */
    $('#newCatName').on('input', updateNewCatPreview);

    function updateNewCatPreview() {
        var name  = $('#newCatName').val().trim() || 'Preview';
        var icon  = $('#newCatIcon').val();
        var color = $('#newCatColor').val();
        var bg    = $('#newCatBg').val();
        if (!color) { $('#newCatPreview').html(''); return; }
        $('#newCatPreview').html(
            '<div style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;background:' + bg + ';color:' + color + ';border:2px solid ' + color + ';font-size:12px;font-weight:700;">' +
            (icon ? '<i class="' + icon + '"></i> ' : '') + name + '</div>'
        );
    }

    /* Save category via AJAX */
    $('#saveCategoryBtn').on('click', function () {
        var name  = $('#newCatName').val().trim();
        var icon  = $('#newCatIcon').val();
        var color = $('#newCatColor').val();
        var bg    = $('#newCatBg').val();

        var valid = true;
        if (!name)  { $('#newCatNameError').show();  valid = false; } else { $('#newCatNameError').hide(); }
        if (!icon)  { $('#newCatIconError').show();  valid = false; } else { $('#newCatIconError').hide(); }
        if (!color) { $('#newCatColorError').show(); valid = false; } else { $('#newCatColorError').hide(); }
        if (!valid) return;

        var $btn = $(this).prop('disabled', true).html('<i class="ti-reload mr-1"></i> Saving...');

        $.ajax({
            url:  '{{ route('expense.category.store') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                label:  name,
                icon:   icon,
                color:  color,
                bg:     bg,
            },
            success: function (res) {
                if (res.success) {
                    // Add new option to the select (before the "Add New Category" option)
                    var $newOpt = $('<option>', {
                        value:  res.key,
                        text:   res.label,
                        'data-icon':  res.icon,
                        'data-color': res.color,
                        'data-bg':    res.bg,
                    });
                    // Insert before the last option ("+ Add New Category")
                    $('#categorySelect option:last').before($newOpt);

                    // Select the new category
                    $('#categorySelect').val(res.key).trigger('change');

                    $('#addCategoryModal').modal('hide');

                    // Toast
                    if (typeof toastr !== 'undefined') {
                        toastr.success(res.label + ' category added');
                    } else {
                        var $toast = $('<div style="position:fixed;bottom:24px;right:24px;background:#1a2340;color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.2);z-index:9999;"><i class=\'ti-check mr-2\' style=\'color:#48bb78;\'></i>' + res.label + ' category added</div>');
                        $('body').append($toast);
                        setTimeout(function () { $toast.fadeOut(300, function () { $(this).remove(); }); }, 2500);
                    }
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to save category.';
                alert(msg);
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="ti-save mr-1"></i> Save Category');
            }
        });
    });

    /* ── Traders by category data ───────────────────────────── */
    var tradersByCat = {};
    try {
        tradersByCat = JSON.parse(document.getElementById('tradersByCategoryData').textContent || '{}');
    } catch(e) {}

    var oldTraderVal = '{{ old('trader_id') }}';

    /**
     * Rebuild the #traderSelect options for the given category key.
     * Merges: traders matching that category key + global traders (null category → '_global').
     */
    function rebuildTraderDropdown(catKey) {
        var $sel = $('#traderSelect');

        // Destroy select2 before modifying options
        if ($.fn.select2 && $sel.data('select2')) {
            $sel.select2('destroy');
        }

        $sel.empty().append('<option value="">— Select Trader —</option>');

        // If no category selected → leave empty, show hint
        if (!catKey) {
            $('#noTradersMsg').hide();
            $sel.prop('disabled', true).css('background', '#f8fafc');
            if ($.fn.select2) {
                $sel.select2({ width: '100%', allowClear: true, placeholder: '— Select category first —' });
            }
            return;
        }

        $sel.prop('disabled', false).css('background', '');

        var catTraders    = tradersByCat[catKey]    || [];
        var globalTraders = tradersByCat['_global'] || [];

        // Category-specific + globals, no duplicates
        var seen = {};
        var merged = [];
        catTraders.concat(globalTraders).forEach(function(t) {
            if (!seen[t.id]) { seen[t.id] = true; merged.push(t); }
        });

        if (merged.length === 0) {
            $('#noTradersMsg').show();
        } else {
            $('#noTradersMsg').hide();
            merged.forEach(function(t) {
                var opt = new Option(t.name, t.id, false, String(t.id) === String(oldTraderVal));
                $sel.append(opt);
            });
        }

        // Re-init select2
        if ($.fn.select2) {
            $sel.select2({ width: '100%', allowClear: true, placeholder: '— Select Trader —' });
        }
    }

    /* ── Category Toggle: show trader dropdown + accessory table ── */
    function toggleAccessoriesFields() {
        var selectedCat = $('#categorySelect').val() || '';

        if (!selectedCat || selectedCat === '__add_new__') {
            // No category selected — clear trader dropdown and disable it
            rebuildTraderDropdown(null);
            $('#traderCategoryBadge').text('');
            $('#accessoriesTableGroup').slideUp(200);
            $('#amountInput').prop('readonly', false).css('background', '');
            return;
        }

        // Update badge label in trader group
        var catLabel = $('#categorySelect option:selected').text().trim();
        $('#traderCategoryBadge').text(catLabel);

        // Rebuild trader dropdown filtered to selected category
        rebuildTraderDropdown(selectedCat);

        if (selectedCat === 'accessories') {
            $('#accessoriesTableGroup').slideDown(200);
            $('#amountInput').prop('readonly', false).css('background', '');
            calculateAccessoriesTotal();
        } else {
            $('#accessoriesTableGroup').slideUp(200);
            $('#amountInput').prop('readonly', false).css('background', '');
        }
    }

    // No longer need pill change handler; dropdown change already calls toggleAccessoriesFields()
    toggleAccessoriesFields();

    /* ── Accessories Calculations ───────────────────────────── */
    function calculateAccessoriesTotal() {
        var grandTotal = 0;
        $('#accessoriesTableBody tr').each(function () {
            var qty = parseInt($(this).find('.acc-qty').val()) || 0;
            var price = parseFloat($(this).find('.acc-price').val()) || 0;
            var rowTotal = qty * price;
            $(this).find('.acc-total').val(rowTotal.toFixed(2));
            grandTotal += rowTotal;
        });
        $('#amountInput').val(grandTotal.toFixed(2));
    }

    $('#accessoriesTableBody').on('input change', '.acc-qty, .acc-price', calculateAccessoriesTotal);

    /* ── Add Accessory Row ──────────────────────────────────── */
    var accRowIdx = 1;
    $('#addAccessoryRowBtn').on('click', function () {
        var newRow = 
            '<tr class="accessory-row">' +
            '<td><input type="text" name="accessories[' + accRowIdx + '][accessory_name]" class="form-control form-control-sm acc-name" required placeholder="e.g. Tyres, Horn"></td>' +
            '<td><input type="number" name="accessories[' + accRowIdx + '][quantity]" class="form-control form-control-sm acc-qty" value="1" min="1" required></td>' +
            '<td><input type="number" step="0.01" name="accessories[' + accRowIdx + '][price]" class="form-control form-control-sm acc-price" value="0.00" min="0" required></td>' +
            '<td><input type="text" class="form-control form-control-sm acc-total" value="0.00" readonly></td>' +
            '<td style="text-align:center;"><button type="button" class="btn btn-sm btn-danger remove-acc-row" style="padding:4px 8px;border-radius:6px;"><i class="ti-trash"></i></button></td>' +
            '</tr>';
        $('#accessoriesTableBody').append(newRow);
        accRowIdx++;
        calculateAccessoriesTotal();
    });

    /* ── Remove Accessory Row ───────────────────────────────── */
    $('#accessoriesTableBody').on('click', '.remove-acc-row', function () {
        if ($('#accessoriesTableBody tr').length > 1) {
            $(this).closest('tr').remove();
            calculateAccessoriesTotal();
        } else {
            alert('At least one accessory row is required when category is Accessories.');
        }
    });

    /* ── Quick Add Trader Modal ────────────────────────────── */
    $('#addTraderModal').on('shown.bs.modal', function () {
        var preselect = $(this).data('preselect-cat') || '';
        // Plain native select — no select2 needed for a small list
        document.getElementById('newTraderCategory').value = preselect;
        $('#newTraderName').focus();
    });

    $('#quickAddTraderBtn').on('click', function () {
        var selectedCat = $('#categorySelect').val() || '';
        if (selectedCat === '__add_new__') selectedCat = '';

        // Store pre-selected category — applied in shown.bs.modal above
        $('#addTraderModal').data('preselect-cat', selectedCat);

        // Reset fields
        $('#newTraderName').val('');
        $('#newTraderPhone').val('');
        $('#newTraderAddress').val('');
        $('#newTraderNameError').hide();
        $('#addTraderModal').modal('show');
    });

    $('#saveQuickTraderBtn').on('click', function () {
        var name     = $('#newTraderName').val().trim();
        var phone    = $('#newTraderPhone').val().trim();
        var address  = $('#newTraderAddress').val().trim();
        var category = $('#newTraderCategory').val();

        if (!name) {
            $('#newTraderNameError').show();
            return;
        } else {
            $('#newTraderNameError').hide();
        }

        var $btn = $(this).prop('disabled', true).html('<i class="ti-reload mr-1"></i> Saving...');

        $.ajax({
            url: '{{ route('trader.store') }}',
            type: 'POST',
            data: {
                _token:   '{{ csrf_token() }}',
                name:     name,
                phone:    phone,
                address:  address,
                category: category || null
            },
            success: function (res) {
                if (res.trader) {
                    // Add to the in-memory map so it appears next time the same category is selected
                    var catKey = res.trader.category || '_global';
                    if (!tradersByCat[catKey]) tradersByCat[catKey] = [];
                    tradersByCat[catKey].push({ id: res.trader.id, name: res.trader.name });

                    // Rebuild dropdown and select the new trader
                    var currentCat = $('#categorySelect').val() || '';
                    if (currentCat === '__add_new__') currentCat = '';
                    rebuildTraderDropdown(currentCat);

                    if ($.fn.select2 && $('#traderSelect').data('select2')) {
                        $('#traderSelect').val(String(res.trader.id)).trigger('change');
                    } else {
                        $('#traderSelect').val(String(res.trader.id));
                    }

                    $('#addTraderModal').modal('hide');

                    // Toast
                    var $toast = $('<div style="position:fixed;bottom:24px;right:24px;background:#1a2340;color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.2);z-index:9999;"><i class=\'ti-check mr-2\' style=\'color:#48bb78;\'></i>' + res.trader.name + ' added as trader</div>');
                    $('body').append($toast);
                    setTimeout(function () { $toast.fadeOut(300, function () { $(this).remove(); }); }, 2500);
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to save trader.';
                alert(msg);
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="ti-save mr-1"></i> Save Trader');
            }
        });
    });

    /* ── Form submit ────────────────────────────────────────── */
    $('#expForm').on('submit', function () {
        $('#saveExpBtn').prop('disabled', true);
        $('#saveBtnIcon').removeClass('ti-save').addClass('ti-reload');
        $('#saveBtnText').text('Saving...');
    });

    /* ── Ctrl+S shortcut ────────────────────────────────────── */
    $(document).on('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if (!$('#saveExpBtn').prop('disabled')) $('#expForm').submit();
        }
    });
});
</script>
@endpush
