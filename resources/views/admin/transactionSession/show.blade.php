@extends('layouts.admin.default')

@section('title')
    Merchant Bank Details
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Transaction Session Data
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
    <div class="d-flex justify-content-between align-items-center my-1">
        <h3>Transaction Session Details</h3>
        <a href="{{ route('transaction-session') }}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i> </a>
    </div>
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
