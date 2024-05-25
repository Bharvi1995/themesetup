@extends('layouts.user.default')

@section('title')
    Generate Payment Link
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('dashboardPage') }}">Dashboard</a> / Generate Payment Link
@endsection

@section('content')
    <div class="row">

        <div class="col-xl-5 col-xxl-5">
            <form action="{{ route('iframe.generate') }}" method="post" id="iframe-form" class="form-dark">
                {!! csrf_field() !!}
                <div class="card">
                    <div class="card-header">
                        <h4 class="mt-50">Generate Payment Link</h4>

                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label class="col-form-label">Enter Amount</label>
                                    <input type="number" step="0.01" class="form-control" name="amount"
                                        placeholder="Enter here">
                                    @if ($errors->has('amount'))
                                        <span class="help-block">
                                            {{ $errors->first('amount') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="col-form-label">Select Currency</label>
                                    <select class="form-control select2" name="currency" id="currency select">
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
                                    </select>
                                    @if ($errors->has('currency'))
                                        <span class="help-block">
                                            {{ $errors->first('currency') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="text">Select Logo</label>
                                    <div class="custom-file">
                                        <input type="file" name="iframe_logo" class="form-control extra_document"
                                            id="iframe_logo" name="logo">
                                    </div>
                                </div>
                                <div class="col-md-12" id="logo-div">
                                    @if (isset(Auth::user()->iframe_logo))
                                        <img src="{{ getS3Url(Auth::user()->iframe_logo) }}" class="custom-file-img" style="height: 35px; width: 100px;">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <button type="submit" class="btn btn-primary">Generate</button>
                            <button id="remove-logo" type="button" class="btn btn-danger">Remove Logo</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-xl-7 col-xxl-7">
            {{-- <div class="iq-card height-auto border-card">
            <div class="iq-card-header bg-info d-flex justify-content-between">
                <div class="iq-header-title">
                    <h4 class="card-title">Card iFrame</h4>
                </div>
            </div>
            <div class="iq-card-body">
                <textarea class="form-control iframe-textarea" id="iframe-code">{{ $iframe_tag }}</textarea>
            </div>
        </div> --}}

            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Card Direct Payment Link</h4>
                    </div>
                </div>

                <div class="card-body form-dark">
                    <textarea class="form-control iframe-textarea" id="direct-link">{{ $iframe_code }}</textarea>
                </div>
            </div>


            @if (!empty($iframe_code_bank))
                {{-- <div class="iq-card height-auto border-card">
            <div class="card-header bg-info d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Bank iFrame</h4>
                </div>
            </div>

            <div class="card-body form-dark">
                <textarea class="form-control iframe-textarea"
                    id="iframe-code" style="height: 134px;">{{ $iframe_tag_bank }}</textarea>
            </div>
        </div> --}}

                <div class="card height-auto border-card">
                    <div class="card-header">
                        <div class="header-title">
                            <h4 class="card-title">Bank Direct Link</h4>
                        </div>
                    </div>

                    <div class="card-body form-dark">
                        <textarea class="form-control iframe-textarea" id="direct-link" style="height: 134px;">{{ $iframe_code_bank }}</textarea>
                    </div>
                </div>
            @endif

            @if (!empty($iframe_code_crypto))
                {{-- <div class="iq-card height-auto border-card">
            <div class="card-header">
                <div class="header-title">
                    <h4 class="card-title">Crypto iFrame</h4>
                </div>
            </div>

            <div class="card-body form-dark">
                <textarea class="form-control iframe-textarea"
                    id="iframe-code" style="height: 134px;">{{ $iframe_tag_crypto }}</textarea>
            </div>
        </div> --}}

                <div class="card height-auto border-card">
                    <div class="card-header">
                        <div class="header-title">
                            <h4 class="card-title">Crypto Direct Link</h4>
                        </div>
                    </div>

                    <div class="card-body form-dark">
                        <textarea class="form-control iframe-textarea" id="direct-link" style="height: 134px;">{{ $iframe_code_crypto }}</textarea>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/front/iframe.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // const textarea = document.getElementById("iframe-code");
            // const height = textarea.scrollHeight;
            // $('#iframe-code').css('height',height+'px');

            const textarea1 = document.getElementById("direct-link");
            const height1 = textarea1.scrollHeight;
            $('#direct-link').css('height', height1 + 'px');

            $(document).on('click', '#remove-logo', function() {
                $.ajax({
                    url: "{{ route('iframe.removeLogo') }}",
                    type: 'post',
                    data: {"_token": CSRF_TOKEN},
                    beforeSend: function(){
                        $('#detailsContent').html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                    },
                    success:function(data) {
                        if (data.status == true) {
                            $('#logo-div').remove();
                            toastr.success(data.message);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function(err) {
                        $('#detailsContent').html(data.html);
                        toastr.error('Something went wrong, please try again.');
                    }
                });
            });
        });
    </script>
@endsection
