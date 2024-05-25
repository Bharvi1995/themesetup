<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'application_id', 'user_id', 'user_type', 'note'
    ];

    protected $table = 'application_note';
}
