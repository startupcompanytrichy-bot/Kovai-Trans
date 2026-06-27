@extends('layouts.app')

@section('content')
<style>
.emi-page{background:#f4f6fb;}
.emi-header{background:linear-gradient(135deg,#1a2340 0%,#2d3a5e 60%,#4338ca 100%);border-radius:14px;padding:14px 24px;color:#fff;margin-bottom:18px;position:relative;overflow:hidden;}
.emi-header::before{content:'';position:absolute;top:-40px;right:-40px;width:160px;height:160px;background:rgba(255,255,255,.06);border-radius:50%;}
.emi-header h4{font-size:20px;font-weight:800;margin:0 0 4px;}
.emi-header .sub{font-size:13px;opacity:.8;}
.emi-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px;}
.emi-stat{background:#fff;border-radius:12px;padding:16px;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;align-items:center;gap:12px;border-left:4px solid transparent;transition:all .2s;}
.emi-stat:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.1);}
.emi-stat .es-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
.emi-stat .es-label{font-size:11px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.4px;}
.emi-stat .es-value{font-size:22px;font-weight:800;color:#1a2340;line-height:1.1;}
.emi-card{background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;margin-bottom:20px;}
.emi-card-header{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f0f2f7;background:#fafbff;flex-wrap:wrap;gap:8px;}
.emi-card-header h6{margin:0;font-size:14px;font-weight:700;color:#1a2340;}
.emi-table-wrap{overflow-x:auto;}
#emiTable{min-width:900px;margin-bottom:0;}
#emiTable th,#emiTable td{height:52px;padding:8px 14px;vertical-align:middle;border-color:#f0f2f7;font-size:13px;}
#emiTable th{background:#f8fafc;color:#14213d;font-weight:800;font-size:11px;text-transform:uppercase;letter-spacing:.4px;}
#emiTable .emi-row:hover td{background:#f4f7ff;}
.emi-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;}
.emi-progress-bar{height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;margin-top:4px;}
.emi-progress-fill{height:100%;border-radius:3px;background:linear-gradient(90deg,#667eea,#764ba2);}
.td-action-btns { display:inline-flex; gap:4px; align-items:center; }
.td-icon-btn { width:30px; height:30px; border-radius:7px; border:none; display:inline-flex; align-items:center; justify-content:center; font-size:13px; cursor:pointer; transition:all .15s; }
.td-icon-btn.edit  { background:#eef2ff; color:#667eea; }
.td-icon-btn.edit:hover  { background:#667eea; color:#fff; }
.td-icon-btn.del   { background:#fff5f5; color:#e53e3e; }
.td-icon-btn.del:hover   { background:#e53e3e; color:#fff; }
.emi-fab{position:fixed;bottom:28px;right:28px;z-index:999;width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#4338ca,#3730a3);color:#fff;font-size:24px;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px rgba(67,56,202,.5);text-decoration:none;transition:all .2s;}
.emi-fab:hover{transform:scale(1.1) rotate(90deg);color:#fff;}
.overdue-row td{background:#fff5f5!important;}
@media(max-width:767.98px){.emi-stats{grid-template-columns:repeat(2,1fr);}}
</style>

<div class="pcoded-inner-content emi-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="emi-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:4px 14px;font-size:12px;font-weight:700;letter-spacing:.5px;margin-bottom:8px;">
                <i class="ti-calendar"></i> Vehicle EMI
            </div>
            <h4>Vehicle EMI & Loan Tracker</h4>
            <div class="sub">Track vehicle loans, EMI schedules, and payment history.</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="{{ route('emi.create') }}" class="btn btn-sm" style="background:#fff;color:#4338ca;font-weight:700;border-radius:8px;padding:9px 20px;box-shadow:0 2px 10px rgba(0,0,0,.15);">
                <i class="ti-plus mr-1"></i> Add EMI Record
            </a>
        </div>
    </div>
</div>

@include('partials.flash')

<div class="emi-stats">
    <div class="emi-stat" style="border-left-color:#4338ca;">
        <div class="es-icon" style="background:#eef2ff;color:#4338ca;"><i class="ti-car"></i></div>
        <div><div class="es-label">Active Loans</div><div class="es-value" style="color:#4338ca;">{{ $stats['active'] }}</div></div>
    </div>
    <div class="emi-stat" style="border-left-color:#e53e3e;">
        <div class="es-icon" style="background:#fff5f5;color:#e53e3e;"><i class="ti-alert"></i></div>
        <div><div class="es-label">Overdue EMIs</div><div class="es-value" style="color:#e53e3e;">{{ $stats['overdue'] }}</div></div>
    </div>
    <div class="emi-stat" style="border-left-color:#d97706;">
        <div class="es-icon" style="background:#fffbeb;color:#d97706;"><i class="ti-timer"></i></div>
        <div><div class="es-label">Due This Week</div><div class="es-value" style="color:#d97706;">{{ $stats['upcoming'] }}</div></div>
    </div>
    <div class="emi-stat" style="border-left-color:#38a169;">
        <div class="es-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-money"></i></div>
        <div><div class="es-label">Monthly EMI</div><div class="es-value" style="color:#38a169;">₹{{ number_format($stats['total_emi'],0) }}</div></div>
    </div>
    <div class="emi-stat" style="border-left-color:#e53e3e;">
        <div class="es-icon" style="background:#fff5f5;color:#e53e3e;"><i class="ti-wallet"></i></div>
        <div><div class="es-label">Outstanding</div><div class="es-value" style="color:#e53e3e;">₹{{ number_format($stats['total_outstanding'],0) }}</div></div>
    </div>
    <div class="emi-stat" style="border-left-color:#667eea;">
        <div class="es-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-list"></i></div>
        <div><div class="es-label">Total Loans</div><div class="es-value" style="color:#667eea;">{{ $stats['total_loans'] }}</div></div>
    </div>
</div>

<div class="emi-card">
    <div class="emi-card-header">
        <h6><i class="ti-calendar mr-2" style="color:#4338ca;"></i>EMI Records <span id="emiCountBadge" style="background:#eef2ff;color:#4338ca;font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;margin-left:6px;">{{ $emis->count() }}</span></h6>
        <a href="{{ route('emi.create') }}" class="btn btn-sm" style="background:#4338ca;color:#fff;border-radius:8px;font-weight:600;padding:6px 16px;">
            <i class="ti-plus mr-1"></i> Add Record
        </a>
    </div>
    <div class="emi-table-wrap">
        <table class="table table-striped table-bordered" id="emiTable">
            <thead>
                <tr>
                    <th style="width:50px;text-align:center;">#</th>
                    <th style="width:160px;">Vehicle</th>
                    <th style="width:160px;">Financier</th>
                    <th style="width:130px;text-align:right;">Loan Amount</th>
                    <th style="width:120px;text-align:right;">EMI / Month</th>
                    <th style="width:130px;">Next Due Date</th>
                    <th style="width:160px;">Progress</th>
                    <th style="width:130px;text-align:right;">Outstanding</th>
                    <th style="width:110px;text-align:center;">Status</th>
                    <th style="width:100px;text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($emis as $i => $emi)
                @php
                    $isOverdue = $emi->is_overdue;
                    $pct = $emi->total_emis > 0 ? round(($emi->paid_emis / $emi->total_emis) * 100) : 0;
                    $statusCfg = [
                        'active'  => ['label'=>'Active',  'color'=>'#38a169','bg'=>'#f0fff4'],
                        'closed'  => ['label'=>'Closed',  'color'=>'#8a94a6','bg'=>'#f4f6fb'],
                        'overdue' => ['label'=>'Overdue', 'color'=>'#e53e3e','bg'=>'#fff5f5'],
                    ];
                    $sc = $isOverdue ? $statusCfg['overdue'] : ($statusCfg[$emi->status] ?? $statusCfg['active']);
                @endphp
                <tr class="emi-row {{ $isOverdue ? 'overdue-row' : '' }}">
                    <td style="text-align:center;">{{ $i+1 }}</td>
                    <td>
                        <div style="font-weight:700;color:#1a2340;">{{ optional($emi->vehicle)->vehicle_number }}</div>
                        <div style="font-size:11px;color:#8a94a6;">{{ optional($emi->vehicle)->vehicle_name }}</div>
                    </td>
                    <td>{{ $emi->financier_name }}</td>
                    <td style="text-align:right;font-weight:700;">₹{{ number_format($emi->loan_amount,0) }}</td>
                    <td style="text-align:right;font-weight:700;color:#4338ca;">₹{{ number_format($emi->emi_amount,0) }}</td>
                    <td>
                        @if($emi->next_due_date)
                            <span style="font-weight:700;color:{{ $isOverdue ? '#e53e3e' : '#1a2340' }};">
                                {{ $emi->next_due_date->format('d M Y') }}
                            </span>
                            @if($isOverdue)<div style="font-size:10px;color:#e53e3e;font-weight:700;">OVERDUE</div>@endif
                        @else
                            <span style="color:#b0bac9;">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:11px;color:#8a94a6;margin-bottom:3px;">{{ $emi->paid_emis }}/{{ $emi->total_emis ?? '?' }} EMIs paid</div>
                        <div class="emi-progress-bar">
                            <div class="emi-progress-fill" style="width:{{ $pct }}%;background:{{ $isOverdue ? 'linear-gradient(90deg,#fc8181,#e53e3e)' : 'linear-gradient(90deg,#667eea,#764ba2)' }};"></div>
                        </div>
                    </td>
                    <td style="text-align:right;font-weight:800;color:{{ $emi->outstanding_balance > 0 ? '#e53e3e' : '#38a169' }};">
                        ₹{{ number_format($emi->outstanding_balance,0) }}
                    </td>
                    <td style="text-align:center;">
                        <span class="emi-badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">{{ $sc['label'] }}</span>
                    </td>
                    <td style="text-align:center;" onclick="event.stopPropagation();">
                        <div class="td-action-btns">
                            <a href="{{ route('emi.edit', $emi->id) }}" class="td-icon-btn edit" title="Edit">
                                <i class="ti-pencil"></i>
                            </a>
                            <button type="button" class="td-icon-btn del" title="Delete"
                                onclick="showDeleteModal('delEmiForm{{ $emi->id }}','{{ addslashes(optional($emi->vehicle)->vehicle_number) }}','EMI Record')">
                                <i class="ti-trash"></i>
                            </button>
                        </div>
                        <form id="delEmiForm{{ $emi->id }}" action="{{ route('emi.destroy', $emi->id) }}" method="POST" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center py-4" style="color:#b0bac9;">
                    <i class="ti-calendar" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    No EMI records found. <a href="{{ route('emi.create') }}">Add your first EMI record</a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- FAB removed: use header button instead --}}

</div></div></div></div>
@endsection
