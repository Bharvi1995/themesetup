@extends('layouts.user.default')

@section('title')
My KYC Edit
@endsection

@section('breadcrumbTitle')
<a href="{{ route('dashboardPage') }}">Dashboard</a> / <a href="{{ route('my-application') }}">My KYC</a> / Edit
@endsection

@section('customeStyle')
<style type="text/css">
    .error {
        color: #bd2525;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12 col-xxl-12">
        <div class="card border-card">
            <div class="card-header">
                <div class="header-title">
                    <h4 class="card-title">My KYC Edit</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    {{ Form::model($data, array('route' => array('applications-update', $data->id), 'method' => 'PUT', 'class' => 'form form-dark', 'enctype'=>'multipart/form-data','id'=>'application-form')) }}
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $data->user_id }}">
                    <div class="form-row row">
                        @include('partials.application.applicationFrom' ,['isEdit' => true])
                        
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Domain Ownership <span class="text-danger">*</span></label>
                                <div class="row mx-auto">
                                    <div class="col-md-10 p-0">
                                        <div class="custom-file">
                                            <input type="file" class="form-control"
                                                name="domain_ownership">
                                        </div>
                                        @if ($errors->has('domain_ownership'))
                                        <span class="text-danger help-block form-error">
                                            {{ $errors->first('domain_ownership') }}
                                        </span>
                                        @endif
                                    </div>
                                    @if ($data->domain_ownership != null)
                                    <div class="col-md-2">
                                        <a href="{{ getS3Url($data->domain_ownership) }}" target="_blank"
                                            class="btn btn-primary btn-icon"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    </div>
                                    @endif
                                </div>

                            </div>
                        </div>

                        <div class="col-md-12 mt-2 text-right">
                            <a href="{{route('my-application')}}" class="btn btn-danger"> Cancel </a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/jquery.validate.min.js') }}"></script>
    <script>
        var isEditPage = true;
    </script>
    <script src="{{ storage_asset('themeAdmin/custom_js/front/applications/applications.js') }}"></script>
    <script src="{{ storage_asset('themeAdmin/custom_js/front/applications/edit.js') }}"></script>

    @endsection