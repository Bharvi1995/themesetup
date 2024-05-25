@extends($WLAgentUserTheme)

@section('title')
    Merchant Create
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('wl-dashboard') }}">Dashboard</a> / <a href="{{ route('wl-rp-whitelist-ip') }}"> IP Whitelist </a> / Add
@endsection

@section('content')
    <style type="text/css">
        .main-select-phone .select2 {
            width: 35% !important;
        }

        .main-select-phone input {
            width: 60% !important;
        }
    </style>
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <div class="mr-auto pr-3">
                        <h4 class="card-title">Add IP</h4>
                    </div>
                    <a href="{{ route('wl-rp-whitelist-ip') }}" class="btn btn-danger rounded d-none d-md-block btn-xxs"><i
                            class="fa fa-arrow-left"></i></a>
                </div>
                {!! Form::open(['route' => 'wl-rp-whitelist-ip-add-submit', 'files' => true]) !!}
                <div class="card-body">
                    <div class="basic-form">
                        <div class="to-left-serach table-responsive">
                            <table class="table mb-0 table-borderless custom-inner-tables">
                                <thead>
                                    <tr class="table-active">
                                        <th>User</th>
                                        <th>Website URL</th>
                                        <th>IP Address</th>
                                        <th style="width: 150px;">Add More</th>
                                    </tr>
                                </thead>
                                <tbody id="tab_logic">
                                    @if (Input::old('generate_apy_key') != '')
                                        <div id="countVar" data-count="{{ count(Input::old('generate_apy_key')) }}"></div>
                                        @foreach (Input::old('generate_apy_key') as $key => $value)
                                            <tr data-id={{ $key == 0 ? $key + 1 : $key }}>
                                                <td>

                                                    <select name="generate_apy_key[{{ $key }}][user_id]"
                                                        id="user_id" class="form-control select2" data-width="100%">
                                                        <option value="" selected disabled>Select</option>
                                                        @foreach ($users as $value)
                                                            <option value="{{ $value->id }}"
                                                                {{ old('user_id') == $value->id ? 'selected' : '' }}
                                                                {{ isset($data->user_id) ? ($data->user_id == $value->id ? 'selected' : '') : '' }}>
                                                                {{ $value->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    @if ($errors->has('generate_apy_key.' . $key . '.user_id'))
                                                        <span class="text-danger help-block form-error">
                                                            {{ $errors->first('generate_apy_key.' . $key . '.user_id') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input placeholder="Enter here..." class="form-control"
                                                        name="generate_apy_key[{{ $key }}][website_name]"
                                                        type="text"
                                                        value="{{ old('generate_apy_key.' . $key . '.website_name') }}">
                                                    @if ($errors->has('generate_apy_key.' . $key . '.website_name'))
                                                        <span class="text-danger help-block form-error">
                                                            {{ $errors->first('generate_apy_key.' . $key . '.website_name') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input placeholder="Enter here..." class="form-control"
                                                        name="generate_apy_key[{{ $key }}][ip_address]"
                                                        type="text"
                                                        value="{{ old('generate_apy_key.' . $key . '.ip_address') }}">
                                                    @if ($errors->has('generate_apy_key.' . $key . '.ip_address'))
                                                        <span class="text-danger help-block form-error">
                                                            {{ $errors->first('generate_apy_key.' . $key . '.ip_address') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($key == 0)
                                                        <button type="button" class="btn btn-primary plus"> <i
                                                                class="fa fa-plus"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-primary plus"> <i
                                                                class="fa fa-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger minus"> <i
                                                                class="fa fa-minus"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <div id="countVar" data-count="0"></div>
                                        <tr data-id="1">
                                            <td>
                                                <select name="generate_apy_key[0][user_id]" id="user_id"
                                                    class="form-control select2" data-width="100%">
                                                    <option value="" selected disabled>Select</option>
                                                    @foreach ($users as $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ old('user_id') == $value->id ? 'selected' : '' }}
                                                            {{ isset($data->user_id) ? ($data->user_id == $value->id ? 'selected' : '') : '' }}>
                                                            {{ $value->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('generate_apy_key.0.user_id'))
                                                    <span class="text-danger help-block form-error">
                                                        {{ $errors->first('generate_apy_key.0.user_id') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <input placeholder="Enter here..." class="form-control"
                                                    name="generate_apy_key[0][website_name]" type="text"
                                                    value="{{ old('generate_apy_key.0.website_name') }}">
                                                @if ($errors->has('generate_apy_key.0.website_name'))
                                                    <span class="text-danger help-block form-error">
                                                        {{ $errors->first('generate_apy_key.0.website_name') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <input placeholder="Enter here..." class="form-control"
                                                    name="generate_apy_key[0][ip_address]" type="text"
                                                    value="{{ old('generate_apy_key.0.ip_address') }}">
                                                @if ($errors->has('generate_apy_key.0.ip_address'))
                                                    <span class="text-danger help-block form-error">
                                                        {{ $errors->first('generate_apy_key.0.ip_address') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-info plus"> <i class="fa fa-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer  submit-buttons-commmon">
                    <button type="submit" class="btn btn-info">Submit</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script type="text/javascript">
        var users = [];
    </script>
    <?php if(!$users->isEmpty()){?>
    <script>
        users = <?php echo json_encode($users); ?>;
    </script>
    <?php } ?>

    <script type="text/javascript">
        $("body").on("click", ".plus", function() {
            // i = $('#tab_logic tr').length;
            var i = $("#tab_logic tr:last").data("id");
            i = i + 1;

            var select_user_html = "<select name='generate_apy_key[" + i +
                "][user_id]' id='user_id' class='form-control select2' data-width='100%'><option value='' selected disabled>Select</option>";
            if (users.length > 0) {
                for (var r = 0; r < users.length; r++) {
                    select_user_html += "<option value='" + users[r].id + "'>" + users[r].name + "</option>"
                }
            }

            select_user_html += "</select>";

            $("#tab_logic").append(
                '<tr data-id="' +
                i +
                '">\
                    <td>' + select_user_html +
                '<td>\
                        <input placeholder="Enter here..." class="form-control" name="generate_apy_key[' +
                i +
                '][website_name]" type="text">\
                    </td>\
                    <td>\
                        <input placeholder="Enter here..." class="form-control" name="generate_apy_key[' +
                i +
                '][ip_address]" type="text">\
                    </td>\
                    <td class="text-center">\
                        <button type="button" class="btn btn-info plus"> <i class="fa fa-plus"></i> </button>\
                        <button type="button" class="btn btn-danger minus"> <i class="fa fa-minus"></i> </button>\
                    </td>\
                </tr>'
            );
            // i++;

            $(".select2").select2({});
        });
        $("body").on("click", ".minus", function() {
            $(this).closest("tr").remove();
            // i--;
        });
    </script>
@endsection
