@extends('layouts.admin.default')
@section('title')
    Deleted Applications
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Deleted Applications</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Deleted Applications</h6>
    </nav>
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
                                        class="form-select btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
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
                                        class="form-select btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
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
                                        class="form-select btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
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
                                        class="form-select btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
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
                                        class="form-select btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
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
                                                class="form-select btn-primary fill_selectbtn_in own_selectbox"
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
        <div class="col-xl-12 col-xxl-12">
            <div class="card mt-1">
                <div class="card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Deleted Applications</h4>
                    </div>
                    <div class="card-header-toolbar align-items-center">
                        <div class="btn-group mr-2">
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
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#searchModal"> Advanced Search &nbsp;
                                <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg>
                            </button>
                            <a href="{{ route('admin.applications.is_deleted') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                        @if (auth()->guard('admin')->user()->can(['export-application']))
                            <a href="{{ route('admin.applications.exportAllDeleted', request()->all()) }}"
                                data-filename="Deleted_Application" class="btn btn-outline-primary btn-sm" id="ExcelLink">Export Excel </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table id="is_completed_applications_list" class="table table-borderless">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" width="50px">
                                        <label class="custom-control form-check-label">
                                            <input type="checkbox" id="selectallcheckbox" name=""
                                                class="multidelete form-check-input">
                                            <span class="overflow-control-indicator"></span>
                                            <span class="overflow-control-description"></span>
                                        </label>
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Business Name</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Merchant Name</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Business Type</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Website URL</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Creation Date</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($applications) && $applications->count())
                                    @foreach ($applications as $key => $value)
                                        @php $key++; @endphp
                                        <tr>
                                            <td class="align-middle text-center text-sm">
                                                <label class="custom-control form-check-label">
                                                    <input type="checkbox" id="checkbox-{{ $value->id }}"
                                                        name="multidelete[]" class="multidelete form-check-input"
                                                        value="{{ $value->user_id }}">
                                                    <span class="overflow-control-indicator"></span>
                                                    <span class="overflow-control-description"></span>
                                                </label>
                                            </td>

                                            <td class="align-middle text-center text-sm">{{ strlen($value->business_name) > 50 ? substr($value->business_name, 0, 30) . '...' : $value->business_name }}
                                            </td>
                                            <td class="align-middle text-center text-sm">{{ $value->user->email ?? 'No Email' }}</td>
                                            <td class="align-middle text-center text-sm">{{ $value->user->name ?? 'No Name' }}</td>
                                            <td class="align-middle text-center text-sm">{{ $value->business_type }}</td>
                                            <td class="align-middle text-center text-sm">
                                                {{ strlen($value->website_url) > 50 ? substr($value->website_url, 0, 30) . '...' : $value->website_url }}
                                            </td>
                                            <td class="align-middle text-center text-sm">{{ convertDateToLocal($value->created_at, 'd-m-Y') }}</td>
                                            <td class="align-middle text-center text-sm">
                                                <div class="dropdown">
                                                    <a href="javascript:;" class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownMenuLink2">
                                                    </a>
                                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink2">
                                                        @if (auth()->guard('admin')->user()->can(['view-application']))
                                                            <li><a href="{{ route('application.view') }}/{{ $value->id }}"
                                                                class="dropdown-item">
                                                                View
                                                            </a></li>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['update-application']))
                                                            <li><a class="dropdown-item restore_modal" data-bs-toggle="modal"
                                                                data-bs-target="#restore_modal"
                                                                data-url="{{ \URL::route('admin.applications.restore', $value->id) }}"
                                                                data-id="{{ $value->id }}">
                                                                Restore</a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        @php $colSpan = 10; @endphp
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


@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/assets/custom_js/admin/applications.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/assets/custom_js/common.js') }}"></script>
@endsection
