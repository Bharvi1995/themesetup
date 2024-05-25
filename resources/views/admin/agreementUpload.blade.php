@extends('layouts.admin.default')

@section('title')
    Agreement Upload
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('dashboardPage') }}">Dashboard</a> / Agreement Upload
@endsection

@section('content')
    <style type="text/css">
        .help-block .text-danger {
            color: red !important;
        }
    </style>
    <div class="row">
        @if (auth()->guard('admin')->user()->can(['merchant-agreement-upload']))
            <div class="col-sm-6">
                {!! Form::open(['route' => 'agreement-upload-store', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class'=>'form-dark']) !!}
                <div class="card">
                    <div class="card-header">
                        <div class="header-title">
                            <h4 class="card-title">Agreement Upload - Merchant</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Business Name</label>
                            <select class="select2" name="business_name" data-size="7" data-live-search="true"
                                data-title="Select here" data-width="100%">
                                <option selected disabled>Select here</option>
                                @foreach ($companyName as $company)
                                    <option value="{{ $company->user_id }}"> {{ $company->business_name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('business_name'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('business_name') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="">Select File</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="form-control custom-file-input filestyle" name="files"
                                        data-buttonname="btn-inverse" accept="image/png, image/jpeg, .pdf, .zip"
                                        id="inputGroupFile1">
                                </div>
                            </div>
                            @if ($errors->has('files'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('files') }}</span>
                                </span>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary mt-1">Upload</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        @endif
        @if (auth()->guard('admin')->user()->can(['rp-agreement-upload']))
            <div class="col-sm-6">
                {!! Form::open(['route' => 'agreement-upload-store-rp', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class'=>'form-dark']) !!}
                <div class="card">
                    <div class="card-header">
                        <div class="header-title">
                            <h4 class="card-title">Agreement Upload - Referral Partners</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Referral Partners Name</label>
                            <select class="select2" name="referral_partner" data-size="7" data-live-search="true"
                                data-title="Select here" data-width="100%">
                                <option selected disabled>Select here</option>
                                @foreach ($rpName as $rp)
                                    <option value="{{ $rp->id }}"> {{ $rp->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('referral_partner'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('referral_partner') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="">Select File</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="form-control custom-file-input filestyle" name="file"
                                        data-buttonname="btn-inverse" accept="image/png, image/jpeg, .pdf, .zip"
                                        id="inputGroupFile2">
                                </div>
                            </div>
                            @if ($errors->has('file'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('file') }}</span>
                                </span>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary mt-1">Upload</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        @endif
    </div>
@endsection
@section('customScript')
@endsection
