@extends('layouts.app')
@section('title', 'Payroll Management')

@section('content')
<style>
.pr-page{background:#f4f6fb;}
.pr-header{background:linear-gradient(135deg,#2c7be5 0%,#1a5bbf 100%);border-radius:10px;padding:14px 20px;color:#fff;margin-bottom:16px;position:relative;overflow:hidden;}
.pr-header::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.07);border-radius:50%;}
.pr-header h4{font-size:16px;font-weight:800;margin:0 0 2px;}
.pr-header .sub{font-size:12px;opacity:.8;}

/* stat cards */
.pr-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px;}
.pr-stat{background:#fff;border-radius:12px;padding:16px;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;align-items:center;gap:12px;border-left:4px solid transparent;transition:transform .15s;}
.pr-stat:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.1);}
.pr-stat .si{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
.pr-stat .sl{font-size:11px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.4px;}
.pr-stat .sv{font-size:22px;font-weight:800;color:#1a2340;line-height:1.1;}

/* tabs */
.pr-tabs{display:flex;gap:4px;background:#f4f6fb;border-radius:10px;padding:4px;margin-bottom:16px;width:fit-content;}
.pr-tab{padding:7px 18px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;border:none;background:transparent;color:#8a94a6;transition:all .15s;}
.pr-tab.active{background:#fff;color:#1a2340;box-shadow:0 2px 8px rgba(0,0,0,.08);}

/* table card */
.pr-card{background:#fff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;margin-bottom:18px;}
.pr-card-hd{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #f0f2f7;background:#fafbff;}
.pr-card-hd h6{margin:0;font-size:13px;font-weight:700;color:#1a2340;}

/* filter bar */
.pr-filter{display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.pr-filter .form-control,.pr-filter select{height:36px;font-size:12px;border:1.5px solid #e2e8f0;border-radius:8px;padding:0 10px;}
.pr-filter .form-control:focus,.pr-filter select:focus{border-color:#2c7be5;box-shadow:0 0 0 2px rgba(44,123,229,.12);outline:none;}

/* action btns */
.btn-view-pr{background:#eef4fd;color:#2c7be5;border:1px solid #bfdbfe;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:4px;}
.btn-edit-pr{background:#f0fff4;color:#38a169;border:1px solid #9ae6b4;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:4px;}
.btn-del-pr{background:#fff5f5;color:#e53e3e;border:1px solid #fca5a5;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:4px;}

/* add-advance modal trigger btn */
.btn-adv{background:linear-gradient(135deg,#d97706,#b45309);color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
.btn-add-pr{background:linear-gradient(135deg,#2c7be5,#1a5bbf);color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}

@media(max-width:768px){.pr-stats{grid-template-columns:repeat(2,1fr);}}
</style>

<div class="pcoded-inner-content pr-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

{{-- HEADER --}}
<div class="pr-header">
    <div class="row align-items-center">
        <div class="col-md-7" style="position:relative;z-index:1;">
            <h4><i class="ti-money mr-2"></i>Payroll Management</h4>
            <div class="sub">Manage employee salaries, allowances, deductions &amp; advances</div>
        </div>
        <div class="col-md-5 text-right mt-2 mt-md-0" style="position:relative;z-index:1;display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap;">
            <button class="btn-adv" data-toggle="modal" data-target="#advanceModal">
                <i class="ti-plus"></i> Add Advance
            </button>
            <a href="{{ route('payroll.create') }}" class="btn-add-pr">
                <i class="ti-plus"></i> Add Payroll
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

{{-- STATS --}}
<div class="pr-stats">
    <div class="pr-stat" style="border-left-color:#2c7be5;">
        <div class="si" style="background:#eef4fd;color:#2c7be5;"><i class="ti-id-badge"></i></div>
        <div><div class="sl">Total Records</div><div class="sv">{{ $stats['total'] }}</div></div>
    </div>
    <div class="pr-stat" style="border-left-color:#38a169;">
        <div class="si" style="background:#f0fff4;color:#38a169;"><i class="ti-check-box"></i></div>
        <div><div class="sl">Paid</div><div class="sv">{{ $stats['paid'] }}</div></div>
    </div>
    <div class="pr-stat" style="border-left-color:#d97706;">
        <div class="si" style="background:#fffbeb;color:#d97706;"><i class="ti-time"></i></div>
        <div><div class="sl">Pending</div><div class="sv">{{ $stats['pending'] }}</div></div>
    </div>
    <div class="pr-stat" style="border-left-color:#7c3aed;">
        <div class="si" style="background:#f5f3ff;color:#7c3aed;"><i class="ti-wallet"></i></div>
        <div><div class="sl">Total Net Pay</div><div class="sv" style="font-size:16px;">₹ {{ number_format($stats['total_net'],0) }}</div></div>
    </div>
</div>

{{-- TABS --}}
<div class="pr-tabs">
    <button class="pr-tab active" onclick="showTab('payroll')">📋 Payroll Records</button>
    <button class="pr-tab" onclick="showTab('advances')">💰 Salary Advances</button>
</div>

{{-- ═══════════════════ TAB: PAYROLL ═══════════════════ --}}
<div id="tab-payroll">
    <div class="pr-card">
        <div class="pr-card-hd">
            <h6><i class="ti-money mr-2" style="color:#2c7be5;"></i>Payroll Records</h6>
            <div class="pr-filter">
                <form method="GET" action="{{ route('payroll') }}" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                    <input type="text" name="employee" class="form-control" placeholder="Employee name…"
                        value="{{ request('employee') }}" style="width:160px;">
                    <input type="month" name="month" class="form-control"
                        value="{{ request('month') }}" style="width:150px;">
                    <select name="status" class="form-control" style="width:120px;">
                        <option value="">All Status</option>
                        <option value="paid"    {{ request('status')=='paid'    ? 'selected':'' }}>Paid</option>
                        <option value="pending" {{ request('status')=='pending' ? 'selected':'' }}>Pending</option>
                    </select>
                    <button type="submit" style="background:#2c7be5;color:#fff;border:none;border-radius:8px;padding:5px 14px;font-size:12px;font-weight:700;cursor:pointer;">
                        <i class="ti-search"></i>
                    </button>
                    @if(request()->hasAny(['employee','month','status']))
                    <a href="{{ route('payroll') }}" style="color:#e53e3e;font-size:12px;font-weight:700;text-decoration:none;">✕ Clear</a>
                    @endif
                </form>
            </div>
        </div>
        <div style="padding:0;">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead style="background:#f8f9fb;">
                        <tr>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">#</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Employee</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Month</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Gross</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Advance</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Net Pay</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Status</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Payment Date</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $i => $p)
                        <tr>
                            <td style="padding:12px 16px;color:#8a94a6;">{{ $i+1 }}</td>
                            <td style="padding:12px 16px;">
                                <div style="font-weight:700;color:#1a2340;">{{ $p->employee_name }}</div>
                                @if($p->driver_id)
                                <div style="font-size:11px;color:#8a94a6;"><i class="ti-truck mr-1"></i>Driver</div>
                                @else
                                <div style="font-size:11px;color:#8a94a6;"><i class="ti-user mr-1"></i>Staff</div>
                                @endif
                            </td>
                            <td style="padding:12px 16px;font-weight:600;color:#1a2340;">{{ $p->payroll_month_label }}</td>
                            <td style="padding:12px 16px;color:#1a2340;">₹ {{ number_format($p->gross_salary,2) }}</td>
                            <td style="padding:12px 16px;">
                                @if($p->advance_deduction > 0)
                                <span style="color:#d97706;font-weight:700;">₹ {{ number_format($p->advance_deduction,2) }}</span>
                                <span style="display:block;font-size:10px;color:#8a94a6;">Advance Recovery</span>
                                @else
                                <span style="color:#8a94a6;">—</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;font-weight:800;color:#1a5bbf;font-size:14px;">₹ {{ number_format($p->net_salary,2) }}</td>
                            <td style="padding:12px 16px;">{!! $p->status_badge !!}</td>
                            <td style="padding:12px 16px;color:#596579;">{{ $p->payment_date ? $p->payment_date->format('d M Y') : '—' }}</td>
                            <td style="padding:12px 16px;text-align:right;">
                                <a href="{{ route('payroll.view', $p->id) }}" class="btn-view-pr mr-1"><i class="ti-eye"></i></a>
                                <a href="{{ route('payroll.edit', $p->id) }}" class="btn-edit-pr mr-1"><i class="ti-pencil"></i></a>
                                <button class="btn-del-pr" onclick="confirmDelete('{{ route('payroll.destroy', $p->id) }}','Delete this payroll record?')">
                                    <i class="ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align:center;padding:40px;color:#8a94a6;">
                                <i class="ti-money" style="font-size:32px;display:block;margin-bottom:8px;opacity:.3;"></i>
                                No payroll records found.
                                <a href="{{ route('payroll.create') }}" style="color:#2c7be5;font-weight:700;display:block;margin-top:6px;">+ Add First Record</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════ TAB: ADVANCES ═══════════════════ --}}
<div id="tab-advances" style="display:none;">
    <div class="pr-card">
        <div class="pr-card-hd">
            <h6><i class="ti-wallet mr-2" style="color:#d97706;"></i>Salary Advances</h6>
            <button class="btn-adv" data-toggle="modal" data-target="#advanceModal">
                <i class="ti-plus"></i> Add Advance
            </button>
        </div>
        <div style="padding:0;">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead style="background:#f8f9fb;">
                        <tr>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">#</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Employee</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Date</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Amount</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Recovered</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Pending</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Mode</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Status</th>
                            <th style="padding:12px 16px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($advances as $i => $adv)
                        <tr>
                            <td style="padding:12px 16px;color:#8a94a6;">{{ $i+1 }}</td>
                            <td style="padding:12px 16px;font-weight:700;color:#1a2340;">{{ $adv->employee_name }}</td>
                            <td style="padding:12px 16px;color:#596579;">{{ $adv->advance_date->format('d M Y') }}</td>
                            <td style="padding:12px 16px;font-weight:700;color:#d97706;">₹ {{ number_format($adv->amount,2) }}</td>
                            <td style="padding:12px 16px;color:#38a169;">₹ {{ number_format($adv->recovered_amount,2) }}</td>
                            <td style="padding:12px 16px;font-weight:800;color:#e53e3e;">₹ {{ number_format($adv->pending_amount,2) }}</td>
                            <td style="padding:12px 16px;color:#596579;text-transform:capitalize;">{{ $adv->payment_mode }}</td>
                            <td style="padding:12px 16px;">{!! $adv->status_badge !!}</td>
                            <td style="padding:12px 16px;text-align:right;">
                                <a href="{{ route('payroll.advance.edit', $adv->id) }}" class="btn-edit-pr mr-1" title="Edit advance"><i class="ti-pencil"></i></a>
                                @if($adv->status !== 'recovered')
                                <button class="btn-del-pr" onclick="confirmDelete('{{ route('payroll.advance.destroy', $adv->id) }}','Delete this advance record?')">
                                    <i class="ti-trash"></i>
                                </button>
                                @else
                                <span style="font-size:11px;color:#38a169;"><i class="ti-check"></i> Done</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align:center;padding:40px;color:#8a94a6;">
                                <i class="ti-wallet" style="font-size:32px;display:block;margin-bottom:8px;opacity:.3;"></i>
                                No advance records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div></div></div></div>

{{-- ═══════════════ ADVANCE MODAL ═══════════════ --}}
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

                        {{-- Trip --}}
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

                        {{-- Driver / Employee Name --}}
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

                        {{-- Amount & Date --}}
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

                        {{-- Payment Mode & Reference --}}
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

                        {{-- Notes --}}
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

@push('scripts')
<script>
function showTab(tab) {
    document.getElementById('tab-payroll').style.display  = tab === 'payroll'  ? '' : 'none';
    document.getElementById('tab-advances').style.display = tab === 'advances' ? '' : 'none';
    document.querySelectorAll('.pr-tab').forEach(function(el, i) {
        el.classList.toggle('active', (i === 0 && tab === 'payroll') || (i === 1 && tab === 'advances'));
    });
}

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

    @if(request()->has('tab') && request('tab') === 'advances')
    showTab('advances');
    @endif

    /* ── Init advance modal Select2 (runs after global footer handler) ── */
    function initAdvModal() {
        var $modal = $('#advanceModal');

        $('#advTripId, #advDriverId, #advPaymentMode').each(function () {
            var $el = $(this);
            // destroy any prior instance first
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }
            $el.select2({
                width: '100%',
                allowClear: true,
                placeholder: $el.data('placeholder') || '',
                dropdownParent: $modal
            });
        });
    }

    // Use setTimeout to run AFTER the global footer shown.bs.modal handler
    $('#advanceModal').on('shown.bs.modal', function () {
        setTimeout(initAdvModal, 0);
    });

    // Clean up on close so next open starts fresh
    $('#advanceModal').on('hide.bs.modal', function () {
        $('#advTripId, #advDriverId, #advPaymentMode').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
    });

    /* ── Trip → auto-fill driver & employee name ── */
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

    /* ── Driver → auto-fill employee name ── */
    $(document).on('select2:select', '#advDriverId', function () {
        var name = $(this).find('option:selected').data('name') || '';
        if (name) { $('#advEmpName').val(name); }
    });
});
</script>
@endpush
@endsection
