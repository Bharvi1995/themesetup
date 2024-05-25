@extends('layouts.user.default')

@section('title')
    Rates & Charges
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('dashboardPage') }}">Dashboard</a> / Rates & Charges
@endsection


@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Your fees as per our agreed-upon rate.</h4>
        </div>
        <div class="card-body">
            @include('partials.user.user_fee')
        </div>
    </div>
@endsection
