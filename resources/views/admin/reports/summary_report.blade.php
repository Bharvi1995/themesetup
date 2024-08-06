@extends('layouts.admin.default')
@section('title')
    Transaction Summary Report
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Summary Report
@endsection
@section('customeStyle')
    <script src="https://cdn.lordicon.com//libs/frhvbuzj/lord-icon-2.0.2.js"></script>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <h4 class="text-black fs-20 mb-3">Summary Report</h4>
        </div>
        <div class="col-sm-6">
            <a href="{!! url('paylaksa/card-summary-report') !!}">
                <div class="card avtivity-card">
                    <div class="card-body p-0">
                        <div class="media align-items-center">
                            <span class="activity-icon bgl-info mr-md-4 mr-3">
                                <i class="fa fa-credit-card text-info" style="line-height: 83px; font-size: 28px;"></i>
                            </span>
                            <div class="media-body">
                                <span class="title text-black font-w600">Card type summary</span>
                            </div>
                        </div>
                        <div class="progress" style="height:5px;">
                            <div class="progress-bar bg-info" style="width: 100%; height:5px;" role="progressbar"></div>
                        </div>
                    </div>
                    <div class="effect bg-info"></div>
                </div>
            </a>
        </div>
        <div class="col-sm-6">
            <a href="{!! url('paylaksa/payment-status-summary-report') !!}">
                <div class="card avtivity-card">
                    <div class="card-body p-0">
                        <div class="media align-items-center">
                            <span class="activity-icon bgl-info mr-md-4 mr-3">
                                <i class="fa fa-credit-card text-info" style="line-height: 83px; font-size: 28px;"></i>
                            </span>
                            <div class="media-body">
                                <span class="title text-black font-w600">Payment status summary</span>
                            </div>
                        </div>
                        <div class="progress" style="height:5px;">
                            <div class="progress-bar bg-info" style="width: 100%; height:5px;" role="progressbar"></div>
                        </div>
                    </div>
                    <div class="effect bg-info"></div>
                </div>
            </a>
        </div>
        <div class="col-sm-6">
            <a href="{!! url('paylaksa/mid-summary-report') !!}">
                <div class="card avtivity-card">
                    <div class="card-body p-0">
                        <div class="media align-items-center">
                            <span class="activity-icon bgl-info mr-md-4 mr-3">
                                <i class="fa fa-credit-card text-info" style="line-height: 83px; font-size: 28px;"></i>
                            </span>
                            <div class="media-body">
                                <span class="title text-black font-w600">MID type summary</span>
                            </div>
                        </div>
                        <div class="progress" style="height:5px;">
                            <div class="progress-bar bg-info" style="width: 100%; height:5px;" role="progressbar"></div>
                        </div>
                    </div>
                    <div class="effect bg-info"></div>
                </div>
            </a>
        </div>

        <div class="col-sm-6">
            <a href="{!! route('summary-report-on-country') !!}">
                <div class="card avtivity-card">
                    <div class="card-body p-0">
                        <div class="media align-items-center">
                            <span class="activity-icon bgl-info mr-md-4 mr-3">
                                <i class="fa fa-credit-card text-info" style="line-height: 83px; font-size: 28px;"></i>
                            </span>
                            <div class="media-body">
                                <span class="title text-black font-w600">Country wise Summary</span>
                            </div>
                        </div>
                        <div class="progress" style="height:5px;">
                            <div class="progress-bar bg-info" style="width: 100%; height:5px;" role="progressbar"></div>
                        </div>
                    </div>
                    <div class="effect bg-info"></div>
                </div>
            </a>
        </div>
    </div>
@endsection
