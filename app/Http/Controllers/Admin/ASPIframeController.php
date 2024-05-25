<?php

namespace App\Http\Controllers\Admin;

use Hash;
use Storage;
use Validator;
use App\User;
use App\MIDDetail;
use App\Application;
use App\Http\Requests;
use App\Http\Controllers\AdminController;
use App\ImageUpload;
use Illuminate\Http\Request;

class ASPIframeController extends AdminController
{

    public function __construct()
    {
        parent::__construct();

        $this->moduleTitleS = 'ASP Iframe';
        $this->moduleTitleP = 'admin.aspiframe';
        $this->MIDDetail = new MIDDetail;
        $this->Application = new Application;

        view()->share('moduleTitleP',$this->moduleTitleP);
        view()->share('moduleTitleS',$this->moduleTitleS);
    }

    public function index(Request $request)
    {
        $mid_details = $this->MIDDetail->getMid();
        $companyName = $this->Application->getCompanyName();
        return view($this->moduleTitleP.'.index', compact('companyName', 'mid_details'));
    }

    // ================================================
    /* method : store
    * @param  : Request $request
    * @Description : save image and generate hosted iframe
    */// ==============================================
    public function store(Request $request)
    {
        $input = \Arr::except($request->all(),array('_token', '_method'));
        $mid = $request->mid;
        $company_id = $request->company_name;

        $mid_details = \DB::table('middetails')
            ->where('id', $mid)
            ->whereNull('deleted_at')
            ->first();

        if ($mid_details == null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid MID details supplied.',
                'data' => json_encode($mid_details),
                'post' => json_encode($request->all()),
            ]);
        }

        $companyName = \DB::table('users')
            ->where('id', $company_id)
            ->whereNull('deleted_at')
            ->first();

        if ($companyName == null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Merchant details supplied.',
                'data' => json_encode($companyName),
                'post' => json_encode($request->all()),
            ]);
        }

        if ($companyName->api_key == null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merchant has not created api_key.'
            ]);
        }

        // amount and currency
        if (
            isset($input['amount']) && !empty($input['amount']) && empty($input['currency']) ||
            isset($input['currency']) && !empty($input['currency']) && empty($input['amount'])
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'Amount and Currency both are required.'
            ]);
        } else {

            if (
                isset($input['amount']) && !empty($input['amount']) &&
                isset($input['currency']) && !empty($input['currency'])
            ) {
                $usd_amount = getAmountInUsd($input['amount'], $input['currency']);

                // min amount
                if ($usd_amount < $mid_details->min_transaction_limit) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'MID Minimum amount limit is '.$mid_details->min_transaction_limit.' USD'
                    ]);
                }

                // min amount
                if ($usd_amount > $mid_details->per_transaction_limit) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'MID Maximum amount limit is '.$mid_details->per_transaction_limit.' USD'
                    ]);
                }
            }
        }
        
        if(isset($input['iframe_logo'])){
            $validator = Validator::make($input, ['iframe_logo'=>'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx|max:35840']);

            if ($validator->passes()) {
                $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageName = $imageName . '.' . $request->file('iframe_logo')->getClientOriginalExtension();
                $filePath = 'uploads/merchange_iframe_logos-' . $imageName;
                Storage::disk('s3')->put($filePath, file_get_contents($request->file('iframe_logo')->getRealPath()));
                $input['iframe_logo'] = $filePath;
                User::where('id', $companyName->id)->update([
                    'iframe_logo' => $input['iframe_logo']
                ]);
            }
        }

        if (isset($request['hosted_request']) && $request['hosted_request'] == '1') {

            $encrypt_method = "AES-256-CBC";
            $secret_key = 'dlwerQWbuasEwomsdvWsvmlfRErvsdsd';
            $secret_iv = '9lkkjjWevsdv67sdjnNwqeQ9veWEbeRvf';
            
            // hash
            $key = hash('sha256', $secret_key);

            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);
            $midType = "";
            if($mid_details->mid_type == 1 || ($mid_details->mid_type == 5 && $mid_details->apm_type == 1)) {
                $midType = "Card";
            } elseif ($mid_details->mid_type == 2 || ($mid_details->mid_type == 5 && $mid_details->apm_type == 2)) {
                $midType = "Bank";
            } elseif ($mid_details->mid_type == 3 || ($mid_details->mid_type == 5 && $mid_details->apm_type == 3)) {
                $midType = "Crypto";
            } elseif ($mid_details->mid_type == 4 || ($mid_details->mid_type == 5 && $mid_details->apm_type == 4)) {
                $midType = "UPI";
            }

            // create array
            $data_array['mid'] = $mid;
            $data_array['user_id'] = $company_id;
            $data_array['api_key'] = $companyName->api_key;
            $data_array['create_by'] = 'admin';
            $data_array["type"] = $midType;
            $data_array["amount"] = $input['amount'] ?? null;
            $data_array["currency"] = $input['currency'] ?? null;
            
            // create json
            $data_array_json = json_encode($data_array);

            // encrypt token;
            $encryptToken = openssl_encrypt($data_array_json, $encrypt_method, $key, 0, $iv);
            $encryptToken = base64_encode($encryptToken);

            // decrypt token
            // $decryptToken = openssl_decrypt(base64_decode($encryptToken), $encrypt_method, $key, 0, $iv);

            /**
             * @var $host_origin [string] [get host URL with current HTTP scheme]
             */
            $host_origin = $request->getSchemeAndHttpHost();
            $iframe_code = route('iframe.index', $encryptToken);

            if ($input['api_version'] == '1') {
                return response()->json([
                    'status' => 'success',
                    // 'iframe' => '<iframe src="'.env('APP_URL').'/iframe-checkout/'.$encryptToken.'"  class="embed-responsive-item" height="1000px" width="1500px" allowfullscreen></iframe>',
                    'link' => env('APP_URL') .'/iframe-checkout/'.$encryptToken
                ]);
            } else {
                return response()->json([
                    'status' => 'success',
                    // 'iframe' => '<iframe src="'.env('APP_URL').'/v2/iframe-checkout/'.$encryptToken.'"  class="embed-responsive-item" height="1000px" width="1500px" allowfullscreen></iframe>',
                    'link' => env('APP_URL') .'/v2/iframe-checkout/'.$encryptToken
                ]);
            }
        }
    }

    public function getIframeLogo(Request $request)
    {
        $user = User::find($request->id);

        $img = null;
        if(!is_null($user) && !is_null($user->iframe_logo)){
            $img = getS3Url($user->iframe_logo);
        }

        return response()->json([
            'success' => $img
        ]);
    }

    public function iframeCheckout($token) {
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'dlwerQWbuasEwomsdvWsvmlfRErvsdsd';
        $secret_iv = '9lkkjjWevsdv67sdjnNwqeQ9veWEbeRvf';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        // decrypt token
        $iframe_json = openssl_decrypt(base64_decode($token), $encrypt_method, $key, 0, $iv);
        
        if($iframe_json == false) {
            return response()->json([
                'status' => 'fail',
                'errors' => 'invalid token iframe code.'
            ]);
        }

        $iframe_array = json_decode($iframe_json, 1);
        
        $userData = User::where('id', $iframe_array['user_id'])
            ->first();
        
        $check_assign_mid = checkAssignMid($iframe_array['mid']);

        if ($check_assign_mid == false) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Your account is temporarily disabled, please contact admin.',
            ]);
        }
        $required_fields = json_decode($check_assign_mid->required_fields, 1);
        return view('gateway.iframe', compact('token', 'required_fields', 'iframe_array'));
    }

    public function checkoutForm($token, Request $request)
    {
        $input = \Arr::except($request->all(), array('_token'));
        $records = json_encode($input);
        return view('gateway.checkout', compact('token', 'records'));
        
    }

}
