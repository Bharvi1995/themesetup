@extends('layouts.bank.default')

@section('title')
  Review Application
@endsection

@section('breadcrumbTitle')
<a href="{{ route('bank.dashboard') }}">Dashboard</a> / <a href="{{ route('bank.applications') }}">Applications</a> / Review
@endsection

@section('customeStyle')
<style type="text/css">
    .DeleteDocument {
      /*padding: 0px 10px 2px 10px;*/
    }
    .bankNotDes{
        color: #FFF;
        width: 100%;
        padding: 15px;
        background: #34383e;
        border-radius: 15px;
        box-shadow: rgb(0 0 0 / 70%) 10px 10px 15px -5px, rgb(0 0 0 / 60%) 5px 5px 5px -10px;
    }
</style>
@endsection

@section('content')
<div class="row">    
	<div class="col-xl-12 col-xxl-12">
        <div class="card mt-2">
            <div class="card-header">
                <div></div>
                <div>
					<a href="{{ \URL::route('application-pdf-for-bank', $data->id) }}" class="btn btn-primary btn-sm btn-shadow me-50" style="float:left;"><i class="fa fa-download text-primary"></i> PDF Download</a>
                    <div class="btn-group">
						<a href="{{ \URL::route('application-docs-for-bank', $data->id) }}" class="btn btn-primary btn-sm btn-shadow pull-right"><i class="fa fa-download"></i> Document Download </a>
                    </div>
                </div>
            </div>                    
        </div>
    </div>
</div>

<div class="row">
	<div class="col-xl-8 col-xxl-8">
		<div class="card border-card height-auto">
		<div class="card-header">
			<div class="header-title">
			<h4 class="card-title">Review Application</h4>
			</div>
		</div>
		<div class="card-body p-0">
			<!-- <div class="row"> -->
			@include('partials.application.applicationShow')
			<!-- </div> -->
		</div>
		</div>
		<div class="card border-card height-auto">
		<div class="card-header">
			<div class="header-title">
			<h4 class="card-title">Documents List</h4>
		</div>
		</div>
		<div class="card-body p-0">
			<div class="table-responsive custom-table">
            	<table class="table table-borderless table-striped">
					@if ($data->licence_document != null)
					<tr>
						<td>Licence Document</td>
						<td>
							<a href="{{ getS3Url($data->licence_document) }}" target="_blank" class="btn btn-primary btn-sm">View</a>
							<a href="{{ route('downloadDocumentsUploadeBank',['file'=>$data->licence_document]) }}"
							class="btn btn-primary btn-sm">Download</a>
						</td>
					</tr>
					@endif
					<tr>
						<td>Passport</td>
						<td>
							<div class="row">
							@foreach (json_decode($data->passport) as $key => $passport )
							<div class="col-md-4">File - {{ $key +1 }}</div>
							<div class="col-md-8">
								<a href="{{ getS3Url($passport) }}" target="_blank" class="btn btn-primary btn-sm">View</a>
								<a href="{{ route('downloadDocumentsUploadeBank',['file'=>$passport]) }}"
								class="btn btn-primary btn-sm">Download</a>
							</div>
							@endforeach
							</div>
						</td>
					</tr>
					<tr>
						<td>Articles Of Incorporation</td>
						<td>
							<a href="{{ getS3Url($data->company_incorporation_certificate) }}" target="_blank"
							class="btn btn-primary btn-sm">View</a>
							<a href="{{ route('downloadDocumentsUploadeBank',['file'=>$data->company_incorporation_certificate]) }}"
							class="btn btn-primary btn-sm">Download</a>
						</td>
					</tr>
					<tr>
						@if(isset($data->domain_ownership))
						<td>Domain Ownership</td>
						<td>
							<a href="{{ getS3Url($data->domain_ownership) }}" target="_blank"
							class="btn btn-primary btn-sm">View</a>
							<a href="{{ route('downloadDocumentsUploadeBank',['file'=>$data->domain_ownership]) }}"
							class="btn btn-primary btn-sm">Download</a>
						</td>
						@endif
					</tr>
					<tr>
						@if(isset($data->latest_bank_account_statement))
						<td>Company's Bank Statement (last 180 days)</td>
						<td>
							<div class="row">
							@foreach (json_decode($data->latest_bank_account_statement) as $key => $bankStatement )
							<div class="col-md-4">File - {{ $key +1 }}</div>
							<div class="col-md-8">
								<a href="{{ getS3Url($bankStatement) }}" target="_blank"
									class="btn btn-primary btn-sm">View</a>
								<a href="{{ route('downloadDocumentsUploadeBank',['file'=>$bankStatement]) }}"
									class="btn btn-primary btn-sm">Download</a>
							</div>
							@endforeach
							</div>
						</td>
						@endif
					</tr>
					<tr>
						@if(isset($data->utility_bill))
						<td>Utility Bill</td>
						<td>
							<div class="row">
								@foreach (json_decode($data->utility_bill) as $key => $utilityBill )
								<div class="col-md-4">File - {{ $key +1 }}</div>
								<div class="col-md-8">
									<a href="{{ getS3Url($utilityBill) }}" target="_blank"
										class="btn btn-primary btn-sm">View</a>
									<a href="{{ route('downloadDocumentsUploadeBank',['file'=>$utilityBill]) }}"
										class="btn btn-primary btn-sm">Download</a>
								</div>
								@endforeach
							</div>
						</td>
						@endif
					</tr>
					<tr>
						@if(isset($data->owner_personal_bank_statement))
						<td>UBO's Bank Statement (last 90 days)</td>
						<td>
							<a href="{{ getS3Url($data->owner_personal_bank_statement) }}" target="_blank"
							class="btn btn-primary btn-sm">View</a>
							<a href="{{ route('downloadDocumentsUploadeBank',['file'=>$data->owner_personal_bank_statement]) }}"
							class="btn btn-primary btn-sm">Download</a>
						</td>
						@endif
					</tr>
					<tr>
						@if(isset($data->previous_processing_statement) && $data->previous_processing_statement != null)
						<td>
							Processing History (if any)
						</td>
						<td>
							<div class="row">
							@php
							$previous_processing_statement_files = json_decode($data->previous_processing_statement);
							@endphp
							<div class="col-md-12">
								<div class="row">
								@php
								$count = 1;
								@endphp
								@foreach($previous_processing_statement_files as $key => $value)
								<div class="col-md-4">File - {{ $count }}</div>
								<div class="col-md-8">
									<a href="{{ getS3Url($value) }}" target="_blank" class="btn btn-primary btn-sm">View</a>
									<a href="{{ route('downloadDocumentsUploadeBank',['file' => $value]) }}"
									class="btn btn-primary btn-sm">Download</a>
								</div>
								@php
								$count++;
								@endphp
								@endforeach
								</div>
							</div>
							</div>
						</td>
						@endif
					</tr>
					<tr>
						@if ($data->moa_document != null)
						<td>MOA(Memorandum of Association) Document</td>
						<td>
							<a href="{{ getS3Url($data->moa_document) }}" target="_blank" class="btn btn-primary btn-sm">View</a>
							<a href="{{ route('downloadDocumentsUploadeBank',['file'=>$data->moa_document]) }}"
							class="btn btn-primary btn-sm">Download</a>
						</td>
						@endif
					</tr>
					<tr>
						@if(isset($data->extra_document) && $data->extra_document != null)
						<td>
							Additional Document
						</td>
						<td>
							<div class="row">
							@php
							$extra_document_files = json_decode($data->extra_document);
							@endphp
							<div class="col-md-12">
								<div class="row">
								@php
								$count = 1;
								@endphp
								@foreach($extra_document_files as $key => $value)
								<div class="col-md-4">File - {{ $count }}</div>
								<div class="col-md-8">
									<a href="{{ getS3Url($value) }}" target="_blank" class="btn btn-primary btn-sm">View</a>
									<a href="{{ route('downloadDocumentsUploadeBank',['file' => $value]) }}"
									class="btn btn-primary btn-sm">Download</a>
								</div>
								@php
								$count++;
								@endphp
								@endforeach
								</div>
							</div>
							</div>
						</td>
						@endif
					</tr>
					<tr>
						@if(isset($bank->extra_documents) && $bank->extra_documents != null)
						<td>
							Referred Reply Document
						</td>
						<td>
							<div class="row">
							@php
							$extra_document_files = json_decode($bank->extra_documents);
							@endphp
							<div class="col-md-12">
								<div class="row">
								@php
								$count = 1;
								@endphp
								@foreach($extra_document_files as $key => $value)
								<div class="col-md-4">File - {{ $count }}</div>
								<div class="col-md-8">
									<a href="{{ getS3Url($value) }}" target="_blank" class="btn btn-primary btn-sm">View</a>
									<a href="{{ route('downloadDocumentsUploadeBank',['file' => $value]) }}"
									class="btn btn-primary btn-sm">Download</a>
								</div>
								@php
								$count++;
								@endphp
								@endforeach
								</div>
							</div>
							</div>
						</td>
						@endif
					</tr>
				</table>
			</div>			
		</div>
		</div>
	</div>  
  	<div class="col-xl-4 col-xxl-4">
	  <div class="card border-card height-auto">
            <div class="card-header">
                <div class="header-title">
                    <h4 class="card-title">Current Status Notes</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="noteForm" class="form-dark">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{ $data->id }}" id="appId">
                            <div class="form-group">
                                <textarea class="form-control" name="note" id="note" rows="3"
                                  placeholder="Write Here Your Note"></textarea>
                                <span class="help-block text-danger">
                                    <span id="note_error"></span>
                                </span>
                            </div>
                            <button type="button" id="submitNoteForm" data-link="{{ route('store-application-note-bank-to-admin') }}" class="btn btn-primary btn-sm pull-right">Submit Note</button>
                        </form>
                    </div>
                    <div class="col-md-12" style="max-height: 350px; overflow-y: auto;">
                        <div id="detailsContent"></div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
@endsection

@section('customScript')
<script type="text/javascript">
    $(document).ready(function(){
        var id = $('#appId').val();
        $.ajax({
            url:'{{ route('get-application-note-bank-to-admin') }}',
            type:'POST',
            data:{ "_token": "{{ csrf_token() }}", 'id' : id},
            beforeSend: function(){
                $('#detailsContent').html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
            },
            success:function(data) {
                $('#detailsContent').html(data.html);
            },
        });
    });

    function getAppNote(id) {
        $.ajax({
            url:'{{ route('get-application-note-bank-to-admin') }}',
            type:'POST',
            data:{ "_token": "{{ csrf_token() }}", 'id' : id},
            beforeSend: function(){
                $('#detailsContent').html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
            },
            success:function(data) {
                $('#detailsContent').html(data.html);
            },
        });
    }

    $('body').on('click', '#submitNoteForm', function(){
        $(this).html('<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
        $(this).css('cursor','not-allowed');

        var noteForm = $("#noteForm");
        var formData = noteForm.serialize();
        $( '#note_error' ).html( "" );
        $( '#note' ).val( "" );
        let apiUrl = $(this).data('link');

        $.ajax({
            url: apiUrl,
            type:'POST',
            data:formData,
            success:function(data) {
                $('#submitNoteForm').html('Submit Note');
                $('#submitNoteForm').css('cursor','pointer');
                if(data.errors) {
                    if(data.errors.note){
                        $( '#note_error' ).html( data.errors.note[0] );
                    }
                }
                if(data.success == '1') {
                    getAppNote(data.id);
                    toastr.success('Add Note Successfully.');
                } else if (data.success == '0')   {
                    toastr.error('Something went wrong, please try again!');
                }
            },
        });
    });
</script>
@endsection