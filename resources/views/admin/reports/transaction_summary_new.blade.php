@extends('layouts.admin.default')
@section('title')
    Transaction Summary Report
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Transaction summary Report
@endsection
@section('content')

    <?php
    if (!empty($_GET['start_date'])) {
        $_GET['start_date'] = date('d-m-Y', strtotime($_GET['start_date']));
    }
    if (!empty($_GET['end_date'])) {
        $_GET['end_date'] = date('d-m-Y', strtotime($_GET['end_date']));
    }
    ?>

    <div class="chatbox">
        <div class="chatbox-close"></div>
        <div class="custom-tab-1">
            <a class="nav-link active" data-bs-toggle="tab" href="#Search">Advanced Search</a>
            <div class="tab-content">
                <div class="tab-pane fade active show" id="Search" role="tabpanel">
                    <form method="" id="search-form">
                        <div class="basic-form">
                            <div class="row ">
                                <div class="form-group col-lg-6">
                                    <label for="business_name">Select Merchant</label>
                                    <select name="user_id" id="business_name" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled> -- Select Merchant -- </option>
                                        @foreach ($companyName as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ isset($_GET['user_id']) && $_GET['user_id'] == $key ? 'selected' : '' }}>
                                                {{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>

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
                                        <input type="text" id="end_date" class="form-control" placeholder="End Date"
                                            name="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-4 submit-buttons-commmon">
                                    <button type="submit" class="btn btn-success" id="extraSearch123">Search</button>
                                    <button type="button" class="btn btn-danger" id="resetForm"></i>Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Transaction summary Report<span class="total-val-in-usd">(USD
                                {{ $totalAmtInUSD <= 0 ? '0.00' : $totalAmtInUSD }})</span></h4>
                    </div>
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-warning bell-link btn-sm"> <i class="fa fa-search-plus"></i>
                            Advanced Search</button>
                        <a href="{{ route('transaction-summary-report') }}" class="btn btn-danger btn-sm">Reset</a>
                    </div>
                    @if (auth()->guard('admin')->user()->can(['export-transaction-summary-report']))
                        <a class="btn btn-secondary btn-sm me-2"
                            data-link="{{ route('transaction-summary-report-excle', request()->all()) }}"
                            data-filename="Transaction_Summary_Excel_" href="#" id="ExcelLink">
                            <i class="fa fa-download"></i> Export
                            Excel</a>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-right pull-right mb-3">
                        <div class="btn-group mb-2 btn-group-sm">
                            <a href="{{ route('transaction-summary-report', ['for' => 'All']) }}" type="button"
                                class="btn {{ isset($_GET['for']) && $_GET['for'] == 'All' ? 'btn-success' : 'btn-warning' }}">All</a>
                            <a href="{{ route('transaction-summary-report', ['for' => 'Daily']) }}" type="button"
                                class="btn {{ (!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily') ? 'btn-success' : 'btn-warning' }}">Daily</a>
                            <a href="{{ route('transaction-summary-report', ['for' => 'Weekly']) }}" type="button"
                                class="btn {{ isset($_GET['for']) && $_GET['for'] == 'Weekly' ? 'btn-success' : 'btn-warning' }}">Weekly</a>
                            <a href="{{ route('transaction-summary-report', ['for' => 'Monthly']) }}" type="button"
                                class="btn {{ isset($_GET['for']) && $_GET['for'] == 'Monthly' ? 'btn-success' : 'btn-warning' }}">Monthly</a>
                        </div>
                    </div>
                    <div class="table-responsive tableFixHead">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th colspan="3" class="text-center text-success">SUCCESSFUL</th>
                                    <th colspan="3" class="text-center text-danger">DECLINED</th>
                                    <th colspan="3" class="text-center text-info">BLOCK</th>
                                </tr>
                                <tr>
                                    <th width="50px">Currency</th>
                                    <th>Count</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                    <th>Count</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                    <th>Count</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($transactions_summary) > 0)
                                    @foreach ($transactions_summary['success'] as $transaction)
                                        <tr>
                                            <td>{{ $transaction->currency }}</td>

                                            <td class="text-right">{{ $transaction->success_count }}</td>
                                            <td class="text-right">{{ $transaction->success_amount }}</td>
                                            <td class="text-right">{{ round($transaction->success_percentage, 2) }}</td>

                                            <td class="text-right">{{ $transaction->declined_count }}</td>
                                            <td class="text-right">
                                                {{ number_format($transaction->declined_amount, 2, '.', ',') }}
                                            </td>
                                            <td class="text-right">{{ round($transaction->declined_percentage, 2) }}</td>

                                            <td class="text-right">{{ $transaction->block_count }}</td>
                                            <td class="text-right">
                                                {{ number_format($transaction->block_amount, 2, '.', ',') }}
                                            </td>
                                            <td class="text-right">{{ round($transaction->block_percentage, 2) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="19">No record found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-xxl-6">
                            <div id="accordion-eleven1" class="accordion accordion-rounded-stylish accordion-bordered">
                                <div class="accordion__item">
                                    <div class="accordion__header collapsed accordion__header--info"
                                        data-bs-toggle="collapse" data-bs-target="#rounded-stylish_collapseOne">
                                        <span class="accordion__header--icon"></span>
                                        <span class="accordion__header--text">Chargeback</span>
                                        <span class="accordion__header--indicator"></span>
                                    </div>
                                    <div id="rounded-stylish_collapseOne" class="collapse accordion__body"
                                        data-parent="#accordion-eleven1">
                                        <div class="accordion__body--text p-0">
                                            <div class="table-responsive tableFixHead">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th width="50px">Currency</th>
                                                            <th>Count</th>
                                                            <th>Amount</th>
                                                            <th>Percentage</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if (count($transactions_summary) > 0)
                                                            @foreach ($transactions_summary['chargeback'] as $transaction)
                                                                <tr>
                                                                    <td>{{ $transaction->currency }}</td>
                                                                    <td class="text-right">
                                                                        {{ $transaction->chargebacks_count }}</td>
                                                                    <td class="text-right">
                                                                        {{ $transaction->chargebacks_amount }}</td>
                                                                    <td class="text-right">
                                                                        {{ round($transaction->chargebacks_percentage, 2) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td class="text-center" colspan="19">No record found.
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-xxl-6">
                            <div id="accordion-eleven2" class="accordion accordion-rounded-stylish accordion-bordered">
                                <div class="accordion__item">
                                    <div class="accordion__header collapsed accordion__header--info"
                                        data-bs-toggle="collapse" data-bs-target="#rounded-stylish_collapseTwo">
                                        <span class="accordion__header--icon"></span>
                                        <span class="accordion__header--text">Refund</span>
                                        <span class="accordion__header--indicator"></span>
                                    </div>
                                    <div id="rounded-stylish_collapseTwo" class="collapse accordion__body"
                                        data-parent="#accordion-eleven2">
                                        <div class="accordion__body--text p-0">
                                            <div class="table-responsive tableFixHead">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th width="50px">Currency</th>
                                                            <th>Count</th>
                                                            <th>Amount</th>
                                                            <th>Percentage</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if (count($transactions_summary) > 0)
                                                            @foreach ($transactions_summary['refund'] as $transaction)
                                                                <tr>
                                                                    <td>{{ $transaction->currency }}</td>
                                                                    <td class="text-right">
                                                                        {{ $transaction->refund_count }}</td>
                                                                    <td class="text-right">
                                                                        {{ $transaction->refund_amount }}</td>
                                                                    <td class="text-right">
                                                                        {{ round($transaction->refund_percentage, 2) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td class="text-center" colspan="19">No record found.
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-xxl-6">
                            <div id="accordion-eleven3" class="accordion accordion-rounded-stylish accordion-bordered">
                                <div class="accordion__item">
                                    <div class="accordion__header collapsed accordion__header--info"
                                        data-bs-toggle="collapse" data-bs-target="#rounded-stylish_collapseThree">
                                        <span class="accordion__header--icon"></span>
                                        <span class="accordion__header--text">Flagged</span>
                                        <span class="accordion__header--indicator"></span>
                                    </div>
                                    <div id="rounded-stylish_collapseThree" class="collapse accordion__body"
                                        data-parent="#accordion-eleven3">
                                        <div class="accordion__body--text p-0">
                                            <div class="table-responsive tableFixHead">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th width="50px">Currency</th>
                                                            <th>Count</th>
                                                            <th>Amount</th>
                                                            <th>Percentage</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if (count($transactions_summary) > 0)
                                                            @foreach ($transactions_summary['flagged'] as $transaction)
                                                                <tr>
                                                                    <td>{{ $transaction->currency }}</td>
                                                                    <td class="text-right">
                                                                        {{ $transaction->flagged_count }}</td>
                                                                    <td class="text-right">
                                                                        {{ $transaction->flagged_amount }}</td>
                                                                    <td class="text-right">
                                                                        {{ round($transaction->flagged_percentage, 2) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td class="text-center" colspan="19">No record found.
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });
    </script>
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });

        $(document).ready(function() {
            var height = $(window).height();
            height = height - 300;
            $('.tableFixHead').css('height', height + 'px');
        });
    </script>
@endsection
