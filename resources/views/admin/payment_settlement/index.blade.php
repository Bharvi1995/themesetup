@extends('layouts.admin.default')
@section('title')
    Payour Report
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
                        <h4 class="card-title">Payout Report</h4>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info bell-link btn-sm" data-bs-toggle="modal"
                            data-bs-target="#searchModal"> <i class="fa fa-search-plus"></i>
                            Advanced
                            Search</button>
                        <a href="{!! url('paylaksa/admin-user') !!}" class="btn btn-primary btn-sm">Reset</a>
                    </div>
                </div>
                <div class="card-body p-0">
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
                                    <th>Action</th>
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
                                        <td>{{ number_format((float) $data->total_payable, 2, '.', '') }}</td>
                                        <td>
                                            @if (isset($user->threshold_amount) && $data->net_payable > $user->threshold_amount)
                                                <span
                                                    class="text-success">{{ number_format((float) $data->gross_payable, 2, '.', '') }}</span>
                                            @else
                                                <span
                                                    class="text-danger">{{ number_format((float) $data->gross_payable, 2, '.', '') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($user->threshold_amount) && $data->net_payable > $user->threshold_amount)
                                                <span
                                                    class="text-success">{{ number_format((float) $data->net_payable, 2, '.', '') }}</span>
                                            @else
                                                <span
                                                    class="text-danger">{{ number_format((float) $data->net_payable, 2, '.', '') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown ml-auto">
                                                <a href="#" class="btn btn-primary sharp rounded-pill"
                                                    data-bs-toggle="dropdown" aria-expanded="true"><svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="18px"
                                                        height="18px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24"
                                                                height="24">
                                                            </rect>
                                                            <circle fill="#FFF" cx="5" cy="12"
                                                                r="2">
                                                            </circle>
                                                            <circle fill="#FFF" cx="12" cy="12"
                                                                r="2">
                                                            </circle>
                                                            <circle fill="#FFF" cx="19" cy="12"
                                                                r="2">
                                                            </circle>
                                                        </g>
                                                    </svg>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @if (isset($user->threshold_amount) && $data->net_payable > $user->threshold_amount && $data->net_payable > 0)
                                                        <li class="dropdown-item">
                                                            <a href="#" class="generatePayoutReport dropdown-item"><i
                                                                    class="fa fa-edit text-secondary me-2"></i>
                                                                Paid
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li class="dropdown-item">
                                                        <a href="{{ route('merchant.generate_thisdate_calculation') }}?date={{ date('Y-m-d', strtotime($data->start_date)) }}&user_id={{ request()->get('user_id') }}"
                                                            class="dropdown-item"><i
                                                                class="fa fa-edit text-secondary me-2"></i>
                                                            Recalculate
                                                        </a>
                                                    </li>
                                                    <li class="dropdown-item">
                                                        <a href="{{ route('merchant.view_report_tilldate') }}?date={{ date('Y-m-d', strtotime($data->start_date)) }}&user_id={{ request()->get('user_id') }}"
                                                            class="dropdown-item"><i
                                                                class="fa fa-edit text-secondary me-2"></i>
                                                            View
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-5">
                        @if (request()->get('user_id'))
                            {{ $getSettlementRepost->appends(request()->input())->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="auto_generate_payout_report" action="{{ route('merchant.auto_payoutreport') }}" method="POST">
        @csrf
        <input type="hidden" name="user_id" value="{{ request()->user_id }}" />
        <input type="hidden" id="start_date" name="start_date" value="" />
    </form>

    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <form method="" id="search-form">
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row">

                                <div class="form-group col-lg-6">
                                    <label for="name">Merchant</label>
                                    <select class="form-control-sm form-control" name="user_id" id="user_id"
                                        style="width: 165px; float: left; margin-right: 5px;">
                                        <option selected value="0"> -- Select Merchant -- </option>
                                        @foreach ($companyList as $item => $value)
                                            <option value="{{ $value->user_id }}"
                                                {{ request()->user_id == $value->user_id ? 'selected' : '' }}>
                                                {{ $value->business_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="email">Start Date</label>
                                    <input type="text" class="form-control datepicker" placeholder="Enter here"
                                        name="start_date"
                                        value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? date('m/d/Y', strtotime($_GET['start_date'])) : '' }}">
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="email">End Date</label>
                                    <input type="text" class="form-control datepicker" placeholder="Enter here"
                                        name="end_date"
                                        value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? date('m/d/Y', strtotime($_GET['end_date'])) : '' }}">
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="resetForm"></i>Clear</button>
                        <button type="submit" class="btn btn-info" id="extraSearch123">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
    <script>
        $(document).on("click", ".generatePayoutReport", function() {

            var startDate = $(this).parents('tr').find('td:eq(0)').text();
            $("#auto_generate_payout_report #start_date").val(startDate);
            $("#auto_generate_payout_report").submit();

        });
        $(document).on("change", "#user_id", function() {
            var user_id = $("#user_id").find(":selected").val();
            if (user_id != 0) {
                location.href = "{{ route('merchant.daily_settlement_report') }}?user_id=" + user_id;
            } else {
                location.href = "{{ route('merchant.daily_settlement_report') }}";
            }
        });
    </script>
@endsection
