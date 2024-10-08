@extends($bankUserTheme)
@section('title')
    All Transactions
@endsection

@section('breadcrumbTitle')
    All Transactions
@endsection

@section('customeStyle')
    <link href="{{ storage_asset('themeAdmin/css/selectize.css') }}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .selectize-control.multi .selectize-input>div {
            cursor: pointer;
            background: #3D3D3D;
            color: #212529;
            border-radius: 5px;
        }
    </style>
@endsection
@section('content')
    @include('requestDate')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <form method="" class="form-dark" id="search-form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">First Name</label>
                                    <input type="text" class="form-control " placeholder="Enter here..."
                                        name="first_name"
                                        value="{{ isset($_GET['first_name']) && $_GET['first_name'] != '' ? $_GET['first_name'] : '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name">Last Name</label>
                                    <input type="text" class="form-control" placeholder="Enter here..." name="last_name"
                                        value="{{ isset($_GET['last_name']) && $_GET['last_name'] != '' ? $_GET['last_name'] : '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="status">Status</label>
                                    <select name="status" class="form-control select2" data-width="100%">
                                        <option disabled selected> -- Status -- </option>
                                        <option value="1"
                                            {{ isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '' }}>Success
                                        </option>
                                        <option value="2"
                                            {{ isset($_GET['status']) && $_GET['status'] == '2' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="3"
                                            {{ isset($_GET['status']) && $_GET['status'] == '3' ? 'selected' : '' }}>
                                            Canceled
                                        </option>
                                        <option value="4"
                                            {{ isset($_GET['status']) && $_GET['status'] == '4' ? 'selected' : '' }}>To Be
                                            Confirm</option>
                                        <option value="0"
                                            {{ isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '' }}>
                                            Declined
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="start_date"
                                            placeholder="Enter here..." id="start_date"
                                            value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="end_date"
                                            placeholder="Enter here..." id="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="order_no">Order no.</label>
                                    <input type="text" class="form-control" placeholder="Enter here..." name="order_id"
                                        value="{{ isset($_GET['order_id']) && $_GET['order_id'] != '' ? $_GET['order_id'] : '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="status">Select Company</label>
                                    <select name="company_name" class="form-control select2" data-width="100%">
                                        <option disabled selected> -- Select Company -- </option>
                                        @foreach ($businessName as $k => $v)
                                            <option value="{{ $v }}"
                                                {{ isset($_GET['company_name']) && $_GET['company_name'] == $v ? 'selected' : '' }}>
                                                {{ $v }}</option>
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
                </div>
            </form>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <h4 class="mt-50">All Transactions</h4>
        </div>
        <div class="col-md-6 text-right">
            @if (!empty($subQueryString))
                <a href="{{ route('bank-merchant-transactions', ['type' => 'xlsx'] + request()->all(), [$subQueryString]) }}"
                    class="btn btn-primary" id="ExcelLink"><i class="fa fa-download mr-2"></i>Export Excel</a>
            @else
                <a href="{{ route('bank-merchant-transactions', ['type' => 'xlsx'] + request()->all()) }}"
                    class="btn btn-primary" id="ExcelLink"><i class="fa fa-download mr-2"></i> Export Excel</a>
            @endif
        </div>
        <div class="col-xl-12 col-xxl-12">
            <div class="card mt-2">
                <div class="card-header">
                    <div></div>
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
                        <form class="pull-right ml-2 form-dark" method="GET" id="search-form2"
                            style="width: 200px; float: left; margin-right: 5px;">
                            <div class="input-group">
                                <input type="text" name="global_search" placeholder="Global Search"
                                    class="form-control-sm form-control"
                                    value="{{ isset($_GET['global_search']) && $_GET['global_search'] != '' ? $_GET['global_search'] : '' }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary btn-sm" type="submit" id="extraSearch2"
                                        style="padding: 7px;">Search</button>
                                </div>
                            </div>
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
                            <a href="{{ route('bank-merchant-transactions') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php
                    $url = Request::fullUrl();
                    $parsedUrl = parse_url($url);
                    $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
                    $subQueryString = $query != '' ? $query . '&type=xlsx' : '';
                    ?>
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>ORDER NO.</th>
                                    <th>NAME</th>
                                    <th>BUSINESS NAME</th>
                                    <th>AMOUNT</th>
                                    <th>CURRENCY</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $transaction)
                                    <tr id="tr_{{ $transaction->id }}">
                                        <td>
                                            <a href="javascript:;" data-bs-target="#transactionShowModal"
                                                data-bs-toggle="modal" class="text-primary showTransaction"
                                                data-id="{{ $transaction->id }}"
                                                data-link="{{ route('bank.merchant-transactions-details') }}">
                                                <strong
                                                    class="text-primary transOrderNo">{{ $transaction->order_id }}</strong>
                                            </a>
                                        </td>
                                        <td>{{ $transaction->first_name }} {{ $transaction->last_name }}</td>
                                        <td>{{ $transaction->business_name }}</td>
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
                                            @if ($transaction->status == '1')
                                                <label class="light badge-sm badge badge-sm badge-success">Success</label>
                                            @elseif($transaction->status == '2')
                                                <label class="light badge-sm badge badge-sm badge-warning">Pending</label>
                                            @elseif($transaction->status == '3')
                                                <label class="light badge-sm badge badge-sm badge-yellow">Canceled</label>
                                            @elseif($transaction->status == '4')
                                                <label class="light badge-sm badge badge-sm badge-primary">To Be
                                                    Confirm</label>
                                            @else
                                                <label class="light badge-sm badge badge-sm badge-danger">Declined</label>
                                            @endif
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
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/admin/transactions.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
    <script>
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });
    </script>
@endsection
