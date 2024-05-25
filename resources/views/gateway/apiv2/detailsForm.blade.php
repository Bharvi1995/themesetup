 <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/select2.min.css') }}">
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
 </style>

 <div class="row">
     {{-- hidden offset --}}
     @if (in_array('address', $data) ||
             in_array('country', $data) ||
             in_array('city', $data) ||
             in_array('state', $data) ||
             in_array('zip', $data) ||
             in_array('phone_no', $data))
         <?php $billing_md = 6; ?>
         <?php $card_md = 0; ?>
     @else
         <?php $billing_md = 0; ?>
         <?php $card_md = 6; ?>
     @endif
     @if (in_array('card_no', $data) ||
             in_array('ccExpiryMonth', $data) ||
             in_array('ccExpiryYear', $data) ||
             in_array('cvvNumber', $data))
         <?php $billing_md += 0; ?>
         <?php $card_md += 6; ?>
     @else
         <?php $billing_md += 6; ?>
         <?php $card_md += 0; ?>
     @endif
     {{-- card holders details --}}
     {{-- @if (in_array('address', $data) || in_array('country', $data) || in_array('city', $data) || in_array('state', $data) || in_array('zip', $data) || in_array('phone_no', $data)) --}}
     <div class="col-sm-12 col-md-6">
         {{-- <div class="col-sm-12 col-md-{!! $billing_md !!}"> --}}
         <div class="row">
             <div class="col-md-12">
                 <h4 class="text-primary">{{ __('messages.cardHolderDetails') }}</h4>
             </div>
             @if (in_array('address', $data))
                 <div class="col-md-12 form-group mb-1">
                     <label>{{ __('messages.address') }}</label>
                     <div>
                         <textarea class="form-control required-field" name="address" id="address" placeholder="{{ __('messages.address') }}"
                             required data-missing="Address field is required">{{ $input['address'] ?? null }}</textarea>
                     </div>
                     @if ($errors->has('address'))
                         <span class="help-block">
                             <strong>{{ $errors->first('address') }}</strong>
                         </span>
                     @endif
                 </div>
             @endif
             @if (in_array('country', $data))
                 <div class="col-md-12 form-group mb-1">
                     <label>{{ __('messages.country') }}</label>
                     <div><select class="form-control select2 required-field" name="country" id="country11" required
                             data-missing="Country field is required">
                             <option disabled> -- {{ __('messages.country') }} -- </option>
                             @foreach (getCountry() as $key => $value)
                                 <option value="{{ $key }}"
                                     {{ isset($input['country']) && $input['country'] == $key ? 'selected' : null }}>
                                     {{ $value }}</option>
                             @endforeach
                         </select></div>
                     @if ($errors->has('country'))
                         <span class="help-block">
                             <strong>{{ $errors->first('country') }}</strong>
                         </span>
                     @endif
                 </div>
             @endif
             @if (in_array('city', $data))
                 <div class="col-md-12 form-group mb-1">
                     <label>{{ __('messages.city') }}</label>
                     <input class="form-control required-field" name="city" type="text" id="city"
                         placeholder="{{ __('messages.city') }}" minlength="2" maxlength="50"
                         value="{{ $input['city'] ?? null }}" required data-missing="City field is required">
                     @if ($errors->has('city'))
                         <span class="help-block">
                             <strong>{{ $errors->first('city') }}</strong>
                         </span>
                     @endif
                 </div>
             @endif
             @if (in_array('state', $data))
                 <div class="col-md-12 form-group mb-1">
                     <label>{{ __('messages.state') }}</label>
                     <input type="text" name="state" placeholder="{{ __('messages.state') }}" id="state"
                         class="form-control required-field" value="{{ $input['state'] ?? null }}"detailsForm required
                         data-missing="State field is required">
                     @if ($errors->has('state'))
                         <span class="help-block">
                             <strong>{{ $errors->first('state') }}</strong>
                         </span>
                     @endif
                 </div>
             @endif
             @if (in_array('zip', $data))
                 <div class="col-md-12 form-group mb-1">
                     <label>{{ __('messages.zipCode') }}</label>
                     <input class="form-control required-field" name="zip" type="text" id="zip"
                         placeholder="{{ __('messages.zipCode') }}" value="{{ $input['zip'] ?? null }}" required
                         data-missing="ZIP field is required">
                     @if ($errors->has('zip'))
                         <span class="help-block">
                             <strong>{{ $errors->first('zip') }}</strong>
                         </span>
                     @endif
                 </div>
             @endif
             @if (in_array('phone_no', $data))
                 <div class="col-md-12 form-group mb-1">
                     <label>{{ __('messages.phoneNo') }}</label>
                     <input class="form-control required-field" name="phone_no" type="text"
                         placeholder="{{ __('messages.phoneNo') }}" id="phone_no"
                         value="{{ $input['phone_no'] ?? null }}" required
                         data-missing="Phone number field is required">
                     @if ($errors->has('phone_no'))
                         <span class="help-block">
                             <strong>{{ $errors->first('phone_no') }}</strong>
                         </span>
                     @endif
                 </div>
             @endif
         </div>
     </div>
     {{-- @endif --}}
     {{-- card details --}}
     @if (in_array('card_no', $data) ||
             in_array('ccExpiryMonth', $data) ||
             in_array('ccExpiryYear', $data) ||
             in_array('cvvNumber', $data))
         <div class="col-sm-12 col-md-6">
             {{-- <div class="col-sm-12 col-md-{!! $card_md !!}"> --}}
             <div class="pd-25">
                 <h4 class="text-primary">{{ __('messages.cardDetails') }}</h4>
                 <div class="form-group mb-1">
                     <label>{{ __('messages.cardNo') }}</label>
                     <div class="input-group card_no_custom">
                         <input type="text" name="card_no" placeholder="Card Number"
                             class="form-control card-no fld-txt required-field-input required-field" id="card"
                             value="{{ $input['card_no'] ?? null }}" required
                             data-missing="Card number field is required">
                     </div>
                     <strong class="text-danger log"></strong>
                 </div>
                 <div class="row">
                     <div class="col-md-6">
                         <div class="form-group mb-1">
                             <label for="ccExpiryMonth">Expiry Month</label>
                             <select id="ccExpiryMonth" class="expiration-month form-control fld-txt required-field"
                                 name="ccExpiryMonth" required data-missing="Card Expiry Month field is required">
                                 <option value="01"
                                     {{ isset($input['ccExpiryMonth']) && $input['ccExpiryMonth'] == '01' ? 'selected' : '' }}>
                                     {{ __('messages.jan') }}</option>
                                 <option value="02"
                                     {{ isset($input['ccExpiryMonth']) && $input['ccExpiryMonth'] == '02' ? 'selected' : '' }}>
                                     {{ __('messages.feb') }}</option>
                                 <option value="03"
                                     {{ isset($input['ccExpiryMonth']) && $input['ccExpiryMonth'] == '03' ? 'selected' : '' }}>
                                     {{ __('messages.march') }}</option>
                                 <option value="04"
                                     {{ isset($input['ccExpiryMonth']) && $input['ccExpiryMonth'] == '04' ? 'selected' : '' }}>
                                     {{ __('messages.april') }}</option>
                                 <option value="05"
                                     {{ isset($input['ccExpiryMonth']) && $input['ccExpiryMonth'] == '05' ? 'selected' : '' }}>
                                     {{ __('messages.may') }}</option>
                                 <option value="06"
                                     {{ isset($input['ccExpiryMonth']) && $input['ccExpiryMonth'] == '06' ? 'selected' : '' }}>
                                     {{ __('messages.june') }}</option>
                                 <option value="07"
                                     {{ isset($input['ccExpiryMonth']) && $input['ccExpiryMonth'] == '07' ? 'selected' : '' }}>
                                     {{ __('messages.july') }}</option>
                                 <option value="08"
                                     {{ isset($input['ccExpiryMonth']) && $input['ccExpiryMonth'] == '08' ? 'selected' : '' }}>
                                     {{ __('messages.august') }}</option>
                                 <option value="09"
                                     {{ isset($input['ccExpiryMonth']) && $input['ccExpiryMonth'] == '09' ? 'selected' : '' }}>
                                     {{ __('messages.sept') }}</option>
                                 <option value="10"
                                     {{ isset($input['ccExpiryMonth']) && $input['ccExpiryMonth'] == '10' ? 'selected' : '' }}>
                                     {{ __('messages.oct') }}</option>
                                 <option value="11"
                                     {{ isset($input['ccExpiryMonth']) && $input['ccExpiryMonth'] == '11' ? 'selected' : '' }}>
                                     {{ __('messages.nov') }}</option>
                                 <option value="12"
                                     {{ isset($input['ccExpiryMonth']) && $input['ccExpiryMonth'] == '12' ? 'selected' : '' }}>
                                     {{ __('messages.dec') }}</option>
                             </select>
                         </div>
                     </div>
                     <div class="col-md-6">
                         <label for="ccExpiryYear">Expiry Year</label>
                         <select id="ccExpiryYear" class="expiration-year form-control fld-txt required-field"
                             name="ccExpiryYear" required data-missing="Card Expiry Year field is required">
                             <option value="2023"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2023' ? 'selected' : '' }}>
                                 2023</option>
                             <option value="2024"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2024' ? 'selected' : '' }}>
                                 2024</option>
                             <option value="2025"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2025' ? 'selected' : '' }}>
                                 2025</option>
                             <option value="2026"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2026' ? 'selected' : '' }}>
                                 2026</option>
                             <option value="2027"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2027' ? 'selected' : '' }}>
                                 2027</option>
                             <option value="2028"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2028' ? 'selected' : '' }}>
                                 2028</option>
                             <option value="2029"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2029' ? 'selected' : '' }}>
                                 2029</option>
                             <option value="2030"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2030' ? 'selected' : '' }}>
                                 2030</option>
                             <option value="2031"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2031' ? 'selected' : '' }}>
                                 2031</option>
                             <option value="2032"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2032' ? 'selected' : '' }}>
                                 2032</option>
                             <option value="2033"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2033' ? 'selected' : '' }}>
                                 2033</option>
                             <option value="2034"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2034' ? 'selected' : '' }}>
                                 2034</option>
                             <option value="2035"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2035' ? 'selected' : '' }}>
                                 2035</option>
                             <option value="2036"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2036' ? 'selected' : '' }}>
                                 2036</option>
                             <option value="2037"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2037' ? 'selected' : '' }}>
                                 2037</option>
                             <option value="2038"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2038' ? 'selected' : '' }}>
                                 2038</option>
                             <option value="2039"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2039' ? 'selected' : '' }}>
                                 2039</option>
                             <option value="2040"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2040' ? 'selected' : '' }}>
                                 2040</option>
                             <option value="2041"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2041' ? 'selected' : '' }}>
                                 2041</option>
                             <option value="2042"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2042' ? 'selected' : '' }}>
                                 2042</option>
                             <option value="2043"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2043' ? 'selected' : '' }}>
                                 2043</option>
                             <option value="2044"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2044' ? 'selected' : '' }}>
                                 2044</option>
                             <option value="2045"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2045' ? 'selected' : '' }}>
                                 2045</option>
                             <option value="2046"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2046' ? 'selected' : '' }}>
                                 2046</option>
                             <option value="2047"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2047' ? 'selected' : '' }}>
                                 2047</option>
                             <option value="2048"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2048' ? 'selected' : '' }}>
                                 2048</option>
                             <option value="2049"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2049' ? 'selected' : '' }}>
                                 2049</option>
                             <option value="2050"
                                 {{ isset($input['ccExpiryYear']) && $input['ccExpiryYear'] == '2050' ? 'selected' : '' }}>
                                 2050</option>
                         </select>
                     </div>
                 </div>
                 <div class="form-group mb-1">
                     <label>CVV</label>
                     <input type="password" name="cvvNumber" placeholder="{{ __('messages.cvvNo') }}"
                         class="form-control fld-txt required-field" id="cvvNumber"
                         value="{{ $input['cvvNumber'] ?? null }}" required
                         data-missing="CVV number field is required">
                 </div>
             </div>
         </div>
     @endif
     <div class="col-md-12">
         <div class="row">
             <div class="col-md-6 order-2 order-md-1  ">
                 <button type="button" id="cancel-button"
                     class="cancel-btn btn btn-primary w-100 mt-1 mt-md-0">{{ __('messages.cancel') }}</button>
             </div>
             <div class="col-md-6 order-1 order-md-2  ">
                 <button type="button" id="submit-button" class="btn btn-danger w-100"
                     style="background-color:#5BB318; border-color: #5BB318; border-radius: 3px;">{{ __('messages.payNow') }}</button>
             </div>

         </div>
         <p class="text-danger mt-1">* {{ __('messages.otpText') }}</p>
     </div>
 </div>

 <script src="{{ storage_asset('NewTheme/assets/lib/cleave.js/cleave.min.js') }}"></script>
 <script src="{{ storage_asset('NewTheme/vendors/js/forms/select/select2.full.min.js') }}"></script>
 <script type="text/javascript">
     submitDisabled();
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
