@extends('layouts.user.default')

@section('title')
My Application Create
@endsection

@section('breadcrumbTitle')
<a href="{{ route('dashboardPage') }}">Dashboard</a> / <a href="{{ route('my-application') }}">My Application</a> /
Create
@endsection

@section('customeStyle')
<style type="text/css">
    .error {
        color: #bd2525;
    }

    .invalid-feedback {
        font-size: 100% !important;
    }

    .selectize-input,
    .selectize-control.single .selectize-input.input-active {
        background: #2B2B2B !important;
        color: #b7aeaf !important;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12 col-xxl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">My Application</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="{{ route('start-my-application-store') }}" method="post" enctype="multipart/form-data"
                        id="application-form" class="form form-dark">
                        @csrf
                        <div class="form-row">
                            @include('partials.application.applicationFrom' ,['isEdit' => false])

                            <div class="col-md-12 mb-3 mt-3">
                                <h5>My Documents <small class="text-info">The document size should not exceed
                                        35MB</small> </h5>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Passport <span class="text-danger">*</span></label>
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
                            <div class="form-group col-md-4">
                                <label>Utility Bill <span class="text-danger">*</span></label>
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
                            <div class="form-group col-md-4">
                                <label>Company's Bank Statement (last 180 days) <span
                                        class="text-danger">*</span></label>
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
                                <label>Domain Ownership <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="form-control" name="domain_ownership">
                                </div>
                                @if ($errors->has('domain_ownership'))
                                <span class="text-danger help-block form-error">
                                    {{ $errors->first('domain_ownership') }}
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

                            <div class="col-md-12 text-right mt-2">
                                <button name="action" type="submit" id="submitbutton" class="btn btn-primary btn-raised"
                                    value="saveDraft">Save Draft</button>
                                <button name="action" type="submit" id="submit_button"
                                    class="btn btn-danger btn-raised" value="save">Submit</button>
                                <a href="" class="btn btn-danger">Cancel</a>
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
    // document.getElementById('submitbutton').onclick = function() {
    //     var form = document.getElementById('application-form');
    //     form.submit();
    // }

    $(document).ready(function(){
        var value = $('.company_licenseOldValue').val()
        var oldIndustryTypeVal = $('.oldIndustryType').val()
        var oldProccessingCountryVal = $('.oldProccessingCountryVal').val()
       if(oldProccessingCountryVal){
           getProcessingCountryEdit(JSON.parse(oldProccessingCountryVal))
       }
        if(value) {
            getLicenseStatus(value)
        }
        otherIndustryType(oldIndustryTypeVal)
    });

    function getProcessingCountry(sel){
        var opts = [],
        opt;
        var len = sel.options.length;
        for (var i = 0; i < len; i++) {
             opt=sel.options[i];
            if (opt.selected) {
                  opts.push(opt.value);
            }
        } 

        getProcessingCountryEdit(opts)
       
    }

    function otherIndustryType(val){
        if(val == 28){
            $('.showOtherIndustryInput').removeClass('d-none')
        }
        else {
            $('.showOtherIndustryInputBox').val('')
            $('.showOtherIndustryInput').addClass('d-none')
        }
    }

    function getLicenseStatus(val){
        if(val == 0){
            $('.toggleLicenceDocs').removeClass('d-none')
        } else {
            $('.toggleLicenceDocs').addClass('d-none')
        }
    }

    function getProcessingCountryEdit(arr){
       
        var isExist = arr.filter(function(item) {
            return item == 'Others'
        })
        if(isExist.length > 0){
            $('.otherProcessingInput').removeClass('d-none')
        } else {
            $('.otherProcessingInput').addClass('d-none')
        }
    }
</script>
@endsection