<div class="col-xl-12 col-sm-12 col-md-12 col-12 row mx-auto Mid-create-duplicate sub-assign-country-spe-mids" style="margin-top:10px;">
    <div class="col-xl-3 col-sm-12 col-md-12 col-12">
    </div>
    <div class="col-xl-4 col-sm-12 col-md-12 col-12">
        <select class="form-control form-control-xs" name="countrySpeMids[0][country]" data-size="7" data-live-search="true" data-title="Select Country" data-width="100%">
        <option value="" selected>   -- Select Country --    </option>
        @foreach(getCountry() as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
        @endforeach
        </select>
    </div>
    <div class="col-xl-4 col-sm-12 col-md-12 col-12">
            <select class="form-control form-control-xs" name="countrySpeMids[0][us_mid]" data-size="7" data-live-search="true" data-title="Select US MID" data-width="100%">
            <option value="" selected>   -- Select US MID --   </option>
            @if($midList)
                @foreach($midList as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            @endif
        </select> 
    </div>
    <div class="col-xl-1 col-sm-12 col-md-12 col-12 d-flex justify-content-end">
        <a href="javascript:void(0);" class="blue-btn remove-assign-country-spe-mids"> <i class="fas fa-minus"></i>  </a>
    </div>
</div>
