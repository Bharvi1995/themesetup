@extends('layouts.admin.default')

@section('title')
    Mass Transaction Action
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Mass Transaction Action
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Mass Transaction Action</h4>
                    </div>
                    <a href="{{ route('agreement_content.index') }}" class="btn btn-primary btn-sm"><i
                            class="fa fa-arrow-left"></i>
                    </a>
                </div>
                <div class="card-body">
                    {!! Form::open([
                        'route' => 'mass-transaction-action.store',
                        'method' => 'POST',
                        'class' => 'form form-dark w-100',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="transaction_type">Action</label>
                                <div class="input-div">
                                    {!! Form::select(
                                        'transaction_type',
                                        ['suspicious' => 'Mark Suspicious', 'remove_suspicious' => 'Remove Suspicious'],
                                        null,
                                        ['class' => 'form-control', 'data-width' => '100%'],
                                    ) !!}
                                </div>
                                @if ($errors->has('transaction_type'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('transaction_type') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label" for="transaction_file">Upload Transaction File</label>

                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="form-control custom-file-input filestyle" name="transaction_file"
                                            data-buttonname="btn-inverse" accept=".csv, .xls, .xlsx" id="inputGroupFile1">
                                    </div>
                                </div>
                                @if ($errors->has('files'))
                                    <p class="text-danger">
                                        <strong>{{ $errors->first('files') }}</strong>
                                    </p>
                                @endif

                            </div>
                        </div>

                        <div class="col-lg-12 mt-1">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <a href="{{ url('admin/technical') }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
