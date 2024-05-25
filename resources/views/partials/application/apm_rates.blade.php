<div class="card">
    <div class="card-header">
        <div></div>
        <button type="button" class="btn btn-primary btn-sm apmAddMoreBtn">Add More</button>
    </div>
    <div class="card-body">
        <form class="apmRateBody form-dark" id="ampRatesForm">
            <input type="hidden" value="{{ $id }}" name="user_id" />
            @if (isset($userApms))
                @foreach ($userApms as $userApm)
                    <div class="row mb-2">
                        <input type="hidden" name="mid_type[]" class="form-control apmMidTypeInput"
                            value="{{ $userApm['apm_type'] }}" />
                        <input type="hidden" name="apm_id[]" value="{{ $userApm['apm_id'] }}"
                            class="form-control apmMidIdInput" />
                        <div class="col-lg-6">
                            <label>APM</label>
                            <select class="form-control select2 apmDropDown" name="apm[]">
                                @foreach ($apms as $item)
                                    <option value="{{ $item->bank_name }}" data-rate="{{ $item->apm_mdr }}"
                                        data-type="{{ $item->apm_type }}" data-apmid="{{ $item->apm_id }}"
                                        {{ $userApm['bank_name'] == $item->bank_name ? 'selected' : '' }}>
                                        {{ $item->bank_name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger errorField"></span>
                        </div>

                        <div class="col-lg-4">
                            <label>APM Rate</label>
                            <input type="text" name="rate[]" class="form-control apmRateInput"
                                value="{{ $userApm['apm_mdr'] }}" />
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-danger  apmRemoveBtn" style="margin-top: 28px;"><i
                                    class="fa fa-minus"></i></button>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="row mb-2">
                    <input type="hidden" name="mid_type[]" class="form-control apmMidTypeInput" />
                    <input type="hidden" name="apm_id[]" class="form-control apmMidIdInput" />

                    <div class="col-lg-6">
                        <label>APM</label>
                        <select class="form-control select2 apmDropDown" name="apm[]" required>
                            <option value="">-- Select APM --</option>
                            @foreach ($apms as $item)
                                <option value="{{ $item->bank_name }}" data-rate="{{ $item->apm_mdr }}"
                                    data-type="{{ $item->apm_type }}" data-apmid="{{ $item->id }}">
                                    {{ $item->bank_name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger errorField"></span>
                    </div>
                    <div class="col-lg-6">
                        <label>APM Rate</label>
                        <input type="text" value="0.00" name="rate[]" required
                            class="form-control apmRateInput" />
                    </div>
                </div>
            @endif

        </form>
    </div>
</div>

{{-- * To append data --}}
<div class="apmAppendData d-none">
    <div class="row mb-2">
        <input type="hidden" name="mid_type[]" class="form-control apmMidTypeInput" />
        <input type="hidden" name="apm_id[]" class="form-control apmMidIdInput" />

        <div class="col-lg-6">
            <label>APM</label>
            <select class="form-control select2 apmDropDown" name="apm[]" required>
                <option value="">-- Select APM --</option>
                @foreach ($apms as $item)
                    <option value="{{ $item->bank_name }}" data-rate="{{ $item->apm_mdr }}"
                        data-type="{{ $item->apm_type }}" data-apmid="{{ $item->id }}">
                        {{ $item->bank_name }}
                    </option>
                @endforeach
            </select>
            <span class="text-danger errorField"></span>
        </div>
        <div class="col-lg-4">
            <label>APM Rate</label>
            <input type="text" value="0.00" name="rate[]" required class="form-control apmRateInput" />
        </div>
        <div class="col-lg-2">
            <button type="button" class="btn btn-danger  apmRemoveBtn" style="margin-top: 28px;"><i
                    class="fa fa-minus"></i></button>
        </div>
    </div>
</div>
