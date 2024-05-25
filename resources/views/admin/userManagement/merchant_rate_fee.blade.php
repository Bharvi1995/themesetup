@extends('layouts.admin.default')

@section('title')
    MID Management
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('users-management') }}">Merchant Management</a>
    / MID
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card  mt-1">
                <div class="card-body br-25">
                    <div class="row align-items-center">
                        <div class="col-xl-10 col-xxl-10 mr-auto">
                            <div class="d-sm-flex d-block align-items-center">
                                <i class="fa fa-key text-primary" style="font-size: 56px;"></i>
                                <div class="ms-2">
                                    <h4 class="fs-20">API Key</h4>
                                    @if (!isset($data->api_key))
                                        <a href="{{ route('api-key-generate', $data->id) }}"
                                            class="btn btn-success btn-sm">Generate API Key</a>
                                    @else
                                        <p class="fs-14 mb-0 text-danger">{{ $data->api_key }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-xxl-2 text-right">
                            {{-- <a href="{{ route('sendmailforlivemid', $data->id) }}" class="blue-btn me-2"><i class="fas fa-envelope me-2"></i>Send Mail For Live MID</a> --}}
                            <a href="{{ route('users-management') }}" class="btn btn-primary btn-sm"><i
                                    class="fa fa-arrow-left" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Company Name : {{ $data->company_name }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Nav tabs -->
                    <div class="custom-tab-1">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('personal-info', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> Personal Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('assign-mid', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> MID Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('card-email-limit', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> Card & Email Limit</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('merchant-rate-fee', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> Merchant Rate/Fee</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('additional-mail', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> Additional Mail Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('merchant-rules', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> Create Rules</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active">
                                <div class="pt-4">
                                    {!! Form::model($data, ['route' => 'assign-mid-store', 'method' => 'post', 'class' => 'form-dark']) !!}
                                    <input type="hidden" name="user_id" value="{{ $data->id }}">
                                    <input type="hidden" name="mid" value="{{ $data->mid }}">
                                    <div class="basic-form">
                                        <div class="row ">
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control"><b> Visa -</b> Merchant Discount
                                                        Rate (%)<span class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('merchant_discount_rate', Request::get('merchant_discount_rate'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('merchant_discount_rate'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('merchant_discount_rate') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control"><b> Master -</b> Merchant
                                                        Discount Rate (%)<span class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('merchant_discount_rate_master_card', Request::get('merchant_discount_rate_master_card'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('merchant_discount_rate_master_card'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('merchant_discount_rate_master_card') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control"><b> Amex -</b> Merchant Discount
                                                        Rate (%)<span class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('merchant_discount_rate_amex_card', Request::get('merchant_discount_rate_amex_card'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('merchant_discount_rate_amex_card'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('merchant_discount_rate_amex_card') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control"><b> Discover -</b> Merchant
                                                        Discount Rate (%)<span class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('merchant_discount_rate_discover_card', Request::get('merchant_discount_rate_discover_card'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('merchant_discount_rate_discover_card'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('merchant_discount_rate_discover_card') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control"><b> UPI -</b> Merchant
                                                        Discount Rate (%)<span class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('merchant_discount_rate_upi', Request::get('merchant_discount_rate_upi'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('merchant_discount_rate_upi'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('merchant_discount_rate_upi') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control"><b> Crypto -</b> Merchant
                                                        Discount Rate (%)<span class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('merchant_discount_rate_crypto', Request::get('merchant_discount_rate_crypto'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('merchant_discount_rate_crypto'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('merchant_discount_rate_crypto') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control"><b> Visa -</b> Setup Fee <span
                                                            class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('setup_fee', Request::get('setup_fee'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('setup_fee'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('setup_fee') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control"><b> Master -</b> Setup Fee <span
                                                            class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('setup_fee_master_card', Request::get('setup_fee_master_card'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('setup_fee_master_card'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('setup_fee_master_card') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control">Rolling Reserve (%) <span
                                                            class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('rolling_reserve_paercentage', Request::get('rolling_reserve_paercentage'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('rolling_reserve_paercentage'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('rolling_reserve_paercentage') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control">Transaction Fee <span
                                                            class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('transaction_fee', Request::get('transaction_fee'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('transaction_fee'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('transaction_fee') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control">Refund Fee <span
                                                            class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('refund_fee', Request::get('refund_fee'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('refund_fee'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('refund_fee') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control">Chargeback Fee <span
                                                            class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('chargeback_fee', Request::get('chargeback_fee'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('chargeback_fee'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('chargeback_fee') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control">Suspicious Transaction Fee <span
                                                            class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('flagged_fee', Request::get('flagged_fee'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('flagged_fee'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('flagged_fee') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control">Retrieval Fee <span
                                                            class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('retrieval_fee', Request::get('retrieval_fee'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('retrieval_fee'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('retrieval_fee') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control">Threshold <span
                                                            class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('threshold_amount', Request::get('threshold_amount'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('threshold_amount'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('threshold_amount') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-12 label-control">Settlement Fee <span
                                                            class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        {!! Form::text('settlement_fee', Request::get('settlement_fee'), [
                                                            'placeholder' => 'Enter here',
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                        @if ($errors->has('settlement_fee'))
                                                            <span class="text-danger help-block form-error">
                                                                <strong>{{ $errors->first('settlement_fee') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-2">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                <a href="{{ url('admin/user-management') }}"
                                                    class="btn btn-danger">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
