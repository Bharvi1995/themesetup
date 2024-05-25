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
        <div class="iq-card border-card">
            <div class="iq-card-header bg-info d-flex justify-content-between">
                <div class="iq-header-title">
                    <h4 class="card-title">Transactions</h4>
                </div>
            </div>
            <div class="iq-card-body">

                <div>
                    <ul>
                        <li>
                            @if( isset($user->threshold_amount) && $getSettlementRepost->first()->net_payable > $user->threshold_amount )
                            <span class="text-success">Your Threshold reached.</span>
                            @else
                            <span class="text-danger">Your Threshold doesn't matched Yet.</span>
                            @endif
                        </li>
                        <li>
                            @if( $getSettlementRepost->first()->net_payable > 0 )
                            <span class="text-success">Your 14 days condition are matched.</span>
                            @else
                            <span class="text-danger">Your 14 days condition doesn't match yet.</span>
                            @endif
                        </li>
                    </ul>
                </div>

                <div class="table-responsive">
                    <table class="table mb-0 table-borderless">
                        <thead>
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Success Amount</th>
                                <th>Success Count</th>
                                <th>Declined Amount</th>
                                <th>Declined Count</th>
                                <th>Chargeback Amount</th>
                                <th>Chargeback Count</th>
                                <th>Suspicious Amount</th>
                                <th>Suspicious Count</th>
                                <th>Refund Amount</th>
                                <th>Refund Count</th>
                                <th>Retreival Amount</th>
                                <th>Retreival Count</th>
                                <th>Pre-Arbitration Amount</th>
                                <th>Pre-Arbitration Count</th>
                                <th>Total Transaction</th>
                                <th>MDR</th>
                                <th>Transaction Fees</th>
                                <th>Refund Fees</th>
                                <th>High Risk Fees</th>
                                <th>Chargeback Fees</th>
                                <th>Retreival Fees</th>
                                <th>Reserve Fees</th>
                                <th>Total Payable Amount</th>
                                <th>Gross Payable Amount</th>
                                <th>Net Payable Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($getSettlementRepost as $data)
                            <tr id="tr_{{ $data->id }}">
                                <td>{{ $data->start_date }}</td>
                                <td>{{ $data->end_date }}</td>
                                <td>{{ $data->totalSuccessAmount }}</td>
                                <td>{{ $data->totalSuccessCount }}</td>
                                <td>{{ $data->totalDeclinedAmount }}</td>
                                <td>{{ $data->totalDeclinedCount }}</td>
                                <td>{{ $data->chbtotalAmount }}</td>
                                <td>{{ $data->chbtotalCount }}</td>
                                <td>{{ $data->suspicioustotalAmount }}</td>
                                <td>{{ $data->suspicioustotalCount }}</td>
                                <td>{{ $data->refundtotalAmount }}</td>
                                <td>{{ $data->refundtotalCount }}</td>
                                <td>{{ $data->retreivaltotalAmount }}</td>
                                <td>{{ $data->retreivaltotalCount }}</td>
                                <td>{{ $data->prearbitrationtotalAmount }}</td>
                                <td>{{ $data->prearbitrationtotalCount }}</td>
                                <td>{{ $data->total_transactions }}</td>
                                <td>{{ $data->mdr_amount }}</td>
                                <td>{{ $data->transactionsfees }}</td>
                                <td>{{ $data->refund_fees }}</td>
                                <td>{{ $data->highrisk_fees }}</td>
                                <td>{{ $data->chb_fees }}</td>
                                <td>{{ $data->retreival_fees }}</td>
                                <td>{{ $data->reserve_amount }}</td>
                                <td>{{ number_format((float)$data->total_payable, 2, '.', '') }}</td>
                                <td>
                                    @if( isset($user->threshold_amount) && $data->net_payable > $user->threshold_amount )
                                        <span class="text-success">{{ number_format((float)$data->gross_payable, 2, '.', '') }}</span>
                                    @else
                                    <span class="text-danger">{{ number_format((float)$data->gross_payable, 2, '.', '') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if( isset($user->threshold_amount) && $data->net_payable > $user->threshold_amount )
                                        <span class="text-success">{{ number_format((float)$data->net_payable, 2, '.', '') }}</span>
                                    @else
                                    <span class="text-danger">{{ number_format((float)$data->net_payable, 2, '.', '') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-5">
                    @if( request()->get('user_id') )
                    {{ $getSettlementRepost->appends(request()->input())->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('customScript')
<script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
@endsection