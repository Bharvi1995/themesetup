@extends($bankUserTheme)

@section('title')
Dashboard
@endsection

@section('breadcrumbTitle')
Dashboard
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4 mb-2">
        <div class="merchantTxnCard">
            <h2>100 %</h2>
            <p class="mb-1" style="color: #FFFFFF;">Total Applications</p>
            <p class="total" style="color: #5F9DF7;">Total Count : <span> {{ count($application) }}</span></p>
        </div>
    </div>
    <div class="col-lg-4 mb-2">
        <div class="merchantTxnCard">
            <h2>{{ count($application)?(count($application->where('status','1'))*100)/count($application):'0' }} %</h2>
            <p class="mb-1" style="color: #FFFFFF;">Approved Applications</p>
            <p class="total" style="color: #82CD47;">Total Count : <span> {{ count($application->where('status','1')) }}</span></p>
        </div>
    </div>
    <div class="col-lg-4 mb-2">
        <div class="merchantTxnCard">
            <h2>{{ count($application)?(count($application->where('status','2'))*100)/count($application):'0' }} %</h2>
            <p class="mb-1" style="color: #FFFFFF;">Declined Applications</p>
            <p class="total" style="color: #BF4146;">Total Count : <span> {{ count($application->where('status','2')) }}</span></p>
        </div>
    </div>
</div>	
@endsection