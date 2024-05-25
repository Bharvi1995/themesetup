<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RpAgreementDocumentUpload extends Model
{
    protected $table = 'rp_agreement_document_upload';
    protected $guarded = [];

    protected $fillable = [
        'rp_id',
        'token',
        'files',
        'sent_files',
        'reassign_reason'
    ];
}
