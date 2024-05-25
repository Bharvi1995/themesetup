<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\AutoMidVolumeMail;
use Carbon\Carbon;

class AdminAutoMidVolumeController extends AdminController
{
    public function sendAutoPayoutMail(Request $request)
    {
        if ($request->get("password") != "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz") {
            exit(404);
        }
        // Get the date for yesterday
        $yesterday = Carbon::yesterday();
        $date = Carbon::now()->subDay()->toFormattedDateString();
        $transactions = DB::table('transactions as t')
            ->select('m.bank_name', DB::raw('SUM(t.amount_in_usd) as total_vol'))
            ->join('middetails as m', 'm.id', '=', 't.payment_gateway_id')
            ->where('t.created_at', '>=', $yesterday)
            ->where('t.created_at', '<=', $yesterday->copy()->addDay())
            ->groupBy('t.payment_gateway_id')
            ->orderBy('total_vol', 'desc')
            ->whereNotIn('t.payment_gateway_id', [1, 2])
            ->where('t.status', '1')
            ->get();

        // Mail::to('example@gmail.com')->cc(['test@gmail.com', 'tech@gmail.com'])->send(new AutoMidVolumeMail($transactions, $date));


        return response()->json(["status" => 200, "msg" => "Email sent successfully!"]);
    }
}