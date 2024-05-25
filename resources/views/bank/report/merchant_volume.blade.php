@extends('layouts.bank.default')
@section('title')
    Merchant Volume
@endsection

@section('breadcrumbTitle')
    Merchant Volume
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

    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="" class="form-dark" id="search-form">
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row">
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
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="resetForm"></i>Clear</button>
                        <button type="submit" class="btn btn-success" id="extraSearch123">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h4 class="mt-50">Merchant Volume</h4>
        </div>
        <div class="col-md-6 text-right">
            <div class="btn-group btn-shadow mr-2">
                <button type="button" class="btn btn-primary bell-link btn-sm" data-bs-toggle="modal"
                    data-bs-target="#searchModal"> <i class="fa fa-search-plus"></i>
                    Advanced Search</button>
                <a href="{{ route('bank-merchant-volume-report') }}" class="btn btn-danger btn-sm"
                    style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
            </div>
            <a href="{{ route('bank-merchant-volume-report-excle', request()->all()) }}"
                class="btn btn-primary btn-sm box-sh" id="ExcelLink">
                <i class="fa fa-download mr-2"></i>
                Export Excel
            </a>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-12 transaction-summary-tbl">
            <div class="card">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-md-9" style="padding: 30px 30px 30px 45px;">
                            <div class="header-title" style="overflow: hidden;">
                                <div class="btn-group btn-group-sm pull-right">
                                    <a href="{{ route('bank-merchant-volume-report', ['for' => 'All']) }}" type="button"
                                        class="btn {{ isset($_GET['for']) && $_GET['for'] == 'All' ? 'btn-danger' : 'btn-primary' }}">All</a>
                                    <a href="{{ route('bank-merchant-volume-report', ['for' => 'Daily']) }}" type="button"
                                        class="btn {{ (!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily') ? 'btn-danger' : 'btn-primary' }}">Daily</a>
                                    <a href="{{ route('bank-merchant-volume-report', ['for' => 'Weekly']) }}"
                                        type="button"
                                        class="btn {{ isset($_GET['for']) && $_GET['for'] == 'Weekly' ? 'btn-danger' : 'btn-primary' }}">Weekly</a>
                                    <a href="{{ route('bank-merchant-volume-report', ['for' => 'Monthly']) }}"
                                        type="button"
                                        class="btn {{ isset($_GET['for']) && $_GET['for'] == 'Monthly' ? 'btn-danger' : 'btn-primary' }}">Monthly</a>
                                </div>
                            </div>
                            <div class="tab-content mt-1">
                                <div class="tab-pane active" id="SUCCESSFUL">
                                    <div class="table-responsive custom-table">
                                        <table class="table table-borderless table-striped">
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
                                                    @foreach ($transactions_summary as $ts)
                                                        <tr>
                                                            <td>{{ $ts['currency'] }}</td>
                                                            <td>{{ $ts['success_count'] }}</td>
                                                            <td>{{ $ts['success_amount'] }}</td>
                                                            <td>{{ round($ts['success_percentage'], 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-white" colspan="4">No record found.
                                                        </td>
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
                                                    <th>Count</th>
                                                    <th>Amount</th>
                                                    <th>Percentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($transactions_summary) > 0)
                                                    @foreach ($transactions_summary as $ts)
                                                        <tr>
                                                            <td>{{ $ts['currency'] }}</td>
                                                            <td>{{ $ts['declined_count'] }}</td>
                                                            <td>{{ number_format($ts['declined_amount'], 2, '.', ',') }}</td>
                                                            <td>{{ round($ts['declined_percentage'], 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-white" colspan="4">No record found.
                                                        </td>
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
                                                    <th>Count</th>
                                                    <th>Amount</th>
                                                    <th>Percentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($transactions_summary) > 0)
                                                    @foreach ($transactions_summary as $ts)
                                                        <tr>
                                                            <td>{{ $ts['currency'] }}</td>
                                                            <td>{{ $ts['chargebacks_count'] }}</td>
                                                            <td>{{ number_format($ts['chargebacks_amount'], 2, '.', ',') }}
                                                            </td>
                                                            <td>{{ round($ts['chargebacks_percentage'], 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-white" colspan="4">No record found.
                                                        </td>
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
                                                    <th>Count</th>
                                                    <th>Amount</th>
                                                    <th>Percentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($transactions_summary) > 0)
                                                    @foreach ($transactions_summary as $ts)
                                                        <tr>
                                                            <td>{{ $ts['currency'] }}</td>
                                                            <td>{{ $ts['refund_count'] }}</td>
                                                            <td>{{ number_format($ts['refund_amount'], 2, '.', ',') }}</td>
                                                            <td>{{ round($ts['refund_percentage'], 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-white" colspan="4">No record found.
                                                        </td>
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
                                                    <th>Count</th>
                                                    <th>Amount</th>
                                                    <th>Percentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($transactions_summary) > 0)
                                                    @foreach ($transactions_summary as $ts)
                                                        <tr>
                                                            <td>{{ $ts['currency'] }}</td>
                                                            <td>{{ $ts['flagged_count'] }}</td>
                                                            <td>{{ number_format($ts['flagged_amount'], 2, '.', ',') }}</td>
                                                            <td>{{ round($ts['flagged_percentage'], 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-white" colspan="4">No record found.
                                                        </td>
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
                                                    <th>Count</th>
                                                    <th>Amount</th>
                                                    <th>Percentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($transactions_summary) > 0)
                                                    @foreach ($transactions_summary as $ts)
                                                        <tr>
                                                            <td>{{ $ts['currency'] }}</td>
                                                            <td>{{ $ts['block_count'] }}</td>
                                                            <td>{{ number_format($ts['block_amount'], 2, '.', ',') }}</td>
                                                            <td>{{ round($ts['block_percentage'], 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-white" colspan="4">No record found.
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <ul class="nav nav-tabs mt-2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#SUCCESSFUL" data-bs-toggle="tab">
                                        Successful
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#DECLINED" data-bs-toggle="tab">
                                        Declined
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#CHARGEBACKS" data-bs-toggle="tab">
                                        Chargebacks
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#REFUND" data-bs-toggle="tab">
                                        Refund
                                    </a>
                                </li>
                            </ul>
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
    </script>
@endsection
