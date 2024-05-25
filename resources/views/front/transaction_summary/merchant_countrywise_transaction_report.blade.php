@extends('layouts.user.default')
@section('title')
    Country-wise Transaction Report
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Country-wise Transaction Report
@endsection
@section('content')

    @php
        $cardType = getCardType();
    @endphp

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
                                    <label for="business_name">Success Percentage</label>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <select name="success_percentage_operator" class="form-control"
                                                id="success_percentage_operator">
                                                <option value=">="
                                                    {{ isset($_GET['success_percentage_operator']) && $_GET['success_percentage_operator'] == '>=' ? 'selected' : '' }}>
                                                    >= </option>
                                                <option value="<="
                                                    {{ isset($_GET['success_percentage_operator']) && $_GET['success_percentage_operator'] == '<=' ? 'selected' : '' }}>
                                                    <= </option>
                                                <option value=">"
                                                    {{ isset($_GET['success_percentage_operator']) && $_GET['success_percentage_operator'] == '>' ? 'selected' : '' }}>
                                                    > </option>
                                                <option value="<"
                                                    {{ isset($_GET['success_percentage_operator']) && $_GET['success_percentage_operator'] == '<' ? 'selected' : '' }}>
                                                    < </option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6">
                                            <input name="success_percentage" placeholder="-- Percentage --"
                                                class="form-control" id="success_percentage" type="number"
                                                value="{{ isset($_GET['success_percentage']) && !empty($_GET['success_percentage']) ? $_GET['success_percentage'] : '' }}" />
                                        </div>
                                    </div>
                                    @if ($errors->has('success_percentage'))
                                        <span class="help-block">
                                            <strong class="text-red">{{ $errors->first('success_percentage') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="business_name">Decline Percentage</label>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <select name="declined_percentage_operator" class="form-control"
                                                id="declined_percentage_operator">
                                                <option value=">="
                                                    {{ isset($_GET['declined_percentage_operator']) && $_GET['declined_percentage_operator'] == '>=' ? 'selected' : '' }}>
                                                    >= </option>
                                                <option value="<="
                                                    {{ isset($_GET['declined_percentage_operator']) && $_GET['declined_percentage_operator'] == '<=' ? 'selected' : '' }}>
                                                    <= </option>
                                                <option value=">"
                                                    {{ isset($_GET['declined_percentage_operator']) && $_GET['declined_percentage_operator'] == '>' ? 'selected' : '' }}>
                                                    > </option>
                                                <option value="<"
                                                    {{ isset($_GET['declined_percentage_operator']) && $_GET['declined_percentage_operator'] == '<' ? 'selected' : '' }}>
                                                    < </option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6">
                                            <input name="declined_percentage" placeholder="-- Percentage --"
                                                class="form-control" id="declined_percentage" type="number"
                                                value="{{ isset($_GET['declined_percentage']) && !empty($_GET['declined_percentage']) ? $_GET['declined_percentage'] : '' }}" />
                                        </div>
                                    </div>
                                    @if ($errors->has('declined_percentage'))
                                        <span class="help-block">
                                            <strong class="text-red">{{ $errors->first('declined_percentage') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="status">Country</label>
                                    <select id="country" class="form-control input-rounded select2" name="country"
                                        data-allow-clear="true">
                                        <option value="" selected> -- Select Country -- </option>
                                        @foreach ($countries as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ isset($_GET['country']) && $_GET['country'] == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="status">Card Type</label>
                                    <select class="form-control input-rounded select2" name="card_type">
                                        <option value="" selected> -- Select Card Type -- </option>
                                        @foreach ($cardType as $key => $type)
                                            <option value="{{ $key }}"
                                                {{ isset($_GET['card_type']) && $_GET['card_type'] == $key ? 'selected' : '' }}>
                                                {{ $type }}</option>
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
                        <h4 class="card-title">Country-wise Transaction Report <span class="text-red"> (
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
                            <a href="{{ route('user-countrywise-transactions-report') }}"
                                class="btn btn-primary btn-sm">Reset</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6>Transaction List </h6>
                    </div>

                    <div class="table-responsive tableFixHead">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="width: 250px">Score</th>
                                    <th>Country</th>
                                    <th>Card Type</th>
                                    <th class="text-green">Successful Count<br>(%)</th>
                                    <th class="text-red">Declined Count <br>(%)</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if (count($ArrReasonData) > 0)
                                    <?php $i = 1; ?>
                                    @foreach ($ArrReasonData as $key => $value)
                                        <tr>
                                            @if ($i == 1)
                                                <td rowspan="{{ count($ArrReasonData) }}" style="vertical-align: top;">
                                                    {{ $i }}</td>
                                                <td rowspan="{{ count($ArrReasonData) }}" style="vertical-align: top;">
                                                    <?php
                                                    $total_succ_dec_cnt = ($success_fail_count['success'] ?? 0) + ($success_fail_count['decline'] ?? 0);
                                                    ?>
                                                    @if (isset($success_fail_count['success']))
                                                        <span class="text-green">Successful :
                                                            {{ $success_fail_count['success'] }}
                                                            ({{ $total_succ_dec_cnt > 0 ? round(($success_fail_count['success'] / $total_succ_dec_cnt) * 100, 2) : 0 }}%)</span>
                                                        <br>
                                                    @endif
                                                    @if (isset($success_fail_count['decline']))
                                                        <span class="text-red">Declined :
                                                            {{ $success_fail_count['decline'] }}
                                                            ({{ $total_succ_dec_cnt > 0 ? round(($success_fail_count['decline'] / $total_succ_dec_cnt) * 100, 2) : 0 }}%)</span>
                                                        <br>
                                                    @endif
                                                </td>
                                            @endif

                                            @if (isset($success_fail_cntry_count[$value['country']]))
                                                <td rowspan="{{ $success_fail_cntry_count[$value['country']]['rowsp'] }}">
                                                    {{ $success_fail_cntry_count[$value['country']]['country_name'] ?? ' ' }}
                                                    <br>
                                                    <?php
                                                    $ttl_succ_dec_ctry_cnt = ($success_fail_cntry_count[$value['country']]['success_count'] ?? 0) + ($success_fail_cntry_count[$value['country']]['declined_count'] ?? 0);
                                                    $j = 0;
                                                    ?>
                                                    @if (isset($success_fail_cntry_count[$value['country']]['success_count']))
                                                        <span class="text-green">
                                                            Successful :
                                                            {{ $success_fail_cntry_count[$value['country']]['success_count'] }}
                                                            ({{ $ttl_succ_dec_ctry_cnt > 0 ? round(($success_fail_cntry_count[$value['country']]['success_count'] / $ttl_succ_dec_ctry_cnt) * 100, 2) : 0 }}%)
                                                        </span> ,
                                                    @endif
                                                    @if (isset($success_fail_cntry_count[$value['country']]['declined_count']))
                                                        <span class="text-red">
                                                            Declined :
                                                            {{ $success_fail_cntry_count[$value['country']]['declined_count'] }}
                                                            ({{ $ttl_succ_dec_ctry_cnt > 0 ? round(($success_fail_cntry_count[$value['country']]['declined_count'] / $ttl_succ_dec_ctry_cnt) * 100, 2) : 0 }}%)
                                                        </span>
                                                        <br>
                                                    @endif
                                                </td>
                                                <?php unset($success_fail_cntry_count[$value['country']]); ?>
                                            @endif
                                            <td>{!! $cardType[$value['card_type']] ?? '----' !!}</td>
                                            <td>
                                                <b>{{ $value['success_count'] }}</b>
                                                ({{ round($value['success_percentage'], 2) }}%)
                                            </td>
                                            <td>
                                                <b>{{ $value['declined_count'] }}</b>
                                                ({{ round($value['declined_percentage'], 2) }}%)
                                            </td>

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
