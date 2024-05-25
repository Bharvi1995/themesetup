<?php

namespace App\Http\Controllers;


use Auth;
use DB;
use URL;
use View;
use Validator;
use App\User;
use App\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $user;

    public function __construct()
    {
        $this->middleware('auth')->except(['directpayapi', 'gettransactiondetailsapi', 'hostedpayapi', 'cryptopayapi','bankpayapi', 'cardtokenizationapi','directpayapiv2', 'refundtransactionapi']);
        $this->middleware(function ($request, $next) {
            $this->user = \Auth::user();
            return $next($request);
        });
        $this->Transaction = new Transaction;

    }

    public function riskComplianceReport(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (auth()->user()->main_user_id == 0) {
            $user_id = auth()->user()->id;
        } else {
            $user_id = auth()->user()->main_user_id;
        }
        $input['user_id'] = $user_id;
        $data = $this->Transaction->getRiskComplianceReportData($input);

        return view('front.reports.risk_compliance_report',compact('data'));
    }
    
}
