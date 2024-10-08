@extends('layouts.admin.default')
@section('title')
    Create Rule
@endsection
@section('customeStyle')
    <link href="{{ storage_asset('css/selectize.css') }}" rel="stylesheet" type="text/css" />
     <style type="text/css">
        .selectize-control.form-control{
            padding: 0px !important;
        }
        .selectize-input{
            padding: 10px 20px !important;
        }
        .selectize-input > input{
            color: #FFF !important;
        }
    </style>
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.create_rules.index') }}">Rules List</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Create Rules</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Create Rules</h6>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Create Rules</h4>
                    </div>
                </div>
                <div class="card-body">
                    {!! Form::open([
                        'route' => 'admin.create_rules.store',
                        'method' => 'POST',
                        'class' => 'form form-dark w-100',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="txtCount" id="txtCount" value="0">
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label>Enter Name</label>
                            {!! Form::text('title', Input::get('title'), ['placeholder' => 'Enter here...', 'class' => 'form-control']) !!}
                            @if ($errors->has('title'))
                                <span class="text-danger help-block form-error">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-12 form-group">
                            <h4 class="mb-2 mt-2">Create Rules Details</h4>
                            <div class="table-responsive custom-table">
                                <input type="hidden" name="type" id="type" value="{{ $type }}">
                                <table id="tbRules" class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Category</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Condition</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Values</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Add More</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tab_logic">
                                        <div id="dvTabRules">
                                            <tr style="display:none">
                                                <td class="align-middle text-center text-sm">
                                                    <input type="hidden" name="txHiddenAdd[]" id="txHiddenAdd_{groupId}"
                                                        value="Y">
                                                    <select class="form-control" name="selector[]" id="selector_{groupId}"
                                                        onchange="fnSelector(this.value,'{groupId}')">
                                                        <option value="">-Category-</option>
                                                        @foreach (getSuporter() as $key => $country)
                                                            <option value="{{ $key }}">{{ $country }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <div id="dvAmount_{groupId}" style="display:none">
                                                        <select class="form-control amountoperator"
                                                            name="amountoperator_{groupId}" id="operator_{groupId}">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value=">=">>=</option>
                                                            <option value="<=">
                                                                <= </option>
                                                            <option value=">">>
                                                            </option>
                                                            <option value="<">
                                                                < </option>
                                                        </select>
                                                    </div>
                                                    <div id="dvCurrency_{groupId}" style="display:none">
                                                        <select class="form-control currencyoperator" data-id='{groupId}'
                                                            name="currencyoperator_{groupId}" id="operator_{groupId}">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value="In">In</option>
                                                            <option value="NotIn">Not In</option>
                                                        </select>
                                                    </div>
                                                    <div id="dvCategory_{groupId}" style="display:none">
                                                        <select class="form-control categoryoperator" data-id='{groupId}'
                                                            name="categoryoperator_{groupId}" id="operator_{groupId}">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value="In">In</option>
                                                            <option value="NotIn">Not In</option>
                                                        </select>
                                                    </div>
                                                    <div id="dvCountry_{groupId}" style="display:none">
                                                        <select class="form-control countryoperator" data-id='{groupId}'
                                                            name="countryoperator_{groupId}" id="operator_{groupId}">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value="In">In</option>
                                                            <option value="NotIn">Not In</option>
                                                        </select>
                                                    </div>
                                                    <div id="dvCardType_{groupId}" style="display:none">
                                                        <select class="form-control cardtypeoperator" data-id='{groupId}'
                                                            name="cardtypeoperator_{groupId}" id="operator_{groupId}">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value="In">In</option>
                                                        </select>
                                                    </div>
                                                    <div id="dvBinCountry_{groupId}" style="display:none">
                                                        <select class="form-control bincountryoperator" data-id='{groupId}'
                                                            name="bincountryoperator_{groupId}" id="operator_{groupId}">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value="In">In</option>
                                                            <option value="NotIn">Not In</option>
                                                        </select>
                                                    </div>
                                                    <div id="dvBinNumber_{groupId}" style="display:none">
                                                        <select class="form-control binnumberoperator" data-id='{groupId}'
                                                            name="binnumberoperator_{groupId}" id="operator_{groupId}">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value="In">In</option>
                                                            <option value="NotIn">Not In</option>
                                                        </select>
                                                    </div>
                                                    <div id="dvCardWl_{groupId}" style="display:none">
                                                        <select class="form-control cardwloperator" data-id='{groupId}'
                                                            name="cardwloperator_{groupId}" id="operator_{groupId}">
                                                            <option value="=">=</option>
                                                        </select>
                                                    </div>
                                                    <div id="dvUser_{groupId}" style="display:none">
                                                        <select class="form-control useroperator" data-id='{groupId}'
                                                            name="useroperator_{groupId}" id="operator_{groupId}">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value="In">In</option>
                                                            <option value="NotIn">Not In</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <input placeholder="Enter amount" class="form-control"
                                                        name="amount_{groupId}" id="amount_{groupId}" type="text"
                                                        style="display:none">
                                                    <select class="form-control" id="country_{groupId}"
                                                        name="country_{groupId}[]" style="display:none">
                                                        <option value=""></option>
                                                        @foreach (getCountry() as $k => $country)
                                                            <option value="{{ $k }}">{{ $country }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <select class="form-control" id="currency_{groupId}"
                                                        name="currency_{groupId}[]" style="display:none">
                                                        <option value=""></option>
                                                        @foreach (config('currency.three_letter') as $key => $currency)
                                                            <option value="{{ $currency }}">{{ $currency }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <select class="form-control" id="category_{groupId}"
                                                        name="category_{groupId}[]" style="display:none">
                                                        <option value=""></option>
                                                        @foreach ($categories as $kc => $categorie)
                                                            <option value="{{ $categorie->id }}">{{ $categorie->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <select class="form-control" id="cardtype_{groupId}"
                                                        name="cardtype_{groupId}[]" style="display:none">
                                                        <option value="" disabled></option>
                                                        @foreach (getCardType() as $kType => $vType)
                                                            <option value="{{ $kType }}">{{ $vType }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <select class="form-control" id="bincountry_{groupId}"
                                                        name="bincountry_{groupId}[]" style="display:none">
                                                        <option value="" disabled></option>
                                                        @foreach (getCountry() as $kBin => $countryBin)
                                                            <option value="{{ $kBin }}">{{ $countryBin }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input placeholder="Enter Here" class="form-control"
                                                        name="binnumber_{groupId}" id="binnumber_{groupId}" type="text" 
                                                        style="display:none">
                                                    <small class="binnumber_note" style="display: none;">Press <kbd class="badge-danger">Tab</kbd> after each number input</small>
                                                    <select class="form-control" id="cardwl_{groupId}"
                                                        name="cardwl_{groupId}[]" style="display:none">
                                                        <option value="0">FT</option>
                                                        <option value="1">WTL</option>
                                                    </select>
                                                    <select class="form-control" id="user_{groupId}"
                                                        name="user_{groupId}[]" style="display:none">
                                                        <option value="" disabled></option>
                                                        @foreach ($users as $user)
                                                            <option value="{{ $user->user_id }}">
                                                                {{ $user->business_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <button type="button" class="btn btn-primary btn-sm btnMinus"
                                                        onClick="fnRemoveRow({groupId})"> Minus
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr id="trRules_0">
                                                <td class="align-middle text-center text-sm">
                                                    <input type="hidden" name="txHiddenAdd[]" id="txHiddenAdd_0"
                                                        value="Y">
                                                    <select class="form-control" name="selector[]" id="selector_0"
                                                        onchange="fnSelector(this.value,'0')">
                                                        <option value="">-Category-</option>
                                                        @foreach (getSuporter() as $key => $country)
                                                            <option value="{{ $key }}">{{ $country }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <div id="dvAmount_0" style="display:none">
                                                        <select class="form-control amountoperator"
                                                            name="amountoperator_0" id="operator_0">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value=">=">>=</option>
                                                            <option value="<=">
                                                                <= </option>
                                                            <option value=">">>
                                                            </option>
                                                            <option value="<">
                                                                < </option>
                                                        </select>
                                                    </div>
                                                    <div id="dvCurrency_0" style="display:none">
                                                        <select class="form-control currencyoperator" data-id='0'
                                                            name="currencyoperator_0" id="operator_0">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value="In">In</option>
                                                            <option value="NotIn">Not In</option>
                                                        </select>
                                                    </div>
                                                    <div id="dvCategory_0" style="display:none">
                                                        <select class="form-control categoryoperator" data-id='0'
                                                            name="categoryoperator_0" id="operator_0">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value="In">In</option>
                                                            <option value="NotIn">Not In</option>
                                                        </select>
                                                    </div>
                                                    <div id="dvCountry_0" style="display:none">
                                                        <select class="form-control countryoperator" data-id='0'
                                                            name="countryoperator_0" id="operator_0">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value="In">In</option>
                                                            <option value="NotIn">Not In</option>
                                                        </select>
                                                    </div>
                                                    <div id="dvBinCountry_0" style="display:none">
                                                        <select class="form-control bincountryoperator" data-id='0'
                                                            name="bincountryoperator_0" id="operator_0">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value="In">In</option>
                                                            <option value="NotIn">Not In</option>
                                                        </select>
                                                    </div>
                                                    <div id="dvBinNumber_0" style="display:none">
                                                        <select class="form-control binnumberoperator" data-id='0'
                                                            name="binnumberoperator_0" id="operator_0">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value="In">In</option>
                                                            <option value="NotIn">Not In</option>
                                                        </select>
                                                    </div>
                                                    <div id="dvCardType_0" style="display:none">
                                                        <select class="form-control cardtypeoperator" data-id='0'
                                                            name="cardtypeoperator_0" id="operator_0">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value="In">In</option>
                                                        </select>
                                                    </div>
                                                    <div id="dvCardWl_0" style="display:none">
                                                        <select class="form-control cardwloperator" data-id='0'
                                                            name="cardwloperator_0" id="operator_0">
                                                            <option value="=">=</option>
                                                        </select>
                                                    </div>
                                                    <div id="dvUser_0" style="display:none">
                                                        <select class="form-control useroperator" data-id='0'
                                                            name="useroperator_0" id="operator_0">
                                                            <option value="">-Operator-</option>
                                                            <option value="=">=</option>
                                                            <option value="In">In</option>
                                                            <option value="NotIn">Not In</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <input placeholder="Enter Values" class="form-control"
                                                        name="amount_0" id="amount_0" type="text"
                                                        style="display:none">
                                                    <select class="form-control" id="country_0" name="country_0[]"
                                                        style="display:none">
                                                        <option value="" disabled></option>
                                                        @foreach (getCountry() as $k => $country)
                                                            <option value="{{ $k }}">{{ $country }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <select class="form-control" id="currency_0" name="currency_0[]"
                                                        style="display:none">
                                                        <option value="" disabled=""></option>
                                                        @foreach (config('currency.three_letter') as $key => $currency)
                                                            <option value="{{ $currency }}">{{ $currency }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <select class="form-control" id="category_0" name="category_0[]"
                                                        style="display:none">
                                                        <option value="" disabled></option>
                                                        @foreach ($categories as $kc => $ca)
                                                            <option value="{{ $ca->id }}">{{ $ca->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <select class="form-control" id="cardtype_0" name="cardtype_0[]"
                                                        style="display:none">
                                                        <option value="" disabled></option>
                                                        @foreach (getCardType() as $kType => $vType)
                                                            <option value="{{ $kType }}">{{ $vType }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <select class="form-control" id="bincountry_0" name="bincountry_0[]"
                                                        style="display:none">
                                                        <option value="" disabled></option>
                                                        @foreach (getCountry() as $k => $country)
                                                            <option value="{{ $k }}">{{ $country }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input placeholder="Enter Values" class="form-control"
                                                        name="binnumber_0" id="binnumber_0" type="text" 
                                                        style="display:none">
                                                    <small class="binnumber_note" style="display: none;">Press <kbd class="badge-danger">Tab</kbd> after each number input</small>
                                                    <select class="form-control" id="cardwl_0" name="cardwl_0[]"
                                                        style="display:none">
                                                        <option value="0">FT</option>
                                                        <option value="1">WTL</option>
                                                    </select>
                                                    <select class="form-control" id="user_0" name="user_0[]"
                                                        style="display:none">
                                                        <option value="" disabled></option>
                                                        @foreach ($users as $user)
                                                            <option value="{{ $user->user_id }}">
                                                                {{ $user->business_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <button type="button" class="btn btn-success btn-sm btnPlus">
                                                        Plus
                                                    </button>
                                                </td>
                                            </tr>
                                        </div>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group col-lg-12 mt-1">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="reset" class="btn btn-danger">Cancel</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')

    <script src="{{ storage_asset('js/selectize.min.js') }}"></script>

    <script type="text/javascript">
        function fnSelector(intId, id) {
            $("#country_" + id).hide();
            $("#amount_" + id).hide();
            $("#currency_" + id).hide();
            $("#category_" + id).hide();
            $("#user_" + id).hide();
            $("#dvAmount_" + id).hide();
            $("#dvCurrency_" + id).hide();
            $("#dvCategory_" + id).hide();
            $("#dvCountry_" + id).hide();
            $("#cardtype_" + id).hide();
            $("#dvCardType_" + id).hide();
            $("#bincountry_" + id).hide();
            $("#dvBinCountry_" + id).hide();
            $("#cardwl_" + id).hide();
            $("#dvCardWl_" + id).hide();
            $("#dvUser_" + id).hide();
            $("#binnumber_" + id).hide();
            $("#dvBinNumber_" + id).hide();
            $(".binnumber_note").hide();
            $(".selectize-control").hide();
            if (intId == "amount") {
                $("#amount_" + id).show();
                $("#dvAmount_" + id).show();
            } else if (intId == "currency") {
                $("#currency_" + id).show();
                $("#dvCurrency_" + id).show();
            } else if (intId == "category") {
                $("#category_" + id).show();
                $("#dvCategory_" + id).show();
            } else if (intId == "country") {
                $("#country_" + id).show();
                $("#dvCountry_" + id).show();
            } else if (intId == "card_type") {
                $("#cardtype_" + id).show();
                $("#dvCardType_" + id).show();
            } else if (intId == "bin_cou_code") {
                $("#bincountry_" + id).show();
                $("#dvBinCountry_" + id).show();
            } else if (intId == "bin_number") {
                $("#binnumber_" + id).show();
                $("#dvBinNumber_" + id).show();
            } else if (intId == "card_wl") {
                $("#dvCardWl_" + id).show();
                $("#cardwl_" + id).show();
            } else if (intId == "user") {
                $("#user_" + id).show();
                $("#dvUser_" + id).show();
            }

        }

        $(document).on("change", ".amountoperator", function() {
            $('.amountoperator').not(this).find('option[value="' + this.value + '"]').remove();
        })

        $(document).on("change", ".currencyoperator", function() {
            var id = $(this).attr("data-id");
            if ($("#currency_" + id).data('select2')) {
                $("#currency_" + id).select2('destroy');
                $("#currency_" + id).removeAttr("multiple");
            }
            if (this.value != "=") {
                $("#currency_" + id).attr("multiple", "multiple");
                $("#currency_" + id).select2({
                    placeholder: "-- Select Currency --"
                });
            }
            $('.currencyoperator').not(this).find('option[value="' + this.value + '"]').hide();
        })

        $(document).on("change", ".countryoperator", function() {
            var id = $(this).attr("data-id");
            if ($("#country_" + id).data('select2')) {
                $("#country_" + id).select2('destroy');
                $("#country_" + id).removeAttr("multiple");
            }
            if (this.value != "=") {
                $("#country_" + id).attr("multiple", "multiple");
                $("#country_" + id).select2({
                    placeholder: "-- Select Country --"
                });
            }
            $('.countryoperator').not(this).find('option[value="' + this.value + '"]').remove();
        })

        $(document).on("change", ".bincountryoperator", function() {
            var id = $(this).attr("data-id");
            if ($("#bincountry_" + id).data('select2')) {
                $("#bincountry_" + id).select2('destroy');
                $("#bincountry_" + id).removeAttr("multiple");
            }
            if (this.value != "=") {
                $("#bincountry_" + id).attr("multiple", "multiple");
                $("#bincountry_" + id).select2({
                    placeholder: "-- Select Country --"
                });
            }
            $('.bincountryoperator').not(this).find('option[value="' + this.value + '"]').remove();
        })

        $(document).on("change", ".binnumberoperator", function() {
            var id = $(this).attr("data-id");
            if (this.value == "=") {
                $("#binnumber_" + id).attr("multiple", "multiple");
                $("#binnumber_" + id).selectize()[0].selectize.destroy();
                $(".binnumber_note").hide();
            }else{
                $(".binnumber_note").show();
                $("#binnumber_" + id).attr("multiple", "multiple");
                $("#binnumber_" + id).selectize({
                    delimiter: ',',
                    persist: false,
                    create: function(input) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                });
            }
        })

        $(document).on("change", ".categoryoperator", function() {
            var id = $(this).attr("data-id");
            if ($("#category_" + id).data('select2')) {
                $("#category_" + id).select2('destroy');
                $("#category_" + id).removeAttr("multiple");
            }
            if (this.value != "=") {
                $("#category_" + id).attr("multiple", "multiple");
                $("#category_" + id).select2({
                    placeholder: "-- Select Category --"
                });
            }
            $('.categoryoperator').not(this).find('option[value="' + this.value + '"]').remove();
        })

        // * For User Operator
        $(document).on("change", ".useroperator", function() {
            var id = $(this).attr("data-id");
            if ($("#user_" + id).data('select2')) {
                $("#user_" + id).select2('destroy');
                $("#user_" + id).removeAttr("multiple");
            }
            if (this.value != "=") {
                $("#user_" + id).attr("multiple", "multiple");
                $("#user_" + id).select2({
                    placeholder: "-- Select User --"
                });
            }
            $('.useroperator').not(this).find('option[value="' + this.value + '"]').hide();
        })

        $(document).on("change", ".cardtypeoperator", function() {
            var id = $(this).attr("data-id");
            if ($("#cardtype_" + id).data('select2')) {
                $("#cardtype_" + id).select2('destroy');
                $("#cardtype_" + id).removeAttr("multiple");
            }
            if (this.value != "=") {
                $("#cardtype_" + id).attr("multiple", "multiple");
                $("#cardtype_" + id).select2({
                    placeholder: "-- Select Category --"
                });
            }
            $('.cardtypeoperator').not(this).find('option[value="' + this.value + '"]').remove();
        })

        $('.btnPlus').on("click", function() {
            var count = $("#txtCount").val();
            count++;
            // var x = document.getElementById("tbRules").rows.length;
            var html = document.getElementById("tbRules").rows.item(1).innerHTML;
            html = html.replace(/{groupId}/g, count);
            html = html.replace(/{indexId}/g, (count + 1));
            var finalHtml = "<tr id='trRules_" + count + "'>" + html + "</tr>";
            $('#tab_logic').append(finalHtml);
            $("#txtCount").val(count);
        })

        function fnRemoveRow(intId) {
            $("#txHiddenAdd_" + intId).val("N");
            $("#trRules_" + intId).hide();
        }
    </script>
@endsection
