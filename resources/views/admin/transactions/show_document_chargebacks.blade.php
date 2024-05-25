<div class="row">
    <div class="col-md-12">
        <form action="{{ route('merchant-chargeback-upload-document') }}" method="POST" enctype="multipart/form-data" class="form-dark">
            @csrf
            <input type="hidden" name="transaction_id" value="{{ $id }}">
            <input type="hidden" name="files_for" value="chargebacks">
            <div class="row {{ $errors->has('files') ? ' has-error' : '' }}">
                <div class="col-md-2">
                    <label class="label" for="files"><strong> Upload Document</strong></label>
                </div>
                <div class="col-md-8">
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="form-control custom-file-input filestyle" name="files[]" data-buttonname="btn-inverse" accept="image/png, image/jpeg, .pdf, .txt, .doc, .docx, .xls, .xlsx, .zip" id="inputGroupFile1" multiple="multiple">
                        </div>
                    </div>
                    @if ($errors->has('files'))
                        <p class="text-danger">
                            <strong>{{ $errors->first('files') }}</strong>
                        </p>
                    @endif
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </div>
        </form>
        <h4>Documents</h4>
        <div class="table-responsive custom-table">
            <table class="table table-borderless table-striped">
                <thead>
                    <tr>
                        <th style="width:50px">No.</th>
                        <th>Name</th>
                        <th style="min-width:185px">Action</th>
                    </tr>
                </thead>
                @if(isset($data))
                <tbody>
                    <?php
                        $data = json_decode($data->files);
                    ?>
                    @if(!empty($data))
                        @foreach($data as $key => $value)
                            <tr>         
                                <td>{{ $key+1 }}</td>                                           
                                <th>Document - {{ $key+1 }}</th>
                                <td>
                                    <a href="{{ getS3Url('uploads/transactionDocumentsUpload/'.$value) }}" target="_blank" class="btn btn-primary btn-sm m-b-5">View</a>
                                    <a href="{{ route('downloadDocumentsUploade',['file'=>$value]) }}" class="btn btn-primary btn-sm m-b-5">Download</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                @else
                <tbody>
                    <td colspan="3" class="text-center">No Documents</td>
                </tbody>
                @endif
            </table>
        </div>
    </div>
</div>               