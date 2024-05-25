<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;
use App\Transaction;

class RemoveDuplicateTransaction
{

    public function removeTransactions(Request $request)
    {
        if($request->password == 'iPayDeleteDuplicate') {
            $data =  Transaction::whereIn('order_id', function ( $query ) {
                $query->select('order_id')
                    ->from('transactions')
                    ->where('is_duplicate_delete', '0')
                    ->groupBy('order_id')
                    ->havingRaw('count(*) > 1');
            })
            // ->whereIn('email', function ( $query ) {
            //     $query->select('email')->from('transactions')->groupBy('email')->havingRaw('count(*) > 1');
            // })
            ->where('id', '>', 1189150)
            ->orderBy('order_id', 'DESC')
            // ->where('status', '1')
            // ->where('user_id', '<>', '22')
            // ->where('payment_gateway_id', '<>', '41')
            ->get();
            // dd($data);
            $i = 1;
            foreach ($data as $key => $value) {
                if($i % 2 == 0) {
                    Transaction::where('id', $value['id'])
                        ->update([
                            'order_id' => 'D-'.$value['order_id'],
                            'deleted_at' => date('Y-m-d H:i:s'),
                            'is_duplicate_delete' => '1'
                        ]);
                }
                $i++;
            }
            echo "Done";
        } elseif ($request->password == 'hari') {
            $data =  Transaction::whereIn('order_id', function ( $query ) {
                $query->select('order_id')
                    ->from('transactions')
                    ->where('is_duplicate_delete', '0')
                    ->groupBy('order_id')
                    ->havingRaw('count(*) > 1');
            })
            ->where('id', '>', 1189150)
            ->orderBy('order_id', 'DESC')
            // ->where('status', '1')
            // ->where('user_id', '<>', '22')
            // ->where('payment_gateway_id', '<>', '41')
            ->get();
            dd($data);
        } else {
            echo 'No no no...';
        }
    }

    public function removeOldOrderID(Request $request)
    {
        $data =  Transaction::whereIn('order_id', function ( $query ) {
                $query->select('order_id')
                    ->from('transactions')
                    ->whereIn([''])
                    ->groupBy('order_id')
                    ->havingRaw('count(*) > 1');
            })
            ->where('id', '<', 1189150)
            ->where('payment_gateway_id', '41')
            ->get();
        dd($data);

        foreach ($data as $key => $value) {
           Transaction::where('id', $value['id'])
            ->delete();
        }
        echo "Yes"; exit();

        $i = 1;
        foreach ($data as $key => $value) {
            // if($i % 2 == 0) {
                Transaction::where('id', $value['id'])
                    ->update([
                        'order_id' => 'D-'.$value['order_id'],
                        'deleted_at' => date('Y-m-d H:i:s'),
                        'is_duplicate_delete' => '1'
                    ]);
            // }
            // $i++;
        }
        echo "Done";
    }

}
