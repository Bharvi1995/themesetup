@extends('layouts.admin.default')
@section('title')
    Create Rule
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a
        href="{{ route('merchant.create_rules.list', ['id' => $id, 'type' => $type]) }}">Rules</a> /
    Create
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Merchant's Create Rules</h4>
                    </div>
                    <a href="{{ route('merchant.create_rules.list', ['id' => $id, 'type' => $type]) }}"
                        class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i></a>
                </div>
                <div class="card-body">
                    {!! Form::open([
                        'route' => 'merchant.create_rules.store',
                        'method' => 'POST',
                        'class' => 'form-dark w-100',
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
                            <h4 class="mb-3 mt-3">Create Rules Details</h4>
                            <div class="table-responsive custom-table">
                                <input type="hidden" name="type" id="type" value="{{ $type }}">
                                <input type="hidden" name="id" id="id" value="{{ $id }}">
                                <table id="tbRules" class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Condition</th>
                                            <th>Values</th>
                                            <th>Add More</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tab_logic">
                                        <div id="dvTabRules">
                                            <tr style="display:none">
                                                <td>
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
                                                <td>
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
                                                    <div id="dvCardWl_{groupId}" style="display:none">
                                                        <select class="form-control cardwloperator" data-id='{groupId}'
                                                            name="cardwloperator_{groupId}" id="operator_{groupId}">
                                                            <option value="=">=</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
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
                                                    <select class="form-control" id="cardwl_{groupId}"
                                                        name="cardwl_{groupId}[]" style="display:none">
                                                        <option value="0">FT</option>
                                                        <option value="1">WTL</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm btnMinus"
                                                        onClick="fnRemoveRow({groupId})"> <i class="fa fa-minus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr id="trRules_0">
                                                <td>
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
                                                <td>
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
                                                </td>
                                                <td>
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
                                                    <select class="form-control" id="cardwl_0" name="cardwl_0[]"
                                                        style="display:none">
                                                        <option value="0">FT</option>
                                                        <option value="1">WTL</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm btnPlus"> <i
                                                            class="fa fa-plus"></i> </button>
                                                </td>
                                            </tr>
                                        </div>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group col-lg-12 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                            <button type="reset" class="btn btn-danger btn-sm">Cancel</button>
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
        function fnSelector(intId, id) {
            $("#country_" + id).hide();
            $("#amount_" + id).hide();
            $("#currency_" + id).hide();
            $("#category_" + id).hide();
            $("#dvAmount_" + id).hide();
            $("#dvCurrency_" + id).hide();
            $("#dvCategory_" + id).hide();
            $("#dvCountry_" + id).hide();
            $("#cardtype_" + id).hide();
            $("#dvCardType_" + id).hide();
            $("#cardwl_" + id).hide();
            $("#dvCardWl_" + id).hide();
            if (intId == "amount") {
                $("#amount_" + id).show();
                $("#dvAmount_" + id).show();
            } else {
                if (intId == "currency") {
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
                } else if (intId == "card_wl") {
                    $("#dvCardWl_" + id).show();
                    $("#cardwl_" + id).show();
                }
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

        $('.btnPlus').on("click", function() {
            var count = $("#txtCount").val();
            count++;
            var x = document.getElementById("tbRules").rows.length;
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
