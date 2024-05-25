<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Redirect;
use App\Notification;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->notification = new Notification;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function adminNotifications()
    {
		$where  ['user_id'] = '1';
		$where  ['type'] = 'admin';
		
		$notifications = $this->notification::where($where)->orderBy('created_at','DESC')->get();

    	return view('admin.notifications.index', compact('notifications'));
    }

    
    // ================================================
    /*  method : readNotifications
    * @ param  : 
    * @ Description : read all notifications of user
    */// ==============================================
    public function readAdminNotifications(Request $request,$id)
    {
    	Notification::where('id', $id)
    			->update(['is_read' => 1]);

        $notifications = Notification::where('id', $id)->first();     
    			
        return view('admin.notifications.show', compact('notifications'));
    }
}
