<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>{{ config('app.name') }} | {{ __('messages.pageTitleCard') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('softtheme/img/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/auth.css') }}">
     <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/select2.min.css') }}">
    <style type="text/css">
     .card_no_custom {
         position: relative;
     }

     .card_no_custom img {
         position: absolute;
         width: 50px;
         height: 43px;
         top: 0px;
         right: 0px;
         z-index: 1001;
         border-radius: 0px 3px 3px 0px;
     }

     .select2-dropdown {
         background-color: #494949 !important;
     }

     .select2-container--default .select2-selection--single .select2-selection__rendered {
         color: #B9B9C3 !important;
     }

     .select2-container--default .select2-results__option--highlighted[aria-selected] {
         background-color: #F44336 !important;
     }
        .card {
            background: var(--white);
            border-radius: 0px 0px 3px 3px;
            box-shadow: 0px 2px 5px 0px #05309533;
        }

        .btn-danger {
            background: var(--primary-1) !important;
            border-color: var(--primary-1) !important;
            color: var(--white) !important;
            border-radius: 3px;
        }

        .btn-primary {
            background: var(--primary-3) !important;
            border-color: var(--primary-3) !important;
            color: var(--primary-4) !important;
            border-radius: 3px;
        }

        .cardSelectMain {
            overflow: hidden;
            padding: 15px;
        }

        .cardSelect {
            float: left;
            width: 100px;
            text-align: left;
        }

        [type=radio] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        [type=radio]+img {
            border: 3px solid #1B1919;
            cursor: pointer;
            width: 80px;
            border-radius: 3px;
            filter: gray;
            -webkit-filter: grayscale(1);
            filter: grayscale(1);
        }

        [type=radio]:checked+img {
            border: 3px solid #1B1919;
            box-shadow: 0px 0px 5px 0px #FFF;
            -webkit-filter: grayscale(0);
            filter: none;
        }

        .error{
            color:#FF3C14 !important;
        }
    </style>
</head>

<body>
    <div id="loading">
        <p class="mt-1">{{ __('messages.loading') }}...</p>
    </div>
    <div class="app-content content">
        <div class="container">
            <div class="row content-body">
                <div class="col-md-6 col-xl-6 col-xxl-6">
                    {{-- ajax error messages --}}
                    

                    <div class="row mb-2 mt-2">
                        <div class="col-md-12 text-center">
                            <img src="{{ storage_asset('setup/images/Logo.png') }}" class="imgPayment" width="260px">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="align-items-center">
                            <div id="validation-errors" class="col-12 mx-auto"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <form method="post" action="{{ route('api.v2.extraDetailsFormSubmit', $order_id) }}" name="extra_details_form" id="extra-details-form" class="validity">
                                @csrf
                                <div class="card-body gateway-card">
                                    
                                    <input type="hidden" name="card_type" id="card-type">
                                    {{-- here comes form fields --}}
                                    <div id="form-fields">
                                        <div class="row">
                                         {{-- hidden offset --}}
                                         @if (in_array('user_address', $data) ||
                                                 in_array('user_country', $data) ||
                                                 in_array('user_city', $data) ||
                                                 in_array('user_state', $data) ||
                                                 in_array('user_zip', $data) ||
                                                 in_array('user_phone_no', $data))
                                             <?php $billing_md = 6; ?>
                                             <?php $card_md = 0; ?>
                                         @else
                                             <?php $billing_md = 0; ?>
                                             <?php $card_md = 6; ?>
                                         @endif
                                         @if (in_array('user_card_no', $data) ||
                                                 in_array('user_ccexpiry_month', $data) ||
                                                 in_array('user_ccexpiry_year', $data) ||
                                                 in_array('user_cvvNumber', $data))
                                             <?php $billing_md += 0; ?>
                                             <?php $card_md += 6; ?>
                                         @else
                                             <?php $billing_md += 6; ?>
                                             <?php $card_md += 0; ?>
                                         @endif
                                         {{-- card holders details --}}
                                         {{-- @if (in_array('user_address', $data) || in_array('country', $data) || in_array('city', $data) || in_array('state', $data) || in_array('zip', $data) || in_array('phone_no', $data)) --}}
                                         <div class="col-sm-12 col-md-12">
                                             <div class="row">
                                                 <div class="col-md-12 mb-2">
                                                     <h4 class="text-primary">Information</h4>
                                                 </div>
                                                 @if (in_array('user_address', $data))
                                                     <div class="col-md-6 form-group mb-1">
                                                         <!-- <label>{{ __('messages.address') }}</label> -->
                                                         <div>
                                                             <textarea class="form-control required-field" name="user_address" id="user_address" placeholder="{{ __('messages.address') }}"
                                                                 required data-missing="Address field is required">{{ $input['user_address'] ?? null }}</textarea>
                                                         </div>
                                                         @if ($errors->has('user_address'))
                                                             <span class="help-block">
                                                                 <strong>{{ $errors->first('user_address') }}</strong>
                                                             </span>
                                                         @endif
                                                     </div>
                                                 @endif
                                                 @if (in_array('user_country', $data))
                                                     <div class="col-md-6 form-group mb-1">
                                                         <!-- <label>{{ __('messages.country') }}</label> -->
                                                         <div><select class="form-control select2 required-field" name="user_country" id="user_country11" required data-missing="Country field is required">
                                                                 <option disabled> -- {{ __('messages.country') }} -- </option>
                                                                 @foreach (getCountry() as $key => $value)
                                                                     <option value="{{ $key }}"
                                                                         {{ isset($input['user_country']) && $input['user_country'] == $key ? 'selected' : null }}>
                                                                         {{ $value }}</option>
                                                                 @endforeach
                                                             </select></div>
                                                         @if ($errors->has('user_country'))
                                                             <span class="help-block">
                                                                 <strong>{{ $errors->first('user_country') }}</strong>
                                                             </span>
                                                         @endif
                                                     </div>
                                                 @endif
                                                 @if (in_array('user_city', $data))
                                                    <div class="col-md-6 form-group mb-1">
                                                         <!-- <label>{{ __('messages.city') }}</label> -->
                                                         <input class="form-control required-field" name="user_city" type="text" id="user_city"
                                                             placeholder="{{ __('messages.city') }}" minlength="2" maxlength="50"
                                                             value="{{ $input['user_city'] ?? null }}" required data-missing="City field is required">
                                                         @if ($errors->has('user_city'))
                                                             <span class="help-block">
                                                                 <strong>{{ $errors->first('user_city') }}</strong>
                                                             </span>
                                                         @endif
                                                     </div>
                                                 @endif
                                                 @if (in_array('user_state', $data))
                                                     <div class="col-md-6 form-group mb-1">
                                                         <!-- <label>{{ __('messages.state') }}</label> -->
                                                         <input type="text" name="user_state" placeholder="{{ __('messages.state') }}" id="user_state"
                                                             class="form-control required-field" value="{{ $input['user_state'] ?? null }}"detailsForm required
                                                             data-missing="State field is required">
                                                         @if ($errors->has('user_state'))
                                                             <span class="help-block">
                                                                 <strong>{{ $errors->first('user_state') }}</strong>
                                                             </span>
                                                         @endif
                                                     </div>
                                                 @endif
                                                 @if (in_array('user_zip', $data))
                                                     <div class="col-md-6 form-group mb-1">
                                                         <!-- <label>{{ __('messages.zipCode') }}</label> -->
                                                         <input class="form-control required-field" name="user_zip" type="text" id="user_zip"
                                                             placeholder="{{ __('messages.zipCode') }}" value="{{ $input['user_zip'] ?? null }}" required
                                                             data-missing="ZIP field is required">
                                                         @if ($errors->has('user_zip'))
                                                             <span class="help-block">
                                                                 <strong>{{ $errors->first('user_zip') }}</strong>
                                                             </span>
                                                         @endif
                                                     </div>
                                                 @endif
                                                 @if (in_array('user_phone_no', $data))
                                                     <div class="col-md-6 form-group mb-1">
                                                         <!-- <label>{{ __('messages.phoneNo') }}</label> -->
                                                         <input class="form-control required-field" name="user_phone_no" type="text"
                                                             placeholder="{{ __('messages.phoneNo') }}" id="user_phone_no"
                                                             value="{{ $input['user_phone_no'] ?? null }}" required
                                                             data-missing="Phone number field is required">
                                                         @if ($errors->has('user_phone_no'))
                                                             <span class="help-block">
                                                                 <strong>{{ $errors->first('user_phone_no') }}</strong>
                                                             </span>
                                                         @endif
                                                     </div>
                                                 @endif
                                             </div>
                                         </div>
                                         {{-- @endif --}}
                                         {{-- card details --}}
                                         @if (in_array('user_card_no', $data) ||  in_array('user_ccexpiry_month', $data) || in_array('user_ccexpiry_year', $data) || in_array('user_cvvNumber', $data))
                                         <div class="col-sm-12 col-md-12">
                                             {{-- <div class="col-sm-12 col-md-{!! $card_md !!}"> --}}
                                             <div class="pd-25">
                                                 <h4 class="text-primary mb-2">{{ __('messages.cardDetails') }}</h4>
                                                 <div class="form-group mb-1">
                                                     <!-- <label>{{ __('messages.cardNo') }}</label> -->
                                                     <div class="input-group card_no_custom">
                                                         <input type="text" name="user_card_no" placeholder="{{ __('messages.cardNo') }}"
                                                             class="form-control card-no fld-txt required-field-input required-field" id="user_card_no"
                                                             value="{{ $input['user_card_no'] ?? null }}" required
                                                             data-missing="Card number field is required">
                                                     </div>
                                                     <strong class="text-danger log"></strong>
                                                 </div>
                                                 <div class="row">
                                                     <div class="col-md-4">
                                                         <div class="form-group mb-1">
                                                             <label for="user_ccexpiry_month">Expiry Month</label>
                                                             <select id="user_ccexpiry_month" class="expiration-month form-control fld-txt required-field"
                                                                 name="user_ccexpiry_month" required data-missing="Card Expiry Month field is required">
                                                                 <option value="01"
                                                                     {{ isset($input['user_ccexpiry_month']) && $input['user_ccexpiry_month'] == '01' ? 'selected' : '' }}>
                                                                     {{ __('messages.jan') }}</option>
                                                                 <option value="02"
                                                                     {{ isset($input['user_ccexpiry_month']) && $input['user_ccexpiry_month'] == '02' ? 'selected' : '' }}>
                                                                     {{ __('messages.feb') }}</option>
                                                                 <option value="03"
                                                                     {{ isset($input['user_ccexpiry_month']) && $input['user_ccexpiry_month'] == '03' ? 'selected' : '' }}>
                                                                     {{ __('messages.march') }}</option>
                                                                 <option value="04"
                                                                     {{ isset($input['user_ccexpiry_month']) && $input['user_ccexpiry_month'] == '04' ? 'selected' : '' }}>
                                                                     {{ __('messages.april') }}</option>
                                                                 <option value="05"
                                                                     {{ isset($input['user_ccexpiry_month']) && $input['user_ccexpiry_month'] == '05' ? 'selected' : '' }}>
                                                                     {{ __('messages.may') }}</option>
                                                                 <option value="06"
                                                                     {{ isset($input['user_ccexpiry_month']) && $input['user_ccexpiry_month'] == '06' ? 'selected' : '' }}>
                                                                     {{ __('messages.june') }}</option>
                                                                 <option value="07"
                                                                     {{ isset($input['user_ccexpiry_month']) && $input['user_ccexpiry_month'] == '07' ? 'selected' : '' }}>
                                                                     {{ __('messages.july') }}</option>
                                                                 <option value="08"
                                                                     {{ isset($input['user_ccexpiry_month']) && $input['user_ccexpiry_month'] == '08' ? 'selected' : '' }}>
                                                                     {{ __('messages.august') }}</option>
                                                                 <option value="09"
                                                                     {{ isset($input['user_ccexpiry_month']) && $input['user_ccexpiry_month'] == '09' ? 'selected' : '' }}>
                                                                     {{ __('messages.sept') }}</option>
                                                                 <option value="10"
                                                                     {{ isset($input['user_ccexpiry_month']) && $input['user_ccexpiry_month'] == '10' ? 'selected' : '' }}>
                                                                     {{ __('messages.oct') }}</option>
                                                                 <option value="11"
                                                                     {{ isset($input['user_ccexpiry_month']) && $input['user_ccexpiry_month'] == '11' ? 'selected' : '' }}>
                                                                     {{ __('messages.nov') }}</option>
                                                                 <option value="12"
                                                                     {{ isset($input['user_ccexpiry_month']) && $input['user_ccexpiry_month'] == '12' ? 'selected' : '' }}>
                                                                     {{ __('messages.dec') }}</option>
                                                             </select>
                                                         </div>
                                                     </div>
                                                     <div class="col-md-4">
                                                         <label for="user_ccexpiry_year">Expiry Year</label>
                                                         <select id="user_ccexpiry_year" class="expiration-year form-control fld-txt required-field"
                                                             name="user_ccexpiry_year" required data-missing="Card Expiry Year field is required">
                                                             <option value="2024"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2024' ? 'selected' : '' }}>
                                                                 2024</option>
                                                             <option value="2025"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2025' ? 'selected' : '' }}>
                                                                 2025</option>
                                                             <option value="2026"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2026' ? 'selected' : '' }}>
                                                                 2026</option>
                                                             <option value="2027"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2027' ? 'selected' : '' }}>
                                                                 2027</option>
                                                             <option value="2028"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2028' ? 'selected' : '' }}>
                                                                 2028</option>
                                                             <option value="2029"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2029' ? 'selected' : '' }}>
                                                                 2029</option>
                                                             <option value="2030"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2030' ? 'selected' : '' }}>
                                                                 2030</option>
                                                             <option value="2031"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2031' ? 'selected' : '' }}>
                                                                 2031</option>
                                                             <option value="2032"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2032' ? 'selected' : '' }}>
                                                                 2032</option>
                                                             <option value="2033"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2033' ? 'selected' : '' }}>
                                                                 2033</option>
                                                             <option value="2034"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2034' ? 'selected' : '' }}>
                                                                 2034</option>
                                                             <option value="2035"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2035' ? 'selected' : '' }}>
                                                                 2035</option>
                                                             <option value="2036"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2036' ? 'selected' : '' }}>
                                                                 2036</option>
                                                             <option value="2037"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2037' ? 'selected' : '' }}>
                                                                 2037</option>
                                                             <option value="2038"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2038' ? 'selected' : '' }}>
                                                                 2038</option>
                                                             <option value="2039"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2039' ? 'selected' : '' }}>
                                                                 2039</option>
                                                             <option value="2040"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2040' ? 'selected' : '' }}>
                                                                 2040</option>
                                                             <option value="2041"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2041' ? 'selected' : '' }}>
                                                                 2041</option>
                                                             <option value="2042"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2042' ? 'selected' : '' }}>
                                                                 2042</option>
                                                             <option value="2043"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2043' ? 'selected' : '' }}>
                                                                 2043</option>
                                                             <option value="2044"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2044' ? 'selected' : '' }}>
                                                                 2044</option>
                                                             <option value="2045"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2045' ? 'selected' : '' }}>
                                                                 2045</option>
                                                             <option value="2046"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2046' ? 'selected' : '' }}>
                                                                 2046</option>
                                                             <option value="2047"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2047' ? 'selected' : '' }}>
                                                                 2047</option>
                                                             <option value="2048"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2048' ? 'selected' : '' }}>
                                                                 2048</option>
                                                             <option value="2049"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2049' ? 'selected' : '' }}>
                                                                 2049</option>
                                                             <option value="2050"
                                                                 {{ isset($input['user_ccexpiry_year']) && $input['user_ccexpiry_year'] == '2050' ? 'selected' : '' }}>
                                                                 2050</option>
                                                         </select>
                                                     </div>
                                                 
                                                    <div class="col-md-4 form-group mb-1">
                                                        <label>CVV</label>
                                                        <input type="password" name="user_cvv_number" placeholder="{{ __('messages.cvvNo') }}"
                                                         class="form-control fld-txt required-field" id="user_cvv_number"
                                                         value="" required data-missing="CVV number field is required">
                                                    </div>
                                                 </div>
                                             </div>
                                         </div>
                                         @endif

                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button type="button" id="cancel-button" class="cancel-btn btn btn-warning mt-1 mt-md-0">Cancel</button>
                                    <button type="button" id="submit-button" class="btn btn-danger" style="background-color:#5BB318; border-color: #5BB318; border-radius: 3px;">Pay Now</button>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js'></script>
    <script src="{{ storage_asset('setup/js/jquery.validity.js') }}"></script>
    <script src="{{ storage_asset('setup/js/creditly.js') }}"></script>
     <script src="{{ storage_asset('setup/assets/lib/cleave.js/cleave.min.js') }}"></script>
 <script src="{{ storage_asset('setup/vendors/js/forms/select/select2.full.min.js') }}"></script>
 <script type="text/javascript">
     //submitDisabled();
 </script>
 <script type="text/javascript">
     $(document).ready(function() {
         $('[data-toggle="tooltip"]').tooltip();
         $(".select2").select2({})
     });
     var cleave = new Cleave('.card-no', {
         creditCard: true
     });
 </script>
    <script>
        jQuery(document).ready(function() {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut("");
        });
        // $(".select2").select2();
    </script>

    <script type="text/javascript">
        $(function() {
            $('.validity').validity()
                .on('submit', function(e) {
                    var $this = $(this),
                        $btn = $this.find('[type="submit"]');
                    $btn.button('loading');
                    if (!$this.valid()) {
                        e.preventDefault();
                        $btn.button('reset');
                    }
                });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            // first visa on load
            $(document).find('#card-type').val(2);
            var data = $('#extra-details-form').serialize();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

           

            // on input card/country details
            var startInputField;
            $(document).on('keyup', '.required-field-input', function(e) {
                e.preventDefault();
                clearTimeout(startInputField);

                let length = $(this).val().length;
                if (length >= 14) {
                    //startInputField = setTimeout(getValidation, 2000);
                }
            });

            $('.required-field-input').on('keydown', function() {
                clearTimeout(startInputField);
            });

            // on change country details
            $(document).on('change', '#country11', function(e) {
                e.preventDefault();
                // getValidation();
            });


            // submit form
            $(document).on('click', '#submit-button', function(e) {
                e.preventDefault();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var data = $('#extra-details-form').serialize();

                $.ajax({
                    url: '{{ route('api.v2.extraDetailsFormSubmit', $order_id) }}',
                    type: 'post',
                    data: data,
                    beforeSend: function() {
                        $('#validation-errors').html('');
                        showLoader();
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            window.location = data.url;
                        } else if (data.errors) {
                            $.each(data.errors, function(key, value) {
                                $('#validation-errors').append(
                                    '<div class="alert alert-danger"><div class="alert-body">' +
                                    value + '</div></div');
                                $('#submit-button').prop('disabled', true);
                            });
                        } else {
                            $('#validation-errors').append(
                                '<div class="alert alert-danger"><div class="alert-body">' +
                                data.message + '</div></div');
                            $('#submit-button').prop('disabled', true);
                        }
                    },
                    fail: function(err) {
                        $('#validation-errors').append(
                            '<div class="alert alert-danger"><div class="alert-body">' +
                            data.message + '</div></div');
                        $('#submit-button').prop('disabled', true);
                    },
                    error: function(jqXHR, exception) {
                        $('#validation-errors').append(
                            '<div class="alert alert-danger"><div class="alert-body">something went wrong, please try again</div></div'
                        );
                        $('#submit-button').prop('disabled', true);
                    }
                }).always(function(jqXHR, exception) {
                    hideLoader();
                    submitDisabled();
                });
            });

            // cancel form
            $(document).on('click', '#cancel-button', function(e) {
                window.location = "{{ route('api.v2.decline', $order_id) }}";
            });
        });
    </script>
    <script type="text/javascript">
        function submitDisabled() {
            var empty = false;
            $('.required-field').each(function() {
                if ($(this).val().length < 2) {
                    empty = true;
                }
            });
            if (empty == true) {
                $('#submit-button').prop('disabled', true);
            } else {
                $('#submit-button').prop('disabled', false);
            }
        }

        function showLoader() {
            jQuery("#load").fadeIn();
            jQuery("#loading").delay().fadeIn();
        }

        function hideLoader() {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut();
        }
        $(document).on('input', '.required-field', function(e) {
            submitDisabled();
        });
        $(document).on('change', '.required-field', function(e) {
            // submitDisabled();
        });
    </script>
</body>

</html>
