@extends('layouts.admin.default')

@section('title')
    Admin Mail Templates
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('mail-templates.index') }}">Mail Templates</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Show</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">Show</h6>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Mail Template Show</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 mb-2">
                            <h5>Template Name</h5>
                        </div>
                        <div class="col-xl-9 col-lg-9">
                            {{ $mailTemplates->title }}
                        </div>

                        <div class="col-xl-3 col-lg-3 mb-2">
                            <h5>Description</h5>
                        </div>
                        <div class="col-xl-9 col-lg-9">
                            {{ $mailTemplates->description }}
                        </div>

                        <div class="col-xl-3 col-lg-3 mb-2">
                            <h5>Email Subject</h5>
                        </div>
                        <div class="col-xl-9 col-lg-9">
                            {{ $mailTemplates->email_subject }}
                        </div>

                        <div class="col-xl-3 col-lg-3 mb-2">
                            <h5>Email Body</h5>
                        </div>
                        <div class="col-xl-9 col-lg-9">
                            {!! $mailTemplates->email_body !!}
                        </div>

                        @php
                            if (!empty($mailTemplates->files)) {
                                $data = json_decode($mailTemplates->files);
                            }
                        @endphp
                        @if (!empty($data))
                            <div class="col-xl-3 col-lg-3">
                                <h5>Email Files</h5>
                            </div>
                            <div class="col-xl-9 col-lg-9">
                                <div class="row">
                                    @foreach ($data as $key => $datas)
                                        <div class="col-md-3">
                                            <h5>
                                                File - {{ ++$key }}
                                                <a href="{{ getS3Url($datas) }}" target="_blank"
                                                    class="btn btn-primary ml-3 btn-sm">View</a>
                                            </h5>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
@endsection
