<div class="row">
    <div class="col-md-12">
        <table class="table mb-0 table-borderless">
            <thead>
                <tr>
                    <th class="text-warning">Bank Name</th>
                    <th class="text-warning">Application Status</th>
                    <th class="text-warning">Reason</th>
                    <th class="text-warning">Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach(getSentBank($id) as $key => $bank)
            <tr>
                <td class="text-white">
                    {{ $bank->bankCompanyName }}
                </td>
                <td>
                    @if($bank->status == 0)
                    <span class="badge badge-info badge-sm">Pending</span>
                    @elseif($bank->status == 1)
                    <span class="badge badge-success badge-sm">Approved</span>
                    @elseif($bank->status == 2)
                    <span class="badge badge-danger badge-sm">Declined</span>
                    @elseif($bank->status == 3)
                    <span class="badge badge-warning badge-sm">Referred</span>
                    @endif
                </td>
                <td class="text-white">
                    @if($bank->referred_note != NULL)
                        <b> Referrde Note:</b> {{ $bank->referred_note }}
                    @endif
                    @if($bank->referred_note != NULL)
                        <br><b> Referred Note Reply:</b> {{ $bank->referred_note_reply }}
                    @endif
                    @if($bank->declined_reason != NULL)
                    {{ $bank->declined_reason }}
                    @endif
                </td>
                <td>
                    @if($bank->status == 3)
                    @if(!$bank->referred_note_reply)
                        <a data-bs-toggle="modal" href="#referredReplyModel" class="referred_note_reply btn btn-info btn-sm" data-id="{{$id}}" data-bank_id="{{$bank->bank_user_id}}">Reply</a>
                    @else
                        <span class="badge badge-info">Replied</span>
                    @endif
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>