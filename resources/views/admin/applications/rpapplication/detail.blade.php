@extends('layouts.admin.default')

@section('title')
    RP Application Detail
@endsection

@section('breadcrumbTitle')
     <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('application-rp.all') }}">RP Applications</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Detail</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Detail</h6>
    </nav>
@endsection

@section('customeStyle')
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/custom_css/sweetalert2.min.css') }}">
@endsection

@section('content')
    <div class="row">

        {{-- Commened the rates sent functionality --}}

        {{-- <div class="col-xl-12 col-xxl-12">
            <div class="card  mt-1 height-auto">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Rate</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-9 col-xxl-9">
                            @if ($application->status != 1)
                                <div class="row form-dark">
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-12 label-control">Add buy Rate For Visa(%)</label>
                                            <div class="col-md-12">
                                                {!! Form::number('add_buy_rate', '', [
                                                    'placeholder' => 'Enter here',
                                                    'class' => 'form-control',
                                                    'id' => 'add_buy_rate',
                                                ]) !!}
                                                <span class="help-block text-danger" id="add_buy_rate_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-12 label-control">Add buy Rate For Master(%)</label>
                                            <div class="col-md-12">
                                                {!! Form::number('add_buy_rate_master', '', [
                                                    'placeholder' => 'Enter here',
                                                    'class' => 'form-control',
                                                    'id' => 'add_buy_rate_master',
                                                ]) !!}
                                                <span class="help-block text-danger" id="add_buy_rate_master_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-dark">
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-12 label-control">Add buy Rate For Amex(%)</label>
                                            <div class="col-md-12">
                                                {!! Form::number('add_buy_rate_amex', '', [
                                                    'placeholder' => 'Enter here',
                                                    'class' => 'form-control',
                                                    'id' => 'add_buy_rate_amex',
                                                ]) !!}
                                                <span class="help-block text-danger" id="add_buy_rate_amex_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-md-12 label-control">Add buy Rate For Discover(%)</label>
                                            <div class="col-md-12">
                                                {!! Form::number('add_buy_rate_discover', '', [
                                                    'placeholder' => 'Enter here',
                                                    'class' => 'form-control',
                                                    'id' => 'add_buy_rate_discover',
                                                ]) !!}
                                                <span class="help-block text-danger"
                                                    id="add_buy_rate_discover_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="table-responsive custom-table">
                                        <table class="table table-borderless table-striped">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Add Buy Rate For Visa(%)</strong></td>
                                                    <td>{{ $application->agent->add_buy_rate }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Add Buy Rate For Master(%)</strong></td>
                                                    <td>{{ $application->agent->add_buy_rate_master }}</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>Add Buy Rate For Amex(%)</strong>
                                                    </td>
                                                    <td>{{ $application->agent->add_buy_rate_amex }}</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>Add Buy Rate For Discover(%)</strong>
                                                    </td>
                                                    <td>
                                                        {{ $application->agent->add_buy_rate_discover }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            @endif
                        </div>
                        <div class="col-xl-3 col-xxl-3">
                            <div class="row">
                                <div class="col-md-12 mt-2">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}


        <div class="col-xl-8 col-xxl-8">
            <div class="card  mt-1 height-auto">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Application Detail</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>Entity Name</strong>
                                        <p>{{ $application->company_name }}</p>
                                    </td>
                                    <td>
                                        <strong>Your Website URL</strong>
                                        <p>{{ $application->website_url }}</p>
                                    </td>
                                    <td>
                                        <strong>Address</strong>
                                        <p>{{ $application->company_address }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Company Email</strong>
                                        <p>{{ $application->company_email }}</p>
                                    </td>
                                    <td>
                                        <strong>Tax Id</strong>
                                        <p>{{ $application->company_registered_number }}</p>
                                    </td>
                                    <td>
                                        <strong>Date Of Birth/Incorporation</strong>
                                        <p>{{ $application->company_registered_number_year }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Average No. of Applications Per Month</strong>
                                        <p>{{ $application->avg_no_of_app }}</p>
                                    </td>
                                    <td>
                                        <strong>Average Volume Commited Per Month (In USD)</strong>
                                        <p>{{ $application->commited_avg_volume_per_month }}</p>
                                    </td>
                                    <td>
                                        <strong>Payment Solutions Needed</strong>
                                        @if ($application->payment_solutions_needed != null)
                                            @php
                                                $payment_solution = json_decode($application->payment_solutions_needed);
                                                if (is_array($payment_solution) && !empty($payment_solution)) {
                                                    foreach ($payment_solution as $key => $value) {
                                                        echo "<span class='badge badge-sm badge-primary'>" . \App\TechnologyPartner::find($value)->name . '</span> ';
                                                    }
                                                }
                                            @endphp
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Industries Referred</strong>
                                        <p>
                                            @if ($application->industries_reffered != null)
                                                @php
                                                    $indutry_types = json_decode($application->industries_reffered);
                                                    if (is_array($indutry_types) && !empty($indutry_types)) {
                                                        foreach ($indutry_types as $key => $value) {
                                                            echo "<span class='badge badge-sm badge-primary'>" . \App\Categories::find($value)->name . '</span> ';
                                                        }
                                                    }
                                                @endphp
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <strong>Major Regions</strong>
                                        <p>
                                            @if ($application->major_regious != null)
                                                @php
                                                    $a = json_decode($application->major_regious);
                                                    if (is_array($a)) {
                                                        if (!empty($a)) {
                                                            foreach ($a as $key => $value) {
                                                                echo "<span class='badge badge-sm badge-success'>" . $value . '</span> ';
                                                            }
                                                        }
                                                    }
                                                @endphp
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <strong>How are the leads generated?</strong>
                                        <p>{{ $application->generated_lead }}</p>
                                    </td>
                                </tr>
                                @if ($application->authorised_individual != null)
                                    @php
                                        $b = json_decode($application->authorised_individual);
                                    @endphp
                                    @if (is_array($b) && !empty($b))
                                        @foreach ($b as $key => $record)
                                            <tr>
                                                <td colspan="3">
                                                    <div class="col-md-6 mb-2"><strong>Authorised Individual
                                                            {{ $key + 1 }}</strong>
                                                    </div>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Name</strong>
                                                    <p> {{ $record->name }}</p>
                                                </td>
                                                <td>
                                                    <strong>Phone Number</strong>
                                                    <p>{{ $record->phone_number }}</p>
                                                </td>
                                                <td>
                                                    <strong>Email</strong>
                                                    <p>{{ $record->email }}</p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endif


                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            {{-- The documents card --}}
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title"> Application Documents </h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if (isset($application->passport) && $application->passport != null)
                            <div class="col-md-6 mt-2"><strong>Passport</strong></div>
                            <div class="col-md-6 mb-2">
                                @foreach (json_decode($application->passport) as $key => $passport)
                                    <div class="row">
                                        <div class="col-md-4 mt-2">File - {{ $key + 1 }}</div>
                                        <div class="col-md-8 mt-2">
                                            <a href="{{ getS3Url($passport) }}" target="_blank"
                                                class="btn btn-primary btn-sm">View</a>
                                            <a href="{{ route('downloadRpApplicationDocumentsUpload', ['file' => $passport]) }}"
                                                class="btn btn-danger btn-sm">Download</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        @if (isset($application->utility_bill) && $application->utility_bill != null)
                            @if (isset($application->utility_bill))
                                <div class="col-md-6 mt-2"><strong>Utility Bill</strong></div>
                                <div class="col-md-6 mb-2">
                                    @foreach (json_decode($application->utility_bill) as $key => $utilityBill)
                                        <div class="row">
                                            <div class="col-md-4 mt-2">File - {{ $key + 1 }}</div>
                                            <div class="col-md-8 mt-2">
                                                <a href="{{ getS3Url($utilityBill) }}" target="_blank"
                                                    class="btn btn-primary btn-sm">View</a>
                                                <a href="{{ route('downloadRpApplicationDocumentsUpload', ['file' => $utilityBill]) }}"
                                                    class="btn btn-danger btn-sm">Download</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="row">
                        @if (isset($application->company_incorporation_certificate) && $application->company_incorporation_certificate != null)
                            <div class="col-md-6 mt-2"><strong>Articles Of Incorporation</strong></div>
                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    <div class="col-md-4 mt-2"></div>
                                    <div class="col-md-8 mt-2">
                                        <a href="{{ getS3Url($application->company_incorporation_certificate) }}"
                                            target="_blank" class="btn btn-primary btn-sm">View</a>
                                        <a href="{{ route('downloadRpApplicationDocumentsUpload', ['file' => $application->company_incorporation_certificate]) }}"
                                            class="btn btn-danger btn-sm">Download</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        @if (isset($application->tax_id) && $application->tax_id != null)
                            <div class="col-md-6 mt-2"><strong>Tax ID</strong></div>
                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    <div class="col-md-4 mt-2"></div>
                                    <div class="col-md-8 mt-2">
                                        <a href="{{ getS3Url($application->tax_id) }}" target="_blank"
                                            class="btn btn-primary btn-sm">View</a>
                                        <a href="{{ route('downloadRpApplicationDocumentsUpload', ['file' => $application->tax_id]) }}"
                                            class="btn btn-danger btn-sm">Download</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-xxl-4">

            <div class="card mt-1">
                <div class="card-header">
                    <h4 class="card-title">Actions</h4>
                </div>
                <div class="card-body">
                    @if (auth()->guard('admin')->user()->can(['update-rp-application']))
                        <div class="d-grid gap-2">
                            @if ($application->status == '0' || $application->status == '3')
                                <button class="btn btn-success   done" id="applicationApprove"
                                    data-link="{{ route('application-rp-approve') }}"
                                    data-id="{{ $application->id }}">Approve</button>
                                <button type="button" class="btn btn-danger  " id="reject" data-bs-toggle="modal"
                                    href="#rejectModel" data-id="{{ $application->id }}">Reject</button>
                                @if ($application->status == '0')
                                    <button type="button" class="btn btn-warning  " id="reassign" data-bs-toggle="modal"
                                        href="#reassignModel" data-id="{{ $application->id }}">Reassign</button>
                                @endif
                            @endif
                            <a href="{{ route('application-rp.edit', ['id' => $application->id]) }}"
                                class="btn btn-primary  " title="Edit">Edit</a>
                        </div>

                    @endif
                </div>
            </div>

            <div class="card  mt-1 height-auto">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Status</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            @if ($application->status == '0')
                                Pending
                            @elseif($application->status == '1')
                                Approved
                            @elseif($application->status == '2')
                                Rejected
                            @elseif($application->status == '3')
                                Reassigned
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card  mt-1 height-auto">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Agreement Sent</h4>
                    </div>
                </div>
                <div class="card-body">
                    @if ($application->status == 1)
                        <div class="row">
                            <div class="col-xl-6 col-xxl-5">
                                <a href="{{ isset($application->agent->agreementDocument->sent_files) ? getS3Url($application->agent->agreementDocument->sent_files) : '#' }}"
                                    target="_blank" class="btn btn-primary btn-block mt-1 btn-sm">View</a>
                            </div>
                            <div class="col-xl-6 col-xxl-5">
                                <a href="{{ isset($application->agent->agreementDocument->sent_files) ? route('downloadDocumentsUploadeAdmin', ['file' => $application->agent->agreementDocument->sent_files]) : '#' }}"
                                    class="btn btn-danger  mt-1 btn-sm">Download</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card  mt-1 height-auto">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Signed Agreement</h4>
                    </div>
                </div>
                <div class="card-body">
                    @if (!empty($application->agent->agreementDocument->files))
                        <div class="custom-control custom-checkbox custom-control-inline mr-0 mb-2">
                            @if ($application->agent->agreement_status == 2)
                                <input type="checkbox" id="is_received_{{ $application->agent->id }}"
                                    data-rp-id="{{ $application->agent->id }}"
                                    data-link="{{ route('rp-agreement-received') }}" name="is_agreement_received"
                                    class="custom-control-input is_received" value="1" checked disabled>
                            @else
                                <input type="checkbox" id="is_received_{{ $application->agent->id }}"
                                    data-rp-id="{{ $application->agent->id }}"
                                    data-link="{{ route('rp-agreement-received') }}" name="is_agreement_received"
                                    class="custom-control-input is_received" value="0">
                            @endif
                            <label class="custom-control-label" for="is_received_{{ $application->agent->id }}">Agreement
                                Received</label>
                        </div>
                    @endif
                    @if (!empty($application->agent->agreementDocument->files))
                        <div class="row">
                            <div class="col-xl-6 col-xxl-3">
                                <a href="{{ getS3Url($application->agent->agreementDocument->files) }}" target="_blank"
                                    class="btn btn-primary btn-block mt-1 btn-sm">View</a>
                            </div>
                            <div class="col-xl-6 col-xxl-4">
                                <a href="{{ route('downloadDocumentsUploadeAdmin', ['file' => $application->agent->agreementDocument->files]) }}"
                                    class="btn btn-danger  mt-1 btn-sm">Download</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @if ($application->agent->agreement_status == 2)
                <div class="card  mt-1 height-auto">
                    <div class="card-header">
                        <div class="iq-header-title">
                            <h4 class="card-title">Cross Signed Agreement</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (empty($application->agent->agreementDocument->cross_signed_agreement))
                            <form action="{{ route('rp-cross-signed-agreement-sent') }}" method="post"
                                enctype="multipart/form-data" id="cross-signed-agreement-form" class="form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="file" class="form-control" id="cross_signed_agreement"
                                            name="cross_signed_agreement">
                                        <input type="hidden" id="rp_id" name="rp_id"
                                            value="{{ $application->agent->id }}">
                                        <input type="hidden" id="application_id" name="application_id"
                                            value="{{ $application->id }}">
                                        <div class="clearfix"></div>
                                        @if ($errors->has('cross_signed_agreement'))
                                            <span class="text-danger help-block form-error">
                                                {{ $errors->first('cross_signed_agreement') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-12 mt-1">
                                        <button type="submit" class="btn btn-primary">Upload File</button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ getS3Url($application->agent->agreementDocument->cross_signed_agreement) }}"
                                        target="_blank" class="btn btn-primary btn-block mt-1 btn-sm">View</a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('downloadDocumentsUploadeAdmin', ['file' => $application->agent->agreementDocument->cross_signed_agreement]) }}"
                                        class="btn btn-danger  mt-1 btn-sm">Download</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            {{-- <div class="card  mt-1 height-auto">
            <div class="card-header">
                <div class="iq-header-title">
                    <h4 class="card-title">Agreement Status</h4>
                </div>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-12 mt-2">
                        @if ($application->agent->agreement_status == 0)
                        <span class="badge badge-sm badge-danger">Pending</span>
                        @elseif($application->agent->agreement_status == 1)
                        <span class="badge badge-sm badge-success">Sent</span>
                        @elseif($application->agent->agreement_status == 2)
                        <span class="badge badge-sm badge-success">Received</span>
                        @else
                        <span class="badge badge-sm badge-success">Reassign</span>
                        @endif
                    </div>
                </div>
                @if ($application->agent->agreement_status == 1 && (isset($application->agent->agreementDocument) && $application->agent->agreementDocument->files))
                <div class="custom-control custom-checkbox custom-control-inline mr-0">
                    <input type="checkbox" id="is_received_{{ $application->agent->id }}"
                        data-rp-id="{{$application->agent->id }}" data-link="{{ route('rp-agreement-received') }}"
                        name="is_agreement_received" class="custom-control-input is_received" value="0">
                    <label class="custom-control-label" for="is_received_{{$application->agent->id }}">Agreement
                        Received</label>
                    </label>

                    <div class="row">
                        <div class="col-xl-5 col-xxl-6">
                            <strong>Agreement Sent</strong>
                        </div>
                        <div class="col-xl-3 col-xxl-3">
                            <a href="{{ getS3Url($application->agent->agreementDocument->sent_files) }}" target="_blank"
                                class="btn btn-primary btn-block mt-1 btn-sm">View</a>
                        </div>
                        <div class="col-xl-4 col-xxl-4">
                            <a href="{{ route('downloadDocumentsUploadeAdmin',['file' => $application->agent->agreementDocument->sent_files]) }}"
                                class="btn btn-danger  mt-1 btn-sm">Download</a>
                        </div>
                    </div>
                    @endif
                    @if (!empty($application->agent->agreementDocument->files))
                    <div class="row">
                        <div class="col-xl-5 col-xxl-6">
                            <strong>Signed Agreement</strong>
                        </div>
                        <div class="col-xl-3 col-xxl-3">
                            <a href="{{ getS3Url($application->agent->agreementDocument->files) }}" target="_blank"
                                class="btn btn-primary btn-block mt-1 btn-sm">View</a>
                        </div>
                        <div class="col-xl-4 col-xxl-4">
                            <a href="{{ route('downloadDocumentsUploadeAdmin',['file' => $application->agent->agreementDocument->files]) }}"
                                class="btn btn-danger  mt-1 btn-sm">Download</a>
                        </div>
                    </div>
                    @endif



                    @if (!empty($application->files))
                    <div class="row">
                        <div class="col-xl-6 col-xxl-5">
                            <a href="{{ getS3Url($application->agent->agreementDocument->files) }}" target="_blank"
                                class="btn btn-primary btn-xxs">View</a>
                        </div>
                        <div class="col-xl-6 col-xxl-5">
                            <a href="{{ route('downloadDocumentsUploadeAdmin',['file' => $application->agent->agreementDocument->files]) }}"
                                class="btn btn-primary btn-xxs">Download</a>
                        </div>
                    </div>
                    @endif


                </div>
            </div>
        </div> --}}


        </div>
    </div>

    {{-- ReAssign Model --}}
    <div class="modal fade bs-example-modal-center" id="reassignModel" tabindex="-1" role="reassignModel"
        aria-hidden="true" style="display: none; padding-right: 15px;">
        <form action="{{ route('application-rp-reassign') }}" method="post" id="reassignForm">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $application->id }}">
            <div class="modal-dialog modal-lg modal-dialog modal-lg-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Reason For Reassign</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="reassign_reason" id="reassign_reason" rows="3" placeholder="Enter here"
                                required></textarea>
                            <span class="help-block text-danger">
                                <strong id="reassign_reason_error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success btn-sm" id="reassignsubmit">Submit</button>
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
        <form action="{{ route('application-rp-reject') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $application->id }}">
            <div class="modal-dialog modal-lg modal-dialog modal-lg-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Reason For Reject</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group form-dark">
                            <label>Reject Reason</label>
                            <textarea class="form-control" name="reject_reason" id="reject_reason" rows="3"
                                placeholder="Write Here Your Reject Reason"></textarea>
                            <span class="help-block text-danger">
                                <strong id="reject_reason_error"></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="rejectsubmit" class="btn btn-success btn-sm">Submit</button>
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
    <script src="{{ storage_asset('themeAdmin/custom_js/RP/custom.js') }}"></script>
    <script type="text/javascript">
        $('#reassignsubmit').on('click', function(e) {
            if ($('#reassign_reason').val() == "") {
                e.preventDefault();
                swal("Error!", "Please provide reason for reassign!", "error");
                return false;
            }
        });
        $('#rejectsubmit').on('click', function(e) {
            if ($('#reject_reason').val() == "") {
                e.preventDefault();
                swal("Error!", "Please provide reason for reject!", "error");
                return false;
            }
        });
    </script>
    <script type="text/javascript">
        $('#applicationApprove').on('click', function() {
            let apiUrl = $(this).data('link');
            var id = $(this).data('id');
            var add_buy_rate = 0;
            var add_buy_rate_master = 0;
            var add_buy_rate_amex = 0;
            var add_buy_rate_discover = 0;

            // var add_buy_rate = document.getElementById('add_buy_rate').value;
            // if (add_buy_rate == '') {
            //     $('#add_buy_rate_error').html('This field is required.');
            // } else {
            //     $('#add_buy_rate_error').html('');
            // }
            // var add_buy_rate_master = document.getElementById('add_buy_rate_master').value;
            // if (add_buy_rate_master == '') {
            //     $('#add_buy_rate_master_error').html('This field is required.');
            // } else {
            //     $('#add_buy_rate_master_error').html('');
            // }
            // var add_buy_rate_amex = document.getElementById('add_buy_rate_amex').value;
            // if (add_buy_rate_amex == '') {
            //     $('#add_buy_rate_amex_error').html('This field is required.');
            // } else {
            //     $('#add_buy_rate_amex_error').html('');
            // }
            // var add_buy_rate_discover = document.getElementById('add_buy_rate_discover').value;
            // if (add_buy_rate_discover == '') {
            //     $('#add_buy_rate_discover_error').html('This field is required.');
            // } else {
            //     $('#add_buy_rate_discover_error').html('');
            // }



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
                $("#applicationApprove").prop("disabled", true)
                $("#applicationApprove").text('Processing...')
                $.ajax({
                    url: apiUrl,
                    type: 'POST',
                    data: {
                        'id': id,
                        '_token': CSRF_TOKEN,
                        'add_buy_rate': add_buy_rate,
                        'add_buy_rate_amex': add_buy_rate_amex,
                        'add_buy_rate_master': add_buy_rate_master,
                        'add_buy_rate_discover': add_buy_rate_discover,
                    },
                    success: function(data) {
                        if (data.success == '1') {
                            toastr.success("Application Approved Successfully!");
                            setTimeout(function() {
                                location.reload();
                            }, 800);
                        } else {
                            $("#applicationApprove").prop("disabled", false)
                            $("#applicationApprove").text('Approve')
                            swal("Error!", "Something went wrong, try again!", "error");
                        }
                    },
                });
            })


        });
    </script>
@endsection
