@extends('layouts.user.default')

@section('title')
    IP Whitelist
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('dashboardPage') }}">Dashboard</a> / <a href="{{ route('whitelist-ip') }}"> IP Whitelist</a> / Add
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h4 class="mt-50">Add IP</h4>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('whitelist-ip') }}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i></a>
        </div>
        <div class="col-xl-12 col-xxl-12">
            <div class="card mt-2">
                <div class="card-header">

                </div>
                {!! Form::open(['route' => 'generate-apy-key', 'files' => true, 'class' => 'form-dark']) !!}
                <div class="card-body">
                    <div class="basic-form">
                        <div class="table-responsive custom-table">
                            <table class="table table-borderless table-striped">
                                <thead>
                                    <tr class="table-active">
                                        <!-- <th>Website URL</th> -->
                                        <th>IP Address</th>
                                        <th style="width: 150px;">Add More</th>
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
                                                    name="generate_apy_key[0][ip_address]" type="text"
                                                    value="{{ old('generate_apy_key.0.ip_address') }}">
                                                @if ($errors->has('generate_apy_key.0.ip_address'))
                                                    <span class="text-danger help-block form-error">
                                                        {{ $errors->first('generate_apy_key.0.ip_address') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-primary plus btn-sm"> <i
                                                        class="fa fa-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Submit</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/front/apidoc.js') }}"></script>
@endsection
