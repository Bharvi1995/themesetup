<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Requests;
use App\AdminLog;
use App\AdminAction;
use App\CronManagements;
use Symfony\Component\Process\Process;

class CronManagementController extends AdminController
{

    public function __construct() {
        parent::__construct();
        $this->AdminLog = new AdminLog;
        $this->AdminAction = new AdminAction;
        $this->CronManagements = new CronManagements;

        $this->moduleTitleS = 'Cron Managements';
        $this->moduleTitleP = 'admin.cronmanagements';

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    public function showAllCron(Request $request) {
        $html = '';
        $cronData = CronManagements::get();
        if (isset($cronData) && count($cronData)):
            $html .='<div class="card-body">
            <div class="col-md-6">';
                foreach ($cronData as $cron):
                    $last_run_at = date("d/m/Y h:i a", strtotime($cron->last_run_at));
                    $diff1 = \Carbon\Carbon::parse($cron->last_run_at)->diffForHumans(null, false, true, 2);
                    if($cron->status == 1){
                        $activ = 'activ'; 
                    }else{ 
                        $activ = 'inactiv'; 
                    } 
                    $html .= '
                    <div class="col-md-8">
                    <p class="stats">
                    <span class="">
                    <i id="status_update_'.$cron->id.'" class="fa fa-circle '.$activ.'" aria-hidden="true"></i>
                    <span id="status_update_text_'.$cron->id.'">';($cron->status == 1)?($html .= 'Active'):($html .= 'Inactive'); 
                    $html .='</span>
                    </span>
                    Run Every 24 hours
                    </p>

                    <h3>ID-'.$cron->id.' : '.$cron->cron_name.'</h3>
                    <p><b>Last Run at</b> : <span id="last_update_date_'.$cron->id.'">'.$last_run_at.'</span> <strong>('.$diff1.')</strong></p>
                    </div>
                    <div class="btn-ar">
                    <button type="button" id="updateButton_'.$cron->id.'" class="';($cron->current_status == 1)?($html.='btn btn-danger'):$html.='btn btn-success'; $html.='" onclick=\'startStopCron("'.$cron->id.'")\' value="'.$cron->current_status.'">';
                    ($cron->current_status == 0)?($html.='Start'):($html.='Stop');
                    $html.='</button>
                    
                    <a href="javascript:;" class="btn btn-info" onclick=\'showCronEditModal("'.$cron->id.'")\'>Edit</a>
                    </div>
                    <p style="min-height:20px">Description: '.$cron->description.'</p>
                
                    ';
                endforeach;
                $html .='</div><div>
            </div>';
        endif;
        echo $html;
    }

    public function index(Request $request) {

        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        return view($this->moduleTitleP . '.index', compact('noList'));
    }

    public function getEditCronForm(Request $request, $user_id="") {
        $id = $request->input('id');
        $crondata = CronManagements::find($id);
        $returnHTML = view('admin.cronmanagements.cron_add_modal')->with('crondata', $crondata)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    public function startStopCron(Request $request) {
        $cronID = $request->get('id');
        $currentStatus = $request->get('current_status');
        $status = 0;
        $responseData = array("success" => 0, "message" => "Request Failed!");
        $currentDateTime = date("Y-m-d H:i:s");
        if($cronID){
            $cronManagements = CronManagements::find($cronID);
            if(!empty($cronManagements)) {
                if($cronManagements->current_status != $currentStatus){
                    $lastRunAt = date("d/m/Y h:i a", strtotime($cronManagements->last_run_at));
                    $message = ($cronManagements->current_status == 0)?"Process already stop":"Process already running";
                    $responseData = array("success" => 2, "message" => $message, "id" => $cronID, "status" => $cronManagements->current_status,"last_run_at" => $lastRunAt);
                } else {
                    if($cronManagements->current_status == 0) {
                        $status = 1;
                        $job = (new \App\Jobs\TransactionDataMiningCron($cronManagements->id))->delay(now()->addSeconds(2));
                        dispatch($job);
                        // $artisanCommand = 'php '.base_path('artisan').' '.$cronManagements->command;
                        // $process = new Process(array($artisanCommand));
                        // $process->setTimeout(0);
                        // $process->disableOutput();
                        // $process->start();
                        // $pid = $process->getPid();
                        $updateData = array(
                            // "last_run_at" => $currentDateTime,
                            // "pid" => $pid,
                            "current_status" => $status
                        );
                        CronManagements::where("id",$cronID)->update($updateData);
                        $lastRunAt = date("d/m/Y h:i a", strtotime($currentDateTime)) ;
                        $responseData = array("success" => 1, "id" => $cronID, "status" => $status, "last_run_at" => $lastRunAt);
                    }else {
                        $status = 0;
                        $commandSearchText = 'artisan'.' '.$cronManagements->command;
                        $currentProcesses = self::getProcessIDsForRunningScript($commandSearchText);
                        $currentProcessesCount = count($currentProcesses);
                        if($currentProcessesCount > 0){
                            // Kill Current Process
                            $artisanCommand = ' php '.base_path('artisan').' '.$cronManagements->command;
                            $process = new Process(array($artisanCommand));
                            $process->setTimeout(0);
                            $process->disableOutput();
                            foreach ($currentProcesses as $key => $currentProcessesValue) {
                                exec(" kill -9 ".$currentProcessesValue);
                            }
                        }
                        $updateData = array( "pid" => '', "current_status" => $status );
                        CronManagements::where("id",$cronID)->update($updateData);

                        $lastRunAt = date("d/m/Y h:i a", strtotime($cronManagements->last_run_at));
                        $responseData = array("success" => 1, "id" => $cronID, "status" => $status, "last_run_at" => $lastRunAt);
                    }
                }
            } else{
                $responseData = array("success" => 0, "message" => "Request Failed!");
            }
        }
        return $responseData;
    }
    
    public function storeAddCron(Request $request) {

        foreach ($request->except('_token') as $data => $value) {
            $valids[$data] = "required";
         }
        
         $request->validate($valids);

        if($request->has('id') && !empty($request->has('id'))) {
            $id = $request->input('id');
            $cronmanagement = CronManagements::find($id);
             $cronmanagement->updated_at = NOW();
        } else {
            $cronmanagement = new CronManagements();
            $cronmanagement->created_at = NOW();
        }
        
        $cronmanagement->days_check = $request->input('days_check');
        $cronmanagement->keywords = json_encode($request->input('keywords'));
       
        if($request->has('id') && !empty($request->has('id'))) {
            $cronmanagement->update();
        } else {
             $cronmanagement->save();
        }
        return response()->json(array('success' => true, 'status' => 200, 'html' => ""), 200);
    }

    public static function getProcessIDsForRunningScript($filename_string) {

        $tmparr = array();
        ob_start();
        system(" ps ax | grep '".trim($filename_string)."'| grep -v 'grep' | awk '{print$1}'");
        $cmdoutput = ob_get_contents();
        ob_end_clean();

        $cmdoutput = trim($cmdoutput);
        if (empty($cmdoutput)) {
            return $tmparr;
        }
        $cmdoutput = preg_replace("#\n#", ",", trim($cmdoutput));
        $tmparr = explode(",", $cmdoutput);
        return $tmparr;
    }
}
