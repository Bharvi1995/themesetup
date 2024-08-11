@extends($agentUserTheme)
@section('title')
   Payments List
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('rp.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Collections List</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Collections List</h6>
    </nav>
@endsection

@section('customStyle')
@endsection
@section('content')
    @include('requestDate')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <form method="" id="search-form" class="form-dark">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">More Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                    </div>
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="status">Status</label>
                                    <select name="status" class="form-control" data-width="100%">
                                        <option disabled selected> -- Status -- </option>
                                        <option value="1"
                                            {{ isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '' }}>Success
                                        </option>
                                        <option value="2"
                                            {{ isset($_GET['status']) && $_GET['status'] == '2' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="0"
                                            {{ isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '' }}>
                                            Declined
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="status">Select Merchant</label>
                                    <select name="company_name" class="form-control" data-width="100%">
                                        <option disabled selected> -- Select Merchant -- </option>
                                        @foreach ($businessName as $k => $v)
                                            <option value="{{ $v }}"
                                                {{ isset($_GET['company_name']) && $_GET['company_name'] == $k ? 'selected' : '' }}>
                                                {{ $v }}</option>
                                        @endforeach
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
                                    <label for="order_no">Order no</label>
                                    <input type="text" class="form-control" placeholder="Enter here..." name="order_id"
                                        value="{{ isset($_GET['order_id']) && $_GET['order_id'] != '' ? $_GET['order_id'] : '' }}">
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
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h5 class="card-title">All Collections</h5>
                    </div>
                    <div class="card-header-toolbar align-items-center">
                        <div class="btn-group mr-2">
                            <a href="{{ route('rp-merchant-transactions', ['type' => 'xlsx'] + request()->all()) }}" class="btn btn-sm btn-outline-primary mr-2" id="ExcelLink"> Export Excel</a>
                            <button class="btn btn-primary btn-sm searchModelOpen" data-bs-toggle="modal" data-bs-target="#searchModal"> More Filter &nbsp;
                            <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                    fill="#FFFFFF" />
                            </svg>
                        </button>
                        <a href="{{ route('rp-merchant-transactions') }}" class="btn btn-danger btn-sm" style="border-radius: 0px 5px 5px 0px !important;">Clear</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    $url = Request::fullUrl();
                    $parsedUrl = parse_url($url);
                    $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
                    $subQueryString = $query != '' ? $query . '&type=xlsx' : '';
                    ?>
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Transactions ID</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Business Name</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $transaction)
                                    <tr id="tr_{{ $transaction->id }}">
                                        <td class="align-middle text-center text-sm">
                                            @if ($transaction->chargebacks == '1')
                                                <span class="badge badge-sm bg-gradient-danger">Chargeback</span>
                                            @elseif ($transaction->is_flagged == '1')
                                                <span class="badge badge-sm bg-gradient-warning">Dispute</span>
                                            @elseif ($transaction->refund == '1')
                                                <span class="badge badge-sm bg-gradient-info">Refund</span>
                                            @else
                                                @if ($transaction->status == '1')
                                                    <span class="badge badge-sm bg-gradient-success">Success</span>
                                                @elseif($transaction->status == '5')
                                                    <span class="badge badge-sm bg-gradient-secondary">Blocked</span>
                                                @elseif($transaction->status == '0')
                                                    <span class="badge badge-sm bg-gradient-danger">Declined</span>
                                                 @else
                                                    <span class="badge badge-sm bg-gradient-warning">Pending</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            {{ $transaction->amount ." ". $transaction->currency }}
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            {{ $transaction->order_id }}
                                        </td>   
                                                    
                                        
                                        <td class="align-middle text-center text-sm">{{ $transaction->email }}
                                            
                                        </td>
                                        <td class="align-middle text-center text-sm">{{ $transaction->business_name }}</td>
                                        <td class="align-middle text-center text-sm">
                                            @if ($transaction->created_at)
                                                {{ $transaction->created_at->format('dS F Y H:i:s') }}
                                            @else
                                                N/A
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
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/admin/transactions.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
@endsection
