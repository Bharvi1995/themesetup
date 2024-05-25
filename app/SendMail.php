<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SendMail extends Model
{
    /**
	 * The attribute that assign the database table.
	 *
	 * @var array
	 */
    protected $table = 'send_mail';

    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = array();

    // ================================================
    /*  method : getEmails
    * @ param  :
    * @ Description : get emails for users
    */// ==============================================
    function getEmails($user_id = null, $limit = null)
    {
        $emails = static::select('send_mail.*', 'admins.name as sendor_name')
        		->join('admins', 'send_mail.sender_id', 'admins.id')
        		->orderBy('send_mail.id', 'desc');
                if ($user_id != null) {
                    $emails = $emails->where('send_mail.receiver_id', $user_id);
                }
                if ($limit != null) {
                    $emails = $emails->limit($limit);
                }
                $emails = $emails->get();

        return $emails;
    }

    // ================================================
    /*  method : getAdminEmails
    * @ param  :
    * @ Description : get admin side emails
    */// ==============================================
    function getAdminEmails($user_id = null, $limit = null)
    {
        $emails = static::select('send_mail.*', 'merchantapplications.company_name as sendor_name')
        		->join('merchantapplications', 'send_mail.sender_id', 'merchantapplications.user_id')
        		->orderBy('send_mail.id', 'desc')
                ->where('send_mail.is_delete_admin', '0');
                if ($user_id != null) {
                    $emails = $emails->where('send_mail.receiver_id', $user_id);
                }
                if ($limit != null) {
                    $emails = $emails->limit($limit);
                }
                $emails = $emails->get();

        return $emails;
    }
}
