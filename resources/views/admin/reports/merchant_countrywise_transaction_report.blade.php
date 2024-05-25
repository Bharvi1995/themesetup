@extends('layouts.admin.default')
@section('title')
    Merchant Country-wise Transaction Report
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Merchant Country-wise Transaction Report
@endsection
@section('content')

    @php
        $cardType = getCardType();
    @endphp

    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <form method="" id="search-form" class="form-dark">
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row ">
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
                                    <label for="business_name">Select Merchant</label>
                                    <select name="user_id" id="business_name" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected value=""> -- Select Merchant -- </option>
                                        @foreach ($businessName as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ isset($_GET['user_id']) && $_GET['user_id'] == $key ? 'selected' : '' }}>
                                                {{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('user_id'))
                                        <span class="help-block">
                                            <strong class="text-red">{{ $errors->first('user_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="status">MID</label>
                                    <select class="form-control input-rounded select2" name="payment_gateway_id">
                                        <option value="" selected> -- MID -- </option>
                                        @foreach ($payment_gateway_id as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($_GET['payment_gateway_id']) && $_GET['payment_gateway_id'] == $value->id ? 'selected' : '' }}>
                                                {{ $value->bank_name }}</option>
                                        @endforeach
                                    </select>
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
                        <button type="button" class="btn btn-danger" id="resetForm"></i>Clear</button>
                        <button type="submit" class="btn btn-success" id="extraSearch123">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Merchant Country-wise Transaction Report <span class="text-red"> (
                                {{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? date('d-m-Y', strtotime($_GET['start_date'])) : date('m-d-Y') }}
                                -
                                {{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? date('d-m-Y', strtotime($_GET['end_date'])) : date('m-d-Y') }}
                                )</span></h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#searchModal"> Advanced
                                Search &nbsp; <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg></button>
                            <a href="{{ route('merchant-countrywise-transactions-report') }}"
                                style="border-radius: 0px 5px 5px 0px !important;" class="btn btn-danger btn-sm">Reset</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">

                    <div class="table-responsive custom-table tableFixHead">
                        <table class="table table-striped table-borderless">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="width: 250px">Merchant Name</th>
                                    <th>Country</th>
                                    <th>Card Type</th>
                                    <th class="text-green">Successful Count<br>(%)</th>
                                    <th class="text-red">Declined Count <br>(%)</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if (count($data) > 0)
                                    @foreach ($data as $key => $value)
                                        <?php
                                        $total_succ_dec_cnt = ($success_fail_count[$key]['success'] ?? 0) + ($success_fail_count[$key]['decline'] ?? 0);
                                        $i = 0;
                                        ?>
                                        <tr>
                                            @if ($i == 0)
                                                <td rowspan="{{ $diff_count[$key] }}" style="vertical-align: top;">
                                                    {{ $loop->iteration }}</td>
                                                <td rowspan="{{ $diff_count[$key] }}" style="vertical-align: top;">
                                                    <b>{{ $key }}</b> <br><br>
                                                    @if (isset($success_fail_count[$key]['success']))
                                                        <span class="text-green">Successful :
                                                            {{ $success_fail_count[$key]['success'] }}
                                                            ({{ $total_succ_dec_cnt > 0 ? round(($success_fail_count[$key]['success'] / $total_succ_dec_cnt) * 100, 2) : 0 }}%)
                                                        </span>
                                                        <br>
                                                    @endif
                                                    @if (isset($success_fail_count[$key]['decline']))
                                                        <span class="text-red">Declined :
                                                            {{ $success_fail_count[$key]['decline'] }}
                                                            ({{ $total_succ_dec_cnt > 0 ? round(($success_fail_count[$key]['decline'] / $total_succ_dec_cnt) * 100, 2) : 0 }}%)</span>
                                                        <br>
                                                    @endif
                                                </td>
                                            @endif
                                            @foreach ($value as $ky => $val)
                                                <?php
                                                $ttl_succ_dec_ctry_cnt = ($success_fail_cntry_count[$key][$ky]['success'] ?? 0) + ($success_fail_cntry_count[$key][$ky]['decline'] ?? 0);
                                                $j = 0;
                                                ?>
                                                @if ($j == 0)
                                                    <td rowspan="{{ $val->count() }}">
                                                        {{ $ky }} <br>
                                                        @if (isset($success_fail_cntry_count[$key][$ky]['success']))
                                                            <span class="text-green">Successful :
                                                                {{ $success_fail_cntry_count[$key][$ky]['success'] }}
                                                                ({{ $ttl_succ_dec_ctry_cnt > 0 ? round(($success_fail_cntry_count[$key][$ky]['success'] / $ttl_succ_dec_ctry_cnt) * 100, 2) : 0 }}%)
                                                            </span>
                                                            ,
                                                        @endif
                                                        @if (isset($success_fail_cntry_count[$key][$ky]['decline']))
                                                            <span class="text-red">Declined :
                                                                {{ $success_fail_cntry_count[$key][$ky]['decline'] }}
                                                                ({{ $ttl_succ_dec_ctry_cnt > 0 ? round(($success_fail_cntry_count[$key][$ky]['decline'] / $ttl_succ_dec_ctry_cnt) * 100, 2) : 0 }}%)</span>
                                                            <br>
                                                        @endif
                                                    </td>
                                                @endif
                                                @foreach ($val as $k => $vl)
                                                    <?php $rowspan3 = $vl->count();
                                                    $ki = 0;
                                                    $k = (int) $k; ?>
                                                    @if ($ki == 0)
                                                        <td rowspan="{{ $rowspan3 }}">{!! $cardType[$k] ?? '----' !!}</td>
                                                    @endif
                                                    @foreach ($vl as $v)
                                                        <td> {{--  class="text-green" --}}
                                                            <b>{{ $v->success_count }}</b>
                                                            ({{ round($v->success_percentage, 2) }}%)
                                                        </td>
                                                        <td> {{--  class="text-red" --}}
                                                            <b>{{ $v->declined_count }}</b>
                                                            ({{ round($v->declined_percentage, 2) }}%)
                                                        </td>
                                        </tr>
                                    @endforeach

                                    <?php $ki++; ?>
                                @endforeach

                                <?php $j++; ?>
                                @endforeach
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
