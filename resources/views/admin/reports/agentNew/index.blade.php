@extends('layouts.appAdmin')

@section('style')
<link href="{{ storage_asset('NewTheme/assets/lib/select2/css/select2.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ storage_asset('NewTheme/assets/css/dashforge.css') }}">
<link rel="stylesheet" href="{{ storage_asset('NewTheme/assets/css/dashforge.demo.css') }}">
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mg-b-15">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                <li class="breadcrumb-item"><a href="{!! url('admin/dashboard') !!}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Show Agent Reports</li>
            </ol>
        </nav>
        <h4 class="mg-b-0 tx-spacing--1">Show Agent Reports</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div data-label="Advanced Search" class="df-example demo-table">
            <form action="{{ route('admin.agent-reports-generate') }}" method="get">
                <div class="row">
                    <div class="col-lg-3">
                        <select class="form-control single-select" name="agent" id="agent">
                            <option selected disabled> -- Select Agent -- </option>
                            @foreach($agent as $key => $value)
                            <option value="{{ $value->id }}"
                                {{ (isset($_GET['agent']) && $_GET['agent'] == $value->name)?'selected':'' }}
                                data-id="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group {{ $errors->has('company_name') ? ' has-error' : '' }}">
                            <select class="form-control single-select" name="company_name" id="company_name">
                                <option selected disabled> -- Select Company Name -- </option>
                            </select>
                            @if ($errors->has('company_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('company_name') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <div class="input-group mg-b-10">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"
                                            id="fromList"></i></span>
                                </div>
                                <input type="text" id="autoclose-datepicker" class="form-control"
                                    placeholder="Start Date"
                                    value="{{ (isset($_GET['start_date']) && $_GET['start_date'] != '')?$_GET['start_date']:'' }}"
                                    name="start_date">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-group mg-b-10">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"
                                        id="toList"></i></span>
                            </div>
                            <input type="text" id="autoclose-datepicker1" class="form-control"
                                value="{{ (isset($_GET['end_date']) && $_GET['end_date'] != '')?$_GET['end_date']:'' }}"
                                placeholder="End Date" name="end_date">
                        </div>
                    </div>
                </div>
                <hr>
                <button class="btn btn-sm btn-success" type="submit">Submit</button>
                <a class="btn btn-sm btn-danger" href="{{ url('admin/agent-reports') }}">Reset</a>
            </form>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-12">
        <div data-label="Agent Report List" class="df-example demo-table">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th>Agent Name</th>
                            <th>User Name</th>
                            <th>Currency</th>
                            <th>Success Amount</th>
                            <th>Success Count</th>
                            <th>Commission Percentage</th>
                            <th>Total Commission</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($data) && $data->count())
                        @foreach($data as $key=>$value)
                        <tr>
                            <td>{{ $value->agentName }}</td>
                            <td>{{ ($value->company_name != '' ? $value->company_name : $value->userName) }}</td>
                            <td class="text-center">{{ $value->currency }}</td>
                            <td class="text-right">{{ number_format($value->amount,2            ,".",",") }}</td>
                            <td class="text-right">{{ number_format($value->count, 0            ,".",",") }}</td>
                            <td class="text-right">{{ number_format($value->commission,2        ,".",",") }} %</td>
                            <td class="text-right">{{ number_format($value->totalCommission,2   ,".",",") }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
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

        $('#startDateDiv').hide();
        $('#endDateDiv').hide();

        $('#autoclose-datepicker').datepicker({
            autoclose: true,
            todayHighlight: true
        });
        $('#autoclose-datepicker1').datepicker({
            autoclose: true,
            todayHighlight: true
        });
        $('.single-select').select2();

        $('#agent').on('change', function(event){
            event.preventDefault();
            agent = this.value;

            $.ajax({
                type: 'POST',
                url: "{{ URL::route('getCompanyByAgent') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'agent': agent
                },
                beforeSend: function() {
                    $('#company_name').attr('disabled', 'disabled');
                },
                success: function(data) {
                    $('#company_name').html(data.html);
                    $('#company_name').attr('disabled', false);
                },
            });
        });



        $('#company_name').on('change', function(event){
            event.preventDefault();
            user_id = this.value;

            $('#startDateDiv').show();
            $('#endDateDiv').show();

            $.ajax({
                type: 'POST',
                url: "{{ URL::route('getDateRangesByCompany') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'user_id': user_id
                },
                beforeSend: function() {
                    $('#startDate').attr('disabled', 'disabled');
                    $('#endDate').attr('disabled', 'disabled');
                },
                success: function(data) {
                    $('#startDate').html(data.startDate);
                    $('#startDate').attr('disabled', false);
                    $('#endDate').html(data.endDate);
                    $('#endDate').attr('disabled', false);
                },
            });
        });

        $('#startDate').on('change', function(event){
            $('#startDateDiv').hide();
            $('#autoclose-datepicker').val( this.value  );
        });

        $('#endDate').on('change', function(event){
            $('#endDateDiv').hide();
            $('#autoclose-datepicker1').val( this.value  );
        });


    });
</script>
@endsection