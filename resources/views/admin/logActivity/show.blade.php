@extends('layouts.appAdmin')
@section('style')
@endsection
@section('content')
    <div id="section_one">
        <div class="heading-title">
            <h3> Log Activity Show </h3>
        </div>
    </div>
    <div id="section-log-activity-by-user" class="common-section pt-3 mt-4">
        <div class="row mx-auto">
            <div class="col-xl-12 col-sm-12 col-md-12 col-12">
                <div class="col-xl-12 col-sm-12 col-md-12 col-12 pl-2 pr-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="has-bottom-line title">Log Activity Show</span></h4>
                        <a href="{{ route('admin-log-activity') }}" class="yellow-btn"><i
                                class="fas fa-angle-double-left me-2" aria-hidden="true"></i>Back</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-sm-12 col-md-12 col-12 row mx-auto mt-4">
                <div class="col-xl-4 col-sm-12 col-md-12 col-12 p-0 row mx-auto mb-4">
                    <div class="col-xl-6 col-sm-12 col-md-12 col-12">
                        <label class="fw-500"> Company Name: </label>
                    </div>
                    <div class="col-xl-6 col-sm-12 col-md-12 col-12">
                        <label class="log-activity-details">{{ $log->company_name }}</label>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-12 col-md-12 col-12 p-0 row mx-auto">
                    <div class="col-xl-6 col-sm-12 col-md-12 col-12">
                        <label class="fw-500"> Subject </label>
                    </div>
                    <div class="col-xl-6 col-sm-12 col-md-12 col-12">
                        <label class="log-activity-details">{{ $log->subject }}</label>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-12 col-md-12 col-12 p-0 row mx-auto mb-4">
                    <div class="col-xl-6 col-sm-12 col-md-12 col-12">
                        <label class="fw-500"> Query Type </label>
                    </div>
                    <div class="col-xl-6 col-sm-12 col-md-12 col-12">
                        <label class="log-activity-details">{{ $log->query_type }}</label>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-12 col-md-12 col-12 p-0 row mx-auto mb-4">
                    <div class="col-xl-6 col-sm-12 col-md-12 col-12">
                        <label class="fw-500"> URL </label>
                    </div>
                    <div class="col-xl-6 col-sm-12 col-md-12 col-12">
                        <label class="log-activity-details">{{ $log->url }}</label>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-12 col-md-12 col-12 p-0 row mx-auto mb-4">
                    <div class="col-xl-6 col-sm-12 col-md-12 col-12">
                        <label class="fw-500"> Method </label>
                    </div>
                    <div class="col-xl-6 col-sm-12 col-md-12 col-12">
                        <label class="log-activity-details">{{ $log->method }}</label>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-12 col-md-12 col-12 p-0 row mx-auto mb-4">
                    <div class="col-xl-6 col-sm-12 col-md-12 col-12">
                        <label class="fw-500"> IP </label>
                    </div>
                    <div class="col-xl-6 col-sm-12 col-md-12 col-12">
                        <label class="log-activity-details">{{ $log->ip }}</label>
                    </div>
                </div>

                <div class="col-xl-12 col-sm-12 col-md-12 col-12 p-0 row mx-auto mb-4">
                    <div class="col-xl-2 col-sm-12 col-md-12 col-12">
                        <label class="fw-500"> Subject </label>
                    </div>
                    <div class="col-xl-10 col-sm-12 col-md-12 col-12">
                        @if (isset($log->query_request))
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Query Request</label>
                                <div class="col-sm-9">
                                    <?php
                                    $code = json_decode($log->query_request);
                                    ?>
                                    <pre
                                        style="white-space: normal; background: rgba(0,0,0,0.1); color: #000; padding: 5px; border:1px solid rgba(72, 94, 144, 0.16);">
                                    {<br>
                                    @if (!empty($code))
@foreach ($code as $key => $value)
&nbsp; {{ $key }} => {{ $value }} <br>
@endforeach
@endif    
                                    }
                              </pre>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-xl-12 col-sm-12 col-md-12 col-12 p-0 row mx-auto mb-4">
                    <div class="col-xl-2 col-sm-12 col-md-12 col-12">
                        <label class="fw-500"> Agent </label>
                    </div>
                    <div class="col-xl-10 col-sm-12 col-md-12 col-12">
                        <label class="log-activity-details"> {{ $log->agent }} </label>
                    </div>
                </div>

            </div>

        </div>
    </div>
    </div>
    </div>



@endsection
@section('script')
@endsection
