<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Str;
use App\Http\Requests;
use Hash;
use Validator;
use App\AdminLog;
use App\AdminAction;
use App\Admin;
use Mail;
use URL;
use Auth;
use Storage;
use DB;
use Exception;

class AdminLogsController extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->AdminLog = new AdminLog;
        $this->AdminAction = new AdminAction;
        $this->Admin = new Admin;

        $this->moduleTitleS = 'Admin Logs';
        $this->moduleTitleP = 'admin.logs';

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->AdminLog->getData($input, $noList);        
        $actionList = $this->AdminAction->getData();
        $adminList = $this->Admin->getData();
        return view($this->moduleTitleP . '.index', compact('data', 'noList', 'actionList', 'adminList'));
    }

    public function show($id){
        $data = $this->AdminLog::where('id',$id)->first();
        $json = json_decode($data->request);
        return view($this->moduleTitleP.'.show', compact('data','json'));
    }

    public function downloadLog()
    {
        $file = storage_path(). "/logs/laravel.log";

        if (file_exists($file)) {
            $headers = [
                'Content-Type' => 'application/text',
            ];
            
            return response()->download($file, 'testpay.log', $headers);
        } else {
            return response()->json(['error' => 'File not found.']);
        }
    }
}
