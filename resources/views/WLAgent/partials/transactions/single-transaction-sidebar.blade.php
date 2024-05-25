<div class="row">
    <!-- Billing info -->
    <div class="col-xl-6">
        <h4>BILLING INFO</h4>
        <div class="row">
            <div class="col-xl-6"><strong>Order No.</strong></div>
            <div class="col-xl-6">{{ $data->order_id }}</div>
        </div>
        <!-- <div class="row">
            <div class="col-xl-6"><strong>Company Name</strong></div>
            <div class="col-xl-6">Mr. Bobby</div>
        </div> -->
        <div class="row">
            <div class="col-xl-6"><strong>First Name</strong></div>
            <div class="col-xl-6">{{ $data->first_name }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>Last Name</strong></div>
            <div class="col-xl-6">{{ $data->last_name }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>Address</strong></div>
            <div class="col-xl-6">{{ $data->address }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>Country</strong></div>
            <div class="col-xl-6">{{ $data->country }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>State</strong></div>
            <div class="col-xl-6">{{ $data->state }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>City</strong></div>
            <div class="col-xl-6">{{ $data->city }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>Zip Code</strong></div>
            <div class="col-xl-6">{{ $data->zip }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>IP Address</strong></div>
            <div class="col-xl-6">{{ $data->ip_address }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>Email</strong></div>
            <div class="col-xl-6">{{ $data->email }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>Phone No.</strong></div>
            <div class="col-xl-6">{{ $data->phone_no }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>Customer Order ID</strong></div>
            <div class="col-xl-6">{{ $data->customer_order_id }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>Reason</strong></div>
            <div class="col-xl-6">{{ $data->reason }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>Status</strong></div>
            <div class="col-xl-6">
                @if($data->status == '1')
                    <label class="light badge badge-success">Success</label>
                @elseif($data->status == '2')
                    <label class="light badge badge-warning">Pending</label>
                @elseif($data->status == '3')
                    <label class="light badge badge-yellow">Cancelled</label>
                @elseif($data->status == '4')
                    <label class="light badge badge-primary">To Be Confirm</label>
                @else
                    <label class="light badge badge-danger">Declined</label>
                @endif
            </div>
        </div>    
        <div class="row">
            <div class="col-xl-6"><strong>Transaction Date</strong></div>
            <div class="col-xl-6">{{ convertDateToLocal($data->created_at, 'd-m-Y H:i:s') }}</div>
        </div>
        @if($data->chargebacks == '1')
            <div class="row">
                <div class="col-xl-6"><strong>Chargebacks</strong></div>
                <div class="col-xl-6"><label class="badge badge-success">YES</label></div>
            </div>
        @endif
        @if($data->refund == '1')
            <div class="row">
                <div class="col-xl-6"><strong>Refund</strong></div>
                <div class="col-xl-6"><label class="badge badge-success">YES</label></div>
            </div>
            @if($data->refund_reason != '')
                <div class="row">
                    <div class="col-xl-6"><strong>Refund Reason</strong></div>
                    <div class="col-xl-6">{{ $data->refund_reason }}</div>
                </div>
            @endif
        @endif
    </div>

    <!-- Card info -->
    <div class="col-xl-6">
        <h4>CARD INFO</h4>
        @if ($data->card_no != null)
        <div class="row">
            <div class="col-xl-6"><strong>Card Type</strong></div>
            <div class="col-xl-6">
                @if($data->card_type == 1)
                    Amex
                @elseif($data->card_type == 2)
                    Visa
                @elseif($data->card_type == 3)
                    Master Card
                @else
                    Discover
                @endif
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-xl-6"><strong>Amount</strong></div>
            <div class="col-xl-6">{{ $data->amount }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>Currency</strong></div>
            <div class="col-xl-6">{{ $data->currency }}</div>
        </div>
        @if ($data->card_no != null)
        <div class="row">
            <div class="col-xl-6"><strong>Card No.</strong></div>
            <div class="col-xl-6">
                @if (strlen($data->card_no) > 4)
                    {!! substr($data->card_no, 0, 6) . 'XXXXXX' . substr($data->card_no, -4) !!}
                @else
                    {!! $data->card_no !!}
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>Expiry Month</strong></div>
            <div class="col-xl-6">{{ $data->ccExpiryMonth }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>Expiry Year</strong></div>
            <div class="col-xl-6">{{ $data->ccExpiryYear }}</div>
        </div>
        <div class="row">
            <div class="col-xl-6"><strong>CVV</strong></div>
            <div class="col-xl-6">XXX</div>
        </div>
        @endif
    </div>
</div>
