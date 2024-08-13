@extends('layouts.admin.default')
@section('title')
    Sub Gateway
@endsection
@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.gateway.index') }}">Gateway List</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Sub Gateway</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Sub Gateway</h6>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ $gateway->title }} Gateway Details</h4>
                    </div>
                    <div class="card-header-toolbar align-items-center">
                        <div class="btn-group mr-2">
                            @if (auth()->guard('admin')->user()->can(['create-sub-gateway']))
                                <a href="{{ route('admin.subgateway.create', ['gateway_id' => $gateway->id]) }}"
                                    class="btn btn-primary pull-right">Create {{ $gateway->title }} MID</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Id</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                    @foreach (json_decode($gateway->credential_titles) as $title)
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ $title }}</th>
                                    @endforeach
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subgateways as $subgateway)
                                    <tr>
                                        <td class="align-middle text-center text-sm">{{ $subgateway->id }}</td>
                                        <td class="align-middle text-center text-sm">{{ $subgateway->name }}</td>
                                        @foreach (json_decode($gateway->credential_titles) as $key => $value)
                                            <td class="align-middle text-center text-sm">{{ $subgateway->$key }}</td>
                                        @endforeach
                                        <td class="align-middle text-center text-sm">
                                            <div class="dropdown">
                                                <a href="javascript:;" class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownMenuLink2">
                                                </a>
                                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink2">
                                                    @if (auth()->guard('admin')->user()->can(['update-sub-gateway']))
                                                    <li>
                                                    <a href="{{ route('subGatway-edit-data', ['gateway_id' => $gateway->id, 'id' => $subgateway->id]) }}" class="dropdown-item">
                                                        Edit
                                                    </a></li>
                                                    @endif
                                                    @if (auth()->guard('admin')->user()->can(['delete-sub-gateway']))
                                                    <li><a href="javascript:void(0);" class="dropdown-item delete_modal"
                                                        data-url="{{ route('subGatway-delete-data', ['gateway_id' => $gateway->id, 'id' => $subgateway->id]) }}"
                                                        data-id="{{ $subgateway->id }}">Delete </a></li>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
@endsection
