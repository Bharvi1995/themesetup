@extends('layouts.admin.default')
@section('title')
    Edit Rule
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
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Edit Rules</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Edit Rules</h6>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="mr-auto pr-3">
                        <h4 class="card-title">Edit Rule</h4>
                    </div>
                </div>
                <div class="card-body">
                    {!! Form::open([
                        'route' => ['admin.create_rules.update', $rule->id],
                        'method' => 'PUT',
                        'class' => 'form w-100 form-dark',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="txtCount" id="txtCount" value="{{ count($conditions) - 1 }}">
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label>Enter Name</label>
                            {!! Form::text('title', $rule->rules_name, ['placeholder' => 'Enter here...', 'class' => 'form-control']) !!}
                            @if ($errors->has('title'))
                                <span class="text-danger help-block form-error">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-12 form-group">
                            <h4 class="mb-3 mt-3">Edit Rules Details</h4>
                            <div class="table-responsive custom-table">
                                <input type="hidden" name="type" id="type" value="{{ $rule->rules_type }}">
                                <table id="tbRules" class="table table-borderless">
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
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
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
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
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
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    <input placeholder="Enter Name" class="form-control"
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
                                                    <input placeholder="Enter amount" class="form-control"
                                                        name="binnumber_{groupId}" id="binnumber_{groupId}" type="text" 
                                                        style="display:none">
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
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    <button type="button" class="btn btn-primary btn-sm btnMinus"
                                                        onClick="fnRemoveRow({groupId})"> Minus
                                                    </button>
                                                </td>
                                            </tr>
                                            @foreach ($conditions as $key => $condition)
                                                <?php
                                                $temp_condition = explode(' ', trim($condition));
                                                // dd($temp_condition);
                                                // dd(explode(",",trim($temp_condition[2],'[')));
                                                ?>
                                                <tr id="trRules_{{ $key }}">
                                                    <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                        <input type="hidden" name="txHiddenAdd[]"
                                                            id="txHiddenAdd_{{ $key }}" value="Y">
                                                        <select class="form-control" name="selector[]"
                                                            id="selector_{{ $key }}"
                                                            onchange="fnSelector(this.value,{{ $key }})">
                                                            <option value="">-Category-</option>
                                                            @foreach (getSuporter() as $k => $country)
                                                                <option value="{{ $k }}"
                                                                    {{ $temp_condition[0] == $k ? 'selected' : '' }}>
                                                                    {{ $country }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                        <div id="dvAmount_{{ $key }}"
                                                            style="@if ($temp_condition[0] != 'amount') display: none; @endif">
                                                            <select class="form-control amountoperator"
                                                                name="amountoperator_{{ $key }}"
                                                                id="operator_{{ $key }}">
                                                                <option value="">-Operator-</option>
                                                                <option value="="
                                                                    {{ $temp_condition[1] == '=' ? 'selected' : '' }}>=
                                                                </option>
                                                                <option value=">="
                                                                    {{ $temp_condition[1] == '>=' ? 'selected' : '' }}>>=
                                                                </option>
                                                                <option value="<="
                                                                    {{ $temp_condition[1] == '<=' ? 'selected' : '' }}>
                                                                    <= </option>
                                                                <option value=">"
                                                                    {{ $temp_condition[1] == '>' ? 'selected' : '' }}>>
                                                                </option>
                                                                <option value="<"
                                                                    {{ $temp_condition[1] == '<' ? 'selected' : '' }}>
                                                                    < </option>
                                                            </select>
                                                        </div>
                                                        <div id="dvCurrency_{{ $key }}"
                                                            style="@if ($temp_condition[0] != 'currency') display: none; @endif">
                                                            <select class="form-control currencyoperator"
                                                                data-id='{{ $key }}'
                                                                name="currencyoperator_{{ $key }}"
                                                                id="operator_{{ $key }}">
                                                                <option value="">-Operator-</option>
                                                                <option value="="
                                                                    {{ $temp_condition[1] == '=' ? 'selected' : '' }}>=
                                                                </option>
                                                                <option value="In"
                                                                    {{ $temp_condition[1] == 'In' ? 'selected' : '' }}>In
                                                                </option>
                                                                <option value="NotIn"
                                                                    {{ $temp_condition[1] == 'NotIn' ? 'selected' : '' }}>
                                                                    Not
                                                                    In</option>
                                                            </select>
                                                        </div>
                                                        <div id="dvCategory_{{ $key }}"
                                                            style="@if ($temp_condition[0] != 'category') display: none; @endif">
                                                            <select class="form-control categoryoperator"
                                                                data-id='{{ $key }}'
                                                                name="categoryoperator_{{ $key }}"
                                                                id="operator_{{ $key }}">
                                                                <option value="">-Operator-</option>
                                                                <option value="="
                                                                    {{ $temp_condition[1] == '=' ? 'selected' : '' }}>=
                                                                </option>
                                                                <option value="In"
                                                                    {{ $temp_condition[1] == 'In' ? 'selected' : '' }}>In
                                                                </option>
                                                                <option value="NotIn"
                                                                    {{ $temp_condition[1] == 'NotIn' ? 'selected' : '' }}>
                                                                    Not
                                                                    In</option>
                                                            </select>
                                                        </div>
                                                        <div id="dvCountry_{{ $key }}"
                                                            style="@if ($temp_condition[0] != 'country') display: none; @endif">
                                                            <select class="form-control countryoperator"
                                                                data-id='{{ $key }}'
                                                                name="countryoperator_{{ $key }}"
                                                                id="operator_{{ $key }}">
                                                                <option value="">-Operator-</option>
                                                                <option value="="
                                                                    {{ $temp_condition[1] == '=' ? 'selected' : '' }}>=
                                                                </option>
                                                                <option value="In"
                                                                    {{ $temp_condition[1] == 'In' ? 'selected' : '' }}>In
                                                                </option>
                                                                <option value="NotIn"
                                                                    {{ $temp_condition[1] == 'NotIn' ? 'selected' : '' }}>
                                                                    Not
                                                                    In</option>
                                                            </select>
                                                        </div>
                                                        <div id="dvBinCountry_{{ $key }}"
                                                            style="@if ($temp_condition[0] != 'bin_cou_code') display: none; @endif">
                                                            <select class="form-control bincountryoperator"
                                                                data-id='{{ $key }}'
                                                                name="bincountryoperator_{{ $key }}"
                                                                id="operator_{{ $key }}">
                                                                <option value="">-Operator-</option>
                                                                <option value="="
                                                                    {{ $temp_condition[1] == '=' ? 'selected' : '' }}>=
                                                                </option>
                                                                <option value="In"
                                                                    {{ $temp_condition[1] == 'In' ? 'selected' : '' }}>In
                                                                </option>
                                                                <option value="NotIn"
                                                                    {{ $temp_condition[1] == 'NotIn' ? 'selected' : '' }}>
                                                                    Not
                                                                    In</option>
                                                            </select>
                                                        </div>
                                                        <div id="dvBinNumber_{{ $key }}"
                                                            style="@if ($temp_condition[0] != 'bin_number') display: none; @endif">
                                                            <select class="form-control binnumberoperator"
                                                                data-id='{{ $key }}'
                                                                name="binnumberoperator_{{ $key }}"
                                                                id="operator_{{ $key }}">
                                                                <option value="">-Operator-</option>
                                                                <option value="="
                                                                    {{ $temp_condition[1] == '=' ? 'selected' : '' }}>=
                                                                </option>
                                                                <option value="In"
                                                                    {{ $temp_condition[1] == 'In' ? 'selected' : '' }}>In
                                                                </option>
                                                                <option value="NotIn"
                                                                    {{ $temp_condition[1] == 'NotIn' ? 'selected' : '' }}>
                                                                    Not
                                                                    In</option>
                                                            </select>
                                                        </div>
                                                        <div id="dvCardType_{{ $key }}"
                                                            style="@if ($temp_condition[0] != 'card_type') display: none; @endif">
                                                            <select class="form-control cardtypeoperator"
                                                                data-id='{{ $key }}'
                                                                name="cardtypeoperator_{{ $key }}"
                                                                id="operator_{{ $key }}">
                                                                <option value="">-Operator-</option>
                                                                <option value="="
                                                                    {{ $temp_condition[1] == '=' ? 'selected' : '' }}>=
                                                                </option>
                                                                <option value="In"
                                                                    {{ $temp_condition[1] == 'In' ? 'selected' : '' }}>In
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div id="dvCardWl_{{ $key }}"
                                                            style="@if ($temp_condition[0] != 'card_wl') display: none; @endif">
                                                            <select class="form-control cardwloperator"
                                                                data-id="{{ $key }}"
                                                                name="cardwloperator_{{ $key }}"
                                                                id="operator_{{ $key }}">
                                                                <option value="="
                                                                    {{ $temp_condition[1] == '=' ? 'selected' : '' }}>=
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div id="dvUser_{{ $key }}"
                                                            style="@if ($temp_condition[0] != 'user') display: none; @endif">
                                                            <select class="form-control useroperator"
                                                                data-id='{{ $key }}'
                                                                name="useroperator_{{ $key }}"
                                                                id="operator_{{ $key }}">
                                                                <option value="">-Operator-</option>
                                                                <option value="="
                                                                    {{ $temp_condition[1] == '=' ? 'selected' : '' }}>=
                                                                </option>
                                                                <option value="In"
                                                                    {{ $temp_condition[1] == 'In' ? 'selected' : '' }}>In
                                                                </option>
                                                                <option value="NotIn"
                                                                    {{ $temp_condition[1] == 'NotIn' ? 'selected' : '' }}>
                                                                    Not
                                                                    In</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                        <?php
                                                        if ($temp_condition[1] != '=') {
                                                            $arr = explode(',', $temp_condition[2]);
                                                            // dd($conditions);
                                                            foreach ($arr as $kt => $value) {
                                                                $arr[$kt] = preg_replace('/[^0-9a-z]/i', '', $value);
                                                            }
                                                            // dd($arr);
                                                        }
                                                        ?>
                                                        <input placeholder="Enter Values" class="form-control"
                                                            name="amount_{{ $key }}"
                                                            id="amount_{{ $key }}" type="text"
                                                            style="@if ($temp_condition[0] != 'amount') display: none; @endif"
                                                            value="@if ($temp_condition[0] == 'amount') {{ $temp_condition[2] }} @endif">
                                                        <select
                                                            class="form-control @if ($temp_condition[0] == 'country' && $temp_condition[1] != '=') select2 @endif"
                                                            id="country_{{ $key }}"
                                                            name="country_{{ $key }}[]"
                                                            style="@if ($temp_condition[0] != 'country') display: none; @endif"
                                                            @if ($temp_condition[0] == 'country' && $temp_condition[1] != '=') multiple @endif>
                                                            <option value="" disabled></option>
                                                            @foreach (getCountry() as $k => $country)
                                                                <option value="{{ $k }}"
                                                                    @if (isset($arr) && in_array($k, $arr)) selected  @else {{ trim($temp_condition[2], '\'') == $k ? 'selected' : '' }} @endif>
                                                                    {{ $country }}</option>
                                                            @endforeach
                                                        </select>
                                                        <select
                                                            class="form-control @if ($temp_condition[0] == 'currency' && $temp_condition[1] != '=') select2 @endif"
                                                            id="currency_{{ $key }}"
                                                            name="currency_{{ $key }}[]"
                                                            @if ($temp_condition[0] != 'currency') style="display: none;" @endif
                                                            @if ($temp_condition[0] == 'currency' && $temp_condition[1] != '=') multiple @endif>
                                                            <option value="" disabled=""></option>
                                                            @foreach (config('currency.three_letter') as $k => $currency)
                                                                <option value="{{ $currency }}"
                                                                    @if ($temp_condition[0] == 'currency') @if (isset($arr) && in_array($currency, $arr)) selected  @else {{ trim($temp_condition[2], '\'') == $currency ? 'selected' : '' }} @endif
                                                                    @endif>{{ $currency }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <select
                                                            class="form-control @if ($temp_condition[0] == 'category' && $temp_condition[1] != '=') select2 @endif"
                                                            id="category_{{ $key }}"
                                                            name="category_{{ $key }}[]"
                                                            @if ($temp_condition[0] != 'category') style="display: none;" @endif
                                                            @if ($temp_condition[0] == 'category' && $temp_condition[1] != '=') multiple @endif>
                                                            <option value="" disabled></option>
                                                            @foreach ($categories as $kc => $ca)
                                                                <option value="{{ $ca->id }}"
                                                                    @if (isset($arr) && in_array($ca->id, $arr)) selected  @else {{ trim($temp_condition[2], '\'') == $ca->id ? 'selected' : '' }} @endif>
                                                                    {{ $ca->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <select
                                                            class="form-control @if ($temp_condition[0] == 'card_type' && $temp_condition[1] != '=') select2 @endif"
                                                            id="cardtype_{{ $key }}"
                                                            name="cardtype_{{ $key }}[]"
                                                            @if ($temp_condition[0] != 'card_type') style="display: none;" @endif
                                                            @if ($temp_condition[0] == 'card_type' && $temp_condition[1] != '=') multiple @endif>
                                                            <option value="" disabled></option>
                                                            @foreach (getCardType() as $kType => $vType)
                                                                <option value="{{ $kType }}"
                                                                    @if (isset($arr) && in_array($kType, $arr)) selected  @else {{ trim($temp_condition[2], '\'') == $kType ? 'selected' : '' }} @endif>
                                                                    {{ $vType }}</option>
                                                            @endforeach
                                                        </select>
                                                        <select
                                                            class="form-control @if ($temp_condition[0] == 'bin_cou_code' && $temp_condition[1] != '=') select2 @endif"
                                                            id="bincountry_{{ $key }}"
                                                            name="bincountry_{{ $key }}[]"
                                                            @if ($temp_condition[0] != 'bin_cou_code') style="display: none;" @endif
                                                            @if ($temp_condition[0] == 'bin_cou_code' && $temp_condition[1] != '=') multiple @endif>
                                                            <option value="" disabled></option>
                                                            @foreach (getCountry() as $k => $country)
                                                                <option value="{{ $k }}"
                                                                    @if (isset($arr) && in_array($k, $arr)) selected  @else {{ trim($temp_condition[2], '\'') == $k ? 'selected' : '' }} @endif>
                                                                    {{ $country }}</option>
                                                            @endforeach
                                                        </select>
                                                        <input placeholder="Enter Values" class="form-control @if ($temp_condition[0] == 'bin_number' && $temp_condition[1] != '=') bin_number_tag @endif"
                                                            name="binnumber_{{ $key }}"
                                                            type="text"
                                                            style="@if ($temp_condition[0] != 'bin_number') display: none; @endif"
                                                            @if ($temp_condition[0] == 'bin_number' && $temp_condition[1] != '=') multiple @endif
                                                            value="@if ($temp_condition[0] == 'bin_number' && $temp_condition[1] != '=') {{ implode(',',json_decode($temp_condition[2])) }} @else{{ str_replace("'","",$temp_condition[2]) }}@endif">
                                                        <select class="form-control" id="cardwl_{{ $key }}"
                                                            name="cardwl_{{ $key }}[]"
                                                            @if ($temp_condition[0] != 'card_wl') style="display: none;" @endif>
                                                            <option value="0"
                                                                {{ $temp_condition[0] == 'card_wl' && trim($temp_condition[2], '\'') == '0' ? 'selected' : '' }}>
                                                                FT
                                                            </option>
                                                            <option value="1"
                                                                {{ $temp_condition[0] == 'card_wl' && trim($temp_condition[2], '\'') == '1' ? 'selected' : '' }}>
                                                                WTL
                                                            </option>
                                                        </select>
                                                        <select
                                                            class="form-control @if ($temp_condition[0] == 'user' && $temp_condition[1] != '=') select2 @endif"
                                                            id="user_{{ $key }}"
                                                            name="user_{{ $key }}[]"
                                                            style="@if ($temp_condition[0] != 'user') display: none; @endif"
                                                            @if ($temp_condition[0] == 'user' && $temp_condition[1] != '=') multiple @endif>
                                                            <option value="" disabled></option>
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->user_id }}"
                                                                    @if (isset($arr) && in_array($user->user_id, $arr)) selected  @else {{ trim($temp_condition[2], '\'') == $user->user_id ? 'selected' : '' }} @endif>
                                                                    {{ $user->business_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                        @if ($key == 0)
                                                            <button type="button" class="btn btn-success btn-sm btnPlus">
                                                                Plus
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-primary btn-sm btnMinus"
                                                                onClick="fnRemoveRow({{ $key }})"> Minus
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </div>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group col-lg-12">
                            <button type="submit" class="btn btn-success btn-sm">Submit</button>
                            <a href="{{ route('admin.create_rules.list', $rule->rules_type) }}"
                                class="btn btn-primary btn-sm">Cancel</a>
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
            } else if (intId == "bin_number") {
                $("#binnumber_" + id).show();
                $("#dvBinNumber_" + id).show();
            } else if (intId == "bin_cou_code") {
                $("#bincountry_" + id).show();
                $("#dvBinCountry_" + id).show();
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

        $(document).on("change", ".binnumberoperator", function() {
            var id = $(this).attr("data-id");
            if (this.value == "=") {
                $("#binnumber_" + id).attr("multiple", "multiple");
                $("#binnumber_" + id).selectize()[0].selectize.destroy();
            }else{
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
