@extends('layouts.app')
@section('title', 'Salary Advances')

@section('content')
<style>
.pr-page{background:#f4f6fb;}
.pr-header{background:linear-gradient(135deg,#d97706 0%,#b45309 100%);border-radius:10px;padding:14px 20px;color:#fff;margin-bottom:16px;position:relative;overflow:hidden;}
.pr-header::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.07);border-radius:50%;}
.pr-header h4{font-size:16px;font-weight:800;margin:0 0 2px;}
.pr-header .sub{font-size:12px;opacity:.8;}

.pr-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px;}
.pr-stat{background:#fff;border-radius:12px;padding:16px;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;align-items:center;gap:12px;border-left:4px solid transparent;transition:transform .15s;}
.pr-stat:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.1);}
.pr-stat .si{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
.pr-stat .sl{font-size:11px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.4px;}
.pr-stat .sv{font-size:22px;font-weight:800;color:#1a2340;line-height:1.1;}

.pr-card{background:#fff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;margin-bottom:18px;}
.pr-card-hd{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #f0f2f7;background:#fafbff;}
.pr-card-hd h6{margin:0;font-size:13px;font-weight:700;color:#1a2340;}

.btn-adv{background:linear-gradient(135deg,#d97706,#b45309);color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
.btn-back-pr{background:#fff;color:#475569;border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 16px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-back-pr:hover{background:#f8fafc;}
.btn-rec{background:linear-gradient(135deg,#38a169,#2f855a);color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
.btn-rec:hover{background:linear-gradient(135deg,#2f855a,#276749);}

.balance-sum{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:0 0 14px;}
.balance-sum .bs-item{background:#fff;border-radius:8px;padding:12px 14px;border:1px solid #f0f2f7;}
.balance-sum .bs-label{font-size:10px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.3px;}
.balance-sum .bs-val{font-size:18px;font-weight:800;color:#1a2340;line-height:1.2;}

.rec-card{background:#fff;border:1.5px solid #d1d5db;border-radius:10px;margin-bottom:16px;overflow:hidden;}
.rec-card .rec-hd{background:linear-gradient(135deg,#38a169,#2f855a);color:#fff;padding:12px 18px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:8px;}
.rec-body{padding:16px 18px;}
.rec-grid{display:grid;grid-template-columns:1fr 1fr 1.4fr;gap:14px;}
.rec-grid .fg{display:flex;flex-direction:column;gap:4px;}
.rec-grid .fg label{font-size:11px;font-weight:700;color:#475569;letter-spacing:.2px;}
.rec-grid .fg input,.rec-grid .fg select{height:40px;border-radius:6px;border:1.5px solid #d1d5db;padding:0 12px;font-size:13px;background:#fff;transition:border-color .15s,box-shadow .15s;}
.rec-grid .fg input:focus,.rec-grid .fg select:focus{outline:none;border-color:#38a169;box-shadow:0 0 0 3px rgba(56,161,105,.12);}
.rec-grid .fg input[type=file]{padding:8px 12px;height:40px;display:flex;align-items:center;}
.rec-grid .fg input:read-only{background:#f8fafc;color:#1e293b;font-weight:700;font-size:15px;cursor:default;border-color:#e2e8f0;}
.rec-grid .fg .val-err{border-color:#e53e3e !important;box-shadow:0 0 0 3px rgba(229,62,62,.12) !important;}
.rec-actions{display:flex;align-items:end;justify-content:flex-end;gap:8px;padding-top:4px;}
.rec-actions .btn-rec{background:linear-gradient(135deg,#38a169,#2f855a);color:#fff;border:none;border-radius:8px;padding:10px 24px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;height:40px;min-width:100px;justify-content:center;}
.rec-actions .btn-rec:hover{background:linear-gradient(135deg,#2f855a,#276749);}
.rec-feedback{font-size:12px;font-weight:600;margin:8px 0 0;min-height:0;display:flex;align-items:center;gap:5px;}
.rec-feedback.ok{color:#38a169;}
.rec-feedback.err{color:#e53e3e;}
.rec-divider{border:none;border-top:1.5px dashed #e2e8f0;margin:12px 0;}

/* Make select2 match our field height + border */
.rec-select2 + .select2-container .select2-selection--single{height:40px;border:1.5px solid #d1d5db;border-radius:6px;display:flex;align-items:center;}
.rec-select2 + .select2-container .select2-selection--single .select2-selection__rendered{padding-left:12px;font-size:13px;line-height:38px;}
.rec-select2 + .select2-container .select2-selection--single .select2-selection__arrow{height:38px;}
.rec-select2 + .select2-container--focus .select2-selection--single{border-color:#38a169;box-shadow:0 0 0 3px rgba(56,161,105,.12);}

@media(max-width:992px){.rec-grid{grid-template-columns:1fr 1fr;}}
@media(max-width:640px){.rec-grid{grid-template-columns:1fr;}}

.btn-view-pr{background:#eef4fd;color:#2c7be5;border:1px solid #bfdbfe;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:4px;}
.btn-edit-pr{background:#f0fff4;color:#38a169;border:1px solid #9ae6b4;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:4px;}
.btn-del-pr{background:#fff5f5;color:#e53e3e;border:1px solid #fca5a5;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:4px;}

@media(max-width:768px){.pr-stats{grid-template-columns:repeat(2,1fr);}}
</style>

<div class="pcoded-inner-content pr-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

{{-- HEADER --}}
<div class="pr-header">
    <div class="row align-items-center">
        <div class="col-md-7" style="position:relative;z-index:1;">
            <h4><i class="ti-wallet mr-2"></i>Salary Advances</h4>
            <div class="sub">Manage all salary advances &amp; recovery tracking
                @if(!empty($currentFY))
                &nbsp;·&nbsp; <span style="background:rgba(255,255,255,.2);border-radius:6px;padding:1px 8px;font-size:11px;font-weight:700;">FY {{ $currentFY->label }}</span>
                @endif
            </div>
        </div>
        <div class="col-md-5 text-right mt-2 mt-md-0" style="position:relative;z-index:1;display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap;">
            <a href="{{ route('payroll') }}" class="btn-back-pr">
                <i class="ti-arrow-left"></i> Back to Payroll
            </a>
            <button class="btn-adv" data-toggle="modal" data-target="#advanceModal">
                <i class="ti-plus"></i> Add Advance
            </button>
        </div>
    </div>
</div>

@include('partials.flash')

{{-- STATS --}}
@php
$totalAdv   = $advances->sum('amount');
$totalRec   = $advances->sum('recovered_amount');
$totalPend  = $advances->sum(fn($a) => $a->amount - $a->recovered_amount);
$advCount   = $grouped->count();
$totalRows  = $advances->count();
$pendCount  = $advances->whereIn('status', ['pending','partial'])->count();
$recCount   = $advances->where('status', 'recovered')->count();
@endphp
<div class="pr-stats">
    <div class="pr-stat" style="border-left-color:#d97706;">
        <div class="si" style="background:#fffbeb;color:#d97706;"><i class="ti-user"></i></div>
        <div><div class="sl">Employees</div><div class="sv">{{ $advCount }}</div></div>
    </div>
    <div class="pr-stat" style="border-left-color:#d97706;">
        <div class="si" style="background:#fffbeb;color:#d97706;"><i class="ti-money"></i></div>
        <div><div class="sl">Total Amount</div><div class="sv" style="font-size:16px;">₹ {{ number_format($totalAdv,0) }}</div></div>
    </div>
    <div class="pr-stat" style="border-left-color:#38a169;">
        <div class="si" style="background:#f0fff4;color:#38a169;"><i class="ti-check-box"></i></div>
        <div><div class="sl">Recovered</div><div class="sv">{{ $recCount }} <span style="font-size:12px;color:#8a94a6;">(₹ {{ number_format($totalRec,0) }})</span></div></div>
    </div>
    <div class="pr-stat" style="border-left-color:#e53e3e;">
        <div class="si" style="background:#fff5f5;color:#e53e3e;"><i class="ti-alert"></i></div>
        <div><div class="sl">Pending</div><div class="sv">{{ $pendCount }} <span style="font-size:12px;color:#8a94a6;">(₹ {{ number_format($totalPend,0) }})</span></div></div>
    </div>
</div>

{{-- BALANCE SUMMARY & RECOVERY FORM (employee detail only) --}}
@if($employeeFilter)
@php
$empTotal  = $advances->sum('amount');
$empRec    = $advances->sum('recovered_amount');
$empPend   = $empTotal - $empRec;
@endphp
<div class="balance-sum">
    <div class="bs-item" style="border-left:3px solid #d97706;">
        <div class="bs-label">Total Advanced</div>
        <div class="bs-val" style="color:#d97706;">₹ {{ number_format($empTotal,0) }}</div>
    </div>
    <div class="bs-item" style="border-left:3px solid #38a169;">
        <div class="bs-label">Total Recovered</div>
        <div class="bs-val" style="color:#38a169;">₹ {{ number_format($empRec,0) }}</div>
    </div>
    <div class="bs-item" style="border-left:3px solid #e53e3e;">
        <div class="bs-label">Pending Balance</div>
        <div class="bs-val" style="color:#e53e3e;">₹ {{ number_format($empPend,0) }}</div>
    </div>
</div>

<div class="rec-card">
    <div class="rec-hd"><i class="ti-check-box"></i> Record Advance Recovery</div>
    <div class="rec-body">
        <form method="POST" action="{{ route('payroll.advance.recovery.store') }}" enctype="multipart/form-data" id="recForm">
            @csrf
            <input type="hidden" name="employee_name" value="{{ $employeeFilter }}">
            <div class="rec-grid">
                <div class="fg">
                    <label>Date</label>
                    <input type="date" name="recovery_date" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="fg">
                    <label>Expenses Type</label>
                    <select name="category" id="recCategory" class="rec-select2" required style="width:100%;">
                        <option value="">— Select —</option>
                        @foreach($categories as $key => $cat)
                        <option value="{{ $key }}">{{ $cat['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Advance</label>
                    <select name="advance_id" id="recAdvanceId" class="rec-select2" required style="width:100%;">
                        <option value="">— Select Advance —</option>
                        @foreach($advances->whereIn('status', ['pending','partial']) as $adv)
                        <option value="{{ $adv->id }}" data-pending="{{ $adv->pending_amount }}">
                            {{ $adv->advance_date->format('d M Y') }} — ₹ {{ number_format($adv->amount,0) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Balance (₹)</label>
                    <input type="text" id="recBalance" name="balance_display" readonly value="0.00">
                </div>
                <div class="fg">
                    <label>Expense Amount (₹) <span style="color:#e53e3e;">*</span></label>
                    <input type="number" id="recAmount" name="amount" placeholder="0.00" min="0.01" step="0.01" required>
                </div>
                <div class="fg" style="display:flex;flex-direction:row;align-items:end;gap:8px;">
                    <div style="flex:1;display:flex;flex-direction:column;gap:4px;">
                        <label>File Upload</label>
                        <input type="file" name="bill_image" accept="image/*,application/pdf" style="height:40px;padding:8px 12px;border-radius:6px;border:1.5px solid #d1d5db;font-size:13px;background:#fff;">
                    </div>
                    <button type="submit" class="btn-rec"><i class="ti-save"></i> Save</button>
                </div>
            </div>
            <div id="recFeedback" class="rec-feedback"></div>
        </form>
    </div>
</div>
@endif

{{-- ADVANCES TABLE --}}
<div class="pr-card">
    <div class="pr-card-hd">
        <h6><i class="ti-wallet mr-2" style="color:#d97706;"></i>Advance Records @if($employeeFilter) <span style="font-size:12px;color:#8a94a6;font-weight:400;">— {{ $employeeFilter }}</span>@endif</h6>
        <div style="display:flex;gap:6px;">
            @if($employeeFilter)
            <a href="{{ route('payroll.advances') }}" class="btn-back-pr" style="padding:6px 12px;font-size:11px;">
                <i class="ti-arrow-left"></i> Back
            </a>
            @endif
            <button class="btn-adv" data-toggle="modal" data-target="#advanceModal">
                <i class="ti-plus"></i> Add Advance
            </button>
        </div>
    </div>
    <div style="padding:0;">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:13px;">
                @if($employeeFilter)
                {{-- Individual advances for selected employee --}}
                <thead style="background:#f8f9fb;">
                    <tr>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">#</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Type</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Date</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Notes / Trip</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Amount</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Expenses</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Recovered</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Pending</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Status</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($advances->load('trip:id,trip_no', 'expenses') as $i => $adv)
                    @php
                        $advExpenses = $adv->expenses->where('is_deleted', false);
                        $expTotal    = $advExpenses->sum('amount');
                    @endphp
                    <tr>
                        <td style="padding:10px 16px;color:#8a94a6;">{{ $i+1 }}</td>
                        <td style="padding:10px 16px;">
                            @if($adv->trip_id)
                            <span style="font-size:11px;font-weight:700;background:#f5f3ff;color:#7c3aed;border-radius:8px;padding:2px 9px;white-space:nowrap;"><i class="ti-truck mr-1"></i>Trip</span>
                            @else
                            <span style="font-size:11px;font-weight:700;background:#fffbeb;color:#d97706;border-radius:8px;padding:2px 9px;white-space:nowrap;"><i class="ti-wallet mr-1"></i>Normal</span>
                            @endif
                        </td>
                        <td style="padding:10px 16px;color:#596579;white-space:nowrap;">{{ $adv->advance_date->format('d M Y') }}</td>
                        <td style="padding:10px 16px;">
                            @if($adv->trip_id && $adv->trip)
                            <span style="font-size:12px;font-weight:700;color:#7c3aed;">{{ $adv->trip->trip_no }}</span>
                            @endif
                            @if($adv->notes)
                            <div style="font-size:11px;color:#8a94a6;">{{ $adv->notes }}</div>
                            @endif
                        </td>
                        <td style="padding:10px 16px;font-weight:700;color:#d97706;">₹ {{ number_format($adv->amount,2) }}</td>
                        <td style="padding:10px 16px;">
                            @if($expTotal > 0)
                            <span style="font-size:12px;font-weight:700;color:#0369a1;">₹ {{ number_format($expTotal,2) }}</span>
                            <div style="display:flex;flex-wrap:wrap;gap:3px;margin-top:3px;">
                            @foreach($advExpenses->groupBy('category') as $cat => $exps)
                                <span style="font-size:10px;font-weight:700;background:#e0f2fe;color:#0369a1;border-radius:4px;padding:1px 5px;">{{ $cat }}: ₹{{ number_format($exps->sum('amount'),0) }}</span>
                            @endforeach
                            </div>
                            @else
                            <span style="color:#8a94a6;font-size:11px;">—</span>
                            @endif
                        </td>
                        <td style="padding:10px 16px;color:#38a169;font-weight:700;">₹ {{ number_format($adv->recovered_amount,2) }}</td>
                        <td style="padding:10px 16px;font-weight:800;color:#e53e3e;">₹ {{ number_format($adv->pending_amount,2) }}</td>
                        <td style="padding:10px 16px;">{!! $adv->status_badge !!}</td>
                        <td style="padding:10px 16px;text-align:right;">
                            <a href="{{ route('payroll.advance.edit', $adv->id) }}" class="btn-edit-pr" title="Edit"><i class="ti-pencil"></i></a>
                            @if($adv->status !== 'recovered')
                            <button class="btn-del-pr" onclick="confirmDelete('{{ route('payroll.advance.destroy', $adv->id) }}','Delete this advance?')">
                                <i class="ti-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:40px;color:#8a94a6;">
                            <i class="ti-wallet" style="font-size:32px;display:block;margin-bottom:8px;opacity:.3;"></i>
                            No records for this employee.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @else
                {{-- Grouped by employee --}}
                <thead style="background:#f8f9fb;">
                    <tr>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">#</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Employee</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Trip Advance</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Normal Advance</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Total</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Recovered</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Pending</th>
                        <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grouped as $employee => $items)
                    @php
                        $tripAmt   = $items->whereNotNull('trip_id')->sum('amount');
                        $normAmt   = $items->whereNull('trip_id')->sum('amount');
                        $totalAmt  = $tripAmt + $normAmt;
                        $totalRec  = $items->sum('recovered_amount');
                        $totalPend = $items->sum(fn($a) => $a->amount - $a->recovered_amount);
                    @endphp
                    <tr>
                        <td style="padding:12px 16px;color:#8a94a6;">{{ $loop->iteration }}</td>
                        <td style="padding:12px 16px;font-weight:700;color:#1a2340;">{{ $employee }}</td>
                        <td style="padding:12px 16px;color:#7c3aed;font-weight:700;">₹ {{ number_format($tripAmt,2) }}</td>
                        <td style="padding:12px 16px;color:#d97706;font-weight:700;">₹ {{ number_format($normAmt,2) }}</td>
                        <td style="padding:12px 16px;font-weight:800;color:#1a2340;">₹ {{ number_format($totalAmt,2) }}</td>
                        <td style="padding:12px 16px;color:#38a169;font-weight:700;">₹ {{ number_format($totalRec,2) }}</td>
                        <td style="padding:12px 16px;font-weight:800;color:#e53e3e;">₹ {{ number_format($totalPend,2) }}</td>
                        <td style="padding:12px 16px;text-align:right;">
                            <a href="{{ route('payroll.advances', ['employee' => $employee]) }}" class="btn-edit-pr" title="View details"><i class="ti-eye mr-1"></i>Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:#8a94a6;">
                            <i class="ti-wallet" style="font-size:32px;display:block;margin-bottom:8px;opacity:.3;"></i>
                            No advance records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @endif
            </table>
        </div>
    </div>
</div>

</div></div></div></div>

{{-- ADVANCE MODAL --}}
<div class="modal fade" id="advanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;" role="document">
        <div class="modal-content" style="border:none;border-radius:12px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#d97706,#b45309);color:#fff;border:none;padding:14px 20px;">
                <h6 class="modal-title" style="font-weight:700;margin:0;"><i class="ti-wallet mr-2"></i>Record Salary Advance</h6>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;"><span>&times;</span></button>
            </div>
            <form action="{{ route('payroll.advance.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="padding:20px;">
                    <div class="row">

                        <div class="col-12">
                            <div class="form-group">
                                <label style="font-size:12px;font-weight:700;color:#596579;">
                                    <i class="ti-location-arrow mr-1" style="color:#d97706;"></i>Link to Trip
                                    <span style="font-size:10px;font-weight:400;color:#8a94a6;margin-left:4px;">(optional — auto-fills driver)</span>
                                </label>
                                <select name="trip_id" id="advTripId"
                                    class="form-control adv-select2"
                                    style="width:100%;"
                                    data-placeholder="— Select Trip (optional) —">
                                    <option value=""></option>
                                    @foreach($trips as $t)
                                    <option value="{{ $t->id }}"
                                        data-driver-id="{{ $t->driver_id }}"
                                        data-driver-name="{{ $t->driver?->name ?? '' }}">
                                        {{ $t->trip_no }}
                                        @if($t->trip_date) ({{ $t->trip_date->format('d M Y') }})@endif
                                        — {{ $t->from_location }} → {{ $t->to_location }}
                                        @if($t->driver) · {{ $t->driver->name }}@endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:12px;font-weight:700;color:#596579;">Driver / Employee</label>
                                <select name="driver_id" id="advDriverId"
                                    class="form-control adv-select2"
                                    style="width:100%;"
                                    data-placeholder="— Select Driver —">
                                    <option value=""></option>
                                    @foreach($drivers as $d)
                                    <option value="{{ $d->id }}" data-name="{{ $d->name }}">
                                        {{ $d->name }}{{ $d->mobile ? ' — '.$d->mobile : '' }}
                                    </option>
                                    @endforeach
                                </select>
                                <small style="color:#8a94a6;font-size:11px;">Or type name below for non-driver staff</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:12px;font-weight:700;color:#596579;">Employee Name <span style="color:#e53e3e;">*</span></label>
                                <input type="text" name="employee_name" id="advEmpName"
                                    class="form-control" style="height:42px;border-radius:8px;"
                                    placeholder="Full name" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:12px;font-weight:700;color:#596579;">Amount <span style="color:#e53e3e;">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text" style="height:42px;border-radius:8px 0 0 8px;">₹</span></div>
                                    <input type="number" name="amount" class="form-control" style="height:42px;border-radius:0 8px 8px 0;" placeholder="0.00" min="1" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:12px;font-weight:700;color:#596579;">Advance Date <span style="color:#e53e3e;">*</span></label>
                                <input type="date" name="advance_date" class="form-control" style="height:42px;border-radius:8px;" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:12px;font-weight:700;color:#596579;">Payment Mode</label>
                                <select name="payment_mode" id="advPaymentMode"
                                    class="form-control adv-select2"
                                    style="width:100%;"
                                    data-placeholder="— Select Mode —">
                                    <option value="cash">💵 Cash</option>
                                    <option value="upi">📱 UPI</option>
                                    <option value="bank">🏦 Bank Transfer</option>
                                    <option value="cheque">📄 Cheque</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:12px;font-weight:700;color:#596579;">Reference No.</label>
                                <input type="text" name="reference_no" class="form-control" style="height:42px;border-radius:8px;" placeholder="UPI / Cheque No.">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group mb-0">
                                <label style="font-size:12px;font-weight:700;color:#596579;">Notes</label>
                                <textarea name="notes" class="form-control" rows="2" style="border-radius:8px;" placeholder="Reason for advance…"></textarea>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f2f7;padding:12px 20px;gap:8px;">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius:8px;">Cancel</button>
                    <button type="submit" class="btn btn-sm" style="background:linear-gradient(135deg,#d97706,#b45309);color:#fff;border:none;border-radius:8px;padding:7px 20px;font-weight:700;">
                        <i class="ti-save mr-1"></i> Save Advance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(url, msg) {
    if (confirm(msg || 'Are you sure?')) {
        var f = document.createElement('form');
        f.method = 'POST'; f.action = url;
        f.innerHTML = '@csrf <input name="_method" value="DELETE">';
        document.body.appendChild(f);
        f.submit();
    }
}

$(document).ready(function () {
    function initAdvModal() {
        $('#advTripId, #advDriverId, #advPaymentMode').each(function () {
            var $el = $(this);
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }
            $el.select2({
                width: '100%',
                allowClear: true,
                placeholder: $el.data('placeholder') || '',
                dropdownParent: $('#advanceModal')
            });
        });
    }

    $('#advanceModal').on('shown.bs.modal', function () {
        setTimeout(initAdvModal, 0);
    });

    $('#advanceModal').on('hide.bs.modal', function () {
        $('#advTripId, #advDriverId, #advPaymentMode').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
    });

    $(document).on('select2:select', '#advTripId', function () {
        var $opt       = $(this).find('option:selected');
        var driverId   = $opt.data('driver-id')   || '';
        var driverName = $opt.data('driver-name') || '';
        if (driverId) {
            $('#advDriverId').val(String(driverId)).trigger('change.select2');
        }
        if (driverName) {
            $('#advEmpName').val(driverName);
        }
    });

    $(document).on('select2:clear', '#advTripId', function () {
        $('#advDriverId').val('').trigger('change.select2');
        $('#advEmpName').val('');
    });

    $(document).on('select2:select', '#advDriverId', function () {
        var name = $(this).find('option:selected').data('name') || '';
        if (name) { $('#advEmpName').val(name); }
    });

    // ── Recovery form: Select2 + balance + validation ──
    function initRecSelect2() {
        $('#recAdvanceId, #recCategory').each(function () {
            var $el = $(this);
            if ($el.hasClass('select2-hidden-accessible')) { $el.select2('destroy'); }
            $el.select2({
                width: '100%',
                allowClear: true,
                placeholder: $el.is('#recAdvanceId') ? '— Select Advance —' : '— Select —',
            });
        });
    }
    $('#recAdvanceId').on('select2:select', function (e) {
        var pending = parseFloat($(e.params.data.element).data('pending')) || 0;
        $('#recBalance').val(pending.toFixed(2));
        validateRecAmount();
    });
    $('#recAdvanceId').on('select2:clear', function () {
        $('#recBalance').val('0.00');
        $('#recAmount').val('');
        $('#recFeedback').removeClass('ok err').html('');
        $('#recAmount').removeClass('val-err');
    });
    $('#recAmount').on('input', function () { validateRecAmount(); });

    function validateRecAmount() {
        var pending   = parseFloat($('#recBalance').val()) || 0;
        var amount    = parseFloat($('#recAmount').val()) || 0;
        var $fb       = $('#recFeedback');
        var $amt      = $('#recAmount');
        $amt.removeClass('val-err');
        $fb.removeClass('ok err');

        if (!amount || !pending) {
            $fb.html('');
            return;
        }
        if (amount > pending) {
            $amt.addClass('val-err');
            $fb.addClass('err').html('<i class="ti-alert"></i> Amount exceeds pending balance (₹ '+pending.toFixed(2)+')');
        } else {
            $fb.addClass('ok').html('<i class="ti-check"></i> Valid — will reduce balance to ₹ '+(pending - amount).toFixed(2));
        }
    }

    $('#recForm').on('submit', function (e) {
        var pending = parseFloat($('#recBalance').val()) || 0;
        var amount  = parseFloat($('#recAmount').val()) || 0;
        if (amount > pending) {
            e.preventDefault();
            $('#recFeedback').addClass('err').html('<i class="ti-alert"></i> Expense amount exceeds pending balance.');
        }
    });

    setTimeout(initRecSelect2, 0);
});
</script>
@endpush