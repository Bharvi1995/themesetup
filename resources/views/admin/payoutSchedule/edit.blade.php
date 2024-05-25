@extends('layouts.admin.default')
@section('title')
    edit Payout Schedule
@endsection
@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('payout-schedule.index') }}">Payout
        Schedule</a> / Edit
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Edit Payout Schedule</h4>
                    </div>
                    <a href="{{ route('payout-schedule.index') }}" class="btn btn-primary btn-sm"><i
                            class="fa fa-arrow-left "></i>
                    </a>
                </div>
                <div class="card-body">
                    {{ Form::model($data, ['route' => ['payout-schedule.update', $data->id], 'method' => 'PUT']) }}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="date-input">
                                    {!! Form::text(
                                        'from_date',
                                        isset($data->from_date)
                                            ? \Carbon\Carbon::createFromFormat('Y-m-d', $data->from_date)->format('m/d/Y')
                                            : old('from_date'),
                                        [
                                            'placeholder' => 'From
                                                                                                                                                                                                                                                        Date',
                                            'class' => 'form-control payoutDateFields',
                                        ],
                                    ) !!}

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
                                    {!! Form::text(
                                        'to_date',
                                        isset($data->to_date) ? \Carbon\Carbon::createFromFormat('Y-m-d', $data->to_date)->format('m/d/Y') : old('to_date'),
                                        [
                                            'placeholder' => 'To
                                                                                                                                                                                                                                                        Date',
                                            'class' => 'form-control payoutDateFields',
                                        ],
                                    ) !!}

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
                                    {!! Form::text(
                                        'issue_date',
                                        isset($data->issue_date)
                                            ? \Carbon\Carbon::createFromFormat('Y-m-d', $data->issue_date)->format('m/d/Y')
                                            : old('issue_date'),
                                        [
                                            'placeholder' => 'Issue
                                                                                                                                                                                                                                                        Date',
                                            'class' => 'form-control payoutDateFields',
                                        ],
                                    ) !!}

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
                                {!! Form::number(
                                    'sequence_number',
                                    isset($data->sequence_number) ? $data->sequence_number : old('sequence_number'),
                                    ['placeholder' => 'Enter Sequence Number', 'class' => 'form-control'],
                                ) !!}
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
                                <a href="{{ route('payout-schedule.index') }}" class="btn btn-primary btn-sm">Cancel</a>
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
