<?php

namespace App\Imports;

use App\Transaction;
use App\Http\Controllers\Repo\TransactionRepo;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class TransactionImport implements ToCollection
{
    public function  __construct($transaction_type)
    {
        $this->transaction_type = $transaction_type;
        $this->TransactionRepo = new TransactionRepo;
    }
    public function collection(Collection $rows)
    {
        foreach ($rows as $key=>$row) 
        {
            if($key != 0){
                $transaction = Transaction::where('order_id',$row[0])->first();
                if ($transaction){
                    if($this->transaction_type == 'suspicious'){
                        if($transaction->refund == '0' && $transaction->chargebacks == '0' && $transaction->is_flagged == '0' && $transaction->is_retrieval == '0' && $transaction->status == '1') {
                            $this->TransactionRepo->markFlagged($transaction->id,$row[1],'1',true);
                        }
                    }
                    if($this->transaction_type == 'remove_suspicious'){
                        if($transaction->is_flagged == '1'){
                            $this->TransactionRepo->removeFlagged($transaction->id);
                        }
                    }
                }
            }
        }
    }
}