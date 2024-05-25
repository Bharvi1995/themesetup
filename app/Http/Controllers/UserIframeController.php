<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\ImageUpload;
use Storage;

class UserIframeController extends HomeController
{
    // ================================================
    /* method : __construct
    * @param  : 
    * @description : create new instance of the class
    */// ===============================================
    public function __construct()
    {
        parent::__construct();

        $this->User = new User;
        $this->moduleTitleS = 'Iframe Ganerator';
        $this->moduleTitleP = 'front.iframe';

        view()->share('moduleTitleP',$this->moduleTitleP);
        view()->share('moduleTitleS',$this->moduleTitleS);
    }

    // ================================================
    /* method : index
    * @param  : 
    * @description : iframe link page
    */// ===============================================
    public function index()
    {
        $user = \Auth::user();

        if(!empty($user->mid)) {
            $encrypt_method = "AES-256-CBC";
            $secret_key = 'dsflkIZxusugQdpMyjqTSE3sadjL5vsd';
            $secret_iv = '7sad4vdsJjas87saMLmlNi9x63MRAFLgk';
            
            // hash
            $key = hash('sha256', $secret_key);
            
            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);
         
            // create array
            $data_array['mid'] = $user->mid;
            $data_array['user_id'] = $user->id;
            $data_array['api_key'] = $user->api_key;
            $data_array['create_by'] = 'user';
            $data_array['type'] = 'Card';
            
            // create json
            $data_array_json = json_encode($data_array);
            
            // encrypt token;
            $encryptToken = openssl_encrypt($data_array_json, $encrypt_method, $key, 0, $iv);
            $encryptToken = base64_encode($encryptToken);

            $iframe_code = route('iframe.checkout', $encryptToken);
            $iframe_tag = '<iframe src="'.$iframe_code.'" class="embed-responsive-item" height="1000px" width="1500px" allowfullscreen></iframe>';
            $iframe_code_crypto = "";
            $iframe_tag_crypto = "";
            $iframe_code_bank = "";
            $iframe_tag_bank = "";

            if(!empty($user->crypto_mid)) {
                $data_crypto_array['mid'] = $user->crypto_mid;
                $data_crypto_array['user_id'] = $user->id;
                $data_crypto_array['api_key'] = $user->api_key;
                $data_crypto_array['create_by'] = 'user';
                $data_crypto_array["type"] = "Crypto";

                $data_crypto_array_json = json_encode($data_crypto_array);
                $encryptTokenCrypto = openssl_encrypt($data_crypto_array_json, $encrypt_method, $key, 0, $iv);
                $encryptTokenCrypto = base64_encode($encryptTokenCrypto);

                $iframe_code_crypto = route('iframe.checkout', $encryptTokenCrypto);
                $iframe_tag_crypto = '<iframe src="'.$iframe_code_crypto.'" class="embed-responsive-item" height="1000px" width="1500px" allowfullscreen></iframe>';
            }

            if(!empty($user->bank_mid)) {
                $data_bank_array['mid'] = $user->bank_mid;
                $data_bank_array['user_id'] = $user->id;
                $data_bank_array['api_key'] = $user->api_key;
                $data_bank_array['create_by'] = 'user';
                $data_bank_array["type"] = "Bank";
                $data_bank_array_json = json_encode($data_bank_array);
                $encryptTokenBank = openssl_encrypt($data_bank_array_json, $encrypt_method, $key, 0, $iv);
                $encryptTokenBank = base64_encode($encryptTokenBank);

                $iframe_code_bank = route('iframe.checkout', $encryptTokenBank);
                $iframe_tag_bank = '<iframe src="'.$iframe_code_bank.'" class="embed-responsive-item" height="1000px" width="1500px" allowfullscreen></iframe>';
            }

            return view($this->moduleTitleP.'.index', compact('iframe_code', 'iframe_tag','iframe_code_crypto','iframe_tag_crypto','iframe_code_bank','iframe_tag_bank'));
        } else {
            notificationMsg('warning', "Yod don\'t have access for iFrame");

            return back();
        }
        
    }

    // ================================================
    /* method : createIframe
    * @param  : 
    * @description : iframe create
    */// ==============================================
    public function createIframe(Request $request)
    {
        $input = \Arr::except($request->all(),array('_token', '_method'));

        $validator = \Validator::make($input, [
            'iframe_logo' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx',
            'currency' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please select currency and amount.',
            ]);
        }

        $user = \Auth::user();
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'dsflkIZxusugQdpMyjqTSE3sadjL5vsd';
        $secret_iv = '7sad4vdsJjas87saMLmlNi9x63MRAFLgk';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        
        if(isset($input['iframe_logo'])){
            $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageName = $imageName . '.' . $request->file('iframe_logo')->getClientOriginalExtension();
            $filePath = 'uploads/merchange_iframe_logos-' . $imageName;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('iframe_logo')->getRealPath()));
            $input['iframe_logo'] = $filePath;
            User::where('id', $user->id)->update([
                'iframe_logo' => $input['iframe_logo']
            ]);
        }
        $mid_details = \DB::table('middetails')
            ->where('id', $user->mid)
            ->whereNull('deleted_at')
            ->first();
        
        $midType = "";
        
        if($mid_details->mid_type == 1){
            $midType = "Card";
        }else if($mid_details->mid_type == 2){
            $midType = "Bank";
        }
        else if($mid_details->mid_type == 3){
            $midType = "Crypto";
        }

        $data_array['mid'] = $user->mid;
        $data_array['user_id'] = $user->id;
        $data_array['create_by'] = 'user';
        $data_array["type"] = $midType;
        $data_array["amount"] = $input['amount'];
        $data_array["currency"] = $input['currency'];

        $data_array_json = json_encode($data_array);
        
        $encryptToken = openssl_encrypt($data_array_json, $encrypt_method, $key, 0, $iv);
        $encryptToken = base64_encode($encryptToken);

        $iframe_code = route('iframe.checkout', $encryptToken);
        // $iframe_tag = '<iframe src="'.$iframe_code.'" class="embed-responsive-item" height="1000px" width="1500px" allowfullscreen></iframe>';

        return response()->json([
            'status' => true,
            'message' => 'Iframe generated successfully.',
            'iframe_code' => $iframe_code,
            // 'iframe_tag' => $iframe_tag
        ]);
    }

    public function removeLogo(Request $request)
    {
        try {
            $user = \Auth::user();
            $user->iframe_logo = null;
            $user->update();
            return response()->json([
                'status' => true,
                'message' => 'Logo removed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
            ]);
        }
    }
}
