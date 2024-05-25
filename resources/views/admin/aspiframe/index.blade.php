@extends('layouts.admin.default')

@section('title')
    Link Generator
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Link Generator
@endsection
@section('content')
    <style type="text/css">
        .form-control {
            line-height: 24px;
        }
    </style>
    <div class="row">
        <div class="col-xl-5 col-xxl-5">
            <form method="post" id="submit-hosted-form" enctype="multipart/form-data" class="form-dark">
                {!! csrf_field() !!}
                <input type="hidden" name="hosted_request" value="1">
                <div class="card">
                    <div class="card-header">
                        <div class="header-title">
                            <h4 class="card-title">Link Generator</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert">×</button>
                                <div class="alert-message">
                                    <span><strong>Error!</strong> {{ $message }}</span>
                                </div>
                            </div>
                        @endif
                        {!! Session::forget('error') !!}
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert">×</button>
                                <div class="alert-message">
                                    <span><strong>Success!</strong> {{ $message }}</span>
                                </div>
                            </div>
                        @endif
                        {!! Session::forget('success') !!}
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label>Select MID</label>
                                <select class="form-control select2" name="mid" id="hosted-mid"
                                    style="width: calc(100% - 15px) !important;">
                                    <option selected="selected" disabled="disabled"> -- Select MID -- </option>
                                    @foreach ($mid_details as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->bank_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('mid'))
                                    <span class="help-block text-danger">
                                        {{ $errors->first('mid') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-lg-12">
                                <label>Select Merchant</label>
                                <select class="form-control select2" name="company_name" id="hosted-merchant"
                                    style="width: calc(100% - 15px) !important;">
                                    <option selected disabled> -- Select Company -- </option>
                                    @foreach ($companyName as $key => $value)
                                        <option value="{{ $value->user_id }}">{{ $value->business_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('company_name'))
                                    <span class="help-block text-danger">
                                        {{ $errors->first('company_name') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group col-md-6">
                                <label for="hosted-currency">Select Currency</label>
                                <select class="form-control select2" name="currency" id="hosted-currency">
                                    <option selected disabled> -- Select Currency -- </option>
                                    <option value="USD">USD</option>
                                    <option value="HKD">HKD</option>
                                    <option value="GBP">GBP</option>
                                    <option value="JPY">JPY</option>
                                    <option value="EUR">EUR</option>
                                    <option value="AUD">AUD</option>
                                    <option value="CAD">CAD</option>
                                    <option value="SGD">SGD</option>
                                    <option value="NZD">NZD</option>
                                    <option value="TWD">TWD</option>
                                    <option value="KRW">KRW</option>
                                    <option value="DKK">DKK</option>
                                    <option value="TRL">TRL</option>
                                    <option value="MYR">MYR</option>
                                    <option value="THB">THB</option>
                                    <option value="INR">INR</option>
                                    <option value="PHP">PHP</option>
                                    <option value="CHF">CHF</option>
                                    <option value="SEK">SEK</option>
                                    <option value="ILS">ILS</option>
                                    <option value="ZAR">ZAR</option>
                                    <option value="RUB">RUB</option>
                                    <option value="NOK">NOK</option>
                                    <option value="AED">AED</option>
                                    <option value="BRL">BRL</option>
                                    <option value="GHS">GHS</option>
                                    <option value="UGX">UGX</option>
                                    <option value="TND">TND</option>
                                    <option value="">Remove Currency</option>
                                </select>
                                @if ($errors->has('currency'))
                                    <span class="help-block text-danger">
                                        {{ $errors->first('currency') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-md-6">
                                <label for="hosted-amount">Input Amount</label>
                                <input type="number" step="0.01" class="form-control" name="amount" id="hosted-amount">
                                @if ($errors->has('amount'))
                                    <span class="help-block text-danger">
                                        {{ $errors->first('amount') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group col-lg-12">
                                <label>Select Logo</label>
                                <div class="custom-file" id="custom-file" style="width: calc(100% - 15px) !important;">
                                    <input type="file" name="iframe_logo" class="custom-file-input form-control" id="customFile">
                                </div>
                            </div>
                            <div class="form-group col-lg-12">
                                <label>Select iFrame Version</label>
                                <select class="form-control select2" name="api_version" id="api-version"
                                    style="width: calc(100% - 15px) !important;">
                                    <option value="1">Version 1</option>
                                    <option value="2">Version 2</option>
                                </select>
                                @if ($errors->has('api_version'))
                                    <span class="help-block text-danger">
                                        {{ $errors->first('api_version') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-lg-12">
                                <img src="" class="custom-file-img" style="max-width: 100px;">
                            </div>
                            <div class="form-group col-lg-12">
                                <button type="button" id="submit-hosted-form-button"
                                    class="btn btn-success">Generate</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-xl-7 col-xxl-7">
            {{-- <div class="card height-auto  mt-1">
            <div class="card-header">
                <div class="iq-header-title">
                    <h4 class="card-title">iFrame</h4>
                </div>
            </div>

            <div class="card-body p-0">
                <textarea class="form-control iframe-textarea bg-primary" id="iframe" rows="4"></textarea>
            </div>
        </div> --}}

            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Direct Link</h4>
                    </div>
                </div>

                <div class="card-body form-dark">
                    <textarea class="form-control iframe-textarea" id="directlink" rows="4"></textarea>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script type="text/javascript">
        $(document).ready(function() {
            // const textarea = document.getElementById("iframe");
            // const height = textarea.scrollHeight;
            // $('#iframe').css('height',height+'px');

            const textarea1 = document.getElementById("directlink");
            const height1 = textarea1.scrollHeight;
            $('#directlink').css('height', height1 + 'px');
        });
        $(document).ready(function() {
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('.custom-file-img').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#customFile").change(function() {
                readURL(this);
            });
            $('body').on('change', '#hosted-merchant', function(event) {
                var id = $(this).val();
                var currentObj = $(this);
                $.ajax({
                    type: 'GET',
                    context: $(this),
                    url: "{{ URL::route('get-iframe-logo') }}",
                    data: {
                        'id': id
                    },
                    beforeSend: function() {
                        $(this).attr('disabled', 'disabled');
                    },
                    success: function(data) {
                        if (data.success != null) {
                            $('#custom-file input').attr('disabled', 'disabled');
                            $('.custom-file-img').attr('src', data.success);
                        } else {
                            $('#custom-file input').attr('disabled', false);
                            $('.custom-file-img').attr('src', '');
                        }
                        currentObj.attr('disabled', false);
                    }
                });
            });

            // get hosted iframe
            $('body').on('click', '#submit-hosted-form-button', function(event) {
                event.preventDefault();
                console.log('test');
                // $('#iframe').val(null);
                $('#directlink').val(null);
                var hostedFormData = new FormData($('#submit-hosted-form')[0]);

                console.log(hostedFormData);
                $.ajax({
                    url: "/superintendent/asp-iframe",
                    type: 'POST',
                    data: hostedFormData,
                    processData: false,
                    contentType: false,
                    context: this,
                    beforeSend: function() {
                        $('#submit-hosted-form-button').attr('disabled', true);
                        $('#submit-hosted-form-button').html(
                            '<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.status == 'success') {
                            // $('#iframe').val(response.iframe);
                            $('#directlink').val(response.link);
                        }
                        if (response.status == 'error') {
                            toastr.error(response.message);
                        }
                        $('#submit-hosted-form-button').attr('disabled', false);
                        $('#submit-hosted-form-button').html('Generate');
                    },
                    error: function(response) {}
                });
            });

            // iframe selected
            $(".iframe-textarea").on("focus keyup", function(e) {

                var keycode = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode;
                if (keycode === 9 || !keycode) {
                    // Hacemos select
                    var $this = $(this);
                    $this.select();

                    // Para Chrome's que da problema
                    $this.on("mouseup", function() {
                        // Unbindeamos el mouseup
                        $this.off("mouseup");
                        return false;
                    });
                }
            });
        });
    </script>
@endsection
