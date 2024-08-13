@extends('layouts.admin.default')
@section('title')
    Technical & Additional
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Technical & Additional</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Technical & Additional</h6>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Technical</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if (auth()->guard('admin')->user()->can(['view-ip-whitelist']))
                            <div class="col-md-12 mb-2">
                                <strong class="mt-2">IP Whitelist</strong>
                                <a class="btn btn-primary btn-sm pull-right" href="{{ route('ip-whitelist') }}">Go </a>
                            </div>
                        @endif

                        @if (auth()->guard('admin')->user()->can(['view-iframe-generator']))
                            <div class="col-md-12 mb-2">
                                <strong class="mt-2">Link Generator</strong>
                                <a class="btn btn-primary btn-sm pull-right" href="{{ route('asp-iframe') }}">Go </a>
                            </div>
                        @endif

                        @if (auth()->guard('admin')->user()->can(['view-transaction-session-data']))
                            <div class="col-md-12 mb-2">
                                <strong class="mt-2">Transaction Session Data</strong>
                                <a class="btn btn-primary btn-sm pull-right" href="{{ route('transaction-session') }}">Go </a>
                            </div>
                        @endif

                        @if (auth()->guard('admin')->user()->can(['view-transaction-session-data']))
                            <div class="col-md-12 mb-2">
                                <strong class="mt-2">Payment API Data</strong>
                                <a class="btn btn-primary btn-sm pull-right" href="{{ route('admin.paymentApi') }}">Go </a>
                            </div>
                        @endif

                        @if (auth()->guard('admin')->user()->can(['view-required-fields']))
                            <div class="col-md-12 mb-2">
                                <strong class="mt-2">Required Fields</strong>
                                <a class="btn btn-primary btn-sm pull-right" href="{!! url('paylaksa/required_fields') !!}">Go </a>
                            </div>
                        @endif
                        @if (auth()->guard('admin')->user()->can(['view-email-card-block-system']))
                            <div class="col-md-12 mb-2">
                                <strong class="mt-2">Email/Card Blacklist</strong>
                                <a class="btn btn-primary btn-sm pull-right" href="{{ route('block-system') }}">Go </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Additional</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if (auth()->guard('admin')->user()->can(['view-industry-type']))
                            <div class="col-md-12 mb-2">
                                <strong class="mt-2">Industry Type</strong>
                                <a class="btn btn-primary btn-sm pull-right" href="{{ route('categories.index') }}">Go </a>
                            </div>
                        @endif

                        @if (auth()->guard('admin')->user()->can(['view-integration-preference']))
                            <div class="col-md-12 mb-2">
                                <strong class="mt-2">Integration Preference</strong>
                                <a class="btn btn-primary btn-sm pull-right"
                                    href="{{ route('integration-preference.index') }}">Go </a>
                            </div>
                        @endif

                        @if (auth()->guard('admin')->user()->can(['view-admin-logs']))
                            <div class="col-md-12 mb-2">
                                <strong class="mt-2">Admin Logs</strong>
                                <a class="btn btn-primary btn-sm pull-right" href="{{ route('admin-logs.index') }}">Go </a>
                            </div>
                        @endif
                        @if (auth()->guard('admin')->user()->can(['view-mail-templates']))
                            <div class="col-md-12 mb-2">
                                <strong class="mt-2">Mail Templates</strong>
                                <a class="btn btn-primary btn-sm pull-right" href="{{ route('mail-templates.index') }}">Go</a>
                            </div>
                        @endif
                        @if (auth()->guard('admin')->user()->can(['view-mass-mid-switching']))
                            <div class="col-md-12 mb-2">
                                <strong class="mt-2">Mass MID</strong>
                                <a class="btn btn-primary btn-sm pull-right" href="{{ route('mass-mid.index') }}">Go</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
