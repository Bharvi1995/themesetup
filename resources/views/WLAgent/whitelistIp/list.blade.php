@extends($WLAgentUserTheme)

@section('title')
    Merchant Management
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('wl-dashboard') }}">Dashboard</a> / IP Whitelist
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">IP List</h4>
                    </div>
                    <a href="{{ route('wl-rp-whitelist-ip-add') }}" class="btn btn-primary rounded d-none d-md-block">Add
                        IP</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless">
                            <thead>
                                <tr>
                                    <th>S. No.</th>
                                    <th>Company Name</th>
                                    <th>User Email</th>
                                    <th>Website URL</th>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                    <th width="100px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($apiWebsiteUrlIP as $key => $value)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $value->business_name }}</td>
                                        <td>{{ $value->email }}</td>
                                        <td>{{ $value->website_name }}</td>
                                        <td>{{ $value->ip_address }}</td>
                                        <td>
                                            @if ($value->is_active == '0')
                                                <label class="badge badge-sm badge-danger">Pending</label>
                                            @else
                                                <label class="badge badge-sm badge-success">Approved</label>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" class="btn btn-danger delete_modal"
                                                data-id="{{ $value->id }}"
                                                data-url="{{ route('wl-rp-deleteWebsiteUrl', $value->id) }}"><i
                                                    class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('body').on('change', '#selectallcheckbox', function() {
                if ($(this).prop("checked") == true) {
                    $('.multicheckmail').prop("checked", true);
                } else if ($(this).prop("checked") == false) {
                    $('.multicheckmail').prop("checked", false);
                }
            });
        });
    </script>

    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
@endsection
