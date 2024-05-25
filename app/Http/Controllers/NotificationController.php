<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use Validator;
use App\FirebaseDeviceToken;
use App\Notification;
use Illuminate\Http\Request;

class NotificationController extends HomeController
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
		$this->Firebase_device_token = new FirebaseDeviceToken;
	}

    // ================================================
    /*  method : sendFirebaseToken
    * @ param  : 
    * @ Description : save firebase device token to database
    */// ==============================================
    public function sendFirebaseToken(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'token' => 'required',
	    ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

		if (Session::get('firebase_token_save') != 'true') {
	        $firebase_notification = FirebaseDeviceToken::updateOrCreate(
			    ['user_id' => $request->user_id, 'type' => 'user'],
			    ['token' => $request->token]
			);

			Session::put('firebase_token_save', 'true');
			
			return response()->json([
				'success' => true,
				'message' => 'token saved.'
			]);
		}
    }

    // ================================================
    /*  method : sendFirebaseNotification
    * @ param  : 
    * @ Description : create firebase notification and send to user
    */// ==============================================
    public function sendFirebaseNotification(Request $request)
    {
    	// this array key should be included
    	$primary_array = [
    		'user_id' => 1,
    		'sendor_id' => Auth::guard('web')->user()->id,
    		'type' => 'admin', //or user
    		'title' => 'New Message',
    		'body' => 'New message arrived',
    	];

    	// this array adds more details send over firebase
    	$secondary_array = [
    		'click_action' => 'localhost:8000/refunds',
    	];

        // trigger push notication
        sendFirebaseNotification($primary_array, $secondary_array);

        // save to firebase database
        saveToFirebaseDatabase($primary_array, $secondary_array);
    }

    // ================================================
    /*  method : notifications
    * @ param  : 
    * @ Description : get users all notifications
    */// ==============================================
    public function notifications()
    {
        if(\Auth::user()->main_user_id != '0')
            $userID = \Auth::user()->main_user_id;
        else
            $userID = \Auth::user()->id;

		$where  ['user_id'] = $userID ;
		$where  ['type'] = 'user';
		
		$notifications = $this->notification::where($where)->orderBy('created_at','DESC')->get();

    	return view('front.notifications.index', compact('notifications'));
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
    			
        return view('front.notifications.show', compact('notifications'));
    }
}
