@extends('layouts.appAdmin')

@section('style')
    <link rel="stylesheet" href="{{ storage_asset('NewTheme/assets/css/dashforge.css') }}">
    <link rel="stylesheet" href="{{ storage_asset('NewTheme/assets/css/dashforge.demo.css') }}">
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mg-b-15">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                    <li class="breadcrumb-item"><a href="{!! url('paylaksa/dashboard') !!}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('admin.new.agent-payout-generate') !!}">Generated Agent Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Upload Report File</li>
                </ol>
            </nav>
            <h4 class="mg-b-0 tx-spacing--1">Upload Report File</h4>
        </div>
        <div class="d-none d-md-block">
            <a href="{!! route('admin.new.agent-payout-generate') !!}" class="btn btn-sm pd-x-15 btn-danger btn-uppercase mg-l-5">
                <i class="fa fa-arrow-left"></i>
                Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert">×</button>
                    <div class="alert-message">
                        <span><strong>Error!</strong> {{ $message }}</span>
                    </div>
                </div>
            @endif
            {!! Session::forget('error') !!}
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert">×</button>
                    <div class="alert-message">
                        <span><strong>Success!</strong> {{ $message }}</span>
                    </div>
                </div>
            @endif
            {!! Session::forget('success') !!}
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div data-label="Upload Report File" class="df-example demo-table">
                {!! Form::open(['route' => 'admin.new.agent-payout-files-store', 'method' => 'post', 'files' => true]) !!}
                <input type="hidden" name="id" value="{{ $data->id }}">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group row {{ $errors->has('files') ? ' has-error' : '' }}">
                            <label class="col-sm-3 col-form-label">Upload Files</label>
                            <div class="col-sm-9">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-warning" type="button">Button</button>
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" name="files" class="custom-file-input"
                                            id="inputGroupFile03">
                                        <label class="custom-file-label" for="inputGroupFile03">Choose file</label>
                                    </div>
                                </div>
                                @if ($errors->has('files'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ $errors->first('files') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <button type="submit" class="btn btn-sm btn-success">Submit</button>
                <a href="{!! route('admin.new.agent-payout-generate') !!}" class="btn btn-sm btn-danger">Cancel</a>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('#inputGroupFile03').on('change', function() {
            //get the file name
            var fileName = $(this).val();
            //replace the "Choose a file" label
            $(this).next('.custom-file-label').html(fileName);
        })
    </script>
@endsection
