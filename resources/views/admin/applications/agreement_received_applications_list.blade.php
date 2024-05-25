@extends('layouts.admin.default')
@section('title')
    Application-Received Agreements
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Application-Received Agreements
@endsection
@section('content')
    @include('requestDate')
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
                        <div class="basic-form">
                            <div class="row ">
                                <div class="form-group col-lg-6">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" placeholder="Enter here" name="name"
                                        value="{{ isset($_GET['name']) && $_GET['name'] != '' ? $_GET['name'] : '' }}">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" placeholder="Enter here" name="email"
                                        value="{{ isset($_GET['email']) && $_GET['email'] != '' ? $_GET['email'] : '' }}">
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="country">Country</label>
                                    <select name="country" id="country" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        @foreach (getCountry() as $key => $country)
                                            <option value="{{ $key }}"
                                                {{ isset($_GET['country']) && $_GET['country'] == $key ? 'selected' : '' }}>
                                                {{ $country }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('country'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('country') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="business_name">Business Name</label>
                                    <select name="user_id" id="business_name" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        @foreach ($businessNames as $key => $value)
                                            <option value="{{ $value->user_id }}"
                                                {{ isset($_GET['user_id']) && $_GET['user_id'] == $value->user_id ? 'selected' : '' }}>
                                                {{ $value->business_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('user_id'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('user_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="text">Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="start_date"
                                            placeholder="Enter here" id="start_date"
                                            value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                    @if ($errors->has('start_date'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('start_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="text">End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="end_date" placeholder="Enter here"
                                            id="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                    @if ($errors->has('end_date'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('end_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="agent_name">Referral Partners</label>
                                    <select name="agent_id" id="agent_name" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        <option value="no-agent"
                                            {{ isset($_GET['agent_id']) && $_GET['agent_id'] == 'no-agent' ? 'selected' : '' }}>
                                            No Referral Partners </option>
                                        @foreach ($agentName as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($_GET['agent_id']) && $_GET['agent_id'] == $value->id ? 'selected' : '' }}>
                                                {{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('agent_id'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('agent_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="technology_partner_id">Technology Partner</label>
                                    <select name="technology_partner_id" id="technology_partner" data-size="7"
                                        data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        @foreach ($technologyPartner as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($_GET['technology_partner_id']) && $_GET['technology_partner_id'] == $value->id ? 'selected' : '' }}>
                                                {{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('technology_partner_id'))
                                        <span class="help-block">
                                            <strong
                                                class="text-danger">{{ $errors->first('technology_partner_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="website_url">Website URL</label>
                                    <input type="text" class="form-control" placeholder="Enter here"
                                        name="website_url"
                                        value="{{ isset($_GET['website_url']) && $_GET['website_url'] != '' ? $_GET['website_url'] : '' }}">
                                    @if ($errors->has('website_url'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('website_url') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="categories">Categories</label>
                                    <select name="category_id" id="categories" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        <option value="no-agent"
                                            {{ isset($_GET['category_id']) && $_GET['category_id'] == 'no-agent' ? 'selected' : '' }}>
                                            No Referral Partners </option>
                                        @foreach ($categories as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($_GET['category_id']) && $_GET['category_id'] == $value->id ? 'selected' : '' }}>
                                                {{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('category_id'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('category_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="monthly_volume">Monthly Volume</label>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <select name="monthly_volume_condition" id="monthly_volume_condition"
                                                data-size="7" data-live-search="true"
                                                class="select2 btn-primary fill_selectbtn_in own_selectbox"
                                                data-width="100%" style="position: absolute; bottom: 0; right: 0;">
                                                <option value="e"
                                                    {{ isset($_GET['monthly_volume_condition']) && $_GET['monthly_volume_condition'] == 'e' ? 'selected' : '' }}>
                                                    =</option>
                                                <option value="l"
                                                    {{ isset($_GET['monthly_volume_condition']) && $_GET['monthly_volume_condition'] == 'l' ? 'selected' : '' }}>
                                                    < </option>
                                                <option value="le"
                                                    {{ isset($_GET['monthly_volume_condition']) && $_GET['monthly_volume_condition'] == 'le' ? 'selected' : '' }}>
                                                    <= </option>
                                                <option value="g"
                                                    {{ isset($_GET['monthly_volume_condition']) && $_GET['monthly_volume_condition'] == 'g' ? 'selected' : '' }}>
                                                    ></option>
                                                <option value="ge"
                                                    {{ isset($_GET['monthly_volume_condition']) && $_GET['monthly_volume_condition'] == 'ge' ? 'selected' : '' }}>
                                                    >=</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-8">
                                            <input type="number" class="form-control" placeholder="Enter Monthly Volume"
                                                name="monthly_volume"
                                                value="{{ isset($_GET['monthly_volume']) && $_GET['monthly_volume'] != '' ? $_GET['monthly_volume'] : '' }}">
                                        </div>
                                    </div>
                                    @if ($errors->has('monthly_volume'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('monthly_volume') }}</strong>
                                        </span>
                                    @endif
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
            <h4>Application-Received Agreements</h4>
        </div>
        <div class="col-xl-6 text-right">
            @if (auth()->guard('admin')->user()->can(['export-application']))
                <a href="{{ route('admin.applications.exportAllAgreementReceived', request()->all()) }}"
                    data-filename="Agreement_Received_Application" class="btn btn-primary btn-sm" id="ExcelLink"><i
                        class="fa fa-download"></i> Export Excel </a>
            @endif
            @if (auth()->guard('admin')->user()->can(['delete-application']))
                <button type="button" class="btn btn-danger btn-sm btn-shadow" id="deleteSelected"
                    data-link="{{ route('delete-all-application') }}"><i class="fa fa-trash"></i> Delete Selected
                    Record</button>
            @endif

        </div>
        <div class="col-xl-12 col-xxl-12">
            <div class="card mt-1">
                <div class="card-header">
                    <div>
                    </div>
                    <div>
                        <form id="noListform" method="GET" style="float: left;" class="form-dark me-50">
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
                            <button type="button" class="btn btn-primary bell-link btn-sm" data-bs-toggle="modal"
                                data-bs-target="#searchModal"> Advanced Search &nbsp;
                                <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg></button>
                            <a href="{{ route('admin.applications.agreement_received') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                        @if (auth()->guard('admin')->user()->can(['send-email-application']))
                            <a href="" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#Send_email"><i class="fa fa-envelope"></i> Send Mail </a>
                        @endif

                        @if (auth()->guard('admin')->user()->can(['not-interested-application']))
                            <a href="javascript:;" class="btn btn-danger btn-sm" id="NotInterested"
                                data-link="{{ route('applications-move-in-not-interested') }}">Not Interested</a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table id="applications_list" class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="custom-control custom-checkbox form-check mr-0">
                                            <input type="checkbox" id="selectallcheckbox" name=""
                                                class="multidelete form-check-input">
                                            <label class="form-check-label" for="selectallcheckbox"></label>
                                        </div>
                                    </th>
                                    <th style="min-width: 140px;">Business Name</th>
                                    <th>Email</th>
                                    <th style="min-width: 150px;">Merchant Name</th>
                                    <th style="min-width: 130px;">Business Type</th>
                                    <th>Website URL</th>
                                    <th style="min-width: 130px;">Creation Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($applications) && $applications->count())
                                    @foreach ($applications as $key => $value)
                                        @php $key++; @endphp
                                        <tr>
                                            <td>
                                                <div class="custom-control custom-checkbox form-check mr-0">
                                                    <input type="checkbox" id="checkbox-{{ $value->id }}"
                                                        name="multidelete[]" class="multidelete form-check-input"
                                                        value="{{ $value->id }}">
                                                    <label class="form-check-label"
                                                        for="checkbox-{{ $value->id }}"></label>
                                                </div>
                                            </td>
                                            <td>{{ strlen($value->business_name) > 50 ? substr($value->business_name, 0, 30) . '...' : $value->business_name }}

                                                @if (!empty($value->user->agent))
                                                    <br />
                                                    <label
                                                        class="badge badge-primary badge-sm">{{ $value->user->agent->name }}
                                                        (Visa-{{ $value->user->agent_commission }}% &
                                                        Master-{{ $value->user->agent_commission_master_card }}%)
                                                    </label>
                                                @endif
                                            </td>
                                            <td>{{ $value->user->email ?? 'No Email' }}</td>
                                            <td>{{ $value->user->name ?? 'No Name' }}</td>
                                            <td>{{ $value->business_type }}</td>
                                            <td>
                                                {{ strlen($value->website_url) > 50 ? substr($value->website_url, 0, 30) . '...' : $value->website_url }}
                                            </td>
                                            <td>{{ convertDateToLocal($value->created_at, 'd-m-Y') }}</td>
                                            <td>
                                                <div class="dropdown ml-auto">
                                                    <button type="button"
                                                        class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                        data-bs-toggle="dropdown">
                                                        <svg width="5" height="17" viewBox="0 0 5 17"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M2.36328 4.69507C1.25871 4.69507 0.363281 3.79964 0.363281 2.69507C0.363281 1.5905 1.25871 0.695068 2.36328 0.695068C3.46785 0.695068 4.36328 1.5905 4.36328 2.69507C4.36328 3.79964 3.46785 4.69507 2.36328 4.69507Z"
                                                                fill="#B3ADAD" />
                                                            <path
                                                                d="M2.36328 10.6951C1.25871 10.6951 0.363281 9.79964 0.363281 8.69507C0.363281 7.5905 1.25871 6.69507 2.36328 6.69507C3.46785 6.69507 4.36328 7.5905 4.36328 8.69507C4.36328 9.79964 3.46785 10.6951 2.36328 10.6951Z"
                                                                fill="#B3ADAD" />
                                                            <path
                                                                d="M2.36328 16.6951C1.25871 16.6951 0.363281 15.7996 0.363281 14.6951C0.363281 13.5905 1.25871 12.6951 2.36328 12.6951C3.46785 12.6951 4.36328 13.5905 4.36328 14.6951C4.36328 15.7996 3.46785 16.6951 2.36328 16.6951Z"
                                                                fill="#B3ADAD" />
                                                        </svg>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        @if (auth()->guard('admin')->user()->can(['pdf-download-application']))
                                                            <a href="{{ \URL::route('application-pdf', $value->id) }}"
                                                                class="dropdown-item"><i
                                                                    class="fa fa-download text-primary me-2"></i>
                                                                PDF Download
                                                            </a>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['doc-download-application']))
                                                            <a href="{{ \URL::route('application-docs', $value->id) }}"
                                                                class="dropdown-item"><i
                                                                    class="fa fa-download text-info me-2"></i>
                                                                Document Download
                                                            </a>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['view-application']))
                                                            <a href="{{ route('application.view') }}/{{ $value->id }}"
                                                                class="dropdown-item"><i
                                                                    class="fa fa-eye text-secondary me-2"></i>
                                                                View
                                                            </a>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['update-application']))
                                                            <a href="{{ route('application.edit') }}/{{ $value->id }}"
                                                                class="dropdown-item"><i
                                                                    class="fa fa-edit text-success me-2"></i>
                                                                Edit
                                                            </a>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['delete-application']))
                                                            <a class="dropdown-item delete_modal"
                                                                data-url="{{ \URL::route('admin.applications.delete', $value->id) }}"
                                                                data-id="{{ $value->id }}"><i
                                                                    class="fa fa-trash text-danger me-2"></i>Delete</a>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['send-to-bank-application']))
                                                            <a class="dropdown-item assign-bank"
                                                                data-bs-target="#assignBankModal" data-bs-toggle="modal"
                                                                data-id="{{ $value->id }}" title="Send To Bank"
                                                                style="cursor: pointer;">
                                                                <i class="fa fa-bank text-success me-2"
                                                                    style="font-size: 12px;"></i> Send To Bank
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        @php $colSpan = 11; @endphp
                                        <td colspan="{{ $colSpan }}">
                                            <p class="text-center"><strong>No applications found.</strong></p>
                                        </td>
                                        @for ($i = 1; $i < $colSpan; $i++)
                                            <td style="display: none">
                                            </td>
                                        @endfor
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer">
                    @if (!empty($applications) && $applications->count())
                        <div class="row">
                            <div class="col-md-8">
                                {!! $applications->appends($_GET)->links() !!}
                            </div>
                            <div class="col-md-4 text-right">
                                Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }} of total
                                {{ $applications->total() }}
                                entries
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('partials.bank.assign-bank-modal')

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
                        <button type="button" id="submitSendMailApp" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                            id="closeReassignForm">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });
    </script>
    <script src="{{ storage_asset('themeAdmin/custom_js/admin/applications.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>

    <script src="https://cdn.ckeditor.com/4.16.2/full/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            CKEDITOR.replace("bodycontent", {
                height: "200px"
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

            $('body').on('click', '#submitSendMailApp', function() {
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
                        url: "{{ route('send-application-multi-mail') }}",
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
                    toastr.warning('Please select atleast one application!');
                }

            });

            // Bank Listing In Model
            $('body').on('click', '.assign-bank', function() {
                var id = $(this).data('id');
                $('#assignBankForm').find('input[name="application_id"][type="hidden"]').val(id);

                $.ajax({
                    url: '{{ route('get-application-bank-admin') }}',
                    type: 'get',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'id': id
                    },
                    beforeSend: function() {
                        $('#bank-list').html(
                            '<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                    },
                    success: function(data) {
                        $('#bank-list').html(data.html);
                    },
                });
            });

            $('body').on('click', '#submitAssignBankForm', function(e) {
                e.preventDefault();
                var assignBankForm = $("#assignBankForm");
                var formData = assignBankForm.serialize();
                $('#sent_to_bank_error').html("");
                $.ajax({
                    url: '{{ route('application-send-to-bank-admin') }}',
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('#submitSendToBankForm').attr('disabled', true);
                        $('#submitAssignBankForm').html(
                            '<span id="wait-spin"><i class="fa fa-spinner fa-spin"></i>  Please Wait...</span>'
                        );
                    },
                    success: function(data) {
                        $('.closeAssignBankForm')[0].click();
                        console.log(data);
                        if (data.errors) {
                            if (data.errors.bank) {
                                $('#sent_to_bank_error').html(data.errors.bank[0]);
                            }
                        }
                        if (data.success == '1') {
                            toastr.success("Application Send To Selected Bank Successfully", {
                                timeOut: "50000"
                            });
                            $('#assignBankForm').find("input[id='applicationID']").remove();
                            // $('.modal-footer').find('#wait-spin').remove();
                            $('#submitAssignBankForm').attr('disabled', false);
                            $('#submitAssignBankForm').html('Send');
                        } else if (data.success == '0') {
                            toastr.error("Something went wrong, please try again !!", {
                                timeOut: "50000"
                            });
                            $('#assignBankForm').find("input[id='applicationID']").remove();
                            // $('.modal-footer').find('#wait-spin').remove();
                            $('#submitAssignBankForm').attr('disabled', false);
                            $('#submitAssignBankForm').html('Send');
                        }
                    },
                });
            });
        });
    </script>
@endsection
