<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Packing Slip - <?php echo e($slip->bill_no ?? 'N/A'); ?></title>
    <style>
        @page {
            margin: 20px 20px;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #008000;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .header-sub {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #0056b3;
            margin-bottom: 25px;
            letter-spacing: 0.5px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .meta-table td {
            padding: 4px 0;
        }

        .bale-layout-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bale-layout-table tr {
            page-break-inside: avoid;
        }

        .bale-layout-table td {
            vertical-align: top;
            padding: 4px;
        }

        .bale-card-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            font-size: 9.5px;
            page-break-inside: avoid;
        }

        .bale-card-table th {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
        }

        .bale-card-table td {
            border: 1px solid #000;
            padding: 3px 2px;
        }

        .bale-card-header {
            font-size: 10.5px;
            text-align: center;
        }

        .col-header {
            color: #0056b3;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header-title"><b>SRI LAKSHMI FABRICS</b></div>
    <div class="header-sub"><b>PALLADAM - 641664</b></div>

    <?php
    $min = $slip->baleItems->min('bale_no');
    $max = $slip->baleItems->max('bale_no');
    $baleCount = $slip->baleItems->unique('bale_no')->count();
    ?>

    <table class="meta-table">
        <tr>
            <td style="width: 50%; text-align: left;">BILL NO : <?php echo e($slip->bill_no ?? '—'); ?></td>
            <td style="width: 50%; text-align: right;">BALE NOS : <?php echo e($min && $max ? $min.'-'.$max : '—'); ?></td>
        </tr>
        <tr>
            <td style="text-align: left;">TO : <?php echo e(strtoupper(optional($slip->customer)->name ?? '—')); ?></td>
            <td style="text-align: right;">NO.OF BALE : <?php echo e($baleCount); ?></td>
        </tr>
        <tr>
            <td style="text-align: left;">QUALITY : <?php echo e(strtoupper($slip->quality ?? '—')); ?></td>
            <td style="text-align: right;">TOTAL MTR : <?php echo e(number_format($slip->total_meter, 2, '.', '')); ?><?php if($slip->notes): ?> (<?php echo e(strtoupper($slip->notes)); ?>)<?php endif; ?></td>
        </tr>
    </table>

    <table class="bale-layout-table">
        <?php
        $groups = $slip->baleItems->groupBy('bale_no')->sortKeys();
        $chunks = $groups->chunk(5);
        ?>
        <?php $__currentLoopData = $chunks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <?php $__currentLoopData = $chunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $baleNo => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <td style="width: 20%;">
                <table class="bale-card-table">
                    <thead>
                        <tr>
                            <th colspan="3" class="bale-card-header"><b>Bale No : <?php echo e($baleNo); ?></b></th>
                        </tr>
                        <tr class="col-header">
                            <th style="width: 22%;" class="text-center"><b>S.No</b></th>
                            <th style="width: 48%;" class="text-center"><b>Meter</b></th>
                            <th style="width: 30%;" class="text-center"><b>Wgt.</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i = 1; $i <= 10; $i++): ?>
                            <?php
                            $bi=$items->firstWhere('s_no', $i);
                            ?>
                            <tr>
                                <td class="text-center"><?php echo e($i); ?></td>
                                <td class="text-center">
                                    <?php echo e($bi && $bi->meter > 0 ? number_format($bi->meter, 2, '.', '') : ''); ?>

                                </td>
                                <td class="text-center">
                                    <?php echo e($bi && $bi->weight > 0 ? floatval($bi->weight) : ''); ?>

                                </td>
                            </tr>
                            <?php endfor; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bold">
                            <td class="text-center"><b>Total</b></td>
                            <td class="text-center">
                                <b><?php echo e(number_format($items->sum('meter'), 2, '.', '')); ?></b>
                            </td>
                            <td class="text-center">
                                <?php $wSum = $items->sum('weight'); ?>
                                <b><?php echo e($wSum > 0 ? floatval($wSum) : ''); ?></b>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php for($i = count($chunk); $i < 5; $i++): ?>
                <td style="width: 20%;">
                </td>
                <?php endfor; ?>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>

</body>

</html><?php /**PATH D:\laragon\www\Kovai-Trans\resources\views/PackingSlip/pdf.blade.php ENDPATH**/ ?>