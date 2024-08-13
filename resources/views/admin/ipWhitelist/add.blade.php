@extends('layouts.admin.default')

@section('title')
    IP Whitelist
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ url('paylaksa/ip-whitelist') }}">IP Whitelist</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Add</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Add</h6>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Add IP</h4>
                    </div>
                </div>
                {!! Form::open(['route' => 'store.ip', 'files' => true, 'class'=>'form-dark']) !!}
                <div class="card-body">
                    <div class="col-lg-6 mb-2">
                        <label>Company Name</label>
                        <select class="form-control select2" name="company_name">
                            <option value="">-- Select Company --</option>
                            @foreach ($company_name as $company)
                                <option value="{{ $company->user_id }}">{{ $company->business_name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('company_name'))
                            <span class="text-danger help-block form-error">
                                {{ $errors->first('company_name') }}
                            </span>
                        @endif
                    </div>
                    <div class="basic-form">
                        <div class="to-left-serach table-responsive custom-table">
                            <table class="table table-borderless custom-inner-tables">
                                <thead>
                                    <tr class="table-active">
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Website URL</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">IP Address</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 150px;">Add More</th>
                                    </tr>
                                </thead>
                                <tbody id="tab_logic">
                                    @if (Input::old('generate_apy_key') != '')
                                        <div id="countVar" data-count="{{ count(Input::old('generate_apy_key')) }}"></div>
                                        @foreach (Input::old('generate_apy_key') as $key => $value)
                                            <tr data-id={{ $key == 0 ? $key + 1 : $key }}>
                                                <td class="align-middle text-center text-sm">
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
                                                <td class="align-middle text-center text-sm">
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
                                                <td class="align-middle text-center text-sm">
                                                    @if ($key == 0)
                                                        <button type="button" class="btn btn-primary btn-sm plus"> Plus
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-primary btn-sm plus"> Plus
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm minus"> Minus
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <div id="countVar" data-count="0"></div>
                                        <tr data-id="1">
                                            <td class="align-middle text-center text-sm">
                                                <input placeholder="Enter here..." class="form-control"
                                                    name="generate_apy_key[0][website_name]" type="text"
                                                    value="{{ old('generate_apy_key.0.website_name') }}">
                                                @if ($errors->has('generate_apy_key.0.website_name'))
                                                    <span class="text-danger help-block form-error">
                                                        {{ $errors->first('generate_apy_key.0.website_name') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <input placeholder="Enter here..." class="form-control"
                                                    name="generate_apy_key[0][ip_address]" type="text"
                                                    value="{{ old('generate_apy_key.0.ip_address') }}">
                                                @if ($errors->has('generate_apy_key.0.ip_address'))
                                                    <span class="text-danger help-block form-error">
                                                        {{ $errors->first('generate_apy_key.0.ip_address') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <button type="button" class="btn btn-primary btn-sm plus"> Plus
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-1">Submit</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/front/apidoc.js') }}"></script>
@endsection
