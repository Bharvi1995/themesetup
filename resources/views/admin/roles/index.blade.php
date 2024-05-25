@extends('layouts.admin.default')
@section('title')
    Admin Roles
@endsection
@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Admin Roles
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card  mt-1">
                <div class="card-header">

                    <h4 class="card-title">Admin Roles</h4>
                    @if (auth()->guard('admin')->user()->can(['role-create']))
                        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm"> Create Role</a>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Rights/Privilege</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $key => $role)
                                    <tr>
                                        <th scope="row">{{ ++$i }}</th>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ ucfirst($role->guard_name) }}</td>
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
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @if (auth()->guard('admin')->user()->can(['role-edit']))
                                                        <a href="{{ route('roles.edit', $role->id) }}"
                                                            class="dropdown-item"><i
                                                                class="fa fa-edit text-primary me-2"></i>
                                                            Edit</a>
                                                    @endif
                                                    <a href="{{ route('roles.show', $role->id) }}" class="dropdown-item"><i
                                                            class="fa fa-eye text-success me-2"></i>
                                                        View</a>
                                                    @if (auth()->guard('admin')->user()->can(['role-delete']))
                                                        <a href="javascript:void(0)" class="dropdown-item delete_modal"
                                                            data-url="{!! URL::route('roles.destroy', $role->id) !!}"
                                                            data-id="{{ $role->id }}"><i
                                                                class="fa fa-trash text-danger me-2"></i>
                                                            Delete</a>
                                                    @endif
                                                </ul>
                                            </div>
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
