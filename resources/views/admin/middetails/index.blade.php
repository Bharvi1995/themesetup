@extends('layouts.admin.default')

@section('title')
    MIDs List
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">MIDs List</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">MIDs List</h6>
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
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">MIDs List</h4>
                    </div>
                    <div class="card-header-toolbar align-items-center">
                        <div class="btn-group mr-2">
                            @if (auth()->guard('admin')->user()->can(['create-mid']))
                                <a href="{{ url('paylaksa/mid-feature-management/create') }}" class="btn btn-primary">Create MID
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless datatable">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Bank Name</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Default Currency</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">MID Type</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                // scrollX: true,
                "order": [
                    [0, "desc"]
                ],
                ajax: '{{ route('get-middetails-data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'bank_name',
                        name: 'bank_name'
                    },
                    {
                        data: 'converted_currency',
                        name: 'converted_currency'
                    },
                    {
                        data: 'mid_type',
                        name: 'mid_type'
                    },
                    {
                        data: 'Actions',
                        name: 'Actions',
                        orderable: false,
                        serachable: false,
                        sClass: 'text-center'
                    },
                ]
            });
        });
    </script>
@endsection
