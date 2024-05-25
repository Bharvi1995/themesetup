@extends('layouts.admin.default')
@section('title')
    Transaction Summary Report
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Auto Suspicious Report
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
                                    <div class="common-check-main">
                                        <label class="custom-control form-check-label mb-0">
                                            <input class="form-check-input" name="include_card" id="include_card"
                                                type="checkbox" value="yes"
                                                {{ isset($_GET['include_card']) && $_GET['include_card'] == 'yes' ? 'checked' : '' }}>
                                            Include Card?
                                            <span class="overflow-control-indicator"></span>
                                            <span class="overflow-control-description"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <input type="text" class="form-control input-rounded"
                                        placeholder="Number of transactions per card" name="nos_card"
                                        value="{{ isset($_GET['nos_card']) && $_GET['nos_card'] != '' ? $_GET['nos_card'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <div class="common-check-main">
                                        <label class="custom-control form-check-label mb-0">
                                            <input class="form-check-input" id="include_email" name="include_email"
                                                type="checkbox" value="yes"
                                                {{ isset($_GET['include_email']) && $_GET['include_email'] == 'yes' ? 'checked' : '' }}>
                                            Include Email?
                                            <span class="overflow-control-indicator"></span>
                                            <span class="overflow-control-description"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <input type="text" class="form-control input-rounded"
                                        placeholder="Number of transactions per email" name="nos_email"
                                        value="{{ isset($_GET['nos_email']) && $_GET['nos_email'] != '' ? $_GET['nos_email'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="text">Select Date</label>
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
                                        <input type="text" id="end_date" class="form-control"
                                            data-multiple-dates-separator=" - " data-language="en" placeholder="End Date"
                                            name="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? date('d-m-Y', strtotime($_GET['end_date'])) : '' }}"
                                            autocomplete="off">
                                    </div>
                                    @if ($errors->has('end_date'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('end_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="business_name">Select Merchant</label>
                                    <select name="user_id" id="business_name" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled> -- Select Merchant -- </option>
                                        @foreach ($businessName as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ isset($_GET['user_id']) && $_GET['user_id'] == $key ? 'selected' : '' }}>
                                                {{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('user_id'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('user_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="status">MID</label>
                                    <select class="form-control input-rounded select2" name="payment_gateway_id">
                                        <option disabled selected> -- MID -- </option>
                                        @foreach ($payment_gateway_id as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($_GET['payment_gateway_id']) && $_GET['payment_gateway_id'] == $value->id ? 'selected' : '' }}>
                                                {{ $value->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="country">Country</label>
                                    <select name="country" id="country" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled> -- Select country -- </option>
                                        @foreach (getCountry() as $key => $country)
                                            <option value="{{ $key }}"
                                                {{ isset($_GET['country']) && $_GET['country'] == $key ? 'selected' : '' }}>
                                                {{ $country }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('country'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('country') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="amount_greater_than">Amount greater than</label>
                                    <input type="text" class="form-control input-rounded"
                                        placeholder="Amount greater than" name="greater_then"
                                        value="{{ isset($_GET['greater_then']) && $_GET['greater_then'] != '' ? $_GET['greater_then'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="amount_less_than">Amount less than</label>
                                    <input type="text" class="form-control input-rounded"
                                        placeholder="Amount less than" name="less_then"
                                        value="{{ isset($_GET['less_then']) && $_GET['less_then'] != '' ? $_GET['less_then'] : '' }}">
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
    @if (count($data) > 0)
        <div class="row">
            <div class="col-lg-12">
                @if (auth()->guard('admin')->user()->can(['export-auto-suspicious-report']))
                    <a href="javascript:;" data-link="{{ route('admin.auto_suspicious.export', request()->all()) }}"
                        data-filename="SuspiciousTransaction_Excel_" class="me-2 btn btn-primary btn-sm "
                        id="ExcelLink"><i class="fa fa-download"></i> Export Excel </a>
                @endif

                @if (auth()->guard('admin')->user()->can(['make-auto-suspicious']))
                    <a href="{{ route('auto-suspicious.startFlag', ['flagged_by' => 'testpay']) }}"
                        class=" btn me-2 btn-danger btn-sm start-flag-mark"> Suspicious By {{ config('app.name') }} </a>
                    <a href="{{ route('auto-suspicious.startFlag', ['flagged_by' => 'bank']) }}"
                        class=" btn btn-danger btn-sm start-flag-mark"> Suspicious By Bank </a>
                @endif
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Auto Suspicious Report</h4>
                    </div>
                    <div>
                        <form id="noListform" method="GET" style="float: left;" class="form-dark">
                            <select class="form-control-sm form-control" name="noList" id="noList">
                                <option value="">--No of Records--</option>
                                <option value="30" {{ request()->get('noList') == '30' ? 'selected' : '' }}>30
                                </option>
                                <option value="50" {{ request()->get('noList') == '50' ? 'selected' : '' }}>50
                                </option>
                                <option value="100" {{ request()->get('noList') == '100' ? 'selected' : '' }}>100
                                </option>
                            </select>
                        </form>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm ms-1" data-bs-toggle="modal"
                                data-bs-target="#searchModal">
                                Advanced
                                Search &nbsp; <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg>
                            </button>
                            <a href="{{ route('auto-suspicious-report') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    @php
                        $getids = implode(',', $arrId);
                    @endphp
                    <div class="table-responsive custom-table">
                        <table class="table table-striped table-borderless">
                            <thead>
                                <tr>
                                    <th class="width50">
                                        <div class="common-check-main">
                                            <label class="custom-control form-check-label mb-0">
                                                <input class="form-check-input" id="checkAll" type="checkbox"
                                                    required="">
                                                <span class="overflow-control-indicator"></span>
                                                <span class="overflow-control-description"></span>
                                                <input type="hidden" name="getIdValue" id="getIdValue"
                                                    value="{{ $getids }}">
                                            </label>
                                        </div>
                                    </th>
                                    <th>Order No</th>
                                    <th>Company Name</th>
                                    <th>MID</th>
                                    <th>Email</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th>Transaction date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $value)
                                    <tr>
                                        <td>
                                            <label class="custom-control form-check-label">
                                                <input type="checkbox" class="form-check-input multidelete"
                                                    name="multicheckmail[]" id="customCheckBox_{{ $value->id }}"
                                                    value="{{ $value->id }}" required="">
                                                <span class="overflow-control-indicator"></span>
                                                <span class="overflow-control-description"></span>
                                            </label>
                                        </td>
                                        <td>{{ $value->order_id }}</td>
                                        <td>{{ $value->business_name }}</td>
                                        <td class="text-right">{{ $value->bank_name }} </td>
                                        <td>{{ $value->email }} <br />
                                            <label class="light badge badge-sm badge-danger">{{ $value->card_no }}</label>
                                        </td>
                                        <td class="text-right">{{ $value->amount }}</td>
                                        <td class="text-right">{{ $value->currency }}</td>
                                        <td class="text-right">{{ date('Y-m-d', strtotime($value->transaction_date)) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer">
                    @if (!empty($data) && $data->count())
                        <div class="row">
                            <div class="col-md-8">
                                {!! $data->appends($_GET)->links() !!}
                            </div>
                            <div class="col-md-4 text-right">
                                Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of total
                                {{ $data->total() }}
                                entries
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find(
                "input[type=text], input[type=email], input[type=number],input[type=checkbox], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });
    </script>
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
    </script>
@endsection
