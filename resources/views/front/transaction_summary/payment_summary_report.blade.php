@extends('layouts.user.default')
@section('title')
    Payment status summary Report
@endsection

@section('breadcrumbTitle')
    Payment status summary Report
@endsection
@section('content')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <form method="" id="search-form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="form-row">
                                <div class="form-group col-lg-6">
                                    <label for="text">Currency</label>
                                    <select class="form-control select2" name="currency" id="currency">
                                        <option selected disabled> -- Select Currency -- </option>
                                        @foreach (config('currency.three_letter') as $key => $currency)
                                            <option value="{{ $currency }}"
                                                {{ isset($_GET['currency']) && $_GET['currency'] == $key ? 'selected' : '' }}>
                                                {{ $currency }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="text">Payment Status</label>
                                    <select class="form-control select2" name="status" id="status">
                                        <option selected disabled> -- Select Status -- </option>
                                        @foreach ($payment_status as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ isset($_GET['status']) && $_GET['status'] == $key ? 'selected' : '' }}>
                                                {{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="text">Select Date</label>
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
                                        <input type="" id="end_date" class="form-control"
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
                        <button type="button" class="btn btn-primary" id="resetForm"></i>Clear</button>
                        <button type="submit" class="btn btn-success" id="extraSearch123">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="iq-card">
                <div class="iq-card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Payment status summary Report</h4>
                    </div>
                    <div>
                        <div class="btn-group mr-2">
                            <button type="button" class="btn btn-info bell-link btn-sm" data-toggle="modal"
                                data-target="#searchModal"> <i class="fa fa-search-plus"></i>
                                Advanced Search</button>
                            <a href="{{ route('user-payment-status-summary-report') }}"
                                class="btn btn-primary btn-sm">Reset</a>
                        </div>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div class="table-responsive tableFixHead">
                        @if (count($arr_t_data) > 0 && isset($_GET['status']))
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th colspan="3" class="text-center {{ $payment_status_class[$_GET['status']] }}">
                                            {{ $payment_status[$_GET['status']] }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th width="350px">Mechant</th>
                                        <th width="50px">Currency</th>
                                        <th>Count</th>
                                        <th>Amount</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($arr_t_data as $user_transaction)
                                        <?php $rowspan = count($user_transaction);
                                        $k = 0; ?>
                                        @foreach ($user_transaction as $ks => $transaction)
                                            <tr>
                                                @if ($k == 0)
                                                    <td rowspan="{{ $rowspan }}">{{ $transaction['business_name'] }}
                                                    </td>
                                                @endif
                                                <td>{{ $transaction['currency'] }}</td>
                                                @if ($_GET['status'] == 1)
                                                    <td class="text-right">{{ $transaction['success_count'] }}</td>
                                                    <td class="text-right">{{ $transaction['success_amount'] }}</td>
                                                    <td class="text-right">
                                                        {{ round($transaction['success_percentage'], 2) }}</td>
                                                @elseif($_GET['status'] == 2)
                                                    <td class="text-right">{{ $transaction['declined_count'] }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($transaction['declined_amount'], 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ round($transaction['declined_percentage'], 2) }}</td>
                                                @elseif($_GET['status'] == 3)
                                                    <td class="text-right">{{ $transaction['chargebacks_count'] }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($transaction['chargebacks_amount'], 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ round($transaction['chargebacks_percentage'], 2) }}</td>
                                                @elseif($_GET['status'] == 4)
                                                    <td class="text-right">{{ $transaction['refund_count'] }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($transaction['refund_amount'], 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">{{ round($transaction['refund_percentage'], 2) }}
                                                    </td>
                                                @elseif($_GET['status'] == 5)
                                                    <td class="text-right">{{ $transaction['flagged_count'] }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($transaction['flagged_amount'], 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ round($transaction['flagged_percentage'], 2) }}</td>
                                                @elseif($_GET['status'] == 7)
                                                    <td class="text-right">{{ $transaction['block_count'] }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($transaction['block_amount'], 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">{{ round($transaction['block_percentage'], 2) }}
                                                    </td>
                                                @endif
                                            </tr>
                                            <?php $k++; ?>
                                        @endforeach
                                    @endforeach

                                </tbody>
                            </table>
                        @else
                            <p style="text-align: center;">No record found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var height = $(window).height();
            height = height - 300;
            $('.tableFixHead').css('height', height + 'px');
        });
    </script>
@endsection
