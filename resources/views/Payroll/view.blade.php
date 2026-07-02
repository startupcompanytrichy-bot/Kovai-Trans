@extends('layouts.app')
@section('title', 'Payroll — '.$payroll->employee_name)
@section('content')
<style>
.pr-page{background:#f4f6fb;}
.pr-view-header{background:linear-gradient(135deg,#2c7be5 0%,#1a5bbf 100%);border-radius:10px;padding:14px 20px;color:#fff;margin-bottom:18px;position:relative;overflow:hidden;}
.pr-view-header::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.07);border-radius:50%;}
.pf-card{background:#fff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:18px;overflow:hidden;}
.pf-card-header{display:flex;align-items:center;gap:10px;padding:12px 18px;border-bottom:1px solid #f0f2f7;background:#fafbff;}
.pf-card-header h6{margin:0;font-size:13px;font-weight:700;color:#1a2340;}
.pf-card-body{padding:18px;}
.info-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f5f7fa;font-size:13px;}
.info-row:last-child{border-bottom:none;}
.info-row .lbl{color:#8a94a6;font-weight:600;}
.info-row .val{color:#1a2340;font-weight:700;}
.slip-table td{padding:10px 14px;font-size:13px;border-bottom:1px solid #f5f7fa;}
.slip-table .earning{color:#38a169;}
.slip-table .deduct{color:#e53e3e;}
.slip-total{background:#f4f6fb;font-weight:800;font-size:14px;}
.net-box{background:linear-gradient(135deg,#2c7be5,#1a5bbf);color:#fff;border-radius:10px;padding:20px 24px;text-align:center;margin-top:16px;}
.net-box .nb-label{font-size:13px;opacity:.8;margin-bottom:4px;}
.net-box .nb-val{font-size:32px;font-weight:800;}
</style>
<div class="pcoded-inner-content pr-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="pr-view-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <h4 style="font-size:16px;font-weight:800;margin:0 0 2px;">{{ $payroll->employee_name }}</h4>
            <div style="font-size:12px;opacity:.8;">Payroll — {{ $payroll->payroll_month_label }} &nbsp;·&nbsp; {!! $payroll->status_badge !!}</div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;display:flex;gap:8px;justify-content:flex-end;">
            <a href="{{ route('payroll.edit', $payroll->id) }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 14px;font-weight:600;">
                <i class="ti-pencil mr-1"></i> Edit
            </a>
            <a href="{{ route('payroll') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 14px;font-weight:600;">
                <i class="ti-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="row">
<div class="col-lg-8">

    {{-- Pay Slip --}}
    <div class="pf-card">
        <div class="pf-card-header">
            <div style="width:32px;height:32px;border-radius:7px;background:#eef4fd;color:#2c7be5;display:flex;align-items:center;justify-content:center;"><i class="ti-receipt"></i></div>
            <h6>Pay Slip — {{ $payroll->payroll_month_label }}</h6>
        </div>
        <div class="pf-card-body" style="padding:0;">
            <table class="table mb-0 slip-table">
                <thead style="background:#f8f9fb;">
                    <tr>
                        <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#596579;text-transform:uppercase;">Earnings</th>
                        <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#596579;text-transform:uppercase;text-align:right;">Amount</th>
                        <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#596579;text-transform:uppercase;">Deductions</th>
                        <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#596579;text-transform:uppercase;text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="earning">Basic Salary</td>
                        <td style="text-align:right;">₹ {{ number_format($payroll->basic_salary,2) }}</td>
                        <td class="deduct">PF</td>
                        <td style="text-align:right;">₹ {{ number_format($payroll->pf,2) }}</td>
                    </tr>
                    <tr>
                        <td class="earning">Other Allowance</td>
                        <td style="text-align:right;">₹ {{ number_format($payroll->other_allowance,2) }}</td>
                        <td class="deduct">ESI</td>
                        <td style="text-align:right;">₹ {{ number_format($payroll->esi,2) }}</td>
                    </tr>
                    <tr>
                        <td class="earning">Bonus</td>
                        <td style="text-align:right;">₹ {{ number_format($payroll->bonus,2) }}</td>
                        <td class="deduct">TDS</td>
                        <td style="text-align:right;">₹ {{ number_format($payroll->tds,2) }}</td>
                    </tr>
                    <tr>
                        <td class="earning">Other Allowance</td>
                        <td style="text-align:right;">₹ {{ number_format($payroll->other_allowance,2) }}</td>
                        <td class="deduct">Advance Recovery</td>
                        <td style="text-align:right;">₹ {{ number_format($payroll->advance_deduction,2) }}</td>
                    </tr>
                    <tr>
                        <td class="earning">Bonus</td>
                        <td style="text-align:right;">₹ {{ number_format($payroll->bonus,2) }}</td>
                        <td class="deduct">Other Deduction</td>
                        <td style="text-align:right;">₹ {{ number_format($payroll->other_deduction,2) }}</td>
                    </tr>
                    <tr class="slip-total">
                        <td style="color:#38a169;">Total Earnings</td>
                        <td style="text-align:right;color:#38a169;">₹ {{ number_format($payroll->gross_salary,2) }}</td>
                        <td style="color:#e53e3e;">Total Deductions</td>
                        <td style="text-align:right;color:#e53e3e;">₹ {{ number_format($payroll->total_deductions,2) }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="net-box">
                <div class="nb-label">Net Pay — {{ $payroll->payroll_month_label }}</div>
                <div class="nb-val">₹ {{ number_format($payroll->net_salary,2) }}</div>
            </div>
        </div>
    </div>

</div>
<div class="col-lg-4">

    {{-- Details --}}
    <div class="pf-card">
        <div class="pf-card-header">
            <div style="width:32px;height:32px;border-radius:7px;background:#eef4fd;color:#2c7be5;display:flex;align-items:center;justify-content:center;"><i class="ti-user"></i></div>
            <h6>Details</h6>
        </div>
        <div class="pf-card-body">
            <div class="info-row"><span class="lbl">Employee</span><span class="val">{{ $payroll->employee_name }}</span></div>
            <div class="info-row"><span class="lbl">Type</span><span class="val">{{ ucfirst($payroll->employee_type) }}</span></div>
            <div class="info-row"><span class="lbl">Month</span><span class="val">{{ $payroll->payroll_month_label }}</span></div>
            <div class="info-row"><span class="lbl">Status</span><span class="val">{!! $payroll->status_badge !!}</span></div>
            <div class="info-row"><span class="lbl">Payment Mode</span><span class="val">{{ ucfirst($payroll->payment_mode) }}</span></div>
            @if($payroll->reference_no)
            <div class="info-row"><span class="lbl">Reference</span><span class="val">{{ $payroll->reference_no }}</span></div>
            @endif
            <div class="info-row"><span class="lbl">Payment Date</span><span class="val">{{ $payroll->payment_date ? $payroll->payment_date->format('d M Y') : '—' }}</span></div>
            @if($payroll->notes)
            <div class="info-row" style="flex-direction:column;align-items:flex-start;gap:4px;">
                <span class="lbl">Notes</span>
                <span style="font-size:13px;color:#596579;">{{ $payroll->notes }}</span>
            </div>
            @endif
        </div>
    </div>

</div>
</div>

</div></div></div></div>
@endsection
