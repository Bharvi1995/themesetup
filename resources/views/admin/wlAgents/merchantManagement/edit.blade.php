@extends('layouts.admin.default')

@section('title')
    White Label RP Merchant Management Edit
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('wl-agents.index') }}">White Label RP</a> / <a
        href="{{ route('wl-agent-merchant', $data->white_label_agent_id) }}">Merchant Management</a> / Edit
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Merchant Edit</h4>
                    </div>
                    <a href="{{ route('wl-agent-merchant', $data->white_label_agent_id) }}"
                        class="btn btn-primary btn-sm rounded"><i class="fa fa-arrow-left"></i>
                    </a>
                </div>
                <div class="card-body">
                    {{ Form::model($data, ['route' => ['wl-agent-merchant-update', $data->id], 'method' => 'PUT', 'class' => 'form form-dark form-horizontal', 'enctype' => 'multipart/form-data']) }}
                    @if (\Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="alert-body">{{ \Session::get('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">

                            </button>
                        </div>
                    @endif
                    {{ \Session::forget('success') }}
                    @if (\Session::get('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="alert-body">{{ \Session::get('error') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">

                            </button>
                        </div>
                    @endif
                    {!! csrf_field() !!}
                    <input type="hidden" name="wl_rp_id" value="{{ $data->white_label_agent_id }}">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="text-danger"><b>Merchant Info</b></h5>
                            <hr>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="text">Name</label>
                                    {!! Form::text('name', $data->name, ['placeholder' => 'Enter here...', 'class' => 'form-control']) !!}
                                    @if ($errors->has('name'))
                                        <span class="help-block font-red-mint text-danger">
                                            <span>{{ $errors->first('name') }}</span>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="text">Email</label>
                                    {!! Form::text('email', $data->email, ['placeholder' => 'Enter here...', 'class' => 'form-control']) !!}
                                    @if ($errors->has('email'))
                                        <span class="help-block font-red-mint text-danger">
                                            <span>{{ $errors->first('email') }}</span>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="">Mobile</label>
                                    <div class="main-select-phone d-flex justify-content-between align-items-center">
                                        <select class="select2 btn-primary fill_selectbtn_in own_selectbox"
                                            name="country_code" data-size="7" data-live-search="true"
                                            data-title="-- Select Country Code --" id="country" data-width="100%">
                                            <option value="44" {{ $data->country_code == 44 ? 'selected' : '' }}>UK
                                                (+44)</option>
                                            <option value="1" {{ $data->country_code == 1 ? 'selected' : '' }}>USA
                                                (+1)</option>
                                            <option data-countryCode="DZ"
                                                {{ $data->country_code == 213 ? 'selected' : '' }} value="213">
                                                Algeria (+213)</option>
                                            <option data-countryCode="AD"
                                                {{ $data->country_code == 376 ? 'selected' : '' }} value="376">
                                                Andorra (+376)</option>
                                            <option data-countryCode="AO"
                                                {{ $data->country_code == 244 ? 'selected' : '' }} value="244">
                                                Angola (+244)</option>
                                            <option data-countryCode="AI"
                                                {{ $data->country_code == 1264 ? 'selected' : '' }} value="1264">Anguilla
                                                (+1264)</option>
                                            <option data-countryCode="AG"
                                                {{ $data->country_code == 1268 ? 'selected' : '' }} value="1268">Antigua
                                                &amp;
                                                Barbuda (+1268)</option>
                                            <option data-countryCode="AR" {{ $data->country_code == 54 ? 'selected' : '' }}
                                                value="54">
                                                Argentina (+54)</option>
                                            <option data-countryCode="AM"
                                                {{ $data->country_code == 374 ? 'selected' : '' }} value="374">
                                                Armenia (+374)</option>
                                            <option data-countryCode="AW"
                                                {{ $data->country_code == 297 ? 'selected' : '' }} value="297">
                                                Aruba (+297)</option>
                                            <option data-countryCode="AU" {{ $data->country_code == 61 ? 'selected' : '' }}
                                                value="61">
                                                Australia (+61)</option>
                                            <option data-countryCode="AT" {{ $data->country_code == 43 ? 'selected' : '' }}
                                                value="43">
                                                Austria (+43)</option>
                                            <option data-countryCode="AZ"
                                                {{ $data->country_code == 994 ? 'selected' : '' }} value="994">
                                                Azerbaijan (+994)</option>
                                            <option data-countryCode="BS"
                                                {{ $data->country_code == 1242 ? 'selected' : '' }} value="1242">Bahamas
                                                (+1242)</option>
                                            <option data-countryCode="BH"
                                                {{ $data->country_code == 973 ? 'selected' : '' }} value="973">
                                                Bahrain (+973)</option>
                                            <option data-countryCode="BD"
                                                {{ $data->country_code == 880 ? 'selected' : '' }} value="880">
                                                Bangladesh (+880)</option>
                                            <option data-countryCode="BB"
                                                {{ $data->country_code == 1246 ? 'selected' : '' }} value="1246">Barbados
                                                (+1246)</option>
                                            <option data-countryCode="BY"
                                                {{ $data->country_code == 375 ? 'selected' : '' }} value="375">
                                                Belarus (+375)</option>
                                            <option data-countryCode="BE" {{ $data->country_code == 32 ? 'selected' : '' }}
                                                value="32">
                                                Belgium (+32)</option>
                                            <option data-countryCode="BZ"
                                                {{ $data->country_code == 501 ? 'selected' : '' }} value="501">
                                                Belize (+501)</option>
                                            <option data-countryCode="BJ"
                                                {{ $data->country_code == 229 ? 'selected' : '' }} value="229">
                                                Benin (+229)</option>
                                            <option data-countryCode="BM"
                                                {{ $data->country_code == 1441 ? 'selected' : '' }} value="1441">Bermuda
                                                (+1441)</option>
                                            <option data-countryCode="BT"
                                                {{ $data->country_code == 975 ? 'selected' : '' }} value="975">
                                                Bhutan (+975)</option>
                                            <option data-countryCode="BO"
                                                {{ $data->country_code == 591 ? 'selected' : '' }} value="591">
                                                Bolivia (+591)</option>
                                            <option data-countryCode="BA"
                                                {{ $data->country_code == 387 ? 'selected' : '' }} value="387">
                                                Bosnia Herzegovina (+387)</option>
                                            <option data-countryCode="BW"
                                                {{ $data->country_code == 267 ? 'selected' : '' }} value="267">
                                                Botswana (+267)</option>
                                            <option data-countryCode="BR"
                                                {{ $data->country_code == 55 ? 'selected' : '' }} value="55">
                                                Brazil (+55)</option>
                                            <option data-countryCode="BN"
                                                {{ $data->country_code == 673 ? 'selected' : '' }} value="673">
                                                Brunei (+673)</option>
                                            <option data-countryCode="BG"
                                                {{ $data->country_code == 359 ? 'selected' : '' }} value="359">
                                                Bulgaria (+359)</option>
                                            <option data-countryCode="BF"
                                                {{ $data->country_code == 226 ? 'selected' : '' }} value="226">
                                                Burkina Faso (+226)</option>
                                            <option data-countryCode="BI"
                                                {{ $data->country_code == 257 ? 'selected' : '' }} value="257">
                                                Burundi (+257)</option>
                                            <option data-countryCode="KH"
                                                {{ $data->country_code == 855 ? 'selected' : '' }} value="855">
                                                Cambodia (+855)</option>
                                            <option data-countryCode="CM"
                                                {{ $data->country_code == 237 ? 'selected' : '' }} value="237">
                                                Cameroon (+237)</option>
                                            <option data-countryCode="CA" {{ $data->country_code == 1 ? 'selected' : '' }}
                                                value="1">
                                                Canada (+1)</option>
                                            <option data-countryCode="CV"
                                                {{ $data->country_code == 238 ? 'selected' : '' }} value="238">
                                                Cape Verde Islands (+238)</option>
                                            <option data-countryCode="KY"
                                                {{ $data->country_code == 1345 ? 'selected' : '' }} value="1345">Cayman
                                                Islands (+1345)</option>
                                            <option data-countryCode="CF"
                                                {{ $data->country_code == 236 ? 'selected' : '' }} value="236">
                                                Central African Republic (+236)</option>
                                            <option data-countryCode="CL"
                                                {{ $data->country_code == 56 ? 'selected' : '' }} value="56">
                                                Chile (+56)</option>
                                            <option data-countryCode="CN"
                                                {{ $data->country_code == 86 ? 'selected' : '' }} value="86">
                                                China (+86)</option>
                                            <option data-countryCode="CO"
                                                {{ $data->country_code == 57 ? 'selected' : '' }} value="57">
                                                Colombia (+57)</option>
                                            <option data-countryCode="KM"
                                                {{ $data->country_code == 269 ? 'selected' : '' }} value="269">
                                                Comoros (+269)</option>
                                            <option data-countryCode="CG"
                                                {{ $data->country_code == 242 ? 'selected' : '' }} value="242">
                                                Congo (+242)</option>
                                            <option data-countryCode="CK"
                                                {{ $data->country_code == 682 ? 'selected' : '' }} value="682">
                                                Cook Islands (+682)</option>
                                            <option data-countryCode="CR"
                                                {{ $data->country_code == 506 ? 'selected' : '' }} value="506">
                                                Costa Rica (+506)</option>
                                            <option data-countryCode="HR"
                                                {{ $data->country_code == 385 ? 'selected' : '' }} value="385">
                                                Croatia (+385)</option>
                                            <option data-countryCode="CU"
                                                {{ $data->country_code == 53 ? 'selected' : '' }} value="53">
                                                Cuba (+53)</option>
                                            <option data-countryCode="CY"
                                                {{ $data->country_code == 90392 ? 'selected' : '' }} value="90392">Cyprus
                                                North (+90392)</option>
                                            <option data-countryCode="CY"
                                                {{ $data->country_code == 357 ? 'selected' : '' }} value="357">
                                                Cyprus South (+357)</option>
                                            <option data-countryCode="CZ"
                                                {{ $data->country_code == 42 ? 'selected' : '' }} value="42">
                                                Czech Republic (+42)</option>
                                            <option data-countryCode="DK"
                                                {{ $data->country_code == 45 ? 'selected' : '' }} value="45">
                                                Denmark (+45)</option>
                                            <option data-countryCode="DJ"
                                                {{ $data->country_code == 253 ? 'selected' : '' }} value="253">
                                                Djibouti (+253)</option>
                                            <option data-countryCode="DM"
                                                {{ $data->country_code == 1767 ? 'selected' : '' }} value="1767">
                                                Dominica
                                                (+1767)</option>
                                            <option data-countryCode="DO"
                                                {{ $data->country_code == 1809 ? 'selected' : '' }} value="1809">
                                                Dominican
                                                Republic (+1809)</option>
                                            <option data-countryCode="EC"
                                                {{ $data->country_code == 593 ? 'selected' : '' }} value="593">
                                                Ecuador (+593)</option>
                                            <option data-countryCode="EG"
                                                {{ $data->country_code == 20 ? 'selected' : '' }} value="20">
                                                Egypt (+20)</option>
                                            <option data-countryCode="SV"
                                                {{ $data->country_code == 503 ? 'selected' : '' }} value="503">
                                                El Salvador (+503)</option>
                                            <option data-countryCode="GQ"
                                                {{ $data->country_code == 240 ? 'selected' : '' }} value="240">
                                                Equatorial Guinea (+240)</option>
                                            <option data-countryCode="ER"
                                                {{ $data->country_code == 291 ? 'selected' : '' }} value="291">
                                                Eritrea (+291)</option>
                                            <option data-countryCode="EE"
                                                {{ $data->country_code == 372 ? 'selected' : '' }} value="372">
                                                Estonia (+372)</option>
                                            <option data-countryCode="ET"
                                                {{ $data->country_code == 251 ? 'selected' : '' }} value="251">
                                                Ethiopia (+251)</option>
                                            <option data-countryCode="FK"
                                                {{ $data->country_code == 500 ? 'selected' : '' }} value="500">
                                                Falkland Islands (+500)</option>
                                            <option data-countryCode="FO"
                                                {{ $data->country_code == 298 ? 'selected' : '' }} value="298">
                                                Faroe Islands (+298)</option>
                                            <option data-countryCode="FJ"
                                                {{ $data->country_code == 679 ? 'selected' : '' }} value="679">
                                                Fiji (+679)</option>
                                            <option data-countryCode="FI"
                                                {{ $data->country_code == 358 ? 'selected' : '' }} value="358">
                                                Finland (+358)</option>
                                            <option data-countryCode="FR"
                                                {{ $data->country_code == 33 ? 'selected' : '' }} value="33">
                                                France (+33)</option>
                                            <option data-countryCode="GF"
                                                {{ $data->country_code == 594 ? 'selected' : '' }} value="594">
                                                French Guiana (+594)</option>
                                            <option data-countryCode="PF"
                                                {{ $data->country_code == 689 ? 'selected' : '' }} value="689">
                                                French Polynesia (+689)</option>
                                            <option data-countryCode="GA"
                                                {{ $data->country_code == 241 ? 'selected' : '' }} value="241">
                                                Gabon (+241)</option>
                                            <option data-countryCode="GM"
                                                {{ $data->country_code == 220 ? 'selected' : '' }} value="220">
                                                Gambia (+220)</option>
                                            <option data-countryCode="GE"
                                                {{ $data->country_code == 995 ? 'selected' : '' }} value="995">
                                                Georgia (+995)</option>
                                            <option data-countryCode="DE"
                                                {{ $data->country_code == 49 ? 'selected' : '' }} value="49">
                                                Germany (+49)</option>
                                            <option data-countryCode="GH"
                                                {{ $data->country_code == 233 ? 'selected' : '' }} value="233">
                                                Ghana (+233)</option>
                                            <option data-countryCode="GI"
                                                {{ $data->country_code == 350 ? 'selected' : '' }} value="350">
                                                Gibraltar (+350)</option>
                                            <option data-countryCode="GR"
                                                {{ $data->country_code == 30 ? 'selected' : '' }} value="30">
                                                Greece (+30)</option>
                                            <option data-countryCode="GL"
                                                {{ $data->country_code == 299 ? 'selected' : '' }} value="299">
                                                Greenland (+299)</option>
                                            <option data-countryCode="GD"
                                                {{ $data->country_code == 1473 ? 'selected' : '' }} value="1473">Grenada
                                                (+1473)</option>
                                            <option data-countryCode="GP"
                                                {{ $data->country_code == 590 ? 'selected' : '' }} value="590">
                                                Guadeloupe (+590)</option>
                                            <option data-countryCode="GU"
                                                {{ $data->country_code == 671 ? 'selected' : '' }} value="671">
                                                Guam (+671)</option>
                                            <option data-countryCode="GT"
                                                {{ $data->country_code == 502 ? 'selected' : '' }} value="502">
                                                Guatemala (+502)</option>
                                            <option data-countryCode="GN"
                                                {{ $data->country_code == 224 ? 'selected' : '' }} value="224">
                                                Guinea (+224)</option>
                                            <option data-countryCode="GW"
                                                {{ $data->country_code == 245 ? 'selected' : '' }} value="245">
                                                Guinea - Bissau (+245)</option>
                                            <option data-countryCode="GY"
                                                {{ $data->country_code == 592 ? 'selected' : '' }} value="592">
                                                Guyana (+592)</option>
                                            <option data-countryCode="HT"
                                                {{ $data->country_code == 509 ? 'selected' : '' }} value="509">
                                                Haiti (+509)</option>
                                            <option data-countryCode="HN"
                                                {{ $data->country_code == 504 ? 'selected' : '' }} value="504">
                                                Honduras (+504)</option>
                                            <option data-countryCode="HK"
                                                {{ $data->country_code == 852 ? 'selected' : '' }} value="852">
                                                Hong Kong (+852)</option>
                                            <option data-countryCode="HU"
                                                {{ $data->country_code == 36 ? 'selected' : '' }} value="36">
                                                Hungary (+36)</option>
                                            <option data-countryCode="IS"
                                                {{ $data->country_code == 354 ? 'selected' : '' }} value="354">
                                                Iceland (+354)</option>
                                            <option data-countryCode="IN"
                                                {{ $data->country_code == 91 ? 'selected' : '' }} value="91">
                                                India (+91)</option>
                                            <option data-countryCode="ID"
                                                {{ $data->country_code == 62 ? 'selected' : '' }} value="62">
                                                Indonesia (+62)</option>
                                            <option data-countryCode="IR"
                                                {{ $data->country_code == 98 ? 'selected' : '' }} value="98">
                                                Iran (+98)</option>
                                            <option data-countryCode="IQ"
                                                {{ $data->country_code == 964 ? 'selected' : '' }} value="964">
                                                Iraq (+964)</option>
                                            <option data-countryCode="IE"
                                                {{ $data->country_code == 353 ? 'selected' : '' }} value="353">
                                                Ireland (+353)</option>
                                            <option data-countryCode="IL"
                                                {{ $data->country_code == 972 ? 'selected' : '' }} value="972">
                                                Israel (+972)</option>
                                            <option data-countryCode="IT"
                                                {{ $data->country_code == 39 ? 'selected' : '' }} value="39">
                                                Italy (+39)</option>
                                            <option data-countryCode="JM"
                                                {{ $data->country_code == 1876 ? 'selected' : '' }} value="1876">
                                                Jamaica
                                                (+1876)</option>
                                            <option data-countryCode="JP"
                                                {{ $data->country_code == 81 ? 'selected' : '' }} value="81">
                                                Japan (+81)</option>
                                            <option data-countryCode="JO"
                                                {{ $data->country_code == 962 ? 'selected' : '' }} value="962">
                                                Jordan (+962)</option>
                                            <option data-countryCode="KZ"
                                                {{ $data->country_code == 7 ? 'selected' : '' }} value="7">
                                                Kazakhstan (+7)</option>
                                            <option data-countryCode="KE"
                                                {{ $data->country_code == 254 ? 'selected' : '' }} value="254">
                                                Kenya (+254)</option>
                                            <option data-countryCode="KI"
                                                {{ $data->country_code == 686 ? 'selected' : '' }} value="686">
                                                Kiribati (+686)</option>
                                            <option data-countryCode="KP"
                                                {{ $data->country_code == 850 ? 'selected' : '' }} value="850">
                                                Korea North (+850)</option>
                                            <option data-countryCode="KR"
                                                {{ $data->country_code == 82 ? 'selected' : '' }} value="82">
                                                Korea South (+82)</option>
                                            <option data-countryCode="KW"
                                                {{ $data->country_code == 965 ? 'selected' : '' }} value="965">
                                                Kuwait (+965)</option>
                                            <option data-countryCode="KG"
                                                {{ $data->country_code == 996 ? 'selected' : '' }} value="996">
                                                Kyrgyzstan (+996)</option>
                                            <option data-countryCode="LA"
                                                {{ $data->country_code == 856 ? 'selected' : '' }} value="856">
                                                Laos (+856)</option>
                                            <option data-countryCode="LV"
                                                {{ $data->country_code == 371 ? 'selected' : '' }} value="371">
                                                Latvia (+371)</option>
                                            <option data-countryCode="LB"
                                                {{ $data->country_code == 961 ? 'selected' : '' }} value="961">
                                                Lebanon (+961)</option>
                                            <option data-countryCode="LS"
                                                {{ $data->country_code == 266 ? 'selected' : '' }} value="266">
                                                Lesotho (+266)</option>
                                            <option data-countryCode="LR"
                                                {{ $data->country_code == 231 ? 'selected' : '' }} value="231">
                                                Liberia (+231)</option>
                                            <option data-countryCode="LY"
                                                {{ $data->country_code == 218 ? 'selected' : '' }} value="218">
                                                Libya (+218)</option>
                                            <option data-countryCode="LI"
                                                {{ $data->country_code == 417 ? 'selected' : '' }} value="417">
                                                Liechtenstein (+417)</option>
                                            <option data-countryCode="LT"
                                                {{ $data->country_code == 370 ? 'selected' : '' }} value="370">
                                                Lithuania (+370)</option>
                                            <option data-countryCode="LU"
                                                {{ $data->country_code == 352 ? 'selected' : '' }} value="352">
                                                Luxembourg (+352)</option>
                                            <option data-countryCode="MO"
                                                {{ $data->country_code == 853 ? 'selected' : '' }} value="853">
                                                Macao (+853)</option>
                                            <option data-countryCode="MK"
                                                {{ $data->country_code == 389 ? 'selected' : '' }} value="389">
                                                Macedonia (+389)</option>
                                            <option data-countryCode="MG"
                                                {{ $data->country_code == 261 ? 'selected' : '' }} value="261">
                                                Madagascar (+261)</option>
                                            <option data-countryCode="MW"
                                                {{ $data->country_code == 265 ? 'selected' : '' }} value="265">
                                                Malawi (+265)</option>
                                            <option data-countryCode="MY"
                                                {{ $data->country_code == 60 ? 'selected' : '' }} value="60">
                                                Malaysia (+60)</option>
                                            <option data-countryCode="MV"
                                                {{ $data->country_code == 960 ? 'selected' : '' }} value="960">
                                                Maldives (+960)</option>
                                            <option data-countryCode="ML"
                                                {{ $data->country_code == 223 ? 'selected' : '' }} value="223">
                                                Mali (+223)</option>
                                            <option data-countryCode="MT"
                                                {{ $data->country_code == 356 ? 'selected' : '' }} value="356">
                                                Malta (+356)</option>
                                            <option data-countryCode="MH"
                                                {{ $data->country_code == 692 ? 'selected' : '' }} value="692">
                                                Marshall Islands (+692)</option>
                                            <option data-countryCode="MQ"
                                                {{ $data->country_code == 596 ? 'selected' : '' }} value="596">
                                                Martinique (+596)</option>
                                            <option data-countryCode="MR"
                                                {{ $data->country_code == 222 ? 'selected' : '' }} value="222">
                                                Mauritania (+222)</option>
                                            <option data-countryCode="YT"
                                                {{ $data->country_code == 269 ? 'selected' : '' }} value="269">
                                                Mayotte (+269)</option>
                                            <option data-countryCode="MX"
                                                {{ $data->country_code == 52 ? 'selected' : '' }} value="52">
                                                Mexico (+52)</option>
                                            <option data-countryCode="FM"
                                                {{ $data->country_code == 691 ? 'selected' : '' }} value="691">
                                                Micronesia (+691)</option>
                                            <option data-countryCode="MD"
                                                {{ $data->country_code == 373 ? 'selected' : '' }} value="373">
                                                Moldova (+373)</option>
                                            <option data-countryCode="MC"
                                                {{ $data->country_code == 377 ? 'selected' : '' }} value="377">
                                                Monaco (+377)</option>
                                            <option data-countryCode="MN"
                                                {{ $data->country_code == 976 ? 'selected' : '' }} value="976">
                                                Mongolia (+976)</option>
                                            <option data-countryCode="MS"
                                                {{ $data->country_code == 1664 ? 'selected' : '' }} value="1664">
                                                Montserrat
                                                (+1664)</option>
                                            <option data-countryCode="MA"
                                                {{ $data->country_code == 212 ? 'selected' : '' }} value="212">
                                                Morocco (+212)</option>
                                            <option data-countryCode="MZ"
                                                {{ $data->country_code == 258 ? 'selected' : '' }} value="258">
                                                Mozambique (+258)</option>
                                            <option data-countryCode="MN"
                                                {{ $data->country_code == 95 ? 'selected' : '' }} value="95">
                                                Myanmar (+95)</option>
                                            <option data-countryCode="NA"
                                                {{ $data->country_code == 264 ? 'selected' : '' }} value="264">
                                                Namibia (+264)</option>
                                            <option data-countryCode="NR"
                                                {{ $data->country_code == 674 ? 'selected' : '' }} value="674">
                                                Nauru (+674)</option>
                                            <option data-countryCode="NP"
                                                {{ $data->country_code == 977 ? 'selected' : '' }} value="977">
                                                Nepal (+977)</option>
                                            <option data-countryCode="NL"
                                                {{ $data->country_code == 31 ? 'selected' : '' }} value="31">
                                                Netherlands (+31)</option>
                                            <option data-countryCode="NC"
                                                {{ $data->country_code == 687 ? 'selected' : '' }} value="687">
                                                New Caledonia (+687)</option>
                                            <option data-countryCode="NZ"
                                                {{ $data->country_code == 64 ? 'selected' : '' }} value="64">
                                                New Zealand (+64)</option>
                                            <option data-countryCode="NI"
                                                {{ $data->country_code == 505 ? 'selected' : '' }} value="505">
                                                Nicaragua (+505)</option>
                                            <option data-countryCode="NE"
                                                {{ $data->country_code == 227 ? 'selected' : '' }} value="227">
                                                Niger (+227)</option>
                                            <option data-countryCode="NG"
                                                {{ $data->country_code == 234 ? 'selected' : '' }} value="234">
                                                Nigeria (+234)</option>
                                            <option data-countryCode="NU"
                                                {{ $data->country_code == 683 ? 'selected' : '' }} value="683">
                                                Niue (+683)</option>
                                            <option data-countryCode="NF"
                                                {{ $data->country_code == 672 ? 'selected' : '' }} value="672">
                                                Norfolk Islands (+672)</option>
                                            <option data-countryCode="NP"
                                                {{ $data->country_code == 670 ? 'selected' : '' }} value="670">
                                                Northern Marianas (+670)</option>
                                            <option data-countryCode="NO"
                                                {{ $data->country_code == 47 ? 'selected' : '' }} value="47">
                                                Norway (+47)</option>
                                            <option data-countryCode="OM"
                                                {{ $data->country_code == 968 ? 'selected' : '' }} value="968">
                                                Oman (+968)</option>
                                            <option data-countryCode="PW"
                                                {{ $data->country_code == 680 ? 'selected' : '' }} value="680">
                                                Palau (+680)</option>
                                            <option data-countryCode="PA"
                                                {{ $data->country_code == 507 ? 'selected' : '' }} value="507">
                                                Panama (+507)</option>
                                            <option data-countryCode="PG"
                                                {{ $data->country_code == 675 ? 'selected' : '' }} value="675">
                                                Papua New Guinea (+675)</option>
                                            <option data-countryCode="PY"
                                                {{ $data->country_code == 595 ? 'selected' : '' }} value="595">
                                                Paraguay (+595)</option>
                                            <option data-countryCode="PE"
                                                {{ $data->country_code == 51 ? 'selected' : '' }} value="51">
                                                Peru (+51)</option>
                                            <option data-countryCode="PH"
                                                {{ $data->country_code == 63 ? 'selected' : '' }} value="63">
                                                Philippines (+63)</option>
                                            <option data-countryCode="PL"
                                                {{ $data->country_code == 48 ? 'selected' : '' }} value="48">
                                                Poland (+48)</option>
                                            <option data-countryCode="PT"
                                                {{ $data->country_code == 351 ? 'selected' : '' }} value="351">
                                                Portugal (+351)</option>
                                            <option data-countryCode="PR"
                                                {{ $data->country_code == 1787 ? 'selected' : '' }} value="1787">Puerto
                                                Rico
                                                (+1787)</option>
                                            <option data-countryCode="QA"
                                                {{ $data->country_code == 974 ? 'selected' : '' }} value="974">
                                                Qatar (+974)</option>
                                            <option data-countryCode="RE"
                                                {{ $data->country_code == 262 ? 'selected' : '' }} value="262">
                                                Reunion (+262)</option>
                                            <option data-countryCode="RO"
                                                {{ $data->country_code == 40 ? 'selected' : '' }} value="40">
                                                Romania (+40)</option>
                                            <option data-countryCode="RU"
                                                {{ $data->country_code == 7 ? 'selected' : '' }} value="7">
                                                Russia (+7)</option>
                                            <option data-countryCode="RW"
                                                {{ $data->country_code == 250 ? 'selected' : '' }} value="250">
                                                Rwanda (+250)</option>
                                            <option data-countryCode="SM"
                                                {{ $data->country_code == 378 ? 'selected' : '' }} value="378">
                                                San Marino (+378)</option>
                                            <option data-countryCode="ST"
                                                {{ $data->country_code == 239 ? 'selected' : '' }} value="239">
                                                Sao Tome &amp; Principe (+239)</option>
                                            <option data-countryCode="SA"
                                                {{ $data->country_code == 966 ? 'selected' : '' }} value="966">
                                                Saudi Arabia (+966)</option>
                                            <option data-countryCode="SN"
                                                {{ $data->country_code == 221 ? 'selected' : '' }} value="221">
                                                Senegal (+221)</option>
                                            <option data-countryCode="CS"
                                                {{ $data->country_code == 381 ? 'selected' : '' }} value="381">
                                                Serbia (+381)</option>
                                            <option data-countryCode="SC"
                                                {{ $data->country_code == 248 ? 'selected' : '' }} value="248">
                                                Seychelles (+248)</option>
                                            <option data-countryCode="SL"
                                                {{ $data->country_code == 232 ? 'selected' : '' }} value="232">
                                                Sierra Leone (+232)</option>
                                            <option data-countryCode="SG"
                                                {{ $data->country_code == 65 ? 'selected' : '' }} value="65">
                                                Singapore (+65)</option>
                                            <option data-countryCode="SK"
                                                {{ $data->country_code == 421 ? 'selected' : '' }} value="421">
                                                Slovak Republic (+421)</option>
                                            <option data-countryCode="SI"
                                                {{ $data->country_code == 386 ? 'selected' : '' }} value="386">
                                                Slovenia (+386)</option>
                                            <option data-countryCode="SB"
                                                {{ $data->country_code == 677 ? 'selected' : '' }} value="677">
                                                Solomon Islands (+677)</option>
                                            <option data-countryCode="SO"
                                                {{ $data->country_code == 252 ? 'selected' : '' }} value="252">
                                                Somalia (+252)</option>
                                            <option data-countryCode="ZA"
                                                {{ $data->country_code == 27 ? 'selected' : '' }} value="27">
                                                South Africa (+27)</option>
                                            <option data-countryCode="ES"
                                                {{ $data->country_code == 34 ? 'selected' : '' }} value="34">
                                                Spain (+34)</option>
                                            <option data-countryCode="LK"
                                                {{ $data->country_code == 94 ? 'selected' : '' }} value="94">
                                                Sri Lanka (+94)</option>
                                            <option data-countryCode="SH"
                                                {{ $data->country_code == 290 ? 'selected' : '' }} value="290">
                                                St. Helena (+290)</option>
                                            <option data-countryCode="KN"
                                                {{ $data->country_code == 1869 ? 'selected' : '' }} value="1869">St.
                                                Kitts
                                                (+1869)</option>
                                            <option data-countryCode="SC"
                                                {{ $data->country_code == 1758 ? 'selected' : '' }} value="1758">St.
                                                Lucia
                                                (+1758)</option>
                                            <option data-countryCode="SD"
                                                {{ $data->country_code == 249 ? 'selected' : '' }} value="249">
                                                Sudan (+249)</option>
                                            <option data-countryCode="SR"
                                                {{ $data->country_code == 597 ? 'selected' : '' }} value="597">
                                                Suriname (+597)</option>
                                            <option data-countryCode="SZ"
                                                {{ $data->country_code == 268 ? 'selected' : '' }} value="268">
                                                Swaziland (+268)</option>
                                            <option data-countryCode="SE"
                                                {{ $data->country_code == 46 ? 'selected' : '' }} value="46">
                                                Sweden (+46)</option>
                                            <option data-countryCode="CH"
                                                {{ $data->country_code == 41 ? 'selected' : '' }} value="41">
                                                Switzerland (+41)</option>
                                            <option data-countryCode="SI"
                                                {{ $data->country_code == 963 ? 'selected' : '' }} value="963">
                                                Syria (+963)</option>
                                            <option data-countryCode="TW"
                                                {{ $data->country_code == 886 ? 'selected' : '' }} value="886">
                                                Taiwan (+886)</option>
                                            <option data-countryCode="TJ"
                                                {{ $data->country_code == 992 ? 'selected' : '' }} value="992">
                                                Tajikstan (+992)</option>
                                            <option data-countryCode="TH"
                                                {{ $data->country_code == 66 ? 'selected' : '' }} value="66">
                                                Thailand (+66)</option>
                                            <option data-countryCode="TG"
                                                {{ $data->country_code == 228 ? 'selected' : '' }} value="228">
                                                Togo (+228)</option>
                                            <option data-countryCode="TO"
                                                {{ $data->country_code == 676 ? 'selected' : '' }} value="676">
                                                Tonga (+676)</option>
                                            <option data-countryCode="TT"
                                                {{ $data->country_code == 1868 ? 'selected' : '' }} value="1868">
                                                Trinidad
                                                &amp; Tobago (+1868)</option>
                                            <option data-countryCode="TN"
                                                {{ $data->country_code == 216 ? 'selected' : '' }} value="216">
                                                Tunisia (+216)</option>
                                            <option data-countryCode="TR"
                                                {{ $data->country_code == 90 ? 'selected' : '' }} value="90">
                                                Turkey (+90)</option>
                                            <option data-countryCode="TM"
                                                {{ $data->country_code == 7 ? 'selected' : '' }} value="7">
                                                Turkmenistan (+7)</option>
                                            <option data-countryCode="TM"
                                                {{ $data->country_code == 993 ? 'selected' : '' }} value="993">
                                                Turkmenistan (+993)</option>
                                            <option data-countryCode="TC"
                                                {{ $data->country_code == 1649 ? 'selected' : '' }} value="1649">Turks
                                                &amp;
                                                Caicos Islands (+1649)</option>
                                            <option data-countryCode="TV"
                                                {{ $data->country_code == 688 ? 'selected' : '' }} value="688">
                                                Tuvalu (+688)</option>
                                            <option data-countryCode="UG"
                                                {{ $data->country_code == 256 ? 'selected' : '' }} value="256">
                                                Uganda (+256)</option>
                                            <option data-countryCode="UA"
                                                {{ $data->country_code == 380 ? 'selected' : '' }} value="380">
                                                Ukraine (+380)</option>
                                            <option data-countryCode="AE"
                                                {{ $data->country_code == 971 ? 'selected' : '' }} value="971">
                                                United Arab Emirates (+971)</option>
                                            <option data-countryCode="UY"
                                                {{ $data->country_code == 598 ? 'selected' : '' }} value="598">
                                                Uruguay (+598)</option>
                                            <option data-countryCode="UZ"
                                                {{ $data->country_code == 998 ? 'selected' : '' }} value="998">
                                                Uzbekistan (+998)</option>
                                            <option data-countryCode="VU"
                                                {{ $data->country_code == 678 ? 'selected' : '' }} value="678">
                                                Vanuatu (+678)</option>
                                            <option data-countryCode="VA"
                                                {{ $data->country_code == 379 ? 'selected' : '' }} value="379">
                                                Vatican City (+379)</option>
                                            <option data-countryCode="VE"
                                                {{ $data->country_code == 58 ? 'selected' : '' }} value="58">
                                                Venezuela (+58)</option>
                                            <option data-countryCode="VN"
                                                {{ $data->country_code == 84 ? 'selected' : '' }} value="84">
                                                Vietnam (+84)</option>
                                            <option data-countryCode="VG"
                                                {{ $data->country_code == 84 ? 'selected' : '' }} value="84">
                                                Virgin Islands - British (+1284)</option>
                                            <option data-countryCode="VI"
                                                {{ $data->country_code == 84 ? 'selected' : '' }} value="84">
                                                Virgin Islands - US (+1340)</option>
                                            <option data-countryCode="WF"
                                                {{ $data->country_code == 681 ? 'selected' : '' }} value="681">
                                                Wallis &amp; Futuna (+681)</option>
                                            <option data-countryCode="YE"
                                                {{ $data->country_code == 969 ? 'selected' : '' }} value="969">
                                                Yemen (North)(+969)</option>
                                            <option data-countryCode="YE"
                                                {{ $data->country_code == 967 ? 'selected' : '' }} value="967">
                                                Yemen (South)(+967)</option>
                                            <option data-countryCode="ZM"
                                                {{ $data->country_code == 260 ? 'selected' : '' }} value="260">
                                                Zambia (+260)</option>
                                            <option data-countryCode="ZW"
                                                {{ $data->country_code == 263 ? 'selected' : '' }} value="263">
                                                Zimbabwe (+263)</option>
                                        </select>
                                        {!! Form::text('mobile_no', Input::get('mobile_no'), [
                                            'placeholder' => 'Enter here...',
                                            'class' => 'form-control',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('country_code'))
                                        <span class="help-block font-red-mint text-danger">
                                            <span>{{ $errors->first('country_code') }}</span>
                                        </span>
                                    @endif
                                    @if ($errors->has('mobile_no'))
                                        <span class="help-block font-red-mint text-danger">
                                            <span>{{ $errors->first('mobile_no') }}</span>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="">Provide Login</label>
                                    <select class="form-control" name="is_login_wl_merchant">
                                        <option value="1" {{ $data->is_login_wl_merchant == 1 ? 'selected' : '' }}>
                                            Yes</option>
                                        <option value="0" {{ $data->is_login_wl_merchant == 0 ? 'selected' : '' }}>
                                            No</option>
                                    </select>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="">Password</label>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="Enter here...">
                                    @if ($errors->has('password'))
                                        <span class="help-block font-red-mint text-danger">
                                            <span>{{ $errors->first('password') }}</span>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control" placeholder="Enter here...">
                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block font-red-mint text-danger">
                                            <span>{{ $errors->first('password_confirmation') }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <h5 class="text-danger"><b>Company Info</b></h5>
                            <hr>
                            <div class="row border-left">
                                <div class="form-group col-lg-6">
                                    <label for="business_name">Company Name<span class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::text('business_name', $data->business_name, [
                                            'placeholder' => 'Enter here...',
                                            'class' => 'form-control',
                                            'id' => 'business_name',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('business_name'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('business_name') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="text">Business Category<span class="text-danger">*</span></label>
                                    <div class="input-div">
                                        {!! Form::select(
                                            'business_type',
                                            [
                                                '' => 'Select',
                                                'Sole Proprietorship' => 'Sole
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        							        Proprietorship',
                                                'Partnership' => 'Partnership',
                                                'Corporation' => 'Corporation/LTD',
                                            ],
                                            [isset($data->business_type) ? $data->business_type : null],
                                            ['class' => 'form-control select2', 'id' => 'business_type'],
                                        ) !!}
                                    </div>
                                    @if ($errors->has('business_type'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('business_type') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="website_url">Your Website URL<span class="text-danger">*</span> <small
                                            class="text-danger" style="font-size: 10px;">https://example.com</small>
                                    </label>
                                    <div class="input-div">
                                        {!! Form::text('website_url', $data->website_url, [
                                            'placeholder' => 'Enter here...',
                                            'class' => 'form-control',
                                            'id' => 'website_url',
                                        ]) !!}
                                    </div>
                                    @if ($errors->has('website_url'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('website_url') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="category_id">Industry Type<span class="text-danger">*</span></label>
                                    <input type="hidden" class="oldIndustryType" value="{{ old('category_id') }}" />

                                    <div class="input-div">
                                        <select name="category_id" id="category_id" class="form-control select2"
                                            data-width="100%" onchange="otherIndustryType(this.value)">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($category as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ old('category_id') == $key ? 'selected' : '' }}
                                                    {{ isset($data->category_id) ? ($data->category_id == $key ? 'selected' : '') : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($errors->has('category_id'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('category_id') }}
                                        </span>
                                    @endif

                                    <div class="form-group mt-2 d-none showOtherIndustryInput">
                                        <label for="category_id">Miscellaneous<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control showOtherIndustryInputBox"
                                            placeholder="Enter here.." name="other_industry_type"
                                            value="{{ $data->other_industry_type }}" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-3">
                            <div class="basic-form">
                                <div class="table-responsive custom-table">
                                    <table class="table table-borderless table-striped">
                                        <thead>
                                            <tr>
                                                <th>Document Name</th>
                                                <th>Document</th>
                                                <th style="width: 150px;">Add More</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tab_logic">
                                            @if (isset($data->wl_extra_document))
                                                @foreach (json_decode($data->wl_extra_document) as $key => $extra_document)
                                                    <tr>
                                                        <td>{{ $key }}</td>
                                                        <td>
                                                            <a href="{{ getS3Url($extra_document) }}" target="_blank"
                                                                class="btn btn-primary btn-sm">View</a>
                                                            <a href="{{ route('downloadDocumentsUploadeAdmin', ['file' => $extra_document]) }}"
                                                                class="btn btn-danger btn-sm">Download</a>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach
                                            @endif

                                            @if (Input::old('wl_extra_document') != '')
                                                <div id="countVar"
                                                    data-count="{{ count(Input::old('wl_extra_document')) }}"></div>
                                                @foreach (Input::old('wl_extra_document') as $key => $value)
                                                    <tr data-id={{ $key == 0 ? $key + 1 : $key }}>
                                                        <td>
                                                            <input placeholder="Enter here..." class="form-control"
                                                                name="wl_extra_document[{{ $key }}][name]"
                                                                type="text"
                                                                value="{{ old('wl_extra_document.' . $key . '.name') }}">
                                                            @if ($errors->has('wl_extra_document.' . $key . '.name'))
                                                                <span class="text-danger help-block form-error">
                                                                    {{ $errors->first('wl_extra_document.' . $key . '.name') }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="custom-file">
                                                                <input type="file"
                                                                    class="custom-file-input form-control"
                                                                    id="validationCustomFile-{{ $key }}"
                                                                    name="wl_extra_document[{{ $key }}][document]">
                                                                <label class="custom-file-label"
                                                                    for="validationCustomFile-{{ $key }}">
                                                                </label>
                                                            </div>
                                                            @if ($errors->has('wl_extra_document.' . $key . '.document'))
                                                                <span class="text-danger help-block form-error">
                                                                    {{ $errors->first('wl_extra_document.' . $key . '.document') }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($key == 0)
                                                                <button type="button"
                                                                    class="btn btn-primary btn-sm plus">
                                                                    <i class="fa fa-plus"></i>
                                                                </button>
                                                            @else
                                                                <button type="button"
                                                                    class="btn btn-primary btn-sm plus">
                                                                    <i class="fa fa-plus"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm minus"> <i
                                                                        class="fa fa-minus"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <div id="countVar" data-count="0"></div>
                                                <tr data-id="1">
                                                    <td>
                                                        <input placeholder="Enter here..." class="form-control"
                                                            name="wl_extra_document[0][name]" type="text"
                                                            value="{{ old('wl_extra_document.0.name') }}">
                                                        @if ($errors->has('wl_extra_document.0.name'))
                                                            <span class="text-danger help-block form-error">
                                                                {{ $errors->first('wl_extra_document.0.name') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input form-control"
                                                                id="validationCustomFile"
                                                                name="wl_extra_document[0][document]">

                                                        </div>
                                                        @if ($errors->has('wl_extra_document.0.document'))
                                                            <span class="text-danger help-block form-error">
                                                                {{ $errors->first('wl_extra_document.0.document') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-primary btn-sm plus"> <i
                                                                class="fa fa-plus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-3">
                            <div class="basic-form">
                                <div class="table-responsive custom-table">
                                    <table class="table table-borderless table-striped">
                                        <thead>
                                            <tr class="table-active">
                                                <th>Website Name</th>
                                                <th>IP</th>
                                                <th style="width: 150px;">Add More</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tab_logic_ip">
                                            @if (Input::old('website') != '')
                                                <div id="countVar" data-count="{{ count(Input::old('website')) }}">
                                                </div>
                                                @foreach (Input::old('website') as $key => $value)
                                                    <tr data-id={{ $key == 0 ? $key + 1 : $key }}>
                                                        <td>
                                                            <input placeholder="Enter here..." class="form-control"
                                                                name="website[]" type="text"
                                                                value="{{ old('website.' . $key . '') }}">
                                                            @if ($errors->has('website.' . $key . ''))
                                                                <span class="text-danger help-block form-error">
                                                                    {{ $errors->first('website.' . $key . '') }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="text" name="ip[]" class="form-control"
                                                                value="{{ old('ip.' . $key) }}"
                                                                placeholder="Enter here...">
                                                            @if ($errors->has('ip.' . $key))
                                                                <span class="text-danger help-block form-error">
                                                                    {{ $errors->first('ip.' . $key) }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($key == 0)
                                                                <button type="button" class="btn btn-primary plus-ip"> <i
                                                                        class="fa fa-plus"></i>
                                                                </button>
                                                            @else
                                                                <button type="button" class="btn btn-primary plus-ip"> <i
                                                                        class="fa fa-plus"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-danger minus"> <i
                                                                        class="fa fa-minus"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                @if (isset($data->websiteUrl))
                                                    <div id="countVar" data-count="{{ count($data->websiteUrl) }}">
                                                    </div>
                                                    @foreach ($data->websiteUrl as $key => $value)
                                                        <tr data-id={{ $key == 0 ? $key + 1 : $key }}>
                                                            <td>
                                                                <input placeholder="Enter here..." class="form-control"
                                                                    name="website[]" type="text"
                                                                    value="{{ $value->website_name }}">
                                                                @if ($errors->has('website.' . $key . ''))
                                                                    <span class="text-danger help-block form-error">
                                                                        {{ $errors->first('website.' . $key . '') }}
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <input type="text" name="ip[]" class="form-control"
                                                                    value="{{ $value->ip_address }}"
                                                                    placeholder="Enter here...">
                                                                @if ($errors->has('ip.' . $key))
                                                                    <span class="text-danger help-block form-error">
                                                                        {{ $errors->first('ip.' . $key) }}
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button"
                                                                    class="btn btn-primary btn-sm plus-ip"> <i
                                                                        class="fa fa-plus"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-lg-12 mt-2">
                            <button type="submit" class="btn btn-primary ">Submit</button>
                            <a href="{{ route('wl-agent-merchant', $data->white_label_agent_id) }}"
                                class="btn btn-danger ">Cancel</a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script type="text/javascript">
        $(document).ready(function() {
            var oldIndustryTypeVal = $('#category_id').val()
            otherIndustryType(oldIndustryTypeVal)
        });

        function otherIndustryType(val) {
            if (val == 28) {
                $('.showOtherIndustryInput').removeClass('d-none')
            } else {
                $('.showOtherIndustryInputBox').val('')
                $('.showOtherIndustryInput').addClass('d-none')
            }
        }

        $("body").on("click", ".plus", function() {
            // i = $('#tab_logic tr').length;
            var i = $("#tab_logic tr:last").data("id");
            i = i + 1;
            $("#tab_logic").append(
                '<tr data-id="' +
                i +
                '">\
                                                                                            	            <td>\
                                                                                            	                <input placeholder="Enter here..." class="form-control" name="wl_extra_document[' +
                i +
                '][name]" type="text">\
                                                                                            	            </td>\
                                                                                            	            <td>\
                                                                                            	            <div class="custom-file">\
                                                                                            		                <input type="file" class="custom-file-input form-control" id="validationCustomFile' +
                i +
                '" name="wl_extra_document[' + i +
                '][document]">\
                                                                                            		                \
                                                                                            		            </div>\
                                                                                            	            </td>\
                                                                                            	            <td class="text-center">\
                                                                                            	                <button type="button" class="btn btn-primary btn-sm plus"> <i class="fa fa-plus"></i> </button>\
                                                                                            	                <button type="button" class="btn btn-danger btn-sm minus"> <i class="fa fa-minus"></i> </button>\
                                                                                            	            </td>\
                                                                                            	        </tr>'
            );
            // i++;

        });
        $("body").on("click", ".minus", function() {
            $(this).closest("tr").remove();
            // i--;
        });

        $("body").on('click', '.plus-ip', function() {
            var i = $("#tab_logic_ip tr:last").data('id')
            i = i + 1
            $("#tab_logic_ip").append(
                `<tr data-id="${i}">
				<td>
					<input placeholder="Enter here..." class="form-control"
						name="website[]" type="text"
						value="">
				</td>
				<td>
					<input type="text" class="form-control" placeholder="Enter here.." name="ip[]">
				</td>
				<td class="text-center">
					<button type="button" class="btn btn-primary btn-sm plus-ip"> <i class="fa fa-plus"></i>
					</button>
					<button type="button" class="btn btn-danger btn-sm minus"> <i class="fa fa-minus"></i> </button>\
				</td>
			</tr>`
            )
        })
    </script>
@endsection
