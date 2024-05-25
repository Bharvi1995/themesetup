@extends('layouts.admin.default')
@section('title')
    System Logs
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / System Logs
@endsection

@section('customeStyle')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.2/styles/atom-one-dark.min.css">

    <style>
        .hljs {
            background: #454545 !important;
            font-size: 16px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <h4>System Logs</h4>
                    <div>
                        <span class="badge badge-danger">File Size :- {{ $fileSize }}</span>
                        <button class="btn btn-sm btn-warning clearLogs">Clear Logs</button>
                    </div>

                </div>
                <div class="card-body">
                    @foreach ($logs as $log)
                        <div class="">
                            <pre><code>{{ $log }}
                                </code></pre>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- * Clear logs Form --}}
    <form id="clearLogsForm" action="{{ route('clear.system.logs') }}" method="POST">
        @csrf
    </form>
@endsection

@section('customScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        hljs.highlightAll();

        $(document).ready(function() {
            $(document).on("click", ".clearLogs", function() {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You want to clear logs!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, clear it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#clearLogsForm").submit();
                    }
                });
            })
        })
    </script>
@endsection
