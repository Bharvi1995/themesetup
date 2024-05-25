@extends('layouts.admin.default')

@section('title')
    White Label RP Merchant Management Show
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('wl-agents.index') }}">White Label RP</a> / <a
        href="{{ route('wl-agent-merchant', $data->white_label_agent_id) }}">Merchant Management</a> / Show
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card  mt-1">
                <div class="card-body  br-25">
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
                            <a href="{{ route('wl-agent-merchant', $data->white_label_agent_id) }}"
                                class="btn btn-primary btn-sm rounded"><i class="fa fa-arrow-left"
                                    aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card  mt-1">
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

                        <div class="col-lg-12 mb-1">
                            <div class="common-check-main">
                                @if ($data->is_ip_remove == '1')
                                    <label class="form-check-label overflow-checkbox">
                                        <input type="checkbox" class="form-check-input" name="isipremove"
                                            id="isipremove{{ $data->id }}" data-id="{{ $data->id }}" checked>
                                        <span class="overflow-control-indicator"></span>
                                        <span class="overflow-control-description">IP Remove</span>
                                    </label>
                                @else
                                    <label class="form-check-label overflow-checkbox">
                                        <input type="checkbox" class="form-check-input" name="isipremove"
                                            id="isipremove{{ $data->id }}" data-id="{{ $data->id }}">
                                        <span class="overflow-control-indicator"></span>
                                        <span class="overflow-control-description">IP Remove</span>
                                    </label>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-12 mb-1">
                            <div class="common-check-main">
                                @if ($data->is_active == 1)
                                    <label class="form-check-label overflow-checkbox">
                                        <input type="checkbox" class="form-check-input" name="is_active"
                                            id="is_active{{ $data->id }}" data-id="{{ $data->id }}">
                                        <span class="overflow-control-indicator"></span>
                                        <span class="overflow-control-description">Deactivated</span>
                                    </label>
                                @else
                                    <label class="form-check-label overflow-checkbox">
                                        <input type="checkbox" class="form-check-input" name="is_active"
                                            id="is_active{{ $data->id }}" data-id="{{ $data->id }}" checked>
                                        <span class="overflow-control-indicator"></span>
                                        <span class="overflow-control-description">Deactivated</span>
                                    </label>
                                @endif
                            </div>
                        </div>
                        <!-- To disable BIN rule -->
                        <div class="col-lg-12 mb-1">
                            <div class="common-check-main">
                                    <label class="form-check-label overflow-checkbox">
                                        <input type="checkbox" class="form-check-input"  id="toggleBinRule"
                                            data-id="{{ $data->id }}" data-bin="{{$data->is_bin_remove}}" data-url="{{route('wl.agent.merchant.togglebin')}}" {{$data->is_bin_remove == "1" ? "checked" :""}}>
                                        <span class="overflow-control-indicator"></span>
                                        <span class="overflow-control-description">Disable BIN checker </span>
                                    </label>
                              
                            </div>
                        </div>
                        <div class="col-lg-12 mb-1">
                            <div class="common-check-main">
                                <label class="form-check-label overflow-checkbox">
                                    <input type="checkbox" class="form-check-input" name="isdisablerule"
                                        id="isdisablerule{{ $data->id }}" data-id="{{ $data->id }}"
                                        @if ($data->is_disable_rule == '1') checked @endif>
                                    <span class="overflow-control-indicator"></span>
                                    <span class="overflow-control-description">Disable Global Rule</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-12 mb-1">
                            <div class="common-check-main">
                                <label class="form-check-label overflow-checkbox">
                                    <input name="is_otp_required" type="checkbox" class="form-check-input"
                                        id="is_otp_required_{{ $data->id }}" data-id="{{ $data->id }}"
                                        @if ($data->is_otp_required == '1') checked @endif>
                                    <span class="overflow-control-indicator"></span>
                                    <span class="overflow-control-description">OTP for Login</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card  mt-1">
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
                                                class="btn btn-primary btn-sm">View</a>
                                            <a href="{{ route('downloadDocumentsUploadeAdmin', ['file' => $extra_document]) }}"
                                                class="btn btn-danger btn-sm">Download</a>
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

@section('customScript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('body').on('change', 'input[name="is_active"]', function() {
                var id = $(this).data('id');
                var is_active = '1';

                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var is_active = '0';
                }

                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('user-deactive') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'is_active': is_active,
                        'id': id
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Merchant activation changed successfully!!');
                        } else {
                            toastr.error('Something went wrong!!');
                        }
                    },
                });
            });

            $('body').on('change', 'input[name="isipremove"]', function() {
                var id = $(this).data('id');
                var is_ip_remove = '0';

                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var is_ip_remove = '1';
                }
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('user-ip-remove') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'is_ip_remove': is_ip_remove,
                        'id': id
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Merchant IP removed changed successfully!!');
                        } else {
                            toastr.error('Something went wrong!!');
                        }
                    },
                });
            });

            $('body').on('change', 'input[name="isdisablerule"]', function() {
                var id = $(this).data('id');
                var is_disable_rule = '0';

                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var is_disable_rule = '1';
                }
                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('user-disable-rules') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'is_disable_rule': is_disable_rule,
                        'id': id
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Merchant disable rules changed successfully!!');
                        } else {
                            toastr.error('Something went wrong!!');
                        }
                    },
                });
            });

            $('body').on('change', 'input[name="is_otp_required"]', function() {
                var id = $(this).data('id');
                var is_otp = '0';

                // change the value based on check / uncheck
                if ($(this).prop("checked") == true) {
                    var is_otp = '1';
                }

                $.ajax({
                    type: 'POST',
                    context: $(this),
                    url: '{{ route('user-otp-required') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'is_otp': is_otp,
                        'id': id
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('Merchant otp login changed successfully!!');
                        } else {
                            toastr.error('Something went wrong!!');
                        }
                    },
                });
            });

            // * Toggle WL BIN rule
            $(document).on("click" ,"#toggleBinRule" ,function() {
                var txnId = $(this).attr("data-id");
                var url = $(this).attr("data-url")
                var bin = $(this).attr("data-bin")

                console.log("The data is" , txnId , url , bin);
                $.ajax({
                    type:"POST",
                    url:url,
                    data:{
                        "_token":CSRF_TOKEN,
                        "id":txnId,
                        "bin_id":bin,
                    },
                    success:function(res) {
                        console.log("The res is" , res)
                        if (res.status == 200) {
                            toastr.success(res.message);
                        } else {
                            toastr.error(res.message);
                        }
                    }
                })
                
            })
        });
    </script>
@endsection
