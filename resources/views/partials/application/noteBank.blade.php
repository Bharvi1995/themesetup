@foreach ($data as $key => $value)
    <div class="bankNotDes mb-2">
        <div class="row">
            <div class="col-md-6">
                <strong class="text-warning">BY - {{ $value->user_type }}</strong>
                <br>
                @if ($value->user_type == 'ADMIN')
                    {{ ucwords(getAdminName($value->user_id)) }}
                @else
                    {{ ucwords(getBankCompanyName($value->user_id)) }}
                @endif
            </div>
            <div class="col-md-6 text-right">
                <strong class="text-warning">Date & Time </strong>
                <br>
                <small>
                    {{ $value->created_at->format('d-m-Y / H:i:s') }}
                </small>
            </div>
            <div class="col-md-12" style="border-top:1px solid #3D3D3D;">
                {{ $value->note }}
            </div>
        </div>
    </div>
@endforeach
