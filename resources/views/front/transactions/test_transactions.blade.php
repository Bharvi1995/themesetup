@extends('layouts.user.default')

@section('title')
    Test Payments
@endsection

@section('breadcrumbTitle')
    @if (\Auth::user()->is_white_label == '1')
        <a href="#">Dashboard</a> / Test Payments
    @else
        <a href="{{ route('dashboardPage') }}">Dashboard</a> / Test Payments
    @endif
@endsection

@section('content')
    @include('requestDate')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
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
                                    <label for="text">Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="start_date"
                                            placeholder="Start Date" id="start_date"
                                            value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="end_date"
                                            placeholder="End Date" id="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
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
        <div class="col-md-6">
            <h4 class="mt-50">Test Payments List</h4>
        </div>
        <div class="col-md-6 text-right">
            @if (\Auth::user()->is_white_label == '0')
                <form method="POST" action="{{ route('testTransactions.export', request()->all()) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary" id="ExcelLink"><i class="fa fa-download mr-2"></i>
                        Export Excel </button>
                </form>
            @endif
        </div>
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <div></div>
                    <div>
                        <form style="float:left;" class="me-50 form-dark" id="noListform" method="GET">
                            <select class="form-control-sm form-control" name="noList" id="noList">
                                <option value="">--No of Records--</option>
                                <option value="30" {{ request()->get('noList') == '30' ? 'selected' : '' }}>30
                                </option>
                                <option value="50" {{ request()->get('noList') == '50' ? 'selected' : '' }}>50
                                </option>
                                <option value="100" {{ request()->get('noList') == '100' ? 'selected' : '' }}>100
                                </option>
                            </select>
                        </form>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm searchModelOpen" data-bs-toggle="modal"
                                data-bs-target="#searchModal"> <i class="fa fa-search-plus"></i> Advanced
                                Search &nbsp;
                                <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg></button>
                            <a href="{{ route('getTestTransactions') }}" class="btn btn-danger btn-sm"
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
                                    <th>Amount</th>
                                    <th>Email</th>
                                    <th style="min-width: 150px;">Timestamp</th>
                                    <th>Action</th>
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
                                                    <label class="light badge badge-sm badge-success">Success</label>
                                                @elseif($transaction->status == '2')
                                                    <label class="light badge badge-sm badge-warning">Pending</label>
                                                @elseif($transaction->status == '5')
                                                    <label class="light badge badge-sm badge-primary">Blocked</label>
                                                @elseif($transaction->status == '7')
                                                    <label class="light badge badge-sm badge-primary">3ds Redirect</label>
                                                @else
                                                    <label class="light badge badge-sm badge-danger">Declined</label>
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
                                            <td>{{ $transaction->email }}</td>
                                            <td>
                                                {{ $transaction->created_at->format('d-m-Y H:i:s') }}
                                            </td>
                                            
                                            <td>
                                                <a class="btn btn-primary btn-icon" href="{{ route('transaction.show', ['id' => $transaction->id]) }}">
                                                    <div class="svg-icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                                    </div>
                                                </a>
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
                    <div class="card-footer">
                        @if (!empty($data) && $data->count())
                            <div class="row">
                                <div class="col-md-8">
                                    {!! $data->appends($_GET)->links() !!}
                                </div>
                                <div class="col-md-4 text-right">
                                    Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of total
                                    {{ $data->total() }}
                                    entries
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.transactions.show-modal')
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/front/transactions.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
@endsection
