@extends('layouts.admin.default')

@section('title')
    Admin Integration Preference
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Integration Preference</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Integration Preference</h6>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Integration Preference</h4>
                    </div>
                    <div class="card-header-toolbar align-items-center">
                        <div class="btn-group mr-2">
                            <a href="{{ route('integration-preference.create') }}" class="btn btn-primary btn-sm">New Integration Preference</a>
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
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($data as $key => $technology)
                                    <tr>
                                        <th class="align-middle text-center text-sm">{{ ++$i }}</th>
                                        <td class="align-middle text-center text-sm">{{ $technology->name }}</td>
                                        <td class="align-middle text-center text-sm w-15">
                                            <div class="dropdown">
                                                <a href="javascript:;" class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownMenuLink2">
                                                </a>
                                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink2">
                                                    <li>
                                                        <a href="{!! URL::route('integration-preference.edit', $technology->id) !!}"
                                                            class="dropdown-item">Edit</a>                                                    
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)" class="dropdown-item delete_modal"
                                                            data-bs-toggle="modal" data-bs-target="#delete_modal"
                                                            data-url="{{ URL::route('integration-preference.destroy', $technology->id) }}"
                                                            data-id="{{ $technology->id }}">Delete</a>
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
@endsection

@section('customScript')
    <script src="{{ storage_asset('newThemes/assets/plugins/DataTables/datatables.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var oTable = $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                ajax: '{{ route('integration-preference.index') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'Actions',
                        name: 'Actions',
                        orderable: false,
                        serachable: false,
                        sClass: 'text-center'
                    },
                ]
            });
        });
    </script>
@endsection
