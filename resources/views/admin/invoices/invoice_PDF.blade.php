<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <title>{{ config('app.name') }} | View Report</title>

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
            color: #5D6975;
            text-decoration: underline;
        }

        body {
            position: relative;
            width: 100%;
            margin: 0 auto;
            background: #202020;
            font-size: 14px;
            font-family: Arial;
            color: #B3ADAD;
        }

        main {
            background: #3D3D3D;
            border-top: 15px solid #1B1919;
            margin-top: 30px;
            margin-bottom: 30px;
            padding: 30px 0px !important;
            position: relative;

        }

        #logo {
            margin-bottom: 0px;
            float: left;
        }

        #logo img {
            width: 200px;
        }

        .title {
            color: #B3ADAD;
            float: right;
        }

        .title h1 {
            margin: 10px 0px 0px 0px;
        }

        #from {
            float: left;
            width: 33.33%;
            font-size: 14px;
            color: #B3ADAD;
        }

        #project {
            float: left;
            width: 50%;
            font-size: 14px;
            color: #B3ADAD;
        }

        #project span {
            color: #B3ADAD;
            text-align: right;
            width: 52px;
            margin-right: 10px;
            display: inline-block;
            font-size: 0.8em;
        }

        #company {
            float: left;
            text-align: left;
            text-align: left;
            width: 50%;
            font-size: 14px;
            color: #B3ADAD;
        }

        .header1 {
            padding: 0px 30px;
            margin-bottom: 30px;
        }

        .header2 {
            background-color: #1B1919;
            padding: 15px 30px;
        }

        #project div,
        #company div {
            margin-bottom: 5px;
            white-space: nowrap;
        }

        .body-table {
            margin: 0px 30px 30px 30px;
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
            color: #B3ADAD;
        }

        .table th {
            padding: 10px 20px;
            white-space: nowrap;
            font-weight: 900;
            background-color: #1B1919;
        }

        .table .service,
        .table .desc {
            text-align: left;
        }

        .table td {
            padding: 20px 20px;
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
            color: #B3ADAD;
            width: 100%;
            bottom: 0;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
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

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;

            /** Extra personal styles **/
            background-color: #1B1919;
            color: #B3ADAD;
            text-align: center;
            line-height: 0.8cm;
        }
    </style>
</head>

<body>
    <main>
        <header class="clearfix">
            <div class="header1">
                <div id="logo">
                    <img src="https://gateway.testpay.com/storage/NewTheme/images/Logo.png">
                </div>
                <div class="title">
                    <h1>{{ config('app.name') }}</h1>
                    <br><span>{{ date('F d, Y') }}</span>
                </div>
                <div style="clear: both;"></div>
            </div>
            <div class="header2">
                <div id="company" class="clearfix">
                    <div><strong>{{ $input['business_name'] }}</strong></div>
                    <div><strong>Email: </strong>{{ $input['email'] }}</div>
                    <div><strong>Company address: </strong>{{ $input['company_address'] }}</div>
                    <div><strong>Phone no: </strong>{{ $input['phone_no'] }}</div>
                </div>
                <div id="project">
                    <div style="float: right;">
                        <div><strong>SALES INVOICE</strong></div>
                        <div># {{ $input['invoice_no'] }}</div>
                    </div>
                </div>
                <div style="clear: both;"></div>
            </div>
        </header>
        <div class="body-table">
            <?php
            $totalAmount = 0;
            ?>


            <table class="table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($input['description'] as $key => $value)
                        @php
                            $totalAmount += $value['amount'];
                        @endphp
                        <tr>
                            <td><strong>{{ $value['description'] }}</strong></td>
                            <td class="right">$ {{ number_format($value['amount'], 2, '.', ',') }}</td>
                        </tr>
                    @endforeach
                    @if (count($input['description']) > 0)
                        <tr>
                            <td><strong>Total Amount:</strong></td>
                            <td class="right">$ {{ number_format($totalAmount, 2, '.', ',') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <table class="table">
                <tbody>
                    <tr>
                        <td colspan="2"><strong>USDT erc20:</strong></td>
                        <td class="total">{{ $input['usdt_erc'] }}</td>
                    </tr>

                    <tr>
                        <td colspan="2"><strong>USDT trc20:</strong></td>
                        <td class="total">{{ $input['usdt_trc'] }}</td>
                    </tr>

                    <tr>
                        <td colspan="2"><strong>BTC:</strong></td>
                        <td class="total">{{ $input['btc'] }}</td>
                    </tr>

                    <tr>
                        <td colspan="2" style="font-weight: 900;"><strong>Important Note:</strong></td>
                        <td class="total" style="font-size: 15px;">
                            <ul>
                                <li>The acceptance of this invoice constitutes and agreement between Next Ring LLC and
                                    the invoice bearer.</li>
                                <li>Transfers via BTC/USDT will be VAT free. Conversions will be based on real time.
                                </li>
                                <li>Amount $ {{ $input['amount_deducted_value'] }} will be deducted from the first
                                    settlement of the merchant this invoice is being raised in the name of; it will be
                                    applicable as per the agreement.</li>
                                <li>Employees/Merchants are not authorised to change the terms of this agreement.</li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>
