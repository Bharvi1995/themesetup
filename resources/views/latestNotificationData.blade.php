@if(isset($latestNotifications) && count($latestNotifications)>0)
    @foreach($latestNotifications as $notification)
    <li>
        <div class="activity-icon">
            <i class="far fa-clock"></i>
        </div>
        <div class="activity-details">
                <h3> {{ $notification->title }}</h3>
                <p>{{convertDateToLocal($notification->created_at, 'd-m-Y')}} {{convertDateToLocal($notification->created_at, 'h:s A')}}</p>
        </div>
    </li>
    @endforeach
@else
<li>No Notification found!.</li>
@endif
