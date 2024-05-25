<?php

namespace App\Http\Controllers\Admin;

use App\WebsiteUrl;
use App\User;
use Redirect;
use App\Application;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use App\Mail\APIKeyIPAproveMail;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IPWhitelistExport;
use DB;
use Mail;

class IPWhitelistController extends AdminController
{

    protected $WebsiteUrl, $application, $moduleTitleP;
    public function __construct()
    {
        parent::__construct();
        $this->WebsiteUrl = new WebsiteUrl;
        $this->application = new Application;

        $this->moduleTitleP = 'admin.ipWhitelist';

        view()->share('moduleTitleP', $this->moduleTitleP);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->WebsiteUrl->getData($input, $noList);
        $companyName = $this->application->getCompanyName();
        return view($this->moduleTitleP . '.index', compact('data', 'companyName'));
    }

    // ================================================
    /* method : approveWebsiteUrl
     * @param  : 
     * @description : approve ip whitelist
     */// ==============================================    
    public function approveWebsiteUrl(Request $request, $id)
    {
        WebsiteUrl::where('id', $id)->update(['is_active' => '1']);

        $company = WebsiteUrl::where('id', $id)->first();

        $user = User::where('id', $company->user_id)->first();

        $companyName = Application::where('user_id', $user->id)->first();

        $content = [
            'company' => $companyName->company_name,
            'data' => $company->ip_address,
            'api_key' => $user->api_key,
        ];

        try {
            // \Mail::to($user->email)->queue(new APIKeyIPAproveMail($content));
        } catch (\Exception $e) {
            // 
        }

        \Session::put('success', 'API Key Website Name And IP Approved Successfully!');

        return Redirect::back();
    }

    public function refuseWebsiteUrl(Request $request, $id)
    {
        WebsiteUrl::where('id', $id)->update(['is_active' => '0']);
        \Session::put('success', 'API Key Website Name And IP Refused Successfully!');
        return Redirect::back();
    }

    public function addIP(Request $request)
    {
        $company_name = $this->application->getCompanyName();
        return view('admin.ipWhitelist.add', compact('company_name'));
    }
    public function storeIP(Request $request)
    {
        $validation['company_name'] = 'required';
        foreach ($request->generate_apy_key as $key => $value) {
            $validation['generate_apy_key.' . $key . '.website_name'] = 'required';
            $validation['generate_apy_key.' . $key . '.ip_address'] = 'required|ip';
        }
        $this->validate($request, $validation);
        try {
            foreach ($request->generate_apy_key as $key => $value) {
                $data = [
                    'user_id' => $request->company_name,
                    'website_name' => $value['website_name'],
                    'ip_address' => $value['ip_address']
                ];
                WebsiteUrl::create($data);
            }
            addToLog('IP whitelist created successfully', $request->generate_apy_key, 'general');

            Session::put('success', 'IP address added successfully!');
            Session::forget('api_key');
            return redirect()->route('ip-whitelist');
        } catch (\Exception $e) {
            Session::put('error', 'API Key not Saved!');
            Session::forget('api_key');
            return back();
        }
    }

    public function ipWhitelistExcel(Request $request)
    {
        $input = $request->all();
        return Excel::download(new IPWhitelistExport(), 'IP_Whitelist_' . date('d-m-Y') . '.xlsx');
    }

    // * Approve the multiIp
    public function approveMultiIP(Request $request)
    {
        try {
            $ids = $request->only(["id"]);

            if (count($ids['id']) > 0) {
                WebsiteUrl::whereIn("id", $ids['id'])->update(["is_active" => "1"]);
                $data = DB::table('website_url')->select("website_url.ip_address", "applications.business_name", "users.api_key", "users.email")
                    ->join("users", "users.id", '=', "website_url.user_id")
                    ->join("applications", "applications.user_id", "=", "website_url.user_id")
                    ->whereIn("website_url.id", $ids['id'])
                    ->get();

                foreach ($data as $item) {
                    $content = [
                        'company' => $item->business_name,
                        'data' => $item->ip_address,
                        'api_key' => $item->api_key,
                    ];
                    Mail::to($item->email)->queue(new APIKeyIPAproveMail($content));
                }
            }
            return response()->json(["status" => 200, "message" => "IP's approved successfully"]);
        } catch (\Throwable $th) {
            return response()->json(["status" => 500, "message" => "Something went wrong please try again later."]);

        }

    }
}