@extends('layouts.admin.default')
@section('title')
    Rules
@endsection

@section('customeStyle')
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Rules List
@endsection


@section('content')
    @if (\Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top:20px">
            <div class="alert-body">
                {{ \Session::get('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (\Session::get('error'))
        <div class="alert alert-danger alert-dismissible show" role="alert" style="margin-top:20px">
            <div class="alert-body">
                <strong>Oh snap!</strong> {{ \Session::get('error') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row">
        <div class=" col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body gateway-card">
                    <div class="row">
                        <div class="col-md-9">
                            <h4 class="card-title mb-1">
                                Card Rules
                            </h4>
                            <p class="card-text">Total Card Rules - {{ $CardRules }}</p>
                        </div>
                        <div class="col-md-3 text-right">
                            <i class="fa fa-credit-card" style="font-size: 32px;"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center CreateRuleBtns">
                        @if (auth()->guard('admin')->user()->can(['create-rule']))
                            <a href="{{ route('admin.create_rules.create', ['type' => 'Card']) }}"
                                class="btn btn-primary mt-1">Create Rules</a>
                        @endif
                        <a href="{{ route('admin.create_rules.list', ['type' => 'Card']) }}"
                            class="btn btn-primary mt-1">View</a>
                    </div>
                </div>
            </div>
        </div>


        <div class=" col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body gateway-card">
                    <div class="row">
                        <div class="col-md-9">
                            <h4 class="card-title mb-1">
                                Bank Rules
                            </h4>
                            <p class="card-text">Total Bank Rules - {{ $BankRules }}</p>
                        </div>
                        <div class="col-md-3 text-right">
                            <i class="fa fa-bank" style="font-size: 32px;"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center CreateRuleBtns">
                        @if (auth()->guard('admin')->user()->can(['create-rule']))
                            <a href="{{ route('admin.create_rules.create', ['type' => 'Bank']) }}"
                                class="btn btn-primary mt-1">Create Rules</a>
                        @endif
                        <a href="{{ route('admin.create_rules.list', ['type' => 'Bank']) }}"
                            class="btn btn-primary mt-1">View</a>
                    </div>
                </div>
            </div>
        </div>

        <div class=" col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body gateway-card">
                    <div class="row">
                        <div class="col-md-9">
                            <h4 class="card-title mb-1">
                                Crypto Rules
                            </h4>
                            <p class="card-text">Total Crypto Rules - {{ $CryptoRules }}</p>

                        </div>
                        <div class="col-md-3 text-right">
                            <i class="fa fa-btc" style="font-size: 32px;"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center CreateRuleBtns">
                        @if (auth()->guard('admin')->user()->can(['create-rule']))
                            <a href="{{ route('admin.create_rules.create', ['type' => 'Crypto']) }}"
                                class="btn btn-primary mt-1">Create Rules</a>
                        @endif
                        <a href="{{ route('admin.create_rules.list', ['type' => 'Crypto']) }}"
                            class="btn btn-primary mt-1">View</a>
                    </div>
                </div>
            </div>
        </div>

        <div class=" col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body gateway-card">
                    <div class="row">
                        <div class="col-md-9">
                            <h4 class="card-title mb-1">
                                UPI Rules
                            </h4>
                            <p class="card-text">Total UPI Rules - {{ $upiRules }}</p>
                        </div>
                        <div class="col-md-3 text-right">
                            <i class="fa fa-money" style="font-size: 32px;"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center CreateRuleBtns">
                        @if (auth()->guard('admin')->user()->can(['create-rule']))
                            <a href="{{ route('admin.create_rules.create', ['type' => 'UPI']) }}"
                                class="btn btn-primary mt-1">Create Rules</a>
                        @endif
                        <a href="{{ route('admin.create_rules.list', ['type' => 'UPI']) }}"
                            class="btn btn-primary mt-1">View</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
@endsection
