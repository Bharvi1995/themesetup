@extends('layouts.admin.default')
@section('title')
    Required Fields
@endsection
@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('required_fields.index') }}">Required Fields</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Create</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Create</h6>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Create Required fields</h4>
                    </div>
                    </a>
                </div>
                <div class="card-body">
                    {!! Form::open([
                        'route' => 'required_fields.store',
                        'method' => 'POST',
                        'class' => 'form form-dark w-100',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <input type="hidden" name="txtCount" id="txtCount" value="0">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive custom-table">
                                <table class="table table-borderless table-striped" id="tbRules">
                                    <thead>
                                        <tr>
                                            <th>Field Title</th>
                                            <th>Field</th>
                                            <th>Type</th>
                                            <th>Validators</th>
                                            <th style="width: 150px;">Add More</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tab_logic">
                                        <div id="dvTabRules">
                                            <tr style="display:none">
                                                <td>
                                                    <input type="hidden" name="txHiddenAdd[]" id="txHiddenAdd_{groupId}"
                                                        value="Y">
                                                    <input type="text" name="txtFieldTitle[]"
                                                        id="txtFieldTitle_{groupId}" class="form-control">
                                                </td>
                                                <td>
                                                    <input type="text" name="txtField[]" id="txtField_{groupId}"
                                                        class="form-control">
                                                </td>
                                                <td>
                                                    <select class="form-control" name="lstType[]">
                                                        <option value="">-Type-</option>
                                                        @foreach (getFieldsType() as $key => $value)
                                                            <option value="{{ $key }}">{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input placeholder="Enter Validation" class="form-control"
                                                        name="txtValidation[]" type="text">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm btnMinus"
                                                        onClick="fnRemoveRow({groupId})"> Minus
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr id="trRules_0">
                                                <td>
                                                    <input type="hidden" name="txHiddenAdd[]" id="txHiddenAdd_0"
                                                        value="Y">
                                                    <input type="text" class="form-control" name="txtFieldTitle[]"
                                                        id="txtFieldTitle_0">
                                                </td>
                                                <td>
                                                    <input type="text" name="txtField[]" id="txtField_{groupId}"
                                                        class="form-control">
                                                </td>
                                                <td>
                                                    <select class="form-control" name="lstType[]" id="lstType_0">
                                                        <option value="">-Type-</option>
                                                        @foreach (getFieldsType() as $key => $value)
                                                            <option value="{{ $key }}">{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input placeholder="Enter Validation" class="form-control"
                                                        name="txtValidation[]" type="text">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-success btn-sm btnPlus"> Plus </button>
                                                </td>
                                            </tr>
                                        </div>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12 mt-1">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('paylaksa/required_fields') }}" class="btn btn-danger">Cancel</a>
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
        $('.btnPlus').on("click", function() {
            var count = $("#txtCount").val();
            count++;
            var x = document.getElementById("tbRules").rows.length;
            var html = document.getElementById("tbRules").rows.item(1).innerHTML;
            html = html.replace(/{groupId}/g, count);
            var finalHtml = "<tr id='trRules_" + count + "'>" + html + "</tr>";
            $('#tab_logic').append(finalHtml);
            $("#txtCount").val(count);
        })

        function fnRemoveRow(intId) {
            $("#txHiddenAdd_" + intId).val("N");
            $("#trRules_" + intId).hide();
        }
    </script>
@endsection
