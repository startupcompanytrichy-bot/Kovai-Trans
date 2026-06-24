<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Packing Slip - <?php echo e($slip->bill_no ?? 'N/A'); ?></title>
    <style>
        @page {
            size: a4;
            margin: 15px;
        }
        body {
            font-family: 'Courier', 'Courier New', monospace;
            font-size: 13px;
            color: #000000;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }
        .no-print-bar {
            background-color: #ffffff;
            padding: 12px 20px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .btn-print {
            background-color: #008000;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            font-size: 14px;
            font-weight: bold;
            font-family: sans-serif;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0, 128, 0, 0.2);
            transition: background-color 0.15s;
        }
        .btn-print:hover {
            background-color: #006400;
        }
        .btn-close {
            background-color: #dc3545;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            font-size: 14px;
            font-weight: bold;
            font-family: sans-serif;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
            transition: background-color 0.15s;
        }
        .btn-close:hover {
            background-color: #bd2130;
        }
        .print-container {
            width: 210mm; /* A4 width */
            margin: 0 auto 30px auto;
            background-color: #ffffff;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            box-sizing: border-box;
            border-radius: 4px;
        }
        .header-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #008000; /* green */
            margin: 0;
            padding: 0;
            letter-spacing: 1px;
        }
        .header-subtitle {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            color: #0056b3; /* blue */
            margin: 4px 0 0 0;
            padding: 0;
            letter-spacing: 1px;
        }
        .meta-table {
            width: 100%;
            margin-top: 15px;
            margin-bottom: 15px;
            font-weight: bold;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .bale-grid-table {
            width: 100%;
            border-collapse: collapse;
        }
        .bale-grid-table tr {
            page-break-inside: avoid;
        }
        .bale-grid-table td {
            padding: 5px;
            vertical-align: top;
        }
        .bale-card-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000000;
        }
        .bale-card-table th, .bale-card-table td {
            border: 1px solid #000000;
            font-size: 11px;
            height: 18px;
        }
        .bale-card-table th {
            font-weight: bold;
            text-align: center;
        }
        .bale-card-table th.text-right {
            text-align: right;
            padding-right: 5px;
        }
        .bale-card-title {
            font-size: 12px;
            font-weight: bold;
            padding: 3px;
            background-color: transparent;
        }
        .col-blue {
            color: #0056b3; /* blue */
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
            padding-right: 5px;
        }

        @media print {
            body {
                background-color: #ffffff;
            }
            .no-print-bar {
                display: none !important;
            }
            .print-container {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <button onclick="window.print()" class="btn-print">Print</button>
        <button onclick="window.close()" class="btn-close">Close</button>
    </div>

    <div class="print-container">
        <div class="header-title">SRI LAKSHMI FABRICS</div>
        <div class="header-subtitle">PALLADAM - 641664</div>

        <?php
            $min = $slip->baleItems->min('bale_no');
            $max = $slip->baleItems->max('bale_no');
            $baleCount = $slip->baleItems->unique('bale_no')->count();
            $groups = $slip->baleItems->groupBy('bale_no')->sortKeys();
        ?>

        <table class="meta-table">
            <tr>
                <td style="width: 13%;">BILL NO</td>
                <td style="width: 3%;">:</td>
                <td style="width: 44%;"><?php echo e($slip->bill_no ?? '—'); ?></td>
                
                <td style="width: 15%;">BALE NOS</td>
                <td style="width: 3%;">:</td>
                <td style="width: 22%;"><?php echo e($min && $max ? $min.'-'.$max : '—'); ?></td>
            </tr>
            <tr>
                <td>TO</td>
                <td>:</td>
                <td><?php echo e(optional($slip->customer)->name ?? '—'); ?></td>
                
                <td>NO.OF BALE</td>
                <td>:</td>
                <td><?php echo e($baleCount); ?></td>
            </tr>
            <tr>
                <td>QUALITY</td>
                <td>:</td>
                <td><?php echo e($slip->quality ?? '—'); ?></td>
                
                <td>TOTAL MTR</td>
                <td>:</td>
                <td>
                    <?php echo e(number_format($slip->total_meter, 2)); ?>

                    <?php if($slip->notes): ?>
                        (<?php echo e($slip->notes); ?>)
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <table class="bale-grid-table">
            <?php $__currentLoopData = $groups->chunk(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <?php $__currentLoopData = $chunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $baleNo => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td style="width: 20%;">
                            <table class="bale-card-table">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="bale-card-title text-center">Bale No : <?php echo e($baleNo); ?></th>
                                    </tr>
                                    <tr class="col-blue">
                                        <th style="width: 25%;">S.No</th>
                                        <th style="width: 45%;">Meter</th>
                                        <th style="width: 30%;">Wgt.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for($i = 1; $i <= 10; $i++): ?>
                                        <?php
                                            $bi = $items->firstWhere('s_no', $i);
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($i); ?></td>
                                            <td class="text-right">
                                                <?php echo e($bi && $bi->meter > 0 ? number_format($bi->meter, 2) : ''); ?>

                                            </td>
                                            <td class="text-right">
                                                <?php echo e($bi && $bi->weight > 0 ? floatval($bi->weight) : ''); ?>

                                            </td>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                                <tfoot>
                                    <tr style="font-weight: bold;">
                                        <th class="text-center" style="font-weight: bold;">Total</th>
                                        <th class="text-right" style="font-weight: bold;"><?php echo e(number_format($items->sum('meter'), 2)); ?></th>
                                        <th class="text-right" style="font-weight: bold;">
                                            <?php $wSum = $items->sum('weight'); ?>
                                            <?php echo e($wSum > 0 ? floatval($wSum) : ''); ?>

                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php for($i = count($chunk); $i < 5; $i++): ?>
                        <td style="width: 20%;"></td>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    </div>

</body>
</html>
<?php /**PATH D:\laragon\www\Kovai-Trans\resources\views/PackingSlip/print.blade.php ENDPATH**/ ?>