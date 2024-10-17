@extends('layouts.admin.default')
@section('title')
    All Transactions
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">All Transactions</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">All Transactions</h6>
    </nav>
@endsection


@section('content')
    @include('requestDate')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog modal-lg-scrollable" role="document">
            <form method="" id="search-form" class="form-dark">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row ">
                                <div class="form-group col-lg-6">
                                    <label for="name">Card No.</label>
                                    <input type="number" class="form-control" placeholder="Enter here..." name="card_no"
                                        value="{{ $_GET['card_no'] ?? '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" placeholder="Enter here..." name="email"
                                        value="{{ isset($_GET['email']) && $_GET['email'] != '' ? $_GET['email'] : '' }}">
                                </div>
                                @if (auth()->guard('admin')->user()->can(['company-name']))
                                    <div class="form-group col-lg-6">
                                        <label>Business Name</label>
                                        <select name="user_id" class="form-control select2" data-width="100%">
                                            <option disabled selected value="first"> -- Select Business Name -- </option>
                                            @foreach ($companyList as $item)
                                                <option value="{{ $item->user_id }}"
                                                    {{ request()->user_id == $item->user_id ? 'selected' : '' }}>
                                                    {{ $item->business_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="form-group col-lg-6">
                                    <label for="name">First Name</label>
                                    <input type="text" class="form-control " placeholder="Enter here..."
                                        name="first_name"
                                        value="{{ isset($_GET['first_name']) && $_GET['first_name'] != '' ? $_GET['first_name'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="name">Last Name</label>
                                    <input type="text" class="form-control" placeholder="Enter here..." name="last_name"
                                        value="{{ isset($_GET['last_name']) && $_GET['last_name'] != '' ? $_GET['last_name'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="text">Created Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="start_date"
                                            placeholder="Enter here..." id="start_date"
                                            value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="text">Created End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="end_date"
                                            placeholder="Enter here..." id="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="text">Transaction Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="transaction_start_date"
                                            placeholder="Enter here..." id="transaction_start_date"
                                            value="{{ isset($_GET['transaction_start_date']) && $_GET['transaction_start_date'] != '' ? $_GET['transaction_start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="text">Transaction End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="transaction_end_date"
                                            placeholder="Enter here..." id="transaction_end_date"
                                            value="{{ isset($_GET['transaction_end_date']) && $_GET['transaction_end_date'] != '' ? $_GET['transaction_end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Status</label>
                                    <select name="status" class="form-control select2" data-width="100%">
                                        <option disabled selected value="first"> -- Select Status -- </option>
                                        <option value="1"
                                            {{ isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '' }}>
                                            Success</option>
                                        <option value="2"
                                            {{ isset($_GET['status']) && $_GET['status'] == '2' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="5"
                                            {{ isset($_GET['status']) && $_GET['status'] == '5' ? 'selected' : '' }}>
                                            Blocked</option>
                                        <option value="7"
                                            {{ isset($_GET['status']) && $_GET['status'] == '7' ? 'selected' : '' }}>3ds
                                            Redirect</option>
                                        <option value="0"
                                            {{ isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '' }}>
                                            Declined</option>
                                    </select>
                                </div>
                                @if (auth()->guard('admin')->user()->can(['company-name']))
                                    <div class="form-group col-lg-6">
                                        <label>MID</label>
                                        <select class="form-control input-rounded select2" name="payment_gateway_id">
                                            <option disabled selected value="first"> -- Select MID -- </option>
                                            @foreach ($payment_gateway_id as $key => $value)
                                                <option value="{{ $value->id }}"
                                                    {{ isset($_GET['payment_gateway_id']) && $_GET['payment_gateway_id'] == $value->id ? 'selected' : '' }}>
                                                    {{ $value->bank_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="form-group col-lg-6">
                                    <label>Card Type</label>
                                    <select class="form-control input-rounded select2" name="card_type" id="">
                                        <option selected disabled value="first"> -- Select Card Type -- </option>
                                        <option value="1"
                                            {{ isset($_GET['card_type']) && $_GET['card_type'] == '1' ? 'selected' : '' }}>
                                            Amex</option>
                                        <option value="2"
                                            {{ isset($_GET['card_type']) && $_GET['card_type'] == '2' ? 'selected' : '' }}>
                                            Visa</option>
                                        <option value="3"
                                            {{ isset($_GET['card_type']) && $_GET['card_type'] == '3' ? 'selected' : '' }}>
                                            Mastercard</option>
                                        <option value="4"
                                            {{ isset($_GET['card_type']) && $_GET['card_type'] == '4' ? 'selected' : '' }}>
                                            Discover</option>
                                        <option value="5"
                                            {{ isset($_GET['card_type']) && $_GET['card_type'] == '5' ? 'selected' : '' }}>
                                            JCB</option>
                                        <option value="6"
                                            {{ isset($_GET['card_type']) && $_GET['card_type'] == '6' ? 'selected' : '' }}>
                                            Maestro</option>
                                        <option value="7"
                                            {{ isset($_GET['card_type']) && $_GET['card_type'] == '7' ? 'selected' : '' }}>
                                            Switch</option>
                                        <option value="8"
                                            {{ isset($_GET['card_type']) && $_GET['card_type'] == '8' ? 'selected' : '' }}>
                                            Solo</option>
                                        <option value="8"
                                            {{ isset($_GET['card_type']) && $_GET['card_type'] == '8' ? 'selected' : '' }}>
                                            UniounPay</option>
                                        <option value="0"
                                            {{ isset($_GET['card_type']) && $_GET['card_type'] == '8' ? 'selected' : '' }}>
                                            Unknown</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Order no.</label>
                                    <input type="text" class="form-control" placeholder="Enter here..."
                                        name="order_id" value="{{ $_GET['order_id'] ?? '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Currency</label>
                                    <select class="form-control select2" name="currency" id="currency">
                                        <option selected disabled value="first"> -- Select Currency -- </option>
                                        @foreach (config('currency.three_letter') as $key => $currency)
                                            <option value="{{ $currency }}"
                                                {{ isset($_GET['currency']) && $_GET['currency'] == $key ? 'selected' : '' }}>
                                                {{ $currency }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Country</label>
                                    <select name="country" id="country" class="form-control select2">
                                        <option selected disabled value="first"> -- Select country -- </option>
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
                                    <label for=>Amount greater than</label>
                                    <input type="text" class="form-control input-rounded" placeholder="Enter here..."
                                        name="greater_then"
                                        value="{{ isset($_GET['greater_then']) && $_GET['greater_then'] != '' ? $_GET['greater_then'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="amount_less_than">Amount less than</label>
                                    <input type="text" class="form-control input-rounded" placeholder="Enter here..."
                                        name="less_then"
                                        value="{{ isset($_GET['less_then']) && $_GET['less_then'] != '' ? $_GET['less_then'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="session_id">Session ID</label>
                                    <input type="text" class="form-control input-rounded" placeholder="Enter here..."
                                        name="session_id"
                                        value="{{ isset($_GET['session_id']) && $_GET['session_id'] != '' ? $_GET['session_id'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="customer_order_id">Customer order ID</label>
                                    <input type="text" class="form-control" placeholder="Enter here..."
                                        name="customer_order_id"
                                        value="{{ isset($_GET['customer_order_id']) && $_GET['customer_order_id'] != '' ? $_GET['customer_order_id'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="gateway_id">Gateway ID</label>
                                    <input type="text" class="form-control input-rounded" placeholder="Enter here..."
                                        name="gateway_id"
                                        value="{{ isset($_GET['gateway_id']) && $_GET['gateway_id'] != '' ? $_GET['gateway_id'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Request From</label>
                                    <select name="is_request_from_vt" class="form-control input-rounded select2">
                                        <option selected disabled value="first"> -- Request From -- </option>
                                        <option value="iFrame"
                                            {{ isset($_GET['is_request_from_vt']) && $_GET['is_request_from_vt'] == 'iFrame' ? 'selected' : '' }}>
                                            iFrame</option>
                                        <option value="API"
                                            {{ isset($_GET['is_request_from_vt']) && $_GET['is_request_from_vt'] == 'API' ? 'selected' : '' }}>
                                            API</option>
                                        <option value="API V2"
                                            {{ isset($_GET['is_request_from_vt']) && $_GET['is_request_from_vt'] == 'API V2' ? 'selected' : '' }}>
                                            API V2</option>
                                        <option value="Pay Button"
                                            {{ isset($_GET['is_request_from_vt']) && $_GET['is_request_from_vt'] == 'Pay Button' ? 'selected' : '' }}>
                                            Pay Button</option>
                                        <option value="WEBHOOK"
                                            {{ isset($_GET['is_request_from_vt']) && $_GET['is_request_from_vt'] == 'WEBHOOK' ? 'selected' : '' }}>
                                            WEBHOOK</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="reason">Reason</label>
                                    <input type="text" class="form-control input-rounded" placeholder="Enter here..."
                                        name="reason"
                                        value="{{ isset($_GET['reason']) && $_GET['reason'] != '' ? $_GET['reason'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="reason">Card FT/WTL</label>
                                    <select class="form-control" name="is_white_label">
                                        <option value="">-- Select Type --</option>
                                        <option value="0"
                                            {{ isset($_GET['is_white_label']) && $_GET['is_white_label'] == '0' ? 'selected' : '' }}>
                                            FT</option>
                                        <option value="1"
                                            {{ isset($_GET['is_white_label']) && $_GET['is_white_label'] == '1' ? 'selected' : '' }}>
                                            WTL</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="resetForm"></i>Clear</button>
                        <button type="submit" class="btn btn-success" id="extraSearch123">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-7 mb-2 text-right">
        </div>
        <div class="col-lg-5 mb-2 text-right">
            <?php
            $url = Request::fullUrl();
            $parsedUrl = parse_url($url);
            $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
            $subQueryString = $query != '' ? $query . '&type=xlsx' : '';
            ?>

            @if (auth()->guard('admin')->user()->can(['export-all-transaction']))
                @if (!empty($subQueryString))
                    <a href="{{ route('all-admin-transactions-csv-export', [$subQueryString]) }}"
                        class="btn btn-outline-primary btn-shadow"> Export Excel </a>
                @else
                    <a href="{{ route('all-admin-transactions-csv-export') }}" class="btn btn-outline-primary btn-shadow"
                        id="ExcelLink">
                        Export Excel
                    </a>
                @endif
            @endif
            @if (auth()->guard('admin')->user()->can(['delete-all-transaction']))
                <button type="button" class="btn btn-outline-danger" id="deleteSelected"
                    data-link="{{ route('delete-transaction') }}"> Delete Selected
                    Record</button>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">All Transactions</h4>
                    </div>
                    <div class="card-header-toolbar align-items-center">
                        <div class="btn-group mr-2">
                        <!-- @if (auth()->guard('admin')->user()->can(['update-all-transaction']))
                            <div class="btn-group form-dark">
                                <select class="form-control  form-control-sm" name="transaction_status" id="transaction_status"
                                    style="border-radius: 5px 0px 0px 5px !important;">
                                    <option selected disabled> -- Select Status -- </option>
                                    <option value="chargebacks">Chargebacks</option>
                                    <option value="refund">Refund</option>
                                    <option value="flagged">Suspicious</option>
                                    <option value="declined">Declined</option>
                                </select>
                                <button type="button" class="btn btn-primary btn-sm" id="transactionMove"
                                    data-change-transaction-status="{{ route('change-transaction-status') }}"
                                    style="border-radius: 0px 5px 5px 0px !important;">Move To</button>
                            </div>
                        @endif -->

                        <form id="noListform" method="GET" style="float:left;" class="me-50 form-dark">
                            <select class="form-control form-control-sm" name="noList" id="noList">
                                <option value="">No of Records</option>
                                <option value="30" {{ request()->get('noList') == '30' ? 'selected' : '' }}>30
                                </option>
                                <option value="50" {{ request()->get('noList') == '50' ? 'selected' : '' }}>50
                                </option>
                                <option value="100" {{ request()->get('noList') == '100' ? 'selected' : '' }}>100
                                </option>
                            </select>
                        </form>
                            <button class="btn btn-primary btn-sm searchModelOpen" data-bs-toggle="modal"
                                data-bs-target="#searchModal">
                                Advance Search &nbsp;
                                <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg>
                            </button>
                            <a href="{{ route('admin.transactions') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 width50" >
                                        <div class="form-check">
                                            <input class="form-check-input" id="checkAll" type="checkbox"
                                                required="">
                                            <label class="form-check-label" for="checkAll"></label>
                                        </div>
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Order No</th>
                                    @if (auth()->guard('admin')->user()->can(['company-name']))
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Company Name</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">MID</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                    @endif
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="min-width: 150px;">Date & Time</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Currency</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Country</th>
                                    @if (auth()->guard('admin')->user())
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($data) > 0)
                                    @foreach ($data as $transaction)
                                        <tr id="tr_{{ $transaction->id }}">
                                            <td class="align-middle text-center text-sm">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input multidelete"
                                                        name="multicheckmail[]"
                                                        id="customCheckBox_{{ $transaction->id }}"
                                                        value="{{ $transaction->id }}" required="">
                                                    <label class="form-check-label"
                                                        for="customCheckBox_{{ $transaction->id }}"></label>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @if (auth()->guard('admin')->user()->can(['details-transaction']))
                                                    <a href="javascript:;" data-bs-target="#transactionShowModal"
                                                        data-bs-toggle="modal" class="text-primary showTransaction"
                                                        data-id="{{ $transaction->id }}"
                                                        data-link="{{ route('admin.merchant-transactions-details') }}">
                                                        <strong
                                                            class="text-primary transOrderNo">{{ $transaction->order_id }}</strong>
                                                    </a><br />
                                                @else
                                                    <a href="javascript:;" class="text-primary">
                                                        <strong class="text-primary">{{ $transaction->order_id }}</strong>
                                                    </a><br />
                                                @endif
                                                @if ($transaction->is_request_from_vt)
                                                    <label
                                                        class="badge badge-sm bg-gradient-success">{{ $transaction->is_request_from_vt }}</label>
                                                @endif

                                                @if ($transaction->payment_type == 'crypto')
                                                    <label class="badge badge-sm bg-gradient-success">Crypto</label>
                                                @elseif ($transaction->payment_type == 'bank')
                                                    <label class="badge badge-sm bg-gradient-success">Bank</label>
                                                @elseif ($transaction->payment_type == 'upi')
                                                    <label class="badge badge-sm bg-gradient-success">UPI</label>
                                                @elseif ($transaction->card_type == '2')
                                                    <label class="badge badge-sm bg-gradient-success">Visa</label>
                                                @elseif($transaction->card_type == '3')
                                                    <label class="badge badge-sm bg-gradient-danger">Master</label>
                                                @elseif($transaction->card_type == '1')
                                                    <label class="badge badge-sm bg-gradient-primary">Amex</label>
                                                @elseif($transaction->card_type == '4')
                                                    <label class="badge badge-sm bg-gradient-primary">Discover</label>
                                                @elseif($transaction->card_type == '5')
                                                    <label class="badge badge-sm bg-gradient-primary">JCB</label>
                                                @elseif($transaction->card_type == '6')
                                                    <label class="badge badge-sm bg-gradient-primary">Maestro</label>
                                                @elseif($transaction->card_type == '7')
                                                    <label class="badge badge-sm bg-gradient-primary">Switch</label>
                                                @elseif($transaction->card_type == '8')
                                                    <label class="badge badge-sm bg-gradient-primary">Solo</label>
                                                @elseif($transaction->card_type == '9')
                                                    <label class="badge badge-sm bg-gradient-primary">UnionPay</label>
                                                @elseif($transaction->card_type == '0')
                                                    <label class="badge badge-sm bg-gradient-primary">Unknown</label>
                                                @endif

                                                {{-- The card whitelabled status --}}
                                                @if ($transaction->is_white_label)
                                                    <label class="badge badge-info badge-sm">WTL</label>
                                                @else
                                                    <label class="badge badge-info badge-sm">FT</label>
                                                @endif
                                            </td>
                                            @if (auth()->guard('admin')->user()->can(['company-name']))
                                                <td class="align-middle text-center text-sm">
                                                    <span>
                                                        <div class="d-flex align-items-center">
                                                            <span class="w-space-no">
                                                                {{ @$Application[$transaction->user_id] }}
                                                                <br>
                                                                <label
                                                                    class="badge badge-sm bg-gradient-success">{{ $transaction->request_origin }}</label>
                                                                <label
                                                                    class="badge badge-sm bg-gradient-warning">{{ $transaction->request_from_ip }}</label>
                                                            </span>
                                                        </div>
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    {{ @$MIDDetail[$transaction->payment_gateway_id] }} <br />
                                                    @if ($transaction->status == '1')
                                                        <label class="badge badge-sm bg-gradient-success">Success</label>
                                                    @elseif($transaction->status == '2')
                                                        <label class="badge badge-sm bg-gradient-warning">Pending</label>
                                                    @elseif($transaction->status == '5')
                                                        <label class="badge badge-sm bg-gradient-primary">Blocked</label>
                                                    @elseif($transaction->status == '7')
                                                        <label class="badge badge-sm bg-gradient-primary">3ds Redirect</label>
                                                    @else
                                                        <label class="badge badge-sm bg-gradient-danger">Declined</label>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center text-sm">{{ $transaction->email }}</td>
                                            @endif
                                            <td class="align-middle text-center text-sm"><span>{{ $transaction->amount }}</span>
                                                @if ($transaction->is_converted == '1')
                                                    @if ($transaction->amount != $transaction->converted_amount)
                                                        <span class="text-warning"> -
                                                            {{ $transaction->converted_amount }}</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @if ($transaction->created_at)
                                                    {{ $transaction->created_at->format('d-m-Y / H:i:s') }}
                                                @else
                                                    N/A
                                                @endif


                                            </td>

                                            <td class="align-middle text-center text-sm">
                                                {{ $transaction->currency }}
                                                @if ($transaction->is_converted == '1')
                                                    @if ($transaction->currency != $transaction->converted_currency)
                                                        <span class="text-warning"> -
                                                            {{ $transaction->converted_currency }}</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="align-middle text-center text-sm">{{ $transaction->country }}</td>
                                            <td class="align-middle text-center text-sm">
                                                <div class="dropdown">
                                                      <a href="javascript:;" class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownMenuLink2">
                                                      </a>
                                                      <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink2">
                                                        @if (auth()->guard('admin')->user()->can(['details-transaction']))
                                                            <li><a href="{{ route('admin.getsingletransaction', ['id' => $transaction->id]) }}"
                                                                class="dropdown-item">
                                                                View
                                                            </a></li>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['delete-all-transaction']))
                                                            <li><a data-id="{{ $transaction->id }}"
                                                                class="dropdown-item deleteTransaction"
                                                                data-link="{{ route('delete-transaction') }}">Delete
                                                            </a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="align-middle text-center text-sm" colspan="8">
                                            <p class="text-center"><strong>No record found</strong></p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    @if (!empty($data) && $data->count())
                        <?php
                        if (!empty($_GET['start_date'])) {
                            $_GET['start_date'] = date('Y-m-d 00:00:00', strtotime($_GET['start_date']));
                        }
                        if (!empty($_GET['end_date'])) {
                            $_GET['end_date'] = date('Y-m-d 23:59:59', strtotime($_GET['end_date']));
                        }
                        if (!empty($_GET['transaction_start_date'])) {
                            $_GET['transaction_start_date'] = date('Y-m-d 00:00:00', strtotime($_GET['transaction_start_date']));
                        }
                        if (!empty($_GET['transaction_end_date'])) {
                            $_GET['transaction_end_date'] = date('Y-m-d 23:59:59', strtotime($_GET['transaction_end_date']));
                        }
                        ?>
                        <div class="row">
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
                        <div class="row">
                            <div class="col-md-12 text-right">
                                Current IST Time: {{ convertDateToLocal(now(), 'd-m-Y H:i:s') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('partials.transactions.show-modal')
    {{-- Chargebacks  Modal --}}
    <div class="modal fade bs-example-modal-center" id="transactionChargebacks" tabindex="-1" role="dialog"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-sm modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transaction Chargebacks</h5>
                    <button type="button" class="btn-close cancelChargeback" data-bs-dismiss="modal"
                        aria-hidden="true"></button>
                </div>
                <div class="modal-body" id="chargebacksContent">
                    <form id="chargebacksForm" class="form-dark">
                        @csrf
                        <div class="mb-2">
                            <label>Chargeback date</label>
                            <input class="form-control datepicker-here" data-multiple-dates-separator=" - "
                                data-language="en" type="text" name="chargebacks_date" id="chargebacks_value"
                                readonly value="{{ date('d-m-Y') }}">
                        </div>
                        <span class="help-block text-danger">
                            <strong id="chargebacks_error"></strong>
                        </span>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="submitChargebacks"
                        data-link="{{ route('change-chargebacks-status') }}">Submit</button>
                    <button type="button" class="btn btn-danger cancelChargeback" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Flagged  Modal --}}
    <div class="modal fade bs-example-modal-center" id="transactionFlagged" tabindex="-1" role="dialog"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-sm modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transaction Suspicious</h5>
                    <button type="button" class="btn-close cancelFlaggedBtn" data-bs-dismiss="modal"
                        aria-hidden="true"></button>
                </div>
                <div class="modal-body" id="flaggedContent">
                    <form id="flaggedForm" class="form-dark">
                        @csrf
                        <div class="form-group">
                            <select class="form-control" name="flagged_by">
                                <option value="testpay" class="testpayOpt">Suspicious By {{ config('app.name') }}</option>
                                <option value="bank" class="bankOpt">Suspicious By Bank</option>
                            </select>
                            <span class="help-block text-danger">
                                <strong id="flagged_type_error"></strong>
                            </span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="submitFlagged"
                        data-link="{{ route('change-transaction-flag') }}">Submit</button>
                    <button type="button" class="btn btn-danger cancelFlaggedBtn" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Reffund  Modal --}}
    <div class="modal fade bs-example-modal-center" id="transactionRefund" tabindex="-1" role="dialog"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-sm modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transaction Refund</h5>
                    <button type="button" class="btn-close cancelRefundBtn" data-bs-dismiss="modal"
                        aria-hidden="true"></button>
                </div>
                <div class="modal-body" id="refundContent">
                    <form id="refundForm" class="form-dark">
                        @csrf
                        <div class="mb-2">
                            <label>Refund date</label>
                            <input class="form-control" data-multiple-dates-separator=" - " data-language="en"
                                type="text" name="refund_date" id="refund_value" readonly
                                value="{{ date('d-m-Y') }}">
                        </div>
                        <span class="help-block text-danger">
                            <strong id="refund_error"></strong>
                        </span>

                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="submitRefund"
                        data-link="{{ route('change-refund-status') }}">Submit</button>
                    <button type="button" class="btn btn-danger cancelRefundBtn" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script type="text/javascript">
        var changeTransactionUnRefund = "{{ route('change-transaction-unRefund') }}";
        var changeTransactionUnChargeback = "{{ route('change-transaction-unChargeback') }}";
        var changeTransactionUnFlagged = "{{ route('change-transaction-unflagged') }}";
        var changeTransactionStatus = "{{ route('change-transaction-status') }}";

        // * Custom JS
        $(document).on('click', '.closeChatBox', function() {
            $('.chatbox').removeClass('active')
        });

        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=number]").val("");
            $(".select2").val('first').trigger('change.select2');
        });

        $("#checkAll").on("change", function() {
            $("td input:checkbox, .custom-checkbox input:checkbox").prop(
                "checked",
                $(this).prop("checked")
            );
        });

        // * Listen Order number event
        $(document).on("click", ".transOrderNo", function() {
            $(".transOrderNo").removeClass("text-danger")
            $(this).addClass("text-danger")
        });
    </script>
    <script src="{{ storage_asset('themeAdmin/custom_js/admin/transactions.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
@endsection
