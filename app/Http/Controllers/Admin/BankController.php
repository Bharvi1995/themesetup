<?php

namespace App\Http\Controllers\Admin;

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

class BankController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bank = new Bank;
        $this->template = new MailTemplates;

        $this->moduleTitleS = 'Bank';
        $this->moduleTitleP = 'admin.banks';

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
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
        $data = $this->bank->getData($input, $noList);

        $template = $this->template->getListForMail();

        return view($this->moduleTitleP . '.index', compact('data','template'))
            ->with('i', 0);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = Categories::orderBy("categories.id", "ASC")->pluck('name', 'id')->toArray();
        return view($this->moduleTitleP . '.create', compact('category'));
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

        $bank = $this->bank->storeData($input);
        
        $referral_code = $bank->id . strtoupper(Str::random(10));

        \DB::table('banks')
            ->where('id', $bank->id)
            ->update(['referral_code' => $referral_code]);

        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        Notification::route('mail', $data['email'])->notify(new NewBankEmail($data));

        notificationMsg('success', 'Bank Created Successfully!');

        return redirect()->route('banks.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        return view($this->moduleTitleP . '.show', compact('bank'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $bank)
    {
        $data = $bank;
        $category = Categories::orderBy("categories.id", "ASC")->pluck('name', 'id')->toArray();
        return view($this->moduleTitleP . '.edit', compact('data','category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank)
    {
        $this->validate($request, [
            'bank_name' => 'required',
            'country' => 'required',
            'processing_country' => 'required',
            'category_id' => 'required',
            'email' => 'required|email|unique:banks,email,' . $bank->id,
            'password' => 'nullable|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'confirm_password' => 'same:password',
        ],[
            'password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)',
            'category_id.required' => 'The category field is required.'
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));

        if ($input['password'] != '') {
            $this->validate($request, [
                'password'   => 'required|min:9|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'password_confirmation' => "same:password",
            ], ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']);

            $input['password'] = Hash::make($input['password']);
            $password          = $input['password'];
        } else {
            $password          = "";
            unset($input['password']);
        }

        $input['category_id'] = implode(",",$input['category_id']);
        $input['processing_country'] = json_encode($input['processing_country']);
        
        // json of extra_email
        if (isset($input['extra_email'])) {
            if ($input['extra_email'] != null) {
                $email_array = explode(',', $input['extra_email']);
                $input['extra_email'] = json_encode($email_array);
            }
        }

        $this->bank->updateData($bank->id, $input);
        
        notificationMsg('success', 'Bank Update Successfully!');
        
        return redirect()->route('banks.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->bank->destroyData($id);
        notificationMsg('success', 'Bank Delete Successfully!');
        return redirect()->route('banks.index');
    }

    public function changeStatus(Request $request, $id)
    {
        $status = $request->get('status');
        $this->bank->updateData($id, ['is_active' => $status]);
        notificationMsg('success', 'Status Change Successfully!');
        return redirect()->route('banks.index');
    }

    public function sendMultiMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_template' => 'required',
            'subject' => 'required',
            'bodycontent' => 'required',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));
        if ($validator->passes()) {
            $ids = explode(',', $input['id']);
            unset($input['id']);

            $details = [
                'ids' => $ids,
                'input' => $input
            ];

            // send all mail in queue.
            $job = (new \App\Jobs\BankUserQueueEmail($details))
                ->delay(now()->addSeconds(2));

            dispatch($job);

            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json(['errors' => $validator->errors()]);
    }

    public function deleteMultiBank(Request $request)
    {
        if ($request->get('type') == 'forall') {
            $allID = $request->get('id');
            foreach ($allID as $key => $value) {
                $this->bank->destroyData($value);
            }
            return response()->json([
                'success' => true,
            ]);
        }
    }
}
