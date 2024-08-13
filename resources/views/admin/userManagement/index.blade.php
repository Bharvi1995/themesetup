@extends('layouts.admin.default')

@section('title')
    Merchant
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Merchant Management</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Merchant Management</h6>
    </nav>
@endsection

@section('customeStyle')
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/custom_css/sweetalert2.min.css') }}" />
@endsection

@section('content')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog modal-lg-scrollable" role="document">
            <form method="" id="search-form" class="form-dark">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        @if (isset($_GET) && $_GET != '')
                            @foreach ($_GET as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        @endif
                        <div class="basic-form">
                            <div class="row ">
                                <div class="form-group col-md-6">
                                    <label for="">Email Verification Status</label>
                                    <select class="form-select" name="verify_status" id="verify_status" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        <option value="1"
                                            {{ isset($_GET['verify_status']) && $_GET['verify_status'] == '1' ? 'selected' : '' }}>
                                            Verified</option>
                                        <option value="0"
                                            {{ isset($_GET['verify_status']) && $_GET['verify_status'] == '0' ? 'selected' : '' }}>
                                            Unverified</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">Application Status</label>
                                    <select class="form-select" name="application_status" data-size="7" data-live-search="true"
                                        data-title="-- Select here --" id="application_status" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        <option value="0"
                                            {{ isset($_GET['application_status']) && $_GET['application_status'] == '0' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                        <option value="1"
                                            {{ isset($_GET['application_status']) && $_GET['application_status'] == '1' ? 'selected' : '' }}>
                                            In Progress
                                        </option>
                                        <option value="2"
                                            {{ isset($_GET['application_status']) && $_GET['application_status'] == '2' ? 'selected' : '' }}>
                                            Reassign
                                        </option>
                                        <option value="4"
                                            {{ isset($_GET['application_status']) && $_GET['application_status'] == '4' ? 'selected' : '' }}>
                                            Pre Approval
                                        </option>
                                        <option value="10"
                                            {{ isset($_GET['application_status']) && $_GET['application_status'] == '10' ? 'selected' : '' }}>
                                            Rate Accepted
                                        </option>
                                        <option value="9"
                                            {{ isset($_GET['application_status']) && $_GET['application_status'] == '9' ? 'selected' : '' }}>
                                            Rate Decline
                                        </option>
                                        <option value="5"
                                            {{ isset($_GET['application_status']) && $_GET['application_status'] == '5' ? 'selected' : '' }}>
                                            Agreement
                                            Sent</option>
                                        <option value="11"
                                            {{ isset($_GET['application_status']) && $_GET['application_status'] == '11' ? 'selected' : '' }}>
                                            Signed Agreement</option>
                                        <option value="6"
                                            {{ isset($_GET['application_status']) && $_GET['application_status'] == '6' ? 'selected' : '' }}>
                                            Agreement
                                            Received</option>
                                        <option value="3"
                                            {{ isset($_GET['application_status']) && $_GET['application_status'] == '3' ? 'selected' : '' }}>
                                            Rejected
                                        </option>
                                        <option value="7"
                                            {{ isset($_GET['application_status']) && $_GET['application_status'] == '7' ? 'selected' : '' }}>
                                            Not
                                            Interested</option>
                                        <option value="8"
                                            {{ isset($_GET['application_status']) && $_GET['application_status'] == '8' ? 'selected' : '' }}>
                                            Terminated</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Email</label>
                                    <input class="form-control" name="email" type="email" placeholder="Enter here"
                                        value="{{ isset($_GET['email']) && $_GET['email'] != '' ? $_GET['email'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Country</label>
                                    <select class="form-select" name="country" data-size="7" data-live-search="true"
                                        data-title="Select here" id="country" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        @foreach ($countries as $key => $country)
                                            <option value="{{ $key }}"
                                                {{ isset($_GET['country']) && $_GET['country'] == $key ? 'selected' : '' }}>
                                                {{ $country }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Company</label>
                                    <select class="form-select" name="company" data-size="7" data-live-search="true"
                                        data-title="Select here" id="company" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        @foreach ($companyName as $company)
                                            <option value="{{ $company->business_name }}"
                                                {{ isset($_GET['company']) && $_GET['company'] == $company->business_name ? 'selected' : '' }}>
                                                {{ $company->business_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Main MID</label>
                                    <select class="form-select" name="payment_gateway_id" data-size="7" data-live-search="true"
                                        data-title="Select Main MID" id="payment_gateway_id" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        @foreach ($payment_gateway_id as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($_GET['payment_gateway_id']) && $_GET['payment_gateway_id'] == $value->id ? 'selected' : '' }}>
                                                {{ $value->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Visa MID</label>
                                    <select class="form-select" name="visamid" data-size="7" data-live-search="true"
                                        data-title="Select Main MID" id="visamid" data-width="100%">
                                        <option value="">Select here</option>
                                        @foreach ($payment_gateway_id as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($_GET['visamid']) && $_GET['visamid'] == $value->id ? 'selected' : '' }}>
                                                {{ $value->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Master MID</label>
                                    <select class="form-select" name="mastercardmid" data-size="7" data-live-search="true"
                                        data-title="Select Main MID" id="mastercardmid" data-width="100%">
                                        <option value="">Select here</option>
                                        @foreach ($payment_gateway_id as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($_GET['mastercardmid']) && $_GET['mastercardmid'] == $value->id ? 'selected' : '' }}>
                                                {{ $value->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Bank MID</label>
                                    <select class="form-select" name="bank_mid" data-size="7" data-live-search="true"
                                        data-title="Select Main MID" id="bank_mid" data-width="100%">
                                        <option value="">Select here</option>
                                        @foreach ($bankMIDData as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($_GET['bank_mid']) && $_GET['bank_mid'] == $value->id ? 'selected' : '' }}>
                                                {{ $value->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Crypto MID</label>
                                    <select class="form-select" name="crypto_mid" data-size="7" data-live-search="true"
                                        data-title="Select Main MID" id="crypto_mid" data-width="100%">
                                        <option value="">Select here</option>
                                        @foreach ($cryptoMIDData as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($_GET['crypto_mid']) && $_GET['crypto_mid'] == $value->id ? 'selected' : '' }}>
                                                {{ $value->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Referral Partners</label>
                                    <select class="form-select" name="agent_id" data-size="7" data-live-search="true"
                                        data-title="Select Referral Partners" id="agent_id" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}"
                                                {{ isset($_GET['agent_id']) && $_GET['agent_id'] == $agent->id ? 'selected' : '' }}>
                                                {{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Category</label>
                                    <select class="form-select" name="category" data-size="7" data-live-search="true"
                                        data-title="Select Category" id="category" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ isset($_GET['category']) && $_GET['category'] == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Website</label>
                                    <input class="form-control" name="website" type="text" placeholder="Enter here"
                                        value="{{ isset($_GET['website']) && $_GET['website'] != '' ? $_GET['website'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Global Rule</label>
                                    <select class="form-control" name="global_rule">
                                        <option value="">-- Select --</option>
                                        <option value="0"
                                            {{ isset($_GET['global_rule']) && $_GET['global_rule'] == '0' ? 'selected' : '' }}>
                                            Enabled</option>
                                        <option value="1"
                                            {{ isset($_GET['global_rule']) && $_GET['global_rule'] == '1' ? 'selected' : '' }}>
                                            Disabled</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Mode</label>
                                    <select class="form-select" name="mode" data-size="7" data-live-search="true"
                                        data-title="Select Mode" id="mode" data-width="100%">
                                        <option selected disabled>Select here</option>

                                        <option value="test"
                                            {{ isset($_GET['mode']) && $_GET['mode'] == 'test' ? 'selected' : '' }}>
                                            Test
                                        </option>
                                        <option value="live"
                                            {{ isset($_GET['mode']) && $_GET['mode'] == 'live' ? 'selected' : '' }}>
                                            Live
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">API KEY</label>
                                    <input class="form-control" name="api_key" type="text" placeholder="Enter here"
                                        value="{{ isset($_GET['api_key']) && $_GET['api_key'] != '' ? $_GET['api_key'] : '' }}">
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
        <div class="col-lg-6">
            <h4 class="me-50">Merchant Management</h4>
        </div>
        <div class="col-lg-6 text-right">
            <?php
            $url = Request::fullUrl();
            $parsedUrl = parse_url($url);
            $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
            $subQueryString = $query != '' ? $query . '&type=xlsx' : '';
            ?>
            @if (auth()->guard('admin')->user()->can(['export-merchant']))
                <a href="{{ route('user-management-csv-export', [$subQueryString]) }}" class="btn btn-outline-primary btn-sm" id="ExcelLink"> Export Excel</a>
            @endif
            @if (auth()->guard('admin')->user()->can(['delete-merchant']))
                <button type="button" class="btn btn-outline-primary btn-sm me-2" id="bulk_delete">
                    Delete Selected User</button>
            @endif
            @if (auth()->guard('admin')->user()->can(['send-mail-to-merchant']))
                <a href="" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal"
                    data-bs-target="#Send_email">
                    Send
                    Mail </a>
            @endif
        </div>
        <div class="col-lg-12">
            <div class="card  mt-1">
                <div class="card-header d-flex justify-content-between">
                    <div>
                    </div>
                    <div class="card-header-toolbar align-items-center">
                        <div class="btn-group mr-2">
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
                            
                            <button type="button" class="btn btn-primary bell-link btn-sm" data-bs-toggle="modal"
                                data-bs-target="#searchModal">
                                Advanced
                                Search &nbsp; <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg></button>
                            <a href="{{ url('paylaksa/users-management') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                        @if (auth()->guard('admin')->user()->can(['create-merchant']))
                            <a href="{{ route('merchant-user-create') }}" class="btn btn-primary btn-sm"
                                id="new_merchant"> Create Account </a>
                        @endif
                        @if (auth()->guard('admin')->user()->can(['sub-users-list']))
                            <a href="{{ route('sub-users-management') }}" class="btn btn-danger btn-sm">
                                Sub User
                            </a>
                        @endif

                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        <div class="custom-control form-check custom-control-inline mr-0">
                                            <input class="form-check-input" id="selectallcheckbox" name=""
                                                type="checkbox">
                                            <label class="form-check-label" for="selectallcheckbox"></label>
                                        </div>
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Business Name</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Merchant Name</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mobile</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Creation Date</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (count($dataT) > 0)
                                    @foreach ($dataT as $key => $data)
                                        <tr>
                                            <td class="align-middle text-center text-sm">
                                                <div class="custom-control form-check custom-control-inline mr-0">
                                                    <input type="checkbox"
                                                        class="form-check-input multicheckmail multidelete"
                                                        name="multicheckmail[]" id="{{ $data->id }}_checkbox"
                                                        value="{{ $data->id }}">
                                                    <label class="form-check-label"
                                                        for="{{ $data->id }}_checkbox"></label>
                                                </div>
                                            </td>

                                            <td class="align-middle text-center text-sm">
                                                @if ($data->email_verified_at != null)
                                                    <span class="badge badge-sm bg-gradient-success badge-sm">Email Verification -
                                                        Verified</span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-danger badge-sm">Email Verification -
                                                        Unverified</span>
                                                @endif
                                                <br>
                                                @if ($data->appStatus == '1')
                                                    <span class="badge badge-sm bg-gradient-primary badge-sm">Application - In
                                                        Progress</span>
                                                @elseif($data->appStatus == '2')
                                                    <span class="badge badge-sm bg-gradient-primary badge-sm">Application -
                                                        Incomplete</span>
                                                @elseif($data->appStatus == '3')
                                                    <span class="badge badge-sm bg-gradient-danger badge-sm">Application - Rejected</span>
                                                @elseif($data->appStatus == '4')
                                                    <span class="badge badge-sm bg-gradient-success badge-sm">Application - Pre
                                                        Approval</span>
                                                @elseif($data->appStatus == '5')
                                                    <span class="badge badge-sm bg-gradient-primary badge-sm">Application - Agreement
                                                        Sent</span>
                                                @elseif($data->appStatus == '6')
                                                    <span class="badge badge-sm bg-gradient-primary badge-sm">Application - Agreement
                                                        Received</span>
                                                @elseif($data->appStatus == '7')
                                                    <span class="badge badge-sm bg-gradient-danger badge-sm">Application - Not
                                                        Interested</span>
                                                @elseif($data->appStatus == '8')
                                                    <span class="badge badge-sm bg-gradient-danger badge-sm">Application -
                                                        Terminated</span>
                                                @elseif($data->appStatus == '9')
                                                    <span class="badge badge-sm bg-gradient-danger badge-sm">Application - Decline</span>
                                                @elseif($data->appStatus == '10')
                                                    <span class="badge badge-sm bg-gradient-success badge-sm">Application - Rate
                                                        Accepted</span>
                                                @elseif($data->appStatus == '11')
                                                    <span class="badge badge-sm bg-gradient-success badge-sm">Application - Signed
                                                        Agreement</span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-danger badge-sm">Application - Pending</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                {{ $data->business_name }}
                                                @if (!empty($data->agent))
                                                    <br />
                                                    <span class="badge badge-sm bg-gradient-primary badge-sm">{{ $data->agent }} </span>
                                                    {{-- <span class="badge badge-sm bg-gradient-primary badge-sm">Visa-{{ $data->agent_commission }}% & Master-{{
                                        }
                    $data->agent_commission_master_card }}%</span> --}}
                                                @endif
                                            </td>
                                            <td class="align-middle text-center text-sm">{{ $data->name }}<br><label
                                                    class="badge badge-sm bg-gradient-primary badge-sm">{{ $data->bank_name }}</label>
                                            </td>
                                            <td class="align-middle text-center text-sm">{{ $data->email }}</td>
                                            <td class="align-middle text-center text-sm">
                                                +{{ $data->country_code }} {{ $data->mobile_no }}
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                {{ convertDateToLocal($data->created_at, 'd-m-Y') }}
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <div class="dropdown">
                                                    <a href="javascript:;" class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownMenuLink2">
                                                    </a>
                                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink2">
                                                        @if (auth()->guard('admin')->user()->can(['update-merchant']))
                                                            
                                                            <li><a href="{{ \URL::route('merchant-user-edit', $data->id) }}"
                                                                class="dropdown-item">
                                                                Edit</a></li>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['sub-users-list']))
                                                            <li><a href="{{ \URL::route('sub-user', $data->id) }}"
                                                                class="dropdown-item">
                                                                Sub User</a></li>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['update-merchant']))
                                                            <li><a href="{{ URL::to('/') }}/userLogin?email={{ encrypt($data->email) }}"
                                                                target="_blank" class="dropdown-item">
                                                                Login</a></li>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['view-merchant']))
                                                            <li><a href="" class="user-show dropdown-item"
                                                                data-bs-toggle="modal" data-id="{{ $data->id }}"
                                                                data-bs-target="#user_list">
                                                                View</a></li>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['assign-reffrel-partner']))
                                                            <li><a href="javascript:void(0)"
                                                                class="dropdown-item btn-editAgentModal"
                                                                data-user="{{ $data->id }}"
                                                                data-agent="{{ $data->agent_id }}"
                                                                data-agent-commission="{{ $data->agent_commission }}"
                                                                data-agent-commission-master="{{ $data->agent_commission_master_card }}"
                                                                data-bs-toggle="modal" data-bs-target="#editAgentModal">
                                                                Referral Partners</a></li>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['merchant-assign-mid']))
                                                            @if (
                                                                $data->appStatus == 4 ||
                                                                    $data->appStatus == 5 ||
                                                                    $data->appStatus == 6 ||
                                                                    $data->appStatus == 10 ||
                                                                    $data->appStatus == 11)
                                                                <li><a href="{{ \URL::route('assign-mid', $data->id) }}"
                                                                    title="Assign MID" class="dropdown-item">
                                                                    Assign MID</a></li>
                                                            @endif
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['update-merchant']))
                                                            
                                                            <li><a href="{{ route('application.create',$data->id)}}" class="dropdown-item">Create Application</a></li>
                                                            
                                                        @endif
                                                    </div>
                                                </div>
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
                    @if (!empty($dataT) && $dataT->count())
                        <div class="row">
                            <div class="col-md-8">
                                {!! $dataT->appends($_GET)->links() !!}
                            </div>
                            <div class="col-md-4 text-right">
                                Showing {{ $dataT->firstItem() }} to {{ $dataT->lastItem() }} of total
                                {{ $dataT->total() }}
                                entries
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Old Code --}}

    <div class="modal right fade" id="Send_email" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Send Mail</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <form id="SendMailForm" method="POST" enctype="multipart/form-data" class="form-dark">
                    <div class="modal-body" id="SendMailBody">
                        @csrf

                        <div class="form-group">
                            <label>Select Template</label>
                            {!! Form::select(
                                'email_template',
                                [
                                    '' => '-- Select Template --',
                                    'addCustom' => 'Add
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        Custom',
                                ] + $template,
                                [],
                                ['class' => 'form-control', 'id' => 'emailTemplate'],
                            ) !!}
                            <span class="help-block text-danger">
                                <strong id="er_email_template"></strong>
                            </span>
                        </div>

                        <div class="form-group form-group-none">
                            <input class="form-control" type="text" name="subject" id="subject" required=""
                                placeholder="Enter Subject">
                            <span class="help-block text-danger">
                                <strong id="er_subject"></strong>
                            </span>
                        </div>

                        <div class="form-group form-group-none">
                            <textarea class="form-control" name="bodycontent" id="bodycontent" required=""
                                placeholder="Enter Mail Text Here...." rows="6"></textarea>
                            <span class="help-block text-danger">
                                <strong id="er_bodycontent"></strong>
                            </span>
                        </div>
                        <div class="file-attached"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="submitSendMail" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                            id="closeReassignForm">Close</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="modal right  fade" id="editAgentModal" role="dialog" aria-labelledby="editAgentModal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog modal-lg-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Referral Partners List</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>

                <div class="modal-body">
                    <form class="form-dark">
                        <input type="hidden" class="agentUserId" value="" />
                        <input type="hidden" class="userAgentId" value="" />
                        <div class="row">
                            <div class="col-xl-12 col-sm-12 col-md-12 col-12 mb-4">
                                <label>Select Referral Partner</label>
                                <select class="form-select" name="agent" id="agentSelect2">
                                    <option selected disabled>Select here</option>
                                    @foreach ($agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->name . ' - ' . $agent->email }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="help-block text-danger">
                                    <span id="agent_error"></span>
                                </span>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer modal-footer-fixed">
                    <button type="button" class="btn btn-primary btn-sm" id="saveEditAgent">Save</button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal right fade" id="user_list" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User Details </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <div class="modal-body" id="userDetailsContent">

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>

    </div>


    <div class="modal right fade bg-modal-fade" id="Change_password" tabindex="-1" role="dialog"
        aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="" id="changePasswordForm" class="form-dark">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Change Password </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <div class="modal-body" id="">
                        <div class="col-xl-12 col-sm-12 col-md-12 col-12 mb-4">
                            <input class="form-control" type="password" name="password" id="password"
                                placeholder="Enter Password" required="">
                            <span class="help-block text-danger">
                                <strong id="er_password"></strong>
                            </span>
                        </div>
                        <div class="col-xl-12 col-sm-12 col-md-12 col-12 mb-4">
                            <input class="form-control" type="password" name="conform_password" id="conform_password"
                                placeholder="Enter Conform Password">
                            <span class="help-block text-danger">
                                <strong id="er_conform_password"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer-fixed">
                        <button type="button" class="btn btn-primary btn-sm" id="submitChangePass">Save</button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.ckeditor.com/4.16.2/full/ckeditor.js"></script>
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            CKEDITOR.replace("bodycontent", {
                height: "200px"
            });
            //select all checkbox for action
            $('body').on('change', '#selectallcheckbox', function() {
                if ($(this).prop("checked") == true) {
                    $('.multicheckmail').prop("checked", true);
                } else if ($(this).prop("checked") == false) {
                    $('.multicheckmail').prop("checked", false);
                }
            });

            //submit multiple mail
            $('body').on('change', '#emailTemplate', function() {
                $('#SendMailForm .form-group').removeClass('form-group-none');
                var temp = $(this).val();
                if (temp != 'addCustom') {
                    $.ajax({
                        url: "{{ route('get-template-data') }}?id=" + temp,
                        method: "GET",
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $(this).attr('disabled', 'disabled');
                            $(this).html(
                                '<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                        },
                        success: function(data) {
                            // console.log(data);
                            $('#subject').val(data.email_subject);
                            CKEDITOR.instances['bodycontent'].setData(data.email_body);
                            if (data.files != null) {
                                var resultArray = JSON.parse(data.files);
                                $('.file-attached').html(
                                    '<p class="text-danger"><strong>File attached :</strong> ' +
                                    resultArray.length + ' files attached</p>');
                            } else {
                                $('.file-attached').html('');
                            }
                        }
                    });
                } else {
                    $('#subject').val("");
                    $('.file-attached').html('');
                    CKEDITOR.instances['bodycontent'].setData("");
                }
            });

            $('body').on('click', '#submitSendMail', function() {
                var id = [];
                $('.multidelete:checked').each(function() {
                    id.push($(this).val());
                });
                var formData = new FormData($('#SendMailForm')[0]);
                formData.append('id', id);
                var desc = CKEDITOR.instances['bodycontent'].getData();
                formData.append('bodycontent', desc);

                if (id.length > 0) {
                    $.ajax({
                        url: "{{ route('send-user-multi-mail') }}",
                        method: "POST",
                        context: $(this),
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $(this).attr('disabled', 'disabled');
                            $(this).html(
                                '<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                        },
                        success: function(data) {
                            console.log(data);
                            if (data.errors) {
                                if (data.errors.email_template) {
                                    $('#er_email_template').html(data.errors.email_template[0]);
                                }
                                if (data.errors.subject) {
                                    $('#er_subject').html(data.errors.subject[0]);
                                }
                                if (data.errors.bodycontent) {
                                    $('#er_bodycontent').html(data.errors.bodycontent[0]);
                                }
                            }

                            if (data.success) {
                                $('.modal.fade').removeClass('in');
                                $('.modal-backdrop.fade').removeClass('in');
                                toastr.success('Mail Send Successfully!');
                                $(this).attr('disabled', false);
                                $(this).html('Submit');
                                window.setTimeout(
                                    function() {
                                        location.reload(true)
                                    },
                                    2000
                                );
                            }
                            $(this).attr('disabled', false);
                            $(this).html('Submit');
                        }
                    });
                } else {
                    toastr.warning('Please select atleast one user!');
                }

            });
            // Delete multiple row with datatable
            $(document).on('click', '#bulk_delete', function() {
                var id = [];
                $('.multicheckmail:checked').each(function() {
                    id.push($(this).val());
                });
                if (id.length > 0) {
                    swal({
                            title: "Are you sure?",
                            text: "Once deleted, you will not be able to recover this record!",
                            icon: "warning",
                            buttons: true,
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                $.ajax({
                                    url: "{{ route('merchant-user-masstransactions') }}",
                                    method: "get",
                                    data: {
                                        id: id
                                    },
                                    success: function(data) {
                                        toastr.success(
                                            'Selected User Delete Successfully!!');
                                        window.setTimeout(
                                            function() {
                                                location.reload(true)
                                            },
                                            2000
                                        );
                                    }
                                });
                            }
                        })
                } else {
                    toastr.error('Please select atleast one user !!');
                }
            });
            //agent remove from merchant
            $('body').on('click', '.agent-remove-from-merchant', function() {
                var url = $(this).attr("data-url");
                swal({
                        title: "Are you sure?",
                        text: "Once deleted, you will not be able to recover this record!",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: url,
                                method: "get",
                                success: function(data) {
                                    if (data == 1) {
                                        toastr.success('Agent Remove Successfully!!');
                                        window.setTimeout(
                                            function() {
                                                location.reload(true)
                                            }, 2000);
                                    } else {
                                        toastr.error('Something went wrong !!');
                                        window.setTimeout(
                                            function() {
                                                location.reload(true)
                                            }, 2000);
                                    }

                                }
                            });
                        }
                    });
            });
            // Get total amount
            $('body').on('click', '.showTransactionAmount', function() {
                var id = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: "{{ URL::route('get-user-total-amount') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': id
                    },
                    beforeSend: function() {
                        $('#transDetails').html(
                            '<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.count === 0) {
                            $('#transDetails').html(
                                '<h2 class="text-center text-danger"> No record found. </h2>'
                            );
                        } else {
                            if (data.success == true)
                                $('#transDetails').html(data.html)
                            else
                                toastr.error('Something went wrong !!');
                        }
                    },
                });
            });
            // show user details
            $('body').on('click', '.user-show', function() {
                var id = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    url: '{{ route('show-user-details') }}',
                    data: {
                        'id': id,
                        '_token': "{{ csrf_token() }}"
                    },
                    context: $(this),
                    beforeSend: function() {
                        $('#userDetailsContent').html(
                            '<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                    },
                    success: function(data) {
                        $('#userDetailsContent').html(data.html);
                    },
                });
            });
            // Change Password
            $('body').on('click', '.changePassBtn', function(e) {
                var ID = $(this).attr('data-id');
                $('#changePasswordForm').append('<input type="hidden" name="id" value="' + ID + '">');
            });
            $('body').on('click', '#submitChangePass', function(e) {
                e.preventDefault();
                $('#refund_error').html("");
                var formdata = $('#changePasswordForm').serialize();
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: "{{ URL::route('change-password') }}",
                    data: formdata,
                    beforeSend: function() {
                        $(this).attr('disabled', 'disabled');
                        $(this).html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                    },
                    success: function(data) {
                        if (data.errors) {
                            if (data.errors.conform_password) {
                                $('#er_conform_password').html(data.errors.conform_password[0]);
                            }
                            if (data.errors.password) {
                                $('#er_password').html(data.errors.password[0]);
                            }
                        }
                        if (data.success == true)
                            toastr.success('Password Change Successfully !!');
                        else
                            toastr.error('Something went wrong !!');
                        $(this).attr('disabled', false);
                        $(this).html('Submit');
                        if (data.success == true || data.success == false) {
                            setInterval(function() {
                                location.reload();
                            }, 2000);
                        }
                    },
                });
            });
            //add agent
            $('body').on('click', '.btn-editAgentModal', function(e) {
                e.preventDefault();
                var user_id = $(this).attr('data-user');
                var agent_id = $(this).attr('data-agent');
                var agent_commission = $(this).attr('data-agent-commission');
                var agent_commission_master = $(this).attr('data-agent-commission-master');
                // $('#editAgentModal .modal-body').find('.user-id').html(
                //     '<input type="hidden" id="user_id" value="' + user_id + '">');
                $(".agentUserId").val(user_id)
                // $('#editAgentModal .modal-body').find('.user-id').val(user_id);
                $('#editAgentModal .modal-body').find('#commission').val(agent_commission);
                $('#editAgentModal .modal-body').find('#commission_master').val(agent_commission_master);
                if (agent_id != '') {
                    $('#editAgentModal #selectAgent').val(agent_id).change();
                }
                $('#editAgentModal').modal('show');
            });

            $('body').on('click', '#saveEditAgent', function(e) {
                e.preventDefault();
                $('#agent_error').html("");
                $('#commission_error').html("");
                $('#commission_master_error').html("");
                var user_id = $(".agentUserId").val()
                var agent_id = $(".userAgentId").val()
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: "{{ URL::route('user-set-agent') }}",
                    data: {
                        _token: function() {
                            return "{{ csrf_token() }}";
                        },
                        user_id: user_id,
                        agent_id: agent_id,
                        // commission: function() {
                        //     return $('#editAgentModal #commission').val();
                        // },
                        // commission_master: function() {
                        //     return $('#editAgentModal #commission_master').val();
                        // }
                    },
                    beforeSend: function() {
                        $(this).attr('disabled', 'disabled');
                        $(this).html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                    },
                    success: function(data) {
                        if (data.errors) {
                            if (data.errors.agent_id) {
                                $('#agent_error').html(data.errors.agent_id[0]);
                            }
                            // if(data.errors.commission){
                            //     $('#commission_error').html( data.errors.commission[0] );
                            // }
                            // if(data.errors.commission_master){
                            //   $('#commission_master_error').html( data.errors.commission_master[0] );
                            // }
                            $(this).attr('disabled', false);
                            $(this).html('Submit');
                        } else {
                            if (data.success == true)
                                toastr.success('Referral Partner Saved Successfully !!');
                            else
                                toastr.error('Something went wrong !!');
                            $(this).attr('disabled', false);
                            $(this).html('Submit');
                            $('#editAgentModal #user_id').remove();
                            $('#editAgentModal').modal('hide');
                            setInterval(function() {
                                location.reload();
                            }, 2000);
                        }
                    },
                });
            });

            $('body').on('change', 'input[name="is_active"]', function() {
                var id = $(this).data('id');
                var is_active = '1';

                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var is_active = '0';
                }

                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('user-deactive') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'is_active': is_active,
                        'id': id
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Merchant activation changed successfully!!');
                        } else {
                            toastr.error('Something went wrong!!');
                        }
                    },
                });
            });

            $('body').on('change', 'input[name="is_otp_required"]', function() {
                var id = $(this).data('id');
                var is_otp = '0';

                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var is_otp = '1';
                }

                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('user-otp-required') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'is_otp': is_otp,
                        'id': id
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Merchant otp login changed successfully!!');
                        } else {
                            toastr.error('Something went wrong!!');
                        }
                    },
                });
            });

            $('body').on('change', 'input[name="isipremove"]', function() {
                var id = $(this).data('id');
                var is_ip_remove = '0';

                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var is_ip_remove = '1';
                }
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('user-ip-remove') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'is_ip_remove': is_ip_remove,
                        'id': id
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Merchant IP removed changed successfully!!');
                        } else {
                            toastr.error('Something went wrong!!');
                        }
                    },
                });
            })

            $('body').on('change', 'input[name="isdisablerule"]', function() {
                var id = $(this).data('id');
                var is_disable_rule = '0';

                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var is_disable_rule = '1';
                }
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('user-disable-rules') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'is_disable_rule': is_disable_rule,
                        'id': id
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Merchant disable rules changed successfully!!');
                        } else {
                            toastr.error('Something went wrong!!');
                        }
                    },
                });
            })

            $('body').on('change', 'input[name="isBinRemove"]', function() {
                var id = $(this).data('id');
                var is_bin_remove = '0';

                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var is_bin_remove = '1';
                }
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('user-bin-remove') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'is_bin_remove': is_bin_remove,
                        'id': id
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Merchant BIN remove changed successfully!!');
                        } else {
                            toastr.error('Something went wrong!!');
                        }
                    },
                });
            })

            // Add agent rp modal parent
            $("#agentSelect2").select2({
                dropdownParent: $("#editAgentModal")
            })

            $(document).on("change", "#agentSelect2", function() {

                $(".userAgentId").val($(this).val())
            })


        });
    </script>
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
@endsection
