<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MailTemplates extends Model
{
    use SoftDeletes;
    public $table = 'mail_tamplates';

    protected $fillable = [
    	'title','description','email_subject','email_body','files'
    ];

    public function getData()
    {
        $data = static::select("mail_tamplates.*")
            ->orderBy("mail_tamplates.id","DESC")
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

    public function getListForMail()
    {
        return static::pluck('title', 'id')->all();
    }

    public function getTemplateForMail($id){
        return static::where('id', $id)->first();
    }
}
