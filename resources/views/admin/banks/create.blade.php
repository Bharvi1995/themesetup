@extends('layouts.admin.default')

@section('title')
    Create Bank User
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> /<a href="{{ route('admin-user.index') }}">Admin Users</a> /
    Create
@endsection

@section('customeStyle')
    <link href="{{ storage_asset('themeAdmin/css/selectize.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Create Bank User</h4>
                    </div>
                    <a href="{{ route('banks.index') }}" class="btn btn-primary btn-sm rounded"> <i class="fa fa-arrow-left"
                            aria-hidden="true"></i></a>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => 'banks.store', 'method' => 'post', 'id' => 'admin-form', 'class' => 'form-dark']) !!}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Name</label>
                                <input class="form-control" name="bank_name" type="text" placeholder="Enter here..."
                                    value="{{ old('bank_name') }}">
                                @if ($errors->has('bank_name'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('bank_name') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Email</label>
                                <input class="form-control" name="email" type="email" placeholder="Enter here..."
                                    value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('email') }}
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Password</label>

                                <input class="form-control" name="password" type="password" placeholder="Enter here..."
                                    value="" autocomplete="off">
                                <small>The password must contain: One Upper, Lower, Numeric and Special Character. </small>

                                @if ($errors->has('password'))
                                    <br>
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('password') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Confirm Password</label>
                                <input class="form-control" name="password_confirmation" type="password"
                                    placeholder="Enter here...
                                " value=""
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="country">Country</label>
                                <div class="input-div">
                                    {!! Form::select('country', ['' => 'Select'] + getCountry(), null, [
                                        'class' => 'form-control select2',
                                        'id' => 'country',
                                        'data-width' => '100%',
                                    ]) !!}
                                </div>
                                @if ($errors->has('country'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('country') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="processing_country">Processing Country</label>
                                <div class="input-div">
                                    {!! Form::select(
                                        'processing_country[]',
                                        ['UK' => 'UK', 'EU' => 'EU', 'US/CANADA' => 'US/CANADA', 'Others' => 'Others'],
                                        isset($data->processing_country) ? json_decode($data->processing_country) : [],
                                        [
                                            'class' => 'form-control
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        select2',
                                            'multiple' => 'multiple',
                                        ],
                                    ) !!}
                                </div>
                                @if ($errors->has('processing_country'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('processing_country') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="category_id">Industry Type</label>
                                <div class="input-div">
                                    {!! Form::select('category_id[]', $category, isset($data->category_id) ? json_decode($data->category_id) : [], [
                                        'id' => 'category_id',
                                        'class' => 'form-control select2',
                                        'multiple' => 'multiple',
                                    ]) !!}
                                </div>
                                @if ($errors->has('category_id'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('category_id') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Change Status</label>
                                <div class="form-group mb-0">
                                    <label class="form-check-label mr-3"><input type="radio" id="rdo-1"
                                            name="is_active" value="1" class="form-check-input"
                                            @if (old('is_active') == '1' || old('is_active') == null) checked @endif> Active</label>
                                    <label class="form-check-label mr-3"><input type="radio" id="rdo-2"
                                            name="is_active" class="form-check-input" value="0"
                                            @if (old('is_active') == '0' || old('is_active') == null)  @endif>
                                        Inactive</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">OTP required for Login</label>
                                <div class="form-group mb-0">
                                    <label class="form-check-label mr-3"><input type="radio" id="rdo-3"
                                            name="is_otp_required" class="form-check-input" value="1"
                                            @if (old('is_otp_required') == '1' || old('is_otp_required') == null) checked @endif> Yes</label>
                                    <label class="form-check-label mr-3"><input type="radio" id="rdo-4"
                                            name="is_otp_required" class="form-check-input" value="0"
                                            @if (old('is_otp_required') == '0') checked @endif>
                                        No</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Extra Email<br>
                                    {!! Form::text('extra_email', null, [
                                        'placeholder' => 'Enter here',
                                        'class' => 'multi-select',
                                        'id' => 'input-tags',
                                        'multiple' => 'multiple',
                                    ]) !!}
                                    <small>Press <kbd class="btn-primary">Tab</kbd> after each email input and <kbd
                                            class="btn-primary">left/right arrow keys</kbd>
                                        to move the cursor between emails.</small></label>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-2">
                            <button type="submit" class="btn btn-primary ">Submit</button>

                            <a href="{{ route('banks.index') }}" class="btn btn-danger ">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
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

        $("#category_id").select2({
            placeholder: "Select"
        });
    </script>
@endsection
