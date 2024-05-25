@extends('layouts.admin.default')
@section('title')
    Suspicious Transactions
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Suspicious Transactions
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
                                    <label>Card No.</label>
                                    <input type="number" class="form-control" placeholder="Enter here..." name="card_no"
                                        value="{{ $_GET['card_no'] ?? '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" placeholder="Enter here..." name="email"
                                        value="{{ isset($_GET['email']) && $_GET['email'] != '' ? $_GET['email'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Business Name</label>
                                    <select name="user_id" class="form-control select2" data-width="100%">
                                        <option disabled selected> -- Select Business Name -- </option>
                                        @foreach ($companyList as $item)
                                            <option value="{{ $item->user_id }}"
                                                {{ request()->user_id == $item->user_id ? 'selected' : '' }}>
                                                {{ $item->business_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>First Name</label>
                                    <input type="text" class="form-control " placeholder="Enter here..."
                                        name="first_name"
                                        value="{{ isset($_GET['first_name']) && $_GET['first_name'] != '' ? $_GET['first_name'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" placeholder="Enter here..." name="last_name"
                                        value="{{ isset($_GET['last_name']) && $_GET['last_name'] != '' ? $_GET['last_name'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Created Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="start_date"
                                            placeholder="Enter here..." id="start_date"
                                            value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Created End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="end_date"
                                            placeholder="Enter here..." id="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Transaction Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="transaction_start_date"
                                            placeholder="Enter here..." id="transaction_start_date"
                                            value="{{ isset($_GET['transaction_start_date']) && $_GET['transaction_start_date'] != '' ? $_GET['transaction_start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Transaction End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="transaction_end_date"
                                            placeholder="Enter here..." id="transaction_end_date"
                                            value="{{ isset($_GET['transaction_end_date']) && $_GET['transaction_end_date'] != '' ? $_GET['transaction_end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Suspicious Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="flagged_start_date"
                                            placeholder="Enter here..." id="flagged_start_date"
                                            value="{{ isset($_GET['flagged_start_date']) && $_GET['flagged_start_date'] != '' ? $_GET['flagged_start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Suspicious End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="flagged_end_date"
                                            placeholder="Enter here..." id="flagged_end_date"
                                            value="{{ isset($_GET['flagged_end_date']) && $_GET['flagged_end_date'] != '' ? $_GET['flagged_end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Status</label>
                                    <select name="status" class="form-control select2" data-width="100%">
                                        <option disabled selected> -- Select Status -- </option>
                                        <option value="1"
                                            {{ isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '' }}>
                                            Success</option>
                                        <option value="2"
                                            {{ isset($_GET['status']) && $_GET['status'] == '2' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="3"
                                            {{ isset($_GET['status']) && $_GET['status'] == '3' ? 'selected' : '' }}>
                                            Canceled</option>
                                        <option value="4"
                                            {{ isset($_GET['status']) && $_GET['status'] == '4' ? 'selected' : '' }}>To Be
                                            Confirm</option>
                                        <option value="0"
                                            {{ isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '' }}>
                                            Declined</option>
                                        <option value="preArbitration"
                                            {{ isset($_GET['status']) && $_GET['status'] == 'preArbitration' ? 'selected' : '' }}>
                                            Pre Arbitration</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>MID</label>
                                    <select class="form-control input-rounded select2" name="payment_gateway_id">
                                        <option disabled selected> -- Select MID -- </option>
                                        @foreach ($payment_gateway_id as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($_GET['payment_gateway_id']) && $_GET['payment_gateway_id'] == $value->id ? 'selected' : '' }}>
                                                {{ $value->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Card Type</label>
                                    <select class="form-control input-rounded select2" name="card_type" id="">
                                        <option selected disabled> -- Select Card Type -- </option>
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
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="order_no">Order no.</label>
                                    <input type="text" class="form-control" placeholder="Enter here..."
                                        name="order_id" value="{{ $_GET['order_id'] ?? '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Currency</label>
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
                                    <label for="customer_order_id">Customer order ID</label>
                                    <input type="text" class="form-control" placeholder="Enter here..."
                                        name="customer_order_id"
                                        value="{{ isset($_GET['customer_order_id']) && $_GET['customer_order_id'] != '' ? $_GET['customer_order_id'] : '' }}">
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="document_status">Document Status</label>
                                    <select class="form-control input-rounded select2"
                                        name="suspicious_transactions_document_upload_files" id="document_status">
                                        <option selected disabled> -- Select Status -- </option>
                                        <option value="0"
                                            {{ isset($_GET['suspicious_transactions_document_upload_files']) && $_GET['suspicious_transactions_document_upload_files'] == '0' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="1"
                                            {{ isset($_GET['suspicious_transactions_document_upload_files']) && $_GET['suspicious_transactions_document_upload_files'] == '1' ? 'selected' : '' }}>
                                            Uploaded</option>
                                    </select>
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
        <div class="col-lg-12 text-right mb-2">
            {{-- @if (auth()->guard('admin')->user()->can(['send-pre-arbitration-notice']))
                <a href="javascript:;" class="btn btn-primary btn-sm" id="preArbitrationNotice">Send Pre
                    Arbitration Notice</a>
            @endif --}}

            @if (auth()->guard('admin')->user()->can(['update-all-transaction']))
                <a href="javascript:;" class="btn btn-primary btn-sm" id="markChargeback">Mark Chargeback</a>
            @endif

            @if (auth()->guard('admin')->user()->can(['send-mail-suspicious-transaction']))
                <button type="button" class="btn btn btn-primary btn-sm" id="resendSuspiciousEmail"> Send
                    Suspicious Email</button>
            @endif

            <?php
            $url = Request::fullUrl();
            $parsedUrl = parse_url($url);
            $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
            $subQueryString = $query != '' ? $query . '&type=xlsx' : '';
            ?>

            @if (auth()->guard('admin')->user()->can(['export-all-transaction']))
                @if (!empty($subQueryString))
                    <a href="{{ route('all-admin-suspicious-transactions-csv-export', [$subQueryString]) }}"
                        class="btn btn-shadow btn-primary btn-sm"><i class="fa fa-download me-2"></i> Export Excel </a>
                @else
                    <a href="{{ route('all-admin-suspicious-transactions-csv-export') }}" class="btn btn-primary btn-sm"
                        id="ExcelLink">
                        <i class="fa fa-download"></i>
                        Export Excel
                    </a>
                @endif
            @endif
            @if (auth()->guard('admin')->user()->can(['delete-all-transaction']))
                <button type="button" class="btn btn-danger btn-sm" id="deleteSelected"
                    data-link="{{ route('delete-transaction') }}"><i class="fa fa-trash"></i> Delete Selected
                    Record</button>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Suspicious Transactions</h4>
                    </div>
                    <div>
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
                        <div class="btn-group">
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
                            <a href="{{ route('admin.suspicious') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th width="50px">
                                        <div class="form-check">
                                            <input type="checkbox" id="selectallcheckbox" name=""
                                                class="form-check-input">
                                            <label class="form-check-label" for="selectallcheckbox"></label>
                                        </div>
                                    </th>
                                    <th style="min-width: 172px;">Alert Date & Time</th>
                                    <th>Order No</th>
                                    <th>Company Name</th>
                                    <th>MID</th>
                                    <th>Email</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th style="min-width: 175px;">Document Status</th>
                                    @if (auth()->guard('admin')->user()->can(['update-suspicious-transaction']))
                                        <th>Suspicious</th>
                                    @endif
                                    @if (auth()->guard('admin')->user())
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($data) > 0)
                                    @foreach ($data as $transaction)
                                        @if ($transaction->is_pre_arbitration == '1')
                                            <tr id="tr_{{ $transaction->id }}" class="bg-danger-light">
                                            @else
                                            <tr id="tr_{{ $transaction->id }}">
                                        @endif
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" id="checkbox-{{ $transaction->id }}"
                                                    name="multiselect[]" class="multiselect multidelete form-check-input"
                                                    value="{{ $transaction->id }}">
                                                <label class="form-check-label"
                                                    for="checkbox-{{ $transaction->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $transaction->flagged_date }}
                                        </td>
                                        <td>
                                            @if (auth()->guard('admin')->user()->can(['details-transaction']))
                                                <a href="javascript:;" data-bs-target="#transactionShowModal"
                                                    data-bs-toggle="modal" class="text-primary showTransaction"
                                                    data-id="{{ $transaction->id }}"
                                                    data-link="{{ route('admin.merchant-transactions-details') }}">
                                                    <strong
                                                        class="text-primary transOrderNo">{{ $transaction->order_id }}</strong>
                                                </a>
                                            @else
                                                <a href="javascript:;" class="text-primary">
                                                    <strong class="text-primary">{{ $transaction->order_id }}</strong>
                                                </a>
                                            @endif
                                            <br />
                                            @if ($transaction->flagged_by)
                                                <label class="light badge-sm badge badge-success">Suspicious by
                                                    {{ $transaction->flagged_by }}</label>
                                            @endif
                                            @if ($transaction->is_pre_arbitration == '1')
                                                <label class="badge-sm badge badge-danger">Pre Arbitration</label>
                                            @endif

                                            {{-- The card whitelabled status --}}
                                            @if ($transaction->is_white_label)
                                                <label class="badge badge-danger badge-sm">WTL</label>
                                            @else
                                                <label class="badge badge-danger badge-sm">FT</label>
                                            @endif
                                        </td>
                                        <td>
                                            <span>
                                                <div class="d-flex align-items-center">
                                                    <span class="w-space-no">
                                                        {{ $transaction->userName }}<br>
                                                        <label
                                                            class="light badge badge-success">{{ $transaction->request_origin }}</label>
                                                        <label
                                                            class="light badge badge-primary">{{ $transaction->request_from_ip }}</label>
                                                    </span>
                                                </div>
                                            </span>
                                        </td>


                                        <td>{{ $transaction->bank_name }}</td>
                                        <td>{{ $transaction->email }}</td>
                                        <td>{{ $transaction->amount }}</td>
                                        <td>
                                            {{ $transaction->currency }}
                                            @if ($transaction->is_converted == '1')
                                                @if ($transaction->currency != $transaction->converted_currency)
                                                    <span class="text-warning"> -
                                                        {{ $transaction->converted_currency }}</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($transaction->transactions_document_upload_files))
                                                <label class="light badge badge-sm badge-success">Uploaded</label>
                                            @else
                                                <label class="light badge badge-sm badge-danger">Pending</label>
                                            @endif
                                        </td>
                                        @if (auth()->guard('admin')->user()->can(['update-suspicious-transaction']))
                                            <td>
                                                <div class="form-check">
                                                    <input type="checkbox" data-id="{{ $transaction->id }}"
                                                        class="form-check-input suspiciousCheckBox"
                                                        id="customCheckBox{{ $transaction->id }}"
                                                        @if ($transaction->is_flagged) checked @else disabled @endif>
                                                    <label class="form-check-label"
                                                        for="customCheckBox{{ $transaction->id }}"></label>
                                                </div>

                                            </td>
                                        @endif
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                    data-bs-toggle="dropdown">
                                                    <svg width="5" height="17" viewBox="0 0 5 17" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M2.36328 4.69507C1.25871 4.69507 0.363281 3.79964 0.363281 2.69507C0.363281 1.5905 1.25871 0.695068 2.36328 0.695068C3.46785 0.695068 4.36328 1.5905 4.36328 2.69507C4.36328 3.79964 3.46785 4.69507 2.36328 4.69507Z"
                                                            fill="#B3ADAD" />
                                                        <path
                                                            d="M2.36328 10.6951C1.25871 10.6951 0.363281 9.79964 0.363281 8.69507C0.363281 7.5905 1.25871 6.69507 2.36328 6.69507C3.46785 6.69507 4.36328 7.5905 4.36328 8.69507C4.36328 9.79964 3.46785 10.6951 2.36328 10.6951Z"
                                                            fill="#B3ADAD" />
                                                        <path
                                                            d="M2.36328 16.6951C1.25871 16.6951 0.363281 15.7996 0.363281 14.6951C0.363281 13.5905 1.25871 12.6951 2.36328 12.6951C3.46785 12.6951 4.36328 13.5905 4.36328 14.6951C4.36328 15.7996 3.46785 16.6951 2.36328 16.6951Z"
                                                            fill="#B3ADAD" />
                                                    </svg>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @if (auth()->guard('admin')->user()->can(['details-transaction']))
                                                        <a href="{{ route('admin.getsingletransaction', ['id' => $transaction->id]) }}"
                                                            class="dropdown-item">
                                                            View
                                                        </a>
                                                    @endif
                                                    @if (auth()->guard('admin')->user()->can(['update-suspicious-transaction']))
                                                        <a data-bs-toggle="modal" data-bs-target="#transactionShowModal"
                                                            href="#showTransaction"
                                                            class="flagged-show-document dropdown-item"
                                                            data-id="{{ $transaction->id }}"
                                                            data-link="{{ route('merchant-flagged-show-documents') }}">View
                                                            Document</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8">
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
                            <div class="col-md-8">
                                {!! $data->appends($_GET)->links() !!}
                            </div>
                            <div class="col-md-4 text-right">
                                Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of total {{ $data->total() }}
                                entries
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
        <div class="modal-dialog modal-lg modal-dialog modal-lg-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transaction Chargebacks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body" id="chargebacksContent">
                    <form id="chargebacksForm" class="form-dark">
                        @csrf
                        <div class="input-group">
                            <input class="form-control datepicker-here" data-multiple-dates-separator=" - "
                                data-language="en" type="text" name="chargebacks_date" id="chargebacks_value"
                                required="" placeholder="Select Date" value="{{ date('d-m-Y') }}">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                        <span class="help-block text-danger">
                            <strong id="chargebacks_error"></strong>
                        </span>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="submitChargebacks"
                        data-link="{{ route('change-chargebacks-status') }}">Submit</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/admin/transactions.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });

        $('body').on('change', '#selectallcheckbox', function() {
            if ($(this).prop("checked") == true) {
                $('.multiselect').prop("checked", true);
            } else if ($(this).prop("checked") == false) {
                $('.multiselect').prop("checked", false);
            }
        });

        $('body').on('click', '#preArbitrationNotice', function() {
            var id = [];
            $('.multiselect:checked').each(function() {
                id.push($(this).val());
            });
            if (id.length > 0) {
                $.ajax({
                    url: '{{ route('pre-arbitration-notice-sent') }}',
                    method: "POST",
                    context: $(this),
                    data: {
                        '_token': CSRF_TOKEN,
                        'id': id
                    },
                    beforeSend: function() {
                        $(this).attr('disabled', 'disabled');
                        $(this).html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
                    },
                    success: function(data) {
                        if (data.success) {
                            console.log(data);
                            toastr.success('Pre Arbitration Notice Sent Successfully.');
                            $(this).attr('disabled', false);
                            $(this).html('Send Pre Arbitration Notice');
                            window.setTimeout(
                                function() {
                                    location.reload(true)
                                },
                                2000
                            );
                        } else {
                            toastr.error('Something went wrong.');
                            $(this).attr('disabled', false);
                            $(this).html('Send Pre Arbitration Notice');
                            window.setTimeout(
                                function() {
                                    location.reload(true)
                                },
                                2000
                            );
                        }
                        $(this).attr('disabled', false);
                        $(this).html('Send Pre Arbitration Notice');
                    }
                });
            } else {
                toastr.error('Please select atleast one records!');
            }
        });

        $(document).on("click", "#markChargeback", function() {
            var id = [];
            $(".multiselect:checked").each(function() {
                id.push($(this).val());
            });
            if (id.length > 0) {
                $("#transactionChargebacks").modal("show");
                var transactionID = id;
                $("#chargebacksForm").append(
                    '<input type="hidden" name="type" value="forall">'
                );
                $("#chargebacksForm").append(
                    '<input type="hidden" name="id" value="' +
                    transactionID +
                    '">'
                );
                $("#chargebacksForm").append(
                    '<input type="hidden" name="status" value="1">'
                );
            } else {
                toastr.warning("Please select atleast one transaction!");
            }
        });
    </script>
    <script>
        $(document).on('click', '.suspiciousCheckBox', function() {
            var id = $(this).data('id')
            var check = !$(this).prop('checked')
            if (check) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.remove.suspicious') }}",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(res) {
                        if (res.status == 200) {
                            toastr.success(res.message)
                            setTimeout(function() {
                                window.location.reload()
                            }, 1000)
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong!')
                    }
                })
            }
        });

        $('body').on('click', '#resendSuspiciousEmail', function() {
            var id = [];
            $('.multidelete:checked').each(function() {
                id.push($(this).val());
            });
            if (id.length > 0) {
                $.ajax({
                    url: '{{ route('resend-suspicious-email') }}',
                    method: "POST",
                    context: $(this),
                    data: {
                        '_token': CSRF_TOKEN,
                        'id': id
                    },
                    beforeSend: function() {
                        $(this).attr('disabled', 'disabled');
                        $(this).html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
                    },
                    success: function(data) {
                        if (data.success) {
                            console.log(data);
                            toastr.success('Email Sent Successfully.');
                            $(this).attr('disabled', false);
                            $(this).html(' Send Suspicious Email');
                            window.setTimeout(
                                function() {
                                    location.reload(true)
                                },
                                2000
                            );
                        } else {
                            toastr.error('Something went wrong.');
                            $(this).attr('disabled', false);
                            $(this).html(' Send Suspicious Email');
                            window.setTimeout(
                                function() {
                                    location.reload(true)
                                },
                                2000
                            );
                        }
                        $(this).attr('disabled', false);
                        $(this).html(' Send Suspicious Email');
                    }
                });
            } else {
                toastr.error('Please select atleast one records!');
            }
        });
    </script>
@endsection
