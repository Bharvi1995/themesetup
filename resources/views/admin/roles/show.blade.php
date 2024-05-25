@extends('layouts.admin.default')

@section('title')
    Admin Role Show
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ url('admin/roles') }}">Role</a> / Show
@endsection

@section('customeStyle')
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Admin Roles Details</h4>
                    </div>
                    <a href="{{ url('admin/roles') }}" class="btn btn-primary btn-sm"> <i class="fa fa-arrow-left"></i></a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <label class="col-sm-2">Name</label>
                        <div class="col-sm-10">
                            {{ $role->name }}
                        </div>
                    </div>
                    <div class="row view-record-main">
                        <label class="col-sm-2">Permissions</label>
                        <div class="col-sm-10">
                            @if (!empty($rolePermissions))
                                @foreach ($rolePermissions as $module => $moduleList)
                                    @foreach ($moduleList as $subModule => $subModuleList)
                                        <p class="mb-1 mt-2  label">{{ ucfirst($module) }} ->
                                            {{ ucfirst($subModule) }}</p>
                                        @foreach ($subModuleList as $v)
                                            <label
                                                class="badge badge-primary badge-sm mb-1 me-1">{{ $v->name }}</label>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
