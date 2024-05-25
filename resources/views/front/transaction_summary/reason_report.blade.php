@extends('layouts.user.default')
@section('title')
    Aggregated Declined Transactions Reasons
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Aggregated Declined Transactions Reasons
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
                                    <label for="text">Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="start_date"
                                            placeholder="Start Date" id="start_date"
                                            value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? date('d-m-Y', strtotime($_GET['start_date'])) : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="end_date">End Date</label>
                                    <div class="date-input">
                                        <input type="text" id="end_date" class="form-control" placeholder="End Date"
                                            name="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? date('d-m-Y', strtotime($_GET['end_date'])) : '' }}"
                                            autocomplete="off">
                                    </div>
                                    @if ($errors->has('end_date'))
                                        <span class="help-block">
                                            <strong class="text-red">{{ $errors->first('end_date') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="status">Currency</label>
                                    <select class="form-control input-rounded select2" name="currency">
                                        <option value="" selected> -- Select Currency -- </option>
                                        @foreach (config('currency.three_letter') as $key => $currency)
                                            <option value="{{ $currency }}"
                                                {{ isset($_GET['currency']) && $_GET['currency'] == $key ? 'selected' : '' }}>
                                                {{ $currency }}</option>
                                        @endforeach
                                    </select>
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
                        <h4 class="card-title">Aggregated Declined Transactions Reasons <span class="text-red"> (
                                {{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? date('d-m-Y', strtotime($_GET['start_date'])) : date('d-m-Y') }}
                                -
                                {{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? date('d-m-Y', strtotime($_GET['end_date'])) : date('d-m-Y') }}
                                )</span></h4>
                    </div>
                    <div>
                        <div class="btn-group mr-2">
                            <button type="button" class="btn btn-info bell-link btn-sm" data-toggle="modal"
                                data-target="#searchModal"> <i class="fa fa-search-plus"></i>
                                Advanced Search</button>
                            <a href="{{ route('transactions-reason-report') }}" class="btn btn-primary btn-sm">Reset</a>
                        </div>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6>Reason List </h6>
                    </div>

                    <div class="table-responsive tableFixHead">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="width: 250px">Score</th>
                                    <th>Reason</th>
                                    <th>Card Type</th>
                                    <th>Currency</th>
                                    <th>Transaction Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $cardType = getCardType();
                                @endphp
                                @if (count($ArrReasonData) > 0)
                                    <?php $i = 1; ?>
                                    @foreach ($ArrReasonData as $key => $value)
                                        <tr>
                                            @if ($i == 1)
                                                <td rowspan="{{ count($ArrReasonData) }}" style="vertical-align: top;">
                                                    {{ $i }}</td>
                                                <td rowspan="{{ count($ArrReasonData) }}" style="vertical-align: top;">
                                                    @foreach ($success_fail_count as $stat => $cnt)
                                                        <?php
                                                        $total_succ_dec_cnt = ($success_fail_count[0] ?? 0) + ($success_fail_count[1] ?? 0);
                                                        ?>
                                                        @if ($stat == 1)
                                                            <span class="text-green">Successful : {{ $cnt }}
                                                                ({{ $total_succ_dec_cnt > 0 ? round(($cnt / $total_succ_dec_cnt) * 100, 2) : 0 }}%)
                                                            </span>
                                                            <br>
                                                        @elseif($stat == 0)
                                                            <span class="text-red">Declined : {{ $cnt }}
                                                                ({{ $total_succ_dec_cnt > 0 ? round(($cnt / $total_succ_dec_cnt) * 100, 2) : 0 }}%)</span>
                                                            </span> <br>
                                                        @elseif($stat == 5)
                                                            <span class="text-primary">Blocked : {{ $cnt }}</span>
                                                            <br>
                                                        @endif
                                                    @endforeach
                                                </td>
                                            @endif
                                            <td>{{ $value['reason'] }}</td>
                                            <td>{!! $cardType[$value['card_type']] ?? 'Unknown' !!}</td>
                                            <td>{{ $value['currency'] }}</td>
                                            <td class="text-center">{{ $value['transaction_count'] }} </td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="6">No record found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!--- Pagination -->
                    {{-- @if (count($data) > 0)
                <div class="d-flex clPagination">
                    {!! $data->appends($_GET)->links() !!}
                </div>
                @endif --}}
                    <!--- Pagination -->
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
    <script type="text/javascript">
        $('body').on('click', '.start-flag-mark', function(event) {
            var myids = $('#getIdValue').val();
            event.preventDefault();

            if (confirm('Proceed for Suspicious?')) {

                var id = [];
                var status = '1';

                $('.multidelete:checked').each(function() {
                    id.push($(this).val());
                });

                if (id.length > 0) {
                    redirectURL = $(this).prop('href') + '&selected=yes&ids=' + id;
                } else {
                    redirectURL = $(this).prop('href') + '&selected=yes&ids=' + myids;
                }
                console.log(redirectURL);

                window.location = redirectURL;
            }
        });
        $(document).ready(function() {
            var height = $(window).height();
            height = height - 300;
            $('.tableFixHead').css('height', height + 'px');
        });
    </script>
@endsection
