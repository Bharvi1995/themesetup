@extends('layouts.user.default')

@section('title')
User Management
@endsection

@section('breadcrumbTitle')
<a href="{{ route('dashboardPage') }}">Dashboard</a> / User Management
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12 col-xxl-12">
        <div class="iq-card">
            <div class="iq-card-header d-flex justify-content-between">
                <div class="iq-header-title">
                    <h4 class="card-title">User Management</h4>
                </div>
                <a href="{{ url('user-management/create') }}" class="btn btn-primary d-none d-md-block">Add User</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mb-0 table-borderless">
                        <thead>   
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(!empty($data) && $data->count())
                            @foreach($data as $key => $value)
                            <tr>
                                <td>{!! $value->id !!}</td>
                                <td>{!! $value->name !!}</td>
                                <td>{!! $value->email !!}</td>
                                <td> 
                                    <div class="dropdown ml-auto">
                                        <a href="#" class="btn btn-primary sharp rounded-pill" data-bs-toggle="dropdown" aria-expanded="true"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#FFF" cx="5" cy="12" r="2"></circle><circle fill="#FFF" cx="12" cy="12" r="2"></circle><circle fill="#FFF" cx="19" cy="12" r="2"></circle></g></svg></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li class="dropdown-item"><a href="{!! URL::route('user-management.edit', $value->id) !!}" class="dropdown-a"><i class="fa fa-edit text-primary mr-2"></i> Edit</a></li>
                                            <li class="dropdown-item"><a href="#" class="dropdown-a text-danger delete_modal" id="" data-target="#delete_modal" data-bs-toggle="modal"  data-url="{!! URL::route('user-management.delete', $value->id) !!}" data-id="{{ $value->id }}"><i class="fa fa-trash text-danger mr-2"></i> Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8">
                                    <p class="text-center">No Users found</p>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection