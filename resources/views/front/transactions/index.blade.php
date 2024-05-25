@extends('layouts.user.default')

@section('title')
    Payments List
@endsection

@section('breadcrumbTitle')
    @if (\Auth::user()->is_white_label == '1')
        <a href="#">Dashboard</a> / Payments List
    @else
        <a href="{{ route('dashboardPage') }}">Dashboard</a> / Payments List
    @endif
@endsection

@section('content')
    @include('requestDate')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document"
            style="max-width:600px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Advanced Search</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="" id="search-form" class="form-dark">
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">First Name</label>
                                    <input type="text" class="form-control " placeholder="First Name" name="first_name"
                                        value="{{ isset($_GET['first_name']) && $_GET['first_name'] != '' ? $_GET['first_name'] : '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name">Last Name</label>
                                    <input type="text" class="form-control" placeholder="Last Name" name="last_name"
                                        value="{{ isset($_GET['last_name']) && $_GET['last_name'] != '' ? $_GET['last_name'] : '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" placeholder="Email" name="email"
                                        value="{{ isset($_GET['email']) && $_GET['email'] != '' ? $_GET['email'] : '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="status">Status</label>
                                    <select name="status" class="form-control select2" data-width="100%">
                                        <option disabled selected> -- Status -- </option>
                                        <option value="1"
                                            {{ isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '' }}>Success
                                        </option>
                                        <option value="2"
                                            {{ isset($_GET['status']) && $_GET['status'] == '2' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="5"
                                            {{ isset($_GET['status']) && $_GET['status'] == '5' ? 'selected' : '' }}>Blocked
                                        </option>
                                        <option value="7"
                                            {{ isset($_GET['status']) && $_GET['status'] == '7' ? 'selected' : '' }}>3ds
                                            Redirect</option>
                                        <option value="0"
                                            {{ isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '' }}>
                                            Declined
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">Created Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="start_date"
                                            placeholder="Start Date" id="start_date"
                                            value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">Created End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="end_date"
                                            placeholder="End Date" id="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label for="text">Currency</label>
                                    <select class="form-control select2" name="currency" id="currency">
                                        <option selected disabled> -- Select Currency -- </option>
                                        @foreach (config('currency.three_letter') as $key => $currency)
                                            <option value="{{ $currency }}"
                                                {{ isset($_GET['currency']) && $_GET['currency'] == $key ? 'selected' : '' }}>
                                                {{ $currency }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="country">Country</label>
                                    <select name="country" id="country" class="form-control select2">
                                        <option selected disabled> -- Select country -- </option>
                                        @foreach (getCountry() as $key => $country)
                                            <option value="{{ $key }}"
                                                {{ isset($_GET['country']) && $_GET['country'] == $key ? 'selected' : '' }}>
                                                {{ $country }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="order_no">Order no.</label>
                                    <input type="text" class="form-control" placeholder="Order No." name="order_id"
                                        value="{{ $_GET['order_id'] ?? '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="transaction_ref">Transactions Reference</label>
                                    <input type="text" class="form-control" placeholder="transaction_ref"
                                        name="transaction_ref"
                                        value="{{ isset($_GET['transaction_ref']) && $_GET['transaction_ref'] != '' ? $_GET['transaction_ref'] : '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="country">Chargeback Transactions</label>
                                    <select name="chargeback" id="chargeback" class="form-control select2">
                                        <option selected disabled> -- Select -- </option>
                                        <option value="1" {{ isset($_GET['chargeback']) && $_GET['chargeback'] == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ isset($_GET['chargeback']) && $_GET['chargeback'] == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">Chargebacks Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="chargebacks_start_date"
                                            placeholder="Chargeback Start Date" id="chargebacks_start_date"
                                            value="{{ isset($_GET['chargebacks_start_date']) && $_GET['chargebacks_start_date'] != '' ? $_GET['chargebacks_start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">Chargebacks End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="chargebacks_end_date"
                                            placeholder="Chargeback End Date" id="chargebacks_end_date"
                                            value="{{ isset($_GET['chargebacks_end_date']) && $_GET['chargebacks_end_date'] != '' ? $_GET['chargebacks_end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="country">Dispute Transactions</label>
                                    <select name="dispute" id="dispute" class="form-control select2">
                                        <option selected disabled> -- Select -- </option>
                                        <option value="1" {{ isset($_GET['dispute']) && $_GET['dispute'] == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ isset($_GET['dispute']) && $_GET['dispute'] == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">Dispute Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="dispute_start_date"
                                            placeholder="Dispute Start Date" id="dispute"
                                            value="{{ isset($_GET['dispute']) && $_GET['dispute'] != '' ? $_GET['dispute'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">Dispute End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="dispute_end_date"
                                            placeholder="Dispute End Date" id="dispute_end_date"
                                            value="{{ isset($_GET['dispute_end_date']) && $_GET['dispute_end_date'] != '' ? $_GET['dispute_end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="country">Refund</label>
                                    <select name="refund" id="refund" class="form-control select2">
                                        <option selected disabled> -- Select -- </option>
                                        <option value="1" {{ isset($_GET['refund']) && $_GET['refund'] == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ isset($_GET['refund']) && $_GET['refund'] == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">Refund Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="refund_start_date"
                                            placeholder="Refund Start Date" id="refund_start_date"
                                            value="{{ isset($_GET['refund_start_date']) && $_GET['refund_start_date'] != '' ? $_GET['refund_start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">Refund End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="refund_end_date"
                                            placeholder="Refund End Date" id="refund_end_date"
                                            value="{{ isset($_GET['refund_end_date']) && $_GET['refund_end_date'] != '' ? $_GET['refund_end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="country">Retrieval</label>
                                    <select name="retrieval" id="retrieval" class="form-control select2">
                                        <option selected disabled> -- Select -- </option>
                                        <option value="1" {{ isset($_GET['retrieval']) && $_GET['retrieval'] == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ isset($_GET['retrieval']) && $_GET['retrieval'] == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">Retrieval Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="retrieval_start_date"
                                            placeholder="Retrieval Start Date" id="retrieval_start_date"
                                            value="{{ isset($_GET['retrieval_start_date']) && $_GET['retrieval_start_date'] != '' ? $_GET['retrieval_start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">Retrieval End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="retrieval_end_date"
                                            placeholder="Retrieval End Date" id="retrieval_end_date"
                                            value="{{ isset($_GET['retrieval_end_date']) && $_GET['retrieval_end_date'] != '' ? $_GET['retrieval_end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="resetForm"></i>Clear</button>
                        <button type="submit" class="btn btn-success" id="extraSearch123">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h4 class="mt-50">Payments List</h4>
        </div>
        <div class="col-md-6 text-right">
            <form action="{{ route('transactions.exportAllTransactions', request()->all()) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary" id="ExcelLink"><i class="fa fa-download"></i>
                    Export Excel</button>
            </form>
        </div>
        <div class="col-xl-12 col-xxl-12">
            <div class="card mt-2">
                <div class="card-header">
                    <div></div>
                    <div>
                        <form id="noListform" method="GET" style="float:left;" class="me-50 form-dark">
                            <select class="form-control form-control-sm" name="noList" id="noList">
                                <option value="">No of Records</option>
                                <option value="30" {{ request()->get('noList') == '30' ? 'selected' : '' }}>30
                                </option>
                                <option value="50" {{ request()->get('noList') == '50' ? 'selected' : '' }}>50
                                </option>
                                <option value="100" {{ request()->get('noList') == '100' ? 'selected' : '' }}>100
                                </option>
                            </select>
                        </form>
                        <div class="btn-group">
                            <button class="btn btn-primary btn-sm searchModelOpen" data-bs-toggle="modal"
                                data-bs-target="#searchModal">
                                Advanced Search &nbsp;
                                <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg>
                            </button>
                            <a href="{{ route('gettransactions') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>Transaction Number</th>
                                    <th>Status</th>
                                    <th style="min-width: 160px;">Amount</th>
                                    <th>Email</th>
                                    <th >Timestamp</th>
                                    <th style="min-width: 160px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($data) > 0)
                                    @foreach ($data as $transaction)
                                        <tr id="tr_{{ $transaction->id }}">
                                            <td>
                                                <a href="javascript:;" class="text-danger">
                                                    {{ $transaction->order_id }}
                                                </a>
                                                <br>
                                                {{ $transaction->customer_order_id }}
                                            </td>
                                            <td>
                                                @if ($transaction->status == '1')
                                                    <label class="badge-sm badge badge-success">Success</label>
                                                @elseif($transaction->status == '5')
                                                    <label class="badge-sm badge badge-primary">Blocked</label>
                                                @elseif($transaction->status == '0')
                                                    <label class="badge-sm badge badge-danger">Declined</label>
                                                 @else
                                                    <label class="badge-sm badge badge-warning">Pending</label>
                                                @endif

                                                @if ($transaction->chargebacks == '1')
                                                    <label class="badge-sm badge badge-danger">Chargeback</label>
                                                @endif
                                                @if ($transaction->is_flagged == '1')
                                                    <label class="badge-sm badge badge-warning">Dispute</label>
                                                @endif
                                                @if ($transaction->is_retrieval == '1')
                                                    <label class="badge-sm badge badge-info">Retrieval</label>
                                                @endif
                                                @if ($transaction->refund == '1')
                                                    <label class="badge-sm badge badge-primary">Refund</label>
                                                @endif
                                            </td>
                                            
                                            <td>
                                                @if ($transaction->is_converted == '1')
                                                    {{ $transaction->converted_amount." ". $transaction->converted_currency }}
                                                @else
                                                    {{ $transaction->amount ." ". $transaction->currency }}
                                                @endif
                                                <br>
                                                @if ($transaction->card_type == '2')
                                                    <label class="text text-info">Visa</label>
                                                @elseif($transaction->card_type == '3')
                                                    <label class="text text-warning">Master</label>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->email }}
                                                
                                            </td>
                                            <td>
                                                @if ($transaction->created_at)
                                                    {{ $transaction->created_at->format('d-m-Y H:i:s') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            

                                            <td>
                                                <a class="btn btn-primary btn-icon" href="{{ route('transaction.show', ['id' => $transaction->id]) }}">
                                                    <div class="svg-icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                                    </div>
                                                </a>
                                                @if ($transaction->refund != '1')
                                                    @if ($transaction->status == '1' && $transaction->chargebacks == 0)
                                                        <a class="btn btn-warning btn-icon refundTransaction" data-bs-toggle="modal" data-toggle="Refund" href="#refundTransaction" data-id="{{ $transaction->id }}">
                                                            <div class="svg-icon">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                                                            </div>
                                                        </a>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8">
                                            <p class="text-center"><strong>No record found</strong></p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    @if (!empty($data) && $data->count())
                        <div class="row">
                            <div class="col-md-9">
                                {!! $data->appends($_GET)->links() !!}
                            </div>
                            <div class="col-md-3 text-right">
                                Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of total {{ $data->total() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('partials.transactions.show-modal')

    {{-- Refund Transaction Modal --}}
    <div class="modal fade" id="refundTransaction" tabindex="-1" role="modal" aria-hidden="true"
        style="display: none; padding-right: 15px;">
        <form id="refundTransactionForm">
            {{ csrf_field() }}
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Refund Transaction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body" style="overflow: hidden;">
                        <div class="form-group form-dark">
                            <label>Refund Reason</label>
                            <textarea class="form-control" name="refund_reason" id="refund_reason" rows="3"
                                placeholder="Write here your refund transaction reason."></textarea>
                            <span class="help-block text-danger">
                                <strong id="refund_reason_error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-primary" data-bs-dismiss="modal"
                            id="closeRefundForm">Close</button>
                        <button type="button" id="submitRefundTtransactionForm"
                            data-link="{{ route('transactions-refund') }}" class="btn btn-success">
                            Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Send email Modal --}}
    <div class="modal fade" id="sendEmailTransaction" tabindex="-1" role="modal" aria-hidden="true"
        style="display: none; padding-right: 15px;">
        <form id="sendEmailTransactionForm">
            {{ csrf_field() }}
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Send Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body" style="overflow: hidden;">
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="email" class="form-control" name="email_address" id="email_address"
                                placeholder="example@gmail.com">
                            <span class="help-block text-danger">
                                <strong id="email_address_error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-primary" data-bs-dismiss="modal"
                            id="closeRefundForm">Close</button>
                        <button type="button" id="submitSendEmailFormSend"
                            data-link="{{ route('transactions-sendmail') }}" class="btn btn-success">Send</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/front/transactions.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
@endsection
