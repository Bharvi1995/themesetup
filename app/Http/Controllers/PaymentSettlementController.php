<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DailySettlementReport;
use Auth;

class PaymentSettlementController extends HomeController
{
    protected $dailySettlementreport;

	public function __construct()
    {
        $this->dailySettlementreport = new DailySettlementReport();
    }

    public function index(){
        $user = Auth::user();
        
        $getSettlementRepost = $this->dailySettlementreport->where('user_id', $user->id)
            ->where('paid', '=', '0')
            ->orderBy('start_date','DESC')
            ->paginate(10);
        
        return view('front.payment_settlement.index', compact('getSettlementRepost', 'user'));


    }

}
