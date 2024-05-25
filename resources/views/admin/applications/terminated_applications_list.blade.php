@extends('layouts.admin.default')
@section('title')
    Terminated Applications
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Terminated Applications
@endsection

@section('content')
    @include('requestDate')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog modal-lg-scrollable" role="document">
            <form method="" id="search-form">
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
                                    <select name="monthly_volume_condition" id="monthly_volume_condition" data-size="7"
                                        data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%"
                                        style="position: absolute; bottom: 0; right: 0;">
                                        <option value="e"
                                            {{ isset($_GET['monthly_volume_condition']) && $_GET['monthly_volume_condition'] == 'e' ? 'selected' : '' }}>
                                            =</option>
                                        <option value="l"
                                            {{ isset($_GET['monthly_volume_condition']) && $_GET['monthly_volume_condition'] == 'l' ? 'selected' : '' }}>
                                            << /option>
                                        <option value="le"
                                            {{ isset($_GET['monthly_volume_condition']) && $_GET['monthly_volume_condition'] == 'le' ? 'selected' : '' }}>
                                            <=< /option>
                                        <option value="g"
                                            {{ isset($_GET['monthly_volume_condition']) && $_GET['monthly_volume_condition'] == 'g' ? 'selected' : '' }}>
                                            ></option>
                                        <option value="ge"
                                            {{ isset($_GET['monthly_volume_condition']) && $_GET['monthly_volume_condition'] == 'ge' ? 'selected' : '' }}>
                                            >=</option>
                                    </select>
                                    <input type="number" class="form-control" placeholder="Enter Monthly Volume"
                                        name="monthly_volume"
                                        value="{{ isset($_GET['monthly_volume']) && $_GET['monthly_volume'] != '' ? $_GET['monthly_volume'] : '' }}"
                                        style="width: 75%; position: absolute; bottom: 0; right: 0;">
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
                        <button type="button" class="btn btn-primary" id="resetForm"></i>Clear</button>
                        <button type="submit" class="btn btn-success" id="extraSearch123">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 text-right mb-2">
            @if (auth()->guard('admin')->user()->can(['export-application']))
                <a href="{{ route('admin.applications.exportAllTerminated', request()->all()) }}"
                    data-filename="Terminated_Application" class="btn btn-primary btn-sm" id="ExcelLink"><i
                        class="fa fa-download"></i> Export Excel </a>
            @endif
            @if (auth()->guard('admin')->user()->can(['delete-application']))
                <button type="button" class="btn btn-primary btn-sm btn-shadow" id="deleteSelected"
                    data-link="{{ route('delete-all-application') }}"><i class="fa fa-trash"></i> Delete Selected
                    Record</button>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card  mt-1">
                <div class="card-header d-flex bg-info justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Terminated Applications</h4>
                    </div>
                    <div>
                        <form id="noListform" method="GET" style="width: 165px; float: left; margin-right: 5px;">
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
                                data-bs-target="#searchModal"> <i class="fa fa-search-plus"></i>
                                Advanced
                                Search</button>
                            <a href="{{ route('admin.applications.is_terminated') }}"
                                class="btn btn-primary btn-sm">Reset</a>
                        </div>

                        @if (auth()->guard('admin')->user()->can(['send-email-application']))
                            <a href="" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#Send_email"><i class="fa fa-envelope"></i> Send Mail </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="is_completed_applications_list" class="table mb-0 table-borderless">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="custom-control custom-checkbox custom-control-inline mr-0">
                                            <input type="checkbox" id="selectallcheckbox" name=""
                                                class="multidelete custom-control-input">
                                            <label class="custom-control-label" for="selectallcheckbox"></label>
                                        </div>
                                    </th>
                                    <th>Action</th>
                                    <th>Note</th>
                                    <th style="min-width: 140px;">Business Name</th>
                                    <th>Email</th>
                                    <th style="min-width: 150px;">Merchant Name</th>
                                    <th style="min-width: 130px;">Business Type</th>
                                    <th>Website URL</th>
                                    <th style="min-width: 130px;">Creation Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($applications) && $applications->count())
                                    @foreach ($applications as $key => $value)
                                        @php $key++; @endphp
                                        <tr>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline mr-0">
                                                    <input type="checkbox" id="checkbox-{{ $value->id }}"
                                                        name="multidelete[]" class="multidelete custom-control-input"
                                                        value="{{ $value->id }}">
                                                    <label class="custom-control-label"
                                                        for="checkbox-{{ $value->id }}"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dropdown ml-auto">
                                                    <a href="#" class="btn btn-primary sharp rounded-pill"
                                                        data-bs-toggle="dropdown" aria-expanded="true"><svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="18px"
                                                            height="18px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <rect x="0" y="0" width="24"
                                                                    height="24">
                                                                </rect>
                                                                <circle fill="#FFF" cx="5" cy="12"
                                                                    r="2">
                                                                </circle>
                                                                <circle fill="#FFF" cx="12" cy="12"
                                                                    r="2">
                                                                </circle>
                                                                <circle fill="#FFF" cx="19" cy="12"
                                                                    r="2">
                                                                </circle>
                                                            </g>
                                                        </svg></a>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @if (auth()->guard('admin')->user()->can(['pdf-download-application']))
                                                            <li class="dropdown-item">
                                                                <a href="{{ \URL::route('application-pdf', $value->id) }}"
                                                                    class="dropdown-item"><i
                                                                        class="fa fa-download text-primary me-2"></i>
                                                                    PDF Download
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['doc-download-application']))
                                                            <li class="dropdown-item">
                                                                <a href="{{ \URL::route('application-docs', $value->id) }}"
                                                                    class="dropdown-item"><i
                                                                        class="fa fa-download text-info me-2"></i>
                                                                    Document Download
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['view-application']))
                                                            <li class="dropdown-item">
                                                                <a href="{{ route('application.view') }}/{{ $value->id }}"
                                                                    class="dropdown-item"><i
                                                                        class="fa fa-eye text-secondary me-2"></i>
                                                                    View
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['delete-application']))
                                                            <li class="dropdown-item">
                                                                <a class="dropdown-item delete_modal"
                                                                    data-url="{{ \URL::route('admin.applications.delete', $value->id) }}"
                                                                    data-id="{{ $value->id }}"><i
                                                                        class="fa fa-trash text-danger me-2"></i>
                                                                    Delete</a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <span data-bs-toggle="modal" data-bs-target="#App_Note"
                                                    data-id="{{ $value->id }}" class="AppNote">
                                                    <a href="javascript:;" data-bs-toggle="tooltip" data-placement="top"
                                                        title="Application Note"
                                                        class="btn btn-sm btn-primary pull-right"><i
                                                            class="fa fa-sticky-note"></i></a>
                                                </span>
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
                    <div class="pagination-wrap">
                        {!! $applications->appends($_GET)->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal right fade" id="App_Note" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Application Note</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <form id="noteForm">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="" id="appId">
                                <div class="form-group">
                                    <label>Write Note</label>
                                    <textarea class="form-control" name="note" id="note" rows="3" placeholder="Write Here Your Note"></textarea>
                                    <span class="help-block text-danger">
                                        <span id="note_error"></span>
                                    </span>
                                </div>
                                <button type="button" id="submitNoteForm"
                                    data-link="{{ route('store-application-note') }}"
                                    class="btn btn-success btn-sm">Submit Note</button>

                                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                                    id="closeNoteForm">Close</button>
                            </form>
                        </div>
                        <div class="col-md-12">
                            <div id="detailsContent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal right fade" id="Send_email" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Send Mail</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <form id="SendMailForm" method="POST" enctype="multipart/form-data">
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
    <script src="{{ storage_asset('themeAdmin/custom_js/admin/applications.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
    <script src="https://cdn.ckeditor.com/4.16.2/full/ckeditor.js"></script>
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('body').on('click', '.AppNote', function() {
                var id = $(this).data('id');
                $('#appId').val(id);
                $('#detailsContent').html('');
                $.ajax({
                    url: '{{ route('get-application-note') }}',
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'id': id
                    },
                    beforeSend: function() {
                        $('#detailsContent').html(
                            '<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                    },
                    success: function(data) {
                        $('#detailsContent').html(data.html);
                    },
                });
            });

            function getAppNote(id) {
                $.ajax({
                    url: '{{ route('get-application-note') }}',
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'id': id
                    },
                    beforeSend: function() {
                        $('#detailsContent').html(
                            '<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                    },
                    success: function(data) {
                        $('#detailsContent').html(data.html);
                    },
                });
            }

            $('body').on('click', '#submitNoteForm', function() {
                var noteForm = $("#noteForm");
                var formData = noteForm.serialize();
                $('#note_error').html("");
                $('#note').val("");
                let apiUrl = $(this).data('link');

                $.ajax({
                    url: apiUrl,
                    type: 'POST',
                    data: formData,
                    success: function(data) {
                        if (data.errors) {
                            if (data.errors.note) {
                                $('#note_error').html(data.errors.note[0]);
                            }
                        }
                        if (data.success == '1') {
                            getAppNote(data.id);
                            toastr.success('Add Note Successfully.');
                        } else if (data.success == '0') {
                            toastr.error('Something went wrong, please try again!');
                        }
                    },
                });
            });

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
        });
    </script>
@endsection
