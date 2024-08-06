@extends('layouts.appAdmin')

@section('style')
    <link href="{{ storage_asset('NewTheme/assets/lib/select2/css/select2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ storage_asset('NewTheme/assets/css/dashforge.css') }}">
    <link rel="stylesheet" href="{{ storage_asset('NewTheme/assets/css/dashforge.demo.css') }}">
    <style type="text/css">
        .custom-control.custom-checkbox{
            margin-top: 10px !important;
        }
    </style>
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mg-b-15">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                <li class="breadcrumb-item"><a href="{!! url('paylaksa/dashboard') !!}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Generated Agent Reports</li>
            </ol>
        </nav>
        <h4 class="mg-b-0 tx-spacing--1">Automatic Generated Agent Reports</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div data-label="Generate Agent Report" class="df-example demo-table">
            <form action="{{ route('admin.new.agent-payout-generate-store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-3">
                        <input type="text" id="min-amount-box" id="minimum-amount" class="form-control" placeholder="Minimum amount" value="{{ old('amount') }}" name="amount" required>
                        @if ($errors->has('amount'))
                            <span class="help-block">
                                <strong>{{ $errors->first('amount') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <div class="input-group mg-b-10">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar" id="fromList"></i></span>
                                </div>
                                <input type="text" id="autoclose-datepicker" class="form-control" placeholder="Start Date" value="" name="start_date" required>
                            </div>
                            @if ($errors->has('start_date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('start_date') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-group mg-b-10">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar" id="toList"></i></span>
                            </div>
                            <input type="text" id="autoclose-datepicker1" class="form-control" value="" placeholder="End Date" name="end_date" required>
                        </div>
                        @if ($errors->has('end_date'))
                            <span class="help-block">
                                <strong>{{ $errors->first('end_date') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-lg-3">
                        <button class="btn btn-sm btn-success" type="submit">Generat Report</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<br />
<div class="row">
    <div class="col-md-8">
        <form class="" method="GET" id="search-form">
            <div class="row">
                <div class="col-md-4">
                    <select class="form-control single-select" name="agent" id="agent">
                        <option selected disabled> -- Select Agent -- </option>
                        @foreach($agent as $key => $value)
                            <option value="{{ $value->id }}" data-id="{{ $value->id }}"  {{ (isset($_GET['agent']) && $_GET['agent'] == $value->id)?'selected':'' }}>{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-control single-select" name="companyName" id="companyName">
                        <option selected disabled> -- Select Company Name -- </option>
                        @foreach($companyName as $key => $value)
                            <option value="{{ $key }}" {{ (isset($_GET['companyName']) && $_GET['companyName'] == $key)?'selected':'' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-success" type="submit" id="extraSearch">Search</button>
                    <a class="btn btn-danger" href="{!! route('admin.agent-payout-generate') !!}">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-12">
        <div data-label="Agent Report List" class="df-example demo-table">
            <div class="row">
                <div class="col-lg-6">
                    <label>
                        Show
                        <select name="DataTables_length" class="form-control form-control-sm" id="DataTables_length" style="display: inline-block;width: 75px;">
                            <option value="10" {{ ($noList == 10) ? "selected" : "" }}>10</option>
                            <option value="25" {{ ($noList == 25) ? "selected" : "" }}>25</option>
                            <option value="50" {{ ($noList == 50) ? "selected" : "" }}>50</option>
                            <option value="100" {{ ($noList == 100) ? "selected" : "" }}>100</option>
                        </select>
                        entries
                    </label>
                </div>
                <div class="col-lg-6 text-right">
                    <button type="button" id="bulk_delete" class="btn btn-danger btn-outline sbold pull-right btn-sm" style="margin-right: 5px;">
                        <i class="icon-trash"></i>
                        Delete Selected Reports
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%">
                    <thead>
                        <tr>
                            <td>*</td>
                            <th>Agent Name</th>
                            <th>Company Name</th>
                            <th>Generated Date</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Make Paid</th>
                            <th>Show on Agent Side</th>
                            <th>Files</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($data) && $data->count())
                            @foreach($data as $key=>$value)
                            <tr>
                                <td>
                                    <div class="md-checkbox has-error">
                                        <div class="custom-control custom-checkbox my-0">
                                            <input id="checkbox{{ $value->id }}" type="checkbox" name="multidelete[]" value="{{$value->id}}" class="mail-checkbox multidelete custom-control-input">
                                            <label for="checkbox{{ $value->id }}" class="custom-control-label"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $value->agent_name }}</td>
                                <td>{{ $value->company_name }}</td>
                                <td>{{ $value->date }}</td>
                                <td>{{ $value->start_date }}</td>
                                <td>{{ $value->end_date }}</td>
                                <td class="text-center">
                                    @if($value->is_paid == '1')
                                        <div class="custom-control custom-checkbox my-0">
                                            <input id="paidstatus{{ $value->id }}" type="checkbox" class="paidstatus custom-control-input" data-id="{{$value->id}}" checked>
                                            <label class="custom-control-label" for="paidstatus{{ $value->id }}"></label>
                                        </div>
                                    @else
                                        <div class="custom-control custom-checkbox my-0 has-error">
                                            <input id="paidstatus{{ $value->id }}" type="checkbox" class="paidstatus custom-control-input" data-id="{{$value->id}}">
                                            <label class="custom-control-label" for="paidstatus{{ $value->id }}"></label>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($value->show_agent_side == '1')
                                        <label class="badge badge-success">Yes</label>
                                        <div class="custom-control custom-checkbox my-0">
                                            <input id="showAgentSide{{ $value->id }}" type="checkbox" class="showAgentSide custom-control-input" data-id="{{$value->id}}" checked>
                                            <label class="custom-control-label" for="showAgentSide{{ $value->id }}"></label>
                                        </div>
                                    @else
                                        <label class="badge badge-danger">No</label>
                                        <div class="custom-control custom-checkbox my-0 has-error">
                                                <input id="showAgentSide{{ $value->id }}" type="checkbox" class="showAgentSide custom-control-input" data-id="{{$value->id}}">
                                                <label class="custom-control-label" for="showAgentSide{{ $value->id }}"></label>
                                            </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($value->files)
                                        @php
                                            $files = '';
                                        @endphp
                                        @foreach(json_decode($value->files) as $key => $file)
                                            @php
                                                $files = $files.' <a href="/'.$file.'" target="_blank" class="btn btn-sm btn-icon-only btn-info">
                                                        <i class="fa fa-file"></i>
                                                    </a>';
                                            @endphp
                                        @endforeach
                                        {!! $files !!}
                                    @else
                                        ---
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.new.agent-payout-upload-files', $value->id) }}" class="btn btn-sm btn-success btn-outline">Upload Files</a>
                                    <a href="{{ route('admin.new.agent-payout-reports-pdf', $value->id) }}" class="btn btn-sm btn-danger btn-outline">PDF</a>
                                    <a href="{{ route('admin.new.agent-payout-reports-show', $value->id) }}" target="_blank" class="btn btn-sm btn-info btn-outline">View</a>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <br>
                <div style="float: right;">
                    {!! $data->appends($_GET)->links() !!}
                </div>
                <br>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="{{ storage_asset('NewTheme/assets/lib/feather-icons/feather.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/assets/lib/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/assets/lib/prismjs/prism.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/assets/lib/jqueryui/jquery-ui.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/assets/lib/select2/js/select2.min.js') }}"></script>
    <script>
    $(document).ready(function() {

        // change pagination
        $('body').on('change', '#DataTables_length', function(){
            var noList = $(this).val();
            window.location.replace(current_page_url+'?noList='+noList);
        });

        $('#autoclose-datepicker').datepicker({
            autoclose: true,
            todayHighlight: true
        });
        $('#autoclose-datepicker1').datepicker({
            autoclose: true,
            todayHighlight: true
        });
        $('.single-select').select2();

        $('#startDate').on('change', function(event) {
            $('#autoclose-datepicker').val(this.value);
        });

        $('#endDate').on('change', function(event) {
            $('#autoclose-datepicker1').val(this.value);
        });

        // make paid
        $('body').on('change', '.paidstatus', function() {
            if($(this).prop("checked") == true){
                var status = '1';
            } else if($(this).prop("checked") == false){
                var status = '0';
            }
            var id = $(this).data('id');
            $.ajax({
                type: 'POST',
                context: $(this),
                url: "{{ URL::route('admin.new.agent-payout-make-report-paid') }}",
                data: {
                    '_token': CSRF_TOKEN,
                    'status': status, 'id': id
                },
                beforeSend: function() {
                    $(this).attr('disabled', 'disabled');
                },
                success: function(data) {
                    if(data.success == true) {
                        Lobibox.notify('success', {
                            pauseDelayOnHover: true,
                            continueDelayOnInactiveTab: false,
                            position: 'top right',
                            icon: 'fa fa-check-circle',
                            msg: 'Report paid status updated successfully !!'
                        });
                    } else {
                        Lobibox.notify('error', {
                            pauseDelayOnHover: true,
                            continueDelayOnInactiveTab: false,
                            position: 'top right',
                            icon: 'fa fa-check-circle',
                            msg: 'Something went wrong !!'
                        });
                    }
                    $(this).attr('disabled', false);
                },
            });
            setTimeout(function(){
               window.location.reload(1);
            }, 2000);
        });

        // report show agent side
        $('body').on('change', '.showAgentSide', function() {
            if($(this).prop("checked") == true) {
                var status = '1';
            } else if($(this).prop("checked") == false) {
                var status = '0';
            }
            var id = $(this).data('id');
            $.ajax({
                type: 'POST',
                context: $(this),
                url: "{{ URL::route('admin.new.agent-payout-show-agent-side') }}",
                data: {
                    '_token': CSRF_TOKEN,
                    'status': status, 'id': id
                },
                beforeSend: function() {
                    $(this).attr('disabled', 'disabled');
                },
                success: function(data) {
                    if(data.success == true)
                        Lobibox.notify('success', {
                            pauseDelayOnHover: true,
                            continueDelayOnInactiveTab: false,
                            position: 'top right',
                            icon: 'fa fa-check-circle',
                            msg: 'Report show client site successfully !!'
                        });
                    else
                        Lobibox.notify('error', {
                            pauseDelayOnHover: true,
                            continueDelayOnInactiveTab: false,
                            position: 'top right',
                            icon: 'fa fa-check-circle',
                            msg: 'Something went wrong !!'
                        });
                    $(this).attr('disabled', false);
                },
            });
            setTimeout(function(){
               window.location.reload(1);
            }, 2000);
        });

        // Delete multiple row with datatable
        $(document).on('click', '#bulk_delete', function() {
            var id = [];

            if(confirm("Are you sure to delete this report?")) {
                $('.multidelete:checked').each(function(){
                    id.push($(this).val());
                });
                if(id.length > 0) {
                    $.ajax({
                        url:"{{ route('admin.new.agent-payout-report-mass-delete')}}",
                        method:"get",
                        data:{id:id},
                        success:function(data)
                        {
                            Lobibox.notify('success', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                icon: 'fa fa-check-circle',
                                msg: 'Selected Report Delete Successfully!!'
                            });
                            $('.datatable').DataTable().ajax.reload();
                        }
                    });
                    setTimeout(function(){
                       window.location.reload(1);
                    }, 2000);
                } else {
                    Lobibox.notify('warning', {
                        pauseDelayOnHover: true,
                        continueDelayOnInactiveTab: false,
                        position: 'top right',
                        icon: 'fa fa-check-circle',
                        msg: 'Please select atleast one report !!'
                    });
                }
            }
        });
    });
    </script>
@endsection
