@extends('layouts.admin.default')
@section('title')
Add Refund for Oculus
@endsection

@section('breadcrumbTitle')
<a href="{{ route('admin.dashboard') }}">Dashboard</a> / Add Refund for Oculus
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 col-xl-12">
        <div class="iq-card border-card">
            <div class="iq-card-header bg-info d-flex justify-content-between">
                <div class="iq-header-title">
                
                    <h4 class="card-title">Add Refund for Oculus</h4>
                </div>
            </div>
            <div class="iq-card-body">
                {!! Form::open(array('route' => 'oculus.refund.store' , 'method' => 'post','id'=>'mid-form'))
                !!}
                <div class="row">
                    <div class="form-group col-lg-6">
                        <label for="">Order Id <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter here..." name="order_id">
                        @if ($errors->has('order_id'))
                        <span class="help-block">
                            <span class="text-danger">{{ $errors->first('order_id') }}</span>
                        </span>
                        @endif
                    </div>
                    <div class="form-group col-lg-6">
                        <label for="name">Gateway Order id <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter here..." name="gateway_order_id">
                        @if ($errors->has('gateway_order_id'))
                        <span class="help-block">
                            <span class="text-danger">{{ $errors->first('gateway_order_id') }}</span>
                        </span>
                        @endif
                    </div>
                    <div class="form-group col-lg-6">
                        <label for="name">Gateway Order id of Oculus<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter here..." name="gateway_order_id_oculus">
                        @if ($errors->has('gateway_order_id_oculus'))
                        <span class="help-block">
                            <span class="text-danger">{{ $errors->first('gateway_order_id_oculus') }}</span>
                        </span>
                        @endif
                    </div>
                    <div class="form-group col-lg-12">
                        <button type="submit" class="btn btn-success btn-sm">Submit</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm">Cancel</a>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
@section('customScript')

@endsection