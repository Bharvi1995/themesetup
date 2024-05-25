<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationAssignToBank extends Model
{
    use SoftDeletes;

    protected $table = 'application_assign_to_bank';
    protected $guarded = array();

    protected $fillable = [
        'application_id',
        'bank_user_id',
        'status',
        'declined_reason',
        'referred_reply',
        'referred_note_reply',
        'extra_documents'
    ];

    public function storeData($input)
    {
    	return static::create($input);
    }

    public function findData($applications_id,$bank_user_id)
    {
        return static::where('application_id',$applications_id)->where('bank_user_id',$bank_user_id)->first();
    }

    public function applicationDeclined($applications_id, $bank_user_id, $declined_reason)
    {
        return static::where(['application_id' => $applications_id, 'bank_user_id' => $bank_user_id])->update(['status' => '2','declined_reason'=>$declined_reason]);
    }

    public function applicationReferred($applications_id, $bank_user_id, $referred_note)
    {
        return static::where(['application_id' => $applications_id, 'bank_user_id' => $bank_user_id])->update(['status' => '3','referred_note'=>$referred_note]);
    }

    public function applicationReferredReply($applications_id, $bank_user_id, $referred_note_reply, $extra_documents)
    {
        return static::where(['application_id' => $applications_id, 'bank_user_id' => $bank_user_id])->update(['status' => '3','referred_note_reply'=>$referred_note_reply, 'extra_documents' => $extra_documents]);
    }

    public function applicationApproved($applications_id, $bank_user_id)
    {
    	return static::where(['application_id' => $applications_id, 'bank_user_id' => $bank_user_id])->update(['status' => '1']);
    }
}
