<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
// use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class ReportUser extends Authenticatable
{
    // use Cachable;
    protected $table = 'reportusers';
    protected $guarded = array();

    public function getData()
    {
        $data = static::select("reportusers.*")->orderBy("reportusers.id","DESC")->get();

        return $data; 
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

    public function userBan($id)
    {
        return static::where('id',$id)->update(array('ban' => 1));
    }

    public function userRevoke($id)
    {
        return static::where('id',$id)->update(array('ban' => 0));
    }
}
