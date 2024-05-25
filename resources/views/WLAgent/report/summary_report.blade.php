@extends($WLAgentUserTheme)
@section('title')
    Summary Report
@endsection

@section('breadcrumbTitle')
    Summary Report
@endsection
@section('customeStyle')
    <style>
        .summaryCard {
            -webkit-box-shadow: 6px 10px 38px 1px rgba(0, 0, 0, 0.77) !important;
            -moz-box-shadow: 6px 10px 38px 1px rgba(0, 0, 0, 0.77) !important;
            box-shadow: 6px 10px 38px 1px rgba(0, 0, 0, 0.77) !important;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Summary Report</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4 ml-2 ">
                            <a href="{!! route('wl-user-card-summary-report') !!}">
                                <div class=" summaryCard">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <span class="activity-icon me-md-4 me-3">
                                                <i class="fa fa-credit-card text-danger"
                                                    style="line-height: 83px; font-size: 28px;"></i>
                                            </span>
                                            <div class="media-body">
                                                <span class="title  font-w600">Card type summary</span>
                                            </div>
                                        </div>
                                        <div class="progress" style="height:5px;">
                                            <div class="progress-bar bg-danger" style="width: 100%; height:5px;"
                                                role="progressbar"></div>
                                        </div>
                                    </div>
                                    <div class="effect bg-danger"></div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{!! route('wl-user-payment-status-summary-report') !!}">
                                <div class=" summaryCard">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <span
                                                class="activity-icon 
                                         mr-md-4 mr-3">
                                                <i class="fa fa-credit-card text-danger"
                                                    style="line-height: 83px; font-size: 28px;"></i>
                                            </span>
                                            <div class="media-body">
                                                <span class="title  font-w600">Payment status summary</span>
                                            </div>
                                        </div>
                                        <div class="progress" style="height:5px;">
                                            <div class="progress-bar bg-danger" style="width: 100%; height:5px;"
                                                role="progressbar"></div>
                                        </div>
                                    </div>
                                    <div class="effect bg-danger"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
