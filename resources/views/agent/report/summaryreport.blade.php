@extends('layouts.agent.default')

@section('title')
Summary Report
@endsection

@section('breadcrumbTitle')
<a href="{{ route('rp.dashboard') }}">Dashboard</a> / Summary Report
@endsection

@section('content')
<div class="row">
    <div class="col-xl-6 col-lg-6 col-sm-6">
        <div class="card overflow-hidden">
            <div class="card-header">
                <h3 class="mt-2 mb-0">Reports</h3>
                <div class="profile-photo">
                    <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" width="150" class="img-fluid"
                        alt="">
                </div>
            </div>
            <div class="card-body">

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex px-0 justify-content-between">
                        <strong class="mt-2">Reports</strong>
                        <a class="btn btn-outline-primary btn-xxs btn-rounded px-5"
                            href="{{ route('rp.merchant.report') }}">Go <i class="fa fa-angle-right ml-1"></i></a>
                    </li>
                    <li class="list-group-item d-flex px-0 justify-content-between">
                        <strong class="mt-2">Card Report</strong>
                        <a class="btn btn-outline-primary btn-xxs btn-rounded px-5"
                            href="{{ route('rp.rp-card-report') }}">Go <i class="fa fa-angle-right ml-1"></i></a>
                    </li>
                    <li class="list-group-item d-flex px-0 justify-content-between">
                        <strong class="mt-2">Payment Status Report</strong>
                        <a class="btn btn-outline-primary btn-xxs btn-rounded px-5"
                            href="{{ route('rp.rp-payment-status-report') }}">Go <i class="fa fa-angle-right ml-1"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection