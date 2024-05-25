@extends('layouts.admin.default')

@section('title')
    Generated Payout Reports
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Generated Payout Reports
@endsection

@section('customeStyle')
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
                <form method="" id="search-form">
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row ">
                                <div class="form-group col-lg-6">
                                    <label>Select Company Name</label>
                                    <select name="user_id" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled> -- Select here -- </option>
                                        @foreach ($companyName as $company)
                                            <option value="{{ $company->user_id }}"
                                                {{ isset($_GET['user_id']) && $_GET['user_id'] == $company->user_id ? 'selected' : '' }}>
                                                {{ $company->business_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label> Select Paid Status</label>
                                    <select name="status" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled> -- Select here -- </option>
                                        <option value="1"
                                            {{ isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '' }}>Paid
                                        </option>
                                        <option value="0"
                                            {{ isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '' }}>UnPaid
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="text">Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" id="start_date_s" name="start_date"
                                            placeholder="Enter here..."
                                            value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>End Date</label>
                                    <div class="date-input">
                                        <input type="text" class="form-control" id="end_date_s"
                                            placeholder="Enter here..." name="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="resetForm"></i>Clear</button>
                        <button type="submit" class="btn btn-info" id="extraSearch123">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (auth()->guard('admin')->user()->can(['form-generate-payout-reports']))
        <div class="row">
            <div class="col-xl-12 col-xxl-12">
                <div class="card  mt-1">
                    <div class="card-header">
                        <div class="iq-header-title">
                            <h4 class="card-title">Generate Report</h4>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <form action="{{ route('payout-report-store') }}" method="post" id="search-form">@csrf
                            <div class="row">
                                <div class="col-xl-3 col-sm-3 col-12 col-md-6 mt-3">
                                    <label for="business_name">Select Company Name <span class="text-danger">*</span>
                                    </label>
                                    <select name="user_id" id="business_name" class="select2" data-width="100%" multiple
                                        data-placeholder="Select a Company">
                                        @foreach ($companyName as $company)
                                            <option value="{{ $company->user_id }}"
                                                {{ isset($_GET['user_id']) && $_GET['user_id'] == $company->user_id ? 'selected' : '' }}>
                                                {{ $company->business_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('user_id'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('user_id') }}</strong>
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
                                <div class="col-xl-3 col-sm-3 col-12 col-md-6 mt-3">
                                    <div class="form-group">
                                        <label>Show Client Side</label>
                                        <div class="form-check">
                                            <input type="checkbox" id="show_client_side check1" name="show_client_side"
                                                value="1" class="form-check-input">
                                            <label class="form-check-label" for="check1">Show on Client Side</label>
                                        </div>
                                    </div>
                                    @if ($errors->has('show_client_side'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('show_client_side') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-12 mt-3">
                                    <button type="submit" class="btn btn-success btn-sm">Generate Report</button>
                                    <a href="{{ route('generate-payout-report') }}"
                                        class="btn btn-primary btn-sm">Cancel</a>
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
            <?php
            $url = Request::fullUrl();
            $parsedUrl = parse_url($url);
            $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
            $subQueryString = $query != '' ? $query . '&type=xlsx' : '';
            ?>
            @if (auth()->guard('admin')->user()->can(['export-generated-payout-reports']))
                @if (!empty($subQueryString))
                    <a href="{{ route('admin.generate_report.export', [$subQueryString]) }}" class="btn btn-info btn-sm"
                        data-filename="GenerateReport_Excel_"><i class="fa fa-download"></i>
                        Download Excel</a>
                @else
                    <a href="{{ route('admin.generate_report.export') }}" class="btn btn-info btn-sm"
                        data-filename="GenerateReport_Excel_"><i class="fa fa-download"></i>
                        Download Excel</a>
                @endif
            @endif
            @if (auth()->guard('admin')->user()->can(['delete-generated-payout-reports']))
                <a id="bulk_delete" class="btn btn-primary btn-sm" style="color:#fff;"><i class="fa fa-trash"></i>
                    Delete Selected Reports </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Generated Payout Reports</h4>
                    </div>
                    <div>
                        <form id="noListform" method="GET" style="width: 165px; float: left; margin-right: 5px;">
                            <select class="form-control-sm form-control" name="noList" id="noList"
                                style="width: 165px; float: left; margin-right: 5px;">
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
                            <button type="button" class="btn btn-info bell-link btn-sm" data-bs-toggle="modal"
                                data-bs-target="#searchModal"> <i class="fa fa-search-plus"></i>
                                Advanced Search</button>
                            <a href="{{ route('generate-payout-report') }}" class="btn btn-primary btn-sm">Reset</a>
                        </div>
                    </div>
                </div>
                @php
                    $getids = implode(',', $arrId);
                @endphp
                <div class="card-body p-0">
                    <div class="table-responsive ">
                        <table id="payout_Report" class="table mb-0 table-borderless">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="custom-control custom-checkbox custom-control-inline mr-0">
                                            <input type="checkbox" id="selectallcheckbox" name=""
                                                class="multidelete custom-control-input">
                                            <label class="custom-control-label" for="selectallcheckbox"></label>
                                            <input type="hidden" name="getIdValue" id="getIdValue"
                                                value="{{ $getids }}">
                                        </div>
                                    </th>
                                    <th style="min-width: 165px;">Company Name</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">
                                        Payout <br> From - To
                                    </th>
                                    <th class="text-center">Chargeback <br> From - To </th>
                                    @if (auth()->guard('admin')->user()->can(['update-generated-payout-reports']))
                                        <th class="text-center">Paid</th>
                                        <th class="text-center" style="min-width: 170px;">Show Client Side </th>
                                    @endif
                                    @if (auth()->guard('admin')->user()->can(['show-generated-payout-reports']))
                                        <th class="text-center">File</th>
                                    @endif
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dataT as $key => $value)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox custom-control-inline mr-0">
                                                <input type="checkbox"
                                                    class="custom-control-input multicheckmail multidelete"
                                                    name="multicheckmail[]" id="checkbox-{{ $value->id }}"
                                                    value="{{ $value->id }}">
                                                <label class="custom-control-label"
                                                    for="checkbox-{{ $value->id }}"></label>
                                            </div>
                                        </td>
                                        <td>{{ $value->company_name }}</td>
                                        <td class="text-center">{{ $value->date }}</td>
                                        <td class="text-center">
                                            {{ date('d-m-Y', strtotime($value->start_date)) }}
                                            <br>-<br>
                                            {{ date('d-m-Y', strtotime($value->end_date)) }}
                                        </td>
                                        <td class="text-center">
                                            {{ date('d-m-Y', strtotime($value->chargebacks_start_date)) }}
                                            <br>-<br>
                                            {{ date('d-m-Y', strtotime($value->chargebacks_end_date)) }}
                                        </td>
                                        @if (auth()->guard('admin')->user()->can(['update-generated-payout-reports']))
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox custom-control-inline mr-0">
                                                    <input type="checkbox" name=""
                                                        id="paidstatus_{{ $value->id }}"
                                                        class="custom-control-input paidstatus"
                                                        data-id="{{ $value->id }}"
                                                        {{ $value->status == '1' ? 'checked disabled' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="paidstatus_{{ $value->id }}"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox custom-control-inline mr-0">
                                                    <input type="checkbox" name=""
                                                        id="showClientSide_{{ $value->id }}"
                                                        class="custom-control-input showClientSide"
                                                        data-id="{{ $value->id }}"
                                                        {{ $value->show_client_side == '1' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="showClientSide_{{ $value->id }}"></label>
                                                </div>
                                                @if ($value->show_client_side == '1')
                                                    <span class="badge badge-sm badge-success">Yes</span>
                                                @endif
                                            </td>
                                        @endif
                                        @if (auth()->guard('admin')->user()->can(['show-generated-payout-reports']))
                                            <td class="text-center">
                                                <?php
                                    if(!empty($value->files)){
                                        $files = json_decode($value->files);
                                        $count = count($files);
                                        if($count > 0){
                                            for($i=0;$i<$count;$i++){
                                                ?>
                                                <li><a target="_blank" href="{{ getS3Url($files[$i]) }}"> <i
                                                            class="fa fa-file text-warning"></i></a></li>
                                                <?php
                                            }
                                        }
                                    }
                                    else{
                                        ?>
                                                <span class='badge badge-sm badge-warning'>N/A</span>
                                                <?php
                                    }
                                    ?>
                                            </td>
                                        @endif
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
                                                    @if (auth()->guard('admin')->user()->can(['update-generated-payout-reports']))
                                                        <li class="dropdown-item">
                                                            <a data-bs-target="#fileModal" data-bs-toggle="modal"
                                                                data-id="{{ $value->id }}"
                                                                class="dropdown-item uploadFiles"><i
                                                                    class="fa fa-upload text-success me-2"></i> Upload
                                                                Files</a>
                                                        </li>
                                                    @endif
                                                    @if (auth()->guard('admin')->user()->can(['show-generated-payout-reports']))
                                                        <li class="dropdown-item">
                                                            <a class="dropdown-item"
                                                                href="{{ route('generate_report.pdf', $value->id) }}"><i
                                                                    class="fa fa-download text-secondary me-2"></i>PDF</a>
                                                        </li>
                                                        <li class="dropdown-item">
                                                            <a href="{{ route('generate_report.show', $value->id) }}"
                                                                class="dropdown-item" target="_blank">
                                                                <i class="fa fa-eye text-primary me-2"></i>
                                                                View
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center clPagination">
                        {!! $dataT->appends($_GET)->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="fileModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Report Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body" id="detailsContent">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-success">Upload Documents </h5>
                            <form action="{{ route('generate-report-document') }}" method="POST"
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
    <script src="{{ storage_asset('themeAdmin/assets/custom_js/common.js') }}"></script>
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            //select all checkbox for action
            $('body').on('change', '#selectallcheckbox', function() {
                if ($(this).prop("checked") == true) {
                    $('.multicheckmail').prop("checked", true);
                } else if ($(this).prop("checked") == false) {
                    $('.multicheckmail').prop("checked", false);
                }
            });

            $('body').on('click', '.paidstatus', function(e) {
                if ($(this).prop("checked") == true) {
                    var status = '1';
                } else if ($(this).prop("checked") == false) {
                    var status = '0';
                }
                var id = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: "{{ URL::route('make-report-paid') }}",
                    data: {
                        '_token': CSRF_TOKEN,
                        'status': status,
                        'id': id
                    },
                    beforeSend: function() {
                        $(this).attr('disabled', 'disabled');
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Paid status updated Successfully!');
                        } else {
                            toastr.error('Something Went Wrong!');
                        }

                        $(this).attr('disabled', false);
                        $(this).html('Submit');
                        if (data.success == true || data.success == false) {
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    },
                });
            });

            $('body').on('click', '.showClientSide', function(e) {
                if ($(this).prop("checked") == true) {
                    var status = '1';
                } else if ($(this).prop("checked") == false) {
                    var status = '0';
                }
                $(this).attr('disabled', 'disabled');
                var id = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: "{{ URL::route('show-report-client') }}",
                    data: {
                        '_token': CSRF_TOKEN,
                        'status': status,
                        'id': id
                    },
                    beforeSend: function() {
                        $(this).attr('disabled', 'disabled');
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Show on client side updated Successfully!');
                        } else {
                            toastr.error('Something Went Wrong!');
                        }
                        $(this).attr('disabled', false);
                        $(this).html('Submit');
                        if (data.success == true || data.success == false) {
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    },
                });
            });

            $(document).on('click', '#bulk_delete', function() {
                var id = [];
                $('.multidelete:checked').each(function() {
                    id.push($(this).val());
                });
                if (id.length > 0) {
                    swal({
                        title: "Are you sure?",
                        text: "you want to delete this record?",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{ route('report-delete') }}",
                                method: "get",
                                data: {
                                    id: id
                                },
                                success: function(data) {
                                    if (data.success == true) {
                                        toastr.success('Record deleted Successfully!');
                                    } else {
                                        toastr.error('Something Went Wrong!');
                                    }
                                    if (data.success == true || data.success == false) {
                                        setTimeout(function() {
                                            location.reload();
                                        }, 2000);
                                    }
                                }
                            });
                        }
                    })
                } else {
                    swal("Please select at least one report");
                }
            });

            $(".uploadFiles").on("click", function() {
                var id = $(this).data('id');
                $("#report_id").val(id);
            })

        });
    </script>
@endsection
