<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'tag_id',
        'slug',
        'title',
        'image',
        'description',
        'meta_keyword',
        'meta_description'
    ];

    public function category()
    {
        return $this->hasOne(ArticlesCategorie::class, 'id', 'category_id');
    }

    public function getData($input)
    {
        $data = static::select("articles.*")
            ->orderBy("articles.id","DESC");
        if (!empty($input['category_id'])) {
            $data = $data->where('category_id', $input['category_id']);
        }

        $data = $data->get();

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

    public function findWithSlugData($slug)
    {
        return static::where("slug", $slug)->first();
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
