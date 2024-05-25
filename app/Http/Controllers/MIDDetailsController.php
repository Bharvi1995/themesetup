<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use URL;
use Session;
use Redirect;
use Input;
use View;
use Auth;
use App\User;
use App\Application;
use App\MIDDetail;

class MIDDetailsController extends HomeController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->user = new User;
        $this->MIDDetail = new MIDDetail;

        $this->moduleTitleS = 'MID Details';
        $this->moduleTitleP = 'front.middetails';

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->main_user_id != 0 && \Auth::user()->is_sub_user == '1')
            $userID = \Auth::user()->main_user_id;
        else
            $userID = \Auth::user()->id;
        $data = $this->user->findData($userID);

        return view($this->moduleTitleP . '.index', compact('data'));
    }

    public function midRateAgree(Request $request)
    {
        $input = $request->except('_token');

        $this->user->where('id', Auth::user()->id)
            ->update(['is_rate_sent' => $input['id'], 'rate_decline_reason' => $input['message']]);

        if ($input['id'] == '3') {
            Application::where('user_id', Auth::user()->id)->update(['status' => '9']);
            $application = Application::where('user_id', Auth::user()->id)->first();
            $notification = [
                'user_id' => '1',
                'sendor_id' => auth()->user()->id,
                'type' => 'admin',
                'title' => 'Fee Schedule Decline',
                'body' => $application->business_name . ' has declined the fee schedule.',
                'url' => '/admin/applications-list/view/' . $application->id,
                'is_read' => '0'
            ];

            addNotification($notification);
        } else {
            Application::where('user_id', Auth::user()->id)->update(['status' => '10']);
            $application = Application::where('user_id', Auth::user()->id)->first();
            $notification = [
                'user_id' => '1',
                'sendor_id' => auth()->user()->id,
                'type' => 'admin',
                'title' => 'Fee Schedule Accepted',
                'body' => $application->business_name . ' has accepted the fee schedule.',
                'url' => '/admin/applications-list/view/' . $application->id,
                'is_read' => '0'
            ];

            addNotification($notification);
        }

        return response()->json([
            'success' => '1',
        ]);
    }
}
