@extends('layouts.admin.default')

@section('title')
    MID Management
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('users-management') }}">Merchant Management</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Merchant Rules</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Merchant Rules</h6>
    </nav>
@endsection

@section('customeStyle')
    <link href="{{ storage_asset('themeAdmin/css/selectize.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .card__shadow {
            -webkit-box-shadow: 10px 6px 18px -3px rgba(0, 0, 0, 0.77);
            -moz-box-shadow: 10px 6px 18px -3px rgba(0, 0, 0, 0.77);
            box-shadow: 10px 6px 18px -3px rgba(0, 0, 0, 0.77);
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body br-25">
                    <div class="row align-items-center">
                        <div class="col-xl-10 col-xxl-10">
                            <div class="d-sm-flex d-block align-items-center">
                                <div class="ms-2">
                                    <h4 class="fs-20">API Key</h4>
                                    @if (!isset($data->api_key))
                                        <a href="{{ route('api-key-generate', $data->id) }}"
                                            class="btn btn-success btn-sm">Generate API Key</a>
                                    @else
                                        <p class="fs-14 mb-0 text-danger">{{ $data->api_key }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-xxl-2 text-right">
                            <a href="{{ route('users-management') }}" class="btn btn-primary btn-sm"><i
                                    class="fa fa-arrow-left" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Company Name : {{ $data->company_name }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Nav tabs -->
                    <div class="custom-tab-1">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('personal-info', $data->id) }}"> Personal Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('assign-mid', $data->id) }}"> MID Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('card-email-limit', $data->id) }}"> Card & Email Limit</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('merchant-rate-fee', $data->id) }}"> Merchant Rate/Fee</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('additional-mail', $data->id) }}"> Additional Mail Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('merchant-rules', $data->id) }}"> Create Rules</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active">
                                <div class="pt-4">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="card iq-mb-3 ">
                                                <div class="card-body  card__shadow">
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <h4 class="card-title mb-1">
                                                                Card Rules
                                                            </h4>
                                                            <p class="card-text">Total Card Rules - {{ $CardRules }}</p>
                                                        </div>
                                                        <div class="col-md-3 text-right">
                                                            <!-- <i class="fa fa-credit-card" style="font-size: 32px;"></i> -->
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-8">
                                                            <a href="{{ route('merchant.create_rules.create', ['id' => $id, 'type' => 'Card']) }}"
                                                                class="btn btn-primary btn-block btn-sm">Create Rules</a>
                                                        </div>
                                                        <div class="col-md-4 pl-0">
                                                            <a href="{{ route('merchant.create_rules.list', ['id' => $id, 'type' => 'Card']) }}"
                                                                class="btn btn-danger btn-sm btn-block">View</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- <div class="col-lg-3">
                                            <div class="card iq-mb-3 ">
                                                <div class="card-body  card__shadow">
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
                                                    <div class="row mt-3">
                                                        <div class="col-md-8">
                                                            <a href="{{ route('merchant.create_rules.create', ['id' => $id, 'type' => 'Bank']) }}"
                                                                class="btn btn-primary btn-block btn-sm">Create Rules</a>
                                                        </div>
                                                        <div class="col-md-4 pl-0">
                                                            <a href="{{ route('merchant.create_rules.list', ['id' => $id, 'type' => 'Bank']) }}"
                                                                class="btn btn-danger btn-sm btn-block">View</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->

                                        <!-- <div class="col-lg-3">
                                            <div class="card iq-mb-3 ">
                                                <div class="card-body  card__shadow">
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <h4 class="card-title mb-1">
                                                                Crypto Rules
                                                            </h4>
                                                            <p class="card-text">Total Crypto Rules - {{ $CryptoRules }}
                                                            </p>
                                                        </div>
                                                        <div class="col-md-3 text-right">
                                                            <i class="fa fa-btc" style="font-size: 32px;"></i>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-8">
                                                            <a href="{{ route('merchant.create_rules.create', ['id' => $id, 'type' => 'Crypto']) }}"
                                                                class="btn btn-primary btn-block btn-sm">Create Rules</a>
                                                        </div>
                                                        <div class="col-md-4 pl-0">
                                                            <a href="{{ route('merchant.create_rules.list', ['id' => $id, 'type' => 'Crypto']) }}"
                                                                class="btn btn-danger btn-sm btn-block">View</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->

                                        <!-- <div class="col-lg-3">
                                            <div class="card iq-mb-3 ">
                                                <div class="card-body  card__shadow">
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
                                                    <div class="row mt-3">
                                                        <div class="col-md-8">
                                                            <a href="{{ route('merchant.create_rules.create', ['id' => $id, 'type' => 'UPI']) }}"
                                                                class="btn btn-primary btn-block btn-sm">Create Rules</a>
                                                        </div>
                                                        <div class="col-md-4 pl-0">
                                                            <a href="{{ route('merchant.create_rules.list', ['id' => $id, 'type' => 'UPI']) }}"
                                                                class="btn btn-danger btn-sm btn-block">View</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/js/selectize.min.js') }}"></script>
    <script type="text/javascript">
        $('#input-tags').selectize({
            delimiter: ',',
            persist: false,
            create: function(input) {
                return {
                    value: input,
                    text: input
                }
            }
        });
    </script>
@endsection
