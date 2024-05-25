<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Agent Report</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <style type="text/css" media="screen">
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        @page {
            margin: 0px;
        }

        a {
            color: #B3ADAD;
            text-decoration: underline;
        }

        body {
            position: relative;
            width: 100%;
            margin: 0 auto;
            background: #f8f8f8;
            font-size: 14px;
            font-family: Arial;
            color: #B3ADAD;
        }

        main {
            width: 21cm;
            background: #FFFFFF;
            margin: 30px auto;
            padding: 30px 0px !important;
            position: relative;
            border-radius: 5px;
            color: #5a5a5a;
            box-shadow: 0px 2px 5px 0px #05309533;
        }

        #logo {
            margin-bottom: 0px;
            float: left;
            margin-top: 10px;
        }

        #logo img {
            width: 200px;
        }

        .title {
            padding-top: 10px;
            float: right;
            color: #6683A9;
        }

        .title h1 {
            color: #6683A9;
            margin: 0px;
        }

        #project {
            float: left;
            width: 50%;
            font-size: 14px;
            color: #000;
        }

        #project span {
            color: #000;
            text-align: right;
            width: 52px;
            margin-right: 10px;
            display: inline-block;
            font-size: 0.8em;
        }

        .header1 {
            padding: 0px 30px;
        }

        .header2 {
            padding: 15px 30px;
        }

        #project div,
        #company div {
            margin-bottom: 5px;
            white-space: nowrap;
        }


        .table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-top: 15px;
        }

        .table th,
        .table td {
            text-align: left;
        }

        .table th {
            color: #B3ADAD;
            white-space: nowrap;
            font-weight: 900;
        }

        .table .service,
        .table .desc {
            text-align: left;
        }

        .table td {
            padding: 5px 20px;
        }

        .table td.service,
        .table td.desc {
            vertical-align: top;
        }

        .table td.unit,
        .table td.qty,
        .table td.total {
            font-size: 1.2em;
        }

        .table td.grand {
            border-top: 1px solid #5D6975;
            ;
        }

        #notices .notice {
            color: #5D6975;
            font-size: 1.2em;
        }

        footer {
            color: #5D6975;
            width: 100%;
            bottom: 0;
            text-align: center;
        }

        .right {
            text-align: right !important;
        }

        .bluebg td {
            background: #DCF4FA !important;
        }

        .greenbg td {
            background: #E3F9E9 !important;
        }

        .redbg td {
            background: #FFF3F7 !important;
        }

        .clear-header {
            clear: both;
        }
    </style>
</head>

<body>
    <main>
        <header class="clearfix">
            <div class="header1">
                <div id="logo">
                    <img src="{{ config('app.logo_url') }}">
                </div>
                <div class="title">
                    <h1>{{ $data->company_name }}</h1>
                    <div><strong>Settlement No.</strong> {{ $data->report_no }}</div>
                    <div><strong>Settlement Date</strong> {{ $data->start_date->format('d-m-Y') }} to
                        {{ $data->end_date->format('d-m-Y') }}
                    </div>
                </div>
                <div style="clear: both;"></div>
            </div>
        </header>
        @php
            $totalCommissionInUsd = 0.0;
        @endphp
        <div class="body-table">
            @foreach ($data->childData as $key => $value)
                @php
                    $totalCommissionInUsd += $value->total_commission_in_usd;
                @endphp
                <table class="table">
                    <thead>
                        <tr>
                            <td colspan="2"
                                style="font-weight: bold; background-color: #6683A9; color: #FFFFFF; padding: 10px 15px;">
                                Currency : {{ $value->currency }} for
                                {{ $value->card_type == 'Other' ? 'Visa' : $value->card_type }}
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong> Success Amount</strong></td>
                            <td class="right">{{ round($value->success_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong> Success Count</strong></td>
                            <td class="right">{{ round($value->success_count, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong> Commission Percentage</strong></td>
                            <td class="right">{{ round($value->commission_percentage, 2) }}%</td>
                        </tr>
                        <tr>
                            <td><strong> Total Commission</strong></td>
                            <td class="right">{{ round($value->total_commission, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        </div>

        <table class="table">
            <tbody>
                <tr style="font-weight: bold; background-color: #6683A9; color: #fff; padding: 10px 15px;">
                    <td colspan="2"><strong>Total Commission in USD</strong></td>
                    <td class="right" style="font-weight: 900;">{{ $totalCommissionInUsd }} USD</td>
                </tr>
            </tbody>
        </table>
    </main>
</body>

</html>
