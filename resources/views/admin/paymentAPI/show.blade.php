@extends('layouts.admin.default')

@section('title')
    Payment API
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Payment API
@endsection

@section('customeStyle')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/atom-one-dark.min.css">
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center my-1">
        <h3>Payment API</h3>
        <div>
            <a href="{{ route('admin.paymentApi') }}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i> </a>
        </div>
    </div>
    <div class="row">

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">General Details</h4>
                </div>
                <div class="card-body">
                    @if (!empty($user))
                        <div class="my-2">
                            {{ $user->business_name }} | <strong>{{ $user->email }}</strong>
                        </div>
                        <div class="my-2">Order ID: <strong>{{ $data->order_id }}</strong></div>
                        <div class="my-2">Session ID: <strong>{{ $data->session_id }}</strong></div>
                        <div class="my-2">Method: <strong>{{ $data->method }}</strong></div>
                        <div class="my-2">Request from IP: <strong>{{ $data->ip }}</strong></div>
                        <div class="my-2">Time: <strong>{{ $data->created_at }}</strong></div>
                    @else
                        <div class="text-center">
                            <p>No details found!!</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4>Request Payload</h4>
                </div>
                <div class="card-body">
                    @php
                        $json = json_decode($data->request, true);
                        if (json_last_error() === 0) {
                            echo '<pre><code class="language-json">';
                            echo json_encode($json, JSON_PRETTY_PRINT);
                            echo '</code></pre>';
                        } else {
                            echo '<pre>' . $data->request . '</pre>';
                        }
                    @endphp
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            @if (!empty($logs))
                @foreach ($logs as $log)
                    <div class="card">
                        <div class="card-header">
                            <h4 class="text-capitalize">{{ $log->type }} Response</h4>
                        </div>
                        <div class="card-body">
                            @if (!empty($log->response))
                                @if ($log->type == 'return')
                                    <pre><code class="langage-text">{{ $log->response }}</code>
                                @else
                                    @php
                                        $jsonD = json_decode($log->response, true);
                                        $response = json_encode($jsonD, JSON_PRETTY_PRINT);
                                    @endphp
                                    <pre><code class="langage-json">{{ $response }}</code>
                                @endif
                            </pre>
                            @else
                                <pre><code class="language-json">{"message":"No Record found"}</code></pre>
                            @endif

                        </div>
                    </div>
                @endforeach
            @else
                <div class="card-body">
                    <p>No response found!!</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('customScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <script>
        hljs.highlightAll();
    </script>
@endsection
