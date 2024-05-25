<div class="row">
    @if (auth()->guard('admin')->user()->can(['update-all-transaction']))
        @if (@isset($tab) == 'all' && $data->status == '1')
            <div class="col-md-12">
                <div class="row" style="background-color: #f1f1f1; padding: 15px 0px;">
                    @if (auth()->guard('admin'))
                        @if (isset($data->refund) && $data->refund == '1' && $data->refund_remove == '0' && $data->chargebacks == '0')
                            <div class="col-md-2">
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" name="refund" class="clRefundChecked form-check-input"
                                            id="refund{{ $data->id }}" data-id="{{ $data->id }}" disabled=""
                                            checked data-bs-toggle="modal" href="#transactionRefund">
                                        <label for="refund{{ $data->id }}" class="form-check-label">Refund</label>
                                    </div>
                                </td>
                            </div>
                        @elseif(
                            (isset($data->chargebacks) && $data->chargebacks == '1' && $data->chargebacks_remove == '0') ||
                                $data->is_flagged == '1')
                        @else
                            <div class="col-md-2">
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" name="refund" class="clRefund form-check-input"
                                            id="refund{{ $data->id }}" data-id="{{ $data->id }}"
                                            data-bs-toggle="modal" href="#transactionRefund"
                                            {{ $data->refund_remove == '1' ? 'disabled' : '' }}>
                                        <label for="refund{{ $data->id }}" class="form-check-label">Refund</label>
                                    </div>

                                </td>
                            </div>
                        @endif

                        @if ($data->chargebacks == '1' && $data->chargebacks_remove == '0')
                            <div class="col-md-2">
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" name="chargebacks" class="form-check-input"
                                            id="chargebacks{{ $data->id }}" data-id="{{ $data->id }}" checked
                                            disabled="" data-bs-toggle="modal" href="#transactionChargebacks">
                                        <label for="chargebacks{{ $data->id }}"
                                            class="form-check-label">Chargebacks</label>
                                    </div>
                                </td>
                            </div>
                        @else
                            <div class="col-md-2">
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" name="chargebacks" class="clChargeback form-check-input"
                                            id="chargebacks{{ $data->id }}" data-id="{{ $data->id }}"
                                            data-bs-toggle="modal" href="#transactionChargebacks"
                                            {{ $data->chargebacks_remove == '1' ? 'disabled' : '' }}>
                                        <label for="chargebacks{{ $data->id }}"
                                            class="form-check-label">Chargebacks</label>
                                    </div>
                                </td>
                            </div>
                        @endif

                        @if (
                            $data->is_flagged == '0' &&
                                $data->is_flagged_remove == '0' &&
                                $data->flagged_by == 'bank' &&
                                !empty($data->flagged_date))
                            <div class="col-md-2">
                                <td class="text-center">
                                    <div class="form-check">
                                        <input type="checkbox" name="TransactionFlagged" class="form-check-input"
                                            id="TransactionFlagged{{ $data->id }}" data-id="{{ $data->id }}"
                                            disabled="">
                                        <label for="TransactionFlagged{{ $data->id }}"
                                            class="form-check-label">Suspicious</label>
                                    </div>
                                </td>
                            </div>
                        @elseif($data->is_flagged == '1')
                            <div class="col-md-2">
                                <td class="text-center">
                                    <div class="form-check">
                                        <input type="checkbox" name="TransactionFlagged" class="form-check-input"
                                            id="TransactionFlagged{{ $data->id }}" data-id="{{ $data->id }}"
                                            checked disabled="">
                                        <label for="TransactionFlagged{{ $data->id }}"
                                            class="form-check-label">Suspicious</label>
                                    </div>
                                </td>
                            </div>
                        @elseif(
                            $data->refund == '1' ||
                                $data->chargebacks == '1' ||
                                $data->refund_remove == '1' ||
                                $data->chargebacks_remove == '1')
                        @else
                            <div class="col-md-2">
                                <td class="text-center">
                                    <div class="form-check">
                                        <input type="checkbox" name="TransactionFlagged"
                                            class="clFlagged form-check-input"
                                            id="TransactionFlagged{{ $data->id }}" data-id="{{ $data->id }}"
                                            data-bs-toggle="modal" href="#transactionFlagged"
                                            data-type="{{ $data->flagged_by }}">
                                        <label for="TransactionFlagged{{ $data->id }}"
                                            class="form-check-label">Suspicious</label>
                                    </div>
                                </td>
                            </div>
                        @endif
                        @if (
                            $data->status == '1' ||
                                $data->chargebacks == '0' ||
                                $data->is_flagged == '0' ||
                                $data->refund == '0')
                            <div class="col-md-2">
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" name="TransactionCancel" class="form-check-input"
                                            id="TransactionCancel{{ $data->id }}" data-id="{{ $data->id }}">
                                        <label for="TransactionCancel{{ $data->id }}"
                                            class="form-check-label">Declined</label>
                                    </div>
                                </td>
                            </div>
                        @else
                        @endif
                    @endif
                </div>
            </div>
        @endif
    @endif
    <div class="col-md-12">
        <h4 class="text-danger mb-1 mt-2"> Order No. : {{ $data->order_id }}</h4>
    </div>
    <div class="col-md-12">
        <div class="custom-tab-1">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#Billiing"> Billing Info</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#Card"> Card Info</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#Extra1"> Extra Info</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#Bin"> Bin Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#Response"> Request Data</a>
                </li>
                @if ($data->status == '1')
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#ChargebackSs"> ChargeBack SS</a>
                    </li>
                @endif
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="Billiing" role="tabpanel">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <tr>
                                <td>
                                    <strong>First Name</strong>
                                    <p class="mb-0"> {{ $data->first_name }} </p>
                                </td>
                                <td>
                                    <strong>Last Name</strong>
                                    <p class="mb-0"> {{ $data->last_name }} </p>
                                </td>
                                <td>
                                    <strong>Address</strong>
                                    <p class="mb-0"> {{ $data->address }} </p>
                                </td>
                                <td>
                                    <strong>Country</strong>
                                    <p class="mb-0"> {{ $data->country }} </p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>State</strong>
                                    <p class="mb-0">{{ $data->state }}</p>
                                </td>
                                <td>
                                    <strong>City</strong>
                                    <p class="mb-0"> {{ $data->city }} </p>
                                </td>
                                <td>
                                    <strong>Zip Code</strong>
                                    <p class="mb-0"> {{ $data->zip }} </p>
                                </td>
                                @if (Auth::guard('admin')->check())
                                    <td>
                                        <strong>IP Address</strong>
                                        <p class="mb-0"> {{ $data->ip_address }} </p>
                                    </td>
                                @endif
                            </tr>
                            <tr>
                                @if (Auth::guard('admin')->check())
                                    <td>
                                        <strong>User IP</strong>
                                        <p class="mb-0"> {{ $data->user_ip }} </p>
                                    </td>
                                @elseif($data->user_ip != null)
                                    <td>
                                        <strong>IP Address</strong>
                                        <p class="mb-0"> {{ $data->user_ip }} </p>
                                    </td>
                                @else
                                    <td>
                                        <strong>IP Address</strong>
                                        <p class="mb-0"> {{ $data->ip_address }} </p>
                                    </td>
                                @endif

                                <td>
                                    <strong>Customer Order ID</strong>
                                    <p class="mb-0"> {{ $data->customer_order_id }} </p>
                                </td>
                                @if (Auth::guard('admin')->check())
                                    <td>
                                        <strong>Session ID</strong>
                                        <p class="mb-0"> {{ $data->session_id }} </p>
                                    </td>
                                    <td>
                                        <strong>Gateway ID</strong>
                                        <p class="mb-0"> {{ $data->gateway_id }} </p>
                                    </td>
                                @endif
                            </tr>
                            <tr>
                                @if (auth()->guard('admin')->user()->can(['company-name']))
                                    <td>
                                        <strong>Email</strong>
                                        <p class="mb-0"> {{ $data->email }} </p>
                                    </td>
                                @endif
                                <td>
                                    <strong>Phone No.</strong>
                                    <p class="mb-0"> {{ $data->phone_no }} </p>
                                </td>
                                <td>
                                    <strong>Reason</strong>
                                    <p class="mb-0"> {{ $data->reason }} </p>
                                </td>
                                <td>
                                    <strong>Status</strong>
                                    <br>
                                    @if ($data->status == '1')
                                        <label class="badge badge-sm badge-success">Success</label>
                                    @elseif($data->status == '2')
                                        <label class="badge badge-sm badge-warning">Pending</label>
                                    @elseif($data->status == '3')
                                        <label class="badge badge-sm badge-yellow">Canceled</label>
                                    @elseif($data->status == '4')
                                        <label class="badge badge-sm badge-primary">To Be Confirm</label>
                                    @elseif($data->status == '5')
                                        <label class="badge badge-sm badge-primary">Blocked</label>
                                    @elseif($data->status == '7')
                                            <label class="badge badge-sm badge-warning">3Ds Redirect</label>
                                    @else
                                        <label class="badge badge-sm badge-danger">Declined</label>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <strong>Transaction Date</strong>
                                    <p class="mb-0">{{ $data->created_at }} </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="Card">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <tr>
                                @if ($data->card_no != null)
                                    <td>
                                        <strong>Card Type</strong>
                                        <p class="mb-0">
                                            @if ($data->card_type == '1')
                                                Amex
                                            @elseif($data->card_type == '2')
                                                Visa
                                            @elseif($data->card_type == '3')
                                                Master Card
                                            @elseif($data->card_type == '4')
                                                Discover
                                            @elseif($data->card_type == '5')
                                                JCB
                                            @elseif($data->card_type == '6')
                                                Maestro
                                            @elseif($data->card_type == '7')
                                                Switch
                                            @elseif($data->card_type == '8')
                                                Solo
                                            @elseif($data->card_type == '9')
                                                Unionpay
                                            @endif
                                        </p>
                                    </td>
                                @endif
                                <td>
                                    <strong>Amount</strong>
                                    <p class="mb-0">{{ $data->amount }}</p>
                                </td>
                                <td>
                                    <strong>Currency</strong>
                                    <p class="mb-0">{{ $data->currency }}</p>
                                </td>
                                @if ($data->card_no != null)
                                    <td>
                                        <strong>Card No.</strong>
                                        <p class="mb-0">
                                            @if (strlen($data->card_no) > 4)
                                                {!! substr($data->card_no, 0, 6) . 'XXXXXX' . substr($data->card_no, -4) !!}
                                            @else
                                                {!! $data->card_no !!}
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <strong>Expiry Month</strong>
                                        <p class="mb-0">{{ $data->ccExpiryMonth }}</p>
                                    </td>
                                    <td>
                                        <strong>Expiry Year</strong>
                                        <p class="mb-0">{{ $data->ccExpiryYear }}</p>
                                    </td>
                                @endif
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="Extra1">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <tr>
                                @if ($data->mid_descriptor != '' && $tab == 'all')
                                    <td>
                                        <strong>Descriptor</strong>
                                        <p class="mb-0">{{ $data->mid_descriptor }}</p>
                                    </td>
                                @endif

                                @if ($data->is_pre_arbitration == '1')
                                    <td>
                                        <strong>Pre Arbitration</strong>
                                        <br><label class="badge badge-sm badge-success">YES</label>
                                    </td>
                                    <td>
                                        <strong>Pre Arbitration Date</strong>
                                        <p class="mb-0">
                                            {{ date('d-m-Y / H:i:s', strtotime($data->pre_arbitration_date)) }}</p>
                                    </td>
                                @endif

                                @if ($data->chargebacks == '1')
                                    <td>
                                        <strong>Chargebacks</strong>
                                        <br><label class="badge badge-sm badge-success">YES</label>
                                    </td>
                                    <td>
                                        <strong>Chargebacks Date</strong>
                                        <p class="mb-0">
                                            {{ date('d-m-Y / H:i:s', strtotime($data->chargebacks_date)) }}</p>
                                    </td>
                                    @if ($data->changebanks_reason != '')
                                        <td>
                                            <strong>Chargebacks Reason</strong>
                                            <p class="mb-0">{{ $data->changebanks_reason }}</p>
                                        </td>
                                    @endif
                                @endif
                            </tr>
                            <tr>
                                @if ($data->refund == '1')
                                    <td>
                                        <strong>Refund</strong>
                                        <p class="mb-0"><label class="badge badge-sm badge-success">YES</label></p>
                                    </td>
                                    <td>
                                        <strong>Refund Date</strong>
                                        <p class="mb-0">{{ date('d-m-Y / H:i:s', strtotime($data->refund_date)) }}
                                        </p>
                                    </td>
                                    @if ($data->refund_reason != '')
                                        <td>
                                            <strong>Refund Reason</strong>
                                            <p class="mb-0">{{ $data->refund_reason }}</p>
                                        </td>
                                    @endif
                                @endif

                                @if ($data->is_flagged == '1')
                                    <td>
                                        <strong>Suspicious</strong>
                                        <p class="mb-0"><label class="badge badge-sm badge-success">YES</label></p>
                                    </td>
                                    <td>
                                        <strong>Suspicious Date</strong>
                                        <p class="mb-0">
                                            {{ convertDateToLocal($data->flagged_date, 'd-m-Y / H:i:s') }}</p>
                                    </td>
                                    @if (Auth::guard('admin')->check())
                                        <td>
                                            <strong>Suspicious by</strong>
                                            <p class="mb-0">{{ $data->flagged_by }}</p>
                                        </td>
                                    @endif
                                @endif
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="Bin">
                    <div class="row">
                        <div class="col-md-12">
                            @php
                                $json = json_decode($data->bin_details);
                                echo '
                            <pre><code class="language-json">';
                                echo json_encode($json, JSON_PRETTY_PRINT);
                                echo '</code></pre>';
                            @endphp
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="Response">
                    <div class="row">
                        <div class="col-md-12">
                            @php
                                $json = json_decode($data->request_data);
                                if (!is_null($json)) {
                                    echo '
                            <pre><code class="language-json">';
                                    echo json_encode($json, JSON_PRETTY_PRINT);
                                    echo '</code></pre>';
                                } else {
                                    echo "<span class='text-red'> No data available. </span>";
                                }
                            @endphp
                        </div>
                    </div>
                </div>

                @if ($data->status == '1')
                    <div class="tab-pane fade" id="ChargebackSs">
                        <div class="table-responsive custom-table">
                            <table class="table table-borderless table-striped">
                                <tr>
                                    <td>
                                        <strong>First Name</strong>
                                        <p class="mb-0"> {{ $data->first_name }} </p>
                                    </td>
                                    <td>
                                        <strong>Last Name</strong>
                                        <p class="mb-0"> {{ $data->last_name }} </p>
                                    </td>
                                    <td>
                                        <strong>Address</strong>
                                        <p class="mb-0"> {{ $data->address }} </p>
                                    </td>
                                    <td>
                                        <strong>Country</strong>
                                        <p class="mb-0"> {{ $data->country }} </p>
                                    </td>
                                    <td>
                                        <strong>State</strong>
                                        <p class="mb-0">{{ $data->state }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>City</strong>
                                        <p class="mb-0"> {{ $data->city }} </p>
                                    </td>
                                    <td>
                                        <strong>Zip Code</strong>
                                        <p class="mb-0"> {{ $data->zip }} </p>
                                    </td>
                                    @if (Auth::guard('admin')->check())
                                        <td>
                                            <strong>IP Address</strong>
                                            <p class="mb-0"> {{ $data->ip_address }} </p>
                                        </td>
                                        <td>
                                            <strong>User IP</strong>
                                            <p class="mb-0"> {{ $data->user_ip }} </p>
                                        </td>
                                    @elseif($data->user_ip != null)
                                        <td>
                                            <strong>IP Address</strong>
                                            <p class="mb-0"> {{ $data->user_ip }} </p>
                                        </td>
                                    @else
                                        <td>
                                            <strong>IP Address</strong>
                                            <p class="mb-0"> {{ $data->ip_address }} </p>
                                        </td>
                                    @endif
                                    <td>
                                        <strong>Customer Order ID</strong>
                                        <p class="mb-0"> {{ $data->customer_order_id }} </p>
                                    </td>
                                </tr>
                                <tr>
                                    @if (Auth::guard('admin')->check())
                                        <td>
                                            <strong>Session ID</strong>
                                            <p class="mb-0"> {{ $data->session_id }} </p>
                                        </td>
                                        <td>
                                            <strong>Gateway ID</strong>
                                            <p class="mb-0"> {{ $data->gateway_id }} </p>
                                        </td>
                                    @endif
                                    <td>
                                        <strong>Email</strong>
                                        <p class="mb-0"> {{ $data->email }} </p>
                                    </td>
                                    <td>
                                        <strong>Phone No.</strong>
                                        <p class="mb-0"> {{ $data->phone_no }} </p>
                                    </td>
                                    <td>
                                        <strong>Reason</strong>
                                        <p class="mb-0">
                                            {{ $data->reason }}
                                            {{-- Showing Detailed Reason while its not empty and status is declined only --}}
                                            @if (!empty($data->detailed_reason))
                                                <br>
                                                <small class="text-muted">
                                                    ({{ $data->detailed_reason }})
                                                </small>
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Status</strong>
                                        <br>
                                        @if ($data->status == '1')
                                            <label class="badge badge-sm badge-success">Success</label>
                                        @elseif($data->status == '2')
                                            <label class="badge badge-sm badge-warning">Pending</label>
                                        @elseif($data->status == '3')
                                            <label class="badge badge-sm badge-yellow">Canceled</label>
                                        @elseif($data->status == '4')
                                            <label class="badge badge-sm badge-info">To Be Confirm</label>
                                        @elseif($data->status == '5')
                                            <label class="badge badge-sm badge-info">Blocked</label>
                                        @elseif($data->status == '7')
                                            <label class="badge badge-sm badge-warning">3Ds Redirect</label>
                                        @else
                                            <label class="badge badge-sm badge-danger">Declined</label>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>Transaction Date</strong>
                                        <p class="mb-0"> {{ $data->created_at }} </p>
                                    </td>
                                    @if ($data->card_no != null)
                                        <td>
                                            <strong>Card Type</strong>
                                            <p class="mb-0">
                                                @if ($data->card_type == '1')
                                                    Amex
                                                @elseif($data->card_type == '2')
                                                    Visa
                                                @elseif($data->card_type == '3')
                                                    Master Card
                                                @elseif($data->card_type == '4')
                                                    Discover
                                                @elseif($data->card_type == '5')
                                                    JCB
                                                @elseif($data->card_type == '6')
                                                    Maestro
                                                @elseif($data->card_type == '7')
                                                    Switch
                                                @elseif($data->card_type == '8')
                                                    Solo
                                                @endif
                                            </p>
                                        </td>
                                    @endif
                                    <td>
                                        <strong>Amount</strong>
                                        <p class="mb-0">{{ $data->amount }}</p>
                                    </td>
                                    <td>
                                        <strong>Currency</strong>
                                        <p class="mb-0">{{ $data->currency }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    @if ($data->card_no != null)
                                        <td>
                                            <strong>Card No.</strong>
                                            <p class="mb-0">
                                                @if (strlen($data->card_no) > 4)
                                                    {!! substr($data->card_no, 0, 6) . 'XXXXXX' . substr($data->card_no, -4) !!}
                                                @else
                                                    {!! $data->card_no !!}
                                                @endif
                                            </p>
                                        </td>
                                        <td>
                                            <strong>Expiry Month</strong>
                                            <p class="mb-0">{{ $data->ccExpiryMonth }}</p>
                                        </td>
                                        <td colspan="3">
                                            <strong>Expiry Year</strong>
                                            <p class="mb-0">{{ $data->ccExpiryYear }}</p>
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
