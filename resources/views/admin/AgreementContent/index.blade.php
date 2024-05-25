@extends('layouts.admin.default')

@section('title')
    Agreement Content
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Agreement Content
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Agreement Content</h4>
                    </div>
                    <a href="{{ route('agreement_content.create') }}" class="btn btn-success btn-sm">Create Agreement</a>
                </div>
                <div class="card-body p-0">
                    <div class="row">
                        <div class="table-responsive custom-table">
                            <table class="table table-borderless table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Type</th>
                                        <th>Body</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($data) > 0)
                                        @foreach ($data as $value)
                                            <tr>
                                                <th>{{ $loop->index + 1 }}</th>
                                                </td>
                                                <td>
                                                    @if ($value->type == 1)
                                                        <span class="badge badge-primary">Merchant</span>
                                                    @else
                                                        <span class="badge badge-primary">RP</span>
                                                    @endif
                                                </td>
                                                <td>{!! Str::limit($value->body, 150) !!}</td>
                                                <td class="w-15">
                                                    <div class="dropdown">
                                                        <button type="button"
                                                            class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                            data-bs-toggle="dropdown">
                                                            <svg width="5" height="17" viewBox="0 0 5 17"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                                            <a href="{!! URL::route('agreement_content.edit', $value->id) !!}" class="dropdown-item">Edit</a>
                                                            <a href="" class="dropdown-item delete_modal"
                                                                data-bs-toggle="modal" data-bs-target="#delete_modal"
                                                                data-url="{{ URL::route('agreement_content.destroy', $value->id) }}"
                                                                data-id="{{ $value->id }}">Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="8">
                                                <p class="text-center"><strong>No record found</strong></p>
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
    </div>
@endsection
