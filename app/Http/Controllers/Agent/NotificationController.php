<?php

namespace App\Http\Controllers\Agent;

use Auth;
use Session;
use Validator;
use App\Notification;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends AgentUserBaseController
{
    // ================================================
	/*  method : __construct
	* @ param  : 
	* @ Description : create instance of the class
	*/// ==============================================
	public function __construct()
	{
		parent::__construct();
		$this->notification = new Notification;
	}

    // ================================================
    /*  method : notifications
    * @ param  : 
    * @ Description : get users all notifications
    */// ==============================================
    public function notifications()
    {
        if(Auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = Auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = Auth()->guard('agentUser')->user()->main_agent_id;
        }

		$where  ['user_id'] = $agentId;
		$where  ['type'] = 'RP';
		
		$notifications = $this->notification::where($where)->orderBy('created_at','DESC')->get();

    	return view('agent.notifications.index', compact('notifications'));
    }

    // ================================================
    /*  method : readNotifications
    * @ param  : 
    * @ Description : read all notifications of user
    */// ==============================================
    public function readNotifications(Request $request,$id)
    {
    	Notification::where('id', $id)
    			->update(['is_read' => 1]);

        $notifications = Notification::where('id', $id)->first();     
    			
        return view('agent.notifications.show', compact('notifications'));
    }
}
