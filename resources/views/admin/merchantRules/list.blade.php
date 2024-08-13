@extends('layouts.admin.default')
@section('title')
    Rules
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Merchant Rules List</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Merchant Rules List</h6>
    </nav>
@endsection

@section('customeStyle')
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">

    <style type="text/css">
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            color: #FFFFFF !important;
        }

        .dataTables_wrapper .dataTables_processing {
            background: #34383e !important
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #6683A9 !important;
            color: #FFFFFF !important;
            border-color: #34383e !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-color: #34383e !important;
        }

        div.dataTables_wrapper div.dataTables_filter label, div.dataTables_wrapper div.dataTables_length label{
            margin-top: 0px !important;
        }
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input{
            border: 1px solid #494949;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            @if (\Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top:20px">
                    <div class="alert-body">
                        {{ \Session::get('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (\Session::get('error'))
                <div class="alert alert-danger alert-dismissible show" role="alert" style="margin-top:20px">
                    <div class="alert-body">
                        <strong>Oh snap!</strong> {{ \Session::get('error') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Merchant Rules List</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-table">
                        <table id="rules_List" class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Merchant Name</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Merchant Email</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rules Name</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="max-width: 100px !important;">Rules</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">MID</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Priority</th>
                                </tr>
                            </thead>
                            <tbody id="tablecontents">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-center" id="assignMIDModal" tabindex="-1" role="dialog" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-sm modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign MID</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body" id="assignMIDContent">
                    <form id="assignMIDForm" class="form-dark">
                        @csrf

                        <select class="form-control select2" id="assignMID" name="assignMID" style="display:none;">
                            <option value="" disabled></option>
                            @foreach ($payment_gateway_id as $k => $p)
                                <option value="{{ $p->id }}">{{ $p->bank_name }}</option>
                            @endforeach
                        </select>

                        <span class="help-block text-danger">
                            <strong id="mid_error"></strong>
                        </span>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm" id="submitAssignMID">Submit</button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#assignMIDModal .select2').select2({
                dropdownParent: $('#assignMIDModal')
            });
        });
        var data_type = '<?php echo $type; ?>';
        $('#rules_List').DataTable({
            dragColumn: true,
            ajax: {
                url: '{{ route('rules.merchant.datatable') }}',
                type: "post",
                data: function(d) {
                    d.data_type = data_type;
                    d._token = '{{ csrf_token() }}';
                },
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'business_name',
                    name: 'business_name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'rules_name',
                    name: 'rules_name'
                },
                {
                    data: 'rule_condition_view',
                    name: 'rule_condition_view',
                    sClass: 'fixed-width'
                },
                {
                    data: 'bank_name',
                    name: 'bank_name'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action'
                },
                {
                    data: 'priority',
                    name: 'priority'
                },
            ],
            "order": [
                [6, "asc"]
            ]
        });
        // $( "#tablecontents" ).sortable({
        //       items: "tr",
        //       cursor: 'move',
        //       opacity: 0.6,
        //       update: function() {
        //           sendOrderToServer();
        //       }
        // });

        function sendOrderToServer() {
            var order = [];
            $('#rules_List tbody>tr').each(function(index, element) {
                order.push({
                    id: $(this).attr('data-id'),
                    position: index + 1
                });
            });
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('sort.rules') }}",
                data: {
                    order: order,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.success == true) {
                        toastr.success('Priority set Successfully  !!');
                    } else {
                        toastr.error('Something Went Wrong !!');
                    }
                    $("#rules_List").DataTable().ajax.reload();
                }
            });
        }

        $("#assignMID").select2();

        $('body').on('click', '#submitAssignMID', function(e) {
            e.preventDefault();
            $('#mid_error').html("");
            var formdata = $('#assignMIDForm').serialize();
            $.ajax({
                type: 'POST',
                context: $(this),
                url: "{{ URL::route('change-assign-mid') }}",
                data: formdata,
                beforeSend: function() {
                    $(this).attr('disabled', 'disabled');
                    $(this).html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                },
                success: function(data) {
                    if (data.success == true) {
                        toastr.success('MID assigned successfully');
                    } else {
                        toastr.error('Something Went Wrong !!');
                    }

                    $(this).attr('disabled', false);
                    $(this).html('Submit');
                    if (data.success == true || data.success == false) {
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                },
            });
        });


        function fnAssignMID(intId, intMID) {
            $("#assignMID").val('').trigger('change')
            if (intMID != '') {
                $("#assignMID").val(intMID).trigger('change')
            }
            $('#assignMIDForm').append('<input type="hidden" name="id" value="' + intId + '">');
        }

        $('body').on('click', '.deleteMID', function() {
            var id = $(this).data('id');
            if (confirm('Are you sure you want to delete this Rules?')) {
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: "{{ URL::route('delete-rules') }}",
                    data: {
                        '_token': CSRF_TOKEN,
                        'id': id
                    },
                    beforeSend: function() {
                        $(this).attr('disabled', 'disabled');
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Rules deleted Successfully !!')
                            location.reload();
                        } else {
                            toastr.warning('Something went wrong !!');
                        }
                        $(this).attr('disabled', false);
                    },
                });
            }
        });
    </script>
@endsection
