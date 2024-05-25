@extends($agentUserTheme)
@section('title')
    Application Edit
@endsection
@section('breadcrumbTitle')
    <a href="{{ route('rp.dashboard') }}">Dashboard</a> / <a href="{{ route('rp.user-management') }}">Merchant
        Management</a> / Application Edit
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Application Edit</h4>
                    </div>
                    <a href="{{ route('rp.user-management') }}" class="btn btn-primary btn-sm rounded"> <i
                            class="fa fa-arrow-left" aria-hidden="true"></i></a>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        {{ Form::model($data, ['route' => ['user-management-application-update', $data->user_id], 'method' => 'PUT', 'class' => 'form-dark', 'enctype' => 'multipart/form-data', 'id' => 'application-form']) }}
                        @csrf
                        <input type="hidden" name="application_id" value="{{ $data->id }}">
                        <div class="row">
                            @include('partials.application.applicationFrom', ['isEdit' => true])

                            <div class="col-md-12 mb-3 mt-3">
                                <h5>Merchant Documents <small class="text-primary">The document size should not exceed
                                        35MB</small>
                                </h5>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Passport <span class="text-danger">*</span></label>
                                    {{-- <i class="fa fa-info tol-info" data-bs-toggle="tooltip" data-placement="top" title="In order to add multiple documents , please click on 'CTRL' and select the multiple files."></i> --}}
                                    <div class="row mx-auto">
                                        <div class="col-lg-12 p-0">
                                            <div class="custom-file">
                                                <input type="file" class="form-control" id="validationCustomFile1"
                                                    name="passport[]">

                                            </div>
                                            <div class="dynamicPassportFields"></div>
                                            @if ($errors->has('passport.*'))
                                                <span class="text-danger help-block form-error">
                                                    {{ $errors->first('passport.*') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        @if (isset($data->passport))
                                            @foreach (json_decode($data->passport) as $key => $value)
                                                <div class="col-md-12 mt-2">
                                                    <p class="pull-left mb-0">File - {{ $key + 1 }}</p>
                                                    <a href="{{ getS3Url($value) }}" target="_blank"
                                                        class="btn btn-danger mr-4 btn-sm pull-right">View</a>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Utility Bill <span class="text-danger">*</span></label>
                                    <div class="row mx-auto">
                                        <div class="col-lg-12 p-0">
                                            <div class="custom-file">
                                                <input type="file" class="form-control" id="validationCustomFile5"
                                                    name="utility_bill[]">
                                            </div>
                                            <div class="dynamicUtilityBillFields"></div>
                                            @if ($errors->has('utility_bill.*'))
                                                <span class="text-danger help-block form-error">
                                                    {{ $errors->first('utility_bill.*') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        @if (isset($data->utility_bill))
                                            @foreach (json_decode($data->utility_bill) as $key => $value)
                                                <div class="col-md-12 mt-2">
                                                    <p class="pull-left mb-0">File - {{ $key + 1 }}</p>
                                                    <a href="{{ getS3Url($value) }}" target="_blank"
                                                        class="btn btn-danger btn-sm mr-4 pull-right">View</a>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Company's Bank Statement (last 180 days) <span
                                            class="text-danger">*</span></label>
                                    <div class="row mx-auto">
                                        <div class="col-lg-12 p-0">
                                            <div class="custom-file">
                                                <input type="file" class="form-control" id="validationCustomFile4"
                                                    name="latest_bank_account_statement[]">
                                            </div>
                                            <div class="dynamicBankStatementFields"></div>
                                            @if ($errors->has('latest_bank_account_statement.*'))
                                                <span class="text-danger help-block form-error">
                                                    {{ $errors->first('latest_bank_account_statement.*') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="row mt-2">
                                        @if (isset($data->latest_bank_account_statement))
                                            @foreach (json_decode($data->latest_bank_account_statement) as $key => $value)
                                                <div class="col-md-12 mt-2">
                                                    <p class="pull-left mb-0">File - {{ $key + 1 }}</p>
                                                    <a href="{{ getS3Url($value) }}" target="_blank"
                                                        class="btn btn-danger btn-sm mr-4 pull-right">View</a>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Articles Of Incorporation <span class="text-danger">*</span></label>
                                    <div class="row mx-auto">
                                        <div class="col-9 col-md-9 p-0">
                                            <div class="custom-file">
                                                <input type="file" class="form-control" id="validationCustomFile2"
                                                    name="company_incorporation_certificate">
                                                @if ($errors->has('company_incorporation_certificate'))
                                                    <span class="text-danger help-block form-error">
                                                        {{ $errors->first('company_incorporation_certificate') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-2 col-md-3">
                                            <a href="{{ getS3Url($data->company_incorporation_certificate) }}"
                                                target="_blank" class="btn btn-danger btn-sm">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>UBO's Bank Statement (last 90 days)</label>
                                    <div class="row mx-auto">
                                        <div class="col-md-9 p-0">
                                            <div class="custom-file">
                                                <input type="file" class="form-control" id="validationCustomFile9"
                                                    name="owner_personal_bank_statement">
                                            </div>
                                        </div>
                                        @if (isset($data->owner_personal_bank_statement))
                                            <div class="col-md-3">
                                                <a href="{{ getS3Url($data->owner_personal_bank_statement) }}"
                                                    target="_blank" class="btn btn-danger btn-sm">View</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Processing History (if any)</label>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="custom-file">
                                                <input type="file" class="form-control"
                                                    name="previous_processing_statement[]" multiple>
                                            </div>

                                            <div class="row">
                                                @if (isset($data->previous_processing_statement) && $data->previous_processing_statement != null)
                                                    @php
                                                        $previous_processing_statement_files = json_decode($data->previous_processing_statement);
                                                    @endphp
                                                    @php
                                                        $count = 1;
                                                    @endphp
                                                    @foreach ($previous_processing_statement_files as $key => $value)
                                                        <div class="col-md-12 mt-2">
                                                            <p class="pull-left mb-0">File - {{ $count }}</p>
                                                            <a href="{{ getS3Url($value) }}" target="_blank"
                                                                class="btn btn-danger mr-4 btn-sm pull-right">View</a>
                                                            @php
                                                                $count++;
                                                            @endphp
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>MOA (Memorandum of Association)</label>
                                    <div class="row mx-auto">
                                        <div class="col-md-9 p-0">
                                            <div class="custom-file">
                                                <input type="file" class="form-control extra-document"
                                                    name="moa_document">

                                            </div>
                                            @if ($errors->has('moa_document'))
                                                <span class="text-danger help-block form-error">
                                                    {{ $errors->first('moa_document') }}
                                                </span>
                                            @endif
                                        </div>
                                        @if (isset($data->moa_document))
                                            <div class="col-md-3">
                                                <a href="{{ getS3Url($data->moa_document) }}" target="_blank"
                                                    class="btn btn-danger btn-sm">View</a>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Domain Ownership <span class="text-danger">*</span></label>
                                    <div class="row mx-auto">
                                        <div class="col-9 col-md-9 p-0">
                                            <div class="custom-file">
                                                <input type="file" class="form-control" name="domain_ownership">
                                                @if ($errors->has('domain_ownership'))
                                                    <span class="text-danger help-block form-error">
                                                        {{ $errors->first('domain_ownership') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-2 col-md-3">
                                            <a href="{{ getS3Url($data->domain_ownership) }}" target="_blank"
                                                class="btn btn-danger btn-sm">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Additional Document</label>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="custom-file">
                                                <input type="file" class="form-control extra_document"
                                                    id="validationCustomFile8" name="extra_document[]" multiple>
                                            </div>
                                            <div class="row">
                                                @if (isset($data->extra_document) && $data->extra_document != null)
                                                    @php
                                                        $extra_document_files = json_decode($data->extra_document);
                                                    @endphp
                                                    @php
                                                        $count = 1;
                                                    @endphp

                                                    @foreach ($extra_document_files as $key => $value)
                                                        <div class="col-md-12 mt-2">
                                                            <p class="pull-left mb-0">File - {{ $count }}</p>
                                                            <a href="{{ getS3Url($value) }}" target="_blank"
                                                                class="btn btn-danger mr-4 btn-sm pull-right">View</a>
                                                            @php
                                                                $count++;
                                                            @endphp
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('my-application') }}" class="btn btn-danger"> Cancel </a>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/jquery.validate.min.js') }}"></script>
    <script>
        var isEditPage = true;
    </script>
    <script src="{{ storage_asset('themeAdmin/custom_js/front/applications/applications.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/custom_js/front/applications/edit.js') }}"></script>
@endsection
