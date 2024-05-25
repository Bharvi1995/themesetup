<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;
use App\User;
use App\SettlementReport;
use App\Transaction;

class MerchantPayoutReportQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $user_id )
    {
        $this->user_id = $user_id;
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $userObject = new User();
        $user = $userObject->getUser( $this->user_id );

        $settlementreport = new SettlementReport();
        $getSettlementRepost = $settlementreport->fetchUserReport( $this->user_id );

        $transaction = new Transaction();

        if( !isset($getSettlementRepost->id) ){
            $startDate = date("Y-m-d 00:00:00", strtotime($user->created_at));
        }else{
            $todayDate = date("Y-m-d");
            $recordDate = date("Y-m-d", strtotime($getSettlementRepost->end_date));
            if( $recordDate < $todayDate ){
                $startDate = date("Y-m-d 00:00:00", strtotime($getSettlementRepost->end_date. ' +1 day'));
            }else{
                $oldDate = date("Y-m-d 00:00:00", strtotime($getSettlementRepost->start_date . ' -1 day '));
                $startDate = date("Y-m-d 00:00:00", strtotime($oldDate. ' +1 day'));
            }
        }
        $endDate = date('Y-m-d 23:59:59', strtotime($startDate. ' +14 day'));

        $successTransactions = $transaction->getSettlementReportSuccessDeclined( $user->id, $startDate, $endDate );
        $data['transactionData'] = $this->processSuccessDeclinedTransactionDate( $successTransactions );
        
        $chargebackTransactions = $transaction->getSettlementReportChbTransactions( $user->id, $startDate, $endDate );
        $data['chbtransactionData'] = $this->processTransactionDate( $chargebackTransactions );

        $SuspiciousTransactions = $transaction->getSettlementReportSuspiciousTransactions( $user->id, $startDate, $endDate );
        $data['suspicioustransactionData'] = $this->processTransactionDate( $SuspiciousTransactions );

        $RefundTransactions = $transaction->getSettlementReportRefundTransactions( $user->id, $startDate, $endDate );
        $data['refundtransactionData'] = $this->processTransactionDate( $RefundTransactions );

        $RetrivalTransactions = $transaction->getSettlementReportRetrivalTransactions( $user->id, $startDate, $endDate );
        $data['retreivaltransactionData'] = $this->processTransactionDate( $RetrivalTransactions );
        
        $preArbitrationTransactions = $transaction->getSettlementReportpreArbitrationTransactions( $user->id, $startDate, $endDate );
        $data['preArbitrationtransactionData'] = $this->processTransactionDate( $preArbitrationTransactions );

        $data['calculate'] = $this->fetchTransactionsDataCalculations( $data, $successTransactions, $user );
        $data['calculate']['refund_fees'] = $data['refundtransactionData']['totalCount'] * $user->refund_fee;
        $data['calculate']['highrisk_fees'] = $data['suspicioustransactionData']['totalCount'] * $user->flagged_fee;
        $data['calculate']['chb_fees'] = $data['chbtransactionData']['totalCount'] * $user->chargeback_fee;
        $data['calculate']['retreival_fees'] = $data['retreivaltransactionData']['totalCount'] * $user->retrieval_fee;
        $data['calculate']['reserve_amount'] = ($user->rolling_reserve_paercentage / 100) * $data['transactionData']['totalSuccessAmount'];

        $total_payable = 0;
        $total_payable = ( $data['transactionData']['totalSuccessAmount'] - $data['chbtransactionData']['totalAmount'] - $data['refundtransactionData']['totalAmount'] - $data['suspicioustransactionData']['totalAmount'] - $data['retreivaltransactionData']['totalAmount'] - $data['preArbitrationtransactionData']['totalAmount'] - $data['calculate']['mdr_amount'] - $data['calculate']['reserve_amount'] - $data['calculate']['transactionsfees'] - $data['calculate']['refund_fees'] - $data['calculate']['retreival_fees'] - $data['calculate']['highrisk_fees'] - $data['calculate']['chb_fees'] );

        $settlementreport->createOrUpdateReport([
            'user_id' => $user->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'totalSuccessAmount' => $data['transactionData']['totalSuccessAmount'],
            'totalSuccessCount' => $data['transactionData']['totalSuccessCount'],
            'totalDeclinedAmount' => $data['transactionData']['totalDeclinedAmount'],
            'totalDeclinedCount' => $data['transactionData']['totalDeclinedCount'],
            'chbtotalAmount' => $data['chbtransactionData']['totalAmount'],
            'chbtotalCount' => $data['chbtransactionData']['totalCount'],
            'suspicioustotalAmount' => $data['suspicioustransactionData']['totalAmount'],
            'suspicioustotalCount' => $data['suspicioustransactionData']['totalCount'],
            'refundtotalAmount' => $data['refundtransactionData']['totalAmount'],
            'refundtotalCount' => $data['refundtransactionData']['totalCount'],
            'retreivaltotalAmount' => $data['retreivaltransactionData']['totalAmount'],
            'retreivaltotalCount' => $data['retreivaltransactionData']['totalCount'],
            'prearbitrationtotalAmount' => $data['preArbitrationtransactionData']['totalAmount'],
            'prearbitrationtotalCount' => $data['preArbitrationtransactionData']['totalCount'],
            'total_transactions' => $data['calculate']['total_transactions'],
            'mdr_amount' => $data['calculate']['mdr_amount'],
            'transactionsfees' => $data['calculate']['transactionsfees'],
            'refund_fees' => $data['calculate']['refund_fees'],
            'highrisk_fees' => $data['calculate']['highrisk_fees'],
            'chb_fees' => $data['calculate']['chb_fees'],
            'retreival_fees' => $data['calculate']['retreival_fees'],
            'reserve_amount' => $data['calculate']['reserve_amount'],
            'total_payable' => $total_payable,
            'paid' => '0',
            'paid_date' => NULL,
        ]);

    }

    public function fetchTransactionsDataCalculations( $data, $successTransactions, $user ){

        $total_transactions = 0;
        $mdr_amount = 0 ;
        $visaTotalAmount = 0;
        $McTotalAmount = 0;

        foreach( $successTransactions as $tkey => $tvalue ){
            
            if( $tvalue->card_type == 3 ){
                $convertedAmount = checkSelectedCurrencyTwo( $tvalue->currency, $tvalue->amount );
                if( $tvalue->status === 1 ){
                    $McTotalAmount += $convertedAmount['amount'];
                }
            }else{
                $convertedAmount = checkSelectedCurrencyTwo( $tvalue->currency, $tvalue->amount );
                if( $tvalue->status === 1 ){
                    $visaTotalAmount += $convertedAmount['amount'];
                }
            }

        }
        $visaMdr = ($user->merchant_discount_rate_master_card / 100) * $visaTotalAmount;
        $mcMdr = ($user->merchant_discount_rate_master_card / 100) * $McTotalAmount;
        $mdr_amount = $visaMdr + $mcMdr;
        

        foreach( $data as $key => $value ){
            if( $key == 'transactionData' ){
                $total_transactions += $value['totalSuccessCount'];
                $total_transactions += $value['totalDeclinedCount'];
            }
        }
        $transactionsfees = $user->transaction_fee * $total_transactions;
        return [
            'total_transactions' => $total_transactions,
            'mdr_amount' => $mdr_amount,
            'transactionsfees' => $transactionsfees,
        ];

    }

    public function processSuccessDeclinedTransactionDate( $successTransactions ){
        $totalSuccessAmount = 0;
        $totalSuccessCount = 0;
        $totalDeclinedAmount = 0;
        $totalDeclinedCount = 0;

        foreach($successTransactions as $key => $value){
            if( $value->currency == 'USD' ){
                if( $value->status === 0 ){
                    $totalDeclinedAmount += $value->amount;
                    $totalDeclinedCount += $value->total; 
                }else{
                    $totalSuccessAmount += $value->amount;
                    $totalSuccessCount += $value->total;
                }
            }else{
                $convertedAmount = checkSelectedCurrencyTwo( $value->currency, $value->amount );

                if( $value->status === 0 ){
                    $totalDeclinedAmount += $convertedAmount['amount'];
                    $totalDeclinedCount += $value->total;
                }else{
                    $totalSuccessAmount += $convertedAmount['amount'];
                    $totalSuccessCount += $value->total;
                }
            }
        }

        return [
            'totalSuccessAmount' => $totalSuccessAmount,
            'totalSuccessCount' => $totalSuccessCount,
            'totalDeclinedAmount' => $totalDeclinedAmount,
            'totalDeclinedCount' => $totalDeclinedCount,
        ];
    }

    public function processTransactionDate( $transactionsDetails ){
        $totalAmount = 0;
        $totalCount = 0;

        foreach($transactionsDetails as $key => $value){
            if( $value->currency == 'USD' ){
                $totalAmount += $value->amount;
            }else{
                $convertedAmount = checkSelectedCurrencyTwo( $value->currency, $value->amount );
                $totalAmount += $convertedAmount['amount'];
            }
            $totalCount += $value->total;
        }

        return [
            'totalAmount' => $totalAmount,
            'totalCount' => $totalCount
        ];
    }

}
