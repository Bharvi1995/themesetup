@extends('layouts.user.default')

@section('title')
Show Notifications
@endsection

@section('breadcrumbTitle')
<a href="{{ route('dashboardPage') }}">Dashboard</a> / <a href="{{ route('merchant-notifications') }}">All Notifications</a> /
Show
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12 col-xxl-12 col-lg-12">
        <div class="card border-card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Show Notifications</h4>
                </div>
                <a href="{{ route('merchant-notifications') }}" class="btn btn-primary btn-sm" title="Back"> <i
                        class="fa fa-arrow-left"></i> </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-9">
                        <h5 class="text-primary">Title : {{ $notifications->title }}</h5>
                    </div>
                    <div class="col-md-3 text-right text-danger">
                        <small>Time : {{ convertDateToLocal($notifications->created_at, 'd-m-Y / H:i:s')}}</small>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="row">
                            <div class="col-md-10">
                                <h5> Description : </h5>{{ $notifications->body }}
                            </div>
                            <div class="col-md-2 text-right">
                                <a href="{{ url($notifications->url) }}" class="btn btn-primary btn-sm shadow">Go to Link</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection