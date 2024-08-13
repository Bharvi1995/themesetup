@extends('layouts.admin.default')
@section('title')
    Admin Roles
@endsection
@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Admin Roles</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Admin Roles</h6>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card  mt-1">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h5 class="card-title">Admin Roles</h5>
                    </div>
                    <div class="card-header-toolbar align-items-center">
                        
                        @if (auth()->guard('admin')->user()->can(['role-create']))
                            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm"> Create Role</a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Role</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rights/Privilege</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $key => $role)
                                    <tr>
                                        <th class="align-middle text-center text-sm">{{ ++$i }}</th>
                                        <td class="align-middle text-center text-sm">{{ $role->name }}</td>
                                        <td class="align-middle text-center text-sm">{{ ucfirst($role->guard_name) }}</td>
                                        <td class="align-middle text-center text-sm">
                                            @if (auth()->guard('admin')->user()->can(['role-delete']))
                                                <a class="btn btn-link text-danger text-gradient px-1 mb-0 delete_modal" href="javascript:;" data-url="{!! URL::route('roles.destroy', $role->id) !!}" data-id="{{ $role->id }}">Delete</a>
                                            @endif
                                            @if (auth()->guard('admin')->user()->can(['role-edit']))
                                                <a class="btn btn-link text-danger text-dark px-1 mb-0" href="{{ route('roles.edit', $role->id) }}" >Edit</a>
                                            @endif
                                            <a class="btn btn-link text-primary text-primary px-1 mb-0" href="{{ route('roles.show', $role->id) }}" >View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer">
                    @if (!empty($roles) && $roles->count())
                        <div class="row">
                            <div class="col-md-8">
                                {!! $roles->appends($_GET)->links() !!}
                            </div>
                            <div class="col-md-4 text-right">
                                Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of total
                                {{ $roles->total() }}
                                entries
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
