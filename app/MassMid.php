<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MassMid extends Model
{
    use SoftDeletes;
    
    protected $table = 'mass_mid';
    protected $guarded = array();

    public function getData()
    {
        return static::select("mass_mid.*")->get();
    }
    public function findData($id)
    {
        return static::find($id);
    }

    public function storeData($input)
    {
        return static::create($input);
    }

    public function destroyData($id)
    {
        return static::find($id)->delete();
    }

    public function updateData($id, $input)
    {
        return static::find($id)->update($input);
    }
}
