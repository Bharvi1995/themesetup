@extends('layouts.admin.default')

@section('title')
    MID Management
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('users-management') }}">Merchant Management</a>
    / MID
@endsection

@section('content')
    <style type="text/css">
        .nav-tabs .nav-item {
            margin-bottom: 0px;
        }
    </style>
    <div class="row">
        <div class="col-xl-12">
            <div class="card  mt-1">
                <div class="card-body  br-25">
                    <div class="row align-items-center">
                        <div class="col-xl-10 col-xxl-10">
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
                                <a class="nav-link active" href="{{ route('card-email-limit', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> Card & Email Limit</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('merchant-rate-fee', $data->id) }}"><i
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
                                    {!! Form::model($data, ['route' => 'assign-mid-store-merchant', 'method' => 'post', 'class' => 'form-dark']) !!}
                                    <input type="hidden" name="user_id" value="{{ $data->id }}">
                                    <div class="basic-form">
                                        <div class="row ">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>One Day Card Limit <span class="text-danger">*</span></label>
                                                    {!! Form::number('one_day_card_limit', Request::get('one_day_card_limit'), [
                                                        'placeholder' => 'Enter here',
                                                        'class' => 'form-control',
                                                    ]) !!}
                                                    @if ($errors->has('one_day_card_limit'))
                                                        <span class="text-danger help-block form-error">
                                                            <strong>{{ $errors->first('one_day_card_limit') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>One Day Email Limit <span class="text-danger">*</span></label>
                                                    {!! Form::number('one_day_email_limit', Request::get('one_day_email_limit'), [
                                                        'placeholder' => 'Enter here',
                                                        'class' => 'form-control',
                                                    ]) !!}
                                                    @if ($errors->has('one_day_email_limit'))
                                                        <span class="text-danger help-block form-error">
                                                            <strong>{{ $errors->first('one_day_email_limit') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>One Week Card Limit <span class="text-danger">*</span></label>
                                                    {!! Form::number('one_week_card_limit', Request::get('one_week_card_limit'), [
                                                        'placeholder' => 'Enter here',
                                                        'class' => 'form-control',
                                                    ]) !!}
                                                    @if ($errors->has('one_week_card_limit'))
                                                        <span class="text-danger help-block form-error">
                                                            <strong>{{ $errors->first('one_week_card_limit') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>One Week Email Limit <span class="text-danger">*</span></label>
                                                    {!! Form::number('one_week_email_limit', Request::get('one_week_email_limit'), [
                                                        'placeholder' => 'Enter here',
                                                        'class' => 'form-control',
                                                    ]) !!}
                                                    @if ($errors->has('one_week_email_limit'))
                                                        <span class="text-danger help-block form-error">
                                                            <strong>{{ $errors->first('one_week_email_limit') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>One Month Card Limit <span class="text-danger">*</span></label>
                                                    {!! Form::number('one_month_card_limit', Request::get('one_month_card_limit'), [
                                                        'placeholder' => 'Enter here',
                                                        'class' => 'form-control',
                                                    ]) !!}
                                                    @if ($errors->has('one_month_card_limit'))
                                                        <span class="text-danger help-block form-error">
                                                            <strong>{{ $errors->first('one_month_card_limit') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>One Month Email Limit <span class="text-danger">*</span></label>
                                                    {!! Form::number('one_month_email_limit', Request::get('one_month_email_limit'), [
                                                        'placeholder' => 'Enter here',
                                                        'class' => 'form-control',
                                                    ]) !!}
                                                    @if ($errors->has('one_month_email_limit'))
                                                        <span class="text-danger help-block form-error">
                                                            <strong>{{ $errors->first('one_month_email_limit') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Daily Card Decline Limit <span class="text-danger">*</span></label>
                                                    {!! Form::number('daily_card_decline_limit', Request::get('daily_card_decline_limit'), [
                                                        'placeholder' => 'Enter here',
                                                        'class' => 'form-control',
                                                    ]) !!}
                                                    @if ($errors->has('daily_card_decline_limit'))
                                                        <span class="text-danger help-block form-error">
                                                            <strong>{{ $errors->first('daily_card_decline_limit') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6"></div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Per Transaction Limit <span class="text-danger">*</span></label>
                                                    {!! Form::number('per_transaction_limit', Request::get('per_transaction_limit'), [
                                                        'placeholder' => 'Enter here',
                                                        'class' => 'form-control',
                                                    ]) !!}
                                                    @if ($errors->has('per_transaction_limit'))
                                                        <span class="text-danger help-block form-error">
                                                            <strong>{{ $errors->first('per_transaction_limit') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Currency <span class="text-danger">*</span></label>
                                                    {!! Form::select(
                                                        'currency',
                                                        [null => 'Select here'] + config('currency.three_letter'),
                                                        Request::get('currency'),
                                                        ['class' => 'form-control select2'],
                                                    ) !!}
                                                    @if ($errors->has('currency'))
                                                        <span class="text-danger help-block form-error">
                                                            <strong>{{ $errors->first('currency') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <button type="submit" class="btn btn-primary me-1">Submit</button>
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
