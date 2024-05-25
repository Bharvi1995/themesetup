<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TechnologyPartner extends Model
{
    use SoftDeletes;

    protected $table   = 'technology_partners';
    protected $guarded = array();

    protected $fillable = [
        'name'
    ];

    protected $hidden = [
        'remember_token'
    ];

    public function getData()
    {
        $data = static::select("technology_partners.*")
            ->orderBy("technology_partners.id","DESC")
            ->get();

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
}
