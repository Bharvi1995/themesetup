@extends('layouts.user.default')
@section('title')
    Card Summary Report
@endsection

@section('breadcrumbTitle')
    Card summary Report
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
                        <h4 class="card-title">Card Summary Report</h4>
                    </div>
                    <div>
                        <div class="btn-group mr-2">
                            <button type="button" class="btn btn-info bell-link btn-sm" data-bs-toggle="modal"
                                data-target="#searchModal"> <i class="fa fa-search-plus"></i>
                                Advanced Search</button>
                            <a href="{{ route('user-card-summary-report') }}" class="btn btn-primary btn-sm">Reset</a>
                        </div>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div class="d-flex justify-content-between align-items-right pull-right mb-3">
                        <div class="btn-group mb-2 btn-group-sm">
                            <a href="{{ route('user-card-summary-report', ['for' => 'All']) }}" type="button"
                                class="btn {{ isset($_GET['for']) && $_GET['for'] == 'All' ? 'btn-success' : 'btn-primary' }}">All</a>
                            <a href="{{ route('user-card-summary-report', ['for' => 'Daily']) }}" type="button"
                                class="btn {{ (!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily') ? 'btn-success' : 'btn-primary' }}">Daily</a>
                            <a href="{{ route('user-card-summary-report', ['for' => 'Weekly']) }}" type="button"
                                class="btn {{ isset($_GET['for']) && $_GET['for'] == 'Weekly' ? 'btn-success' : 'btn-primary' }}">Weekly</a>
                            <a href="{{ route('user-card-summary-report', ['for' => 'Monthly']) }}" type="button"
                                class="btn {{ isset($_GET['for']) && $_GET['for'] == 'Monthly' ? 'btn-success' : 'btn-primary' }}">Monthly</a>
                        </div>
                    </div>
                    <div class="table-responsive tableFixHead">
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
                                            <td>{{ $transaction['card_type'] > 0 ? $card_type[$transaction['card_type']] : 'N/A' }}
                                            </td>

                                            <td class="text-right">{{ $transaction['success_count'] }}</td>
                                            <td class="text-right">{{ $transaction['success_amount'] }}</td>
                                            <td class="text-right">{{ round($transaction['success_percentage'], 2) }}</td>

                                            <td class="text-right">{{ $transaction['declined_count'] }}</td>
                                            <td class="text-right">
                                                {{ number_format($transaction['declined_amount'], 2, '.', ',') }}
                                            </td>
                                            <td class="text-right">{{ round($transaction['declined_percentage'], 2) }}</td>

                                            <td class="text-right">{{ $transaction['chargebacks_count'] }}</td>
                                            <td class="text-right">
                                                {{ number_format($transaction['chargebacks_amount'], 2, '.', ',') }}
                                            </td>
                                            <td class="text-right">{{ round($transaction['chargebacks_percentage'], 2) }}
                                            </td>

                                            <td class="text-right">{{ $transaction['refund_count'] }}</td>
                                            <td class="text-right">
                                                {{ number_format($transaction['refund_amount'], 2, '.', ',') }}</td>
                                            <td class="text-right">{{ round($transaction['refund_percentage'], 2) }}</td>

                                            <td class="text-right">{{ $transaction['flagged_count'] }}</td>
                                            <td class="text-right">
                                                {{ number_format($transaction['flagged_amount'], 2, '.', ',') }}
                                            </td>
                                            <td class="text-right">{{ round($transaction['flagged_percentage'], 2) }}</td>

                                            <td class="text-right">{{ $transaction['block_count'] }}</td>
                                            <td class="text-right">
                                                {{ number_format($transaction['block_amount'], 2, '.', ',') }}
                                            </td>
                                            <td class="text-right">{{ round($transaction['block_percentage'], 2) }}</td>
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
