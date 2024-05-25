@extends($WLAgentUserTheme)
@section('title')
    All Transactions
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('wl-dashboard') }}">Dashboard</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4 mb-2">
            <div class="merchantTxnCard">
                <h2>{{ round($transaction->successfullP, 2) }} %</h2>
                <p class="mb-1" style="color: #82CD47;">Successful</p>
                <p class="total">Total Count : <span style="color: #FFFFFF;"> {{ $transaction->successfullC }}</span>
                </p>
            </div>
        </div>
        <div class="col-lg-4 mb-2">
            <div class="merchantTxnCard">
                <h2>{{ round($transaction->declinedP, 2) }} %</h2>
                <p class="mb-1" style="color: #5F9DF7;">Declined</p>
                <p class="total">Total Count : <span style="color: #FFFFFF;"> {{ $transaction->declinedC }}</span>
                </p>
            </div>
        </div>

        <div class="col-lg-4 mb-2">
            <div class="merchantTxnCard">
                <h2>{{ round($transaction->chargebackP, 2) }} %</h2>
                <p class="mb-1" style="color: #C47AFF;">Chargeback</p>
                <p class="total">Total Count : <span style="color: #FFFFFF;">
                        {{ $transaction->chargebackC }}</span></p>
            </div>
        </div>
        <div class="col-lg-4 mb-2">
            <div class="merchantTxnCard">
                <h2>{{ round($transaction->suspiciousP, 2) }} %</h2>
                <p class="mb-1" style="color: #C47AFF;">Marked</p>
                <p class="total">Total Count : <span style="color: #FFFFFF;">
                        {{ $transaction->suspiciousC }}</span></p>
            </div>
        </div>
        <div class="col-lg-4 mb-2">
            <div class="merchantTxnCard">
                <h2>{{ round($transaction->refundP, 2) }} %</h2>
                <p class="mb-1" style="color: #BF4146;">Refund</p>
                <p class="total">Total Count : <span style="color: #FFFFFF;"> {{ $transaction->refundC }}</span>
                </p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Merchants</h4>
                    <div>
                        <a href="{!! url('wl/rp/merchant-management') !!}" class="btn btn-primary btn-sm">View All <i
                                class="fa fa-chevron-right"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Business Name</th>
                                    <th>Phone Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latestMerchants as $key => $value)
                                    <tr>
                                        <td>{{ $value->name }}</td>
                                        <td>{{ $value->email }}</td>
                                        <td>{{ $value->business_name }}</td>
                                        <td>{{ $value->mobile_no }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Transactions Summary Report</h4>
                    <div>
                        <a href="{{ route('wl-transaction-summary-reports') }}" class="btn btn-primary btn-sm">View All
                            <i class="fa fa-chevron-right"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th colspan="2" class="text-center text-success">Successful</th>
                                    <th colspan="2" class="text-center text-danger">Declined</th>
                                    <th colspan="2" class="text-center text-secondary">Chargeback</th>
                                    <th colspan="2" class="text-center text-info">Refund</th>
                                    <th colspan="2" class="text-center text-warning">Suspicious</th>
                                </tr>
                                <tr>
                                    <th class="text-dark">Currency</th>
                                    <th class="text-success">Amount</th>
                                    <th class="text-success">Count</th>
                                    <th class="text-danger">Amount</th>
                                    <th class="text-danger">Count</th>
                                    <th class="text-secondary">Amount</th>
                                    <th class="text-secondary">Count</th>
                                    <th class="text-info">Amount</th>
                                    <th class="text-info">Count</th>
                                    <th class="text-warning">Amount</th>
                                    <th class="text-warning">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($TransactionSummary as $k => $v)
                                    <tr>
                                        <td>{{ $v->currency }}</td>
                                        <td>{{ $v->successAmount }}</td>
                                        <td>{{ $v->successCount }}</td>
                                        <td>{{ $v->declinedAmount }}</td>
                                        <td>{{ $v->declinedCount }}</td>
                                        <td>{{ $v->chargebackAmount }}</td>
                                        <td>{{ $v->chargebackCount }}</td>
                                        <td>{{ $v->refundAmount }}</td>
                                        <td>{{ $v->refundCount }}</td>
                                        <td>{{ $v->flagAmount }}</td>
                                        <td>{{ $v->flagCount }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Transactions</h4>
                    <div>
                        <a href="{!! url('wl/rp/merchant-transaction') !!}" class="btn btn-primary btn-sm">View All <i
                                class="fa fa-chevron-right"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>Order No.</th>
                                    <th>Business Name</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latest10Transactions as $key => $value)
                                    <tr>
                                        <td>{{ $value->order_id }}</td>
                                        <td>{{ $value->business_name }}</td>
                                        <td>{{ $value->amount }}</td>
                                        <td>{{ $value->currency }}</td>
                                        <td>{{ convertDateToLocal($value->created_at, 'd-m-Y') }}</td>
                                        <td>
                                            @if ($value->status == '1')
                                                <label class="light badge-sm badge badge-success">Success</label>
                                            @elseif($value->status == '2')
                                                <label class="light badge-sm badge badge-warning">Pending</label>
                                            @elseif($value->status == '3')
                                                <label class="light badge-sm badge badge-primary">Canceled</label>
                                            @elseif($value->status == '4')
                                                <label class="light badge-sm badge badge-primary">To Be Confirm</label>
                                            @else
                                                <label class="light badge-sm badge badge-danger">Declined</label>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script type="text/javascript">
        $(document).ready(function() {
            var radialBar = function() {
                var options = {
                    series: ['<?php echo number_format($transaction->successfullP, 2); ?>'],
                    chart: {
                        height: 280,
                        type: 'radialBar',
                        offsetY: -10
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -135,
                            endAngle: 135,
                            dataLabels: {
                                name: {
                                    fontSize: '16px',
                                    color: undefined,
                                    offsetY: 120
                                },
                                value: {
                                    offsetY: 0,
                                    fontSize: '34px',
                                    color: 'black',
                                    formatter: function(val) {
                                        return val + "%";
                                    }
                                }
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        colors: '#5c746b',
                        gradient: {
                            shade: 'dark',
                            shadeIntensity: 0.15,
                            inverseColors: false,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 50, 65, 91]
                        },
                    },
                    stroke: {
                        lineCap: 'round',
                        colors: '#5c746b'
                    },
                    labels: [''],
                };

                var chart = new ApexCharts(document.querySelector("#radialBar"), options);
                chart.render();
            }
            radialBar();

            var donutChart = function() {
                $("span.donut").peity("donut", {
                    width: "90",
                    height: "90"
                });
            }
            donutChart();
        });
    </script>
@endsection
