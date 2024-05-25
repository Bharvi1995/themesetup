<div class="row">
    <div class="col-md-12">
        <h5 class="text-danger">Upload Documents </h5>
        <span class="text-danger">Kindly upload below documents for the chargeback transactions as requested by the
            acquiring bank: <br>
        </span>
        <ul>
            <li>1) Proof of transaction (Bank confirmation email, transaction detail from the statement, etc.)</li>
            <li>2) KYC details like proof of ID, Address and any other details deemed relevant.</li>
        </ul>
        <hr>
        <form action="{{ route('transaction-documents-upload-edit') }}" class="form-dark" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="transaction_id" value="{{ $id }}">
            <input type="hidden" name="files_for" value="chargebacks">
            <div class="row {{ $errors->has('files') ? ' has-error' : '' }}">
                <div class="col-md-2">
                    <label class="label" for="files"><strong> Document</strong></label>
                </div>
                <div class="col-md-8">
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="form-control filestyle" name="files[]"
                                data-buttonname="btn-inverse"
                                accept="image/png, image/jpeg, .pdf, .txt, .doc, .docx, .xls, .xlsx, .zip"
                                id="inputGroupFile1" multiple="multiple">

                        </div>
                    </div>
                    @if ($errors->has('files'))
                        <p class="text-danger">
                            <strong>{{ $errors->first('files') }}</strong>
                        </p>
                    @endif
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
        <hr>
    </div>
    <div class="col-md-12">
        <h5 class="text-success">Documents</h5>
        <div class="table-responsive custom-table">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="50px">No.</th>
                        <th>Name</th>
                        <th style="min-width:200px">Action</th>
                    </tr>
                </thead>
                @if (isset($data))
                    <tbody>
                        <?php
                        $datas = json_decode($data->files);
                        ?>
                        @if (!empty($datas))
                            @foreach ($datas as $key => $value)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>Document - {{ $key + 1 }}</td>
                                    <td>
                                        <a href="{{ getS3Url('uploads/transactionDocumentsUpload/' . $value) }}"
                                            target="_blank" class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('user.downloadDocumentsUploade', ['file' => $value]) }}"
                                            class="btn btn-warning btn-sm">Download</a>
                                        <a class="btn btn-danger btn-sm remove-record"
                                            data-bs-target="#custom-width-modal" data-bs-toggle="modal"
                                            data-url="{{ route('chargebacks-document-delete', $data['transaction_id']) }}"
                                            data-id="{{ $value }}">
                                            Delete
                                        </a>
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
<script>
    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
