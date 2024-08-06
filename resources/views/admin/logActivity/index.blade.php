@extends('layouts.appAdmin')

@section('style')
    <link href="{{ storage_asset('NewTheme/assets/lib/datatables.net-dt/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ storage_asset('NewTheme/assets/lib/datatables.net-responsive-dt/css/responsive.dataTables.min.css') }}"
        rel="stylesheet">
    <link href="{{ storage_asset('NewTheme/assets/lib/select2/css/select2.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ storage_asset('NewTheme/assets/css/dashforge.css') }}">
    <link rel="stylesheet" href="{{ storage_asset('NewTheme/assets/css/dashforge.demo.css') }}">

    <style type="text/css">
        .demo-table .table th,
        .demo-table .table td {
            white-space: unset;
        }
    </style>
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mg-b-15">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                    <li class="breadcrumb-item"><a href="{!! url('paylaksa/dashboard') !!}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Log Activity</li>
                </ol>
            </nav>
            <h4 class="mg-b-0 tx-spacing--1">Log Activity</h4>
        </div>
        <div class="d-none d-md-block">
            <a href="" class="btn btn-danger remove-record btn-sm text-white" data-bs-target="#custom-width-modal"
                data-bs-toggle="modal" data-url="{{ route('log-activity-delete') }}" data-id="" style="height: auto;"><i
                    class="fa fa-trash"></i> Delete Logs Before 10 Days</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div data-label="Log Activity List" class="df-example demo-table">
                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Company Name</th>
                                <th>Subject</th>
                                <th>Query Type</th>
                                <th class="text-danger">URL</th>
                                <th>Method</th>
                                <th class="text-danger">IP</th>
                                <th style="width: 150px;">Agent</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Show Merchant Transaction Modal --}}
    <div class="modal fade" id="showTransactionNew" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg modal-full">
            <div class="modal-content border-primary">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Transaction Details</h5>
                    <button type="button" class="close closeRefundForm text-white" data-bs-dismiss="modal"
                        aria-label="Close">

                    </button>
                </div>
                <div class="modal-body" id="detailsContentNew">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="showTransaction" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg modal-full">
            <div class="modal-content border-primary">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Transaction Details</h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <div class="modal-body" id="detailsContent">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ storage_asset('NewTheme/assets/lib/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/assets/lib/datatables.net-dt/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/assets/lib/datatables.net-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script src="{{ storage_asset('NewTheme/assets/lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js') }}">
    </script>
    <script src="{{ storage_asset('NewTheme/assets/lib/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                // scrollX: true,
                "order": [
                    [0, "desc"]
                ],
                ajax: '{{ route('get-admin-log-activity') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'company_name',
                        name: 'company_name'
                    },
                    {
                        data: 'subject',
                        name: 'subject'
                    },
                    {
                        data: 'query_type',
                        name: 'query_type'
                    },
                    {
                        data: 'url',
                        name: 'url',
                        sClass: 'text-danger'
                    },
                    {
                        data: 'method',
                        name: 'method'
                    },
                    {
                        data: 'ip',
                        name: 'ip',
                        sClass: 'text-danger'
                    },
                    {
                        data: 'agent',
                        name: 'agent'
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
            $('.dataTables_length select').select2({
                minimumResultsForSearch: Infinity
            });
        });
    </script>
@endsection
