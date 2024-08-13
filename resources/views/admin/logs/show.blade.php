@extends('layouts.admin.default')

@section('title')
    Log Details
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin-logs.index') }}">Admin Logs</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Log Details</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Log Details</h6>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Log Details</h4>
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
