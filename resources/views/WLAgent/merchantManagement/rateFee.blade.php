@extends($WLAgentUserTheme)

@section('title')
    Rate/Fee
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('wl-dashboard') }}">Dashboard</a> / Rate/Fee
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Rate/Fee</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <strong><b> Visa -</b> Discount Rate (%)</strong> :
                            {{ auth()->guard('agentUserWL')->user()->discount_rate }}
                        </div>

                        <div class="col-lg-6 mb-3">
                            <strong><b> Master -</b> Discount Rate (%)</strong> :
                            {{ auth()->guard('agentUserWL')->user()->discount_rate_master_card }}
                        </div>

                        <div class="col-lg-6 mb-3">
                            <strong><b> Visa -</b> Setup Fee</strong> :
                            {{ auth()->guard('agentUserWL')->user()->setup_fee }}
                        </div>

                        <div class="col-lg-6 mb-3">
                            <strong><b> Master -</b> Setup Fee</strong> :
                            {{ auth()->guard('agentUserWL')->user()->setup_fee_master_card }}
                        </div>

                        <div class="col-lg-6 mb-3">
                            <strong>Rolling Reserve (%)</strong> :
                            {{ auth()->guard('agentUserWL')->user()->rolling_reserve_paercentage }}
                        </div>

                        <div class="col-lg-6 mb-3">
                            <strong>Transaction Fee</strong> : {{ auth()->guard('agentUserWL')->user()->transaction_fee }}
                        </div>

                        <div class="col-lg-6 mb-3">
                            <strong>Refund Fee</strong> : {{ auth()->guard('agentUserWL')->user()->refund_fee }}
                        </div>

                        <div class="col-lg-6 mb-3">
                            <strong>Chargeback Fee</strong> : {{ auth()->guard('agentUserWL')->user()->chargeback_fee }}
                        </div>

                        <div class="col-lg-6 mb-3">
                            <strong>Suspicious Transaction Fee</strong> :
                            {{ auth()->guard('agentUserWL')->user()->flagged_fee }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
