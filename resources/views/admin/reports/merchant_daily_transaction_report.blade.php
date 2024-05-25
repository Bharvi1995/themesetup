@extends('layouts.admin.default')
@section('title')
    Merchant Daily Transaction Report
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Merchant Daily Transaction Report
@endsection
@section('content')

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Merchant Daily Transaction Report (USD {{ $total_amt_in_usd }})</h4>

                        @php
                            $total_count = $total_success + $total_declined;
                        @endphp

                        @if ($total_count > 0)
                            <label class="mt-3 fs-18">
                                ( <span class="text-green">Total Success : {{ $total_success }}
                                    ({{ round(($total_success / $total_count) * 100, 2) }}%)</span> ,
                                <span class="text-red">Total Declined : {{ $total_declined }}
                                    ({{ round(($total_declined / $total_count) * 100, 2) }}%)</span> )
                            </label>
                        @endif
                    </div>
                    <div class="btn-group  btn-group-sm">
                        <a href="{{ route('merchant-daily-transactions-report', ['for' => 'Today']) }}" type="button"
                            class="btn {{ !isset($_GET['for']) || (isset($_GET['for']) && $_GET['for'] == 'Today') ? 'btn-danger' : 'btn-primary' }}">Today</a>
                        <a href="{{ route('merchant-daily-transactions-report', ['for' => 'Yesterday']) }}" type="button"
                            class="btn {{ isset($_GET['for']) && $_GET['for'] == 'Yesterday' ? 'btn-danger' : 'btn-primary' }}">Yesterday</a>
                        <a href="{{ route('merchant-daily-transactions-report', ['for' => 'twoDaysBack']) }}"
                            type="button"
                            class="btn {{ isset($_GET['for']) && $_GET['for'] == 'twoDaysBack' ? 'btn-danger' : 'btn-primary' }}">Two
                            Days Back</a>
                    </div>
                </div>
                <div class="card-body p-0">

                    <div class="table-responsive custom-table tableFixHead">
                        <table class="table table-striped table-borderless">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Merchant Name</th>
                                    <th class="text-center text-green">SUCCESSFUL COUNT</th>
                                    <th class="text-center text-red">DECLINED COUNT</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if (count($merchant_daily_transactions) > 0)
                                    @foreach ($merchant_daily_transactions as $transaction)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ ucfirst($transaction->business_name) }}</td>

                                            <td class="text-center">
                                                {{ $transaction->success_count }}
                                                @if ($transaction->success_count > 0)
                                                    ({{ round(($transaction->success_count * 100) / ($transaction->success_count + $transaction->declined_count), 2) }}%)
                                                @else
                                                    ( 0 )
                                                @endif
                                            </td>

                                            <td class="text-center">{{ $transaction->declined_count }}
                                                @if ($transaction->declined_count > 0)
                                                    ({{ round(($transaction->declined_count * 100) / ($transaction->success_count + $transaction->declined_count), 2) }}%)
                                                @else
                                                    ( 0 )
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center text-red" colspan="4">No record found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var height = $(window).height();
            height = height - 300;
            $('.tableFixHead').css('height', height + 'px');
        });
    </script>
@endsection
