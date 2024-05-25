@extends('layouts.appAdmin')
@section('style')
@endsection
@section('content')
    <div id="section_one">
        <div class="heading-title">
            <h3> Log Activity </h3>
        </div>
    </div>
    <div id="section_Merchant" class="common-section pt-3 mt-4">
        <div class="row mx-auto">
            <div class="col-xl-12 col-sm-12 col-md-12 col-12">
                <div class="col-xl-12 col-sm-12 col-md-12 col-12 pl-2 pr-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="has-bottom-line title">Log Activity List</span></h4>
                    </div>
                </div>
            </div>
            <div class="col-12 pb-4 to-left-serach table-responsive mt-4">
                <!-- <span class="mb-2 clr-gray"> Your last 10 Transactions List </span> -->
                <table id="Log_activity_list" class="table table-hover responsive nowrap custom-inner-tables"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Company Name </th>
                            <th>Subject</th>
                            <th>Query Type</th>
                            <th>URL</th>
                            <th>Method</th>
                            <th>IP</th>
                            <th>Agent</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($data) > 0)
                            @foreach ($data as $logdata)
                                <tr>
                                    <td>{{ $logdata->id }}</td>
                                    <td>{{ $logdata->company_name }}</td>
                                    <td>{{ $logdata->subject }}</td>
                                    <td>{{ $logdata->query_type }}</td>
                                    <td><a href="{{ $logdata->url }}" class="blue-btn">{{ $logdata->url }}</a> </td>
                                    <td>{{ $logdata->method }}</td>
                                    <td> <span class="blue-btn"> {{ $logdata->ip }} </span> </td>
                                    <td> {{ $logdata->agent }} </td>
                                    <td>
                                        <a href="{{ route('admin-log-activity-show', [$logdata->id]) }}"
                                            class="table-action-btn btn-action-eye me-2"><i class="fas fa-eye"></i> </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    </div>



@endsection
@section('script')
@endsection
