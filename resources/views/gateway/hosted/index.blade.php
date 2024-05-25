<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>{{ config('app.name') }} | {{ __('messages.pageTitleLink') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.14/semantic.css">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('NewTheme/css/auth.css') }}">

    <style type="text/css">
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

        .ui.attached.segment {
            background: transparent;
            border: unset;
        }

        .ui.icon.input>i.icon {
            color: #FFFFFF;
        }

        .btn-sm {
            font-size: 11px;
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
                    <div class="row">
                        <div class="col-md-12 text-center mb-3">
                            <h4 class="text-center text-primary">{{ __('messages.payWithCardText') }}</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body gateway-card">
                                    <form class="ui payment form attached segment" id="payment-form"
                                        action="{{ route('hostedAPI.cardSubmit', $session_id) }}" method="POST"
                                        onsubmit='document.getElementById("disableBTN").disabled=true; document.getElementById("disableBTN")'>
                                        <div class="row">
                                            <div class="col-md-12 mb-2">
                                                <div class="field">
                                                    <label>{{ __('messages.cardNo') }}</label>
                                                    <div class="ui icon input">
                                                        <i class="credit card alternative icon"></i>
                                                        <input type="tel" id="cc-number"
                                                            placeholder="•••• •••• •••• ••••" data-payment='cc-number'
                                                            name="card_no" value="{{ old('card_no') }}"
                                                            class="inputCreditCard">
                                                    </div>
                                                    @error('card_no')
                                                        <div class="my-1 text-danger" role="alert">{{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="field">
                                                    <label>{{ __('messages.cardExpiry') }}</label>
                                                    <input type="tel" id="cc-exp" placeholder="•• / ••••"
                                                        data-payment='cc-exp' name="ccExpiryMonthYear">
                                                </div>
                                                @error('ccExpiryMonthYear')
                                                    <div class="my-1 text-danger" role="alert">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <div class="field">
                                                    <label>{{ __('messages.cvvNo') }}</label>
                                                    <input type="password" id="cc-cvc" placeholder="•••"
                                                        data-payment='cc-cvc' name="cvvNumber">
                                                </div>
                                                @error('cvvNumber')
                                                    <div class="my-1 text-danger" role="alert">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            @if (\Session::has('error'))
                                                <div class="my-1 text-danger" role="alert">
                                                    {{ \Session::get('error') }}</div>
                                                {{ \Session::forget('error') }}
                                            @endif

                                            <div class="col-md-6 mt-4">
                                                <div class="paybutton field">
                                                    <div class="ui labeled button">
                                                        <button class="ui red button" type="submit" tabindex="0"
                                                            id="disableBTN">
                                                            {{ __('messages.payNow') }}
                                                        </button>
                                                        <a class="ui basic red left pointing label">
                                                            {{ $input['currency'] }} {{ $input['amount'] }}
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="ui error message"></div>
                                            </div>
                                            <div class="col-md-6 mt-4">
                                                <a href="{{ route('iframe-checkout-cancel', $session_id) }}"
                                                    class="btn btn-block btn-primary btn-sm cancel-btn"
                                                    id="disableCancel">{{ __('messages.cancel') }}</a>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="text-danger"><small>{{ __('messages.otpText') }}</small></p>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js'></script>

    <script src="{{ storage_asset('NewTheme/assets/lib/cleave.js/cleave.min.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/creditly.js') }}"></script>
    <script src="{{ storage_asset('NewTheme/js/jquery.validity.js') }}"></script>

    <script>
        jQuery(document).ready(function() {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut("");
        });
    </script>

    <script>
        $(function() {
            Creditly.initialize(
                '.expiration-month-and-year',
                '.card-type');
        });
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        $("#disableCancel").on("click", function(e) {
            $("#disableCancel").attr('disabled', "disabled");
            $("#disableCancel").css("pointer-events", "none");
            $("#disableCancel").css("cursor", "not-allowed");
        })

        var cleave = new Cleave('.inputCreditCard', {
            creditCard: true,
            onCreditCardTypeChanged: function(type) {
                console.log(type)
                var card = $('#creditCardType').find('.' + type);
                $("#card_type").val(type);
                if (card.length) {
                    card.addClass('text-primary');
                    card.siblings().removeClass('text-primary');
                } else {
                    $('#creditCardType span').removeClass('text-primary');
                }
            }
        });
    </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.14/semantic.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/3.0.0/jquery.payment.js">
    </script>
    <script type="text/javascript">
        /**
         * paymentForm
         *
         * A plugin that validates a group of payment fields.  See jquery.payment.js
         * Adapted from https://gist.github.com/Air-Craft/1300890
         */

        // if (!window.L) { window.L = function () { console.log(arguments);} } // optional EZ quick logging for debugging

        (function($) {

            /**
             * The plugin namespace, ie for $('.selector').paymentForm(options)
             * 
             * Also the id for storing the object state via $('.selector').data()  
             */
            var PLUGIN_NS = 'paymentForm';

            var Plugin = function(target, options) {
                this.$T = $(target);
                this._init(target, options);

                /** #### OPTIONS #### */
                this.options = $.extend(
                    true, // deep extend
                    {
                        DEBUG: false
                    },
                    options
                );

                this._cardIcons = {
                    "visa": "visa icon",
                    "mastercard": "mastercard icon",
                    "amex": "american express icon",
                    "dinersclub": "diners club icon",
                    "discover": "discover icon",
                    "jcb": "japan credit bureau icon",
                    "default": "credit card alternative icon"
                };

                return this;
            }

            /** #### INITIALISER #### */
            Plugin.prototype._init = function(target, options) {
                var base = this;

                base.number = this.$T.find("[data-payment='cc-number']");
                base.exp = this.$T.find("[data-payment='cc-exp']");
                base.cvc = this.$T.find("[data-payment='cc-cvc']");
                base.brand = this.$T.find("[data-payment='cc-brand']");
                base.onlyNum = this.$T.find("[data-numeric]");

                // Set up all payment fields inside the payment form
                base.number.payment('formatCardNumber').data('payment-error-message',
                    'Please enter a valid credit card number.');
                base.exp.payment('formatCardExpiry').data('payment-error-message',
                    'Please enter a valid expiration date.');
                base.cvc.payment('formatCardCVC').data('payment-error-message', 'Please enter a valid CVC.');
                base.onlyNum.payment('restrictNumeric');

                // Update card type on input
                base.number.on('input', function() {
                    base.cardType = $.payment.cardType(base.number.val());
                    var fg = base.number.closest('.ui.icon.input');
                    if (base.cardType) {
                        base.brand.text(base.cardType);
                        // Also set an icon
                        var icon = base._cardIcons[base.cardType] ? base._cardIcons[base.cardType] : base
                            ._cardIcons["default"];
                        fg.children('i').attr("class", icon);
                        //("<i class='" + icon + "'></i>");
                    } else {
                        $("[data-payment='cc-brand']").text("");
                    }
                });

                // Validate card number on change
                base.number.on('change', function() {
                    base._setValidationState($(this), !$.payment.validateCardNumber($(this).val()));
                });

                // Validate card expiry on change
                base.exp.on('change', function() {
                    base._setValidationState($(this), !$.payment.validateCardExpiry($(this).payment(
                        'cardExpiryVal')));
                });

                // Validate card cvc on change
                base.cvc.on('change', function() {
                    base._setValidationState($(this), !$.payment.validateCardCVC($(this).val(), base
                        .cardType));
                });
            };

            /** #### PUBLIC API (see notes) #### */
            Plugin.prototype.valid = function() {
                var base = this;

                var num_valid = $.payment.validateCardNumber(base.number.val());
                var exp_valid = $.payment.validateCardExpiry(base.exp.payment('cardExpiryVal'));
                var cvc_valid = $.payment.validateCardCVC(base.cvc.val(), base.cardType);

                base._setValidationState(base.number, !num_valid);
                base._setValidationState(base.exp, !exp_valid);
                base._setValidationState(base.cvc, !cvc_valid);

                return num_valid && exp_valid && cvc_valid;
            }

            /** #### PRIVATE METHODS #### */
            Plugin.prototype._setValidationState = function(el, erred) {
                var fg = el.closest('.field');
                fg.toggleClass('error', erred).toggleClass('', !erred);
                fg.find('.payment-error-message').remove();
                if (erred) {
                    fg.append("<span class='ui pointing red basic label payment-error-message'>" + el.data(
                        'payment-error-message') + "</span>");
                }
                return this;
            }

            /**
             * EZ Logging/Warning (technically private but saving an '_' is worth it imo)
             */
            Plugin.prototype.DLOG = function() {
                if (!this.DEBUG) return;
                for (var i in arguments) {
                    console.log(PLUGIN_NS + ': ', arguments[i]);
                }
            }
            Plugin.prototype.DWARN = function() {
                this.DEBUG && console.warn(arguments);
            }


            /*###################################################################################
             * JQUERY HOOK
             ###################################################################################*/

            /**
             * Generic jQuery plugin instantiation method call logic 
             * 
             * Method options are stored via jQuery's data() method in the relevant element(s)
             * Notice, myActionMethod mustn't start with an underscore (_) as this is used to
             * indicate private methods on the PLUGIN class.   
             */
            $.fn[PLUGIN_NS] = function(methodOrOptions) {
                if (!$(this).length) {
                    return $(this);
                }
                var instance = $(this).data(PLUGIN_NS);

                // CASE: action method (public method on PLUGIN class)        
                if (instance &&
                    methodOrOptions.indexOf('_') != 0 &&
                    instance[methodOrOptions] &&
                    typeof(instance[methodOrOptions]) == 'function') {

                    return instance[methodOrOptions](Array.prototype.slice.call(arguments, 1));


                    // CASE: argument is options object or empty = initialise            
                } else if (typeof methodOrOptions === 'object' || !methodOrOptions) {

                    instance = new Plugin($(this), methodOrOptions); // ok to overwrite if this is a re-init
                    $(this).data(PLUGIN_NS, instance);
                    return $(this);

                    // CASE: method called before init
                } else if (!instance) {
                    $.error('Plugin must be initialised before using method: ' + methodOrOptions);

                    // CASE: invalid method
                } else if (methodOrOptions.indexOf('_') == 0) {
                    $.error('Method ' + methodOrOptions + ' is private!');
                } else {
                    $.error('Method ' + methodOrOptions + ' does not exist.');
                }
            };
        })(jQuery);

        /* Initialize validation */
        var payment_form = $('#payment-form').paymentForm();
    </script>
</body>

</html>
