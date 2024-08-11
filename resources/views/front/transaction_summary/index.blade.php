@extends('layouts.user.default')

@section('title')
    Summary
@endsection

@section('breadcrumbTitle')
<nav aria-label="breadcrumb">
   <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
      <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('dashboardPage') }}">Dashboard</a></li>
      <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Summary</li>
   </ol>
   <h6 class="font-weight-bolder mb-0">Summary</h6>
</nav>

@endsection

@section('customeStyle')
@endsection

@section('content')
<div class="col-xxl-8">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="header-title">
                <h5 class="card-title">All Type of Summary</h5>
            </div>
            <div class="card-header-toolbar align-items-center">
                <div class="btn-group mr-2">
                    <button class="btn btn-primary btn-sm searchModelOpen" data-bs-toggle="modal" data-bs-target="#searchModal"> More Filter &nbsp;
                    <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                            fill="#FFFFFF" />
                    </svg>
                </button>
                <a href="{{ route('transaction-summary') }}" class="btn btn-danger btn-sm" style="border-radius: 0px 5px 5px 0px !important;">Clear</a>
                </div>
            </div>
        </div>
        @php
            $successArr = [];
            $successArrCurrency = [];
            
            $declinedArr = [];
            $declinedArrCurrency = [];
            
            $refundArr = [];
            $refundArrCurrency = [];

            $chargebackArr = [];
            $chargebackArrCurrency = [];

            if(count($TransactionSummary) > 0)
            {
                foreach($TransactionSummary as $ts)
                {
                    array_push($successArr, round($ts['success_amount'], 2));
                    array_push($successArrCurrency, $ts['currency']);

                    if(round($ts['declined_amount'], 2) > 0){
                        array_push($declinedArr, round($ts['declined_amount'], 2));
                        array_push($declinedArrCurrency, $ts['currency']);
                    }

                    if(round($ts['refund_amount'], 2)){
                        array_push($refundArr, round($ts['refund_amount'], 2));
                        array_push($refundArrCurrency, $ts['currency']);
                    }

                    if(round($ts['chargebacks_amount'], 2)){
                        array_push($chargebackArr, round($ts['chargebacks_amount'], 2));
                        array_push($chargebackArrCurrency, $ts['currency']);
                    }
                }
            }
        @endphp
        <div class="card-body">
            <div class="row">
                @if(count($successArr) > 0)
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Success</h5>
                        </div>
                        <div class="card-body">
                            <!-- <div id="radialBarChart"></div> -->
                            <div class="table-responsive p-0">
                               <table class="table align-items-center justify-content-center mb-0">
                                  <thead>
                                     <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Value</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Count</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Completion</th>
                                        <th></th>
                                     </tr>
                                  </thead>
                                  <tbody>
                                    @foreach($TransactionSummary as $ts)
                                     <tr>
                                        <td  class="align-middle text-center text-sm">
                                           <p class="text-sm font-weight-bold mb-0">{{ round($ts['success_amount'], 2) . $ts['currency'] }}</p>
                                        </td>
                                        <td  class="align-middle text-center text-sm">
                                           <span class="text-xs font-weight-bold">{{ round($ts['success_count'], 2) }}</span>
                                        </td>
                                        <td  class="align-middle text-center text-sm">
                                           <div class="d-flex align-items-center justify-content-center">
                                              <span class="me-2 text-xs font-weight-bold">{{ round($ts['success_percentage'],2) }}%</span>
                                              <div>
                                                 <div class="progress">
                                                    <div class="progress-bar bg-gradient-success" role="progressbar" aria-valuenow="{{ round($ts['success_percentage'],2) }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $ts['success_percentage'] }}%;"></div>
                                                 </div>
                                              </div>
                                           </div>
                                        </td>
                                        <td class="align-middle">
                                           <button class="btn btn-link text-secondary mb-0">
                                           <i class="fa fa-ellipsis-v text-xs"></i>
                                           </button>
                                        </td>
                                     </tr>
                                     @endforeach
                                  </tbody>
                               </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if(count($declinedArr) > 0)
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Failed</h5>
                        </div>
                        <div class="card-body">
                            <!-- <div id="radialBarFailedChart"></div> -->
                            <div class="table-responsive p-0">
                               <table class="table align-items-center justify-content-center mb-0">
                                  <thead>
                                     <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Value</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Count</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Completion</th>
                                        <th></th>
                                     </tr>
                                  </thead>
                                  <tbody>
                                    @foreach($TransactionSummary as $ts)
                                     <tr>
                                        <td  class="align-middle text-center text-sm">
                                           <p class="text-sm font-weight-bold mb-0">{{ round($ts['declined_amount'], 2) . $ts['currency'] }}</p>
                                        </td>
                                        <td  class="align-middle text-center text-sm">
                                           <span class="text-xs font-weight-bold">{{ round($ts['declined_count'], 2) }}</span>
                                        </td>
                                        <td  class="align-middle text-center text-sm">
                                           <div class="d-flex align-items-center justify-content-center">
                                              <span class="me-2 text-xs font-weight-bold">{{ round($ts['declined_percentage'],2) }}%</span>
                                              <div>
                                                 <div class="progress">
                                                    <div class="progress-bar bg-gradient-danger" role="progressbar" aria-valuenow="{{ round($ts['success_percentage'],2) }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $ts['declined_percentage'] }}%;"></div>
                                                 </div>
                                              </div>
                                           </div>
                                        </td>
                                        <td class="align-middle">
                                           <button class="btn btn-link text-secondary mb-0">
                                           <i class="fa fa-ellipsis-v text-xs"></i>
                                           </button>
                                        </td>
                                     </tr>
                                     @endforeach
                                  </tbody>
                               </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="row">
                @if(count($refundArr) > 0)
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Refund</h5>
                        </div>
                        <div class="card-body">
                            <!-- <div id="radialBarRefundChart"></div> -->
                            <div class="table-responsive p-0">
                               <table class="table align-items-center justify-content-center mb-0">
                                  <thead>
                                     <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Value</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Count</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Completion</th>
                                        <th></th>
                                     </tr>
                                  </thead>
                                  <tbody>
                                    @foreach($TransactionSummary as $ts)
                                     <tr>
                                        <td  class="align-middle text-center text-sm">
                                           <p class="text-sm font-weight-bold mb-0">{{ round($ts['refund_amount'], 2) . $ts['currency'] }}</p>
                                        </td>
                                        <td  class="align-middle text-center text-sm">
                                           <span class="text-xs font-weight-bold">{{ round($ts['refund_count'], 2) }}</span>
                                        </td>
                                        <td  class="align-middle text-center text-sm">
                                           <div class="d-flex align-items-center justify-content-center">
                                              <span class="me-2 text-xs font-weight-bold">{{ round($ts['refund_percentage'],2) }}%</span>
                                              <div>
                                                 <div class="progress">
                                                    <div class="progress-bar bg-gradient-secondary" role="progressbar" aria-valuenow="{{ round($ts['success_percentage'],2) }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $ts['refund_percentage'] }}%;"></div>
                                                 </div>
                                              </div>
                                           </div>
                                        </td>
                                        <td class="align-middle">
                                           <button class="btn btn-link text-secondary mb-0">
                                           <i class="fa fa-ellipsis-v text-xs"></i>
                                           </button>
                                        </td>
                                     </tr>
                                     @endforeach
                                  </tbody>
                               </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(count($chargebackArr) > 0)
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Chargebacks</h5>
                        </div>
                        <div class="card-body">
                            <!-- <div id="radialBarChargebacksChart"></div> -->
                            <div class="table-responsive p-0">
                               <table class="table align-items-center justify-content-center mb-0">
                                  <thead>
                                     <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Value</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Count</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Completion</th>
                                        <th></th>
                                     </tr>
                                  </thead>
                                  <tbody>
                                    @foreach($TransactionSummary as $ts)
                                     <tr>
                                        <td  class="align-middle text-center text-sm">
                                           <p class="text-sm font-weight-bold mb-0">{{ round($ts['chargeback_amount'], 2) . $ts['currency'] }}</p>
                                        </td>
                                        <td  class="align-middle text-center text-sm">
                                           <span class="text-xs font-weight-bold">{{ round($ts['chargeback_count'], 2) }}</span>
                                        </td>
                                        <td  class="align-middle text-center text-sm">
                                           <div class="d-flex align-items-center justify-content-center">
                                              <span class="me-2 text-xs font-weight-bold">{{ round($ts['chargeback_percentage'],2) }}%</span>
                                              <div>
                                                 <div class="progress">
                                                    <div class="progress-bar bg-gradient-warning" role="progressbar" aria-valuenow="{{ round($ts['success_percentage'],2) }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $ts['chargeback_percentage'] }}%;"></div>
                                                 </div>
                                              </div>
                                           </div>
                                        </td>
                                        <td class="align-middle">
                                           <button class="btn btn-link text-secondary mb-0">
                                           <i class="fa fa-ellipsis-v text-xs"></i>
                                           </button>
                                        </td>
                                     </tr>
                                     @endforeach
                                  </tbody>
                               </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <form method="GET" id="search-form" class="form-dark">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Advanced Search</h4>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="basic-form">
                        <div class="form-row row">
                            <div class="form-group col-md-12">
                                <label for="text">Start Date</label>
                                <input class="form-control" type="text" name="start_date" placeholder="Start Date"
                                    id="start_date"
                                    value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : '' }}"
                                    autocomplete="off">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="text">End Date</label>
                                <input class="form-control" type="text" name="end_date" placeholder="End Date"
                                    id="end_date"
                                    value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="resetForm"></i>Clear</button>
                    <button type="submit" class="btn btn-success" id="extraSearch123">Search</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('customScript')
<script type="text/javascript">
    var successArr = @json($successArr);
    var successArrCurrency = @json($successArrCurrency);
    var chartElement = document.querySelector("#radialBarChart");
    
    if(chartElement){
        var radialBarChartoptions = {
            series: successArr,
            chart: {
                height: 343,
                type: 'radialBar',
            },
            plotOptions: {
                radialBar: {
                    dataLabels: {
                        name: {
                            fontSize: '22px',
                        },
                        value: {
                            fontSize: '16px',
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function (w) {
                                return successArr.reduce((a, b) => a + b, 0).toFixed(2); // Sum of success percentages
                            }
                        }
                    }
                }
            },
            labels: successArrCurrency,
        };
        var chart = new ApexCharts(document.querySelector("#radialBarChart"), radialBarChartoptions);
        chart.render();
    }

    var declinedArr = @json($declinedArr);
    var declinedArrCurrency = @json($declinedArrCurrency);
    
    var chartElementFailed = document.querySelector("#radialBarFailedChart");
    if(chartElementFailed){
        var radialFailedBarChartoptions = {
            series: declinedArr,
            chart: {
                height: 343,
                type: 'radialBar',
            },
            plotOptions: {
                radialBar: {
                    dataLabels: {
                        name: {
                            fontSize: '22px',
                        },
                        value: {
                            fontSize: '16px',
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function (w) {
                                return declinedArr.reduce((a, b) => a + b, 0).toFixed(2); // Sum of success percentages
                            }
                        }
                    }
                }
            },
            labels: declinedArrCurrency,
        };
        var chartFailed = new ApexCharts(document.querySelector("#radialBarFailedChart"), radialFailedBarChartoptions);
        chartFailed.render();
    }


    
    var refundArr = @json($refundArr);
    var refundArrCurrency = @json($refundArrCurrency);
    var chartElementRefund = document.querySelector("#radialBarRefundChart");
    if(chartElementRefund){
        var radialRefundBarChartoptions = {
            series: refundArr,
            chart: {
                height: 343,
                type: 'radialBar',
            },
            plotOptions: {
                radialBar: {
                    dataLabels: {
                        name: {
                            fontSize: '22px',
                        },
                        value: {
                            fontSize: '16px',
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function (w) {
                                return refundArr.reduce((a, b) => a + b, 0).toFixed(2); // Sum of success percentages
                            }
                        }
                    }
                }
            },
            labels: refundArrCurrency,
        };
        var chartRefund = new ApexCharts(document.querySelector("#radialBarRefundChart"), radialRefundBarChartoptions);
        chartRefund.render();
    }

    var chargebackArr = @json($chargebackArr);
    var chargebackArrCurrency = @json($chargebackArrCurrency);
    var chartElementChargeback = document.querySelector("#radialBarChargebacksChart");
    if(chartElementChargeback){
        var radialBarChargebacksChart = {
            series: chargebackArr,
            chart: {
                height: 343,
                type: 'radialBar',
            },
            plotOptions: {
                radialBar: {
                    dataLabels: {
                        name: {
                            fontSize: '22px',
                        },
                        value: {
                            fontSize: '16px',
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function (w) {
                                return chargebackArr.reduce((a, b) => a + b, 0).toFixed(2); // Sum of success percentages
                            }
                        }
                    }
                }
            },
            labels: chargebackArrCurrency,
        };
        var chartChargebacks = new ApexCharts(document.querySelector("#radialBarChargebacksChart"), radialBarChargebacksChart);
        chartChargebacks.render();
    }

    
</script>
@endsection
