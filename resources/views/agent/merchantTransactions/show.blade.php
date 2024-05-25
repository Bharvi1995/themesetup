@extends($agentUserTheme)
@section('title')
    Details of Transaction
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('rp.dashboard') }}">Dashboard</a> / Details of Transaction
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="iq-card">
                <div class="iq-card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Details of Transaction</h4>
                    </div>
                    <a href="{{ route('rp-merchant-transactions') }}" class="btn btn-primary btn-sm"> <i
                            class="fa fa-arrow-left" aria-hidden="true"></i></a>
                </div>
                <div class="card-body">
                    <div class="custom-tab-1">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#Billing">Billing Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#Card">Card Info</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="Billing" role="tabpanel">
                                <div class="pt-4">
                                    <div class="table-responsive custom-table">
                                        <table class="table table-borderless table-striped">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Order No.</strong></td>
                                                    <td>{{ $data->order_id }}</td>
                                                </tr>
                                                <!-- <tr>
                                                            <td><strong>Company Name</strong></td>
                                                            <td>Mr. Bobby</td>
                                                        </tr> -->
                                                <tr>
                                                    <td><strong>First Name</strong></td>
                                                    <td>{{ $data->first_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Last Name</strong></td>
                                                    <td>{{ $data->last_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Address</strong></td>
                                                    <td>{{ $data->address }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Country</strong></td>
                                                    <td>{{ $data->country }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>State</strong></td>
                                                    <td>{{ $data->state }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>City</strong></td>
                                                    <td>{{ $data->city }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Zip Code</strong></td>
                                                    <td>{{ $data->zip }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>IP Address</strong></td>
                                                    <td>{{ $data->ip_address }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Email</strong></td>
                                                    <td>{{ $data->email }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Phone No.</strong></td>
                                                    <td>{{ $data->phone_no }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Reason</strong></td>
                                                    <td>{{ $data->reason }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Status</strong></td>
                                                    <td>
                                                        @if ($data->status == '1')
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
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Transaction Date</strong></td>
                                                    <td>{{ convertDateToLocal($data->created_at, 'd-m-Y / H:i:s') }}</td>
                                                </tr>
                                                @if ($data->chargebacks == '1')
                                                    <tr>
                                                        <td><strong>Chargebacks</strong></td>
                                                        <td><label class="badge badge-success">YES</label></td>
                                                    </tr>
                                                @endif
                                                @if ($data->refund == '1')
                                                    <tr>
                                                        <td><strong>Refund</strong></td>
                                                        <td><label class="badge badge-success">YES</label></td>
                                                    </tr>
                                                    @if ($data->refund_reason != '')
                                                        <tr>
                                                            <td><strong>Refund Reason</strong></td>
                                                            <td>{{ $data->refund_reason }}</td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="Card">
                                <div class="pt-4">
                                    <div class="table-responsive">
                                        <table class="table table-responsive-md">
                                            <tbody>
                                                @if ($data->card_no != null)
                                                    <tr>
                                                        <td><strong>Card Type</strong></td>
                                                        <td>
                                                            @if ($data->card_type == 1)
                                                                Amex
                                                            @elseif($data->card_type == 2)
                                                                Visa
                                                            @elseif($data->card_type == 3)
                                                                Master Card
                                                            @else
                                                                Discover
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td><strong>Amount</strong></td>
                                                    <td>{{ $data->amount }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Currency</strong></td>
                                                    <td>{{ $data->currency }}</td>
                                                </tr>
                                                @if ($data->card_no != null)
                                                    <tr>
                                                        <td><strong>Card No.</strong></td>
                                                        <td>
                                                            @if (strlen($data->card_no) > 4)
                                                                {!! substr($data->card_no, 0, 6) . 'XXXXXX' . substr($data->card_no, -4) !!}
                                                            @else
                                                                {!! $data->card_no !!}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Expiry Month</strong></td>
                                                        <td>{{ $data->ccExpiryMonth }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Expiry Year</strong></td>
                                                        <td>{{ $data->ccExpiryYear }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>CVV</strong></td>
                                                        <td>XXX</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
