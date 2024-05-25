@extends($WLAgentUserTheme)
@section('title')
    All Transactions
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('wl-dashboard') }}">
        Dashboard</a> / All Transactions
@endsection

@section('customeStyle')
@endsection

@section('content')
    @include('requestDate')

    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <form method="" id="search-form" class="form-dark">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">First Name</label>
                                    <input type="text" class="form-control " placeholder="Enter here..."
                                        name="first_name"
                                        value="{{ isset($_GET['first_name']) && $_GET['first_name'] != '' ? $_GET['first_name'] : '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name">Last Name</label>
                                    <input type="text" class="form-control" placeholder="Enter here..." name="last_name"
                                        value="{{ isset($_GET['last_name']) && $_GET['last_name'] != '' ? $_GET['last_name'] : '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name">Email</label>
                                    <input type="text" class="form-control" placeholder="Enter here..." name="email"
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
                                        <option value="3"
                                            {{ isset($_GET['status']) && $_GET['status'] == '3' ? 'selected' : '' }}>
                                            Canceled
                                        </option>
                                        <option value="4"
                                            {{ isset($_GET['status']) && $_GET['status'] == '4' ? 'selected' : '' }}>To Be
                                            Confirm</option>
                                        <option value="0"
                                            {{ isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '' }}>
                                            Declined
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="start_date"
                                            placeholder="Enter here..." id="start_date"
                                            value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="end_date"
                                            placeholder="Enter here..." id="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="order_no">Order no.</label>
                                    <input type="text" class="form-control" placeholder="Enter here..." name="order_id"
                                        value="{{ isset($_GET['order_id']) && $_GET['order_id'] != '' ? $_GET['order_id'] : '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="order_no">Customer Order id.</label>
                                    <input type="text" class="form-control" placeholder="Enter here..."
                                        name="customer_order_id"
                                        value="{{ isset($_GET['customer_order_id']) && $_GET['customer_order_id'] != '' ? $_GET['customer_order_id'] : '' }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="status">Select Company</label>
                                    <select name="company_name" class="form-control select2" data-width="100%">
                                        <option disabled selected> -- Select Company -- </option>
                                        @foreach ($businessName as $k => $v)
                                            <option value="{{ $v }}"
                                                {{ isset($_GET['company_name']) && $_GET['company_name'] == $k ? 'selected' : '' }}>
                                                {{ $k }} </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="resetForm"></i>Clear</button>
                        <button type="submit" class="btn btn-success" id="extraSearch123">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Transactions</h4>
                    <div>
                        <div class="btn-group mr-2">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#searchModal"> Advanced
                                Search &nbsp; <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg></button>
                            <a href="{{ route('wl-merchant-transaction') }}" class="btn btn-sm btn-danger"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                        <a href="{{ route('wl-merchant-transaction-excel', request()->all()) }}"
                            class="ms-1 btn btn-primary btn-sm">
                            <i class="fa fa-download mr-1"></i> Export Excel
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th><strong>ORDER NO.</strong></th>
                                    <th><strong>Customer ORDER NO.</strong></th>

                                    <th><strong>Email</strong></th>
                                    <th><strong>BUSINESS NAME</strong></th>
                                    <th><strong>AMOUNT</strong></th>
                                    <th><strong>STATUS</strong></th>
                                    <th><strong>REASON</strong></th>
                                    <th><strong>Created at</strong></th>
                                    <th><strong>ACTION</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $transaction)
                                    <tr id="tr_{{ $transaction->id }}">
                                        <td>
                                            <strong class="text-danger">{{ $transaction->order_id }}</strong>

                                        </td>
                                        <td>{{ $transaction->customer_order_id }}</td>

                                        <td>{{ $transaction->email }} <br /> <span
                                                class="badge badge-sm  badge-success ">{{ $transaction->first_name }}
                                                {{ $transaction->last_name }}</span></td>
                                        <td>{{ $transaction->business_name }}</td>
                                        <td>
                                            <span>
                                                @if ($transaction->is_converted == '1')
                                                    {{ $transaction->converted_amount }}
                                                    {{ $transaction->converted_currency }}
                                                @else
                                                    {{ $transaction->amount }} {{ $transaction->currency }}
                                                @endif
                                            </span>

                                        </td>
                                        <td>
                                            @if ($transaction->status == '1')
                                                <label class="light badge-sm badge badge-sm badge-success">Success</label>
                                            @elseif($transaction->status == '2')
                                                <label class="light badge-sm badge badge-sm badge-warning">Pending</label>
                                            @elseif($transaction->status == '3')
                                                <label class="light badge-sm badge badge-sm badge-yellow">Canceled</label>
                                            @elseif($transaction->status == '4')
                                                <label class="light badge-sm badge badge-sm badge-primary">To Be
                                                    Confirm</label>
                                            @else
                                                <label class="light badge-sm badge badge-sm badge-danger">Declined</label>
                                            @endif

                                        </td>
                                        <td> <span>{{ $transaction->reason }}</span></td>
                                        <td> {{ $transaction->created_at->format('d-m-Y / H:i:s') }}</td>
                                        <td>
                                            @if ($transaction->status == '1' && $transaction->chargebacks == '0' && $transaction->refund == '0')
                                                <a class="btn light btn-sm btn-danger sbold refundTransaction"
                                                    data-bs-toggle="modal" href="#refundTransaction"
                                                    data-id="{{ $transaction->id }}"> Refund </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer">
                    @if (!empty($data) && $data->count())
                        <div class="row">
                            <div class="col-md-8">
                                {!! $data->appends($_GET)->links() !!}
                            </div>
                            <div class="col-md-4 text-right">
                                Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of total {{ $data->total() }}
                                entries
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Reffund  Modal --}}
    <div class="modal fade bs-example-modal-center" id="refundTransaction" tabindex="-1" role="dialog"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lgmodal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transaction Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body" id="refundContent">
                    <form id="refundTransactionForm" class="form-dark">
                        @csrf
                        <div class="form-group">
                            <label>Refund Reason</label>
                            <textarea class="form-control" name="refund_reason" id="refund_reason" rows="3"
                                placeholder="Write here your refund transaction reason."></textarea>
                            <span class="help-block text-danger">
                                <strong id="refund_reason_error"></strong>
                            </span>
                        </div>
                        <div class="input-group">
                            <input class="form-control datepicker-here" type="hidden" name="refund_date"
                                id="refund_value" placeholder="Select Date" value="{{ date('d-m-Y') }}">
                        </div>

                        <span class="help-block text-danger">
                            <strong id="refund_error"></strong>
                        </span>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="submitRefundTtransactionForm"
                        data-link="">Submit</button>
                    <button type="button" id="closeRefundForm" class="btn btn-danger"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
    <script type="text/javascript">
        $('body').on('click', '.refundTransaction', function() {
            var id = $(this).data('id');
            $('#refundTransactionForm').append('<input type="hidden" id="transactionsID" name="id" value="' + id +
                '">');
        });
        $('body').on('click', '#closeRefundForm', function() {
            $('#refundTransactionForm').find("input[id='transactionsID']").remove();
            $('#refund_reason_error').html("");
            $('#refund_reason').val("");
        });
        $('body').on('click', '#submitRefundTtransactionForm', function() {
            var refundTransactionForm = $("#refundTransactionForm");
            var formData = refundTransactionForm.serialize();
            $('#refund_reason_error').html("");


            $.ajax({
                url: "{{ route('merchant-transactions-refund') }}",
                type: 'POST',
                context: $(this),
                data: formData,
                beforeSend: function() {
                    $(this).attr('disabled', 'disabled');
                },
                success: function(data) {
                    console.log(data);
                    if (data.errors) {
                        if (data.errors.refund_reason) {
                            $('#refund_reason_error').html(data.errors.refund_reason[0]);
                        }
                    }
                    if (data.success == '1') {
                        $('#refundTransaction').modal('hide');
                        toastr.success(
                            "Your transaction has been submitted for a refund. The refund will be processed within next 24 hours."
                        );
                        $('#refundTransactionForm').find("input[id='transactionsID']").remove();
                    } else if (data.success == '0') {
                        toastr.error("Your transaction is not refund, please try again !!");
                        $('#refundTransactionForm').find("input[id='transactionsID']").remove();
                    }
                    $('#refund_reason').val("");
                    // setTimeout(function() {
                    //     window.location.reload(1);
                    // }, 2000);
                },
            });
        });
    </script>
@endsection
