@extends('layouts.admin.default')

@section('title')
    Agreement Generate
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('dashboardPage') }}">Dashboard</a> / Agreement Generate
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            {!! Form::open(['route' => 'agreement-generate-store', 'method' => 'post']) !!}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Agreement Generate</h4>
                </div>

                <div class="card-body">
                    <div class="basic-form">
                        <div class="row ">
                            <div class="form-group col-lg-6">
                                <label for="">Company Name</label>
                                <select class="select2" name="user_id" data-size="7" data-live-search="true"
                                    data-title="Select here" id="company" data-width="100%">
                                    @foreach ($companyName as $company)
                                        <option value="{{ $company->user_id }}"> {{ $company->business_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Generate</button>
                    <a href="javascript:;" class="btn btn-danger">Cancel</a>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
