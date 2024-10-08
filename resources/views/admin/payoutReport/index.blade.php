@extends('layouts.admin.default')

@section('title')
    Generated Payout Reports
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Generated Payout Reports
@endsection

@section('customeStyle')
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/custom_css/sweetalert2.min.css') }}">
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
                                            {{ isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '' }}>
                                            Paid</option>
                                        <option value="0"
                                            {{ isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '' }}>
                                            UnPaid</option>
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
                        <button type="button" class="btn btn-danger" id="resetForm"></i>Clear</button>
                        <button type="submit" class="btn btn-success" id="extraSearch123">Search</button>
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
                        <div>
                            @php
                                $url = Request::fullUrl();
                                $parsedUrl = parse_url($url);
                                $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
                                $subQueryString = $query != '' ? $query . '&type=xlsx' : '';
                            @endphp
                            @if (auth()->guard('admin')->user()->can(['export-generated-payout-reports']))
                                @if (!empty($subQueryString))
                                    <a href="{{ route('admin.generate_report.export', [$subQueryString]) }}"
                                        class="btn btn-shadow btn-primary btn-sm" data-filename="GenerateReport_Excel_"><i
                                            class="fa fa-download"></i>
                                        Download Excel</a>
                                @else
                                    <a href="{{ route('admin.generate_report.export') }}"
                                        class="btn btn-shadow btn-primary btn-sm" data-filename="GenerateReport_Excel_"><i
                                            class="fa fa-download"></i>
                                        Download Excel</a>
                                @endif
                            @endif
                            @if (auth()->guard('admin')->user()->can(['update-generated-payout-reports']))
                                <a id="bulk_delete" class="btn btn-danger btn-shadow btn-sm" style="color:#fff;"><i
                                        class="fa fa-trash"></i>
                                    Delete Selected Reports </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('payout-report-store') }}" method="post" id="search-form"
                            class="form-dark">
                            @csrf
                            <div class="row">
                                <div class="col-xl-3 col-sm-3 col-12 col-md-6 mt-3">
                                    <label for="business_name">Select Company Name <span class="text-danger">*</span>
                                    </label>
                                    <select name="user_id" id="business_name" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%">
                                        <option selected disabled> -- Select here -- </option>
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
                                    <label for="text">Start Date <span class="text-danger">*</span></label>
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
                                <div class="col-xl-3 col-sm-3 col-12 col-md-6 mt-3">
                                    <label for="text">Chargebacks Start Date <span
                                            class="text-danger">*</span></label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="chargebacks_start_date"
                                            placeholder="Enter here..." id="chargebacks_start_date"
                                            value="{{ isset($_GET['chargebacks_start_date']) && $_GET['chargebacks_start_date'] != '' ? $_GET['chargebacks_start_date'] : '' }}"
                                            autocomplete="off">

                                    </div>
                                    @if ($errors->has('chargebacks_start_date'))
                                        <span class="help-block">
                                            <strong
                                                class="text-danger">{{ $errors->first('chargebacks_start_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-xl-3 col-sm-3 col-12 col-md-6 mt-3">
                                    <label for="chargebacks_end_date">Chargebacks End Date <span
                                            class="text-danger">*</span></label>
                                    <div class="date-input">
                                        <input type="text" id="chargebacks_end_date" class="form-control"
                                            placeholder="Enter here..." name="chargebacks_end_date"
                                            value="{{ isset($_GET['chargebacks_end_date']) && $_GET['chargebacks_end_date'] != '' ? $_GET['chargebacks_end_date'] : '' }}"
                                            autocomplete="off">

                                    </div>
                                    @if ($errors->has('chargebacks_end_date'))
                                        <span class="help-block">
                                            <strong
                                                class="text-danger">{{ $errors->first('chargebacks_end_date') }}</strong>
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
                                    <button type="submit" class="btn btn-primary btn-sm">Generate Report</button>
                                    <a href="{{ route('generate-payout-report') }}"
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
            <div class="card mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Generated Payout Reports</h4>
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
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary ms-1 btn-sm" data-bs-toggle="modal"
                                data-bs-target="#searchModal"> Advanced
                                Search &nbsp; <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg></button>
                            <a href="{{ route('generate-payout-report') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                    </div>
                </div>
                @php
                    $getids = implode(',', $arrId);
                @endphp
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table id="payout_Report" class="table table-striped table-borderless">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="custom-control custom-checkbox form-check mr-0">
                                            <input type="checkbox" id="selectallcheckbox" name=""
                                                class="multidelete form-check-input">
                                            <label class="form-check-label" for="selectallcheckbox"></label>
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
                                            <div class="custom-control custom-checkbox form-check mr-0">
                                                <input type="checkbox" class="form-check-input multicheckmail multidelete"
                                                    name="multicheckmail[]" id="checkbox-{{ $value->id }}"
                                                    value="{{ $value->id }}">
                                                <label class="form-check-label"
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
                                                <div class="custom-control custom-checkbox form-check mr-0">
                                                    <input type="checkbox" name=""
                                                        id="paidstatus_{{ $value->id }}"
                                                        class="form-check-input paidstatus" data-id="{{ $value->id }}"
                                                        {{ $value->status == '1' ? 'checked disabled' : '' }}>
                                                    <label class="form-check-label"
                                                        for="paidstatus_{{ $value->id }}"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox form-check mr-0">
                                                    <input type="checkbox" name=""
                                                        id="showClientSide_{{ $value->id }}"
                                                        class="form-check-input showClientSide"
                                                        data-id="{{ $value->id }}"
                                                        {{ $value->show_client_side == '1' ? 'checked="checked"' : '' }}>
                                                    <label class="form-check-label"
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
                                                <li style="list-style: none;"><a target="_blank"
                                                        href="{{ getS3Url($files[$i]) }}"> <i
                                                            class="fa fa-file text-info"></i></a></li>
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
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                    data-bs-toggle="dropdown">
                                                    <svg width="5" height="17" viewBox="0 0 5 17" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
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
                                                    @if (auth()->guard('admin')->user()->can(['update-generated-payout-reports']))
                                                        <a data-bs-target="#fileModal" data-bs-toggle="modal"
                                                            data-id="{{ $value->id }}"
                                                            class="dropdown-item uploadFiles text-white"><i
                                                                class="fa fa-upload text-success me-2"></i> Upload
                                                            Files</a>
                                                    @endif
                                                    @if (auth()->guard('admin')->user()->can(['show-generated-payout-reports']))
                                                        <a class="dropdown-item"
                                                            href="{{ route('generate_report.pdf', $value->id) }}"><i
                                                                class="fa fa-download text-secondary me-2"></i>PDF</a>


                                                        <a href="{{ route('generate_report.show', $value->id) }}"
                                                            class="dropdown-item" target="_blank">
                                                            <i class="fa fa-eye text-primary me-2"></i>
                                                            View
                                                        </a>
                                                    @endif
                                                    </ul>
                                                </div>
                                        </td>
                                    </tr>
                                @endforeach
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
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });
    </script>
    <script src="{{ storage_asset('themeAdmin/custom_js/sweetalert2.min.js') }}"></script>
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
                    if ($(this).val() != "on") {
                        id.push($(this).val());
                    }
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
