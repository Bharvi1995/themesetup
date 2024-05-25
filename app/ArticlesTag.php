<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticlesTag extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'title',
        'meta_keyword',
        'meta_description'
    ];

    public function getData()
    {
        $data = static::select("articles_tags.*")
            ->orderBy("articles_tags.id","DESC")
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
