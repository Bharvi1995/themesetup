@extends('layouts.admin.default')

@section('title')
    Merchant Bank Details
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('transaction-session') }}">Transaction session</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">View</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">View</h6>
    </nav>
@endsection

@section('customeStyle')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/atom-one-dark.min.css">
    <style>
        .pre code.hljs {
            font-size: 30px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h5 class="card-title">Request Payload</h5>
                    </div>

                </div>
                <div class="card-body">
                    @php
                        echo '
                        <pre><code class="language-json">';
                        echo json_encode($json, JSON_PRETTY_PRINT);
                        echo '</code></pre>';
                    @endphp
                </div>
            </div>

            <!-- MID Request Payload -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h5 class="card-title">Mid Request Payload</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if (!empty($data->mid_payload))
                        @php
                            $json = json_decode($data->mid_payload);
                            echo '
                        <pre><code class="language-json">';
                            echo json_encode($json, JSON_PRETTY_PRINT);
                            echo '</code></pre>';
                        @endphp
                    @else
                        <div class="d-flex justify-content-center align-items-center flex-column p-2">
                            <h4>No MID payload found</h4>
                            <p>Hopefully you are checking old transaction. </p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
        <div class="col-lg-6">


            @if (!empty($data->response_data))
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h5 class="card-title">Response Payload</h5>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $json = json_decode($data->response_data);
                            echo '
                        <pre><code class="language-json">';
                            echo json_encode($json, JSON_PRETTY_PRINT);
                            echo '</code></pre>';
                        @endphp
                    </div>
                </div>
            @endif

            <!-- Webhook Request Payload -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h5 class="card-title">Mid Webhook Payload</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if (!empty($data->webhook_response))
                        @php
                            $json = json_decode($data->webhook_response);
                            echo '
                        <pre><code class="language-json">';
                            echo json_encode($json, JSON_PRETTY_PRINT);
                            echo '</code></pre>';
                        @endphp
                    @else
                        <div class="d-flex justify-content-center align-items-center flex-column p-2">
                            <h4>No Webhook payload found</h4>
                        </div>
                    @endif

                </div>
            </div </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <script>
        hljs.highlightAll();
    </script>
@endsection
