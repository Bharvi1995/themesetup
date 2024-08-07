@extends('layouts.user.default')

@section('title')
    Details of Transaction
@endsection

@section('breadcrumbTitle')
    @if (\Auth::user()->is_white_label == '1')
        <a href="#">Dashboard</a> / <a href="{{ url('transactions') }}">Transactions</a> / Details
    @else
        <a href="{{ route('dashboardPage') }}">Dashboard</a> / <a href="{{ url('transactions') }}">Transactions</a> / Details
    @endif
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
    
    <div class="col-lg-6 col-xl-6">
        <div class="card">
            <div class="card-header">
                <div class="header-title">
                    <h4 class="card-title">Order Details</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- <div class="col-md-6"> -->
                        <div class="table-responsive custom-table">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Order Id</strong></td>
                                    <td>{{ $data->order_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Transaction Ref</strong></td>
                                    <td>{{ $data->customer_order_id }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 180px;"><strong>Amount</strong></td>
                                    <td> {{ $data->amount." ".$data->currency }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Card Number</strong></td>
                                    <td>{{ $data->card_no }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>
                                        @if ($data->status == '1')
                                            <label class="badge bg-gradient-success">Success</label>
                                        @elseif($data->status == '2')
                                            <label class="badge bg-gradient-warning">Pending</label>
                                        @elseif($data->status == '3')
                                            <label class="badge bg-gradient-yellow">Cancelled</label>
                                        @elseif($data->status == '4')
                                            <label class="badge bg-gradient-primary">To Be Confirm</label>
                                        @else
                                            <label class="badge bg-gradient-danger">Declined</label>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Reason</strong></td>
                                    <td>{{ $data->reason }}</td>
                                </tr>
                                 @if ($data->chargebacks == '1')
                                    <tr>
                                        <td><strong>Chargebacks</strong></td>
                                        <td><label class="badge badge-success">YES</label> ({{date("d-m-Y H:i:s",strtotime($data->chargebacks_date))}}) </td>
                                    </tr>
                                @endif
                                @if ($data->is_flagged == '1')
                                    <tr>
                                        <td><strong>Dispute</strong></td>
                                        <td><label class="badge badge-success">YES</label>({{date("d-m-Y H:i:s",strtotime($data->flagged_date))}})</td>
                                    </tr>
                                @endif
                               @if ($data->refund == '1')
                                    <tr>
                                        <td><strong>Refund</strong></td>
                                        <td><label class="badge badge-success">YES</label>({{date("d-m-Y H:i:s",strtotime($data->refund_date))}})</td>
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
                    <!-- </div>                         -->
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-xl-6">
        <div class="card">
            <div class="card-header">
                <div class="header-title">
                    <h4 class="card-title">Personal Details</h4>
                </div>
               <!--  <div>
                    <h4 class="card-title">Order No / {{ $data->order_id }}</h4>
                </div> -->
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless">
                            <tr>
                                <td style="width: 180px;"><strong>First Name</strong></td>
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
                            <!-- <tr>
                                <td><strong>IP Address</strong></td>
                                <td>{{ $data->ip_address }}</td>
                            </tr> -->
                            
                            
                        </table>
                    </div>                       
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
