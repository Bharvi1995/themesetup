@extends('layouts.admin.default')
@section('title')
    Edit Application
@endsection
@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.applications.list') }}">Applications</a>
    / Edit
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Edit Application</h4>
                    </div>
                    <a href="{{ route('admin.applications.list') }}" class="btn btn-primary btn-sm"> <i
                            class="fa fa-arrow-left" aria-hidden="true"></i></a>
                </div>
                {{ Form::model($data, ['route' => ['admin.applications.update', $data->id], 'method' => 'PUT', 'class' => 'form-dark w-100', 'enctype' => 'multipart/form-data']) }}
                    @csrf
                <div class="card-body">
                    
                    <input type=hidden name="id" value="{{ $data->id }}">
                    <input type="hidden" name="user_id" value="{{ $data->user_id }}">
                    <div class="row mt-1">
                        @include('partials.application.applicationFrom', ['isEdit' => true])
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Passport <span class="text-danger">*</span></label>
                                <div class="row mx-auto">
                                    <div class="col-lg-12 p-0">
                                        <div class="custom-file">
                                            <input type="file" class="form-control" multiple id="validationCustomFile1"
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
                                    @if ($data->passport != null)
                                        @foreach (json_decode($data->passport) as $key => $value)
                                            <div class="col-md-12 mt-2">
                                                <p class="pull-left mb-0">File - {{ $key + 1 }}</p>
                                                <a href="{{ getS3Url($value) }}" target="_blank"
                                                    class="mr-4 btn btn-primary btn-icon pull-right"><i class="fa fa-eye" aria-hidden="true"></i></a>
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
                                    @if ($data->utility_bill != null)
                                        @foreach (json_decode($data->utility_bill) as $key => $value)
                                            <div class="col-md-12 mt-2">
                                                <p class="pull-left mb-0">File - {{ $key + 1 }}</p>
                                                <a href="{{ getS3Url($value) }}" target="_blank"
                                                    class="btn mr-4 btn-primary btn-icon pull-right"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Company's Bank Statement (last 180 days)<span class="text-danger">*</span></label>
                                <div class="row mx-auto">
                                    <div class="col-lg-12 p-0">
                                        <div class="custom-file">
                                            <input type="file" class="form-control" id="validationCustomFile4"
                                                name="latest_bank_account_statement[]">

                                        </div>
                                        <div class="dynamicBankStatementFields"></div>
                                        @if ($errors->has('latest_bank_account_statement.*'))
                                            <span class="text-danger help-block form-error">
                                                <span>{{ $errors->first('latest_bank_account_statement.*') }}</span>
                                            </span>
                                        @endif
                                    </div>

                                </div>
                                <div class="row mt-2">
                                    @if ($data->latest_bank_account_statement != null)
                                        @foreach (json_decode($data->latest_bank_account_statement) as $key => $value)
                                            <div class="col-md-12 mt-2">
                                                <p class="pull-left mb-0">File - {{ $key + 1 }}</p>
                                                <a href="{{ getS3Url($value) }}" target="_blank"
                                                    class="btn mr-4 btn-primary btn-icon pull-right"><i class="fa fa-eye" aria-hidden="true"></i></a>
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
                                    <div class="col-10 col-md-10 p-0">
                                        <div class="custom-file">
                                            <input type="file" class="form-control" id="validationCustomFile2"
                                                name="company_incorporation_certificate">

                                            @if ($errors->has('company_incorporation_certificate'))
                                                <span class="text-danger help-block form-error">
                                                    <span>{{ $errors->first('company_incorporation_certificate') }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($data->company_incorporation_certificate != null)
                                        <div class="col-2 col-md-2">
                                            <a href="{{ getS3Url($data->company_incorporation_certificate) }}"
                                                target="_blank" class="btn btn-primary btn-icon"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>UBO's Bank Statement (last 90 days)</label>
                                <div class="row mx-auto">
                                    <div class="col-10 col-md-10 p-0">
                                        <div class="custom-file">
                                            <input type="file" class="form-control" id="validationCustomFile9"
                                                name="owner_personal_bank_statement">

                                        </div>
                                    </div>
                                    @if ($data->owner_personal_bank_statement != null)
                                        <div class="col-2 col-md-2">
                                            <a href="{{ getS3Url($data->owner_personal_bank_statement) }}"
                                                target="_blank" class="btn btn-primary btn-icon"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>MOA (Memorandum of Association)</label>
                                @if ($data->moa_document != null)
                                    <div class="row">
                                        <div class="col-md-10 p-0">
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

                                        <div class="col-md-2">
                                            <a href="{{ getS3Url($data->moa_document) }}" target="_blank"
                                                class="btn btn-primary btn-icon"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                @else
                                    <div class="custom-file">
                                        <input type="file" class="form-control extra-document" name="moa_document">

                                    </div>
                                    @if ($errors->has('moa_document'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('moa_document') }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Domain Ownership</label>
                                @if ($data->domain_ownership != null)
                                    <div class="row">
                                        <div class="col-md-10 p-0">
                                            <div class="custom-file">
                                                <input type="file" class="form-control" name="domain_ownership">

                                            </div>
                                            @if ($errors->has('domain_ownership'))
                                                <span class="text-danger help-block form-error">
                                                    {{ $errors->first('domain_ownership') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-md-2">
                                            <a href="{{ getS3Url($data->domain_ownership) }}" target="_blank"
                                                class="btn btn-primary btn-icon"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        </div>

                                    </div>
                                @else
                                    <div class="custom-file">
                                        <input type="file" class="form-control" name="domain_ownership">

                                    </div>
                                    @if ($errors->has('domain_ownership'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('domain_ownership') }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                     <a href="{{ route('admin.applications.list') }}" class="btn btn-danger "> Cancel</a> 
                    <button type="submit" class="btn btn-primary ">Submit</button>
                           
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/jquery.validate.min.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/custom_js/front/applications/edit.js') }}"></script>
    <script>
        var isEditPage = true;
    </script>
    <script src="{{ storage_asset('themeAdmin/custom_js/front/applications/applications.js') }}"></script>
    <script type="text/javascript">

        $('.form-control').on('select2:open', function(e) {
            var y = $(window).scrollTop();
            $(window).scrollTop(y + 0.1);
        });
    </script>

    <script></script>
@endsection
