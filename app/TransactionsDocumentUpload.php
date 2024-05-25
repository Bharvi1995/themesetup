<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionsDocumentUpload extends Model
{
    protected $table = 'transactions_document_upload';
    protected $guarded = [];

    protected $fillable = [
        'transaction_id',
        'files',
        'files_for'
    ];
}
