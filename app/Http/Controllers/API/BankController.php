<?php

namespace App\Http\Controllers\Api;

use Str;
use Validator;
use App\Bank;
use App\Categories;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewBankEmail;
use App\MailTemplates;
use App\Mail\bankRegisterMail;
use Illuminate\Support\Facades\Mail;

class BankController extends Controller
{
    public function __construct()
    {
        // parent::__construct();
        $this->bank = new Bank;
        $this->template = new MailTemplates;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        $category = Categories::orderBy("categories.id", "ASC")->pluck('name', 'id')->toArray();
        $countries = getCountry();
        $processing_country = ['UK'=>'UK','EU'=>'EU','US/CANADA'=>'US/CANADA','Others'=>'Others'];
        return response()->json([
            'status' => 'success',
            'category' => $category,
            'countries' => $countries,
            'processing_country' => $processing_country,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'bank_name' => 'required',
                'email' => 'required|string|email|max:255|unique:banks,email,NULL,id,deleted_at,NULL',
                'password' => 'required|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'country' => 'required',
                'processing_country' => 'required',
                'category_id' => 'required'
            ],
            [
                'password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)',
                'category_id.required' => 'The category field is required.'
            ]
        );
        $input = \Arr::except($request->all(), array('_token', 'password_confirmation'));
        $input['password'] = bcrypt($input['password']);
        
        $input['category_id'] = implode(",",$input['category_id']);
        $input['processing_country'] = json_encode($input['processing_country']);

        // json of extra_email
        if (isset($input['extra_email'])) {
            if ($input['extra_email'] != null) {
                $email_array = explode(',', $input['extra_email']);
                $input['extra_email'] = json_encode($email_array);
            }
        }
        
        $input['token'] = Str::random(40) . time();
        $input['is_active'] = '0';
        $bank = $this->bank->storeData($input);
        
        $referral_code = $bank->id . strtoupper(Str::random(10));

        \DB::table('banks')
            ->where('id', $bank->id)
            ->update(['referral_code' => $referral_code]);

        $data['token'] = $input['token'];
        $data['name'] = $input['bank_name'];
        Mail::to($input['email'])->send(new bankRegisterMail($data));

        /* $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
        Notification::route('mail', $data['email'])->notify(new NewBankEmail($data)); */

        return response()->json([
            'status' => 'success',
            'message' => 'Your account has been registered successfully. You will receive an email shortly to activate your account.',
        ]);
    }
}
