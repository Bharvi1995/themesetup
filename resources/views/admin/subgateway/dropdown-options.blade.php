<option value="">--Assign Gateway MID--</option> 
@foreach ($subgateways as $key=>$value)
	<option value="{{ $key }}">{{ $value }}</option>
@endforeach