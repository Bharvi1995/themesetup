@extends('layouts.user.default')

@section('title')
    IP Whitelist
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('dashboardPage') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('whitelist-ip') }}">IP Support</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Add IP</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Add IP</h6>
    </nav>
@endsection

@section('content')
<div class="row">
   <div class="col-12">
    <div class="col-xxl-8">
        <div class="card">
            <div class="card-header">
                <h5>Add IP</h5>
            </div>

            <div class="card-body">
                {!! Form::open(['route' => 'generate-apy-key', 'files' => true, 'class' => 'form-dark']) !!}
                    <div class="basic-form">
                        <div class="table-responsive custom-table">
                            <table class="table table-borderless">
                                <thead>
                                    <tr class="table-active">
                                        <!-- <th>Website URL</th> -->
                                        <th>IP Address</th>
                                        <th style="width: 150px;">Add</th>
                                    </tr>
                                </thead>
                                <tbody id="tab_logic">
                                    @if (Input::old('generate_apy_key') != '')
                                        <div id="countVar" data-count="{{ count(Input::old('generate_apy_key')) }}"></div>
                                        @foreach (Input::old('generate_apy_key') as $key => $value)
                                            <tr data-id={{ $key == 0 ? $key + 1 : $key }}>
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
                                                        <button type="button" class="btn btn-primary plus"> Plus
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-primary plus"> Plus
                                                        </button>
                                                        <button type="button" class="btn btn-danger minus"> Minus
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
                                                    name="generate_apy_key[0][ip_address]" type="text"
                                                    value="{{ old('generate_apy_key.0.ip_address') }}">
                                                @if ($errors->has('generate_apy_key.0.ip_address'))
                                                    <span class="text-danger help-block form-error">
                                                        {{ $errors->first('generate_apy_key.0.ip_address') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-primary plus"> Plus
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Submit</button>
                <!-- </div> -->
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/front/apidoc.js') }}"></script>
@endsection
