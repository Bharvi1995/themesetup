
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    @if(config('app.env') == 'production')
        <link rel="shortcut icon" type="image/x-icon" href="{{ storage_asset('softtheme/img/favicon.ico') }}">
    @endif
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="{{ storage_asset('softtheme/css/nucleo-icons.css')}}" rel="stylesheet" />
    <link href="{{ storage_asset('softtheme/css/nucleo-svg.css')}}" rel="stylesheet" />
    <link href="{{ storage_asset('softtheme/css/nucleo-svg.css')}}" rel="stylesheet" />
    <link id="pagestyle" href="{{ storage_asset('softtheme/css/soft-ui-dashboard.min.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/fontawesome.min.css"/>
</head>
<body class="light-theme">
    <nav class="navbar navbar-expand-lg position-absolute top-0 z-index-3 w-100 shadow-none my-3 navbar-transparent mt-4">
    <div class="container">
      <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 text-white" href="pages/dashboard.html">
      Soft UI Dashboard
      </a>
      <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon mt-2">
      <span class="navbar-toggler-bar bar1"></span>
      <span class="navbar-toggler-bar bar2"></span>
      <span class="navbar-toggler-bar bar3"></span>
      </span>
      </button>
   </div>
</nav>
<main class="main-content  mt-0">
   <section class="min-vh-100 mb-8">
      <div class="page-header align-items-start min-vh-50 pt-5 pb-11 m-3 border-radius-lg" style="background-image: url('storage/softtheme/img/curved-images/curved14.jpg');">
         <span class="mask bg-gradient-dark opacity-6"></span>
         <div class="container">
            <div class="row justify-content-center">
               <div class="col-lg-5 text-center mx-auto">
                  <h1 class="text-white mb-2 mt-5">Welcome!</h1>
                  <p class="text-lead text-white">Use these awesome forms to login or create new account in Merchant Panel for free.</p>
               </div>
            </div>
         </div>
      </div>
      <div class="container">
         <div class="row mt-lg-n10 mt-md-n11 mt-n10">
            <div class="col-xl-5 col-lg-5 col-md-7 mx-auto">
               <div class="card z-index-0">
                    <div class="card-header text-center pt-4">
                        <h5>Registration</h5>
                    </div>
                    <div class="card-body">
                    @if(\Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="alert-body">
                            {{ \Session::get('success') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
                    </div>
                    @endif
                    {{ \Session::forget('success') }}
                    @if(\Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="alert-body">
                            {{ \Session::get('error') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
                    </div>
                    @endif
                    {{ \Session::forget('error') }}
                    
                    <form action="{{ route('applynow-store') }}" id="signup-form" method="post">
                        {!! csrf_field() !!}
                        @if(app('request')->input('RP') && app('request')->input('RP') != '')
                            <input type="hidden" name="RP" value="{{ app('request')->input('RP') }}">
                        @endif
                        <div class="mb-3">
                           <input type="text" class="form-control" placeholder="Name" aria-label="Name" aria-describedby="email-addon" name="name" value="{{ old('name') }}">
                           @if ($errors->has('name'))
                            <span class="help-block font-red-mint text-danger">
                                {{ $errors->first('name') }}
                            </span>
                            @endif
                        </div>
                        <div class="mb-3">
                           <input type="email" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="email-addon" name="email" value="{{ old('email') }}">
                           @if ($errors->has('email'))
                            <span class="help-block font-red-mint text-danger">
                                {{ $errors->first('email') }}
                            </span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-4 pr-0">
                                    <select class="form-control select2" name="country_code">
                                        <option value="" selected disabled>-Code-</option>
                                        <option data-countryCode="DZ" {{ old('country_code')=='213'
                                            ?'selected':'' }} value="213">Algeria (+213)</option>
                                        <option data-countryCode="AD" {{ old('country_code')=='376'
                                            ?'selected':'' }} value="376">Andorra (+376)</option>
                                        <option data-countryCode="AO" {{ old('country_code')=='244'
                                            ?'selected':'' }} value="244">Angola (+244)</option>
                                        <option data-countryCode="AI" {{ old('country_code')=='1264'
                                            ?'selected':'' }} value="1264">Anguilla (+1264)</option>
                                        <option data-countryCode="AG" {{ old('country_code')=='1268'
                                            ?'selected':'' }} value="1268">Antigua &amp; Barbuda (+1268)
                                        </option>
                                        <option data-countryCode="AR" {{ old('country_code')=='54'
                                            ?'selected':'' }} value="54">Argentina (+54)</option>
                                        <option data-countryCode="AM" {{ old('country_code')=='374'
                                            ?'selected':'' }} value="374">Armenia (+374)</option>
                                        <option data-countryCode="AW" {{ old('country_code')=='297'
                                            ?'selected':'' }} value="297">Aruba (+297)</option>
                                        <option data-countryCode="AU" {{ old('country_code')=='61'
                                            ?'selected':'' }} value="61">Australia (+61)</option>
                                        <option data-countryCode="AT" {{ old('country_code')=='43'
                                            ?'selected':'' }} value="43">Austria (+43)</option>
                                        <option data-countryCode="AZ" {{ old('country_code')=='994'
                                            ?'selected':'' }} value="994">Azerbaijan (+994)</option>
                                        <option data-countryCode="BS" {{ old('country_code')=='1242'
                                            ?'selected':'' }} value="1242">Bahamas (+1242)</option>
                                        <option data-countryCode="BH" {{ old('country_code')=='973'
                                            ?'selected':'' }} value="973">Bahrain (+973)</option>
                                        <option data-countryCode="BD" {{ old('country_code')=='880'
                                            ?'selected':'' }} value="880">Bangladesh (+880)</option>
                                        <option data-countryCode="BB" {{ old('country_code')=='1246'
                                            ?'selected':'' }} value="1246">Barbados (+1246)</option>
                                        <option data-countryCode="BY" {{ old('country_code')=='375'
                                            ?'selected':'' }} value="375">Belarus (+375)</option>
                                        <option data-countryCode="BE" {{ old('country_code')=='32'
                                            ?'selected':'' }} value="32">Belgium (+32)</option>
                                        <option data-countryCode="BZ" {{ old('country_code')=='501'
                                            ?'selected':'' }} value="501">Belize (+501)</option>
                                        <option data-countryCode="BJ" {{ old('country_code')=='229'
                                            ?'selected':'' }} value="229">Benin (+229)</option>
                                        <option data-countryCode="BM" {{ old('country_code')=='1441'
                                            ?'selected':'' }} value="1441">Bermuda (+1441)</option>
                                        <option data-countryCode="BT" {{ old('country_code')=='975'
                                            ?'selected':'' }} value="975">Bhutan (+975)</option>
                                        <option data-countryCode="BO" {{ old('country_code')=='591'
                                            ?'selected':'' }} value="591">Bolivia (+591)</option>
                                        <option data-countryCode="BA" {{ old('country_code')=='387'
                                            ?'selected':'' }} value="387">Bosnia Herzegovina (+387)</option>
                                        <option data-countryCode="BW" {{ old('country_code')=='267'
                                            ?'selected':'' }} value="267">Botswana (+267)</option>
                                        <option data-countryCode="BR" {{ old('country_code')=='55'
                                            ?'selected':'' }} value="55">Brazil (+55)</option>
                                        <option data-countryCode="BN" {{ old('country_code')=='673'
                                            ?'selected':'' }} value="673">Brunei (+673)</option>
                                        <option data-countryCode="BG" {{ old('country_code')=='359'
                                            ?'selected':'' }} value="359">Bulgaria (+359)</option>
                                        <option data-countryCode="BF" {{ old('country_code')=='226'
                                            ?'selected':'' }} value="226">Burkina Faso (+226)</option>
                                        <option data-countryCode="BI" {{ old('country_code')=='257'
                                            ?'selected':'' }} value="257">Burundi (+257)</option>
                                        <option data-countryCode="KH" {{ old('country_code')=='855'
                                            ?'selected':'' }} value="855">Cambodia (+855)</option>
                                        <option data-countryCode="CM" {{ old('country_code')=='237'
                                            ?'selected':'' }} value="237">Cameroon (+237)</option>
                                        <option data-countryCode="CA" {{ old('country_code')=='1' ?'selected':''
                                            }} value="1">Canada (+1)</option>
                                        <option data-countryCode="CV" {{ old('country_code')=='238'
                                            ?'selected':'' }} value="238">Cape Verde Islands (+238)</option>
                                        <option data-countryCode="KY" {{ old('country_code')=='1345'
                                            ?'selected':'' }} value="1345">Cayman Islands (+1345)</option>
                                        <option data-countryCode="CF" {{ old('country_code')=='236'
                                            ?'selected':'' }} value="236">Central African Republic (+236)
                                        </option>
                                        <option data-countryCode="CL" {{ old('country_code')=='56'
                                            ?'selected':'' }} value="56">Chile (+56)</option>
                                        <option data-countryCode="CN" {{ old('country_code')=='86'
                                            ?'selected':'' }} value="86">China (+86)</option>
                                        <option data-countryCode="CO" {{ old('country_code')=='57'
                                            ?'selected':'' }} value="57">Colombia (+57)</option>
                                        <option data-countryCode="KM" {{ old('country_code')=='269'
                                            ?'selected':'' }} value="269">Comoros (+269)</option>
                                        <option data-countryCode="CG" {{ old('country_code')=='242'
                                            ?'selected':'' }} value="242">Congo (+242)</option>
                                        <option data-countryCode="CK" {{ old('country_code')=='682'
                                            ?'selected':'' }} value="682">Cook Islands (+682)</option>
                                        <option data-countryCode="CR" {{ old('country_code')=='506'
                                            ?'selected':'' }} value="506">Costa Rica (+506)</option>
                                        <option data-countryCode="HR" {{ old('country_code')=='385'
                                            ?'selected':'' }} value="385">Croatia (+385)</option>
                                        <option data-countryCode="CU" {{ old('country_code')=='53'
                                            ?'selected':'' }} value="53">Cuba (+53)</option>
                                        <option data-countryCode="CY" {{ old('country_code')=='90392'
                                            ?'selected':'' }} value="90392">Cyprus North (+90392)</option>
                                        <option data-countryCode="CY" {{ old('country_code')=='357'
                                            ?'selected':'' }} value="357">Cyprus South (+357)</option>
                                        <option data-countryCode="CZ" {{ old('country_code')=='42'
                                            ?'selected':'' }} value="42">Czech Republic (+42)</option>
                                        <option data-countryCode="DK" {{ old('country_code')=='45'
                                            ?'selected':'' }} value="45">Denmark (+45)</option>
                                        <option data-countryCode="DJ" {{ old('country_code')=='253'
                                            ?'selected':'' }} value="253">Djibouti (+253)</option>
                                        <option data-countryCode="DM" {{ old('country_code')=='1767'
                                            ?'selected':'' }} value="1767">Dominica (+1767)</option>
                                        <option data-countryCode="DO" {{ old('country_code')=='1809'
                                            ?'selected':'' }} value="1809">Dominican Republic (+1809)</option>
                                        <option data-countryCode="EC" {{ old('country_code')=='593'
                                            ?'selected':'' }} value="593">Ecuador (+593)</option>
                                        <option data-countryCode="EG" {{ old('country_code')=='20'
                                            ?'selected':'' }} value="20">Egypt (+20)</option>
                                        <option data-countryCode="SV" {{ old('country_code')=='503'
                                            ?'selected':'' }} value="503">El Salvador (+503)</option>
                                        <option data-countryCode="GQ" {{ old('country_code')=='240'
                                            ?'selected':'' }} value="240">Equatorial Guinea (+240)</option>
                                        <option data-countryCode="ER" {{ old('country_code')=='291'
                                            ?'selected':'' }} value="291">Eritrea (+291)</option>
                                        <option data-countryCode="EE" {{ old('country_code')=='372'
                                            ?'selected':'' }} value="372">Estonia (+372)</option>
                                        <option data-countryCode="ET" {{ old('country_code')=='251'
                                            ?'selected':'' }} value="251">Ethiopia (+251)</option>
                                        <option data-countryCode="FK" {{ old('country_code')=='500'
                                            ?'selected':'' }} value="500">Falkland Islands (+500)</option>
                                        <option data-countryCode="FO" {{ old('country_code')=='298'
                                            ?'selected':'' }} value="298">Faroe Islands (+298)</option>
                                        <option data-countryCode="FJ" {{ old('country_code')=='679'
                                            ?'selected':'' }} value="679">Fiji (+679)</option>
                                        <option data-countryCode="FI" {{ old('country_code')=='358'
                                            ?'selected':'' }} value="358">Finland (+358)</option>
                                        <option data-countryCode="FR" {{ old('country_code')=='33'
                                            ?'selected':'' }} value="33">France (+33)</option>
                                        <option data-countryCode="GF" {{ old('country_code')=='594'
                                            ?'selected':'' }} value="594">French Guiana (+594)</option>
                                        <option data-countryCode="PF" {{ old('country_code')=='689'
                                            ?'selected':'' }} value="689">French Polynesia (+689)</option>
                                        <option data-countryCode="GA" {{ old('country_code')=='241'
                                            ?'selected':'' }} value="241">Gabon (+241)</option>
                                        <option data-countryCode="GM" {{ old('country_code')=='220'
                                            ?'selected':'' }} value="220">Gambia (+220)</option>
                                        <option data-countryCode="GE" {{ old('country_code')=='995'
                                            ?'selected':'' }} value="995">Georgia (+995)</option>
                                        <option data-countryCode="DE" {{ old('country_code')=='49'
                                            ?'selected':'' }} value="49">Germany (+49)</option>
                                        <option data-countryCode="GH" {{ old('country_code')=='233'
                                            ?'selected':'' }} value="233">Ghana (+233)</option>
                                        <option data-countryCode="GI" {{ old('country_code')=='350'
                                            ?'selected':'' }} value="350">Gibraltar (+350)</option>
                                        <option data-countryCode="GR" {{ old('country_code')=='30'
                                            ?'selected':'' }} value="30">Greece (+30)</option>
                                        <option data-countryCode="GL" {{ old('country_code')=='299'
                                            ?'selected':'' }} value="299">Greenland (+299)</option>
                                        <option data-countryCode="GD" {{ old('country_code')=='1473'
                                            ?'selected':'' }} value="1473">Grenada (+1473)</option>
                                        <option data-countryCode="GP" {{ old('country_code')=='590'
                                            ?'selected':'' }} value="590">Guadeloupe (+590)</option>
                                        <option data-countryCode="GU" {{ old('country_code')=='671'
                                            ?'selected':'' }} value="671">Guam (+671)</option>
                                        <option data-countryCode="GT" {{ old('country_code')=='502'
                                            ?'selected':'' }} value="502">Guatemala (+502)</option>
                                        <option data-countryCode="GN" {{ old('country_code')=='224'
                                            ?'selected':'' }} value="224">Guinea (+224)</option>
                                        <option data-countryCode="GW" {{ old('country_code')=='245'
                                            ?'selected':'' }} value="245">Guinea - Bissau (+245)</option>
                                        <option data-countryCode="GY" {{ old('country_code')=='592'
                                            ?'selected':'' }} value="592">Guyana (+592)</option>
                                        <option data-countryCode="HT" {{ old('country_code')=='509'
                                            ?'selected':'' }} value="509">Haiti (+509)</option>
                                        <option data-countryCode="HN" {{ old('country_code')=='504'
                                            ?'selected':'' }} value="504">Honduras (+504)</option>
                                        <option data-countryCode="HK" {{ old('country_code')=='852'
                                            ?'selected':'' }} value="852">Hong Kong (+852)</option>
                                        <option data-countryCode="HU" {{ old('country_code')=='36'
                                            ?'selected':'' }} value="36">Hungary (+36)</option>
                                        <option data-countryCode="IS" {{ old('country_code')=='354'
                                            ?'selected':'' }} value="354">Iceland (+354)</option>
                                        <option data-countryCode="IN" {{ old('country_code')=='91'
                                            ?'selected':'' }} value="91">India (+91)</option>
                                        <option data-countryCode="ID" {{ old('country_code')=='62'
                                            ?'selected':'' }} value="62">Indonesia (+62)</option>
                                        <option data-countryCode="IR" {{ old('country_code')=='98'
                                            ?'selected':'' }} value="98">Iran (+98)</option>
                                        <option data-countryCode="IQ" {{ old('country_code')=='964'
                                            ?'selected':'' }} value="964">Iraq (+964)</option>
                                        <option data-countryCode="IE" {{ old('country_code')=='353'
                                            ?'selected':'' }} value="353">Ireland (+353)</option>
                                        <option data-countryCode="IL" {{ old('country_code')=='972'
                                            ?'selected':'' }} value="972">Israel (+972)</option>
                                        <option data-countryCode="IT" {{ old('country_code')=='39'
                                            ?'selected':'' }} value="39">Italy (+39)</option>
                                        <option data-countryCode="JM" {{ old('country_code')=='1876'
                                            ?'selected':'' }} value="1876">Jamaica (+1876)</option>
                                        <option data-countryCode="JP" {{ old('country_code')=='81'
                                            ?'selected':'' }} value="81">Japan (+81)</option>
                                        <option data-countryCode="JO" {{ old('country_code')=='962'
                                            ?'selected':'' }} value="962">Jordan (+962)</option>
                                        <option data-countryCode="KZ" {{ old('country_code')=='7' ?'selected':''
                                            }} value="7">Kazakhstan (+7)</option>
                                        <option data-countryCode="KE" {{ old('country_code')=='254'
                                            ?'selected':'' }} value="254">Kenya (+254)</option>
                                        <option data-countryCode="KI" {{ old('country_code')=='686'
                                            ?'selected':'' }} value="686">Kiribati (+686)</option>
                                        <option data-countryCode="KP" {{ old('country_code')=='850'
                                            ?'selected':'' }} value="850">Korea North (+850)</option>
                                        <option data-countryCode="KR" {{ old('country_code')=='82'
                                            ?'selected':'' }} value="82">Korea South (+82)</option>
                                        <option data-countryCode="KW" {{ old('country_code')=='965'
                                            ?'selected':'' }} value="965">Kuwait (+965)</option>
                                        <option data-countryCode="KG" {{ old('country_code')=='996'
                                            ?'selected':'' }} value="996">Kyrgyzstan (+996)</option>
                                        <option data-countryCode="LA" {{ old('country_code')=='856'
                                            ?'selected':'' }} value="856">Laos (+856)</option>
                                        <option data-countryCode="LV" {{ old('country_code')=='371'
                                            ?'selected':'' }} value="371">Latvia (+371)</option>
                                        <option data-countryCode="LB" {{ old('country_code')=='961'
                                            ?'selected':'' }} value="961">Lebanon (+961)</option>
                                        <option data-countryCode="LS" {{ old('country_code')=='266'
                                            ?'selected':'' }} value="266">Lesotho (+266)</option>
                                        <option data-countryCode="LR" {{ old('country_code')=='231'
                                            ?'selected':'' }} value="231">Liberia (+231)</option>
                                        <option data-countryCode="LY" {{ old('country_code')=='218'
                                            ?'selected':'' }} value="218">Libya (+218)</option>
                                        <option data-countryCode="LI" {{ old('country_code')=='417'
                                            ?'selected':'' }} value="417">Liechtenstein (+417)</option>
                                        <option data-countryCode="LT" {{ old('country_code')=='370'
                                            ?'selected':'' }} value="370">Lithuania (+370)</option>
                                        <option data-countryCode="LU" {{ old('country_code')=='352'
                                            ?'selected':'' }} value="352">Luxembourg (+352)</option>
                                        <option data-countryCode="MO" {{ old('country_code')=='853'
                                            ?'selected':'' }} value="853">Macao (+853)</option>
                                        <option data-countryCode="MK" {{ old('country_code')=='389'
                                            ?'selected':'' }} value="389">Macedonia (+389)</option>
                                        <option data-countryCode="MG" {{ old('country_code')=='261'
                                            ?'selected':'' }} value="261">Madagascar (+261)</option>
                                        <option data-countryCode="MW" {{ old('country_code')=='265'
                                            ?'selected':'' }} value="265">Malawi (+265)</option>
                                        <option data-countryCode="MY" {{ old('country_code')=='60'
                                            ?'selected':'' }} value="60">Malaysia (+60)</option>
                                        <option data-countryCode="MV" {{ old('country_code')=='960'
                                            ?'selected':'' }} value="960">Maldives (+960)</option>
                                        <option data-countryCode="ML" {{ old('country_code')=='223'
                                            ?'selected':'' }} value="223">Mali (+223)</option>
                                        <option data-countryCode="MT" {{ old('country_code')=='356'
                                            ?'selected':'' }} value="356">Malta (+356)</option>
                                        <option data-countryCode="MH" {{ old('country_code')=='692'
                                            ?'selected':'' }} value="692">Marshall Islands (+692)</option>
                                        <option data-countryCode="MQ" {{ old('country_code')=='596'
                                            ?'selected':'' }} value="596">Martinique (+596)</option>
                                        <option data-countryCode="MR" {{ old('country_code')=='222'
                                            ?'selected':'' }} value="222">Mauritania (+222)</option>
                                        <option data-countryCode="YT" {{ old('country_code')=='269'
                                            ?'selected':'' }} value="269">Mayotte (+269)</option>
                                        <option data-countryCode="MX" {{ old('country_code')=='52'
                                            ?'selected':'' }} value="52">Mexico (+52)</option>
                                        <option data-countryCode="FM" {{ old('country_code')=='691'
                                            ?'selected':'' }} value="691">Micronesia (+691)</option>
                                        <option data-countryCode="MD" {{ old('country_code')=='373'
                                            ?'selected':'' }} value="373">Moldova (+373)</option>
                                        <option data-countryCode="MC" {{ old('country_code')=='377'
                                            ?'selected':'' }} value="377">Monaco (+377)</option>
                                        <option data-countryCode="MN" {{ old('country_code')=='976'
                                            ?'selected':'' }} value="976">Mongolia (+976)</option>
                                        <option data-countryCode="MS" {{ old('country_code')=='1664'
                                            ?'selected':'' }} value="1664">Montserrat (+1664)</option>
                                        <option data-countryCode="MA" {{ old('country_code')=='212'
                                            ?'selected':'' }} value="212">Morocco (+212)</option>
                                        <option data-countryCode="MZ" {{ old('country_code')=='258'
                                            ?'selected':'' }} value="258">Mozambique (+258)</option>
                                        <option data-countryCode="MN" {{ old('country_code')=='95'
                                            ?'selected':'' }} value="95">Myanmar (+95)</option>
                                        <option data-countryCode="NA" {{ old('country_code')=='264'
                                            ?'selected':'' }} value="264">Namibia (+264)</option>
                                        <option data-countryCode="NR" {{ old('country_code')=='674'
                                            ?'selected':'' }} value="674">Nauru (+674)</option>
                                        <option data-countryCode="NP" {{ old('country_code')=='977'
                                            ?'selected':'' }} value="977">Nepal (+977)</option>
                                        <option data-countryCode="NL" {{ old('country_code')=='31'
                                            ?'selected':'' }} value="31">Netherlands (+31)</option>
                                        <option data-countryCode="NC" {{ old('country_code')=='687'
                                            ?'selected':'' }} value="687">New Caledonia (+687)</option>
                                        <option data-countryCode="NZ" {{ old('country_code')=='64'
                                            ?'selected':'' }} value="64">New Zealand (+64)</option>
                                        <option data-countryCode="NI" {{ old('country_code')=='505'
                                            ?'selected':'' }} value="505">Nicaragua (+505)</option>
                                        <option data-countryCode="NE" {{ old('country_code')=='227'
                                            ?'selected':'' }} value="227">Niger (+227)</option>
                                        <option data-countryCode="NG" {{ old('country_code')=='234'
                                            ?'selected':'' }} value="234">Nigeria (+234)</option>
                                        <option data-countryCode="NU" {{ old('country_code')=='683'
                                            ?'selected':'' }} value="683">Niue (+683)</option>
                                        <option data-countryCode="NF" {{ old('country_code')=='672'
                                            ?'selected':'' }} value="672">Norfolk Islands (+672)</option>
                                        <option data-countryCode="NP" {{ old('country_code')=='670'
                                            ?'selected':'' }} value="670">Northern Marianas (+670)</option>
                                        <option data-countryCode="NO" {{ old('country_code')=='47'
                                            ?'selected':'' }} value="47">Norway (+47)</option>
                                        <option data-countryCode="OM" {{ old('country_code')=='968'
                                            ?'selected':'' }} value="968">Oman (+968)</option>
                                        <option data-countryCode="PW" {{ old('country_code')=='680'
                                            ?'selected':'' }} value="680">Palau (+680)</option>
                                        <option data-countryCode="PA" {{ old('country_code')=='507'
                                            ?'selected':'' }} value="507">Panama (+507)</option>
                                        <option data-countryCode="PG" {{ old('country_code')=='675'
                                            ?'selected':'' }} value="675">Papua New Guinea (+675)</option>
                                        <option data-countryCode="PY" {{ old('country_code')=='595'
                                            ?'selected':'' }} value="595">Paraguay (+595)</option>
                                        <option data-countryCode="PE" {{ old('country_code')=='51'
                                            ?'selected':'' }} value="51">Peru (+51)</option>
                                        <option data-countryCode="PH" {{ old('country_code')=='63'
                                            ?'selected':'' }} value="63">Philippines (+63)</option>
                                        <option data-countryCode="PL" {{ old('country_code')=='48'
                                            ?'selected':'' }} value="48">Poland (+48)</option>
                                        <option data-countryCode="PT" {{ old('country_code')=='351'
                                            ?'selected':'' }} value="351">Portugal (+351)</option>
                                        <option data-countryCode="PR" {{ old('country_code')=='1787'
                                            ?'selected':'' }} value="1787">Puerto Rico (+1787)</option>
                                        <option data-countryCode="QA" {{ old('country_code')=='974'
                                            ?'selected':'' }} value="974">Qatar (+974)</option>
                                        <option data-countryCode="RE" {{ old('country_code')=='262'
                                            ?'selected':'' }} value="262">Reunion (+262)</option>
                                        <option data-countryCode="RO" {{ old('country_code')=='40'
                                            ?'selected':'' }} value="40">Romania (+40)</option>
                                        <option data-countryCode="RU" {{ old('country_code')=='7' ?'selected':''
                                            }} value="7">Russia (+7)</option>
                                        <option data-countryCode="RW" {{ old('country_code')=='250'
                                            ?'selected':'' }} value="250">Rwanda (+250)</option>
                                        <option data-countryCode="SM" {{ old('country_code')=='378'
                                            ?'selected':'' }} value="378">San Marino (+378)</option>
                                        <option data-countryCode="ST" {{ old('country_code')=='239'
                                            ?'selected':'' }} value="239">Sao Tome &amp; Principe (+239)
                                        </option>
                                        <option data-countryCode="SA" {{ old('country_code')=='966'
                                            ?'selected':'' }} value="966">Saudi Arabia (+966)</option>
                                        <option data-countryCode="SN" {{ old('country_code')=='221'
                                            ?'selected':'' }} value="221">Senegal (+221)</option>
                                        <option data-countryCode="CS" {{ old('country_code')=='381'
                                            ?'selected':'' }} value="381">Serbia (+381)</option>
                                        <option data-countryCode="SC" {{ old('country_code')=='248'
                                            ?'selected':'' }} value="248">Seychelles (+248)</option>
                                        <option data-countryCode="SL" {{ old('country_code')=='232'
                                            ?'selected':'' }} value="232">Sierra Leone (+232)</option>
                                        <option data-countryCode="SG" {{ old('country_code')=='65'
                                            ?'selected':'' }} value="65">Singapore (+65)</option>
                                        <option data-countryCode="SK" {{ old('country_code')=='421'
                                            ?'selected':'' }} value="421">Slovak Republic (+421)</option>
                                        <option data-countryCode="SI" {{ old('country_code')=='386'
                                            ?'selected':'' }} value="386">Slovenia (+386)</option>
                                        <option data-countryCode="SB" {{ old('country_code')=='677'
                                            ?'selected':'' }} value="677">Solomon Islands (+677)</option>
                                        <option data-countryCode="SO" {{ old('country_code')=='252'
                                            ?'selected':'' }} value="252">Somalia (+252)</option>
                                        <option data-countryCode="ZA" {{ old('country_code')=='27'
                                            ?'selected':'' }} value="27">South Africa (+27)</option>
                                        <option data-countryCode="ES" {{ old('country_code')=='34'
                                            ?'selected':'' }} value="34">Spain (+34)</option>
                                        <option data-countryCode="LK" {{ old('country_code')=='94'
                                            ?'selected':'' }} value="94">Sri Lanka (+94)</option>
                                        <option data-countryCode="SH" {{ old('country_code')=='290'
                                            ?'selected':'' }} value="290">St. Helena (+290)</option>
                                        <option data-countryCode="KN" {{ old('country_code')=='1869'
                                            ?'selected':'' }} value="1869">St. Kitts (+1869)</option>
                                        <option data-countryCode="SC" {{ old('country_code')=='1758'
                                            ?'selected':'' }} value="1758">St. Lucia (+1758)</option>
                                        <option data-countryCode="SD" {{ old('country_code')=='249'
                                            ?'selected':'' }} value="249">Sudan (+249)</option>
                                        <option data-countryCode="SR" {{ old('country_code')=='597'
                                            ?'selected':'' }} value="597">Suriname (+597)</option>
                                        <option data-countryCode="SZ" {{ old('country_code')=='268'
                                            ?'selected':'' }} value="268">Swaziland (+268)</option>
                                        <option data-countryCode="SE" {{ old('country_code')=='46'
                                            ?'selected':'' }} value="46">Sweden (+46)</option>
                                        <option data-countryCode="CH" {{ old('country_code')=='41'
                                            ?'selected':'' }} value="41">Switzerland (+41)</option>
                                        <option data-countryCode="SI" {{ old('country_code')=='963'
                                            ?'selected':'' }} value="963">Syria (+963)</option>
                                        <option data-countryCode="TW" {{ old('country_code')=='886'
                                            ?'selected':'' }} value="886">Taiwan (+886)</option>
                                        <option data-countryCode="TJ" {{ old('country_code')=='992'
                                            ?'selected':'' }} value="992">Tajikstan (+992)</option>
                                        <option data-countryCode="TH" {{ old('country_code')=='66'
                                            ?'selected':'' }} value="66">Thailand (+66)</option>
                                        <option data-countryCode="TG" {{ old('country_code')=='228'
                                            ?'selected':'' }} value="228">Togo (+228)</option>
                                        <option data-countryCode="TO" {{ old('country_code')=='676'
                                            ?'selected':'' }} value="676">Tonga (+676)</option>
                                        <option data-countryCode="TT" {{ old('country_code')=='1868'
                                            ?'selected':'' }} value="1868">Trinidad &amp; Tobago (+1868)
                                        </option>
                                        <option data-countryCode="TN" {{ old('country_code')=='216'
                                            ?'selected':'' }} value="216">Tunisia (+216)</option>
                                        <option data-countryCode="TR" {{ old('country_code')=='90'
                                            ?'selected':'' }} value="90">Turkey (+90)</option>
                                        <option data-countryCode="TM" {{ old('country_code')=='7' ?'selected':''
                                            }} value="7">Turkmenistan (+7)</option>
                                        <option data-countryCode="TM" {{ old('country_code')=='993'
                                            ?'selected':'' }} value="993">Turkmenistan (+993)</option>
                                        <option data-countryCode="TC" {{ old('country_code')=='1649'
                                            ?'selected':'' }} value="1649">Turks &amp; Caicos Islands (+1649)
                                        </option>
                                        <option data-countryCode="TV" {{ old('country_code')=='688'
                                            ?'selected':'' }} value="688">Tuvalu (+688)</option>
                                        <option data-countryCode="UG" {{ old('country_code')=='256'
                                            ?'selected':'' }} value="256">Uganda (+256)</option>
                                        <option value="44" {{ old('country_code')=='44' ?'selected':'' }}>UK (+44)</option>
                                        <option value="1" {{ old('country_code')=='1' ?'selected':'' }}>USA (+1)
                                        </option>
                                        <option data-countryCode="UA" {{ old('country_code')=='380'
                                            ?'selected':'' }} value="380">Ukraine (+380)</option>
                                        <option data-countryCode="AE" {{ old('country_code')=='971'
                                            ?'selected':'' }} value="971">United Arab Emirates (+971)</option>
                                        <option data-countryCode="UY" {{ old('country_code')=='598'
                                            ?'selected':'' }} value="598">Uruguay (+598)</option>
                                        <option data-countryCode="UZ" {{ old('country_code')=='998'
                                            ?'selected':'' }} value="998">Uzbekistan (+998)</option>
                                        <option data-countryCode="VU" {{ old('country_code')=='678'
                                            ?'selected':'' }} value="678">Vanuatu (+678)</option>
                                        <option data-countryCode="VA" {{ old('country_code')=='379'
                                            ?'selected':'' }} value="379">Vatican City (+379)</option>
                                        <option data-countryCode="VE" {{ old('country_code')=='58'
                                            ?'selected':'' }} value="58">Venezuela (+58)</option>
                                        <option data-countryCode="VN" {{ old('country_code')=='84'
                                            ?'selected':'' }} value="84">Vietnam (+84)</option>
                                        <option data-countryCode="VG" {{ old('country_code')=='84'
                                            ?'selected':'' }} value="84">Virgin Islands - British (+1284)
                                        </option>
                                        <option data-countryCode="VI" {{ old('country_code')=='84'
                                            ?'selected':'' }} value="84">Virgin Islands - US (+1340)</option>
                                        <option data-countryCode="WF" {{ old('country_code')=='681'
                                            ?'selected':'' }} value="681">Wallis &amp; Futuna (+681)</option>
                                        <option data-countryCode="YE" {{ old('country_code')=='969'
                                            ?'selected':'' }} value="969">Yemen (North)(+969)</option>
                                        <option data-countryCode="YE" {{ old('country_code')=='967'
                                            ?'selected':'' }} value="967">Yemen (South)(+967)</option>
                                        <option data-countryCode="ZM" {{ old('country_code')=='260'
                                            ?'selected':'' }} value="260">Zambia (+260)</option>
                                        <option data-countryCode="ZW" {{ old('country_code')=='263'
                                            ?'selected':'' }} value="263">Zimbabwe (+263)</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="mobile_no" class="form-control" id="mobile_no" placeholder="Mobile Number" value="{{old('mobile_no')}}" autocomplete="off">
                                </div>
                            </div>
                            @if ($errors->has('country_code'))
                            <span class="text-danger help-block form-error">
                                {{ $errors->first('country_code') }}
                            </span>
                            @endif
                            @if ($errors->has('mobile_no'))
                            <span class="text-danger help-block form-error">
                                {{ $errors->first('mobile_no') }}
                            </span>
                            @endif
                        </div>
                        <div class="mb-3">
                           <input type="password" class="form-control" placeholder="Password" name="password" aria-label="Password" aria-describedby="password-addon">
                           @if ($errors->has('password'))
                            <span class="help-block font-red-mint text-danger">
                                {{ $errors->first('password') }}
                            </span>
                            @endif
                        </div>
                        <div class="mb-3">
                           <input type="password" class="form-control" placeholder="Enter Confirm Password" name="password_confirmation" aria-label="Enter Confirm Password" aria-describedby="password-addon">
                        </div>
                        <!-- <div class="form-check form-check-info text-left">
                           <input class="form-check-input" type="checkbox" value id="flexCheckDefault" checked>
                           <label class="form-check-label" for="flexCheckDefault">
                           I agree the <a href="javascript:;" class="text-dark font-weight-bolder">Terms and Conditions</a>
                           </label>
                        </div> -->
                        <div class="text-center">
                           <button class="btn bg-gradient-dark w-100 my-4 mb-2 g-recaptcha" data-sitekey="{{ config('app.captch_sitekey') }}" data-callback="onSubmit" data-action="submit">Sign up</button>
                        </div>
                        <p class="text-sm mt-3 mb-0">Already have an account? <a href="{{route('login')}}" class="text-dark font-weight-bolder">Sign in</a></p>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
   <footer class="footer py-5">
      <div class="container">
         <div class="row">
            <!-- <div class="col-lg-8 mb-4 mx-auto text-center">
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
               Company
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
               About Us
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
               Team
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
               Products
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
               Blog
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
               Pricing
               </a>
            </div>
            <div class="col-lg-8 mx-auto text-center mb-4 mt-2">
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
               <span class="text-lg fab fa-dribbble"></span>
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
               <span class="text-lg fab fa-twitter"></span>
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
               <span class="text-lg fab fa-instagram"></span>
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
               <span class="text-lg fab fa-pinterest"></span>
               </a>
               <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
               <span class="text-lg fab fa-github"></span>
               </a>
            </div> -->
         </div>
         <div class="row">
            <div class="col-8 mx-auto text-center mt-1">
               <p class="mb-0 text-secondary">
                  © <script>
                    document.write(new Date().getFullYear())
                    </script>,
                    {{ config('app.name') }} Solution Team LTD.
               </p>
            </div>
         </div>
      </div>
   </footer>
</main>
    
    <script src="{{ storage_asset('themesetup/assets/vendor/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/core/popper.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/core/bootstrap.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/plugins/perfect-scrollbar.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/plugins/smooth-scrollbar.min.js')}}"></script>
    <script src="{{ storage_asset('softtheme/js/soft-ui-dashboard.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/fontawesome.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
        function onSubmit(token) {
            document.getElementById("signup-form").submit();
        }
    </script>
    <!-- for demo purpose -->
</body>
</html>