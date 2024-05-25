@extends('layouts.agent.default')
@section('title')
    Card Summary Report
@endsection
@section('breadcrumbTitle')
    <a href="{{ route('rp.dashboard') }}">Dashboard</a> / Card Summary Report
@endsection
@section('content')
    <div class="chatbox">
        <div class="chatbox-close"></div>
        <div class="custom-tab-1">
            <a class="nav-link active" data-bs-toggle="tab" href="#Search">Advanced Search</a>
            <div class="tab-content">
                <div class="tab-pane fade active show" id="Search" role="tabpanel">
                    <form method="" id="search-form">
                        <div class="basic-form">
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="business_name">Select Merchant</label>
                                    <select name="user_id" id="business_name" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled> -- Select Merchant -- </option>
                                        @foreach ($companyName as $key => $value)
                                            <option value="{{ $value->user_id }}"
                                                {{ request()->user_id == $value->user_id ? 'selected' : '' }}>
                                                {{ $value->business_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="text">Card</label>
                                    <select class="form-control select2" name="card_type" id="card_type">
                                        <option selected disabled> -- Select Card -- </option>
                                        @foreach ($card_type as $key => $val)
                                            <option value="{{ $key }}"
                                                {{ isset($_GET['card_type']) && $_GET['card_type'] == $key ? 'selected' : '' }}>
                                                {{ $val }}</option>
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
                                <div class="col-sm-12 mt-4 submit-buttons-commmon">
                                    <button type="submit" class="btn btn-success" id="extraSearch123">Search</button>
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
                <div class="card-header">
                    <div class="mr-auto pr-3">
                        <h4 class="card-title">Card Summary Report</h4>
                    </div>
                    <div class="btn-group mr-2">
                        <button type="button" class="btn btn-warning bell-link btn-sm"> <i class="fa fa-search-plus"></i>
                            Advanced Search</button>
                        <a href="{{ route('rp.rp-card-report') }}" class="btn btn-danger btn-sm">Reset</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-right pull-right mb-3">
                        <div class="btn-group mb-2 btn-group-sm">
                            <a href="{{ route('rp.rp-card-report', ['for' => 'All']) }}" type="button"
                                class="btn {{ isset($_GET['for']) && $_GET['for'] == 'All' ? 'btn-success' : 'btn-warning' }}">All</a>
                            <a href="{{ route('rp.rp-card-report', ['for' => 'Daily']) }}" type="button"
                                class="btn {{ (!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily') ? 'btn-success' : 'btn-warning' }}">Daily</a>
                            <a href="{{ route('rp.rp-card-report', ['for' => 'Weekly']) }}" type="button"
                                class="btn {{ isset($_GET['for']) && $_GET['for'] == 'Weekly' ? 'btn-success' : 'btn-warning' }}">Weekly</a>
                            <a href="{{ route('rp.rp-card-report', ['for' => 'Monthly']) }}" type="button"
                                class="btn {{ isset($_GET['for']) && $_GET['for'] == 'Monthly' ? 'btn-success' : 'btn-warning' }}">Monthly</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th colspan="3" class="text-center text-success">SUCCESSFUL</th>
                                    <th colspan="3" class="text-center text-danger">DECLINED</th>
                                    <th colspan="3" class="text-center text-info">CHARGEBACKS</th>
                                    <th colspan="3" class="text-center text-info">REFUND</th>
                                    <th colspan="3" class="text-center text-info">SUSPICIOUS</th>
                                    <th colspan="3" class="text-center text-info">BLOCK</th>
                                </tr>
                                <tr>
                                    <th width="50px">Card</th>
                                    <th>Count</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                    <th>Count</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                    <th>Count</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
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
                                    @foreach ($transactions_summary as $transaction)
                                        <tr>
                                            <td>{{ $transaction->card_type > 0 ? $card_type[$transaction->card_type] : 'N/A' }}
                                            </td>

                                            <td class="text-right">{{ $transaction->success_count }}</td>
                                            <td class="text-right">{{ $transaction->success_amount }}</td>
                                            <td class="text-right">{{ round($transaction->success_percentage, 2) }}</td>

                                            <td class="text-right">{{ $transaction->declined_count }}</td>
                                            <td class="text-right">
                                                {{ number_format($transaction->declined_amount, 2, '.', ',') }}
                                            </td>
                                            <td class="text-right">{{ round($transaction->declined_percentage, 2) }}</td>

                                            <td class="text-right">{{ $transaction->chargebacks_count }}</td>
                                            <td class="text-right">
                                                {{ number_format($transaction->chargebacks_amount, 2, '.', ',') }}
                                            </td>
                                            <td class="text-right">{{ round($transaction->chargebacks_percentage, 2) }}
                                            </td>

                                            <td class="text-right">{{ $transaction->refund_count }}</td>
                                            <td class="text-right">
                                                {{ number_format($transaction->refund_amount, 2, '.', ',') }}</td>
                                            <td class="text-right">{{ round($transaction->refund_percentage, 2) }}</td>

                                            <td class="text-right">{{ $transaction->flagged_count }}</td>
                                            <td class="text-right">
                                                {{ number_format($transaction->flagged_amount, 2, '.', ',') }}
                                            </td>
                                            <td class="text-right">{{ round($transaction->flagged_percentage, 2) }}</td>

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
                </div>
            </div>
        </div>
    </div>
@endsection
