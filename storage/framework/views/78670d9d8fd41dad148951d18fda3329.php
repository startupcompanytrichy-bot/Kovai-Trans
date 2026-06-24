<?php $__env->startPush('styles'); ?>
<style>
/* ══════════════════════════════════════════════════════════════
   DASHBOARD — Layout & Reset
══════════════════════════════════════════════════════════════ */

/* Ensure page-body doesn't overflow on any screen */
.pcoded-inner-content .page-body {
    overflow-x: hidden;
}

/* Remove Bootstrap row negative margins that cause horizontal scroll */
.dash-row {
    display: flex;
    flex-wrap: wrap;
    margin-left: -10px;
    margin-right: -10px;
}
.dash-col {
    padding-left: 10px;
    padding-right: 10px;
    box-sizing: border-box;
}

/* ── Section label ─────────────────────────────────────────── */
.s-label {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #8a94a6;
    margin: 4px 0 12px;
    padding: 0;
    display: block;
}

/* ── Card base ─────────────────────────────────────────────── */
.dc {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 1px 6px rgba(15,23,42,.07);
    margin-bottom: 20px;
    overflow: hidden; /* prevents child overflow */
}
.dc-pad  { padding: 18px 20px; }
.dc-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 4px;
}
.dc-title { font-size: 14px; font-weight: 800; color: #1a2340; line-height: 1.3; }
.dc-sub   { font-size: 12px; color: #8a94a6; margin: 2px 0 12px; }
.dc-link  {
    flex-shrink: 0;
    font-size: 11.5px; font-weight: 700; color: #667eea;
    background: #eef2ff; border: none; border-radius: 8px;
    padding: 4px 12px; text-decoration: none; white-space: nowrap;
    display: inline-block; line-height: 1.6;
}
.dc-link:hover { background: #e0e7ff; color: #4f46e5; text-decoration: none; }

/* ── KPI card ──────────────────────────────────────────────── */
.kc {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 1px 6px rgba(15,23,42,.07);
    padding: 18px 20px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 20px;
    transition: box-shadow .2s, transform .2s;
    overflow: hidden;
    min-width: 0; /* flex child shrink fix */
}
.kc:hover { box-shadow: 0 6px 20px rgba(15,23,42,.11); transform: translateY(-2px); }
.kc-ico {
    width: 46px; height: 46px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 19px; flex-shrink: 0;
}
.kc-body { flex: 1; min-width: 0; overflow: hidden; }
.kc-lbl  { font-size: 10.5px; font-weight: 700; color: #8a94a6; text-transform: uppercase; letter-spacing: .06em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.kc-val  { font-size: 26px; font-weight: 900; color: #1a2340; line-height: 1.15; margin: 3px 0 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.kc-ft   { font-size: 11.5px; color: #8a94a6; display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }

/* growth badges */
.gbadge {
    display: inline-flex; align-items: center; gap: 2px;
    font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 20px;
}
.gbadge-up   { background: #ecfdf5; color: #059669; }
.gbadge-down { background: #fff5f5; color: #e53e3e; }

/* small KPI variant */
.kc-sm { padding: 14px 16px; }
.kc-sm .kc-ico { width: 38px; height: 38px; border-radius: 10px; font-size: 16px; }
.kc-sm .kc-val { font-size: 22px; }

/* ── Today's Status Strip ──────────────────────────────────── */
.today-strip {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 1px 6px rgba(15,23,42,.07);
    padding: 14px 18px;
    display: flex;
    align-items: stretch;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 20px;
}
.ts-item {
    flex: 1 1 80px;
    min-width: 72px;
    text-align: center;
    padding: 10px 8px;
    border-radius: 10px;
    text-decoration: none;
    transition: filter .15s;
}
.ts-item:hover { filter: brightness(.96); text-decoration: none; }
.ts-val { font-size: 24px; font-weight: 900; line-height: 1; display: block; }
.ts-lbl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #8a94a6; margin-top: 4px; display: block; white-space: nowrap; }
.ts-sep { width: 1px; background: #f1f3f8; align-self: stretch; flex-shrink: 0; margin: 6px 2px; }

/* ── Progress bar (top parties/vehicles) ───────────────────── */
.prog-wrap { height: 5px; border-radius: 10px; background: #f1f3f8; overflow: hidden; margin-top: 5px; }
.prog-fill  { height: 100%; border-radius: 10px; }

/* ── Rank badge ────────────────────────────────────────────── */
.rank-badge {
    width: 22px; height: 22px; border-radius: 50%;
    background: #f1f3f8;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800; color: #8a94a6; flex-shrink: 0;
}

/* ── List row (EMI / top items) ────────────────────────────── */
.list-row {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 0; border-bottom: 1px solid #f1f3f8;
}
.list-row:last-child { border-bottom: none; }
.list-main { flex: 1; min-width: 0; }
.list-title { font-size: 13px; font-weight: 700; color: #1a2340; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.list-sub   { font-size: 11.5px; color: #8a94a6; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.list-right { flex-shrink: 0; text-align: right; }
.list-amt   { font-size: 13px; font-weight: 800; }
.emi-tag    { display: inline-block; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 20px; }

/* ── Alert banner ──────────────────────────────────────────── */
.alert-strip {
    background: #fff5f5; border: 1.5px solid #fed7d7; border-radius: 10px;
    padding: 9px 14px; display: flex; align-items: center; gap: 8px;
    font-size: 12.5px; font-weight: 700; color: #c53030; margin-bottom: 12px;
}

/* ── Status pill ───────────────────────────────────────────── */
.spill {
    display: inline-flex; align-items: center;
    padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 700;
    white-space: nowrap;
}

/* ── Running trip card ─────────────────────────────────────── */
.run-card {
    background: linear-gradient(135deg,#fffbeb,#fff8e0);
    border: 1.5px solid #fde68a; border-radius: 12px;
    padding: 11px 14px; margin-bottom: 10px;
    display: flex; align-items: center; gap: 12px; min-width: 0;
}
.run-card:last-child { margin-bottom: 0; }
.run-card .rc-body { flex: 1; min-width: 0; }
.run-card .rc-route { font-size: 13px; font-weight: 800; color: #1a2340; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.run-card .rc-meta  { font-size: 11.5px; color: #8a94a6; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.run-card .rc-right { flex-shrink: 0; text-align: right; }

/* ── Table fixes ───────────────────────────────────────────── */
.dash-tbl { width: 100%; border-collapse: collapse; }
.dash-tbl th {
    font-size: 10.5px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .05em; color: #8a94a6; background: #f8f9fc;
    padding: 10px 12px; border: none; white-space: nowrap;
}
.dash-tbl td {
    font-size: 12.5px; padding: 10px 12px;
    vertical-align: middle; border-bottom: 1px solid #f1f3f8; border-top: none;
}
.dash-tbl tr:last-child td { border-bottom: none; }
.tbl-trip   { font-weight: 800; color: #667eea; white-space: nowrap; }
.tbl-date   { color: #8a94a6; white-space: nowrap; }
.tbl-route  { font-size: 12px; color: #4a5568; max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.tbl-party  { font-size: 12px; color: #4a5568; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.tbl-amt    { font-weight: 800; color: #059669; white-space: nowrap; }

/* ── Header buttons ────────────────────────────────────────── */
.hdr-btn {
    font-size: 13px; font-weight: 700; border-radius: 10px;
    padding: 7px 16px; border: none; cursor: pointer;
    text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    white-space: nowrap; line-height: 1.4;
}
.hdr-btn:hover { text-decoration: none; opacity: .9; }
.hdr-btn-exp  { background: #fff5f5; color: #e53e3e; border: 1.5px solid #fed7d7; }
.hdr-btn-trip { background: linear-gradient(135deg,#667eea,#764ba2); color: #fff; box-shadow: 0 4px 12px rgba(102,126,234,.35); }

/* ── Responsive overrides ──────────────────────────────────── */
@media (max-width: 575px) {
    .kc-val   { font-size: 20px; }
    .ts-val   { font-size: 20px; }
    .dash-tbl .tbl-route,
    .dash-tbl .tbl-party { display: none; }  /* hide on xs to prevent scroll */
    .run-card .rc-meta    { display: none; }
    .hdr-btn-exp          { display: none; } /* hide secondary button on mobile */
}

@media (max-width: 767px) {
    .ts-sep { display: none; }
    .today-strip { gap: 6px; padding: 12px 14px; }
}

@media (max-width: 991px) {
    .dc-head { flex-wrap: wrap; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="pcoded-inner-content">
<div class="main-body">
<div class="page-wrapper">
<div class="page-body" style="padding:20px 20px 40px; background:#f4f6fb; min-height:100vh;">


<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:22px;">
    <div>
        <h4 style="font-size:21px;font-weight:900;color:#1a2340;margin:0;line-height:1.2;">Dashboard</h4>
        <p  style="font-size:12.5px;color:#8a94a6;margin:3px 0 0;"><?php echo e($today->format('l, d F Y')); ?> &nbsp;·&nbsp; Live overview</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="<?php echo e(route('expense.create')); ?>" class="hdr-btn hdr-btn-exp"><i class="ti-receipt"></i> Add Expense</a>
        <a href="<?php echo e(route('trip.create')); ?>"    class="hdr-btn hdr-btn-trip"><i class="ti-plus"></i> New Trip</a>
    </div>
</div>


<div class="dash-row">
    <div class="dash-col" style="width:100%;flex:0 0 100%;max-width:100%;
        @media (min-width:576px){flex:0 0 50%;max-width:50%;}">
    </div>
</div>

<div class="row" style="margin-left:-10px;margin-right:-10px;">
    
    <div class="col-6 col-md-6 col-xl-3" style="padding:0 10px;">
        <div class="kc">
            <div class="kc-ico" style="background:#ecfdf5;color:#059669"><i class="ti-money"></i></div>
            <div class="kc-body">
                <div class="kc-lbl">Total Revenue</div>
                <div class="kc-val">₹<?php echo e(number_format(($allTrips->total_revenue??0)/1000,1)); ?>k</div>
                <div class="kc-ft">
                    Completed
                    <?php if($revenueGrowth['up']): ?>
                        <span class="gbadge gbadge-up"><i class="ti-arrow-up"></i><?php echo e($revenueGrowth['value']); ?>%</span>
                    <?php else: ?>
                        <span class="gbadge gbadge-down"><i class="ti-arrow-down"></i><?php echo e($revenueGrowth['value']); ?>%</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-md-6 col-xl-3" style="padding:0 10px;">
        <div class="kc">
            <div class="kc-ico" style="background:#eef2ff;color:#667eea"><i class="ti-bar-chart"></i></div>
            <div class="kc-body">
                <div class="kc-lbl">This Month</div>
                <div class="kc-val">₹<?php echo e(number_format(($monthTrips->revenue??0)/1000,1)); ?>k</div>
                <div class="kc-ft">
                    <?php echo e($monthTrips->total??0); ?> trips
                    <?php if($tripGrowth['up']): ?>
                        <span class="gbadge gbadge-up"><i class="ti-arrow-up"></i><?php echo e($tripGrowth['value']); ?>%</span>
                    <?php else: ?>
                        <span class="gbadge gbadge-down"><i class="ti-arrow-down"></i><?php echo e($tripGrowth['value']); ?>%</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-md-6 col-xl-3" style="padding:0 10px;">
        <div class="kc">
            <div class="kc-ico" style="background:#fff8e1;color:#d97706"><i class="ti-wallet"></i></div>
            <div class="kc-body">
                <div class="kc-lbl">Outstanding</div>
                <div class="kc-val" style="color:#d97706;">₹<?php echo e(number_format(($allTrips->total_outstanding??0)/1000,1)); ?>k</div>
                <div class="kc-ft">Uncollected balance</div>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-md-6 col-xl-3" style="padding:0 10px;">
        <div class="kc">
            <div class="kc-ico" style="background:#fff5f5;color:#e53e3e"><i class="ti-receipt"></i></div>
            <div class="kc-body">
                <div class="kc-lbl">Expenses / Month</div>
                <div class="kc-val" style="color:#e53e3e;">₹<?php echo e(number_format($expenseMonth/1000,1)); ?>k</div>
                <div class="kc-ft">
                    ₹<?php echo e(number_format($expenseYear/1000,1)); ?>k yr
                    <?php if($expenseGrowth['up']): ?>
                        <span class="gbadge gbadge-down"><i class="ti-arrow-up"></i><?php echo e($expenseGrowth['value']); ?>%</span>
                    <?php else: ?>
                        <span class="gbadge gbadge-up"><i class="ti-arrow-down"></i><?php echo e($expenseGrowth['value']); ?>%</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row" style="margin-left:-10px;margin-right:-10px;">
    <?php
    $kpis2 = [
        ['lbl'=>'Total Trips', 'val'=>$allTrips->total??0,    'sub'=>'All time',        'ico'=>'ti-truck',   'ic'=>'#0369a1','ib'=>'#f0f9ff'],
        ['lbl'=>'Vehicles',    'val'=>$totalVehicles,          'sub'=>$activeVehicles.' active', 'ico'=>'ti-car',    'ic'=>'#7c3aed','ib'=>'#f5f3ff'],
        ['lbl'=>'Drivers',     'val'=>$totalDrivers,           'sub'=>'Registered',      'ico'=>'ti-id-badge','ic'=>'#059669','ib'=>'#ecfdf5'],
        ['lbl'=>'Parties',     'val'=>$totalParties,           'sub'=>'Customers',       'ico'=>'ti-layers',  'ic'=>'#b45309','ib'=>'#fff8e1'],
    ];
    ?>
    <?php $__currentLoopData = $kpis2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-6 col-md-3" style="padding:0 10px;">
        <div class="kc kc-sm">
            <div class="kc-ico" style="background:<?php echo e($k['ib']); ?>;color:<?php echo e($k['ic']); ?>"><i class="<?php echo e($k['ico']); ?>"></i></div>
            <div class="kc-body">
                <div class="kc-lbl"><?php echo e($k['lbl']); ?></div>
                <div class="kc-val"><?php echo e($k['val']); ?></div>
                <div class="kc-ft"><?php echo e($k['sub']); ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<span class="s-label">Today — <?php echo e($today->format('d M Y')); ?></span>
<div class="today-strip">
    <?php
    $strips = [
        ['lbl'=>'Total',     'val'=>$todayTrips->total??0,     'c'=>'#667eea','bg'=>'#eef2ff'],
        ['lbl'=>'Planned',   'val'=>$todayTrips->planned??0,   'c'=>'#7c3aed','bg'=>'#f5f3ff'],
        ['lbl'=>'Running',   'val'=>$todayTrips->running??0,   'c'=>'#f6ad55','bg'=>'#fffbeb'],
        ['lbl'=>'Completed', 'val'=>$todayTrips->completed??0, 'c'=>'#059669','bg'=>'#ecfdf5'],
        ['lbl'=>'Cancelled', 'val'=>$todayTrips->cancelled??0, 'c'=>'#e53e3e','bg'=>'#fff5f5'],
    ];
    ?>
    <?php $__currentLoopData = $strips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($i > 0): ?><div class="ts-sep"></div><?php endif; ?>
        <a href="<?php echo e(route('trip')); ?>" class="ts-item" style="background:<?php echo e($s['bg']); ?>;">
            <span class="ts-val" style="color:<?php echo e($s['c']); ?>;"><?php echo e($s['val']); ?></span>
            <span class="ts-lbl"><?php echo e($s['lbl']); ?></span>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <div class="ts-sep"></div>
    <div class="ts-item" style="background:#f0f9ff;">
        <span class="ts-val" style="color:#0369a1;">₹<?php echo e(number_format($expenseToday,0)); ?></span>
        <span class="ts-lbl">Today Exp.</span>
    </div>
    <div class="ts-sep"></div>
    <div class="ts-item" style="background:#fff5f5;">
        <span class="ts-val" style="color:#e53e3e;"><?php echo e(count($overdueEmis)); ?></span>
        <span class="ts-lbl">Overdue EMI</span>
    </div>
</div>


<div class="row" style="margin-left:-10px;margin-right:-10px;">
    <div class="col-12 col-lg-8" style="padding:0 10px;">
        <div class="dc dc-pad">
            <div class="dc-head">
                <div>
                    <div class="dc-title">Revenue vs Collection</div>
                    <div class="dc-sub"><?php echo e(now()->year); ?> — month-by-month, completed trips</div>
                </div>
                <a href="<?php echo e(route('reports.trips')); ?>" class="dc-link">Report</a>
            </div>
            <div id="chartRevenue" style="height:280px;"></div>
        </div>
    </div>
    <div class="col-12 col-lg-4" style="padding:0 10px;">
        <div class="dc dc-pad" style="height:calc(100% - 20px);">
            <div class="dc-head">
                <div>
                    <div class="dc-title">Trip Status</div>
                    <div class="dc-sub">All-time distribution</div>
                </div>
            </div>
            <div id="chartTripDonut" style="height:195px;"></div>
            <div style="display:flex;flex-wrap:wrap;gap:8px 16px;margin-top:8px;">
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#4a5568;"><span style="width:9px;height:9px;border-radius:50%;background:#667eea;flex-shrink:0;display:inline-block;"></span>Planned (<?php echo e($allTrips->planned??0); ?>)</div>
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#4a5568;"><span style="width:9px;height:9px;border-radius:50%;background:#f6ad55;flex-shrink:0;display:inline-block;"></span>Running (<?php echo e($allTrips->running??0); ?>)</div>
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#4a5568;"><span style="width:9px;height:9px;border-radius:50%;background:#059669;flex-shrink:0;display:inline-block;"></span>Completed (<?php echo e($allTrips->completed??0); ?>)</div>
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#4a5568;"><span style="width:9px;height:9px;border-radius:50%;background:#e53e3e;flex-shrink:0;display:inline-block;"></span>Cancelled (<?php echo e($allTrips->cancelled??0); ?>)</div>
            </div>
        </div>
    </div>
</div>


<div class="row" style="margin-left:-10px;margin-right:-10px;">
    <div class="col-12 col-lg-7" style="padding:0 10px;">
        <div class="dc dc-pad">
            <div class="dc-head">
                <div>
                    <div class="dc-title">Monthly Trip Activity</div>
                    <div class="dc-sub">Total · Completed · Cancelled — <?php echo e(now()->year); ?></div>
                </div>
                <a href="<?php echo e(route('trip')); ?>" class="dc-link">All Trips</a>
            </div>
            <div id="chartTripActivity" style="height:230px;"></div>
        </div>
    </div>
    <div class="col-12 col-lg-5" style="padding:0 10px;">
        <div class="dc dc-pad">
            <div class="dc-head">
                <div>
                    <div class="dc-title">Expense by Category</div>
                    <div class="dc-sub">This month — ₹<?php echo e(number_format($expenseMonth,0)); ?></div>
                </div>
                <a href="<?php echo e(route('expense')); ?>" class="dc-link">View All</a>
            </div>
            <?php if(count($expenseByCategory) > 0): ?>
                <div id="chartExpCat" style="height:230px;"></div>
            <?php else: ?>
                <div style="text-align:center;padding:40px 0;color:#8a94a6;">
                    <i class="ti-receipt" style="font-size:34px;display:block;margin-bottom:8px;opacity:.3;"></i>
                    <span style="font-size:13px;">No expenses this month</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<span class="s-label">Expense Periods</span>
<div class="row" style="margin-left:-10px;margin-right:-10px;">
    <?php
    $expPer = [
        ['lbl'=>'Today',      'val'=>$expenseToday,  'sub'=>'vs ₹'.number_format($expenseYesterday,0).' yday', 'c'=>'#e53e3e','bg'=>'#fff5f5','ico'=>'ti-time'],
        ['lbl'=>'This Week',  'val'=>$expenseWeek,   'sub'=>'Current week',   'c'=>'#7c3aed','bg'=>'#f5f3ff','ico'=>'ti-calendar'],
        ['lbl'=>'This Month', 'val'=>$expenseMonth,  'sub'=>'Current month',  'c'=>'#0369a1','bg'=>'#f0f9ff','ico'=>'ti-stats-up'],
        ['lbl'=>'This Year',  'val'=>$expenseYear,   'sub'=>now()->year.' total', 'c'=>'#b45309','bg'=>'#fff8e1','ico'=>'ti-money'],
    ];
    ?>
    <?php $__currentLoopData = $expPer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-6 col-md-3" style="padding:0 10px;">
        <div class="dc dc-pad" style="display:flex;align-items:flex-start;gap:12px;margin-bottom:20px;">
            <div class="kc-ico" style="background:<?php echo e($ep['bg']); ?>;color:<?php echo e($ep['c']); ?>;width:38px;height:38px;border-radius:10px;font-size:16px;flex-shrink:0;">
                <i class="<?php echo e($ep['ico']); ?>"></i>
            </div>
            <div style="min-width:0;flex:1;overflow:hidden;">
                <div style="font-size:10.5px;font-weight:700;color:#8a94a6;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;"><?php echo e($ep['lbl']); ?></div>
                <div style="font-size:20px;font-weight:900;color:<?php echo e($ep['c']); ?>;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">₹<?php echo e(number_format($ep['val'],0)); ?></div>
                <div style="font-size:11px;color:#8a94a6;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo e($ep['sub']); ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="col-12" style="padding:0 10px;">
        <div class="dc dc-pad">
            <div class="dc-head">
                <div class="dc-title">Monthly Expenses — <?php echo e(now()->year); ?></div>
            </div>
            <div id="chartMonthExp" style="height:200px;"></div>
        </div>
    </div>
</div>


<div class="row" style="margin-left:-10px;margin-right:-10px;">

    
    <div class="col-12 col-lg-4" style="padding:0 10px;">
        <div class="dc dc-pad">
            <div class="dc-head" style="margin-bottom:10px;">
                <div>
                    <div class="dc-title">Vehicle EMIs</div>
                    <div class="dc-sub" style="margin-bottom:0;">Due this month: <strong>₹<?php echo e(number_format($emiMonthTotal,0)); ?></strong></div>
                </div>
                <a href="<?php echo e(route('emi')); ?>" class="dc-link">View All</a>
            </div>

            
            <?php if(count($overdueEmis) > 0): ?>
            <div class="alert-strip">
                <i class="ti-alert" style="font-size:17px;flex-shrink:0;"></i>
                <?php echo e(count($overdueEmis)); ?> EMI<?php echo e(count($overdueEmis)>1?'s':''); ?> overdue — immediate action needed
            </div>
            <?php $__currentLoopData = $overdueEmis->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="list-row">
                <div class="list-main">
                    <div class="list-title"><?php echo e($oe->vehicle->vehicle_number ?? 'N/A'); ?></div>
                    <div class="list-sub"><?php echo e($oe->financier_name); ?> · <?php echo e(\Carbon\Carbon::parse($oe->next_due_date)->diffForHumans()); ?></div>
                </div>
                <div class="list-right">
                    <div class="list-amt" style="color:#e53e3e;">₹<?php echo e(number_format($oe->emi_amount,0)); ?></div>
                    <span class="emi-tag" style="background:#fff5f5;color:#e53e3e;">Overdue</span>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if(count($overdueEmis) > 2): ?>
            <div style="font-size:12px;color:#e53e3e;font-weight:600;margin-top:4px;">+<?php echo e(count($overdueEmis)-2); ?> more overdue</div>
            <?php endif; ?>
            <hr style="border-color:#f1f3f8;margin:12px 0;">
            <?php endif; ?>

            
            <div style="font-size:10.5px;font-weight:800;color:#8a94a6;text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">Upcoming — next 30 days</div>
            <?php $__empty_1 = true; $__currentLoopData = $upcomingEmis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $dl=$emi->next_due_date?\Carbon\Carbon::today()->diffInDays($emi->next_due_date,false):0; $urg=$dl<=5; ?>
            <div class="list-row">
                <div class="list-main">
                    <div class="list-title"><?php echo e($emi->vehicle->vehicle_number ?? 'N/A'); ?></div>
                    <div class="list-sub"><?php echo e($emi->financier_name); ?></div>
                </div>
                <div class="list-right">
                    <div class="list-amt" style="color:<?php echo e($urg?'#e53e3e':'#1a2340'); ?>;">₹<?php echo e(number_format($emi->emi_amount,0)); ?></div>
                    <span class="emi-tag" style="background:<?php echo e($urg?'#fff5f5':'#ecfdf5'); ?>;color:<?php echo e($urg?'#e53e3e':'#059669'); ?>;">
                        <?php if($dl===0): ?> Today <?php elseif($dl===1): ?> Tomorrow <?php else: ?> In <?php echo e($dl); ?>d <?php endif; ?>
                    </span>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="text-align:center;padding:20px 0;color:#8a94a6;">
                <i class="ti-check-box" style="font-size:26px;display:block;margin-bottom:6px;color:#059669;"></i>
                <span style="font-size:12px;">No EMIs in next 30 days</span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="col-12 col-lg-8" style="padding:0 10px;">

        
        <?php if(count($runningTrips) > 0): ?>
        <div class="dc dc-pad" style="margin-bottom:16px;">
            <div class="dc-head" style="margin-bottom:10px;">
                <div>
                    <div class="dc-title" style="color:#d97706;"><i class="ti-control-forward"></i>&nbsp;Live Running Trips</div>
                    <div class="dc-sub" style="margin-bottom:0;"><?php echo e(count($runningTrips)); ?> trip<?php echo e(count($runningTrips)>1?'s':''); ?> currently on road</div>
                </div>
                <a href="<?php echo e(route('trip')); ?>" class="dc-link">All Trips</a>
            </div>
            <?php $__currentLoopData = $runningTrips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="run-card">
                <div style="font-size:20px;color:#f6ad55;flex-shrink:0;"><i class="ti-truck"></i></div>
                <div class="rc-body">
                    <div class="rc-route">
                        <a href="<?php echo e(route('trip.view',$rt->id)); ?>" style="color:#667eea;"><?php echo e($rt->trip_no ?? '#'.$rt->id); ?></a>
                        &nbsp;·&nbsp;<?php echo e($rt->from_location??'?'); ?> → <?php echo e($rt->to_location??'?'); ?>

                    </div>
                    <div class="rc-meta">
                        <?php echo e(optional($rt->driver)->name ?? 'No driver'); ?> &nbsp;·&nbsp;
                        <?php echo e(optional($rt->vehicle)->vehicle_number ?? '—'); ?> &nbsp;·&nbsp;
                        <?php echo e($rt->trip_date?->format('d M')); ?>

                    </div>
                </div>
                <div class="rc-right">
                    <div style="font-size:13px;font-weight:800;color:#059669;">₹<?php echo e(number_format($rt->freight_amount??0,0)); ?></div>
                    <div style="font-size:11px;color:#8a94a6;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <?php echo e(optional($rt->party)->company_name ?: (optional($rt->party)->name ?? '—')); ?>

                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        
        <div class="dc">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px 12px;flex-wrap:wrap;gap:8px;">
                <div>
                    <div class="dc-title">Recent Trips</div>
                    <div style="font-size:12px;color:#8a94a6;margin:2px 0 0;">Latest 8 trips</div>
                </div>
                <a href="<?php echo e(route('trip')); ?>" class="dc-link">View All</a>
            </div>
            <?php
            $smap = [
                'planned'   => ['c'=>'#667eea','b'=>'#eef2ff'],
                'running'   => ['c'=>'#f6ad55','b'=>'#fffbeb'],
                'completed' => ['c'=>'#059669','b'=>'#ecfdf5'],
                'cancelled' => ['c'=>'#e53e3e','b'=>'#fff5f5'],
            ];
            ?>
            <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
                <table class="dash-tbl" style="min-width:500px;">
                    <thead>
                        <tr>
                            <th>Trip No</th>
                            <th>Date</th>
                            <th class="d-none d-sm-table-cell">Route</th>
                            <th class="d-none d-md-table-cell">Party</th>
                            <th>Freight</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentTrips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $s = $smap[$trip->status] ?? ['c'=>'#8a94a6','b'=>'#f4f6fb']; ?>
                        <tr>
                            <td class="tbl-trip">
                                <a href="<?php echo e(route('trip.view',$trip->id)); ?>" style="color:#667eea;"><?php echo e($trip->trip_no ?? '#'.$trip->id); ?></a>
                            </td>
                            <td class="tbl-date"><?php echo e($trip->trip_date?->format('d M Y') ?? '—'); ?></td>
                            <td class="tbl-route d-none d-sm-table-cell"><?php echo e(($trip->from_location??'?').' → '.($trip->to_location??'?')); ?></td>
                            <td class="tbl-party d-none d-md-table-cell"><?php echo e(optional($trip->party)->company_name ?: (optional($trip->party)->name ?? '—')); ?></td>
                            <td class="tbl-amt">₹<?php echo e(number_format($trip->freight_amount??0,0)); ?></td>
                            <td><span class="spill" style="color:<?php echo e($s['c']); ?>;background:<?php echo e($s['b']); ?>;"><?php echo e(ucfirst($trip->status??'planned')); ?></span></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" style="text-align:center;padding:30px;color:#8a94a6;">No trips yet</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="row" style="margin-left:-10px;margin-right:-10px;margin-top:4px;">
    <div class="col-12 col-md-6" style="padding:0 10px;">
        <div class="dc dc-pad">
            <div class="dc-head" style="margin-bottom:12px;">
                <div>
                    <div class="dc-title">Top Parties by Revenue</div>
                    <div class="dc-sub" style="margin-bottom:0;"><?php echo e(now()->year); ?></div>
                </div>
                <a href="<?php echo e(route('parties')); ?>" class="dc-link">All</a>
            </div>
            <?php $maxR = $topParties->max('total_revenue') ?: 1; ?>
            <?php $__empty_1 = true; $__currentLoopData = $topParties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="list-row">
                <div class="rank-badge"><?php echo e($i+1); ?></div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:700;color:#1a2340;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo e($p->party_name); ?></div>
                    <div class="prog-wrap"><div class="prog-fill" style="width:<?php echo e(round(($p->total_revenue/$maxR)*100)); ?>%;background:#667eea;"></div></div>
                </div>
                <div style="text-align:right;flex-shrink:0;margin-left:10px;">
                    <div style="font-size:13px;font-weight:800;color:#059669;">₹<?php echo e(number_format($p->total_revenue/1000,1)); ?>k</div>
                    <div style="font-size:11px;color:#8a94a6;"><?php echo e($p->trip_count); ?> trip<?php echo e($p->trip_count>1?'s':''); ?></div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="text-align:center;padding:20px;color:#8a94a6;font-size:13px;">No data yet</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-12 col-md-6" style="padding:0 10px;">
        <div class="dc dc-pad">
            <div class="dc-head" style="margin-bottom:12px;">
                <div>
                    <div class="dc-title">Top Vehicles by Trips</div>
                    <div class="dc-sub" style="margin-bottom:0;"><?php echo e(now()->year); ?></div>
                </div>
                <a href="<?php echo e(route('vehicle')); ?>" class="dc-link">All</a>
            </div>
            <?php $maxT = $topVehicles->max('trip_count') ?: 1; ?>
            <?php $__empty_1 = true; $__currentLoopData = $topVehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="list-row">
                <div class="rank-badge"><?php echo e($i+1); ?></div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:700;color:#1a2340;"><?php echo e($v->vehicle_number); ?></div>
                    <div class="prog-wrap"><div class="prog-fill" style="width:<?php echo e(round(($v->trip_count/$maxT)*100)); ?>%;background:#f6ad55;"></div></div>
                </div>
                <div style="text-align:right;flex-shrink:0;margin-left:10px;">
                    <div style="font-size:13px;font-weight:800;color:#1a2340;"><?php echo e($v->trip_count); ?> trips</div>
                    <div style="font-size:11px;color:#8a94a6;">₹<?php echo e(number_format($v->revenue/1000,1)); ?>k</div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="text-align:center;padding:20px;color:#8a94a6;font-size:13px;">No data yet</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</div>
<div id="styleSelector"></div>
</div></div></div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.2/dist/apexcharts.min.js"></script>
<script>
(function () {
    'use strict';

    const months = <?php echo json_encode($monthLabels, 15, 512) ?>;
    const fmtINR = v => '₹' + Number(v).toLocaleString('en-IN');
    const fmtK   = v => '₹' + (Math.abs(v) >= 1000 ? (v / 1000).toFixed(1) + 'k' : v);
    const base   = {
        chart    : { toolbar: { show: false }, fontFamily: "'Open Sans', sans-serif", foreColor: '#8a94a6', animations: { speed: 500 } },
        grid     : { borderColor: '#f1f3f8', strokeDashArray: 4 },
        tooltip  : { theme: 'light', style: { fontSize: '12px' } },
        dataLabels: { enabled: false },
    };

    /* ── 1. Revenue vs Collection — smooth area ── */
    new ApexCharts(document.getElementById('chartRevenue'), {
        ...base,
        chart   : { ...base.chart, type: 'area', height: 280 },
        series  : [
            { name: 'Revenue',   data: <?php echo json_encode($monthlyRevenueArr, 15, 512) ?> },
            { name: 'Collected', data: <?php echo json_encode($monthlyCollectedArr, 15, 512) ?> },
        ],
        colors  : ['#667eea', '#059669'],
        fill    : { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.02, stops: [0, 100] } },
        stroke  : { curve: 'smooth', width: 2.5 },
        markers : { size: 4, strokeWidth: 2, hover: { size: 6 } },
        xaxis   : { categories: months, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '11px' } } },
        yaxis   : { labels: { formatter: fmtK, style: { fontSize: '11px' } } },
        legend  : { position: 'top', horizontalAlign: 'right', fontSize: '12px', markers: { width: 10, height: 10, radius: 10 } },
        tooltip : { y: { formatter: fmtINR } },
    }).render();

    /* ── 2. Trip Status — donut ── */
    new ApexCharts(document.getElementById('chartTripDonut'), {
        ...base,
        chart   : { ...base.chart, type: 'donut', height: 195 },
        series  : [<?php echo e($allTrips->planned??0); ?>, <?php echo e($allTrips->running??0); ?>, <?php echo e($allTrips->completed??0); ?>, <?php echo e($allTrips->cancelled??0); ?>],
        labels  : ['Planned', 'Running', 'Completed', 'Cancelled'],
        colors  : ['#667eea', '#f6ad55', '#059669', '#e53e3e'],
        plotOptions: { pie: { donut: { size: '68%', labels: {
            show: true,
            total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 800, color: '#1a2340',
                formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0) }
        }}}},
        legend  : { show: false },
        stroke  : { width: 2 },
        tooltip : { y: { formatter: v => v + ' trips' } },
    }).render();

    /* ── 3. Monthly Trip Activity — grouped bar ── */
    new ApexCharts(document.getElementById('chartTripActivity'), {
        ...base,
        chart   : { ...base.chart, type: 'bar', height: 230 },
        series  : [
            { name: 'Total',     data: <?php echo json_encode($monthlyTripArr, 15, 512) ?> },
            { name: 'Completed', data: <?php echo json_encode($monthlyCompletedArr, 15, 512) ?> },
            { name: 'Cancelled', data: <?php echo json_encode($monthlyCancelledArr, 15, 512) ?> },
        ],
        colors  : ['#667eea', '#059669', '#e53e3e'],
        plotOptions: { bar: { borderRadius: 5, columnWidth: '58%' } },
        xaxis   : { categories: months, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '11px' } } },
        yaxis   : { labels: { style: { fontSize: '11px' } }, tickAmount: 4, min: 0 },
        legend  : { position: 'top', horizontalAlign: 'right', fontSize: '12px', markers: { width: 10, height: 10, radius: 10 } },
        tooltip : { y: { formatter: v => v + ' trips' } },
    }).render();

    /* ── 4. Expense by Category — horizontal bar ── */
    <?php if(count($expenseByCategory) > 0): ?>
    <?php
    $catColors = ['#e53e3e','#7c3aed','#059669','#d97706','#0369a1','#b45309','#667eea','#8a94a6'];
    ?>
    new ApexCharts(document.getElementById('chartExpCat'), {
        ...base,
        chart   : { ...base.chart, type: 'bar', height: 230 },
        series  : [{ name: 'Amount', data: <?php echo json_encode(array_column($expenseByCategory, 'total'), 512) ?> }],
        colors  : <?php echo json_encode(array_slice($catColors, 0, count($expenseByCategory))) ?>,
        plotOptions: { bar: { borderRadius: 6, horizontal: true, barHeight: '55%', distributed: true, dataLabels: { position: 'top' } } },
        xaxis   : { categories: <?php echo json_encode(array_column($expenseByCategory, 'label'), 512) ?>, labels: { formatter: fmtK, style: { fontSize: '11px' } } },
        yaxis   : { labels: { style: { fontSize: '12px', fontWeight: 600 } } },
        legend  : { show: false },
        dataLabels: { enabled: true, formatter: fmtINR, offsetX: 4, style: { fontSize: '11px', colors: ['#4a5568'], fontWeight: 600 } },
        tooltip : { y: { formatter: fmtINR } },
    }).render();
    <?php endif; ?>

    /* ── 5. Monthly Expenses — gradient column ── */
    new ApexCharts(document.getElementById('chartMonthExp'), {
        ...base,
        chart   : { ...base.chart, type: 'bar', height: 200 },
        series  : [{ name: 'Expenses', data: <?php echo json_encode($monthlyExpenseArr, 15, 512) ?> }],
        colors  : ['#e53e3e'],
        fill    : { type: 'gradient', gradient: { shade: 'light', type: 'vertical', shadeIntensity: 0.4, gradientToColors: ['#fc8181'], opacityFrom: 1, opacityTo: 0.7 } },
        plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
        xaxis   : { categories: months, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '11px' } } },
        yaxis   : { labels: { formatter: fmtK, style: { fontSize: '11px' } } },
        tooltip : { y: { formatter: fmtINR } },
    }).render();

})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\Kovai-Trans\resources\views/Index.blade.php ENDPATH**/ ?>