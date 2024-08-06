@extends('layouts.admin.default')

@section('title')
    MID Management
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('users-management') }}">Merchant Management</a>
    / MID
@endsection

@section('customeStyle')
    <link href="{{ storage_asset('themeAdmin/css/selectize.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card  mt-1">
                <div class="card-body br-25">
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
                                <a class="nav-link" href="{{ route('card-email-limit', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> Card & Email Limit</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('merchant-rate-fee', $data->id) }}"><i
                                        class="fa fa-hand-o-right me-2"></i> Merchant Rate/Fee</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('additional-mail', $data->id) }}"><i
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
                                                <div class="form-group">
                                                    <label>Merchant mail</label>
                                                    <select class="form-control select2"
                                                        name="merchant_transaction_notification">
                                                        <option value="1"
                                                            {{ $data->merchant_transaction_notification == '1' ? 'selected' : '' }}>
                                                            Yes</option>
                                                        <option value="0"
                                                            {{ $data->merchant_transaction_notification == '0' ? 'selected' : '' }}>
                                                            No</option>
                                                    </select>
                                                    @if ($errors->has('merchant_transaction_notification'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('merchant_transaction_notification') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Client mail</label>
                                                    <select class="form-control select2"
                                                        name="user_transaction_notification">
                                                        <option value="1"
                                                            {{ $data->user_transaction_notification == '1' ? 'selected' : '' }}>
                                                            Yes</option>
                                                        <option value="0"
                                                            {{ $data->user_transaction_notification == '0' ? 'selected' : '' }}>
                                                            No</option>
                                                    </select>
                                                    @if ($errors->has('user_transaction_notification'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('user_transaction_notification') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Additional mail notification <br>
                                                        <small>Press <kbd class="badge-danger">Tab</kbd> after each email
                                                            input and
                                                            <kbd class="badge-danger">left/right arrow keys</kbd> to move
                                                            the cursor between
                                                            emails.</small></label>
                                                    {!! Form::text('additional_merchant_transaction_notification', $additional_merchant_transaction_notification, [
                                                        'placeholder' => 'Enter here',
                                                        'class' => 'multi-select',
                                                        'id' => 'input-tags',
                                                        'multiple' => 'multiple',
                                                    ]) !!}
                                                    @if ($errors->has('additional_merchant_transaction_notification'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('additional_merchant_transaction_notification') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Additional mail notification for compliance (chargeback, refund
                                                        and suspicious)</label><br>
                                                    {!! Form::text('additional_mail', Request::get('one_day_card_limit'), [
                                                        'placeholder' => 'Enter here',
                                                        'class' => 'form-control',
                                                        'id' => 'additional-mail',
                                                    ]) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <button type="submit" class="btn btn-primary ">Submit</button>
                                                <a href="{{ url('paylaksa/user-management') }}"
                                                    class="btn btn-danger ">Cancel</a>
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

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/js/selectize.min.js') }}"></script>
    <script type="text/javascript">
        $('#input-tags').selectize({
            delimiter: ',',
            persist: false,
            create: function(input) {
                return {
                    value: input,
                    text: input
                }
            }
        });
    </script>
@endsection
