<?php

namespace App\Http\Controllers;


use App\User;
use App\Admin;
use App\WebsiteUrl;
use App\Application;
use App\Mail\APIKeyIPMail;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class UserAPIController extends HomeController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $User, $moduleTitleS, $moduleTitleP;

    public function __construct()
    {
        parent::__construct();
        $this->User = new User;
        $this->moduleTitleS = 'API Documents';
        $this->moduleTitleP = 'front.apidoc';
        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view($this->moduleTitleP . '.index');
    }

    public function userApiKey()
    {
        if (Auth::user()->main_user_id != 0 && Auth::user()->is_sub_user == '1')
            $userID = Auth::user()->main_user_id;
        else
            $userID = Auth::user()->id;

        $data = $this->User->findData($userID);

        $apiWebsiteUrlIP = WebsiteUrl::where('user_id', Auth::user()->id)->get();

        return view($this->moduleTitleP . '.apiKey', compact('data', 'apiWebsiteUrlIP'));
    }

    public function userApiKeyAdd()
    {
        return view($this->moduleTitleP . '.apiKey_add');
    }

    public function ipWhitelistExport()
    {
        // code...
    }

    public function directpayapi(Request $request)
    {
        /**
         * @var $host_origin [string] [get host URL with current HTTP scheme]
         */
        $host_origin = $request->getSchemeAndHttpHost();

        return view($this->moduleTitleP . '.directpayapi', compact('host_origin'));
    }

    public function cardtokenizationapi(Request $request)
    {
        /**
         * @var $host_origin [string] [get host URL with current HTTP scheme]
         */
        $host_origin = $request->getSchemeAndHttpHost();

        return view($this->moduleTitleP . '.cardtokenizationapi', compact('host_origin'));
    }

    public function gettransactiondetailsapi(Request $request)
    {
        /**
         * @var $host_origin [string] [get host URL with current HTTP scheme]
         */
        $host_origin = $request->getSchemeAndHttpHost();

        return view($this->moduleTitleP . '.transactiondetails', compact('host_origin'));
    }

    public function hostedpayapi(Request $request)
    {
        /**
         * @var $host_origin [string] [get host URL with current HTTP scheme]
         */
        $host_origin = $request->getSchemeAndHttpHost();

        return view($this->moduleTitleP . '.hostedpayapi', compact('host_origin'));
    }

    public function cryptopayapi(Request $request)
    {
        $host_origin = $request->getSchemeAndHttpHost();

        return view($this->moduleTitleP . '.cryptopayapi', compact('host_origin'));
    }

    public function bankpayapi(Request $request)
    {
        $host_origin = $request->getSchemeAndHttpHost();

        return view($this->moduleTitleP . '.bankpayapi', compact('host_origin'));
    }

    public function generateAPIKey(Request $request)
    {
        $input = Arr::except($request->all(), array('_token', '_method'));
        $validation = [];
        $validation_message = [];
        foreach ($request->generate_apy_key as $key => $value) {
            // $validation['generate_apy_key.' . $key . '.website_name'] = 'required';
            // $validation_message['generate_apy_key.' . $key . '.website_name.required'] = 'This fileld is required';
            $validation['generate_apy_key.' . $key . '.ip_address'] = 'required|ip';
            $validation_message['generate_apy_key.' . $key . '.ip_address.required'] = 'This field is required';
            $validation_message['generate_apy_key.' . $key . '.ip_address.ip'] = 'Please enter valid IP address';
        }
        $this->validate($request, $validation, $validation_message);

        if (Auth::user()->api_key == null) {
            $api_key = Auth::user()->id . Str::random(64);
            $input2 = ['api_key' => $api_key];
            $this->User->updateData(Auth::user()->id, $input2);
        } else {
            $api_key = User::where('id', Auth::user()->id)->value('api_key');
        }

        try {
            foreach ($request->generate_apy_key as $key => $value) {
                $data = [
                    'user_id' => Auth::user()->id,
                    'website_name' => $value['website_name'] ?? "",
                    'ip_address' => $value['ip_address']
                ];
                WebsiteUrl::create($data);
            }

            $company = Application::where('user_id', Auth::user()->id)->first();
            $content = [
                'company' => $company->business_name,
                'websites' => $request->generate_apy_key,
                'api_key' => $api_key,
            ];
            Session::put('success', 'Your IP address has been added successfully! Our team will review the changes and revert to you shortly');
            Session::forget('api_key');
            return redirect()->route('whitelist-ip');
        } catch (\Exception $e) {
            Session::put('error', 'Something went wrong with your request. Kindly try again');
            Session::forget('api_key');
            return redirect()->route('whitelist-ip');
        }
    }

    public function deleteWebsiteUrl(Request $request, $id)
    {
        $websites = WebsiteUrl::findOrFail($id);
        if (Auth::user()->id == $websites->user_id) {
            WebsiteUrl::where('id', $id)->delete();
            addToLog('IP whitelist delete successfully', [$id], 'general');
            Session::put('success', 'Your API Key Website Name Delete Successfully !!');
        } else {
            Session::put('error', 'Something went wrong !!');
        }


        return redirect()->back();
    }

    public function generateIframe()
    {
        if (!Auth::user()->api_key || Auth::user()->api_key == '') {
            Session::put('warning', 'You must be generate API key then you able to create iFrame!');
            return redirect()->back();
        }

        return view($this->moduleTitleP . '.generateiframe');
    }

    public function storeIframe(Request $request)
    {
        $this->validate($request, [
            'callback_url' => 'required',
        ]);

        $input = Arr::except($request->all(), array('_token', '_method'));

        $this->User->updateData(Auth::user()->id, $input);

        Session::put('success', 'Your iframe generated Successfully !!');
        return redirect()->back();
    }

    public function testIframe()
    {
        return view($this->moduleTitleP . '.testiframe');
    }

    // ================================================
    /* method  : secure3DSApi
     * @ param  :
     * @ Description : 3d secure api docs
     */// ==============================================
    public function secure3DSApi()
    {
        if (Auth::user()->main_user_id != 0 && Auth::user()->is_sub_user == '1')
            $userID = Auth::user()->main_user_id;
        else
            $userID = Auth::user()->id;

        $data = $this->User->findData($userID);
        return view($this->moduleTitleP . '.3dsecureapi', compact('data'));
    }

    public function directpayapiv2(Request $request)
    {
        /**
         * @var $host_origin [string] [get host URL with current HTTP scheme]
         */
        $host_origin = $request->getSchemeAndHttpHost();

        return view($this->moduleTitleP . '.directpayapiv2', compact('host_origin'));
    }

    public function refundtransactionapi(Request $request)
    {
        $host_origin = $request->getSchemeAndHttpHost();
        return view($this->moduleTitleP . '.refunddetails', compact('host_origin'));
    }
}