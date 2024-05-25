@extends($agentUserTheme)

@section('title')
    Show Notification
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('rp.dashboard') }}">Dashboard</a> / <a href="{{ route('notifications') }}">All
        Notifications</a> / Show
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Show Notification</h4>
                    </div>
                    <a href="{{ route('notifications') }}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <h5 class="text-info">Title : {{ $notifications->title }}</h5>
                        </div>
                        <div class="col-md-3 text-right text-danger">
                            <small>Time : {{ convertDateToLocal($notifications->created_at, 'd-m-Y / H:i:s') }}</small>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="row">
                                <div class="col-md-10">
                                    <h5>
                                        Description :
                                    </h5>
                                    {{ $notifications->body }}
                                </div>
                                <div class="col-md-2 text-right">
                                    <a href="{{ url($notifications->url) }}?for=read" target="_blank"
                                        class="btn btn-primary btn-xxs shadow">Go to Link</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
