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
            <div class="col-xl-4 col-sm-12 col-md-12 col-12">
                <div class="common-check-main">
                    <label class="overflow-checkbox">
                        <input type="checkbox" class="form-check-input" name="isBinRemove"
                            id="isBinRemove{{ $data->id }}" data-id="{{ $data->id }}"
                            @if ($data->is_bin_remove == '1') checked @endif>
                        <span class="overflow-control-indicator"></span>
                        &nbsp;&nbsp;
                        <span class="overflow-control-description">Disable Bin Checker</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
