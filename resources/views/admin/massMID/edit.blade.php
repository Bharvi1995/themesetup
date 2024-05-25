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
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Change Mass MID</h4>
                    </div>
                    <a href="{{ route('mass-mid.index') }}" class="btn btn-primary btn-sm"><i
                            class="fa fa-arrow-left"></i></a>
                </div>
                <div class="card-body">
                    {!! Form::model($mass_mid, [
                        'route' => ['mass-mid.update', $mass_mid->id],
                        'method' => 'patch',
                        'id' => 'mass-mid-form',
                    ]) !!}
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <div id="validation-errors"></div>
                            <label for="change-type">Select MID Type <span class="text-danger">* <small>Which type of mid
                                        want to change</small></span></label>
                            <select class="select2 form-control form-control-lg" name="change_type" id="change-type"
                                disabled>
                                @foreach ($midtypes as $key => $value)
                                    @if ($mass_mid->change_type == $key)
                                        <option value="{{ $key }}" selected>{{ $value }}</option>
                                    @endif
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
                            <label id="old-mid">Select Current MID <span class="text-danger">* <small>Old MID which will
                                        be replaced</small></span></label>
                            <select class="select2" name="old_mid" id="old-mid" data-size="7" data-live-search="true"
                                disabled data-title="Select MID" data-width="100%">
                                @foreach ($midData as $key => $value)
                                    @if ($mass_mid->old_mid == $value->id)
                                        <option value="{{ $value->id }}" selected>{{ $value->bank_name }}</option>
                                    @endif
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
                                        {{ $mass_mid->new_mid == $value->id ? 'selected' : '' }}
                                        {{ $mass_mid->old_mid == $value->id ? 'disabled' : '' }}>{{ $value->bank_name }}
                                    </option>
                                @endforeach
                                <option value="0" {{ $mass_mid->new_mid == '0' ? 'selected' : '' }}>Remove MID
                                </option>
                            </select>
                            @if ($errors->has('new_mid'))
                                <span class="text-danger">
                                    <strong>{{ $errors->first('new_mid') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-lg-12">
                            <button type="button" id="mass-mid-confirm" class="btn btn-success btn-sm">Submit</button>
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
            $(document).on('click', '#mass-mid-confirm', function(e) {
                e.preventDefault();

                var data = {
                    new_mid: $('#new-mid').val(),
                    id: {{ $mass_mid->id }},
                    change_type: {{ $mass_mid->change_type }},
                    old_mid: {{ $mass_mid->old_mid }},
                };

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route('mass-mid.updateConfirm') }}',
                    type: 'post',
                    data: data,
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
