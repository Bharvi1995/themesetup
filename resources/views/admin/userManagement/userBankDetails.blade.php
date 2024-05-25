@extends('layouts.admin.default')

@section('title')
    Merchant Bank Details
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Merchant Bank Details
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Merchant Bank Details</h4>
                    </div>
                    <div class="btn-group me-2">
                        <a href="{{ route('users-management') }}" class="me-2 btn btn-primary btn-sm"><i
                                class="fa fa-arrow-left"></i> </a>
                    </div>


                </div>
                <div class="card-body">
                    @if ($bankDetails)
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Bank Name
                                <span>{{ $bankDetails->name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Bank Address
                                <span>{{ $bankDetails->address }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ABA Routing #(US Banks)
                                <span>{{ $bankDetails->aba_routing }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                SWIFT Code and/or BIC
                                <span>{{ $bankDetails->swift_code }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                IBAN
                                <span>{{ $bankDetails->iban }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Account Name (Not Payee Name)
                                <span>{{ $bankDetails->account_name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Account Number
                                <span>{{ $bankDetails->account_number }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Account Holder's Address
                                <span>{{ $bankDetails->account_holder_address }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Wallet Type
                                <span>{{ $bankDetails->walletDetail ? $bankDetails->walletDetail->name : '' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Wallet ID
                                <span>{{ $bankDetails->walletDetail ? $bankDetails->wallet_id : '' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Additional Information
                                <span>{{ $bankDetails->additional_information ? $bankDetails->additional_information : 'N/A' }}</span>
                            </li>
                        </ul>
                    @else
                        <div class="text-center">
                            No Record
                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>
@endsection
