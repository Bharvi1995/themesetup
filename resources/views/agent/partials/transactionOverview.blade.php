<div class="agentTransactionOverview">
    <div class="iq-card">
        <div class="iq-card-header d-flex justify-content-between">
            <div class="iq-header-title">
                <h4 class="card-title">Transaction Overview</h4>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="row align-items-center">
                <div class="col-lg-4 mb-lg-0 mb-4 text-center radialBar">
                    <a href="{!! url('rp/merchant-transactions') !!}">
                        <div id="radialBar"></div>
                        <h4 style="margin: -35px 0px 30px 0px;">Successful</h4>
                    </a>
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-sm-6">
                            <a href="{!! url('rp/merchant-chargebacks-transactions') !!}">
                                <div class="d-flex align-items-center mb-sm-5 mb-3">
                                    <div class="d-inline-block relative donut-chart-sale mr-3">
                                        <span class="donut"
                                            data-peity='{ "fill": ["rgb(160, 44, 250)", "rgba(236, 236, 236, 1)"],   "innerRadius": 34, "radius": 10}'>2.5/10</span>
                                        <small>
                                            <i class="flaticon-381-transfer"
                                                style="font-size: 34px; color:#A02CFA;"></i>
                                        </small>
                                    </div>
                                    <div>
                                        <h4 class="fs-18 text-black">Chargeback ({{ $chargebacksPercentage }}%)
                                        </h4>
                                        <span>{{ $transaction->chargebacks }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="{!! url('rp/merchant-suspicious-transactions') !!}">
                                <div class="d-flex align-items-center mb-sm-5 mb-3">
                                    <div class="d-inline-block relative donut-chart-sale mr-3">
                                        <span class="donut"
                                            data-peity='{ "fill": ["rgb(255, 188, 17)", "rgba(236, 236, 236, 1)"],   "innerRadius": 34, "radius": 10}'>1.8/10</span>
                                        <small>
                                            <i class="flaticon-381-transfer"
                                                style="font-size: 34px; color:#FFBC11;"></i>
                                        </small>
                                    </div>
                                    <div>
                                        <h4 class="fs-18 text-black">Suspicious ({{ $flaggedPercentage }}%)</h4>
                                        <span>{{ $transaction->flagged }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="{!! url('rp/merchant-refund-transactions') !!}">
                                <div class="d-flex align-items-center mb-sm-0 mb-3">
                                    <div class="d-inline-block relative donut-chart-sale mr-3">
                                        <span class="donut"
                                            data-peity='{ "fill": ["#1EA7C5", "rgba(236, 236, 236, 1)"],   "innerRadius": 34, "radius": 10}'>8/10</span>
                                        <small>
                                            <i class="flaticon-381-transfer"
                                                style="font-size: 34px; color:#1EA7C5;"></i>
                                        </small>
                                    </div>
                                    <div>
                                        <h4 class="fs-18 text-black">Refund ({{ $refundPercentage }}%)</h4>
                                        <span>{{ $transaction->refund }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
