@extends($agentUserTheme)
@section('title')
    Application Create
@endsection
@section('breadcrumbTitle')
    <a href="{{ route('rp.dashboard') }}">Dashboard</a> / <a href="{{ route('rp.user-management') }}">Merchant
        Management</a> / Application Create
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Application Create</h4>
                    </div>
                    <a href="{{ route('rp.user-management') }}" class="btn btn-primary btn-sm rounded"> <i
                            class="fa fa-arrow-left" aria-hidden="true"></i></a>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <form action="{{ route('user-management-application-store', $id) }}" method="post"
                            enctype="multipart/form-data" id="application-form" class="form-dark">
                            @csrf
                            <div class="row">
                                @include('partials.application.applicationFrom', ['isEdit' => false])
                                <div class="col-md-12 mb-3 mt-3">
                                    <h5>Merchant Documents <small class="text-primary">The document size should not exceed
                                            35MB</small>
                                    </h5>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Passport <span class="text-danger">*</span></label>
                                    {{-- <i class="fa fa-info tol-info" data-bs-toggle="tooltip" data-placement="top"
                                title="In order to add multiple documents , please click on 'CTRL' and select the multiple files."></i> --}}
                                    <div class="custom-file">
                                        <input type="file" class="form-control" id="validationCustomFile1"
                                            name="passport[]">

                                    </div>
                                    <div class="dynamicPassportFields"></div>
                                    @if ($errors->has('passport'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('passport') }}
                                        </span>
                                    @endif
                                    @if ($errors->has('passport.*'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('passport.*') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Utility Bill <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" class="form-control" id="validationCustomFile5"
                                            name="utility_bill[]">

                                    </div>
                                    <div class="dynamicUtilityBillFields"></div>
                                    @if ($errors->has('utility_bill'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('utility_bill') }}
                                        </span>
                                    @endif
                                    @if ($errors->has('utility_bill.*'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('utility_bill.*') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Company's Bank Statement (last 180 days) <span
                                            class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" class="form-control" id="validationCustomFile4"
                                            name="latest_bank_account_statement[]">

                                    </div>
                                    <div class="dynamicBankStatementFields"></div>
                                    @if ($errors->has('latest_bank_account_statement'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('latest_bank_account_statement') }}
                                        </span>
                                    @endif
                                    @if ($errors->has('latest_bank_account_statement.*'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('latest_bank_account_statement.*') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Domain Ownership <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" class="form-control" name="domain_ownership">

                                        @if ($errors->has('domain_ownership'))
                                            <span class="text-danger help-block form-error">
                                                {{ $errors->first('domain_ownership') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Articles Of Incorporation <span class="text-danger">*</span></label>
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
                                <div class="form-group col-md-4">
                                    <label>UBO's Bank Statement (last 90 days)</label>
                                    <div class="custom-file">
                                        <input type="file" class="form-control" id="validationCustomFile9"
                                            name="owner_personal_bank_statement">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Processing History (if any)</label>
                                    <div class="custom-file">
                                        <input type="file" class="form-control" id="validationCustomFile8"
                                            name="previous_processing_statement[]" multiple>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
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
                                <div class="form-group col-md-4">
                                    <label>Additional Documents</label>
                                    <div class="custom-file">
                                        <input type="file" class="form-control extra-document" name="extra_document[]"
                                            multiple>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <hr>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="{{ route('rp.user-management') }}" class="btn btn-danger"> Cancel </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/jquery.validate.min.js') }}"></script>
    <script>
        var isEditPage = false;
    </script>
    <script src="{{ storage_asset('themeAdmin/custom_js/front/applications/applications.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/custom_js/front/applications/start.js') }}"></script>

    <script>
        $(document).ready(function() {
            var value = $('.company_licenseOldValue').val()
            var oldIndustryTypeVal = $('.oldIndustryType').val()
            var oldProccessingCountryVal = $('.oldProccessingCountryVal').val()
            if (oldProccessingCountryVal) {
                getProcessingCountryEdit(JSON.parse(oldProccessingCountryVal))
            }
            if (value) {
                getLicenseStatus(value)
            }
            otherIndustryType(oldIndustryTypeVal)
        });

        function getProcessingCountry(sel) {
            var opts = [],
                opt;
            var len = sel.options.length;
            for (var i = 0; i < len; i++) {
                opt = sel.options[i];
                if (opt.selected) {
                    opts.push(opt.value);
                }
            }

            getProcessingCountryEdit(opts)

        }

        function otherIndustryType(val) {
            if (val == 28) {
                $('.showOtherIndustryInput').removeClass('d-none')
            } else {
                $('.showOtherIndustryInputBox').val('')
                $('.showOtherIndustryInput').addClass('d-none')
            }
        }

        function getLicenseStatus(val) {
            if (val == 0) {
                $('.toggleLicenceDocs').removeClass('d-none')
            } else {
                $('.toggleLicenceDocs').addClass('d-none')
            }
        }

        function getProcessingCountryEdit(arr) {

            var isExist = arr.filter(function(item) {
                return item == 'Others'
            })
            if (isExist.length > 0) {
                $('.otherProcessingInput').removeClass('d-none')
            } else {
                $('.otherProcessingInput').addClass('d-none')
            }
        }
    </script>
@endsection
