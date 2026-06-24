<?php $__env->startSection('content'); ?>
<style>
    .ps-bg {
        background: #f4f6fb;
    }

    .ps-hero {
        background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%);
        border-radius: 14px;
        padding: 20px 24px;
        color: #fff;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .ps-hero::before {
        content: '';
        position: absolute;
        top: -30px;
        right: -30px;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, .07);
        border-radius: 50%;
    }

    .ps-hero h4 {
        font-size: 18px;
        font-weight: 800;
        margin: 0 0 4px;
    }

    .ps-hero .sub {
        font-size: 12px;
        opacity: .8;
    }

    .ps-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        overflow: hidden;
    }

    .ps-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-bottom: 1px solid #f0f2f7;
        background: #fafbff;
        flex-wrap: wrap;
        gap: 8px;
    }

    .ps-card-header h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #1a2340;
    }

    .ps-table-wrap {
        overflow-x: auto;
    }

    #psTable {
        min-width: 1000px;
        margin-bottom: 0;
    }

    #psTable th,
    #psTable td {
        height: 44px;
        padding: 6px 12px;
        vertical-align: middle;
        border-color: #f0f2f7;
        font-size: 12px;
    }

    #psTable th {
        background: #f8fafc;
        color: #14213d;
        font-weight: 800;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .4px;
        white-space: nowrap;
    }

    .ps-stat {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }

    .ps-stat-card {
        background: #fff;
        border-radius: 10px;
        padding: 14px 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
        text-align: center;
    }

    .ps-stat-card .lbl {
        font-size: 10px;
        font-weight: 700;
        color: #8a94a6;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .ps-stat-card .val {
        font-size: 20px;
        font-weight: 800;
        color: #1a2340;
        margin-top: 2px;
    }

    .expand-btn {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        font-size: 11px;
        cursor: pointer;
        background: #eef2ff;
        color: #4338ca;
        transition: all .15s;
    }

    .expand-btn:hover {
        background: #9333ea;
        color: #fff;
    }

    .sub-table {
        margin: 0;
        font-size: 11px;
    }

    .sub-table th {
        background: #f8fafc;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .4px;
        padding: 4px 8px;
        border-color: #e8ecf4;
        color: #14213d;
    }

    .sub-table td {
        padding: 4px 8px;
        border-color: #f0f2f7;
    }
</style>

<div class="pcoded-inner-content ps-bg">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">

                <?php if(session('success')): ?>
                <div class="alert alert-success" style="border-radius:10px;font-size:13px;padding:12px 16px;"><?php echo e(session('success')); ?></div>
                <?php endif; ?>

                <div class="ps-hero">
                    <div class="row align-items-center">
                        <div class="col-md-6" style="position:relative;z-index:1;">
                            <h4><i class="ti-layout mr-2"></i>Packing Slips</h4>
                            <div class="sub">Manage all packing slips</div>
                        </div>
                        <div class="col-md-6 text-right" style="position:relative;z-index:1;">
                            <a href="<?php echo e(route('packing-slip.create')); ?>" class="btn btn-sm" style="border-radius:8px;background:#fff;color:#9333ea;border:none;padding:7px 18px;font-weight:700;">
                                <i class="ti-plus mr-1"></i> Add Slip
                            </a>
                        </div>
                    </div>
                </div>

                <div class="ps-stat">
                    <div class="ps-stat-card">
                        <div class="lbl">Total Slips</div>
                        <div class="val" style="color:#9333ea;"><?php echo e($slips->count()); ?></div>
                    </div>
                    <div class="ps-stat-card">
                        <div class="lbl">Total Bales</div>
                        <div class="val" style="color:#16a34a;"><?php echo e(number_format($slips->sum('no_of_bale'), 0)); ?></div>
                    </div>
                    <div class="ps-stat-card">
                        <div class="lbl">Total Meter</div>
                        <div class="val" style="color:#2563eb;"><?php echo e(number_format($slips->sum('total_meter'), 2)); ?></div>
                    </div>
                </div>

                <div class="ps-card">
                    <div class="ps-card-header">
                        <h6><i class="ti-layout mr-2" style="color:#9333ea;"></i>All Packing Slips</h6>
                        <div><input type="text" id="psSearch" class="form-control" style="min-height:36px;font-size:12px;border-radius:8px;border-color:#e2e8f0;width:220px;" placeholder="Search..." onkeyup="filterSlips()"></div>
                    </div>
                    <div class="ps-table-wrap">
                        <table class="table table-bordered" id="psTable">
                            <thead>
                                <tr>
                                    <!-- <th style="width:36px;"></th> -->
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Bill No</th>
                                    <th>Lot No</th>
                                    <th>Customer</th>
                                    <th>Quality</th>
                                    <th style="text-align:right;">Bales</th>
                                    <th style="text-align:right;">Meter</th>
                                    <th style="width:50px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $slips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="slip-row" data-id="<?php echo e($s->id); ?>" style="cursor:pointer;">
                                    <!-- <td style="text-align:center;padding:4px;" onclick="event.stopPropagation();">
                        <?php if($s->baleItems->count()): ?>
                        <button class="expand-btn" onclick="toggleRows(<?php echo e($s->id); ?>)" id="expandBtn-<?php echo e($s->id); ?>"><i class="ti-plus"></i></button>
                        <?php endif; ?>
                    </td> -->
                                    <td><?php echo e($i+1); ?></td>
                                    <td><?php echo e($s->slip_date?->format('d/m/Y')); ?></td>
                                    <td><?php echo e($s->bill_no ?? '—'); ?></td>
                                    <td><?php echo e($s->lot_no ?? '—'); ?></td>
                                    <td><?php echo e(optional($s->customer)->name ?? '—'); ?></td>
                                    <td style="font-size:11px;"><?php echo e($s->quality ?? '—'); ?></td>
                                    <td style="text-align:right;font-weight:700;"><?php echo e($s->no_of_bale ? number_format($s->no_of_bale, 0) : '—'); ?></td>
                                    <td style="text-align:right;font-weight:700;"><?php echo e($s->total_meter ? number_format($s->total_meter, 2) : '—'); ?></td>
                                    <td style="text-align:center;">
                                        <a href="<?php echo e(route('packing-slip.print', $s->id)); ?>" class="expand-btn" style="background:#eef2ff;color:#4338ca;" title="Print PDF">
                                            <i class="ti-printer"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr id="detail-<?php echo e($s->id); ?>" style="display:none;">
                                    <td colspan="10" style="padding:8px 16px;background:#fafbff;">
                                        <table class="table table-bordered sub-table" style="width:auto;min-width:400px;">
                                            <thead>
                                                <tr>
                                                    <th style="width:40px;">S.No</th>
                                                    <th style="width:60px;">Bale No</th>
                                                    <th>Meter</th>
                                                    <th>Weight</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $s->baleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td style="text-align:center;"><?php echo e($bi->s_no); ?></td>
                                                    <td style="text-align:center;font-weight:700;"><?php echo e($bi->bale_no); ?></td>
                                                    <td style="text-align:right;"><?php echo e(number_format($bi->meter, 2)); ?></td>
                                                    <td style="text-align:right;"><?php echo e(number_format($bi->weight, 2)); ?></td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight:800;background:#f8fafc;">
                                                    <td colspan="2" style="text-align:right;padding:4px 8px;">TOTAL</td>
                                                    <td style="text-align:right;padding:4px 8px;color:#9333ea;"><?php echo e(number_format($s->baleItems->sum('meter'), 2)); ?></td>
                                                    <td style="text-align:right;padding:4px 8px;color:#9333ea;"><?php echo e(number_format($s->baleItems->sum('weight'), 2)); ?></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="10" class="text-center py-4" style="color:#b0bac9;">No packing slips found. <a href="<?php echo e(route('packing-slip.create')); ?>" style="color:#9333ea;">Add one now</a></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function filterSlips() {
        var q = document.getElementById('psSearch').value.toLowerCase();
        document.querySelectorAll('#psTable tbody tr').forEach(function(r) {
            if (r.id.startsWith('detail-')) return;
            r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    function toggleRows(id) {
        var detail = document.getElementById('detail-' + id);
        var btn = document.getElementById('expandBtn-' + id);
        if (detail.style.display === 'none') {
            detail.style.display = '';
            btn.innerHTML = '<i class="ti-minus"></i>';
        } else {
            detail.style.display = 'none';
            btn.innerHTML = '<i class="ti-plus"></i>';
        }
    }

document.querySelectorAll('.slip-row').forEach(function(row) {
    row.addEventListener('click', function() {
        window.location.href = '<?php echo e(url("packing-slip")); ?>/' + this.dataset.id + '/edit';
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\Kovai-Trans\resources\views/PackingSlip/index.blade.php ENDPATH**/ ?>