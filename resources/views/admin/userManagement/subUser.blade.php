@extends('layouts.admin.default')
@section('title')
    Sub User Management
@endsection
@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Sub Users Management</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Sub Users Management</h6>
    </nav>
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
                        @if (isset($_GET) && $_GET != '')
                            @foreach ($_GET as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        @endif
                        <div class="basic-form">
                            <div class="row ">
                                <div class="form-group col-md-6">
                                    <label>Select Main MID</label>
                                    <select class="form-select" name="payment_gateway_id" data-size="7" data-live-search="true"
                                        data-title="Select Main MID" id="payment_gateway_id" data-width="100%">
                                        <option selected disabled> -- Select Main MID -- </option>
                                        @foreach ($payment_gateway_id as $key => $value)
                                            <option value="{{ $value->id }}">{{ $value->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Business Name</label>
                                    <input class="form-control" type="text" name="business_name" id="business_name"
                                        placeholder="Enter here..."
                                        value="{{ isset($_GET['business_name']) && $_GET['business_name'] != '' ? $_GET['business_name'] : '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Email</label>
                                    <input class="form-control" type="text" placeholder="Enter here..." name="email"
                                        id="email"
                                        value="{{ isset($_GET['email']) && $_GET['email'] != '' ? $_GET['email'] : '' }}">
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
        <div class="col-lg-12">
            <div class="card mt-1">
                <div class="card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Sub User Management</h4>
                    </div>
                    <div class="card-header-toolbar align-items-center">
                        <div class="btn-group mr-2">
                            <button type="button" class="btn btn-primary  btn-sm" data-bs-target="#searchModal"
                                data-bs-toggle="modal"> Advanced
                                Search &nbsp; <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg></button>
                            <a href="{{ route('sub-user', $id) }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                        <a href="#" class="btn btn-outline-primary btn-sm" id="SendMail" data-bs-toggle="modal"
                            data-bs-target="#Send_email"> Send Mail</a>
                        <a href="{{ route('sub-user', ['id' => $id, 'type' => 'xlsx', 'ids' => $id] + request()->all()) }}"
                            class="btn btn-warning btn-sm" id="ExcelLink"> Export
                            Excel</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        <div class="custom-control form-check form-check-input custom-control-inline mr-0">
                                            <input type="checkbox" class="form-check-input multicheckmail"
                                                id="selectallcheckbox">
                                            <label class="form-check-label" for="selectallcheckbox"></label>
                                        </div>
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created AT</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($dataT) && $dataT->count())
                                    @foreach ($dataT as $key => $data)
                                        <tr>
                                            <td class="align-middle text-center text-sm">
                                                <div class="custom-control form-check custom-control-inline mr-0">
                                                    <input type="checkbox" class="form-check-input multicheckmail"
                                                        id="checkbox{{ $data->id }}" name="multicheckmail[]"
                                                        value="{{ $data->id }}">
                                                    <label class="form-check-label"
                                                        for="checkbox{{ $data->id }}"></label>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">{{ $data->name }}</td>
                                            <td class="align-middle text-center text-sm">{{ $data->email }}</td>
                                            <td class="align-middle text-center text-sm">
                                                {{ convertDateToLocal($data->created_at, 'Y-m-d') }}
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <div class="dropdown">
                                                    <a href="javascript:;" class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownMenuLink2">
                                                    </a>
                                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink2">
                                                        <li><a href="{{ \URL::route('sub-users-edit', $data->id) }}"
                                                            class="dropdown-item">
                                                            Edit</a></li>
                                                        <li><a href="{{ URL::to('/') }}/userLogin?email={{ $data->email }}"
                                                            target="_blank" class="dropdown-item">
                                                            Login</a></li>

                                                        <li><a href="javascript:void(0)" class="dropdown-item delete_modal"
                                                            data-url="{{ \URL::route('sub-users-delete', $data->id) }}"
                                                            data-id="{{ $data->id }}">
                                                            Delete</a></li>

                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="align-middle text-center text-sm" colspan="6">
                                            <p class="text-center"><strong>No record found.</strong></p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer">
                    @if (!empty($dataT) && $dataT->count())
                        <div class="row">
                            <div class="col-md-8">
                                {!! $dataT->appends($_GET)->links() !!}
                            </div>
                            <div class="col-md-4 text-right">
                                Showing {{ $dataT->firstItem() }} to {{ $dataT->lastItem() }} of total
                                {{ $dataT->total() }}
                                entries
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal right fade" id="Send_email" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Mail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <form id="SendMailForm" method="POST" enctype="multipart/form-data" class="form-dark">
                    @csrf
                    <div class="modal-body" id="SendMailBody">
                        <div class="col-xl-12 col-sm-12 col-md-12 col-12 mb-4">
                            <input class="form-control" type="text" name="subject" id="subject"
                                placeholder="Enter Subject" value="">
                            <span class="help-block text-danger">
                                <strong id="er_subject"></strong>
                            </span>
                        </div>
                        <div class="col-xl-12 col-sm-12 col-md-12 col-12 mb-4">
                            <textarea class="form-control" name="bodycontent" id="bodycontent" placeholder="Enter Mail Text Here...."
                                rows="5"></textarea>
                            <span class="help-block text-danger">
                                <strong id="er_bodycontent"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer-fixed">
                        <button type="button" class="btn btn-primary " id="submitSendMail">Send Mail</button>
                        <button type="button" class="btn btn-danger " data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script type="text/javascript">
        $(document).ready(function() {
            var CSRF_TOKEN = "{{ csrf_token() }}";
            //   $('#payment_gateway_id').select2();
            var ids = "{{ $id }}";

            $('body').on('change', '#DataTables_length', function() {
                var noList = $(this).val();
                window.location.replace(current_page_url + '?noList=' + noList);
            });
            // Delete multiple row with datatable
            $('body').on('change', '#selectallcheckbox', function() {
                if ($(this).prop("checked") == true) {
                    $('.multicheckmail').prop("checked", true);
                } else if ($(this).prop("checked") == false) {
                    $('.multicheckmail').prop("checked", false);
                }
            });
            $('body').on('click', '#submitSendMail', function() {
                var id = [];
                $('.multicheckmail:checked').each(function() {
                    id.push($(this).val());
                });
                var formData = new FormData($('#SendMailForm')[0]);
                formData.append('id', id);
                console.log(formData);

                if (id.length > 0) {
                    $.ajax({
                        url: "{{ route('send-user-multi-mail') }}",
                        method: "POST",
                        context: $(this),
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $(this).attr('disabled', 'disabled');
                            $(this).html(
                                '<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                        },
                        success: function(data) {
                            console.log(data);
                            if (data.errors) {
                                if (data.errors.subject) {
                                    $('#er_subject').html(data.errors.subject[0]);
                                }
                                if (data.errors.bodycontent) {
                                    $('#er_bodycontent').html(data.errors.bodycontent[0]);
                                }
                            }

                            if (data.success) {
                                toastr.success('Mail Send Successfully!!');
                                window.setTimeout(function() {
                                    location.reload(true)
                                }, 2000);
                            }
                            $(this).attr('disabled', false);
                            $(this).html('Submit');
                            window.setTimeout(function() {
                                location.reload(true)
                            }, 2000);
                        }
                    });
                } else {
                    toastr.error('Please select atleast one user !!');
                }

            });
        });
    </script>
@endsection
