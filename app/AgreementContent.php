<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgreementContent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type', 'body'
    ];

    protected $table = 'agreement_content';
}
