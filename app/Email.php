<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Auth;

class Email extends Model
{
    use Cachable;
	protected $table = 'send_mail';

    protected $guarded = array();

    public function storeData($data)
    {
    	return static::create($data);
    }

    public function getEmailById($id)
    {
    	return static::where('id', $id)->first();
    }

    public function getEmailSentAdmin()
    {
    	return static::where('sender_user_type','admin')->where('sender_id',auth()->guard('admin')->user()->id)->where('is_delete_admin', '0')->orderBy('id','desc')->paginate(15);
    }

    public function getEmailSentUser()
    {
        return static::where('sender_user_type','user')->where('sender_id',auth()->user()->id)->where('is_delete_user', '0')->orderBy('id','desc')->paginate(15);
    }

    public function getEmailInboxAdmin()
    {
        return static::where('receiver_user_type','admin')->where('receiver_id',auth()->guard('admin')->user()->id)->where('is_delete_admin', '0')->orderBy('id','desc')->paginate(15);
    }

    public function getEmailInboxUser()
    {
        return static::where('receiver_user_type','user')->where('receiver_id',auth()->user()->id)->where('is_delete_user', '0')->orderBy('id','desc')->paginate(15);
    }

    public function deleteEmailByAdmin($id)
    {
        return static::where('id', $id)->update(['is_delete_admin' => '1']);
    }

    public function deleteEmailByUser($id)
    {
        return static::where('id', $id)->update(['is_delete_user' => '1']);
    }
}
