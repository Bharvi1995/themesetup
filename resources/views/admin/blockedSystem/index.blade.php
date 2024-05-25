@extends('layouts.admin.default')
@section('title')
    Admin Users
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Blocked System
@endsection

@section('content')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <form method="" id="search-form">
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row ">
                                <div class="form-group col-lg-6">
                                    <label for="name">Card No.</label>
                                    <input type="number" class="form-control" placeholder="Enter here..." name="card_no"
                                        value="{{ $_GET['card_no'] ?? '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="status">Business Name</label>
                                    <select name="user_id" class="form-control select2" data-width="100%">
                                        <option disabled selected value="first"> -- Select Business Name -- </option>
                                        @foreach ($companyList as $item)
                                            <option value="{{ $item->user_id }}"
                                                {{ request()->user_id == $item->user_id ? 'selected' : '' }}>
                                                {{ $item->business_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="status">Status</label>
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
                                <div class="form-group col-lg-6">
                                    <label for="status">Card Type</label>
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
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="order_no">Order no.</label>
                                    <input type="text" class="form-control" placeholder="Enter here..." name="order_id"
                                        value="{{ $_GET['order_id'] ?? '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="text">Currency</label>
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
                                    <label for="country">Country</label>
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
                                    <label for="amount_greater_than">Amount greater than</label>
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
                                    <label for="reason">Reason</label>
                                    <input type="text" class="form-control input-rounded" placeholder="Enter here..."
                                        name="reason"
                                        value="{{ isset($_GET['reason']) && $_GET['reason'] != '' ? $_GET['reason'] : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="resetForm"></i>Clear</button>
                        <button type="submit" class="btn btn-info" id="extraSearch123">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Blocked System</h4>
                    </div>
                    <div>
                        <form id="noListform" method="GET" style="width: 165px; float: left; margin-right: 5px;">
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
                            <button type="button" class="btn btn-info bell-link btn-sm" data-bs-toggle="modal"
                                data-bs-target="#searchModal"> <i class="fa fa-search-plus"></i>
                                Advanced
                                Search</button>
                            <a href="{!! url('admin/blocked-system') !!}" class="btn btn-primary btn-sm">Reset</a>
                        </div>

                        <?php
                        $url = Request::fullUrl();
                        $parsedUrl = parse_url($url);
                        $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
                        $subQueryString = $query != '' ? $query . '&type=xlsx' : '';
                        ?>
                        @if (auth()->guard('admin')->user()->can(['delete-block-card']))
                            <button type="button" class="btn btn-primary btn-sm" id="deleteSelectedRecord"
                                data-link="{{ route('delete-card') }}"><i class="fa fa-trash"></i> Delete Selected
                                Records</button>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="custom-control custom-checkbox custom-control-inline mr-0">
                                            <input type="checkbox" id="checkAll" name=""
                                                class="multidelete custom-control-input">
                                            <label class="custom-control-label" for="checkAll"></label>
                                        </div>
                                    </th>
                                    <th>Order No</th>
                                    <th>Card No <br />
                                        Company Name
                                    </th>
                                    <th>Country</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                    <th>Date & Time</th>
                                    @if (auth()->guard('admin')->user()->can(['delete-block-card']))
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($data) && $data->count())
                                    @foreach ($data as $key => $value)
                                        <tr id="tr_{{ $value->id }}">
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline mr-0">
                                                    <input type="checkbox" class="custom-control-input multidelete"
                                                        name="multicheckmail[]" id="customCheckBox_{{ $value->id }}"
                                                        value="{{ $value->id }}" required="">
                                                    <label class="custom-control-label"
                                                        for="customCheckBox_{{ $value->id }}"></label>
                                                </div>
                                            </td>
                                            <td>
                                                @if (auth()->guard('admin')->user()->can(['view-blocked-system']))
                                                    <a href="javascript:;" data-bs-target="#transactionShowModal"
                                                        data-bs-toggle="modal" class="text-primary showTransaction"
                                                        data-id="{{ $value->transaction_id }}"
                                                        data-link="{{ route('admin.merchant-transactions-details') }}">
                                                        <strong
                                                            class="text-primary transOrderNo">{{ $value->order_id }}</strong>
                                                    </a><br />
                                                @else
                                                    <a href="javascript:;" class="text-primary">
                                                        <strong class="text-primary">{{ $value->order_id }}</strong>
                                                    </a><br />
                                                @endif
                                                @if ($value->card_type == '2')
                                                    <label class="light badge badge-sm badge-success">Visa</label>
                                                @elseif($value->card_type == '3')
                                                    <label class="light badge badge-sm badge-danger">Master</label>
                                                @elseif($value->card_type == '1')
                                                    <label class="light badge badge-sm badge-primary">Amex</label>
                                                @elseif($value->card_type == '4')
                                                    <label class="light badge badge-sm badge-primary">Discover</label>
                                                @elseif($value->card_type == '5')
                                                    <label class="light badge badge-sm badge-primary">JCB</label>
                                                @elseif($value->card_type == '6')
                                                    <label class="light badge badge-sm badge-primary">Maestro</label>
                                                @elseif($value->card_type == '7')
                                                    <label class="light badge badge-sm badge-primary">Switch</label>
                                                @elseif($value->card_type == '8')
                                                    <label class="light badge badge-sm badge-primary">Solo</label>
                                                @endif
                                            </td>
                                            <td>{!! $value->card_no !!} <br />
                                                <label
                                                    class="light badge badge-sm badge-success">{{ $value->userName }}</label>
                                            </td>
                                            <td>{!! $value->country !!}</td>
                                            <td>{{ $value->amount }}</td>
                                            <td>{{ $value->currency }}</td>
                                            <td>
                                                @if ($value->status == '1')
                                                    <label class="light badge badge-sm badge-success">Success</label>
                                                @elseif($value->status == '2')
                                                    <label class="light badge badge-sm badge-warning">Pending</label>
                                                @elseif($value->status == '5')
                                                    <label class="light badge badge-sm badge-primary">Blocked</label>
                                                @elseif($value->status == '7')
                                                    <label class="light badge badge-sm badge-primary">3ds Redirect</label>
                                                @else
                                                    <label class="light badge badge-sm badge-danger">Declined</label>
                                                @endif
                                            </td>
                                            <td>{{ $value->reason }}</td>
                                            <td>{{ $value->created_at->format('d-m-Y / H:i:s') }}</td>
                                            @if (auth()->guard('admin')->user()->can(['delete-block-card']))
                                                <td>
                                                    <div class="dropdown ml-auto">
                                                        <a href="#" class="btn btn-primary sharp rounded-pill"
                                                            data-bs-toggle="dropdown" aria-expanded="true"><svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px"
                                                                height="18px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24"
                                                                        height="24">
                                                                    </rect>
                                                                    <circle fill="#FFF" cx="5" cy="12"
                                                                        r="2">
                                                                    </circle>
                                                                    <circle fill="#FFF" cx="12" cy="12"
                                                                        r="2">
                                                                    </circle>
                                                                    <circle fill="#FFF" cx="19" cy="12"
                                                                        r="2">
                                                                    </circle>
                                                                </g>
                                                            </svg></a>
                                                        <ul class="dropdown-menu dropdown-menu-end">

                                                            <li class="dropdown-item">

                                                                <a href="javascript:void(0)"
                                                                    class="dropdown-item delete_modal"
                                                                    data-bs-toggle="modal" data-bs-target="#delete_modal"
                                                                    data-url="{{ URL::route('blocked-system-destroy', $value->id) }}"
                                                                    data-id="{{ $value->id }}"><i
                                                                        class="fa fa-trash text-danger me-2"></i>
                                                                    Delete</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10">
                                        <p class="text-center"><strong>No record found.</strong></p>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center clPagination">
                        {!! $data->appends($_GET)->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.transactions.show-modal')
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/assets/custom_js/common.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/assets/custom_js/admin/transactions.js') }}"></script>
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });
    </script>

    <script type="text/javascript">
        $(document).on("click", "#deleteSelectedRecord", function() {
            var id = [];
            $(".multidelete:checked").each(function() {
                id.push($(this).val());
            });
            const apiUrl = $(this).data("link");
            if (id.length > 0) {
                swal({
                    title: "Are you sure?",
                    text: "you want to delete this record?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: "POST",
                            context: $(this),
                            url: apiUrl,
                            data: {
                                _token: CSRF_TOKEN,
                                id: id,
                                type: "forall",
                            },
                            beforeSend: function() {
                                $(this).attr("disabled", "disabled");
                            },
                            success: function(data) {
                                if (data.success == true) {
                                    toastr.success("BlockCard deleted Successfully!");
                                    location.reload();
                                } else {
                                    toastr.warning("Something went wrong!");
                                }
                                $(this).attr("disabled", false);
                            },
                        });
                    }
                });
            } else {
                toastr.warning("Please select atleast one card!");
            }
        });
    </script>
@endsection
