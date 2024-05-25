<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Favicon icon -->
  <link rel="icon" type="image/png" sizes="16x16" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
  <title>{{ config('app.name') }} | Show Report</title>

  <style type="text/css" media="screen">
    .clearfix:after {
      content: "";
      display: table;
      clear: both;
    }

    @page {
      margin: 0px;
    }

    body {
      margin: 0px;
    }

    a {
      color: #5D6975;
      text-decoration: underline;
    }

    body {
      position: relative;
      width: 21cm;
      margin: 0 auto;
      background: #FFFFFF;
      font-size: 14px;
      font-family: Arial;
    }

    main {
      background: #ffffff;
      border-top: 15px solid #34383e;
      margin-top: 30px;
      margin-bottom: 30px;
      padding: 30px 0px !important;
      position: relative;
      box-shadow: 0 1px 21px #808080;
    }

    #logo {
      margin-bottom: 0px;
      float: left;
    }

    #logo img {
      width: 200px;
    }

    .title {
      color: #34383e;
      float: right;
    }

    .title h1 {
      margin: 10px 0px 0px 0px;
    }

    #from {
      float: left;
      width: 33.33%;
      font-size: 14px;
      color: #000;
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

    #company {
      float: left;
      text-align: left;
      text-align: left;
      width: 50%;
      font-size: 14px;
      color: #000;
    }

    .header1 {
      padding: 0px 30px;
      margin-bottom: 30px;
    }

    .header2 {
      background-color: #ffebe5;
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
    }

    .table th {
      padding: 10px 20px;
      color: #FFF;
      white-space: nowrap;
      font-weight: 900;
      background-color: #34383e;
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
          <h1>Report</h1>
        </div>
        <div style="clear: both;"></div>
      </div>
      <div class="header2">
        <div id="company" class="clearfix">
        </div>
        <div id="project">
          <div style="float: right;">
            <div><strong>Date</strong> 2021-07-26 to 2021-11-30</div>
          </div>
        </div>
        <div style="clear: both;"></div>
      </div>
    </header>
    <div class="body-table">
      @foreach($transactions as $currency => $transaction)
      <table style="width: 250px; margin-top: 15px;">
        <thead>
          <tr>
            <td style="font-weight: bold; background-color: #5c746b; color: #fff; padding: 10px 15px;">
              Currency : {{ $currency }}
            </td>
          </tr>
        </thead>
      </table>
      <table class="table">
        <thead>
          <tr>
            <th>Summary</th>
            <th>Tally</th>
            <th class="right">Capital</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Successful transaction</td>
            <td>{{ number_format($transaction->successfullC, 0) }}</td>
            <td class="right">{{ round($transaction->successfullV, 2) }}</td>
          </tr>

          <tr>
            <td>Declined transaction </td>
            <td>{{ number_format($transaction->declinedC, 0) }}</td>
            <td class="right">{{ round($transaction->declinedV, 2) }}</td>
          </tr>
          <tr>
            <td>Chargebacks</td>
            <td>{{ number_format($transaction->chargebackC, 0) }}</td>
            <td class="right" style="color: #F94687; font-weight: 900;">
              {{ round($transaction->chargebackV, 2) }}</td>
          </tr>
          <tr>
            <td>Refunds</td>
            <td>{{ number_format($transaction->refundC, 0) }}</td>
            <td class="right" style="color: #F94687; font-weight: 900;">{{ round($transaction->refundV, 2) }}
            </td>
          </tr>
        </tbody>
      </table>
      @endforeach
    </div>
  </main>
</body>

</html>