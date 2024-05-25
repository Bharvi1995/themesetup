@extends('layouts.admin.default')
@section('title')
    Rules
@endsection

@section('customeStyle')
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Merchant Rules List
@endsection

@section('content')
    @if (\Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top:20px">
            <div class="alert-body">
                {{ \Session::get('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="Close"></button>
        </div>
    @endif
    @if (\Session::get('error'))
        <div class="alert alert-danger alert-dismissible show" role="alert" style="margin-top:20px">
            <div class="alert-body">
            <strong>Oh snap!</strong> {{ \Session::get('error') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="Close"></button>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body gateway-card">
                    <div class="row">
                        <div class="col-md-9">
                            <h4 class="card-title mb-1">
                                Merchant Card Rules
                            </h4>
                            <p class="card-text">Total Merchant Card Rules - {{ $CardRules }}</p>
                        </div>
                        <div class="col-md-3 text-right">
                            <i class="fa fa-credit-card" style="font-size: 32px;"></i>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <a href="{{ route('admin.merchant_rules.list', ['type' => 'Card']) }}"
                                class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body gateway-card">
                    <div class="row">
                        <div class="col-md-9">
                            <h4 class="card-title mb-1">
                                Merchant Bank Rules
                            </h4>
                            <p class="card-text">Total Merchant Bank Rules - {{ $BankRules }}</p>
                        </div>
                        <div class="col-md-3 text-right">
                            <i class="fa fa-bank" style="font-size: 32px;"></i>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <a href="{{ route('admin.merchant_rules.list', ['type' => 'Bank']) }}"
                                class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body gateway-card">
                    <div class="row">
                        <div class="col-md-9">
                            <h4 class="card-title mb-1">
                                Merchant Crypto Rules
                            </h4>
                            <p class="card-text">Total Merchant Crypto Rules - {{ $CryptoRules }}</p>
                        </div>
                        <div class="col-md-3 text-right">
                            <i class="fa fa-btc" style="font-size: 32px;"></i>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <a href="{{ route('admin.merchant_rules.list', ['type' => 'Crypto']) }}"
                                class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body gateway-card">
                    <div class="row">
                        <div class="col-md-9">
                            <h4 class="card-title mb-1">
                                Merchant UPI Rules
                            </h4>
                            <p class="card-text">Total Merchant UPI Rules - {{ $upiRules }}</p>
                        </div>
                        <div class="col-md-3 text-right">
                            <i class="fa fa-money" style="font-size: 32px;"></i>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <a href="{{ route('admin.merchant_rules.list', ['type' => 'UPI']) }}"
                                class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
@endsection
