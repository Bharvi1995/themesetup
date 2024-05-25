<?php

namespace App\Http\Controllers\Admin;

use App\Application;
use App\PaymentAPI;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class PaymentAPIController extends AdminController
{
    protected $PaymentAPI, $Application, $moduleTitleS, $moduleTitleP;
    public function __construct()
    {
        parent::__construct();
        $this->PaymentAPI = new PaymentAPI;
        $this->Application = new Application;
        $this->moduleTitleS = 'Payment API';
        $this->moduleTitleP = 'admin.paymentAPI';
    }

    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (!isset($input['noList'])) {
            $input['noList'] = 10;
        }
        $payment_gateway_id = \DB::table('middetails')->get();
        $company_name = $this->Application->getCompanyName();

        $data = $this->PaymentAPI->getAPIData($input);
        $userIds = [];
        foreach ($data as $value) {
            array_push($userIds, $value->user_id);
        }
        $businessData = Application::select("user_id", "business_name")->whereIn("user_id", $userIds)->pluck("business_name", "user_id")->toArray();
        return view($this->moduleTitleP . '.index', compact('data', 'payment_gateway_id', 'company_name', 'businessData'));
    }

    public function show($id)
    {
        $data = PaymentAPI::select("user_id", "order_id", "session_id", "request", "method", "ip", "created_at")->where('id', $id)->first();
        if (empty($data)) {
            abort(404);
        }
        $logs = PaymentAPI::select("id", "type", "response")->where("order_id", $data->order_id)->orderBy("id", "desc")->get();
        $user = Application::select('applications.business_name', 'users.email')
            ->join('users', 'applications.user_id', 'users.id')
            ->where('users.id', $data->user_id)
            ->first();

        // return $logs;
        return view($this->moduleTitleP . '.show', compact('data', 'user', "logs"));
    }
}