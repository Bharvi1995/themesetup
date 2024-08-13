@extends('layouts.admin.default')
@section('title')
    Create Application
@endsection
@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('users-management') }}">Merchant Management</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Create Applications</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Create Applications</h6>
    </nav>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Create Application</h4>
                    </div>
                </div>
                <form action="{{ route('admin.applications.store',$id) }}" method="post" enctype="multipart/form-data" id="application-form" class="form form-dark">
                    @csrf
                <div class="card-body">
                    <input type="hidden" name="user_id" value="{{ $id }}">
                    <div class="row mt-1">
                        @include('partials.application.applicationFrom', ['isEdit' => false])
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Passport</label>
                                <div class="row mx-auto">
                                    <div class="col-lg-12 p-0">
                                        <div class="custom-file">
                                            <input type="file" class="form-control" multiple id="passport"
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
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Utility Bill </label>
                                <div class="row mx-auto">
                                    <div class="col-lg-12 p-0">
                                        <div class="custom-file">
                                            <input type="file" class="form-control" id="utility_bill"
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
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Company's Bank Statement (last 180 days)</label>
                                <div class="row mx-auto">
                                    <div class="col-lg-12 p-0">
                                        <div class="custom-file">
                                            <input type="file" class="form-control" id="latest_bank_account_statement"
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
                                
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Articles Of Incorporation</label>
                                <div class="row mx-auto">
                                    <div class="col-12 col-md-12 p-0">
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
                                   
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>UBO's Bank Statement (last 90 days)</label>
                                <div class="row mx-auto">
                                    <div class="col-12 col-md-12 p-0">
                                        <div class="custom-file">
                                            <input type="file" class="form-control" id="owner_personal_bank_statement"
                                                name="owner_personal_bank_statement">

                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>MOA (Memorandum of Association)</label>
                                
                                <div class="custom-file">
                                    <input type="file" class="form-control extra-document" name="moa_document">

                                </div>
                                @if ($errors->has('moa_document'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('moa_document') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Domain Ownership</label>
                                
                                <div class="custom-file">
                                    <input type="file" class="form-control" name="domain_ownership">

                                </div>
                                @if ($errors->has('domain_ownership'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('domain_ownership') }}
                                    </span>
                                @endif
                                
                            </div>
                        </div>
                    </div>                    
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('admin.applications.list') }}" class=" btn btn-danger "> Cancel</a>
                    <button type="submit" class="btn btn-primary ">Submit</button>
                          
                </div>
                </form>
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
