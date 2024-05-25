<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Transaction;
use App\AdminAction;
use App\User;
use App\Application;
use App\AgentPayoutReport;
use App\Agent;
use App\AgentPayoutReportChild;
use Log;
use DB;

class ReferralReportCronWithArg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referralreport:cron {days?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto generate referral partner report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $days = ($this->argument('days') > 0) ? $this->argument('days') : 6;
        $current_date = date("y-m-d");
        $start_date = date('Y-m-d', strtotime("-$days days"));
        $agents = DB::table('agents')->whereNULL('deleted_at')->get(['id', 'name', 'email']);
        
        if( !empty($agents) ){
            foreach($agents as $agent){

                $companyName = Application::select('transactions.user_id', 'applications.business_name', 'users.agent_id')
                ->join('users', 'applications.user_id', '=', 'users.id')
                ->join('transactions', 'transactions.user_id', '=', 'applications.user_id')
                ->where('users.agent_id', $agent->id)
                ->groupBy('transactions.user_id')->first();

                if(!empty($companyName)){

                    $data = [];
                    $data['report_no'] = rand(1111, 9999) . time();
                    $data['agent_id'] = $agent->id;
                    $data['agent_name'] = $agent->name;
                    $data['user_id'] = $companyName->user_id;
                    $data['company_name'] = $companyName->business_name;
                    $data['date'] = date('d/m/Y', time());
                    $data['start_date'] = $start_date;
                    $data['end_date'] = $current_date;
                    $reportId =  AgentPayoutReport::create($data);

                    $ReportData = DB::table('transactions as trans')
                        ->select(
                            'trans.currency',
                            'users.agent_commission as commission',
                            DB::raw('SUM(trans.amount) as success_amount'),
                            DB::raw('COUNT(trans.user_id) as successCount'),
                            DB::raw('round(((users.agent_commission * SUM(trans.amount))/100),2) as totalCommission')
                        )
                        ->join('users', 'trans.user_id', '=', 'users.id')
                        ->join('agents', 'users.agent_id', '=', 'agents.id')
                        ->join('applications as app', 'users.id', '=', 'app.user_id')
                        ->where('app.user_id', $companyName->user_id)
                        ->where('agents.id', $agent->id)
                        ->where('trans.status', '1')
                        ->where("trans.refund", "0")
                        ->where("trans.chargebacks", "0")
                        ->where("trans.is_flagged", "0")
                        ->where("trans.is_retrieval", "0")
                        ->whereNotIn('trans.payment_gateway_id', ['1','2'])
                        ->whereNull('app.deleted_at')
                        ->whereNull('trans.deleted_at')
                        ->whereNull('agents.deleted_at')
                        ->groupBy('agents.id', 'trans.currency')
                        ->get();

                        foreach ($ReportData as  $value) {
                            $childData['report_id'] = $reportId->id;
                            $childData['currency'] = $value->currency;
                            $childData['success_amount'] = $value->success_amount;
                            $childData['success_count'] = $value->successCount;
                            $childData['commission_percentage'] = $value->commission;
                            $childData['total_commission'] = $value->totalCommission;
                            AgentPayoutReportChild::create($childData);
                        }
                }
            }
            exit("Report created : " . $start_date ."====".$current_date);
        }
    }
}
