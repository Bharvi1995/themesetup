@extends('layouts.admin.default')
@section('title')
    Gateway Create
@endsection
@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.gateway.index') }}">Gateway List</a> /
    Create
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Create Gateway</h4>
                    </div>
                    <a href="{{ route('admin.gateway.index') }}" class="btn btn-primary btn-sm"><i
                            class="fa fa-arrow-left"></i></a>
                </div>
                <div class="card-body">
                    {!! Form::open([
                        'route' => 'admin.gateway.store',
                        'method' => 'POST',
                        'class' => 'form form-horizontal form-dark',
                        'enctype' => 'multipart/form-data',
                        'id' => 'gateway-form',
                    ]) !!}
                    <div class="row">
                        <div class="form-group col-lg-12">
                            {!! Form::text('title', Input::get('title'), ['placeholder' => 'Enter here...', 'class' => 'form-control']) !!}
                            @if ($errors->has('title'))
                                <span class="text-danger help-block form-error">
                                    <span>{{ $errors->first('title') }}</span>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-12 form-group">
                            <div class="table-responsive custom-table">
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Credential Title</th>
                                            <th>Credential Name</th>
                                            <th>Credential Type</th>
                                            <th>Is Required</th>
                                            <th>Add More</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tab_logic">
                                        <div id="countVar" data-count="0"></div>
                                        <tr data-id="1">
                                            <td>
                                                1
                                            </td>
                                            <td>
                                                <input placeholder="Enter here..." class="form-control"
                                                    name="credential_title[0]" type="text"
                                                    value="{{ old('credential_title.0') }}">
                                                @if ($errors->has('credential_title.0'))
                                                    <span
                                                        class="text-danger">{{ $errors->first('credential_title.0') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <input placeholder="Enter here..." class="form-control" name="name[0]"
                                                    type="text" value="{{ old('name.0') }}">
                                                @if ($errors->has('name.0'))
                                                    <span class="text-danger">{{ $errors->first('name.0') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <select class="form-control" name="type[0]" data-size="7" data-width="100%"
                                                    required>
                                                    <option disabled>Select Type</option>
                                                    <option value="string" selected
                                                        {{ old('type[0]') == 'string' ? 'selected' : '' }}>String</option>
                                                    <option value="text" selected
                                                        {{ old('type[0]') == 'text' ? 'selected' : '' }}>Text</option>
                                                    <option value="boolean"
                                                        {{ old('type[0]') == 'boolean' ? 'selected' : '' }}>
                                                        Yes / No</option>
                                                </select>
                                                @if ($errors->has('type.0'))
                                                    <span class="text-danger">{{ $errors->first('type.0') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <select class="form-control " name="is_required[0]" data-width="100%">
                                                    <option disabled>Is Required</option>
                                                    <option value="0" selected
                                                        {{ old('is_required[0]') == '0' ? 'selected' : '' }}>No</option>
                                                    <option value="1"
                                                        {{ old('is_required[0]') == '1' ? 'selected' : '' }}>Yes
                                                    </option>
                                                </select>
                                                @if ($errors->has('is_required[0]'))
                                                    <span class="text-danger">{{ $errors->first('is_required[0]') }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-success btn-sm plus"> <i
                                                        class="fa fa-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group col-lg-12">
                            <h4 class="mt-2 mb-2">Required Fields</h4>
                            <div class="basic-form">
                                <div class="form-group row">
                                    @foreach ($required_fields as $field_key => $field_value)
                                        <div class="col-md-3">
                                            <div
                                                class="custom-control form-check custom-checkbox custom-control-inline mr-0">
                                                <input class="form-check-input" id="{{ $field_value->id }}"
                                                    name="required_fields[]" type="checkbox"
                                                    value="{{ $field_value->field }}" checked="checked">
                                                <label class="form-check-label" for="{{ $field_value->id }}">
                                                    {{ $field_value->field_title }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('admin.gateway.index') }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script type="text/javascript">
        $('body').on('click', '.plus', function() {
            // i = $('#tab_logic tr').length; 
            var i = $('#tab_logic tr:last').data('id');
            i = i + 1;
            $('#tab_logic').append('<tr data-id="' + i + '">\
                                                    <td>' + i +
                '</td>\
                                                    <td>\
                                                        <input placeholder="Enter here..." class="form-control" name="credential_title[' +
                i +
                ']" type="text">\
                                                    </td>\
                                                    <td>\
                                                        <input placeholder="Enter here..." class="form-control" name="name[' +
                i + ']" type="text">\
                                                    </td>\
                                                    <td>\
                                                        <select class="form-control select2" name="type[' + i + ']">\
                                                            <option selected disabled>Select Type</option>\
                                                            <option value="string">String</option>\
                                                             <option value="text">Text</option>\
                                                            <option value="boolean">Yes / No</option>\
                                                        </select>\
                                                    </td>\
                                                    <td>\
                                                        <select class="form-control select2" name="is_required[' + i + ']">\
                                                            <option selected disabled>Is Required</option>\
                                                            <option value="0">No</option>\
                                                            <option value="1">Yes</option>\
                                                        </select>\
                                                    </td>\
                                                    <td class="text-center">\
                                                        <button type="button" class="btn btn-success btn-sm plus"> <i class="fa fa-plus"></i> </button>\
                                                        <button type="button" class="btn btn-primary btn-sm minus"> <i class="fa fa-minus"></i> </button>\
                                                    </td>\
                                                </tr>');
            // i++;
        });
        $('body').on('click', '.minus', function() {
            $(this).closest('tr').remove();
            // i--;
        });

        $('#gateway-form').submit(function() {
            $(this).find('input:text').each(function() {
                $(this).val($.trim($(this).val()));
            });
        });
    </script>
@endsection
