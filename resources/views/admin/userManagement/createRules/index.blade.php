@extends('layouts.admin.default')
@section('title')
    Rules
@endsection

@section('customeStyle')
    <link rel="stylesheet" href="{{ storage_asset('/theme/vendor/datatables/css/jquery.dataTables.min.css') }}" />
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
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">

            </button>
        </div>
    @endif
    @if (\Session::get('error'))
        <div class="alert alert-danger alert-dismissible show" role="alert" style="margin-top:20px">
            <div class="alert-body">
            <strong>Oh snap!</strong> {{ \Session::get('error') }}
        </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">

            </button>
        </div>
    @endif
    <div class="row">
        <div class="col-xl-4 col-lg-6 col-sm-6">
            <div class="card overflow-hidden">
                <div class="card-body p-0">
                    <div class="text-center">
                        <div class="profile-photo">
                            <img src="images/profile/profile.png" width="100" class="img-fluid rounded-circle"
                                alt="">
                        </div>
                        <h3 class="mt-4 mb-1">Card Rules</h3>
                        <a class="btn btn-outline-primary btn-rounded mt-3 px-5"
                            href="{{ route('admin.create_rules.create', ['type' => 'Card']) }}">Create Rules</a>
                    </div>
                </div>
                <div class="card-footer pt-0 pb-0 text-center">
                    <div class="row">
                        <div class="col-12 pt-3 pb-3">
                            <a href="{{ route('admin.create_rules.list', ['type' => 'Card']) }}">
                                <h3 class="mb-1">{{ $CardRules }}</h3> <span>Total Card Rules</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-sm-6">
            <div class="card overflow-hidden">
                <div class="card-body p-0">
                    <div class="text-center">
                        <div class="profile-photo">
                            <img src="images/profile/profile.png" width="100" class="img-fluid rounded-circle"
                                alt="">
                        </div>
                        <h3 class="mt-4 mb-1">Bank Rules</h3>
                        <a class="btn btn-outline-primary btn-rounded mt-3 px-5"
                            href="{{ route('admin.create_rules.create', ['type' => 'Bank']) }}">Create Rules</a>
                    </div>
                </div>
                <div class="card-footer pt-0 pb-0 text-center">
                    <div class="row">
                        <div class="col-12 pt-3 pb-3">
                            <a href="{{ route('admin.create_rules.list', ['type' => 'Bank']) }}">
                                <h3 class="mb-1">{{ $BankRules }}</h3> <span>Total Bank Rules</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-sm-6">
            <div class="card overflow-hidden">
                <div class="card-body p-0">
                    <div class="text-center">
                        <div class="profile-photo">
                            <img src="images/profile/profile.png" width="100" class="img-fluid rounded-circle"
                                alt="">
                        </div>
                        <h3 class="mt-4 mb-1">Crypto Rules</h3>
                        <a class="btn btn-outline-primary btn-rounded mt-3 px-5"
                            href="{{ route('admin.create_rules.create', ['type' => 'Crypto']) }}">Create Rules</a>
                    </div>
                </div>
                <div class="card-footer pt-0 pb-0 text-center">
                    <div class="row">
                        <div class="col-12 pt-3 pb-3">
                            <a href="{{ route('admin.create_rules.list', ['type' => 'Crypto']) }}">
                                <h3 class="mb-1">{{ $CryptoRules }}</h3> <span>Total Crypto Rules</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
@endsection
