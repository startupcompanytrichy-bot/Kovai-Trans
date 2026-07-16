@extends('layouts.app')
@section('title', 'Daily Check In')

@section('content')
<style>
.dci-page{background:#f4f6fb;}
.dci-header{background:linear-gradient(135deg,#0ea5e9 0%,#0284c7 100%);border-radius:10px;padding:14px 20px;color:#fff;margin-bottom:16px;position:relative;overflow:hidden;}
.dci-header::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.07);border-radius:50%;}
.dci-header h4{font-size:16px;font-weight:800;margin:0 0 2px;}
.dci-header .sub{font-size:12px;opacity:.8;}

.dci-tabs{display:flex;gap:4px;background:#f4f6fb;border-radius:10px;padding:4px;margin-bottom:16px;width:fit-content;}
.dci-tab{padding:7px 18px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;border:none;background:transparent;color:#8a94a6;transition:all .15s;}
.dci-tab.active{background:#fff;color:#1a2340;box-shadow:0 2px 8px rgba(0,0,0,.08);}

.dci-card{background:#fff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;margin-bottom:18px;}
.dci-card-hd{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #f0f2f7;background:#fafbff;}
.dci-card-hd h6{margin:0;font-size:13px;font-weight:700;color:#1a2340;}

.dci-badge{display:inline-flex;padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;}
.dci-badge.active{background:#e8f5e9;color:#2e7d32;}
.dci-badge.overdue{background:#fbe9e7;color:#c62828;}
.dci-badge.closed{background:#eceff1;color:#546e7a;}

.vehicle-tbl th{background:#f8f9fb;padding:10px 10px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;white-space:nowrap;vertical-align:middle;}
.vehicle-tbl td{padding:8px 10px;vertical-align:middle;font-size:12px;}
.vehicle-tbl .form-control{height:32px;font-size:12px;padding:4px 8px;border:1.5px solid #e2e8f0;border-radius:6px;}
.vehicle-tbl .form-control:focus{border-color:#0ea5e9;box-shadow:0 0 0 2px rgba(14,165,233,.12);}
.btn-make-change{padding:4px 12px;font-size:11px;font-weight:700;border-radius:6px;border:none;background:#d1d5db;color:#9ca3af;cursor:not-allowed;transition:all .15s;white-space:nowrap;}
.btn-make-change.active{background:#0ea5e9;color:#fff;cursor:pointer;}
.btn-make-change.active:hover{background:#0284c7;}
.vehicle-tbl .expired{color:#ef4444;font-weight:700;}
.vehicle-tbl .expiring-soon{color:#f59e0b;font-weight:600;}
.btn-send-reminder{padding:3px 8px;font-size:10px;font-weight:700;border-radius:4px;border:none;cursor:pointer;transition:all .15s;white-space:nowrap;}
.btn-send-reminder.send{background:#22c55e;color:#fff;}
.btn-send-reminder.send:hover{background:#16a34a;}
.btn-send-reminder.sent{background:#e8f5e9;color:#2e7d32;cursor:default;}
.btn-send-reminder.sending{background:#f59e0b;color:#fff;cursor:wait;}
</style>

<div class="pcoded-inner-content dci-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="dci-header">
    <div class="row align-items-center">
        <div class="col-md-12" style="position:relative;z-index:1;">
            <h4><i class="ti-clipboard mr-2"></i>Daily Check In</h4>
            <div class="sub">{{ date('l, d M Y') }} &bull; {{ $vehicles->count() }} active vehicles</div>
        </div>
    </div>
</div>

{{-- TABS --}}
<div class="dci-tabs">
    <button class="dci-tab active" onclick="showDciTab('vehicle')">Vehicle General</button>
    <button class="dci-tab" onclick="showDciTab('emi')">Vehicle EMI</button>
</div>

{{-- ═══════════════════ TAB: VEHICLE GENERAL ═══════════════════ --}}
<div id="tab-vehicle">
    <div class="dci-card">
        <div class="dci-card-hd">
            <h6><i class="ti-truck mr-2" style="color:#0ea5e9;"></i>Vehicle Document Tracker</h6>
            <span style="font-size:12px;color:#8a94a6;font-weight:600;">{{ $vehicles->count() }} vehicles</span>
        </div>
        <div style="padding:0;">
            <div class="table-responsive">
                <table class="table mb-0 vehicle-tbl" id="vehicleTbl">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th style="min-width:120px;">Vehicle No</th>
                            <th style="min-width:160px;">Insurance Expiry</th>
                            <th style="min-width:160px;">Fitness Expiry</th>
                            <th style="min-width:160px;">PUC Expiry</th>
                            <th style="min-width:160px;">National Permit</th>
                            <th style="min-width:160px;">Permit Expiry</th>
                            <th style="width:110px;text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $i => $v)
                        @php
                            $today = now()->startOfDay();
                            $insExp     = $v->insurance_expiry_date ? \Carbon\Carbon::parse($v->insurance_expiry_date) : null;
                            $fitExp     = $v->fitness_expiry_date ? \Carbon\Carbon::parse($v->fitness_expiry_date) : null;
                            $pucExp     = $v->puc_expiry_date ? \Carbon\Carbon::parse($v->puc_expiry_date) : null;
                            $natPermit  = $v->national_permit_date ? \Carbon\Carbon::parse($v->national_permit_date) : null;
                            $permitExp  = $v->permit_expiry_date ? \Carbon\Carbon::parse($v->permit_expiry_date) : null;
                            $insDays     = $insExp    ? (int) $today->diffInDays($insExp, false) : null;
                            $fitDays     = $fitExp    ? (int) $today->diffInDays($fitExp, false) : null;
                            $pucDays     = $pucExp    ? (int) $today->diffInDays($pucExp, false) : null;
                            $natDays     = $natPermit ? (int) $today->diffInDays($natPermit, false) : null;
                            $permitDays  = $permitExp ? (int) $today->diffInDays($permitExp, false) : null;
                        @endphp
                        @php
                            $dayLabel = function($days) {
                                if ($days === null) return '<span style="font-size:10px;color:#b0bac9;">—</span>';
                                if ($days < 0) return '<span style="font-size:10px;color:#ef4444;font-weight:700;">' . abs($days) . ' day' . (abs($days) > 1 ? 's' : '') . ' overdue</span>';
                                if ($days === 0) return '<span style="font-size:10px;color:#ef4444;font-weight:700;">Due today</span>';
                                if ($days <= 7) return '<span style="font-size:10px;color:#ef4444;font-weight:600;">' . $days . ' day' . ($days > 1 ? 's' : '') . ' left</span>';
                                if ($days <= 30) return '<span style="font-size:10px;color:#f59e0b;font-weight:600;">' . $days . ' days left</span>';
                                return '<span style="font-size:10px;color:#22c55e;font-weight:600;">' . $days . ' days left</span>';
                            };
                            $sendBtn = function($vehicleId, $field, $reminderData, $v) {
                                $data = $reminderData[$v->id][$field] ?? null;
                                if (!$data) return '';
                                $days = $data['days_remaining'];
                                $sent = $data['already_sent'];
                                if ($days > 30) return '';
                                if ($sent) {
                                    return '<button class="btn-send-reminder sent" disabled>Sent</button>';
                                }
                                return '<button class="btn-send-reminder send" onclick="sendReminder(' . $vehicleId . ', \'' . $field . '\', this)">Send</button>';
                            };
                        @endphp
                        <tr id="vehicle-row-{{ $v->id }}">
                            <td style="color:#b0bac9;font-weight:600;">{{ $i + 1 }}</td>
                            <td style="font-weight:700;color:#1a2340;">{{ $v->vehicle_number }}</td>
                            <td>
                                <input type="date" class="form-control vehicle-date" data-vehicle="{{ $v->id }}" data-field="insurance_expiry_date"
                                    value="{{ $v->insurance_expiry_date ? date('Y-m-d', strtotime($v->insurance_expiry_date)) : '' }}"
                                    style="{{ $insExp && $insExp->lt($today) ? 'border-color:#ef4444;' : ($insExp && $insExp->lte($today->copy()->addDays(30)) ? 'border-color:#f59e0b;' : '') }}">
                                    <div class="d-flex align-items-center gap-2">{!! $dayLabel($insDays) !!} {!! $sendBtn($v->id, 'insurance_expiry_date', $reminderData, $v) !!}</div>
                            </td>
                            <td>
                                <input type="date" class="form-control vehicle-date" data-vehicle="{{ $v->id }}" data-field="fitness_expiry_date"
                                    value="{{ $v->fitness_expiry_date ? date('Y-m-d', strtotime($v->fitness_expiry_date)) : '' }}"
                                    style="{{ $fitExp && $fitExp->lt($today) ? 'border-color:#ef4444;' : ($fitExp && $fitExp->lte($today->copy()->addDays(30)) ? 'border-color:#f59e0b;' : '') }}">
                                    <div class="d-flex align-items-center gap-2">{!! $dayLabel($fitDays) !!} {!! $sendBtn($v->id, 'fitness_expiry_date', $reminderData, $v) !!}</div>
                            </td>
                            <td>
                                <input type="date" class="form-control vehicle-date" data-vehicle="{{ $v->id }}" data-field="puc_expiry_date"
                                    value="{{ $v->puc_expiry_date ? date('Y-m-d', strtotime($v->puc_expiry_date)) : '' }}"
                                    style="{{ $pucExp && $pucExp->lt($today) ? 'border-color:#ef4444;' : ($pucExp && $pucExp->lte($today->copy()->addDays(30)) ? 'border-color:#f59e0b;' : '') }}">
                                    <div class="d-flex align-items-center gap-2">{!! $dayLabel($pucDays) !!} {!! $sendBtn($v->id, 'puc_expiry_date', $reminderData, $v) !!}</div>
                            </td>
                            <td>
                                <input type="date" class="form-control vehicle-date" data-vehicle="{{ $v->id }}" data-field="national_permit_date"
                                    value="{{ $v->national_permit_date ? date('Y-m-d', strtotime($v->national_permit_date)) : '' }}"
                                    style="{{ $natPermit && $natPermit->lt($today) ? 'border-color:#ef4444;' : ($natPermit && $natPermit->lte($today->copy()->addDays(30)) ? 'border-color:#f59e0b;' : '') }}">
                                    <div class="d-flex align-items-center gap-2">{!! $dayLabel($natDays) !!} {!! $sendBtn($v->id, 'national_permit_date', $reminderData, $v) !!}</div>
                            </td>
                            <td>
                                <input type="date" class="form-control vehicle-date" data-vehicle="{{ $v->id }}" data-field="permit_expiry_date"
                                    value="{{ $v->permit_expiry_date ? date('Y-m-d', strtotime($v->permit_expiry_date)) : '' }}"
                                    style="{{ $permitExp && $permitExp->lt($today) ? 'border-color:#ef4444;' : ($permitExp && $permitExp->lte($today->copy()->addDays(30)) ? 'border-color:#f59e0b;' : '') }}">
                                    <div class="d-flex align-items-center gap-2">{!! $dayLabel($permitDays) !!} {!! $sendBtn($v->id, 'permit_expiry_date', $reminderData, $v) !!}</div>
                            </td>
                            <td style="text-align:center;">
                                <button class="btn-make-change" id="change-btn-{{ $v->id }}" data-vehicle="{{ $v->id }}" disabled>Make Change</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4" style="color:#b0bac9;">No active vehicles found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════ TAB: VEHICLE EMI ═══════════════════ --}}
<div id="tab-emi" style="display:none;">
    <div class="dci-card">
        <div class="dci-card-hd">
            <h6><i class="ti-calendar mr-2" style="color:#f59e0b;"></i>Vehicle EMI Records</h6>
            <span style="font-size:12px;color:#8a94a6;font-weight:600;">{{ $emis->count() }} records</span>
        </div>
        <div style="padding:0;">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead style="background:#f8f9fb;">
                        <tr>
                            <th style="padding:10px 14px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">#</th>
                            <th style="padding:10px 14px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Vehicle</th>
                            <th style="padding:10px 14px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Financier</th>
                            <th style="padding:10px 14px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">EMI Amount</th>
                            <th style="padding:10px 14px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Paid / Total</th>
                            <th style="padding:10px 14px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Next Due</th>
                            <th style="padding:10px 14px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Remaining</th>
                            <th style="padding:10px 14px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Balance</th>
                            <th style="padding:10px 14px;font-weight:700;color:#596579;font-size:11px;text-transform:uppercase;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emis as $i => $e)
                        @php
                            $overdue = $e->is_overdue;
                            $emiDays = $e->next_due_date ? (int) today()->diffInDays($e->next_due_date, false) : null;
                        @endphp
                        <tr>
                            <td style="padding:10px 14px;color:#b0bac9;font-weight:600;">{{ $i + 1 }}</td>
                            <td style="padding:10px 14px;font-weight:700;color:#1a2340;">{{ $e->vehicle->vehicle_name ?? '—' }} <span style="font-weight:400;color:#8a94a6;">{{ $e->vehicle->vehicle_number ?? '' }}</span></td>
                            <td style="padding:10px 14px;color:#596579;">{{ $e->financier_name }}</td>
                            <td style="padding:10px 14px;font-weight:700;">₹{{ number_format($e->emi_amount, 0) }}</td>
                            <td style="padding:10px 14px;color:#596579;">{{ $e->paid_emis ?? 0 }} / {{ $e->total_emis ?? 0 }}</td>
                            <td style="padding:10px 14px;color:{{ $overdue ? '#ef4444' : '#596579' }};font-weight:{{ $overdue ? '700' : '400' }};">
                                {{ $e->next_due_date?->format('d M Y') ?: '—' }}
                            </td>
                            <td style="padding:10px 14px;">
                                @if($emiDays === null)
                                <span style="font-size:12px;color:#b0bac9;">—</span>
                                @elseif($emiDays < 0)
                                <span style="font-size:12px;color:#ef4444;font-weight:700;">{{ abs($emiDays) }} day{{ abs($emiDays) > 1 ? 's' : '' }} overdue</span>
                                @elseif($emiDays === 0)
                                <span style="font-size:12px;color:#ef4444;font-weight:700;">Due today</span>
                                @elseif($emiDays <= 7)
                                <span style="font-size:12px;color:#ef4444;font-weight:600;">{{ $emiDays }} day{{ $emiDays > 1 ? 's' : '' }} left</span>
                                @else
                                <span style="font-size:12px;color:#22c55e;font-weight:600;">{{ $emiDays }} days left</span>
                                @endif
                            </td>
                            <td style="padding:10px 14px;font-weight:700;">₹{{ number_format($e->outstanding_balance, 0) }}</td>
                            <td style="padding:10px 14px;">
                                <span class="dci-badge {{ $e->status }}">
                                    {{ $e->status === 'active' ? ($overdue ? 'Overdue' : 'Active') : ($e->status === 'closed' ? 'Closed' : ucfirst($e->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4" style="color:#b0bac9;">No EMI records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div></div></div></div>
@endsection

@push('scripts')
<script>
function showDciTab(tab) {
    var tabs = ['tab-vehicle', 'tab-emi'];
    tabs.forEach(function(id) {
        document.getElementById(id).style.display = id === 'tab-' + tab ? '' : 'none';
    });
    document.querySelectorAll('.dci-tab').forEach(function(el) {
        var t = el.textContent.trim().toLowerCase().includes('vehicle general') ? 'vehicle' : 'emi';
        el.classList.toggle('active', t === tab);
    });
}

function sendReminder(vehicleId, documentType, btn) {
    if (!confirm('Send WhatsApp reminder for this document?')) return;

    btn.classList.remove('send');
    btn.classList.add('sending');
    btn.textContent = 'Sending...';
    btn.disabled = true;

    fetch('{{ route("daily-check-in.send-reminder") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            vehicle_id: vehicleId,
            document_type: documentType
        })
    })
    .then(function(res) { return res.json(); })
    .then(function(resp) {
        if (resp.success) {
            btn.classList.remove('sending');
            btn.classList.add('sent');
            btn.textContent = 'Sent';
            if (typeof toastr !== 'undefined') {
                toastr['success'](resp.message, 'Success');
            }
        } else {
            btn.classList.remove('sending');
            btn.classList.add('send');
            btn.textContent = 'Send';
            btn.disabled = false;
            if (typeof toastr !== 'undefined') {
                toastr['error'](resp.message, 'Error');
            }
        }
    })
    .catch(function(err) {
        btn.classList.remove('sending');
        btn.classList.add('send');
        btn.textContent = 'Send';
        btn.disabled = false;
        if (typeof toastr !== 'undefined') {
            toastr['error']('Error: ' + err.message, 'Error');
        }
    });
}

(function () {
    var changedData = {};

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('vehicle-date')) {
            var vehicleId = e.target.getAttribute('data-vehicle');
            changedData[vehicleId] = changedData[vehicleId] || {};
            changedData[vehicleId][e.target.getAttribute('data-field')] = e.target.value || null;
            var btn = document.getElementById('change-btn-' + vehicleId);
            if (btn) { btn.disabled = false; btn.classList.add('active'); }
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-make-change') && !e.target.disabled) {
            var vehicleId = e.target.getAttribute('data-vehicle');
            var data = changedData[vehicleId];
            if (!data) return;

            e.target.textContent = 'Saving...';
            e.target.disabled = true;
            e.target.classList.remove('active');

            var url = '{{ route('daily-check-in.update-vehicle', ['id' => '___ID___']) }}'.replace('___ID___', vehicleId);
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(function(res) { return res.json(); })
            .then(function(resp) {
                if (resp.success) {
                    delete changedData[vehicleId];
                    fetch(window.location.href)
                        .then(function(r) { return r.text(); })
                        .then(function(html) {
                            var parser = new DOMParser();
                            var doc = parser.parseFromString(html, 'text/html');
                            var newTab = doc.getElementById('tab-vehicle');
                            var oldTab = document.getElementById('tab-vehicle');
                            if (newTab && oldTab) {
                                oldTab.innerHTML = newTab.innerHTML;
                            }
                        });
                    if (typeof toastr !== 'undefined') {
                        toastr['success']('Vehicle dates updated successfully.', 'Success');
                    }
                } else {
                    alert('Update failed. Please try again.');
                }
            })
            .catch(function(err) {
                alert('Error: ' + err.message);
            });
        }
    });
})();
</script>
@endpush
