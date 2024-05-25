@extends('layouts.admin.default')

@section('title')
    Agreement Content
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('agreement_content.index') }}">Agreement
        Content</a> / Create
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Create Agreement Content</h4>
                    </div>
                    <a href="{{ route('agreement_content.index') }}" class="btn btn-primary btn-sm"><i
                            class="fa fa-arrow-left"></i>
                    </a>
                </div>
                <div class="card-body">
                    {!! Form::open([
                        'route' => 'agreement_content.store',
                        'method' => 'POST',
                        'class' => 'form form-dark w-100',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="type">Agreement Content For</label>
                                <div class="input-div">
                                    {!! Form::select('type', ['1' => 'Merchant', '2' => 'RP'], null, [
                                        'class' => 'form-control',
                                        'data-width' => '100%',
                                    ]) !!}
                                </div>
                                @if ($errors->has('type'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('type') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="body">Agreement Content</label>

                                <textarea name="body" id="example" class="form-control"></textarea>

                                @if ($errors->has('body'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('body') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-12 mt-1">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('admin/agreement_content') }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script src="https://cdn.ckeditor.com/4.16.2/full/ckeditor.js"></script>

    <script>
        CKEDITOR.replace('example');
    </script>
@endsection
