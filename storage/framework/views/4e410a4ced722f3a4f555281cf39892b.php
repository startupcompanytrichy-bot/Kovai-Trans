<?php $__env->startSection('content'); ?>
<style>
.rpt-page { background:#f4f6fb; }
.rpt-header { background:linear-gradient(135deg,#9333ea 0%,#7c3aed 100%); border-radius:14px; padding:20px 24px; color:#fff; margin-bottom:20px; position:relative; overflow:hidden; }
.rpt-header::before { content:''; position:absolute; top:-30px; right:-30px; width:120px; height:120px; background:rgba(255,255,255,.07); border-radius:50%; }
.rpt-header h4 { font-size:18px; font-weight:800; margin:0 0 4px; }
.rpt-header .sub { font-size:12px; opacity:.8; }
.rpt-filter { background:#fff; border-radius:12px; padding:14px 18px; box-shadow:0 2px 10px rgba(0,0,0,.06); margin-bottom:16px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.rpt-filter .form-control { min-height:40px; font-size:13px; border-color:#e2e8f0; border-radius:8px; }
.col-summary { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:16px; }
.col-sum-card { background:#fff; border-radius:10px; padding:14px 16px; box-shadow:0 2px 8px rgba(0,0,0,.05); text-align:center; }
.col-sum-card .csc-label { font-size:10px; font-weight:700; color:#8a94a6; text-transform:uppercase; letter-spacing:.4px; }
.col-sum-card .csc-value { font-size:20px; font-weight:800; color:#1a2340; margin-top:2px; }
.rpt-card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); overflow:hidden; }
.rpt-card-header { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid #f0f2f7; background:#fafbff; flex-wrap:wrap; gap:8px; }
.rpt-card-header h6 { margin:0; font-size:14px; font-weight:700; color:#1a2340; }
.rpt-table-wrap { overflow-x:auto; }
#psTable { min-width:1100px; margin-bottom:0; }
#psTable th, #psTable td { height:44px; padding:6px 12px; vertical-align:middle; border-color:#f0f2f7; font-size:12px; }
#psTable th { background:#f8fafc; color:#14213d; font-weight:800; font-size:11px; text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; position:sticky; top:0; z-index:2; }
.rpt-badge { display:inline-flex; align-items:center; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700; text-transform:uppercase; }
@media print {
    .pcoded-navbar,.pcoded-header,.pcoded-footer,.rpt-filter,.rpt-card-header .btn,.rpt-card-header .d-flex { display:none!important; }
    .rpt-page,.pcoded-content,.pcoded-inner-content,.main-body,.page-wrapper,.page-body { background:#fff!important; padding:0!important; margin:0!important; }
}
</style>

<div class="pcoded-inner-content rpt-page">
<div class="main-body"><div class="page-wrapper"><div class="page-body">

<div class="rpt-header">
    <div class="row align-items-center">
        <div class="col-md-8" style="position:relative;z-index:1;">
            <h4><i class="ti-layout mr-2"></i>Packing Slip Ledger</h4>
            <div class="sub"><?php echo e($totalTrips); ?> trips &bull; Total Qty: <?php echo e(number_format($totalQty,0)); ?></div>
        </div>
        <div class="col-md-4 text-right mt-2 mt-md-0" style="position:relative;z-index:1;">
            <a href="<?php echo e(route('reports')); ?>" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-weight:600;margin-right:6px;">
                <i class="ti-arrow-left mr-1"></i> Reports
            </a>
            <button onclick="exportPsExcel()" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 14px;font-weight:600;margin-right:6px;">
                <i class="ti-export mr-1"></i> Excel
            </button>
            <button onclick="window.print()" class="btn btn-sm" style="background:#fff;color:#9333ea;border-radius:8px;padding:7px 16px;font-weight:700;">
                <i class="ti-printer mr-1"></i> Print
            </button>
        </div>
    </div>
</div>

<form method="GET" action="<?php echo e(route('reports.packing-slip-ledger')); ?>">
<div class="rpt-filter">
    <select name="party_id" class="form-control select2" data-placeholder="All Parties" style="min-width:160px;width:auto;">
        <option value=""></option>
        <?php $__currentLoopData = $parties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($p->id); ?>" <?php echo e(request('party_id') == $p->id ? 'selected' : ''); ?>><?php echo e($p->company_name ?: $p->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <input type="date" name="date_from" class="form-control" style="max-width:150px;" value="<?php echo e(request('date_from')); ?>" title="From Date">
    <input type="date" name="date_to" class="form-control" style="max-width:150px;" value="<?php echo e(request('date_to')); ?>" title="To Date">
    <button type="submit" class="btn btn-primary btn-sm" style="border-radius:8px;padding:8px 18px;white-space:nowrap;background:#9333ea;border-color:#9333ea;">
        <i class="ti-search mr-1"></i> Filter
    </button>
    <a href="<?php echo e(route('reports.packing-slip-ledger')); ?>" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;padding:8px 14px;">
        <i class="ti-close mr-1"></i> Clear
    </a>
</div>
</form>

<div class="col-summary">
    <div class="col-sum-card">
        <div class="csc-label">Total Trips</div>
        <div class="csc-value" style="color:#9333ea;"><?php echo e($totalTrips); ?></div>
    </div>
    <div class="col-sum-card">
        <div class="csc-label">Total Quantity</div>
        <div class="csc-value" style="color:#16a34a;"><?php echo e(number_format($totalQty,0)); ?></div>
    </div>
    <div class="col-sum-card">
        <div class="csc-label">Parties</div>
        <div class="csc-value" style="color:#2563eb;"><?php echo e($trips->pluck('party_id')->unique()->count()); ?></div>
    </div>
</div>

<div class="rpt-card">
    <div class="rpt-card-header">
        <h6><i class="ti-layout mr-2" style="color:#9333ea;"></i>Packing Slips (<?php echo e($totalTrips); ?> records)</h6>
        <div style="display:flex;gap:8px;">
            <button onclick="exportPsExcel()" class="btn btn-sm btn-outline-success" style="border-radius:8px;"><i class="ti-export mr-1"></i> Export Excel</button>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="ti-printer mr-1"></i> Print</button>
        </div>
    </div>
    <div class="rpt-table-wrap">
        <table class="table table-striped table-bordered" id="psTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>LR No</th>
                    <th>Party</th>
                    <th>From → To</th>
                    <th>Material</th>
                    <th style="text-align:right;">Qty</th>
                    <th>Vehicle</th>
                    <th>Invoice</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $trips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($i+1); ?></td>
                    <td><?php echo e($t->trip_date?->format('d/m/Y')); ?></td>
                    <td><strong style="color:#9333ea;"><?php echo e($t->lr_no); ?></strong></td>
                    <td><?php echo e(optional($t->party)->company_name ?: optional($t->party)->name ?: '—'); ?></td>
                    <td style="font-size:11px;">
                        <?php echo e($t->from_location ?? '—'); ?> → <?php echo e($t->to_location ?? '—'); ?>

                        <?php if($t->from_state || $t->to_state): ?>
                        <div style="font-size:9px;color:#8a94a6;"><?php echo e($t->from_state ?? ''); ?> → <?php echo e($t->to_state ?? ''); ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($t->material ?? '—'); ?></td>
                    <td style="text-align:right;font-weight:700;"><?php echo e($t->quantity ? number_format($t->quantity,0) : '—'); ?></td>
                    <td><?php echo e(optional($t->vehicle)->vehicle_number ?? '—'); ?></td>
                    <td>
                        <?php if($t->invoice_no): ?>
                        <span class="rpt-badge" style="background:#eef2ff;color:#4338ca;"><?php echo e($t->invoice_no); ?></span>
                        <?php else: ?>
                        <span style="color:#b0bac9;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="9" class="text-center py-4" style="color:#b0bac9;">
                    <i class="ti-layout" style="font-size:32px;display:block;margin-bottom:8px;opacity:.3;"></i>
                    No packing slips found.
                </td></tr>
                <?php endif; ?>
            </tbody>
            <?php if($trips->count()): ?>
            <tfoot>
                <tr style="background:#f8fafc;font-weight:800;">
                    <td colspan="5" style="text-align:right;font-size:13px;">Total</td>
                    <td></td>
                    <td style="text-align:right;color:#16a34a;"><?php echo e(number_format($totalQty,0)); ?></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

</div></div></div></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function exportPsExcel() {
    var table = document.getElementById('psTable');
    var wb = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    wb += '<head><meta charset="UTF-8"></head><body><table>' + table.innerHTML + '</table></body></html>';
    var blob = new Blob([wb], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    var url  = URL.createObjectURL(blob);
    var a    = document.createElement('a');
    a.href   = url;
    a.download = 'Packing_Slip_Ledger_<?php echo e(now()->format("Y-m-d")); ?>.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\Kovai-Trans\resources\views/Reports/Packing_Slip_Ledger.blade.php ENDPATH**/ ?>