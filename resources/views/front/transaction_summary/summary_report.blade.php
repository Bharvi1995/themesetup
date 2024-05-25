@extends('layouts.user.default')
@section('title')
Summary Report
@endsection

@section('breadcrumbTitle')
Summary Report
@endsection
@section('customeStyle')
<script src="https://cdn.lordicon.com//libs/frhvbuzj/lord-icon-2.0.2.js"></script>
@endsection
@section('content')
<div class="row">
    <div class="col-sm-6">
        <a href="{!! url('user-card-summary-report') !!}">
            <div class="iq-card avtivity-card bg-primary">
                <div class="iq-card-body">
                    <div class="media align-items-center">
                        <span class="activity-icon mr-md-4 mr-3">
                            <i class="fa fa-credit-card text-warning" style="line-height: 83px; font-size: 28px;"></i>
                        </span>
                        <div class="media-body">
                            <h4 class="text-warning">Card type summary</h4>
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
        <a href="{!! url('user-payment-status-summary-report') !!}">
            <div class="iq-card avtivity-card bg-primary">
                <div class="iq-card-body">
                    <div class="media align-items-center">
                        <span class="activity-icon mr-md-4 mr-3">
                            <i class="fa fa-credit-card text-warning" style="line-height: 83px; font-size: 28px;"></i>
                        </span>
                        <div class="media-body">
                            <h4 class="text-warning">Payment status summary</h4>
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