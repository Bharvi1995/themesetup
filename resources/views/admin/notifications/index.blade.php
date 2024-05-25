@extends('layouts.admin.default')
@section('title')
    Admin Notifications
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Admin Notifications
@endsection

@section('customeStyle')
    <style type="text/css">

        td {
            padding: 15px 20px !important;
        }
        td a{
            color: #5a5a5a !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">All Notifications</h4>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm"> Back</a>
                </div>
                <div class="card-body p-0 p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped">
                            @foreach ($notifications as $notification)
                                <tr>
                                    <td>
                                        <a href="{{ url($notification->url) }}?for=read">
                                            <strong class="text-primary-3">{{ $notification->title }}</strong>
                                            <span class="text-dark-2"> &nbsp; | &nbsp;
                                                {{ convertDateToLocal($notification->created_at, 'd-m-Y / H:i:s') }}</span>

                                            <p class="mt-25 mb-0">{{ Str::limit($notification->body, 120) }}</p>
                                        </a>

                                    </td>

                                    <td>
                                        <a href="{{ url($notification->url) }}?for=read" target="_blank"
                                            class="btn btn-primary btn-sm">Go to
                                            Link</a>

                                        <a href="{{ route('read-admin-notifications', $notification->id) }}"
                                            class="btn btn-primary btn-sm">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
