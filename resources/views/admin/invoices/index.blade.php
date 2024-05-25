@extends('layouts.admin.default')

@section('title')
    Invoices
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Invoices
@endsection

@section('customeStyle')
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/custom_css/sweetalert2.min.css') }}">
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
                <form method="" id="search-form" class="form-dark">
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row ">
                                <div class="form-group col-lg-6">
                                    <label>Select Company Name</label>
                                    <select name="user_id" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled> -- Select here -- </option>
                                        @foreach ($companyName as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ request()->company_id == $value->id ? 'selected' : '' }}>
                                                {{ $value->business_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="email">Invoice No</label>
                                    <input class="form-control" name="invoice_no" type="text" placeholder="Enter here"
                                        value="{{ isset($_GET['invoice_no']) && $_GET['invoice_no'] != '' ? $_GET['invoice_no'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="email">Agent Name</label>
                                    <input class="form-control" name="agent_name" type="text" placeholder="Enter here"
                                        value="{{ isset($_GET['agent_name']) && $_GET['agent_name'] != '' ? $_GET['agent_name'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="name">Select Status</label>
                                    <select class="form-control select2" name="is_paid" id="is_paid">
                                        <option disabled selected value=""> -- Select Status -- </option>
                                        <option value="1"
                                            {{ isset($_GET['is_paid']) && $_GET['is_paid'] == '1' ? 'selected' : '' }}>
                                            Paid</option>
                                        <option value="0"
                                            {{ isset($_GET['is_paid']) && $_GET['is_paid'] == '0' ? 'selected' : '' }}>
                                            Unpaid</option>
                                    </select>
                                </div>
                                <div class="col-lg-6 form-group">
                                    <label for="email">Transaction Hash</label>
                                    <input class="form-control" name="transaction_hash" type="text"
                                        placeholder="Enter here"
                                        value="{{ isset($_GET['transaction_hash']) && $_GET['transaction_hash'] != '' ? $_GET['transaction_hash'] : '' }}">
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
        <div class="col-lg-6">
            <h4 class="card-title">Generated Invoices</h4>
        </div>
        <div class="col-lg-6 text-right">
            @php
                $url = Request::fullUrl();
                $parsedUrl = parse_url($url);
                $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
                $subQueryString = $query != '' ? $query . '&type=xlsx' : '';
            @endphp

            @if (auth()->guard('admin')->user()->can(['export-generated-payout-reports']))
                @if (!empty($subQueryString))
                    <a href="{{ route('invoice-csv-export', [$subQueryString]) }}" class="btn btn-primary btn-sm"
                        data-filename="GenerateReport_Excel_"><i class="fa fa-download"></i>
                        Export Excel</a>
                @else
                    <a href="{{ route('invoice-csv-export') }}" class="btn btn-primary btn-sm"
                        data-filename="GenerateReport_Excel_"><i class="fa fa-download"></i>
                        Export Excel</a>
                @endif
            @endif
            @if (auth()->guard('admin')->user()->can(['update-generated-payout-reports']))
                <button id="deleteSelected" class="btn btn-danger btn-sm mx-1"><i class="fa fa-trash"></i>
                    Delete Selected Invoices </button>
            @endif
            <div class="d-inline" style="float: right;">
                <a class="btn btn-primary btn-sm" href="{{ route('invoices.create') }}"><i class="fa fa-plus"></i>
                    Create Invoice </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div>
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
                                data-bs-target="#searchModal"> Advanced
                                Search &nbsp; <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg>
                            </button>
                            <a href="{{ route('invoices.index') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-striped table-borderless">
                            <thead>
                                <tr>
                                    <th class="width50">
                                        <div class="common-check-main form-check">
                                            <label class="custom-control form-check-label mb-0">
                                                <input class="form-check-input" id="checkAll" type="checkbox"
                                                    required="">
                                                <span class="overflow-control-indicator"></span>
                                                <span class="overflow-control-description"></span>
                                            </label>
                                        </div>
                                    </th>
                                    <th>#</th>
                                    <th>Company name</th>
                                    <th>Agent name</th>
                                    <th>Invoice no</th>
                                    <th>Total amount</th>
                                    <th>Paid Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($invoices) && $invoices->count())
                                    @foreach ($invoices as $key => $value)
                                        <?php
                                        $IsCheckd = '';
                                        if ($value->is_paid == '1') {
                                            $IsCheckd = 'checked disabled';
                                        }
                                        ?>
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
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $value->business_name }}</td>
                                            <td>{{ $value->agent_name ? $value->agent_name : 'N/A' }}</td>
                                            <td>{{ $value->invoice_no }}</td>
                                            <td>${{ number_format($value->total_amount, 2, '.', ',') }}</td>
                                            <td class="text-center">
                                                @if (empty($value->transaction_hash) && $value->transaction_hash == null)
                                                    <label>N/A</label>
                                                @else
                                                    <label class="custom-control form-check-label">
                                                        <input data-bs-target="#paidStatus" data-bs-toggle="modal"
                                                            type="checkbox" name=""
                                                            class="form-check-input paidstatus"
                                                            data-id="{{ $value->id }}" value="{{ $value->id }}"
                                                            {{ $IsCheckd }}>
                                                        <span class="overflow-control-indicator"></span>
                                                        <span class="overflow-control-description"></span>
                                                    </label>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown ml-auto">
                                                    <button type="button"
                                                        class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                        data-bs-toggle="dropdown">
                                                        <svg width="5" height="17" viewBox="0 0 5 17"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                                        <a target="_blank" href="{{ getS3Url($value->invoice_url) }}"
                                                            class="dropdown-item"><i
                                                                class="fa fa-eye text-secondary me-2"></i>View</a>

                                                        <a href="{{ route('invoice.download', ['file' => $value->invoice_url]) }}"
                                                            class="dropdown-item"><i
                                                                class="fa fa-download text-primary me-2"></i>Download</a>


                                                        <a href="" data-bs-target="#transactionHashModal"
                                                            data-bs-toggle="modal" data-id="{{ $value->id }}"
                                                            data-hash="{{ $value->transaction_hash }}"
                                                            class="dropdown-item transaction_hash"><i
                                                                class="fa fa-ticket text-primary me-2"></i>Transaction
                                                            hash</a>

                                                        <a class="delete_modal dropdown-item"
                                                            data-url="{!! URL::route('invoices.destroy', $value->id) !!}"
                                                            data-id="{{ $value->id }}" href="javascript:void(0)"><i
                                                                class="fa fa-trash text-danger me-2"></i>Delete</a>

                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9">
                                            <p class="text-center"><strong>No Any records avilable.</strong></p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer">
                    @if (!empty($invoices) && $invoices->count())
                        <div class="row">
                            <div class="col-md-8">
                                {!! $invoices->appends($_GET)->links() !!}
                            </div>
                            <div class="col-md-4 text-right">
                                Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of total
                                {{ $invoices->total() }}
                                entries
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Transaction Hash Modal Start --}}
    <div class="modal right fade" id="transactionHashModal" tabindex="-1" role="dialog"
        aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Transaction Hash</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                {!! Form::open(['route' => 'invoice.updatedTransactionHash', 'method' => 'post', 'id' => 'transactionHash']) !!}
                {{ csrf_field() }}
                <input type="hidden" value="" name="invoice_id" class="invoice_id">
                <div class="modal-body form-dark">
                    <div class="form-group">
                        <label>Transaction Hash</label>
                        <input type="text" name="transaction_hash" class="form-control inputTransactionHash"
                            placeholder="Enter here..." value="" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">Submit</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Transaction Hash Modal End --}}
    {{-- Invoice Change Status Modal Start --}}
    <div class="modal right fade" id="paidStatus" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content form-dark">
                <div class="modal-header">
                    <h4 class="modal-title">Invoice Confirmation</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                {!! Form::open(['route' => 'make-invoice-paid', 'method' => 'post', 'id' => 'make-invoice-paid']) !!}
                {{ csrf_field() }}
                <input type="hidden" value="" name="is_paid" class="is_paid">
                <div class="modal-body">
                    Are you sure you want change status to Paid Invoice?
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">Yes</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Invoice Change Status Modal End --}}
@endsection
@section('customScript')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var modalInputID;

            $('.transaction_hash').click(function() {
                modalInputID = $(this).attr('data-id');
                modalInputHash = $(this).attr('data-hash');
                $('.invoice_id').val(modalInputID);
                $('.inputTransactionHash').val(modalInputHash);
            });
            $('.paidstatus').click(function() {
                modalInputPaidID = $(this).attr('data-id');
                $('.is_paid').val(modalInputPaidID);
            });
            $('body').on('change', '#selectallcheckbox', function() {
                if ($(this).prop("checked") == true) {
                    $('.multidelete').prop("checked", true);
                } else if ($(this).prop("checked") == false) {
                    $('.multidelete').prop("checked", false);
                }
            });
            // Delete multiple row with datatable
            $(document).on('click', '#deleteSelected', function() {
                var id = [];
                $('.multidelete:checked').each(function() {
                    id.push($(this).val());
                });
                console.log(id);
                if (id.length > 0) {
                    swal({
                            title: "Are you sure?",
                            text: "Once deleted, you will not be able to recover this record!",
                            icon: "warning",
                            buttons: true,
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                $.ajax({
                                    url: "{{ route('invoice.massdelete') }}",
                                    type: "POST",
                                    context: $(this),
                                    data: {
                                        id: id,
                                        type: "forall",
                                        _token: CSRF_TOKEN
                                    },
                                    beforeSend: function() {
                                        $(this).attr("disabled", "disabled");
                                    },
                                    success: function(data) {
                                        if (data.success == true) {
                                            toastr.success("Invoice deleted Successfully!");
                                            location.reload();
                                        } else {
                                            toastr.warning("Something went wrong!");
                                        }
                                        $(this).attr("disabled", false);
                                    }
                                });
                            }
                        })
                } else {
                    toastr.warning('Please select atleast one invoice !!');
                }
            });
            $("#resetForm").click(function() {
                $('#search-form').find("input[type=text], input[type=email], input[type=number]").val("");
                $(".select2").val("").select2();
            });
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
        });
    </script>
@endsection
