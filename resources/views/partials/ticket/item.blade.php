<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="header-title">
                <h4 class="card-title">View Ticket Details</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-8 col-xs-8">
                            <h4 class="mb-3" style="padding-left: 80px;">
                                {{$ticket->title}}
                            </h4>
                        </div>
                        <div class="col-sm-8 col-xs-8">
                            <div class="media mr-2 media-info">
                                <img src="{{ storage_asset('softtheme/img/favicon.ico') }}" width="45px">
                            </div>
                            <div class="desc">
                                <small class="">Date :
                                    {{ convertDateToLocal($ticket->created_at, 'd-m-Y') }}</small>
                                <small class=" pull-right">Created By : {{ $ticket->user->name }}</small> <br>
                                <small class=" pull-right">Email : {{ $ticket->user->email }} </small><br>
                                <div class="mt-2">
                                    {{ $ticket->body }}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-xs-4">
                            <div class="row">
                                @if(!empty($ticket->files))
                                @foreach(json_decode($ticket->files) as $key=>$file)
                                <div class="col-sm-12 col-xs-12 mt-2">
                                    Attachment - {{ ++$key }}
                                    &nbsp; &nbsp; &nbsp;
                                    <a href="{{ getS3Url('uploads/tickets/'.$file) }}" target="_blank"
                                        class="btn btn-sm light btn-info"><i class="fa fa-eye"></i></a>
                                    <a href="{{ route('downloadFilesUploaded',['file'=>$file]) }}"
                                        class="btn btn-sm btn-primary"><i class="fa fa-download"></i></a>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!empty($ticket->replies) && $ticket->replies->count())
<div class="col-md-12 mt-2">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="header-title">
                <h4 class="card-title">Ticket Reply</h4>
            </div>
        </div>

        <div class="card-body">
            @foreach ($ticket->replies as $reply)
            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="media mr-2 media-info">
                        <img src="{{ storage_asset('softtheme/img/favicon.ico') }}" width="45px">
                    </div>
                    <div class="desc">
                        <small class="">Date : {{ convertDateToLocal($reply->created_at, 'd-m-Y') }}</small>
                        <small class=" pull-right">Reply By : {{ $reply->user->name }}</small><br>
                        <div class="mt-2">{{ $reply->body }}</div>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <div class="row mt-3">
                        @if(!empty($reply->files))
                        @foreach(json_decode($reply->files) as $key=>$file)
                        <div class="col-sm-12 col-xs-12 mt-2">
                            Attachment - {{ ++$key }}
                            &nbsp; &nbsp; &nbsp;
                            <a href="{{ getS3Url('uploads/tickets/'.$file) }}" target="_blank"
                                class="btn btn-sm light btn-info"><i class="fa fa-eye"></i></a>
                            <a href="{{ route('downloadFilesUploaded',['file'=>$file]) }}"
                                class="btn btn-sm btn-danger"><i class="fa fa-download"></i></a>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@if($ticket->status != '3')
<div class="col-md-12 mt-2">
    <div class="form-validation">
        <form class="form-valide form-dark"
            action="{{ get_guard() == 'admin'?route('admin.ticket.reply.store'):route('ticket.reply.store') }}"
            enctype="multipart/form-data" method="post" id="ticket-form">
            @csrf
            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Reply</h4>
                    </div>
                </div>

                <div class="card-body">
                    <div class="basic-form">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Message :</label>
                                <textarea name="body" class="form-control" rows="5" cols="30" required></textarea>
                                @error('reply')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label>Attach Files :</label>
                                <div class="custom-file">
                                    <input type="file" class="form-control" id="validationCustomFile"
                                        name="files[]"
                                        accept="image/png, image/jpeg, .pdf, .txt, .doc, .docx, .xls, .xlsx, .zip">
                                    @if($errors->has('image'))
                                    <span class="text-danger help-block form-error">
                                        {{ $errors->first('image') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Reply</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif