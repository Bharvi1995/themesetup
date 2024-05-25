@extends('layouts.admin.default')

@section('title')
    Application-Sent To Bank
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Application-Sent To Bank
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
                                    <label for="website_url">Website URL</label>
                                    <input type="text" class="form-control" placeholder="Enter here" name="website_url"
                                        value="{{ isset($_GET['website_url']) && $_GET['website_url'] != '' ? $_GET['website_url'] : '' }}">
                                    @if ($errors->has('website_url'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('website_url') }}</strong>
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
                                    <label for="bank_name">Bank Name</label>
                                    <select name="bank_id" id="bank_name" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        @foreach ($bankName as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($_GET['bank_id']) && $_GET['bank_id'] == $value->id ? 'selected' : '' }}>
                                                {{ $value->bank_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('bank_id'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('bank_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="bank_name">Status</label>
                                    <select name="status" id="status" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        <option value="0"
                                            {{ isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                        <option value="1"
                                            {{ isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '' }}>
                                            Approved
                                        </option>
                                        <option value="2"
                                            {{ isset($_GET['status']) && $_GET['status'] == '2' ? 'selected' : '' }}>
                                            Declined
                                        </option>
                                        <option value="3"
                                            {{ isset($_GET['status']) && $_GET['status'] == '3' ? 'selected' : '' }}>
                                            Referred
                                        </option>
                                    </select>
                                    @if ($errors->has('bank_id'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('bank_id') }}</strong>
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
                                            No Referral Partners
                                        </option>
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
            <h4 class="card-title">Application-Sent To Bank</h4>
        </div>
        <div class="col-lg-6 text-right">
            @if (auth()->guard('admin')->user()->can(['export-application']))
                <a href="{{ route('admin.applications.exportAllSentToBank', request()->all()) }}"
                    data-filename="Sent_To_Bank_Application" class="btn btn-primary btn-sm" id="ExcelLink"><i
                        class="fa fa-download"></i> Export Excel </a>
            @endif
            @if (auth()->guard('admin')->user()->can(['delete-application']))
                <button type="button" class="btn btn-danger btn-sm btn-shadow" id="deleteSelected"
                    data-link="{{ route('delete-all-application') }}"><i class="fa fa-trash"></i> Delete Selected
                    Record
                </button>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div>
                    </div>
                    <div>
                        <form id="noListform" method="GET" style="float: left;" class="form-dark">
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
                        <div class="btn-group ms-1">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#searchModal"> Advanced Search &nbsp;
                                <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg>
                            </button>
                            <a href="{{ route('admin.applications.sent_to_bank') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>

                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table id="applications_list" class="table table-striped table-borderless">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="custom-control custom-checkbox form-check mr-0">
                                            <input type="checkbox" id="selectallcheckbox" name=""
                                                class="multidelete form-check-input">
                                            <label class="form-check-label" for="selectallcheckbox"></label>
                                        </div>
                                    </th>
                                    <th>Action</th>
                                    <th>Business Name</th>
                                    <th>Email</th>
                                    <th>Sent To Bank</th>
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
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ strlen($value->business_name) > 50 ? substr($value->business_name, 0, 30) . '...' : $value->business_name }}
                                            </td>
                                            <td>{{ $value->email ?? 'No Email' }}</td>
                                            <td>
                                                <a href="javascript:;" data-bs-target="#sentBankShowModal"
                                                    data-bs-toggle="modal" class="btn btn-primary btn-sm sentBankList"
                                                    data-id="{{ $value->id }}"
                                                    data-link="{{ route('admin.send-to-bank-list') }}">
                                                    View Bank List
                                                </a>
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

    @include('partials.application.sentBank')

    <div class="modal fade bs-example-modal-center" id="referredReplyModel" tabindex="-1" role="referredReplyModel"
        aria-hidden="true" style="display: none; padding-right: 15px;">
        <form id="referredReplyForm" enctype="multipart/form-data"
            action="{{ route('admin.application-referred-reply') }}" method="POST">
            {{ csrf_field() }}
            <div class="modal-dialog modal-lg modal-dialog modal-lg-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Reply for referred note</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Referred Reply</label>
                            <textarea class="form-control" name="referred_note_reply" id="referred_note_reply" rows="3"
                                placeholder="Write Here Your Reply for referred note"></textarea>
                            <span style="color: red;" id="referred_note_reply_error"></span>
                        </div>
                        <div class="from-group">
                            <label>Extra Documents (if any)</label><br>
                            <code>You can upload multiple files here.</code>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="validationCustomFile8"
                                    name="extra_documents[]" multiple>
                                <label class="custom-file-label" for="validationCustomFile8">Choose file...</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="submitReferredReplyForm" class="btn btn-success btn-sm">Confirm Submit
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                            id="closeReferredReplyForm">Close
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('customScript')
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });


        $("body").on("click", ".sentBankList", function() {
            var id = $(this).data("id");

            const apiUrl = $(this).data("link");
            $("#detailsContent").html("");
            $.ajax({
                url: apiUrl,
                type: "POST",
                data: {
                    _token: CSRF_TOKEN,
                    id: id
                },
                beforeSend: function() {
                    $("#detailsContent").html(
                        '<i class="fa fa-spinner fa-spin"></i>  Please Wait...'
                    );
                },
                success: function(data) {
                    $("#detailsContent").html(data.html);
                },
                error: function(xhr, status, error) {
                    $("#detailsContent").html(error);
                },
            });
        });
    </script>
    <script src="{{ storage_asset('themeAdmin/custom_js/bank/applications.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
@endsection
