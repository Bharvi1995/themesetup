@extends('layouts.admin.default')

@section('title')
    Dashboard
@endsection

@section('breadcrumbTitle')
    Dashboard
@endsection

@section('customeStyle')
    <style>
        .dark-layout .select2-container .select2-selection,
        .dark-layout .select2-container .select2-selection__placeholder {
            /*min-width: 250px !important;*/
            /*max-width: 300px !important;*/
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            @if (auth()->guard('admin')->user()->can(['overview-transaction-statistics']))
                <div class="row">
                    <div class="col-lg-9">
                        <h4 class="mt-1">Merchant Transactions Breakdown</h4>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-control select2 merchantSelectBox">
                            <option value="">-- Select Merchant --</option>
                            @foreach ($companyList as $company)
                                <option value="{{ $company->user_id }}">{{ $company->business_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="merchantTxnPercentages" class="mt-2">
                    @include('partials.adminDashboard.dashboardTxnPercentages', [$transaction])
                </div>
            @endif

            @if (auth()->guard('admin')->user()->can(['overview-view']))
                <div class="row">
                    <div class="col-lg-9">
                        <h4 class="mt-1">Merchant's Status Overview</h4>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-control select2 agentSelectBox">
                            <option value="">-- Select Agent --</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="agentMerchantsOverview" class="mt-2">
                    @include('partials.adminDashboard.dashboardMerchantStatusOverview', [
                        $merchants,
                    ])
                </div>
            @endif

            @if (auth()->guard('admin')->user()->can(['overview-transaction-statistics']))
                <div class="row" id="dashboardTransactionSummary">
                    <div class="col-md-12 transaction-summary-tbl">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="row">
                                    <div class="col-md-12" style="padding: 30px 30px 30px 45px;">
                                        <div class="header-title">
                                            <h5 class="card-title">Transactions Volume Report</h5>
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div class="spinner-grow text-secondary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- @include('partials.adminDashboard.dashboardTransactionSummary', $TransactionSummary) --}}
                </div>
            @endif

            @if (auth()->guard('admin')->user()->can(['overview-latest-transactions']))
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h5 class="card-title">Recent Transactions</h5>
                                </div>
                                <div class="card-header-toolbar d-flex align-items-center">
                                    <a href="{!! url('admin/transactions') !!}" class="btn btn-sm btn-primary">View All</a>
                                </div>
                            </div>
                            <div class="card-body p-0 p-0">
                                <div class="table-responsive custom-table" id="latest_transactions">
                                    <table class="table table-borderless table-striped">
                                        <thead>
                                            <tr>
                                                <th>Order No.</th>
                                                <th>Date & Time</th>
                                                <th>Amount</th>
                                                <th>Currency</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($Transaction as $key => $value)
                                                <tr>
                                                    <td>{{ $value->order_id }}</td>
                                                    <td>
                                                        {{ $value->created_at ? $value->created_at->format('d-m-Y / H:i:s') : 'N/A' }}
                                                    </td>
                                                    <td>{{ $value->amount }}</td>
                                                    <td>{{ $value->currency }}</td>
                                                    <td>
                                                        @if ($value->status == '1')
                                                            <label class="badge-sm badge badge-success">Success</label>
                                                        @elseif($value->status == '2')
                                                            <label class="badge-sm badge badge-warning">Pending</label>
                                                        @elseif($value->status == '3')
                                                            <label class="badge-sm badge badge-primary">Canceled</label>
                                                        @elseif($value->status == '4')
                                                            <label class="badge-sm badge badge-primary">To Be
                                                                Confirm</label>
                                                        @else
                                                            <label class="badge-sm badge badge-danger">Declined</label>
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-lg-4">
            @if (auth()->guard('admin')->user()->can(['overview-highest-processing-merchants']))
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h5 class="card-title">Highest Processing Merchants</h5>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <a href="{{ route('merchant-transaction-report') }}" class="btn btn-sm btn-primary">View
                                All</a>
                        </div>
                    </div>
                    <div class="card-body p-0 p-0">
                        <div class="table-responsive custom-table">
                            @if (!empty($dashboardSuccessRecord) && $dashboardSuccessRecord->count())
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th>Business Name</th>
                                            <th class="text-right">Amount(USD)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dashboardSuccessRecord as $key => $value)
                                            <tr>
                                                <td>{{ $value->business_name }}</td>
                                                <td class="text-right">{{ $value->successfullV }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="p30 mb-0">No Records</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if (auth()->guard('admin')->user()->can(['overview-highest-processing-mid']))
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h5 class="card-title">Highest Processing MIDs</h5>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <a href="{{ route('mid-summary-report') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                    </div>
                    <div class="card-body p-0 p-0">
                        <div class="table-responsive custom-table">
                            @if (!empty($dashboardMIDRecord) && $dashboardMIDRecord->count())
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th>MID</th>
                                            <th class="text-right">Amount(USD)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dashboardMIDRecord as $key => $value)
                                            <tr>
                                                <td>{{ $value->bank_name }}</td>
                                                <td class="text-right">{{ $value->successfullV }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="p30 mb-0">No Records</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if (auth()->guard('admin')->user()->can(['overview-merchant-chargeback-frequency']))
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h5 class="card-title">Merchant Chargeback Frequency</h5>
                        </div>
                    </div>
                    <div class="card-body p-0 p-0">
                        <div class="table-responsive custom-table">
                            @if (!empty($dashboardChargeback) && $dashboardChargeback->count())
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th>Business Name</th>
                                            <th class="text-right">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dashboardChargeback as $key => $value)
                                            <tr>
                                                <td>{{ $value->business_name }}</td>
                                                <td class="text-right">{{ $value->totalCount }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="p30 mb-0">No Records</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if (auth()->guard('admin')->user()->can(['overview-merchant-refund-frequency']))
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h5 class="card-title">Merchant Refund Frequency</h5>
                        </div>
                    </div>
                    <div class="card-body p-0 p-0">
                        <div class="table-responsive custom-table">
                            @if (!empty($dashboardRefund) && $dashboardRefund->count())
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th>Business Name</th>
                                            <th class="text-right">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dashboardRefund as $key => $value)
                                            <tr>
                                                <td>{{ $value->business_name }}</td>
                                                <td class="text-right">{{ $value->totalCount }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="p30 mb-0">No Records</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if (auth()->guard('admin')->user()->can(['overview-merchant-suspicious-frequency']))
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h5 class="card-title">Merchant Suspicious Frequency</h5>
                        </div>
                    </div>
                    <div class="card-body p-0 p-0">
                        <div class="table-responsive custom-table">
                            @if (!empty($dashboardFlagged) && $dashboardFlagged->count())
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th>Business Name</th>
                                            <th class="text-right">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dashboardFlagged as $key => $value)
                                            <tr>
                                                <td>{{ $value->business_name }}</td>
                                                <td class="text-right">{{ $value->totalCount }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="p30 mb-0">No Records</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('customScript')
    <script type="text/javascript">
        var transactionSummaryURL = "{{ route('dashboard.transactionSummary') }}";
        var merchantTxnPercentUrl = "{{ route('dashboard.merchantTxnPercentage') }}"
        var agentMerchantOverviewUrl = "{{ route('dashboard.rp.merchant.overview') }}"
    </script>
    <script src="{{ storage_asset('themeAdmin/custom_js/admin/dashboard.js') }}"></script>
@endsection
