@extends('layouts.admin.default')
@section('title')
    Merchant Payout Report
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Merchant Payout Report
@endsection
@section('content')
    @include('requestDate')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog modal-lg-scrollable" role="document">
            <form method="" id="search-form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row ">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="resetForm"></i>Clear</button>
                        <button type="submit" class="btn btn-info" id="extraSearch123">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mb-3 text-right">
            <?php
            $url = Request::fullUrl();
            $parsedUrl = parse_url($url);
            $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
            $subQueryString = $query != '' ? $query . '&type=xlsx' : '';
            ?>

            @if (!empty($subQueryString))
                <a href="{{ route('merchant-payout-report-export', [$subQueryString]) }}" class="btn btn-info btn-sm"
                    id="ExcelLink">
                    <i class="fa fa-download"></i>
                    Export Excel
                </a>
            @else
                <a href="{{ route('merchant-payout-report-export', [$subQueryString]) }}" class="btn btn-info btn-sm"
                    id="ExcelLink">
                    <i class="fa fa-download"></i>
                    Export Excel
                </a>
            @endif

        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title"><?php if ($CompanyName != '') {
                            echo $CompanyName . ' ';
                        } ?>Transactions</h4>
                    </div>
                    <div class="col-md-3">
                        <select name="user_id" id="user_id" data-size="7" data-live-search="true"
                            class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                            <option selected value="0"> -- Select Merchant -- </option>
                            @foreach ($companyList as $item => $value)
                                <option value="{{ $item }}" {{ request()->user_id == $item ? 'selected' : '' }}>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total Processing Amount</th>
                                    <th>Declined Amount</th>
                                    <th>ChargeBack Amount</th>
                                    <th>Refund Amount</th>
                                    <th>Flagged Amount</th>
                                    <th>Pre Arbitration Amount</th>
                                    <th>Approved Count</th>
                                    <th>Declined Count</th>
                                    <th>ChargeBack Count</th>
                                    <th>Refund Count</th>
                                    <th>Flagged Count</th>
                                    <th>Pre Arbitration Count</th>
                                    <th>Pre Arbitration Count</th>
                                    <th>MDR</th>
                                    <th>Reserve</th>
                                    <th>Transaction Fee</th>
                                    <th>Refund Fee</th>
                                    <th>High Risk Transaction Fee</th>
                                    <th>ChargeBack Fee</th>
                                    <th>Total Payable</th>
                                    <th>Gross Payable</th>
                                    <th>Net Payable</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $transaction)
                                    <tr id="tr_{{ $transaction['id'] }}">
                                        <td>{{ $transaction['created_date'] }}</td>
                                        <td>{{ $transaction['total_processing_amount'] }}</td>
                                        <td>{{ $transaction['approved_amount'] }}</td>
                                        <td>{{ $transaction['declined_amount'] }}</td>
                                        <td>{{ $transaction['chargeback_amount'] }}</td>
                                        <td>{{ $transaction['refund_amount'] }}</td>
                                        <td>{{ $transaction['flagged_amount'] }}</td>
                                        <td>{{ $transaction['pre_arbitration_amount'] }}</td>
                                        <td>{{ $transaction['approved_count'] }}</td>
                                        <td>{{ $transaction['declined_count'] }}</td>
                                        <td>{{ $transaction['chargeback_count'] }}</td>
                                        <td>{{ $transaction['refund_count'] }}</td>
                                        <td>{{ $transaction['flagged_count'] }}</td>
                                        <td>{{ $transaction['pre_arbitration_count'] }}</td>
                                        <td>{{ $transaction['total_no_of_transactions_count'] }}</td>
                                        <td>{{ $transaction['mdr'] }}</td>
                                        <td>{{ $transaction['reserve'] }}</td>
                                        <td>{{ $transaction['transaction_fee'] }}</td>
                                        <td>{{ $transaction['refund_fee'] }}</td>
                                        <td>{{ $transaction['high_risk_transaction_fee'] }}</td>
                                        <td>{{ $transaction['chargeback_fee'] }}</td>
                                        <td>{{ $transaction['total_payable'] }}</td>
                                        <td>{{ $transaction['gross_payable'] }}</td>
                                        <td>{{ $transaction['net_payable'] }}</td>
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
                                                    </svg></a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li class="dropdown-item">
                                                        <a href="#" class="dropdown-item"><i
                                                                class="fa fa-edit text-secondary me-2"></i>
                                                            View
                                                        </a>
                                                    </li>
                                                    <li class="dropdown-item">
                                                        <a data-id="{{ $transaction['id'] }}"
                                                            class="dropdown-item deleteTransaction" data-link="#"><i
                                                                class="fa fa-trash text-danger me-2"></i> Delete
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
                location.href = "{{ route('merchant-payout-report') }}?user_id=" + user_id;
            } else {
                location.href = "{{ route('merchant-payout-report') }}";
            }
        });
    </script>
@endsection
