@extends('layouts.admin.default')
@section('title')
    Required Fields
@endsection
@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('required_fields.index') }}">Required Fields</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Edit</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Edit</h6>
    </nav>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Edit Required fields</h4>
                    </div>
                    </a>
                </div>
                <div class="card-body">
                    {{ Form::model($data, ['route' => ['required_fields.update', $data->id], 'method' => 'patch', 'id' => 'agent-form', 'class'=>'form-dark']) }}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Field Title</label>
                                <input class="form-control" name="field_title" type="text" placeholder="Field Title"
                                    value="{{ $data->field_title }}">
                                @if ($errors->has('field_title'))
                                    <span class="help-block">
                                        <span class="text-danger">{{ $errors->first('field_title') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Field</label>
                                <input class="form-control" name="field" type="text" placeholder="Field"
                                    value="{{ $data->field }}">
                                @if ($errors->has('field'))
                                    <span class="help-block">
                                        <span class="text-danger">{{ $errors->first('field') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Type</label>
                                <select class="form-control" name="field_type">
                                    <option value="">-Type-</option>
                                    @foreach (getFieldsType() as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ $key == $data->field_type ? 'selected' : '' }}>
                                            {{ $value }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('field_type'))
                                    <span class="help-block">
                                        <span class="text-danger">{{ $errors->first('field_type') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Field Validation</label>
                                <input class="form-control" name="field_validation" type="text" placeholder="Field Title"
                                    value="{{ $data->field_validation }}">
                                @if ($errors->has('field_validation'))
                                    <span class="help-block">
                                        <span class="text-danger">{{ $errors->first('field_validation') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-12 mt-1">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('paylaksa/required_fields') }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
