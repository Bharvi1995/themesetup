<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class BlockData extends Model
{
    use SoftDeletes;
	
    protected $table = 'block_data';
    protected $guarded = [];
    protected $fillable = [
        'type',
		'field_value',
    ];

    public function getData($input, $noList)
    {
        $data = static::select("block_data.*")
            ->orderBy("block_data.id", "DESC");
        if (isset($input['type']) && $input['type'] != '') {
            $data = $data->where('block_data.type',  '=' , $input['type']);
        }
        if (isset($input['field_value']) && $input['field_value'] != '') {
            $field_value = Str::of($input['field_value'])->trim();
            $data = $data->where('block_data.field_value', '=',  $field_value);
        }
        $data = $data->paginate($noList);

        return $data;
    }
}
