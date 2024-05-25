@php
    $currency = ['USD', 'HKD', 'GBP', 'JPY', 'EUR', 'AUD', 'CAD', 'SGD', 'NZD', 'TWD', 'KRW', 'DKK', 'TRL', 'MYR', 'THB', 'INR', 'PHP', 'CHF', 'SEK', 'ILS', 'ZAR', 'RUB', 'NOK', 'AED', 'BRL', 'CLP', 'PEN', 'MXN', 'TND', 'AZN'];
@endphp
<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>{{ config('app.name') }} | {{ __('messages.pageTitleLink') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('setup/images/favicon.ico') }}">
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

    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/select2.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/auth.css') }}">
    <style type="text/css">
        .auth-form {
            padding: 30px;
            border-radius: 3px;
        }

        .error {
            color: #ea5455 !important;
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

        .langDropdown {
            background: #ffffff !important;
        }

        .langDropdown li:hover {
            background-color: var(--primary-1) !important;

        }

        .langDropdown .dropdown-item:hover {
            color: #ffffff !important;
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
                <div class="col-md-5 col-xl-5 col-xxl-5">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mt-2">
                                <div class="card-body gateway-card">
                                    <form method="post" action="{{ route('checkout-form', $token) }}"
                                        accept-charset="UTF-8"
                                        onsubmit="document.getElementById('disableBTN').disabled=true; document.getElementById('disableBTN')"
                                        class="validity form-dark" enctype="multipart/form-data" novalidate="true">
                                        @csrf
                                        <div class="row">
                                            @if (in_array('user_first_name', $required_fields))
                                                <div class="col-md-6 form-group mb-1">
                                                    <label class="mb-25">{{ __('messages.firstName') }} <span
                                                            class="text-danger">*</span>
                                                    </label>
                                                    <div><input type="text" name="user_first_name" class="form-control"
                                                            id="user_first_name"
                                                            placeholder="{{ __('messages.enterHere') }}"
                                                            value="{{ isset($_GET['user_first_name']) ? $_GET['user_first_name'] : '' }}"
                                                            {{ isset($_GET['user_first_name']) ? 'readonly' : '' }} required
                                                            data-missing="It's required Field"></div>
                                                    @if ($errors->has('user_first_name'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('user_first_name') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (in_array('user_last_name', $required_fields))
                                                <div class="col-md-6 form-group mb-1">
                                                    <label class="mb-25">{{ __('messages.lastName') }} <span
                                                            class="text-danger">*</span></label>
                                                    <div><input type="text" name="user_last_name" class="form-control"
                                                            id="user_last_name" placeholder="{{ __('messages.enterHere') }}"
                                                            value="{{ isset($_GET['user_last_name']) ? $_GET['user_last_name'] : '' }}"
                                                            {{ isset($_GET['user_last_name']) ? 'readonly' : '' }} required
                                                            data-missing="It's required Field"></div>
                                                    @if ($errors->has('user_last_name'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('user_last_name') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (in_array('user_email', $required_fields))
                                                <div class="col-md-6 form-group mb-1">
                                                    <label class="mb-25">{{ __('messages.email') }} <span
                                                            class="text-danger">*</span></label>
                                                    <div><input type="email" name="user_email" class="form-control"
                                                            id="user_email" placeholder="{{ __('messages.enterHere') }}"
                                                            value="{{ isset($_GET['user_email']) ? $_GET['user_email'] : '' }}"
                                                            {{ isset($_GET['user_email']) ? 'readonly' : '' }} required
                                                            data-missing="It's required Field"></div>
                                                    @if ($errors->has('user_email'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('user_email') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (in_array('user_phone_no', $required_fields))
                                                <div class="col-md-6 form-group mb-1">
                                                    <div class="row">
                                                        <!-- <div class="col-md-3">
                                                            <label
                                                                class="mb-25">{{ __('messages.countryCode') }}</strong>
                                                                <span class="text-danger">*</span></label>
                                                            <div>
                                                                <select class="select2 form-control" name="country_code"
                                                                    required data-missing="It's required Field">
                                                                    <option value="" disabled selected>Select
                                                                    </option>
                                                                    <option data-countryCode="GB" value="44">UK
                                                                        (+44)
                                                                    </option>
                                                                    <option data-countryCode="US" value="1">USA
                                                                        (+1)
                                                                    </option>
                                                                    <optgroup label="Other countries">
                                                                        <option data-countryCode="DZ" value="213">
                                                                            Algeria
                                                                            (+213)</option>
                                                                        <option data-countryCode="AD" value="376">
                                                                            Andorra
                                                                            (+376)</option>
                                                                        <option data-countryCode="AO" value="244">
                                                                            Angola
                                                                            (+244)</option>
                                                                        <option data-countryCode="AI" value="1264">
                                                                            Anguilla
                                                                            (+1264)</option>
                                                                        <option data-countryCode="AG" value="1268">
                                                                            Antigua
                                                                            &amp; Barbuda (+1268)</option>
                                                                        <option data-countryCode="AR" value="54">
                                                                            Argentina
                                                                            (+54)</option>
                                                                        <option data-countryCode="AM" value="374">
                                                                            Armenia
                                                                            (+374)</option>
                                                                        <option data-countryCode="AW" value="297">
                                                                            Aruba
                                                                            (+297)</option>
                                                                        <option data-countryCode="AU" value="61">
                                                                            Australia
                                                                            (+61)</option>
                                                                        <option data-countryCode="AT" value="43">
                                                                            Austria
                                                                            (+43)</option>
                                                                        <option data-countryCode="AZ" value="994">
                                                                            Azerbaijan
                                                                            (+994)</option>
                                                                        <option data-countryCode="BS" value="1242">
                                                                            Bahamas
                                                                            (+1242)</option>
                                                                        <option data-countryCode="BH" value="973">
                                                                            Bahrain
                                                                            (+973)</option>
                                                                        <option data-countryCode="BD" value="880">
                                                                            Bangladesh
                                                                            (+880)</option>
                                                                        <option data-countryCode="BB" value="1246">
                                                                            Barbados
                                                                            (+1246)</option>
                                                                        <option data-countryCode="BY" value="375">
                                                                            Belarus
                                                                            (+375)</option>
                                                                        <option data-countryCode="BE" value="32">
                                                                            Belgium
                                                                            (+32)</option>
                                                                        <option data-countryCode="BZ" value="501">
                                                                            Belize
                                                                            (+501)</option>
                                                                        <option data-countryCode="BJ" value="229">
                                                                            Benin
                                                                            (+229)</option>
                                                                        <option data-countryCode="BM" value="1441">
                                                                            Bermuda
                                                                            (+1441)</option>
                                                                        <option data-countryCode="BT" value="975">
                                                                            Bhutan
                                                                            (+975)</option>
                                                                        <option data-countryCode="BO" value="591">
                                                                            Bolivia
                                                                            (+591)</option>
                                                                        <option data-countryCode="BA" value="387">
                                                                            Bosnia
                                                                            Herzegovina (+387)</option>
                                                                        <option data-countryCode="BW" value="267">
                                                                            Botswana
                                                                            (+267)</option>
                                                                        <option data-countryCode="BR" value="55">
                                                                            Brazil
                                                                            (+55)</option>
                                                                        <option data-countryCode="BN" value="673">
                                                                            Brunei
                                                                            (+673)</option>
                                                                        <option data-countryCode="BG" value="359">
                                                                            Bulgaria
                                                                            (+359)</option>
                                                                        <option data-countryCode="BF" value="226">
                                                                            Burkina
                                                                            Faso (+226)</option>
                                                                        <option data-countryCode="BI" value="257">
                                                                            Burundi
                                                                            (+257)</option>
                                                                        <option data-countryCode="KH" value="855">
                                                                            Cambodia
                                                                            (+855)</option>
                                                                        <option data-countryCode="CM" value="237">
                                                                            Cameroon
                                                                            (+237)</option>
                                                                        <option data-countryCode="CA" value="1">
                                                                            Canada (+1)
                                                                        </option>
                                                                        <option data-countryCode="CV" value="238">
                                                                            Cape Verde
                                                                            Islands (+238)</option>
                                                                        <option data-countryCode="KY" value="1345">
                                                                            Cayman
                                                                            Islands (+1345)</option>
                                                                        <option data-countryCode="CF" value="236">
                                                                            Central
                                                                            African Republic (+236)</option>
                                                                        <option data-countryCode="CL" value="56">
                                                                            Chile (+56)
                                                                        </option>
                                                                        <option data-countryCode="CN" value="86">
                                                                            China (+86)
                                                                        </option>
                                                                        <option data-countryCode="CO" value="57">
                                                                            Colombia
                                                                            (+57)</option>
                                                                        <option data-countryCode="KM" value="269">
                                                                            Comoros
                                                                            (+269)</option>
                                                                        <option data-countryCode="CG" value="242">
                                                                            Congo
                                                                            (+242)</option>
                                                                        <option data-countryCode="CK" value="682">
                                                                            Cook
                                                                            Islands (+682)</option>
                                                                        <option data-countryCode="CR" value="506">
                                                                            Costa Rica
                                                                            (+506)</option>
                                                                        <option data-countryCode="HR" value="385">
                                                                            Croatia
                                                                            (+385)</option>
                                                                        <option data-countryCode="CU" value="53">
                                                                            Cuba (+53)
                                                                        </option>
                                                                        <option data-countryCode="CY" value="90392">
                                                                            Cyprus
                                                                            North (+90392)</option>
                                                                        <option data-countryCode="CY" value="357">
                                                                            Cyprus
                                                                            South (+357)</option>
                                                                        <option data-countryCode="CZ" value="42">
                                                                            Czech
                                                                            Republic (+42)</option>
                                                                        <option data-countryCode="DK" value="45">
                                                                            Denmark
                                                                            (+45)</option>
                                                                        <option data-countryCode="DJ" value="253">
                                                                            Djibouti
                                                                            (+253)</option>
                                                                        <option data-countryCode="DM" value="1809">
                                                                            Dominica
                                                                            (+1809)</option>
                                                                        <option data-countryCode="DO" value="1809">
                                                                            Dominican
                                                                            Republic (+1809)</option>
                                                                        <option data-countryCode="EC" value="593">
                                                                            Ecuador
                                                                            (+593)</option>
                                                                        <option data-countryCode="EG" value="20">
                                                                            Egypt (+20)
                                                                        </option>
                                                                        <option data-countryCode="SV" value="503">
                                                                            El
                                                                            Salvador (+503)</option>
                                                                        <option data-countryCode="GQ" value="240">
                                                                            Equatorial
                                                                            Guinea (+240)</option>
                                                                        <option data-countryCode="ER" value="291">
                                                                            Eritrea
                                                                            (+291)</option>
                                                                        <option data-countryCode="EE" value="372">
                                                                            Estonia
                                                                            (+372)</option>
                                                                        <option data-countryCode="ET" value="251">
                                                                            Ethiopia
                                                                            (+251)</option>
                                                                        <option data-countryCode="FK" value="500">
                                                                            Falkland
                                                                            Islands (+500)</option>
                                                                        <option data-countryCode="FO" value="298">
                                                                            Faroe
                                                                            Islands (+298)</option>
                                                                        <option data-countryCode="FJ" value="679">
                                                                            Fiji
                                                                            (+679)</option>
                                                                        <option data-countryCode="FI" value="358">
                                                                            Finland
                                                                            (+358)</option>
                                                                        <option data-countryCode="FR" value="33">
                                                                            France
                                                                            (+33)</option>
                                                                        <option data-countryCode="GF" value="594">
                                                                            French
                                                                            Guiana (+594)</option>
                                                                        <option data-countryCode="PF" value="689">
                                                                            French
                                                                            Polynesia (+689)</option>
                                                                        <option data-countryCode="GA" value="241">
                                                                            Gabon
                                                                            (+241)</option>
                                                                        <option data-countryCode="GM" value="220">
                                                                            Gambia
                                                                            (+220)</option>
                                                                        <option data-countryCode="GE" value="7880">
                                                                            Georgia
                                                                            (+7880)</option>
                                                                        <option data-countryCode="DE" value="49">
                                                                            Germany
                                                                            (+49)</option>
                                                                        <option data-countryCode="GH" value="233">
                                                                            Ghana
                                                                            (+233)</option>
                                                                        <option data-countryCode="GI" value="350">
                                                                            Gibraltar
                                                                            (+350)</option>
                                                                        <option data-countryCode="GR" value="30">
                                                                            Greece
                                                                            (+30)</option>
                                                                        <option data-countryCode="GL" value="299">
                                                                            Greenland
                                                                            (+299)</option>
                                                                        <option data-countryCode="GD" value="1473">
                                                                            Grenada
                                                                            (+1473)</option>
                                                                        <option data-countryCode="GP" value="590">
                                                                            Guadeloupe
                                                                            (+590)</option>
                                                                        <option data-countryCode="GU" value="671">
                                                                            Guam
                                                                            (+671)</option>
                                                                        <option data-countryCode="GT" value="502">
                                                                            Guatemala
                                                                            (+502)</option>
                                                                        <option data-countryCode="GN" value="224">
                                                                            Guinea
                                                                            (+224)</option>
                                                                        <option data-countryCode="GW" value="245">
                                                                            Guinea -
                                                                            Bissau (+245)</option>
                                                                        <option data-countryCode="GY" value="592">
                                                                            Guyana
                                                                            (+592)</option>
                                                                        <option data-countryCode="HT" value="509">
                                                                            Haiti
                                                                            (+509)</option>
                                                                        <option data-countryCode="HN" value="504">
                                                                            Honduras
                                                                            (+504)</option>
                                                                        <option data-countryCode="HK" value="852">
                                                                            Hong Kong
                                                                            (+852)</option>
                                                                        <option data-countryCode="HU" value="36">
                                                                            Hungary
                                                                            (+36)</option>
                                                                        <option data-countryCode="IS" value="354">
                                                                            Iceland
                                                                            (+354)</option>
                                                                        <option data-countryCode="IN" value="91">
                                                                            India (+91)
                                                                        </option>
                                                                        <option data-countryCode="ID" value="62">
                                                                            Indonesia
                                                                            (+62)</option>
                                                                        <option data-countryCode="IR" value="98">
                                                                            Iran (+98)
                                                                        </option>
                                                                        <option data-countryCode="IQ" value="964">
                                                                            Iraq
                                                                            (+964)</option>
                                                                        <option data-countryCode="IE" value="353">
                                                                            Ireland
                                                                            (+353)</option>
                                                                        <option data-countryCode="IL" value="972">
                                                                            Israel
                                                                            (+972)</option>
                                                                        <option data-countryCode="IT" value="39">
                                                                            Italy (+39)
                                                                        </option>
                                                                        <option data-countryCode="JM" value="1876">
                                                                            Jamaica
                                                                            (+1876)</option>
                                                                        <option data-countryCode="JP" value="81">
                                                                            Japan (+81)
                                                                        </option>
                                                                        <option data-countryCode="JO" value="962">
                                                                            Jordan
                                                                            (+962)</option>
                                                                        <option data-countryCode="KZ" value="7">
                                                                            Kazakhstan
                                                                            (+7)</option>
                                                                        <option data-countryCode="KE" value="254">
                                                                            Kenya
                                                                            (+254)</option>
                                                                        <option data-countryCode="KI" value="686">
                                                                            Kiribati
                                                                            (+686)</option>
                                                                        <option data-countryCode="KP" value="850">
                                                                            Korea
                                                                            North (+850)</option>
                                                                        <option data-countryCode="KR" value="82">
                                                                            Korea South
                                                                            (+82)</option>
                                                                        <option data-countryCode="KW" value="965">
                                                                            Kuwait
                                                                            (+965)</option>
                                                                        <option data-countryCode="KG" value="996">
                                                                            Kyrgyzstan
                                                                            (+996)</option>
                                                                        <option data-countryCode="LA" value="856">
                                                                            Laos
                                                                            (+856)</option>
                                                                        <option data-countryCode="LV" value="371">
                                                                            Latvia
                                                                            (+371)</option>
                                                                        <option data-countryCode="LB" value="961">
                                                                            Lebanon
                                                                            (+961)</option>
                                                                        <option data-countryCode="LS" value="266">
                                                                            Lesotho
                                                                            (+266)</option>
                                                                        <option data-countryCode="LR" value="231">
                                                                            Liberia
                                                                            (+231)</option>
                                                                        <option data-countryCode="LY" value="218">
                                                                            Libya
                                                                            (+218)</option>
                                                                        <option data-countryCode="LI" value="417">
                                                                            Liechtenstein (+417)</option>
                                                                        <option data-countryCode="LT" value="370">
                                                                            Lithuania
                                                                            (+370)</option>
                                                                        <option data-countryCode="LU" value="352">
                                                                            Luxembourg
                                                                            (+352)</option>
                                                                        <option data-countryCode="MO" value="853">
                                                                            Macao
                                                                            (+853)</option>
                                                                        <option data-countryCode="MK" value="389">
                                                                            Macedonia
                                                                            (+389)</option>
                                                                        <option data-countryCode="MG" value="261">
                                                                            Madagascar
                                                                            (+261)</option>
                                                                        <option data-countryCode="MW" value="265">
                                                                            Malawi
                                                                            (+265)</option>
                                                                        <option data-countryCode="MY" value="60">
                                                                            Malaysia
                                                                            (+60)</option>
                                                                        <option data-countryCode="MV" value="960">
                                                                            Maldives
                                                                            (+960)</option>
                                                                        <option data-countryCode="ML" value="223">
                                                                            Mali
                                                                            (+223)</option>
                                                                        <option data-countryCode="MT" value="356">
                                                                            Malta
                                                                            (+356)</option>
                                                                        <option data-countryCode="MH" value="692">
                                                                            Marshall
                                                                            Islands (+692)</option>
                                                                        <option data-countryCode="MQ" value="596">
                                                                            Martinique
                                                                            (+596)</option>
                                                                        <option data-countryCode="MR" value="222">
                                                                            Mauritania
                                                                            (+222)</option>
                                                                        <option data-countryCode="YT" value="269">
                                                                            Mayotte
                                                                            (+269)</option>
                                                                        <option data-countryCode="MX" value="52">
                                                                            Mexico
                                                                            (+52)</option>
                                                                        <option data-countryCode="FM" value="691">
                                                                            Micronesia
                                                                            (+691)</option>
                                                                        <option data-countryCode="MD" value="373">
                                                                            Moldova
                                                                            (+373)</option>
                                                                        <option data-countryCode="MC" value="377">
                                                                            Monaco
                                                                            (+377)</option>
                                                                        <option data-countryCode="MN" value="976">
                                                                            Mongolia
                                                                            (+976)</option>
                                                                        <option data-countryCode="MS" value="1664">
                                                                            Montserrat (+1664)</option>
                                                                        <option data-countryCode="MA" value="212">
                                                                            Morocco
                                                                            (+212)</option>
                                                                        <option data-countryCode="MZ" value="258">
                                                                            Mozambique
                                                                            (+258)</option>
                                                                        <option data-countryCode="MN" value="95">
                                                                            Myanmar
                                                                            (+95)</option>
                                                                        <option data-countryCode="NA" value="264">
                                                                            Namibia
                                                                            (+264)</option>
                                                                        <option data-countryCode="NR" value="674">
                                                                            Nauru
                                                                            (+674)</option>
                                                                        <option data-countryCode="NP" value="977">
                                                                            Nepal
                                                                            (+977)</option>
                                                                        <option data-countryCode="NL" value="31">
                                                                            Netherlands
                                                                            (+31)</option>
                                                                        <option data-countryCode="NC" value="687">
                                                                            New
                                                                            Caledonia (+687)</option>
                                                                        <option data-countryCode="NZ" value="64">
                                                                            New Zealand
                                                                            (+64)</option>
                                                                        <option data-countryCode="NI" value="505">
                                                                            Nicaragua
                                                                            (+505)</option>
                                                                        <option data-countryCode="NE" value="227">
                                                                            Niger
                                                                            (+227)</option>
                                                                        <option data-countryCode="NG" value="234">
                                                                            Nigeria
                                                                            (+234)</option>
                                                                        <option data-countryCode="NU" value="683">
                                                                            Niue
                                                                            (+683)</option>
                                                                        <option data-countryCode="NF" value="672">
                                                                            Norfolk
                                                                            Islands (+672)</option>
                                                                        <option data-countryCode="NP" value="670">
                                                                            Northern
                                                                            Marianas (+670)</option>
                                                                        <option data-countryCode="NO" value="47">
                                                                            Norway
                                                                            (+47)</option>
                                                                        <option data-countryCode="OM" value="968">
                                                                            Oman
                                                                            (+968)</option>
                                                                        <option data-countryCode="PW" value="92">
                                                                            Pakistan
                                                                            (+92)</option>
                                                                        <option data-countryCode="PW" value="680">
                                                                            Palau
                                                                            (+680)</option>
                                                                        <option data-countryCode="PA" value="507">
                                                                            Panama
                                                                            (+507)</option>
                                                                        <option data-countryCode="PG" value="675">
                                                                            Papua New
                                                                            Guinea (+675)</option>
                                                                        <option data-countryCode="PY" value="595">
                                                                            Paraguay
                                                                            (+595)</option>
                                                                        <option data-countryCode="PE" value="51">
                                                                            Peru (+51)
                                                                        </option>
                                                                        <option data-countryCode="PH" value="63">
                                                                            Philippines
                                                                            (+63)</option>
                                                                        <option data-countryCode="PL" value="48">
                                                                            Poland
                                                                            (+48)</option>
                                                                        <option data-countryCode="PT" value="351">
                                                                            Portugal
                                                                            (+351)</option>
                                                                        <option data-countryCode="PR" value="1787">
                                                                            Puerto
                                                                            Rico (+1787)</option>
                                                                        <option data-countryCode="QA" value="974">
                                                                            Qatar
                                                                            (+974)</option>
                                                                        <option data-countryCode="RE" value="262">
                                                                            Reunion
                                                                            (+262)</option>
                                                                        <option data-countryCode="RO" value="40">
                                                                            Romania
                                                                            (+40)</option>
                                                                        <option data-countryCode="RU" value="7">
                                                                            Russia (+7)
                                                                        </option>
                                                                        <option data-countryCode="RW" value="250">
                                                                            Rwanda
                                                                            (+250)</option>
                                                                        <option data-countryCode="SM" value="378">
                                                                            San Marino
                                                                            (+378)</option>
                                                                        <option data-countryCode="ST" value="239">
                                                                            Sao Tome
                                                                            &amp; Principe (+239)</option>
                                                                        <option data-countryCode="SA" value="966">
                                                                            Saudi
                                                                            Arabia (+966)</option>
                                                                        <option data-countryCode="SN" value="221">
                                                                            Senegal
                                                                            (+221)</option>
                                                                        <option data-countryCode="CS" value="381">
                                                                            Serbia
                                                                            (+381)</option>
                                                                        <option data-countryCode="SC" value="248">
                                                                            Seychelles
                                                                            (+248)</option>
                                                                        <option data-countryCode="SL" value="232">
                                                                            Sierra
                                                                            Leone (+232)</option>
                                                                        <option data-countryCode="SG" value="65">
                                                                            Singapore
                                                                            (+65)</option>
                                                                        <option data-countryCode="SK" value="421">
                                                                            Slovak
                                                                            Republic (+421)</option>
                                                                        <option data-countryCode="SI" value="386">
                                                                            Slovenia
                                                                            (+386)</option>
                                                                        <option data-countryCode="SB" value="677">
                                                                            Solomon
                                                                            Islands (+677)</option>
                                                                        <option data-countryCode="SO" value="252">
                                                                            Somalia
                                                                            (+252)</option>
                                                                        <option data-countryCode="ZA" value="27">
                                                                            South
                                                                            Africa (+27)</option>
                                                                        <option data-countryCode="ES" value="34">
                                                                            Spain (+34)
                                                                        </option>
                                                                        <option data-countryCode="LK" value="94">
                                                                            Sri Lanka
                                                                            (+94)</option>
                                                                        <option data-countryCode="SH" value="290">
                                                                            St. Helena
                                                                            (+290)</option>
                                                                        <option data-countryCode="KN" value="1869">
                                                                            St. Kitts
                                                                            (+1869)</option>
                                                                        <option data-countryCode="SC" value="1758">
                                                                            St. Lucia
                                                                            (+1758)</option>
                                                                        <option data-countryCode="SD" value="249">
                                                                            Sudan
                                                                            (+249)</option>
                                                                        <option data-countryCode="SR" value="597">
                                                                            Suriname
                                                                            (+597)</option>
                                                                        <option data-countryCode="SZ" value="268">
                                                                            Swaziland
                                                                            (+268)</option>
                                                                        <option data-countryCode="SE" value="46">
                                                                            Sweden
                                                                            (+46)</option>
                                                                        <option data-countryCode="CH" value="41">
                                                                            Switzerland
                                                                            (+41)</option>
                                                                        <option data-countryCode="SI" value="963">
                                                                            Syria
                                                                            (+963)</option>
                                                                        <option data-countryCode="TW" value="886">
                                                                            Taiwan
                                                                            (+886)</option>
                                                                        <option data-countryCode="TJ" value="7">
                                                                            Tajikstan
                                                                            (+7)</option>
                                                                        <option data-countryCode="TH" value="66">
                                                                            Thailand
                                                                            (+66)</option>
                                                                        <option data-countryCode="TG" value="228">
                                                                            Togo
                                                                            (+228)</option>
                                                                        <option data-countryCode="TO" value="676">
                                                                            Tonga
                                                                            (+676)</option>
                                                                        <option data-countryCode="TT" value="1868">
                                                                            Trinidad
                                                                            &amp; Tobago (+1868)</option>
                                                                        <option data-countryCode="TN" value="216">
                                                                            Tunisia
                                                                            (+216)</option>
                                                                        <option data-countryCode="TR" value="90">
                                                                            Turkey
                                                                            (+90)</option>
                                                                        <option data-countryCode="TM" value="7">
                                                                            Turkmenistan
                                                                            (+7)</option>
                                                                        <option data-countryCode="TM" value="993">
                                                                            Turkmenistan (+993)</option>
                                                                        <option data-countryCode="TC" value="1649">
                                                                            Turks
                                                                            &amp; Caicos Islands (+1649)</option>
                                                                        <option data-countryCode="TV" value="688">
                                                                            Tuvalu
                                                                            (+688)</option>
                                                                        <option data-countryCode="UG" value="256">
                                                                            Uganda
                                                                            (+256)</option>
                                                                        <option data-countryCode="UA" value="380">
                                                                            Ukraine
                                                                            (+380)</option>
                                                                        <option data-countryCode="AE" value="971">
                                                                            United
                                                                            Arab Emirates (+971)</option>
                                                                        <option data-countryCode="UY" value="598">
                                                                            Uruguay
                                                                            (+598)</option>
                                                                        <option data-countryCode="UZ" value="7">
                                                                            Uzbekistan
                                                                            (+7)</option>
                                                                        <option data-countryCode="VU" value="678">
                                                                            Vanuatu
                                                                            (+678)</option>
                                                                        <option data-countryCode="VA" value="379">
                                                                            Vatican
                                                                            City (+379)</option>
                                                                        <option data-countryCode="VE" value="58">
                                                                            Venezuela
                                                                            (+58)</option>
                                                                        <option data-countryCode="VN" value="84">
                                                                            Vietnam
                                                                            (+84)</option>
                                                                        <option data-countryCode="VG" value="84">
                                                                            Virgin
                                                                            Islands - British (+1284)</option>
                                                                        <option data-countryCode="VI" value="84">
                                                                            Virgin
                                                                            Islands - US (+1340)</option>
                                                                        <option data-countryCode="WF" value="681">
                                                                            Wallis
                                                                            &amp; Futuna (+681)</option>
                                                                        <option data-countryCode="YE" value="969">
                                                                            Yemen
                                                                            (North)(+969)</option>
                                                                        <option data-countryCode="YE" value="967">
                                                                            Yemen
                                                                            (South)(+967)</option>
                                                                        <option data-countryCode="ZM" value="260">
                                                                            Zambia
                                                                            (+260)</option>
                                                                        <option data-countryCode="ZW" value="263">
                                                                            Zimbabwe
                                                                            (+263)</option>
                                                                    </optgroup>
                                                                </select>
                                                            </div>
                                                        </div> -->
                                                        <div class="col-md-12">
                                                            <label
                                                                class="mb-25">{{ __('messages.phoneNo') }}</strong>
                                                                <span class="text-danger">*</span></label>
                                                            <div><input class="form-control" name="user_phone_no"
                                                                    type="text"
                                                                    placeholder="{{ __('messages.enterHere') }}"
                                                                    id="user_phone_no"
                                                                    value="{{ isset($_GET['user_phone_no']) ? $_GET['user_phone_no'] : '' }}"
                                                                    {{ isset($_GET['user_phone_no']) ? 'readonly' : '' }}
                                                                    required data-missing="It's required Field">
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                    @if ($errors->has('country_code'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('country_code') }}</strong>
                                                        </span>
                                                    @endif
                                                    @if ($errors->has('user_phone_no'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('user_phone_no') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (in_array('user_currency', $required_fields))
                                                <div class="col-md-12 form-group mb-1">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="mb-25">{{ __('messages.currency') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <div>
                                                                <select class="form-control" name="user_currency"
                                                                    id="user_currency" required
                                                                    data-missing="It's required Field">
                                                                    @if (isset($iframe_array['currency']) &&
                                                                            in_array($iframe_array['currency'], $currency) &&
                                                                            !empty($iframe_array['currency']))
                                                                        <option
                                                                            value="{{ $iframe_array['currency'] }}">
                                                                            {{ $iframe_array['currency'] }}</option>
                                                                    @else
                                                                        <option value="" selected disabled>Select
                                                                        </option>
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
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="mb-25">{{ __('messages.amount') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <div><input class="form-control" name="user_amount"
                                                                    type="text"
                                                                    placeholder="{{ __('messages.enterHere') }}"
                                                                    id="user_amount"
                                                                    value="{{ isset($iframe_array['amount']) ? $iframe_array['amount'] : '' }}"
                                                                    {{ isset($iframe_array['amount']) ? 'readonly' : '' }}
                                                                    required data-missing="It's required Field">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('user_amount'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('user_amount') }}</strong>
                                                        </span>
                                                    @endif
                                                    @if ($errors->has('user_currency'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('user_currency') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (in_array('user_address', $required_fields))
                                                <div class="col-md-12 form-group mb-1">
                                                    <label class="mb-25">{{ __('messages.address') }} <span
                                                            class="text-danger">*</span></label>
                                                    <div>
                                                        <textarea class="form-control" name="user_address" id="user_address" placeholder="{{ __('messages.enterHere') }}"
                                                            {{ isset($_GET['user_address']) ? 'readonly' : '' }} required data-missing="It's required Field">{{ isset($_GET['address']) ? $_GET['address'] : '' }}</textarea>
                                                    </div>
                                                    @if ($errors->has('user_address'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('user_address') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (in_array('user_country', $required_fields))
                                                <div class="col-md-6 form-group mb-1">
                                                    <label class="mb-25">{{ __('messages.country') }} <span
                                                            class="text-danger">*</span></label>
                                                    @if (isset($_GET['country']))
                                                        <text" name="user_country" class="form-control"
                                                            value="{{ isset($_GET['user_country']) ? $_GET['user_country'] : '' }}"
                                                            {{ isset($_GET['country']) ? 'readonly' : '' }} required
                                                            data-missing="It's required Field">
                                                        @else
                                                            <div>
                                                                <select class="form-control select2" name="user_country"
                                                                    id="country11" required
                                                                    data-missing="It's required Field">
                                                                    <option disabled selected value="">Select
                                                                    </option>
                                                                    @foreach (getCountry() as $key => $value)
                                                                        <option value="{{ $key }}">
                                                                            {{ $value }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                    @endif
                                                    @if ($errors->has('user_country'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('user_country') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (in_array('user_zip', $required_fields))
                                                <div class="col-md-6 form-group mb-1">
                                                    <label class="mb-25">{{ __('messages.zipCode') }} <span
                                                            class="text-danger">*</span></label>
                                                    <div><input class="form-control" name="user_zip" type="text"
                                                            id="user_zip"
                                                            placeholder="{{ __('messages.enterHere') }}"
                                                            value="{{ isset($_GET['user_zip']) ? $_GET['user_zip'] : '' }}"
                                                            {{ isset($_GET['user_zip']) ? 'readonly' : '' }} required
                                                            data-missing="It's required Field"></div>
                                                    @if ($errors->has('user_zip'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('user_zip') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (in_array('user_city', $required_fields))
                                                <div class="col-md-6 form-group mb-1">
                                                    <label class="mb-25">{{ __('messages.city') }} <span
                                                            class="text-danger">*</span></label>
                                                    <div><input class="form-control" name="user_city" type="text"
                                                            id="user_city"
                                                            placeholder="{{ __('messages.enterHere') }}"
                                                            value="{{ isset($_GET['user_city']) ? $_GET['user_city'] : '' }}"
                                                            {{ isset($_GET['user_city']) ? 'readonly' : '' }} required
                                                            data-missing="It's required Field"></div>
                                                    @if ($errors->has('user_city'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('user_city') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (in_array('user_state', $required_fields))
                                                <div class="col-md-6 form-group mb-1">
                                                    <label class="mb-25">{{ __('messages.state') }} <span
                                                            class="text-danger">*</span></label>
                                                    <div><input type="text" name="user_state"
                                                            placeholder="{{ __('messages.enterHere') }}"
                                                            id="user_state" class="form-control"
                                                            value="{{ isset($_GET['user_state']) ? $_GET['user_state'] : '' }}"
                                                            {{ isset($_GET['state']) ? 'readonly' : '' }} required
                                                            data-missing="It's required Field"></div>
                                                    @if ($errors->has('user_state'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('user_state') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            <!-- <div class="col-md-6 mt-1">
                                                <button type="button" id="disableBTN"
                                                    class="btn btn-primary w-100">{{ __('messages.cancel') }}</button>
                                            </div> -->
                                            <div class="col-md-12 mt-1 ">
                                                <button type="submit"
                                                    class="btn btn-danger w-100">{{ __('messages.payNow') }}</button>
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
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.js'></script>

    <script src="{{ storage_asset('themeAdmin/js/select2.min.js') }}"></script>

    <script src="{{ storage_asset('setup/js/jquery.validity.js') }}"></script>

    <script>
        // var url = "{{ route('change.lang') }}"

        jQuery(document).ready(function() {
            jQuery("#load").fadeOut();
            jQuery("#loading").delay().fadeOut("");

            // $(document).on("click", ".langListBtn", function() {
            //     var val = $(this).attr("data-lang");
            //     window.location.href = url + "?lang=" + val;
            // });
        });
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
        $(".select2").select2();
    </script>
</body>

</html>
