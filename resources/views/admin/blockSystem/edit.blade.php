@extends('layouts.admin.default')

@section('title')
    Block Card/Email
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('dashboardPage') }}">Dashboard</a> / <a href="{{ url('admin/block-system') }}"> Block Card/Email</a> /
    Edit
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Edit Block Card/Email</h4>
                    </div>
                    <a href="{{ url('admin/block-system') }}" class="btn btn-primary d-none d-md-block btn-sm"><i
                            class="fa fa-arrow-left"></i></a>
                </div>
                {!! Form::open(['route' => ['update.block-system', $data->id], 'files' => true, 'class'=>'form-dark']) !!}
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label>Type</label>
                            <select class="form-control select2" name="type">
                                <option value="">-- Select Type --</option>
                                <option value="Card" {{ $data->type == 'Card' ? 'selected' : '' }}>Card</option>
                                <option value="Email" {{ $data->type == 'Email' ? 'selected' : '' }}>Email</option>
                            </select>
                            @if ($errors->has('type'))
                                <span class="text-danger help-block form-error">
                                    {{ $errors->first('type') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Field Value</label>
                            <input type="text" name="field_value" id="field_value" class="form-control"
                                value="{{ $data->field_value }}">
                            @if ($errors->has('field_value'))
                                <span class="text-danger help-block form-error">
                                    {{ $errors->first('field_value') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-1">Submit</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
