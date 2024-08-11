@extends('layouts.agent.default')
@section('title')
Summary
@endsection
@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('rp.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Summary</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Summary</h6>
    </nav>
@endsection
@section('content')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <form method="" id="search-form" class="form-dark">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">More Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="text">Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="start_date"
                                            placeholder="Start Date" id="start_date"
                                            value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="end_date">End Date</label>
                                    <div class="date-input">
                                        <input type="text" id="end_date" class="form-control"
                                            data-multiple-dates-separator=" - " data-language="en" placeholder="End Date"
                                            name="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
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
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="col-md-12">
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
                            <a href="{{ route('rp.merchant-transaction-report') }}" class="btn btn-danger btn-sm" style="border-radius: 0px 5px 5px 0px !important;">Clear</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(count($transactions_summary) > 0)
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Success</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive p-0">
                                           <table class="table align-items-center justify-content-center mb-0">
                                              <thead>
                                                 <tr>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Merchant</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Value</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Count</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Completion</th>
                                                    <th></th>
                                                 </tr>
                                              </thead>
                                              <tbody>
                                                @if (count($transactions_summary) > 0)
                                                    @foreach ($transactions_summary as $userid => $c_transaction)
                                                        <?php $rowspan = count($c_transaction);
                                                        $k = 0; ?>
                                                        @foreach ($c_transaction as $transaction)
                                                        <tr>
                                                            <td  class="align-middle text-center text-sm">
                                                               <p class="text-sm font-weight-bold mb-0">
                                                                    @if (isset($companyName[$userid]))
                                                                        {{ $companyName[$userid] }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </p>
                                                            </td>
                                                            <td  class="align-middle text-center text-sm">
                                                               <p class="text-sm font-weight-bold mb-0">{{ round($transaction['success_amount'], 2) . " ".$transaction['currency'] }}</p>
                                                            </td>
                                                            <td  class="align-middle text-center text-sm">
                                                               <span class="text-xs font-weight-bold">{{ round($transaction['success_count'], 2) }}</span>
                                                            </td>
                                                            <td  class="align-middle text-center text-sm">
                                                               <div class="d-flex align-items-center justify-content-center">
                                                                  <span class="me-2 text-xs font-weight-bold">{{ round($transaction['success_percentage'],2) }}%</span>
                                                                  <div>
                                                                     <div class="progress">
                                                                        <div class="progress-bar bg-gradient-success" role="progressbar" aria-valuenow="{{ round($transaction['success_percentage'],2) }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $transaction['success_percentage'] }}%;"></div>
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
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-white" colspan="5">No record found.
                                                        </td>
                                                    </tr>
                                                @endif
                                              </tbody>
                                           </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(count($transactions_summary) > 0)
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Failed</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive p-0">
                                           <table class="table align-items-center justify-content-center mb-0">
                                              <thead>
                                                 <tr>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Merchant</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Value</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Count</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Completion</th>
                                                    <th></th>
                                                 </tr>
                                              </thead>
                                              <tbody>
                                                @if (count($transactions_summary) > 0)
                                                    @foreach ($transactions_summary as $userid => $c_transaction)
                                                        <?php $rowspan = count($c_transaction);
                                                        $k = 0; ?>
                                                        @foreach ($c_transaction as $transaction)
                                                        <tr>
                                                            <td  class="align-middle text-center text-sm">
                                                               <p class="text-sm font-weight-bold mb-0">
                                                                    @if (isset($companyName[$userid]))
                                                                        {{ $companyName[$userid] }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </p>
                                                            </td>
                                                            <td  class="align-middle text-center text-sm">
                                                               <p class="text-sm font-weight-bold mb-0">{{ round($transaction['declined_amount'], 2) . " ".$transaction['currency'] }}</p>
                                                            </td>
                                                            <td  class="align-middle text-center text-sm">
                                                               <span class="text-xs font-weight-bold">{{ round($transaction['declined_count'], 2) }}</span>
                                                            </td>
                                                            <td  class="align-middle text-center text-sm">
                                                               <div class="d-flex align-items-center justify-content-center">
                                                                  <span class="me-2 text-xs font-weight-bold">{{ round($transaction['declined_percentage'],2) }}%</span>
                                                                  <div>
                                                                     <div class="progress">
                                                                        <div class="progress-bar bg-gradient-danger" role="progressbar" aria-valuenow="{{ round($transaction['declined_percentage'],2) }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $transaction['declined_percentage'] }}%;"></div>
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
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-white" colspan="5">No record found.
                                                        </td>
                                                    </tr>
                                                @endif
                                              </tbody>
                                           </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(count($transactions_summary) > 0)
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Chargeback</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive p-0">
                                           <table class="table align-items-center justify-content-center mb-0">
                                              <thead>
                                                 <tr>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Merchant</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Value</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Count</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Completion</th>
                                                    <th></th>
                                                 </tr>
                                              </thead>
                                              <tbody>
                                                @if (count($transactions_summary) > 0)
                                                    @foreach ($transactions_summary as $userid => $c_transaction)
                                                        <?php $rowspan = count($c_transaction);
                                                        $k = 0; ?>
                                                        @foreach ($c_transaction as $transaction)
                                                        <tr>
                                                            <td  class="align-middle text-center text-sm">
                                                               <p class="text-sm font-weight-bold mb-0">
                                                                    @if (isset($companyName[$userid]))
                                                                        {{ $companyName[$userid] }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </p>
                                                            </td>
                                                            <td  class="align-middle text-center text-sm">
                                                               <p class="text-sm font-weight-bold mb-0">{{ round($transaction['chargebacks_amount'], 2) . " ".$transaction['currency'] }}</p>
                                                            </td>
                                                            <td  class="align-middle text-center text-sm">
                                                               <span class="text-xs font-weight-bold">{{ round($transaction['chargebacks_count'], 2) }}</span>
                                                            </td>
                                                            <td  class="align-middle text-center text-sm">
                                                               <div class="d-flex align-items-center justify-content-center">
                                                                  <span class="me-2 text-xs font-weight-bold">{{ round($transaction['chargebacks_percentage'],2) }}%</span>
                                                                  <div>
                                                                     <div class="progress">
                                                                        <div class="progress-bar bg-gradient-warning" role="progressbar" aria-valuenow="{{ round($transaction['chargebacks_percentage'],2) }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $transaction['chargebacks_percentage'] }}%;"></div>
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
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-white" colspan="5">No record found.
                                                        </td>
                                                    </tr>
                                                @endif
                                              </tbody>
                                           </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(count($transactions_summary) > 0)
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Refund</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive p-0">
                                           <table class="table align-items-center justify-content-center mb-0">
                                              <thead>
                                                 <tr>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Merchant</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Value</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Count</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Completion</th>
                                                    <th></th>
                                                 </tr>
                                              </thead>
                                              <tbody>
                                                @if (count($transactions_summary) > 0)
                                                    @foreach ($transactions_summary as $userid => $c_transaction)
                                                        <?php $rowspan = count($c_transaction);
                                                        $k = 0; ?>
                                                        @foreach ($c_transaction as $transaction)
                                                        <tr>
                                                            <td  class="align-middle text-center text-sm">
                                                               <p class="text-sm font-weight-bold mb-0">
                                                                    @if (isset($companyName[$userid]))
                                                                        {{ $companyName[$userid] }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </p>
                                                            </td>
                                                            <td  class="align-middle text-center text-sm">
                                                               <p class="text-sm font-weight-bold mb-0">{{ round($transaction['refund_amount'], 2) . " ".$transaction['currency'] }}</p>
                                                            </td>
                                                            <td  class="align-middle text-center text-sm">
                                                               <span class="text-xs font-weight-bold">{{ round($transaction['refund_count'], 2) }}</span>
                                                            </td>
                                                            <td  class="align-middle text-center text-sm">
                                                               <div class="d-flex align-items-center justify-content-center">
                                                                  <span class="me-2 text-xs font-weight-bold">{{ round($transaction['refund_percentage'],2) }}%</span>
                                                                  <div>
                                                                     <div class="progress">
                                                                        <div class="progress-bar bg-gradient-secondary" role="progressbar" aria-valuenow="{{ round($transaction['refund_percentage'],2) }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $transaction['refund_percentage'] }}%;"></div>
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
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-white" colspan="5">No record found.
                                                        </td>
                                                    </tr>
                                                @endif
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
        </div>
    </div>
</div>
@endsection
@section('customScript')
    <!-- <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script> -->
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            // $(".select2").val('first').trigger('change.select2');
        });
    </script>
@endsection
