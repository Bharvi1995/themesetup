@extends('layouts.admin.default')
@section('title')
    Admin Integration Preference
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('integration-preference.index') }}">Integration
        Preference</a> / Edit
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Edit Integration Preference</h4>
                    </div>
                    <a href="{{ route('integration-preference.index') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left"></i> </a>
                </div>
                <div class="card-body">
                    {{ Form::model($technologypartner, ['route' => ['integration-preference.update', $technologypartner->id], 'method' => 'PUT', 'class' => 'form form-dark form-horizontal', 'id' => 'technology-form']) }}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="text">Name</label>
                                {!! Form::text('name', Input::get('name'), ['placeholder' => 'Enter Name', 'class' => 'form-control']) !!}
                                @if ($errors->has('name'))
                                    <span class="text-danger help-block form-error">
                                        <span>{{ $errors->first('name') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-12 mt-1">
                            <button type="submit" class="btn btn-primary">
                                Save
                            </button>
                            <a href="{{ route('integration-preference.index') }}" type="button"
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
@section('customScript')
    <script type="text/javascript">
        $('#technology-form').submit(function() {
            $(this).find('input:text').each(function() {
                $(this).val($.trim($(this).val()));
            });
        });
    </script>
@endsection
