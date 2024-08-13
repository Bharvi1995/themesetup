@extends('layouts.admin.default')
@section('title')
    Required Fields
@endsection
@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Required Fields</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Required Fields</h6>
    </nav>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Required fields</h4>
                    </div>
                    <div class="card-header-toolbar align-items-center">
                        <div class="btn-group mr-2">
                            <a href="{{ route('required_fields.create') }}" class="btn btn-primary btn-sm">Create Fields</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="row">
                        <div class="table-responsive custom-table">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Title</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fields</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Validations</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($RequiredFields as $value)
                                        <tr>
                                            <th class="align-middle text-center text-sm">{{ $loop->index + 1 }}</th>
                                            </td>
                                            <td class="align-middle text-center text-sm">{{ $value->field_title }}</td>
                                            <td class="align-middle text-center text-sm">{{ $value->field }}</td>
                                            <td class="align-middle text-center text-sm">{{ $value->field_type }}</td>
                                            <td class="align-middle text-center text-sm">{{ $value->field_validation }}</td>
                                            <td class="align-middle text-center text-sm w-15">
                                                <div class="dropdown">
                                                    <a href="javascript:;" class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownMenuLink2">
                                                    </a>
                                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink2">
                                                        <li>
                                                            <a href="{!! URL::route('required_fields.edit', $value->id) !!}"
                                                                class="dropdown-item">Edit</a>
                                                        </li>
                                                        <li>
                                                            <a href="" class="dropdown-item delete_modal"
                                                            data-bs-toggle="modal" data-bs-target="#delete_modal"
                                                            data-url="{{ URL::route('required_fields.destroy', $value->id) }}"
                                                            data-id="{{ $value->id }}">Delete</a>
                                                        </li>
                                                    </ul>
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
    </div>
@endsection
