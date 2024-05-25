<div class="common-section">
    <div class="heading-title mb-4">
        <h3 class="mb-0" > Latest Tickets  </h3>
        <a href="{{ route('ticket') }}" class="btn btn-yellow">View All <i class="fas fa-chevron-right ml-1"></i> </a>
    </div>
    @if(isset($ticketData) && count($ticketData)>0)
        @foreach($ticketData as $ticket)
            <div class="common-tickets-listing col-12">
                <div class="side-tickets">
                    <div class="right-arrow">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="text-tickets">
                        <h4> {{ $ticket->title }} </h4>
                        <p>{{ convertDateToLocal($ticket->created_at, 'd-m-Y')}}</p>
                    </div>
                </div>
                <div class="right-text">
                        @if($ticket->department == '1')
                            <p>Technical</p>
                        @elseif($ticket->department == '2')
                            <p>Finance</p>
                        @else
                            <p>Customer Service</p>
                        @endif

                        @if($ticket->status == '0')
                            <span>Panding</span>
                        @elseif($ticket->status == '1')
                            <span>Done</span>
                        @elseif($ticket->status == '2')
                            <span>Re-assign</span>
                        @else
                            <span>Close</span>
                        @endif
                </div>
            </div>
        @endforeach
    @else
    <p>No record found!.</p>
    @endif
</div>
