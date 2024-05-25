<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgreementDocumentUpload extends Model
{
    protected $table = 'agreement_document_upload';
    protected $guarded = [];

    protected $fillable = [
        'user_id',
        'application_id',
        'sent_files',
        'files',
        'token'
    ];
}
