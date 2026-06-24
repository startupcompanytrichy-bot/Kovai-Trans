<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Packing Slip - {{ $slip->bill_no ?? 'N/A' }}</title>
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

    @php
    $min = $slip->baleItems->min('bale_no');
    $max = $slip->baleItems->max('bale_no');
    $baleCount = $slip->baleItems->unique('bale_no')->count();
    @endphp

    <table class="meta-table">
        <tr>
            <td style="width: 50%; text-align: left;">BILL NO : {{ $slip->bill_no ?? '—' }}</td>
            <td style="width: 50%; text-align: right;">BALE NOS : {{ $min && $max ? $min.'-'.$max : '—' }}</td>
        </tr>
        <tr>
            <td style="text-align: left;">TO : {{ strtoupper(optional($slip->customer)->name ?? '—') }}</td>
            <td style="text-align: right;">NO.OF BALE : {{ $baleCount }}</td>
        </tr>
        <tr>
            <td style="text-align: left;">QUALITY : {{ strtoupper($slip->quality ?? '—') }}</td>
            <td style="text-align: right;">TOTAL MTR : {{ number_format($slip->total_meter, 2, '.', '') }}@if($slip->notes) ({{ strtoupper($slip->notes) }})@endif</td>
        </tr>
    </table>

    <table class="bale-layout-table">
        @php
        $groups = $slip->baleItems->groupBy('bale_no')->sortKeys();
        $chunks = $groups->chunk(5);
        @endphp
        @foreach($chunks as $chunk)
        <tr>
            @foreach($chunk as $baleNo => $items)
            <td style="width: 20%;">
                <table class="bale-card-table">
                    <thead>
                        <tr>
                            <th colspan="3" class="bale-card-header"><b>Bale No : {{ $baleNo }}</b></th>
                        </tr>
                        <tr class="col-header">
                            <th style="width: 22%;" class="text-center"><b>S.No</b></th>
                            <th style="width: 48%;" class="text-center"><b>Meter</b></th>
                            <th style="width: 30%;" class="text-center"><b>Wgt.</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 1; $i <= 10; $i++)
                            @php
                            $bi=$items->firstWhere('s_no', $i);
                            @endphp
                            <tr>
                                <td class="text-center">{{ $i }}</td>
                                <td class="text-center">
                                    {{ $bi && $bi->meter > 0 ? number_format($bi->meter, 2, '.', '') : '' }}
                                </td>
                                <td class="text-center">
                                    {{ $bi && $bi->weight > 0 ? floatval($bi->weight) : '' }}
                                </td>
                            </tr>
                            @endfor
                    </tbody>
                    <tfoot>
                        <tr class="bold">
                            <td class="text-center"><b>Total</b></td>
                            <td class="text-center">
                                <b>{{ number_format($items->sum('meter'), 2, '.', '') }}</b>
                            </td>
                            <td class="text-center">
                                @php $wSum = $items->sum('weight'); @endphp
                                <b>{{ $wSum > 0 ? floatval($wSum) : '' }}</b>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </td>
            @endforeach
            @for($i = count($chunk); $i < 5; $i++)
                <td style="width: 20%;">
                </td>
                @endfor
        </tr>
        @endforeach
    </table>

</body>

</html>