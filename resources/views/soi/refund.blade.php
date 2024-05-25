@extends('layouts.admin.default')

@section('title')
Add Refund for SOI
@endsection
@section('breadcrumbTitle')
<a href="{{ route('admin.dashboard') }}">Dashboard</a> /Add Refund for SOI
@endsection
@section('customeStyle')
<style type="text/css">
    .text-danger {
        color: red !important;
    }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 col-xl-12">
        <div class="iq-card border-card">
            <div class="iq-card-header bg-info d-flex justify-content-between">
                <div class="iq-header-title">
                    <h4 class="card-title">Add Refund for SOI</h4>
                </div>
                <a href="{{ route('admin-user.index') }}" class="btn btn-primary btn-xs rounded"> <i
                        class="fa fa-arrow-left" aria-hidden="true"></i></a>
            </div>
            <div class="iq-card-body">
                {!! Form::open(array('route' => 'soi.refund.store' , 'method' => 'post','id'=>'admin-form')) !!}
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Order Id</label>
                            <input class="form-control" name="order_id" type="text" placeholder="Enter here..."
                                value="{{ old('order_id') }}">
                            @if ($errors->has('order_id'))
                            <span class="help-block">
                                <span class="text-danger">{{ $errors->first('order_id') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Gateway Order id</label>
                            <input class="form-control" name="gateway_order_id" type="text" placeholder="Enter here..."
                                value="{{ old('gateway_order_id') }}">
                            @if ($errors->has('gateway_order_id'))
                            <span class="help-block">
                                <span class="text-danger">{{ $errors->first('gateway_order_id') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12 mt-3">
                        <button type="submit" class="btn btn-success btn-sm">Submit</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

</div>
@endsection