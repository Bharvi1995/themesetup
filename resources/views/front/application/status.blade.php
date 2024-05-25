@extends('layouts.user.default')

@section('title')
Verification
@endsection

@section('breadcrumbTitle')
<a href="{{ route('dashboardPage') }}">Dashboard</a> 
@endsection

@section('customeStyle')
<style type="text/css">
    .imgHome{
        max-width:300px;
    }

    .rowHome{
        justify-content: center !important;
    }


</style>
@endsection

@section('content')
@if(\Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <div class="alert-body">
        {{ \Session::get('success') }}
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
{{ \Session::forget('success') }}


<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body p30">
                <div class="row rowHome">
                    <img src="{{ storage_asset('setup/images/home.svg') }}" class="imgHome" >
                </div>
                <div class="row text-center">
                    <div class="col-md-12">
                        <h4 class="mb-2 mt-2 h4Home">Welcome to testpay!</h4>
                        <p class="mb-2">Please wait for your customer relationship manager to reach out to you. They will contact you shortly to assist you further.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection