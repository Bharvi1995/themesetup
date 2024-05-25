@extends('layouts.admin.default')
@section('title')
    Payment API
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> /Payment API
@endsection
@include('requestDate')
@section('content')
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
                                <div class="form-group col-md-6">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" placeholder="Email" name="email"
                                        value="{{ isset($_GET['email']) && $_GET['email'] != '' ? $_GET['email'] : '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name">Company Name</label>
                                    <select class="form-control select2" name="user_id">
                                        <option value="">-- Select Company --</option>
                                        @foreach ($company_name as $value)
                                            <option value="{{ $value->user_id }}"
                                                {{ isset($_GET['user_id']) && $_GET['user_id'] == $value->user_id ? 'selected' : '' }}
                                                data-id="{{ $value->user_id }}">{{ $value->business_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="order_id">Order Id</label>
                                    <input type="text" id="order_id" class="form-control" placeholder="Order Id"
                                        name="order_id" value="{{ $_GET['order_id'] ?? '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="session_id">Session Id</label>
                                    <input type="text" id="session_id" class="form-control" placeholder="Session Id"
                                        name="session_id" value="{{ $_GET['session_id'] ?? '' }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="customer_order_id">Customer Order Id</label>
                                    <input type="text" id="customer_order_id" class="form-control"
                                        placeholder="Customer Order Id" name="customer_order_id"
                                        value="{{ $_GET['customer_order_id'] ?? '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="start_date">Created Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" id="start_date" name="start_date"
                                            placeholder="Enter here..."
                                            value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="end_date">Created End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" id="end_date" name="end_date"
                                            placeholder="Enter here..."
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
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
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Payment API</h4>
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
                            <a href="{{ route('admin.paymentApi') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>COMPANY NAME</th>
                                    <th>ORDER ID</th>
                                    <th>SESSION ID</th>
                                    <th style="min-width: 135px;">DATE</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $newData)
                                    <tr>
                                        <td>{{ @$businessData[$newData->user_id] }}</td>
                                        <td>{{ $newData->order_id }}</td>
                                        <td>{{ $newData->session_id }}</td>
                                        <td>{{ $newData->created_at->format('d-m-Y H:i:s') }}</td>
                                        <td>
                                            <a href="{{ route('admin.paymentApi.show', $newData->id) }}"
                                                class="btn btn-primary btn-sm"><i class="fa fa-eye"
                                                    aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    @if (!empty($data))
                        <div class="row">
                            <div class="col-md-8">
                                {!! $data->appends($_GET)->links() !!}
                            </div>

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script>
        $(document).ready(function() {
            $(document).on("change", "#noList", function() {
                var url = new URL(window.location.href);
                if (url.search) {
                    if (url.searchParams.has("noList")) {
                        url.searchParams.set("noList", $(this).val());
                        location.href = url.href;
                    } else {
                        var newUrl = url.href + "&noList=" + $(this).val();
                        location.href = newUrl;
                    }
                } else {
                    document.getElementById("noListform").submit();
                }
            });
            $("#resetForm").click(function() {
                $('#search-form').find("input[type=text], input[type=number]").val("");
                $(".select2").val('first').trigger('change.select2');
            });
        });
    </script>
@endsection
