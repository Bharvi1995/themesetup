@extends('layouts.agent.default')

@section('title')
    Generated Referral Partner's Reports
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('rp.dashboard') }}">Dashboard</a> / Generate Referral Partner's Report
@endsection

@section('customStyle')
    <style type="text/css">
        table.dataTable thead th,
        table.dataTable tbody td {
            padding: 8px 15px !important;
        }
    </style>
@endsection

@section('content')
    <div class="chatbox">
        <div class="chatbox-close"></div>
        <div class="custom-tab-1">
            <a class="nav-link active" data-bs-toggle="tab" href="#Search">Advanced Search</a>
            <div class="tab-content">
                <div class="tab-pane fade active show" id="Search" role="tabpanel">
                    <input type="hidden" class="getUserId" value="{{ request()->get('user_id') }}" />
                    <form method="" id="search-form">
                        <div class="basic-form">
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label>Select Company Name</label>
                                    <select name="user_id" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox agnetCompany"
                                        data-width="100%">
                                        <option selected disabled> -- Select here -- </option>
                                        @foreach ($companyName as $cmp)
                                            <option value="{{ $cmp->user_id }}"
                                                {{ isset($_GET['user_id']) && $_GET['user_id'] == $cmp->user_id ? 'selected' : '' }}>
                                                {{ $cmp->business_name }}</option>
                                        @endforeach
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
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <div class="mr-auto pr-3">
                        <h4 class="card-title">Generated Referral Partner's Report</h4>
                    </div>
                    <div class="btn-group mr-2">
                        <button type="button" class="btn btn-warning bell-link btn-sm"> <i class="fa fa-search-plus"></i>
                            Advanced Search</button>
                        <a href="{{ route('rp.rp-report') }}" class="btn btn-danger btn-sm">Reset</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="payout_Report" class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th>Referral Partner's Name </th>
                                    <th>Company Name</th>
                                    <th>Generated Date</th>
                                    <th>Start Date</th>
                                    <th>End Date </th>
                                    <th>Make Paid</th>
                                    <th>File</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payoutReports as $report)
                                    <tr>
                                        <td>{{ $report->agent_name }}</td>
                                        <td>{{ $report->company_name }}</td>
                                        <td>{{ $report->date }} </td>
                                        <td> {{ $report->start_date->format('d-m-Y') }}</td>
                                        <td>{{ $report->end_date->format('d-m-Y') }}</td>
                                        <td>
                                            {{ $report->is_paid ? 'Paid' : 'Un-paid' }}
                                        </td>
                                        <td>
                                            @if (!empty($report->files))
                                                @php
                                                    $files = json_decode($report->files);
                                                    $count = count($files);
                                                @endphp
                                                @if ($count > 0)
                                                    @for ($i = 0; $i < $count; $i++)
                                                        <li><a target="_blank" href="{{ getS3Url($files[$i]) }}"> <i
                                                                    class="fa fa-file text-primary"></i></a>
                                                        </li>
                                                    @endfor
                                                @else
                                                    <span class='badge badge-sm badge-warning'>N/A</span>
                                                @endif
                                            @else
                                                <span class='badge badge-sm badge-warning'>N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown ml-auto">
                                                <a href="#" class="btn btn-primary sharp" data-bs-toggle="dropdown"
                                                    aria-expanded="true"><svg xmlns="http://www.w3.org/2000/svg"
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
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li class="dropdown-item">
                                                        <a href="{{ route('rp.generate.rp.report.pdf', $report->id) }}"
                                                            target="_blank" class="dropdown-item"><i
                                                                class="fa fa-download text-secondary mr-2"></i>
                                                            PDF
                                                        </a>
                                                    </li>
                                                    <li class="dropdown-item">
                                                        <a href="{{ route('generate.agent.report.show', $report->id) }}"
                                                            target="_blank" class="dropdown-item"><i
                                                                class="fa fa-eye text-primary mr-2"></i>
                                                            View
                                                        </a>
                                                    </li>
                                                </ul>
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
        <div class="modal-dialog modal-xl">
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
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
@endsection
