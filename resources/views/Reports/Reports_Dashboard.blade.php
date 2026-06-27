@extends('layouts.app')

@section('content')
<style>
    .rpt-page {
        background: #f4f6fb;
        padding: 6px 0;
    }

    .rpt-header {
        background: linear-gradient(135deg, #1a2340 0%, #2d3a5e 60%, #667eea 100%);
        border-radius: 16px;
        padding: 14px 28px;
        color: #fff;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
    }

    .rpt-header::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, .06);
        border-radius: 50%;
    }

    .rpt-header h4 {
        font-size: 20px;
        font-weight: 800;
        margin: 0 0 2px;
    }

    .rpt-header .sub {
        font-size: 13px;
        opacity: .75;
    }

    .rpt-section-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
        margin: 0 0 14px;
        padding-left: 4px;
    }

    .rpt-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 36px;
    }

    .rpt-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px 26px;
        box-shadow: 0 2px 14px rgba(0, 0, 0, .06);
        display: flex;
        align-items: flex-start;
        gap: 18px;
        text-decoration: none;
        color: inherit;
        transition: all .25s;
        border: 2px solid transparent;
        min-height: 100%;
    }

    .rpt-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 32px rgba(0, 0, 0, .1);
        text-decoration: none;
        color: inherit;
    }

    .rpt-card-body {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
        flex: 1;
    }

    .rpt-card .rc-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .rpt-card .rc-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 2px;
    }

    .rpt-card .rc-desc {
        font-size: 13px;
        color: #64748b;
        line-height: 1.55;
        margin: 0;
    }

    .rpt-card .rc-arrow {
        margin-left: auto;
        font-size: 18px;
        color: #cbd5e1;
        flex-shrink: 0;
        transition: all .25s;
        align-self: center;
    }

    .rpt-card:hover .rc-arrow {
        color: #667eea;
        transform: translateX(5px);
    }

    .rpt-card .rc-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 12px;
        border-radius: 20px;
        margin-top: 8px;
        display: inline-block;
        align-self: flex-start;
    }

    @media(max-width:991.98px) {
        .rpt-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media(max-width:575.98px) {
        .rpt-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }
        .rpt-header { padding: 24px 20px; }
        .rpt-card { padding: 18px 20px; }
    }
</style>

<div class="pcoded-inner-content rpt-page">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">

                <div class="rpt-header">
                    <div style="position:relative;z-index:1;">
                        <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:4px 14px;font-size:12px;font-weight:700;letter-spacing:.5px;margin-bottom:8px;">
                            <i class="ti-bar-chart"></i> Reports & Analytics
                        </div>
                        <h4>Reports Dashboard</h4>
                        <div class="sub">Generate detailed reports for trips, expenses, P&L, collections, and EMI.</div>
                    </div>
                </div>

                <div class="rpt-grid">
                    <a href="{{ route('reports.parties-payment-ledger') }}" class="rpt-card" style="border-color:#f3e8ff;">
                        <div class="rc-icon" style="background:#f3e8ff;color:#9333ea;"><i class="ti-wallet"></i></div>
                        <div class="rpt-card-body">
                            <div class="rc-title">Parties Payment Ledger</div>
                            <div class="rc-desc">View payment ledger with party selection. Filter by month, year, date, or custom date range.</div>
                            <span class="rc-badge" style="background:#f3e8ff;color:#9333ea;">Party-wise</span>
                        </div>
                        <i class="ti-arrow-right rc-arrow"></i>
                    </a>
                    <a href="{{ route('reports.invoices') }}" class="rpt-card" style="border-color:#eef2ff;">
                        <div class="rc-icon" style="background:#eef2ff;color:#4338ca;"><i class="ti-receipt"></i></div>
                        <div class="rpt-card-body">
                            <div class="rc-title">Invoice Report</div>
                            <div class="rc-desc">Invoice date, payment collected date, grand total (incl. tax), collected and balance per invoice.</div>
                            <span class="rc-badge" style="background:#eef2ff;color:#4338ca;">PDF &amp; Excel</span>
                        </div>
                        <i class="ti-arrow-right rc-arrow"></i>
                    </a>
                    <a href="{{ route('reports.emi') }}" class="rpt-card" style="border-color:#eef2ff;">
                        <div class="rc-icon" style="background:#eef2ff;color:#4338ca;"><i class="ti-calendar"></i></div>
                        <div class="rpt-card-body">
                            <div class="rc-title">EMI Details Report</div>
                            <div class="rc-desc">Vehicle loan EMI details, outstanding balances, payment progress, and overdue alerts.</div>
                            <span class="rc-badge" style="background:#eef2ff;color:#4338ca;">PDF &amp; Excel</span>
                        </div>
                        <i class="ti-arrow-right rc-arrow"></i>
                    </a>
                    <a href="{{ route('reports.trips') }}" class="rpt-card" style="border-color:#eef2ff;">
                        <div class="rc-icon" style="background:#eef2ff;color:#667eea;"><i class="ti-location-arrow"></i></div>
                        <div class="rpt-card-body">
                            <div class="rc-title">Trip Report</div>
                            <div class="rc-desc">All trips with filters by date, driver, vehicle, party, and status.</div>
                            <span class="rc-badge" style="background:#eef2ff;color:#667eea;">Printable</span>
                        </div>
                        <i class="ti-arrow-right rc-arrow"></i>
                    </a>
                    <a href="{{ route('reports.pnl') }}" class="rpt-card" style="border-color:#f0fff4;">
                        <div class="rc-icon" style="background:#f0fff4;color:#38a169;"><i class="ti-stats-up"></i></div>
                        <div class="rpt-card-body">
                            <div class="rc-title">Profit & Loss Report</div>
                            <div class="rc-desc">Net profit/loss per trip. Identify profitable routes and loss-making trips.</div>
                            <span class="rc-badge" style="background:#f0fff4;color:#38a169;">Completed Trips</span>
                        </div>
                        <i class="ti-arrow-right rc-arrow"></i>
                    </a>
                    <a href="{{ route('reports.expenses') }}" class="rpt-card" style="border-color:#fff5f5;">
                        <div class="rc-icon" style="background:#fff5f5;color:#e53e3e;"><i class="ti-receipt"></i></div>
                        <div class="rpt-card-body">
                            <div class="rc-title">Expense Report</div>
                            <div class="rc-desc">Analyze expenses by category, vehicle, and date range. Track spending patterns.</div>
                            <span class="rc-badge" style="background:#fff5f5;color:#e53e3e;">Category Breakdown</span>
                        </div>
                        <i class="ti-arrow-right rc-arrow"></i>
                    </a>
                    <a href="{{ route('reports.collection') }}" class="rpt-card" style="border-color:#fffbeb;">
                        <div class="rc-icon" style="background:#fffbeb;color:#d97706;"><i class="ti-credit-card"></i></div>
                        <div class="rpt-card-body">
                            <div class="rc-title">Pending Collection Report</div>
                            <div class="rc-desc">Track outstanding payments, overdue collections, and customer balances.</div>
                            <span class="rc-badge" style="background:#fffbeb;color:#d97706;">Overdue Alerts</span>
                        </div>
                        <i class="ti-arrow-right rc-arrow"></i>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection