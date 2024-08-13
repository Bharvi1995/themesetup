@extends('layouts.admin.default')

@section('title')
    Details of Transaction
@endsection

@section('breadcrumbTitle')
     <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.transactions') }}">All Transactions</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Details of Transactions</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Details of Transactions</h6>
    </nav>
@endsection

@section('customeStyle')
    <style type="text/css">
        #card-back {
            top: 40px;
            right: 0px;
        }

        #card-cvc {
            width: 60px;
            margin-bottom: 0;
        }

        #card-front {
            box-shadow: rgb(0 0 0 / 70%) 10px 10px 15px -5px, rgb(0 0 0 / 60%) 5px 5px 5px -10px;
            z-index: 2;
        }

        #card-back {
            z-index: 1;
            box-shadow: rgb(0 0 0 / 70%) 10px 10px 15px -5px, rgb(0 0 0 / 60%) 5px 5px 5px -10px;
        }

        #card-front,
        #card-back {
            position: absolute;
            background: linear-gradient(to right, rgb(102, 131, 169), rgb(128, 161, 194)) !important;
            width: 360px;
            height: 250px;
            border-radius: 6px;
            padding: 20px 20px 0;
            box-sizing: border-box;
            letter-spacing: 1 color: white;
            color: #202020;
        }

        #card-image {
            float: right;
            height: 100%;
        }

        #card-image i {
            font-size: 40px;
        }

        #card-month {
            width: 45% !important;
        }

        #card-number,
        #card-holder {
            width: 100%;
        }

        #card-stripe {
            width: 100%;
            height: 55px;
            background-color: #1B1919;
            position: absolute;
            right: 0;
        }

        #card-year {
            width: 45%;
            float: right;
        }

        #cardholder-container {
            width: 60%;
            display: inline-block;
        }

        #cvc-container {
            position: absolute;
            width: 110px;
            right: -115px;
            bottom: -10px;
            padding-left: 20px;
            box-sizing: border-box;
        }

        #cvc-container label {
            width: 100%;
        }

        #cvc-container p {
            font-size: 6px;
            text-transform: uppercase;
            opacity: 0.6;
            letter-spacing: .5px;
        }

        #form-container {
            margin: auto;
            width: 100%;
            height: 290px;
            position: relative;
        }

        #exp-container {
            margin-left: 10px;
            width: 32%;
            display: inline-block;
            float: right;
        }

        #image-container {
            width: 100%;
            position: relative;
            height: 55px;
            margin-bottom: 5px;
            line-height: 55px;
        }

        #image-container img {
            position: absolute;
            right: 0;
            top: 0;
        }

        input {
            border: none;
            outline: none;
            background-color: #1B1919;
            height: 30px;
            line-height: 30px;
            padding: 0 10px;
            margin: 0 0 25px;
            color: white;
            box-sizing: border-box;
            border-radius: 4px;
            letter-spacing: .7px;
        }

        input::-webkit-input-placeholder {
            color: #fff;
            opacity: 0.7;
            letter-spacing: 1px;
        }

        input:-moz-placeholder {
            color: #fff;
            opacity: 0.7;
            letter-spacing: 1px;
        }

        input::-moz-placeholder {
            color: #fff;
            opacity: 0.7;
            letter-spacing: 1px;
        }

        input:-ms-input-placeholder {
            color: #fff;
            opacity: 0.7;
            letter-spacing: 1px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Details of Transaction</h4>
                    </div>
                    <div>
                        <h4 class="card-title">Order No / {{ $data->order_id }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="table-responsive custom-table">
                                <table class="table table-borderless">
                                    <tr>
                                        <td style="width: 200px;"><strong>First Name</strong></td>
                                        <td>{{ $data->first_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Name</strong></td>
                                        <td>{{ $data->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Address</strong></td>
                                        <td>{{ $data->address }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Country</strong></td>
                                        <td>{{ $data->country }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>State</strong></td>
                                        <td>{{ $data->state }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>City</strong></td>
                                        <td>{{ $data->city }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Zip Code</strong></td>
                                        <td>{{ $data->zip }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>IP Address</strong></td>
                                        <td>{{ $data->ip_address }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email</strong></td>
                                        <td>{{ $data->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone No.</strong></td>
                                        <td>{{ $data->phone_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td>
                                            @if ($data->status == '1')
                                                <label class="light badge badge-success">Success</label>
                                            @elseif($data->status == '2')
                                                <label class="light badge badge-warning">Pending</label>
                                            @elseif($data->status == '3')
                                                <label class="light badge badge-yellow">Cancelled</label>
                                            @elseif($data->status == '4')
                                                <label class="light badge badge-primary">To Be Confirm</label>
                                            @else
                                                <label class="light badge badge-danger">Declined</label>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Reason</strong></td>
                                        <td>{{ $data->reason }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Transaction Date</strong></td>
                                        <td>{{ convertDateToLocal($data->created_at, 'd-m-Y / H:i:s') }}</td>
                                    </tr>
                                    @if ($data->chargebacks == '1')
                                        <tr>
                                            <td><strong>Chargebacks</strong></td>
                                            <td><label class="badge badge-success">YES</label></td>
                                        </tr>
                                    @endif
                                    @if ($data->refund == '1')
                                        <tr>
                                            <td><strong>Refund</strong></td>
                                            <td><label class="badge badge-success">YES</label></td>
                                        </tr>
                                        @if ($data->refund_reason != '')
                                            <tr>
                                                <td><strong>Refund Reason</strong></td>
                                                <td>{{ $data->refund_reason }}</td>
                                            </tr>
                                        @endif
                                    @endif
                                </table>
                            </div>
                        </div>

                        <div class="col-md-4 offset-md-1 mt-5">
                            <div id="form-container" class="mt-3">
                                <div id="card-front">
                                    <div id="image-container">
                                        <span id="amount">Amount: <strong>{{ $data->currency }}
                                                {{ $data->amount }}</strong></span>
                                        <span id="card-image">
                                            @if ($data->card_type == 1)
                                                <i class="fa fa-cc-amex"></i>
                                            @elseif($data->card_type == 2)
                                                <i class="fa fa-cc-visa"></i>
                                            @elseif($data->card_type == 3)
                                                <i class="fa fa-cc-mastercard"></i>
                                            @else
                                                <i class="fa fa-cc-discover"></i>
                                            @endif
                                        </span>
                                    </div>

                                    <label for="card-number">
                                        Card Number
                                    </label>
                                    @if ($data->card_no != null)
                                        @if (strlen($data->card_no) > 4)
                                            <input type="text" id="card-number" placeholder="{!! substr($data->card_no, 0, 6) . 'XXXXXX' . substr($data->card_no, -4) !!}"
                                                disabled="disabled">
                                        @else
                                            <input type="text" id="card-number" placeholder="{!! $data->card_no !!}"
                                                disabled="disabled">
                                        @endif
                                    @endif
                                    <div id="cardholder-container">
                                        <label for="card-holder">Card Holder</label>
                                        <input type="text" id="card-holder"
                                            placeholder="{{ $data->first_name }} {{ $data->last_name }}"
                                            disabled="disabled" />
                                    </div>
                                    <div id="exp-container">
                                        <label for="card-exp">Expiration </label>
                                        @if ($data->card_no != null)
                                            <input id="card-month" type="text" placeholder="{{ $data->ccExpiryMonth }}"
                                                disabled="disabled">
                                            <input id="card-year" type="text"
                                                placeholder="{{ substr($data->ccExpiryYear, -2) }}" disabled="disabled">
                                        @endif
                                    </div>
                                    <div id="cvc-container">
                                        <label for="card-cvc"> CVC/CVV</label>
                                        <input id="card-cvc" placeholder="XXX" type="text" disabled="disabled">
                                        <p>Last 3 or 4 digits</p>
                                    </div>
                                </div>
                                <div id="card-back">
                                    <div id="card-stripe">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($data->webhook_url != null && $data->webhook_url != '')
                    <div class="row mt-3">
                        <div class="col-md-5 p30">
                            <h4 class="pull-left mt-25 mb-0">Webhook Details</h4>
                            <a href="{{ route('send-transaction-webhook', $data->id) }}"
                                class="btn btn-primary btn-sm pull-right">Sendwebhook</a>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive custom-table">
                                <table class="table table-borderless table-striped">
                                    <tr>
                                        <td style="width: 200px;">Response URL</td>
                                        <td>{{ $data->response_url }}</td>
                                    </tr>
                                    <tr>
                                        <td>Webhook URL</td>
                                        <td>{{ $data->webhook_url }}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>{{ $data->webhook_status }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
