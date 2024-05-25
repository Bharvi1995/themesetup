@extends('layouts.admin.default')

@section('title')
    Create Invoice
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Create Invoice
@endsection

@section('customeStyle')
    <style type="text/css">
        table.dataTable thead th,
        table.dataTable tbody td {
            padding: 8px 15px !important;
        }
    </style>
@endsection

@section('content')


    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Create Invoice</h4>
                    </div>
                </div>
                <div class="card-body  form-dark">
                    <form method="POST" action="{{ route('invoices.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Company Name</label>
                                    <select class="form-control select2" name="company_id" id="company_id">
                                        <option value="">-- Select Company --</option>
                                        @foreach ($companyName as $key => $value)
                                            <option value="{{ $value->id }}">{{ $value->business_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('company_id'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('company_id') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Business Name</label>
                                    <input placeholder="Enter here..." class="form-control" name="business_name"
                                        id="business_name" type="text" value="{{ old('business_name') }}">
                                    @if ($errors->has('business_name'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('business_name') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Agent Name</label>
                                    <input placeholder="Enter here..." class="form-control" name="agent_name"
                                        id="agent_name" type="text" value="{{ old('agent_name') }}">
                                    @if ($errors->has('agent_name'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('agent_name') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input name="email" type="email" id="email" placeholder="Enter here..."
                                        value="{{ old('email') }}" class="form-control">
                                    @if ($errors->has('email'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('email') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Phone No</label>
                                    <input name="phone_no" type="text" id="phone_no" placeholder="Enter here..."
                                        value="{{ old('phone_no') }}" class="form-control">
                                    @if ($errors->has('phone_no'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('phone_no') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Company address</label>
                                    <input name="company_address" type="text" id="company_address"
                                        placeholder="Enter here..." value="{{ old('company_address') }}"
                                        class="form-control">
                                    @if ($errors->has('company_address'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('company_address') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>USDT erc20</label>
                                    <input name="usdt_erc" type="text" id="usdt_erc" placeholder="Enter here..."
                                        value="{{ config('app.usdt_erc') }}" class="form-control">
                                    @if ($errors->has('usdt_erc'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('usdt_erc') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>USDT trc20</label>
                                    <input name="usdt_trc" type="text" id="usdt_trc" placeholder="Enter here..."
                                        value="{{ config('app.usdt_trc') }}" class="form-control">
                                    @if ($errors->has('usdt_trc'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('usdt_trc') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>BTC</label>
                                    <input name="btc" type="text" id="btc" placeholder="Enter here..."
                                        value="{{ config('app.btc') }}" class="form-control">
                                    @if ($errors->has('btc'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('btc') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="basic-form">
                            <div class="to-left-serach table-responsive custom-table">
                                <table class="table table-striped table-borderless">
                                    <thead>
                                        <tr class="table-active">
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th style="width: 150px;">Add More</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tab_logic">
                                        <tr data-id="1">
                                            <td>
                                                <input placeholder="Enter here..." class="form-control"
                                                    name="amount_deducted" type="text" id="amount_deducted"
                                                    value="Amount to be deducted from 1st settlement.">
                                                @if ($errors->has('amount_deducted'))
                                                    <span class="text-danger help-block form-error">
                                                        {{ $errors->first('amount_deducted') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <input placeholder="Enter here..." class="form-control"
                                                    id="amount_deducted_value" name="amount_deducted_value"
                                                    type="text" value="{{ old('amount_deducted_value') }}">
                                                @if ($errors->has('amount_deducted_value'))
                                                    <span class="text-danger help-block form-error">
                                                        {{ $errors->first('amount_deducted_value') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                            </td>
                                        </tr>
                                        @if (Input::old('generate_apy_key') != '')
                                            <div id="countVar" data-count="{{ count(Input::old('generate_apy_key')) }}">
                                            </div>
                                            @foreach (Input::old('generate_apy_key') as $key => $value)
                                                <tr data-id={{ $key == 0 ? $key + 1 : $key }}>
                                                    <td>
                                                        <input placeholder="Enter here..." class="form-control"
                                                            name="generate_apy_key[{{ $key }}][website_name]"
                                                            type="text"
                                                            value="{{ old('generate_apy_key.' . $key . '.website_name') }}">
                                                        @if ($errors->has('generate_apy_key.' . $key . '.website_name'))
                                                            <span class="text-danger help-block form-error">
                                                                {{ $errors->first('generate_apy_key.' . $key . '.website_name') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input placeholder="Enter here..." class="form-control"
                                                            name="generate_apy_key[{{ $key }}][ip_address]"
                                                            type="text"
                                                            value="{{ old('generate_apy_key.' . $key . '.ip_address') }}">
                                                        @if ($errors->has('generate_apy_key.' . $key . '.ip_address'))
                                                            <span class="text-danger help-block form-error">
                                                                {{ $errors->first('generate_apy_key.' . $key . '.ip_address') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($key == 0)
                                                            <button type="button" class="btn btn-primary plus"> <i
                                                                    class="fa fa-plus"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-primary plus"> <i
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
                                            <div id="countVar" data-count="0"></div>
                                            <tr data-id="1">
                                                <td>
                                                    <input placeholder="Enter here..." class="form-control"
                                                        name="description[0][description]" type="text"
                                                        value="Setup fees [Upfront]">
                                                    @if ($errors->has('description.0.description'))
                                                        <span class="text-danger help-block form-error">
                                                            {{ $errors->first('description.0.description') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input placeholder="Enter here..." class="form-control"
                                                        name="description[0][amount]" type="text"
                                                        value="{{ old('description.0.amount') }}">
                                                    @if ($errors->has('description.0.amount'))
                                                        <span class="text-danger help-block form-error">
                                                            {{ $errors->first('description.0.amount') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">

                                                </td>
                                            </tr>
                                            <tr data-id="2">
                                                <td>
                                                    <input placeholder="Enter here..." class="form-control"
                                                        name="description[1][description]" type="text"
                                                        value="Annual fees [Upfront]">
                                                    @if ($errors->has('description.1.description'))
                                                        <span class="text-danger help-block form-error">
                                                            {{ $errors->first('description.1.description') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input placeholder="Enter here..." class="form-control"
                                                        name="description[1][amount]" type="text"
                                                        value="{{ old('description.1.amount') }}">
                                                    @if ($errors->has('description.1.amount'))
                                                        <span class="text-danger help-block form-error">
                                                            {{ $errors->first('description.1.amount') }}
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
                        <div class="row mt-2">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-danger">Submit</button>
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>




@endsection
@section('customScript')
    <script type="text/javascript">
        $("#company_id").on("change", function() {
            var company_id = $("#company_id").val();
            $.ajax({
                url: '{{ route('get-application-invoice') }}',
                type: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'id': company_id
                },

                success: function(response) {
                    var companyData = response.data;
                    $("#business_name").val(companyData.business_name);
                    $("#email").val(companyData.email);
                    $("#phone_no").val("+" + companyData.country_code + companyData.phone_no);
                    $("#company_address").val(companyData.business_address1);
                },
            });
        })

        $("body").on("click", ".plus", function() {
            // i = $('#tab_logic tr').length;
            var i = $("#tab_logic tr:last").data("id");
            i = i + 1;
            $("#tab_logic").append(
                '<tr data-id="' +
                i +
                '">\
                                                        	            <td>\
                                                        	                <input placeholder="Enter here..." class="form-control form-control-lg form-control-solid" name="description[' +
                i +
                '][description]" type="text">\
                                                        	            </td>\
                                                        	            <td>\
                                                        	                <input placeholder="Enter here..." class="form-control form-control-lg form-control-solid" name="description[' +
                i +
                '][amount]" type="text">\
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
    </script>
@endsection
