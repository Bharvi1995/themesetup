@extends('layouts.admin.default')

@section('title')
    Add Industry Type
@endsection
@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('categories.index') }}">Industry Type</a> / Add
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Add Industry Type</h4>
                    </div>
                    <a href="{{ route('categories.index') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left"></i> </a>
                </div>
                <div class="card-body">
                    {!! Form::open([
                        'route' => 'categories.store',
                        'method' => 'POST',
                        'class' => 'form form-dark form-horizontal',
                        'id' => 'category-form',
                    ]) !!}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="text">Name</label>
                                {!! Form::text('name', Input::get('name'), ['placeholder' => 'Enter here', 'class' => 'form-control']) !!}
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
                            <a href="{{ route('categories.index') }}" type="button" class="btn btn-danger">
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
        $('#category-form').submit(function() {
            $(this).find('input:text').each(function() {
                $(this).val($.trim($(this).val()));
            });
        });
    </script>
@endsection
