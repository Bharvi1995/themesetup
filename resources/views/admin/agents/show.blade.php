@extends('layouts.admin.default')
@section('title')
    View Referral Partner
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('agents.index') }}">Referral Partner</a> / View
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">View Referral Partner</h4>
                    </div>
                    <a href="{{ route('agents.index') }}" class="btn btn-primary btn-sm rounded"> <i class="fa fa-arrow-left"
                            aria-hidden="true"></i></a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label>Name</label>
                            <h6>{!! $agent->name !!}</h6>
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Email</label>
                            <h6>{!! $agent->email !!}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
