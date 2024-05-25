@extends('layouts.user.default')

@section('title')
Dashboard
@endsection

@section('breadcrumbTitle')
Dashboard
@endsection

@section('customeStyle')
<!-- <script src="https://cdn.lordicon.com/libs/frhvbuzj/lord-icon-2.0.2.js"></script> -->
<!-- <link href="{{ storage_asset('/theme/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet"> -->
<style type="text/css">
    .dropdown.bootstrap-select.default-select {
        width: 130px !important;
    }
</style>
@endsection

@section('content')
@php
$transactionPermission = 0;
$settingsPermission = 0;
@endphp
@if(Auth()->user()->main_user_id != '0')
@if(Auth()->user()->transactions == '1')
@php
$transactionPermission = 1;
@endphp
@endif
@if(Auth()->user()->settings == '1')
@php
$settingsPermission = 1;
@endphp
@endif
@endif
@if(!empty(Auth::user()->application))
@if(Auth::user()->application->status == 4 || Auth::user()->application->status == 5 ||
Auth::user()->application->status == 6 || Auth::user()->application->status == 10 || Auth::user()->application->status
== 11)
@php
$transactionPermission = 1;
$settingsPermission = 1;
@endphp
@endif
@endif
@if($transactionPermission == 1)
<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-sm-6 col-lg-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="subheader">Successful</div>
                  </div>
                  <div class="d-flex align-items-baseline">
                    <div class="h2 mb-0 me-2">$ {{ $transaction->successfullV }}</div>
                    <div class="me-auto">
                      <span class="text-success d-inline-flex align-items-center lh-1">
                        {{ round($transaction->successfullP,2) }} %
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                      </span>
                    </div>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      Total Count: {{ $transaction->successfullC }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="subheader">Declined</div>
                  </div>
                  <div class="d-flex align-items-baseline">
                    <div class="h2 mb-0 me-2">$ {{$transaction->declinedV}}</div>
                    <div class="me-auto">
                      <span class="text-danger d-inline-flex align-items-center lh-1">
                        {{round($transaction->declinedP,2)}} %
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                      </span>
                    </div>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      Total Count: {{$transaction->declinedC}}
                    </div>
                  </div>
                </div>
                <div id="chart-revenue-bg" class="chart-sm"></div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="subheader">Refund</div>
                  </div>
                  <div class="d-flex align-items-baseline">
                    <div class="h2 mb-0 me-2">$  {{ $transaction->refundV }}</div>
                    <div class="me-auto">
                      <span class="text-warning d-inline-flex align-items-center lh-1">
                        {{ round($transaction->refundP,2) }} %  
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                      </span>
                    </div>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      Total Count:  {{ $transaction->refundC }}
                    </div>
                  </div>
                  <div id="chart-new-clients" class="chart-sm"></div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="subheader">Chargebacks</div>
                  </div>
                  <div class="d-flex align-items-baseline">
                    <div class="h2 mb-0 me-2">$  {{ $transaction->chargebackV }}</div>
                    <div class="me-auto">
                      <span class="text-warning d-inline-flex align-items-center lh-1">
                        {{ round($transaction->chargebackP,2) }} %  
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                      </span>
                    </div>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      Total Count:  {{ $transaction->chargebackC }}
                    </div>
                  </div>
                  <div id="chart-new-clients" class="chart-sm"></div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="subheader">Dispute</div>
                  </div>
                  <div class="d-flex align-items-baseline">
                    <div class="h2 mb-0 me-2">$  {{ $transaction->suspiciousV }}</div>
                    <div class="me-auto">
                      <span class="text-primary d-inline-flex align-items-center lh-1">
                        {{ round($transaction->suspiciousP,2) }} %  
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                      </span>
                    </div>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      Total Count:  {{ $transaction->suspiciousC }}
                    </div>
                  </div>
                  <div id="chart-new-clients" class="chart-sm"></div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="subheader">Retrieval</div>
                  </div>
                  <div class="d-flex align-items-baseline">
                    <div class="h2 mb-0 me-2">$  {{ $transaction->retrievalV }}</div>
                    <div class="me-auto">
                      <span class="text-info d-inline-flex align-items-center lh-1">
                        {{ round($transaction->retrievalP,2) }} %  
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                      </span>
                    </div>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      Total Count:  {{ $transaction->retrievalC }}
                    </div>
                  </div>
                  <div id="chart-new-clients" class="chart-sm"></div>
                </div>
              </div>
            </div>
        </div>
    </div>
    <!-- <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="header-title">
                    <h5 class="card-title">Statistics Reports</h5>
                </div>
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                      <a class="nav-link active" href="#SUCCESSFUL" data-bs-toggle="tab">Successful</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#DECLINED" data-bs-toggle="tab">Declined</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#REFUND" data-bs-toggle="tab">Refund</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#CHARGEBACKS" data-bs-toggle="tab">Chargebacks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#SUSPICIOUS" data-bs-toggle="tab">Dispute</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#RETRIEVAL" data-bs-toggle="tab">Retrieval</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#BLOCK" data-bs-toggle="tab">Block</a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="SUCCESSFUL">
                        <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th width="50px">Currency</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($TransactionSummary) > 0)
                                @foreach($TransactionSummary as $ts)
                                <tr>
                                    <td>{{ $ts['currency'] }}</td>
                                    <td>{{ $ts['success_amount'] }}</td>
                                    <td>{{ round($ts['success_percentage'],2) }}</td>
                                    <td>{{ $ts['success_count'] }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center text-white" colspan="4">No record found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="DECLINED">
                        <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th width="50px">Currency</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($TransactionSummary) > 0)
                                @foreach($TransactionSummary as $ts)
                                <tr>
                                    <td>{{ $ts['currency'] }}</td>
                                    <td>{{
                                        number_format($ts['declined_amount'],2,".",",") }}</td>
                                    <td>{{ round($ts['declined_percentage'],2) }}
                                    <td>{{ $ts['declined_count'] }}</td>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center text-white" colspan="4">No record found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="CHARGEBACKS">
                        <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th width="50px">Currency</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($TransactionSummary) > 0)
                                @foreach($TransactionSummary as $ts)
                                <tr>
                                    <td>{{ $ts['currency'] }}</td>
                                    <td>{{
                                        number_format($ts['chargebacks_amount'],2,".",",") }}</td>
                                    <td>{{ round($ts['chargebacks_percentage'],2) }}
                                    </td>
                                    <td>{{ $ts['chargebacks_count'] }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center text-white" colspan="4">No record found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="REFUND">
                        <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th width="50px">Currency</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($TransactionSummary) > 0)
                                @foreach($TransactionSummary as $ts)
                                <tr>
                                    <td>{{ $ts['currency'] }}</td>
                                    <td>{{
                                        number_format($ts['refund_amount'],2,".",",") }}</td>
                                    <td>{{ round($ts['refund_percentage'],2) }}</td>
                                    <td>{{ $ts['refund_count'] }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center text-white" colspan="4">No record found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="SUSPICIOUS">
                        <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th width="50px">Currency</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($TransactionSummary) > 0)
                                @foreach($TransactionSummary as $ts)
                                <tr>
                                    <td>{{ $ts['currency'] }}</td>
                                    <td>{{
                                        number_format($ts['flagged_amount'],2,".",",") }}</td>
                                    <td>{{ round($ts['flagged_percentage'],2) }}</td>
                                    <td>{{ $ts['flagged_count'] }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center text-white" colspan="4">No record found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="RETRIEVAL">
                        <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th width="50px">Currency</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($TransactionSummary) > 0)
                                @foreach($TransactionSummary as $ts)
                                <tr>
                                    <td>{{ $ts['currency'] }}</td>
                                    <td>{{
                                        number_format($ts['retrieval_amount'],2,".",",") }}</td>
                                    <td>{{ round($ts['retrieval_percentage'],2) }}</td>
                                    <td>{{ $ts['retrieval_count'] }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center text-white" colspan="4">No record found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="BLOCK">
                        <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th width="50px">Currency</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($TransactionSummary) > 0)
                                @foreach($TransactionSummary as $ts)
                                <tr>
                                    <td>{{ $ts['currency'] }}</td>
                                    <td>{{
                                        number_format($ts['block_amount'],2,".",",") }}</td>
                                    <td>{{ round($ts['block_percentage'],2) }}</td>
                                    <td>{{ $ts['block_count'] }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center text-white" colspan="4">No record found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <!-- <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h5 class="card-title">Transactions Summary</h5>
                </div>
                <div class="card-header-toolbar d-flex align-items-center">
                    <a href="{!! route('transaction-summary') !!}" class="btn btn-primary btn-sm">View All</a>
                </div>
            </div>
            <div class="card-body">
                <canvas id="myChart2"></canvas>
            </div>
        </div>
    </div> -->
    <!-- <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h5 class="card-title">Latest Payments</h5>
                </div>
                <div class="card-header-toolbar d-flex align-items-center">
                    <a href="{{ route('gettransactions') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive custom-table">
                    <table id="latest_transactions" class="table mb-0 table-borderless table-striped">
                       <thead>
                          <tr>
                             <th>Transaction Number</th>
                             <th>Status</th>
                             <th>Amount</th>
                             <th>Date</th>
                          </tr>
                       </thead>
                        <tbody>
                            @if(isset($latestTransactionsData) && count($latestTransactionsData)>0)
                                @foreach($latestTransactionsData as $allTransaction)
                                    <tr>
                                        <td>{{ $allTransaction->order_id }}</td>
                                        <td>
                                            @if($allTransaction->status == '1')
                                                <label class="badge badge-success">Success</label>
                                            @elseif($allTransaction->status == '2')
                                                <label class="badge badge-warning">Pending</label>
                                            @elseif($allTransaction->status == '3')
                                                <label class="badge badge-yellow">Canceled</label>
                                            @elseif($allTransaction->status == '5')
                                                <label class="badge badge-primary">Blocked</label>
                                            @else
                                                <label class="badge badge-danger">Declined</label>
                                            @endif
                                        </td>
                                        <td>{{ $allTransaction->amount." ". $allTransaction->currency }}</td>
                                        <td>{{ convertDateToLocal($allTransaction->created_at, 'd-m-Y H:i') }}</td>
                                        
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                   <td colspan="10">No Record found!.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> -->
</div>
@endif
@endsection

@section('customScript')

<script type="text/javascript">

    <?php 
        $transactionsDate = array_column($transactionsLine, 'date');
        $successTransactions = array_column($transactionsLine, 'successTransactions');
        $declinedTransactions = array_column($transactionsLine, 'declinedTransactions');
    ?>

    var ctx2 = document.getElementById("myChart2").getContext('2d');
    var myChart2 = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($transactionsDate); ?>,
            datasets: [{ 
                data: <?php echo json_encode($successTransactions); ?>,
                label: "Successful",
                borderColor: "#56C65A",
                fill: false
            }, { 
                data: <?php echo json_encode($declinedTransactions); ?>,
                label: "Declined",
                borderColor: "#46344E",
                fill: false
            }
          ]
        },
        
        options: {
          title: {
            display: true,
            text: 'Wallet Insight'
          },stroke: { curve: "smooth", width: 2 }
        }
    });


</script>
@endsection