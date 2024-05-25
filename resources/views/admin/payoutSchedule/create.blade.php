@extends('layouts.admin.default')
@section('title')
    Create Payout Schedule
@endsection
@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('payout-schedule.index') }}">Payout
        Schedule</a> / Create
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Create Payout Schedule</h4>
                    </div>
                    <a href="{{ route('payout-schedule.index') }}" class="btn btn-primary btn-sm"><i
                            class="fa fa-arrow-left "></i>
                    </a>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => 'payout-schedule.store', 'method' => 'POST']) !!}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="date-input">
                                    <input class="form-control payoutDateFields" type="text" name="from_date"
                                        placeholder="From Date" value="{{ old('from_date') }}" autocomplete="off">

                                </div>
                                @if ($errors->has('from_date'))
                                    <span class="text-danger help-block form-error">
                                        <span>{{ $errors->first('from_date') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="date-input">
                                    <input class="form-control payoutDateFields" type="text" name="to_date"
                                        placeholder="To Date" value="{{ old('to_date') }}" autocomplete="off">

                                </div>
                                @if ($errors->has('to_date'))
                                    <span class="text-danger help-block form-error">
                                        <span>{{ $errors->first('to_date') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="date-input">
                                    <input class="form-control payoutDateFields" type="text" name="issue_date"
                                        placeholder="Issue Date" value="{{ old('issue_date') }}" autocomplete="off">

                                </div>
                                @if ($errors->has('issue_date'))
                                    <span class="text-danger help-block form-error">
                                        <span>{{ $errors->first('issue_date') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="number" name="sequence_number" class="form-control" type="text"
                                    placeholder="Sequence Number" value="">
                                @if ($errors->has('sequence_number'))
                                    <span class="text-danger help-block form-error">
                                        <span>{{ $errors->first('sequence_number') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success btn-sm">Submit</button>
                                <a href="" class="btn btn-primary btn-sm">Cancel</a>
                            </div>
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
        $(document).ready(function() {
            $(".payoutDateFields").datepicker({
                todayHighlight: true,
                format: "dd/mm/yyyy",
            });
        });
    </script>
@endsection
