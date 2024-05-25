<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rules extends Model
{


    protected $table = 'rules';
    protected $guarded = [];

    protected $fillable = [
        'rules_name',
        'assign_mid',
        'status',
        'rule_condition',
        'rule_condition_view',
        'priority',
        'rules_type',
        'is_card_type',
        'user_id'
    ];
}
