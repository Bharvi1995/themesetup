@extends('layouts.admin.default')

@section('title')
    Bank Application Detail
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('application-bank.all') }}">Bank
        Applications</a> / Detail
@endsection

@section('customeStyle')
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/custom_css/sweetalert2.min.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-8 col-xxl-8">
            <div class="card  mt-1 height-auto">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Application Detail</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6 mb-2"><strong>Company Name</strong></div>
                        <div class="col-md-6 mb-2">{{ $application->company_name }}</div>
                        <div class="col-md-6 mb-2"><strong>Your Website URL</strong></div>
                        <div class="col-md-6 mb-2">{{ $application->website_url }}</div>
                        <div class="col-md-6 mb-2"><strong>Company Address</strong></div>
                        <div class="col-md-6 mb-2">{{ $application->company_address }}</div>
                        <div class="col-md-6 mb-2"><strong>Company Register Number / Year</strong></div>
                        <div class="col-md-6 mb-2">{{ $application->company_registered_number_year }}</div>
                        <div class="col-md-6 mb-2"><strong>Settlement Method for Crypto</strong></div>
                        <div class="col-md-6 mb-2">{{ $application->settlement_method_for_crypto }}</div>
                        <div class="col-md-6 mb-2"><strong>Settlement Method for Fiat</strong></div>
                        <div class="col-md-6 mb-2">{{ $application->settlement_method_for_fiat }}</div>
                        <div class="col-md-6 mb-2"><strong>MCC Codes</strong></div>
                        <div class="col-md-6 mb-2">{{ $application->mcc_codes }}</div>
                        <div class="col-md-6 mb-2"><strong>Descriptors</strong></div>
                        <div class="col-md-6 mb-2">{{ $application->descriptors }}</div>
                        @foreach (json_decode($application->authorised_individual) as $key => $record)
                            <div class="col-md-6 mb-2"><strong>Authorised Individual {{ $key + 1 }}</strong></div>
                            <div class="col-md-6 mb-2"><strong>Name:</strong> {{ $record->name }}<br><strong>Phone Number:
                                </strong>{{ $record->phone_number }}<br><strong>Email: </strong>{{ $record->email }}</div>
                        @endforeach
                        @if ($application->license_image != null)
                            <div class="col-md-6 mt-2"><strong>Licence Document</strong></div>
                            <div class="col-md-4 mb-2">
                                <a href="{{ getS3Url($application->license_image) }}" target="_blank"
                                    class="btn btn-primary btn-sm">View</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-xxl-4">
            <div class="card  mt-1 height-auto">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Status</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            @if ($application->status == '0')
                                <i class="fa fa-circle text-info mr-1"></i>
                                Pending
                            @elseif($application->status == '1')
                                <i class="fa fa-circle text-success mr-1"></i>
                                Approved
                            @elseif($application->status == '2')
                                <i class="fa fa-circle text-danger mr-1"></i>
                                Rejected
                            @elseif($application->status == '3')
                                <i class="fa fa-circle text-info mr-1"></i>
                                Reassigned
                            @endif
                        </div>
                        <div class="col-md-12 mt-2">
                            @if (auth()->guard('admin')->user()->can(['update-bank-application']))
                                @if ($application->status == '0' || $application->status == '3')
                                    <button class="btn btn-success btn-sm  done" id="applicationApprove"
                                        data-link="{{ route('application-bank-approve') }}"
                                        data-id="{{ $application->id }}">Approve</button>
                                    <button type="button" class="btn btn-danger btn-sm " id="reject"
                                        data-bs-toggle="modal" href="#rejectModel"
                                        data-id="{{ $application->id }}">Reject</button>
                                    @if ($application->status == '0')
                                        <button type="button" class="btn btn-danger btn-sm " id="reassign"
                                            data-bs-toggle="modal" href="#reassignModel"
                                            data-id="{{ $application->id }}">Reassign</button>
                                    @endif
                                @endif
                                <a href="{{ route('application-bank.edit', [$application->id]) }}"
                                    class="btn btn-primary btn-sm " title="Edit">Edit</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ReAssign Model --}}
    <div class="modal fade bs-example-modal-center" id="reassignModel" tabindex="-1" role="reassignModel"
        aria-hidden="true" style="display: none; padding-right: 15px;">
        <form action="{{ route('application-bank-reassign') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $application->id }}">
            <div class="modal-dialog modal-lg modal-dialog modal-lg-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Reason For Reassign</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body form-dark">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="reassign_reason" id="reassign_reason" rows="3" placeholder="Enter here"></textarea>
                            <span class="help-block text-danger">
                                <strong id="reassign_reason_error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success btn-sm">Submit</button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                            id="closeReassignForm">Close</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Reject Model --}}
    <div class="modal fade bs-example-modal-center" id="rejectModel" tabindex="-1" role="rejectModel"
        aria-hidden="true" style="display: none; padding-right: 15px;">
        <form action="{{ route('application-bank-reject') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $application->id }}">
            <div class="modal-dialog modal-lg modal-dialog modal-lg-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Reason For Reject</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body form-dark">
                        <div class="form-group">
                            <label>Reject Reason</label>
                            <textarea class="form-control" name="reject_reason" id="reject_reason" rows="3"
                                placeholder="Write Here Your Reject Reason"></textarea>
                            <span class="help-block text-danger">
                                <strong id="reject_reason_error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="submitRejectForm" data-link="{{ route('application-reject') }}"
                            class="btn btn-success btn-sm">Submit</button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                            id="closeRejectForm">Close</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/sweetalert2.min.js') }}"></script>
    <script type="text/javascript">
        $('#applicationApprove').on('click', function() {
            let apiUrl = $(this).data('link');
            var id = $(this).data('id');

            swal({
                title: 'Are you sure?',
                text: "You want to approve this application?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0CC27E',
                cancelButtonColor: '#FF586B',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                confirmButtonClass: 'btn btn-success btn-raised mr-5',
                cancelButtonClass: 'btn btn-danger btn-raised',
                buttonsStyling: false
            }).then(function() {
                $('#loading').show();
                $.ajax({
                    url: apiUrl,
                    type: 'POST',
                    data: {
                        'id': id,
                        '_token': CSRF_TOKEN
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.success == '1') {
                            $('#loading').hide();
                            swal("Done!", "Application Approved Successfully!", "success");
                            setInterval(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            $('#loading').hide();
                            swal("Error!", "Something went wrong, try again!", "error");
                        }
                    },
                });
            })
        });
    </script>
@endsection
