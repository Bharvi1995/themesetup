<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequiredField extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'field_title', 'field', 'field_type', 'field_validation'
    ];

    public function destroyData($id)
    {
        return RequiredField::destroy($id);
    }
}
