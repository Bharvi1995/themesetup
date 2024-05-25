@extends('layouts.user.default')

@section('title')
Helpdesk Create
@endsection

@section('breadcrumbTitle')
<a href="{{ route('dashboardPage') }}">Dashboard</a> / <a href="{{ route('ticket') }}">Helpdesk</a> / Create
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12 col-xxl-12">
        @if($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <div class="alert-message">
                    <span><strong>Error!</strong> {{ $message }}</span>
                </div>
            </div>
        @endif
        {!! Session::forget('error') !!}
        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <div class="alert-message">
                    <span><strong>Success!</strong> {{ $message }}</span>
                </div>
            </div>
        @endif
        {!! Session::forget('success') !!}

        {!! Form::open(array('route' => 'ticket.store','method'=>'POST','class'=>'form-dark','enctype'=>'multipart/form-data','id'=>'ticket-form')) !!}
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">HelpDesk</h4>
                    </div>
                    <a href="{{ route('ticket') }}" class="btn btn-primary btn-sm btn-icon" title="Back">  <i class="fa fa-arrow-left"></i> </a>
                </div>

                <div class="card-body">
                    <div class="basic-form">
                        <div class="form-row row">
                            <!-- <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Title</label>
                                    {!! Form::text('title', Input::get('title'), array('placeholder' => 'Enter Title','class' => 'form-control')) !!}
                                    @if ($errors->has('title'))
                                        <span class="help-block text-danger">
                                            {{ $errors->first('title') }}
                                        </span>
                                    @endif
                                </div>
                            </div> -->
                           <!--  <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Select Department</label>
                                    {!! Form::Select('department', [''=>'Select Department','1'=>'Technical','2'=>'Finance','3'=>'Customer Service'], null, array('class'=>'form-control select2', 'data-size'=>'7','data-live-search'=>'true', 'data-title'=>'--Roles--', 'data-width'=>'100%')) !!}
                                    @if ($errors->has('department'))
                                        <span class="help-block text-danger">
                                            {{ $errors->first('department') }}
                                        </span>
                                    @endif
                                </div>           
                            </div>   -->                 
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Description</label>
                                    {!! Form::textarea('body', Input::get('body'), array('placeholder' => 'Description','class' => 'form-control', 'rows' => '5')) !!}
                                    @if ($errors->has('body'))
                                        <span class="help-block text-danger">
                                            {{ $errors->first('body') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">File</label>
                                    <input type="file" name="files[]" class="form-control" id="inputGroupFile03" multiple="multiple">
                                    @if($errors->has('files.*'))
                                        @foreach ($errors->get('files.*') as $error)
                                            <li class="text-danger help-block form-error">{{ $error[0] }}</li>
                                        @endforeach
                                    @endif
                                </div>                                
                            </div>
                        </div>
                    </div>
                    <button type="reset" class="btn btn-danger mt-1">Cancel</button>
                    <button type="submit" class="btn btn-primary mt-1">Submit</button>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/front/ticket.js') }}"></script>
@endsection