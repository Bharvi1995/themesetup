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
        @page { margin: 0px; }
        body { margin: 0px; }
        
        a {
          color: #5D6975;
          text-decoration: underline;
        }

        body {
          position: relative;
          width: 100%;  
          background: #FFFFFF; 
          font-size: 14px; 
          font-family: Arial;
        }
        main{
          background: #ffffff;
         border-top: 15px solid #1EA7C5;
         padding: 30px 0px !important;
         position: relative;
         -webkit-box-shadow: 0 1px 21px #808080;
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
          color: #1EA7C5;
          float: right;
        }
        .title h1 {
            margin:10px 0px 0px 0px;
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

        .header1{
          padding: 0px 30px;
          margin-bottom: 30px;
        }
        .header2{
          background-color: #DCF4FA;
          padding: 15px 30px;
        }

        #project div,
        #company div {
            margin-bottom: 5px;
            white-space: nowrap;        
        }
        .body-table{
          margin:0px 30px 30px 30px;
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
          background-color: #1EA7C5;
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
          border-top: 1px solid #5D6975;;
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
        .bluebg td{
            background: #DCF4FA !important;
        }
        .greenbg td{
            background: #E3F9E9 !important;
        }
        .redbg td{
            background: #FFF3F7 !important;
        }
        .clear-header{
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
        @foreach($childData as $key => $value)
        <table style="width: 150px; margin-top: 15px;">
          <thead>
              <tr>
                <td style="font-weight: bold; background-color: #000; color: #fff; padding: 10px 15px;">
                    Currency : {{ $value->currency }}
                </td>
              </tr>
          </thead>
        </table>
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Count</th>
                    <th class="right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Approved</td>
                    <td>{{ number_format($value->approve_transaction_count, 0) }}</td>
                    <td class="right">{{ round($value->approve_transaction_sum, 2) }}</td>
                </tr>
                <tr>
                    <td>Declined</td>
                    <td>{{ number_format($value->declined_transaction_count, 0) }}</td>
                    <td class="right">{{ round($value->declined_transaction_sum, 2) }}</td>
                </tr>
                <tr class="bluebg">
                    <td>Total Attempts</td>
                    <td>{{ number_format($value->total_transaction_count, 0) }}</td>
                    <td class="right" style="font-weight: 900;">{{ round($value->total_transaction_sum, 2) }}</td>
                </tr>
                <tr>
                    <td>Chargebacks</td>
                    <td>{{ number_format($value->chargeback_transaction_count, 0) }}</td>
                    <td class="right" style="color: #F94687; font-weight: 900;">{{ round($value->chargeback_transaction_sum, 2) }}</td>
                </tr>
                @if($value->remove_past_chargebacks > 0)
                <tr>
                    <td>Remove Chargebacks</td>
                    <td>{{ number_format($value->remove_past_chargebacks, 0) }}</td>
                    <td class="right" style="color: green; font-weight: 900;">{{ round($value->past_chargebacks_sum, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td>Refunds</td>
                    <td>{{ number_format($value->refund_transaction_count, 0) }}</td>
                    <td class="right" style="color: #F94687; font-weight: 900;">{{ round($value->refund_transaction_sum, 2) }}</td>
                </tr>
                <tr>
                    <td>Flagged</td>
                    <td>{{ number_format($value->flagged_transaction_count, 0) }}</td>
                    <td class="right" style="color: #F94687; font-weight: 900;">{{ round($value->flagged_transaction_sum, 2) }}</td>
                </tr>
                @if($value->remove_past_flagged > 0)
                <tr>
                    <td>Remove Flagged</td>
                    <td>{{ number_format($value->remove_past_flagged, 0) }}</td>
                    <td class="right" style="color: green; font-weight: 900;">{{ round($value->past_flagged_sum, 2) }}</td>
                </tr>
                @endif
                <tr class="bluebg">
                    <td>Total Settlement</td>
                    <td>{{ number_format($value->approve_transaction_count, 0) }}</td>
                    <td class="right" style="font-weight: 900;">{{ round($value->sub_total, 2) }}</td>
                </tr>
                <tr class="redbg">
                    <td>Merchant Discount Rate</td>
                    <td>{{ $data->merchant_discount_rate }} %</td>
                    <td class="right" style="color: #F94687; font-weight: 900;">
                        {{ $value->mdr }}
                    </td>
                </tr>
                <tr class="redbg">
                    <td>Rolling Reserve (180 Days)</td>
                    <td>{{ $data->rolling_reserve_paercentage }} %</td>
                    <td class="right" style="color: #F94687; font-weight: 900;">
                        {{ $value->rolling_reserve }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Transaction Fees</strong></td>
                    <td><strong style="color: #1EA7C5">* Avg Fee Rate</strong></td>
                    <td class="right"><strong style="color: #1EA7C5">Fee Amount</strong></td>
                </tr>
                <tr>
                    <td>Approved</td>
                    <td>{{ $data->transaction_fee_paercentage }}</td>
                    <td class="right">{{ $value->transaction_fee }}</td>
                </tr>
                <tr>
                    <td>Declined</td>
                    <td>{{ $data->declined_fee_paercentage }}</td>
                    <td class="right">{{ $value->declined_transaction_fee }}</td>
                </tr>
                <tr>
                    <td>Chargebacks</td>
                    <td>{{ $data->chargebacks_fee_paercentage }}</td>
                    <td class="right">{{ $value->chargeback_fee }}</td>
                </tr>
                <tr>
                    <td>Refunds</td>
                    <td>{{ $data->refund_fee_paercentage }}</td>
                    <td class="right">{{ $value->refund_fee }}</td>
                </tr>
                <tr>
                    <td>Flagged</td>
                    <td>{{ $data->flagged_fee_paercentage }}</td>
                    <td class="right">{{ $value->flagged_fee }}</td>
                </tr>
                <tr class="redbg">
                    <td>Transaction Fee Total</td>
                    <td></td>
                    <td class="right" style="color: #F94687; font-weight: 900;">{{ $value->transactions_fee_total }}</td>
                </tr>
                <tr class="greenbg">
                    <td colspan="2"><strong>TOTAL PAYOUT</strong></td>
                    <td class="total" style="font-weight: 900; text-align: right;">{{ $value->net_settlement_amount }}</td>
                </tr>
            </tbody>
        </table>
        @endforeach
    </div> 
    </main>
</body>
</html>