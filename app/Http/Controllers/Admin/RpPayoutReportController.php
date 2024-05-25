<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\AdminAction;
use App\User;
use App\Application;
use App\AgentPayoutReport;
use App\Agent;
use App\AgentPayoutReportChild;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RpReportExport;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Mail\ShowReportAgent;
use Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RpPayoutReportController extends Controller
{
    public function agentReport(Request $request)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        $TransactionSummary = DB::table("transactions as trans")
            ->select(
                'agents.name as agent_name',
                'trans.currency',
                'trans.user_id',
                'users.name as user_name',
                'agents.add_buy_rate as add_buy_rate',
                'agents.add_buy_rate_master as add_buy_rate_master',
                'agents.add_buy_rate_amex as add_buy_rate_amex',
                'agents.add_buy_rate_discover as add_buy_rate_discover',
                'users.agent_commission as commission',
                'users.agent_commission_master_card as master_commission',
                'users.agent_commission as amex_commission',
                'users.agent_commission as discover_commission',
                DB::raw('SUM(IF(trans.`card_type` = 3,1, 0)) AS MasterSuccessCount'),
                DB::raw('SUM(IF(trans.`card_type` = 1,1, 0)) AS AmexSuccessCount'),
                DB::raw('SUM(IF(trans.`card_type` = 4,1, 0)) AS DiscoverSuccessCount'),
                DB::raw('SUM(IF(trans.`card_type` NOT IN (1,3,4),1, 0)) AS OtherSuccessCount'),
                DB::raw('SUM(IF(trans.`card_type` = 3,trans.amount, 0)) AS MasterSuccessAmount'),
                DB::raw('SUM(IF(trans.`card_type` = 1,trans.amount, 0)) AS AmexSuccessAmount'),
                DB::raw('SUM(IF(trans.`card_type` = 4,trans.amount, 0)) AS DiscoverSuccessAmount'),
                DB::raw('SUM(IF(trans.`card_type` NOT IN (1,3,4),trans.amount, 0)) AS OtherSuccessAmount'),
            )
            ->join('users', 'trans.user_id', '=', 'users.id')
            ->join('agents', 'users.agent_id', '=', 'agents.id')
            ->where('trans.status', '1')
            ->where("trans.refund", "0")
            ->where("trans.chargebacks", "0")
            ->where("trans.is_flagged", "0")
            ->where("trans.is_retrieval", "0")
            ->whereNotIn('trans.payment_gateway_id', $payment_gateway_id)
            ->whereNull('trans.deleted_at')
            ->whereNull('agents.deleted_at')
            ->whereNull('users.deleted_at')
            ->groupBy('trans.user_id')
            ->groupBy('trans.currency')
            ->orderBy('MasterSuccessAmount', 'desc');
        if ($request->agent_id) {
            $TransactionSummary = $TransactionSummary->where('users.agent_id', $request->agent_id);
        }
        if ($request->user_id) {
            $TransactionSummary = $TransactionSummary->where('trans.user_id', $request->user_id);
        }
        if ($request->start_date) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $TransactionSummary = $TransactionSummary->where('trans.created_at', '>=', $start_date . " 00:00:00");
        }
        if ($request->end_date) {
            $end_date = date('Y-m-d', strtotime($request->end_date));
            $TransactionSummary = $TransactionSummary->where('trans.created_at', '<=', $end_date . " 23:59:59");
        }
        $TransactionSummary = $TransactionSummary->get()->toArray();

        $arr_t_data = array();
        if (!empty($TransactionSummary)) {
            foreach ($TransactionSummary as $k => $v) {
                $arr_t_data[$v->user_id][] = $v;
            }
        }

        $agents = DB::table('agents')->whereNULL('deleted_at')->where('main_agent_id', '0')->get(['id', 'name', 'email']);
        return view("admin.payoutReport.agent_reports", compact('agents', 'TransactionSummary', 'arr_t_data'));
    }

    public function getCompanyByAgent(Request $request)
    {
        $id = $request->id;
        $userIds = User::where('agent_id', $id)->pluck('id');
        $companyName = Application::select('user_id', 'business_name')->whereIn('user_id', $userIds)->get();
        return response()->json(['status' => 200, 'companyName' => $companyName]);
    }

    public function generateAgentReport(Request $request)
    {

        $payoutReports = new AgentPayoutReport;
        if ($request->agent_id) {
            $payoutReports = $payoutReports->where('agent_id', $request->agent_id);
        }
        if ($request->user_id) {
            $payoutReports = $payoutReports->where('user_id', $request->user_id);
        }
        if ($request->is_paid) {
            $payoutReports = $payoutReports->where('is_paid', $request->is_paid);
        }
        $payoutReports = $payoutReports->orderBy('id', 'desc')->get();
        $agents = DB::table('agents')->whereNULL('deleted_at')->where('main_agent_id', '0')->get(['id', 'name', 'email']);
        return view("admin.payoutReport.generate_agent_reports", compact('agents', 'payoutReports'));
    }

    public function storeAgentReport(Request $request)
    {
        $payload = $request->validate(
            [
                'agent' => 'required',
                'user_id' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
            ],
            [
                'agent.required' => 'This field is required.',
                'user_id.required' => 'This field is required.',
                'start_date.required' => 'This field is required.',
                'end_date.required' => 'This field is required.',
            ]
        );
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];
        $agent = Agent::find($payload['agent']);
        $startDate = date('Y-m-d', strtotime($request->start_date));
        $endDate = date('Y-m-d', strtotime($request->end_date));

        DB::beginTransaction();

        try {
            foreach ($payload["user_id"] as $user_id) {
                $application = Application::select("id", "business_name")->where('user_id', $user_id)->first();
                $data = [];
                $data['report_no'] = rand(1111, 9999) . time();
                $data['agent_id'] = $payload['agent'];
                $data['agent_name'] = $agent->name;
                $data['user_id'] = $user_id;
                $data['company_name'] = $application->business_name;
                $data['date'] = date('d/m/Y', time());
                $data['start_date'] = date('Y-m-d', strtotime($request->start_date));
                $data['end_date'] = date('Y-m-d', strtotime($request->end_date));


                $reportId = AgentPayoutReport::create($data);
                addAdminLog(AdminAction::GENERATE_REFERRAL_PARTNER_REPORT, $reportId->id, $data, "Referral Partner Report Generated Successfully!");

                $ReportDataOther = DB::table('transactions as trans')
                    ->select(
                        'trans.currency',
                        DB::raw('users.agent_commission as commission'),
                        DB::raw('SUM(trans.amount) as success_amount'),
                        DB::raw('COUNT(trans.user_id) as successCount'),
                        DB::raw('round((( (users.agent_commission) * SUM(trans.amount))/100),2) as totalCommission')
                    )
                    ->join('users', 'trans.user_id', '=', 'users.id')
                    ->join('agents', 'users.agent_id', '=', 'agents.id')
                    ->join('applications as app', 'users.id', '=', 'app.user_id')
                    ->where('app.user_id', $user_id)
                    ->where('agents.id', $payload['agent'])
                    ->where('trans.status', '1')
                    // ->where("trans.refund", "0")
                    // ->where("trans.chargebacks", "0")
                    // ->where("trans.is_flagged", "0")
                    // ->where("trans.is_retrieval", "0")
                    ->whereNotIn("trans.card_type", ["1", "3", "4"])
                    ->where(DB::raw('DATE(trans.created_at)'), '>=', $startDate)
                    ->where(DB::raw('DATE(trans.created_at)'), '<=', $endDate)
                    ->whereNotIn('trans.payment_gateway_id', $payment_gateway_id)
                    ->whereNull('app.deleted_at')
                    ->whereNull('trans.deleted_at')
                    ->whereNull('agents.deleted_at')
                    ->groupBy('agents.id', 'trans.currency')
                    ->get();
                $ReportDataMasterCard = DB::table('transactions as trans')
                    ->select(
                        'trans.currency',
                        DB::raw('users.agent_commission_master_card as commission'),
                        DB::raw('SUM(trans.amount) as success_amount'),
                        DB::raw('COUNT(trans.user_id) as successCount'),
                        DB::raw('round((( (users.agent_commission_master_card) * SUM(trans.amount))/100),2) as totalCommission')
                    )
                    ->join('users', 'trans.user_id', '=', 'users.id')
                    ->join('agents', 'users.agent_id', '=', 'agents.id')
                    ->join('applications as app', 'users.id', '=', 'app.user_id')
                    ->where('app.user_id', $user_id)
                    ->where('agents.id', $payload['agent'])
                    ->where('trans.status', '1')
                    // ->where("trans.refund", "0")
                    // ->where("trans.chargebacks", "0")
                    // ->where("trans.is_flagged", "0")
                    // ->where("trans.is_retrieval", "0")
                    ->where("trans.card_type", "=", "3")
                    ->where(DB::raw('DATE(trans.created_at)'), '>=', $startDate)
                    ->where(DB::raw('DATE(trans.created_at)'), '<=', $endDate)
                    ->whereNotIn('trans.payment_gateway_id', $payment_gateway_id)
                    ->whereNull('app.deleted_at')
                    ->whereNull('trans.deleted_at')
                    ->whereNull('agents.deleted_at')
                    ->groupBy('agents.id', 'trans.currency')
                    ->get();

                $ReportDataAmexCard = DB::table('transactions as trans')
                    ->select(
                        'trans.currency',
                        DB::raw('users.agent_commission as commission'),
                        DB::raw('SUM(trans.amount) as success_amount'),
                        DB::raw('COUNT(trans.user_id) as successCount'),
                        DB::raw('round((( (users.agent_commission) * SUM(trans.amount))/100),2) as totalCommission')
                    )
                    ->join('users', 'trans.user_id', '=', 'users.id')
                    ->join('agents', 'users.agent_id', '=', 'agents.id')
                    ->join('applications as app', 'users.id', '=', 'app.user_id')
                    ->where('app.user_id', $user_id)
                    ->where('agents.id', $payload['agent'])
                    ->where('trans.status', '1')
                    // ->where("trans.refund", "0")
                    // ->where("trans.chargebacks", "0")
                    // ->where("trans.is_flagged", "0")
                    // ->where("trans.is_retrieval", "0")
                    ->where("trans.card_type", "=", "1")
                    ->where(DB::raw('DATE(trans.created_at)'), '>=', $startDate)
                    ->where(DB::raw('DATE(trans.created_at)'), '<=', $endDate)
                    ->whereNotIn('trans.payment_gateway_id', $payment_gateway_id)
                    ->whereNull('app.deleted_at')
                    ->whereNull('trans.deleted_at')
                    ->whereNull('agents.deleted_at')
                    ->groupBy('agents.id', 'trans.currency')
                    ->get();

                $ReportDataDiscoverCard = DB::table('transactions as trans')
                    ->select(
                        'trans.currency',
                        DB::raw('users.agent_commission as commission'),
                        DB::raw('SUM(trans.amount) as success_amount'),
                        DB::raw('COUNT(trans.user_id) as successCount'),
                        DB::raw('round((( (users.agent_commission) * SUM(trans.amount))/100),2) as totalCommission')
                    )
                    ->join('users', 'trans.user_id', '=', 'users.id')
                    ->join('agents', 'users.agent_id', '=', 'agents.id')
                    ->join('applications as app', 'users.id', '=', 'app.user_id')
                    ->where('app.user_id', $user_id)
                    ->where('agents.id', $payload['agent'])
                    ->where('trans.status', '1')
                    // ->where("trans.refund", "0")
                    // ->where("trans.chargebacks", "0")
                    // ->where("trans.is_flagged", "0")
                    // ->where("trans.is_retrieval", "0")
                    ->where("trans.card_type", "=", "4")
                    ->where(DB::raw('DATE(trans.created_at)'), '>=', $startDate)
                    ->where(DB::raw('DATE(trans.created_at)'), '<=', $endDate)
                    ->whereNotIn('trans.payment_gateway_id', $payment_gateway_id)
                    ->whereNull('app.deleted_at')
                    ->whereNull('trans.deleted_at')
                    ->whereNull('agents.deleted_at')
                    ->groupBy('agents.id', 'trans.currency')
                    ->get();

                // * Fetch the rates  of currencies
                $response = Http::get('https://apilayer.net/api/live?access_key=' . config("custom.currency_converter_access_key"))->json();
                $rates = $response['quotes'];

                foreach ($ReportDataOther as $value) {
                    $childData['report_id'] = $reportId->id;
                    $childData['currency'] = $value->currency;
                    $childData["card_type"] = "Other";
                    $childData['success_amount'] = $value->success_amount;
                    $childData['success_count'] = $value->successCount;
                    $childData['commission_percentage'] = $value->commission;
                    $childData['total_commission'] = $value->totalCommission;
                    $successAmountConverted = getConversionAmountInUsd($rates, $value->currency, $value->success_amount);
                    $percentageAmountConverted = getConversionAmountInUsd($rates, $value->currency, $value->totalCommission);
                    $childData['success_amount_in_usd'] = $successAmountConverted;
                    $childData['total_commission_in_usd'] = $percentageAmountConverted;
                    AgentPayoutReportChild::create($childData);
                }
                foreach ($ReportDataMasterCard as $vMaster) {
                    $childDataMasterCard['report_id'] = $reportId->id;
                    $childDataMasterCard['currency'] = $vMaster->currency;
                    $childDataMasterCard["card_type"] = "MasterCard";
                    $childDataMasterCard['success_amount'] = $vMaster->success_amount;
                    $childDataMasterCard['success_count'] = $vMaster->successCount;
                    $childDataMasterCard['commission_percentage'] = $vMaster->commission;
                    $childDataMasterCard['total_commission'] = $vMaster->totalCommission;
                    $successAmountConverted = getConversionAmountInUsd($rates, $vMaster->currency, $vMaster->success_amount);
                    $percentageAmountConverted = getConversionAmountInUsd($rates, $vMaster->currency, $vMaster->totalCommission);
                    $childDataMasterCard['success_amount_in_usd'] = $successAmountConverted;
                    $childDataMasterCard['total_commission_in_usd'] = $percentageAmountConverted;
                    AgentPayoutReportChild::create($childDataMasterCard);
                }

                foreach ($ReportDataAmexCard as $vAmex) {
                    $childDataAmexCard['report_id'] = $reportId->id;
                    $childDataAmexCard['currency'] = $vAmex->currency;
                    $childDataAmexCard["card_type"] = "AmexCard";
                    $childDataAmexCard['success_amount'] = $vAmex->success_amount;
                    $childDataAmexCard['success_count'] = $vAmex->successCount;
                    $childDataAmexCard['commission_percentage'] = $vAmex->commission;
                    $childDataAmexCard['total_commission'] = $vAmex->totalCommission;
                    $successAmountConverted = getConversionAmountInUsd($rates, $vAmex->currency, $vAmex->success_amount);
                    $percentageAmountConverted = getConversionAmountInUsd($rates, $vAmex->currency, $vAmex->totalCommission);
                    $childDataAmexCard['success_amount_in_usd'] = $successAmountConverted;
                    $childDataAmexCard['total_commission_in_usd'] = $percentageAmountConverted;
                    AgentPayoutReportChild::create($childDataAmexCard);
                }

                foreach ($ReportDataDiscoverCard as $vDiscover) {
                    $childDataDiscoverCard['report_id'] = $reportId->id;
                    $childDataDiscoverCard['currency'] = $vDiscover->currency;
                    $childDataDiscoverCard["card_type"] = "DiscoverCard";
                    $childDataDiscoverCard['success_amount'] = $vDiscover->success_amount;
                    $childDataDiscoverCard['success_count'] = $vDiscover->successCount;
                    $childDataDiscoverCard['commission_percentage'] = $vDiscover->commission;
                    $childDataDiscoverCard['total_commission'] = $vDiscover->totalCommission;
                    $successAmountConverted = getConversionAmountInUsd($rates, $vDiscover->currency, $vDiscover->success_amount);
                    $percentageAmountConverted = getConversionAmountInUsd($rates, $vDiscover->currency, $vDiscover->totalCommission);
                    $childDataDiscoverCard['success_amount_in_usd'] = $successAmountConverted;
                    $childDataDiscoverCard['total_commission_in_usd'] = $percentageAmountConverted;
                    AgentPayoutReportChild::create($childDataDiscoverCard);
                }
            }
            DB::commit();
            return back()->with('success', 'RP payout report generated successfully!');
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error(["rp-report-generate-err" => $err]);
            return back()->with('error', 'Something went wrong.please try again later!');
        }


    }

    public function agentReportExcel(Request $request)
    {
        $ArrRequest = !empty($request->ids) ? ['id' => implode(',', $request->ids)] : '';
        addAdminLog(AdminAction::REFERRAL_PARTNER_DOWNLOAD_EXCEL, null, $ArrRequest, "Referral Partner Report Download Excel File");

        return (new RpReportExport($request->ids))->download();
        //        return Excel::download(new RpReportExport($request->ids), 'RP_Report_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function deleteAgentReport(Request $request)
    {
        $allID = $request->get('id');
        foreach ($allID as $value) {
            AgentPayoutReport::where('id', $value)->delete();
            AgentPayoutReportChild::where('report_id', $value)->delete();
        }
        $ArrRequest = ['id' => implode(",", $allID)];
        addAdminLog(AdminAction::REFERRAL_PARTNER_DELETE, null, $ArrRequest, "Referral Partner Report Deleted");
        return response()->json([
            'success' => true,
        ]);
    }

    public function changeIsPaidStatus(Request $request)
    {
        $id = $request->id;
        AgentPayoutReport::where('id', $id)->update(['is_paid' => $request->is_paid]);
        $ArrRequest = ['is_paid' => $request->is_paid];
        $msg = ($request->is_paid == 1) ? "Referral Partner Report Make Paid" : "Referral Partner Report Make Un-Paid";
        addAdminLog(AdminAction::REFERRAL_PARTNER_PAID, $id, $ArrRequest, $msg);
        return response()->json(['status' => 200]);
    }

    public function uploadRPDocument(Request $request)
    {
        $request->validate([
            'files' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx|max:35840',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));
        $arr = [];
        if ($request->hasFile('files')) {
            $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageName = $imageName . '.' . $request->file('files')->getClientOriginalExtension();
            $filePath = 'uploads/generated_rp_report/' . $imageName;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('files')->getRealPath()));
            $arr['files'] = $filePath;
        } else {
            $arr['files'] = '';
        }
        $files = AgentPayoutReport::find($request->report_id);
        if ($files->files == null) {
            $arr['files'] = json_encode([$arr['files']]);
        } else {
            $files = json_decode($files->files);
            array_push($files, $arr['files']);
            $arr['files'] = json_encode($files);
        }
        $ArrRequest = $arr;
        addAdminLog(AdminAction::REFERRAL_PARTNER_UPLOAD_FILES, $request->report_id, $ArrRequest, "File Uploaded Successfully!");
        AgentPayoutReport::find($request->report_id)->update(['files' => $arr['files']]);
        notificationMsg('success', 'File Uploaded Successfully!');
        return redirect()->back();
    }

    public function changeClientSideStatus(Request $request)
    {
        $id = $request->id;
        if ($request->client_side == 1) {
            $data = AgentPayoutReport::where('id', $id)->with('childData')->first();
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);
            $html = view('admin.payoutReport.show_generate_rp_report', compact('data'))->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $fileName = str_replace('/', '-', $data->date) . '-' . $data->company_name . '-' . $data->id . 'pdf';
            Storage::disk('public')->put("pdf/" . $fileName, $dompdf->output());
            $agent = Agent::find($data->agent_id);
            //$agent->email
            Mail::to('test@gmail.com')->send(new ShowReportAgent($fileName));
            unlink(storage_path('app/public/pdf/' . $fileName));
            $ArrRequest = ['show_agent_side' => $request->client_side];
            addAdminLog(AdminAction::REFERRAL_PARTNER_SHOW, $id, $ArrRequest, "Referral Partner Report show to client");
        } else {
            $ArrRequest = ['show_agent_side' => $request->client_side];
            addAdminLog(AdminAction::REFERRAL_PARTNER_SHOW, $id, $ArrRequest, "Referral Partner Report can't show to client");
        }
        AgentPayoutReport::where('id', $id)->update(['show_agent_side' => $request->client_side]);
        return response()->json(['status' => 200]);
    }

    public function showAgentreport(Request $request, $id)
    {
        $data = AgentPayoutReport::where('id', $id)->with('childData')->first();
        return view('admin.payoutReport.show_generate_rp_report', compact('data'));
    }

    public function getAgentreportPdf(Request $request, $id)
    {
        $ArrRequest = [];
        addAdminLog(AdminAction::REFERRAL_PARTNER_GENERATE_PDF, $id, $ArrRequest, "Referral Partner Report Generate PDF");
        $data = AgentPayoutReport::where('id', $id)->with('childData')->first();
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $html = view('admin.payoutReport.show_generate_rp_report', compact('data'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return $dompdf->stream(str_replace('/', '-', $data->date) . '-' . $data->company_name . '- Payout Report' . '.pdf');
    }
}