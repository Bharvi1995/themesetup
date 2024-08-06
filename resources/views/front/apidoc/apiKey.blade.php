@extends('layouts.user.default')

@section('title')
    IP Support
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('dashboardPage') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">IP Support</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">IP Support</h6>
    </nav>
@endsection

@section('content')
    @if (!empty($data->api_key))
        

        <div class="col-xxl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h5 class="card-title">IP Support</h5>
                    </div>
                    <div class="card-header-toolbar align-items-center">
                        <div class="btn-group mr-2">
                            <a href="{{ route('whitelist-ip-csv-export') }}" class="btn btn-secondary" id="ExcelLink">
                                Export Excel
                            </a>
                            <a href="{{ route('whitelist-ip-add') }}" class="btn btn-primary ml-2">Add IP</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-borderless ">
                        <thead>
                            <tr>
                                <!-- <th>Website URL</th> -->
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">IP Address</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($apiWebsiteUrlIP as $key => $value)
                                <tr>
                                    <!-- <td>{{ $value->website_name }}</td> -->
                                    <td class="align-middle text-center text-sm">{{ $value->ip_address }}</td>
                                    <td class="align-middle text-center text-sm">
                                        @if ($value->is_active == '0')
                                            <label class="badge badge-sm bg-gradient-secondary">Pending</label>
                                        @else
                                            <label class="badge badge-sm bg-gradient-success">Approved</label>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <a href="javascript:;" class="text-secondary font-weight-bold text-xs delete_modal" data-toggle="tooltip" data-original-title="Delete IP" data-id="{{ $value->id }}" data-url="{{ route('deleteWebsiteUrl', $value->id) }}"> Delete</a>
                                        <!-- <a href="javascript:void(0)" class="btn btn-danger btn-sm delete_modal"
                                            data-id="{{ $value->id }}"
                                            data-url="{{ route('deleteWebsiteUrl', $value->id) }}"><i
                                                class="fa fa-trash"></i></a> -->
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
@endsection
