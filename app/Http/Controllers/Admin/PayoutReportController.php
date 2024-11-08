<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\AdminAction;
use App\MIDDetail;
use App\User;
use App\Application;
use App\PayoutReports;
use App\PayoutReportsChild;
use App\Transaction;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GenerateReportExport;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\DB;
use App\AgentPayoutReport;
use App\AgentPayoutReportChild;
use Illuminate\Support\Facades\Storage;
use Mail;
use App\Mail\ShowReport;
use App\Exports\MerchantPayoutReportExport;

class PayoutReportController extends AdminController
{

    protected $MIDDetail, $PayoutReports, $PayoutReportsChild, $User, $Application, $transaction;

    public function __construct()
    {
        parent::__construct();
        $this->MIDDetail = new MIDDetail;
        $this->PayoutReports = new PayoutReports;
        $this->PayoutReportsChild = new PayoutReportsChild;
        $this->User = new User;
        $this->Application = new Application;
        $this->transaction = new Transaction;
    }

    public function index(Request $request)
    {
        $companyName = $this->Application::select('user_id', 'business_name')->orderBy('id', 'desc')->get();
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $payment_gateway_id = \DB::table('middetails')->get();
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $dataT = $this->PayoutReports->getAllReportData($noList, $input);
        $arrId = [];
        if (!empty($dataT)) {
            $arrId = $dataT->pluck('id')->all();
        }
        return view("admin.payoutReport.index", compact('companyName', 'payment_gateway_id', 'dataT', 'arrId'));
    }

    public function indexNew(Request $request)
    {
        $dataNew = PayoutReports::select(DB::raw('MAX(id) as id'))->groupBy('user_id')->get()->pluck("id")->toArray();
        $companyName = $this->Application::select('user_id', 'business_name')->orderBy('id', 'desc')->get();
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $payment_gateway_id = \DB::table('middetails')->get();
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $dataT = $this->PayoutReports->getAllReportData($noList, $input);
        $arrId = [];
        if (!empty($dataT)) {
            $arrId = $dataT->pluck('id')->all();
        }
        return view("admin.payoutReport.indexNew", compact('companyName', 'payment_gateway_id', 'dataT', 'arrId', 'dataNew'));
    }

    public function storeNew(Request $request)
    {
        $this->validate($request, [
            'end_date' => 'required',
            'user_id' => 'required',
            'show_client_side' => 'nullable|in:1',
        ], [
            'end_date.required' => 'This field is required.',
            'user_id.required' => 'This field is required.',
        ]);
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $input['end_date'] = date('Y-m-d', strtotime($request->end_date));
        if ($input['end_date'] >= date('Y-m-d')) {
            \Session::flash('error', 'End date should be older than today.');
            return redirect()->back();
        }
        // try {
            // * Fetch All rates
            $rates = getAllCurrenciesRates();
            \Log::info(["rates" => $rates]);
            \Log::info(["input" => $input]);
            foreach ($input['user_id'] as $user_id) {
                \Log::info(["user_id" => $user_id]);
                $old_payout_report = PayoutReports::where('user_id', $user_id)->orderBy('id', 'desc')->first();
                if ($old_payout_report == null) {
                    $transaction = Transaction::select('created_at')->where('user_id', $user_id)->first();
                    if ($transaction != null) {
                        $input['start_date'] = date('Y-m-d', strtotime($transaction->created_at));
                        $input['chargebacks_start_date'] = date('Y-m-d', strtotime($transaction->created_at));
                    } else {
                        continue;
                    }
                } else {
                    $input['start_date'] = date('Y-m-d', strtotime($old_payout_report->end_date . ' + 1 days'));
                    $input['chargebacks_start_date'] = date('Y-m-d', strtotime($old_payout_report->chargebacks_end_date . ' + 1 days'));
                }
                $input['chargebacks_end_date'] = date('Y-m-d', strtotime('-1 days'));
                $userData = \DB::table('users')
                    ->select('applications.*', 'users.*')
                    ->join('applications', 'applications.user_id', '=', 'users.id')
                    ->where('users.id', $user_id)
                    ->first();
                    \Log::info(["userData" => $userData]);
                $currencyArray = \DB::table('transactions')
                    ->where('user_id', $user_id)
                    ->where(function ($q) use ($input) {
                        $q->where(function ($query) use ($input) {
                            $query->whereBetween(\DB::raw('DATE(transactions.created_at)'), [$input['start_date'], $input['end_date']]);
                        })
                            ->orWhere(function ($query) use ($input) {
                                $query->whereBetween(\DB::raw('DATE(transactions.chargebacks_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                                    ->orWhereBetween(\DB::raw('DATE(transactions.flagged_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                                    ->orWhereBetween(\DB::raw('DATE(transactions.retrieval_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                                    ->orWhereBetween(\DB::raw('DATE(transactions.refund_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']]);
                            });
                    })
                    ->whereNotIn('payment_gateway_id', ['1', '2'])
                    ->whereNull('deleted_at')
                    ->groupBy('transactions.currency')
                    ->pluck('currency')
                    ->toArray();
                if (empty($currencyArray)) {
                    continue;
                }
                // add APM for merchants
                $user_apms = [];
                $user_apm_ids = [];
                \DB::beginTransaction();
                // try {
                    $prearbitration_transaction = \DB::table('transactions')
                        ->select(DB::raw('count("*") as count'))
                        ->where('user_id', $user_id)
                        ->whereNotIn('payment_gateway_id', ['1', '2'])
                        ->whereNull('deleted_at')
                        ->where('is_pre_arbitration', '1')
                        ->whereBetween(\DB::raw('DATE(transactions.pre_arbitration_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                        ->count();
                    $data = [];
                    $data['user_id'] = $user_id;
                    $data['date'] = date('d/m/Y', time());
                    $data['processor_name'] = 'testpay';
                    $data['company_name'] = $userData->business_name;
                    $data['address'] = '';
                    $data['phone_no'] = $userData->phone_no;
                    $data['start_date'] = $input['start_date'];
                    $data['end_date'] = $input['end_date'];
                    $data['chargebacks_start_date'] = $input['chargebacks_start_date'];
                    $data['chargebacks_end_date'] = $input['chargebacks_end_date'];
                    $data['merchant_discount_rate'] = $userData->merchant_discount_rate; //Crerdit
                    $data['merchant_discount_rate_master'] = $userData->merchant_discount_rate_master_card; //Crerdit
                    $data['merchant_discount_rate_discover'] = $userData->merchant_discount_rate_discover_card;
                    $data['merchant_discount_rate_amex'] = $userData->merchant_discount_rate_amex_card;
                    $data['merchant_discount_rate_crypto'] = $userData->merchant_discount_rate_crypto;
                    $data['merchant_discount_rate_upi'] = $userData->merchant_discount_rate_upi;
                    $data['merchant_discount_rate_apm'] = json_encode($user_apms);
                    $data['rolling_reserve_paercentage'] = $userData->rolling_reserve_paercentage;
                    $data['transaction_fee_paercentage'] = $userData->transaction_fee;
                    $data['declined_fee_paercentage'] = $userData->transaction_fee;
                    $data['refund_fee_paercentage'] = $userData->refund_fee;
                    $data['chargebacks_fee_paercentage'] = $userData->chargeback_fee;
                    $data['flagged_fee_paercentage'] = $userData->flagged_fee;
                    $data['retrieval_fee_paercentage'] = $userData->retrieval_fee;
                    $data['wire_fee'] = 50; // 50
                    $data['invoice_no'] = getReportInvoiceNo();
                    $data['genereted_by'] = 'User';
                    $data['show_client_side'] = $input['show_client_side'] ?? 0;
                    $data['pre_arbitration_fee'] = $prearbitration_transaction * config('custom.pre_arbitration_clause_fee_value');
                    // dd($currencyArray);
                    $current_payout_report = $this->PayoutReports->storeData($data);
                    //addAdminLog(AdminAction::GENERATE_PAYOUT_REPORT, $current_payout_report->id, $data, "Report Generated Successfully!");
                    foreach ($currencyArray as $value) {
                        if ($userData->transaction_fee != 0) {
                            if ($value != "USD") {
                                $convert_transaction_fee_array = getConversionAmount($rates, $value, $userData->transaction_fee);
                                $input['convert_transaction_fee'] = $convert_transaction_fee_array;
                            } else {
                                $input['convert_transaction_fee'] = $userData->transaction_fee;
                            }
                        } else {
                            $input['convert_transaction_fee'] = 0;
                        }
                        $check_visa_transaction = $this->autoReportcheckingTransaction($value, $input, 'Visa', $user_id, $user_apm_ids);
                        // $check_master_transaction = $this->autoReportcheckingTransaction($value, $input, 'MasterCard', $user_id, $user_apm_ids);
                        // $check_amex_transaction = $this->autoReportcheckingTransaction($value, $input, 'Amex', $user_id, $user_apm_ids);
                        // $check_discover_transaction = $this->autoReportcheckingTransaction($value, $input, 'Discover', $user_id, $user_apm_ids);
                        // $check_crypto_transaction = $this->autoReportcheckingTransaction($value, $input, 'Crypto', $user_id, $user_apm_ids);
                        // $check_upi_transaction = $this->autoReportcheckingTransaction($value, $input, 'UPI', $user_id, $user_apm_ids);

                        if ($check_visa_transaction > 0) {
                            $this->autoReportingChild($value, $input, $userData, $user_apm_ids, $current_payout_report, $old_payout_report, 'Visa', $rates);
                        }
                        // if ($check_master_transaction > 0) {
                        //     $this->autoReportingChild($value, $input, $userData, $user_apm_ids, $current_payout_report, $old_payout_report, 'MasterCard', $rates);
                        // }
                        // if ($check_amex_transaction > 0) {
                        //     $this->autoReportingChild($value, $input, $userData, $user_apm_ids, $current_payout_report, $old_payout_report, 'Amex', $rates);
                        // }
                        // if ($check_discover_transaction > 0) {
                        //     $this->autoReportingChild($value, $input, $userData, $user_apm_ids, $current_payout_report, $old_payout_report, 'Discover', $rates);
                        // }
                        // if ($check_crypto_transaction > 0) {
                        //     $this->autoReportingChild($value, $input, $userData, $user_apm_ids, $current_payout_report, $old_payout_report, 'Crypto', $rates);
                        // }
                        // if ($check_upi_transaction > 0) {
                        //     $this->autoReportingChild($value, $input, $userData, $user_apm_ids, $current_payout_report, $old_payout_report, 'UPI', $rates);
                        // }
                        
                    }
                    \DB::commit();
                // } catch (\Exception $e) {
                //     \Log::error(['auto_generated_report_exception' => $e->getMessage()]);
                //     \DB::rollback();
                //     \Session::put('error', 'Error in report generation !');
                //     return redirect()->back();
                // }
            }
            \Session::put('success', 'Report Generated Successfully !');
            return redirect()->back();
        // } catch (\Exception $e) {
        //     \Log::error(['auto_generated_report_exception' => $e->getMessage()]);
        //     \DB::rollback();
        //     \Session::put('error', 'Error in report generation !');
        //     return redirect()->back();
        // }
    }

    public function autoReportcheckingTransaction($value, $input, $type, $user_id, $user_apm_ids)
    {
        $checkTransaction = \DB::table('transactions')
            ->where('user_id', $user_id)
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', array_merge($user_apm_ids, ['1', '2']))
            ->where(function ($q) use ($input) {
                $q->where(function ($query) use ($input) {
                    $query->whereBetween(\DB::raw('DATE(transactions.created_at)'), [$input['start_date'], $input['end_date']]);
                })
                    ->orWhere(function ($query) use ($input) {
                        $query->whereBetween(\DB::raw('DATE(transactions.chargebacks_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                            ->orWhereBetween(\DB::raw('DATE(transactions.flagged_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                            ->orWhereBetween(\DB::raw('DATE(transactions.retrieval_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                            ->orWhereBetween(\DB::raw('DATE(transactions.refund_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']]);
                    });
            })
            ->whereNull('deleted_at');
        // if ($type == 'Crypto') {
        //     $checkTransaction = $checkTransaction->where('payment_type', 'crypto');
        // } elseif ($type == 'UPI') {
        //     $checkTransaction = $checkTransaction->where('payment_type', 'upi');
        // } elseif ($type == 'Visa') {
        //     $checkTransaction = $checkTransaction->where('payment_type', 'card')
        //         ->whereNotIn('card_type', ['1', '3', '4']);
        // } elseif ($type == 'MasterCard') {
        //     $checkTransaction = $checkTransaction->where('payment_type', 'card')
        //         ->where('card_type', '3');
        // } elseif ($type == 'Amex') {
        //     $checkTransaction = $checkTransaction->where('payment_type', 'card')
        //         ->where('card_type', '1');
        // } elseif ($type == 'Discover') {
        //     $checkTransaction = $checkTransaction->where('payment_type', 'card')
        //         ->where('card_type', '4');
        // }
        $checkTransaction = $checkTransaction->count();
        return $checkTransaction;
    }

    public function autoReportcheckingAPMTransaction($value, $input, $user_id, $apm_id)
    {
        $checkTransaction = \DB::table('transactions')
            ->where('user_id', $user_id)
            ->where('currency', $value)
            ->where('payment_gateway_id', $apm_id)
            ->where(function ($q) use ($input) {
                $q->where(function ($query) use ($input) {
                    $query->whereBetween(\DB::raw('DATE(transactions.created_at)'), [$input['start_date'], $input['end_date']]);
                })
                    ->orWhere(function ($query) use ($input) {
                        $query->whereBetween(\DB::raw('DATE(transactions.chargebacks_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                            ->orWhereBetween(\DB::raw('DATE(transactions.flagged_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                            ->orWhereBetween(\DB::raw('DATE(transactions.retrieval_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                            ->orWhereBetween(\DB::raw('DATE(transactions.refund_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']]);
                    });
            })
            ->whereNull('deleted_at')
            ->count();
        return $checkTransaction;
    }

    public function autoReportingChild($value, $input, $userData, $user_apm_ids, $current_payout_report, $old_payout_report, $type, $rates)
    {
        $common_transaction = \DB::table('transactions')
            ->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->where('user_id', $userData->user_id)
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', array_merge($user_apm_ids, ['1', '2']))
            ->whereNull('deleted_at');
        $approved_transaction = clone $common_transaction;
        $approved_transaction = $approved_transaction->where('status', '1')
            ->whereBetween(\DB::raw('DATE(transactions.created_at)'), [$input['start_date'], $input['end_date']])
            ->first();
        $declined_transaction = clone $common_transaction;
        $declined_transaction = $declined_transaction->where('status', '0')
            ->whereBetween(\DB::raw('DATE(transactions.created_at)'), [$input['start_date'], $input['end_date']])
            ->first();
        $chargebacks_transaction = clone $common_transaction;
        $chargebacks_transaction = $chargebacks_transaction->where('chargebacks', '1')
            ->where('chargebacks_remove', '0')
            ->whereBetween(\DB::raw('DATE(transactions.chargebacks_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();
        $refund_transaction = clone $common_transaction;
        $refund_transaction = $refund_transaction->where('refund', '1')
            ->where('chargebacks', "0")
            ->where('refund_remove', '0')
            ->whereBetween(\DB::raw('DATE(transactions.refund_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();
        $total_flagged = clone $common_transaction;
        $total_flagged = $total_flagged->where('is_flagged', '1')
            ->where("is_flagged_remove", "0")
            ->where("chargebacks", "0")
            ->whereBetween(\DB::raw('DATE(transactions.flagged_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();
        $total_retrieval = clone $common_transaction;
        $total_retrieval = $total_retrieval->where('is_retrieval', '1')
            ->where('chargebacks', "0")
            ->where('is_retrieval_remove', '0')
            ->whereBetween(\DB::raw('DATE(transactions.retrieval_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();
        $total_past_refund = clone $common_transaction;
        $total_past_refund = $total_past_refund->where('refund_remove', '1')
            ->whereBetween(\DB::raw('DATE(transactions.refund_remove_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();
        $total_past_flagged = clone $common_transaction;
        $total_past_flagged = $total_past_flagged->where('is_flagged_remove', '1')
            ->whereBetween(\DB::raw('DATE(transactions.flagged_remove_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();
        $total_past_chargeback = clone $common_transaction;
        $total_past_chargeback = $total_past_chargeback->where('chargebacks_remove', '1')
            ->whereBetween(\DB::raw('DATE(transactions.chargebacks_remove_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();
        $total_past_retrieval = clone $common_transaction;
        $total_past_retrieval = $total_past_retrieval->where('is_retrieval_remove', '1')
            ->whereBetween(\DB::raw('DATE(transactions.retrieval_remove_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();

        $childData['user_id'] = $userData->user_id;
        $childData['payoutreport_id'] = $current_payout_report->id;
        $childData["total_transaction_count"] = $approved_transaction->count + $declined_transaction->count;
        $childData["total_transaction_sum"] = $approved_transaction->amount + $declined_transaction->amount;
        $childData['approve_transaction_count'] = $approved_transaction->count;
        $childData['approve_transaction_sum'] = $approved_transaction->amount ?? 0;
        $childData['declined_transaction_count'] = $declined_transaction->count;
        $childData['declined_transaction_sum'] = $declined_transaction->amount ?? 0;
        $childData['chargeback_transaction_count'] = $chargebacks_transaction->count;
        $childData['chargeback_transaction_sum'] = $chargebacks_transaction->amount ?? 0;
        $childData['refund_transaction_count'] = $refund_transaction->count;
        $childData['refund_transaction_sum'] = $refund_transaction->amount ?? 0;
        $childData['flagged_transaction_count'] = $total_flagged->count;
        $childData['flagged_transaction_sum'] = $total_flagged->amount ?? 0;
        $childData['retrieval_transaction_count'] = $total_retrieval->count;
        $childData['retrieval_transaction_sum'] = $total_retrieval->amount ?? 0;
        $childData['currency'] = $value;
        $childData['card_type'] = $type;
        $childData['mdr'] = ($userData->merchant_discount_rate * $approved_transaction->amount) / 100;

        $childData['transaction_fee'] = ($userData->transaction_fee * ($approved_transaction->count + $declined_transaction->count));
        $childData['rolling_reserve'] = ($userData->rolling_reserve_paercentage * $approved_transaction->amount) / 100;
        if ($childData['transaction_fee'] != 0) {
            if ($value != "USD") {
                $transaction_fee_converted = getConversionAmount($rates, $value, $childData['transaction_fee']);
            } else {
                $transaction_fee_converted = $childData['transaction_fee'];
            }
        } else {
            $transaction_fee_converted = 0;
        }
        $childData['refund_fee'] = ($userData->refund_fee * $refund_transaction->count);
        if ($childData['refund_fee'] != 0) {
            if ($value != "USD") {
                $refund_fee_converted = getConversionAmount($rates, $value, $childData['refund_fee']);
            } else {
                $refund_fee_converted = $childData['refund_fee'];
            }
        } else {
            $refund_fee_converted = 0;
        }
        $childData['chargeback_fee'] = ($userData->chargeback_fee * $chargebacks_transaction->count);
        if ($childData['chargeback_fee'] != 0) {
            if ($value != "USD") {
                $chargeback_fee_converted = getConversionAmount($rates, $value, $childData['chargeback_fee']);
            } else {
                $chargeback_fee_converted = $childData['chargeback_fee'];
            }
        } else {
            $chargeback_fee_converted = 0;
        }
        $childData['flagged_fee'] = ($userData->flagged_fee * $total_flagged->count);
        if ($childData['flagged_fee'] != 0) {
            if ($value != "USD") {
                $flagged_fee_converted = getConversionAmount($rates, $value, $childData['flagged_fee']);
            } else {
                $flagged_fee_converted = $childData['flagged_fee'];
            }
        } else {
            $flagged_fee_converted = 0;
        }
        $childData['retrieval_fee'] = ($userData->retrieval_fee * $total_retrieval->count);
        if ($childData['retrieval_fee'] != 0) {
            if ($value != 'USD') {
                $retrieval_fee_converted = getConversionAmount($rates, $value, $childData['retrieval_fee']);
            } else {
                $retrieval_fee_converted = $childData['retrieval_fee'];
            }
        } else {
            $retrieval_fee_converted = 0;
        }
        $childData['remove_past_flagged'] = $total_past_flagged->count;
        $childData['past_flagged_charge_amount'] = ($userData->flagged_fee * $total_past_flagged->count);
        if ($childData['past_flagged_charge_amount'] != 0) {
            if ($value != "USD") {
                $past_flagged_fee_converted = getConversionAmount($rates, $value, $childData['past_flagged_charge_amount']);
            } else {
                $past_flagged_fee_converted = $childData['past_flagged_charge_amount'];
            }
        } else {
            $past_flagged_fee_converted = 0;
        }

        $past_flagged_sum_deduction = (($userData->merchant_discount_rate * $total_past_flagged->amount) / 100) + (($userData->rolling_reserve_paercentage * $total_past_flagged->amount) / 100);
       
        if ($total_past_flagged->amount != 0) {
            $childData['past_flagged_sum'] = ($total_past_flagged->amount) - $past_flagged_sum_deduction - ($input['convert_transaction_fee'] * $total_past_flagged->count);
        } else {
            $childData['past_flagged_sum'] = 0;
        }

        $childData["remove_past_chargebacks"] = $total_past_chargeback->count;
        $childData["past_chargebacks_charge_amount"] = ($userData->chargeback_fee * $total_past_chargeback->count);
        $childData["past_chargebacks_sum"] = $total_past_chargeback->amount;

        $childData["remove_past_retrieval"] = $total_past_retrieval->count;
        $childData["past_retrieval_charge_amount"] = ($userData->retrieval_fee * $total_past_retrieval->count);
        $past_retrieval_sum_deduction = (($userData->merchant_discount_rate * $total_past_retrieval->amount) / 100) + (($userData->rolling_reserve_paercentage * $total_past_retrieval->amount) / 100);
        if ($total_past_retrieval->amount != 0) {
            $childData["past_retrieval_sum"] = ($total_past_retrieval->amount) - $past_retrieval_sum_deduction - ($input['convert_transaction_fee'] * $total_past_retrieval->count);
        } else {
            $childData["past_retrieval_sum"] = 0;
        }

        $returnFlaggedFee = 0;
        $totalChargebackAmount = 0;
        $totalChargebackCount = 0;

        if (isset($old_payout_report)) {
            $payout_start_date = date('Y-m-d', strtotime($old_payout_report->start_date));
            $payout_end_date = date('Y-m-d', strtotime($old_payout_report->end_date));

            $checkedPastFlagged = clone $common_transaction;
            $checkedPastFlagged = $checkedPastFlagged->where('is_flagged', '1')
                ->where('chargebacks', '1')
                ->where(\DB::raw('DATE(transactions.flagged_date)'), '>=', $payout_start_date)
                ->where(\DB::raw('DATE(transactions.flagged_date)'), '<=', $payout_end_date)
                ->whereBetween(\DB::raw('DATE(transactions.chargebacks_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                ->whereNotIn('payment_gateway_id', array_merge($user_apm_ids, ['1', '2']));
            // if ($type == "Crypto") {
            //     $checkedPastFlagged = $checkedPastFlagged->where("payment_type", "crypto");
            // } elseif ($type == "UPI") {
            //     $checkedPastFlagged = $checkedPastFlagged->where("payment_type", "upi");
            // } elseif ($type == "Visa") {
            //     $checkedPastFlagged = $checkedPastFlagged->where('payment_type', 'card')
            //         ->whereNotIn("card_type", ["1", "3", "4"]);
            // } elseif ($type == "MasterCard") {
            //     $checkedPastFlagged = $checkedPastFlagged->where('payment_type', 'card')
            //         ->where("card_type", "3");
            // } elseif ($type == "Amex") {
            //     $checkedPastFlagged = $checkedPastFlagged->where('payment_type', 'card')
            //         ->where("card_type", "1");
            // } elseif ($type == "Discover") {
            //     $checkedPastFlagged = $checkedPastFlagged->where('payment_type', 'card')
            //         ->where("card_type", "4");
            // }
            $checkedPastFlagged = $checkedPastFlagged->first();
            $pastFlaggedChargebackAmount = 0;
            if ($checkedPastFlagged->amount != 0) {
                $pastFlaggedChargebackAmount = ($checkedPastFlagged->amount) - (($userData->merchant_discount_rate * $checkedPastFlagged->amount) / 100) - (($userData->rolling_reserve_paercentage * $checkedPastFlagged->amount) / 100) - ($input['convert_transaction_fee'] * $checkedPastFlagged->count);
            }
            $totalChargebackCount += $checkedPastFlagged->count;

            $checkedPastRefund = clone $common_transaction;
            $checkedPastRefund = $checkedPastRefund->where('refund', '1')
                ->where('chargebacks', '1')
                ->where(\DB::raw('DATE(transactions.refund_date)'), '>=', $payout_start_date)
                ->where(\DB::raw('DATE(transactions.refund_date)'), '<=', $payout_end_date)
                ->whereBetween(\DB::raw('DATE(transactions.chargebacks_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                ->whereNotIn('payment_gateway_id', array_merge($user_apm_ids, ['1', '2']));
            // if ($type == "Crypto") {
            //     $checkedPastRefund = $checkedPastRefund->where("payment_type", "crypto");
            // } elseif ($type == "UPI") {
            //     $checkedPastRefund = $checkedPastRefund->where("payment_type", "upi");
            // } elseif ($type == "Visa") {
            //     $checkedPastRefund = $checkedPastRefund->where('payment_type', 'card')
            //         ->whereNotIn("card_type", ["1", "3", "4"]);
            // } elseif ($type == "MasterCard") {
            //     $checkedPastRefund = $checkedPastRefund->where('payment_type', 'card')
            //         ->where("card_type", "3");
            // } elseif ($type == "Amex") {
            //     $checkedPastRefund = $checkedPastRefund->where('payment_type', 'card')
            //         ->where("card_type", "1");
            // } elseif ($type == "Discover") {
            //     $checkedPastRefund = $checkedPastRefund->where('payment_type', 'card')
            //         ->where("card_type", "4");
            // }
            $checkedPastRefund = $checkedPastRefund->first();
            $pastRefundChargebackAmount = 0;
            if ($checkedPastRefund->amount != 0) {
                $pastRefundChargebackAmount = ($checkedPastRefund->amount) - (($userData->merchant_discount_rate * $checkedPastRefund->amount) / 100) - (($userData->rolling_reserve_paercentage * $checkedPastRefund->amount) / 100) - ($input['convert_transaction_fee'] * $checkedPastRefund->count);
            }
            $totalChargebackCount += $checkedPastRefund->count;

            $checkedPastRetrieval = clone $common_transaction;
            $checkedPastRetrieval = $checkedPastRetrieval->where('is_retrieval', '1')
                ->where('chargebacks', '1')
                ->where(\DB::raw('DATE(transactions.retrieval_date)'), '>=', $payout_start_date)
                ->where(\DB::raw('DATE(transactions.retrieval_date)'), '<=', $payout_end_date)
                ->whereBetween(\DB::raw('DATE(transactions.chargebacks_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                ->whereNotIn('payment_gateway_id', array_merge($user_apm_ids, [1, 2]));
            // if ($type == "Crypto") {
            //     $checkedPastRetrieval = $checkedPastRetrieval->where("payment_type", "crypto");
            // } elseif ($type == "UPI") {
            //     $checkedPastRetrieval = $checkedPastRetrieval->where("payment_type", "upi");
            // } elseif ($type == "Visa") {
            //     $checkedPastRetrieval = $checkedPastRetrieval->where('payment_type', 'card')
            //         ->whereNotIn("card_type", ["1", "3", "4"]);
            // } elseif ($type == "MasterCard") {
            //     $checkedPastRetrieval = $checkedPastRetrieval->where('payment_type', 'card')
            //         ->where("card_type", "3");
            // } elseif ($type == "Amex") {
            //     $checkedPastRetrieval = $checkedPastRetrieval->where('payment_type', 'card')
            //         ->where("card_type", "1");
            // } elseif ($type == "Discover") {
            //     $checkedPastRetrieval = $checkedPastRetrieval->where('payment_type', 'card')
            //         ->where("card_type", "4");
            // }
            $checkedPastRetrieval = $checkedPastRetrieval->first();
            $pastRetrievalChargebackAmount = 0;
            if ($checkedPastRetrieval->amount != 0) {
                $pastRetrievalChargebackAmount = ($checkedPastRetrieval->amount) - (($userData->merchant_discount_rate * $checkedPastRetrieval->amount) / 100) - (($userData->rolling_reserve_paercentage * $checkedPastRetrieval->amount) / 100) - ($input['convert_transaction_fee'] * $checkedPastRetrieval->count);
            }
            $totalChargebackCount += $checkedPastRetrieval->count;
            $totalChargebackAmount = $pastRetrievalChargebackAmount + $pastRefundChargebackAmount + $pastFlaggedChargebackAmount;
            $returnFlaggedFee = ($userData->flagged_fee * $checkedPastFlagged->count) + ($userData->refund_fee * $checkedPastRefund->count) + ($userData->retrieval_fee * $checkedPastRetrieval->count);
        }
        if ($returnFlaggedFee != 0) {
            if ($value != "USD") {
                $childData['past_flagged_fee'] = getConversionAmount($rates, $value, $returnFlaggedFee);
            } else {
                $childData['past_flagged_fee'] = $returnFlaggedFee;
            }
        } else {
            $childData['past_flagged_fee'] = 0;
        }

        $childData['return_fee'] = $totalChargebackAmount;
        $childData['return_fee_count'] = $totalChargebackCount;
        $childData['transactions_fee_total'] = $chargeback_fee_converted + $flagged_fee_converted + $retrieval_fee_converted + $transaction_fee_converted;

        $childData['sub_total'] = $approved_transaction->amount - ($refund_transaction->amount + $chargebacks_transaction->amount + $total_flagged->amount + $total_retrieval->amount);
        $childData['net_settlement_amount'] = $childData['sub_total'] - ($childData['transactions_fee_total'] + $childData['rolling_reserve'] + $childData['mdr']) + $childData['past_flagged_sum'] + $childData['past_retrieval_sum'] + $childData['past_flagged_fee'] + $childData['return_fee'];

        if ($value != "USD") {
            $childData['net_settlement_amount_in_usd'] = getConversionAmountInUsd($rates, $value, $childData['net_settlement_amount']);
        } else {
            $childData['net_settlement_amount_in_usd'] = $childData['net_settlement_amount'];
        }

        $this->PayoutReportsChild->storeData($childData);
    }

    public function autoReportingAPMChild($value, $input, $userData, $apm_id, $current_payout_report, $old_payout_report, $rate, $type, $rates)
    {
        $common_transaction = \DB::table('transactions')
            ->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->where('user_id', $userData->user_id)
            ->where('currency', $value)
            ->where('payment_gateway_id', $apm_id)
            ->whereNull('deleted_at');

        $approved_transaction = clone $common_transaction;
        $approved_transaction = $approved_transaction->where('status', '1')
            ->whereBetween(\DB::raw('DATE(transactions.created_at)'), [$input['start_date'], $input['end_date']])
            ->first();
        $declined_transaction = clone $common_transaction;
        $declined_transaction = $declined_transaction->where('status', '0')
            ->whereBetween(\DB::raw('DATE(transactions.created_at)'), [$input['start_date'], $input['end_date']])
            ->first();
        $chargebacks_transaction = clone $common_transaction;
        $chargebacks_transaction = $chargebacks_transaction->where('chargebacks', '1')
            ->where('chargebacks_remove', '0')
            ->whereBetween(\DB::raw('DATE(transactions.chargebacks_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();
        $refund_transaction = clone $common_transaction;
        $refund_transaction = $refund_transaction->where('refund', '1')
            ->where('chargebacks', "0")
            ->where('refund_remove', '0')
            ->whereBetween(\DB::raw('DATE(transactions.refund_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();
        $total_flagged = clone $common_transaction;
        $total_flagged = $total_flagged->where('is_flagged', '1')
            ->where("is_flagged_remove", "0")
            ->where("chargebacks", "0")
            ->whereBetween(\DB::raw('DATE(transactions.flagged_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();
        $total_retrieval = clone $common_transaction;
        $total_retrieval = $total_retrieval->where('is_retrieval', '1')
            ->where('chargebacks', "0")
            ->where('is_retrieval_remove', '0')
            ->whereBetween(\DB::raw('DATE(transactions.retrieval_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();
        $total_past_refund = clone $common_transaction;
        $total_past_refund = $total_past_refund->where('refund_remove', '1')
            ->whereBetween(\DB::raw('DATE(transactions.refund_remove_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();
        $total_past_flagged = clone $common_transaction;
        $total_past_flagged = $total_past_flagged->where('is_flagged_remove', '1')
            ->whereBetween(\DB::raw('DATE(transactions.flagged_remove_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();
        $total_past_chargeback = clone $common_transaction;
        $total_past_chargeback = $total_past_chargeback->where('chargebacks_remove', '1')
            ->whereBetween(\DB::raw('DATE(transactions.chargebacks_remove_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();
        $total_past_retrieval = clone $common_transaction;
        $total_past_retrieval = $total_past_retrieval->where('is_retrieval_remove', '1')
            ->whereBetween(\DB::raw('DATE(transactions.retrieval_remove_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
            ->first();

        $childData['user_id'] = $userData->user_id;
        $childData['payoutreport_id'] = $current_payout_report->id;
        $childData["total_transaction_count"] = $approved_transaction->count + $declined_transaction->count;
        $childData["total_transaction_sum"] = $approved_transaction->amount + $declined_transaction->amount;
        $childData['approve_transaction_count'] = $approved_transaction->count;
        $childData['approve_transaction_sum'] = $approved_transaction->amount ?? 0;
        $childData['declined_transaction_count'] = $declined_transaction->count;
        $childData['declined_transaction_sum'] = $declined_transaction->amount ?? 0;
        $childData['chargeback_transaction_count'] = $chargebacks_transaction->count;
        $childData['chargeback_transaction_sum'] = $chargebacks_transaction->amount ?? 0;
        $childData['refund_transaction_count'] = $refund_transaction->count;
        $childData['refund_transaction_sum'] = $refund_transaction->amount ?? 0;
        $childData['flagged_transaction_count'] = $total_flagged->count;
        $childData['flagged_transaction_sum'] = $total_flagged->amount ?? 0;
        $childData['retrieval_transaction_count'] = $total_retrieval->count;
        $childData['retrieval_transaction_sum'] = $total_retrieval->amount ?? 0;
        $childData['currency'] = $value;
        $childData['card_type'] = $type;
        $childData['mdr'] = ($rate * $approved_transaction->amount) / 100;
        $childData['transaction_fee'] = ($userData->transaction_fee * ($approved_transaction->count + $declined_transaction->count));
        $childData['rolling_reserve'] = ($userData->rolling_reserve_paercentage * $approved_transaction->amount) / 100;
        if ($childData['transaction_fee'] != 0) {
            if ($value != "USD") {
                $transaction_fee_converted = getConversionAmount($rates, $value, $childData['transaction_fee']);
            } else {
                $transaction_fee_converted = $childData['transaction_fee'];
            }
        } else {
            $transaction_fee_converted = 0;
        }
        $childData['refund_fee'] = ($userData->refund_fee * $refund_transaction->count);
        if ($childData['refund_fee'] != 0) {
            if ($value != "USD") {
                $refund_fee_converted = getConversionAmount($rates, $value, $childData['refund_fee']);
            } else {
                $refund_fee_converted = $childData['refund_fee'];
            }
        } else {
            $refund_fee_converted = 0;
        }
        $childData['chargeback_fee'] = ($userData->chargeback_fee * $chargebacks_transaction->count);
        if ($childData['chargeback_fee'] != 0) {
            if ($value != "USD") {
                $chargeback_fee_converted = getConversionAmount($rates, $value, $childData['chargeback_fee']);
            } else {
                $chargeback_fee_converted = $childData['chargeback_fee'];
            }
        } else {
            $chargeback_fee_converted = 0;
        }
        $childData['flagged_fee'] = ($userData->flagged_fee * $total_flagged->count);
        if ($childData['flagged_fee'] != 0) {
            if ($value != "USD") {
                $flagged_fee_converted = getConversionAmount($rates, $value, $childData['flagged_fee']);
            } else {
                $flagged_fee_converted = $childData['flagged_fee'];
            }
        } else {
            $flagged_fee_converted = 0;
        }
        $childData['retrieval_fee'] = ($userData->retrieval_fee * $total_retrieval->count);
        if ($childData['retrieval_fee'] != 0) {
            if ($value != 'USD') {
                $retrieval_fee_converted = getConversionAmount($rates, $value, $childData['retrieval_fee']);
            } else {
                $retrieval_fee_converted = $childData['retrieval_fee'];
            }
        } else {
            $retrieval_fee_converted = 0;
        }

        $childData['remove_past_flagged'] = $total_past_flagged->count;
        $childData['past_flagged_charge_amount'] = ($userData->flagged_fee * $total_past_flagged->count);
        if ($childData['past_flagged_charge_amount'] != 0) {
            if ($value != "USD") {
                $past_flagged_fee_converted = getConversionAmount($rates, $value, $childData['past_flagged_charge_amount']);
            } else {
                $past_flagged_fee_converted = $childData['past_flagged_charge_amount'];
            }
        } else {
            $past_flagged_fee_converted = 0;
        }
        $past_flagged_sum_deduction = (($rate * $total_past_flagged->amount) / 100) + (($userData->rolling_reserve_paercentage * $total_past_flagged->amount) / 100);

        if ($total_past_flagged->amount != 0) {
            $childData['past_flagged_sum'] = ($total_past_flagged->amount) - $past_flagged_sum_deduction - ($input['convert_transaction_fee'] * $total_past_flagged->count);
        } else {
            $childData['past_flagged_sum'] = 0;
        }

        $childData["remove_past_chargebacks"] = $total_past_chargeback->count;
        $childData["past_chargebacks_charge_amount"] = ($userData->chargeback_fee * $total_past_chargeback->count);
        $childData["past_chargebacks_sum"] = $total_past_chargeback->amount;

        $childData["remove_past_retrieval"] = $total_past_retrieval->count;
        $childData["past_retrieval_charge_amount"] = ($userData->retrieval_fee * $total_past_retrieval->count);
        $past_retrieval_sum_deduction = (($rate * $total_past_retrieval->amount) / 100) + (($userData->rolling_reserve_paercentage * $total_past_retrieval->amount) / 100);
        if ($total_past_retrieval->amount != 0) {
            $childData["past_retrieval_sum"] = ($total_past_retrieval->amount) - $past_retrieval_sum_deduction - ($input['convert_transaction_fee'] * $total_past_retrieval->count);
        } else {
            $childData["past_retrieval_sum"] = 0;
        }

        $returnFlaggedFee = 0;
        $totalChargebackAmount = 0;
        $totalChargebackCount = 0;

        if (isset($old_payout_report)) {
            $payout_start_date = date('Y-m-d', strtotime($old_payout_report->start_date));
            $payout_end_date = date('Y-m-d', strtotime($old_payout_report->end_date));

            $checkedPastFlagged = clone $common_transaction;
            $checkedPastFlagged = $checkedPastFlagged->where('is_flagged', '1')
                ->where('chargebacks', '1')
                ->where(\DB::raw('DATE(transactions.flagged_date)'), '>=', $payout_start_date)
                ->where(\DB::raw('DATE(transactions.flagged_date)'), '<=', $payout_end_date)
                ->whereBetween(\DB::raw('DATE(transactions.chargebacks_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                ->where('payment_gateway_id', $apm_id)
                ->first();

            $pastFlaggedChargebackAmount = 0;
            if ($checkedPastFlagged->amount != 0) {
                $pastFlaggedChargebackAmount = ($checkedPastFlagged->amount) - (($userData->merchant_discount_rate * $checkedPastFlagged->amount) / 100) - (($userData->rolling_reserve_paercentage * $checkedPastFlagged->amount) / 100) - ($input['convert_transaction_fee'] * $checkedPastFlagged->count);
            }
            $totalChargebackCount += $checkedPastFlagged->count;

            $checkedPastRefund = clone $common_transaction;
            $checkedPastRefund = $checkedPastRefund->where('refund', '1')
                ->where('chargebacks', '1')
                ->where(\DB::raw('DATE(transactions.refund_date)'), '>=', $payout_start_date)
                ->where(\DB::raw('DATE(transactions.refund_date)'), '<=', $payout_end_date)
                ->whereBetween(\DB::raw('DATE(transactions.chargebacks_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                ->where('payment_gateway_id', $apm_id)
                ->first();

            $pastRefundChargebackAmount = 0;
            if ($checkedPastRefund->amount != 0) {
                $pastRefundChargebackAmount = ($checkedPastRefund->amount) - (($userData->merchant_discount_rate * $checkedPastRefund->amount) / 100) - (($userData->rolling_reserve_paercentage * $checkedPastRefund->amount) / 100) - ($input['convert_transaction_fee'] * $checkedPastRefund->count);
            }
            $totalChargebackCount += $checkedPastRefund->count;

            $checkedPastRetrieval = clone $common_transaction;
            $checkedPastRetrieval = $checkedPastRetrieval->where('is_retrieval', '1')
                ->where('chargebacks', '1')
                ->where(\DB::raw('DATE(transactions.retrieval_date)'), '>=', $payout_start_date)
                ->where(\DB::raw('DATE(transactions.retrieval_date)'), '<=', $payout_end_date)
                ->whereBetween(\DB::raw('DATE(transactions.chargebacks_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                ->where('payment_gateway_id', $apm_id)
                ->first();

            $pastRetrievalChargebackAmount = 0;
            if ($checkedPastRetrieval->amount != 0) {
                $pastRetrievalChargebackAmount = ($checkedPastRetrieval->amount) - (($userData->merchant_discount_rate * $checkedPastRetrieval->amount) / 100) - (($userData->rolling_reserve_paercentage * $checkedPastRetrieval->amount) / 100) - ($input['convert_transaction_fee'] * $checkedPastRetrieval->count);
            }
            $totalChargebackCount += $checkedPastRetrieval->count;
            $totalChargebackAmount = $pastRetrievalChargebackAmount + $pastRefundChargebackAmount + $pastFlaggedChargebackAmount;
            $returnFlaggedFee = ($userData->flagged_fee * $checkedPastFlagged->count) + ($userData->refund_fee * $checkedPastRefund->count) + ($userData->retrieval_fee * $checkedPastRetrieval->count);
        }
        if ($returnFlaggedFee != 0) {
            if ($value != "USD") {
                $returnFee = getConversionAmount($rates, $value, $returnFlaggedFee);
                $childData['past_flagged_fee'] = $returnFee;
            } else {
                $childData['past_flagged_fee'] = $returnFlaggedFee;
            }
        } else {
            $childData['past_flagged_fee'] = 0;
        }

        $childData['return_fee'] = $totalChargebackAmount;
        $childData['return_fee_count'] = $totalChargebackCount;
        $childData['transactions_fee_total'] = $chargeback_fee_converted + $flagged_fee_converted + $retrieval_fee_converted + $refund_fee_converted + $transaction_fee_converted;
        $childData['sub_total'] = $approved_transaction->amount - ($refund_transaction->amount + $chargebacks_transaction->amount + $total_flagged->amount + $total_retrieval->amount);
        $childData['net_settlement_amount'] = $childData['sub_total'] - ($childData['transactions_fee_total'] + $childData['rolling_reserve'] + $childData['mdr']) + $childData['past_flagged_sum'] + $childData['past_retrieval_sum'] + $childData['past_flagged_fee'] + $childData['return_fee'];
        if ($value != "USD") {
            $childData['net_settlement_amount_in_usd'] = getConversionAmountInUsd($rates, $value, $childData['net_settlement_amount']);
        } else {
            $childData['net_settlement_amount_in_usd'] = $childData['net_settlement_amount'];
        }

        $this->PayoutReportsChild->storeData($childData);
    }

    public function autoPayout(Request $request)
    {
        $companyName = $this->Application::select('user_id', 'business_name')->orderBy('id', 'desc')->get();
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $payment_gateway_id = \DB::table('middetails')->get();
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $dataT = $this->PayoutReports->getAllReportData($noList, $input);
        $arrId = [];
        if (!empty($dataT)) {
            $arrId = $dataT->pluck('id')->all();
        }
        return view("admin.payoutReport.autopayout", compact('companyName', 'payment_gateway_id', 'dataT', 'arrId'));
    }

    public function autoPayoutStore(Request $request)
    {
        $this->validate($request, [
            'end_date' => 'required',
            'user_id' => 'required',
        ], [
            'end_date.required' => 'This field is required.',
            'user_id.required' => 'This field is required.',
        ]);
        $input = \Arr::except($request->all(), array('_token', '_method'));
        //$startDate = date('Y-m-d', strtotime($request->start_date));
        $endDate = date('Y-m-d', strtotime($request->end_date));
        $countUser = count($input["user_id"]);
        $count = 0;
        if (isset($input['show_client_side']) && $input['show_client_side'] != '') {
            $show_client_side = '1';
        } else {
            $show_client_side = '0';
        }
        try {
            for ($u = 0; $u < $countUser; $u++) {
                $user_id = $input["user_id"][$u];
                $payout_report = PayoutReports::where('user_id', $user_id)->orderBy("id", "DESC")->first();
                if ($payout_report == null) {
                    $transaction = Transaction::where('user_id', $user_id)->orderBy("id", "ASC")->first();
                    if ($transaction != null) {
                        $startDate = date('Y-m-d', strtotime($transaction->created_at));
                    } else {
                        continue;
                    }
                } else {
                    $startDate = date('Y-m-d', strtotime($payout_report->end_date . ' + 1 days'));
                }
                $chargebacksStartDate = $startDate;
                $chargebacksEndDate = date('Y-m-d');
                $userData = \DB::table('users')
                    ->select('applications.*', 'users.*')
                    ->join('applications', 'applications.user_id', '=', 'users.id')
                    ->where('users.id', $user_id)
                    ->first();
                $countTransaction = \DB::table('transactions')
                    ->where('user_id', $user_id)
                    ->where("deleted_at", NULL)
                    ->whereNotIn('payment_gateway_id', ['1', '2']);
                if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
                    $countTransaction = $countTransaction->where('payment_gateway_id', $input['payment_gateway_id']);
                }
                $countTransaction = $countTransaction->where(\DB::raw('DATE(transactions.created_at)'), '>=', $startDate)
                    ->where(\DB::raw('DATE(transactions.created_at)'), '<=', $endDate)
                    ->count();
                if ($countTransaction == 0) {
                    \Session::put('warning', 'No transaction found on this date rang');
                    return redirect()->back();
                }

                $currencyArray = \DB::table('transactions')->where('user_id', $user_id)->where("deleted_at", NULL)->whereNotIn('payment_gateway_id', ['1', '2']);
                if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
                    $currencyArray = $currencyArray->where('payment_gateway_id', $input['payment_gateway_id']);
                }
                $currencyArray = $currencyArray->where(\DB::raw('DATE(transactions.created_at)'), '>=', $startDate)
                    ->where(\DB::raw('DATE(transactions.created_at)'), '<=', $endDate)->groupBy("transactions.currency")->pluck("currency")->toArray();
                \DB::beginTransaction();
                try {
                    $data = [];
                    $data['user_id'] = $user_id;
                    $data['date'] = date('d/m/Y', time());
                    $data['processor_name'] = 'testpay';
                    $data['company_name'] = $userData->business_name;
                    $data['address'] = '';
                    $data['phone_no'] = $userData->phone_no;
                    $data['start_date'] = $startDate;
                    $data['end_date'] = $endDate;
                    $data['chargebacks_start_date'] = $chargebacksStartDate;
                    $data['chargebacks_end_date'] = $chargebacksEndDate;
                    $data['merchant_discount_rate'] = $userData->merchant_discount_rate; //Crerdit
                    $data['merchant_discount_rate_master'] = $userData->merchant_discount_rate_master_card; //Crerdit
                    $data['rolling_reserve_paercentage'] = $userData->rolling_reserve_paercentage;
                    $data['transaction_fee_paercentage'] = $userData->transaction_fee;
                    $data['declined_fee_paercentage'] = $userData->transaction_fee;
                    $data['refund_fee_paercentage'] = $userData->refund_fee;
                    $data['chargebacks_fee_paercentage'] = $userData->chargeback_fee;
                    $data['flagged_fee_paercentage'] = $userData->flagged_fee;
                    $data['retrieval_fee_paercentage'] = $userData->retrieval_fee;
                    $data['wire_fee'] = 50; // 50
                    $data['invoice_no'] = getReportInvoiceNo();
                    $data['genereted_by'] = 'User';
                    $data['show_client_side'] = $show_client_side;
                    $reportID = $this->PayoutReports->storeData($data);
                    addAdminLog(AdminAction::GENERATE_PAYOUT_REPORT, $reportID->id, $data, "Report Generated Successfully!");
                    foreach ($currencyArray as $key => $value) {
                        $convertTransactionFee = 0;
                        if ($userData->transaction_fee != 0) {
                            $convertTransactionFeearr = checkSelectedCurrencyTwo('USD', $userData->transaction_fee, $value);
                            $convertTransactionFee = $convertTransactionFeearr["amount"];
                        }
                        $chekTransactionInCurrency = \DB::table('transactions')
                            ->where('user_id', $user_id)
                            ->where('currency', $value)
                            ->where(\DB::raw('DATE(transactions.created_at)'), '>=', $startDate)
                            ->where(\DB::raw('DATE(transactions.created_at)'), '<=', $endDate)
                            ->where("deleted_at", NULL)
                            ->count();
                        if ($chekTransactionInCurrency > 0) {
                            $checkAllOtherTransaction = $this->checkingTransactionNew($value, $input, $startDate, $endDate, "Visa", "Card", $user_id);
                            $checkAllMasterCardTransaction = $this->checkingTransactionNew($value, $input, $startDate, $endDate, "MasterCard", "Card", $user_id);
                            $checkAllAmexCardTransaction = $this->checkingTransactionNew($value, $input, $startDate, $endDate, "Amex", "Card", $user_id);
                            $checkAllDiscoverCardTransaction = $this->checkingTransactionNew($value, $input, $startDate, $endDate, "Discover", "Card", $user_id);
                            $checkAllCryptoTransaction = $this->checkingTransactionNew($value, $input, $startDate, $endDate, "", "Crypto", $user_id);

                            $checkSuccessOtherTransaction = $this->checkOtherTransactionNew($value, $input, $chargebacksStartDate, $chargebacksEndDate, "Visa", "Card", $user_id);
                            $checkSuccessMasterCardTransaction = $this->checkOtherTransactionNew($value, $input, $chargebacksStartDate, $chargebacksEndDate, "MasterCard", "Card", $user_id);
                            $checkSuccessAmexCardTransaction = $this->checkOtherTransactionNew($value, $input, $chargebacksStartDate, $chargebacksEndDate, "Amex", "Card", $user_id);
                            $checkSuccessDiscoverCardTransaction = $this->checkOtherTransactionNew($value, $input, $chargebacksStartDate, $chargebacksEndDate, "Discover", "Card", $user_id);
                            $checkSuccessCryptoTransaction = $this->checkOtherTransactionNew($value, $input, $chargebacksStartDate, $chargebacksEndDate, "", "Crypto", $user_id);

                            if ($checkAllOtherTransaction > 0 || $checkSuccessOtherTransaction > 0) {
                                $this->autoPayoutReportChild($value, $input, $startDate, $endDate, $chargebacksStartDate, $chargebacksEndDate, $userData, $reportID, $payout_report, "Visa", $convertTransactionFee, "Card", $user_id);
                            }
                            if ($checkAllMasterCardTransaction > 0 || $checkSuccessMasterCardTransaction > 0) {
                                $this->autoPayoutReportChild($value, $input, $startDate, $endDate, $chargebacksStartDate, $chargebacksEndDate, $userData, $reportID, $payout_report, "MasterCard", $convertTransactionFee, "Card", $user_id);
                            }
                            if ($checkAllAmexCardTransaction > 0 || $checkSuccessAmexCardTransaction > 0) {
                                $this->autoPayoutReportChild($value, $input, $startDate, $endDate, $chargebacksStartDate, $chargebacksEndDate, $userData, $reportID, $payout_report, "Amex", $convertTransactionFee, "Card", $user_id);
                            }
                            if ($checkAllDiscoverCardTransaction > 0 || $checkSuccessDiscoverCardTransaction > 0) {
                                $this->autoPayoutReportChild($value, $input, $startDate, $endDate, $chargebacksStartDate, $chargebacksEndDate, $userData, $reportID, $payout_report, "Discover", $convertTransactionFee, "Card", $user_id);
                            }
                            if ($checkAllCryptoTransaction > 0 || $checkSuccessCryptoTransaction > 0) {
                                $this->autoPayoutReportChild($value, $input, $startDate, $endDate, $chargebacksStartDate, $chargebacksEndDate, $userData, $reportID, $payout_report, "", $convertTransactionFee, "Crypto", $user_id);
                            }
                        }
                    }
                    \DB::commit();
                } catch (\Exception $e) {
                    \Log::info([
                        'generatedreport_exception' => $e->getMessage()
                    ]);
                    \DB::rollback();
                    \Session::put('error', 'Error in report generation !');
                    return redirect()->back();
                }
            }
            \Session::put('success', 'Report Generated Successfully !');
            return redirect()->back();
        } catch (\Exception $e) {
            \Log::info([
                'generatedreport_exception' => $e->getMessage()
            ]);
            \DB::rollback();
            \Session::put('error', 'Error in report generation !');
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'start_date' => 'required',
            'end_date' => 'required',
            'user_id' => 'required',
            'chargebacks_start_date' => 'required',
            'chargebacks_end_date' => 'required'
        ], [
            'start_date.required' => 'This field is required.',
            'end_date.required' => 'This field is required.',
            'user_id.required' => 'This field is required.',
            'chargebacks_start_date.required' => 'This field is required.',
            'chargebacks_end_date.required' => 'This field is required.',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));
        $input['start_date'] = date('Y-m-d', strtotime($request->start_date));
        $input['end_date'] = date('Y-m-d', strtotime($request->end_date));
        $input['chargebacks_start_date'] = date('Y-m-d', strtotime($request->chargebacks_start_date));
        $input['chargebacks_end_date'] = date('Y-m-d', strtotime($request->chargebacks_end_date));
        $user_id = $input['user_id'];
        $rates = getAllCurrenciesRates();
        $old_payout_report = PayoutReports::where('user_id', $user_id)->orderBy('id', 'desc')->first();
        $userData = \DB::table('users')
            ->select('applications.*', 'users.*')
            ->join('applications', 'applications.user_id', '=', 'users.id')
            ->where('users.id', $user_id)
            ->first();
        $currencyArray = \DB::table('transactions')
            ->where('user_id', $user_id)
            ->where(function ($q) use ($input) {
                $q->where(function ($query) use ($input) {
                    $query->whereBetween(\DB::raw('DATE(transactions.created_at)'), [$input['start_date'], $input['end_date']]);
                })
                    ->orWhere(function ($query) use ($input) {
                        $query->whereBetween(\DB::raw('DATE(transactions.chargebacks_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                            ->orWhereBetween(\DB::raw('DATE(transactions.flagged_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                            ->orWhereBetween(\DB::raw('DATE(transactions.retrieval_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                            ->orWhereBetween(\DB::raw('DATE(transactions.refund_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']]);
                    });
            })
            ->whereNotIn('payment_gateway_id', ['1', '2'])
            ->whereNull('deleted_at')
            ->groupBy('transactions.currency')
            ->pluck('currency')
            ->toArray();
        if (empty($currencyArray)) {
            \Session::put('error', 'No transaction to generate report.');
            return redirect()->back()->withInput();
        }
        // add APM for merchants
        $user_apms = [];
        $user_apm_ids = [];
        if (!empty($userData->apm)) {
            $user_apms = json_decode($userData->apm, true);
            $user_apm_ids = array_column($user_apms, 'apm_id');
        }
        \DB::beginTransaction();
        try {
            $prearbitration_transaction = \DB::table('transactions')
                ->select(DB::raw('count("*") as count'))
                ->where('user_id', $user_id)
                ->whereNotIn('payment_gateway_id', ['1', '2'])
                ->whereNull('deleted_at')
                ->where('is_pre_arbitration', '1')
                ->whereBetween(\DB::raw('DATE(transactions.pre_arbitration_date)'), [$input['chargebacks_start_date'], $input['chargebacks_end_date']])
                ->count();
            $data = [];
            $data['user_id'] = $user_id;
            $data['date'] = date('d/m/Y', time());
            $data['processor_name'] = 'testpay';
            $data['company_name'] = $userData->business_name;
            $data['address'] = '';
            $data['phone_no'] = $userData->phone_no;
            $data['start_date'] = $input['start_date'];
            $data['end_date'] = $input['end_date'];
            $data['chargebacks_start_date'] = $input['chargebacks_start_date'];
            $data['chargebacks_end_date'] = $input['chargebacks_end_date'];
            $data['merchant_discount_rate'] = $userData->merchant_discount_rate; //Crerdit
            $data['merchant_discount_rate_master'] = $userData->merchant_discount_rate_master_card; //Crerdit
            $data['merchant_discount_rate_discover'] = $userData->merchant_discount_rate_discover_card;
            $data['merchant_discount_rate_amex'] = $userData->merchant_discount_rate_amex_card;
            $data['merchant_discount_rate_apm'] = json_encode($user_apms);
            $data['rolling_reserve_paercentage'] = $userData->rolling_reserve_paercentage;
            $data['transaction_fee_paercentage'] = $userData->transaction_fee;
            $data['declined_fee_paercentage'] = $userData->transaction_fee;
            $data['refund_fee_paercentage'] = $userData->refund_fee;
            $data['chargebacks_fee_paercentage'] = $userData->chargeback_fee;
            $data['flagged_fee_paercentage'] = $userData->flagged_fee;
            $data['retrieval_fee_paercentage'] = $userData->retrieval_fee;
            $data['wire_fee'] = 50; // 50
            $data['invoice_no'] = getReportInvoiceNo();
            $data['genereted_by'] = 'User';
            $data['show_client_side'] = $input['show_client_side'] ?? 0;
            $data['pre_arbitration_fee'] = $prearbitration_transaction * config('custom.pre_arbitration_clause_fee_value');

            $current_payout_report = $this->PayoutReports->storeData($data);
            addAdminLog(AdminAction::GENERATE_PAYOUT_REPORT, $current_payout_report->id, $data, "Report Generated Successfully!");
            foreach ($currencyArray as $value) {
                if ($userData->transaction_fee != 0) {
                    if ($value != "USD") {
                        $convert_transaction_fee_array = getConversionAmount($rates, $value, $userData->transaction_fee);
                        $input['convert_transaction_fee'] = $convert_transaction_fee_array;
                    } else {
                        $input['convert_transaction_fee'] = $userData->transaction_fee;
                    }
                } else {
                    $input['convert_transaction_fee'] = 0;
                }
                $check_visa_transaction = $this->autoReportcheckingTransaction($value, $input, 'Visa', $user_id, $user_apm_ids);
                $check_master_transaction = $this->autoReportcheckingTransaction($value, $input, 'MasterCard', $user_id, $user_apm_ids);
                $check_amex_transaction = $this->autoReportcheckingTransaction($value, $input, 'Amex', $user_id, $user_apm_ids);
                $check_discover_transaction = $this->autoReportcheckingTransaction($value, $input, 'Discover', $user_id, $user_apm_ids);
                $check_crypto_transaction = $this->autoReportcheckingTransaction($value, $input, 'Crypto', $user_id, $user_apm_ids);

                if ($check_visa_transaction > 0) {
                    $this->autoReportingChild($value, $input, $userData, $user_apm_ids, $current_payout_report, $old_payout_report, 'Visa', $rates);
                }
                if ($check_master_transaction > 0) {
                    $this->autoReportingChild($value, $input, $userData, $user_apm_ids, $current_payout_report, $old_payout_report, 'MasterCard', $rates);
                }
                if ($check_amex_transaction > 0) {
                    $this->autoReportingChild($value, $input, $userData, $user_apm_ids, $current_payout_report, $old_payout_report, 'Amex', $rates);
                }
                if ($check_discover_transaction > 0) {
                    $this->autoReportingChild($value, $input, $userData, $user_apm_ids, $current_payout_report, $old_payout_report, 'Discover', $rates);
                }
                if ($check_crypto_transaction > 0) {
                    $this->autoReportingChild($value, $input, $userData, $user_apm_ids, $current_payout_report, $old_payout_report, 'Crypto', $rates);
                }
                if (!empty($user_apms)) {
                    foreach ($user_apms as $apm) {
                        $checking_apm_transaction = $this->autoReportcheckingAPMTransaction($value, $input, $user_id, $apm['apm_id']);
                        if ($checking_apm_transaction > 0) {
                            $this->autoReportingAPMChild($value, $input, $userData, $apm['apm_id'], $current_payout_report, $old_payout_report, $apm['apm_mdr'], $apm['bank_name'], $rates);
                        }
                    }
                }
            }
            \DB::commit();
        } catch (\Exception $e) {
            \Log::error(['report_exception' => $e->getMessage()]);
            \DB::rollback();
            \Session::put('error', 'Error in report generation.');
            return redirect()->back();
        }

        \Session::put('success', 'Report generated successfully.');
        return redirect()->back();
    }

    public function generatePayoutReport($input, $startDate, $endDate, $chargebacksStartDate, $chargebacksEndDate, $dailySettlementReport = null)
    {
        // $count = 0;
        if (isset($input['show_client_side']) && $input['show_client_side'] != '') {
            $show_client_side = '1';
        } else {
            $show_client_side = '0';
        }
        $userData = \DB::table('users')
            ->select('applications.*', 'users.*')
            ->join('applications', 'applications.user_id', '=', 'users.id')
            ->where('users.id', $input['user_id'])
            ->first();
        $countTransaction = \DB::table('transactions')
            ->where('user_id', $input['user_id'])
            ->where("deleted_at", NULL)
            ->whereNotIn('payment_gateway_id', ['1', '2']);
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $countTransaction = $countTransaction->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        $countTransaction = $countTransaction->where(\DB::raw('DATE(transactions.created_at)'), '>=', $startDate)
            ->where(\DB::raw('DATE(transactions.created_at)'), '<=', $endDate)
            ->count();
        if ($countTransaction == 0) {
            \Session::put('warning', 'No transaction found on this date rang');
            return redirect()->back();
        }

        $currencyArray = \DB::table('transactions')->where('user_id', $input['user_id'])->where("deleted_at", NULL)->whereNotIn('payment_gateway_id', ['1', '2']);
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $currencyArray = $currencyArray->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        $currencyArray = $currencyArray->where(\DB::raw('DATE(transactions.created_at)'), '>=', $startDate)
            ->where(\DB::raw('DATE(transactions.created_at)'), '<=', $endDate)->groupBy("transactions.currency")->pluck("currency")->toArray();
        \DB::beginTransaction();
        // try {
        $data = [];
        $data['user_id'] = $input['user_id'];
        $data['date'] = date('d/m/Y', time());
        $data['processor_name'] = config("app.name");
        $data['company_name'] = $userData->business_name;
        $data['address'] = '';
        $data['phone_no'] = $userData->phone_no;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        $data['chargebacks_start_date'] = $chargebacksStartDate;
        $data['chargebacks_end_date'] = $chargebacksEndDate;
        $data['merchant_discount_rate'] = $userData->merchant_discount_rate; //Crerdit
        $data['merchant_discount_rate_master'] = $userData->merchant_discount_rate_master_card; //Crerdit
        $data['rolling_reserve_paercentage'] = $userData->rolling_reserve_paercentage;
        $data['transaction_fee_paercentage'] = $userData->transaction_fee;
        $data['declined_fee_paercentage'] = $userData->transaction_fee;
        $data['refund_fee_paercentage'] = $userData->refund_fee;
        $data['chargebacks_fee_paercentage'] = $userData->chargeback_fee;
        $data['flagged_fee_paercentage'] = $userData->flagged_fee;
        $data['retrieval_fee_paercentage'] = $userData->retrieval_fee;
        $data['wire_fee'] = 50; // 50
        $data['invoice_no'] = getReportInvoiceNo();
        $data['genereted_by'] = 'User';
        $data['show_client_side'] = $show_client_side;
        $payout_report = PayoutReports::where('user_id', $input['user_id'])->orderBy("id", "DESC")->first();

        $reportID = $this->PayoutReports->storeData($data);
        addAdminLog(AdminAction::GENERATE_PAYOUT_REPORT, $reportID->id, $data, "Report Generated Successfully!");
        foreach ($currencyArray as $key => $value) {
            $convertTransactionFee = 0;
            if ($userData->transaction_fee != 0) {
                if ($value != 'USD') {
                    $convertTransactionFeearr = checkSelectedCurrencyTwo('USD', $userData->transaction_fee, $value);
                    $convertTransactionFee = $convertTransactionFeearr["amount"];
                } else {
                    $convertTransactionFeearr = $userData->transaction_fee;
                }
            }
            $chekTransactionInCurrency = \DB::table('transactions')
                ->where('user_id', $input['user_id'])
                ->where('currency', $value)
                ->where(\DB::raw('DATE(transactions.created_at)'), '>=', $startDate)
                ->where(\DB::raw('DATE(transactions.created_at)'), '<=', $endDate)
                ->where("deleted_at", NULL)
                ->count();
            if ($chekTransactionInCurrency > 0) {
                $checkAllOtherTransaction = $this->checkingTransaction($value, $input, $startDate, $endDate, "Other");
                $checkAllMasterCardTransaction = $this->checkingTransaction($value, $input, $startDate, $endDate, "MasterCard");
                //echo $checkAllOtherTransaction."<br>".$checkAllMasterCardTransaction."<br>";
                $checkSuccessOtherTransaction = $this->checkOtherTransaction($value, $input, $chargebacksStartDate, $chargebacksEndDate, "Other");
                $checkSuccessMasterCardTransaction = $this->checkOtherTransaction($value, $input, $chargebacksStartDate, $chargebacksEndDate, "MasterCard");
                //echo $checkSuccessOtherTransaction."<br>".$checkSuccessMasterCardTransaction."<br>";exit();
                //echo $checkAllOtherTransaction.$checkAllMasterCardTransaction;exit();
                if ($checkAllOtherTransaction > 0 || $checkSuccessOtherTransaction > 0) {
                    $this->payoutReportChild($value, $input, $startDate, $endDate, $chargebacksStartDate, $chargebacksEndDate, $userData, $reportID, $payout_report, "Other", $convertTransactionFee);
                }
                if ($checkAllMasterCardTransaction > 0 || $checkSuccessMasterCardTransaction > 0) {
                    $this->payoutReportChild($value, $input, $startDate, $endDate, $chargebacksStartDate, $chargebacksEndDate, $userData, $reportID, $payout_report, "MasterCard", $convertTransactionFee);
                }
                // $start_date1 = date('Y-m-d', strtotime('-1 week', strtotime(date('Y-m-d'))));
                // $end_date1 = date('Y-m-d');
                //$childData['declined_fee'] = ($userData->transaction_fee*$declined_transaction->count);
            }
        }
        \DB::commit();

        if (!empty($dailySettlementReport)) {
            $dailySettlementReport->where('user_id', '=', $input['user_id'])
                ->where('paid', '=', '0')
                ->where('start_date', '>=', $startDate)
                ->where(\DB::raw('DATE(end_date)'), '<=', $endDate)
                ->update(['paid' => '1', 'paid_date' => date('Y-m-d H:i:s')]);

            $begin = new \DateTime($endDate);
            $end = new \DateTime();

            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($begin, $interval, $end);

            foreach ($period as $dt) {
                $date = $dt->format("Y-m-d");
                $bulkchargeback = (new \App\Jobs\MerchantDailyPayoutReportQueue($input['user_id'], $date))->delay(now()->addSeconds(2));
                dispatch($bulkchargeback);
            }
        }

        \Session::put('success', 'Report Generated Successfully !');
        return redirect()->back();
        // } catch (\Exception $e) {
        //     // dd($e);
        //     \DB::rollback();
        //     \Session::put('error', 'Error in report generation !');
        //     return redirect()->back();
        // }
    }

    public function checkingTransaction($value, $input, $startDate, $endDate, $type)
    {
        // echo "Currency ".$value."<br>";
        // echo "user_id ".$input['user_id']."<br>";
        // echo "start date".$startDate."<br>";
        // echo "end date".$endDate."<br>";
        $checkTransaction = \DB::table('transactions')
            ->where('user_id', $input['user_id'])
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1', '2'])
            ->where(\DB::raw('DATE(transactions.created_at)'), '>=', $startDate)
            ->where(\DB::raw('DATE(transactions.created_at)'), '<=', $endDate)
            ->where("deleted_at", NULL);
        if ($type == "Other") {
            $checkTransaction = $checkTransaction->where("card_type", "!=", "3");
        } else if ($type == "MasterCard") {
            $checkTransaction = $checkTransaction->where("card_type", "3");
        }
        $checkTransaction = $checkTransaction->count();
        //echo $checkTransaction."<br>";exit()
        return $checkTransaction;
    }

    public function checkOtherTransaction($value, $input, $startDate, $endDate, $type)
    {
        $chekSuccessTransaction = \DB::table('transactions')
            ->whereNotIn('payment_gateway_id', ['1', '2'])
            ->where('user_id', $input['user_id'])
            ->where('currency', $value)
            ->where("deleted_at", NULL)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween(\DB::raw('DATE(transactions.chargebacks_date)'), [$startDate, $endDate])
                    ->orWhereBetween(\DB::raw('DATE(transactions.flagged_date)'), [$startDate, $endDate])
                    ->orWhereBetween(\DB::raw('DATE(transactions.retrieval_date)'), [$startDate, $endDate])
                    ->orWhereBetween(\DB::raw('DATE(transactions.refund_date)'), [$startDate, $endDate]);
            });
        if ($type == "Other") {
            $chekSuccessTransaction = $chekSuccessTransaction->where("card_type", "!=", "3");
        } else if ($type == "MasterCard") {
            $chekSuccessTransaction = $chekSuccessTransaction->where("card_type", "3");
        }
        $chekSuccessTransaction = $chekSuccessTransaction->count();
        return $chekSuccessTransaction;
    }

    public function payoutReportChild($value, $input, $startDate, $endDate, $chargebacksStartDate, $chargebacksEndDate, $userData, $reportID, $payout_report, $type, $convertTransactionFee)
    {
        $approved_transaction = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->where('user_id', $input['user_id'])
            ->where('currency', $value)
            ->where('status', '1')
            ->where("deleted_at", NULL)
            ->whereNotIn('payment_gateway_id', ['1', '2']);
        $declined_transaction = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->where('user_id', $input['user_id'])
            ->where('currency', $value)
            ->where('status', '0')
            ->where("deleted_at", NULL)
            ->whereNotIn('payment_gateway_id', ['1', '2']);
        if ($type == "Other") {
            $approved_transaction = $approved_transaction->where("card_type", "!=", "3");
            $declined_transaction = $declined_transaction->where("card_type", "!=", "3");
        } else if ($type == "MasterCard") {
            $approved_transaction = $approved_transaction->where("card_type", "3");
            $declined_transaction = $declined_transaction->where("card_type", "3");
        }
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $approved_transaction = $approved_transaction->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        $approved_transaction = $approved_transaction->where(\DB::raw('DATE(transactions.created_at)'), '>=', $startDate)
            ->where(\DB::raw('DATE(transactions.created_at)'), '<=', $endDate)
            ->first();
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $declined_transaction = $declined_transaction->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        $declined_transaction = $declined_transaction->where(\DB::raw('DATE(transactions.created_at)'), '>=', $startDate)
            ->where(\DB::raw('DATE(transactions.created_at)'), '<=', $endDate)
            ->first();
        $chargebacks_transaction = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->where('user_id', $input['user_id'])
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1', '2'])
            ->where("deleted_at", NULL)
            ->where('chargebacks', '1')->where('chargebacks_remove', '0');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $chargebacks_transaction = $chargebacks_transaction->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if ($type == "Other") {
            $chargebacks_transaction = $chargebacks_transaction->where("card_type", "!=", "3");
        } else if ($type == "MasterCard") {
            $chargebacks_transaction = $chargebacks_transaction->where("card_type", "3");
        }
        $chargebacks_transaction = $chargebacks_transaction->where(\DB::raw('DATE(transactions.chargebacks_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '<=', $chargebacksEndDate)
            ->first();

        $refund_transaction = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->where('user_id', $input['user_id'])
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1', '2'])
            ->where("deleted_at", NULL)
            ->where('refund', '1')->where('chargebacks', "0")->where('refund_remove', '0');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $refund_transaction = $refund_transaction->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if ($type == "Other") {
            $refund_transaction = $refund_transaction->where("card_type", "!=", "3");
        } else if ($type == "MasterCard") {
            $refund_transaction = $refund_transaction->where("card_type", "3");
        }
        $refund_transaction = $refund_transaction->where(\DB::raw('DATE(transactions.refund_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.refund_date)'), '<=', $chargebacksEndDate)
            ->first();
        $total_flagged = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->where('user_id', $input['user_id'])
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1', '2'])
            ->where("deleted_at", NULL)
            ->where('is_flagged', '1')->where("is_flagged_remove", "0")->where("chargebacks", "0");
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $total_flagged = $total_flagged->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if ($type == "Other") {
            $total_flagged = $total_flagged->where("card_type", "!=", "3");
        } else if ($type == "MasterCard") {
            $total_flagged = $total_flagged->where("card_type", "3");
        }
        $total_flagged = $total_flagged->where(\DB::raw('DATE(transactions.flagged_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.flagged_date)'), '<=', $chargebacksEndDate)
            ->first();
        $total_retrieval = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->where('user_id', $input['user_id'])
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1', '2'])
            ->where("deleted_at", NULL)
            ->where('is_retrieval', '1')->where('chargebacks', "0")->where('is_retrieval_remove', '0');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $total_retrieval = $total_retrieval->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if ($type == "Other") {
            $total_retrieval = $total_retrieval->where("card_type", "!=", "3");
        } else if ($type == "MasterCard") {
            $total_retrieval = $total_retrieval->where("card_type", "3");
        }
        $total_retrieval = $total_retrieval->where(\DB::raw('DATE(transactions.retrieval_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.retrieval_date)'), '<=', $chargebacksEndDate)
            ->first();

        $total_past_refund = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->where('user_id', $input['user_id'])
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1', '2'])
            ->where("deleted_at", NULL)
            ->where('refund_remove', '1');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $total_past_refund = $total_past_refund->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if ($type == "Other") {
            $total_past_refund = $total_past_refund->where("card_type", "!=", "3");
        } else if ($type == "MasterCard") {
            $total_past_refund = $total_past_refund->where("card_type", "3");
        }
        $total_past_refund = $total_past_refund->where(\DB::raw('DATE(transactions.refund_remove_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.refund_remove_date)'), '<=', $chargebacksEndDate)
            ->first();
        $total_past_flagged = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->where('user_id', $input['user_id'])
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1', '2'])
            ->where("deleted_at", NULL)
            ->where('is_flagged_remove', '1');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $total_past_flagged = $total_past_flagged->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if ($type == "Other") {
            $total_past_flagged = $total_past_flagged->where("card_type", "!=", "3");
        } else if ($type == "MasterCard") {
            $total_past_flagged = $total_past_flagged->where("card_type", "3");
        }
        $total_past_flagged = $total_past_flagged->where(\DB::raw('DATE(transactions.flagged_remove_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.flagged_remove_date)'), '<=', $chargebacksEndDate)
            ->first();

        $total_past_chargeback = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->where('user_id', $input['user_id'])
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1', '2'])
            ->where("deleted_at", NULL)
            ->where('chargebacks_remove', '1');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $total_past_chargeback = $total_past_chargeback->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if ($type == "Other") {
            $total_past_chargeback = $total_past_chargeback->where("card_type", "!=", "3");
        } else if ($type == "MasterCard") {
            $total_past_chargeback = $total_past_chargeback->where("card_type", "3");
        }
        $total_past_chargeback = $total_past_chargeback->where(\DB::raw('DATE(transactions.chargebacks_remove_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.chargebacks_remove_date)'), '<=', $chargebacksEndDate)
            ->first();

        $total_past_retrieval = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->where('user_id', $input['user_id'])
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1', '2'])
            ->where("deleted_at", NULL)
            ->where('is_retrieval_remove', '1');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $total_past_retrieval = $total_past_retrieval->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if ($type == "Other") {
            $total_past_retrieval = $total_past_retrieval->where("card_type", "!=", "3");
        } else if ($type == "MasterCard") {
            $total_past_retrieval = $total_past_retrieval->where("card_type", "3");
        }
        $total_past_retrieval = $total_past_retrieval->where(\DB::raw('DATE(transactions.retrieval_remove_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.retrieval_remove_date)'), '<=', $chargebacksEndDate)
            ->first();
        $childData['user_id'] = $input['user_id'];
        $childData['payoutreport_id'] = $reportID->id;
        $childData["total_transaction_count"] = $approved_transaction->count + $declined_transaction->count;
        $childData["total_transaction_sum"] = $approved_transaction->amount + $declined_transaction->amount;
        $childData['approve_transaction_count'] = $approved_transaction->count;
        $childData['approve_transaction_sum'] = $approved_transaction->amount;
        $childData['declined_transaction_count'] = $declined_transaction->count;
        $childData['declined_transaction_sum'] = $declined_transaction->amount;
        $childData['chargeback_transaction_count'] = $chargebacks_transaction->count;
        $childData['chargeback_transaction_sum'] = $chargebacks_transaction->amount;
        $childData['refund_transaction_count'] = $refund_transaction->count;
        $childData['refund_transaction_sum'] = $refund_transaction->amount;
        $childData['flagged_transaction_count'] = $total_flagged->count;
        $childData['flagged_transaction_sum'] = $total_flagged->amount;
        $childData['retrieval_transaction_count'] = $total_retrieval->count;
        $childData['retrieval_transaction_sum'] = $total_retrieval->amount;
        $childData['currency'] = $value;

        if ($type == "Other") {
            $childData['mdr'] = ($userData->merchant_discount_rate * $approved_transaction->amount) / 100;
        } else if ($type == "MasterCard") {
            $childData['mdr'] = ($userData->merchant_discount_rate_master_card * $approved_transaction->amount) / 100;
        }

        //$childData['mdr'] = ($userData->merchant_discount_rate * $approved_transaction->amount) / 100;
        $childData['rolling_reserve'] = ($userData->rolling_reserve_paercentage * $approved_transaction->amount) / 100;
        $tramsactionFee = ($userData->transaction_fee * ($approved_transaction->count + $declined_transaction->count));
        $transactionFeeConvertedAmount = 0;
        if ($tramsactionFee != 0) {
            $returnFee = checkSelectedCurrencyTwo('USD', $tramsactionFee, $value);
            // dd($returnFee, $tramsactionFee, $value);
            $transactionFeeConvertedAmount = $returnFee["amount"];
        }
        $childData['transaction_fee'] = $tramsactionFee;
        $childData['refund_fee'] = ($userData->refund_fee * $refund_transaction->count);
        $chargebacks_fee = ($userData->chargeback_fee * $chargebacks_transaction->count);
        $chargebackFeeConvertedAmount = 0;
        if ($chargebacks_fee != 0) {
            $chargebackFee = checkSelectedCurrencyTwo('USD', $chargebacks_fee, $value);
            $chargebackFeeConvertedAmount = $chargebackFee["amount"];
        }
        $childData['chargeback_fee'] = $chargebacks_fee;
        $flagged_fee = ($userData->flagged_fee * $total_flagged->count);
        $flaggedFeeConvertedAmount = 0;
        if ($flagged_fee != 0) {
            $flaggedReturnFee = checkSelectedCurrencyTwo('USD', $flagged_fee, $value);
            $flaggedFeeConvertedAmount = $flaggedReturnFee["amount"];
        }
        $childData['flagged_fee'] = $flagged_fee;
        $retrieval_fee = ($userData->retrieval_fee * $total_retrieval->count);
        $retrievalFeeConvertedAmount = 0;
        if ($retrieval_fee != 0) {
            $retrievalReturnFee = checkSelectedCurrencyTwo('USD', $retrieval_fee, $value);
            $retrievalFeeConvertedAmount = $retrievalReturnFee["amount"];
        }
        $childData['retrieval_fee'] = $retrieval_fee;
        $childData["remove_past_flagged"] = $total_past_flagged->count;
        $past_flagged_fee = ($userData->flagged_fee * $total_past_flagged->count);
        $pastFlaggedFeeConvertedAmount = 0;
        if ($past_flagged_fee != 0) {
            $pastFlaggedFee = checkSelectedCurrencyTwo('USD', $past_flagged_fee, $value);
            $pastFlaggedFeeConvertedAmount = $pastFlaggedFee["amount"];
        }
        $childData["past_flagged_charge_amount"] = $past_flagged_fee;

        if ($type == "Other") {
            $past_flagged_sum_deduction = (($userData->merchant_discount_rate * $total_past_flagged->amount) / 100) + (($userData->rolling_reserve_paercentage * $total_past_flagged->amount) / 100);
        } else if ($type == "MasterCard") {
            $past_flagged_sum_deduction = (($userData->merchant_discount_rate_master_card * $total_past_flagged->amount) / 100) + (($userData->rolling_reserve_paercentage * $total_past_flagged->amount) / 100);
        }
        $finalPastFalggedAmount = 0;
        if ($total_past_flagged->amount != 0) {
            $finalPastFalggedAmount = ($total_past_flagged->amount) - $past_flagged_sum_deduction - ($convertTransactionFee * $total_past_flagged->count);
        }
        $childData["past_flagged_sum"] = $finalPastFalggedAmount;
        $childData["remove_past_chargebacks"] = $total_past_chargeback->count;
        $past_chargeback_fee = ($userData->chargeback_fee * $total_past_chargeback->count);
        $pastChargebackAmount = 0;
        if ($past_chargeback_fee != 0) {
            $pastChargebackFee = checkSelectedCurrencyTwo('USD', $past_chargeback_fee, $value);
            $pastChargebackAmount = $pastChargebackFee["amount"];
        }
        $childData["past_chargebacks_charge_amount"] = $past_chargeback_fee;
        $childData["past_chargebacks_sum"] = $total_past_chargeback->amount;
        $childData["remove_past_retrieval"] = $total_past_retrieval->count;
        $past_retrieval_charge_amount = ($userData->retrieval_fee * $total_past_retrieval->count);
        $pastRetrievalAmount = 0;
        if ($past_retrieval_charge_amount != 0) {
            $pastRetrievalFee = checkSelectedCurrencyTwo('USD', $past_retrieval_charge_amount, $value);
            $pastRetrievalAmount = $pastRetrievalFee["amount"];
        }
        $childData["past_retrieval_charge_amount"] = $past_retrieval_charge_amount;

        if ($type == "Other") {
            $past_retrieval_sum_deduction = (($userData->merchant_discount_rate * $total_past_retrieval->amount) / 100) + (($userData->rolling_reserve_paercentage * $total_past_retrieval->amount) / 100);
        } else if ($type == "MasterCard") {
            $past_retrieval_sum_deduction = (($userData->merchant_discount_rate_master_card * $total_past_retrieval->amount) / 100) + (($userData->rolling_reserve_paercentage * $total_past_retrieval->amount) / 100);
        }
        $finalPastRetrievalAmount = 0;
        if ($total_past_retrieval->amount != 0) {
            $finalPastRetrievalAmount = ($total_past_retrieval->amount) - $past_retrieval_sum_deduction - ($convertTransactionFee * $total_past_retrieval->count);
        }
        $childData["past_retrieval_sum"] = $finalPastRetrievalAmount;
        $returnFlaggedFee = 0;
        $totalChargebackAmount = 0;
        $totalChargebackCount = 0;
        if (isset($payout_report)) {
            $payout_start_date = date('Y-m-d', strtotime($payout_report->start_date));
            $payout_end_date = date('Y-m-d', strtotime($payout_report->end_date));
            $checkedPastFlagged = \DB::table('transactions')
                ->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                ->where('user_id', $input['user_id'])
                ->where("deleted_at", NULL)
                ->where('currency', $value)->where(["is_flagged" => "1", "chargebacks" => "1"])
                ->where(\DB::raw('DATE(transactions.flagged_date)'), '>=', $payout_start_date)
                ->where(\DB::raw('DATE(transactions.flagged_date)'), '<=', $payout_end_date)
                ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '>=', $chargebacksStartDate)
                ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '<=', $chargebacksEndDate)
                ->whereNotIn('payment_gateway_id', ['1', '2']);
            if ($type == "Other") {
                $checkedPastFlagged = $checkedPastFlagged->where("card_type", "!=", "3");
            } else if ($type == "MasterCard") {
                $checkedPastFlagged = $checkedPastFlagged->where("card_type", "3");
            }
            $checkedPastFlagged = $checkedPastFlagged->first();
            $pastFlaggedChargebackAmount = 0;
            if ($checkedPastFlagged->amount != 0) {
                $pastFlaggedChargebackAmount = ($checkedPastFlagged->amount) - (($userData->merchant_discount_rate * $checkedPastFlagged->amount) / 100) - (($userData->rolling_reserve_paercentage * $checkedPastFlagged->amount) / 100) - ($convertTransactionFee * $checkedPastFlagged->count);
            }
            $totalChargebackCount += $checkedPastFlagged->count;
            $checkedPastRefund = \DB::table('transactions')
                ->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                ->where('user_id', $input['user_id'])
                ->where("deleted_at", NULL)
                ->where('currency', $value)->where(["refund" => "1", "chargebacks" => "1"])
                ->where(\DB::raw('DATE(transactions.refund_date)'), '>=', $payout_start_date)
                ->where(\DB::raw('DATE(transactions.refund_date)'), '<=', $payout_end_date)
                ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '>=', $chargebacksStartDate)
                ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '<=', $chargebacksEndDate)
                ->whereNotIn('payment_gateway_id', ['1', '2']);
            if ($type == "Other") {
                $checkedPastRefund = $checkedPastRefund->where("card_type", "!=", "3");
            } else if ($type == "MasterCard") {
                $checkedPastRefund = $checkedPastRefund->where("card_type", "3");
            }
            $checkedPastRefund = $checkedPastRefund->first();
            $pastRefundChargebackAmount = 0;
            if ($checkedPastRefund->amount != 0) {
                $pastRefundChargebackAmount = ($checkedPastRefund->amount) - (($userData->merchant_discount_rate * $checkedPastRefund->amount) / 100) - (($userData->rolling_reserve_paercentage * $checkedPastRefund->amount) / 100) - ($convertTransactionFee * $checkedPastRefund->count);
            }
            $totalChargebackCount += $checkedPastRefund->count;
            $checkedPastRetrieval = \DB::table('transactions')
                ->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                ->where('user_id', $input['user_id'])
                ->where("deleted_at", NULL)
                ->where('currency', $value)->where(["is_retrieval" => "1", "chargebacks" => "1"])
                ->where(\DB::raw('DATE(transactions.retrieval_date)'), '>=', $payout_start_date)
                ->where(\DB::raw('DATE(transactions.retrieval_date)'), '<=', $payout_end_date)
                ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '>=', $chargebacksStartDate)
                ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '<=', $chargebacksEndDate)
                ->whereNotIn('payment_gateway_id', ['1', '2']);
            if ($type == "Other") {
                $checkedPastRetrieval = $checkedPastRetrieval->where("card_type", "!=", "3");
            } else if ($type == "MasterCard") {
                $checkedPastRetrieval = $checkedPastRetrieval->where("card_type", "3");
            }
            $checkedPastRetrieval = $checkedPastRetrieval->first();
            $pastRetrievalChargebackAmount = 0;
            if ($checkedPastRetrieval->amount != 0) {
                $pastRetrievalChargebackAmount = ($checkedPastRetrieval->amount) - (($userData->merchant_discount_rate * $checkedPastRetrieval->amount) / 100) - (($userData->rolling_reserve_paercentage * $checkedPastRetrieval->amount) / 100) - ($convertTransactionFee * $checkedPastRetrieval->count);
            }
            $totalChargebackCount += $checkedPastRetrieval->count;
            $totalChargebackAmount = $pastRetrievalChargebackAmount + $pastRefundChargebackAmount + $pastFlaggedChargebackAmount;
            $returnFlaggedFee = ($userData->flagged_fee * $checkedPastFlagged->count) + ($userData->refund_fee * $checkedPastRefund->count) + ($userData->retrieval_fee * $checkedPastRetrieval->count);
        }
        $returnFeeAmount = 0;
        if ($returnFlaggedFee != 0) {
            $returnFee = checkSelectedCurrencyTwo('USD', $returnFlaggedFee, $value);
            $returnFeeAmount = $returnFee["amount"];
        }
        $totalFee = $chargebackFeeConvertedAmount + $flaggedFeeConvertedAmount + $retrievalFeeConvertedAmount + $transactionFeeConvertedAmount;
        $childData['return_fee'] = $totalChargebackAmount;
        $childData['return_fee_count'] = $totalChargebackCount;
        $childData["past_flagged_fee"] = $returnFeeAmount;
        $childData["transactions_fee_total"] = $totalFee;
        $childData['sub_total'] = $approved_transaction->amount - ($refund_transaction->amount + $chargebacks_transaction->amount + $total_flagged->amount + $total_retrieval->amount);
        $net_settlement_amount_value = $childData['sub_total'] - ($totalFee + $childData['rolling_reserve'] + $childData['mdr']) + $childData["past_flagged_sum"] + $childData["past_retrieval_sum"] + $returnFeeAmount + $totalChargebackAmount;
        $childData['net_settlement_amount'] = $net_settlement_amount_value;
        $net_amount_converted = checkSelectedCurrencyTwo($value, $net_settlement_amount_value, 'USD');
        $childData['net_settlement_amount_in_usd'] = $net_amount_converted["amount"];
        $childData['card_type'] = $type;
        $this->PayoutReportsChild->storeData($childData);
    }

    public function makeReportPaid(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if ($this->PayoutReports->updateData($request->get('id'), ['status' => $request->get('status')])) {
            $ArrRequest = ['status' => $request->get('status')];
            addAdminLog(AdminAction::PAYOUT_REPORT_PAID, $request->get('id'), $ArrRequest, "Payout Report Paid");
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function showReportClient(Request $request)
    {
        if ($this->PayoutReports->updateData($request->get('id'), ['show_client_side' => $request->get('status')])) {
            if ($request->get('status') == 1) {
                $input = \Arr::except($request->all(), array('_token', '_method'));
                $id = $request->get('id');
                $data = $this->PayoutReports->findData($id);
                $childData = $this->PayoutReportsChild->findDataByReportID($id);
                $users = \DB::table('users')->find($data->user_id);
                $totalFlagged = \DB::table('payout_report_child')->where('payoutreport_id', $id)->sum('flagged_transaction_count');
                $start_date = $data->start_date;
                $start_date = str_replace('/', '-', $start_date);
                if (date('d', strtotime($start_date)) < 8) {
                    $annual_fee = \DB::table('users')->select('annual_fee')->where('id', $data->user_id)->first()->annual_fee;
                } else {
                    $annual_fee = 0;
                }
                view()->share('data', $data);
                view()->share('childData', $childData);
                view()->share('annual_fee', $annual_fee);
                view()->share('totalFlagged', $totalFlagged);
                $options = new Options();
                $options->setIsRemoteEnabled(true);
                $dompdf = new Dompdf($options);
                $dompdf->loadHtml(view('admin.payoutReport.show_report_PDF'));
                $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');
                $dompdf->render();
                $fileName = str_replace('/', '-', $data->date) . '-' . $data->company_name . '-' . $data->id . '-' . $data->processor_name . '.' . 'pdf';
                Storage::disk('public')->put("pdf/" . $fileName, $dompdf->output());
                // Mail::to($users->email)->send(new ShowReport($fileName));
                unlink(storage_path('app/public/pdf/' . $fileName));
                $ArrRequest = ['show_client_side' => $request->get('status')];
                addAdminLog(AdminAction::PAYOUT_REPORT_SHOW, $request->get('id'), $ArrRequest, "Payout Report show to client");
            } else {
                $ArrRequest = ['show_client_side' => $request->get('status')];
                addAdminLog(AdminAction::PAYOUT_REPORT_SHOW, $request->get('id'), $ArrRequest, "Payout Report can't show to client");
            }

            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function massremove(Request $request)
    {
        try {
            $report_id_array = $request->input('id');
            foreach ($report_id_array as $key => $value) {
                PayoutReportsChild::where('payoutreport_id', $value)->delete();
                $this->PayoutReports->destroyData($value);
            }
            $ArrRequest = ['id' => implode(",", $report_id_array)];
            addAdminLog(AdminAction::PAYOUT_REPORT_DELETE, null, $ArrRequest, "Payout Reports Deleted");
            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function reportFilesUpload(Request $request)
    {
        $this->validate($request, [
            'files' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));
        $arr = [];
        if ($request->hasFile('files')) {
            $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageName = $imageName . '.' . $request->file('files')->getClientOriginalExtension();
            $filePath = 'uploads/generatedreport/' . $imageName;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('files')->getRealPath()));
            $arr['files'] = $filePath;
        } else {
            $arr['files'] = '';
        }
        $files = $this->PayoutReports->findData($request->get('id'));
        if ($files == null) {
            $arr['files'] = json_encode([$arr['files']]);
        } else {
            $files = json_decode($files);
            array_push($files, $arr['files']);
            $arr['files'] = json_encode($files);
        }
        $this->PayoutReports->updateData($input["report_id"], $arr);
        $ArrRequest = $arr;
        addAdminLog(AdminAction::PAYOUT_REPORT_UPLOAD_FILES, $input["report_id"], $ArrRequest, "File Uploaded Successfully!");
        notificationMsg('success', 'File Uploaded Successfully!');
        return redirect()->back();
    }

    public function generateReportExport(Request $request)
    {
        $ArrRequest = [];
        if (isset($request->ids) && !empty($request->ids)) {
            $ArrRequest = ['id' => implode(",", $request->ids)];
        }
        addAdminLog(AdminAction::PAYOUT_REPORT_DOWNLOAD_EXCEL, null, $ArrRequest, "Payout Report Download Excel File");
        return Excel::download(new GenerateReportExport($request->ids), 'GenerateReport_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function show($id)
    {
        $data = $this->PayoutReports->findData($id);
        $childData = $this->PayoutReportsChild->findDataByReportID($id);

        $totalFlagged = \DB::table('payout_report_child')->where('payoutreport_id', $id)->sum('flagged_transaction_sum');
        $start_date = $data->start_date;
        $start_date = str_replace('/', '-', $start_date);

        if (date('d', strtotime($start_date)) < 8) {
            $annual_fee = \DB::table('users')->select('annual_fee')->where('id', $data->user_id)->first()->annual_fee;
        } else {
            $annual_fee = 0;
        }
        view()->share('data', $data);
        view()->share('childData', $childData);
        view()->share('annual_fee', $annual_fee);
        view()->share('totalFlagged', $totalFlagged);

        return view('admin.payoutReport.show_report_PDF', compact('data', 'childData', 'totalFlagged'));
    }

    public function generatePDF($id)
    {
        $data = $this->PayoutReports->findData($id);
        $childData = $this->PayoutReportsChild->findDataByReportID($id);
        $totalFlagged = \DB::table('payout_report_child')->where('payoutreport_id', $id)->sum('flagged_transaction_count');
        $start_date = $data->start_date;
        $start_date = str_replace('/', '-', $start_date);
        if (date('d', strtotime($start_date)) < 8) {
            $annual_fee = \DB::table('users')->select('annual_fee')->where('id', $data->user_id)->first()->annual_fee;
        } else {
            $annual_fee = 0;
        }
        view()->share('data', $data);
        view()->share('childData', $childData);
        view()->share('annual_fee', $annual_fee);
        view()->share('totalFlagged', $totalFlagged);

        $ArrRequest = [];
        addAdminLog(AdminAction::PAYOUT_REPORT_GENERATE_PDF, $id, $ArrRequest, "Payout Report Generated PDF");

        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('admin.payoutReport.show_report_PDF'));

        // (Optional) Setup the paper size and orientation
        // $dompdf->setPaper('A4', 'landscape');
        $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');

        $dompdf->render();
        \DB::table('payout_reports')->where('id', $id)->update(['is_download' => '1']);
        $dompdf->stream(str_replace('/', '-', $data->date) . '-' . $data->company_name . '-' . $data->id . '-' . $data->processor_name . '.pdf');
    }
}