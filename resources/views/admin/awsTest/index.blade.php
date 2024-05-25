@extends('layouts.admin.default')
@section('title')
    Bank Users
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Bank Users
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Test S3 Bucket</h4>
                </div>
                <div class="card-body">
                    <form class="form-dark" enctype="multipart/form-data" action="{{ route('aws-s3-test.store') }}"
                        method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <label>Title</label>
                                <input type="text" class="form-control" placeholder="Enter some title" name="title" />
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label>File</label>
                                <input type="file" class="form-control" name="file" />
                                @error('file')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-12 mt-2">
                                <button type="submit" class="btn btn-danger">Upload file</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Uploaded files</h4>
                </div>
                <div class="card-body p-0 text-dark">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($files) > 0)
                                    @foreach ($files as $item)
                                        <tr>
                                            <td>{{ $item->title }}</td>
                                            <td>{{ $item->created_at }}</td>
                                            <td>
                                                <a href="{{ getS3Url($item->file) }}" target="_blank"
                                                    class="btn btn-primary btn-sm">View</a>
                                                <button type="button"
                                                    data-url="{{ route('aws-s3-test.destroy', [$item->id]) }}"
                                                    class="btn btn-danger btn-sm delete_modal">Delete</button>
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

                    <div class="card-footer">
                        @if (!empty($files) && $files->count())
                            <div class="row">
                                <div class="col-md-8">
                                    {!! $files->appends($_GET)->links() !!}
                                </div>
                                <div class="col-md-4 text-right">
                                    Showing {{ $files->firstItem() }} to {{ $files->lastItem() }} of total
                                    {{ $files->total() }}
                                    entries
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
