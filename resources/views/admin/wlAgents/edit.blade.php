@extends('layouts.admin.default')
@section('title')
    Edit White Label RP
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('wl-agents.index') }}">White Label RP</a> /
    Edit
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Edit White Label RP</h4>
                    </div>
                    <a href="{{ route('wl-agents.index') }}" class="btn btn-primary btn-sm rounded"> <i
                            class="fa fa-arrow-left" aria-hidden="true"></i></a>
                </div>
                <div class="card-body">
                    {{ Form::model($data, ['route' => ['wl-agents.update', $data->id], 'method' => 'patch', 'id' => 'agent-form', 'class' => 'form-dark']) }}
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="">Name</label>
                            <input class="form-control" name="name" type="text" placeholder="Enter here..."
                                value="{{ $data->name }}">
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Email</label>
                            <input class="form-control" name="email" type="email" placeholder="Enter here..."
                                value="{{ $data->email }}">
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Enter here...">
                            @if ($errors->has('password'))
                                <span class="help-block font-red-mint text-danger">
                                    <span>{{ $errors->first('password') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control" placeholder="Enter here...">
                            @if ($errors->has('password_confirmation'))
                                <span class="help-block font-red-mint text-danger">
                                    <span>{{ $errors->first('password_confirmation') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-6 form-group">
                            <label for="">OTP required for Login</label>
                            <div class="form-group mb-0">
                                <label class="form-check-label mr-3"><input type="radio" id="rdo-1"
                                        name="is_otp_required" value="1" class="form-check-input"
                                        {{ $data->is_otp_required == 1 ? 'checked' : '' }}>
                                    Yes</label>
                                <label class="form-check-label mr-3"><input type="radio" id="rdo-2"
                                        name="is_otp_required" class="form-check-input" value="0"
                                        {{ $data->is_otp_required == 0 ? 'checked' : '' }}>
                                    No</label>
                            </div>
                            @if ($errors->has('is_otp_required'))
                                <span class="help-block">
                                    <span class="text-danger">{{ $errors->first('is_otp_required') }}</span>
                                </span>
                            @endif
                        </div>

                        <div class="col-md-12">
                            <hr>
                            <h5> White Label RP Rate/Fee</h5>
                            <hr>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-12 label-control"><b> Visa -</b> Discount Rate (%)<span
                                        class="text-danger">*</span></label>
                                <div class="col-md-12">
                                    {!! Form::number('discount_rate', $data->discount_rate, [
                                        'placeholder' => 'Enter here',
                                        'class' => 'form-control',
                                        'step' => '0.1',
                                    ]) !!}
                                    @if ($errors->has('discount_rate'))
                                        <span class="help-block">
                                            <span class="text-danger">{{ $errors->first('discount_rate') }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-12 label-control"><b> Master -</b> Discount Rate (%)<span
                                        class="text-danger">*</span></label>
                                <div class="col-md-12">
                                    {!! Form::number('discount_rate_master_card', $data->discount_rate_master_card, [
                                        'placeholder' => 'Enter here',
                                        'class' => 'form-control',
                                        'step' => '0.1',
                                    ]) !!}
                                    @if ($errors->has('discount_rate_master_card'))
                                        <span class="help-block">
                                            <span
                                                class="text-danger">{{ $errors->first('discount_rate_master_card') }}</span>
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
                                    {!! Form::number('setup_fee', $data->setup_fee, [
                                        'placeholder' => 'Enter here',
                                        'class' => 'form-control',
                                        'step' => '0.1',
                                    ]) !!}
                                    @if ($errors->has('setup_fee'))
                                        <span class="help-block">
                                            <span class="text-danger">{{ $errors->first('setup_fee') }}</span>
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
                                    {!! Form::number('setup_fee_master_card', $data->setup_fee_master_card, [
                                        'placeholder' => 'Enter here',
                                        'class' => 'form-control',
                                        'step' => '0.1',
                                    ]) !!}
                                    @if ($errors->has('setup_fee_master_card'))
                                        <span class="help-block">
                                            <span class="text-danger">{{ $errors->first('setup_fee_master_card') }}</span>
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
                                    {!! Form::number('rolling_reserve_paercentage', $data->rolling_reserve_paercentage, [
                                        'placeholder' => 'Enter here',
                                        'class' => 'form-control',
                                        'step' => '0.1',
                                    ]) !!}
                                    @if ($errors->has('rolling_reserve_paercentage'))
                                        <span class="help-block">
                                            <span
                                                class="text-danger">{{ $errors->first('rolling_reserve_paercentage') }}</span>
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
                                    {!! Form::number('transaction_fee', $data->transaction_fee, [
                                        'placeholder' => 'Enter here',
                                        'class' => 'form-control',
                                        'step' => '0.1',
                                    ]) !!}
                                    @if ($errors->has('transaction_fee'))
                                        <span class="help-block">
                                            <span class="text-danger">{{ $errors->first('transaction_fee') }}</span>
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
                                    {!! Form::number('refund_fee', $data->refund_fee, [
                                        'placeholder' => 'Enter here',
                                        'class' => 'form-control',
                                        'step' => '0.1',
                                    ]) !!}
                                    @if ($errors->has('refund_fee'))
                                        <span class="help-block">
                                            <span class="text-danger">{{ $errors->first('refund_fee') }}</span>
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
                                    {!! Form::number('chargeback_fee', $data->chargeback_fee, [
                                        'placeholder' => 'Enter here',
                                        'class' => 'form-control',
                                        'step' => '0.1',
                                    ]) !!}
                                    @if ($errors->has('chargeback_fee'))
                                        <span class="help-block">
                                            <span class="text-danger">{{ $errors->first('chargeback_fee') }}</span>
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
                                    {!! Form::number('flagged_fee', $data->flagged_fee, [
                                        'placeholder' => 'Enter here',
                                        'class' => 'form-control',
                                        'step' => '0.1',
                                    ]) !!}
                                    @if ($errors->has('flagged_fee'))
                                        <span class="help-block">
                                            <span class="text-danger">{{ $errors->first('flagged_fee') }}</span>
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
                                    {!! Form::number('retrieval_fee', $data->retrieval_fee, [
                                        'placeholder' => 'Enter here',
                                        'class' => 'form-control',
                                        'step' => '0.1',
                                    ]) !!}
                                    @if ($errors->has('retrieval_fee'))
                                        <span class="help-block">
                                            <span class="text-danger">{{ $errors->first('retrieval_fee') }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 form-group mt-2">
                            <button type="submit" class="btn btn-primary ">Submit</button>
                            <a href="{{ url('paylaksa/wl-agents') }}" class="btn btn-danger "></i>Cancel</a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
