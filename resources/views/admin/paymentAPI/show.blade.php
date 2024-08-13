@extends('layouts.admin.default')

@section('title')
    Payment API
@endsection

@section('breadcrumbTitle')
    <nav aria-label="breadcrumb">
       <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.paymentApi') }}">Payment API</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">View</li>
       </ol>
       <h6 class="font-weight-bolder mb-0">View</h6>
    </nav>
@endsection

@section('customeStyle')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/atom-one-dark.min.css">
@endsection

@section('content')
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
