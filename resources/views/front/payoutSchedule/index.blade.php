@extends('layouts.user.default')

@section('title')
    Payout Schedule
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('dashboardPage') }}">Dashboard</a> / Payout Schedule
@endsection

@section('customeStyle')
    <style type="text/css">
        .head-icon {
            background-color: #F44336;
            color: #FFFFFF;
            font-size: 18px;
            padding: 10px;
            border-radius: 50%;
            line-height: 18px;
            margin-right: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        {{-- @if (!empty($data) && $data->count()) --}}
        @foreach ($data as $key => $value)
            <div class="col-xl-3 col-xxl-3">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="d-flex align-items-center btn-primary p-3">
                            <i class="fa fa-calendar-check-o head-icon"></i>
                            <div>
                                <h6 class="fs-16 text-danger mb-0">Issue Date</h6>
                                {{-- <span class="fs-12 text-white">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $value->issue_date)->format('d-M-Y') }}</span> --}}
                                <span class="fs-12 text-white">{{ $value['issue_date'] }}</span>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap p-4">
                            <div class="mr-5 mb-3">
                                <p class="fs-14 mb-2">From Date</p>
                                {{-- <span class="fs-20 font-w500 text-black">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $value->from_date)->format('d-M-Y') }}</span> --}}
                                <span class="fs-20 font-w500 ">{{ $value['from_date'] }}</span>
                            </div>
                            <div class="mr-5 mb-3">
                                <p class="fs-14 mb-2">To Date</p>
                                {{-- <span class="fs-20 font-w500 ">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $value->to_date)->format('d-M-Y') }}</span> --}}
                                <span class="fs-20 font-w500 ">{{ $value['to_date'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        {{-- @endif --}}
    </div>
@endsection
