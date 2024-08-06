@extends('layouts.admin.default')

@section('title')
    Merchant
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Merchant Stores
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
                                    <label for="">Store Name</label>
                                    <input class="form-control" name="name" type="text" placeholder="Enter here"
                                        value="{{ isset($_GET['name']) && $_GET['name'] != '' ? $_GET['name'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Email</label>
                                    <input class="form-control" name="email" type="email" placeholder="Enter here"
                                        value="{{ isset($_GET['email']) && $_GET['email'] != '' ? $_GET['email'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Merchant</label>
                                    <select name="user_id" class="form-control select2" data-placeholder="Select Merchant">
                                        <option value="">Select Merchant</option>
                                        @foreach ($merchants as $merchant_key => $merchant_value)
                                            <option value="{{ $merchant_value->id }}"
                                                @if (isset($_GET['user_id']) && $_GET['user_id'] == $merchant_value->id) selected @endif>
                                                {{ $merchant_value->name }}</option>
                                        @endforeach
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
                        <h4 class="card-title">Merchant Stores</h4>
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
                            <a href="{{ url('paylaksa/merchant-stores') }}" class="btn btn-primary btn-sm">Reset</a>
                        </div>
                        @if (auth()->guard('admin')->user()->can(['merchant-store-excel-export']))
                            <a href="{{ route('user-stores-csv-export') }}" class="btn btn-info btn-sm" id="ExcelLink">
                                <i class="fa fa-download"></i>
                                Export Excel
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Store Name</th>
                                    <th>Email</th>
                                    <th>Merchant Name</th>
                                    <th>Currency</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dataT as $key => $data)
                                    <tr>
                                        <td>
                                            <div class="dropdown">
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
                                                    @if (auth()->guard('admin')->user()->can(['merchant-store-products']))
                                                        <li class="dropdown-item"><a
                                                                href="{{ route('admin.merchant.stores.products', $data->id) }}"
                                                                class="user-show" class="dropdown-item"><i
                                                                    class="fa fa-product-hunt text-success me-2"></i>
                                                                Products</a></li>
                                                    @endif
                                                    @if (auth()->guard('admin')->user()->can(['view-merchant-stores']))
                                                        <li class="dropdown-item"><a target="_blank"
                                                                href="{{ route('stores.index', $data->slug) }}"
                                                                class="user-show" class="dropdown-item"><i
                                                                    class="fa fa-eye text-success me-2"></i>
                                                                Show</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $data->name }}
                                        </td>
                                        <td>{{ $data->email }}</td>
                                        <td>{{ $data->user->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $data->currency }}
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

    <div class="modal right fade" id="Send_email" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Send Mail</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <form id="SendMailForm" method="POST" enctype="multipart/form-data">
                    <div class="modal-body" id="SendMailBody">
                        @csrf

                        <div class="form-group">
                            <label>Select Template</label>
                            {{-- {!! Form::select('email_template', [''=>'-- Select Template --','addCustom'=>'Add
                Custom']+$template, [], array('class' => 'form-control','id'=>'emailTemplate')) !!}
                <span class="help-block text-danger">
                    <strong id="er_email_template"></strong>
                </span> --}}
                        </div>

                        <div class="form-group form-group-none">
                            <input class="form-control" type="text" name="subject" id="subject" required=""
                                placeholder="Enter Subject">
                            <span class="help-block text-danger">
                                <strong id="er_subject"></strong>
                            </span>
                        </div>

                        <div class="form-group form-group-none">
                            <textarea class="form-control" name="bodycontent" id="bodycontent" required=""
                                placeholder="Enter Mail Text Here...." rows="6"></textarea>
                            <span class="help-block text-danger">
                                <strong id="er_bodycontent"></strong>
                            </span>
                        </div>
                        <div class="file-attached"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="submitSendMail" class="btn btn-info">Submit</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                            id="closeReassignForm">Close</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="modal right fade" id="editAgentModal" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Referral Partners List</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>

                <div class="modal-body" id="">
                    <div class="user-id"></div>
                    <div class="row">
                        <div class="col-xl-12 col-sm-12 col-md-12 col-12 mb-4">
                            <label>Select Referral Partner</label>
                            <select class="select2" data-size="7" data-live-search="true"
                                data-title="Select Referral Partners" id="selectAgent" data-width="100%">
                                <option selected disabled>Select here</option>
                                {{-- @foreach ($agents as $agent)
            <option value="{{ $agent->id }}">{{ $agent->name . " - " . $agent->email }}</option>
            @endforeach --}}
                            </select>
                            <span class="help-block text-danger">
                                <span id="agent_error"></span>
                            </span>
                        </div>
                        <form class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-6 col-sm-6 col-md-6 col-6 mb-4">
                                    <label>Enter Commission For Visa</label>
                                    <input type="number" id="commission" name="commission" placeholder="Commission"
                                        class="form-control" value="{{ Input::get('commission') }}">

                                    <span class="help-block text-danger">
                                        <span id="commission_error"></span>
                                    </span>
                                </div>

                                <div class="col-xl-6 col-sm-6 col-md-6 col-6 mb-4">
                                    <label>Enter Commission For Master</label>
                                    <input type="number" id="commission_master" name="commission_master"
                                        placeholder="Commission" class="form-control"
                                        value="{{ Input::get('commission_master') }}">

                                    <span class="help-block text-danger">
                                        <span id="commission_master_error"></span>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer modal-footer-fixed">
                    <button type="button" class="btn btn-success btn-sm" id="saveEditAgent">Save</button>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal right fade" id="user_list" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User Details </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <div class="modal-body" id="userDetailsContent">

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>

    </div>


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
            CKEDITOR.replace("bodycontent", {
                height: "200px"
            });
            //select all checkbox for action
            $('body').on('change', '#selectallcheckbox', function() {
                if ($(this).prop("checked") == true) {
                    $('.multicheckmail').prop("checked", true);
                } else if ($(this).prop("checked") == false) {
                    $('.multicheckmail').prop("checked", false);
                }
            });

            //submit multiple mail
            $('body').on('change', '#emailTemplate', function() {
                $('#SendMailForm .form-group').removeClass('form-group-none');
                var temp = $(this).val();
                if (temp != 'addCustom') {
                    $.ajax({
                        url: "{{ route('get-template-data') }}?id=" + temp,
                        method: "GET",
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $(this).attr('disabled', 'disabled');
                            $(this).html(
                                '<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                        },
                        success: function(data) {
                            // console.log(data);
                            $('#subject').val(data.email_subject);
                            CKEDITOR.instances['bodycontent'].setData(data.email_body);
                            if (data.files != null) {
                                var resultArray = JSON.parse(data.files);
                                $('.file-attached').html(
                                    '<p class="text-danger"><strong>File attached :</strong> ' +
                                    resultArray.length + ' files attached</p>');
                            } else {
                                $('.file-attached').html('');
                            }
                        }
                    });
                } else {
                    $('#subject').val("");
                    $('.file-attached').html('');
                    CKEDITOR.instances['bodycontent'].setData("");
                }
            });

            $('body').on('click', '#submitSendMail', function() {
                var id = [];
                $('.multidelete:checked').each(function() {
                    id.push($(this).val());
                });
                var formData = new FormData($('#SendMailForm')[0]);
                formData.append('id', id);
                var desc = CKEDITOR.instances['bodycontent'].getData();
                formData.append('bodycontent', desc);

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
                                if (data.errors.email_template) {
                                    $('#er_email_template').html(data.errors.email_template[0]);
                                }
                                if (data.errors.subject) {
                                    $('#er_subject').html(data.errors.subject[0]);
                                }
                                if (data.errors.bodycontent) {
                                    $('#er_bodycontent').html(data.errors.bodycontent[0]);
                                }
                            }

                            if (data.success) {
                                $('.modal.fade').removeClass('in');
                                $('.modal-backdrop.fade').removeClass('in');
                                toastr.success('Mail Send Successfully!');
                                $(this).attr('disabled', false);
                                $(this).html('Submit');
                                window.setTimeout(
                                    function() {
                                        location.reload(true)
                                    },
                                    2000
                                );
                            }
                            $(this).attr('disabled', false);
                            $(this).html('Submit');
                        }
                    });
                } else {
                    toastr.warning('Please select atleast one user!');
                }

            });
            // Delete multiple row with datatable
            $(document).on('click', '#bulk_delete', function() {
                var id = [];
                $('.multicheckmail:checked').each(function() {
                    id.push($(this).val());
                });
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
                                    url: "{{ route('merchant-user-masstransactions') }}",
                                    method: "get",
                                    data: {
                                        id: id
                                    },
                                    success: function(data) {
                                        toastr.success(
                                            'Selected User Delete Successfully!!');
                                        window.setTimeout(
                                            function() {
                                                location.reload(true)
                                            },
                                            2000
                                        );
                                    }
                                });
                            }
                        })
                } else {
                    toastr.error('Please select atleast one user !!');
                }
            });
            //agent remove from merchant
            $('body').on('click', '.agent-remove-from-merchant', function() {
                var url = $(this).attr("data-url");
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
                                url: url,
                                method: "get",
                                success: function(data) {
                                    if (data == 1) {
                                        toastr.success('Agent Remove Successfully!!');
                                        window.setTimeout(
                                            function() {
                                                location.reload(true)
                                            }, 2000);
                                    } else {
                                        toastr.error('Something went wrong !!');
                                        window.setTimeout(
                                            function() {
                                                location.reload(true)
                                            }, 2000);
                                    }

                                }
                            });
                        }
                    });
            });
            // Get total amount
            $('body').on('click', '.showTransactionAmount', function() {
                var id = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: "{{ URL::route('get-user-total-amount') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': id
                    },
                    beforeSend: function() {
                        $('#transDetails').html(
                            '<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.count === 0) {
                            $('#transDetails').html(
                                '<h2 class="text-center text-danger"> No record found. </h2>'
                            );
                        } else {
                            if (data.success == true)
                                $('#transDetails').html(data.html)
                            else
                                toastr.error('Something went wrong !!');
                        }
                    },
                });
            });
            // show user details
            $('body').on('click', '.user-show', function() {
                var id = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    url: '{{ route('show-user-details') }}',
                    data: {
                        'id': id,
                        '_token': "{{ csrf_token() }}"
                    },
                    context: $(this),
                    beforeSend: function() {
                        $('#userDetailsContent').html(
                            '<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                    },
                    success: function(data) {
                        $('#userDetailsContent').html(data.html);
                    },
                });
            });
            // Change Password
            $('body').on('click', '.changePassBtn', function(e) {
                var ID = $(this).attr('data-id');
                $('#changePasswordForm').append('<input type="hidden" name="id" value="' + ID + '">');
            });
            $('body').on('click', '#submitChangePass', function(e) {
                e.preventDefault();
                $('#refund_error').html("");
                var formdata = $('#changePasswordForm').serialize();
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: "{{ URL::route('change-password') }}",
                    data: formdata,
                    beforeSend: function() {
                        $(this).attr('disabled', 'disabled');
                        $(this).html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                    },
                    success: function(data) {
                        if (data.errors) {
                            if (data.errors.conform_password) {
                                $('#er_conform_password').html(data.errors.conform_password[0]);
                            }
                            if (data.errors.password) {
                                $('#er_password').html(data.errors.password[0]);
                            }
                        }
                        if (data.success == true)
                            toastr.success('Password Change Successfully !!');
                        else
                            toastr.error('Something went wrong !!');
                        $(this).attr('disabled', false);
                        $(this).html('Submit');
                        if (data.success == true || data.success == false) {
                            setInterval(function() {
                                location.reload();
                            }, 2000);
                        }
                    },
                });
            });
            //add agent
            $('body').on('click', '.btn-editAgentModal', function(e) {
                e.preventDefault();
                var user_id = $(this).attr('data-user');
                var agent_id = $(this).attr('data-agent');
                var agent_commission = $(this).attr('data-agent-commission');
                var agent_commission_master = $(this).attr('data-agent-commission-master');
                $('#editAgentModal .modal-body').find('.user-id').html(
                    '<input type="hidden" id="user_id" value="' + user_id + '">');
                $('#editAgentModal .modal-body').find('#commission').val(agent_commission);
                $('#editAgentModal .modal-body').find('#commission_master').val(agent_commission_master);
                if (agent_id != '') {
                    $('#editAgentModal #selectAgent').val(agent_id).change();
                }
                $('#editAgentModal').modal('show');
            });

            $('body').on('click', '#saveEditAgent', function(e) {
                e.preventDefault();
                $('#agent_error').html("");
                $('#commission_error').html("");
                $('#commission_master_error').html("");
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: "{{ URL::route('user-set-agent') }}",
                    data: {
                        _token: function() {
                            return "{{ csrf_token() }}";
                        },
                        user_id: function() {
                            return $('#editAgentModal #user_id').val();
                        },
                        agent_id: function() {
                            return $('#editAgentModal #selectAgent').val();
                        },
                        commission: function() {
                            return $('#editAgentModal #commission').val();
                        },
                        commission_master: function() {
                            return $('#editAgentModal #commission_master').val();
                        }
                    },
                    beforeSend: function() {
                        $(this).attr('disabled', 'disabled');
                        $(this).html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                    },
                    success: function(data) {
                        if (data.errors) {
                            if (data.errors.agent_id) {
                                $('#agent_error').html(data.errors.agent_id[0]);
                            }
                            if (data.errors.commission) {
                                $('#commission_error').html(data.errors.commission[0]);
                            }
                            if (data.errors.commission_master) {
                                $('#commission_master_error').html(data.errors
                                    .commission_master[0]);
                            }
                            $(this).attr('disabled', false);
                            $(this).html('Submit');
                        } else {
                            if (data.success == true)
                                toastr.success('Referral Partner Saved Successfully !!');
                            else
                                toastr.error('Something went wrong !!');
                            $(this).attr('disabled', false);
                            $(this).html('Submit');
                            $('#editAgentModal #user_id').remove();
                            $('#editAgentModal').modal('hide');
                            setInterval(function() {
                                location.reload();
                            }, 2000);
                        }
                    },
                });
            });

            $('body').on('change', 'input[name="is_active"]', function() {
                var id = $(this).data('id');
                var is_active = '1';

                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var is_active = '0';
                }

                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('user-deactive') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'is_active': is_active,
                        'id': id
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Merchant activation changed successfully!!');
                        } else {
                            toastr.error('Something went wrong!!');
                        }
                    },
                });
            });

            $('body').on('change', 'input[name="is_otp_required"]', function() {
                var id = $(this).data('id');
                var is_otp = '0';

                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var is_otp = '1';
                }

                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('user-otp-required') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'is_otp': is_otp,
                        'id': id
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Merchant otp login changed successfully!!');
                        } else {
                            toastr.error('Something went wrong!!');
                        }
                    },
                });
            });

            $('body').on('change', 'input[name="isipremove"]', function() {
                var id = $(this).data('id');
                var is_ip_remove = '0';

                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var is_ip_remove = '1';
                }
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('user-ip-remove') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'is_ip_remove': is_ip_remove,
                        'id': id
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Merchant IP removed changed successfully!!');
                        } else {
                            toastr.error('Something went wrong!!');
                        }
                    },
                });
            })

            $('body').on('change', 'input[name="isdisablerule"]', function() {
                var id = $(this).data('id');
                var is_disable_rule = '0';

                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var is_disable_rule = '1';
                }
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('user-disable-rules') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'is_disable_rule': is_disable_rule,
                        'id': id
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Merchant disable rules changed successfully!!');
                        } else {
                            toastr.error('Something went wrong!!');
                        }
                    },
                });
            })

            $('body').on('change', 'input[name="isBinRemove"]', function() {
                var id = $(this).data('id');
                var is_bin_remove = '0';

                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var is_bin_remove = '1';
                }
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('user-bin-remove') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'is_bin_remove': is_bin_remove,
                        'id': id
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Merchant BIN remove changed successfully!!');
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
