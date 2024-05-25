<div class="table-responsive" style="max-height: 300px;">
	<div class="widget-timeline">
	    <ul class="timeline">
	    	@foreach($data as $key => $value)
	        <li>
	            <div class="timeline-badge info">
	            </div>
	            <div class="timeline-panel text-muted">
	                <p class="mb-0 pull-left"><strong class="text-info">{{ $value->user_type }}</strong> - {{ ucwords(getAdminName($value->user_id)) }}</p>
	                <span class="pull-right">Date & Time - {{ $value->created_at->format('d-m-Y / H:i:s') }}</span>
					<div style="clear: both;"></div>
					<p class="mb-0 text-black">
						{{ $value->note }}
					</p>
	            </div>
	        </li>
			@endforeach
	    </ul>
	</div>
</div>

