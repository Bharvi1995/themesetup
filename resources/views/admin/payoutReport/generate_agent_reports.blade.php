@extends('layouts.admin.default')

@section('title')
    Generated Referral Partner's Reports
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Generate Referral Partner's Report</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Generate Referral Partner's Report</h6>
    </nav>
@endsection

@section('customeStyle')
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/custom_css/sweetalert2.min.css') }} " />
@endsection

@section('content')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <form method="" id="search-form" class="form-dark">
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row ">
                                <div class="form-group col-lg-6">
                                    <label>Select Referral Partner Name</label>
                                    <select name="agent_id" data-size="7" data-live-search="true"
                                        class="form-select btn-primary fill_selectbtn_in own_selectbox" data-width="100%"
                                        onchange="getAgentId(this.value , null)">
                                        <option selected disabled> -- Select here -- </option>
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}"
                                                {{ isset($_GET['agent_id']) && $_GET['agent_id'] == $agent->id ? 'selected' : '' }}>
                                                {{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Select Company Name</label>
                                    <select name="user_id" data-size="7" data-live-search="true"
                                        class="form-select btn-primary fill_selectbtn_in own_selectbox agnetCompany"
                                        data-width="100%">
                                        <option selected disabled> -- Select here -- </option>

                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label> Select Paid Status</label>
                                    <select name="is_paid" data-size="7" data-live-search="true"
                                        class="form-select btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled> -- Select here -- </option>
                                        <option value="1" {{ request()->get('is_paid') == '1' ? 'selected' : '' }}>Paid
                                        </option>
                                        <option value="0" {{ request()->get('is_paid') == '0' ? 'selected' : '' }}>
                                            UnPaid
                                        </option>
                                    </select>
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

    @if (auth()->guard('admin')->user()->can(['form-generate-rp-payout-reports']))
        <div class="row">
            <div class="col-xl-12 col-xxl-12">
                <div class="card  mt-1">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Referral Partner's Report</h4>
                        </div>
                        <div class="card-header-toolbar align-items-center">
                            <div class="btn-group mr-2">
                                @if (auth()->guard('admin')->user()->can(['export-generated-rp-payout-reports']))
                                    <form method="POST" action="{{ route('rp.generated.report.excel') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm ">
                                            Download
                                            all reports in Excel</button>
                                    </form>
                                    <a href="{{ route('rp.generated.report.excel', request()->all()) }}"
                                        data-filename="RP_Report_Excel_" class="btn btn-success btn-sm mx-1" id="ExcelLink">
                                        Download
                                        Excel </a>
                                @endif
                                @if (auth()->guard('admin')->user()->can(['delete-generated-rp-payout-reports']))
                                    <button type="button" class="btn btn-danger btn-sm" id="deleteSelected"
                                        data-link="{{ route('generate.rp.report.delete') }}">
                                        Delete Selected Reports</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('generate.agent.report.store') }}" class="form-dark">
                            @csrf
                            <div class="row">
                                <div class="col-xl-3 col-sm-3 col-12 col-md-6 mt-3">
                                    <label for="agent">Select Referral Partner <span class="text-danger">*</span>
                                    </label>
                                    <select name="agent" id="agent" data-size="7" data-live-search="true"
                                        class="form-select btn-primary fill_selectbtn_in own_selectbox" data-width="100%"
                                        onchange="getAgentId(this.value , null)">
                                        <option selected disabled> -- Select here -- </option>
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}"
                                                {{ isset($_GET['agent_id']) && $_GET['agent_id'] == $agent ? 'selected' : '' }}>
                                                {{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('agent'))
                                        <span class="help-block">
                                            <span class="text-danger">{{ $errors->first('agent') }}</span>
                                        </span>
                                    @endif
                                </div>

                                <div class="col-xl-3 col-sm-3 col-12 col-md-6 mt-3">
                                    <label for="business_name">Select Company Name <span
                                            class="text-danger">*</span></label>
                                    <select name="user_id[]" id="business_name" data-size="7" data-live-search="true"
                                        class="form-select btn-primary fill_selectbtn_in own_selectbox agnetCompany agentMerchants"
                                        data-width="100%" multiple>


                                    </select>
                                    @if ($errors->has('user_id'))
                                        <span class="help-block">
                                            <span class="text-danger">{{ $errors->first('user_id') }}</span>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-xl-3 col-sm-3 col-12 col-md-6 mt-3">
                                    <label for="text">Select Date <span class="text-danger">*</span></label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="start_date"
                                            placeholder="Enter here..." id="start_date"
                                            value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                    @if ($errors->has('start_date'))
                                        <span class="help-block">
                                            <span class="text-danger">{{ $errors->first('start_date') }}</span>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-xl-3 col-sm-3 col-12 col-md-6 mt-3">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <div class="date-input">
                                        <input type="text" id="end_date" class="form-control"
                                            placeholder="Enter here..." name="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                    @if ($errors->has('end_date'))
                                        <span class="help-block">
                                            <span class="text-danger">{{ $errors->first('end_date') }}</span>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-12 mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm">Generate Report</button>
                                    <a href="{{ route('generate-agent-report') }}"
                                        class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card  mt-1">
                <div class="card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Generated Referral Partner's Report</h4>
                    </div>
                    <div class="card-header-toolbar align-items-center">
                        <div class="btn-group mr-2">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#searchModal"> Advanced
                                Search &nbsp; <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg></button>
                            <a href="{{ route('generate-agent-report') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table id="payout_Report" class="table table-striped table-borderless">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        <div class="custom-control custom-checkbox form-check mr-0">
                                            <input type="checkbox" id="selectallcheckbox" name=""
                                                class="multidelete form-check-input">
                                            <label class="form-check-label" for="selectallcheckbox"></label>
                                        </div>
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Referral Partner's Name </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Company Name</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Generated Date</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Start Date</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">End Date </th>
                                    @if (auth()->guard('admin')->user()->can(['update-generated-rp-payout-reports']))
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Make Paid</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Show Referral Partner's Side </th>
                                    @endif
                                    @if (auth()->guard('admin')->user()->can(['show-generated-rp-payout-reports']))
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">File</th>
                                    @endif
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payoutReports as $report)
                                    <tr>
                                        <td class="align-middle text-center text-sm">
                                            <div class="custom-control custom-checkbox form-check mr-0">
                                                <input type="checkbox" class="form-check-input multicheckmail multidelete"
                                                    name="multicheckmail[]" id="customCheckBox_{{ $report->id }}"
                                                    value="{{ $report->id }}">
                                                <label class="form-check-label"
                                                    for="customCheckBox_{{ $report->id }}"></label>
                                            </div>

                                        </td>
                                        <td class="align-middle text-center text-sm">{{ $report->agent_name }}</td>
                                        <td class="align-middle text-center text-sm">{{ $report->company_name }}</td>
                                        <td class="align-middle text-center text-sm">{{ $report->date }} </td>
                                        <td class="align-middle text-center text-sm"> {{ $report->start_date->format('d-m-Y') }}</td>
                                        <td class="align-middle text-center text-sm">{{ $report->end_date->format('d-m-Y') }}</td>
                                        @if (auth()->guard('admin')->user()->can(['update-generated-rp-payout-reports']))
                                            <td class="align-middle text-center text-sm">
                                                <div class="custom-control custom-checkbox form-check mr-0">
                                                    <input type="checkbox" name=""
                                                        id="isPaidStatus_{{ $report->id }}"
                                                        class="form-check-input isPaidStatus"
                                                        data-value="{{ $report->is_paid }}"
                                                        data-id="{{ $report->id }}"
                                                        {{ $report->is_paid ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="isPaidStatus_{{ $report->id }}"></label>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <div class="custom-control custom-checkbox form-check mr-0">
                                                    <input type="checkbox" name=""
                                                        id="showClientSide_{{ $report->id }}"
                                                        data-value="{{ $report->show_agent_side }}"
                                                        data-id="{{ $report->id }}"
                                                        class="form-check-input showClientSide"
                                                        {{ $report->show_agent_side ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="showClientSide_{{ $report->id }}"></label>
                                                </div>
                                            </td>
                                        @endif

                                        <td class="align-middle text-center text-sm">
                                            @if (!empty($report->files))
                                                @php
                                                    $files = json_decode($report->files);
                                                    $count = count($files);
                                                @endphp
                                                @if ($count > 0)
                                                    @for ($i = 0; $i < $count; $i++)
                                                        <li><a target="_blank" href="{{ getS3Url($files[$i]) }}">
                                                                <i class="fa fa-file text-primary"></i></a>
                                                        </li>
                                                    @endfor
                                                @else
                                                    <span class='badge badge-sm badge-warning'>N/A</span>
                                                @endif
                                            @endif
                                        </td>

                                        <td class="align-middle text-center text-sm">
                                            <div class="dropdown">
                                                <a href="javascript:;" class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownMenuLink2">
                                                      </a>
                                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink2">
                                                    @if (auth()->guard('admin')->user()->can(['update-generated-rp-payout-reports']))
                                                        <li><a href="javascript:void(0)" data-bs-target="#rpGeneratefileModal"
                                                            data-bs-toggle="modal" data-id="{{ $report->id }}"
                                                            class="dropdown-item uploadFiles">
                                                            Upload Files
                                                        </a></li>
                                                    @endif
                                                    @if (auth()->guard('admin')->user()->can(['show-generated-rp-payout-reports']))
                                                        <li><a href="{{ route('generate.agent.report.pdf', $report->id) }}"
                                                            target="_blank" class="dropdown-item">
                                                            PDF
                                                        </a></li>


                                                        <li><a href="{{ route('admin.generate.agent.report.show', $report->id) }}"
                                                            target="_blank" class="dropdown-item">
                                                            View
                                                        </a></li>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    {{-- @if (count($payoutReports) > 0)
                <div class="d-flex">
                    {!! $payoutReports->links() !!}
                </div>
                @endif --}}
                </div>
            </div>
        </div>
    </div>

    {{-- * Upload File Modal --}}
    <div class="modal fade" id="rpGeneratefileModal" tabindex="-1" role="dialog" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-lg modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Referral Partner's Report Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body" id="detailsContent">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-success">Upload Documents </h5>
                            <form method="POST" action="{{ route('generate.rp.report.document') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="report_id" id="report_id" value="">
                                <div class="row {{ $errors->has('files') ? ' has-error' : '' }}">
                                    <div class="col-md-2">
                                        <label class="label" for="files"><strong> Document</strong></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input filestyle" name="files"
                                                    data-buttonname="btn-inverse"
                                                    accept="image/png, image/jpeg, .pdf, .txt, .doc, .docx, .xls, .xlsx, .zip"
                                                    id="inputGroupFile1">
                                                <label class="custom-file-label" for="inputGroupFile1">Choose file</label>
                                            </div>
                                        </div>
                                        @if ($errors->has('files'))
                                            <p class="text-danger">
                                                <strong>{{ $errors->first('files') }}</strong>
                                            </p>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-info">Upload</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/sweetalert2.min.js') }}"></script>
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {

            $(".agentMerchants").select2({
                placeholder: "Select merchant",
                allowClear: true,
            })

            //select all checkbox for action
            $('body').on('change', '#selectallcheckbox', function() {
                if ($(this).prop("checked") == true) {
                    $('.multicheckmail').prop("checked", true);
                } else if ($(this).prop("checked") == false) {
                    $('.multicheckmail').prop("checked", false);
                }
            });
            var id = $('.getAgentId').val();
            var userId = $('.getUserId').val();
            if (id) {
                getAgentId(id, userId)
            }

            $(document).on('click', '.isPaidStatus', function() {
                var value = $(this).attr('data-value');
                var id = $(this).attr('data-id')
                var status = null;
                if (value == '0') {
                    status = '1';
                } else {
                    status = '0'
                }
                $.ajax({
                    type: 'POST',
                    url: "{{ route('generate.rp.report.isPaid') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        is_paid: status,
                        id: id
                    },
                    success: function(res) {
                        if (res.status == 200) {
                            toastr.success("Paid status updated successfully!");
                            location.reload();
                        }
                    }
                })

            });

            $(document).on('click', '.showClientSide', function() {
                var value = $(this).attr('data-value');
                var id = $(this).attr('data-id')
                console.log(value, id)
                var status = null;
                if (value == '0') {
                    status = '1';
                } else {
                    status = '0'
                }
                $.ajax({
                    type: 'POST',
                    url: "{{ route('generate.rp.report.clientSide') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        client_side: status,
                        id: id
                    },
                    success: function(res) {
                        if (res.status == 200) {
                            toastr.success("Agent status updated successfully!");
                            location.reload();
                        }
                    }
                })
            });

            $(".uploadFiles").on("click", function() {
                var id = $(this).data('id');
                $("#report_id").val(id);
            });

            $(document).on('submit', "#rpReportDocument", function(event) {
                event.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type: "POST",
                    url: "{{ route('generate.rp.report.document') }}",
                    data: formData,
                    success: function(res) {
                        console.log("success")
                    },
                    error: function(res) {
                        console.log(res)
                    }
                })

            });
        });

        function getAgentId(id, userId) {
            $.ajax({
                type: 'POST',
                url: "{{ route('agent.company') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(res) {
                    if (res.status == 200) {
                        var html = ``;
                        // html += '<option selected disabled> -- Select here -- </option>';
                        res.companyName.forEach(function(item, index) {
                            html +=
                                `<option value="${item.user_id}" ${userId && item.user_id == userId ? 'selected' :''}> ${item.business_name}</option>`
                        });

                        $('.agnetCompany').empty().append(html)
                    }
                }
            });
        }
    </script>
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
@endsection
