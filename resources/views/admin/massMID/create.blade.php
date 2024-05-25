@extends('layouts.admin.default')
@section('title')
    Mass MID
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.technical') }}">Technical &
        Additional</a> / <a href="{{ route('mass-mid.index') }}">Mass MID List</a> / Change
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Change Mass MID</h4>
                    </div>
                    <a href="{{ route('mass-mid.index') }}" class="btn btn-primary btn-sm"><i
                            class="fa fa-arrow-left"></i></a>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => 'mass-mid.store', 'method' => 'post', 'class'=>'form-dark','id' => 'mass-mid-form']) !!}
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <div id="validation-errors"></div>
                            <label for="change-type">Select MID Type <span class="text-danger">* <small>Which type of mid
                                        want to change</small></span></label>
                            <select class="select2 form-control form-control-lg merchant-dropdown" name="change_type"
                                id="change-type">
                                <option selected disabled>Select one</option>
                                @foreach ($midtypes as $key => $value)
                                    <option value="{{ $key }}" {{ old('change_type') == $key ? 'selected' : '' }}>
                                        {{ $value }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('change_type'))
                                <span class="text-danger">
                                    <strong>{{ $errors->first('change_type') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="old-mid">Select Current MID <span class="text-danger">* <small>Old MID which will
                                        be replaced</small></span></label>
                            <select class="select2 merchant-dropdown" name="old_mid" id="old-mid" data-size="7"
                                data-live-search="true" data-title="Select MID" data-width="100%">
                                <option selected disabled>Select here</option>
                                @foreach ($midData as $key => $value)
                                    <option value="{{ $value->id }}"
                                        {{ old('old_mid') == $value->id ? 'selected' : '' }}>{{ $value->bank_name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('old_mid'))
                                <span class="text-danger">
                                    <strong>{{ $errors->first('old_mid') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="new-mid">Select new MID <span class="text-danger">* <small>New MID which will
                                        replace</small></span></label>
                            <select class="select2" name="new_mid" id="new-mid" data-size="7" data-live-search="true"
                                data-title="Select MID" data-width="100%">
                                <option selected disabled>Select here</option>
                                @foreach ($midData as $key => $value)
                                    <option value="{{ $value->id }}"
                                        {{ old('new_mid') == $value->id ? 'selected' : '' }}>{{ $value->bank_name }}
                                    </option>
                                @endforeach
                                <option value="0" {{ old('new_mid') == '0' ? 'selected' : '' }}>Remove MID</option>
                            </select>
                            @if ($errors->has('new_mid'))
                                <span class="text-danger">
                                    <strong>{{ $errors->first('new_mid') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-12 mt-1">
                            <button type="button" id="mass-mid-confirm" class="btn btn-success">Submit</button>
                        </div>
                        <div class="form-group col-lg-12" id="merchant-list">
                            @if ($errors->has('user_id'))
                                <span class="text-danger">
                                    <strong>{{ $errors->first('user_id') }}</strong>
                                </span>
                            @endif
                            {{-- here comes merchant list --}}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    {{-- confirm modal --}}
    <div class="modal right fade" id="mass-mid-modal" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg modal-lg modal-dialog modal-lg-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirm </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <div class="modal-body" id="mass-mid-body">
                    <h3 id="mass-mid-message"></h3>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirm-mass-submit" class="btn btn-primary btn-sm">Confirm</button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script type="text/javascript">
        $(document).ready(function() {

            // get merchant list on select
            $(document).on('change', '.merchant-dropdown', function(e) {
                e.preventDefault();

                var changeType = $('#change-type').val();
                var old_mid = $('#old-mid').val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                // if anyone is selected
                if (changeType == null || old_mid == null) {
                    if (changeType == null) {
                        toastr.error('Please select MID Type.');
                    }
                    return false;
                }
                $.ajax({
                    url: '{{ route('mass-mid.getMerchants') }}',
                    type: 'post',
                    data: {
                        old_mid: old_mid,
                        change_type: changeType
                    },
                    beforeSend: function() {
                        $('#merchant-list').html('');
                        $('#validation-errors').html('');
                        toastr.clear();
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            toastr.success(data.message);
                            $('#merchant-list').html(data.html);
                        } else {
                            if (data.errors) {
                                $.each(data.errors, function(key, value) {
                                    $('#validation-errors').append(
                                        '<div class="alert alert-danger">' + value +
                                        '</div');
                                });
                            } else {
                                $('#validation-errors').append(
                                    '<div class="alert alert-danger">' + data.message +
                                    '</div');

                            }
                            toastr.error(data.message);
                        }
                    },
                    fail: function(err) {
                        toastr.error(err);
                    },
                    error: function(jqXHR, exception) {
                        $('#validation-errors').html('');
                        $('#validation-errors').append(
                            '<div class="alert alert-danger">something went wrong, please try again</div'
                        );
                        toastr.error('something went wrong, please try again');
                    }
                });
            });

            $('body').on('change', '#checkAll', function() {
                if ($(this).prop("checked") == true) {
                    $('.multiselect').prop("checked", true);
                } else if ($(this).prop("checked") == false) {
                    $('.multiselect').prop("checked", false);
                }
            });

            // confirm
            $(document).on('click', '#mass-mid-confirm', function(e) {
                e.preventDefault();

                var data = $('#mass-mid-form').serialize();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route('mass-mid.createConfirm') }}',
                    type: 'post',
                    data: data,
                    beforeSend: function() {
                        $('#validation-errors').html('');
                        toastr.clear();
                    },
                    success: function(data) {
                        $('#validation-errors').html('');
                        if (data.status == 'success') {
                            $('#mass-mid-modal').modal('show');
                            $('#mass-mid-message').text(data.message);
                        } else {
                            if (data.errors) {
                                $.each(data.errors, function(key, value) {
                                    $('#validation-errors').append(
                                        '<div class="alert alert-danger">' + value +
                                        '</div');
                                });
                            } else {
                                $('#validation-errors').append(
                                    '<div class="alert alert-danger">' + data.message +
                                    '</div');

                            }
                            toastr.error(data.message);
                        }
                    },
                    fail: function(err) {
                        toastr.error(err);
                    },
                    error: function(jqXHR, exception) {
                        $('#validation-errors').html('');
                        $('#validation-errors').append(
                            '<div class="alert alert-danger">something went wrong, please try again</div'
                        );
                        toastr.error('something went wrong, please try again');
                    }
                });
            });

            // confirm
            $(document).on('click', '#confirm-mass-submit', function() {
                $('#mass-mid-form').submit();
            });
        });
    </script>
@endsection
