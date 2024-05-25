<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Tutorial extends Model
{
    use Cachable;

    protected $fillable = [
        'title','slug','category_id','description'
    ];

    protected $table = 'tutorials';

    public function storeData($input)
    {
        return static::create(\Arr::only($input,$this->fillable));
    }

    public function updateData($id,$input)
    {
    	return static::where('id',$id)->update(\Arr::only($input,$this->fillable));
    }

    public function getData()
    {
        return static::orderBy('created_at','desc')->get();
    }

    public function findData($id)
    {
        return static::find($id);
    }

    public function getFrontTutorials($input)
    {
        $data =  static::orderBy('created_at','desc');

        if (!empty($input) && !empty($input['tutorial_search'])) {
            $data = $data->where("title","LIKE","%{$input['tutorial_search']}%");
        }

        return $data->paginate(10);
    }

    public function getFrontTutorialsWithCategoryId($category_id)
    {
        return static::orderBy('created_at','desc')
                ->where('category_id',$category_id)
                ->paginate(10);
    }

    public function getFrontTutorialWithSlug($slug)
    {
        return static::where('slug',$slug)
                ->first();
    }

}
