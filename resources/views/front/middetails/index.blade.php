@extends('layouts.user.default')

@section('title')
MID Rate
@endsection

@section('breadcrumbTitle')
<a href="{{ route('dashboardPage') }}">Dashboard</a> / MID Rate
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12 col-xxl-12">
        <div class="iq-card height-auto">
            <div class="iq-card-header d-flex justify-content-between">
                <div class="iq-header-title">
                    <h4 class="card-title">MID Rate</h4>
                </div>
            </div>

            <div class="iq-card-body">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr class="table-active">
                                <td width="50%"><b>Visa -</b> Merchant Discount Rate (%)</td>
                                <td class="text-right"> {{ $data->merchant_discount_rate }}</td>
                            </tr>
                            <tr class="table-active">
                                <td><b>Master -</b> Merchant Discount Rate (%)</td>
                                <td class="text-right"> {{ $data->merchant_discount_rate_master_card }}</td>
                            </tr>
                            <tr class="table-active">
                                <td><b>Visa -</b> Setup Fee</td>
                                <td class="text-right"> {{ $data->setup_fee }}</td>
                            </tr>
                            <tr class="table-active">
                                <td><b>Master -</b> Setup Fee</td>
                                <td class="text-right"> {{ $data->setup_fee_master_card }}</td>
                            </tr>
                            <tr class="table-success">
                                <td>Transaction Fee</td>
                                <td class="text-right"> {{ $data->transaction_fee }} </td>
                            </tr>
                            <tr class="table-success">
                                <td>Chargeback Fee</td>
                                <td class="text-right"> {{ $data->chargeback_fee }} </td>
                            </tr>
                            <tr class="table-danger">
                                <td>Suspicious Transaction Fee</td>
                                <td class="text-right"> {{ $data->flagged_fee }} </td>
                            </tr>
                            <tr class="table-danger">
                                <td>Refund Fee</td>
                                <td class="text-right"> {{ $data->refund_fee }} </td>
                            </tr>
                            <tr class="table-danger">
                                <td>Rolling Reserve (%)</td>
                                <td class="text-right"> {{ $data->rolling_reserve_paercentage }} </td>
                            </tr>
                            <tr class="table-info">
                                <td>Payment Frequency</td>
                                <td class="text-right"> {{ config('custom.payment_frequency') }}</td>
                            </tr>
                            <tr class="table-info">
                                <td>Minimum Settlement Amount</td>
                                <td class="text-right"> {{ config('custom.minimum_settlement_amount') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>    
    </div>
</div>    
@endsection