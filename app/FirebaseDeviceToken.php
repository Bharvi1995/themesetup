<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FirebaseDeviceToken extends Model
{
    protected $table = 'firebase_device_tokens';
    protected $guarded = array();
}
