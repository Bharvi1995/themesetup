<div class="merchant-detaiils-main">
    <div class="row pb-3">
        <div class="main-merchant-wrap">
            <div class="common-user-list row mx-auto">
                <div class="col-xl-12 col-sm-12 col-md-12 col-12 mb-3">
                    <b>API Key : </b><b class="text-danger">{!! isset($data->api_key) ? $data->api_key : '' !!}</b>
                </div>
                <div class="col-xl-6 col-sm-12 col-md-6 col-6">
                    <p> Email : {!! $data->email !!}</p>
                </div>
                <div class="col-xl-6 col-sm-12 col-md-6 col-6">
                    <p> OTP : {!! $data->otp !!}</p>
                </div>
                <div class="col-xl-6 col-sm-12 col-md-6 col-6">
                    <p> UUID : {!! $data->uuid !!}</p>
                </div>
            </div>

            <div class="row  mt-3">
                <div class="col-xl-8 col-sm-12 col-md-12 col-12">
                    {{-- <a href="{{\URL::route('send-password', $data->id)}}" class="btn btn-blue"> Send Login Credential
               </a> --}}
                    <a href="" class="btn btn-danger btn-sm changePassBtn" data-bs-toggle="modal"
                        data-bs-target="#Change_password" data-id="{{ $data->id }}"> Change Password </a>
                </div>
            </div>
            <div class="row mx-auto mt-3 border-top-1 pt-2">
                <div class="col-xl-4 col-sm-12 col-md-12 col-12">
                    <div class="common-check-main">
                        @if ($data->is_ip_remove == '1')
                            <div class="form-check form-check-info text-left mr-0">
                                <label class="custom-control form-check-label mb-2">
                                    <input type="checkbox" class="form-check-input" name="isipremove"
                                        id="isipremove{{ $data->id }}" data-id="{{ $data->id }}" checked>
                                    <span class="overflow-control-indicator"></span>
                                    <span class="overflow-control-description">IP Remove</span>
                                </label>
                            </div>
                        @else
                            <div class="form-check form-check-info text-left mr-0">
                                <label class="custom-control form-check-label mb-2">
                                    <input type="checkbox" class="form-check-input" name="isipremove"
                                        id="isipremove{{ $data->id }}" data-id="{{ $data->id }}">
                                    <span class="overflow-control-indicator"></span>
                                    <span class="overflow-control-description">IP Remove</span>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
                {{--  <div class="col-xl-4 col-sm-12 col-md-12 col-12">
                     <div class="common-check-main">
                     @if (auth()->guard('admin')->user()->can(['user-ban-refund']))
                        @if ($data->is_desable_vt == '1')
                            <div class="form-check form-check-info text-left mr-0">
                           <label class="custom-control form-check-label mb-2">
                              <input type="checkbox" class="form-check-input" name="activestatus" id="activestatus{{$data->id}}" data-id="{{$data->id}}" checked>
                                <span class="overflow-control-indicator"></span>
                                <span class="overflow-control-description">Disable VT</span>
                            </label>
                        </div>
                        @else
                        <div class="form-check form-check-info text-left mr-0">
                        <label class="custom-control form-check-label mb-2">
                            <input type="checkbox" class="form-check-input" name="activestatus" id="activestatus{{$data->id}}"
                            data-id="{{$data->id}}" checked>
                            <span class="overflow-control-indicator"></span>
                            <span class="overflow-control-description">Disable VT</span>
                        </label>
                    </div>
                        @endif
                    @else
                ----
                @endif

   </div>
</div>
<div class="col-xl-4 col-sm-12 col-md-12 col-12">
   <div class="common-check-main">
      @if (auth()->guard('admin')->user()->can(['user-ban-refund']))
      @if ($data->make_refund == '0')
      <div class="form-check form-check-info text-left mr-0">
      <label class="custom-control form-check-label mb-2">
         <input type="checkbox" class="form-check-input" name="banrefund" id="banrefund{{$data->id}}"
            data-id="{{$data->id}}" checked>
         <span class="overflow-control-indicator"></span>
         <span class="overflow-control-description">Ban Refund</span>
      </label>
  </div>
      @else
    <div class="form-check form-check-info text-left mr-0">
      <label class="custom-control form-check-label mb-2">
         <input type="checkbox" class="form-check-input" name="banrefund" id="banrefund{{$data->id}}"
            data-id="{{$data->id}}">
         <span class="overflow-control-indicator"></span>
         <span class="overflow-control-description">Ban Refund</span>
      </label>
  </div>
      @endif
      @else
      ----
      @endif

   </div>
</div> --}}
                <div class="col-xl-4 col-sm-12 col-md-12 col-12">
                    <div class="common-check-main">
                        @if ($data->is_active == 1)
                            <div class="form-check form-check-info text-left mr-0">
                            <label class="custom-control form-check-label mb-2">
                                <input type="checkbox" class="form-check-input" name="is_active"
                                    id="is_active{{ $data->id }}" data-id="{{ $data->id }}">
                                <span class="overflow-control-indicator"></span>
                                <span class="overflow-control-description">Deactivated</span>
                            </label>
                        </div>
                        @else
                            <div class="form-check form-check-info text-left mr-0">
                            <label class="custom-control form-check-label mb-2">
                                <input type="checkbox" class="form-check-input" name="is_active"
                                    id="is_active{{ $data->id }}" data-id="{{ $data->id }}" checked>
                                <span class="overflow-control-indicator"></span>
                                <span class="overflow-control-description">Deactivated</span>
                            </label>
                        </div>
                        @endif

                    </div>
                </div>
                {{-- <div class="col-xl-4 col-sm-12 col-md-12 col-12">
                        <div class="common-check-main">
                              <label class="custom-control overflow-checkbox">
                                 <input type="checkbox" class="form-check-input" id="enable_admin_dashboard_{{$data->id}}"
data-did="{{$data->id}}" value="{{$data->id}}" @if ($data->enable_product_dashboard == 'yes') checked @endif>
<span class="overflow-control-indicator"></span>
<span class="overflow-control-description">Enable Product Dashboard</span>
</label>
</div>
</div> --}}
                <div class="col-xl-4 col-sm-12 col-md-12 col-12">
                    <div class="common-check-main">
                        <div class="form-check form-check-info text-left mr-0">
                        <label class="custom-control overflow-checkbox">
                            <input name="is_otp_required" type="checkbox" class="form-check-input"
                                id="is_otp_required_{{ $data->id }}" data-id="{{ $data->id }}"
                                @if ($data->is_otp_required == '1') checked @endif>
                            <span class="overflow-control-indicator"></span>
                            <span class="overflow-control-description">OTP for Login</span>
                        </label>
                    </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-12 col-md-12 col-12">
                    <div class="common-check-main">
                        <div class="form-check form-check-info text-left mr-0">
                        <label class="custom-control overflow-checkbox">
                            <input type="checkbox" class="form-check-input" name="isdisablerule"
                                id="isdisablerule{{ $data->id }}" data-id="{{ $data->id }}"
                                @if ($data->is_disable_rule == '1') checked @endif>
                            <span class="overflow-control-indicator"></span>
                            <span class="overflow-control-description">Disable Global Rule</span>
                        </label>
                    </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-12 col-md-12 col-12">
                    <div class="common-check-main">
                        <div class="form-check form-check-info text-left mr-0">
                        <label class="custom-control overflow-checkbox">
                            <input type="checkbox" class="form-check-input" name="isBinRemove"
                                id="isBinRemove{{ $data->id }}" data-id="{{ $data->id }}"
                                @if ($data->is_bin_remove == '1') checked @endif>
                            <span class="overflow-control-indicator"></span>
                            <span class="overflow-control-description">Disable Bin Checker</span>
                        </label>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
