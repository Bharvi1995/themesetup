@extends('layouts.admin.default')

@section('title')
    Block Card/Email
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ url('paylaksa/block-system') }}">Block Card/Email System</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Edit</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Edit</h6>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Edit Block Card/Email</h4>
                    </div>
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
