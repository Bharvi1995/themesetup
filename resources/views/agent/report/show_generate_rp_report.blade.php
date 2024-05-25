<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title></title>
  <style type="text/css" media="screen">
    .clearfix:after {
      content: "";
      display: table;
      clear: both;
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
      border-top: 15px solid #1EA7C5;
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
      padding-top: 15px;
    }

    .title {
      float: right;
    }

    .title h1 {
      color: #1EA7C5;
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
      margin-bottom: 30px;
    }

    .header2 {
      /*background-color: #DCF4FA;*/
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
      color: #1EA7C5;
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
    <div class="body-table">
      @foreach($data->childData as $key => $value)
      <table class="table">
        <thead>
          <tr>
            <td colspan="2" style="font-weight: bold; background-color: #000; color: #fff; padding: 10px 15px;">
              Currency : {{ $value->currency }}
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
    <!-- <div id="notices">
      <p style="text-align: center;color: #F94687;">*Your payment will be processed in next 48 hours and will reflect in
        your account within 4-5 business days.</p>
    </div>
    <footer>
      Invoice was created on a computer and is valid without the signature and seal.
    </footer> -->
  </main>
</body>

</html>