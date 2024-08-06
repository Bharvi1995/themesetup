<?php

namespace App\Http\Controllers;

use URL;
use Hash;
use View;
use Input;
use Session;
use App\User;
use Redirect;
use Validator;
use App\Ticket;
use App\Warning;
use App\SendMail;
use App\MIDDetail;
use App\ImageUpload;
use App\Transaction;
use App\Notification;
use App\PayoutSchedule;
use App\UserBankDetails;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\userEmailChange;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\UserBankDetailFormRequest;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $user, $ticket, $Warning, $SendMail, $Transaction, $Notification, $payoutSchedule;

    public function __construct()
    {
        $this->middleware('auth')->except(['directpayapi', 'gettransactiondetailsapi', 'hostedpayapi', 'cryptopayapi', 'bankpayapi', 'cardtokenizationapi', 'directpayapiv2', 'refundtransactionapi']);
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });

        $this->user = new User;
        $this->ticket = new Ticket;
        $this->Warning = new Warning;
        $this->SendMail = new SendMail;
        $this->Transaction = new Transaction;
        $this->Notification = new Notification;
        $this->payoutSchedule = new PayoutSchedule;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function home(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        $start_date = date(\Carbon\Carbon::today()->subDays(6));
        $end_date = date('Y-m-d 23:59:59');

        if (auth()->user()->main_user_id == 0) {
            $user_id = auth()->user()->id;
        } else {
            $user_id = auth()->user()->main_user_id;
        }

        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        $transaction = DB::table("transactions as t")
            ->selectRaw(
                "
                        sum(if(t.status = '1', amount, 0.00)) as successfullV,
                        sum(if(t.status = '1', 1, 0)) as successfullC,
                        round((100*sum(if(t.status = '1', 1, 0)))/(sum(if(t.status = '0', 1, 0))+sum(if(t.status = '1', 1, 0))) , 2) as successfullP,
                        
                        sum(if(t.status = '0' , amount,0.00 )) as declinedV,
                        sum(if(t.status = '0', 1, 0)) as declinedC,
                        round((100*sum(if(t.status = '0', 1, 0)))/(sum(if(t.status = '0', 1, 0))+sum(if(t.status = '1', 1, 0))) ,2) as declinedP,
                        
                        sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', amount, 0)) as chargebackV,
                        sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)) as chargebackC,
                        round((100*sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2) as chargebackP,
                        
                        sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', amount, 0)) as suspiciousV,
                        sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)) as suspiciousC,
                        round((100*sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2) as suspiciousP,

                        sum(if(t.status = '1' and t.is_retrieval = '1' and t.is_retrieval_remove = '0', amount, 0)) as retrievalV,
                        sum(if(t.status = '1' and t.is_retrieval = '1' and t.is_retrieval_remove = '0', 1, 0)) as retrievalC,
                        round((100*sum(if(t.status = '1' and t.is_retrieval = '1' and t.is_retrieval_remove = '0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2) as retrievalP,
                        
                        sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', amount, 0)) as refundV,
                        sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0)) as refundC,
                        round((100*sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2) as refundP,
                        round((100*sum(if(t.status = '5', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2) as blockP",



            )
            ->where('t.user_id', $user_id)
            ->whereNotIn('t.payment_gateway_id', $payment_gateway_id)
            ->where('t.deleted_at', NULL)
            ->first();
        $transactionWeek = DB::table("transactions as t")
            ->selectRaw(
                "
                        sum(if(t.status = '1', amount, 0.00)) as successfullV,
                        sum(if(t.status = '1', 1, 0)) as successfullC,
                        round((100*sum(if(t.status = '1', 1, 0)))/(sum(if(t.status = '0', 1, 0))+sum(if(t.status = '1', 1, 0))) , 2) as successfullP,
                        
                        sum(if(t.status = '0' , amount,0.00 )) as declinedV,
                        sum(if(t.status = '0', 1, 0)) as declinedC,
                        round((100*sum(if(t.status = '0', 1, 0)))/(sum(if(t.status = '0', 1, 0))+sum(if(t.status = '1', 1, 0))) ,2) as declinedP,
                        
                        sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', amount, 0)) as chargebackV,
                        sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)) as chargebackC,
                        round((100*sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2) as chargebackP,
                        
                        sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', amount, 0)) as suspiciousV,
                        sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)) as suspiciousC,
                        round((100*sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2) as suspiciousP,
                        
                        sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', amount, 0)) as refundV,
                        sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0)) as refundC,
                        round((100*sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2) as refundP",
            )
            ->where('t.user_id', $user_id)
            ->whereNotIn('t.payment_gateway_id', $payment_gateway_id)
            ->where('t.deleted_at', NULL)
            ->where('t.transaction_date', '>=', $start_date)
            ->where('t.transaction_date', '<=', $end_date)
            ->first();

        $transactionsLine = DB::table("transactions as t")->select([
            DB::raw('DATE_FORMAT(DATE(transaction_date), "%d-%b") AS date'),
            DB::raw("sum(if(t.status = '1', amount, 0.00)) as successfullV"),
            DB::raw("sum(if(t.status = '1', 1, 0)) as successTransactions"),
            DB::raw("sum(if(t.status = '0', 1, 0)) as declinedTransactions"),
            DB::raw("sum(if(t.status = '0' , amount,0.00 )) as declinedV")
        ])
            ->where('t.transaction_date', '>=', $start_date)
            ->where('t.transaction_date', '<=', $end_date)
            ->where('t.user_id', $user_id)
            ->whereNotIn('t.payment_gateway_id', $payment_gateway_id)
            ->where('t.deleted_at', NULL)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();
        $latestTransactionsData = $this->Transaction->getLatestTransactionsDash();
        $date = \Carbon\Carbon::today()->subDays(6)->format("Y-m-d");
        $inputs['user_id'] = $user_id;
        $inputs['for'] = 'Weekly';
        $TransactionSummary = $this->Transaction->getTransactionSummaryRP($inputs, 1);
        return view('home', compact('transaction', 'transactionWeek', 'transactionsLine', 'TransactionSummary','latestTransactionsData'));
    }

    public function getTransactionBreakUp(Request $request)
    {
        $input = $request->except('_token');

        if ($input['selectedValue'] == 1) {
            $start_date = date('Y-m-d 23:59:59');
        } elseif ($input['selectedValue'] == 2) {
            $start_date = date(\Carbon\Carbon::today()->subDays(6));
        } elseif ($input['selectedValue'] == 3) {
            $start_date = date(\Carbon\Carbon::today()->subDays(31));
        }

        $end_date = date('Y-m-d 23:59:59');

        if (auth()->user()->main_user_id == 0) {
            $user_id = auth()->user()->id;
        } else {
            $user_id = auth()->user()->main_user_id;
        }

        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        $transaction = DB::table("transactions as t")
            ->selectRaw(
                "
                        sum(if(t.status = '1', amount, 0.00)) as successfullV,
                        sum(if(t.status = '1', 1, 0)) as successfullC,
                        round((100*sum(if(t.status = '1', 1, 0)))/(sum(if(t.status = '0', 1, 0))+sum(if(t.status = '1', 1, 0))) , 2) as successfullP,
                        
                        sum(if(t.status = '0' , amount,0.00 )) as declinedV,
                        sum(if(t.status = '0', 1, 0)) as declinedC,
                        round((100*sum(if(t.status = '0', 1, 0)))/(sum(if(t.status = '0', 1, 0))+sum(if(t.status = '1', 1, 0))) ,2) as declinedP,
                        
                        sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', amount, 0)) as chargebackV,
                        sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)) as chargebackC,
                        round((100*sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2) as chargebackP,
                        
                        sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', amount, 0)) as suspiciousV,
                        sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)) as suspiciousC,
                        round((100*sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2) as suspiciousP,
                        
                        sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', amount, 0)) as refundV,
                        sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0)) as refundC,
                        round((100*sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2) as refundP",
            )
            ->where('t.user_id', $user_id)
            ->whereNotIn('t.payment_gateway_id', $payment_gateway_id)
            ->where('t.deleted_at', NULL);
        if ($input['selectedValue'] == 1) {
            $transaction = $transaction->where('transaction_date', '<=', date('Y-m-d 23:59:59'))
                ->where('transaction_date', '>=', date('Y-m-d 00:00:00'));
        } else {
            $transaction = $transaction->where('transaction_date', '>=', $start_date)
                ->where('transaction_date', '<=', $end_date);
        }
        $transaction = $transaction->first();

        return response()->json([
            'status' => '1',
            'successfullC' => $transaction->successfullC,
            'declinedC' => $transaction->declinedC,
            'chargebackC' => $transaction->chargebackC,
            'suspiciousC' => $transaction->suspiciousC,
            'refundC' => $transaction->refundC
        ]);
    }

    public function transactionSummaryReport(Request $request)
    {

        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (auth()->user()->main_user_id == 0) {
            $user_id = auth()->user()->id;
        } else {
            $user_id = auth()->user()->main_user_id;
        }
        $input['user_id'] = $user_id;
        $TransactionSummary = $this->Transaction->getTransactionSummaryRP($input, 1);
        return view('front.transaction_summary.report', compact('TransactionSummary'));
    }

    public function transactionSummary(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['start_date'])) {
            $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
        } else {
            $start_date = date(\Carbon\Carbon::today()->subDays(6));
        }

        if (isset($input['end_date'])) {
            $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));
        } else {
            $end_date = date('Y-m-d 23:59:59');
        }


        if (auth()->user()->main_user_id == 0) {
            $user_id = auth()->user()->id;
        } else {
            $user_id = auth()->user()->main_user_id;
        }

        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];
        $input['user_id'] = $user_id;
        $TransactionSummary = $this->Transaction->getTransactionSummaryRP($input, 1);
        // dd($TransactionSummary);
        return view('front.transaction_summary.index', compact('TransactionSummary'));
    }

    public function toDelimitedString($array)
    {
        $data = '';
        foreach ($array as $value) {
            $data .= '"' . implode(",", $value) . ' \n" + ';
        }
        $data = rtrim($data, ' ');
        return rtrim($data, '+');
    }

    public function getDashboardData(Request $request)
    {

        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (Auth::user()->main_user_id != '0')
            $userID = Auth::user()->main_user_id;
        else
            $userID = Auth::user()->id;

        // Latest Transactions Data
        $latestTransactionsData = $this->Transaction->getLatestTransactionsDash();
        $html_latestTransactionsData = view('latestTransactionsData', compact('latestTransactionsData'))->render();

        return response()->json([
            'success' => 1,
            'latestTransactionsData' => $html_latestTransactionsData,
        ]);
    }

    public function profile()
    {
        $data = $this->user::where('id', Auth::user()->id)->first();

        return view('front.profile', compact('data'));
    }

    public function updatePasswordIndex(){
        return view('front.password');
    }

    public function securitySettings()
    {
        $data = Auth::user();

        return view('front.security_settings', compact('data'));
    }



    public function updateProfile(Request $request, $id)
    {
        $user = \DB::table('users')->where('id', $id)->first();
        $this->validate(
            $request,
            [
                'name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
                'email' => ['required', Rule::unique('users')->ignore($id)->whereNull('deleted_at')],
            ],
            [
                'name.regex' => 'Please Enter Only Alphanumeric Characters.'
            ]
        );
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $data["id"] = $user->id;
        $data['token'] = Str::random(40) . time();
        $input['token'] = $data['token'];
        $data["name"] = $input["name"];
        $data["email"] = $input["email"];
        $this->user->updateData($id, $input);
        notificationMsg('success', 'Your profile details have been updated successfully.');
        return redirect()->back();
    }

    public function resendEmailProfile()
    {
        $user = \DB::table('users')->where('id', auth()->user()->id)->first();
        if (!is_null($user)) {
            $data["id"] = $user->id;
            $data['token'] = \Str::random(40) . time();
            $data["name"] = $user->name;
            $data["email"] = $user->email_changes;
            \DB::table('users')->where('id', auth()->user()->id)->update(['token' => $data['token']]);
            Mail::to($user->email)->send(new userEmailChange($data));
            notificationMsg('success', 'You will shortly receive an email to activate your new email.');
        } else {
            notificationMsg('error', 'Something went Wrong.!');
        }
        return redirect()->back();
    }

    public function changePass(Request $request)
    {
        $this->validate(
            $request,
            [
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ],
            ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']
        );

        if (Hash::check($request->input('current_password'), auth()->user()->password)) {
            $input = \Arr::except($request->all(), array('_token', 'password_confirmation', 'current_password'));

            \DB::table('users')
                ->where('id', auth()->guard('web')->user()->id)
                ->update(['password' => bcrypt($input['password'])]);
            $notification = [
                'user_id' => auth()->guard('web')->user()->id,
                'sendor_id' => auth()->guard('web')->user()->id,
                'type' => 'user',
                'title' => 'Password Reset',
                'body' => 'Password Updated successfully',
                'url' => '/dashboard',
                'is_read' => '0'
            ];

            $realNotification = addNotification($notification);
            \Session::put('success', 'Your Password successfully Updated');

            addToLog('Password Change Successfully.', $input, 'general');

            return Redirect::route('setting');
        } else {
            $input = \Arr::except($request->all(), array('_token', 'password_confirmation'));

            \Session::put('error', 'Your old password is wrong!');

            addToLog('Your old password is wrong!', $input, 'general');

            return Redirect::route('setting');
        }
    }

    public function pagenotfound()
    {
        return view('front.pagenotfound');
    }

    public function userBankdetails(Request $request)
    {
        $bank = UserBankDetails::where('user_id', Auth::user()->id)->first();
        return view('front.bankDetails')->with('bank', $bank);
    }

    public function updateUserBankDetail(UserBankDetailFormRequest $request)
    {
        $input = \Arr::except($request->all(), array('_token'));
        $input['user_id'] = Auth::user()->id;
        if (
            isset($input['name']) || isset($input['address']) ||
            isset($input['aba_routing']) || isset($input['swift_code']) ||
            isset($input['iban']) || isset($input['account_name']) ||
            isset($input['account_number']) || isset($input['account_holder_address']) ||
            isset($input['additional_information'])
        ) {

            if (isset($input['name'])) {
                if (check_alpha_numeric_string($input['name']) == 0) {
                    return back()->with('error', 'Please Enter Only Alphanumeric Characters in Bank Name.');
                }
            }

            if (isset($input['address'])) {
                if (check_address_string($input['address']) == 0) {
                    return back()->with('error', 'Please Enter Only Alphanumeric Characters in Address.');
                }
            }

            if (isset($input['aba_routing'])) {
                if (check_alpha_numeric_string($input['aba_routing']) == 0) {
                    return back()->with('error', 'Please Enter Only Alphanumeric Characters in ABA Routing.');
                }
            }

            if (isset($input['swift_code'])) {
                if (check_alpha_numeric_string($input['swift_code']) == 0) {
                    return back()->with('error', 'Please Enter Only Alphanumeric Characters in SWIFT Code/BIC.');
                }
            }

            if (isset($input['iban'])) {
                if (check_alpha_numeric_string($input['iban']) == 0) {
                    return back()->with('error', 'Please Enter Only Alphanumeric Characters in IBAN.');
                }
            }

            if (isset($input['account_name'])) {
                if (check_alpha_numeric_string($input['account_name']) == 0) {
                    return back()->with('error', 'Please Enter Only Alphanumeric Characters in Account Name.');
                }
            }

            if (isset($input['account_number'])) {
                if (check_alpha_numeric_string($input['account_number']) == 0) {
                    return back()->with('error', 'Please Enter Only Alphanumeric Characters in Account Number.');
                }
            }

            if (isset($input['account_holder_address'])) {
                if (check_address_string($input['account_holder_address']) == 0) {
                    return back()->with('error', 'Please Enter Only Alphanumeric Characters in Account Holder Address.');
                }
            }

            if (isset($input['additional_information'])) {
                if (check_address_string($input['additional_information']) == 0) {
                    return back()->with('error', 'Please Enter Only Alphanumeric Characters in Additional Information.');
                }
            }

            $getBankDetails = UserBankDetails::where('user_id', Auth::user()->id)->first();

            if ($getBankDetails) {
                UserBankDetails::where('user_id', Auth::user()->id)->update($input);
                return back()->with('success', 'Bank Details updated successfully!');
            } else {
                UserBankDetails::create($input);
                return back()->with('success', 'Bank Details saved successfully!');
            }
        } else {
            return back()->with('error', 'Something is wrong.!');
        }
    }

    public function summaryReport()
    {
        return view('front.transaction_summary.summary_report');
    }

    public function cardSummaryReport(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (auth()->user()->main_user_id == 0) {
            $user_id = auth()->user()->id;
        } else {
            $user_id = auth()->user()->main_user_id;
        }

        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        if (empty($input)) {
            $input['for'] = 'Daily';
        }
        $input['user_id'] = $user_id;
        $input['groupBy'] = 'card_type';
        $input['SelectFields'] = ['card_type'];
        $transactionssummary = $this->Transaction->getSummaryReportData($input);
        $transactions_summary = $this->Transaction->PorcessSumarryData('CardTypeSumamry', $transactionssummary);
        $card_type = config('card.type');

        return view("front.transaction_summary.card_summary_report", compact('payment_gateway_id', 'transactions_summary', 'card_type'));
    }

    public function paymentStatusSummaryReport(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (auth()->user()->main_user_id == 0) {
            $user_id = auth()->user()->id;
        } else {
            $user_id = auth()->user()->main_user_id;
        }
        if (empty($input)) {
            $input['for'] = 'Daily';
        }
        $input['user_id'] = $user_id;
        $input['groupBy'] = ['transactions.user_id', 'transactions.currency'];
        $input['SelectFields'] = ['transactions.user_id', 'applications.business_name'];
        $input['JoinTable'] = [
            'table' => 'applications',
            'condition' => 'applications.user_id',
            'conditionjoin' => 'transactions.user_id'
        ];

        $transactionssummary = $this->Transaction->getSummaryReportData($input);
        $arr_t_data = $this->Transaction->PorcessSumarryData('PaymentSsummary', $transactionssummary);

        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        $payment_status = array('1' => 'Success', '2' => 'Declined', '3' => 'Chargeback', '4' => 'Refund', '5' => 'Suspicious', '6' => 'Retrieval', '7' => 'Block');
        $payment_status_class = array('1' => 'text-success', '2' => 'text-danger', '3' => 'text-info', '4' => 'text-info', '5' => 'text-info', '6' => 'text-info', '7' => 'text-info');

        return view("front.transaction_summary.payment_summary_report", compact('payment_gateway_id', 'arr_t_data', 'payment_status', 'payment_status_class'));
    }

    public function usermidSummaryReport(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (auth()->user()->main_user_id == 0) {
            $user_id = auth()->user()->id;
        } else {
            $user_id = auth()->user()->main_user_id;
        }

        if (empty($input)) {
            $input['for'] = 'Daily';
        }
        $input['user_id'] = $user_id;
        $input['groupBy'] = ['transactions.payment_gateway_id', 'transactions.currency'];
        $input['SelectFields'] = ['transactions.payment_gateway_id', 'middetails.bank_name'];
        $input['JoinTable'] = [
            'table' => 'middetails',
            'condition' => 'middetails.id',
            'conditionjoin' => 'transactions.payment_gateway_id'
        ];
        $transactionssummary = $this->Transaction->getSummaryReportData($input);
        $transactions_summary = $this->Transaction->PorcessSumarryData('midSummary', $transactionssummary);

        $payment_gateway_id = \DB::table('middetails')->get();
        $companyName = \DB::table('applications')
            ->join('users', 'users.id', 'applications.user_id')
            ->pluck('business_name', 'user_id')->toArray();

        return view("front.transaction_summary.mid_summary_report", compact('transactions_summary', 'companyName', 'payment_gateway_id'));
    }

    public function usermidSummaryReportOnCountry(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (auth()->user()->main_user_id == 0) {
            $user_id = auth()->user()->id;
        } else {
            $user_id = auth()->user()->main_user_id;
        }
        if (empty($input)) {
            $input['for'] = 'Daily';
        }
        $input['user_id'] = $user_id;
        $input['groupBy'] = ['transactions.country'];
        $input['SelectFields'] = ['transactions.country'];
        $transactionssummary = $this->Transaction->getSummaryReportData($input);
        $transactions_summary = $this->Transaction->PorcessSumarryData('CountrySummary', $transactionssummary);
        $payment_gateway_id = \DB::table('middetails')->get();
        $companyName = \DB::table('applications')
            ->join('users', 'users.id', 'applications.user_id')
            ->pluck('business_name', 'user_id')->toArray();

        return view("front.transaction_summary.mid_summary_report_on_country", compact('payment_gateway_id', 'companyName', 'transactions_summary'));
    }


    public function reasonReport(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (auth()->user()->main_user_id == 0) {
            $user_id = auth()->user()->id;
        } else {
            $user_id = auth()->user()->main_user_id;
        }
        $finalId = [];
        if (!isset($input['noList'])) {
            $input['noList'] = 10;
        }
        $input['user_id'] = $user_id;
        $data = $this->Transaction->getReasonReportData($input);
        // $data = $data->groupby('bank_name');
        $arrId = [];
        $diff_count = $total_txn = $success_fail_count = $reason_count = [];
        $ArrReasonData = [];
        if (!empty($data)) {
            $arrId = $data->pluck('id')->all();
            foreach ($data as $ky => $val) {
                $uniq = md5($val->txn_status . $val->card_type . $val->currency . $val->reason);
                if (isset($ArrReasonData[$uniq])) {
                    $ArrReasonData[$uniq]['transaction_count'] += $val->transaction_count;
                } else {
                    $ArrReasonData[$uniq]['currency'] = $val->currency;
                    $ArrReasonData[$uniq]['card_type'] = $val->card_type;
                    $ArrReasonData[$uniq]['reason'] = $val->reason;
                    $ArrReasonData[$uniq]['transaction_count'] = $val->transaction_count;
                }
                if (isset($success_fail_count[$val->txn_status])) {
                    $success_fail_count[$val->txn_status] += $val->transaction_count;
                } else {
                    $success_fail_count[$val->txn_status] = $val->transaction_count;
                }
            }
        }
        return view('front.transaction_summary.reason_report', compact('data', 'arrId', 'ArrReasonData', 'success_fail_count', ));
    }

    public function merchantCountrywiseTransactionReport(Request $request)
    {

        $countries = $this->Transaction->getAllTransactionCountry();

        $input = \Arr::except($request->all(), array('_token', '_method'));
        $finalId = [];
        if (!isset($input['noList'])) {
            $input['noList'] = 10;
        }
        if (auth()->user()->main_user_id == 0) {
            $user_id = auth()->user()->id;
        } else {
            $user_id = auth()->user()->main_user_id;
        }
        $input['user_id'] = $user_id;
        $data = $this->Transaction->getMerchantCountryWiseTxnReportData($input);
        $data = $data->sortBy('country_name');

        $ArrReasonData = $diff_count = $total_txn = $success_fail_count = $success_fail_cntry_count = [];
        if (!empty($data)) {
            $rowsp = 1;
            foreach ($data as $ky => $val) {
                $uniq = md5($val->card_type . $val->country);
                if (isset($ArrReasonData[$uniq])) {
                    $ArrReasonData[$uniq]['success_count'] += $val->success_count;
                    $ArrReasonData[$uniq]['success_percentage'] += $val->success_percentage;
                    $ArrReasonData[$uniq]['declined_count'] += $val->declined_count;
                    $ArrReasonData[$uniq]['declined_percentage'] += $val->declined_percentage;
                } else {
                    $ArrReasonData[$uniq]['country'] = $val->country;
                    $ArrReasonData[$uniq]['card_type'] = $val->card_type;
                    $ArrReasonData[$uniq]['success_count'] = $val->success_count;
                    $ArrReasonData[$uniq]['success_percentage'] = $val->success_percentage;
                    $ArrReasonData[$uniq]['declined_count'] = $val->declined_count;
                    $ArrReasonData[$uniq]['declined_percentage'] = $val->declined_percentage;
                }

                if (isset($success_fail_count['success'])) {
                    $success_fail_count['success'] += $val->success_count;
                } else {
                    $success_fail_count['success'] = $val->success_count;
                }
                if (isset($success_fail_count['decline'])) {
                    $success_fail_count['decline'] += $val->declined_count;
                } else {
                    $success_fail_count['decline'] = $val->declined_count;
                }
                $uniqC = $val->country;
                if (isset($success_fail_cntry_count[$uniqC])) {
                    $success_fail_cntry_count[$uniqC]['success_count'] += $val->success_count;
                    $success_fail_cntry_count[$uniqC]['declined_count'] += $val->declined_count;
                    $success_fail_cntry_count[$uniqC]['rowsp'] += +1;
                } else {
                    $success_fail_cntry_count[$uniqC]['success_count'] = $val->success_count;
                    $success_fail_cntry_count[$uniqC]['declined_count'] = $val->declined_count;
                    $success_fail_cntry_count[$uniqC]['country_name'] = $val->country_name;
                    $success_fail_cntry_count[$uniqC]['rowsp'] = 1;
                }
            }
        }
        return view(
            'front.transaction_summary.merchant_countrywise_transaction_report',
            compact(
                'data',
                'success_fail_count',
                'ArrReasonData',
                'success_fail_cntry_count',
                'countries'
            )
        );
    }

    public function otpRequired(Request $request)
    {
        $user = Auth::user();
        $user->is_otp_required = $request->is_otp ? '1' : '0';

        if ($user->save()) {
            return response()->json(['success' => 1]);
        } else {
            return response()->json(['success' => 0]);
        }
    }

    public function userRatesFee()
    {
        return view('front.user_rates_fee');
    }

    public function updatePassword(Request $request)
    {

        $this->validate(
            $request,
            [
                'password' => 'required|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ],
            ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']
        );

        $input = \Arr::except($request->all(), array('_token', 'password_confirmation'));
        try {
            \DB::table('users')->where('id', auth()->user()->id)->update(['password' => bcrypt($input['password'])]);
            \Session::put('success', 'Your password has been successfully updated.');
            return Redirect::back();
        } catch (Exception $e) {
            \Session::put('error', 'Something went wrong with your request. Kindly try again');
            return Redirect::back();
        }
            
        
    }
}