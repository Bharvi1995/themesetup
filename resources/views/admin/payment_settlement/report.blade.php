@extends('layouts.admin.default')
@section('title')
    All Transactions
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Merchant Transaction Report
@endsection
@section('content')
    @include('requestDate')

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Payout Settlement Report</h4>
                    </div>
                </div>
                <div class="card-body p-0">

                    <div class="row">
                        <div class="col-md-12">
                            <h3>Merchant Details</h3>
                        </div>
                    </div>

                    <div class="row">
                        @if (isset(request()->user_id))
                            <div class="col-md-12">
                                <h3>Invoice</h3>
                            </div>
                            <div class="col-xl-4 col-sm-4 mt-3">
                                <div class="card">
                                    <div class="card-header flex-wrap border-0 pb-0">
                                        <div class="mr-3 mb-2">
                                            <p class="fs-14 mb-1">Total Payable</p>
                                            <span class="fs-24 text-black font-w600">$
                                                {{ number_format($data['payable_amount'], 2, '.', '') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-4 mt-3">
                                <div class="card">
                                    <div class="card-header flex-wrap border-0 pb-0">
                                        <div class="mr-3 mb-2">
                                            <p class="fs-14 mb-1">Gross Payable</p>
                                            <span
                                                class="fs-24 text-black font-w600">${{ number_format($data['gross_payable'], 2, '.', '') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-4 mt-3">
                                <div class="card">
                                    <div class="card-header flex-wrap border-0 pb-0">
                                        <div class="mr-3 mb-2">
                                            <p class="fs-14 mb-1">Net payable</p>
                                            <span
                                                class="fs-24 text-black font-w600">${{ number_format($data['net_payable'], 2, '.', '') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12"></div>
                            <br />

                            <div class="col-md-12">
                                <h3>Transaction Details</h3>
                            </div>

                            <div class="col-md-12">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Transaction Detail</th>
                                        <th>Count</th>
                                        <th>Amount</th>
                                    </tr>
                                    <tr>
                                        <td>Success Transactions</td>
                                        <td>{{ $data['totalSuccessCount'] }}</td>
                                        <td>${{ $data['totalSuccessAmount'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Declined Transactions</td>
                                        <td>{{ $data['totalDeclinedCount'] }}</td>
                                        <td>${{ $data['totalDeclinedAmount'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Chargeback Transactions</td>
                                        <td>{{ $data['chb_totalCount'] }}</td>
                                        <td>${{ $data['chb_totalAmount'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Suspicious Transactions</td>
                                        <td>{{ $data['sus_totalCount'] }}</td>
                                        <td>${{ $data['sus_totalAmount'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Refund Transactions</td>
                                        <td>{{ $data['refund_totalCount'] }}</td>
                                        <td>${{ $data['refund_totalAmount'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Retrival Transactions</td>
                                        <td>{{ $data['ret_totalCount'] }}</td>
                                        <td>${{ $data['ret_totalAmount'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pre-Arbitration Transactions</td>
                                        <td>{{ $data['preat_totalCount'] }}</td>
                                        <td>${{ $data['preat_totalAmount'] }}</td>
                                    </tr>
                                </table>
                            </div>


                            <div class="col-md-12"></div>
                            <br />

                            <div class="col-md-12">
                                <h3>Transaction Fees</h3>
                            </div>

                            <div class="col-md-12">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Transaction Fess Detail</th>
                                        <th>Amount</th>
                                    </tr>
                                    <tr>
                                        <td>Total Transactions</td>
                                        <td>{{ $data['total_transactions'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>MDR</td>
                                        <td>${{ $data['mdr_amount'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Reserve Amount</td>
                                        <td>${{ $data['reserve_amount'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Transactions Fees</td>
                                        <td>${{ $data['transactionsfees'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Refund Fees</td>
                                        <td>${{ $data['refund_fees'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>High Risk Fees</td>
                                        <td>${{ $data['highrisk_fees'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Charge Back Fees</td>
                                        <td>${{ $data['chb_fees'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Retreival Fees</td>
                                        <td>${{ $data['retreival_fees'] }}</td>
                                    </tr>
                                </table>
                            </div>
                        @endif


                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
    <script>
        $(document).on("change", "#user_id", function() {
            var user_id = $("#user_id").find(":selected").val();
            if (user_id != 0) {
                location.href = "{{ route('merchant.settlement_report') }}?user_id=" + user_id;
            } else {
                location.href = "{{ route('merchant.settlement_report') }}";
            }
        });
    </script>
@endsection
