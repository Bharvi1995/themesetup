@extends('layouts.admin.default')

@section('title')
    Admin Role Show
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ url('paylaksa/roles') }}">Role</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Show Role</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Show Role</h6>
    </nav>
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
                                                class="badge badge-sm bg-gradient-primary mb-1 me-1">{{ $v->name }}</label>
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
