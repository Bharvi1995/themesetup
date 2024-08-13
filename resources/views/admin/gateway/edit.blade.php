@extends('layouts.admin.default')
@section('title')
    Gateway Edit
@endsection
@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.gateway.index') }}">Gateway List</a></li>
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
                        <h4 class="card-title">Edit Gateway</h4>
                    </div>
                </div>
                <div class="card-body">
                    {!! Form::open([
                        'route' => ['admin.gateway.update', $data->id],
                        'method' => 'patch',
                        'class' => 'form form-dark form-horizontal',
                        'enctype' => 'multipart/form-data',
                        'id' => 'gateway-form',
                    ]) !!}
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <h4>Required Fields Edit</h4>
                            <div class="basic-form">
                                <div class="form-group row">
                                    @foreach ($required_fields as $field_key => $field_value)
                                        <?php
                                        $data1 = json_decode($data->required_fields);
                                        ?>
                                        <div class="col-md-3">
                                            <div class="custom-control form-check custom-checkbox custom-control-inline mr-0">
                                                <input class="form-check-input" id="{{ $field_value->id }}"
                                                    name="required_fields[]" type="checkbox"
                                                    value="{{ $field_value->field }}"
                                                    {{ in_array($field_value->field, $data1) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="{{ $field_value->id }}">
                                                    {{ $field_value->field_title }}
                                                </label>
                                            </div>
                                            @if ($errors->has('required_fields'))
                                                <div class="text-danger">{{ $errors->first('required_fields') }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('admin.gateway.index') }}" class="btn btn-danger">Cancel</a>
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
        $('#gateway-form').submit(function() {
            $(this).find('input:text').each(function() {
                $(this).val($.trim($(this).val()));
            });
        });
    </script>
@endsection
