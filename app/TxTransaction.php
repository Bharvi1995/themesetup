<?php

namespace App;

use DB;
use Auth;
use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class TxTransaction extends Model
{
    protected $table = 'tx_transactions';
    protected $guarded = []; 

    protected $fillable = [
    	'user_id', 'company_name', 'currency', 'agent_id', 'payment_gateway_id', 'status', 'TXs', 'TXsP', 'VOLs', 'TXd', 'TXdP', 'VOLd', 'CBTX', 'CBTXP', 'CBV', 'REFTX', 'REFTXP', 'REFV', 'FLGTX', 'FLGTXP', 'FLGV', 'RETTX', 'RETTXP', 'RETV', 'TXb', 'TXbP', 'VOLb', 'transaction_date',
    ];

    public function getTransactionSummaryReport($input)
    {
        $data = static::select(
            'currency',
            DB::raw("SUM(TXs) as success_count"),
            DB::raw("SUM(VOLs) AS success_amount"),
            DB::raw("(SUM(TXs)*100)/(SUM(TXs)+SUM(TXd)) AS success_percentage"),
            DB::raw("SUM(TXd) as declined_count"),
            DB::raw("SUM(VOLd) AS declined_amount"),
            DB::raw("(SUM(TXd)*100)/(SUM(TXs)+SUM(TXd)) AS declined_percentage"),
            DB::raw("SUM(CBTX) AS chargebacks_count"),
            DB::raw("SUM(CBV) AS chargebacks_amount"),
            DB::raw("SUM(CBTX)*100 / SUM(TXs) AS chargebacks_percentage"),
            DB::raw("SUM(FLGTX) AS flagged_count"),
            DB::raw("SUM(FLGV) AS flagged_amount"),
            DB::raw("SUM(FLGTX) / SUM(TXs) AS flagged_percentage"),
            DB::raw("SUM(REFTX) AS refund_count"),
            DB::raw("SUM(REFV) AS refund_amount"),
            DB::raw("SUM(REFTX) / SUM(TXs) AS refund_percentage"),
            DB::raw("SUM(RETTX) AS retrieval_count"),
            DB::raw("SUM(RETV) AS retrieval_amount"),
            DB::raw("SUM(RETTX) / SUM(TXs) AS retrieval_percentage"),
            DB::raw("SUM(TXb) AS block_count"),
            DB::raw("SUM(VOLb) AS block_amount"),
            DB::raw("SUM(TXb) / SUM(TXs) AS block_percentage")
        );

        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('user_id', $input['user_id']);
        }

        if (isset($input['currency']) && $input['currency'] != null) {
            $data = $data->where('currency', $input['currency']);
        }

        if((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d 00:00:00',strtotime($input['start_date']));
            $end_date = date('Y-m-d 23:59:59',strtotime($input['end_date']));

            $data= $data->where('tx_transactions.created_at', '>=', $start_date)
                ->where('tx_transactions.created_at', '<=', $end_date);
        }

        if((!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily')) {
            $data= $data->where('tx_transactions.created_at', '>=', date('Y-m-d 00:00:00'))
            ->where('tx_transactions.created_at', '<=', date('Y-m-d 23:59:59'));
        }

        if(isset($input['for']) && $input['for'] == 'Weekly'){ 
            $data= $data->where('tx_transactions.created_at', '>=', date('Y-m-d 00:00:00', strtotime('-6 days')))
                ->where('tx_transactions.created_at', '<=', date('Y-m-d 23:59:59'));
        }

        if(isset($input['for']) && $input['for'] == 'Monthly'){ 
            $data= $data->where('tx_transactions.created_at', '>=', date('Y-m-d 00:00:00', strtotime('-30 days')))
                ->where('tx_transactions.created_at', '<=', date('Y-m-d 23:59:59'));
        }

        $data = $data->groupBy('currency')->orderBy('success_amount', 'desc')->get()->toArray();
        // ->toSql();
        // echo $data;exit();
        //->get()->toArray();

        return $data;
    }
}
