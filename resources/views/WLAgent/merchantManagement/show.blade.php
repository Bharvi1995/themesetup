@extends($WLAgentUserTheme)

@section('title')
    Merchant Show
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('wl-dashboard') }}">Dashboard</a> / Merchant Show
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body br-25">
                    <div class="row align-items-center">
                        <div class="col-xl-10 col-xxl-10 mr-auto">
                            <div class="d-sm-flex d-block align-items-center">
                                <i class="fa fa-key text-primary" style="font-size: 56px;"></i>
                                <div class="ms-2">
                                    <h4 class="fs-20">API Key</h4>
                                    <p class="fs-14 mb-0 text-danger">{{ $data->api_key }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-xxl-2 text-right">
                            <a href="{{ route('wl-merchant-management') }}" class="btn btn-primary btn-sm rounded"><i
                                    class="fa fa-arrow-left" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Merchant Info</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <strong>User Name</strong> : {{ $data->name }}
                        </div>

                        <div class="col-lg-12 mb-3">
                            <strong>Email</strong> : {{ $data->email }}
                        </div>

                        <div class="col-lg-12 mb-3">
                            <strong>Phone Number</strong> : +{{ $data->countryCode }} {{ $data->phoneNo }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">IP Whitelist</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>Website</th>
                                    <th>IP</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->websiteUrl as $websiteUrl)
                                    <tr>
                                        <td><a href="{{ $websiteUrl->website_name }}"
                                                target="_blank">{{ $websiteUrl->website_name }}</a></td>
                                        <td>{{ $websiteUrl->ip_address }}</td>
                                        <td>
                                            @if ($websiteUrl->is_active == 0)
                                                <span class="badge badge-primary">Pending</span>
                                            @else
                                                <span class="badge badge-success">Approved</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Company Info</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <strong>Company Name</strong> : {{ $data->business_name }}
                        </div>
                        <div class="col-lg-12 mb-3">
                            <strong>Business Category</strong> : {{ $data->business_type }}
                        </div>
                        <div class="col-lg-12 mb-3">
                            <strong>Website URL</strong> : <a href="{{ $data->website_url }}" class="text-danger"
                                target="_blank">{{ $data->website_url }}</a>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <strong>Industry Type</strong> :

                            @if (isset($data->category_id))
                                @if (getCategoryName($data->category_id) != 'Miscellaneous')
                                    <span
                                        class='badge badge-sm badge-success'>{{ getCategoryName($data->category_id) }}</span>
                                @else
                                    @if ($data->other_industry_type != null)
                                        <span class="badge badge-primary badge-sm">{{ $data->other_industry_type }}</span>
                                    @endif
                                @endif
                            @else
                                ---
                            @endif
                        </div>

                        <div class="col-lg-12">
                            @if (isset($data->wl_extra_document))
                                @foreach (json_decode($data->wl_extra_document) as $key => $extra_document)
                                    <div class="row mb-1">
                                        <div class="col-lg-6">
                                            <strong>{{ $key }}</strong> :
                                        </div>
                                        <div class="col-lg-6 text-right">
                                            <a href="{{ getS3Url($extra_document) }}" target="_blank"
                                                class="btn btn-danger btn-sm">View</a>
                                            <a href="{{ route('downloadDocumentsUploadeWLUser', ['file' => $extra_document]) }}"
                                                class="btn btn-primary btn-sm">Download</a>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
