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
            color: #6683A9;
            float: right;
        }

        .title h1 {
            margin: 25px 0px 0px 0px;
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
            color: #FFFFFF;
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
            color: #FFFFFF;
        }

        .header1 {
            padding: 0px 30px;
            margin-bottom: 15px;
        }

        .header2 {
            /* background: linear-gradient(to right, rgb(244, 67, 54), rgb(173 79 70)) !important; */
            background: #6683A9 !important;
            padding: 15px 30px;
            color: #FFFFFF;
        }

        #project div,
        #company div {
            margin-bottom: 5px;
            white-space: nowrap;
        }

        .body-table {
            /*margin: 0px 30px 30px 30px;*/
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
            color: #FFFFFF;
            white-space: nowrap;
            font-weight: 900;
            background: #6683A9 !important;
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
            color: #1B1919;
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
            background: #6683A9 !important;
            color: #FFFFFF;
        }

        .greenbg td {
            background: #6683A9 !important;
            color: #FFFFFF;
        }

        .redbg td {
            background: #6683A9 !important;
            color: #FFFFFF;
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
          <h1>{{ $data->processor_name }}</h1>
        </div>
        <div style="clear: both;"></div>
      </div>
      <div class="header2">
        <div id="company" class="clearfix">
          <div>To</div>
          <div><strong>{{ $data->company_name }}</strong></div>
          <div><strong>{{ $data->phone_no }}</strong></div>
        </div>
        <div id="project">
          <div style="float: right;">
            <div><strong>Settlement Date</strong> {{ $data->start_date }} to {{ $data->end_date }}</div>
            <div><strong>Settlement No.</strong> {{ $data->invoice_no }}</div>
            <div><strong>MID</strong> {{ $data->user_id }}</div>
          </div>
        </div>
        <div style="clear: both;"></div>
      </div>
    </header>
    <div class="body-table">
      <?php
      $totalPayoutReport = 0;
      ?>
      @foreach($childData as $key => $value)
      <?php
      $totalPayoutReport  += $value->net_settlement_amount_in_usd;
      ?>
      <table style="width: 250px; margin-top: 15px; margin-left: -2px;">
        <thead>
          <tr>
            <td
              style="font-weight: bold; background-color: #6683A9; color: #fff; padding: 10px 15px;box-shadow: rgb(0 0 0 / 70%) 10px 10px 15px -5px, rgb(0 0 0 / 60%) 5px 5px 5px -10px;">
              Currency : {{ $value->currency }} for {{($value->card_type == "Other")?'Visa': $value->card_type}}
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
          <?php
       // echo "<pre>";print_r($value);
        ?>
          <tr>
            <td>Successful transaction</td>
            <td>{{ number_format($value->approve_transaction_count, 0) }}</td>
            <td class="right">{{ round($value->approve_transaction_sum, 2) }}</td>
          </tr>

          <tr>
            <td>Declined transaction </td>
            <td>{{ number_format($value->declined_transaction_count, 0) }}</td>
            <td class="right">{{ round($value->declined_transaction_sum, 2) }}</td>
          </tr>

          <tr>
            <td>Total Transactions</td>
            <td>{{ number_format($value->total_transaction_count, 0) }}</td>
            <td class="right" style="font-weight: 900;">{{ round($value->total_transaction_sum, 2) }}</td>
          </tr>
          <tr>
            <td>Chargebacks</td>
            <td>{{ number_format($value->chargeback_transaction_count, 0) }}</td>
            <td class="right" style="color: #F94687; font-weight: 900;">
              {{ round($value->chargeback_transaction_sum, 2) }}</td>
          </tr>

          <?php
          $totalAmount = 0;
          $totalCount = 0;
          if($value->remove_past_chargebacks > 0){
              $totalCount += number_format($value->remove_past_chargebacks, 0);
              $totalAmount += $value->past_chargebacks_sum;
          }
          ?>
          <tr>
            <td>Refunds</td>
            <td>{{ number_format($value->refund_transaction_count, 0) }}</td>
            <td class="right" style="color: #F94687; font-weight: 900;">{{ round($value->refund_transaction_sum, 2) }}
            </td>
          </tr>
          <tr>
            <td>Suspicious</td>
            <td>{{ number_format($value->flagged_transaction_count, 0) }}</td>
            <td class="right" style="color: #F94687; font-weight: 900;">{{ round($value->flagged_transaction_sum, 2) }}
            </td>
          </tr>
          <?php
          if($value->remove_past_flagged > 0){
              $totalCount += number_format($value->remove_past_flagged, 0);
              $totalAmount += $value->past_flagged_sum;
          }
          ?>
          if($value->return_fee > 0){
            $totalCount += $value->return_fee_count;
            $totalAmount  += $value->return_fee;
          }
          ?>
          @if($totalCount > 0)
          <tr>
            <td>Reversed Transaction Value</td>
            <td>{{number_format($totalCount, 0)}}</td>
            <td class="right" style="color: green; font-weight: 900;">{{round($totalAmount,2)}} </td>
          </tr>
          @endif
          <tr>
            <td>Total Settlement</td>
            <td>{{ number_format($value->approve_transaction_count, 0) }}</td>
            <td class="right" style="font-weight: 900;">{{ round($value->sub_total, 2) }}</td>
          </tr>
          <tr>
            <td>Merchant Discount Rate </td>
            @if($value->card_type == "Other")
            <td>{{ $data->merchant_discount_rate }} %</td>
            <td class="right" style="color: #F94687; font-weight: 900;">
              {{ $value->mdr }}
            </td>
            @elseif($value->card_type == "MasterCard")
            <td>{{ $data->merchant_discount_rate_master }} %</td>
            <td class="right" style="color: #F94687; font-weight: 900;">
              {{ $value->mdr }}
            </td>
            @endif


          </tr>
          <tr>
            <td>Rolling Reserve (180 Days)</td>
            <td>{{ $data->rolling_reserve_paercentage }} %</td>
            <td class="right" style="color: #F94687; font-weight: 900;">
              {{ $value->rolling_reserve }}
            </td>
          </tr>
          <tr>
            <td><strong>Transaction Fees</strong></td>
            <td><strong>Avg Fee Rate *</strong></td>
            <td class="right"><strong>Fee Amount</strong></td>
          </tr>
          <tr>
            <td>Total Transaction Fee</td>
            <td>{{ $data->transaction_fee_paercentage }}</td>
            <td class="right">{{ $value->transaction_fee }}</td>
          </tr>
          {{-- <tr>
            <td>Declined</td>
            <td>{{ $data->declined_fee_paercentage }}</td>
            <td class="right">{{ $value->declined_transaction_fee }}</td>
          </tr> --}}
          <tr>
            <td>Chargeback Fee</td>
            <td>{{ $data->chargebacks_fee_paercentage }}</td>
            <td class="right">{{ $value->chargeback_fee }}</td>
          </tr>
          <tr>
            <td>Refund Fee</td>
            <td>{{ $data->refund_fee_paercentage }}</td>
            <td class="right">{{ $value->refund_fee }}</td>
          </tr>
          <tr>
            <td>Suspicious Transaction Fee</td>
            <td>{{ $data->flagged_fee_paercentage }}</td>
            <td class="right">{{ $value->flagged_fee }}</td>
          </tr>
          <tr>
            <td>Calculate Total fees based on USD</td>
            <td></td>
            <td class="right" style="color: #F94687; font-weight: 900;">{{ $value->transactions_fee_total }}</td>
          </tr>
          <tr>
            <td style="padding-bottom:15px;">Reversed fee</td>
            <td style="padding-bottom:15px;">0</td>
            <td class="right" style="color: green; font-weight: 900;padding-bottom:15px;">{{ $value->past_flagged_fee }}
            </td>
          </tr>
          <tr class="greenbg">
            <td colspan="2"><strong>TOTAL PAYOUT</strong></td>
            <td class="total" style="font-weight: 900;">{{ $value->net_settlement_amount }}</td>
          </tr>
          <tr class="greenbg">
            <td colspan="2"><strong>TOTAL PAYOUT IN USD</strong></td>
            <td class="total" style="font-weight: 900;">{{ $value->net_settlement_amount_in_usd}}</td>
          </tr>
        </tbody>
      </table>
      @endforeach
      <table class="table">
        <tbody>
          <tr class="greenbg">
            <td colspan="2"><strong>Total payout of all Currency in USD</strong></td>
            <td class="total" style="font-weight: 900;">{{ $totalPayoutReport }}</td>
          </tr>
        </tbody>
      </table>
      @if($data->id == 62 || $data->id == 63)
      <div style="margin-top: 10px; text-align: center; color:red;">
        <strong>Pre-Arbitration penalty $5000</strong>
      </div>
      @endif
    </div>
  </main>
</body>

</html>