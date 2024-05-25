@extends('layouts.admin.default')
@section('title')
    Show MID
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="route('mid-feature-management.index')">MID List</a> /
    Show
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Show MID</h4>
                    </div>
                    <a href="{{ route('mid-feature-management.index') }}" class="btn btn-primary btn-sm"><i
                            class="fa fa-arrow-left"></i></a>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <tr>
                                <td>
                                    <strong>MID No</strong>
                                    <p class="mb-0">{{ $data->mid_no }}</p>
                                </td>
                                <td>
                                    <strong>Bank Name</strong>
                                    <p class="mb-0">{{ $data->bank_name }}</p>
                                </td>
                                <td>
                                    <strong>Gateway</strong>
                                    <p class="mb-0">{{ $data->is_gateway_mid == 1 ? 'Yes' : 'No' }}</p>
                                </td>
                                <td>
                                    <strong>Gateway</strong>
                                    <p class="mb-0">{{ $gateway->title }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Sub Gateway</strong>
                                    <p class="mb-0">{{ $subgateways }}</p>
                                </td>
                                <td>
                                    <strong>Converted Currency</strong>
                                    <p class="mb-0">{{ $data->converted_currency }}</p>
                                </td>
                                <td>
                                    <strong>Refund</strong>
                                    <p class="mb-0">{{ $data->is_provide_refund == 1 ? 'Yes' : 'No' }}</p>
                                </td>
                                <td>
                                    <strong>Per Day Email Limit</strong>
                                    <p class="mb-0">{{ $data->per_day_email }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Per Day Card Limit</strong>
                                    <p class="mb-0">{{ $data->per_day_card }}</p>
                                </td>
                                <td>
                                    <strong>Per Week Email Limit</strong>
                                    <p class="mb-0">{{ $data->per_week_email }}</p>
                                </td>
                                <td>
                                    <strong>Per Week Card Limit</strong>
                                    <p class="mb-0">{{ $data->per_week_card }}</p>
                                </td>
                                <td>
                                    <strong>Per Month Email Limit</strong>
                                    <p class="mb-0">{{ $data->per_month_email }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Per Month Card Limit</strong>
                                    <p class="mb-0">{{ $data->per_month_card }}</p>
                                </td>
                                <td>
                                    <strong>Minimum Transaction amount</strong>
                                    <p class="mb-0">{{ $data->min_transaction_limit }}</p>
                                </td>
                                <td>
                                    <strong>Per Transaction Limit</strong>
                                    <p class="mb-0">{{ $data->per_transaction_limit }}</p>
                                </td>
                                <td>
                                    <strong>Per Day Limit</strong>
                                    <p class="mb-0">{{ $data->per_day_limit }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="max-width:500px;">
                                    <strong>Blocked Countries</strong>
                                    <p class="mb-0">{{ $blocked_country }}</p>
                                </td>
                                <td>
                                    <strong>MID Type</strong>
                                    @if ($data->mid_type == 1)
                                        <p class="mb-0"> Card</p>
                                    @elseif ($data->mid_type == 2)
                                        <p class="mb-0"> Bank</p>
                                    @elseif ($data->mid_type == 3)
                                        <p class="mb-0"> Crypto</p>
                                    @elseif ($data->mid_type == 4)
                                        <p class="mb-0"> UPI</p>
                                    @else
                                        <p class="mb-0"> Card</p>
                                    @endif
                                </td>
                                <td colspan="2">
                                    <strong>Descriptor</strong>
                                    <p class="mb-0">{{ $data->descriptor }}</p>
                                </td>

                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
