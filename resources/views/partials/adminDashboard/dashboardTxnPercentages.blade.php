<div class="row">
    <div class="col-lg-3 mb-2">
        <div class="merchantTxnCard">
            <h2>{{ round($transaction->successfullP, 2) }} %</h2>
            <p class="mb-1" style="color: #FFFFFF;">Successful</p>
            <p class="total" style="color: #82CD47;">Total Count : <span>
                    {{ $transaction->successfullC }}</span></p>
        </div>
    </div>
    <div class="col-lg-3 mb-2">
        <div class="merchantTxnCard">
            <h2>{{ round($transaction->declinedP, 2) }} %</h2>
            <p class="mb-1" style="color: #FFFFFF;">Declined</p>
            <p class="total" style="color: #5F9DF7;">Total Count : <span>
                    {{ $transaction->declinedC }}</span></p>
        </div>
    </div>
    <div class="col-lg-3 mb-2">
        <div class="merchantTxnCard">
            <h2>{{ round($transaction->suspiciousP, 2) }} %</h2>
            <p class="mb-1" style="color: #FFFFFF;">Marked</p>
            <p class="total" style="color: #C47AFF;">Total Count : <span>
                    {{ $transaction->suspiciousC }}</span></p>
        </div>
    </div>
    <div class="col-lg-3 mb-2">
        <div class="merchantTxnCard">
            <h2>{{ round($transaction->refundP, 2) }} %</h2>
            <p class="mb-1" style="color: #FFFFFF;">Refund</p>
            <p class="total" style="color: #BF4146;">Total Count : <span>
                    {{ $transaction->refundC }}</span></p>
        </div>
    </div>
</div>
