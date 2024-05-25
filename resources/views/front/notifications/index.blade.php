@extends('layouts.user.default')

@section('title')
All Notifications
@endsection

@section('breadcrumbTitle')
<a href="{{ route('dashboardPage') }}">Dashboard</a> / All Notifications
@endsection

@section('customeStyle')
<style type="text/css">
    /*.table-striped > tbody > tr:nth-of-type(odd){
        --bs-table-accent-bg: #1D1D1D !important;
    }
    .table-striped > tbody > tr:nth-of-type(even){
        --bs-table-accent-bg: var(--secondary-2) !important;
    }*/
    td{
        padding: 15px 20px !important;
    }
    td a{
        color: #5a5a5a !important;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12 col-xxl-12 col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">All Notifications</h4>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless table-striped">
                        @foreach($notifications as $notification)
                        <tr>
                            <td>
                                <a href="{{ url($notification->url) }}?for=read">
                                    <strong class="text-primary-3">{{ $notification->title }}</strong>
                                    <span class="text-dark-2"> &nbsp; | &nbsp; {{ convertDateToLocal($notification->created_at, 'd-m-Y / H:i:s')}}</span>

                                    <p class="mt-25 mb-0">{{ Str::limit($notification->body,120) }}</p>
                                </a>

                            </td>

                            <td>
                                <a href="{{ url($notification->url) }}?for=read" target="_blank"
                                class="btn btn-primary btn-sm">Go to
                                Link</a>

                                <a href="{{ route('merchant-read-notifications',$notification->id) }}"
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