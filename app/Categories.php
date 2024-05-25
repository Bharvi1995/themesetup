<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    use SoftDeletes;

    protected $table   = 'categories';
    protected $guarded = array();

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'remember_token'
    ];

    public function getData()
    {
        $data = static::select("categories.*")
            ->orderBy("categories.id","DESC")
            ->get();

        return $data;
    }

    public function storeData($input)
    {
        return static::create($input);
    }

    public function findData($id)
    {
        return static::find($id);
    }

	public function updateData($id, $input)
    {
        return static::find($id)->update($input);
    }

    public function deleteData($id)
    {
        return static::find($id)->delete();
    }
}
