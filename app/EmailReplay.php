<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Auth;

class EmailReplay extends Model
{
    use Cachable;
	protected $table = 'send_mail_replay';

    protected $guarded = array();

    public function storeData($data)
    {
    	return static::create($data);
    }

    public function getEmailByEmailId($id)
    {
    	return	static::select("send_mail_replay.*","admins.email as admins_email","users.email as users_email","admins.name as admins_name","users.name as users_name")
    				->leftjoin("users",function($join){
    					$join->on("users.id","=","send_mail_replay.receiver_id")
    					->on("send_mail_replay.receiver_user_type","=",\DB::raw("'user'"));
    				})
    				->leftjoin("admins",function($join){
    					$join->on("admins.id","=","send_mail_replay.receiver_id")
    					->on("send_mail_replay.receiver_user_type","=",\DB::raw("'admin'"));
    				})
    				->where('send_mail_replay.email_id', $id)
                    ->orderBy('send_mail_replay.id','DESC')
    				->get();
    }
}