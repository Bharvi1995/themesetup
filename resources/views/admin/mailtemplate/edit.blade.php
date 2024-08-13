@extends('layouts.admin.default')
@section('title')
    Admin Mail Templates
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('mail-templates.index') }}">Mail Templates</a></li>
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
                        <h4 class="card-title">Edit Mail Templates</h4>
                    </div>
                </div>
                <div class="card-body">
                    {{ Form::model($mailTemplates, ['route' => ['mail-templates.update', $mailTemplates->id], 'method' => 'PUT', 'class' => 'form form-horizontal form-dark', 'id' => 'technology-form', 'enctype' => 'multipart/form-data']) }}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="text">Name</label>
                                {!! Form::text('title', Input::get('title'), [
                                    'placeholder' => 'Enter Name',
                                    'class' => 'form-control',
                                    'value' => $mailTemplates->title,
                                ]) !!}
                                @if ($errors->has('title'))
                                    <span class="text-danger help-block form-error">
                                        <span>{{ $errors->first('title') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="text">Email subject</label>
                                {!! Form::text('email_subject', Input::get('email_subject'), [
                                    'placeholder' => 'Enter Email Subject',
                                    'class' => 'form-control',
                                    'value' => $mailTemplates->email_subject,
                                ]) !!}
                                @if ($errors->has('email_subject'))
                                    <span class="text-danger help-block form-error">
                                        <span>{{ $errors->first('email_subject') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="text">Description</label>
                                <textarea class="form-control" name="description" id="description" placeholder="Enter Description" rows="2">{{ $mailTemplates->description }}</textarea>
                                @if ($errors->has('description'))
                                    <span class="text-danger help-block form-error">
                                        <span>{{ $errors->first('description') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="text">Email Body</label>
                                <textarea class="form-control" name="email_body" id="email_body">{{ $mailTemplates->email_body }}</textarea>
                                @if ($errors->has('email_body'))
                                    <span class="text-danger help-block form-error">
                                        <span>{{ $errors->first('email_body') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="text">Files</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input form-control filestyle"
                                            name="email-template-files[]" data-buttonname="btn-inverse"
                                            accept="image/png, image/jpeg, .pdf, .zip" id="email-template-files"
                                            multiple="multiple">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 mt-1">
                            <button type="submit" class="btn btn-primary">
                                Save
                            </button>
                            <a href="{{ route('mail-templates.index') }}" type="button"
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
    <script src="https://cdn.ckeditor.com/4.15.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('email_body');
    </script>
@endsection
