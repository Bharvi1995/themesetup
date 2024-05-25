@extends('layouts.admin.default')

@section('title')
    Show Notification
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin-notifications') }}">All
        Notifications</a> / Show
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Show Notification</h4>
                    </div>
                    <a href="{{ route('admin-notifications') }}" class="btn btn-primary btn-sm"> Back</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <h5 class="text-primary">Title : {{ $notifications->title }}</h5>
                        </div>
                        <div class="col-md-3 text-right text-danger">
                            <small>Time : {{ convertDateToLocal($notifications->created_at, 'd-m-Y / H:i:s') }}</small>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="row">
                                <div class="col-md-10">
                                    <h5>
                                        Description :
                                    </h5>
                                    {{ $notifications->body }}
                                </div>
                                <div class="col-md-2 text-right">
                                    <a href="{{ url($notifications->url) }}?for=read" target="_blank"
                                        class="btn btn-primary btn-sm shadow">Go to Link</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
