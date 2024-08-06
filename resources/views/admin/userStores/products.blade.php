@extends('layouts.admin.default')

@section('title')
    Merchant
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Merchant Products
@endsection

@section('content')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog modal-lg-scrollable" role="document">
            <form method="" id="search-form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <div class="modal-body">
                        @if (isset($_GET) && $_GET != '')
                            @foreach ($_GET as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        @endif
                        <div class="basic-form">
                            <div class="row ">
                                <div class="form-group col-lg-6">
                                    <label for="">Product Name</label>
                                    <input class="form-control" name="name" type="text" placeholder="Enter here"
                                        value="{{ isset($_GET['name']) && $_GET['name'] != '' ? $_GET['name'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="1" @if (isset($_GET['status']) && $_GET['status'] == 1) selected @endif>Active
                                        </option>
                                        <option value="0" @if (isset($_GET['status']) && $_GET['status'] == 0) selected @endif>In-active
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="resetForm"></i>Clear</button>
                        <button type="submit" class="btn btn-info" id="extraSearch123">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Merchant Products</h4>
                    </div>
                    <div>
                        <form style="width: 165px; float: left; margin-right: 5px;" class="me-2" id="noListform"
                            method="GET">
                            <select class="form-control-sm form-control" name="noList" id="noList">
                                <option value="">--No of Records--</option>
                                <option value="30" {{ request()->get('noList') == '30' ? 'selected' : '' }}>30</option>
                                <option value="50" {{ request()->get('noList') == '50' ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request()->get('noList') == '100' ? 'selected' : '' }}>100
                                </option>
                            </select>
                        </form>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info bell-link btn-sm" data-bs-toggle="modal"
                                data-bs-target="#searchModal"> <i class="fa fa-search-plus"></i> Advanced
                                Search</button>
                            <a href="{{ url('paylaksa/merchant-stores-products/' . $id) }}"
                                class="btn btn-primary btn-sm">Reset</a>
                        </div>
                        <a href="{{ route('user-stores-product-csv-export', $id) . '?' . http_build_query($_GET) }}"
                            class="btn btn-info btn-sm" id="ExcelLink">
                            <i class="fa fa-download"></i>
                            Export Excel
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Product Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dataT as $key => $data)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input status"
                                                    id="status-{{ $data->id }}" name="status"
                                                    data-id="{{ $data->id }}" {{ $data->status ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                    for="status-{{ $data->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $data->name }}
                                        </td>
                                        <td>{{ $data->description }}</td>
                                        <td>{{ $data->price ?? '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-wrap mt-3">
                        {!! $dataT->appends($_GET)->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Old Code --}}


    <div class="modal right fade bg-modal-fade" id="Change_password" tabindex="-1" role="dialog"
        aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="" id="changePasswordForm">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Change Password </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <div class="modal-body" id="">
                        <div class="col-xl-12 col-sm-12 col-md-12 col-12 mb-4">
                            <input class="form-control" type="password" name="password" id="password"
                                placeholder="Enter Password" required="">
                            <span class="help-block text-danger">
                                <strong id="er_password"></strong>
                            </span>
                        </div>
                        <div class="col-xl-12 col-sm-12 col-md-12 col-12 mb-4">
                            <input class="form-control" type="password" name="conform_password" id="conform_password"
                                placeholder="Enter Conform Password">
                            <span class="help-block text-danger">
                                <strong id="er_conform_password"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer-fixed">
                        <button type="button" class="btn btn-success btn-sm" id="submitChangePass">Save</button>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
@section('customScript')
    <script src="https://cdn.ckeditor.com/4.16.2/full/ckeditor.js"></script>
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('body').on('change', '.status', function() {
                var status = '0';
                var product_id = $(this).attr('data-id');
                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var status = '1';
                }

                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('change-store-product-status') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'product_id': product_id,
                        'status': status
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Product status changed successfully!!');
                        } else {
                            toastr.error('Something went wrong!!');
                        }
                    },
                });
            })
        });
    </script>
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
@endsection
