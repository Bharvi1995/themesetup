@extends('layouts.admin.default')

@section('title')
    Generated Referral Partner's Reports
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Generate Referral Partner's Report
@endsection


@section('content')
    <div class="chatbox">
        <div class="chatbox-close"></div>
        <div class="custom-tab-1">
            <a class="nav-link active" data-bs-toggle="tab" href="#Search">Advanced Search</a>
            <div class="tab-content">
                <div class="tab-pane fade active show" id="Search" role="tabpanel">
                    <input type="hidden" class="getAgentId" value="{{ request()->get('agent_id') }}" />
                    <input type="hidden" class="getUserId" value="{{ request()->get('user_id') }}" />
                    <form method="" id="search-form" class="form-check">
                        <div class="basic-form">
                            <div class="row ">
                                <div class="form-group col-lg-6">
                                    <label>Select Referral Partner Name</label>
                                    <select name="agent_id" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%"
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
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox agnetCompany"
                                        data-width="100%">
                                        <option selected disabled> -- Select here -- </option>

                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label> Select Paid Status</label>
                                    <select name="is_paid" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled> -- Select here -- </option>
                                        <option value="1" {{ request()->get('is_paid') == '1' ? 'selected' : '' }}>Paid
                                        </option>
                                        <option value="0" {{ request()->get('is_paid') == '0' ? 'selected' : '' }}>
                                            UnPaid
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-12 mt-4 submit-buttons-commmon">
                                    <button type="submit" class="btn btn-success" id="extraSearch123"> Search</button>
                                    <button type="button" class="btn btn-danger" id="resetForm"></i>Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (auth()->guard('admin')->user()->can(['form-generate-referral-partner-reports']))
        <div class="row">
            <div class="col-xl-12 col-xxl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Generate Referral Partner's Report</h4>
                        <div>
                            @if (auth()->guard('admin')->user()->can(['export-generated-referral-partner-reports']))
                                <form method="POST" action="{{ route('rp.generated.report.excel') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm"><i
                                            class="fa fa-download me-2"></i> Download
                                        all reports in Excel</button>
                                </form>
                                <a href="javascript:;"
                                    data-link="{{ route('rp.generated.report.excel', request()->all()) }}"
                                    data-filename="RP_Report_Excel_" class="btn btn-primary btn-sm" id="ExcelLink"><i
                                        class="fa fa-download me-2"></i> Download
                                    Excel </a>
                            @endif
                            @if (auth()->guard('admin')->user()->can(['delete-generated-referral-partner-reports']))
                                <button type="button" class="btn btn-danger btn-sm" id="deleteSelected"
                                    data-link="{{ route('generate.rp.report.delete') }}"><i class="fa fa-trash me-2"></i>
                                    Delete Selected
                                    Reports</button>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <form method="POST" action="{{ route('generate.agent.report.store') }}" class="form-dark">
                            @csrf
                            <div class="row">
                                <div class="col-xl-3 col-sm-3 col-12 col-md-6 mt-3">
                                    <label for="agent">Select Referral Partner <span class="text-danger">*</span>
                                    </label>
                                    <select name="agent" id="agent" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%"
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
                                            <strong class="text-danger">{{ $errors->first('agent') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="col-xl-3 col-sm-3 col-12 col-md-6 mt-3">
                                    <label for="business_name">Select Company Name <span
                                            class="text-danger">*</span></label>
                                    <select name="user_id" id="business_name" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox agnetCompany"
                                        data-width="100%">
                                        <option selected disabled> -- Select here -- </option>

                                    </select>
                                    @if ($errors->has('user_id'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('user_id') }}</strong>
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
                                            <strong class="text-danger">{{ $errors->first('start_date') }}</strong>
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
                                            <strong class="text-danger">{{ $errors->first('end_date') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-12 mt-3">
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
        <div class="col-lg-12 mb-3">

        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Generated Referral Partner's Report</h4>
                    </div>
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-warning bell-link btn-sm"> <i
                                class="fa fa-search-plus"></i>
                            Advanced Search</button>
                        <a href="{{ route('generate-agent-report') }}" class="btn btn-danger btn-sm">Reset</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="payout_Report" class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="common-check-main">
                                            <label class="custom-control overflow-checkbox mb-0">
                                                <input class="overflow-control-input" id="selectallcheckbox"
                                                    name="" type="checkbox">
                                                <span class="overflow-control-indicator"></span>
                                                <span class="overflow-control-description"></span>
                                            </label>
                                        </div>
                                    </th>
                                    <th>Referral Partner's Name </th>
                                    <th>Company Name</th>
                                    <th>Generated Date</th>
                                    <th>Start Date</th>
                                    <th>End Date </th>
                                    @if (auth()->guard('admin')->user()->can(['update-generated-referral-partner-reports']))
                                        <th>Make Paid</th>
                                        <th>Show Referral Partner's Side </th>
                                    @endif
                                    @if (auth()->guard('admin')->user()->can(['show-generated-referral-partner-reports']))
                                        <th>File</th>
                                    @endif
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($payoutReports) > 0)
                                    @foreach ($payoutReports as $report)
                                        <tr>
                                            <td>
                                                <label class="custom-control overflow-checkbox">
                                                    <input type="checkbox"
                                                        class="overflow-control-input multicheckmail multidelete"
                                                        name="multicheckmail[]" id="customCheckBox_{{ $report->id }}"
                                                        value="{{ $report->id }}">
                                                    <span class="overflow-control-indicator"></span>
                                                    <span class="overflow-control-description"></span>
                                                </label>
                                            </td>
                                            <td>{{ $report->agent_name }}</td>
                                            <td>{{ $report->company_name }}</td>
                                            <td>{{ $report->date }} </td>
                                            <td> {{ $report->start_date->format('d-m-Y') }}</td>
                                            <td>{{ $report->end_date->format('d-m-Y') }}</td>
                                            @if (auth()->guard('admin')->user()->can(['update-generated-referral-partner-reports']))
                                                <td>
                                                    <label class="custom-control overflow-checkbox">
                                                        <input type="checkbox" name=""
                                                            class="overflow-control-input isPaidStatus"
                                                            data-value="{{ $report->is_paid }}"
                                                            data-id="{{ $report->id }}"
                                                            {{ $report->is_paid ? 'checked' : '' }}>
                                                        <span class="overflow-control-indicator"></span>
                                                        <span class="overflow-control-description"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="custom-control overflow-checkbox">
                                                        <input type="checkbox" name=""
                                                            data-value="{{ $report->show_agent_side }}"
                                                            data-id="{{ $report->id }}"
                                                            class="overflow-control-input showClientSide"
                                                            {{ $report->show_agent_side ? 'checked' : '' }}>
                                                        <span class="overflow-control-indicator"></span>
                                                        <span class="overflow-control-description"></span>
                                                    </label>
                                                </td>
                                            @endif
                                            @if (auth()->guard('admin')->user()->can(['show-generated-referral-partner-reports']))
                                                <td>
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
                                            @endif
                                            <td>
                                                <div class="dropdown ml-auto">
                                                    <a href="#" class="btn btn-primary sharp"
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
                                                        </svg>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @if (auth()->guard('admin')->user()->can(['update-generated-referral-partner-reports']))
                                                            <li class="dropdown-item">
                                                                <a href="javascript:void(0)"
                                                                    data-bs-target="#rpGeneratefileModal"
                                                                    data-bs-toggle="modal" data-id="{{ $report->id }}"
                                                                    class="dropdown-item uploadFiles"><i
                                                                        class="fa fa-upload text-success me-2"></i>
                                                                    Upload Files
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['show-generated-referral-partner-reports']))
                                                            <li class="dropdown-item">
                                                                <a href="{{ route('generate.agent.report.pdf', $report->id) }}"
                                                                    target="_blank" class="dropdown-item"><i
                                                                        class="fa fa-download text-secondary me-2"></i>
                                                                    PDF
                                                                </a>
                                                            </li>
                                                            <li class="dropdown-item">
                                                                <a href="{{ route('admin.generate.agent.report.show', $report->id) }}"
                                                                    target="_blank" class="dropdown-item"><i
                                                                        class="fa fa-eye text-primary me-2"></i>
                                                                    View
                                                                </a>
                                                            </li>
                                                        @endif
                                                    </ul>
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
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {

            $('#payout_Report').DataTable()

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
                        html += '<option selected disabled> -- Select here -- </option>';
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
    <script src="{{ storage_asset('themeAdmin/assets/custom_js/common.js') }}"></script>
@endsection
