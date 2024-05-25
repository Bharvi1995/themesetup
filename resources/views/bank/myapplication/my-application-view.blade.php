@extends('layouts.bank.default')

@section('title')
    Application Detail
@endsection

@section('breadcrumbTitle')
    Application Detail
@endsection

@section('customeStyle')
    <link href="{{ storage_asset('themeAdmin/css/selectize.css') }}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .selectize-control.multi .selectize-input>div {
            cursor: pointer;
            background: #3D3D3D;
            color: #212529;
            border-radius: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-8 col-xxl-8">
            <div class="card border-card height-auto">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">My Application</h4>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <tbody>
                                <tr>
                                    <td><strong>Company Name</strong></td>
                                    <td>{{ $data->company_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Your Website URL</strong></td>
                                    <td>{{ $data->website_url }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Company Address</strong></td>
                                    <td>{{ $data->company_address }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Company Register Number / Year</strong></td>
                                    <td>{{ $data->company_registered_number_year }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Settlement Method for Crypto</strong></td>
                                    <td>{{ $data->settlement_method_for_crypto }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Settlement Method for Fiat</strong></td>
                                    <td>{{ $data->settlement_method_for_fiat }}</td>
                                </tr>
                                <tr>
                                    <td><strong>MCC Codes</strong></td>
                                    <td>{{ $data->mcc_codes }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Descriptors</strong></td>
                                    <td>{{ $data->descriptors }}</td>
                                </tr>
                                @foreach (json_decode($data->authorised_individual) as $key => $record)
                                    <tr>
                                        <td><strong>Authorised Individual {{ $key + 1 }}</strong></td>
                                        <td><strong>Name:</strong> {{ $record->name }}<br><strong>Phone Number:
                                            </strong>{{ $record->phone_number }}<br><strong>Email:
                                            </strong>{{ $record->email }}</td>
                                    </tr>
                                @endforeach
                                @if ($data->license_image != null)
                                    <tr>
                                        <td><strong>Licence Document</strong></td>
                                        <td>
                                            <a href="{{ getS3Url($data->license_image) }}" target="_blank"
                                                class="btn btn-primary btn-sm">View</a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-xxl-4">
            <div class="card border-card height-auto">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Status</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9 mt-50">
                            @if ($data->status == '0')
                                <i class="fa fa-circle text-info mr-1"></i>
                                Pending
                            @elseif($data->status == '1')
                                <i class="fa fa-circle text-success mr-1"></i>
                                Approved
                            @elseif($data->status == '2')
                                <i class="fa fa-circle text-danger mr-1"></i>
                                Rejected
                            @elseif($data->status == '3')
                                <i class="fa fa-circle text-info mr-1"></i>
                                Reassigned
                            @endif
                        </div>
                        <div class="col-md-3">
                            @if ($data->status == '0' || $data->status == '3')
                                <a href="{{ route('bank.my-application.edit') }}" class="btn btn-primary pull-right"
                                    title="Edit">Edit</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
