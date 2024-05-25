@extends('layouts.admin.default')
@section('title')
    Sub Gateway
@endsection
@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Sub Gateway
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-sm-12 mb-2">
            @if (auth()->guard('admin')->user()->can(['create-sub-gateway']))
                <a href="{{ route('admin.subgateway.create', ['gateway_id' => $gateway->id]) }}"
                    class="btn btn-success pull-right">Create {{ $gateway->title }} MID</a>
            @endif
        </div>
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">{{ $gateway->title }} Gateway Details</h4>
                    </div>
                    <div>
                        <a href="{{ route('admin.gateway.index') }}" class="btn btn-primary btn-sm"><i
                                class="fa fa-arrow-left"></i> </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    @foreach (json_decode($gateway->credential_titles) as $title)
                                        <th>{{ $title }}</th>
                                    @endforeach
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subgateways as $subgateway)
                                    <tr>
                                        <td>{{ $subgateway->id }}</td>
                                        <td>{{ $subgateway->name }}</td>
                                        @foreach (json_decode($gateway->credential_titles) as $key => $value)
                                            <td>{{ $subgateway->$key }}</td>
                                        @endforeach
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                    data-bs-toggle="dropdown">
                                                    <svg width="5" height="17" viewBox="0 0 5 17" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M2.36328 4.69507C1.25871 4.69507 0.363281 3.79964 0.363281 2.69507C0.363281 1.5905 1.25871 0.695068 2.36328 0.695068C3.46785 0.695068 4.36328 1.5905 4.36328 2.69507C4.36328 3.79964 3.46785 4.69507 2.36328 4.69507Z"
                                                            fill="#B3ADAD" />
                                                        <path
                                                            d="M2.36328 10.6951C1.25871 10.6951 0.363281 9.79964 0.363281 8.69507C0.363281 7.5905 1.25871 6.69507 2.36328 6.69507C3.46785 6.69507 4.36328 7.5905 4.36328 8.69507C4.36328 9.79964 3.46785 10.6951 2.36328 10.6951Z"
                                                            fill="#B3ADAD" />
                                                        <path
                                                            d="M2.36328 16.6951C1.25871 16.6951 0.363281 15.7996 0.363281 14.6951C0.363281 13.5905 1.25871 12.6951 2.36328 12.6951C3.46785 12.6951 4.36328 13.5905 4.36328 14.6951C4.36328 15.7996 3.46785 16.6951 2.36328 16.6951Z"
                                                            fill="#B3ADAD" />
                                                    </svg>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @if (auth()->guard('admin')->user()->can(['update-sub-gateway']))
                                                    <a href="{{ route('subGatway-edit-data', ['gateway_id' => $gateway->id, 'id' => $subgateway->id]) }}" class="dropdown-item">
                                                        Edit
                                                    </a>
                                                    @endif
                                                    @if (auth()->guard('admin')->user()->can(['delete-sub-gateway']))
                                                    <a href="javascript:void(0);" class="dropdown-item delete_modal"
                                                        data-url="{{ route('subGatway-delete-data', ['gateway_id' => $gateway->id, 'id' => $subgateway->id]) }}"
                                                        data-id="{{ $subgateway->id }}">Delete </a>
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
