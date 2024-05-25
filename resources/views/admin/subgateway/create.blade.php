@extends('layouts.admin.default')

@section('title')
    Sub Gateway Create
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> /
    <a href="{{ route('admin.subgateway.index', ['gateway_id' => $gateway->id]) }}"> Sub Gateway </a> / Create
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Sub Gateway Create</h4>
                    </div>
                    <a href="{{ route('admin.subgateway.index', ['gateway_id' => $gateway->id]) }}"
                        class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i></a>
                </div>
                <div class="card-body">
                    {!! Form::open([
                        'route' => ['admin.subgateway.store', $gateway->id],
                        'method' => 'POST',
                        'class' => 'form form-dark form-horizontal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="">Name</label>
                            {!! Form::text('name', Input::get('name'), ['placeholder' => 'Enter Name', 'class' => 'form-control']) !!}
                            @if ($errors->has('name'))
                                <span class="text-danger help-block form-error">
                                    <span>{{ $errors->first('name') }}</span>
                                </span>
                            @endif
                        </div>
                        @foreach (json_decode($gateway->credential_titles) as $key => $value)
                            <div class="form-group col-lg-6">
                                <label for="">{{ $value }}</label>
                                {!! Form::text($key, Input::get($key), ['placeholder' => $value, 'class' => 'form-control']) !!}
                                @if ($errors->has($key))
                                    <span class="text-danger help-block form-error">
                                        <span>{{ $errors->first($key) }}</span>
                                    </span>
                                @endif
                            </div>
                        @endforeach
                        <div class="form-group col-lg-12 mt-1">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('admin.subgateway.index', ['gateway_id' => $gateway->id]) }}"
                                class="btn btn-danger">
                                Cancel
                            </a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
