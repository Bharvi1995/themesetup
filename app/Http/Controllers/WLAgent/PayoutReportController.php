<?php
namespace App\Http\Controllers\WLAgent;
use Illuminate\Http\Request;
use App\PayoutReportsRP;
use Auth;
use App\PayoutReportsChildRP;
use Dompdf\Dompdf;
use Dompdf\Options;
class PayoutReportController extends WLAgentUserBaseController
{
	public function __construct() {
        view()->share('WLAgentUserTheme', 'layouts.WLAgent.default');
        $this->PayoutReportsRP = new PayoutReportsRP;
        $this->PayoutReportsChildRP = new PayoutReportsChildRP;
    }

    public function getPayoutReport(Request $request) {
    	$input = \Arr::except($request->all(),array('_token', '_method'));
        $input["user_id"] = Auth::user()->id;
        $input["show_client_side"] = 1;
        if(isset($input['noList'])){
            $noList = $input['noList'];
        }else{
            $noList = 10;
        }
        $dataT = $this->PayoutReportsRP->getAllReportData($noList, $input);
        return view('WLAgent.report.payout_report')->with(['data'=>$dataT]);
    }

    public function generatePDF($id) {
        $data = $this->PayoutReportsRP->findData($id);
        if($data->user_id != \Auth::user()->id){
            return redirect()->back();
        }
        $childData = $this->PayoutReportsChildRP->findDataByReportID($id);
        $totalFlagged = \DB::table('payout_reports_child_rp')->where('payoutreport_id', $id)->sum('flagged_transaction_count');
        $start_date = $data->start_date;
        $start_date = str_replace('/', '-', $start_date);
        if(date('d',strtotime($start_date)) < 8) {
            $annual_fee = \DB::table('users')->select('annual_fee')->where('id', $data->user_id)->first()->annual_fee;
         } else {
            $annual_fee = 0;
         }
        view()->share('data',$data);
        view()->share('childData',$childData);
        view()->share('annual_fee',$annual_fee);
        view()->share('totalFlagged',$totalFlagged);
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('admin.payoutReport.show_report_PDF'));
        $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');
        $dompdf->render();
        \DB::table('payout_reports_rp')->where('id', $id)->update(['is_download' => '1']);
        $dompdf->stream(str_replace('/','-',$data->date).'-'.$data->company_name.'-'.$data->id.'-'.$data->processor_name.'.pdf');
    }

    public function show($id) {
        $data = $this->PayoutReportsRP->findData($id);
        if(!$data){
            return redirect()->route('wl-payout-report');
        }
        if($data && $data->user_id != \Auth::user()->id){
            return redirect()->back();
        }
        $childData = $this->PayoutReportsChildRP->findDataByReportID($id);
        $totalFlagged = \DB::table('payout_reports_child_rp')->where('payoutreport_id', $id)->sum('flagged_transaction_sum');
        $start_date = $data->start_date;
        $start_date = str_replace('/', '-', $start_date);
        
        if (date('d', strtotime($start_date)) < 8) {
            $annual_fee = \DB::table('users')->select('annual_fee')->where('id', $data->user_id)->first()->annual_fee;
        } else {
            $annual_fee = 0;
        }
        view()->share('data', $data);
        view()->share('childData', $childData);
        view()->share('annual_fee', $annual_fee);
        view()->share('totalFlagged', $totalFlagged);
        return view('admin.payoutReport.show_report_PDF', compact('data', 'childData', 'totalFlagged'));
    }
}
