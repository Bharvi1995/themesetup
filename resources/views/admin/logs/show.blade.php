@extends('layouts.admin.default')

@section('title')
    Log Details
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Log Details
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Log Details</h4>
                    </div>
                    <div class="btn-group me-2">
                        <a href="{{ route('admin-logs.index') }}" class="btn btn-primary btn-sm"><i
                                class="fa fa-arrow-left"></i> </a>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        echo '<pre>';
                        echo json_encode($json, JSON_PRETTY_PRINT);
                        echo '</pre>';
                    @endphp
                </div>
            </div>
        </div>
    </div>
@endsection
