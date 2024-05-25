@extends($agentUserTheme)
@section('title')
    Notifications
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('rp.dashboard') }}">Dashboard</a> / Notifications
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">All Notifications</h4>
                    </div>
                    <a href="{{ route('rp.dashboard') }}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i>
                    </a>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        @foreach ($notifications as $notification)
                            <li>
                                <div class="timeline-dots"></div>
                                <h6 class="float-left mb-1">{{ $notification->title }}</h6>
                                <small
                                    class="float-right mt-1">{{ convertDateToLocal($notification->created_at, 'd-m-Y / H:i:s') }}</small>
                                <div class="d-inline-block w-100">
                                    <p>{{ Str::limit($notification->body, 120) }}</p>

                                    <a href="{{ url($notification->url) }}?for=read" target="_blank"
                                        class="btn btn-primary btn-sm rounded-pill">Go to
                                        Link</a>
                                    <a href="{{ route('read-admin-notifications', $notification->id) }}"
                                        class="btn btn-warning rounded-pill btn-sm">View</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
