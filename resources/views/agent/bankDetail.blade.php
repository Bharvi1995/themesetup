@extends($agentUserTheme)
@section('title')
    Bank Details
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('dashboardPage') }}">Dashboard</a> / Bank Details
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="{{ route('agent.bank.details.store') }}" class="form-dark">
                @csrf
                <div class="card">
                    <div class="card-header">

                        <h4 class="card-title">Bank Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 form-group">
                                <label>Bank Name </label>
                                <input type="text" class="form-control" placeholder="Enter here.." name="name"
                                    value="{{ $bank ? $bank->name : old('name') }}" />
                            </div>
                            <div class="col-lg-6 form-group">
                                <label>Bank Address </label>
                                <textarea class="form-control" placeholder="Enter here.." name="address">{{ $bank ? $bank->address : old('address') }}</textarea>
                            </div>
                            <div class="col-lg-6 form-group">
                                <label>ABA Routing #(US Banks)</label>
                                <input type="text" class="form-control" placeholder="Enter here.." name="aba_routing"
                                    value="{{ $bank ? $bank->aba_routing : old('aba_routing') }}" />
                            </div>

                            <div class="col-lg-6 form-group">
                                <label>SWIFT Code and/or BIC </label>
                                <input type="text" class="form-control" placeholder="Enter here.." name="swift_code"
                                    value="{{ $bank ? $bank->swift_code : old('swift_code') }}" />
                            </div>
                            <div class="col-lg-6 form-group">
                                <label>IBAN </label>
                                <input type="text" class="form-control" placeholder="Enter here.." name="iban"
                                    value="{{ $bank ? $bank->iban : old('iban') }}" />
                            </div>
                            <div class="col-lg-6 form-group">
                                <label>Account Name (Not Payee Name) </label>
                                <input type="text" class="form-control" placeholder="Enter here.." name="account_name"
                                    value="{{ $bank ? $bank->account_name : old('account_name') }}" />
                            </div>
                            <div class="col-lg-6 form-group">
                                <label>Account Number </label>
                                <input type="number" class="form-control" placeholder="Enter here.." name="account_number"
                                    value="{{ $bank ? $bank->account_number : old('account_number') }}" />
                            </div>
                            <div class="col-lg-6 form-group">
                                <label>Wallet Detail</label>
                                <div class="input-group mb-3">
                                    <select class="select2" name="wallet" id="wallet" style="width: 300px;">
                                        <option value="">-Wallet Type-</option>
                                        @foreach (getwallet() as $key => $wallet)
                                            <option value="{{ $wallet->id }}"
                                                {{ $bank && $wallet->id == $bank->wallet ? 'selected' : '' }}>
                                                {{ $wallet->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control col-md-8" placeholder="Wallet ID"
                                        name="wallet_id" value="{{ $bank ? $bank->wallet_id : old('wallet_id') }}" />
                                </div>
                            </div>

                            <div class="col-lg-6 form-group">
                                <label>Account Holder's Address </label>
                                <textarea class="form-control" placeholder="Enter here.." name="account_holder_address">{{ $bank ? $bank->account_holder_address : old('account_holder_address') }}</textarea>
                            </div>
                            <div class="col-lg-6 form-group">
                                <label>Additional Information </label>
                                <textarea class="form-control" placeholder="Enter here.." name="additional_information">{{ $bank ? $bank->additional_information : old('additional_information') }}</textarea>
                            </div>
                            <div class="col-lg-12 form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('dashboardPage') }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
