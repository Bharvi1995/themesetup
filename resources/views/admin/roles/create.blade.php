@extends('layouts.admin.default')

@section('title')
    Admin Role Create
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ url('paylaksa/roles') }}">Role</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Create Role</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Create Role</h6>
    </nav>
@endsection

@section('customeStyle')
<style type="text/css">
    .form-check-label{
        width: 92%;
    }
</style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Admin Roles</h4>
                    </div>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => 'roles.store', 'method' => 'POST', 'class' => 'form-dark', 'id' => 'role-form']) !!}
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label for="">Name</label>
                            <input type="text" class="form-control" id="text" name="name"
                                placeholder="Enter here..." value="">
                            @if ($errors->has('name'))
                                <span class="help-block text-danger">
                                    {{ $errors->first('name') }}
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-12 form-group">
                            <div class="custom-tab-1">
                                <ul class="nav nav-tabs">
                                    @foreach ($moduleList as $k => $module)
                                        <li class="nav-item" style="margin-bottom: 0px;">
                                            <a class="nav-link {{ $k === 0 ? 'active' : '' }}" data-bs-toggle="tab"
                                                href="#{{ str_replace(' ', '', $module) }}" style="padding: 0.5rem 0.7rem;">
                                                @if ($module == 'mid')
                                                    MID
                                                @else
                                                    {{ ucfirst($module) }}
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content">
                                    @foreach ($moduleList as $k => $module)
                                        <div id="{{ str_replace(' ', '', $module) }}"
                                            class="tab-pane fade {{ $k === 0 ? 'show active' : '' }}">
                                            @foreach ($permission[$module] as $key => $subModule)
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <p class="mt-4">
                                                            <?php
                                                            $mainname = strtolower(str_replace(' ', '-', trim($key)));
                                                            ?>
                                                            <strong>
                                                                <div class="form-check form-check-info text-left">
                                                                <input type="checkbox" name="main_name[]"
                                                                    class="form-check-input" id="chk{{ $mainname }}"
                                                                    onclick="chkGroupElememts(this, '{{ $mainname }}');">
                                                                @if ($key == 'mid')
                                                                    MID
                                                                @else
                                                                    {{ ucfirst($key) }}
                                                                @endif
                                                            </div>
                                                            </strong>
                                                        </p>
                                                    </div>
                                                    @foreach ($subModule as $value)
                                                        <div class="col-md-3">
                                                            <div
                                                                class="form-check form-check-info text-left mr-0">
                                                                {{ Form::checkbox('permission[]', $value->id, false, ['class' => 'form-check-input ' . $mainname, 'id' => $value->id]) }}
                                                                <label class="custom-control-label"
                                                                    for="{{ $value->id }}">
                                                                    @if (substr(kebabToHumanString($value->name), -3) == 'Mid')
                                                                        {{ substr(kebabToHumanString($value->name), 0, -3) }}
                                                                        MID
                                                                    @else
                                                                        {{ kebabToHumanString($value->name) }}
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-2">
                            <button type="submit" class="btn btn-primary ">Submit</button>
                            <a href="{{ url('paylaksa/roles') }}" class="btn btn-danger ">Cancel</a>
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
        $('#role-form').submit(function() {
            $(this).find('input:text').each(function() {
                $(this).val($.trim($(this).val()));
            });
        });

        function chkGroupElememts(thiss, controlid) {
            if (thiss.checked) {
                $('.' + controlid).each(function() {
                    this.checked = true;
                });
            } else {
                $('.' + controlid).each(function() {
                    this.checked = false;
                });
            }
        }
    </script>
@endsection
