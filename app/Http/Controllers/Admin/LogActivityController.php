<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\AdminController;
use Hash;
use Input;
use Validator;
use App\LogActivity;

class LogActivityController extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->LogActivity = new LogActivity;

        $this->moduleTitleS = 'Log Activity';
        $this->moduleTitleP = 'admin.logActivity';

        view()->share('moduleTitleP',$this->moduleTitleP);
        view()->share('moduleTitleS',$this->moduleTitleS);
    }
    
    public function index(Request $request)
    {
        return view($this->moduleTitleP.'.index');                    
    }

    public function getLogActivity()
    {
        $data = $this->LogActivity->getData();

        return \DataTables::of($data)
            ->addColumn('company_name', function($data) {
                if($data->user_id == 0)
                    return '---------------';
                else
                    return $data->company_name;
            })
            ->addColumn('Actions', function($data) {
                return '<a href="'.\URL::route('admin-log-activity-show',[$data->id]).'" class="btn btn-icon waves-effect waves-light btn-info"><i class="fa fa-eye"></i></a>';
            })
            ->rawColumns(['Actions', 'company_name'])
            ->make(true);
    }

    public function show($id)
    {
        $log = $this->LogActivity->getDataById($id);

        return view($this->moduleTitleP.'.show', compact('log'));
    }

    public function logActivityByUser($id)
    {
        $data = $this->LogActivity->getDataByUser($id);
        
        return view($this->moduleTitleP.'.indexByUser', compact('id','data'));
    }

    public function getLogActivityByUser(Request $request)
    {
        $id = $request->get('user_id');
        
        $data = $this->LogActivity->getDataByUser($id);
        
        return \DataTables::of($data)
            ->addColumn('Actions', function($data) {
                return '<a href="'.\URL::route('admin-log-activity-show',[$data->id]).'" class="btn btn-icon waves-effect waves-light btn-info"><i class="fa fa-eye"></i></a>';
            })
            ->rawColumns(['Actions'])
            ->make(true);
    }

    public function distroy(Request $request)
    {
        $old_day = date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date("m") , date("d") - 10, date("Y")));

        $this->LogActivity->distroyData($old_day);

        notificationMsg('success','Delete Logs Successfully!');

        return redirect()->route('admin-log-activity');
    }
}