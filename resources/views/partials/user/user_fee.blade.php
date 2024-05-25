<div class="row table-responsive custom-table">
    <div class="col-md-6 mb-2">
        <table class="table table-striped table-borderless">
            <tr>
                <th>Description</th>
                <th class="text-center">Rates/ Fee</th>
            </tr>
            <tr>
                <td colspan="2" style="padding: 5px 0px;"></td>
            </tr>
                <td>Rolling Reserve (%)</td>
                <td class="text-center">{{ \auth::user()->rolling_reserve_paercentage }}%</td>
            </tr>
            <tr>
                <td>Cards Fee</td>
                <td class="text-center">{{ \auth::user()->merchant_discount_rate }}%</td>
                
            </tr>
            <tr>
                <td>Account Setup Fee</td>
                <td class="text-center">{{ \auth::user()->setup_fee }} USDT</td>
            </tr>

            <tr>
                <td>Settlement Fee</td>
                <td class="text-center">{{ \auth::user()->settlement_fee }} %</td>
            </tr>

            <tr>
                <td>Settlement Period</td>
                <td class="text-center">T +{{ \auth::user()->payment_frequency }}</td>
            </tr>

            <tr>
                <td>Settlement Threshold</td>
                <td class="text-center">{{ \auth::user()->minimum_settlement_amount }} USDT</td>
            </tr>
            
        </table>
    </div>
    <div class="col-md-6 mb-2">
        <table class="table table-striped table-borderless">
            <tr>
                <th>Description</th>
                <th class="text-center">Rates/ Fee</th>
            </tr>
            <tr>
                <td>Transaction Fee</td>
                <td class="text-center">{{ \auth::user()->transaction_fee }} USD</td>
            </tr>
            <tr>
                <td>Chargeback Fee</td>
                <td class="text-center">{{ \auth::user()->chargeback_fee }} USD</td>
            </tr>
            
            <tr>
                <td>Refund Fee</td>
                <td class="text-center">{{ \auth::user()->refund_fee }} USD</td>
            </tr>
            <tr>
                <td>Dispute Fee</td>
                <td class="text-center">{{ \auth::user()->flagged_fee }} USD</td>
            </tr>
            <tr>
                <td>Retrieval Fee</td>
                <td class="text-center">{{ \auth::user()->retrieval_fee }} USD</td>
            </tr>
        </table>
    </div>
    @if (isset(\auth::user()->apm))
        <div class="col-md-6 mb-2">
            <div class="card">
                <div class="card-header">
                    <h4>APM & Rates</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-borderless">
                        <thead>
                            <tr>
                                <th>APM</th>
                                <th>Rates %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $userApms = json_decode(\auth::user()->apm, true);
                            @endphp
                            @foreach ($userApms as $userApm)
                                <tr>
                                    <td>
                                        {{ $userApm['bank_name'] }}
                                    </td>
                                    <td>{{ $userApm['apm_mdr'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

</div>
